<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Shipment_scan_temporary_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }

    public function add_num($data,$where){

    	$tof = false;

    	$scan = $this->getOne($where,true);

    	if($scan){
    		
    		$data['num'] = $scan['num'] +1;
    		
    		$where = array();

    		$where['id'] = $scan['id'];

    		$tof = $this->update($data,$where);

    	}else{
    		$data['num'] = 1;
    		$tof = $this->add($data);
    	}

    	return $tof;
    	
    }
}

/* End of file Shipment_scan_temporary_model.php */
/* Location: ./defaute/models/shipment/Shipment_scan_temporary_model.php */