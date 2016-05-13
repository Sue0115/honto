<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Shipment_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }
    //获取所有的物流信息
    public function getAllShipment($type=1){
      $select=array('shipmentID','shipmentTitle');
      $where=array('shipmentEnable'=>'1');
      $option=array(
        'select'=>$select,
      	'where' =>$where,
      	'order' =>'shipmentID asc'
      );
      $result=$this->getAll2array($option);
      if($type==2){
      	$new_result = array();
      	foreach($result as $re){
      	  $new_result[$re['shipmentID']] = $re['shipmentTitle'];
      	}
        return $new_result;
      }else{
        return $result;
      }
      
    }

    //
    public function get_one_get_template($id){

      $join[] = array('erp_printing_template t',"t.id={$this->_table}.shipment_template");

      $options = array();

      $where = array();

      $where['shipmentID'] = (int)$id;

      $options['where'] = $where;

      $options['join'] = $join;

      $data = $this->getOne($options,true);

      return $data;

    }

    //查询物流信息
    public function get_shipment_template($shipment_id){

      $join[] = array('erp_printing_template t',"t.id={$this->_table}.shipment_template");

      $options = array();

      $where_in = array();

      $where_in['shipmentID'] = $shipment_id;

      $options['where_in'] = $where_in;

      $options['join'] = $join;

      $data = $this->getAll2array($options,true);

      return $data;

    }
    
//根据物流id获取物流信息
    public function getInfoById($id){
      $option = array();
      $where  = array();
      $where['shipmentID'] = $id;
      $option['where']  =  $where;
      return $this->getOne($option,true);
    }

}

/* End of file Shipment_model.php */
/* Location: ./defaute/models/shipment/Shipment_model.php */