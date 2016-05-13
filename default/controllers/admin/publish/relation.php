<?php
/**
 * smt关联手动设置产品关联模板
 */
class Relation extends Admin_Controller{

    function __construct(){
        parent::__construct();
        $this->load->model(array(
            'smt/Slme_relation_model',
            'smt/Smt_user_tokens_model',
            'sharepage'
        ));
        $this->model = $this->Slme_relation_model;
        $this->userToken = $this->Smt_user_tokens_model;
    }

    /**
     * 自定义关联产品模板列表
     */
    public function index(){

        //获取所有可用的速卖通账号列表
        $token_condition = array(
            'where' => array('token_status' => 0)
        );
        $token_list = $this->userToken->formatSmtTokenList($token_condition);

        //定义的所有状态
        $status_list = $this->model->getDefinedStatus();

        //查询的参数
        $token_id = $this->input->get_post('token_id');
        $name     = trim($this->input->get_post('name'));
        $status = $this->input->get_post('status');

        //分页参数
        $cupage   = (int)$this->config->item('site_page_num');
        $per_page = (int)$this->input->get_post('per_page');

        //查询模板条件及结果
        $where  = array();
        $like   = array();
        $params = array(); //参数条件
        if (!empty($token_id)) { //账号
            $where['token_id']  = $token_id;
            $params['token_id'] = $token_id;
        }
        if (!empty($name)) { //名称
            $like           = array('name' => $name);
            $params['name'] = $name;
        }
        if (isset($status) && $status != ''){ //可用
            $where['status']  = $status;
            $params['status'] = $status;
        }

        $options       = array(
            'where'    => $where,
            'like'     => $like,
            'page'     => $cupage,
            'per_page' => $per_page
        );
        $return_data   = array('total_rows' => true);
        $relation_list = $this->model->getAll($options, $return_data);

        $c_url = admin_base_url('publish/relation/index');
        $url   = $c_url . '?' . http_build_query($params);

        $page = $this->sharepage->showPage($url, $return_data['total_rows'], $cupage);

        $data = array(
            'relation_list' => $relation_list,
            'token_list'    => $token_list,
            'status_list'   => $status_list,
            'page'          => $page,
            'totals'        => $return_data['total_rows'],
            'token_id'      => $token_id,
            'name'          => $name,
            'status'        => $status
        );

        $this->_template('admin/publish/smt/relation_list', $data);
    }

    /**
     * 新增、编辑并保存
     */
    public function info(){
        parent::info();

        //传进来的id
        $id = $this->input->get_post('id');

        //模板信息
        $relation_info = $this->model->getRelationInfo($id);

        //账号信息
        $token_condition = array(
            'where' => array('token_status' => 0)
        );
        $token_list = $this->userToken->formatSmtTokenList($token_condition);

        //定义的所有状态
        $status_list = $this->model->getDefinedStatus();

        $data = array(
            'relation_info' => $relation_info,
            'token_list'    => $token_list,
            'status_list'   => $status_list
        );
        $this->_template('admin/publish/smt/relation_info', $data);
    }

    //配合info方法使用
    public function save(){
        $id = (int)$this->input->get_post('id');

        $data['token_id'] = $this->input->get_post('token_id');

        $data['name'] = trim($this->input->get_post('name'));

        $data['status'] = $this->input->get_post('status');

        $data['content'] = htmlspecialchars(trim($this->input->get_post('content')));

        //保存信息
        if ($id > 0) {
            $data['id'] = $id;
            $result     = $this->model->update($data);
        } else {
            $data['created_at'] = time();
            $result = $this->model->add($data);
        }

        $info = $id ? '修改' : '添加';

        //信息返回操作
        if ($result) {

            $val = $id ? $id : $result;

            ajax_return($info . '成功', true, 'id' . $val);
        } else {

            ajax_return($info . '失败', false);
        }
    }

    /**
     * 复制一个模板
     */
    public function copy(){
        $id = $this->input->get_post('id');

        $relation_info = $this->model->getRelationInfo($id);
        if (empty($relation_info['id'])) {
            ajax_return('ID为' . $id . '的数据不存在，请刷新');
        }

        $relation_info['name'] .= '-copy';
        unset($relation_info['id']);
        if ($this->model->add($relation_info)) {
            ajax_return('复制成功', true);
        } else {
            ajax_return('复制失败', false);
        }
    }
}