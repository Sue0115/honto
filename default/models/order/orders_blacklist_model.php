<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 黑名单客户模型类
 * 
 */
class Orders_blacklist_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //获取黑名单erp_orders_blacklist表中黑名单数据   
    public function get_blacklist(){
		
       $options = array(); 
	   
	   $result=$this->getOne($options,true);
	   
	   return $result;
    }
    
    //获取订单表erp_orders黑名单客户的订单数据
    public function get_orders_blacklist($time = ''){
		
		if(!$time){
			
			$time = date('Y-m-d H:i:s',time()-3600*24*365);     //1年时间
		}
		
        $options = array();
		
		$this->_table = 'erp_orders';    
		
		$options['select'] = array('erp_orders_id','orders_type','buyer_name',"buyer_id",'buyer_zip','buyer_email','orders_export_time','sales_account');

        $where['orders_is_refund'] = '1';   //退款订单
		
		$where['orders_is_join'] = '0';   //合并订单
		
		$where['orders_is_backorder'] = '0';   //不欠货
		
		$where['orders_print_time >'] = $time;      //订单打印时间1年以内
		
		$options['where']=$where;
		
        $data = $this->getAll2Array($options);

        return $data;
    }
	//获取黑名单表erp_orders_blacklist黑名单客户数据
    public function get_allorders_blacklist($options = ''){
		
		if(!$options){
			
			$options = array();
		
		    $options['where']['status'] = 0;
			
		}
		
		$options['group_by'] = "buyer_name,buyer_id,buyer_zip,orders_type";   //去重 取唯一
		
		if(isset($options['where']['status'])){
			
			if($options['where']['status'] ==2 || $options['where']['status'] ==1){    //取导入数据的时候  不进行分组
			
			$options['group_by'] = "erp_orders_id";   //去重 取唯一
		  }
			
		}
		
        $data = $this->getAll2Array($options);

        return $data;
    }
	
	//一次性多条add黑名单客户信息记录到erp_orders_blacklist
    function add_black_list($add_values){
    	
		$add_sql = "INSERT INTO `erp_orders_blacklist` (`erp_orders_id`,`orders_type`,`buyer_id`,`buyer_name`,`buyer_zip`,`buyer_email`,`status`,`orders_export_time`,`times`,`sales_account`,`color_type`,`orders_count`) VALUES";
		
		$sql = $add_sql.$add_values;
		
        $ret = $this->db->query($sql);
		
        return true;
    }
	
	//确认为黑名单客户操作  (status=1,删除黑名单操作)
    public function change_black_status($orderid = '',$status = ''){

		if($orderid){
			
		if(!$status){
			
			$ret = $this->db->query("UPDATE `erp_orders_blacklist` SET `status` = 1 WHERE `erp_orders_id` IN (".$orderid.") ");

		}else{
			
			$ret = $this->db->query("UPDATE `erp_orders_blacklist` SET `status` = ".$status." WHERE `erp_orders_id` IN (".$orderid.") ");
		}
			
    	return $ret;
		
		}else{
			return false;
		}
    }
	//获得要导出的数据
	function get_orders_id($orderid = '',$orders_type = '',$status = ''){
		if($orderid){
			
		    return $this->result_array("select * from erp_orders_blacklist where erp_orders_id in(".$orderid.")");
			
		}else if($orders_type !== 'all' && $status !== ''){
			
			return $this->result_array("select * from erp_orders_blacklist where orders_type=".$orders_type." and status=".$status." group by buyer_name,buyer_id,buyer_zip,orders_type");
		}else if($orders_type == 'all' && $status !== ''){
			
			return $this->result_array("select * from erp_orders_blacklist where status=".$status." group by buyer_name,buyer_id,buyer_zip,orders_type");
		}else if($orders_type !== 'all' && $status == ''){
				
				return $this->result_array("select * from erp_orders_blacklist where orders_type=".$orders_type." group by buyer_name,buyer_id,buyer_zip,orders_type");
		}else if($orders_type == 'all' && $status == ''){
			
			return $this->result_array("select * from erp_orders_blacklist where 1 group by buyer_name,buyer_id,buyer_zip,orders_type");
		}
	}
	
	//清空待处理黑名单数据
	function del_blacklist(){
		
		$sql = "DELETE FROM erp_orders_blacklist where status=0";
		
		$ret = $this->db->query($sql);//清空待处理黑名单数据  留确认为黑名单status=1和导入为黑名单数据status=2
		
	}
	//导入时批量insert数据
    function import_black_list($add_values){
    	
		$add_sql = "INSERT INTO `erp_orders_blacklist` (`erp_orders_id`,`orders_type`,`buyer_id`,`remark`,`status`,`buyer_name`,`buyer_email`,`buyer_zip`,`times`,`color_type`,`orders_count`) VALUES";
		
		$sql = $add_sql.$add_values;
		
        $ret = $this->db->query($sql);
		
        return true;
    }
	//查看erp_orders_blacklist表是否存在导入的数据  buyer_id相同and remark不相同则覆盖
	function get_identical_list($buyer_id){
		
        $options = array();
		
		$this->_table = 'erp_orders_blacklist';
		
		$where['buyer_id'] = $buyer_id;   //buyer_id收货人id相同
		
		$options['where']=$where;
		
        $data = $this->getAll2Array($options);

        return $data;
		
		
	}
	//查看erp_orders_blacklist表中对应buyer_id的信息
	function get_buyerid_info($buyer_id){
		
        $options = array();
		
		$this->_table = 'erp_orders'; //erp_orders表
		
		$where['buyer_id'] = $buyer_id;   //buyer_id收货人id相同
		
		$options['where']=$where;
		
        $data = $this->getAll2Array($options);

        return $data;
		
		
	}
	
	//导入的覆盖前面的数据
	function edit_identical_list($erp_orders_id,$identical_id,$remark,$buyer_name,$buyer_email,$buyer_zip,$sales_account,$color_type,$orders_count,$times){
		
		$tof = '';
		
		if($identical_id){
		
		$sql = "update erp_orders_blacklist set erp_orders_id='".$erp_orders_id."',remark='".$remark."',buyer_name='".$buyer_name."',buyer_email='".$buyer_email."',buyer_zip='".$buyer_zip."',sales_account='".$sales_account."',color_type=".$color_type.",orders_count=".$orders_count.",times=".$times." where buyer_id='".$identical_id."'";
		
		$tof = $this->db->query($sql);
		
		}
    	return $tof;
		
		
	}
	//黑名单表erp_orders_blacklist黑名单客户数据
    public function allorders_blacklist($options = ''){
		
		$options['group_by'] = "buyer_name,buyer_id,buyer_zip,orders_type";   //去重 取唯一
		
		$where['status <>'] = 3;
		
		$options['where']=$where;
		
        $data = $this->getAll2Array($options);

        return $data;
    }
	//查出条件下的总数
	public function get_count_buyer($email = '',$id = '',$zip = '',$buyer_name = ''){
		
        $options = array();
		
		$this->_table = 'erp_orders';    
		
		$options['select'] = array('count(*) as num,orders_type');
		
		if($email !== '' && $email !== ' '){
			
			$where['buyer_email'] = $email; 
		}
		if($id !== '' && $id !== ' '){
			$where['buyer_id'] = $id; 
		}
		if($buyer_name !== '' && $buyer_name !== ' '){
			$where['buyer_name'] = $buyer_name; 
		}     
		
		$where['orders_is_join'] = '0';   //合并订单
		
		$where['orders_is_backorder'] = '0';   //不欠货
		
		$options['where']=$where;
		
        $data = $this->getAll2Array($options);

        return $data;
		
	}
	//查出条件下的退款总数
	public function get_refund_buyer($email = '',$id = '',$zip = '',$buyer_name = ''){
		
		$time = date('Y-m-d H:i:s',time()-3600*24*365);     //1年时间
		
		$data =  array();
		
		$sql = "SELECT COUNT( * ) as num
				FROM erp_moneyback m
				INNER JOIN erp_orders o ON o.orders_is_join =0
				AND o.orders_is_backorder =0
				WHERE m.erp_orders_id = o.erp_orders_id
				AND m.moneyback_status =  'refunded' AND o.orders_print_time >  '".$time."'";   //查询退款订单数
		
		if($email !== ''){
			
			$sql = str_replace("o.orders_is_join =0","o.orders_is_join =0 AND o.buyer_email ='" . $email . "' ",$sql);
		}
		if($id !== ''){
			$sql = str_replace("o.orders_is_join =0","o.orders_is_join =0 AND o.buyer_id ='" . $id . "' ",$sql);
		}
		if($buyer_name !== ''){
			$sql = str_replace("o.orders_is_join =0","o.orders_is_join =0 AND o.buyer_name ='" . $buyer_name . "' ",$sql);
		}     
	    if($email !== '' || $id !== '' || $buyer_name !== ''){
			
        $data = $this->result_array($sql);
		}
        return $data;
		
	}
	//确认为的黑名单客户  status=1
	public function get_success_blacklist(){
		
        $options = array();
		
		$this->_table = 'erp_orders_blacklist';
		
		$options['select'] = array('erp_orders_id','buyer_id','buyer_name','buyer_email','color_type');
		
		$where['status'] = 1;   //确认为的黑名单客户
		
		$options['where']=$where;
		
        $data = $this->getAll2Array($options);

        return $data;
		
		
	}
	//erp_orders 黑名单客户订单增加备注
	public function up_blacklist_remark($buyer_email,$buyer_name,$buyer_id){
		
		$tof = '';
		
		if($buyer_email !== ''){
		
		    $sql = "update erp_orders set orders_remark='黑名单客户订单;' where buyer_email in (".$buyer_email.")";
		
		}else if($buyer_name !== ''){
			
			$sql = "update erp_orders set orders_remark='黑名单客户订单;' where buyer_name in (".$buyer_name.")";
		}else if($buyer_id !== ''){
			
			$sql = "update erp_orders set orders_remark='黑名单客户订单;' where buyer_id in (".$buyer_id.")";
		}
		if($sql){
			
			$tof = $this->db->query($sql);
			
		}
    	
		return $tof;
		
	}
	
	//改变重新计算的黑名单的计算结果(总数和退款数)
    public function change_order_num($color,$color_type,$count,$refund,$order_id = '',$buyer_email = '',$id = '',$name = ''){

    	$tof = false;

    	$options = array();

    	$data = array();
		
		$this->_table = 'erp_orders_blacklist';  

    	$options['where']['erp_orders_id'] = $order_id;
		
		$options['where']['color_type'] = $color_type;
		
		if($buyer_email){   //根据值判断是哪个来搜索计算的数据
			
			$options['where']['buyer_email'] = $buyer_email;
			
		}else if($name){
			
			$options['where']['buyer_name'] = $name;
		}else if($id){
			
			$options['where']['buyer_id'] = $id;
		}
		if($color){
			
			$data['color_type'] = $color;
		}

    	$data['times'] = $refund;
		
		$data['orders_count'] = $count;
		
		$data['orders_export_time'] = date('Y-m-d H:i:s',time());
        
    	$tof = $this->update($data,$options);

    	return $tof;
    }
	
	function return_array($sql){
		if($sql){
			return $this->result_array($sql);
		}
	}
	function return_query($sql){
		if($sql){
			return $this->db->query($sql);
		}
	}
	
}

/* End of file Pick_model.php */
/* Location: ./defaute/models/order/Pick_model.php */