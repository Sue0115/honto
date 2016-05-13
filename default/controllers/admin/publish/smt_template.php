<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 速卖通模板设置
 * User: admin
 * Date: 2015/1/7
 * Time: 15:52
 */
class smt_template extends Admin_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model(array(
            'Slme_smt_template_model',
            'Orders_type_model',
            'sharepage'
        ));
        $this->model = $this->Slme_smt_template_model;
        $this->plat  = $this->Orders_type_model;
    }

    /**
     * 速卖通模板显示列表
     */
    public function index()
    {
        //查询的参数
        $plat_selected = $this->input->get_post('plat');
        $name          = trim($this->input->get_post('name'));

        //平台列表
        $plat_list = $this->plat->getOrdersType(false, array('publish_show' => 1));

        //分页参数
        $cupage   = (int)$this->config->item('site_page_num');
        $per_page = (int)$this->input->get_post('per_page');

        //查询模板条件及结果
        $where  = array();
        $like   = array();
        $params = array(); //参数条件
        if ($plat_selected) {
            $where['plat']  = $plat_selected;
            $params['plat'] = $plat_selected;
        }
        if ($name) {
            $like           = array('name' => $name);
            $params['name'] = $name;
        }

        $options       = array(
            'where'    => $where,
            'like'     => $like,
            'page'     => $cupage,
            'per_page' => $per_page
        );
        $return_data   = array('total_rows' => true);
        $template_list = $this->model->getAll($options, $return_data);

        $c_url = admin_base_url('publish/smt_template/index');
        $url   = $c_url . '?' . http_build_query($params);

        $page = $this->sharepage->showPage($url, $return_data['total_rows'], $cupage);

        $data = array(
            'plat_list'     => $plat_list,
            'plat_selected' => $plat_selected,
            'name'          => $name,
            'template_list' => $template_list,
            'page'          => $page,
            'totals'        => $return_data['total_rows']
        );

        $this->_template('admin/publish/smt/template_list', $data);
    }

    /**
     * 模板信息详情及编辑
     */
    public function info()
    {
        parent::info();

        $id = $this->input->get_post('id');

        //模板信息
        $template_info = $this->model->getTemplateInfo($id);

        //平台类型列表 --一维数组
        $plat_type = $this->plat->getOrdersType(false, array('publish_show' => 1));

        $data = array(
            'template_info' => $template_info,
            'plat_type'     => $plat_type
        );
        $this->_template('admin/publish/smt/template_info', $data);
    }

    //这个配合上边的info函数，不惜要
    public function save()
    {
        $id = (int)$this->input->get_post('id');

        $data['plat'] = $this->input->get_post('plat');

        $data['name'] = $this->input->get_post('name');

        //图片的路径 --尚未处理
        $data['pic_path'] = $this->input->get_post('pic_path');

        $data['content'] = htmlspecialchars(trim($this->input->get_post('content')));

        //保存信息
        if ($id > 0) {
            $data['id'] = $id;
            $result     = $this->model->update($data);
        } else {

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
     * 删除模板信息
     */
    public function delete()
    {
        $id               = $this->input->get_post('id');
        $options['where'] = array('id' => $id);
        if ($this->model->delete($options)) {
            echo json_encode(array('msg' => '删除成功', 'status' => 1));
        } else {
            echo json_encode(array('msg' => '删除失败', 'status' => 0));
        }
        exit();
    }

    /**
     * 复制模板
     */
    public function copy()
    {
        $id = $this->input->get_post('id');

        $template_info = $this->model->getTemplateInfo($id);
        if (!$template_info) {
            ajax_return('ID为' . $id . '的数据不存在，请刷新');
        }

        $template_info['name'] .= '-copy';
        unset($template_info['id']);
        if ($this->model->add($template_info)) {
            ajax_return('复制成功', true);
        } else {
            ajax_return('复制失败', false);
        }
    }

    /**
     * 异步获取平台对应的模板
     */
    public function ajaxGetPlatTemplateList(){
        //平台ID
        $plat = $this->input->get_post('plat');

        if ($plat){
            $data = $this->model->getTemplateList(array('select' => 'id, name', 'plat' => $plat));
            $options = '';
            if ($data){
                foreach ($data as $row){
                    $options .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                }
            }
            unset($data);
            ajax_return('', true, $options);
        }else {
            ajax_return('平台错误', false);
        }
    }
}