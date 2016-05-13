<?php
//物流查询中心自动脚本
/**
 * 新逻辑
 * 只查两次，第四天、第六天查询
 * 如果第四天能查到，就不再查询，如果查询不到，第六天再查一次
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auto_selltool_tracking extends MY_Controller{

	private $api_day = -3;
	
	private $date_array = array(
				0 => 4,
				1 => 6
			);
    
    function __construct(){
        
        parent::__construct();

        $this->load->model(array(
                'sellertool_api_info_model',
                'sellertool_api_info_detail_model',
                'sellertool_api_log_model',
        		'system_model'
                )
        );

        $this->load->library('Selltool_api');

        $this->model = $this->sellertool_api_info_model;

        $this->api = new Selltool_api();
       
    }

    function tracking_orders($per_page=1,$start='',$end=''){

        ini_set('memory_limit', '300M');
        set_time_limit(0);

    	header("Content-type: text/html; charset=utf-8");
    	

        //获取需要查询的订单,每次100个订单
        $options = array();

        $where = array();

        if($start){
            $start_day = date('Y-m-d',strtotime($start.' day'));
            $where['orders_shipping_time <'] = $start_day;
        }
    	
        if($end){
            $end_day = date('Y-m-d',strtotime($end.' day'));
            $where['orders_shipping_time >='] = $end_day;
        }

        $where['orders_shipping_time <'] = date('Y-m-d',strtotime('-3 day')).' 23:59:59';

        $where['orders_shipping_time >='] = date('Y-m-d',strtotime('-45 day'));

        $where['next_call <='] = date('Y-m-d');
        
    	//只查询待查询的
    	$where['status'] = 0;
    	
    	$where['num <'] = 2;//查询次数不能超过2次的

    	$options['where'] = $where;

        $options['page'] = 4;//条数

        $options['per_page'] = ($per_page-1)*$options['page'];

    	$order = $this->model->getAll2Array($options);

    	if(empty($order)){
    		die('没有需要查询的订单');
    	}

    	$j = 0;
    	
    	$total = count($order);

    	$shiping_code = array();
    	
    	$update_data = array();//更新记录的请求次数和下次请求时间的数组

    	foreach ($order as $key => $o) {
    		
            $data = array();
            $data['api_time'] = date('Y-m-d H:i:s');
            $data['num']	  = $o['num']+1;
            //下一次请求时间，订单发货时间加上对应次数的时间
    		$ship_time = strtotime($o['orders_shipping_time']);
    		$n = $this->date_array[$data['num']];//距离发货时间的天数
    		$add_time = $n*86400;
			$next_call = date('Y-m-d',($ship_time+$add_time));
			$data['next_call'] = $next_call;
           
            $update_data[trim($o['orders_shipping_code'])] = $data;

    		$shiping_code[] = trim($o['orders_shipping_code']);

    		$j++;

    		$tof_api = false;
            
    		if($j == 10 || ($total - $key) == 1){
    			$tof_api = true;
    		}

    		//10个提交一次
    		if($tof_api){

    			$j=0;
    			
    			$flag = 0;//是否可以更新api次数和下次api请求时间的标志，等于0可以更新,大于0不可以更新

    			$numbers = join(',',$shiping_code);
    			
    			//查询api
    			$result = $this->api->track_numbers($numbers);
    			
    			$flag = strpos($result['Error'],'is not enough');
    
    			//写入api查询日志
    			$data = array();
    			$data['numbers'] = $numbers;
    			$data['api_result'] = var_export($result ,true);
    			$data['numbers_count'] = count($shiping_code);
    			$data['api_date'] = date('Y-m-d H:i:s');
    			$this->sellertool_api_log_model->add($data);
    			
    			if($flag == 0 && !empty($result)){
	    			//在请求API的时候才更新下次请求时间和api的请求次数
	    			foreach($shiping_code as $scode){
	    			  $options = array();
	    			  $up = array();//要更新的数据
            		  $options['where']['orders_shipping_code'] = $scode;
            		  $up = $update_data[$scode];
            		  $this->sellertool_api_info_model->update($up,$options);
	    			}
    			}
    			
    			if(isset($result[0]['Number'])){
    				$this->do_result($result);
    			}

    			$update_data = array();
    			$shiping_code = array();
                print_r($result);
    		}
    		
    	}

    }

    //处理查询结果
    function do_result($result){

    	$data = $result;

    	foreach ($data as $k => $v) {

    		$shiping_code = trim($v['Number']);

    		$status = trim($v['Status']);

    		$order = $this->get_num_api_num($shiping_code);
    
    		//如果状态是1-查询不到,且api查询次数为1，则状态还是待查询
    		if($status == 1 && empty($v['OriginTracking']['Events'])){
    			
    			if($order['num'] == 1){
    				$status = 0;
    			}
    		}

            if($status == 1 && isset($v['OriginTracking']['Events']) && !empty($v['OriginTracking']['Events'])){
                $status = 2;
            }

    		//查询不到的原因
    		$not_found_status = $v['NotFoundReason'];

    		$data = array();

    		$data['status'] = $status;

    		$data['not_found_status'] = $not_found_status;

    		$data['api_time'] = date('Y-m-d H:i:s');

            if(isset($v['OriginTracking']['Carrier'])){
                $data['carrier1'] = $v['OriginTracking']['Carrier'];
            }
    		
            if(isset($v['DestTracking']['Carrier'])){
                $data['carrier2'] = $v['DestTracking']['Carrier'];
            }
    		
    		//上网时间
    		if($order['day_num'] == '0' && isset($v['OriginTracking']['Events'])){
    			$date = end($v['OriginTracking']['Events']);
    			if(isset($date['Time']) && !empty($date['Time'])){
    				$data['day'] = date('Y-m-d H:i:s',strtotime($date['Time']));
    				$data['day_num'] = count_day_date_to_date($order['orders_shipping_time'],$data['day']);
    			}
    			
    		}

    		//更新数据
    		$options = array();
    		$options['where']['orders_shipping_code'] = $shiping_code;
    		$tof = $this->model->update($data,$options);
    		
    		//删除详情表的数据
    		$del_tof = $this->sellertool_api_info_detail_model->delete($options);

    		$detail_data = array();

    		//插入物流详情信息
    		if(isset($v['OriginTracking']['Events']) && !empty($v['OriginTracking']['Events'])){
    			foreach ($v['OriginTracking']['Events'] as $e) {
    				$data = array();
    				$data['orders_shipping_code'] = $shiping_code;
    				$data['description'] = addslashes($e['Description']);
    				$data['location'] = addslashes($e['Location']);
    				$data['carrier'] = $v['OriginTracking']['Carrier'];
    				$data['reTime'] = date('Y-m-d H:i:s',strtotime($e['Time']));
    				$data['erp_orders_id'] = $order['erp_orders_id'];
    				$tof_detail = $this->sellertool_api_info_detail_model->add($data);
    			}
    		}
    		
    		//插入物流详情信息
    		if(isset($v['DestTracking']['Events']) && !empty($v['DestTracking']['Events'])){
    			foreach ($v['DestTracking']['Events'] as $e) {
    				$data = array();
    				$data['orders_shipping_code'] = $shiping_code;
    				$data['description'] = addslashes($e['Description']);
    				$data['location'] = addslashes($e['Location']);
    				$data['carrier'] = $v['DestTracking']['Carrier'];
    				$data['reTime'] = date('Y-m-d H:i:s',strtotime($e['Time']));
    				$data['erp_orders_id'] = $order['erp_orders_id'];
    				$tof_detail = $this->sellertool_api_info_detail_model->add($data);
    			}
    		}

    	}

    }

    //查看当前挂号码已查询次数
    function get_num_api_num($shiping_code){

    	$options = array();

    	$where = array();

    	$where['orders_shipping_code'] = trim($shiping_code);

    	$options['where'] = $where;

    	$data = $this->model->getOne($options,true);

    	return $data;
    }

    function wish_shipment(){
        $this->load->model(array(
                'wish_logistics_service_model',
                'wish_logistics_service_backup_model'  
                )
        );

        $data = $this->wish_logistics_service_backup_model->getAll2Array();

        foreach ($data as $k => $v) {
            
            $options['where']['logistics_name'] = $v['logistics_name'];

            $tof = $this->wish_logistics_service_model->getOne($options,true);

            if(empty($tof)){
                unset($v['logistics_id']);
                $this->wish_logistics_service_model->add($v);
            }
        }
    }

    function auto_kuaiyou(){

          $this->load->model(array(
                'order/orders_model',
                'order/orders_products_model',
                'products/products_data_model'
                )
        );

         $this->load->library('kuai_you_api');

         $kuai_you = new kuai_you_api();

         //产品数据
         $options = array();

         $options['where']['products_sku'] = 'E2731A1';

         $sku = $this->products_data_model->getOne($options);


         //查找订单
         $options = array();

         $options['where_in']['erp_orders_id'] = array(6105759,6105077,6105097,6090824,6081490,6077186,6056329,6054972,6048869,6030466);

         $data = $this->orders_model->getAll2Array($options);

         foreach ($data as $key => $v) {
              
              $kuai_you->upload_data($v);

          } 

    }

    function add_category(){

          $this->load->model(array(
                'category_model'
                )
        );

        $data = $this->category_model->getAll2Array();

        foreach ($data as $key => $v) {
              if($v['categoryTypePlat']){

                $options = array();

                $options['where']['category_id'] = $v['category_id'];

                $data = array();

                $categoryTypePlat = unserialize($v['categoryTypePlat']);
                
                $new = array();

                $new['ordersType'] = 14;

                $new['platForm'] = 35;

                array_push($categoryTypePlat,$new);

                print_r($categoryTypePlat);die;

                $data['categoryTypePlat'] = serialize($categoryTypePlat);

                $this->category_model->update($data,$options);

              }
          }  

    }

    function add_cnz_code(){
         $this->load->model(array(
                'order/orders_model'
                )
        );
    }

    //生成挂号码函数，为了让平邮网址可查询
    function create_shipping_code($erp_orders_id){
        $code = '';
        $abc='123456789';
        $a ='';
        for ($i=0; $i <2 ; $i++) { 
            $a .= mt_rand(0,9);
        }
        $code = 'ST'.$a.'11'.$erp_orders_id;
        return $code;
    }
    
    //根据挂号查询物流结果，在老系统调用该API接口
    function get_shipmentData_by_API(){
      
      $orders_shipping_code = trim($this->input->get_post('orders_shipping_code'));
    
      //查询api前先改变api——time
      $options = array();
      $options['where_in']['orders_shipping_code'] = $orders_shipping_code;
      $data = array();
      $data['api_time'] = date('Y-m-d H:i:s');
      $this->sellertool_api_info_model->update($data,$options);

      //查询api
      $result = $this->api->track_numbers($orders_shipping_code);

	  if(isset($result[0]['Number'])){
    	 $this->do_result($result);
      }

      //写入api查询日志
      $data = array();
      $data['numbers'] = $orders_shipping_code;
      $data['api_result'] = var_export($result ,true);
      $data['numbers_count'] = 1;
      $data['api_date'] = date('Y-m-d H:i:s');
      $this->sellertool_api_log_model->add($data);
      
      echo json_encode($result);
      exit;
    }
    
    /**
     * 自动检查API次数，不够的话发邮件推送
     */
    public function checkAPICount(){
    	
       $orders_shipping_code = 'RL123767663CN';
       
       //查询api
       $result = $this->api->track_numbers($orders_shipping_code);

       $mailInfo = $this->system_model->getDataByID(101);
       
       $mailArr = array();//要发送的邮箱
       
       if(!empty($mailInfo)){
          $mailArr = explode("\n",$mailInfo['system_value']);
       }

       //返回空数组说明token无效
       if($result['Status']==0 && $result['Error']=='Your access token request count is not enough, please recharge.'){
       	
          $this->load->library('mail/phpmailer');
          
          $body = empty($result['Error']) ? '无效token' : $result['Error'];
          
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
          
          foreach($mailArr as $m){
            $mail->AddAddress($m);  
          } 
          
      
          $mail->IsHTML(true);                                  // Set email format to HTML
          $mail->Subject = '赛兔国际包裹查询的API次数为0，请充值';
          $mail->Body = $body;
    
          //判断邮件是否发送成功
          $isSend = $mail->Send();
          echo $isSend."<br/>";
    
       }

    }
    
    
    
}    
