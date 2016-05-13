<?php
class  MyEbay {
	
	private $ebay_token; //应用的访问令牌

	private $devID; //开发者ID

	private $appID; //应用ID
	
	private $certID; //证书ID

	private $server_url; //请求URL

	private $api_level; //API 版本

	private $siteID; //ebay站点ID 如(0 = US, 2 = Canada, 3 = UK, ...)

	private $verb; //使用的接口名称

	private $service_name; //使用的service名称

	private $operation_name; //请求的操作名称
	
	private $headers__text_array = array(
	
									'service_name'=>'X-EBAY-SOA-SERVICE-NAME',
	
									'operation_name'=>'X-EBAY-SOA-OPERATION-NAME',
	
									'ebay_token'=>'X-EBAY-SOA-SECURITY-TOKEN',
	
									'api_level'=>'X-EBAY-API-COMPATIBILITY-LEVEL',
	
									'devID'=>'X-EBAY-API-DEV-NAME',
	
									'appID'=>'X-EBAY-API-APP-NAME',
	
									'certID'=>'X-EBAY-API-CERT-NAME',
	
									'verb'=>'X-EBAY-API-CALL-NAME',
	
									'siteID'=>'X-EBAY-API-SITEID'
	
									);

	private $site_id_array = array('us'=>0,'canada'=>2,'uk'=>3);

	private $headers_data;
	
	/**
	 * 
	 * 初始化
	 * @param $config
	 */
	function __construct($config = array()){
		
		if(is_array($config) && !empty($config)){
			
			if(isset($config['siteID'])){
				$config['siteID'] = $this->site_id_array[$config['siteID']];		
			}
	
			foreach ($config as $key => $v){
				$this->{$key} = $v;
			}
		}
	}
	
	/**
	 * 
	 * 创建头部信息
	 * @param $options
	 */
	function create_headers($options = null){
		
		$headers = array();
		
		$headers['X-EBAY-SOA-SERVICE-NAME'] = $this->service_name;
		
		$headers['X-EBAY-SOA-OPERATION-NAME'] = $this->operation_name;
		
		$headers['X-EBAY-SOA-SERVICE-VERSION'] = '1.1.0';
		
		$headers['X-EBAY-SOA-SECURITY-TOKEN'] = $this->ebay_token;
		
		$headers['X-EBAY-SOA-REQUEST-DATA-FORMAT'] = 'XML';
		
		$headers['X-EBAY-API-COMPATIBILITY-LEVEL'] = $this->api_level;
		
		$headers['X-EBAY-API-DEV-NAME'] = $this->devID;
		
		$headers['X-EBAY-API-APP-NAME'] = $this->appID;
		
		$headers['X-EBAY-API-CERT-NAME'] = $this->certID;
		
		$headers['X-EBAY-API-CALL-NAME'] = $this->verb;
		
		$headers['X-EBAY-API-SITEID'] = $this->siteID;
		
		if(is_array($options) && !empty($options)){
			
			foreach ($options as $k => $v){
				$headers[$this->headers__text_array[$k]] = $v;	
			}
			
		}
		
		$data = array();
		
		foreach ($headers as $k => $v){
			$data[] = $k.': '.$v;
		}
		
		$this->headers_data = $data;
		
		return $data;
		
	}

	/**
	 * 
	 * 发送http请求
	 * @param $data $data['headers']-头部信息,$data['request_body']-请求的xml
	 * @param $return_array 结果是否返回数组
	 */
	public function send_http_request($data = array(),$return_array = true){
		
		if(empty($data['request_body'])){
			return false;
		}
		
		if(is_array($data) && !empty($data['headers'])){
			$this->create_headers($data['headers']);
		}
		
		$headers = $this->headers_data;
		
		
		$connection = curl_init();

		//请求的URL地址
		curl_setopt($connection, CURLOPT_URL, $this->server_url);

		//使用HTTPS协议，服务器端不需要身份验证
		curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, false);

		//http header
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);

		//设置POST请求方式
		curl_setopt($connection, CURLOPT_POST, true);

		//设置请求的XML内容
		curl_setopt($connection, CURLOPT_POSTFIELDS, $data['request_body']);

		curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);

		$xml = curl_exec($connection);
		
		curl_close($connection);
		
		if($xml && $return_array){
			$xml = (array) simplexml_load_string ( $xml, 'SimpleXMLElement', LIBXML_NOCDATA );
		}

		return $xml;

	}
	
	function create_xml($data = array()){
	
		$xml = '<?xml version="1.0" encoding="utf-8" ?>';
		
	}

}