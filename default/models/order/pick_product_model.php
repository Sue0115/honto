<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Pick_product_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }

    //获取拣货单下，每个订单状态的数量
    public function get_pick_order_status_count($pick_id){

    	$pick_id = $pick_id;

    	$orders = $this->getAll2Array(array('pick_id'=>$pick_id));

    	if(empty($orders)){
    		return array();
    	}

    	$result = array();

    	$order_data = array();

    	$product_data = array();

    	foreach ($orders as $k => $v) {
    		$data[trim($v['orders_id'])] = $v['status'];
            $str = $v['status'] == 1 ? 'product_num' : 'scan_num';
    		if(isset($result[$v['status']]['product_num'])){
    			$result[$v['status']]['product_num'] += $v[$str];
    		}else{
    			$result[$v['status']]['product_num'] = $v[$str];
    		}	
    	}

    	foreach ($data as $k => $v) {
    		if(isset($result[$v]['order_num'])){
    			$result[$v]['order_num']++;
    		}else{
    			$result[$v]['order_num'] = 1;
    		}
    	}

    	return $result;

    }

    //根据拣货单号获取该拣货单的所有产品
    function get_product_by_pick_id($pick_id){

        $options = array();

        $options['where']['pick_id'] = $pick_id;

        $data = $this->getAll2Array($options);

        return $data;
    }

    //根据sku查找商品,未扫描的
    function find_order_by_sku($pick_id,$sku){

        $options = array();

        if(empty($pick_id)){
            return array();
        }

        if(empty($sku)){
            return array();
        }

        $options['where']['status <='] = 2;

        $options['where']['product_num > scan_num'] = null;

        $options['where']['product_sku'] = strtoupper(trim($sku));

        $options['where']['pick_id'] = trim($pick_id);

        $options['order_by'] = 'orders_id ASC';

        $data = $this->getOne($options,true);
        
        return $data;
    }

    //检查订单是否可以打印
    function check_order_is_can_print($orders_id,$pick_id){

        $options = array();

        $options['where']['status <='] = 2;

        $options['where']['orders_id'] = trim($orders_id);

        $options['where']['pick_id'] = trim($pick_id);

        $options['where']['product_num != scan_num'] = null;

        $data = $this->getOne($options,true);

        return $data;

    }
    
    //检查拣货单中是否存在未包装的订单（包裹），
    function check_order_is_no_packaging($pick_id){
    	
    	$this->load->model(array('order/pick_model'));

        $options = array();

        $options['where']['status <'] = 3;

        $options['where']['pick_id'] = trim($pick_id);

        $data = $this->getOne($options,true);
        
        $pof = false;
        
        if(empty($data)){
          $pof=$this->pick_model->change_pickStatus_is_packaging($pick_id);
        }
        return $pof;
        
    }

    //改变订单的状态为已打印
    function change_status_is_printed($orders_id){

        $options = array();

        $options['where']['orders_id'] = $orders_id;

        $data = array();

        $data['status'] = 3;

        $data['print_time'] = time();

        $tof = $this->update($data,$options);

        return $tof;

    }

     //改变订单的状态为已发货
    function change_status_is_shiped($orders_id){

        $uid = $this->user_info->id;//登录用户id

        $options = array();

        $options['where']['orders_id'] = $orders_id;

        $data = array();

        $data['status'] = 4;

        $data['ship_time'] = time();

        //$data['ship_uid']  = $uid;

        $tof = $this->update($data,$options);

        return $tof;

    }



    function get_all_by_sattus($pick_id,$warehouse,$status,$uid){

        $options = array();

        $where = array();

        $where['status'] = $status;

        $where['pick_id'] = $pick_id;

        $where['scan_uid'] = $uid;

        $where['p.product_warehouse_id'] = $warehouse;

        $options['where'] = $where;

        $options['select'] = array("{$this->_table}.*","p.products_name_cn,p.products_warring_string,p.products_with_adapter,p.pack_method,p.products_imgs");

        if($status == 2){
            $options['order_by'] = 'scan_time desc';
        }

        if($status == 3){
             $options['group_by'] = 'orders_id';
             $options['order_by'] = 'print_time desc';
        }

        if($status == 4){
             $options['group_by'] = 'orders_id';
             $options['order_by'] = 'ship_time desc';
        }



        $join[] = array("{$this->_table_pre}products_data p","p.products_sku={$this->_table}.product_sku");

        $options['join'] = $join;

        $data = $this->getAll2Array($options);

        return $data;

    }
    
   //发货统计，erp_pick_product和erp_slme_user表联合查询
    public function getShippingCount($cupage,$per_page,$return_arr){
    	
      $option['select'] = array("{$this->_table}.*","u.nickname");
      
      $option['where'] = array("{$this->_table}.status" => 4);
      
      $join[] = array("erp_slme_user u","{$this->_table}.scan_uid = u.id");
      
      $option['join'] = $join;
      
      $option['page'] = $cupage;
      
      $option['per_page'] = $per_page;
      
      return $this->getAll($option,$return_arr);
      
    }

    function get_pick_product_by_pick_id($pick_id){

        $options = array();

        $where = array();

        $where['pick_id'] = $pick_id;

        $options['where'] = $where;

        $options['order_by'] = 'basket_num asc';

        $data = $this->getAll2Array($options);

        $result = array();

        if($data){
            foreach ($data as $k => $v) {
                $result[$v['basket_num']]['sku_info'][] = $v;
                $result[$v['basket_num']]['shiped'] = 1;  //是否已标记发货
                if($v['status'] != 3 && $v['status']!=4 ){
                    $result[$v['basket_num']]['shiped'] = 0;
                }
            }
        }

        return $result;
    }


    /**
     * 找出某个时间段的发货数量
     * 可以是当月、当天、昨天
     */
    public function getProductCountByTime($start,$end){
        //找出发货的订单
	  	$where = array();
	    $option = array();
	    $where = array(
	      'status' => 4,
	      'ship_time >='=> $start,
	      'ship_time <' => $end
	    );
	    $select = array();
	    $select = array('COUNT(DISTINCT(orders_id)) as all_num','sum(scan_num) as total_num','ship_uid');
	    $option = array(
	      'select'  =>  $select,
	      'where'   =>  $where,
	      'order_by'=>  'all_num desc',
	      'group_by'=>  'ship_uid'
	      
	    );
	    return $this->model->getAll2array($option);
	    
    }
    
    /**
     * 找出某个时间点的发货商品数
     */
    public function getOrderProductCountByTime($uid,$start,$end){
	  	$where = array();
	    $option = array();
	    $select = array();
	    $where = array(
	      'status' => 4,
	      'ship_uid' => $uid,
	      'ship_time >=' => $start,
	      'ship_time <'  => $end
	    );
	    $select = array();
	    $select = array('sum(scan_num) as total_num','ship_uid');
	    $option = array(
	      'select'  =>  $select,
	      'where'   =>  $where
	      
	    );
	    return $this->model->getOne($option,true);
	    
    }
	/**
	 * 根据传递过来的sku种类和数量从拣货单产品表中获取能够匹配这些sku的订单号
	 * pick_id,skuString,skuNum
	 */
	public function get_pick_product($pick_id,$skuArr,$skuCount){
	  $option = array();
	  $where = array();
	  $in = array();
	  $select = array('COUNT(*) AS num','orders_id');
	  $where = array(
	    'pick_id' => $pick_id,
	    'status <='  => 3
	  );
	  $in = array('product_sku'=>$skuArr);
	  $option = array(
	    'select' => $select,
	    'where'  => $where,
	    'where_in'=> $in,
	    'group_by'=> 'orders_id  HAVing num='.$skuCount
	  );
	  return $this->getAll2array($option);
	}
	/**
	 * 根据拣货单id和订单id获取拣货单信息
	 */
	public function get_info_by_pickid_orderid($pick_id,$order_id){
	  $option = array();
	  $where = array();
	 
	  $where = array(
	    'pick_id' => $pick_id,
	    'orders_id'  => $order_id
	  );
	
	  $option = array(
	    'where'  => $where,
	  );
	  return $this->getAll2array($option);
	}
	public function get_pick_package($sku,$orders_id,$pick_id){  //判断是否进行过订单修改状态  导致两个拣货单同一订单
		
        $options = array();

        $options['where']['pick_id >'] = $pick_id;

        $options['where']['product_sku'] = trim($sku);

        $options['where']['orders_id'] = trim($orders_id);

        $data = $this->getOne($options,true);
        
        return $data;
		
	}
	/*
	*获得生成的拣货单详情数据
	*对应的sku储位
	*/
	public function get_pickproduct_data($pick_id,$warehouse){
		
        $options = array();

        $where = array();

        $options['select'] = array($this->_table.'.*','p.products_location');

        $join[] = array('erp_products_data p',$this->_table.'.product_sku=p.products_sku AND p.product_warehouse_id ='.$warehouse.'','inner');

        $options['join'] = $join;
		
		$options['where']['pick_id'] = $pick_id;
		
        $data = $this->getAll2array($options,true);
        
        return $data;

	}
}

/* End of file Pick_product_model.php */
/* Location: ./defaute/models/order/Pick_product_model.php */