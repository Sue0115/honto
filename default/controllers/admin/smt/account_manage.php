<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-01-18
 * Time: 15:03
 */
header("Content-type:text/html;charset=utf-8");

class Account_manage extends Admin_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->library('MySmt');
        $this->load->model(array(
            'smt/Smt_user_tokens_model',
            'sharepage'
        ));
        $this->smt       = new MySmt();
        $this->userToken = $this->Smt_user_tokens_model;
    }


    public function manage_index(){
        $where = array(); //查询条件
        $in = array(); //in查询条件
        $string = array(); //URL参数
        $curpage = (int)$this->config->item('site_page_num');
        $per_page = (int)$this->input->get_post('per_page');
        $search = $this->input->get_post('search');
        if (isset($search['token_id']) && $search['token_id']) {
            $where['token_id'] = trim($search['token_id']);
            $string[] = 'search[token_id]=' . trim($search['token_id']);
        }

        $curpage = 100;
        $options = array(
            'where' => $where,
            'where_in' => $in,
            'page' => $curpage,
            'per_page' => $per_page,
        );


        $return_data = array('total_rows' => true);
        $data_array = $this->userToken->getAll($options, $return_data);
        $c_url = admin_base_url('smt/account_manage/manage_index');
        $url = $c_url . '?' . implode('&', $string);
        $page = $this->sharepage->showPage($url, $return_data['total_rows'], $curpage);

        $token_options = array('where' => array('token_status' => 0));
        $token_array   = $this->userToken->getAll2Array($token_options);

        $data = array(
            'data' => $data_array,
            'token_array'=>$token_array,
            'search' => $search,
            'page' => $page,
            'totals' => $return_data['total_rows'],
            'c_url' => $c_url,
        );
        $this->_template('admin/smt/account_manage', $data);
    }

    public function do_action(){
        $token_id=$_POST['token_id'];
        $type = $_POST['type'];
        $option = array();
        $option['where']['token_id'] = $token_id;
        $token_array = $this->Smt_user_tokens_model->getOne($option,true);
        $this->smt->setToken($token_array);
        if($type=="check"){
            $para ='keyword=case';
            $result = $this->smt->getJsonData("api.recommendCategoryByKeyword", $para);
            $result= json_decode($result,true);
            if(isset($result['success'])&&$result['success']){
                ajax_return("账号正常",1);
            }else{
                $msg = isset($result['error_message'])?$result['error_message']:"API调用失败";
                ajax_return($msg,2);
            }



        }elseif($type=="refresh"){
            $result =    $this->smt->resetAccessToken();
            $result = json_decode($result,true);

            if(isset($result['access_token'])){
                $update_data= array();
                $update_data['access_token'] = $result['access_token'];
                $update_data['access_token_date'] = date("Y-m-d H:i:s",time());
                $re = $this->Smt_user_tokens_model->update($update_data,$option);
                if($re){
                    ajax_return("刷新成功",1);
                }else{
                    ajax_return("刷新失败",2);
                }
            }else{

                $msg = isset($result['error_description'])?$result['error_description']:'API请求错误';
                $msg = isset($result['error_message'])?$result['error_message']:$msg;
                ajax_return($msg,2);
            }
        }else{
            ajax_return("未知错误",2);
        }


    }

    public function auto_refresh_refresh_token($token_id=''){
        if(empty($token_id)){
            echo "请指定账号token_id";
            exit;
        }

        $option = array();
        $option['where']['token_id'] = $token_id;

        $token_array = $this->Smt_user_tokens_model->getOne($option,true);
        $this->smt->setToken($token_array);
        $this->smt->appAuthorNew();
        echo '<script>window.close();</script>'; exit;

    }

    public function getSmtToken()
    {
        if(isset($_GET['code'])){
            $code= $_GET['code'];
           echo '复制页面代码，输入到对话框中:'.$code;
        }
        exit;
    }


    public function code_update_refresh_token(){
        $code = trim($_POST['code']);
        $option = array();
        $option['where']['token_id'] = trim($_POST['token_id']);
        $token_array = $this->Smt_user_tokens_model->getOne($option,true);
        $this->smt->setToken($token_array);
        $result =  $this->smt->getAppCode($code);
        $result = json_decode($result,true);
        if(isset($result['refresh_token'])){ //SUCCESS

            $update_data = array();
            $update_data['refresh_token']=$result['refresh_token'];
            $update_data['access_token']=$result['access_token'];
            $update_data['access_token_date']=date('Y-m-d H:i:s',time());
            $update_data['next_call_time']=date('Y-m-d H:i:s', strtotime(mb_substr($result['refresh_token_timeout'], 0, 14)));

           $re= $this->Smt_user_tokens_model->update($update_data,$option);
            if($re){
                ajax_return('授权成功',1);
            }else{
                ajax_return('授权失败',1);
            }
        }else{
            ajax_return('授权失败',1);
        }
    }
}