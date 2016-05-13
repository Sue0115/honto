<?php
/**
 * 
 * 速卖通API类
 * @author robin
 * @date 2013-7-15
 *
 */

class MySmt {
	const GWURL = 'gw.api.alibaba.com';
	public $_access_token;         //获取数据令牌
	public $_refresh_token;        //刷新令牌
	public $_access_token_date;    //获取令牌时间
	public $_seller_account;       //销售账号
	public $_appkey;               //应用key
	public $_appsecret;            //应用密匙
	public $_returnurl;            //回传地址
	public $_token_id;             //tokenId
	public $_accountSuffix; 	   //账号前缀
	public $_salesSuffix;          //销售前缀
	public $_customer_id;
	public $_version = 1;
	public $_curlErrorLogPath = "/data/smtAPICallLog/CurlErrorLog/";
	public $_curlErrorLog;
	private $_CI;
    
    //调试输出
    public $debug = false;
    
	function __construct() {
	    $this->_CI = & get_instance();
	}
	
	/**
	 * 设置token信息，返回变更后的信息以便修改
	 * @param [type] $tokenArr [description]
	 */
	public function setToken($tokenArr){
		$this->_token_id          = $tokenArr["token_id"];
		$this->_seller_account    = $tokenArr["seller_account"];
		$this->_appkey            = $tokenArr["appkey"];
		$this->_appsecret         = $tokenArr["appsecret"];
		$this->_returnurl         = $tokenArr["returnurl"];
		$this->_access_token_date = $tokenArr["access_token_date"];	
		$this->_refresh_token     = $tokenArr["refresh_token"];
		$this->_customer_id       = $tokenArr["customerservice_id"];
		$this->_accountSuffix     = $tokenArr['accountSuffix'];
		$this->_salesSuffix       = $tokenArr['salesSuffix'];
		$access_token             = $this->isResetAccesstoken();   //是否过期的accesstoken
		$this->_access_token      = $access_token == false ? $tokenArr["access_token"] : $access_token;
	}

	/**
	 * 使用access_token 令牌获取数据
	 * @param string $action api动作
	 * @param string $parameter 传输参数
	 * @param boolen $_aop_signature 是否需要签名
	 */
	public function getJsonData($action, $parameter, $_aop_signature=true) {
		//接口URL
		$app_url = "http://" . self::GWURL . "/openapi/";
		//apiinfo	aliexpress.open
		$apiInfo = "param2/" . $this->_version . "/aliexpress.open/{$action}/" . $this->_appkey;

		//参数
		$app_parameter_url = ($parameter ? "$parameter&" : '')."access_token=" . $this->_access_token;
		$sign_url = '';
		if ($_aop_signature) { //是否需要签名
			//获取对应URL的签名
			$sign     = $this->getApiSign ( $apiInfo, $app_parameter_url );
			$sign_url = "&_aop_signature=$sign"; //签名参数
		}
		//组装URL
		$get_url = $app_url . $apiInfo . '?' . $app_parameter_url . $sign_url;
		//if ( $this->debug ) echo $get_url. "\n";
		$result  = $this->getCurlData ( $get_url );
		return $result;
	}

	/**
	 * API交互，POST方式
	 * @param  [type] $api [description]
	 * @param  [type] $arr [description]
	 * @return [type]      [description]
	 */
	public function getJsonDataUsePostMethod($action, $parameter){
		//接口URL
		$app_url  = "http://" . self::GWURL . "/openapi/";
		//apiinfo	aliexpress.open
		$api_info = "param2/" . $this->_version . "/aliexpress.open/{$action}/" . $this->_appkey . "";
		$parameter['access_token'] = $this->_access_token;

		$parameter['_aop_signature'] = $this->getApiSignature($api_info, $parameter);

		//参数
		$result = $this->postCurlHttpsData ( $app_url.$api_info,  $parameter);

		return $result;
	}

	/**
	 * 速卖通上传图片到图片银行或临时目录专用
	 * @param  [type] $action     api名称
	 * @param  [type] $fileName   若为数组格式array('srcFileName' => $filename),字符串格式则为srcFileName=$filename
	 * @param  [type] $fileStream 图片流,二进制文件
	 * @return [type]             [description]
	 */
	public function uploadBankImage($action, $file, $fileName=''){
		//接口URL
		$app_url = "http://" . self::GWURL . "/fileapi/";
		//apiinfo	aliexpress.open
		$apiInfo = "param2/" . $this->_version . "/aliexpress.open/{$action}/" . $this->_appkey;
		$parameter= '';
		$fileName = ($fileName ? rawurlencode($fileName) : time().random(1000, 9999)).'.jpg';
		if ($action == 'api.uploadImage') {
			$param = 'fileName='.$fileName;
		}elseif ($action == 'api.uploadTempImage') {
			$param = 'srcFileName='.$fileName;
		}
		//参数
		$app_parameter_url = $param."&access_token=" . $this->_access_token;

		$sign_url = '';

		//获取对应URL的签名
		$sign     = $this->getApiSign ( $apiInfo, $app_parameter_url );
		$sign_url = "&_aop_signature=$sign"; //签名参数

		//组装URL
		$get_url = $app_url . $apiInfo . '?' . $app_parameter_url . $sign_url;

		$data = file_get_contents($file);
		$ch   = curl_init ();
		curl_setopt($ch, CURLOPT_URL, $get_url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-from-urlencoded'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		$result = curl_exec($ch);
		if (curl_error($ch)) {
			$this->setCurlErrorLog(curl_error ( $ch ));
			die(curl_error ( $ch )); //异常错误
		}
		curl_close($ch);
		return json_decode($result, true);
	}

	//原方法 保留下，日后学习
/*	public function uploadBankImageBySignature($action, $file, $fileName=''){

		$app_url  = 'http://'.self::GWURL.'/fileapi/param2/'.$this->_version.'/aliexpress.open/'.$action.'/'.$this->_appkey.'?access_token='.$this->_access_token;
		$param    = '';
		$fileName = ($fileName ? rawurlencode($fileName) : time().random(1000, 9999)).'.jpg';
		if ($action == 'api.uploadImage') {
			$param = '&fileName='.$fileName;
		}elseif ($action == 'api.uploadTempImage') {
			$param = '&srcFileName='.$fileName;
		}

		$data = file_get_contents($file);
	    $ch   = curl_init ();
	    curl_setopt($ch, CURLOPT_URL, $app_url.$param);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-from-urlencoded'));
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	    $result = curl_exec($ch);
	    if (curl_error($ch)) {
	    	$this->setCurlErrorLog(curl_error ( $ch ));
			die(curl_error ( $ch )); //异常错误
		}
		curl_close($ch);
		return json_decode($result, true);
	}*/

	/**
	 * 获取签名
	 * @param  [type] $apiInfo       [description]
	 * @param  [type] $parameter_arr [description]
	 * @return [type]                [description]
	 */
	public function getApiSignature($apiInfo, $parameter_arr){
	    ksort($parameter_arr);
		$sign_str = '';
	    foreach ($parameter_arr as $key=>$val)
	        $sign_str .= $key . $val;
		$sign_str  = $apiInfo . $sign_str;
		$code_sign = strtoupper ( bin2hex ( hash_hmac ( "sha1", $sign_str, $this->_appsecret, true ) ) );
	    return $code_sign;
	}

	/**
	 * 获取acees_token 
	 * 判断access_token是否过期(10小时)
	 * 
	 */
	public function isResetAccesstoken() {
		$now   = date ( "Y-m-d H:i:s" );
		$hours = (strtotime ( $now ) - strtotime ( $this->_access_token_date )) / 60 / 60;
		if ($hours > 9.5) { //大于10小时(提前半小时)
			$json   = $this->resetAccessToken (); //获取最新的access_token
			$data   = json_decode ( $json, true );
			$this->_CI->db->query("update smt_user_tokens set access_token = '".$data["access_token"]."', access_token_date = NOW() where token_id = ".$this->_token_id);
			return $data["access_token"];
		} else {
			return false;
		}
	}

	/**
	 * 请求应用授权
	 * 返回临时授权码code
	 * @param string $code_sign
	 */
	public function appAuthor() {
		//http://gw.api.alibaba.com/auth/authorize.htm?client_id=4716323&redirect_uri=http://localhost:12508/auth_callback_url&site=aliexpress&_aop_signature=21B5A73981D6BC4560FC3C4C84578BA6AE79EA64
		$app_author_url = "http://" . self::GWURL . "/auth/authorize.htm?client_id=" . $this->_appkey . "&site=china&redirect_uri=" . $this->_returnurl . "&_aop_signature={$this->getCodeSign ()}";
		return $this->getCurlData($app_author_url);
	
	}
	public function appAuthorNew() {

		$this->_returnurl =  admin_base_url("smt/account_manage/getSmtToken");
		$app_author_url = "http://" . self::GWURL . "/auth/authorize.htm?client_id=" . $this->_appkey . "&site=aliexpress&redirect_uri=" . $this->_returnurl . "&_aop_signature={$this->getCodeSignNew ()}";
		echo("<script>window.open('".$app_author_url."');</script>");

	}
	
	/**
	 * 
	 * 使用code获取令牌
	 * 返回令牌   refresh_token 用于刷新令牌  access_token 用于获取数据  memderID 用户ID
	 */
	public function getAppCode($code) {
		$getAppCodeUrl = "https://" . self::GWURL . "/openapi/http/".$this->_version."/system.oauth2/getToken/" . $this->_appkey . "";
		$postdata = "grant_type=authorization_code&need_refresh_token=true&client_id=" . $this->_appkey . "&client_secret=" . $this->_appsecret . "&redirect_uri=" . $this->_returnurl . "&code=" . $code . "";
		return $this->postCurlHttpsData ( $getAppCodeUrl, $postdata );
	}
	
	/**
	 * 
	 * refreshToken换取accessToken  POST https
	 * @param string $refresg_token
	 */
	public function resetAccessToken() {
		$serverurl = "https://" . self::GWURL . "/openapi/http/".$this->_version."/system.oauth2/getToken/" . $this->_appkey . "";
		$refresh_token = $this->_refresh_token;
		$postdata = "grant_type=refresh_token&client_id=" . $this->_appkey . "&client_secret=" . $this->_appsecret . "&refresh_token=" . $refresh_token . "";
		return $this->postCurlHttpsData ( $serverurl, $postdata );
	}


	public function getCodeSignNew(){
		$code_arr = array ('client_id' => $this->_appkey, 'redirect_uri' => $this->_returnurl, 'site' => 'aliexpress' );
		ksort ( $code_arr );
		$sign_str = '';
		foreach ( $code_arr as $key => $val )
			$sign_str .= $key . $val;
		//die($sign_str);
		$code_sign = strtoupper ( bin2hex ( hash_hmac ( "sha1", $sign_str, $this->_appsecret, true ) ) );
		return $code_sign;
	}
	/**
	 * 
	 * 参数签名算法只使用请求参数作为签名因子进行签名，仅针对客户端或WEB端授权时请求临时令牌code
	 * @param string $appKey
	 * @param string $appSecret
	 * @param string $redirectUrl
	 */
	public function getCodeSign() {
		//生成签名
		$code_arr = array ('client_id' => $this->_appkey, 'redirect_uri' => $this->_returnurl, 'site' => 'china' );
		ksort ( $code_arr );
		$sign_str = '';
		foreach ( $code_arr as $key => $val )
			$sign_str .= $key . $val;
		//die($sign_str);	
		$code_sign = strtoupper ( bin2hex ( hash_hmac ( "sha1", $sign_str, $this->_appsecret, true ) ) );
		return $code_sign;
	}

	/**
	 * 
	 * API签名算法主要是使用urlPath和请求参数作为签名因子进行签名，主要针对api 调用
	 * @param $apiInfo URL信息
	 * @param $strcode 参数
	 */
	public function getApiSign($apiInfo, $strcode) {
		$code_arr = explode ( "&", $strcode );//去掉&
		$newcode_arr = array ();
		foreach ( $code_arr as $key => $val ) {
			$code_narr = explode ( "=", $val );//分割=
			$newcode_arr [$code_narr [0]] = $code_narr [1];//重组数组
		}
		ksort ( $newcode_arr );//排序
		$sign_str="";
		foreach ( $newcode_arr as $key => $val ){//获取值
			$sign_str .= $key . rawurldecode($val);
		}
		$sign_str = $apiInfo . $sign_str;//连接
		//加密
        //if ( $this->debug ) echo $sign_str. "\n";
		$code_sign = strtoupper ( bin2hex ( hash_hmac ( "sha1", $sign_str, $this->_appsecret, true ) ) );
		return $code_sign;
	}

	/**
	 * 返回错误码
	 * 
	 * @param array $codeArr
	 */
	public function smtApiErrorCode($codejson){
		$jsonResult=json_decode($codejson);
		if($jsonResult->error_code!=""){
			return false;
		}
		return true;
	}

	/**
	 * 保存设置Curl日志
	 * 
	 */
	public function setCurlErrorLog($str){
		$time    = date ( "Y-m-d H:i:s", time () );
		$now     = date ( "Y-m-d", time () );
		$nowTime = date ( "H-i-s", time () );
		$logPath = "$this->_curlErrorLogPath"."/$now";
		@mkdir ( $logPath, 0777, true );
		$logPathUrl = "$logPath/$nowTime.txt";
		$this->_curlErrorLog=$logPathUrl;
		$this->writeDatelog($this->_curlErrorLog,"时间：$time 原因：$str");
	}
	
	/**
	 * 保存日志
	 * @param string $log
	 * @param string $text
	 */
	public function writeDatelog($log, $text = '') {
		$fp = fopen ( $log, 'a+' );
		@fwrite ( $fp, $text );
		fclose ( $fp );
        //输出日志
        if ( $this->debug ) echo str_replace("\r\n", "\n", $text);
	}

	/**
	 * Curl http Get 数据
	 * 使用方法：
	 * getCurlData('http://www.test.cn/restServer.php');
	 */
	public function getCurlData($remote_server) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $remote_server );
        //curl_setopt ( $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=utf-8'));
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true ); // 获取数据返回
		curl_setopt ( $ch, CURLOPT_BINARYTRANSFER, true ); // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
		$output = curl_exec ( $ch );
		if (curl_errno ( $ch )) {
			$this->setCurlErrorLog(curl_error ( $ch ));
			die(curl_error ( $ch )); //异常错误
		}
		curl_close ( $ch );
		return $output;
	}
	
	/**
	 * Curl http Post 数据 --sw20141126modify
	 * 使用方法：
	 * $post_string = "app=request&version=beta";
	 * postCurlData('http://www.test.cn/restServer.php',$post_string);
	 */
	public function postCurlData($remote_server, $post_string) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $remote_server );
		// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_string );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		$data = curl_exec ( $ch );
		if (curl_errno ( $ch )) {
			$this->setCurlErrorLog(curl_error ( $ch ));
			die(curl_error ( $ch )); //异常错误
		}
		curl_close ( $ch );
		return $data;
	}
	
	/**
	 * Curl https Post 数据
	 * 使用方法：
	 * $post_string = "app=request&version=beta";
	 * request_by_curl('https://www.test.cn/restServer.php',$post_string);
	 */
	public function postCurlHttpsData($url, $data) { // 模拟提交数据函数
		$curl = curl_init (); // 启动一个CURL会话
		curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, 0 ); // 对认证证书来源的检查
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
		curl_setopt ( $curl, CURLOPT_USERAGENT, $_SERVER ['HTTP_USER_AGENT'] ); // 模拟用户使用的浏览器
		curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, 1 ); // 使用自动跳转
		curl_setopt ( $curl, CURLOPT_AUTOREFERER, 1 ); // 自动设置Referer
		curl_setopt ( $curl, CURLOPT_POST, 1 ); // 发送一个常规的Post请求
		curl_setopt ( $curl, CURLOPT_POSTFIELDS, $data ); // Post提交的数据包
		curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 ); // 设置超时限制防止死循环
		curl_setopt ( $curl, CURLOPT_HEADER, 0 ); // 显示返回的Header区域内容
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
		$tmpInfo = curl_exec ( $curl ); // 执行操作
		if (curl_errno ( $curl )) {
			$this->setCurlErrorLog(curl_error ( $curl ));
			die(curl_error ( $curl )); //异常错误
		}
		curl_close ( $curl ); // 关闭CURL会话
		return $tmpInfo; // 返回数据
	}

	/**
	 * 
	 * 解析json数据     返回toReturn 数组
	 * @param json $jsondata
	 */
	public function getJsonArrayResult($jsondata){
		$jsondata = json_decode($jsondata,true);
		return $jsondata["result"]["toReturn"];
	}
	
	/**
	 * 
	 * 解析json数据     返回toReturn对象信息
	 * @param json $jsondata
	 */
	public function getJsonObjResult($jsondata){
		$jsondata = json_decode($jsondata);
		return $jsondata->result->toReturn;
	}
	
	/**
	 * 
	 * 解析json数据     返回total值
	 * @param json $jsondata
	 */
	public function getJsonTotal($jsondata){
		$jsondata = json_decode($jsondata);
		return $jsondata->result->total;
	}
}