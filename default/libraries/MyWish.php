<?php
class MyWish{

	/**
	 * 获取wish线上product数据
	 * @param unknown_type $keyStr
	 * @param unknown_type $account
	 * @param unknown_type $time
	 */
	
	public $access_token = '';//是否有访问token
	
	public $code = '';//是否有wish提供的code
	
	public $proxy_address = '';//账号的代理ip地址
	
	public $account_info;
	
	
	//解析sku
	public function dealing_sku($oriangal_sku){
		//去掉sku中[]的信息，解析oriangal_sku
		if (stripos($oriangal_sku, '[') !== false) {
			$oriangal_sku = preg_replace('/\[.*\]/', '', $oriangal_sku);
		}
		//去掉sku中()的信息
		if (stripos($oriangal_sku, '(') !== false) {
			$oriangal_sku = preg_replace('/\(.*\)/', '', $oriangal_sku);
		}
		//去中括号后的sku中存在*
		if(strripos($oriangal_sku, '*') !== false){
			$skuArr = explode('*',$oriangal_sku);
			foreach($skuArr as $va){//如果不是数字的话，就是产品sku
				if(!is_numeric($va) && !empty($va)){
				 $oriangal_sku = $va;
				 break;
				}
			}
		}
		return $oriangal_sku;
	}

	
	 public function getWishProductByApi($keyStr,$account='',$time='',$s_num,$l_num){
	 	
	        $start = $s_num;
			$limit = $l_num;
			$since = $time;
			$apiArr=array();//api请求数组
			$apiRetrunArr = array();//存放返回的数据
			$apiArr['limit'] = urlencode($limit);
			$apiArr['start'] = urlencode($start);
			if($since != ''){
				$apiArr['since'] = urlencode($since);
			}
			
			$apiArr['key'] = ($keyStr);
			
			$apiString = http_build_query($apiArr);
			
			$url = "https://merchant.wish.com/api/v1/product/multi-get?";
			
			$apiUrl = $url.$apiString;
			
			$data = $this->getCurlData($apiUrl,'300');
			
			while($data === false){
				$data = $this->getCurlData($apiUrl,'300');
				if(!empty($data)){
				  break;
				}
			}

			$dataArr = json_decode($data,true);
			
			if(empty($dataArr['data'])){
			  return $apiRetrunArr;
			}
			
     		foreach($dataArr['data'] as $ordersInfoArr){					
				$apiRetrunArr[] = $ordersInfoArr;				
			}

        	return $apiRetrunArr;
	}
	

	
	public function get_one($key,$wish_id){
	
	    $url = "https://merchant.wish.com/api/v1/order?id=".trim($wish_id)."&key=".$key;
	
	    $data = $this->getCurlData($url);
	
	    return json_decode($data,true);
	
	}
	
	/**
	 * SKU处理返回数组
	 * type = 1 ,下单sku的处理
	 * type = 2 ,广告的sku，标记发货处理拆单的线上sku
	 */
	public function filter_sku($sku_array,$type=1){
	
	    $data = array();
	
	    //1.先去掉'+' eg 002*MHM083+MHM088
	    $tmp_sku = explode('+', $sku_array['sku']);
	
	    $sku_num = count($tmp_sku);
	
	    foreach ($tmp_sku as $k => $sku){
	        	
	        if($this->account_info['sku_type']==2){//sku的新解析
	            $pre_part = substr($sku,0,1);
	            $suff_part = substr($sku,$this->account_info[0]['sku_num']+1);
	            $sku = $pre_part.$suff_part;
	            $data[$k]['sku'] = $sku;
	        }else{
	            //3.忽略中括号内的信息
	            if (stripos($sku, '[') !== false) {
	                $sku = preg_replace('/\[.*\]/', '', $sku);
	            }
	            if (stripos($sku, '(') !== false && $type==2) {
	                $sku = preg_replace('/\(.*\)/', '', $sku);
	            }
	            //2.去掉* eg：002*MHM083
	            $tmp_s = explode('*', $sku);
	            $i = count($tmp_s)-1;
	
	            $data[$k]['sku'] = $tmp_s[$i];
	        }
	    }
	
	    return $data;
	
	}
	
	//获取单个产品的信息
	public function getProductInfoByProductID($keyStr,$productID){
	   $apiArr=array();//api请求数组
	   $apiArr['key'] = ($keyStr);
	   $apiArr['productID'] = ($productID);
	}
	
	//处理从wish线上同步下来的广告数据
	public function wishLineAdvertisingArr($arr,$account){
		
		$newArr = array();//存放广告的数组
		
		foreach($arr as $k => $v){
			
			//被wish下架了的不需要下下来，直接过滤掉
			if($v['Product']['review_status']=='rejected'){
				continue;
			}
			
			$sellerID = '';//销售id号
			
			$flag = 0;//存储sku的数量
			
			$Taga = '';//存放tags
			
			$skuCount = count($v['Product']['variants']);
			
			$newArr[$k]['products']['account'] = $account;
			
			$newArr[$k]['products']['productID'] = $v['Product']['id'];
			
			if($v['Product']['is_promoted']=='True'){
				$newArr[$k]['products']['is_promoted'] = 1;
			}else{
				$newArr[$k]['products']['is_promoted'] = 0;
			}
			
			$newArr[$k]['products']['review_status'] = $v['Product']['review_status'];
			$newArr[$k]['products']['product_description'] = $v['Product']['description'];
			$newArr[$k]['products']['product_name'] = $v['Product']['name'];
			$newArr[$k]['products']['parent_sku'] = $v['Product']['parent_sku'];
			
			//处理tags
			foreach($v['Product']['tags'] as $tag){
			  $Taga .=','.$tag['Tag']['name'];
			}
			$Tag = trim(substr($Taga,1));
			$newArr[$k]['products']['Tags'] = $Tag;
			foreach($v['Product']['variants'] as $key => $skuarray){
				$skuColor = '';//sku的颜色
				$skuSize = '';//sku的尺寸
				$sku = $skuarray['Variant']['sku'];
				$newArr[$k]['skus'][$key]['original_sku'] = $sku;
				//去掉sku中[]的信息
				if (stripos($sku, '[') !== false) {
					$sku = preg_replace('/\[.*\]/', '', $sku);
				}
				//去中括号后的sku中存在*
				if(strripos($sku, '*') !== false){
					$skuArr = explode('*',$sku);
					foreach($skuArr as $va){//如果不是数字的话，就是产品sku
						if(!is_numeric($va) && !empty($va)){
							$sku = $va;
							break;
						}
					}
				   if(is_numeric($skuArr[0])){
					 $newArr[$k]['skus'][$key]['sellerID']=$skuArr[0];	
					 if(empty($sellerID)){
					   $sellerID = $skuArr[0];
					 }
					 
				   }
				    $newArr[$k]['skus'][$key]['sku'] = trim($sku);//已经处理后的sku
				}else{
					$newArr[$k]['skus'][$key]['sku'] = trim($sku);//已经处理后的sku
				}
				
				//判断sku的颜色和尺寸是否存在
				if(isset($skuarray['Variant']['color'])){
				  $skuColor = $skuarray['Variant']['color'];
				}
				if(isset($skuarray['Variant']['size'])){
				  $skuSize = $skuarray['Variant']['size'];
				}
				
				$newArr[$k]['skus'][$key]['product_price'] = $skuarray['Variant']['price'];//sku价格
				$newArr[$k]['skus'][$key]['product_count'] = $skuarray['Variant']['inventory'];//sku库存
				$newArr[$k]['skus'][$key]['productID'] = $v['Product']['id'];//产品ID
				$newArr[$k]['skus'][$key]['shipping'] = $skuarray['Variant']['shipping'];//sku运费
				$newArr[$k]['skus'][$key]['soldNum'] = $v['Product']['number_sold'];//sku售出数
				$newArr[$k]['skus'][$key]['shipping_time'] = $skuarray['Variant']['shipping_time'];//sku运输时间
				$newArr[$k]['skus'][$key]['msrp'] = $skuarray['Variant']['msrp'];//sku打折后的价格
				$newArr[$k]['skus'][$key]['color'] = $skuColor;//sku的颜色
				$newArr[$k]['skus'][$key]['size'] = $skuSize;//sku的尺寸
				$newArr[$k]['skus'][$key]['main_image'] = $v['Product']['main_image'];//sku的主图
				$newArr[$k]['skus'][$key]['extra_image'] = $v['Product']['extra_images'];//sku的附图
				$newArr[$k]['skus'][$key]['accounts'] = $account;//sku的账号
				//线上sku的状态  0-下架  1-未下架
				if($skuarray['Variant']['enabled']=='True'){
					$newArr[$k]['skus'][$key]['enabled'] = 1;
					$flag +=1;
				}else{
					$newArr[$k]['skus'][$key]['enabled'] = 0;
				}
			}
			
			$newArr[$k]['products']['sellerID'] = $sellerID;
			
			//判断广告是否下架，广告下的sku有一个enabled=false，判断此广告下架
			if($flag==$skuCount){
				$newArr[$k]['products']['status'] = 0;//未下架
			}else{
				$newArr[$k]['products']['status'] = 1;//已下架
			}

		}
		return $newArr;
	}
	
	/**
	 * 更改wish在线sku的价格
	 */
	public function changeWishProductPrice($data){
      $url = 'https://merchant.wish.com/api/v1/variant/update?';
      $apiString = http_build_query($data);
      $apiUrl = $url.$apiString;
      $result = $this->getCurlData($apiUrl,5);
      for($i=1;$i>=1;$i++){
        if($result===false){
      	  $result = $this->getCurlData($apiUrl,5);
        }else{
          break;
        }
      }
      
     // var_dump($result);
      return json_decode($result,true);
	}
	
	/**
	 * 新增sku解析
	 * 加上账号编码
	 */
	public function deal_sku_new($accountInfo,$sku){
	  if($accountInfo['sku_type']==2){
	  
	  	    $pre_part = substr($sku,0,$accountInfo['sku_num']);
	        $suff_part = substr($sku,$accountInfo['sku_num']);
	        $sku = $pre_part.$accountInfo['account_code'].$suff_part;
	  	
      }
      return $sku;
	}
	
	/**
	 * 新增sku解析
	 * 去除销售代码
	 */
	public function deal_sku_code($accountInfo,$sku){
	   if($accountInfo['sku_type']==2){
	  
	  	    $pre_part = substr($sku,0,1);
	        $suff_part = substr($sku,$accountInfo['sku_num']+1);
	        $sku = $pre_part.$suff_part;
	  	
       }
      return $sku;
	}
	
	/**
	 * Curl http Get 数据
	 * 使用方法：
	 * getCurlData('http://www.test.cn/restServer.php');
	 */
	public function getCurlData($url,$time='120') {
	   if($this->access_token!='' && $this->code!=''){
			$urlString = str_replace('v1','v2',$url);
			//替换key值
			$key_len = stripos($urlString,'key');
			$url = substr($urlString,0,$key_len-1);
			$url = $url.'&access_token='.$this->access_token;
		}
	
		$curl = curl_init (); // 启动一个CURL会话
		curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, 0 ); // 对认证证书来源的检查
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 1 ); // 从证书中检查SSL加密算法是否存在
		curl_setopt ( $curl, CURLOPT_USERAGENT, $_SERVER ['HTTP_USER_AGENT'] ); // 模拟用户使用的浏览器
		curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, 1 ); // 使用自动跳转
		curl_setopt ( $curl, CURLOPT_AUTOREFERER, 1 ); // 自动设置Referer
	    if($this->proxy_address!=''){
		   curl_setopt ($curl, CURLOPT_PROXY, $this->proxy_address); 
		   curl_setopt($curl,CURLOPT_PROXYPORT,'808');
		}
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
		
		if($this->access_token!='' && $this->code!=''){
			$url = str_replace('v1','v2',$url);
			unset($data['key']);
			$data['access_token'] = $this->access_token;
		}

		$curl = curl_init (); // 启动一个CURL会话
		curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, 0 ); // 对认证证书来源的检查
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
		curl_setopt ( $curl, CURLOPT_USERAGENT, $_SERVER ['HTTP_USER_AGENT'] ); // 模拟用户使用的浏览器
		curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, 1 ); // 使用自动跳转
		curl_setopt ( $curl, CURLOPT_AUTOREFERER, 1 ); // 自动设置Referer
		
		if($this->proxy_address!=''){
		   curl_setopt ($curl, CURLOPT_PROXY, $this->proxy_address); 
		   curl_setopt($curl,CURLOPT_PROXYPORT,'808');
		}
		
		curl_setopt ( $curl, CURLOPT_POST, 1 ); // 发送一个常规的Post请求
		curl_setopt ( $curl, CURLOPT_POSTFIELDS, $data ); // Post提交的数据包
		curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 ); // 设置超时限制防止死循环
		curl_setopt ( $curl, CURLOPT_HEADER, 0 ); // 显示返回的Header区域内容
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
		$tmpInfo = curl_exec ( $curl ); // 执行操作
		if (curl_errno ( $curl )) {
			
			//die(curl_error ( $curl )); //异常错误
		}
		curl_close ( $curl ); // 关闭CURL会话
		return $tmpInfo; // 返回数据
	}
}