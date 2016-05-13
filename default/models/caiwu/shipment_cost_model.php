<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 物流对账model
 * 
 */
class Shipment_cost_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
       	parent::__construct();
    }

    public function get_one_by_orders_id($erp_orders_id){

    	$rs = array();

    	$erp_orders_id = trim($erp_orders_id);

    	if(empty($erp_orders_id)){
    		return $rs;
    	}

    	$options = array();

    	$options['where']['orders_id'] = $erp_orders_id;

    	$rs = $this->getOne($options,true);

    	return $rs;

    }

}