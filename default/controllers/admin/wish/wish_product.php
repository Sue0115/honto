<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type: text/html; charset=utf-8");
/**
 * wish广告列表，同步
 * @authors zengrihua
 * @date    2015-03-12 
 */
class Wish_product extends Admin_Controller{

	protected $wish;
    protected $userToken;
    
	function __construct(){
        parent::__construct();
        $this->load->helper('http_helper');
        $this->load->library('MyWish');
        $this->load->Model(array(
           'wish/wish_user_tokens_model','wish/wish_product_model','wish/wish_product_detail_model',
           'sharepage','print/products_data_model','Manages_model'
        ));
        $this->model = $this->wish_user_tokens_model;
        $this->wish = new MyWish();
    }
	/**
	 * 显示产品列表
	 * product_type_status = 0  显示正常的的广告
	 */
	public function productManage(){
		
	    $key = $this->user_info->key;//用户组key
		
		$uid = $this->user_info->id;//登录用户id
		
		$time = date('Y-m-d',strtotime('-7 day'));//上一天的时间
		 
		$string = '';
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= intval($this->config->item('site_page_num')); //每页显示个数
		
		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		//$account = array('salamoer1108@126.com','fangzheng@moonarstore.com');
		
		$account = $this->wish_user_tokens_model->getWishTokenList($option=array());
		$accountArr = array();
        foreach($account as $user){
         $accountArr[$user['account_name']] = $user['choose_code'];
        }

		$in  = array();
		
		//$in['account'] = $account;
		
		$prodcutIDArr = array();//存放productID的数据
		//搜索
		$search_data = $this->input->get_post('search');

		$productId='';//产品ID
		$sku='';//产品sku
		$Acc = '';//账号
		$start_date = '';//刊登开始时间
		$end_date = '';//刊登结束时间
		$sellerID ='';
		if(isset($search_data['productId']) && $productId = trim($search_data['productId'])){
			$where['productID'] = $productId;
			$string .= 'search[productId]='.$productId;
		}

		if(isset($search_data['sellerID']) && $sellerID = trim($search_data['sellerID'])){
			$where['sellerID'] = $sellerID;
			$string .= 'search[sellerID]='.$sellerID;
		}


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
		//刊登开始时间
		if(isset($search_data['start_date']) && $start_date = trim($search_data['start_date'])){
			$where['publishedTime >='] = $start_date;
			$string .= 'search[start_date]='.$start_date;
		}
		//刊登结束时间
		if(isset($search_data['end_date']) && $end_date = trim($search_data['end_date'])){
			$where['publishedTime <='] = $end_date;
			$string .= 'search[end_date]='.$end_date;
		}
		if(isset($search_data['account']) && $Acc = trim($search_data['account'])){
			$where['account'] = $Acc;
			//unset($in['account']);
			$string .= 'search[account]='.$Acc;
		}
		//$where['updateTime >='] = $time;
		$where['productID !='] = '';
		$where['product_type_status'] = 0;

		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
		    'where'     => $where,
		    'where_in'  => $in,
		    'order'		=> 'publishedTime desc'
		);
		
		
		
		$data_list = $this->wish_product_model->getAll($options, $return_arr); //查询所有信息
		$productIDArr = array();
		foreach($data_list as $pD){
		  $productIDArr[] = $pD->productID;
		}
		
		//根据productID获取sku
		$productSkuArr = $this->wish_product_detail_model->getProductSkus($productIDArr);

		//根据product获取主图
		$mainImageArr = $this->wish_product_detail_model->getMainImage($productIDArr);

		$c_url='wish/wish_product';
		
		$url = admin_base_url('wish/wish_product/productManage?').$string;
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$search_data['productId'] = $productId;
		$search_data['sku'] = $sku;
		$search_data['account'] = $Acc;
		$search_data['start_date']= $start_date;
		$search_data['end_date']= $end_date;
		$search_data['sellerID'] = $sellerID;
		
		$data = array(
		  'data'     => $data_list,
		  'user_info' =>$this->defineEbaySellerPrefix(),
		  'page'     => $page,
		  'search'   => $search_data,
		  'productSku'=> $productSkuArr,
		  'mainImangeArr' => $mainImageArr,
		  'account'		=> $accountArr
		);
		
	    $this->_template('admin/wish/product_list',$data);
	}
	
	//根据账号名设置访问token和code
	public function set_access_token($account_name){
	    $accountInfo = $this->model->getKeyByAccount($account_name);
	    $this->wish->code 		= $accountInfo['code'];
	    $this->wish->access_token = $accountInfo['access_token'];
	}
	
	/**
	 * 在线编辑广告
	 * 视图
	 * type=1,编辑单个广告
	 * type=2，批量修改广告
	 */
	public function edit_product(){
	    
       $productID = $this->input->get_post('id');
       
       $type = $this->input->get_post('type');
       
       $options = array();
       
       $productInfoArr = array();
       
       $colorArr = array();
       
       if($type==1){
           
           $options['select'] = array("{$this->wish_product_model->_table}.*",'pd.*');
            
           $join[] = array('erp_wish_product_detail pd',"pd.productID={$this->wish_product_model->_table}.productID");
            
           $options['join'] = $join;
            
           $options['where'] = array("{$this->wish_product_model->_table}.productID" => $productID);
            
           $productInfoArr = $this->wish_product_model->getAll2array($options);
           
           if(!empty($productInfoArr)){
               foreach($productInfoArr as $p){
                   $colorArr[] = $p['color'];
               }
               
               $colorArr = array_unique($colorArr); 
           }
       
           
       }
 
       $data = array(
           'productInfoArr' => $productInfoArr,
           'type'           => $type,
           'productID'      => $productID,
           'colorArr'       => $colorArr
       );

       $this->template('admin/wish/edit_product',$data);
	}
	
	/**
	 * 保存编辑的广告并且提交wish后台更改
	 * 单个编辑广告
	 */
	public function saveToProduct(){
	    
	   $post = $this->input->post();
	  
	   $account = $post['account'];
	   
	   $products_sku = $post['products_sku'];//产品sku的数
	   
	   $mainImage = '';//主图;
	   
	   $fuImage = '';//附图
	   
	   //根据账号获取key值
	   $accountInfo = $this->wish_user_tokens_model->getKeyByAccount($account);
	   
	   //产品描述
	   $description 				           = trim($post['product_description']);
	
	   $descripts					           = str_replace('&nbsp;',' ',$description);
	   $post_product['description']	           = $descripts;
	   
	   //产品Tags
	   $post_product['tags']	               = trim($post['Tags']);
	   
	   //产品标题
	   $post_product['name']	               = trim($post['product_name']);
	   
	   $post_product['key']                    = $accountInfo['wish_key'];
	   
	   $productID = $post['productID'];
	   
	   $post_product['id'] = $productID;
	
	   //图片处理
	   if(isset($post['detailPicList'])&&!empty($post['detailPicList'])){
	       $mainImage = $post['detailPicList'][0];
	       //主副图处理,附图以|链接
	       unset($post['detailPicList'][0]);
	       foreach($post['detailPicList'] as $f){
	           $fuImage .= '|'.trim($f);
	       }
	       $fuImage = substr($fuImage,1);
	       
	       $post_product['main_image'] = $mainImage;
	       $post_product['extra_images'] = $fuImage;
	   }
	   
	   $this->set_access_token($account);
	   
	   $url = "https://merchant.wish.com/api/v1/product/update";
	   
	   $result = $this->wish->postCurlHttpsData($url,$post_product);
	   
	   $re = json_decode($result,true);
	   
	   $msg = '';
	   
	   if( isset($re['code']) && $re['code']==0){
	       $updata = array();
	       $option = array();
	       $updata['product_description']  =  $post_product['description'];
	       $updata['Tags']                 =  $post_product['tags'];
	       $updata['product_name']         =  $post_product['name'];
	       $updata['updateTime']           =  date('Y-m-d H:i:s');
	       $option = array(
	           'where' => array('productID'=>$productID)
	       );
	       $id = $this->wish_product_model->update($updata,$option);
	       
	       //如果主副图有更新，则要更新附表的数据
	       if(isset($post['detailPicList'])&&!empty($post['detailPicList'])){
	           $upDetail = array();
	           $options = array();
	           $upDetail['main_image'] = $mainImage;
	           $upDetail['extra_image'] = $fuImage;
	           $options = array(
	               'where' => array('productID'=>$productID)
	           );
	           $this->wish_product_detail_model->update($upDetail,$options);
	           
	           //如果有图片就更新产品sku的主图
	           foreach($products_sku as $pd){
	               $add_data = array();
	               $sku_colorArr = array();
	               $sku_colorArr = explode('-',$pd);
	               $sku_mainImage = $post[$sku_colorArr[1]];//sku的主图
	               $add_url = 'https://merchant.wish.com/api/v1/variant/update';
	               $add_data['sku'] 		= $sku_colorArr[0];
	               $add_data['main_image']  = empty($sku_mainImage) ? $mainImage : $sku_mainImage;
	               $add_data['key']         = $accountInfo['wish_key'];
	               
	               $add_return = $this->wish->postCurlHttpsData($add_url,$add_data);
	           }
	       }
	       
	       if($id>0){
	           $msg = '<span style="color:green">产品ID为'.$productID.'的广告修改wish线上成功，本地广告更新成功</span>';
	       }else{
	           $msg = '<span style="color:green">产品ID为'.$productID.'的广告修改wish线上成功，本地广告更新失败</span>';
	       }
	   }else{
	       $msg = '<span style="color:red">产品ID为'.$productID.'的广告修改wish线上失败，原因:'.$re['message'].'</span>';
	   }
	   echo $msg;
	   exit;
	}
	
	/**
	 * ajax获取图片
	 */
	/**
	 * 先从新图片系统中获取该sku下的图片
	 * 如果没有获取到，ajax读取美国图片服务器
	 */
	public function ajaxUploadDirImage(){
	     
	    $dirName  = strtoupper(trim($this->input->get_post('dirName')));
	     
	    $account      = trim($this->input->get_post('account'));
	    
	    $opt = trim($this->input->get_post('opt'));
	    
	    $shui = trim($this->input->get_post('shui'));
	    
	    //根据账号获取key值
	    $accountInfo = $this->wish_user_tokens_model->getKeyByAccount($account);

	    $host_url = $accountInfo['photo_url'];
	    
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
	                $result[$ke] = str_replace('imgurl.moonarstore.com',$host_url.':81',$v);
	            }
	        }
	        echo json_encode($result);
	        exit;
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
		         
	        	/**
	            foreach($result as $ke => $v){
	                if($suo==true){
	                    $photo_name = str_replace('/image/','',$v['url']);
	                    $s_url = '/image-resize/100x-x75/'.$photo_name;
	                }else{
	                    $s_url = $v['url'];
	                }
	                $result[$ke] = 'http://'.$host_url.':3000'.$s_url;
	            }
	            /**/
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
	                $result[$ke] = str_replace('imgurl.moonarstore.com',$host_url.':81',$v);
	            }
	        }
	    }
	     echo json_encode($result);
	     exit;
	
	}
	
	/**
	 * 批量编辑广告
	 */
	public function batchSaveToProduct(){
	     
	    $post = $this->input->post();
	
	    $productID = $post['productID'];
	    
	    $productIDArr = explode(',',$productID);
	    
	    $productIDs = array_filter($productIDArr);
	    
	    $msg = '';
	    
	    foreach($productIDs as $id){
	        
	        $productInfo = array();
	        
	        $accountInfo = array();
	        
	        $post_product = array();
	        
	        $productInfo = $this->wish_product_model->getInfoByProductID($id);
	        
	        //根据账号获取key值
	        $accountInfo = $this->wish_user_tokens_model->getKeyByAccount($productInfo['account']);
	        
	        //产品描述
	        $description 				           = trim($post['product_description']);
			$description					           = str_replace('&nbsp;',' ',$description);
			$description					           = str_replace('<p>',' ',$description);
			$descripts					           = str_replace('</p>',' ',$description);
	        $post_product['description']	        = $descripts;
	        
	        $post_product['key']                    = $accountInfo['wish_key'];
	        
	        $post_product['id']                     = $id;
	        
	        $this->set_access_token($productInfo['account']);
	        
	        $url = "https://merchant.wish.com/api/v1/product/update";
	        
	        $result = $this->wish->postCurlHttpsData($url,$post_product);
	        
	        $re = json_decode($result,true);
	        
	        if( isset($re['code']) && $re['code']==0){
	            $updata = array();
	            $option = array();
	            $updata['product_description']  =  $post_product['description'];

	            $option = array(
	                'where' => array('productID'=>$id)
	            );
	            $ids = $this->wish_product_model->update($updata,$option);
	            if($ids>0){
	                $msg .= '<span style="color:green">产品ID为'.$id.'的广告修改wish线上成功，本地广告更新成功</span><br/>';
	            }else{
	                $msg .= '<span style="color:green">产品ID为'.$id.'的广告修改wish线上成功，本地广告更新失败</span><br/>';
	            }
	        }else{
	            $msg .= '<span style="color:red">产品ID为'.$id.'的广告修改wish线上失败</span><br/>';
	        }
	        
	        echo $msg;
	        
	    }
	
	}
	
	/**
	 * 循环请求wish线上的API数据
	 * 和wishProductByApi方法相结合用
	 */
	public function wishGetProductData(){
	  for($i=1;$i>=1;$i++){
	  	$limit = 500;
	  	$start = ($i-1)*$limit;

	    $res = $this->wishProductByApi($start,$limit);
	    if($res['status'] === false){
	      $result['status'] = true;
	      echo json_encode($result);
	      exit;
	    }
	  }
	}
	
	/**
	 * 获取线上产品数据
	 * 重组线上的数据
	 */
	public function wishProductByApi($start,$limit){

	  set_time_limit ( 0 ); //页面不过时
	  ini_set('memory_limit', '2024M');
		
	  $result['status'] = true;

	  //获取wish账号列表
	  $option = array();
	  
	  $account = $this->model->getWishTokenList($option);
	  
	  $newAccount = array();
      //整理需要跑数据的账号数组
      foreach($account as $va){
         if($va['token_id'] ==1 || $va['token_id'] == 4 ){
            $newAccount[] = $va;
         }
      }

	  foreach($newAccount as $v){

		  //获取大账号的账号和key
		  $accounts['name'] = $v['account_name'];
		  $accounts['key'] = $v['wish_key'];

		  //调用wish类获取product的api,获取线上的原始数据
		  $arrdata = $this->wish->getWishProductByApi($accounts['key'],$accounts['name'],'',$start,$limit);

		  if(empty($arrdata)){
		  	$result['status'] = false;
		  	return $result;
		  }
		  
		  //对线上的原始数据重新整合
		  $dealData = $this->wish->wishLineAdvertisingArr($arrdata,$accounts['name']);

		  //把数据循环插入erp_wish_product_list表和erp_wish_product_list_detail表
		  foreach($dealData as  $ke => $v){
	       
		  	$flag = 0;
	  		$skuCount = count($v['skus']);//每个广告下的sku总数
	        
	        $this->db->trans_begin();//事务开始
	        
	        //先判断表中有没有这条数据，没有就插入，有的话就更新,判断依据是广告id
	        $productInfo = $this->wish_product_model->getInfoByProductID($v['products']['productID']);
	        
	        if(!empty($productInfo)){//如果广告存在，更新广告的时间，删除原广告下的sku详情
	        	
	          //更新广告数据
	          $up = $this->wish_product_model->updateDataByProductID($v['products']);
	
	          if(!$up){
	          	$result['status'] = false;
	            $this->db->trans_rollback();//事务回滚
	            continue;
	          }
	          
	          //广告更新成功，删除原有的productID下的sku
	          $de = $this->wish_product_detail_model->deleteByProductID($v['products']['productID']);
	          if(!$de){
	          	$result['status'] = false;
	            $this->db->trans_rollback();//事务回滚
	            continue;
	          }
	          
	          //重新插入该广告下的sku
	          foreach($v['skus'] as $s){
	             $sk = $this->wish_product_detail_model->add($s);
	             if($sk){
	               $flag +=1;
	             }
	          }
	
	         if($flag==$skuCount){
		        $this->db->trans_commit();//事务结束
		      }else{
		         $result['status'] = false;
		         $this->db->trans_rollback();//事务回滚
		         continue;//下一个广告数据
		      }
	          
	        }else{//如果广告ID不存在，插入数据
	           $v['products']['publishedTime'] = date('Y-m-d H:i:s');
	           $p = $this->wish_product_model->add($v['products']);
	
	           if(!$p){
	           	 $result['status'] = false;
		         $this->db->trans_rollback();//事务回滚
		         continue;//下一个广告数据
	           }
	           
	           //如果广告插入成功，把该广告下的sku插入详情表
	          foreach($v['skus'] as $s){
	             $sk = $this->wish_product_detail_model->add($s);
	             if($sk){
	               $flag +=1;
	             }
	          }
	          
	          if($flag==$skuCount){
		        $this->db->trans_commit();//事务结束
		      }else{
		         $result['status'] = false;
		         $this->db->trans_rollback();//事务回滚
		         continue;//下一个广告数据
		      }
		      
	        }
		  }
	  }
	  return $result;
	 // echo json_encode($result);exit;die;
	}
	
	/**
	 * 根据prodcutID获取线上产品信息
	 * 549cc0c5653d51047182d9f8
	 */
	public function getProductInfoByProductID(){
	
	}
	
	/**
	 * 数据导出
	 */
	public function exportData(){
		set_time_limit ( 0 ); //页面不过时
		ini_set('memory_limit', '1024M');
	    $type = $this->input->get_post('pageData');//type=some导出选中的数据，type=null导出所有的数据
	    $account = $this->input->get_post('account');//判断是否有筛选账号

	    $productID = array();//存放广告ID
	    if($type=='some'){
	      $productID = $this->input->get_post('productIds');//勾选中的ID
	    }else{
	      //查出该账号下所有的产品ID
	      $productID = $this->wish_product_model->getAllProductID($account);
	    }

	    $newData = array();
	    foreach($productID as $p){

	    	$options = array();
	    	$where = array();
	    	$join = array();

	    	$where = array('pd.productID'=>$p);
		    $options['select'] = array("{$this->wish_product_model->_table}.*",'pd.*');
			$join[] = array('erp_wish_product_detail pd',"pd.productID={$this->wish_product_model->_table}.productID");
			$options['join'] = $join;
			$options['where'] = $where;
			$data_list = $this->wish_product_model->getAll2array($options); //查询所有信息
			
			//拆分附图，已|分隔
//			$extra_image = array();
//			$extra_image = explode('|',$data_list['extra_image']);
//			unset($data_list['extra_image']);
//			$data_list['extra_image'] = $extra_image;
			foreach($data_list as $da){
				$extra_image = array();
				$extra_image = explode('|',$da['extra_image']);
				unset($da['extra_image']);
				$da['extra_image'] = $extra_image;
				$newData[] = $da;
			}
			
	    }

	   $this->load->library(array('phpexcel/PHPExcel'));
	   //大数据量导出
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array ('memoryCacheSize' => '128MB' );
		PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );
		$objPHPExcel = new PHPExcel ();
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
	    $objPHPExcel->getActiveSheet()
	        ->setCellValue('A1', 'Parent Unique ID')
	        ->setCellValue('B1', 'Unique ID')
	        ->setCellValue('C1', 'Product Name')
	        ->setCellValue('D1', 'Color')
	        ->setCellValue('E1', 'Size')
	        ->setCellValue('F1', 'Quantity')
	        ->setCellValue('G1', 'Tags')
	        ->setCellValue('H1', 'Description')
	        ->setCellValue('I1', 'Price')
	        ->setCellValue('J1', 'Shipping')
	        ->setCellValue('K1', 'Shipping Time')
	        ->setCellValue('L1', 'MSRP')
	        ->setCellValue('M1', 'Main Image URL')
	        ->setCellValue('N1', 'Extra Image URL 1')
	        ->setCellValue('O1', 'Extra Image URL 2')
	        ->setCellValue('P1', 'Extra Image URL 3')
	        ->setCellValue('Q1', 'Extra Image URL 4')
	        ->setCellValue('R1', 'Extra Image URL 5')
	        ->setCellValue('S1', 'Extra Image URL 6')
	        ->setCellValue('T1', 'Extra Image URL 7')
	        ->setCellValue('U1', 'Extra Image URL 8')
	        ->setCellValue('V1', 'Extra Image URL 9')
	        ->setCellValue('W1', 'Extra Image URL 10');
	    foreach($newData as $key => $v){
	       $i=$key+2;
	       $skuArr = array();
	       if (stripos($v['parent_sku'], '*') == false) {
	         //父sku去掉*前面的数据
	         $skuArr[1] = $v['parent_sku'];
	       }else{
	         $skuArr = explode('*',$v['parent_sku']);
	       }
	       
	       //去掉sku中[]的信息
		   if (stripos($skuArr[1], '[') !== false) {
			  $skuArr[1] = preg_replace('/\[.*\]/', '', $skuArr[1]);
		   }
	      //去掉sku中{}的信息
		   if (stripos($skuArr[1], '{') !== false) {
			  $skuArr[1] = preg_replace('/\{.*\}/', '', $skuArr[1]);
		   }
	       $objPHPExcel->setActiveSheetIndex(0)
	            ->setCellValue('A' . $i, isset($skuArr[1])?$skuArr[1]:'')
	            ->setCellValue('B' . $i, $v['sku'])
	            ->setCellValue('C' . $i, $v['product_name'])
	            ->setCellValue('D' . $i, $v['color'])
	            ->setCellValue('E' . $i, $v['size'])
	            ->setCellValue('F' . $i, $v['product_count'])
	            ->setCellValue('G' . $i, $v['Tags'])
	            ->setCellValue('H' . $i, $v['product_description'])
	            ->setCellValue('I' . $i, $v['product_price'])
	            ->setCellValue('J' . $i, $v['shipping'])
	            ->setCellValue('K' . $i, $v['shipping_time'])
	            ->setCellValue('L' . $i, $v['msrp'])
	            ->setCellValue('M' . $i, $v['main_image'])
	            ->setCellValue('N' . $i, isset($v['extra_image'][0]) ? $v['extra_image'][0] : '')
	            ->setCellValue('O' . $i, isset($v['extra_image'][1]) ? $v['extra_image'][1] : '')
	            ->setCellValue('P' . $i, isset($v['extra_image'][2]) ? $v['extra_image'][2] : '')
	            ->setCellValue('Q' . $i, isset($v['extra_image'][3]) ? $v['extra_image'][3] : '')
	            ->setCellValue('R' . $i, isset($v['extra_image'][4]) ? $v['extra_image'][4] : '')
	            ->setCellValue('S' . $i, isset($v['extra_image'][5]) ? $v['extra_image'][5] : '')
	            ->setCellValue('T' . $i, isset($v['extra_image'][6]) ? $v['extra_image'][6] : '')
	            ->setCellValue('U' . $i, isset($v['extra_image'][7]) ? $v['extra_image'][7] : '')
	            ->setCellValue('V' . $i, isset($v['extra_image'][8]) ? $v['extra_image'][8] : '')
	            ->setCellValue('W' . $i, isset($v['extra_image'][9]) ? $v['extra_image'][9] : '');
	    }
		$objPHPExcel->setActiveSheetIndex ( 0 );
		$objPHPExcel->getActiveSheet ()->setTitle ( 'ordersInfo' );
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="wish平台products_'.time().'.xls"' );
		header ( 'Cache-Control: max-age=0' );
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
		$objWriter->save ( 'php://output' );
		die();
	}

	 public function defineEbaySellerPrefix(){
		 $retrun_array =array();
		$all_array =array(
			array('prefix' => '001', 'userId' => 48),  //wangfei
			array('prefix' => '002', 'userId' => 22),  //chenlixia
			array('prefix' => '003', 'userId' => 29),  //chenjing
			array('prefix' => '004', 'userId' => 105), //wangxian
			array('prefix' => '005', 'userId' => 27),  //liufei
			array('prefix' => '006', 'userId' => 123), //wuqun
			array('prefix' => '007', 'userId' => 144), //yanghong
			array('prefix' => '008', 'userId' => 148),  //maonuosha
			array('prefix' => '009', 'userId' => 107),  //yangyang
			array('prefix' => '010', 'userId' => 150),  //taoling
			array('prefix' => '011', 'userId' => 132),  //liulian
			array('prefix' => '012', 'userId' => 128),  //sujing
			array('prefix' => '013', 'userId' => 161),  //xuhao
			array('prefix' => '014', 'userId' => 160),  //xuzhidan
			array('prefix' => '015', 'userId' => 163),  //jiangling
			array('prefix' => '016', 'userId' => 164),  //zhangsusu
			array('prefix' => '017', 'userId' => 162),  //zhaowenjin
			array('prefix' => '018', 'userId' => 165),  //lijiemeng
			array('prefix' => '019', 'userId' => 151),  //daiqi
			array('prefix' => '020', 'userId' => 152),  //liushuang
			array('prefix' => '021', 'userId' => 175),  //chenbowen
			array('prefix' => '022', 'userId' => 174),  //liulinlin
			array('prefix' => '023', 'userId' => 181),  //lihui
			array('prefix' => '024', 'userId' => 177),  //chendan
			array('prefix' => '025', 'userId' => 176),  //zhouzhixu
			array('prefix' => '027', 'userId' => 418),  //huchunyu
			array('prefix' => '028', 'userId' => 320),  //huangrong
			array('prefix' => '029', 'userId' => 470),  //yangleen
			array('prefix' => '030', 'userId' => 302),  //heyanhua
			array('prefix' => '031', 'userId' => 477),  //liuxiaoqing
			array('prefix' => '032', 'userId' => 407), //malin
			array('prefix' => '033', 'userId' => 492), // chenmeilin
			array('prefix' => '034', 'userId' => 504), // guohui
			array('prefix' => '035', 'userId' => 509), // liangguixiang
			array('prefix' => '036', 'userId' => 414), // caili
			array('prefix' => '037', 'userId' => 516), //yufang
			array('prefix' => '40', 'userId' => 567),//chengqiao
			array('prefix' => '42', 'userId' => 570),//boziwei
			array('prefix' => '330', 'userId' => 48), //wangfei2
			array('prefix' => '331', 'userId' => 22),//chenlixia2
			array('prefix' => '332', 'userId' => 107),//yangyang2
			array('prefix' => '333', 'userId' => 176),//zhouzhixu2
			array('prefix' => '334', 'userId' => 181),//lihuo2
			array('prefix' => '335', 'userId' => 320),//huangrong2
			array('prefix' => '336', 'userId' => 477),//liuxiaoqing2
			array('prefix' => '337', 'userId' => 302),//heyanhua2
			array('prefix' => '338', 'userId' => 492),//chenmeilin2
			array('prefix' => '339', 'userId' => 470),//yangleen2
			array('prefix' => '340', 'userId' => 516),//yufang2
			array('prefix' => '360', 'userId' => 567),//chengqiao2
			array('prefix' => '366', 'userId' => 570),//boziwei2
		);


		 foreach($all_array as $user){
			 $option = array();
			 $option['where']['id'] = $user['userId'];

			 $user_info = $this->Manages_model->getOne($option,true);

			 $prefix = intval($user['prefix']);

			 if(!empty($user_info)){
				 $retrun_array[$prefix] = $user_info['name'];
			 }else{
				 $retrun_array[$prefix] = '未找到该用户';
			 }

		 }

		 return $retrun_array;


	}

    
}
 
 