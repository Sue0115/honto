<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 亚马逊自定义分类模型
 */
class Category extends Admin_Controller{

    function __construct(){
        parent::__construct();

        $this->load->model(array(
            'amz/Slme_amz_category_model',
            'sharepage'
        ));
    }

    public function index(){

        //url参数
        $params = $this->input->get();

        //每页的记录条数
        $cupage = (int)$this->config->item('site_page_num');

        //删除分页记录参数
        if (!empty($params) && array_key_exists('per_page', $params)) unset($params['per_page']);

        $return_arr['total_rows'] = true;

        $categoryList = $this->Slme_amz_category_model->getCategoryList($params, $return_arr);

        //组装分页的URL信息
        $url  = admin_base_url('amz/category/index') .'?'.(!empty($params) ? http_build_query($params) : '');
        $page = $this->sharepage->showPage($url, $return_arr['total_rows'], $cupage);

        $data = array(
            'categoryList' => $categoryList,
            'params'       => $params,
            'page'         => $page
        );

        $this->_template('admin/amz/category/list', $data);
    }

    /**
     * 新增或修改信息
     */
    public function info(){
        parent::info();

        $id = $this->input->get_post('id');

        $categoryInfo = $this->Slme_amz_category_model->getCategoryInfo($id);

        $data = array(
            'id'           => $id,
            'categoryInfo' => $categoryInfo
        );

        $this->_template('admin/amz/category/info', $data);
    }

    public function save(){
        $this->Slme_amz_category_model->save();
    }

    /**
     * 删除数据
     */
    public function delete(){
        //只接收post过来的数据
        $id = $this->input->post('id'); //当然，这里是ajaxpost过来的

        if ($id){
            $rs = $this->Slme_amz_category_model->delete(array('id' => $id));
            echo json_encode(array(
                'status' => $rs ? true : false,
                'msg'    => $rs ? '删除成功' : '删除失败'
            ));
        }else {
            echo json_encode(array('status' => false, 'msg' => '非法操作'));
        }
        exit;
    }
}