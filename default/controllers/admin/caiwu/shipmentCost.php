<?php
ini_set('memory_limit', '2048M');
set_time_limit(0);
header('Content-Type: text/html; Charset=utf-8');
class shipmentCost extends Admin_Controller{
const NO_UPDATE = 1;//未更新
CONST IS_UPDATE = 2;//已更新
    function __construct(){

        parent::__construct();
        $this->load->model(
            array(
                'caiwu/shipment_cost_model','order/orders_model','order/orders_products_model','shipment/shipment_model','caiwu/shipment_cost_exception_model','sharepage',
                'caiwu/shipment_cost_main_model','caiwu/shipment_cost_update_weight_model','slme_user_model','products/products_data_model','operate_log_model'//'manages_model'
            )
        );
        $this->load->library('phpexcel/PHPExcel','phpexcel/PHPExcel/Reader/Excel5.php');

    }
    /**
     * SKU重量使用状态
     * @return multitype:number
     */
    static function statusEdit(){
        return array(
            '1' => '未更新',
            '2' => '已更新',
        );
    }
    /**
     * 模版显示.新增
     */
    public function listShow(){
 
           
        $data=array();
        $where   = array();
        $string='';
        $orders_id_search                 = htmlspecialchars($this->input->get_post("orders_id_search"));//搜索条件
        $main_id_search                   = htmlspecialchars($this->input->get_post("main_id_search"));//搜索条件
        $shipping_code_search             = htmlspecialchars($this->input->get_post("shipping_code_search"));
        $orders_sku_search                = htmlspecialchars($this->input->get_post("orders_sku_search"));
        $weight_search_start              = htmlspecialchars($this->input->get_post("weight_search_start"));
        $weight_search_end                = htmlspecialchars($this->input->get_post("weight_search_end"));
        $shipping_start_date              = htmlspecialchars($this->input->get_post("shipping_start_date"));
        $shipping_end_date                = htmlspecialchars($this->input->get_post("shipping_end_date"));
        $shipmentAutoMatched_search       = htmlspecialchars($this->input->get_post("shipmentAutoMatched_search"));
        $buyer_country_code_search        = htmlspecialchars($this->input->get_post("buyer_country_code_search"));
        $shipment_name_search             = htmlspecialchars($this->input->get_post("shipment_name_search"));
        $import_start_date                = htmlspecialchars($this->input->get_post("import_start_date"));
        $import_end_date                  = htmlspecialchars($this->input->get_post("import_end_date"));
        $weight_differential_search_start = htmlspecialchars($this->input->get_post("weight_differential_search_start"));//重量差异百分比(大于等于)
        $weight_differential_search_end   = htmlspecialchars($this->input->get_post("weight_differential_search_end"));//重量差异百分比(小于等于)
        
        $data['search']['orders_id_search']                 = $orders_id_search;
        $data['search']['main_id_search']                   = $main_id_search;
        $data['search']['shipping_code_search']             = $shipping_code_search;
        $data['search']['orders_sku_search']                = $orders_sku_search;
        $data['search']['weight_search_start']              = $weight_search_start;
        $data['search']['weight_search_end']                = $weight_search_end;
        $data['search']['shipping_start_date']              = $shipping_start_date;
        $data['search']['shipping_end_date']                = $shipping_end_date;
        $data['search']['shipmentAutoMatched_search']       = $shipmentAutoMatched_search;
        $data['search']['buyer_country_code_search']        = $buyer_country_code_search;
        $data['search']['shipment_name_search']             = $shipment_name_search;
        $data['search']['import_start_date']                = $import_start_date;
        $data['search']['import_end_date']                  = $import_end_date;
        $data['search']['weight_differential_search_start'] = $weight_differential_search_start;
        $data['search']['weight_differential_search_end']   = $weight_differential_search_end;
        
        if(isset($orders_id_search) && !empty($orders_id_search)){//内单号
            $where['orders_id']= $orders_id_search;
            $string.="&orders_id_search=$orders_id_search";
            
        }
        if(isset($main_id_search) && !empty($main_id_search)){//批次号
            $where['main_id']= $main_id_search;
            $string.="&main_id_search=$main_id_search";
        
        }
        if(isset($shipping_code_search) && !empty($shipping_code_search)){//挂号码
            $where['shipping_code']= $shipping_code_search;
            $string.="&shipping_code_search=$shipping_code_search";
        }
        if(isset($orders_sku_search) && !empty($orders_sku_search)){//订单SKU
            $where['orders_sku']= $orders_sku_search;
            $string.="&orders_sku_search=$orders_sku_search";
        }
               
        if(isset($weight_search_start) && !empty($weight_search_start)){
            $where['weight >=']= $weight_search_start;
            $string.="&weight_search_start=$weight_search_start";
        }
        
        if(isset($weight_search_end) && !empty($weight_search_end)){
            $where['weight <=']= $weight_search_end;
            $string.="&weight_search_end=$weight_search_end";
        }
        
        if(isset($shipping_start_date) && !empty($shipping_start_date)){//发货开始时间
            $where['orders_shipping_time >=']= $shipping_start_date;
            $string.="&shipping_start_date=$shipping_start_date";
        }
        
        if(isset($shipping_end_date) && !empty($shipping_end_date)){//发货结束时间
            $where['orders_shipping_time <=']= $shipping_end_date;
            $string.="&shipping_end_date=$shipping_end_date";
        }
        
        if(isset($shipmentAutoMatched_search) && !empty($shipmentAutoMatched_search)){//匹配物流
            $where['shipmentAutoMatched']= $shipmentAutoMatched_search;
            $string.="&shipmentAutoMatched_search=$shipmentAutoMatched_search";
        }
        
        if(isset($buyer_country_code_search) && !empty($buyer_country_code_search)){//国家简称
            $where['buyer_country_code']= $buyer_country_code_search;
            $string.="&buyer_country_code_search=$buyer_country_code_search";
        }
        
        if(isset($shipment_name_search) && !empty($shipment_name_search)){//渠道名称
            $where['shipment_name']= $shipment_name_search;
            $string.="&shipment_name_search=$shipment_name_search";
        }
        
        if(isset($weight_differential_search_start) && !empty($weight_differential_search_start)){//重量差异
            $where['(weight-packetWeight)/packetWeight>=']= $weight_differential_search_start;
            $string.="&weight_differential_search_start=$weight_differential_search_start";
        }
        
        if(isset($weight_differential_search_end) && !empty($weight_differential_search_end)){//重量差异
            $where['(weight-packetWeight)/packetWeight<=']= $weight_differential_search_end;
            $string.="&weight_differential_search_end=$weight_differential_search_end";
        }
        
        if(isset($import_start_date) && !empty($import_start_date)){//导入开始时间
            $where['import_time >=']= $import_start_date;
            $string.="&import_start_date=$import_start_date";

        }
        
        if(isset($import_end_date) && !empty($import_end_date)){//导入结束时间
            $where['import_time <=']= $import_end_date;
            $string.="&import_end_date=$import_end_date";
        }
        $return_arr = array ('total_rows' => true );
        $per_page	= (int)$this->input->get_post('per_page');
        
        $url = admin_base_url('caiwu/shipmentCost/listShow?').$string;
        
        $cupage	= intval($this->config->item('site_page_num')); //每页显示个数

        $option	= array(
            'page'		   => $cupage,
            'per_page'	   => $per_page,
            'where'        => $where,
            'order'		   => "id asc",
        );

        $data['info']         = $this->shipment_cost_model->getAll($option,$return_arr); 

        $data['page'] = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
        
        foreach($data['info'] as $key=>$val){
            $shipmentOption['where']['shipmentID'] = $val->shipmentAutoMatched;
            $shipmentCode = (array)$this->shipment_model->getOne($shipmentOption);//获取物流方式
            $data['info'][$key]->shipmentAutoMatched = $shipmentCode['shipmentTitle'];
        }
        
        
        $this->_template('admin/caiwu/shipment_cost_list',array('data'=>$data,'page'=>$data['page'],'main_id_search'=>$main_id_search));
        
    }
    
    /**
     * ajax删除栏目数据
     */
    function ajaxDeleteData(){
        $id = $_REQUEST['id'];
        $options = '';
        $detailOptions='';
        $deleteFlag = true;
        $options = "id in (".$id.")";
        //事务开启
        $this->db->trans_begin();
        $deleteInfo = $this->shipment_cost_main_model->getAll($options);
        $result = $this->shipment_cost_main_model->delete($options);
        if(empty($result)){
            $deleteFlag = false;
            $this->db->trans_rollback();
        }
        $detailOptions = "main_id in (".$id.")";        
        $detailResult  = $this->shipment_cost_model->delete($detailOptions);//删除明细
        if(empty($detailResult)){
            $deleteFlag = false;
            $this->db->trans_rollback();
        }       
        //添加日志记录
        $addLogData = array();       
        if(!empty($deleteInfo)){
            foreach($deleteInfo as $val){
                $addLogData['operateUser'] = $this->user_info->id;
                $addLogData['operateTime'] = date('Y-m-d H:i:s');
                $addLogData['operateType'] = 'delete';
                $addLogData['operateMod']  = 'deleteShipmentMain';
                $addLogData['operateKey']  = $val->main_name;
                $addLogData['operateText'] = '批量删除物流对账批次:'.$val->main_name;
                $addLogRows = $this->operate_log_model->add($addLogData);
                if(empty($addLogRows)){
                    $deleteFlag = false;
                }  
            }           
        }else{
            $deleteFlag = false;
        }
        
        if($this->db->trans_status() === TRUE && $deleteFlag !=false){
            $this->db->trans_commit();//事务结束
        }
        if($deleteFlag){
            echo '删除成功';
        }else{
            echo '删除失败';
        }die();
    }
    
    /**
     * 删除对账明细
     */
    function ajaxDeleteDataDetail(){
        $id = $_REQUEST['id'];
        $detailOptions='';
        $deleteFlag = true;
        //事务开启
        $this->db->trans_begin();     
        $detailOptions = "id in (".$id.")";
        $deleteInfo = $this->shipment_cost_model->getAll($detailOptions);
        $detailResult  = $this->shipment_cost_model->delete($detailOptions);//删除明细
        if(empty($detailResult)){
            $deleteFlag = false;
            $this->db->trans_rollback();
        }   
        //添加日志记录
        $addLogData = array();
        if(!empty($deleteInfo)){
            foreach($deleteInfo as $val){
                $addLogData['operateUser'] = $this->user_info->id;
                $addLogData['operateTime'] = date('Y-m-d H:i:s');
                $addLogData['operateType'] = 'delete';
                $addLogData['operateMod']  = 'deleteShipmentDetail';
                $addLogData['operateKey']  = $val->shipping_code;
                $addLogData['operateText'] = '批量删除物流对账批次:'.$val->main_name;
                $addLogRows = $this->operate_log_model->add($addLogData);
                if(empty($addLogRows)){
                    $deleteFlag = false;
                }
            }
        }else{
            $deleteFlag = false;
        }
    
        if($this->db->trans_status() === TRUE && $deleteFlag !=false){
            $this->db->trans_commit();//事务结束
        }
        if($deleteFlag){
            echo '删除成功';
        }else{
            echo '删除失败';
        }die();
    }
    
    
    //删除重复的数据
    public function checkRepeatToUpdate(){
        $options = array(
            'select' => 'id,count(*) as total,shipping_code',
            'group_by'  => 'shipping_code'
        );       
        $data = $this->shipment_cost_model->getAll($options);
        $deleteArr = array();
//         echo $this->db->last_query();exit;
            foreach($data as $key=>$val){
                if($val->total >= 2){
                    $option = "id in (".$val->id.")";
                    $result = $this->shipment_cost_model->delete($option);
                }
            }       
    }
    
    
  //修复单品单件sku
    public function checkSkuInfo(){
        $data = $this->shipment_cost_model->getAll();
        //         echo $this->db->last_query();exit;
        foreach($data as $key=>$val){            
            $ordersProductsOption['where']['erp_orders_id'] = $val->orders_id;          
            $ordersProductsDataRows = $this->orders_products_model->getTotal($ordersProductsOption);//查询订单产品表数据总条数           
            $ordersProductsData = array();
            $options = array();
            $updateInfo = array();
            $updateInfo['orders_sku']='';
            if($ordersProductsDataRows==1){//单品
                $ordersProductsData = $this->orders_products_model->getOne($ordersProductsOption,true);//查询订单产品表数据   
                if($ordersProductsData['item_count']==1){ //单件                                                       
                    $updateInfo['orders_sku'] = $ordersProductsData['orders_sku'];
                }
            }
            $options['where']['id'] = $val->id;           
            $result = $this->shipment_cost_model->update($updateInfo,$options);
           
        }
    }
    
    /**
     * 物流对账
     */
    public function mainList(){
        //导入新增
        if(isset($_REQUEST['add']) && ($_REQUEST['add']=='add')){
            $phpExcel = new PHPExcel_Reader_Excel5;
            $extend = explode("." ,$_FILES['excelFile']["name"]);
            $val    = count($extend)-1;
            $extend = strtolower($extend[$val]);
            if($extend != 'xls'){
                echo '<script language="javascript">alert("文件格式不正确，请上传EXCEL文件");history.go(-1);</script>';
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
                    $data_array[$i]['shipping_code']=trim($sheet->getCell("A".$j)->getValue());//挂号码
                    $data_array[$i]['country']=trim($sheet->getCell("B".$j)->getValue());//目的地
                    $data_array[$i]['weight']=trim($sheet->getCell("C".$j)->getValue()); //实际重量
                    $data_array[$i]['shipment_name']=trim($sheet->getCell("D".$j)->getValue());//渠道名称
                    $data_array[$i]['have_not_cost']=trim($sheet->getCell("E".$j)->getValue());//不含挂号费
                    $data_array[$i]['is_cost']=trim($sheet->getCell("F".$j)->getValue());//挂号费
                    $data_array[$i]['discount']=trim($sheet->getCell("G".$j)->getValue());//通折
                    $data_array[$i]['not_discount']=trim($sheet->getCell("H".$j)->getValue());//非通折
                    $i++;
                }
            }
            //事务开启
            $this->db->trans_begin();    
            //导入的excel有数据
            $insertID = 0;
            if(isset($data_array) && !empty($data_array) ){               
               $data = array();
               $data['main_name']   = $main_name;//批次名
               $data['import_time'] = date('Y-m-d H:i:s');
               $data['import_user'] = $this->user_info->id;//登录用户id
               $insertID = $this->shipment_cost_main_model->add($data);
               unset($data);
            }
            if(empty($insertID)){                
                    $this->db->trans_rollback();                            
                echo '<script language="javascript">alert("导入excel失败");history.go(-1);</script>';
                exit;
            }
            $total_cost          = 0; //计费总运费
            $total_shipping_cost = 0; //理论总运费
            $total_weight        = 0; //计费总重量
            $total_packet_weight = 0; //理论总重量
            $total_num           = 0; //总条数             
            foreach($data_array as $key=>$val){
                $data                 = array();
                $checkOption          = array();
                $ordersOption         = array();
                $shipmentOption       = array();
                $ordersProductsOption = array();
        
                $checkOption['where']['shipping_code'] = trim($val['shipping_code']);
                $checkData = $this->shipment_cost_model->getOne($checkOption);//检测是否存在
                if($checkData){
                    $exceptionData = array();
                    $exceptionData['main_id']              = $insertID;
                    $exceptionData['main_name']            = $main_name;//批次名
                    $exceptionData['shipping_code']        = $val['shipping_code']; //挂号码(excel)
                    $exceptionData['country']              = $val['country'];  //目的地(excel)
                    $exceptionData['weight']               = $val['weight'];//计费重量(excel)
                    $exceptionData['shipment_name']        = trim($val['shipment_name']);  //渠道名称//(excel)
                    $exceptionData['import_time']          = date('Y-m-d H:i:s');//导入时间
                    $exceptionData['is_cost']              = $val['is_cost'];  //挂号费
                    $exceptionData['have_not_cost']        = $val['have_not_cost'];//不含挂号费
                    $exceptionData['discount']             = $val['discount'];//通折
                    $exceptionData['not_discount']         = $val['not_discount'];//非通折
                    $exceptionData['description']          = '跟踪号已导入';
                    $this->shipment_cost_exception_model->add($exceptionData);
                    unset($exceptionData);
                    continue;
                }
        
                $ordersOption['where']['orders_shipping_code'] = $val['shipping_code'];
                $ordersData     = (array)$this->orders_model->getOne($ordersOption);//查询订单表数据
                if(empty($ordersData)){
                    $exceptionData = array();
                    $exceptionData['main_id']              = $insertID;
                    $exceptionData['main_name']            = $main_name;//批次名
                    $exceptionData['shipping_code']        = $val['shipping_code']; //挂号码(excel)
                    $exceptionData['country']              = $val['country'];  //目的地(excel)
                    $exceptionData['weight']               = $val['weight'];//计费重量(excel)
                    $exceptionData['shipment_name']        = trim($val['shipment_name']);  //渠道名称//(excel)
                    $exceptionData['import_time']          = date('Y-m-d H:i:s');//导入时间
                    $exceptionData['is_cost']              = $val['is_cost'];  //挂号费
                    $exceptionData['have_not_cost']        = $val['have_not_cost'];//不含挂号费
                    $exceptionData['discount']             = $val['discount'];//通折
                    $exceptionData['not_discount']         = $val['not_discount'];//非通折
                    $exceptionData['description']          = '跟踪号对应erp内单号不存在';
                    $this->shipment_cost_exception_model->add($exceptionData);
                    unset($exceptionData);
                    continue;
                    continue;//没有订单数据则跳过
                }
        
                $ordersProductsOption['where']['erp_orders_id'] = $ordersData['erp_orders_id'];
                $ordersProductsDataRows = $this->orders_products_model->getTotal($ordersProductsOption);//查询订单产品表数据总条数
                $dataOne = array();
                $ordersProductsData = array();
                if($ordersProductsDataRows==1 ){//单品
                    $dataOne= $this->orders_products_model->getOne($ordersProductsOption,true);//查询订单产品表数据
                    if( $dataOne['item_count']==1){//单件
                        $ordersProductsData = $dataOne;
                    }
                }
        
                $cost = 0;//计费运费计算开始
                if(!empty($val['discount']) && empty($val['not_discount'])){//通折存在，非通折不存在((不含+挂号费)*通折)
                    $cost = ($val['have_not_cost']+$val['is_cost'])*$val['discount'];
                }elseif (empty($val['discount']) && !empty($val['not_discount'])){//通折不存在，非通折存在((不含*非通折)+挂号费)
                    $cost = $val['have_not_cost']*$val['not_discount']+$val['is_cost'];
                }elseif(empty($val['discount']) && empty($val['not_discount'])){
                    $cost = $val['have_not_cost']+$val['is_cost'];//不含挂号费+挂号费
                } 
                $data['main_id']              = $insertID;
                $data['main_name']            = $main_name;//批次名
                $data['shipping_code']        = $val['shipping_code']; //挂号码(excel)
                $data['orders_id']            = $ordersData['erp_orders_id'];    //内单号
                $data['orders_type']          = $ordersData['orders_type']; //订单类型
                $data['orders_sku']           = !empty($ordersProductsData)?$ordersProductsData['orders_sku']:'';  //订单SKU
                $data['erp_shippingCost']     = $ordersData['shippingCost']-0.35;  //理论运费,在理论运费上每单减掉0.35的处理费
                $data['orders_shipping_time'] = $ordersData['orders_shipping_time'];//订单发货时间
                $data['shipmentAutoMatched']  = $ordersData['shipmentAutoMatched'];  //订单匹配物流
                $data['buyer_country_code']   = $ordersData['buyer_country_code'];  //国家简称
                $data['country']              = $val['country'];  //目的地(excel)
                $data['weight']               = $val['weight'];//计费重量(excel)
                $data['packetWeight']         = $ordersData['packetWeight']; //理论重量
                $data['cost']                 = $cost; //计费运费(计算得出)
                $data['shipment_name']        = trim($val['shipment_name']);  //渠道名称//(excel)
                $data['import_time']          = date('Y-m-d H:i:s');//导入时间
                $data['is_cost']              = $val['is_cost'];  //挂号费
                $data['have_not_cost']        = $val['have_not_cost'];//不含挂号费
                $data['discount']             = $val['discount'];//通折
                $data['not_discount']         = $val['not_discount'];//非通折
                $retult = '';
                $retult = $this->shipment_cost_model->add($data);
                
                $total_cost          += $cost;//计费总运费;
                $total_shipping_cost += $ordersData['shippingCost']-0.35;// 理论总运费
                $total_weight        += $val['weight']; //计费总重量
                $total_packet_weight += $ordersData['packetWeight'];//理论总重量
                $total_num++;           //总条数                
                if(empty($retult)){
                    $this->db->trans_rollback();
                }
                unset($data);
            }          
            $updateOptions = array();
            $updateInfo    = array();
            $updateInfo['total_cost']          = $total_cost;
            $updateInfo['total_shipping_cost'] = $total_shipping_cost;
            $updateInfo['total_weight']        = $total_weight;
            $updateInfo['total_packet_weight'] = $total_packet_weight;
            $updateInfo['total_num']           = $total_num;
            $updateOptions['where']['id']      = $insertID;
            if($total_cost==0 && $total_shipping_cost==0 && $total_weight==0 && $total_packet_weight==0 && $total_num==0){
                $deleteOptions = 0;$rows = 0;
                $deleteOptions = "id =".$insertID."";
                $rows = $this->shipment_cost_main_model->delete($deleteOptions);//删除主表信息
                if(empty($rows)) $this->db->trans_rollback();
            }else{
                $rows = 0;
                $rows = $this->shipment_cost_main_model->update($updateInfo,$updateOptions);//更新主表信息
                if(empty($rows)) $this->db->trans_rollback();
            }
            if($this->db->trans_status() === TRUE){
                $this->db->trans_commit();//事务结束
            }
            //计算并更新均价
            $avgPriceOptions = array();
            $avgPrice    = '';//均价
            $avgPriceStr = '';//均价字符串
            $avgPriceOptions['select']  = array('sum(have_not_cost) as total_cost','sum(weight) as total_weight','discount','not_discount','shipment_name');
            $avgPriceOptions['where']['main_id'] = $insertID;
            $avgPriceOptions['group_by'] = 'shipment_name';
            $avgPriceData = $this->shipment_cost_model->getAll($avgPriceOptions);
            foreach($avgPriceData as $avgk =>$avgv){
                $discount = 0;//初始化折扣
                if(!empty($avgv->discount) && empty($avgv->not_discount)){//通折存在，非通折不存在
                    $discount = $avgv->discount;
                }elseif (empty($avgv->discount) && !empty($avgv->not_discount)){//通折不存在，非通折存在
                    $discount = $avgv->not_discount;
                }
                $avgPrice=($avgv->total_cost*$discount)/$avgv->total_weight;//各渠道:(不含挂号费之和*折扣)/计费重量之和
                $avgPriceStr.=$avgv->shipment_name.'的均价是:'.number_format($avgPrice,2).";<br/>";
            }
            $updateAvgPriceInfo = array();
            $updateAvgPriceOptions = array();
            $updateAvgPriceInfo['avg_price_str']       = $avgPriceStr;
            $updateAvgPriceOptions['where']['id']      = $insertID;
            $this->shipment_cost_main_model->update($updateAvgPriceInfo,$updateAvgPriceOptions);//更新主表信息           
        }
        
        $option     = array();
        $where      = array();
        $like       = array();
        $string     = '';
        $main_name_search   = htmlspecialchars($this->input->get_post("main_name_search"));//搜索条件
        $import_start_date  = htmlspecialchars($this->input->get_post("import_start_date"));
        $import_end_date    = htmlspecialchars($this->input->get_post("import_end_date"));       
        if(isset($main_name_search) && !empty($main_name_search)){//id
            $like['main_name']= $main_name_search;
            $string.="&main_name_search=$main_name_search";
        
        }
        if(isset($import_start_date) && !empty($import_start_date)){//导入开始时间
            $where['import_time >=']= $import_start_date;
            $string.="&import_start_date=$import_start_date";
        
        }
        
        if(isset($import_end_date) && !empty($import_end_date)){//导入结束时间
            $where['import_time <=']= $import_end_date;
            $string.="&import_end_date=$import_end_date";
        }
        $return_arr = array ('total_rows' => true );
        $per_page	= (int)$this->input->get_post('per_page');
        
        $url        = admin_base_url('caiwu/shipmentCost/mainList?').$string;
        
        $cupage	    = intval($this->config->item('site_page_num')); //每页显示个数
        
        $option	    = array(
            'page'		   => $cupage,
            'per_page'	   => $per_page,
            'where'        => $where,
            'like'         => $like,
            'order'		   => "id desc",
        );
        $data = $this->shipment_cost_main_model->getAll($option,$return_arr);
        foreach($data as $k=>$v){
            $userArr = array();
            $userArr = $this->slme_user_model->getInfoByUid($v->import_user);
            $data[$k]->import_users = (isset($userArr) && !empty($userArr))?$userArr['nickname']:'';
        }
        $page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
        $this->_template('admin/caiwu/shipment_cost_main',array('data'=>$data,'page'=>$page,'main_name_search'=>$main_name_search,'import_start_date'=>$import_start_date,'import_end_date'=>$import_end_date));
    }
   
    /**
     * 更新重量
     */
    public function updateWeightList(){
        $option                = array();
        $where                 = array();
        $searchArr             = array();
        $string                = '';
        $sku_search            = htmlspecialchars($this->input->get_post("sku_search"));//搜索条件
        $status                = htmlspecialchars($this->input->get_post("status_search"));
        
        
        $percent_search_start  = htmlspecialchars($this->input->get_post("percent_search_start"));
        $percent_search_end    = htmlspecialchars($this->input->get_post("percent_search_end"));
        
        $update_start_date     = htmlspecialchars($this->input->get_post("update_start_date"));
        $update_end_date       = htmlspecialchars($this->input->get_post("update_end_date"));
        
        if(isset($sku_search) && !empty($sku_search)){//sku
            $where['sku']= $sku_search;
            $string.="&sku_search=$sku_search"; 
            $searchArr['sku_search'] = $sku_search;
        }
        if( empty($status)){//默认未更新
            $where['status'] = self::NO_UPDATE;
            $string.="&status_search=".self::NO_UPDATE;
            $searchArr['status_search'] = self::NO_UPDATE;
        }
        
        if(isset($status) && !empty($status)){
            if($status==3){//等于3显示全部
                $string.="&status_search=$status";
                $searchArr['status_search'] = $status;
            }else{
                $where['status'] = $status;
                $string.="&status_search=$status";
                $searchArr['status_search'] = $status;
            }
        }
        
        if(isset($percent_search_start) && !empty($percent_search_start)){//重量差异百分比
            $where['(update_weight-erp_weight)/erp_weight >=']= $percent_search_start;
            $string.="&percent_search_start=$percent_search_start";
            $searchArr['percent_search_start'] = $percent_search_start;
        
        }
        
        if(isset($percent_search_end) && !empty($percent_search_end)){//重量差异百分比
            $where['(update_weight-erp_weight)/erp_weight <=']= $percent_search_end;
            $string.="&percent_search_end=$percent_search_end";
            $searchArr['percent_search_end'] = $percent_search_end;
        }
        
        if(isset($update_start_date) && !empty($update_start_date)){//更新时间
            $where['update_time >=']= $update_start_date;
            $string.="&update_start_date=$update_start_date";
            $searchArr['update_start_date'] = $update_start_date;
        
        }
        
        if(isset($update_end_date) && !empty($update_end_date)){//更新时间
            $where['update_time <=']= $update_end_date;
            $string.="&update_end_date=$update_end_date";
            $searchArr['update_end_date'] = $update_end_date;
        }
        $return_arr = array ('total_rows' => true );
        $per_page	= (int)$this->input->get_post('per_page');
        
        $url        = admin_base_url('caiwu/shipmentCost/updateWeightList?').$string;
        
        $cupage	    = intval($this->config->item('site_page_num')); //每页显示个数
        
        $option	    = array(
            'page'		   => $cupage,
            'per_page'	   => $per_page,
            'where'        => $where,
            'order'		   => "id desc",
        );
//         foreach($data as $k=>$v){//用户ID转换成中文名
//             $userArr = array();
//             $userArr = $this->slme_user_model->getInfoByUid($v->import_user);
//             $data[$k]->import_users = $userArr['nickname'];
//         }
        
        $data = $this->shipment_cost_update_weight_model->getAll($option,$return_arr);
        $page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
        $statusArr = array();
        $statusArr = self::statusEdit();
        $this->_template('admin/caiwu/shipment_cost_update_weight',array('data'=>$data,'page'=>$page,'searchArr'=>$searchArr,'statusArr'=>$statusArr));
    }
    
    
    /**
     * 计算重量
     */
    public function countWeight(){
        $options = array();
        $options['select']   = array('products_sku','products_weight');
        $options['where']['products_sku !=']  = '';
        $data = $this->products_data_model->getAll($options);//获取产品SKU
        
//          echo $this->db->last_query();echo "<hr/>";exit();
        foreach($data as $key=>$val){
            $detailOptions = array();
            $result = array();
            $count  = 0;//查询20条记录后返回的数组长度
            $detailOptions['select']              = 'weight';
            $detailOptions['where']['orders_sku'] = $val->products_sku;
            $detailOptions['per_page']            = 0;
            $detailOptions['page']                = 20;//查询20条
            $result = $this->shipment_cost_model->getAll($detailOptions);//查询物流对账详情记录
//             echo $this->db->last_query();echo "<hr/>";
             if(empty($result) || count($result)<5)continue;//查询结果集为空或少于5条记录则跳过
            $count     = count($result);//返回结果集长度
            $resultArr = array(); 
            foreach($result as $k=>$v){
                $resultArr[] = $v->weight;
            }
            $max    = 0; //最大值初始化
            $min    = 0; //最小值初始化
            $total  = 0;//除去最大值和最小值后的和初始化
            $avg    = 0; //平均值初始化
            $newArr = array();//出去最大值和最小值之后的数组
            $max    = array_search(max($resultArr),$resultArr); //最大值
            $min    = array_search(min($resultArr),$resultArr); //最小值            
            foreach($resultArr as $k=>$v ){
                if($k==$max || $k==$min)continue;
                $newArr[] = $v;
                $total += $v;
            }
            $avg = $total/count($newArr);
            if(empty($avg))continue;
            if(abs(($avg-$val->products_weight)/$val->products_weight)>=0.15){
              $weightData = array();
              $checkOption= array();
              $weightData['sku']            = $val->products_sku;
              $weightData['status']         = self::NO_UPDATE;//未更新状态
              $weightData['erp_weight']     = $val->products_weight;
              $weightData['update_weight']  = number_format($avg,3);
              $weightData['modify_user']    = $this->user_info->id;
              $weightData['modify_time']    = date('Y-m-d H:i:s');//计算更新后重量的时间

              $checkOption['where']['sku']  = $val->products_sku;
              $checkOption['status']         = self::NO_UPDATE;//未更新状态
              $checkResult = $this->shipment_cost_update_weight_model->getOne($checkOption,true);//检测是否存在未更新
              if(!empty($checkResult))continue;
              $this->shipment_cost_update_weight_model->add($weightData);  
            }
                
        }
        $this->updateWeightList();
//         echo $this->db->last_query();
    }
    
    
    /**
     * 批量删除sku重量记录
     */
    public function ajaxDeleteSkuWeight(){
        $id = $_REQUEST['id'];
        $options = "id in (".$id.")";
        $result = $this->shipment_cost_update_weight_model->delete($options);
        if($result){
            echo '删除成功';
        }else{
            echo '删除失败';
        }die();
    }
    
    /**
     * 批量更新重量
     */
    public function batchUpdateWeight(){
        $id = $_REQUEST['id'];
        $options = "id in (".$id.") and status=".self::NO_UPDATE;
        $result = $this->shipment_cost_update_weight_model->getAll($options);
        if(!empty($result)){
            $flag = true;
        }else{
            $flag = false;
        }
        //事务开启
        $this->db->trans_begin();
        $msg = '';
        foreach($result as $key=>$val){
            $updateRows         = 0;
            $updateStatusRows   = 0;
            $addLogRows         = 0;
            $updateData         = array();
            $updateOptions      = array();
            $updateData['products_weight'] = $val->update_weight;
            $updateOptions['where']['products_sku'] = $val->sku;            
            $updateRows = $this->products_data_model->update($updateData,$updateOptions);
            if(empty($updateRows)){
                $flag = false;
                $msg .= $val->sku .'更新重量失败,';
            }
            //产品日志表添加 记录
            $addLogData = array();
            $addLogData['operateUser'] = $this->user_info->id;
            $addLogData['operateTime'] = date('Y-m-d H:i:s');
            $addLogData['operateType'] = 'insert';
            $addLogData['operateMod']  = 'productsManage';
            $addLogData['operateKey']  = $val->sku;
            $addLogData['operateText'] = $val->sku."的重量从".$val->erp_weight."更新到".$val->update_weight;
            $addLogRows = $this->operate_log_model->add($addLogData);
            if(empty($addLogRows)){
                $flag = false;
                $msg .= $val->sku .'添加日志失败,';
            }
            $updateStatusRows = $this->shipment_cost_update_weight_model->update(array('status'=>self::IS_UPDATE,'update_user'=>$this->user_info->id,'update_time'=>date('Y-m-d H:i:s')),array('where'=>array('sku'=>$val->sku,'status'=>self::NO_UPDATE)));
            if(empty($updateStatusRows)){                
                $flag = false;
                $msg .= $val->sku .'更新状态失败,';
            }
        }
        if($this->db->trans_status() === TRUE && $flag === true){
            $this->db->trans_commit();//事务结束
        }else{
            $this->db->trans_rollback();
        }
        if($flag){
            echo '更新成功';
        }else{
            echo '更新失败,SKU:'.$msg;
        }die();
    }
    
    
    
    /**
     * 全部更新重量
     */
    public function allUpdateWeight(){
        $options = array();  //未更新
        $options['where']['status'] = self::NO_UPDATE;
        $result = $this->shipment_cost_update_weight_model->getAll($options);
        if(!empty($result)){
            $flag = true;
        }else{
            $flag = false;
        }
        //事务开启
        $this->db->trans_begin();
        $msg = '';
        foreach($result as $key=>$val){
            $updateRows         = 0;
            $updateStatusRows   = 0;
            $addLogRows         = 0;
            $updateData         = array();
            $updateOptions      = array();
            $updateData['products_weight'] = $val->update_weight;
            $updateOptions['where']['products_sku'] = $val->sku;
            $updateRows = $this->products_data_model->update($updateData,$updateOptions);
            if(empty($updateRows)){
                $flag = false;
                $msg .= $val->sku .',';
            }
           
            $addLogData = array();
            $addLogData['operateUser'] = $this->user_info->id;
            $addLogData['operateTime'] = date('Y-m-d H:i:s');
            $addLogData['operateType'] = 'insert';
            $addLogData['operateMod']  = 'productsManage';
            $addLogData['operateKey']  = $val->sku;
            $addLogData['operateText'] = $val->sku."的重量从".$val->erp_weight."更新到".$val->update_weight;
            $addLogRows = $this->operate_log_model->add($addLogData);
            if(empty($addLogRows)){
                $flag = false;
            }
           
            $updateStatusRows = $this->shipment_cost_update_weight_model->update(array('status'=>self::IS_UPDATE,'update_user'=>$this->user_info->id,'update_time'=>date('Y-m-d H:i:s')),array('where'=>array('sku'=>$val->sku,'status'=>self::NO_UPDATE)));
            if(empty($updateStatusRows)){
                $flag = false;
            }
        }
        if($this->db->trans_status() === TRUE && $flag === true){
            $this->db->trans_commit();//事务结束
        }else{
            $this->db->trans_rollback();
        }
        if($flag){
            echo '更新成功';
        }else{
            echo '更新失败,SKU:'.$msg;
        }die();
    }
    

    /**
     * 异常数据显示
     */
    public function exceptionListShow(){       
        $data=array();
        $where   = array();
        $string='';
        $main_id_search                   = htmlspecialchars($this->input->get_post("main_id_search"));//搜索条件
        $shipping_code_search             = htmlspecialchars($this->input->get_post("shipping_code_search"));
        $shipment_name_search             = htmlspecialchars($this->input->get_post("shipment_name_search"));
        $import_start_date                = htmlspecialchars($this->input->get_post("import_start_date"));
        $import_end_date                  = htmlspecialchars($this->input->get_post("import_end_date"));

        $data['search']['main_id_search']                   = $main_id_search;
        $data['search']['shipping_code_search']             = $shipping_code_search;       
        $data['search']['shipment_name_search']             = $shipment_name_search;
        $data['search']['import_start_date']                = $import_start_date;
        $data['search']['import_end_date']                  = $import_end_date;
       
    
     
        if(isset($main_id_search) && !empty($main_id_search)){//批次号
            $where['main_id']= $main_id_search;
            $string.="&main_id_search=$main_id_search";
    
        }
        if(isset($shipping_code_search) && !empty($shipping_code_search)){//挂号码
            $where['shipping_code']= $shipping_code_search;
            $string.="&shipping_code_search=$shipping_code_search";
        }
 
        if(isset($shipment_name_search) && !empty($shipment_name_search)){//渠道名称
            $where['shipment_name']= $shipment_name_search;
            $string.="&shipment_name_search=$shipment_name_search";
        }
    
        
    
        if(isset($import_start_date) && !empty($import_start_date)){//导入开始时间
            $where['import_time >=']= $import_start_date;
            $string.="&import_start_date=$import_start_date";
    
        }
    
        if(isset($import_end_date) && !empty($import_end_date)){//导入结束时间
            $where['import_time <=']= $import_end_date;
            $string.="&import_end_date=$import_end_date";
        }
        $return_arr = array ('total_rows' => true );
        $per_page	= (int)$this->input->get_post('per_page');
    
        $url = admin_base_url('caiwu/shipmentCost/listShow?').$string;
    
        $cupage	= intval($this->config->item('site_page_num')); //每页显示个数
    
        $option	= array(
            'page'		   => $cupage,
            'per_page'	   => $per_page,
            'where'        => $where,
            'order'		   => "id asc",
        );    
        $data['info']         = $this->shipment_cost_exception_model->getAll($option,$return_arr);    
        $data['page'] = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );

        $this->_template('admin/caiwu/shipment_cost_exception_list',array('data'=>$data,'page'=>$data['page'],'main_id_search'=>$main_id_search));
    
    }
    
    
    /**
     * 导出本批次异常数据
     */
    public function exceptionExportOut(){
        $mainID        = (isset($_GET['main_id']) && !empty($_GET['main_id']))?$_GET['main_id']:'';
        $selectOptions = array();
        $selectOptions['where']['main_id'] = $mainID;
        $data = $this->shipment_cost_exception_model->getAll($selectOptions);
        $filename='exceptionShipmentCost'.date('Y-m-d');
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        header("Content-Disposition:attachment;filename=".$filename.".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        $table = '';
        $table.='<html  xmlns:x="urn:schemas-microsoft-com:office:excel" ><table cellpadding="0" cellspacing="0" border="1" style="br:mso-data-placement:same-cell;">'.PHP_EOL;
        $table.='<thead>
            <tr>
            <td style="font-size:14px;font-weight:bold;padding:5px;">批次号'.iconv("gb2312","utf-8",'批次号').'</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">挂号码</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">目的地</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">计费重量</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">渠道名称</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">不含挂号费</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">挂号费</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">通折</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">非通折</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">异常原因</td>   
            </tr>
            </thead>'.PHP_EOL;
        foreach($data as $key =>$val){
            $table.='<tr><td>'.$val->main_name.'</td>
            		        <td>'.$val->shipping_code.'</td>
            		        <td>'.$val->country.'</td>
            		        <td>'.$val->weight.'</td>
            		        <td>'.$val->shipment_name.'</td>
            		        <td>'.$val->have_not_cost.'</td>
            		        <td>'.$val->is_cost.'</td>
            		        <td>'.$val->discount.'</td>
            		        <td>'.$val->not_discount.'</td>
            		        <td>'.$val->description.'</td>'.PHP_EOL;
        }
        $table.='</table></html>'.PHP_EOL;
        echo $table;
    
    }
    
    
    
    /**
     * 导出本批次重量差异大于百分之15的数据
     */
    public function exportOut(){
        $mainID        = (isset($_GET['main_id']) && !empty($_GET['main_id']))?$_GET['main_id']:'';
        $selectOptions = array();
        if(isset($weight_differential_search_start) && !empty($weight_differential_search_start)){//重量差异
            $where['(weight-packetWeight)/packetWeight>=']= $weight_differential_search_start;
            $string.="&weight_differential_search_start=$weight_differential_search_start";
        }
        $selectOptions['where']['(weight-packetWeight)/packetWeight>='] = 0.15;
        $selectOptions['where']['main_id']                              = $mainID;
        $data = $this->shipment_cost_model->getAll($selectOptions);
        $filename='shipmentCost'.date('Y-m-d');
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        header("Content-Disposition:attachment;filename=".$filename.".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        $table = '';
        $table.='<html  xmlns:x="urn:schemas-microsoft-com:office:excel" ><table cellpadding="0" cellspacing="0" border="1" style="br:mso-data-placement:same-cell;">'.PHP_EOL;
        $table.='<thead>
            <tr>          
            <td style="font-size:14px;font-weight:bold;padding:5px;">批次号</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">挂号码</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">内单号</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">订单类型</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">订单SKU</td>            
            <td style="font-size:14px;font-weight:bold;padding:5px;">订单发货时间</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">订单匹配物流</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">国家简称</td>            
            <td style="font-size:14px;font-weight:bold;padding:5px;">目的地</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">计费重量(kg)</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">理论重量</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">重量差异百分比</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">计费运费(元)</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">理论运费</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">渠道名称</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">导入时间</td>                        
            </tr>
            </thead>'.PHP_EOL;
        foreach($data as $key =>$val){
            $table.='<tr><td>'.$val->main_name.'</td>
            		        <td>'.$val->shipping_code.'</td>
            		        <td>'.$val->orders_id.'</td>
            		        <td>'.$val->orders_type.'</td>
            		        <td>'.$val->orders_sku.'</td>		        
            		        <td>'.$val->orders_shipping_time.'</td>
            		        <td>'.$val->shipmentAutoMatched.'</td>
            		        <td>'.$val->buyer_country_code.'</td>
            		        <td>'.$val->country.'</td>
            		        <td>'.$val->weight.'</td>
            		        <td>'.$val->packetWeight.'</td>          	            
            	            <td>'.number_format((($val->weight-$val->packetWeight)/$val->packetWeight)*100,2)."%".'</td>           	                      		        
            		        <td>'.$val->cost.'</td>
            		        <td>'.$val->erp_shippingCost.'</td>
            		        <td>'.$val->shipment_name.'</td>
            		        <td>'.$val->import_time.'</td></tr>'.PHP_EOL;
        }
        $table.='</table></html>'.PHP_EOL;
        echo $table;
        
    }
    
    
    /**
     * 导出所有未更新重量数据
     */
    public function exceptionWeightExportOut(){       
        $selectOptions = array();
        $selectOptions['where']['status'] = self::NO_UPDATE;//未更新
        $data = $this->shipment_cost_update_weight_model->getAll($selectOptions);
        $filename='exceptionUpdateWeight'.date('Y-m-d');
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        header("Content-Disposition:attachment;filename=".$filename.".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        $table = '';
        $table.='<html  xmlns:x="urn:schemas-microsoft-com:office:excel" ><table cellpadding="0" cellspacing="0" border="1" style="br:mso-data-placement:same-cell;">'.PHP_EOL;
        $table.='<thead>
            <tr>
            <td style="font-size:14px;font-weight:bold;padding:5px;">'.iconv("gb2312","utf-8",'SKU').'</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">erp重量</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">更新后重量</td>
            </tr>
            </thead>'.PHP_EOL;
        foreach($data as $key =>$val){
            $table.='<tr><td>'.$val->sku.'</td>
            		        <td>'.$val->erp_weight.'</td>
            		        <td>'.$val->update_weight.'</td>'.PHP_EOL;
        }
        $table.='</table></html>'.PHP_EOL;
        echo $table;
    
    }
 
}