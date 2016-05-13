<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * lazada承运商管理模型类
 */
class erp_lazada_logistics_service_model extends MY_Model {

    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct('erp_lazada_logistics_service');

    }

    public function getLazadaShipName($shipmentLazadaCodeID)
    {
        $options = array();

        $options['select'] = array('logistics_name');

        $where['logistics_id ']=$shipmentLazadaCodeID;

        $options['where']=$where;


        $data = $this->getOne($options,true);

        return $data;
    }
}