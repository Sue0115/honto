<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Pick_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //根据pick_id获取拣货单信息
    public function get_info_by_pick_id($pick_id){
       $options = array(); 

	   $options['where']['id'] = $pick_id; 
	   
	   $result=$this->getOne($options,true);
	   
	   return $result;
    }
    
    //改变拣货单状态为包装完成
    function change_pickStatus_is_packaging($pick_id){
    	
        $options = array(); 

		$options['where']['id'] = $pick_id; 
		
		$data = array(); 
		
		$data['status'] = 4; 
		
		$data['pick_end_time'] = time(); 
		
		$tof = $this->update($data,$options);
        
        return $tof;
    }
    
	//改变拣货单状态为已发货
    function change_pickStatus_is_deliver($pick_id){
    	
        $options = array(); 

		$options['where']['id'] = $pick_id;

		$options['where']['status'] = 3; 
		
		$data = array(); 
		
		$data['status'] = 5; 
		
		$data['pick_end_time'] = time(); 
		
		$tof = $this->update($data,$options);
        
        return $tof;
    }
	//ERP_PICK_PRODUCT修改订单表未发货的为已通过操作
    function check_pick($orders_id){
		if($orders_id){
			$sql = "update erp_orders set `orders_status`='3',`orders_is_backorder`='1',`orders_print_time`=null where `erp_orders_id` IN (".$orders_id.") AND `orders_status`=4";//修改状态为已通过
		    $ret = $this->db->query($sql);
		}
		return $ret;
	}
	//拣货单详情表未拣货订单改为已通过
	function change_pick_status($pick_id){
		if($pick_id){
			$sql = "update erp_pick_product set `status`='8',result='订单退回，状态变为已通过' where `pick_id` IN (".$pick_id.") and `status` != '4'";//修改状态为已通过
		    $ret = $this->db->query($sql);
		}
		return $ret;
	}
	//ERP_PICK包装完成操作
    function change_pick_id($pick_id){
		if($pick_id){
			$sql = "update erp_pick set `status`='5' where `id` IN (".$pick_id.")";//修改状态为已标记发货
		    $ret = $this->db->query($sql);
		}
		return $ret;
	}
	//已发货的订单总数
	function getorders_num($pick_id){
		$sql = "SELECT COUNT(distinct orders_id) AS num FROM erp_pick_product WHERE `status` = '4' AND `pick_id`='".$pick_id."'";
		return $this->result_array($sql); 
	}
	//条件正在包装的拣货单status=3 3天后
	function pick_complete(){
		$sql = "SELECT COUNT(*) AS num, 
			pro.`pick_id` , 
			pick.type, 
			pick.sku_num 
			FROM erp_pick pick 
			INNER JOIN erp_pick_product pro ON pro.`pick_id` = pick.`id` 
			WHERE pro.`status` != 4 
			AND pick.`status` = 3 
			AND pick.`create_time` < UNIX_TIMESTAMP(NOW() - INTERVAL 3 DAY) 
			GROUP BY pro.pick_id";
		return $this->result_array($sql); 
	}
	//获得拣货单下未拣货完成的订单id
	function getorders_id($pick_id){
		if($pick_id){
			$sql = "SELECT o.`erp_orders_id`,p.`pick_id`
					FROM erp_orders o, `erp_pick_product` p
					WHERE p.`pick_id`
					IN (
					".$pick_id."
					)
					AND p.`status` !=4
					AND p.orders_id = o.erp_orders_id GROUP BY `erp_orders_id`";
		    return $this->result_array($sql);
		}
	}
	//去除已全部成功发货的订单
	function remove_repeat($pid){
		if($pid){
			$sql = "UPDATE `erp_pick` SET `status`=5 WHERE `id` IN(".$pid.")";
			$ret = $this->db->query($sql);
		}
		return $ret;
	}
	//erp_orders表判断订单是否是已撤单订单
    public function get_orders_over($orders_id){
		
        $options = array();
		
		$this->_table = 'erp_orders';    
		
		$where['orders_status'] = '6';   //是否是撤单状态
		
		$where['erp_orders_id'] = $orders_id;
		
		$options['where']=$where;
		
        $data = $this->getAll2Array($options);

        return $data;
    }
	
		/*
	  *查询sku所在的仓库库位
	  *区分深圳一仓和深圳二仓
	  */
	  public function get_sku_location($sku = ''){
			
			$options = array();
			
			$this->_table = 'erp_products_data';  
			
			$options['select'] = array('products_location','product_warehouse_id');

			$where['products_sku'] = $sku;   //sku相等
			
			$where['product_warehouse_id'] = 1000;   //只针对深圳仓
			
			$options['where']=$where;
			
			$data = $this->getAll2Array($options);
			
			$this->_table = 'erp_pick';   //恢复默认table为erp_pick

			return $data;
			
		}
	 /*
	 *根据拣货单pick_id查出对应拣货单下订单
	 *判断订单是否深圳一仓和二仓
	 */
	 public function get_pick_location($pick_id){
		 
		$options = array();

        $where = array();
		
		$this->_table = 'erp_pick_product';   //拣货单详情表
		
		$where['pick_id'] = $pick_id;    //条件
		
		$options['select'] = array('product_sku');   //产品sku   

        $options['where'] = $where;
		 
		$result = $this->getOne($options,true);    //取一条
		
		if($result){
			
			return $this->get_sku_location($result['product_sku']);    //执行库位查找
		}else{    //没有查到数据 返回空数组
			
			return array();
		}
		
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