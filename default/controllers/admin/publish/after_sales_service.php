<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 售后服务模板
 * User: admin
 * Date: 2015/1/7
 * Time: 15:52
 */
class after_sales_service extends Admin_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model(array(
            'Slme_after_sales_service_model',
            'Orders_type_model',
            'sharepage',
            'smt/Smt_user_tokens_model'
        ));
        $this->model     = $this->Slme_after_sales_service_model;
        $this->plat      = $this->Orders_type_model;
        $this->userToken = $this->Smt_user_tokens_model;
    }

    /**
     * 速卖通模板显示列表
     */
    public function index()
    {
        //查询的参数
        $plat_selected = $this->input->get_post('plat');
        $name          = trim($this->input->get_post('name'));
        $token_id      = $this->input->get_post('token_id');

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
        if ($token_id){
            $where['token_id'] = $token_id;
            $params['token_id'] = $name;
        }

        $options       = array(
            'where'    => $where,
            'like'     => $like,
            'page'     => $cupage,
            'per_page' => $per_page
        );
        $return_data   = array('total_rows' => true);
        $template_list = $this->model->getAll($options, $return_data);

        $c_url = admin_base_url('publish/after_sales_service/index');
        $url   = $c_url . '?' . http_build_query($params);

        //分页
        $page = $this->sharepage->showPage($url, $return_data['total_rows'], $cupage);



        $smt_user_options = array(
            'select' => 'token_id, seller_account,accountSuffix',
            'where'  => array('token_status' => 0),
        );
        //速卖通账号
        $token_list = $this->userToken->getSmtTokenList($smt_user_options);
        $data = array(
            'plat_list'     => $plat_list,
            'plat_selected' => $plat_selected,
            'name'          => $name,
            'template_list' => $template_list,
            'page'          => $page,
            'totals'        => $return_data['total_rows'],
            'token_list'    => $token_list,
            'token_id'      => $token_id
        );

        $this->_template('admin/publish/smt/after_sales_list', $data);
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

        //平台类型列表
        $plat_type = $this->plat->getOrdersType(false, array('publish_show' => 1));

        $type[0] = '--请选择--'; //组装下，那个方法比较二
        if ($plat_type){
            foreach ($plat_type as $key => $plat){
                $type[$key] = $plat;
            }
        }
        $data = array(
            'template_info' => $template_info,
            'plat_type'     => $type
        );

        if ($template_info['plat'] == 6) { //平台是速卖通
            //SMT账号列表
            $smt_options = array(
                'where' => array('token_status' => 0)
            );
            $token_list  = $this->userToken->formatSmtTokenListAccountSuffix($smt_options);


            array_unshift($token_list, '--请选择--');
            $data        = array_merge($data, array('token_list' => $token_list));
        }

        $this->_template('admin/publish/smt/after_sales_info', $data);
    }

    //这个配合上边的info函数，不惜要
    public function save()
    {
        $id = (int)$this->input->get_post('id');

        $data['plat'] = $this->input->get_post('plat');

        $data['name'] = $this->input->get_post('name');

        $data['token_id'] = (int)$this->input->get_post('token_id');

        $content = trim($this->input->get_post('content'));

        //匹配下图片，要是http开头的就上传到smt图片银行，不是的话，还是继续保存在这
        if ($data['plat'] == 6 && !empty($data['token_id']) && $content) { //速卖通平台，账号必须要存在
            $pattern = '/<img[^>]+src\s*=\s*"?([^>"\s]+)"?[^>]*>/im';
            preg_match_all($pattern, $content, $matches);
            if (!empty($matches[1])) { //有匹配上是本地的图片
                $this->load->library('MySmt');
                $smt       = new MySmt();
                $tokenInfo = $this->userToken->getOneTokenInfo($data['token_id']);
                $api       = 'api.uploadImage';
                $smt->setToken($tokenInfo);
                $site = trim(base_url());
                if (substr($site, strlen($site)-1) == '/'){
                    $site = substr($site, 0, strlen($site)-1);
                }
                foreach ($matches[1] as $key => $match) {
                    if (!preg_match('/http:\/\/.*/i', $match)) { //不以http开头，说明肯定是被上传到本地了
                        $url    = '';
                        $result = $smt->uploadBankImage($api, $site.$match); //上传图片
                        if ($result['status'] == 'SUCCESS' || $result['status'] == 'DUPLICATE') {
                            $url = $result['photobankUrl']; //返回的url链接
                        }
                        $content = str_replace($match, $url, $content);
                    }
                }
            }
        }

        $data['content'] = htmlspecialchars($content);

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
     * 异步获取对应平台的账号
     */
    public function ajaxGetTokenList(){
        //平台
        $plat = $this->input->get_post('plat');

        if (!$plat){
            ajax_return('平台错误，找不到对应的账号', false);
        }else {
            $data = array();
            switch ($plat){
                case 6: //SMT平台
                    //SMT账号列表
                    $smt_options = array(
                        'select' => array('token_id', 'seller_account','accountSuffix'),
                        'where' => array('token_status' => 0)
                    );
                    $data  = $this->userToken->getSmtTokenList($smt_options);
                    break;
            }

            if ($data){
                ajax_return('', true, $data);
            }else {
                ajax_return('没有找到对应的账号', false);
            }

        }
    }

    /**
     * 异步获取速卖通售后服务模板列表,返回下拉框的选项，方便调用统一接口
     */
    public function ajaxSmtAfterServiceList(){
        //账号
        $token_id = $this->input->get_post('token_id');

        if ($token_id){
            $data = $this->model->getTemplateList(array('select' => 'id, name', 'plat' => 6, 'token_id' => $token_id));
            $options = '';
            if ($data){
                foreach ($data as $row){
                    $options .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                }
            }
            unset($data);
            ajax_return('', true, $options);
        }else {
            ajax_return('账号错误', 'false');
        }
    }
}