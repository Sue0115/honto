<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 自定义的关联产品模型
 */
class Slme_relation_model extends MY_Model{

    const ENABLED = 1; //可用
    const UNENABLED = 0; //禁用

    function __construct(){
        parent::__construct();
    }

    /**
     * 定义模板的状态
     * @return array
     */
    public function getDefinedStatus(){
        return array(
            self::ENABLED => '可用',
            self::UNENABLED => '禁用'
        );
    }

    /**
     * 获取一个自定义关联产品信息
     * @param $id
     * @return Ambigous
     */
    public function getRelationInfo($id)
    {
        $options = array();

        if ($id){
            $options['where'] = array('id' => $id);
        }

        return $this->getOne($options, true);
    }
}