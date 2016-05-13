<?php
/**
 * create:2015-10-16
 * 
 */
set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");

class Auto_wish_get_tokens extends MY_Controller
{
	private $wish;
	
    function __construct()
    {
        parent::__construct();
        $this->load->library('MyWish');
        $this->load->model(array(
            'wish/wish_user_tokens_model',
        ));
        $this->wish = new MyWish();
    }

    public function auto_refresh_access_token($accounts='')
    {
        $option = array();
        $option['where']['refresh_token !='] = '';
        if(!empty($accounts)){
          $option['where']['account_name'] = $accounts;
        }
        $result = $this->wish_user_tokens_model->getAll2Array($option);

        foreach ($result as $re) {
           $this->getAccessTokenByRefresh($re);
        }

    }
    
	//使用refreshtoken重新获取access_token
	function getAccessTokenByRefresh($account){
		$getData = array();
		$getData['client_id'] 		= $account['client_id'];
		$getData['client_secret'] 	= $account['client_secret'];
		$getData['refresh_token'] 	= $account['refresh_token'];
		$getData['grant_type'] 		= 'refresh_token';
		
		echo $account['account_name'].'账号开始执行更换access_token<br/>';

		$url = 'https://merchant.wish.com/api/v2/oauth/refresh_token';
		$return = $this->wish->postCurlHttpsData($url,$getData);
		
		var_dump($return);
		
		$return_data = json_decode($return,true);
		
		if($return_data['code']==0 && !empty($return_data['data'])){
			$up_data = array();
			$op = array();
			$up_data['access_token']  = $return_data['data']['access_token'];
			$up_data['expiry_time']   = $return_data['data']['expiry_time'];
			$up_data['refresh_token'] = $return_data['data']['refresh_token'];
			$op['where'] = array('account_name'=>$account['account_name']);
			$this->wish_user_tokens_model->update($up_data,$op);
			echo $account['account_name'].'账号更换access_token成功<br/>';
		}else{
		    echo $account['account_name'].'账号更换access_token失败<br/>';
		}
    
}


}