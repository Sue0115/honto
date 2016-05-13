<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Orders_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
       
        
    }

    public function get_pick_order($options = array(),$is_array = false){

    	$options['select'] = array("{$this->_table}.*",'p.orders_sku','p.item_count','count(*) as num');

        $options['user_index'] = 'USE INDEX (IDX_ST_SH)';

    	$join[] = array($this->_table_pre.'orders_products p',"p.erp_orders_id={$this->_table}.erp_orders_id");

    	$options['join'] = $join;

        //拣货单类型
        $type = $options['type'];
        unset($options['type']);

        if($type == 1){//单品单件
            $options['group_by'] = 'erp_orders_id  HAVING num=1';
        }

        if($type == 2){//单品多件
            $options['group_by'] = 'erp_orders_id  HAVING num=1';
        }

        if($type == 3){//多品多件
            $options['group_by'] = 'erp_orders_id  HAVING num>1';
        }

    	if($is_array){
    		$data = $this->getAll2Array($options);
    	}else{
    		$data = $this->getAllObj($options);
    	}

    	return $data;

    }
	//改变订单表里的订单状态
    public function change_order_status($order_id,$orders_status,$uid=''){

    	$tof = false;

    	$options = array();

    	$data = array();

    	$options['where']['erp_orders_id'] = $order_id;

    	$options['where']['orders_status !='] = 5;

    	$data['orders_status'] = $orders_status;

    	if($orders_status == 4){
    		$data['orders_print_time'] = date('Y-m-d H:i:s');
    	}

    	if($orders_status == 5){
    		$data['orders_shipping_time'] = date('Y-m-d H:i:s');
    		$data['orders_shipping_user'] = $uid;
    	}

    	$tof = $this->update($data,$options);

    	return $tof;
    }

    //货找面单标记发货
    public function shipping_order_by_sku($data= array()){

        $result = array();

        $result['status'] = 0;

        $result['info'] = '数据为空';

        if(empty($data)){
            return $result;
        }

        $order_id = $data['order_id'];

        $pick_id  = $data['pick_id'];

        $sku = $data['sku'];

        $uid = $data['uid'];
        
        if(empty($sku)){
            $result['info'] = 'sku信息为空';
            return $result;
        }

        $this->load->model('order/pick_product_model');

        $this->load->model('stock/stock_detail_model');
        
        $this->load->model('stock_detail_operate_record_detail_model');
        
        //事务开始
        $this->db->trans_begin();

        //改变拣货单详情状态
        /** 不在此处修改拣货单的状态，挪动到该订单打印面单之后
        $tof_pick = $this->pick_product_model->change_status_is_shiped($order_id);

        if(empty($tof_pick)){
            $this->db->trans_rollback();
            $result['info'] = '标记拣货单详情状态为发货失败';
            return $result;
        }
/**/
       
        //改变订单的状态为已发货
        $tof_order = $this->change_order_status($order_id,'5',$uid);

        if(empty($tof_order)){
             $this->db->trans_rollback();
             $result['info'] = '标记订单状态为已发货失败';
            return $result;
        }

        //减库存
        $tof_stock = true;

        foreach ($sku as $k => $v) {
            
            $p_sku = $v['sku'];

            $num = $v['num'];

            $warehouse = $v['warehouse'];

            $product_id = $v['product_id'];

            //减掉相应库存
            $tof = $this->stock_detail_model->add_or_reduce_stock($p_sku,$num,$warehouse,'-');
            
            //获取该产品的实库存
            $stockArr=$this->stock_detail_model->getStockBySku($p_sku,$warehouse);
            if(!empty($stockArr)){
              $stock=$stockArr['actual_stock'];
            }else{
              $stock=0;
            }
			
            if(empty($tof)){
                 $tof_stock = false;
                 $this->db->trans_rollback();
                 $result['info'] = 'SKU：'.$p_sku.'减库存失败';
                 
                 return $result;
            }else{
              //减库存成功,添加库存进出记录，erp_stock_detail_operate_record_detail 表
              $reData=array();
              $reData=array(
                'operate_type' => 'out',
              	'product_id'   => $product_id,
                'operate_count'=> $num,
              	'stock'		   => $stock,
                'operate_time' => date('Y-m-d H:i:s',time()),
              );
              
              $this->stock_detail_operate_record_detail_model->add($reData);
              
            }
        }
        
        if($tof_stock && $this->db->trans_status() === TRUE){
            $this->db->trans_commit();//事务结束
            
            $result['info'] = '标记发货成功';
            $result['status'] = 1;
            
        }

        return $result; 

    }

    public function get_order_remark($order_id){

        $remark = '';

        if($order_id > 0){
            $data = $this->getOne(array('erp_orders_id'=>$order_id),true);

            if(!empty($data) && !empty($data['orders_remark'])){
                $remark = $data['orders_remark'];
            }
        }

        return $remark;
    }

    public function get_order_info_and_shipment_info($shipping_code){

        $options = array();

        $where = array();

        $options['select'] = array($this->_table.'.*','s.shipmentCategoryID','s.shipmentTitle','s.shipmentID','s.shipmentScanMethod');

        $where['orders_shipping_code'] = trim($shipping_code);

        $where['orders_is_join'] = 0;

        $options['where'] = $where;

        $join[] = array($this->_table_pre.'shipment s',$this->_table.'.shipmentAutoMatched=s.shipmentID');

        $options['join'] = $join;

        $data = $this->getAll2Array($options);

        return $data;
    }

    
    //根据订单中的追踪码获取订单详情和订单所属物流的信息
	public function get_order_info_and_shipment_info_one($shipping_code){

        $options = array();

        $where = array();

        $options['select'] = array($this->_table.'.*','s.shipmentCategoryID','s.shipmentTitle','s.shipmentID','s.shipmentScanMethod','s.shipmentNeedTrackingCode');

        $where['orders_shipping_code'] = trim($shipping_code);

        $where['orders_is_join'] = 0;

        $options['where'] = $where;

        $join[] = array($this->_table_pre.'shipment s',$this->_table.'.shipmentAutoMatched=s.shipmentID');

        $options['join'] = $join;

        $data = $this->getOne($options,true);

        return $data;
    }
    
   //根据buyer_id获取订单详情
   public function getOrderInfoByID($orderID,$filed=""){
      $option = array();
      if($filed!==''){
        $option['select'] = array('sales_account');
      }
      $where = array();
      $where['buyer_id'] = $orderID;
      $option['where'] = $where;
      return $this->getOne($option,true);
   }

   //根据订单号获取订单信息详情
   public function get_orders_info($orderID){
      $option = array();
      $where = array();
      $where['erp_orders_id'] = $orderID;
      $option['where'] = $where;
      return $this->getOne($option,true);
   }

    //查找状态为已通过并且追踪号为空的订单
    public function info($shipping_id){
        $options = array();

        $where = array();

        $options['select'] = array("erp_orders_id","shipmentAutoMatched",'orders_status','orders_shipping_code','orders_is_backorder','orders_is_join');

        $where['orders_status <=']=3;

        $where['orders_shipping_code']='';

        $where['shipmentAutoMatched']=$shipping_id;

        //$where['orders_is_backorder']=0;

        $where['orders_is_join']=0;

        $options['where']=$where;

        $options['limit'] = 500;

        $data = $this->getAll2Array($options);
        
        return $data;

    }

    //更新订单物流追踪号
    public function update_order_track_number($track_number,$order_id)
    {
        $this->db->set('orders_shipping_code',"'$track_number'",false);

        $this->db->where('erp_orders_id', $order_id);

        $tof = $this->db->update('erp_orders');

        $tof=$this->db->affected_rows();

        return $tof;
    }

    public function insert_lazada($order_info,$cur_value=0.2759)
    {
        $data['ebay_orders_id'] = $order_info['OrderId'];

        $data['buyer_id'] = $order_info['OrderNumber'];

        $data['buyer_name'] = $order_info['CustomerFirstName'];

        if(!empty($order_info['CustomerLastName']))//名字拼接
        {
            $data['buyer_name'] = $order_info['CustomerFirstName'].$order_info['CustomerLastName'];
        }

        $data['pay_method'] = $order_info['PaymentMethod'];

        if(!empty($cur_value))
        {
            $data['currency_value'] = 1/$cur_value; //要变换成美元对马来西亚币种的汇率
        }

        if(!empty($order_info['Remarks'])) {

            $data['orders_remark'] = $order_info['Remarks'];
        }

        $data['orders_paid_time'] = $order_info['CreatedAt'];//设为付款时间

        //$data[''] = $order_info['UpdatedAt'];//数据库字段未知
        $data['ShippingServiceSelected'] = "dropshipping";

        $data['orders_export_time'] = date("Y-m-d H:i:s",time());//订单导入数据库时间

        $data['buyer_phone'] = $order_info['AddressShipping']['Phone'];

        if(empty($order_info['Phone1'])&&!empty($order_info['Phone2'])) {//电话1不存在，电话2存在

            $data['buyer_phone'] = $order_info['AddressShipping']['Phone2'];
        }

        $data['buyer_address_1'] = $order_info['AddressShipping']['Address1'];

        if(!empty($order_info['AddressShipping']['Address2'])) {
            $data['buyer_address_2'] = $order_info['AddressShipping']['Address2'];
        }

        $data['buyer_city'] = $order_info['AddressShipping']['City'];

        //$data[''] = $order_info['AddressShipping']['Ward'];//数据库字段未知

        //$data[''] = $order_info['AddressShipping']['Region'];//数据库字段未知

        $data['buyer_zip'] = $order_info['AddressShipping']['PostCode'];

        $data['buyer_country'] = $order_info['AddressShipping']['Country'];

        //$data[''] = $order_info['NationalRegistrationNumber'];//数据库字段未知

        $data['orders_type'] = 15;

        $tof = $this->add($data);

        return $tof;
    }

    public function getIdByEid($id,$account)
    {
        $option = array();

        $where = array();

        //$where['orders_type'] = 15;

        $where['buyer_id'] = $id;
        $where['sales_account']=$account;
  
        $option['where'] = $where;

        return $this->getOne($option,true);
    }

    // 查找出需要标记的订单
    public function getLazadaTacking($sales_account)
    {
        $options = array();
        $options['select'] = array($this->_table.'.erp_orders_id',$this->_table.'.orders_shipping_code',$this->_table.'.shipmentAutoMatched');
        $where['orders_status <=']=5;
        $where['orders_status >=']=3;
        $where['orders_shipping_code !=']='';
        $where['orders_type ']=15;
        $where['ebayStatusIsMarked '] = 0;
        $where['orders_is_join '] =0;
        $where['orders_export_time >']=  date('Y-m-d H:i:s',strtotime('-15 day'));
        $where['orders_export_time <='] = date('Y-m-d H:i:s',strtotime('-1 day'));
      //  $where['orders_export_time <='] ='2015-11-02 16:30:54';
       // $where['orders_is_split !=']=1;
        //$where['erp_orders_id'] = 4959925;
        $where['sales_account'] = $sales_account;
       // $options['limit'] = 1;
        $options['where']=$where;
        $data = $this->getAll2Array($options);
        return $data;
    }
    //根据内单号 查找出订单对应的orderlineitemid
    public function getLazadaOrderItemId($erp_orders_id)
    {
        $options = array();

        $options['select'] = array($this->_table_pre.'orders_products','orderlineitemid');

        $where['erp_orders_id ']=$erp_orders_id;


        $options['where']=$where;


        $data = $this->getAll2Array($options);

        return $data;
    }

    public function upDataOrderByShip($erp_orders_id)
    {
        $data = array(
            'ebayStatusIsMarked' => 1,
        );

        $this->db->where('erp_orders_id', $erp_orders_id);

        $tof = $this->db->update('erp_orders', $data);

        $tof=$this->db->affected_rows();

        return $tof;
    }

    public function lazadaOrderOperating_log($type,$operating,$success)
    {
        $data = array(
            'type' => $type ,
            'operating' =>$operating ,
            'success' => $success,
            'operating_time'=> date("Y-m-d H:i:s",time()),
        );

        $this->db->insert('erp_lazada_operating_log', $data);
    }

    // 根据$erp_orders_id 获取 shipmentLazadaCodeID
    public function lazadaOrderGetShipName($erp_orders_id)
    {
        $options = array();

        $where = array();

        $options['select'] = array('s.shipmentLazadaCodeID');

        $where['erp_orders_id'] = trim($erp_orders_id);

        $options['where'] = $where;

        $join[] = array($this->_table_pre.'shipment s',$this->_table.'.shipmentAutoMatched=s.shipmentID');

        $options['join'] = $join;

        $data = $this->getOne($options,true);

        return $data;
    }
    
    /**
     * 查询订单是否已经拉取过
     * @param unknown $id
     * @return Ambigous <Ambigous, unknown>
     */
    public function getErpIdByDhOrderid($id)
    {
        $option = array();
    
        $where = array();
    
        //$where['orders_type'] = 15;
    
        $where['buyer_id'] = $id;
    
        $option['where'] = $where;
    
        return $this->getOne($option,true);
    }
    
    /**
     * 获取需要上传的敦煌订单
     * @param unknown $sales_account
     * @return unknown
     */
    public function getDhgateTrackNo($sales_account)
    {
        $options = array();
        $options['select'] = array($this->_table.'.erp_orders_id',$this->_table.'.ebay_orders_id',$this->_table.'.buyer_id',$this->_table.'.orders_shipping_code',$this->_table.'.shipmentAutoMatched');
         $where['orders_status <=']=5;
         $where['orders_status >=']=3;
         $where['orders_shipping_code !=']='';  //跟踪号
         $where['orders_type ']=16;
         $where['ebayStatusIsMarked '] = 0;
         $where['orders_is_join'] = 0;
         $where['sales_account'] = $sales_account;
        $options['where']=$where;
        $data = $this->getAll2Array($options);   
        return $data;
    }
    
    /**
     * 根据内单号 查找出订单对应的orderlineitemid
     * @param unknown $erp_orders_id
     * @return unknown
     */
    public function getDhgateOrderItemId($erp_orders_id){
        $options = array();
    
        $options['select'] = 'orderlineitemid';
    
        $where['erp_orders_id ']=$erp_orders_id;
    
    
        $options['where']=$where;
   
        $data = $this->getAll2Array($options);
    
        return $data;
    }
    
    /**
     * 根据$erp_orders_id 获取 shipmentDhgateCodeID
     * @param unknown $erp_orders_id
     * @return Ambigous <Ambigous, unknown>
     */
    public function dhgateOrderGetShipName($erp_orders_id) {
        $options = array();
    
        $where = array();
    
        $options['select'] = array('s.shipmentDhgateCodeID');
    
        $where['erp_orders_id'] = trim($erp_orders_id);
    
        $options['where'] = $where;
    
        $join[] = array($this->_table_pre.'shipment s',$this->_table.'.shipmentAutoMatched=s.shipmentID');
    
        $options['join'] = $join;
    
        $data = $this->getOne($options,true);
    
        return $data;
    }
    
 
    /**
     * dh更新买家确认收获时间
     */
    public function UpdateDhBuyersReceivingTime($data,$buyerId){
        $options = array();       
        $options['where']['buyer_id']   = $buyerId;      
       return $this->update($data,$options);
    }
    
    /**
     * 获取需要标记发货的cdiscount订单
     * @param unknown $sales_account
     * @return unknown
     */
    public function getCdiscountTrackNo($sales_account)
    {
        $options = array();
        $options['select'] = array($this->_table.'.erp_orders_id',$this->_table.'.ebay_orders_id',$this->_table.'.buyer_id',$this->_table.'.orders_shipping_code',$this->_table.'.shipmentAutoMatched',$this->_table.'.orders_ship_fee');
        $where['orders_status <=']=5;
        $where['orders_status >=']=3;        
        $where['orders_type ']=17;//Cdiscount 的type是17
        $where['ebayStatusIsMarked '] = 0;
        $where['orders_is_join'] = 0;
        $where['sales_account'] = $sales_account;
        $options['where']=$where;
        $data = $this->getAll2Array($options);
        return $data;
    }
    
    
    /**
     * 捡货异常订单查询
     * @param unknown $sql
     */
    public function getExceptionOrderInfo($sql){
       return $this->result_array($sql); 
    }
	/*
	*异常订单sql anuthow@he
	*/
	public function oddsql(){
	$oddsql = "SELECT ok.product_sku,ok.orders_warehouse_id,d.products_location,actual_stock 
FROM erp_stock_detail de
INNER JOIN (
	SELECT pick.product_sku,o.orders_warehouse_id
	FROM erp_orders o
	INNER JOIN (
		SELECT COUNT(product_sku) AS SUM, orders_id, product_sku 
		FROM erp_pick_product 
		WHERE `create_time` >= UNIX_TIMESTAMP(NOW() - INTERVAL 15 DAY) AND 
		status in(8,9) 
		GROUP BY orders_id, product_sku 
		HAVING SUM>=1
	) pick ON o.erp_orders_id = pick.orders_id 
WHERE orders_status in (1,2,4)
	GROUP BY product_sku,o.orders_warehouse_id
) ok ON ok.product_sku = de.products_sku  AND ok.orders_warehouse_id = de.stock_warehouse_id
INNER JOIN erp_products_data d ON ok.orders_warehouse_id = d.product_warehouse_id 
	AND ok.product_sku = d.products_sku 
WHERE `actual_stock` BETWEEN 1 AND 10 ORDER BY orders_warehouse_id, products_location";
				return $oddsql;
  }
  /*
  *查出sku下所有异常订单
  */
  public function abnormalorders(){
	  $orders_sql = "SELECT ok.product_sku,ok.orders_id
		FROM erp_stock_detail de
		INNER JOIN (

		SELECT pick.product_sku, o.orders_warehouse_id, pick.orders_id
		FROM erp_orders o
		INNER JOIN (

		SELECT COUNT( product_sku ) AS SUM, orders_id, product_sku
		FROM erp_pick_product
		WHERE `create_time` >= UNIX_TIMESTAMP( NOW( ) - INTERVAL 15
		DAY )
		AND
		STATUS IN ( 8, 9 )
		GROUP BY orders_id, product_sku
		HAVING SUM >=1
		)pick ON o.erp_orders_id = pick.orders_id
		WHERE orders_status
		IN ( 1, 2, 4 )
		)ok ON ok.product_sku = de.products_sku
		AND ok.orders_warehouse_id = de.stock_warehouse_id
		INNER JOIN erp_products_data d ON ok.orders_warehouse_id = d.product_warehouse_id
		AND ok.product_sku = d.products_sku
		WHERE `actual_stock`
		BETWEEN 1
		AND 10
		ORDER BY orders_warehouse_id, products_location";
		return $orders_sql;
  }
  //查出拆分订单的主订单
  public function get_main_orders($is_split = ''){
	  
	  if($is_split){
		  $sql = "SELECT erp_orders_id,orders_is_join,orders_is_split FROM erp_orders WHERE erp_orders_id IN (".$is_split.") AND orders_is_join =8 and orders_is_split=0";
		  
		  return $this->result_array($sql);
		  
	  }
	  
  }
  //根据主订单合并规则反向查出拆分后的子订单号  确保反向查出拆分后的子订单号orders_is_join =0 and orders_is_split=1 (拆分之后的子订单)
  public function get_split_orders($split = ''){
	  
	  if($split){
		  
		  $sql = "SELECT p.erp_orders_id,p.ebay_orders_id FROM erp_orders_products p ,erp_orders o WHERE p.ebay_orders_id =".$split." and p.erp_orders_id= o.erp_orders_id and o.orders_is_join =0 and o.orders_is_split=1 GROUP BY erp_orders_id";
		  
		  return $this->result_array($sql);
	  }
	  
  }
  
}
	

/* End of file Orders_model.php */
/* Location: ./defaute/models/order/Orders_model.php */