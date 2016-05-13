<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 速卖通分类管理控制器
 * sw20141212
 */
header("content-type:text/html; charset=utf-8");
set_time_limit(0);
class Category extends MY_Controller
{

    protected $smt;
    protected $userToken;
    protected $model;

    function __construct()
    {
        parent::__construct();
        $this->load->library('MySmt');
        $this->load->model(array(
            'smt/Smt_user_tokens_model',
            'smt/Slme_smt_categorylist_model',
            'smt/Slme_smt_category_attribute_model',
        ));
        $this->smt       = new MySmt();
        $this->userToken = $this->Smt_user_tokens_model;
        $this->model     = $this->Slme_smt_categorylist_model;
    }

    /**
     * 同步在线分类列表 --要注意删除没用的
     * 分类对所有账号都是共用的，没有的话，就取24的那个账号吧
     */
    function getCategoryList()
    {
        $token_id = (int)$this->input->get_post('token_id');
        $token_id = $token_id > 0 ? $token_id : 1;

        //账号详情信息
        $token_info = $this->userToken->getOneTokenInfo($token_id);


        $flag = false;
        if ($token_info) {
            $this->smt->setToken($token_info);

            //开始同步
            $this->getCategory(0);
            $flag = true;

            //删除过期的分类
            $this->_deleteExpiredCategory();
        }

        echo json_encode(array(
            'status' => $flag,
            'info'   => '同步完成,如有疑问，请联系IT'
        ));
    }

    /**
     * 获取一个类目的子叶类 --递归获取并保存
     * @param  integer $cateId [description]
     * @param  integer $level [description]
     * @return [type]          [description]
     */
    public function getCategory($cateId = 0, $level = 1)
    {
        $api       = 'api.getChildrenPostCategoryById';
        $result    = $this->smt->getJsonData($api, "cateId=$cateId");
        $cate_list = json_decode($result, true);

        //获取数据成功，且有数据，还是先保存吧
        if ($cate_list['success'] && $cate_list['aeopPostCategoryList']) {
            //保存数据信息
            foreach ($cate_list['aeopPostCategoryList'] as $category) {
                $data['category_id']      = $category['id'];
                $data['category_name']    = !empty($category['names']['zh'])?$category['names']['zh']:'';
                $data['category_name_en'] = !empty($category['names']['en'])?$category['names']['en']:'';
                $data['level']            = $level;
                $data['isleaf']           = $category['isleaf'] == 1 ? 1 : 0;
                $data['pid']              = $cateId;
                $data['last_update_time'] = date('Y-m-d H:i:s');


                $id = $this->model->checkCategoryIsExists($category['id']);

                if ($id) { //存在就更新

                    $data['id'] = $id;
                    $this->model->update($data);
                } else { //不存在就插入
                    $this->model->add($data);
                }

                unset($data);
                //看是否还要继续下一层
                if ($category['isleaf'] != 1) { //等于1的时候不再有子叶
                    $this->getCategory($category['id'], $level + 1);
                }
            }
        }
    }


    //用这个方法，慢了点，但是上面获取不全
    public function getCategoryListNew(){
        $token_id = (int)$this->input->get_post('token_id');
        $token_id = $token_id > 0 ? $token_id : 1;

        //账号详情信息
        $token_info = $this->userToken->getOneTokenInfo($token_id);
     //   var_dump($token_info);exit;
        $this->smt->setToken($token_info);

        $this->getCategoryNew(0); //先获取第一轮
        for($i=1;$i<6;$i++){
            $option = array();
            $option['where']['level'] = $i;
            $result = $this->model->getAll2Array($option);
            if(!empty($result)){
                foreach($result as $re){
                    if($re['isleaf'] !=1){
                        $this->getCategoryNew($re['category_id'],$i+1);
                    }else{
                        continue;
                    }

                }
            }
        }
        $this->_deleteExpiredCategory();

        echo '更新完成';


    }


    public function getCategoryNew($cateId = 0, $level = 1)
    {
        $api       = 'api.getChildrenPostCategoryById';
        $result    = $this->smt->getJsonData($api, "cateId=$cateId");
        $cate_list = json_decode($result, true);
    //    var_dump($cate_list);exit;

        //获取数据成功，且有数据，还是先保存吧
        if(isset($cate_list['aeopPostCategoryList'])){
            if ($cate_list['success'] && $cate_list['aeopPostCategoryList']) {
                //保存数据信息
                foreach ($cate_list['aeopPostCategoryList'] as $category) {
                    $data['category_id']      = $category['id'];
                    $data['category_name']    = !empty($category['names']['zh'])?$category['names']['zh']:'';
                    $data['category_name_en'] = !empty($category['names']['en'])?$category['names']['en']:'';
                    $data['level']            = $level;
                    $data['isleaf']           = $category['isleaf'] == 1 ? 1 : 0;
                    $data['pid']              = $cateId;
                    $data['last_update_time'] = date('Y-m-d H:i:s');


                    $id = $this->model->checkCategoryIsExists($category['id']);

                    if ($id) { //存在就更新
                        $data['id'] = $id;
                        $this->model->update($data);
                    } else { //不存在就插入
                        $this->model->add($data);
                    }
                    unset($data);
                }
            }
        }

    }
    /**
     * 删除过期的分类 --即本次没有同步到的分类
     */
    private function _deleteExpiredCategory()
    {
        $this->model->deleteExpiredCategory();
    }


    /**
     * 同步属性信息及分类与属性的对应关系 --一个账号调用次数有限，可能要和参数结合使用
     * token_id:参数
     * pageSize:参数
     * page:参数
     * @return [type] [description]
     */
    public function getAttibuteList()
    {
        // exit('请仔细查看，确定无误，则注释本行代码');
        $token_id   = $this->input->get_post('token_id');
        $token_info = $this->userToken->getOneTokenInfo($token_id);
        $this->smt->setToken($token_info);

        $pageSize = (int)$this->input->get_post('pageSize');
        $page     = (int)$this->input->get_post('page');

        $options['where'] = array('isleaf' => 1, 'order' => 'id');
        //翻页就翻页吧，不然就是一次全查出来了
        if ($page) {
            $options['where'] = array_merge($options['where'], array('per_page' => $page)); //这键名真2
        }
        if ($pageSize) {
            $options['where'] = array_merge($options['where'], array('page' => $pageSize));
        }

        $cate_list = $this->model->getAll($options);
        $flag      = false;
        $msg       = '没有找到分类信息';

        if ($cate_list) {
            foreach ($cate_list as $row) {
                $this->getCategoryAttributes($row->category_id);
            }
            $flag = true;
            $msg  = '同步完成，如有疑问，请联系IT';
        }
        unset($cate_list);
        echo json_encode(array(
            'status' => $flag,
            'info'   => $msg
        ));
    }

    /**
     * 获取各分类对应的属性，观察了几个，应该只有末子叶的才有这个属性
     * 暂时不会用到了，速卖通说该接口已废弃
     * @param  integer $category_id 分类ID
     * @return [type]               [description]
     */
    public function getCategoryAttributes($category_id = 0)
    {
        $result = '';
        $api    = 'api.getAttributesResultByCateId';
        $result = $this->smt->getJsonData($api, 'cateId=' . $category_id);

        $rs = json_decode($result, true);

        if ($rs['success']) { //返回成功了
            //判断分类ID是否存在，不存在就插入，存在就UPdate
            $options = array(
                'category_id'      => $category_id,
                'attribute'        => serialize($rs['attributes']),
                'last_update_time' => date('Y-m-d H:i:s')
            );
            $id      = $this->Slme_smt_category_attribute_model->checkCategoryAttribute($category_id);
            if ($id) {
                $options['id'] = $id;
                $this->Slme_smt_category_attribute_model->update($options);
            } else {
                $this->Slme_smt_category_attribute_model->add($options);
            }
            unset($id);
            unset($options);
        }
    }
}