<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type: text/html; charset=utf-8");
/**
 * wish刊登
 * @authors zengrihua
 * @date    2015-05-04 
 */
class wish extends Admin_Controller{

	protected $wish;
    protected $userToken;
    
    private $products_type = array('1'=>'草稿','2'=>'待发布');
    
    private $products_auditing = array('1'=>'未审核','2'=>'审核已通过','3'=>'审核未通过');//wish自动刊登的审核状态
    
    private $products_upload = array('1'=>'未刊登','2'=>'已刊登');
    
    private $price_status = array('1'=>'未调价','2'=>'已调价','3'=>'异常');
    
    private $price_type = array('1'=>'指定价格','2'=>'上调','3'=>'下调');//调价类型
    
	function __construct(){
        parent::__construct();
        $this->load->helper('http_helper');
        $this->load->library('MyWish');
        $this->load->Model(array(
           'wish/wish_user_tokens_model','wish/wish_product_model','wish/wish_product_detail_model',
           'sharepage','print/products_data_model','wish_import_data_model','wish_price_task_model',
           'wish/wish_publish_log_model'
        ));
        $this->model = $this->wish_user_tokens_model;
        $this->wish = new MyWish();
        //$this->reflashAccesstoken();
    }
    
    //实例化的时候更新所有的账号的过期token
	public function reflashAccesstoken(){
		$accountArr = $this->model->getWishTokenList(array());
		foreach($accountArr as $account){
			//第一次获取access_token
//		    if($account['code']!='' && $account['access_token']==''){
//		    	$this->getAccessToken($account);
//		    	continue;
//		    }
			//用refresh_token重新获取access_token
			if($account['refresh_token']!='' && $account['access_token']!=''){
			  $time = time();
			  if($account['expiry_time']-$time<259200){//如果已经过期，重新获取access_token
			  	$this->getAccessTokenByRefresh($account);
			  }
			}
			continue;
		}
	}
	
	/**
	 * 调用API获取access_token，第一次获取
	 */
	public function getAccessToken($account){
		$getData = array();
		$getData['client_id'] 		= $account['client_id'];
		$getData['client_secret'] 	= $account['client_secret'];
		$getData['code'] 			= $account['code'];
		$getData['grant_type'] 		= 'authorization_code';
		$getData['redirect_uri'] 	= $account['redirect_uri'];

		$url = 'https://merchant.wish.com/api/v2/oauth/access_token';
		$return_data = $this->wish_post_API($url,$getData);

		if($return_data['code']==0 && !empty($return_data['data'])){
			$option = array();
			$in_data = array();
			$in_data['access_token']  = $return_data['data']['access_token'];
			$in_data['expiry_time']   = $return_data['data']['expiry_time'];
			$in_data['refresh_token'] = $return_data['data']['refresh_token'];
			$option['where'] = array('account_name'=>$account['account_name']);
			$this->model->update($in_data,$option);
		}
		
	}
	/**
	 * 用refresh重新获取访问token
	 * 并把wish的token重新更新
	 */
	public function getAccessTokenByRefresh($account){
		$getData = array();
		$getData['client_id'] 		= $account['client_id'];
		$getData['client_secret'] 	= $account['client_secret'];
		$getData['refresh_token'] 	= $account['refresh_token'];
		$getData['grant_type'] 		= 'refresh_token';
		$url = 'https://merchant.wish.com/api/v2/oauth/refresh_token';
		$return_data = $this->wish_post_API($url,$getData);
		if($return_data['code']==0 && !empty($return_data['data'])){
			$option = array();
			$up_data = array();
			$up_data['access_token']  = $return_data['data']['access_token'];
			$up_data['expiry_time']   = $return_data['data']['expiry_time'];
			$up_data['refresh_token'] = $return_data['data']['refresh_token'];
			$option['where'] = array('account_name'=>$account['account_name']);
			$this->model->update($up_data,$option);
		}
	}
    
    //根据账号名设置访问token和code
	public function set_access_token($account_name){
		$accountInfo = $this->model->getKeyByAccount($account_name);
		$this->wish->code 		= $accountInfo['code'];
		$this->wish->access_token = $accountInfo['access_token'];
		$this->wish->proxy_address = $accountInfo['proxy_address'];
	}
	
	/**
	 * 新增和修改广告
	 * wish刊登
	 */
	public function productInfo(){
		//获取wish账号列表
	    $option = array();
	  	$account = $this->model->getWishTokenList($option);
	  	$newAccount = array();
	  	foreach($account as $a){
	  	  $newAccount[$a['account_name']] = $a;
	  	}
	  	$productID = '';
	  	$productInfo = array();
	  	$productID = $this->input->get_post('id');
	  	$newProductInfo = array();
	  	if(!empty($productID)){
	  	  $options['select'] = array("{$this->wish_product_model->_table}.*",'pd.*');
  		
  		  $join[] = array('erp_wish_product_detail pd',"pd.productID={$this->wish_product_model->_table}.productID");
  		
  		  $options['join'] = $join;
  		  
  		  $options['where'] = array("{$this->wish_product_model->_table}.productID" => $productID);
  		  
  		  $productInfo = $this->wish_product_model->getAll2array($options);
  		  
  		  foreach($productInfo as $k => $vp){
  		  	$par_sku_arr = explode('*',$vp['parent_sku']);
  		    $newProductInfo[$k] = $vp;
  		    $newProductInfo[$k]['oriange_sku'] = $par_sku_arr[1];
  		  }
	  	}

	  	$data = array(
	  	  'productInfo' => $newProductInfo,
	  	  'account' => $newAccount
	  	);

		$this->_template('admin/publish/wish/product_info',$data);
	}
	
	
	/**
	 * ajax获取账户刊登代码
	 */
	public function getPublishCode(){

	  $result['status'] = 0;//0-新账号，去掉sku前缀后缀；1-老帐号，添加刊登后缀
	  $result['code'] = '';//刊登后缀
	  $account = $this->input->get_post('account');
	  $accountInfo = $this->wish_user_tokens_model->getKeyByAccount($account);
	  if($accountInfo['publish_code']!='' && $accountInfo['sku_type']==1){
	      $result['status'] = 1;
	      $result['code'] = $accountInfo['publish_code'];
	  }
	  echo json_encode($result);
	  exit;

	}
	
	/**
	 * 模糊搜索sku
	 */
	public function search_sku(){
	  $result['status'] = 0;
	  $sku = strtoupper($this->input->post('sku_search'));
	  $sku_new_pre = $this->input->post('sku_new_pre');
	  
	  $option = array();
	  $where = array();
	  //$like = array();
	  $skuArr = array();//存放最后找到的sku数组
	 // $like['products_sku'] = $sku;
	  $this->db->like('products_sku', $sku, 'after');
	  $where['product_warehouse_id'] = 1000;
	 // $where['products_status_2 !='] = 'sellWaiting';
	  $where['productsIsActive'] = 1;
	  $option = array(
	    //'like' => $like,
	    'where'=> $where
	  );
	  $results = $this->products_data_model->getAll2array($option);
	  foreach($results as $re){
	  	$skus = $re['products_sku'];
	  	if(!empty($sku_new_pre)){
	  	  //将sku拆分成两部分
	  	  $pre = substr($re['products_sku'],0,1);
	  	  $suff = substr($re['products_sku'],1);
	  	  $skus = $pre.$sku_new_pre.$suff;
	  	}
	    $skuArr[] = $skus;
	  }
	  if(!empty($skuArr)){
	    $result['status'] = 1;
	  }
	  $result['skus'] = $skuArr;
      echo json_encode($result);
      die;
	}
	
	/**
	 * 搜索sku的英文介绍
	 * @param unknown_type $sku
	 */
	public function getSkuInfoLike(){ 
		$sku = strtoupper($this->input->post('sku_search'));
		$rs = $this->db->select('products_sku,products_html_mod') 
		->like('products_sku', $sku, 'after')->group_by('products_sku') 
		->get($this->products_data_model->_table)->result_array();
		$new_rs = array();
		foreach($rs as $k => $v){
		  if($k<1){
		  	$new_rs[] = htmlspecialchars_decode($v['products_html_mod']);
		  }
		} 
		$result['skuInfo'] = $new_rs;
		echo json_encode($result);
		die;
	}
	
	
	/**
	 * ajax读取目录
	 */
//	public function ajaxUploadDirImage(){
//
//        $dirName  = trim($this->input->get_post('dirName'));
//        $opt      = trim($this->input->get_post('opt'));
//
//        //本程序的上级目录
//        $topDir = str_replace('\\', '/', dirname(ROOTPATH));
//        //$topDir = 'www.erp.com';
//        //图片库中ebay图片的位置
//        $ebayPicDir = $topDir . '/erp/imgServer/upload/SMT';
//
//        $skuDir = $ebayPicDir . '/' . $dirName;
//        
//        $imge_url = 'http://120.24.100.157:70/imgServer/upload/SMT/'.$dirName;
//
//        if (strtoupper($opt) == 'SP'){
//            $spArray = array('SP', 'sp', 'Sp', 'sP');
//            $hasFlag = false;
//            foreach ($spArray as $sp){
//                $skuDir = $ebayPicDir . '/' . $dirName.'/'.$sp;
//                if (file_exists($skuDir)){ //文件夹还是存在的
//                    $hasFlag = true;
//                    break;
//                }
//            }
//            if (!$hasFlag){
//                ajax_return('SKU对应的文件夹不存在(若发现该SKU目录的名称含有小写，请让修改)', false);
//            }
//        }else {
//            if (!file_exists($skuDir)) { //SKU对应的文件夹不存在
//                ajax_return('SKU对应的文件夹不存在(若发现该SKU目录的名称含有小写，请让修改)', false);
//            }
//        }
//
//        if (!is_dir($skuDir)) {
//            ajax_return('SKU对应的信息不是文件夹，请检查路径', false);
//        }
//
//        $dh = opendir($skuDir);
//
//        //图片扩展列表
//        $imageExt = defineWishImageExd();
//
//        $success = array();
//        $error   = array();
//
//       
//        while ($file = readdir($dh)) {
//            if ($file != '.' && $file != '..' && !is_dir($skuDir . '/' . $file)) {
//                $exd = strtolower(getFileExtendName($file));
//                if (in_array($exd, $imageExt)) { //是速卖通的图片
//                    $temp      = explode('.', $file);
//                    $fileName  = array_shift($temp);
//                    //$imagePath = $skuDir . '/' . $file; //真实的图片路径
//                    $imagePath = $imge_url . '/' . $file; //真实的图片路径
//                     $success[] = $imagePath;
//                }
//            }
//        }
//        closedir($dh);
//
//        ajax_return('', true, $success);
//    }
    
    /**
     * 先从新图片系统中获取该sku下的图片
     * 如果没有获取到，ajax读取美国图片服务器
     */
    public function ajaxUploadDirImage(){
        
    	$shui = $this->input->get_post('shui');//判断是否有水印的图片上传
        
    	$dirName  = strtoupper(trim($this->input->get_post('dirName')));
    	
        $opt      = trim($this->input->get_post('opt'));
        
        $host_url = $this->input->get_post('host_url');
        
        if(!empty($shui)){//如果获取无水印图片
           
            $url = "http://imgurl.moonarstore.com/get_image.php?dirName=wish/".$dirName;//美国图片服务器脚本的路径
            $get_data = curlRequest($url,'','GET');
            $result = json_decode($get_data,true);
            
            if(empty($result)){
                $url = "http://imgurl.moonarstore.com/get_image.php?dirName=wish/".$dirName."&dir=SP";//美国图片服务器脚本的路径
                $get_data = curlRequest($url,'','GET');
                $result = json_decode($get_data,true);
            }
            if(!empty($host_url)){
                foreach($result as $ke => $v){
                    if($host_url!='img.ibship.com'){//该账号的图片链接的端口有问题，先停用81端口
                        $result[$ke] = str_replace('imgurl.moonarstore.com',$host_url.':81',$v);
                    }else{
                        $result[$ke] = str_replace('imgurl.moonarstore.com',$host_url,$v);
                    }
                }
            }
            ajax_return('', true, $result);
        }

	    //$url = '120.24.100.157:3000/api/sku/'.$dirName.'?include-sub=true&distinct=true';
	    $url = 'http://120.24.100.157:70/getSkuImageInfo/getSkuImageInfo.php?distinct=true&include_sub=true&sku='.$dirName;

        $get_data = curlRequest($url,'','GET');
        
        $result = json_decode($get_data,true);

        $suo = true;//是否返回缩略图
      
        if(!empty($result)){//如果新图片系统没有图片，用美国的图片服务器
        	
	        if(!empty($host_url)){
	          foreach($result as $ke => $v){
	          	
	          	//added by andy.
	          	$photo_name = $v['filename'];
	          	$s_url = '/getSkuImageInfo/sku/'.$photo_name;
	            $result[$ke] = 'http://'.$host_url.$s_url;
	            
	          }
	        }
	        
        }else{
	        if(!empty($opt)){
	            $url = "http://imgurl.moonarstore.com/get_image.php?dirName=".$dirName."&dir=".$opt;//美国图片服务器脚本的路径
	        }else{
	            $url = "http://imgurl.moonarstore.com/get_image.php?dirName=".$dirName;//美国图片服务器脚本的路径
	        }
	        $get_data = curlRequest($url,'','GET');
	        $result = json_decode($get_data,true);
	      
	        if(!empty($host_url)){
	          foreach($result as $ke => $v){
	              if($host_url!='img.ibship.com'){//该账号的图片链接的端口有问题，先停用81端口
	                  $result[$ke] = str_replace('imgurl.moonarstore.com',$host_url.':81',$v);
	              }else{
	                  $result[$ke] = str_replace('imgurl.moonarstore.com',$host_url,$v);
	              }
	           
	          }
	        }
        }
     

        ajax_return('', true, $result);
    }
	public function ajaxUploadDirImage_backup(){
        
    	$shui = $this->input->get_post('shui');//判断是否有水印的图片上传
        
    	$dirName  = strtoupper(trim($this->input->get_post('dirName')));
    	
        $opt      = trim($this->input->get_post('opt'));
        
        $host_url = $this->input->get_post('host_url');
        
        if(!empty($shui)){//如果获取无水印图片
           
            $url = "http://imgurl.moonarstore.com/get_image.php?dirName=wish/".$dirName;//美国图片服务器脚本的路径
            $get_data = curlRequest($url,'','GET');
            $result = json_decode($get_data,true);
            
            if(empty($result)){
                $url = "http://imgurl.moonarstore.com/get_image.php?dirName=wish/".$dirName."&dir=SP";//美国图片服务器脚本的路径
                $get_data = curlRequest($url,'','GET');
                $result = json_decode($get_data,true);
            }
            if(!empty($host_url)){
                foreach($result as $ke => $v){
                    if($host_url!='img.ibship.com'){//该账号的图片链接的端口有问题，先停用81端口
                        $result[$ke] = str_replace('imgurl.moonarstore.com',$host_url.':81',$v);
                    }else{
                        $result[$ke] = str_replace('imgurl.moonarstore.com',$host_url,$v);
                    }
                }
            }
            ajax_return('', true, $result);
        }

	    $url = '120.24.100.157:3000/api/sku/'.$dirName.'?include-sub=true&distinct=true';

        $get_data = curlRequest($url,'','GET');
        
        $result = json_decode($get_data,true);

        $suo = true;//是否返回缩略图
      
        if(!empty($result)){//如果新图片系统没有图片，用美国的图片服务器
	        if(!empty($host_url)){
	          foreach($result as $ke => $v){
	          	if($suo==true){
	          	  $photo_name = str_replace('/image/','',$v['url']);
	          	  $s_url = '/image-resize/100x-x75/'.$photo_name;
	          	}else{
	          	  $s_url = $v['url'];
	          	}
	            $result[$ke] = 'http://'.$host_url.':3000'.$s_url;
	          }
	        }
        }else{
	        if(!empty($opt)){
	            $url = "http://imgurl.moonarstore.com/get_image.php?dirName=".$dirName."&dir=".$opt;//美国图片服务器脚本的路径
	        }else{
	            $url = "http://imgurl.moonarstore.com/get_image.php?dirName=".$dirName;//美国图片服务器脚本的路径
	        }
	        $get_data = curlRequest($url,'','GET');
	        $result = json_decode($get_data,true);
	      
	        if(!empty($host_url)){
	          foreach($result as $ke => $v){
	              if($host_url!='img.ibship.com'){//该账号的图片链接的端口有问题，先停用81端口
	                  $result[$ke] = str_replace('imgurl.moonarstore.com',$host_url.':81',$v);
	              }else{
	                  $result[$ke] = str_replace('imgurl.moonarstore.com',$host_url,$v);
	              }
	           
	          }
	        }
        }
     

        ajax_return('', true, $result);
    }
    
 
    /**
     * wish刊登保存并发布
     * first--先把刊登数据存入erp_wish_product表和erp_wish_product_detail表
     * second--使用wish的API吧数据post上去(https://merchant.wish.com/api/v1/product/add)
     * 草稿修改的保存并发布
     * 待发布修改的保存并发布
     */
    public function doAction(){
    	
      header('Content-Type: text/html; Charset=utf-8');
      set_time_limit(0);
      
      $wish_accountArr = array(//wish新账号
          'yuffalyp@126.com','gbstore@126.com','wishwdf@126.com','ychw2016@126.com','hkdajin@126.com','Lancefr'
      );
      $wish_sale_code = array(//wish新销售代码
          '330','331','332','333','334','335','336','337','338','339','340','360','366'
      );
      
      $uid = $this->user_info->id;//登录用户id

      //提及数据
      $posts = $this->input->post();

      if(!isset($posts['choose_account'][0]) && empty($posts['choose_account'][0])){
        ajax_return('刊登失败,未勾选账号', false);
        exit;
      }
      
      if(in_array($posts['choose_account'][0],$wish_accountArr)){
    
          //获取sku的销售前缀
          $skus = $posts['sku'][0];
          $preArr = explode('*',$skus);
          if(!in_array($preArr[0],$wish_sale_code)){
              ajax_return('请使用新的销售代码刊登', false);
              exit;
          }
          
      }
    
      
     
      $productID = (isset($posts['old_product_id']) && ($posts['old_product_id'] != '')) ? $posts['old_product_id'] : '';

      $action = $posts['action'];
      
      //注意Tag标签不能超过十个
      $tags = $posts['Tags'];
      $tagsArr = explode(',',$tags);
      if(count($tagsArr)>10){
        ajax_return('Tag标签不能超过10个', false);
        exit;
      }
      
      if($action=='saveToPost'){//保存为待发布的情况
         $this->saveToPost($posts);
      }
      
      if($action=='save'){//编辑草稿的时候，点击保存按钮的情况
         $this->draft_save($posts);
      }
      
      if($action=='saveToDraft'){//刊登保存为草稿
        $this->saveToDraft($posts);
      }

      //获取所有账号，注意，刊登的时候不能多帐号刊登
      $accountArr = $posts['choose_account'];
      if(count($accountArr)>1){
        ajax_return('刊登失败,不能多帐号同时刊登', false);
        exit;
      }
  
      //根据账号获取key值
      $accountInfo = $this->wish_user_tokens_model->getKeyByAccount($accountArr[0]);
      
      //处理sku的步骤
      $sku = strtoupper(trim($posts['sku'][0]));
      
	  $sku = $this->wish->deal_sku_new($accountInfo,$sku);
   
      //必须参数
      $post_product['main_image']      = trim($posts['detailPicList'][0]);									 
      $post_product['name']      	   = trim($posts['subject']);
      $post_product['parent_sku']      = strtoupper(trim($posts['parent_sku']));
      
      //$post_product['sku']       	   = $sku;
      $post_product['sku']       	   = strtoupper(trim($posts['parent_sku']));
      
      $post_product['inventory']       = trim($posts['inventory']);
      
      //$post_product['price']		   = trim($posts['prices'][0]);
      $post_product['price']		   = trim($posts['price']);
      
      $post_product['shipping']  	   = trim($posts['shipping']);
      $description 					   = trim($posts['detail']);
	  $description					   = str_replace('<p>',' ',$description);
	  $description					   = str_replace('</p>',' ',$description);
      $descripts					   = str_replace('&nbsp;',' ',$description);
      //$post_product['description']	   = preg_replace("/(\r\n|\n|\r|\t)/i", '', $descripts);
      $post_product['description']	   = strip_tags($descripts);
      $post_product['tags']   		   = trim($posts['Tags']);
      $post_product['key'] 			   = trim($accountInfo['wish_key']);

       //主副图处理,附图以|链接
      unset($posts['detailPicList'][0]);
      $fu_image = '';//存放附图
      foreach($posts['detailPicList'] as $f){
        $fu_image .= '|'.trim($f);
      }
      $fu_image = substr($fu_image,1);
      $post_product['extra_images']     = trim($fu_image);
      
      //可选参数
     // $post_product['color']  		   = trim($posts['color'][0]);
      //$post_product['size']			   = trim($posts['size'][0]);
      $post_product['msrp']   		   = trim($posts['msrp']);
      $post_product['shipping_time']    = trim($posts['shipping_time']);
      $post_product['brand']    		   = trim($posts['brand']);
      $post_product['landing_page_url'] = trim($posts['landing_page_url']);
      $post_product['upc'] 			   = trim($posts['upc']);

      //组装插入erp_wish_product表里的数据
      $product = array();
      $product['account'] 			  = $accountArr[0];
      $product['publishedTime'] 	  = date('Y-m-d H:i:s');
      $product['sellerID'] 			  = $uid;
      $product['is_promoted'] 		  = 0;//0-在售     1-促销
      $product['status'] 		  	  = 0;//0-未下架  1-已下架
      $product['product_description'] = $post_product['description'];
      $product['product_name'] 		  = $post_product['name'];
      $product['parent_sku'] 		  = $posts['parent_sku'];
      $product['Tags']				  = $post_product['tags'];
      $product['product_type_status'] = 0;
      
      
      //事务开启
  	  $this->db->trans_begin();
  	  
  	  $product_id = $this->wish_product_model->add($product);
//      }

  	  $flag = false;
  	  $flagcount = 0;
  	  $product_detail_idArr = array();//存放产品详情id的数组
  	  foreach($posts['sku'] as $k => $v){
	  	  //组装插入erp_wish_product_detail表的数据
	  	  
	      
	      $original_sku = $this->wish->deal_sku_new($accountInfo,$v);
	      
	      //处理sku的步骤
	      $sk = $this->wish->dealing_sku($v);
	      $skus = $this->wish->deal_sku_code($accountInfo,$sk);
	     
	      $colorsa = str_replace('_',' ',$posts['color'][$k]);
	      
	      $product_detail = array();
	      $product_detail['sellerID'] 		= $uid;
	      $product_detail['original_sku'] 	= $original_sku;
	      $product_detail['product_price']  = $post_product['price'];
	      $product_detail['product_count']  = $post_product['inventory'];
	      $product_detail['shipping'] 		= $post_product['shipping'];
	      $product_detail['soldNum'] 		= 0;//默认售出个数为0
	      $product_detail['enabled'] 		= 1;//默认未下架
	      $product_detail['shipping_time']  = $post_product['shipping_time'];
	      $product_detail['msrp'] 			= $post_product['msrp'];
	      $product_detail['color'] 			= $posts['color'][$k];
	      $product_detail['size'] 			= $posts['size'][$k];
	      $product_detail['product_price'] 	= $posts['prices'][$k];
	      $product_detail['main_image'] 	= $post_product['main_image'];
	      $product_detail['extra_image'] 	= $post_product['extra_images'];
	      $product_detail['accounts']		= $accountArr[0];
	      $product_detail['sku']			= $skus;

	      $product_detail_id = $this->wish_product_detail_model->add($product_detail);
	      $product_detail_idArr[] = $product_detail_id;
	      if($product_detail_id){
	        $flagcount +=1;
	      }
  	  }
  	  if(count($posts['sku'])== $flagcount){
  	    $flag = true;
  	  }

      if($this->db->trans_status() === TRUE && $product_id && $flag){
        $this->db->trans_commit();//事务结束
      }else{
  	    $this->db->trans_rollback();
  	    ajax_return('刊登时，广告插入数据库失败', false);
  	  }
      	
        //去除上传数组中，为空的键名
        foreach($post_product as $k => $p){
          if(empty($p) && $k!='shipping'){
            unset($post_product[$k]);
          }
        }

        $this->set_access_token($accountArr[0]);

        //本地表插入成功以后，开始上传到wish
        $url = 'https://merchant.wish.com/api/v1/product/add';
        for($i=0;$i<4;$i++){
        	
         $return = $this->wish->postCurlHttpsData($url,$post_product);
   		
   		 $result = json_decode($return,true);
       
          if(isset($result['code'])){
            break;
          }
          
          sleep(3);
        }

        //刊登到wish上成功，更新本地erp_wish_product表和erp_wish_product_detail表
        if($result['code'] == 0 && !empty($result['data']['Product'])){
        	
          $return_data = array();
          
          $up_product = array();//要更新到erp_wish_product表的数据

          $return_data = $result['data']['Product'];
          //上传成功后，更新erp_wish_product表的数据
          $up_product['productID'] = $return_data['id'];
          $up_product['updateTime'] = date('Y-m-d H:i:s');
          $up_product['review_status'] = $return_data['review_status'];

          $where_p = array('id'=>$product_id);

          $status_p = $this->wish_product_model->update($up_product,array('where'=>$where_p));

          $reasonArr = array();
        
          foreach($posts['sku'] as $ke => $s){
          	
          	  //if($ke >=1){//两个以上sku 的情况
          	    $add_data = array();
          	    
          	    //父sku
          	    $parent_sku =  $this->wish->deal_sku_new($accountInfo,strtoupper($posts['parent_sku']));
          	    
          	    //处理sku的步骤
			    $skuss = strtoupper($s);;
			    $sku = $this->wish->deal_sku_new($accountInfo,$skuss);
			    
			    //属性sku的主图
			  //  $sku_mainImage = str_replace(':81','',$posts[$posts['color'][$ke]]);
			    
			  //  $colorsaa = str_replace('_',' ',$posts['color'][$ke]);
			    
          	    $add_url = 'https://merchant.wish.com/api/v1/variant/add';
          	    $add_data['parent_sku'] = $parent_sku;
          	    $add_data['sku'] 		= $sku;
          	    $add_data['color'] 		= $posts['color'][$ke];
          	    $add_data['size'] 		= $posts['size'][$ke];
          	    $add_data['inventory']  = $posts['inventory'];
          	    $add_data['price'] 		= $posts['prices'][$ke];
          	    $add_data['shipping'] 	= $posts['shipping'];
          	    $add_data['msrp'] 		= $posts['msrp'];
          	    $add_data['shipping_time'] = $posts['shipping_time'];

			  	if(isset($posts['mul_color_one']['color'][$posts['color'][$ke]][0])&&(!empty($posts['mul_color_one']['color'][$posts['color'][$ke]][0]))){
					$add_data['main_image'] = $posts['mul_color_one']['color'][$posts['color'][$ke]][0];
				}elseif(isset($posts['mul_color_one']['size'][$posts['size'][$ke]])&&($posts['mul_color_one']['size'][$posts['size'][$ke]][0])){
					$add_data['main_image'] = $posts['mul_color_one']['size'][$posts['size'][$ke]][0];
				}else{
					$add_data['main_image'] = $post_product['main_image'];
				}
          	    $add_data['key']  =  $accountInfo['wish_key'];
          	    //去除上传数组中，为空的键名
		        foreach($add_data as $kes => $ps){
		          if(empty($ps) && $kes!='shipping'){
		            unset($add_data[$kes]);
		          }
		        }
		
		        for($i=1;$i<5;$i++){
		        
		          $add_return = $this->wish->postCurlHttpsData($add_url,$add_data);
   		
		   		  $add_result = json_decode($add_return,true);
		       
		          if(isset($add_result['code'])){
		            break;
		          }
		          
		          sleep(3);
		          
		        }
			  if(isset($posts['mul_color_one']['color'][$posts['color'][$ke]][0])||isset($posts['mul_color_one']['size'][$posts['size'][$ke]][0])){ //说明存在多属性图片
				  $update_url = 'https://merchant.wish.com/api/v1/variant/add';
				  $update_data['sku'] =	$sku;
				  $update_data['main_image'] = $add_data['main_image'];
				  $this->wish->postCurlHttpsData($update_url,$update_data);
			  }
		        
		        if($add_result['code']!=0){
			        //写入wish刊登错误日志
		            $data = array();
			    	$data['uid'] = $uid;
			    	$data['result'] = var_export($add_result,true);
			    	$data['result_json'] = var_export($add_return,true);
			    	$data['api_time'] = date('Y-m-d H:i:s');
			    	$data['account'] = $accountInfo['account_name'];
			    	$this->wish_publish_log_model->add($data);
		        }
		        
          	    $reasonArr[] = $add_result;
          	  //}

          	  $up_product_detail = array();//要更新到erp_wish_product_detail表的数据
	          $up_product_detail['productID'] = $return_data['id'];
	          $where_pd = array('id'=>$product_detail_idArr[$ke]);
	          $status_pd = $this->wish_product_detail_model->update($up_product_detail,array('where'=>$where_pd));
          }
       
          if( $status_p && $status_pd){
            ajax_return('published wish online and update erp data success,productID is '.$return_data['id'], true);
          }else{
            ajax_return('published wish online success,but update erp data failed,productID is '.$return_data['id'], true);
          }
        }elseif($result['code']==1000&&$result['data']==2227){
        	
        	$msg = '';
        	
       		foreach($posts['sku'] as $ke => $s){
          	
          	  //if($ke >=1){//两个以上sku 的情况
          	    $add_data = array();
          	    
          	    //父sku
          	    $parent_sku =  $this->wish->deal_sku_new($accountInfo,strtoupper($posts['parent_sku']));
          	    
          	    //处理sku的步骤
			    $skuss = strtoupper($s);;
			    $sku = $this->wish->deal_sku_new($accountInfo,$skuss);
			    
			    //属性sku的主图
			   // $sku_mainImage = str_replace(':81','',$posts[$posts['color'][$ke]]);
			    
			   // $colorsaa = str_replace('_',' ',$posts['color'][$ke]);
			    
          	    $add_url = 'https://merchant.wish.com/api/v1/variant/add';
          	    $add_data['parent_sku'] = $parent_sku;
          	    $add_data['sku'] 		= $sku;
          	    $add_data['color'] 		= $posts['color'][$ke];
          	    $add_data['size'] 		= $posts['size'][$ke];
          	    $add_data['inventory']  = $posts['inventory'];
          	    $add_data['price'] 		= $posts['prices'][$ke];
          	    $add_data['shipping'] 	= $posts['shipping'];
          	    $add_data['msrp'] 		= $posts['msrp'];
          	    $add_data['shipping_time'] = $posts['shipping_time'];
				if(isset($posts['mul_color_one']['color'][$posts['color'][$ke]][0])&&(!empty($posts['mul_color_one']['color'][$posts['color'][$ke]][0]))){
					$add_data['main_image'] = $posts['mul_color_one']['color'][$posts['color'][$ke]][0];
				}elseif(isset($posts['mul_color_one']['size'][$posts['size'][$ke]])&&($posts['mul_color_one']['size'][$posts['size'][$ke]][0])){
					$add_data['main_image'] = $posts['mul_color_one']['size'][$posts['size'][$ke]][0];
				}else{
					$add_data['main_image'] = $post_product['main_image'];
				}
          	    $add_data['key']  =  $accountInfo['wish_key'];
          	    //去除上传数组中，为空的键名
		        foreach($add_data as $kes => $ps){
		          if(empty($ps) && $kes!='shipping'){
		            unset($add_data[$kes]);
		          }
		        }

				if(isset($posts['mul_color_one']['color'][$posts['color'][$ke]][0])||isset($posts['mul_color_one']['size'][$posts['size'][$ke]][0])){ //说明存在多属性图片
					$update_url = 'https://merchant.wish.com/api/v1/variant/add';
					$update_data['sku'] =	$sku;
					$update_data['main_image'] = $add_data['main_image'];
					$this->wish->postCurlHttpsData($update_url,$update_data);
				}
		
		        for($i=1;$i<5;$i++){
		        
		          $add_return = $this->wish->postCurlHttpsData($add_url,$add_data);
   		
		   		  $add_result = json_decode($add_return,true);
		       
		          if(isset($add_result['code'])){
		            break;
		          }
		          
		          sleep(3);
		          
		        }
		        
		        if($add_result['code']==0 && !empty($add_result['data'])){
		          //更新wish附表的产品ID
		          $up_product_detail = array();//要更新到erp_wish_product_detail表的数据
		          $up_product_detail['productID'] = $add_result['data']['Variant']['product_id'];
		          $where_pd = array('id'=>$product_detail_idArr[$ke]);
		          $status_pd = $this->wish_product_detail_model->update($up_product_detail,array('where'=>$where_pd));
		          
		          //更新主表数据
		          $up_product = array();//要更新到erp_wish_product表的数据

		          $up_product['productID'] = $add_result['data']['Variant']['product_id'];
		          $up_product['updateTime'] = date('Y-m-d H:i:s');
		          $where_p = array('id'=>$product_id);
		          $status_p = $this->wish_product_model->update($up_product,array('where'=>$where_p));
		          $msg .= '子sku'.$sku.'published wish online and update erp data success,productID is'.$add_result['data']['Variant']['product_id'].'<br/>';
		          
			       
		        }else{
		           //写入wish刊登错误日志
		            $data = array();
			    	$data['uid'] = $uid;
			    	$data['result'] = var_export($add_result,true);
			    	$data['result_json'] = var_export($add_return,true);
			    	$data['api_time'] = date('Y-m-d H:i:s');
			    	$data['account'] = $accountInfo['account_name'];
			    	$this->wish_publish_log_model->add($data);
			    	$msg .= '子sku'.$sku.'published wish online failed,reason:'.$result['message'].'<br/>';
		        }
		       
          	    $reasonArr[] = $add_result;
          	  //}

          	  
          }
          //返回子sku的刊登信息
          ajax_return($msg, true);
          
        }else{

          //写入wish刊登错误日志
            $data = array();
	    	$data['uid'] = $uid;
	    	$data['result'] = var_export($result,true);
	    	$data['result_json'] = var_export($return,true);
	    	$data['api_time'] = date('Y-m-d H:i:s');
	    	$data['account'] = $accountInfo['account_name'];
	    	$this->wish_publish_log_model->add($data);
	    	
           ajax_return('published wish online failed,reason:'.$result['message'], false);
        }
   
    }
    
    

    /**
     * wish刊登的时候直接点击另存为草稿按钮
     */
    public function saveToDraft($posts){
    	
       $uid = $this->user_info->id;//登录用户id
    	
       $accountArr = $posts['choose_account'];
       
       $msg = '';

       foreach($accountArr as $account){
       	
         //根据账号获取key值
         $accountInfo = $this->wish_user_tokens_model->getKeyByAccount($account);

	       $productID = time().rand(1000,100000000);
	      
	       //组装插入erp_wish_product表里的数据
		    $product = array();
		    $product['account'] 			  = $account;
		    $product['publishedTime'] 	  	  = date('Y-m-d H:i:s');
		    $product['status'] 		  	 	  = 0;//0-未下架  1-已下架
		    $product['is_promoted'] 		  = 0;//0-在售     1-促销
		    $product['sellerID'] 			  = $uid;
		    $product['productID']			  = $productID;
		    $description 					  = strip_tags(trim($posts['detail']),'');
	      	$descriptions	   				  = str_replace('&nbsp;','',$description);
		    $product['product_description']	  = $descriptions;
		    $product['product_name'] 		  = trim($posts['subject']);
		    $product['parent_sku'] 		 	  = trim($posts['parent_sku']);
		    $product['Tags']				  = trim($posts['Tags']);
		    $product['product_type_status']   = 1;
	
		    //事务开启
		  	$this->db->trans_begin();
		  	$product_id = $this->wish_product_model->add($product);
		  	
	       	  $flag = false;
		  	  $flagcount = 0;
		  	  $product_detail_idArr = array();//存放产品详情id的数组
		  	  foreach($posts['sku'] as $k => $v){
			  	  //组装插入erp_wish_product_detail表的数据
			  	  
		  	  	  //处理sku的步骤
		  	  	  $sku = $this->wish->dealing_sku($v);
		  	  	  if(!empty($accountInfo['account_code']) && !empty($accountInfo['sku_num'])){
		  	  	    $pre_part = substr($sku,0,$accountInfo['sku_num']);
		  	  	    $suff_part = substr($sku,$accountInfo['sku_num']);
		  	  	    $sku = $pre_part.$accountInfo['account_code'].$suff_part;
		  	  	  }
			      $product_detail = array();
			      $product_detail['productID'] = $productID;
			      $product_detail['sellerID'] 		= $uid;
			      $product_detail['original_sku'] 	= $v;
			      $product_detail['product_price']  = trim($posts['prices'][0]);
			      $product_detail['product_count']  = trim($posts['inventory']);
			      $product_detail['shipping'] 		= trim($posts['shipping']);
			      $product_detail['soldNum'] 		= 0;//默认售出个数为0
			      $product_detail['enabled'] 		= 1;//默认未下架
			      $product_detail['shipping_time']  = trim($posts['shipping_time']);
			      $product_detail['msrp'] 			= trim($posts['msrp']);
			      $product_detail['color'] 			= $posts['color'][$k];
			      $product_detail['size'] 			= $posts['size'][$k];
			      $product_detail['product_price'] 	= $posts['prices'][$k];
			      $product_detail['accounts']		= $account;
			      $product_detail['sku']			= $sku;
		
			      $product_detail_id = $this->wish_product_detail_model->add($product_detail);
			     
			      $product_detail_idArr[] = $product_detail_id;
			      if($product_detail_id){
			        $flagcount +=1;
			      }
		  	  }
		  	  if(count($posts['sku'])== $flagcount){
		  	    $flag = true;
		  	  }
		  	  if($product_id && $flag && $this->db->trans_status() === TRUE){
	          	$this->db->trans_commit();//事务结束
	          	$msg .="账号{$account}另存为草稿成功,虚拟产品ID为{$productID}<br/>";
	            
		  	  }else{
		  	    $this->db->trans_rollback();
	  	    	$msg .="账号{$account}另存为草稿失败<br/>";
		  	  }
      }
 
	  ajax_return($msg, true);
      
   }
    
    
	/**
     * wish修改草稿的时候点击保存按钮
     */
    public function draft_save($posts){

      $uid = $this->user_info->id;//登录用户id
      
      $productID = trim($posts['old_product_id']);

      //组装更新erp_wish_product表里的数据
      $product = array();
      $product['account'] 			  = trim($posts['choose_account']);
      $product['updateTime'] 	  = date('Y-m-d H:i:s');
      $product['sellerID'] 			  = $uid;
      $product['product_description'] = trim($posts['detail']);
      $product['product_name'] 		  = trim($posts['subject']);
      $product['parent_sku'] 		  = trim($posts['parent_sku']);
      $product['Tags']				  = trim($posts['Tags']);

       //事务开启
  	  $this->db->trans_begin();
  	  
  	  $product_id = $this->wish_product_model->update($product,array('where'=>array('productID'=>$productID)));

      if(!$product_id){
	     $this->db->trans_rollback();//事务回滚
	    //  continue;
	  }

  	  //删除该productID下的详情表数据
      $de = $this->wish_product_detail_model->deleteByProductID($productID);
	  if(!$de){
	     $this->db->trans_rollback();//事务回滚
	     // continue;
	  }

       //主副图处理,附图以|链接
      $main_image = $posts['detailPicList'][0];
      unset($posts['detailPicList'][0]);
      $fu_image = '';//存放附图
      foreach($posts['detailPicList'] as $f){
        $fu_image .= '|'.$f;
      }
      $fu_image = substr($fu_image,1);
      
      $flag = false;
  	  $flagcount = 0;
  	  $product_detail_idArr = array();//存放产品详情id的数组
  	  foreach($posts['sku'] as $k => $v){
	  	  //组装插入erp_wish_product_detail表的数据
	  	  
  	  	  //处理sku的步骤
  	  	  $sku = $this->wish->dealing_sku($v);
  	  	  if(!empty($accountInfo['account_code']) && !empty($accountInfo['sku_num'])){
  	  	    $pre_part = substr($sku,0,$accountInfo['sku_num']);
  	  	    $suff_part = substr($sku,$accountInfo['sku_num']);
  	  	    $sku = $pre_part.$accountInfo['account_code'].$suff_part;
  	  	  }
	      $product_detail = array();
	      $product_detail['sellerID'] 		= $uid;
	      $product_detail['original_sku'] 	= $v;
	      $product_detail['productID']		= $productID;
	      $product_detail['product_price']  = trim($posts['price']);
	      $product_detail['product_count']  = trim($posts['inventory']);
	      $product_detail['shipping'] 		= trim($posts['shipping']);
	      $product_detail['soldNum'] 		= 0;//默认售出个数为0
	      $product_detail['enabled'] 		= 1;//默认未下架
	      $product_detail['shipping_time']  = trim($posts['shipping_time']);
	      $product_detail['msrp'] 			= trim($posts['msrp']);
	      $product_detail['color'] 			= trim($posts['color'][$k]);
	      $product_detail['size'] 			= trim($posts['size'][$k]);
	      $product_detail['product_price'] 	= trim($posts['prices'][$k]);
	      $product_detail['main_image'] 	= $main_image;
	      $product_detail['extra_image'] 	= $fu_image;
	      $product_detail['accounts']		= trim($posts['choose_account']);
	      $product_detail['sku']			= $this->wish->dealing_sku($v);
	      $product_detail_id = $this->wish_product_detail_model->add($product_detail);

	      $product_detail_idArr[] = $product_detail_id;
	      if($product_detail_id){
	        $flagcount +=1;
	      }
  	  }
  	  if(count($posts['sku'])== $flagcount){
  	    $flag = true;
  	  }
  	  if($this->db->trans_status() === TRUE && $product_id && $flag){
      	
        $this->db->trans_commit();//事务结束
        
        ajax_return('保存成功',true);
        
  	  }else{
  	    $this->db->trans_rollback();
  	    ajax_return('保存失败', false);
  	  }

    }
    
    /**
     * wish保存为待发布
     * 刊登的时候保存为待发布
     * 修改的时候保存为待发布
     */
    public function saveToPost($posts){
    	
      $uid = $this->user_info->id;//登录用户id
      
      $productID = (isset($posts['old_product_id']) && ($posts['old_product_id'] != '')) ? $posts['old_product_id'] : '';
      
      
      if($productID !== ''){//修改草稿的情况，该数据不重新插入，更新产品表，产品详情表的数据先重新删除再插入
      	
      	  //根据productID获取草稿的广告信息
      	 // $old_data = $this->wish_product_model->getInfoByProductID($productID);  
      	
	       //组装更新erp_wish_product表里的数据
	      $product = array();
	      $product['account'] 			  = trim($posts['choose_account']);
	      $product['updateTime'] 	  = date('Y-m-d H:i:s');
	      $product['sellerID'] 			  = $uid;
	      $product['product_description'] = trim($posts['detail']);
	      $product['product_name'] 		  = trim($posts['subject']);
	      $product['parent_sku'] 		  = trim($posts['parent_sku']);
	      $product['Tags']				  = trim($posts['Tags']);
	      $product['product_type_status'] = 2;//1-草稿  2-保存为待发布
	       //事务开启
	  	  $this->db->trans_begin();
	  	  
	  	  $product_id = $this->wish_product_model->update($product,array('where'=>array('productID'=>$productID)));
	      if(!$product_id){
		     $this->db->trans_rollback();//事务回滚
		   //   continue;
		  }
	  	  
	  	  //删除该productID下的详情表数据
	      $de = $this->wish_product_detail_model->deleteByProductID($productID);
		  if(!$de){
		     $this->db->trans_rollback();//事务回滚
		   //   continue;
		  }
      }else{
	      //组装插入erp_wish_product表里的数据
	      $product = array();
	      $product['account'] 			  = trim($posts['choose_account']);
	      $product['productID']			  = $productID;
	      $product['publishedTime'] 	  = date('Y-m-d H:i:s');
	      $product['sellerID'] 			  = $uid;
	      $product['is_promoted'] 		  = 0;//0-在售     1-促销
	      $product['status'] 		  	  = 0;//0-未下架  1-已下架
	      $product['product_description'] = trim($posts['detail']);
	      $product['product_name'] 		  = trim($posts['subject']);
	      $product['parent_sku'] 		  = trim($posts['parent_sku']);
	      $product['Tags']				  = trim($posts['Tags']);
	      $product['product_type_status'] = 2;//1-草稿  2-保存为待发布
	      
	       //事务开启
	  	  $this->db->trans_begin();
	  	  
	  	  $product_id = $this->wish_product_model->add($product);
      }

      
      
       //主副图处理,附图以|链接
      $main_image = $posts['detailPicList'][0];
      unset($posts['detailPicList'][0]);
      $fu_image = '';//存放附图
      foreach($posts['detailPicList'] as $f){
        $fu_image .= '|'.$f;
      }
      $fu_image = substr($fu_image,1);
      
      $flag = false;
  	  $flagcount = 0;
  	  $product_detail_idArr = array();//存放产品详情id的数组
  	  foreach($posts['sku'] as $k => $v){
	  	  //组装插入erp_wish_product_detail表的数据
	      $product_detail = array();
	      $product_detail['sellerID'] 		= $uid;
	      $product_detail['original_sku'] 	= $v;
	      $product_detail['productID']		= $productID;
	      $product_detail['product_price']  = trim($posts['price']);
	      $product_detail['product_count']  = trim($posts['inventory']);
	      $product_detail['shipping'] 		= trim($posts['shipping']);
	      $product_detail['soldNum'] 		= 0;//默认售出个数为0
	      $product_detail['enabled'] 		= 1;//默认未下架
	      $product_detail['shipping_time']  = trim($posts['shipping_time']);
	      $product_detail['msrp'] 			= trim($posts['msrp']);
	      $product_detail['color'] 			= trim($posts['color'][$k]);
	      $product_detail['size'] 			= trim($posts['size'][$k]);
	      $product_detail['product_price'] 	= trim($posts['prices'][$k]);
	      $product_detail['main_image'] 	= $main_image;
	      $product_detail['extra_image'] 	= $fu_image;
	      $product_detail['accounts']	    = $posts['choose_account'];
	      $product_detail['sku']			= $this->wish->dealing_sku($v);

	      $product_detail_id = $this->wish_product_detail_model->add($product_detail);
	      $product_detail_idArr[] = $product_detail_id;
	      if($product_detail_id){
	        $flagcount +=1;
	      }
  	  }
  	  if(count($posts['sku'])== $flagcount){
  	    $flag = true;
  	  }
  	  if($this->db->trans_status() === TRUE && $product_id && $flag){
      	
        $this->db->trans_commit();//事务结束
        
        ajax_return('保存为待发布成功',true);
        
  	  }else{
  	    $this->db->trans_rollback();
  	    ajax_return('保存为待发布失败', false);
  	  }

    }
    
    /**
     * wish平台，post数据的方法
     * url   要发送的网址
     * apiArr  要发送的api数组
     */
    public function wish_post_API($url,$apiArr){
    
   		$return = $this->wish->postCurlHttpsData($url,$apiArr);
   		
   		return json_decode($return,true);
			
    }
    
    /**
     * 另存为草稿的时候要显示的账号
     * wish导入数据的时候要显示的账号，action=import
     */
    public function showAccountToCopyProduct()
    {
    	$action = '';
    	$action = $this->input->get_post('action');
        $options['select'] = array('token_id', 'account_name');

        $userInfo = $this->wish_user_tokens_model->getWishTokenList($option=array());//获取所有账号的用户信息
      
        $data              = array(
            'account_list' => $userInfo,
        	'action'	   => $action
        );
        $this->template('admin/wish/wish_account_copy_product', $data);
    }
    
	/**
     * 复制广告成为草稿
     */
    public function copyListingToDraft()
    {
        //产品ID列表
        $productIds = $this->input->get_post('productIds');
        //账号列表
        $tokenIds = $this->input->get_post('tokenIds');
        
        $uid = $this->user_info->id;//登录用户id
    
        $product_array = explode(',', $productIds);//把,拼接的广告id拆成数组
        
        $token_array   = explode(',', $tokenIds);//把,拼接的账号token_id拆成数组
        
        $userInfo = $this->wish_user_tokens_model->getWishTokenList($option=array());//获取所有账号的用户信息

        $flag  = false; //标识
        
        $error = array();
        
        foreach ($product_array as $productId) {//w外循环广告，内循环账号
            //读取产品的数据
            //产品信息属性
            $list_info = $this->wish_product_model->getInfoByProductID($productId);
            
            //获取原账号的token_id
            $accountInfo = $this->wish_user_tokens_model->getKeyByAccount($list_info['account']);

            if ($list_info) {
                //产品图片
                $detail_info = $this->wish_product_detail_model->getDetailInfoByProductID($productId);

                //各账号循环插入数据 
                foreach ($token_array as $token_id) {

                    $this->db->trans_begin();

                    /*********插入到主表的草稿数据开始*********/
                    $draft_product['account']       = $userInfo[$token_id]['account_name'];
                    $draft_product['productID']  	= $productId.'-'.$token_id.'-'.rand(10000, 99999);
                    $draft_product['publishedTime'] = date('Y-m-d H:i:s');
                    $draft_product['status']  		= $list_info['status'];
                    $draft_product['is_promoted']   = $list_info['is_promoted'];
                    $draft_product['review_status'] = $list_info['review_status'];
                    $draft_product['sellerID'] 		= $uid;
                    $draft_product['product_description']  = $list_info['product_description'];
                    $draft_product['product_name']  = $list_info['product_name'];
                    $draft_product['parent_sku']    = $list_info['parent_sku'];
                    $draft_product['Tags']  		= $list_info['Tags'];
                    $draft_product['product_type_status'] = 1;
                    /*********插入到主表的草稿数据结束*********/
                    $id = $this->wish_product_model->add($draft_product);

                    if (!$id) {
                        $error[] = $productId . '的广告复制错误';
                        $this->db->trans_rollback();
                        continue;
                    }

                    $detail_flag = 0;
                    /***************插入到广告详情表的草稿数据开始******************/
                    foreach($detail_info as $df){
                    	
                    	$draft_detail = array();
                    	
//                     	if(!empty($userInfo[$token_id]['photo_url'])){//主图附图处理
//				            $draft_detail['extra_image'] = str_replace($accountInfo['photo_url'],$userInfo[$token_id]['photo_url'],$df['extra_image']);
//				            $draft_detail['main_image']  = str_replace($accountInfo['photo_url'],$userInfo[$token_id]['photo_url'],$df['main_image']);
//				        }else{
//				            $draft_detail['extra_image'] = $df['extra_image'];
//				            $draft_detail['main_image']  = $df['main_image'];
//				        }
                        $draft_detail['sellerID']        = $uid;
	                    $draft_detail['productID'] 	 	 = $draft_product['productID'];
	                    $draft_detail['original_sku']    = $df['original_sku'];
	                    $draft_detail['sku']             = $df['sku'];
	                    $draft_detail['product_price']   = $df['product_price'];
	                    $draft_detail['product_count']   = $df['product_count'];
	                    $draft_detail['shipping']  		 = $df['shipping'];
	                    $draft_detail['soldNum']         = $df['soldNum'];
	                    $draft_detail['runTime']         = $df['runTime'];
	                    $draft_detail['enabled']      	 = $df['enabled'];
	                    $draft_detail['shipping_time']   = $df['shipping_time'];
	                    $draft_detail['msrp']            = $df['msrp'];
	                    $draft_detail['color']           = $df['color'];
	                    $draft_detail['size']            = $df['size'];
	                    $draft_detail['accounts']        = $accountInfo['account_name'];
	                    
	                    $detail_id = $this->wish_product_detail_model->add($draft_detail);
	                    if($detail_id){
	                      $detail_flag += 1;
	                    }
                    }
                    /***************插入到草稿详情表数据结束******************/
                   
                    if ($detail_flag != count($detail_info)) {
                        $error[] = $productId . '的广告详情复制错误';
                        $this->db->trans_rollback();
                        continue;
                    }else{
                        $flag = true;
                        $this->db->trans_commit();
                    }
                }
            }
            
        }

        ajax_return('另存为草稿' . ($flag ? '成功' : '失败'), $flag, $error);
    }
    
    //待发布的数据
    public function wait_post(){
      $product_type=2;
      $this->get_draft_list($product_type);
    }
    
    /**
     * 获取草稿、待发布的数据列表
     * $product_type = 1;//表示草稿
     * $product_type = 2;//表示待发布
     */
    public function get_draft_list($product_type="1"){
      
      $userInfo = $this->wish_user_tokens_model->getWishTokenList($option=array());//获取所有账号的用户信息
      
      $accountArr = array();
       foreach($userInfo as $user){
         $accountArr[$user['account_name']] = $user['choose_code'];
       }
   
       $key = $this->user_info->key;//用户组key
		
	   $uid = $this->user_info->id;//登录用户id
		
		 
		$string = '';
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= intval($this->config->item('site_page_num')); //每页显示个数
		
		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		$in  = array();
		
		$prodcutIDArr = array();//存放productID的数据
		//搜索
		$search_data = $this->input->get_post('search');

		$productId='';//产品ID
		$sku='';//产品sku
		$Acc = '';//账号
		$subject = '';//标题
		//产品号筛选
		if(isset($search_data['productId']) && $productId = trim($search_data['productId'])){
			$where['productID'] = $productId;
			$string .= 'search[productId]='.$productId;
		}
		//sku筛选
		if(isset($search_data['sku']) && $sku = trim($search_data['sku'])){
			//获取匹配sku的prodcutID
			$product_ids = array();
			$product_ids = $this->wish_product_detail_model->getProductIdWithSku($sku);
			if($product_ids){
			  $in['productID'] = $product_ids;
			}else{
			  $in['productID'] = '0';
			}
			$string .= '&search[sku]='.$sku;
		}
		//账号筛选
		if(isset($search_data['account']) && $Acc = trim($search_data['account'])){
			$where['account'] = $Acc;
			$string .= 'search[account]='.$Acc;
		}
    	//标题筛选
		if(isset($search_data['subject']) && $subject = trim($search_data['subject'])){
			$where['product_name'] = $subject;
			$string .= 'search[subject]='.$subject;
		}
		
		$where['product_type_status'] = $product_type;

		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
		    'where'     => $where,
			'where_in'  => $in
		);
		
		
		
		$data_list = $this->wish_product_model->getAll($options, $return_arr); //查询所有信息
		$productIDArr = array();
		foreach($data_list as $pD){
		  //$pd_arr = explode('-',$pD->productID);
		  $productIDArr[] = $pD->productID;
		}
		
		$productSkuArr = array();
		//根据productID获取sku
		$productSkuArr = $this->wish_product_detail_model->getProductSkus($productIDArr);
		
		$mainImageArr = array();
		//根据product获取主图
		$mainImageArr = $this->wish_product_detail_model->getMainImage($productIDArr);

		$c_url='publish/wish';
		
		if($product_type==1){//草稿
		  $url = admin_base_url('publish/wish/get_draft_list?').$string;
		}else{
		  $url = admin_base_url('publish/wish/wait_post?').$string;
		}

		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$search_data['productId'] = $productId;
		$search_data['sku'] = $sku;
		$search_data['account'] = $Acc;
		$search_data['subject'] = $subject;
	
        $data = array(
	        'product_type' => $this->products_type[$product_type],
	        'userInfo'     => $accountArr,
	        'mainImageArr' => $mainImageArr,
	        'productSkuArr'=> $productSkuArr,
	        'data_list'    => $data_list,
	      	'page'     => $page,
			'search'   => $search_data
        );
      $this->_template('admin/publish/wish/wish_draft_list',$data);
    }
    
 	/**
     * 改变产品的状态
     * 草稿状态改为待发布
     */
    public function changeToWaitPost(){
        $productIds = $this->input->get_post('ids');
        if (!$productIds){
            ajax_return('产品ID不能为空', false);
        }

        $productArray = explode(',', $productIds);

        $error = array();
        $success = array();
        
		foreach($productArray as $ke => $products){
		    $list_data = $this->wish_product_model->getInfoByProductID($products);
			if (!$list_data){//如果该产品ID数据不存在，跳过本次循环
	            $error = $products;
	            continue;
	        }
	        $data = array();
	        $option = array();
	        $data['product_type_status'] = 2;
	        $data['updateTime'] = date('Y-m-d H:i:s');
	        $option['where'] = array('productID'=>$products);
	        $re = $this->wish_product_model->update($data,$option);
	        
	        if($re){
	          $success[] = $products;
	          
	        }else{
	          $error = $products;
	        }
		}
		
		$error_notic = $error ? implode(',', $error).'失败了<br/>' : '';
		$success_notic = $success ? implode(',', $success).'成功了' : '';
		ajax_return($error_notic.$success_notic, false);
        
    }
    /**
     * 批量删除草稿
     */
    public function batchDel(){
    	
    	$productIds = $this->input->get_post('ids');
    	$action =  $this->input->get_post('action');
    	if(isset($action) && $action=='batch'){//批量删除
	    	if (!empty($productIds)){
	            $productIdArr = explode(',', $productIds);
	            //循环删除草稿
	            $success = array();
	            $error = array();
	            foreach ($productIdArr as $id){
	            	
	            	$this->db->trans_begin();
	            	
	            	$rp = $this->wish_product_model->deleteByProductIDs($id);
	            	if(!$rp){
	            	  $this->db->trans_rollback();
	            	  $error[] = "草稿$id 删除失败";
	            	  continue;
	            	}
	            	
	            	$rpd = $this->wish_product_detail_model->deleteByProductID($id);
	           	    if(!$rpd){
	            	  $this->db->trans_rollback();
	            	  $error[] = "草稿$id 删除失败";
	            	  continue;
	            	}
	            	
		            if($this->db->trans_status() === TRUE){
		                $this->db->trans_commit();//事务结束
		                $success[] = "草稿$id 删除成功";
		            }
	 
	            }
	            
	            $msg = !empty($success) ? implode(';', $success) : '';
	            $msg .= !empty($error) ? implode(';', $error) : '';
	           ajax_return($msg, true);
	        }else {
	          ajax_return('非法操作', false);
	        }
    	}else{//单个删除
    		
    				$this->db->trans_begin();
	            	
	            	$rp = $this->wish_product_model->deleteByProductIDs($productIds);
	            	if(!$rp){
	            	  $this->db->trans_rollback();
	            	  $msg = "$productIds 删除失败";
	            	  ajax_return($msg, false);
	            	}
	            	
	            	$rpd = $this->wish_product_detail_model->deleteByProductID($productIds);
	           	    if(!$rpd){
	            	  $this->db->trans_rollback();
	            	  $msg = "$productIds 删除失败";
	            	  ajax_return($msg, false);
	            	}
	            	
		            if($this->db->trans_status() === TRUE){
		                $this->db->trans_commit();//事务结束
		                $msg = "$productIds 删除成功";
		                ajax_return($msg, true);
		            }
		            
    	}
        

    }
    
	/**
     * 产品批量发布
     */
    public function batchPost(){
        //产品ID列表
        $productIds = $this->input->get_post('productIds');

        if (!$productIds){
            $return[] = array('status' => false, 'info' => '请传入产品数据');
        }else {
            $products = explode(',', $productIds);
            $return   = array();
            foreach ($products as $productId) {
                $return[] = $this->post_product($productId);
                //$return[] = array('status' => true, 'info' => $productId . ' 发布成功');
            }
        }
        $this->template('admin/publish/wish/wish_batch_post', array('return' => $return));
    }
    
    /**
     * 批量发布产品的方法
     */
    public function post_product($productId){
    	
    	$result = array();
    	
    	//根据广告id获取广告数据
    	$list_data = $this->wish_product_model->getInfoByProductID($productId);
    	
    	if(empty($list_data)){
    	  $result['status'] = false;
    	  $result['info'] = $productId.'的数据不存在';
    	  return $result;
    	  die;
    	}
    	
    	//根据广告id获取广告详情数据
    	$detail_list_data = $this->wish_product_detail_model->getDetailInfoByProductID($productId);
    	if(empty($detail_list_data)){
    	  $result['status'] = false;
    	  $result['info'] = $productId.'的产品详情数据不存在';
    	  return $result;
    	  die;
    	}
    	

     //根据账号获取key值
      $accountInfo = $this->wish_user_tokens_model->getKeyByAccount($list_data['account']);
      
      //必须参数
      $post_product['main_image']      = $detail_list_data[0]['main_image'];
      $post_product['name']      	   = trim($list_data['product_name']);
      $post_product['parent_sku']      = trim($list_data['parent_sku']);
      $post_product['sku']       	   = trim($detail_list_data[0]['original_sku']);
      $post_product['inventory']       = trim($detail_list_data[0]['product_count']);
      $post_product['price']		   = trim($detail_list_data[0]['product_price']);
      $post_product['shipping']  	   = trim($detail_list_data[0]['shipping']);
		$description =                trim($list_data['product_description']);
		$description					   = str_replace('<p>',' ',$description);
		$description					   = str_replace('</p>',' ',$description);
		$post_product['description']	 	 = str_replace('&nbsp;',' ',$description);

      $post_product['tags']   		   = trim($list_data['Tags']);
      $post_product['key'] 			   = trim($accountInfo['wish_key']);
      $post_product['extra_images']    = trim($detail_list_data[0]['extra_image']);
      
      //可选参数
      $post_product['color']  		   = trim($detail_list_data[0]['color']);
      $post_product['size']			   = trim($detail_list_data[0]['size']);
      $post_product['msrp']   		   = trim($detail_list_data[0]['msrp']);
      $post_product['shipping_time']   = trim($detail_list_data[0]['shipping_time']);
      $post_product['brand']    	   = '';
      $post_product['landing_page_url']= '';
      $post_product['upc'] 			   = '';

        //去除上传数组中，为空的键名
        foreach($post_product as $k => $p){
          if(empty($p)){
            unset($post_product[$k]);
          }
        }
        
       //本地表插入成功以后，开始上传到wish
       $url = 'https://merchant.wish.com/api/v1/product/add';
       $re = $this->wish_post_API($url,$post_product);
       //刊登到wish上成功，更新本地erp_wish_product表和erp_wish_product_detail表
       if($re['code'] == 0){
       	
          $return_data = array();
          
          $up_product = array();//要更新到erp_wish_product表的数据
          
          $return_data = $re['data']['Product'];
          //上传成功后，更新erp_wish_product表的数据
          $up_product['productID'] = $return_data['id'];
          $up_product['updateTime'] = date('Y-m-d H:i:s');
          $up_product['review_status'] = $return_data['review_status'];
          $up_product['product_type_status'] = 0;
          $where_p = array('productID'=>$productId);
          $status_p = $this->wish_product_model->update($up_product,array('where'=>$where_p));
          
      	  $flag_de = 0;
      	  $reason  = array();
          foreach($detail_list_data as $ke => $s){
          	
          	  if($ke >=1){//两个以上sku 的情况
          	    $add_data = array();
          	    $add_url = 'https://merchant.wish.com/api/v1/variant/add';
          	    $add_data['parent_sku'] = strtoupper($detail_list_data[0]['original_sku']);
          	    $add_data['sku'] 		= strtoupper($s['original_sku']);
          	    $add_data['color'] 		= $s['color'];
          	    $add_data['size'] 		= $s['size'];
          	    $add_data['inventory']  = $s['product_count'];
          	    $add_data['price'] 		= $s['product_price'];
          	    $add_data['shipping'] 	= $s['shipping'];
          	    $add_data['msrp'] 		= $s['msrp'];
          	    $add_data['shipping_time'] = $s['shipping_time'];
          	    $add_data['main_image'] = $s['main_image'];
          	    $add_data['key']  =  $accountInfo['wish_key'];
          	    //去除上传数组中，为空的键名
		        foreach($add_data as $kes => $ps){
		          if(empty($ps)){
		            unset($add_data[$kes]);
		          }
		        }
		        for($i=0;$i>=0;$i++){
		          $res = $this->wish_post_API($add_url,$add_data);
		          if(isset($res['code'])){
		          	 $reason[] = $res;
		          	 $flag_de += 1;
		             break;
		          }
		          
		        }
          	  }
          	  $up_product_detail = array();//要更新到erp_wish_product_detail表的数据
	          $up_product_detail['productID'] = $return_data['id'];
	          $where_pd = array('id'=>$s['id']);
	          $status_pd = $this->wish_product_detail_model->update($up_product_detail,array('where'=>$where_pd));
          }

          $need_count = count($detail_list_data)-1;
          if($flag_de == $need_count){
	          $result['status'] = true;
	    	  $result['info'] = $productId.'的产品刊登到wish线上成功，新产品ID'.$return_data['id'];
          }else{
              $result['status'] = true;
	    	  $result['info'] = $productId.'的产品刊登到wish线上成功，新产品ID'.$return_data['id'].',副sku有些刊登失败';
          }
       }else{
          $result['status'] = false;
    	  $result['info'] = $productId.'的产品刊登到wish线上失败，原因'.$re['message'];
       }
 		return $result;
    	die;
    }
    
	/**
     * 批量修改产品
     
    public function batchModifyProducts(){
        $productIds = $this->input->get_post('operateProductIds');
        $from       = $this->input->get_post('from'); //判断数据来源

        $productList = array();
        $productDetail = array();
        if (!empty($productIds)) { //产品ID非空

            //获取产品信息并显示出来(图片，标题，关键词，单位，重量，尺寸，产品信息模块，服务模板，运费模板，零售价，产品id，分类id)
            $productIdArr = explode(',', $productIds);

            $token_id = $this->Smt_product_list_model->checkProductsInSameAccount($productIdArr);

            if (!$token_id){
                $data = array(
                    'error' => '选择的产品无账号或不在同一个账号，请重新选择'
                );
            }else {

                $productList   = $this->Smt_product_list_model->getProductsFields($productIdArr, 'productId, subject, categoryId, packageLength, packageWidth, packageHeight, grossWeight, productMinPrice, productMaxPrice, token_id');
                $productDetail = $this->Smt_product_detail_model->getProductDetailsFields($productIdArr, 'imageURLs, keyword, productMoreKeywords1, productMoreKeywords2, productUnit, freightTemplateId, promiseTemplateId, productId, packageType, lotNum');

                $this->load->model('smt/Slme_smt_unit_model');
                //单位列表
                $unitList = $this->Slme_smt_unit_model->getUnitList();
                //运费模板列表
                $freightList = $this->Slme_smt_freight_template_model->getFreightTemplateList($token_id);
                //服务模板列表
                $serveList = $this->Slme_smt_service_template_model->getServiceTemplateList($token_id);

                //产品分组
                $groupList = $this->Slme_smt_product_group_model->getProductGroupList($token_id);

                $data = array( //传过去的数据
                    'productIds'    => $productIds,
                    'productList'   => $productList,
                    'productDetail' => $productDetail,
                    'unitList'      => $unitList,
                    'freightList'   => $freightList,
                    'serveList'     => $serveList,
                    'groupList'     => $groupList,
                    'token_id'      => $token_id,
                    'from'          => $from
                );
            }
        }else {
            $data = array(
                'error' => '请先选择要修改的产品'
            );
        }

        $this->template('admin/wish/wish_batch_modify', $data);
    }
    */
    
    /**
     * wish数据导入的列表
     * 用于自动刊登
     */
    public function wish_data_import(){
    	
       $userInfo = $this->wish_user_tokens_model->getWishTokenList($option=array());//获取所有账号的用户信息
    	$accountArr = array();
       foreach($userInfo as $user){
         $accountArr[$user['account_name']] = $user['choose_code'];
       }

       $key = $this->user_info->key;//用户组key
		
	   $uid = $this->user_info->id;//登录用户id
		
		 
		$string = '';
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= intval($this->config->item('site_page_num')); //每页显示个数

		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		$like = array();
		
		//搜索
		$search_data = $this->input->get_post('search');

		$auditing='';//审核状态
		$Acc = '';//账号
		$productID = '';//产品ID
		$is_upload = '';//刊登状态
		$sku_search = '';//sku搜索
		//审核状态筛选
		if(isset($search_data['auditing']) && $auditing = trim($search_data['auditing'])){
			$where['auditing_status'] = $auditing;
			$string .= '&search[auditing]='.$auditing;
		}
		//产品ID筛选
		if(isset($search_data['productID']) && $productID = trim($search_data['productID'])){
			$where['productID'] = $productID;
			$string .= '&search[productID]='.$productID;
		}
		//账号筛选
		if(isset($search_data['account']) && $Acc = trim($search_data['account'])){
			$where['account'] = $Acc;
			$string .= '&search[account]='.$Acc;
		}
    	//刊登状态筛选
    	if(isset($search_data['upload']) && $is_upload = trim($search_data['upload'])){
    		
			$where['is_upload'] = $is_upload;
			
			$string .= '&search[upload]='.$is_upload;
		}
   		//sku搜索
		if(isset($search_data['parent_sku']) && $sku_search = trim($search_data['parent_sku'])){
			$like['parent_sku'] = $sku_search;
			$string .= '&search[parent_sku]='.$sku_search;
		}
		
		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
		    'where'     => $where,
		    'like'		=> $like,
			'order'		=> 'id DESC'
		);
		
		
		
		$data_list = $this->wish_import_data_model->getAll($options, $return_arr); //查询所有信息

		$c_url='publish/wish';

	    $url = admin_base_url('publish/wish/wish_data_import?').$string;

		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$search_data['account'] = $Acc;
		$search_data['auditing'] = $auditing;
		$search_data['productID'] = $productID;
		$search_data['upload'] = $is_upload;
		$search_data['parent_sku'] = $sku_search;
	
        $data = array(
	        'userInfo'     		=> $accountArr,
	        'data_list'   		=> $data_list,
	      	'page'    		    => $page,
            'products_auditing' => $this->products_auditing,
        	'products_upload'   => $this->products_upload,
          	'search'			=> $search_data
        );
       $this->_template('admin/publish/wish/wish_data_import_list',$data);
    }
    public function ajaxmodifyorder(){
    	$data = $_POST;
		$id = (int)$_POST['id'];
		$where = array(
			'id'=>$id
		);
		$tjdata =array();
		foreach($data as $key=>$val){
			if($key != 'id'){
				$tjdata[$key] = $val;
			}
		}
		$res = $this->wish_import_data_model->update($tjdata,$where);
		if($res){
			//修改成功
			$sta = '1';
		}else{
			$sta = '2';
		}
    	ajax_return('',$sta,$data);
    }
    /**
     * wish上传文件的界面
     */
    public function upload_file(){
      $account = $this->input->get_post('account');
      $token_array   = explode(',', $account);//把,拼接的账号token_id拆成数组
      $data = array(
        'token_array' => $token_array
      );
      $this->template('admin/publish/wish/upload_file',$data);
    }
    /**
     * wish上传文件的处理
     */
    public function do_deal_upload_file(){
    	$this->load->library(array('phpexcel/PHPExcel'));//载入excel类
    	$uid = $this->user_info->id;//登录用户id
        $posts = $this->input->post();
        $token_array = $posts['token_id'];
        
        $userInfo = $this->wish_user_tokens_model->getWishTokenList($option=array());//获取所有账号的用户信息
        
        $uploadFiles = $_FILES["readfile"]["tmp_name"];	//临时存储目录

        //开始读取excel文件的数据
		$PHPExcel = new PHPExcel(); 
		/**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/ 
		$PHPReader = new PHPExcel_Reader_Excel2007(); 
		if(!$PHPReader->canRead($uploadFiles)){ 
			$PHPReader = new PHPExcel_Reader_Excel5(); 
			if(!$PHPReader->canRead($uploadFiles)){ 
				'no Excel'; 
				return ; 
			} 
		} 
		$val=array();//存放读取的数据
		$PHPExcel = $PHPReader->load($uploadFiles); 
		
		/**读取excel文件中的第一个工作表*/ 
		$currentSheet = $PHPExcel->getSheet(0); 
		
		/**取得最大的列号*/ 
		$allColumn = $currentSheet->getHighestColumn(); 
		
		/**取得一共有多少行*/ 
		$allRow = $currentSheet->getHighestRow(); 
		
		$data = array();//存放读取的表格数据
		
	    /**从第二行开始输出，因为excel表中第一行为列名*/ 
		for($currentRow = 2;$currentRow <= $allRow;$currentRow++){ 
		/**从第A列开始输出*/ 
			for($currentColumn= 65;$currentColumn<= 87; $currentColumn++){ 

				//$colName = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,1)->getValue();//获取对应列的列明
				
				$pronum= $currentSheet->getCellByColumnAndRow($currentColumn - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
			
				$data[$currentRow][] = $pronum;
			
			}
		}
		
		//外层循环是循环账号的
		foreach($token_array as $token_id){
			//读取了数据后，把数据插入erp_wish_import_data表
			foreach($data as $k => $d){
			   if($d[0]=='' || $d[1]==''){//空数组的话直接跳过
			     continue;
			   }
			   //先查询该条记录是否存在，存在则删除
			   $getData = $this->wish_import_data_model->getDataBySku(trim($d[0]),trim($d[1]),$userInfo[$token_id]['account_name']);
			   if(!empty($getData)&&$getData['is_upload']==2){
			     echo '<span style="color:red;">账号'.$userInfo[$token_id]['account_name'].'导入的表格里，第'.$k.'行数据导入失败，该行记录已经刊登</span><br/>';
			     continue;
			   }

			   if(!empty($getData)){//删除记录
			      $this->wish_import_data_model->deleteDataBySku(trim($d[0]),trim($d[1]),$userInfo[$token_id]['account_name']);
			   }
			   $insert_data = array();//存放要插入的数据
			   $count = count($d);//该数组的个数
			   $original_fu_img = '';//存放原始的附图
			   $insert_data['account'] = $userInfo[$token_id]['account_name'];
			   $insert_data['parent_sku'] = trim($d[0]);
			   $insert_data['original_sku'] = trim($d[1]);
			   $insert_data['product_name'] = trim($d[2]);
			   $insert_data['color'] = trim($d[3]);
			   $insert_data['size'] = trim($d[4]);
			   $insert_data['quantity'] = trim($d[5]);
			   $insert_data['Tags'] = trim($d[6]);
			   $insert_data['product_description'] = trim($d[7]);
			   $insert_data['price'] = trim($d[8]);
			   $insert_data['shipping'] = trim($d[9]);
			   $insert_data['shipping_time'] = trim($d[10]);
			   $insert_data['msrp'] = trim($d[11]);
			   $insert_data['original_main_image'] = trim($d[12]);
			   
			   //处理附图，用|相连接
			   for($i=13;$i<$count;$i++){
			     $original_fu_img.='|'.$d[$i];
			   }
			   $insert_data['original_extra_image'] = substr($original_fu_img,1);
			   
			   $insert_data['createTime'] = date('Y-m-d H:i:s');
			   $insert_data['uid'] = $uid;
			   
			   $id = $this->wish_import_data_model->add($insert_data);
			   
			   if($id>0){
			     echo '<span style="color:green;">账号'.$userInfo[$token_id]['account_name'].'导入的表格里，第'.$k.'行数据导入成功</span><br/>';
			   }else{
			     echo '<span style="color:red;">账号'.$userInfo[$token_id]['account_name'].'导入的表格里，第'.$k.'行数据导入失败</span><br/>';
			   }
			}
		}
		
		
    }
    
    /**
     * 根据id获取刊登数据详情
     */
    public function getInfoByID(){
       $id = $this->input->get_post('id');
       $Info = $this->wish_import_data_model->getDataById($id);
       //获取域名
       $yuming = '';
       if(!empty($Info['erp_main_image'])){
         $len = strpos($Info['erp_main_image'],'upload');
         $yuming = substr($Info['erp_main_image'],0,$len-1);
       }
       $Info['yuming'] = $yuming;

       $data = array(
         'result' => $Info
       );
       $this->template('admin/publish/wish/publishInfo',$data);
       
    }
    
    /**
     * 批量操作
     * action=1    批量审核通过
     * action=2    批量审核未通过
     */
    public function batch_operate(){
      $IDs = $this->input->post('Ids');
      $action = $this->input->post('action');//2-批量审核通过，3-批量审核未通过
      $IDArr = array();//存放要审核通过的ID
      $IDArr = explode(',',$IDs);
      $sucesID = array();
      $errID = array();
      
      foreach($IDArr as $id){
      	$option = array();
      	$option['where'] = array('id'=>$id);
      	$up_data['auditing_status'] = $action;
      	$up_data['auditingTime'] = date('Y-m-d H:i:s');
      	$result = $this->wish_import_data_model->update($up_data,$option);
      	if($result){
      	  $sucesID[] = $id;
      	}else{
      	  $errID[]=$id.'的修改失败';
      	}
      }
      $msg = ($action==1)? '批量审核通过' : '批量审核未通过';
      $flag = (count($sucesID)==count($IDArr)) ? true : false;
      ajax_return($msg . ($flag ? '成功' : '失败'), $flag, $errID);
      
    }
    
    /**
     * 添加wish自动调价的方法
     * 创建调价任务
     */
    public function create_wish_price_task(){
       $option = array();
       $accountList = $this->model->getWishTokenList($option);
       $accountArr = array();
       foreach($accountList as $user){
         $accountArr[$user['account_name']] = $user['choose_code'];
       }
       $data = array(
         'accountList' => $accountArr
       );
       $this->_template('admin/publish/wish/create_price_task',$data);
    }
    
    /**
     * 生成调价任务并存表
     * erp_wish_price_task
     */
    public function creating_price_task(){
      //存放结果数组
      $result = array(
        'status' => 0,
      	'msg'    => ''
      );
    	
      $posts = $this->input->post();

      $account_name = $posts['account'];//要调价的账号
      
      $wish_status = $posts['wish_status'];//wish状态  1-在售(审核通过)  2-在售(待审核)  3-促销  4-下架
      
      $price_type = $posts['price_type'];//调控价格的类型：1-指定价格  2-上调价格  3-下调价格
      
      $price = $posts['price'];//要调整的价格大小
      
      //获取所有符合条件的广告
      $option = array();
      
      $select = array("{$this->wish_product_model->_table}.*",'pd.product_price','pd.enabled','pd.original_sku');
	  
	  $join[] = array('erp_wish_product_detail pd',"pd.productID={$this->wish_product_model->_table}.productID");
	
	 
      $where = array();
      
      switch($wish_status){
				case 1:
					$where =array("status"=>0,"is_promoted"=>0,"review_status"=>'approved');
					break;
				case 2:
					$where =array("status"=>0,"is_promoted"=>0,"review_status"=>'pending');
					break;
				case 3:
					$where =array("status"=>0,"is_promoted"=>1);
					break;
				case 4:
					$where =array("status"=>1,"enabled"=>0);
					break;
				default:
					break;
     }
     
     $where['account'] = $account_name;
     
     $where['product_type_status'] = 0;
     
     $option = array(
				'select' => $select,
		  		'join'   => $join,
		  	    'where'  => $where
	 );
	
	 $productArr = $this->wish_product_model->getAll2array($option);

	 $flag = 0;//是否全部插入成功的标志

	 //开始组装插入表 的数据
	 foreach($productArr as $product){
	 	
	   $insertData = array();
	   $insertData['productID']    = $product['productID'];
	   $insertData['account']      = $account_name;
	   $insertData['original_sku'] = $product['original_sku'];
	   $insertData['price_type']   = $price_type;
	   $insertData['price_amount'] = $price;
	   //价格处理
	   if($price_type==1){//如果是指定价格，则直接赋值
	   	  $insertData['price']     = $price;
	   }elseif($price_type==2){//上调价格
	      $insertData['price']     = $price+$product['product_price'];
	   }elseif($price_type==3){//下调价格
	      $insertData['price']     = $product['product_price']-$price;
	   }
	   
	   $insertData['create_time']  = date('Y-m-d H:i:s');
	   
	   //插入数据到erp_wish_price_task
	   $fla = $this->wish_price_task_model->add($insertData);
	   
	   if($fla){
	     $flag +=1;
	   }
	   
	 }
	 
	 if($flag==count($productArr)){
	   $result = array(
        'status' => 1,
      	'msg'    => '调价任务生成成功'
       );
	 }else{
	 	$result = array(
        'status' => 1,
      	'msg'    => '部分调价任务生成失败'
       );
	 }
	 echo json_encode($result);
	 exit; 
    }
    
    /**
     * wish调价任务列表
     */
    public function price_task_list(){
    	
   	   $userInfo = $this->wish_user_tokens_model->getWishTokenList($option=array());//获取所有账号的用户信息
   	   
       $accountArr = array();
       foreach($userInfo as $user){
         $accountArr[$user['account_name']] = $user['choose_code'];
       }

       $key = $this->user_info->key;//用户组key
		
	   $uid = $this->user_info->id;//登录用户id
		
		 
		$string = '';
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= intval($this->config->item('site_page_num')); //每页显示个数

		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		$like = array();
		
		$orderBy = 'id DESC';
		
		//搜索
		$search_data = $this->input->get_post('search');

		$price_status='';//调价状态
		$Acc = '';//账号
		$productID = '';//产品ID
		$is_upload = '';//刊登状态
		$sku_search = '';//sku搜索
		//审核状态筛选
		if(isset($search_data['price_status']) && $price_status = trim($search_data['price_status'])){
			if($price_status !=1){
			  $orderBy = 'API_time DESC';
			}
			$where['status'] = $price_status;
			$string .= '&search[price_status]='.$price_status;
		}
		//产品ID筛选
		if(isset($search_data['productID']) && $productID = trim($search_data['productID'])){
			$where['productID'] = $productID;
			$string .= '&search[productID]='.$productID;
		}
		//账号筛选
		if(isset($search_data['account']) && $Acc = trim($search_data['account'])){
			$where['account'] = $Acc;
			$string .= '&search[account]='.$Acc;
		}
    	
   		//sku搜索
		if(isset($search_data['parent_sku']) && $sku_search = trim($search_data['parent_sku'])){
			$like['original_sku'] = $sku_search;
			$string .= '&search[parent_sku]='.$sku_search;
		}
		
		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
		    'where'     => $where,
		    'like'		=> $like,
		    'order'		=> $orderBy
		);
		
		$data_list = $this->wish_price_task_model->getAll($options, $return_arr); //查询所有信息
		
		$c_url='publish/wish';

	    $url = admin_base_url('publish/wish/price_task_list?').$string;

		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$search_data['account'] = $Acc;
		$search_data['price_status'] = $price_status;
		$search_data['productID'] = $productID;
		$search_data['parent_sku'] = $sku_search;
	
        $data = array(
	        'userInfo'     		=> $userInfo,
	        'data_list'   		=> $data_list,
	      	'page'    		    => $page,
            'price_status' 		=> $this->price_status,
        	'price_type'		=> $this->price_type,
        	'userInfo'			=> $accountArr,
          	'search'			=> $search_data
        );
        
       $this->_template('admin/publish/wish/price_task_list',$data);
    }
    
    /**
     * 批量删除
     */
	public function batch_delete_price(){
      $IDs = $this->input->post('Ids');
      $IDArr = array();//存放要删除的ID
      $IDArr = explode(',',$IDs);
      $msg = '';
      foreach($IDArr as $id){
      	$option = array();
      	$option['where'] = array('id'=>$id);
		$data = $this->wish_price_task_model->getOne($option,true);
		if($data['status']!=1){
		   $msg .= "{$data['productID']}的记录已经执行，不允许删除<br/>";
		   continue;
		}
      	$result = $this->wish_price_task_model->delete($option);
      	if($result){
      	   $msg .= "{$data['productID']}的记录已经删除<br/>";
      	}else{
      	   $msg .= "{$data['productID']}的记录删除失败<br/>";
      	}
      }
     
      ajax_return($msg,true);
      
    }

	public function generate_sku(){
		$last_result =array();
		$generate_sku_parent = $this->input->post('generate_sku_parent');
		$generate_sku_color = $this->input->post('generate_sku_color');
		$generate_sku_size = $this->input->post('generate_sku_size');

		$generate_sku_color_array = explode(',',$generate_sku_color);
		$generate_sku_size_array = explode(',',$generate_sku_size);
		$i=0;
		foreach($generate_sku_color_array as $color){
			foreach($generate_sku_size_array as $size){
				$last_result[$i]['sku']=$generate_sku_parent.'_'.$color.'_'.$size;
				$last_result[$i]['color']=$color;
				$last_result[$i]['size']=$size;


				$i++;
			}
		}
		ajax_return('',1,$last_result);


	}
}
 
 