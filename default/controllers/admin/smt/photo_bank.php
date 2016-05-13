<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 速卖通图片银行管理
 * sw20141212
 */
class Photo_bank extends Admin_Controller
{

    protected $smt;
    public $locationType = array(//图片存放位置，同步图片时必须
                                 'allGroup' => array('text' => 'allGroup', 'auto' => true),
                                 'temp'     => array('text' => 'temp', 'auto' => false),
                                 'subGroup' => array('text' => 'subGroup', 'auto' => false),
                                 'unGroup'  => array('text' => 'unGroup', 'auto' => false),
    );

    function __construct()
    {
        error_reporting(E_ALL);
        parent::__construct();
        $this->load->library('MySmt');
        $this->load->model(array(
            'smt/Smt_user_tokens_model',
            'smt/Slme_smt_photo_bank_model',
            'smt/Slme_smt_photo_group_model',
            'sharepage'
        ));
        $this->smt       = new MySmt();
        $this->model     = $this->Slme_smt_photo_bank_model;
        $this->userToken = $this->Smt_user_tokens_model;
    }

    /**
     * 图片银行首页 --列表显示
     */
    public function index()
    {
        //速卖通账号列表
        $token_options = array('where' => array('token_status' => 0));
        $token_array   = $this->userToken->getSmtTokenList($token_options);

        $group_list = array(); //图片银行分组列表
        $where      = array(); //where条件
        $token_id   = $this->input->get_post('token_id'); //查询的账号

        if ($token_id) {
            $where['token_id'] = $token_id;
            $group_list        = $this->Slme_smt_photo_group_model->formAtPhotoGroupList($token_id);
        }

        $curpage = $this->input->get_post('pageSize');
        $curpage = $curpage > 0 ? $curpage : 40;
        //$curpage  = (int)$this->config->item('site_page_num'); //每页数量
        $per_page = (int)$this->input->get_post('per_page');   //第几页
        //图片列表
        $options = array(
            'page'     => $curpage,
            'per_page' => $per_page
        );

        $groupId = $this->input->get_post('groupId'); //图片银行分组信息
        if ($groupId) {
            switch ($groupId) {
                case 'allGroup':
                    break;
                case 'unGroup':
                    $where['groupId'] = '0';
                    break;
                default :
                    //查本分类及子分类
                    $groups              = $this->Slme_smt_photo_group_model->getSelfAndChildrenPhotoGroup($token_id, $groupId);
                    $options['where_in'] = array('groupId' => $groups);
            }
        }

        if ($where) {
            $options['where'] = $where;
        }
        $return_arr = array('total_rows' => true);
        $photo_list = $this->Slme_smt_photo_bank_model->getAll($options, $return_arr, true);

        $url = admin_base_url('smt/photo_bank/index').'?'.($where ? http_build_query($where) : '');

        $page = $this->sharepage->showPage($url, $return_arr['total_rows'], $curpage);
        $data = array(
            'token'      => $token_array,
            'page'       => $page,
            'photo_list' => $photo_list,
            'groupId'    => $groupId,
            'group_list' => $group_list,
            'token_id'   => $token_id,
            'totals'     => $return_arr['total_rows']
        );

        $this->_template('admin/smt/image_list', $data);
    }

    /**
     * 异步获取图片银行分组
     * @param $token_id
     */
    public function ajaxGetPhotoGroup()
    {
        $token_id = $this->input->get_post('token_id');
        $data     = array();
        if ($token_id) {
            $data = $this->Slme_smt_photo_group_model->formAtPhotoGroupList($token_id);
        }
        ajax_return('', true, $data);
    }

    /*************下边都是同步图片银行信息用的**************/
    /**
     * 同步图片银行分组信息
     */
    public function getPhotoBankGroup()
    {
        $token_id    = $this->input->get_post('token_id');
        $token_array = array();
        //查询账号信息
        if ($token_id) {
            $token_info  = $this->userToken->getOneTokenInfo($token_id);
            $token_array = array($token_info);
        } else {
            $token_options = array('where' => array('token_status' => 0));
            $token_array   = $this->userToken->getSmtTokenList($token_options);
        }

        $flag = false;
        if ($token_array) {
            foreach ($token_array as $token) {
                //设置Token信息
                $this->smt->setToken($token);
                $this->_handleGroupData();

                //删除过期的分组信息
                $this->Slme_smt_photo_group_model->deleteExpiredGroup($token['token_id']);
                $flag = true;
            }
        }
        ajax_return('图片分组信息同步' . ($flag ? '成功' : '失败'), $flag);
    }

    /**
     * 处理图片银行分组数据
     * @param string $groupId
     */
    private function _handleGroupData($groupId = '')
    {
        $token_id = $this->smt->_token_id;
        $data     = $this->listGroup($groupId);
        if ($data && $data['success'] && $data['photoBankImageGroupList']) {
            foreach ($data['photoBankImageGroupList'] as $item) {
                $options['groupId']          = $item['groupId'];
                $options['groupName']        = $item['groupName'];
                $options['parentId']         = $groupId;
                $options['token_id']         = $token_id;
                $options['last_update_time'] = date('Y-m-d H:i:s');

                $id = $this->Slme_smt_photo_group_model->checkIsExists($token_id, $item['groupId']);
                if ($id) { //已经存在了
                    $options['id'] = $id;
                    $this->Slme_smt_photo_group_model->update($options);
                } else {
                    $this->Slme_smt_photo_group_model->add($options);
                }
                unset($options);

                //递归同步下
                $this->_handleGroupData($item['groupId']);
            }
        }
    }

    /**
     * 同步线上图片银行分组数据
     * @return mixed
     */
    protected function listGroup($groupId = '')
    {
        $api    = 'api.listGroup';
        $params = $groupId ? 'groupId=' . $groupId : '';
        $result = $this->smt->getJsonData($api, $params);
        $data   = json_decode($result, true);

        return $data;
    }


    /**
     * 同步图片银行列表
     */
    public function getPhotoBankImageList()
    {
        $token_id = $this->input->get_post('token_id');

        $token_array = array();
        if ($token_id) {
            $token_info  = $this->userToken->getOneTokenInfo($token_id);
            $token_array = array($token_info);
        } else {
            $options['where'] = array('token_status' => '0');
            $token_array      = $this->userToken->getSmtTokenList($options);
        }

        //分组ID
        $groupId = $this->input->get_post('groupId');

        $flag = false;
        if ($token_array) {
            foreach ($token_array as $token) {
                $this->smt->setToken($token);
                //foreach ($this->locationType as $locationType) {
                //
                //}

                //默认获取的位置是allGroup
                if (in_array($groupId, array('allGroup', 'unGroup'))) {
                    $this->_handleImageData($groupId);
                } elseif ($groupId) { //选择同步某个分组那就哪个分组
                    $this->_handleImageData('subGroup', $groupId);
                } else {
                    $this->_handleImageData();
                }

                $flag = true;
                //过期的图片暂时不删除吧,后边再说
            }
        }
        ajax_return('图片同步' . ($flag ? '成功' : '失败'), $flag);
    }

    /**
     * 获取及处理图片数据
     * @param string $locationType
     */
    private function _handleImageData($locationType = 'allGroup', $groupId = '')
    {
        $data      = $this->listImagePagination($locationType, 1, $groupId);
        $totalPage = 0;
        $token_id  = $this->smt->_token_id;
        if ($data && $data['success'] && $data['total'] > 0) {
            $totalPage  = $data['totalPage'];
            $image_list = $data['images'];
            //保存数据
            $this->_parseAndSaveImageData($image_list, $token_id);
        }
        if ($totalPage > 1) { //用循环不用递归会比较好，递归的话，中间环节有问题就game over了
            for ($i = 2; $i <= $totalPage; $i++) {
                $data2 = $this->listImagePagination($locationType, $i, $groupId);
                if ($data2 && $data2['success']) {
                    $image_list2 = $data2['images'];
                    //保存数据
                    $this->_parseAndSaveImageData($image_list2, $token_id);
                }
            }
        }
    }

    /**
     * 解析并保存图片银行数据信息
     * @param $data
     * @param $token_id
     */
    private function _parseAndSaveImageData($data, $token_id)
    {
        if ($data) {
            foreach ($data as $item) {
                $options['fileSize']       = $item['fileSize'];
                $options['height']         = $item['height'];
                $options['width']          = $item['width'];
                $options['referenceCount'] = $item['referenceCount'];
                $options['url']            = $item['url'];
                $options['displayName']    = $item['displayName'];
                $options['updateTime']     = date('Y-m-d H:i:s');
                if (array_key_exists('groupId', $item)) {
                    $options['groupId'] = $item['groupId'];
                }

                $id = $this->model->checkPicIsExists($token_id, $item['iid']);
                if ($id) {
                    $options['id'] = $id;
                    $this->model->update($options);
                } else {
                    $options['iid']      = $item['iid'];
                    $options['token_id'] = $token_id;
                    $this->model->add($options);
                }
                unset($options);
                unset($item);
            }
            unset($data);
        }
    }

    /**
     * 获取图片银行线上的图片列表
     * @param int $page
     * @return string
     */
    public function listImagePagination($locationType, $page = 1, $groupId = '')
    {
        $api                    = 'api.listImagePagination';
        $params['currentPage']  = $page;
        $params['pageSize']     = 50;
        $params['locationType'] = $locationType;
        if (!in_array($locationType, array('allGroup', 'unGroup'))) {
            $params['groupId'] = $groupId;
        }
        $result = $this->smt->getJsonData($api, http_build_query($params));

        return json_decode($result, true);
    }

    /**
     * 同步图片银行信息
     */
    public function getPhotoBankDetail()
    {
        $token_id = $this->input->get_post('token_id');

        if ($token_id) { //设置了账号
            $token_info = $this->userToken->getOneTokenInfo($token_id);
            $this->smt->setToken($token_info);

            //获取信息
            $this->getPhotoBankInfo();
        } else {
            //循环同步所有账号吧
            $options['where'] = array('token_status' => '0');
            $token_array      = $this->userToken->getSmtTokenList($options);
            if ($token_array) {
                foreach ($token_array as $token) {
                    $this->smt->setToken($token);

                    $this->getPhotoBankInfo();
                }
            }
            unset($token_array);
        }
    }


    /**
     * 同步图片银行信息 --api获取
     */
    public function getPhotoBankInfo()
    {
        $api    = 'api.getPhotoBankInfo';
        $result = $this->smt->getJsonData($api, '');

        return json_decode($result, true);
    }
}