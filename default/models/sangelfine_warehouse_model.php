<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 仓库管理模型类
 */
class sangelfine_warehouse_model extends MY_Model {

    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
	
    public function __construct() {
       parent::__construct('sangelfine_warehouse'); 
    }
    
    public function get_all_warehouse($flag=false){
      
    	$where['erp_is_show']=1;
   		$options=array(
   		  'where' => $where,
   		);
   		if($flag){
   		  $result = $this->getAll2Array($options);
   		  $newData =array();
   		  foreach($result as $re){
   		    $newData[$re['warehouseID']] = $re['warehouseTitle'];
   		  }
   		  return $newData;
   		}else{
   		  return $this->getAll2Array($options);
   		}
    }
}

/* End of file sangelfine_warehouse_model.php */
/* Location: ./defaute/models/Shipment_suppliers_model.php */