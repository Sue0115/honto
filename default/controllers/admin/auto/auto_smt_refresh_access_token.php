<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-09-14
 * Time: 14:24
 */
set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");

class Auto_smt_refresh_access_token extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('MySmt');
        $this->load->model(array(
            'smt/Smt_user_tokens_model',
        ));
        $this->smt = new MySmt();
    }

    public function auto_refresh_access_token($token_id = '')
    {
        $option = array();
        $option['where']['token_status'] = 0;
        if (!empty($token_id)) //指定账号
        {
            $option['where']['token_id'] = $token_id;
        }

        $result = $this->Smt_user_tokens_model->getAll2Array($option);
        foreach ($result as $re) {
            $serverurl = "https://gw.api.alibaba.com/openapi/http/1/system.oauth2/getToken/" . $re['appkey'] . "";
            $postdata = "grant_type=refresh_token&client_id=" . $re['appkey'] . "&client_secret=" . $re['appsecret'] . "&refresh_token=" . $re['refresh_token'] . "";
            $json_data = $this->smt->postCurlHttpsData($serverurl, $postdata);
            $last_result = json_decode($json_data, true);
            if (isset($last_result['access_token'])) // 获得了新的access_token 更新 到数据库
            {
                $now = date('Y-m-d H;i:s', time());
                $update_data = array();
                $update_data['access_token'] = $last_result['access_token'];
                $update_data['access_token_date'] = $now;

                $update_option = array();
                $update_option['where']['token_id'] = $re['token_id'];

                $this->Smt_user_tokens_model->update($update_data, $update_option);

                echo "账号:" . $re['seller_account'] . " 刷新access_token   执行结果 " . $last_result['access_token'];
                echo '</br>';

            } else {
                echo "账号:" . $re['seller_account'] . " 刷新access_token   执行结果 " ;
                echo isset($last_result['error_description']) ? $last_result['error_description'] : $last_result['error'];
                echo '</br>';
            }
        }

    }


}