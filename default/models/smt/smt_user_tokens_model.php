<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 速卖通账号授权列表model
 * suwei 20141117
 */
class Smt_user_tokens_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
       	parent::__construct('smt_user_tokens');
    }

    /**
     * 获取某个账号信息
     * @param unknown $token_id
     * @return Ambigous <unknown, Ambigous, StdClass, boolean, mixed>
     */
    public function getOneTokenInfo($token_id){
        $options['where'] = array(
                'token_id' => $token_id,
        );
        return $this->getOne($options, true);
    }
    
    /**
     * 获取速卖通账号列表
     * @param  integer $status [description]
     * @return [type]          [description]
     */
    public function getSmtTokenList($options){
    	$rs = array();
    	$array   = null;
    	$result  = $this->getAll($options, $array, true);
    	if ($result) {
    		foreach ($result as $r) {
    			$rs[$r['token_id']] = $r;
    		}
    	}
    	return $rs;
    }

    public function formatSmtTokenList($options){
        $data_list = $this->getSmtTokenList($options);
        $rs        = array();
        foreach ($data_list as $item) {
            $rs[$item['token_id']] = $item['seller_account'];
        }
        return $rs;
    }

    public function formatSmtTokenListAccountSuffix($options){
        $data_list = $this->getSmtTokenList($options);
        $rs        = array();
        foreach ($data_list as $item) {
            $rs[$item['token_id']] = $item['accountSuffix'];
        }
        return $rs;
    }
    
    /**
     * 获取字段
     * @param $params
     * @param $return_arr
     * @param $fields: 字段信息
     * @return mixed
     */
    public function getColumnsList(){
        return $this->getFields();
    }
    
    /**
     * 修改单条记录
     * @param unknown $data
     * @param unknown $id
     * @return Ambigous <object, boolean>
     */
    public function updateTemplateInfo($data,$id){
        $options = array();
        $options['where']['token_id'] = $id;
        return $this->update($data,$options);
    }
}