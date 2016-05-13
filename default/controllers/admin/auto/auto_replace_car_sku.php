<?php

/**
 * 自动替换SKU(新换老)
 * 
 * 
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Auto_replace_car_sku extends MY_Controller{
    const ORDERS_ID_BACKORDER = 1;//订单欠货状态
    
    function __construct(){
        parent::__construct();

        $this->load->library('mail/phpmailer.php');
        $this->load->model(array(
                'order/orders_model',
                'order/orders_products_model',
                'order/car_old_sku_model' ,
                'operate_log_model',
                'base_country_model',
                'country_model',
                'system_model'
                )
        );

        $this->model = $this->orders_model;
       
    }

    
    static function getArrSpec($key){
       $result =  array(   
        	'1'=>'美规',
        	'2'=>'欧规',
        	'3'=>'英规',	
        	'4'=>'澳规'
        );
       return $result[$key];
    }
    
    public function replace_sku($orders_type = 7){
        $type = $this->input->get_post('type');
        $orders_type = (isset($type) && !empty($type))?$type:'';
        header("Content-type: text/html; charset=utf-8");
        //查找所有映射的SKU
        $new_sku = $this->car_old_sku_model->getAll2Array();

        if(empty($new_sku)){
            die('没有映射的SKU');
        }
        $sku = array();

        $sku_key = array();

        foreach ($new_sku as $s) {
            $sku[] = trim($s['old_sku']);
            $sku_key[trim($s['old_sku'])] = trim($s['new_sku']);
        }

        //查找订单
        $options = array();

        $where = array();

        $where_in = array();

        $where_in['p.orders_sku'] = $sku;

        if(!empty($type)){
            $where['orders_type'] = $orders_type;
        }
        

        $where['orders_status <='] = 3;

        $where['orders_is_join'] = 0;

        $options['where'] = $where;

        $options['where_in'] = $where_in;

        $options['select'] = array('p.*');

        $join[] = array($this->orders_products_model->_table.' p',"p.erp_orders_id={$this->model->_table}.erp_orders_id");

        $options['join'] = $join;

        $options['limit'] = '100';

        $order = $this->model->getAll2Array($options);
//         echo $this->db->last_query();exit;
        if(empty($order)){
            die('没有需要替换SKU的订单');
        }

        //如果有符合条件的订单
        foreach ($order as $key => $v) {

            $sku = $sku_key[$v['orders_sku']];

            if(empty($sku)){
                continue;
            }
            
            $options = array();

            $options['where']['orders_products_id'] = $v['orders_products_id'];

            $data = array();

            $data['orders_sku'] = $sku;

            $tof = $this->orders_products_model->update($data,$options);

            //写入操作日志
            if($tof){
                $data = array();
                $data['operateUser'] = 30;
                $data['operateKey'] = $v['erp_orders_id'];
                $data['operateMod']  = 'newtoold';
                $data['operateText'] = '系统自动替换订单SKU，原SKU：'.$v['orders_sku'].',新SKU：'.$sku;
                $log_tof = $this->operate_log_model->add_order_operate_log($data);
                echo '订单：'.$v['erp_orders_id'].'替换SKU成功，原SKU：'.$v['orders_sku'].'，新SKU：'.$sku.'<br/>';
            }
        }

    }
    
    
    /**
     * 邮件推送欠货新SKU详情
     */
    public function autoSendMail(){//and orders_is_backorder = 1 and orders_status != 6
        header("Content-type: text/html; charset=utf-8");
        //查找所有映射的SKU
        $new_sku = $this->car_old_sku_model->getAll2Array();
        
        if(empty($new_sku)){
            die('没有映射的SKU');
        }
        
        $sku = array();
        
        $sku_key = array();
        
        foreach ($new_sku as $s) {
            $sku[] = trim($s['new_sku']);
        }
        
        //查找订单产品
        $options = array();
        
        $where = array();
        
        $where_in = array();
        
        $where_in['p.orders_sku'] = $sku;        
//         $where['orders_type'] = $orders_type;
        $where['orders_is_backorder'] = self::ORDERS_ID_BACKORDER;//订单欠货状态
        $where['orders_status <='] = 3;
        
        $where['orders_is_join'] = 0;
        
        $options['where'] = $where;
        
        $options['where_in'] = $where_in;
        
        $options['select'] = array("p.orders_sku,{$this->model->_table}.buyer_country_code");
        
        $join[] = array($this->orders_products_model->_table.' p',"p.erp_orders_id={$this->model->_table}.erp_orders_id");
        
        $options['join'] = $join;
        $options['group_by'] = "p.orders_sku";//,{$this->model->_table}.buyer_country_code
//         $options['limit'] = '50';
        
        $order = $this->model->getAll2Array($options);
//         echo $this->db->last_query();exit;
        if(empty($order))exit('没有欠货sku');
        $baseCountryAll = array();
        $baseCountry    = array();
        $specAll        = array();
        $baseCountryAll = $this->base_country_model->getAll2Array();       
        
        foreach($baseCountryAll as $key=>$val){           
            $specAll = $this->country_model->getOne(array('where'=>array('country_en'=>$val['country_en'])),true);
            $baseCountry[$val['country_code']] = @self::getArrSpec($specAll['adapter_spec']);           
        }
        
        //邮件表格
        $html = '';
        $html .= '<table cellpadding="0" cellspacing="0" border="1">'.PHP_EOL.'
        <tr>
            <th>序号</th>
            <th>SKU</th>
            <th><table  cellpadding="0" cellspacing="0" border="1"><tr><td style="width:100px;">规格</td><td style="width:100px;">数量</td></tr></table></th>
            <th>欠货总数量</th>
        </tr>'.PHP_EOL;
        $beforeSku = '';
        $i = 0;
        foreach($order as $key=>$val){ 
            if($val['orders_sku']==$beforeSku) continue;
            $beforeSku = $val['orders_sku'];
            //查找订单
            $options = array();            
            $where = array(); 
            $join = array();           
            $where['p.orders_sku'] = $val['orders_sku'];
//             $where['buyer_country_code'] = $val['buyer_country_code'];
            //         $where['orders_type'] = $orders_type;
            $where['orders_is_backorder'] = self::ORDERS_ID_BACKORDER;//订单欠货状态
            $where['orders_status <='] = 3;            
            $where['orders_is_join'] = 0;           
            $options['where'] = $where;          
            $options['select'] = array("p.orders_sku,p.erp_orders_id,p.item_count,{$this->model->_table}.buyer_country_code");           
            $join[] = array($this->orders_products_model->_table.' p',"p.erp_orders_id={$this->model->_table}.erp_orders_id");
            
            $options['join'] = $join;          
//             $options['group_by'] = "{$this->model->_table}.buyer_country_code";
            $orderInfo = array();
            $orderInfo = $this->model->getAll2Array($options);
            if(empty($orderInfo))continue;
            $html .='<tr><td>'.($i+1).'</td>
                         <td>'.$val['orders_sku'].'</td><td><table cellpadding="0" cellspacing="0" border="1">'.PHP_EOL; 
            $totalNum = 0;//欠货总数 
          
            $countryArr = array(); 
                                        
            foreach($orderInfo as $k=>$v){//<td style="width:100px;">订单号</td> SKU对应的订单信息 <td  style="width:100px;">'.$v['erp_orders_id'].'<td>
                $totalNum += $v['item_count'];
                if(!empty($baseCountry[$v['buyer_country_code']]))
                $countryArr[$baseCountry[$v['buyer_country_code']]][] = $v['item_count'];                           
            }      
            foreach($countryArr as $k=>$v){
                $total = 0;
                foreach($v as $vv){
                    $total += $vv;
                }
                $countryArr[$k] = $total;
            } 
            foreach($countryArr as $c =>$cc){
            $html .='<tr>
                                 <td  style="width:100px;">'.$c.'</td>
                                 <td  style="width:100px;">'.$cc.'</td>
                             </tr>';
            }
            $html .='</table></td><td>'.$totalNum.'</td></tr>'.PHP_EOL;
            $i++;
        }
        $html .= '</table>'.PHP_EOL;
        if ($html){//有数据
            $mail = new PHPMailer();
            $mail->IsSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.exmail.qq.com';                   // Specify main and backup server
            $mail->Port = 25;  //:465
        
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'stockupdate@moonarstore.com';                            // SMTP username
            $mail->Password = 'salamoer1234';                           // SMTP password
            // 	$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted
        
            $mail->CharSet ="UTF-8";
            $mail->From = 'stockupdate@moonarstore.com';
            $mail->FromName = 'stockupdate';
            $mailArr = array();
            $mailArr = array(
                'caihongling@moonarstore.com',
                'zengyangshun@moonarstore.com',
                'sujinyu@moonarstore.com',
                'luokefei@moonarstore.com'         
            );

            if ($mailArr){//有邮箱就发送到邮箱
                foreach ($mailArr as $email){
                    $mail->AddAddress($email);
                }
            }else{
                $mail->AddAddress('zhaohao@moonarstore.com');
             }
            $mail->IsHTML(true);                                  // Set email format to HTML
            $mail->Subject = date('Y-m-d').'欠货详情';
            $mail->Body = $html;
        
            //判断邮件是否发送成功
            $isSend = $mail->Send();
            echo $isSend."<br/>";
        }
    }

}







