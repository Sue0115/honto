<?php
/**
 * 订单数据导入(通关时间导入)
 * 2015-10-22
 */
ini_set('memory_limit', '2048M');
set_time_limit(0);
header('Content-Type: text/html; Charset=utf-8');
class orderMemo extends Admin_Controller{
    
    function __construct(){
        parent::__construct();
        $this->load->model(
            array(
                'order_memo_model',//'sharepage'
            )
        );
        $this->load->library('phpexcel/PHPExcel','phpexcel/PHPExcel/Reader/Excel5.php');

    }

    /**
     * 订单通关时间
     */
    public function mainList(){
        //导入新增
        if(isset($_REQUEST['add']) && ($_REQUEST['add']=='add')){
            $url = admin_base_url('export_data/orderMemo/mainList');
            $phpExcel = new PHPExcel_Reader_Excel5;
            $extend = explode("." ,$_FILES['excelFile']["name"]);
            $val    = count($extend)-1;
            $extend = strtolower($extend[$val]);
            if($extend != 'xls' && $extend !='xlsx'){
                echo '<script>alert("文件格式不正确，请上传EXCEL文件");history.go(-1);</script>';
                exit;
            }
            $filename = $_FILES['excelFile']["tmp_name"];
            $main_name= substr($_FILES['excelFile']["name"],0,strpos($_FILES['excelFile']["name"],'.'));
            $objPHPExcel = $phpExcel->load($filename);
            $sheet = $objPHPExcel->getSheet();
            $rows=$sheet->getHighestRow();//EXCEL行数
            
            //             $cols=$sheet->getHighestColumn();
            $i=0;
            $data_array=array();
            for($j=2;$j<=$rows;$j++){
                if(trim($sheet->getCell("A".$j)->getValue())){
                    $data_array[$i]['erp_orders_id']  = trim($sheet->getCell("A".$j)->getValue());//内单号
                    $data_array[$i]['clearance_time'] = trim($sheet->getCell("B".$j)->getValue());//通关时间
                    $data_array[$i]['create_user']    = $this->user_info->id;//登录用户id
                    $data_array[$i]['create_time']    = date('Y-m-d H:i:s'); //导入时间                  
                    $i++;
                }
            }
            
            //事务开启
            $this->db->trans_begin();
            //导入的excel有数据
            $insertID = 0;
            if(isset($data_array) && !empty($data_array) ){
                $str ='';
                foreach($data_array as $key=>$val){ 
                    $flag = true;
                    $flag = strtotime($val['clearance_time'])?true:false;
                    if(!$flag){
                        $str .= '订单通关时间格式错误：'.$val['erp_orders_id'].',';
                        continue;
                    }
                    $ordersOption = array();
                    $ordersData   = array();
                    $ordersOption['where']['erp_orders_id'] = $val['erp_orders_id'];
                    $ordersData     = $this->order_memo_model->getOne($ordersOption,true);//查询订单表数据
                    if(!empty($ordersData)){
                        $str .= '重复订单：'.$val['erp_orders_id'].',';
                        continue;
                    }
                    $retult = '';                   
                    $retult = $this->db->insert('erp_order_memo', $val);                
                    if(empty($retult)){
                        $this->db->trans_rollback();
                        echo "<script>alert('导入excel失败,订单'".$val['erp_orders_id']."'导入失败;');location.href='".$url."'</script>";
                        exit;
                    }
                }
            }else{
                $str = '导入数据为空';
                $this->db->trans_rollback();
            }           
           
            if($this->db->trans_status() === TRUE){
                if(empty($str)){
                    $str = '导入成功';
                }
                $this->db->trans_commit();//事务结束
            }
            
            echo "<script>alert('".$str."');location.href='".$url."'</script>";
        }
       
        $this->_template('admin/export_data/order_memo_main_list');
    }
}