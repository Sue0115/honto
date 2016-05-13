<?php
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");
class auto_get_wish_message extends MY_Controller{
	
	private $wish;//实例化wish
	
    function __construct()
    {
        parent::__construct();
        
        $this->load->model(array(
            'wish/wish_user_tokens_model','wish/wish_message_list_model','wish/wish_message_detail_model'
        
          )
        );
	   $this->load->library('MyWish');
	   $this->wish = new MyWish;
    }
    
    function all_get_message($account=''){
    	
         $option = array();

         if(!empty($account)){
           $option['where']['account_name'] = $account;
         }
         
         $result = $this->wish_user_tokens_model->getAll2Array($option);

         foreach($result as $accountInfo){
         	
            for($i=1;$i>0;$i++){//使用死循环请求数据，防止暴力请求

               $start = ($i-1)*500;
               $limit = 500;
               
               $result = $this->get_message($accountInfo,$start,$limit);
               if($result==false){//如果沒有返回任何值，跳出死循環
                 break;
               }
               
            }
            
         }
    }
    
    function get_message($accountInfo,$start,$limit){
 
      $data = array();
      $get_data = array();//返回的數據
      $data['start'] = $start;
      $data['limit'] = $limit;
      $data['access_token'] = $accountInfo['access_token'];
      $url_string = http_build_query($data);
      $url = 'https://merchant.wish.com/api/v2/ticket/get-action-required?'.$url_string;
      $resultArr = $this->wish->getCurlData($url);
      $get_data = json_decode($resultArr,true);
      if(empty($get_data['data'])){
      	echo '没有要回复的信件';
        return false;
      }
      print_r($get_data);
      //循环插入erp_wish_message_list和erp_wish_message_detail表
      foreach($get_data['data'] as $gd){
      	
          $this->db->trans_begin();//开启事务
          
          
          $fu_flag = false;//附表的数据是否全部插入成功
          
          $info = array();
          //插入数据前判断邮件是否存在
          $info = $this->wish_message_list_model->getInfoByMailID($gd['Ticket']['id']);
          if(!empty($info)){
          	
          	$msg = '更新';
          	
          	$op_where = array('where'=>array('mailID'=>$gd['Ticket']['id']));
          	
            //更新主表内容
            //$this->wish_message_detail_model->deleteByMailID($gd['Ticket']['id']);
            $update_data = array();
            $update_data['label']   	 	 = addslashes($gd['Ticket']['label']);//邮件标题（英文）
            $update_data['sublabel']	 	 = addslashes($gd['Ticket']['sublabel']);//未知
            $update_data['subject']		 	 = addslashes($gd['Ticket']['subject']);//邮件标题（发件人本地语言）
            $update_data['state']  		 	 = $gd['Ticket']['state'];//wish邮件状态说明
            $update_data['stateID']		 	 = $gd['Ticket']['state_id'];//wish邮件状态ID
            $update_data['orderInfo']	  	 = serialize($gd['Ticket']['items']);//wish订单信息
            $update_data['open_date'] 	  	 = str_replace('T',' ',$gd['Ticket']['open_date']);//发件人发邮件的时间
            $update_data['last_update_date'] = str_replace('T',' ',$gd['Ticket']['last_update_date']);//最后更新时间，邮件发送时间取该值
            $update_data['isRead']		 	 = 1;//是否读取过
            $update_data['isReturn']		 = 1;//邮件是否回复过
            $update_data['photo_proof']		 = $gd['Ticket']['photo_proof'];//邮件是否包含图片
            $update_data['mail_update_time'] = date('Y-m-d H:i:s');//邮件更新时间
            $id = $this->wish_message_list_model->update($update_data,$op_where);
            
            //查找附表内容，用信件的键值来插入附表的数据
            $in_fu_data = array();
            $in_fu_data = $this->wish_message_detail_model->getDeailInfoByMailID($gd['Ticket']['id']);

            $H_count = count($in_fu_data);//该邮件已有的发送和回复的次数
            $X_count = count($gd['Ticket']['replies']);//该邮件所有的发送和回复的次数
            $c_count = 0;//插入附表的个数
            $Y_count = $X_count-$H_count;//需要插入的个数

            for($i=$H_count;$i<$X_count;$i++){

                $fu_data = array();
			    //处理图片url
			    //[u'https://s3-us-west-1.amazonaws.com/sweeper-production-ticket-image-uploads/bc2e4fa47e6111e596a7024da5ac6195.jpg']
			    $image_url = str_replace('[', '',$gd['Ticket']['replies'][$i]['Reply']['image_urls']);
			    $image_url = str_replace(']', '',$image_url);
			    $fu_data['mailID'] 			 = $gd['Ticket']['id'];//邮件ID号
			    $fu_data['account']			 = $accountInfo['account_name'];//账号
			    $fu_data['content_en'] 		 = isset($gd['Ticket']['replies'][$i]['Reply']['translated_message']) ? addslashes($gd['Ticket']['replies'][$i]['Reply']['translated_message']) : '';//邮件内容（英文）
			    $fu_data['content_cn']		 = isset($gd['Ticket']['replies'][$i]['Reply']['translated_message_zh']) ? addslashes($gd['Ticket']['replies'][$i]['Reply']['translated_message_zh']) : '';//邮件内容（中文）
			    $fu_data['content_oriagnal'] = addslashes($gd['Ticket']['replies'][$i]['Reply']['message']);//邮件内容（发件人本地语言）
			    $fu_data['sender']			 = $gd['Ticket']['replies'][$i]['Reply']['sender'];//发件人（user,wish,卖家）
			    $fu_data['img_url'] 		 = $image_url;//图片url
			    $fu_data['message_date']	 = str_replace('T',' ',$gd['Ticket']['replies'][$i]['Reply']['date']);
			    $fu_data['key']				 = $i;
				$fid = $this->wish_message_detail_model->add($fu_data);
				
	            if($fid>0){
				  $c_count += 1;
				}
            }
            if($Y_count==$c_count){
              $fu_flag = true;
            }
           
          }else{
          	
          	  $msg = '导入';
          	
          	  $fu_count = 0;//附表成功插入表的次数
          	  
	          $list_data = array();//主表message_list的数据
	          $list_data['account']		 	 = $accountInfo['account_name'];//账号
	          $list_data['mailID']  	 	 = $gd['Ticket']['id'];//邮件ID号
	          $list_data['label']   	 	 = addslashes($gd['Ticket']['label']);//邮件标题（英文）
	          $list_data['sublabel']	 	 = addslashes($gd['Ticket']['sublabel']);//未知
	          $list_data['subject']		 	 = addslashes($gd['Ticket']['subject']);//邮件标题（发件人本地语言）
	          $list_data['state']  		 	 = $gd['Ticket']['state'];//wish邮件状态说明
	          $list_data['stateID']		 	 = $gd['Ticket']['state_id'];//wish邮件状态ID
	          $list_data['userInfo']	 	 = serialize($gd['Ticket']['UserInfo']);//发件人用户信息
	          $list_data['orderInfo']	  	 = serialize($gd['Ticket']['items']);//wish订单信息
	          $list_data['transactionID'] 	 = $gd['Ticket']['transaction_id'];//wish交易号
	          $list_data['open_date'] 	  	 = str_replace('T',' ',$gd['Ticket']['open_date']);//发件人发邮件的时间
	          $list_data['last_update_date'] = str_replace('T',' ',$gd['Ticket']['last_update_date']);//最后更新时间，邮件发送时间取该值
	          $list_data['mail_update_time'] = date('Y-m-d H:i:s');//邮件导入时间
	          $list_data['mail_export_time'] = date('Y-m-d H:i:s');//邮件导入时间
	          $list_data['photo_proof']		 = $gd['Ticket']['photo_proof'];//邮件是否包含图片
			  $id = $this->wish_message_list_model->add($list_data);

	          //插入附表数据
			  foreach($gd['Ticket']['replies'] as $k => $v){
			    $fu_data = array();
			    //处理图片url
			    //[u'https://s3-us-west-1.amazonaws.com/sweeper-production-ticket-image-uploads/bc2e4fa47e6111e596a7024da5ac6195.jpg']
			    $image_url = str_replace('[', '',$v['Reply']['image_urls']);
			    $image_url = str_replace(']', '',$image_url);
			    $fu_data['mailID'] 			 = $gd['Ticket']['id'];//邮件ID号
			    $fu_data['account']			 = $accountInfo['account_name'];//账号
			    $fu_data['content_en'] 		 = isset($v['Reply']['translated_message']) ? addslashes($v['Reply']['translated_message']) : '';//邮件内容（英文）
			    $fu_data['content_cn']		 = isset($v['Reply']['translated_message_zh']) ? addslashes($v['Reply']['translated_message_zh']) : '';//邮件内容（中文）
			    $fu_data['content_oriagnal'] = addslashes($v['Reply']['message']);//邮件内容（发件人本地语言）
			    $fu_data['sender']			 = $v['Reply']['sender'];//发件人（user,wish,卖家）
			    $fu_data['img_url'] 		 = $image_url;//图片url
			    $fu_data['message_date']	 = str_replace('T',' ',$v['Reply']['date']);
			    $fu_data['key']				 = $k;
				$fid = $this->wish_message_detail_model->add($fu_data);

				if($fid>0){
				  $fu_count += 1;
				}
			  }
			  
			  //附表的插入次数等于邮件的个数，可以插入
			  if(count($gd['Ticket']['replies'])==$fu_count){
			     $fu_flag = true;
			  }
			  
          }
          
      	  if($this->db->trans_status() === TRUE && $id>0 && $fu_flag === true){
              $this->db->trans_commit();//事务结束
              echo '<span style="color:green;">'.$accountInfo['account_name'].'账号下的邮件'.$msg.'erp成功，邮件号'.$gd['Ticket']['id'].'</span><br/>';
          }else{
              $this->db->trans_rollback();
              echo '<span style="color:red;">'.$accountInfo['account_name'].'账号下的邮件'.$msg.'erp失败，邮件号'.$gd['Ticket']['id'].'</span><br/>';
          }
		  
      }
      return true;
  
    }
    
    //根据邮件好获取信息
    public function get_one_message($mailID){
    	
      if($mailID==""){
        echo '没有输入邮件ID';
        exit;
      }
      
      $re = $this->wish_message_list_model->getInfoByMailID($mailID);
      if(empty($re)){
        echo '邮件不存在';
        exit;
      }
      
      $accountInfo = $this->wish_user_tokens_model->getKeyByAccount($re['account']);
      
      $url = 'https://merchant.wish.com/api/v2/ticket?id='.$mailID.'&access_token='.$accountInfo['access_token'];
      $resultArr = $this->wish->getCurlData($url);
      $get_data = json_decode($resultArr,true);
      print_r($get_data);exit;
    }
}