<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 分类属性管理模型类
 */
class Slme_smt_category_attribute_model extends MY_Model {
    
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
     * 获取数据库中的分类属性
     * @param $category_id
     * @return Ambigous
     */
    public function getCategoryAttributes($category_id){
        $options = array();
        if ($category_id) {
            $options['where'] = array('category_id' => $category_id);
        }
        return $this->getOne($options);
    }

    /**
     * 判断分类对应的属性是否存在
     * @param $category_id
     * @return bool
     */
    public function checkCategoryAttribute($category_id){
        $options['where'] = array('category_id' => $category_id);
        $data = $this->getOne($options, true);
        return $data ? $data['id'] : false;
    }

    /**
     * 同步在线属性
     * @param $token_id
     * @param $category_id
     * @return bool
     */
    public function getAttributesResultByCateId($token_id, $category_id)
    {
        //加载类和模板
        $this->load->library('MySmt');
        $this->load->model('smt/Smt_user_tokens_model');

        if ($token_id && $category_id){
            $smt    = new MySmt();
            //获取账号的信息
            $tokenInfo = $this->Smt_user_tokens_model->getOneTokenInfo($token_id);
            if ($tokenInfo) {
                $smt->setToken($tokenInfo);
                $api    = 'api.getAttributesResultByCateId';
                $result = $smt->getJsonData($api, 'cateId=' . $category_id);

                $rs = json_decode($result, true);

                if ($rs['success']) { //返回成功了
                    //判断分类ID是否存在，不存在就插入，存在就UPdate
                    $options = array(
                        'category_id'      => $category_id,
                        'attribute'        => serialize($rs['attributes']),
                        'last_update_time' => date('Y-m-d H:i:s')
                    );
                    $id = $this->checkCategoryAttribute($category_id);
                    if ($id) {
                        $options['id'] = $id;
                        $this->update($options);
                    } else {
                        $this->add($options);
                    }
                    return $rs['attributes'];
                }else {
                    return false;
                }
            }else {
                return false;
            }
        }else {
            return false;
        }
    }

    /**
     * 同步分类在线产品属性2:新API，
     * 缺点：目前只获取了主要属性，不知道属性是否有子属性，需要再判断
     * @param $token_id
     * @param $category_id
     * @param string $parentAttrValueList
     * @return bool
     */
    public function getChildAttributesResultByPostCateIdAndPath($token_id, $category_id, $parentAttrValueList=''){
        //加载类和模板
        $this->load->library('MySmt');
        $this->load->model('smt/Smt_user_tokens_model');

        if ($token_id && $category_id){
            $smt    = new MySmt();
            //获取账号的信息
            $tokenInfo = $this->Smt_user_tokens_model->getOneTokenInfo($token_id);
            if ($tokenInfo) {
                $smt->setToken($tokenInfo);
                $api    = 'getChildAttributesResultByPostCateIdAndPath';
                $result = $smt->getJsonData($api, 'cateId=' . $category_id.(!empty($parentAttrValueList) ? '&parentAttrValueList='.$parentAttrValueList : ''));

                $rs = json_decode($result, true);

                if (array_key_exists('success', $rs) && $rs['success']) { //返回成功了
                    //判断分类ID是否存在，不存在就插入，存在就UPdate
                    $options = array(
                        'category_id'      => $category_id,
                        'attribute'        => serialize($rs['attributes']),
                        'last_update_time' => date('Y-m-d H:i:s')
                    );
                    $id = $this->checkCategoryAttribute($category_id);
                    if ($id) {
                        $options['id'] = $id;
                        $this->update($options);
                    } else {
                        $this->add($options);
                    }
                    return $rs['attributes'];
                }else {
                    return false;
                }
            }else {
                return false;
            }
        }else {
            return false;
        }
    }
}

/* End of file Slme_smt_category_attribute_model.php */
/* Location: ./defaute/models/smt/Slme_smt_category_attribute_model.php */