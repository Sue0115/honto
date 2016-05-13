<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 图片银行分组模型类
 */
class Slme_smt_photo_group_model extends MY_Model {
    
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
     * 判断分组是否已存在
     * @param  [type] $token_id [description]
     * @param  [type] $iid      [description]
     * @return [type]           [description]
     */
    public function checkIsExists($token_id, $groupId){
		$options['where'] = array('token_id' => $token_id, 'groupId' => (string)$groupId);
		$rs               = $this->getOne($options, true);
    	return $rs ? $rs['id'] : false;
    }

    /**
     * 删除当前账号过期的分组信息
     * @param $token_id
     */
    public function deleteExpiredGroup($token_id){
        $sql = "DELETE FROM erp_slme_smt_photo_group WHERE token_id = $token_id AND last_update_time < NOW() - INTERVAL 1 DAY";
        $this->query($sql);
    }

    /**
     * 获取某个账号的图片银行分组列表
     * @param $token_id
     * @return mixed
     */
    public function getPhotoGroupList($token_id){
        $options['where'] = array('token_id' => $token_id);
        $return = null;
        return $this->getAll($options, $return, true);
    }

    /**
     * 获取并格式化账号的分组信息
     * @param $token_id
     */
    public function formAtPhotoGroupList($token_id){
        $rs = array();
        $group_array = $this->getPhotoGroupList($token_id);

        if ($group_array){
            $temp = array();
            foreach ($group_array as $group){ //先组装下数组
                if ($group['parentId'] <> '0'){
                    $temp[$group['parentId']][] = $group;
                }
            }

            foreach ($group_array as $group){
                if ($group['parentId'] == '0'){
                    $rs[] = $this->_groupData($group, $temp);
                }
            }
        }
        return $rs;
    }

    /**
     * 组装图片分组的子分组
     */
    private function _groupData($row, $tempArray){
        foreach ($tempArray as $parent_id => $item){
            if ($row['groupId'] == $parent_id){
                foreach ($item as $array){
                    $array = $this->_groupData($array, $tempArray); //递归获取
                    $row['child'][] = $array;
                }
            }
        }
        return $row;
    }

    /**
     * 获取图片银行分组的子分组ID
     * @param $token_id
     * @param $groupId
     */
    public function getChildrenPhotoGroup($token_id, $groupId){
        $options['where'] = array('token_id' => $token_id, 'parentId' => (string)$groupId);
        $return_array     = null;
        $result           = $this->getAll($options, $return_array, true);
        $rs = array();
        if ($result){
            foreach ($result as $row){
                $return = $this->getChildrenPhotoGroup($token_id, $row['groupId']);
                $rs[]   = $row['groupId'];
                $rs     = array_merge($rs, $return);
            }
        }
        return $rs;
    }

    /**
     * 获取图片银行分组本分类及子分类
     * @param $token_id
     * @param $groupId
     * @return array
     */
    public function getSelfAndChildrenPhotoGroup($token_id, $groupId){
        $return = $this->getChildrenPhotoGroup($token_id, $groupId);
        array_push($return, $groupId);
        $rs = array_unique($return);
        return $rs;
    }
}

/* End of file Slme_smt_photo_group_model.php */
/* Location: ./defaute/models/smt/Slme_smt_photo_group_model.php */