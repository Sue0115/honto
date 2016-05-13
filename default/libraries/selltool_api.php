<?php
//91Track 查询api类
class Selltool_api {

	private $token = 'qaW42HBCxHdXpuWeWKlVGmEa0nf/cBZlEarN02iar/bS8PtJhB+09wJadBY1SpYt';
	//private $token = 'mNBj9WmP/Dxw7Fr1hZlSKnmJ8BWDVtjqsGZ+cSOw1L6QPdXwsU4MJqktLzVVUYX6';

	private $url = 'http://api.91track.com/track?';

	function __construct($token =''){

		if($token){
			$this->token = $token;
		}

	}

	/*
	 *参数名称	参数说明
	 *Token	必选参数，api调用凭证，需要encode, 在 这里申请API Token， url encode工具
	 *Numbers	必选参数，跟踪号，多个跟踪号用逗号分割
	 *Culture	可选参数，语言，目前只支持中文（zh-cn）和英文（en），默认为中文
	 */
	public function track_numbers($numbers,$culture=''){

		$data = array();

		$data['numbers'] = $numbers;

		$data['token'] = urlencode($this->token);

		$url = 'numbers='.$data['numbers'].'&token='.$data['token'];

		if($culture){
			$url .='&culture='.$culture; 
		}

		$url = $this->url.$url;
		
		$result = $this->getCurlData($url);
		
		return json_decode($result,true);

	}

	/**
	 * Curl http Get 数据
	 * 使用方法：
	 * getCurlData('http://www.test.cn/restServer.php');
	 */
	public function getCurlData($url,$time='300') {
        
		$curl = curl_init (); // 启动一个CURL会话
		curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, 0 ); // 对认证证书来源的检查
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 1 ); // 从证书中检查SSL加密算法是否存在
		if(isset($_SERVER ['HTTP_USER_AGENT']) && !empty($_SERVER ['HTTP_USER_AGENT'])){
		  curl_setopt ( $curl, CURLOPT_USERAGENT, $_SERVER ['HTTP_USER_AGENT'] ); // 模拟用户使用的浏览器
		}
		curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, 1 ); // 使用自动跳转
		curl_setopt ( $curl, CURLOPT_AUTOREFERER, 1 ); // 自动设置Referer
		curl_setopt ( $curl, CURLOPT_CONNECTTIMEOUT, $time);
		curl_setopt ( $curl, CURLOPT_TIMEOUT, $time); // 设置超时限制防止死循环
		curl_setopt ( $curl, CURLOPT_HEADER, 0 ); // 显示返回的Header区域内容
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
		
		$tmpInfo = curl_exec ( $curl );
		//var_dump($tmpInfo);
		/*
		if (curl_errno ( $curl )) {
			$error = curl_error ( $curl ); //异常错误
			echo $error.'<br/>';
		}*/
		curl_close ( $curl ); // 关闭CURL会话
		return $tmpInfo; // 返回数据
	}

}