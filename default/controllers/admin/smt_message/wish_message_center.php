<?php
class wish_message_center extends Admin_Controller{
	
  private $wish;
  
  public $orders_status = array(
    1 => '新录入',
	2 => '不通过',
	3 => '已通过',
	4 => '已打印',
	5 => '已发货',
	6 => '已撤单',
	7 => '未付款',
	8 => '已发货[FBA]', 
	9 => '预打印'
  );
  
  public $lanauge = array(
  	 "be"=>"Belarusian", 
	 "cs"=>"Czech",
	 "da"=>"Danish", 
	 "nl"=>"Dutch",
	 "en"=>"English", 
	 "et"=>"Estonian", 
	 "fi"=>"Finnish", 
	 "de"=>"German", 
	 "el"=>"Greek", 
	 "hu"=>"Hungarian", 
	 "id"=>"Indonesian", 
	 "ja"=>"Japanese", 
	 "ko"=>"Korean", 
	 "lt"=>"Lithuanian", 
	 "nb"=>"Norwegian", 
	 "pt"=>"Portuguese", 
	 "ro"=>"Romanian", 
	 "ru"=>"Russian", 
	 "sk"=>"Slovak", 
	 "sl"=>"Slovenian", 
	 "es"=>"Spanish", 
	 "fr"=>"French", 
	 "it"=>"Italian", 
	 "sv"=>"Swedish",
	 "th"=>"Thai", 
	 "tr"=>"Turkish", 
	 "vi"=>"Vietnamese", 
	 "ar"=>"Arabic", 
	 "hr"=>"Croatian"
  );
	
  function __construct()
    {
        parent::__construct();
        $this->load->library('MyWish');
        $this->load->model(array(
            'sharepage','wish/wish_message_list_model','wish/wish_user_tokens_model','wish/wish_message_detail_model',
            'order/orders_model','moneyback_model','order/orders_products_model','smt_message/email_mod_model',
            'smt_message/email_mod_class_model','slme_user_model','wish/wish_message_reply_model','base_country_model'
        ));
        $this->model = $this->wish_message_list_model;
        $this->wish = new MyWish();
    }
    
    //获取所有的邮件信息
    public function message_center(){

    	$key = $this->user_info->key;//用户组key
		
		$uid = $this->user_info->id;//登录用户id
		 
		$string = '';
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= 50; //每页显示个数
		
		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		$in  = array();
		
		//$in['account'] = $account;
		
		//搜索
		$search_data = $this->input->get_post('search');
		$transactionID='';
		$isRead = '';//是否读取
		$isReturn = '';//是否回复
		$Acc = '';//账号
		$start_date = '';//刊登开始时间
		$end_date = '';//刊登结束时间

		if(isset($search_data['transactionID']) && $transactionID = trim($search_data['transactionID'])){
			$where['transactionID'] = $transactionID;
			$string .= '&search[transactionID]='.$transactionID;
		}
		//是否读取
		if(isset($search_data['isRead']) && $isRead=trim($search_data['isRead'])){
			$where['isRead'] = $isRead;
			$string .= '&search[isRead]='.$isRead;
		}
    	//是否回复
		if(isset($search_data['isReturn']) && $isReturn=trim($search_data['isReturn'])){
			$where['isReturn'] = $isReturn;
			$string .= '&search[isReturn]='.$isReturn;
		}
		//刊登开始时间
		if(isset($search_data['start_date']) && $start_date = trim($search_data['start_date'])){
			$where['last_update_date >='] = $start_date;
			$string .= '&search[start_date]='.$start_date;
		}
		//刊登结束时间
		if(isset($search_data['end_date']) && $end_date = trim($search_data['end_date'])){
			$where['last_update_date <'] = $end_date;
			$string .= '&search[end_date]='.$end_date;
		}
		if(isset($search_data['account']) && $Acc = trim($search_data['account'])){
			$where['account'] = $Acc;
			//unset($in['account']);
			$string .= '&search[account]='.$Acc;
		}

		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
		    'where'     => $where,
		    'order'	    => 'last_update_date desc'
		);

		$account_list = $this->wish_user_tokens_model->getWishTokenList($option=array());//获取所有的账号
		
		$data_list = $this->wish_message_list_model->getAll($options, $return_arr);

		$url = admin_base_url('smt_message/wish_message_center/message_center?').$string;
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$search_data['transactionID'] = $transactionID;
		$search_data['isRead'] = $isRead;
		$search_data['isReturn'] = $isReturn;
		$search_data['account'] = $Acc;
		$search_data['start_date']= $start_date;
		$search_data['end_date']= $end_date;
		
		$data = array(
		  'data'     => $data_list,
		  'page'     => $page,
		  'search'   => $search_data,
		  'account'	 => $account_list
		);
		
       $this->_template('admin/smt_message/wish_message_center',$data);
    }
    
    //获取邮件统计情况
    public function show_message_count(){
    	
       $account_list = $this->wish_user_tokens_model->getWishTokenList($option=array());//获取所有的账号   
    
       $search_data = $this->input->get_post('search');
 
	   $start_date = trim($search_data['start_date']);//导入开始时间
	   $end_date = trim($search_data['end_date']);//导入结束时间
	   
		
	   $sql = "SELECT isReturn,account,COUNT(*) as num FROM erp_wish_message_list";
	   
       if(!empty($start_date) && !empty($end_date)){
        $sql .= " where mail_export_time>='".$start_date."' and mail_export_time<'".$end_date."'";
       }
		
       $sql .=" group by isReturn,account";
      
     
     	$total = $this->model->result_array($sql);
     	
     	$dealData = array();
     	
     	foreach($total as $t){
     	  $dealData[$t['isReturn']][$t['account']] = $t;
     	}
     	
     	$search_data['start_date']= $start_date;
		$search_data['end_date']= $end_date;
 
     	$data['data'] = $dealData;
     	$data['account'] = $account_list;
     	$data['search'] = $search_data;

       $this->_template('admin/smt_message/wish_message_count',$data);
    }
    
    //读取邮件详情
    public function show_detail(){
    	
      $uid = $this->user_info->id;//登录用户id
     
      $id = $this->input->get_post('id');
      
      $mailInfo = $this->wish_message_list_model->getInfoByID($id);
    	
      $mailID = $mailInfo['mailID'];
      
      $account = $mailInfo['account'];
      
      //先改变该邮件状态为已读状态
      $this->wish_message_list_model->updateMailByID(array('mailID'=>$mailID,'account'=>$account),array('isRead'=>2));

      //根据邮件ID获取主表数据和附表数据
      $main_data = $this->wish_message_list_model->getInfoByMailID($mailID);
      $main_data['userInfo'] = unserialize($main_data['userInfo']);
      $main_data['orderInfo'] = unserialize($main_data['orderInfo']);
      $fu_data = $this->wish_message_detail_model->getDeailInfoByMailID($mailID);
     
      $dealArr = array();
      //处理图片链接
      foreach($fu_data as $key => $d){
        $imgs = array();
        $imgs = explode(',',$d['img_url']);
        foreach($imgs as $k => $i){
          $i = substr(trim($i),1);
          $i = str_replace("'","",$i);
          $d['imgArr'][$k] = trim($i);
        }
        $dealArr[$key] = $d;
      }
      
      //根据交易号获取订单详情数据和物流信息
		$options = array();

        $where = array();

        $options['select'] = array($this->orders_model->_table.'.*','s.*');

        $where["{$this->orders_model->_table}.pay_id"] = $main_data['transactionID'];
        
        $where["{$this->orders_model->_table}.sales_account"] = $account;
        
        $where["{$this->orders_model->_table}.orders_is_join"] = 0;

        $options['where'] = $where;

        $join[] = array('erp_shipment s',$this->orders_model->_table.'.shipmentAutoMatched=s.shipmentID');

        $options['join'] = $join;
      	
        $orderAndShipmentInfo = $this->orders_model->getAll2array($options);

        if(empty($orderAndShipmentInfo)){
          echo '该交易号'.$main_data['transactionID'].'的订单信息不存在';
          exit;
        }
        
        $orderAndShipmentInfoArr = array();
        foreach($orderAndShipmentInfo as $os){
	        //订单状态数据，发货状态还要加入追踪码查询
	    	 $orderStatusDescription = '';
	         switch ($os['orders_status']){
	                case 3:
	                $orderStatusDescription = '下载时间:'.$os['orders_export_time'];
	                break;
	
	                case 4:
	                $orderStatusDescription = '打印时间:'.$os['orders_print_time'];
	                break;
	
	                case 5:
	                $tracking_result = $this->wish_message_list_model->track_info($os['orders_shipping_code']);
	                        
	                $orderStatusDescription = '打印时间:'.$os['orders_print_time'];
	                $orderStatusDescription .= '<br />发货时间:'.$os['orders_shipping_time'];
	                $orderStatusDescription .= '<br />追踪码:' .($tracking_result ? '&nbsp;<a href="http://120.24.100.157:70/sellertool_api_info_detail.php?code='.$os['orders_shipping_code'].'&carrier1='.$tracking_result['carrier1'].'&carrier2='.$tracking_result['carrier2'].' title="点击展开关闭详情" target="_blank">'.$os['orders_shipping_code'].'</a>' : '&nbsp;'.$os['orders_shipping_code']);
	                $orderStatusDescription .= '<br />物流查询网址:'.$os['shipmentDescription'];
	                     
	                break;
	
	                default:
	                break;
	         }
	         
	         //获取该订单的退款信息
       		 $moneyBack  = $this->moneyback_model->getInfoByID(array('erp_orders_id'=>$os['erp_orders_id']));
       		 //获取该订单的产品信息
             $orders_products = $this->orders_products_model->getProductSkuByOrderId($os['erp_orders_id'],$os['orders_warehouse_id']);
	         $skuInfo = '';//显示sku的信息
	         foreach($orders_products as $op){
	          $skuInfo .='&nbsp;&nbsp;&nbsp;<a href="http://120.24.100.157:70/productsShow.php?pID='.$op['products_id'].'" target="_blank">'.$op['products_name_cn'].'('.$op['orders_sku'].'*'.$op['item_count'].')</a>';
	         }

	         //根据国家简码获取国家中文名称
       		 $codeArr = $this->base_country_model->getCountryInfoByCode($os['buyer_country_code']);
	         
	         $orderAndShipmentInfoArr[$os['erp_orders_id']]['orderStatusDescription'] = $orderStatusDescription;
	         $orderAndShipmentInfoArr[$os['erp_orders_id']]['moneyBack'] = $moneyBack;
	         $orderAndShipmentInfoArr[$os['erp_orders_id']]['orders_products'] = $orders_products;
	         $orderAndShipmentInfoArr[$os['erp_orders_id']]['skuInfo'] = $skuInfo;
	         $orderAndShipmentInfoArr[$os['erp_orders_id']]['codeArr'] = $codeArr;
	         $orderAndShipmentInfoArr[$os['erp_orders_id']]['orderInfo'] = $os;
        }
       
       
       //获取标记发货的天数
       $mark_array = array();
       foreach($main_data['orderInfo'] as $mo){
           $times = '';
	       $day = 0;
	       $ship_date = isset($mo['Order']['shipped_date']) ? $mo['Order']['shipped_date'] : '';
	       $mark_msg = '';
	       if(!empty($ship_date)){
	         $times = time()-strtotime($ship_date);
	         $day = floor($times/86400);
	       }
		   if($day>0){
		     $mark_msg = $day.' days ago';
		   }
		   $mark_array[$mo['Order']['order_id']] = $mark_msg; 
       }
      
       
       
       
      
       //获取wish回信主表的内容
       $mainTemplatArr = $this->email_mod_class_model->getMessageTemplateByType('WISH');
       
       //获取wish回信附表的数据
       $fuTemplatArr = $this->email_mod_model->getDataByID();
       $newFuTemplatArr = array();
      foreach($fuTemplatArr as $fu){
        $newFuTemplatArr[$fu['modID']] = $fu['modContent'];
      }
      
      //获取用户的所有信息
      $userArr = $this->slme_user_model->get_all_user_info('nickname');

      $data = array();
      $data['main'] = $main_data;//邮件主表信息
      $data['fu']   = $dealArr;//邮件附表信息
      $data['orders_status'] = $this->orders_status;
	  $data['mainTemplatArr'] = $mainTemplatArr;//wish回信主表的内容
	  $data['userArr'] = $userArr;
	  $data['lanauge'] = $this->lanauge;
	  $data['orderAndShipmentArr'] =$orderAndShipmentInfoArr;
	  $data['mark_msg'] = $mark_array;
	  $data['uid'] = $uid;

      $this->template('admin/smt_message/wish_message_detail',$data);
    }
    
    //根据回信模块主表数据获取附表数据
    public function getTemplateData(){
       $pID = $this->input->get_post('pID');
       $type = $this->input->get_post('type');
       
       //type=1，获取根据父ID获取附表数据；type=2，根据自增ID获取附表数据
       $result['status'] = 0;
       $data = array();
       $data = $this->email_mod_model->getDataByID($pID,$type);
       if(!empty($data)){
         $result['d'] = $data;
         $result['status'] = 1;
       }
       echo json_encode($result);
       die;
    }
    
    /**
     * type=1,正常回复邮件
     * type=2,邮件状态变成不必回复并且关闭邮件
     * type=3,请求wish的support；邮件状态变成已回复
     * type=4,手动关闭erp的信件
     */
    public function replayEmail(){
    	
     $uid = $this->user_info->id;//登录用户的信息
    	
     $result['status'] = false;//邮件发送默认失败
     
     $post = $this->input->post();

     $mailID = $post['mailID'];
     
     $account = $post['account'];
     
     $type = $post['type'];
     
    //根据账号名称获取账号信息
     $accountInfo = $this->wish_user_tokens_model->getKeyByAccount($account);
     
     $countmail = $post['countmail'];//已有的邮件回复次数
     
     //不必回复
     if($type==2){
       $this->noReplayEmail($mailID,$accountInfo,$countmail);
     }
     
     if($type==3){
       $this->ReplayWishSupport($mailID,$accountInfo,$countmail);
     }
     
     if($type==4){
       $this->closeErpMail($mailID,$accountInfo,$countmail);
     }
     
     $content = trim($post['content']);
     if(empty($content)){
       $result['msg'] = '回复内容不允许为空';
       echo json_encode($result);
       exit;
     }
     
     //根据邮件ID和账号获取邮件主表信息
     $mailInfo = $this->wish_message_list_model->getInfoByMailID($mailID);
     if($mailInfo['isReturn']==2){
       $result['msg'] = '该邮件已经回复过了';
       echo json_encode($result);
       exit;
     }
     
     $url = 'https://merchant.wish.com/api/v2/ticket/reply';
     $post_data = array();
     $post_data['id'] = $mailID;
     $post_data['reply'] = $content;
     $post_data['access_token'] = $accountInfo['access_token'];
     
     $replayResult = $this->wish->postCurlHttpsData($url,$post_data);
     
     $re = json_decode($replayResult,true);

     if(!empty($re['data']) && $re['data']['success']==1){
	      //邮件回复成功以后，往更改主表的状态，
	     $up_ml = array();
	     $op_ml = array();
	     $up_ml['isRead'] = 2;
	     $up_ml['isReturn'] = 2;
	     $up_ml['mail_update_time'] = date('Y-m-d H:i:s');
	     $op_ml['where'] = array('mailID'=>$mailID,'account'=>$account);
	     $this->wish_message_list_model->update($up_ml,$op_ml);
	     
	     //往附表添加一条信息记录
	     $add_md = array();
	     $add_md['account'] 	 = $account;
	     $add_md['mailID']  	 = $mailID;
	     $add_md['content_en']	 = $content;
	     $add_md['sender'] 		 = 'merchant';
	     $add_md['message_date'] = date('Y-m-d H:i:s');
	     $add_md['userID'] 		 = $uid;
	     $add_md['key'] 		 = $countmail;
	     $this->wish_message_detail_model->add($add_md);
	     
	     //往邮件统计表里添加记录erp_wish_message_reply
	     $add_ry = array();
	     $add_ry['mailID'] 		= $mailID;
	     $add_ry['userID'] 		= $uid;
	     $add_ry['create_date'] = date('Y-m-d H:i:s');
	     $add_ry['key'] 		= $countmail;
	     $add_ry['type'] 		= 1;
	     $this->wish_message_reply_model->add($add_ry);
	     
	     $result['status'] = true;
     }elseif($re['code']==1010 && $re['message']=='The ticket is closed'){
         $up_ml = array();
	     $op_ml = array();
	     $up_ml['isRead'] = 2;
	     $up_ml['isReturn'] = 4;//邮件已经关闭
	     $up_ml['mail_update_time'] = date('Y-m-d H:i:s');
	     $op_ml['where'] = array('mailID'=>$mailID,'account'=>$account);
	     $this->wish_message_list_model->update($up_ml,$op_ml);
         $result['msg'] = $re['message'];
     }else{
        $result['msg'] = $re['message'];
     }
     echo json_encode($result);
     exit;
    }
    
    //不必回复邮件
    public function noReplayEmail($mailID,$accountInfo,$countmail){
       $uid = $this->user_info->id;//登录用户的信息
       $result['status'] = true;
       $post = $this->input->post();
     
       $url = 'https://merchant.wish.com/api/v2/ticket/close';
	   $post_data = array();
	   $post_data['id'] = $mailID;
	   $post_data['access_token'] = $accountInfo['access_token'];

	   $replayResult = $this->wish->postCurlHttpsData($url,$post_data);
	   $re = json_decode($replayResult,true);
     
       if(!empty($re['data']) && $re['data']['success']==1){
          //先改变该邮件状态为isReturn=3
          $this->wish_message_list_model->updateMailByID(array('mailID'=>$mailID,'account'=>$accountInfo['account_name']),array('isReturn'=>3,'isRead'=>2));
          
           //往附表添加一条信息记录
		     $add_md = array();
		     $add_md['account'] 	 = $accountInfo['account_name'];
		     $add_md['mailID']  	 = $mailID;
		     $add_md['content_en']	 = '该邮件不必回复';
		     $add_md['sender'] 		 = 'merchant';
		     $add_md['message_date'] = date('Y-m-d H:i:s');
		     $add_md['userID'] 		 = $uid;
		     $add_md['key'] 		 = $countmail;
		     $this->wish_message_detail_model->add($add_md);
          
          //往邮件统计表里添加记录erp_wish_message_reply
	      $add_ry = array();
	      $add_ry['mailID'] 		= $mailID;
	      $add_ry['userID'] 		= $uid;
	      $add_ry['create_date'] 	= date('Y-m-d H:i:s');
	      $add_ry['key'] 			= $countmail;
	      $add_ry['type'] 			= 2;
	      $this->wish_message_reply_model->add($add_ry);
	      
	      $result['msg'] = '邮件关闭成功';
       }else{
       	  $result['status'] = 'false';
          $result['msg'] = $re['message'];
       }
//	     
//	     $re = json_decode($replayResult,true);
		echo json_encode($result);
		exit;
    }
    
    //请求wish support的邮件
	public function ReplayWishSupport($mailID,$accountInfo,$countmail){
       $uid = $this->user_info->id;//登录用户的信息
       $result['status'] = true;
       $post = $this->input->post();
     
       $url = 'https://merchant.wish.com/api/v2/ticket/appeal-to-wish-support';
	   $post_data = array();
	   $post_data['id'] = $mailID;
	   $post_data['access_token'] = $accountInfo['access_token'];

	   $replayResult = $this->wish->postCurlHttpsData($url,$post_data);
	   
	   $re = json_decode($replayResult,true);
     
       if(!empty($re['data']) && $re['data']['success']==1){
          //先改变该邮件状态为isReturn=2
          $this->wish_message_list_model->updateMailByID(array('mailID'=>$mailID,'account'=>$accountInfo['account_name']),array('isReturn'=>2,'isRead'=>2));
          
           //往附表添加一条信息记录
		     $add_md = array();
		     $add_md['account'] 	 = $accountInfo['account_name'];
		     $add_md['mailID']  	 = $mailID;
		     $add_md['content_en']	 = '该邮件请求wish support';
		     $add_md['sender'] 		 = 'merchant';
		     $add_md['message_date'] = date('Y-m-d H:i:s');
		     $add_md['userID'] 		 = $uid;
		     $add_md['key'] 		 = $countmail;
		     $this->wish_message_detail_model->add($add_md);
          
          //往邮件统计表里添加记录erp_wish_message_reply
	      $add_ry = array();
	      $add_ry['mailID'] 		= $mailID;
	      $add_ry['userID'] 		= $uid;
	      $add_ry['create_date'] 	= date('Y-m-d H:i:s');
	      $add_ry['key'] 			= $countmail;
	      $add_ry['type'] 			= 3;
	      $this->wish_message_reply_model->add($add_ry);
	      
	      $result['msg'] 	= '请求wish support成功';
       }else{
       	  $result['status'] = 'false';
          $result['msg'] 	= $re['message'];
       }
//	     
//	     $re = json_decode($replayResult,true);
		echo json_encode($result);
		exit;
    }
    
   public function closeErpMail($mailID,$accountInfo,$countmail){
      $uid = $this->user_info->id;//登录用户的信息
      $result['status'] = true;
      $post = $this->input->post();
     
       
      //先改变该邮件状态为isReturn=3
      $this->wish_message_list_model->updateMailByID(array('mailID'=>$mailID,'account'=>$accountInfo['account_name']),array('isReturn'=>3,'isRead'=>2));
          
      //往附表添加一条信息记录
      $add_md = array();
      $add_md['account'] 	 = $accountInfo['account_name'];
      $add_md['mailID']  	 = $mailID;
      $add_md['content_en']	 = '该邮件在wish后台已经关闭，不必回复';
      $add_md['sender'] 	 = 'merchant';
      $add_md['message_date'] = date('Y-m-d H:i:s');
      $add_md['userID'] 	  = $uid;
      $add_md['key'] 		 = $countmail;
      $this->wish_message_detail_model->add($add_md);
          
      //往邮件统计表里添加记录erp_wish_message_reply
      $add_ry = array();
      $add_ry['mailID'] 		= $mailID;
      $add_ry['userID'] 		= $uid;
      $add_ry['create_date'] 	= date('Y-m-d H:i:s');
      $add_ry['key'] 			= '0';
      $add_ry['type'] 			= 2;
      $this->wish_message_reply_model->add($add_ry);
      
      $result['msg'] = '邮件关闭成功';
      
	  echo json_encode($result);
	  exit;
    }
    
    //操作wish退款
    public function replayWishOrder(){
       $id = $this->input->get_post('id');
       $account = $this->input->get_post('account');
       $mailID = $this->input->get_post('mailID');

       $return_reason = array(
           '-1'  => '其他',
           '18'  => '误下单了',
	       '20'  => '配送时间过长',
	       '22'  => '商品不合适',
	       '23'  => '收到错误商品',
	       '24'  => '商品为假冒伪劣品',
	       '25'  => '商品已损坏',
	       '26'  => '商品与描述不符',
	       '27'  => '商品与清单不符',
	       '30'  => '产品被配送至错误的地址',
	       '31'  => '用户提供了错误的地址',
	       '32'  => '商品退还至发货人',
	       '33'  => 'Incomplete Order',
	       '34'  => '店铺无法履行订单',
	       '1001'  => 'Received the wrong color',
	       '1002'  => 'Item is of poor quality',
	       '1004'  => 'Product listing is missing information',
	       '1005'  => 'Item did not meet expectations',
	       '1006'  => 'Package was empty'
       );
       $data['reason'] = $return_reason;
       $data['wish_id'] = $id;
       $data['account'] = $account;
       $data['mailID'] = $mailID;

       $this->template('admin/smt_message/wish_message_refurnd',$data);
    }
    
    //处理wish退款
    public function preplying_refurn_wish_order(){
    	 $uid = $this->user_info->id;//登录用户的信息
	     $post = $this->input->post();

	     //根据账号名称获取账号信息
	     $accountInfo = $this->wish_user_tokens_model->getKeyByAccount($post['account']);
	
	     $data = array();
	     $data['id'] = $post['wishID'];
	     $data['reason_code'] = $post['reason_code'];
	     $data['reason_note'] = $post['content'];
	     $data['access_token'] = $accountInfo['access_token'];
	     
	     $url = 'https://merchant.wish.com/api/v2/order/refund';

	     $replyResult = $this->wish->postCurlHttpsData($url,$data);
	     $re = json_decode($replyResult,true);
  		 
	     $msg = '';
	     
	    if(!empty($re['data']) && $re['data']['success']==1){
	    	
          //先改变该邮件状态为isReturn=4已关闭
          $this->wish_message_list_model->updateMailByID(array('mailID'=>$post['mailID'],'account'=>$accountInfo['account_name']),array('isReturn'=>4,'isRead'=>2));
         
           //关闭邮件
           $c_url = 'https://merchant.wish.com/api/v2/ticket/close';
		   $post_data = array();
		   $post_data['id'] = $post['mailID'];
		   $post_data['access_token'] = $accountInfo['access_token'];
		   $closeResult = $this->wish->postCurlHttpsData($c_url,$post_data);
	       $reclose = json_decode($closeResult,true);
	       if(!empty($reclose['data']) && $reclose['data']['success']==1){
	         $msg = 'wish退款成功，邮件关闭成功';
	       }else{
	         $msg = 'wish退款成功，邮件关闭失败';
	       }
          
          $add_ry = array();
          //往邮件统计表里添加记录erp_wish_message_reply
	      $add_ry = array();
	      $add_ry['mailID'] 		= $post['mailID'];
	      $add_ry['userID'] 		= $uid;
	      $add_ry['create_date'] 	= date('Y-m-d H:i:s');
	      $add_ry['key'] 			= 0;
	      $add_ry['type'] 			= 4;//wish退款
	      $this->wish_message_reply_model->add($add_ry);
	     
	      $result['status'] = true;
	      
        }else{
          $msg = 'wish退款失败';
       	  $result['status'] = false;
        }
       
       $result['msg'] 	= $msg;

       echo json_encode($result);
       exit;
      
    }
    
    /**
     * wish的客服回信统计
     */
    public function customerReply(){
    	
       $sql = "SELECT userID,type,COUNT(mailID) as num FROM erp_wish_message_reply";
       
       $search_data = $this->input->get_post('search');
       
   	   $start_date = trim($search_data['start_date']);//导入开始时间
   	   
	   $end_date = trim($search_data['end_date']);//导入结束时间
		
	   
       if(!empty($start_date) && !empty($end_date)){
        $sql .= " where create_date>='".$start_date."' and create_date<'".$end_date."'";
       }
	   
       $sql .=" group by userID,type";

       $total = $this->wish_message_reply_model->result_array($sql);
       
       $deal = array();
       
       foreach($total as $t){
         $deal[$t['userID']][$t['type']] = $t['num']; 
       }
       
       //获取用户的所有信息
       $userArr = $this->slme_user_model->get_all_user_info('nickname');
       
       $search_data['start_date']= $start_date;
	   $search_data['end_date']= $end_date;
       
       $data = array(
         'data' => $deal,
         'user' => $userArr,
         'search' => $search_data
       );
      $this->template('admin/smt_message/wish_customer_count',$data);
    }
    
    /**
     * wish信件详情请求物流
     */
    public function get_shipping_info(){
    	
      $shipping_code = $this->input->get_post('shipping_code');
      
      $client = new SoapClient('http://120.24.100.157:70/service/track.wsdl');
      
      $result = $client->Get($shipping_code);
      
      $re = json_decode($result,true);
     
      $result = array();//存放结果数据
      
      $data = array();
      
      $result = $re[0];
      
      $data['result'] = $result;
      
      $this->template('admin/smt_message/wish_message_shipmentCheck',$data);
      
    }
}