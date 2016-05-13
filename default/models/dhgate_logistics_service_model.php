<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * dh承运商管理模型类
 */
class dhgate_logistics_service_model extends MY_Model {

    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();

    }

    public function getDhgateShipName($shipmentDhgateCodeID)
    {
        $options = array();

        $options['select'] = array('logistics_name');

        $where['logistics_id ']=$shipmentDhgateCodeID;

        $options['where']=$where;


        $data = $this->getOne($options,true);

        return $data;
    }
    
    /**
     * 添加物流发货方式
     * @param unknown $data
     */
    public function addShippingType($data){
       return $this->dhgate_logistics_service_model->add($data);
    }
}