<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 图片银行模型类
 */
class Slme_smt_photo_bank_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }

    /**
     * 判断图片是否已存在
     * @param  [type] $token_id [description]
     * @param  [type] $iid      [description]
     * @return [type]           [description]
     */
    public function checkPicIsExists($token_id, $iid){
		$options['where'] = array('token_id' => $token_id, 'iid' => $iid);
		$rs               = $this->getOne($options, true);
    	return $rs ? $rs['id'] : false;
    }

    /**
     * 根据账号，删除过期的图片
     * @param $token_id
     */
    public function deleteExpiredPic($token_id){
        $sql = "DELETE FROM erp_slme_smt_photo_bank WHERE token_id = $token_id AND updateTime < NOW() - INTERVAL 1 DAY";
        $this->query($sql);
    }
}

/* End of file Slme_smt_photo_bank_model.php */
/* Location: ./defaute/models/smt/Slme_smt_photo_bank_model.php */