<?php
/**
 * wish监控
 * @author Administrator
 *
 */
class wish_monitor extends Admin_Controller{

	private $wish;
	
	function __construct(){
        parent::__construct();
        $this->load->Model(array(
            'order/orders_model','sharepage','wish/wish_user_tokens_model','shipping_by_orders_old_shipping_code_model',
            'wish_logistics_service_model','wish/wish_user_tokens_model','order/orders_products_model','wish/wish_shipping_model'
        ));
        $this->model = $this->orders_model;
        $this->load->library('MyWish');
        $this->wish = new MyWish();
       
    }
    
    //wish订单已发货但是平台未标记发货列表
    //最近两个月内
    function shipping_no_mark(){
       
       $string='';
    	
       $time = date('Y-m-d H:i:s',strtotime('-2 month'));
       
       $ship_time = date('Y-m-d H:i:s',strtotime('-1 day'));

       $per_page	= (int)$this->input->get_post('per_page');
		
	   $cupage	= intval($this->config->item('site_page_num')); //每页显示个数
		
	   $return_arr = array ('total_rows' => true );
	   
	   //搜索
	   $search_data = $this->input->get_post('search');
	   
	   $Acc = '';//账号
	   $orderID = '';//内单号
	   $buyer_id = '';//买家id
	   $start_date = '';//刊登开始时间
	   $end_date = '';//刊登结束时间

	   $where = array();
	   
	   $like = array();
	   
       $option = array();

       $where['orders_type'] = 13;
       
       $where['orders_status'] = 5;
       
       $where['orders_is_join'] = 0;
       
       $where['orders_export_time >='] = $time;
       
       $where['orders_shipping_time <='] = $ship_time;
       
       $where['ebayStatusIsMarked'] = 0;
       
       if(isset($search_data['account']) && $Acc = trim($search_data['account'])){
			$where['sales_account'] = $Acc;
			$string .= 'search[account]='.$Acc;
		}
    	if(isset($search_data['orderID']) && $orderID = trim($search_data['orderID'])){
			$where['erp_orders_id'] = $orderID;
			$string .= 'search[orderID]='.$orderID;
		}
    	if(isset($search_data['buyer_id']) && $buyer_id = trim($search_data['buyer_id'])){
			$like['buyer_id'] = $buyer_id;
			$string .= 'search[buyer_id]='.$buyer_id;
		}
       //发货开始时间
		if(isset($search_data['start_date']) && $start_date = trim($search_data['start_date'])){
			$where['orders_shipping_time >='] = $start_date;
			$string .= 'search[start_date]='.$start_date;
		}
		//发货结束时间
		if(isset($search_data['end_date']) && $end_date = trim($search_data['end_date'])){
			$where['orders_shipping_time <'] = $end_date;
			$string .= 'search[end_date]='.$end_date;
		}
		
       $option	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
		    'where'     => $where,
            'like'		=> $like,
       	    'order'     => 'orders_shipping_time asc',
       	    'user_index'=> 'USE INDEX (IDX_EBAYSTATUSISMARKED_TYPE_EXPORTTIME)'
		);
		
	   $data_list = $this->model->getAll($option, $return_arr); //查询所有信息

	   $account = $this->wish_user_tokens_model->getWishTokenList($option=array());
	   
       $accountArr = array();
       foreach($account as $user){
         $accountArr[$user['account_name']] = $user['choose_code'];
       }
	   
	   $url = admin_base_url('wish/wish_monitor/shipping_no_mark?').$string;
		
	   $page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
	   
	   $search_data['account'] = $Acc;
	   $search_data['orderID'] = $orderID;
	   $search_data['buyer_id'] = $buyer_id;
	   $search_data['start_date']= $start_date;
	   $search_data['end_date']= $end_date;
	   
	   $data = array(
		  'data'     => $data_list,
		  'page'     => $page,
	      'account'  => $accountArr,
	   	  'search'   => $search_data,
	      'type'	 => 1
		);
		
	   $this->_template('admin/wish/shipping_no_mark_list',$data);
	   
    }
    
    //欠货超过四天平台还未标记发货的
    function back_no_mark(){
    	
       $time_date = date('Y-m-d H:i:s',strtotime('-5 day'));
       
       $time = date('Y-m-d H:i:s',strtotime('-2 month'));
    	
       $string='';
    	
       $per_page	= (int)$this->input->get_post('per_page');
		
	   $cupage	= intval($this->config->item('site_page_num')); //每页显示个数
		
	   $return_arr = array ('total_rows' => true );
	   
	   //搜索
	   $search_data = $this->input->get_post('search');
	   
	   $Acc = '';//账号
	   $orderID = '';//内单号
	   $buyer_id = '';//买家id
	   $start_date = '';//刊登开始时间
	   $end_date = '';//刊登结束时间

	   $where = array();
	   
	   $like = array();
	   
       $option = array();

       $where['orders_export_time <='] = $time_date;
       
       $where['orders_export_time >='] = $time;
       
       $where['orders_is_join'] = 0;
       
       $where['orders_type'] = 13;
       
       $where['orders_status <'] = 5;
       
       $where['orders_status !='] = 2;
       
       $where['ebayStatusIsMarked'] = 0;
       
       if(isset($search_data['account']) && $Acc = trim($search_data['account'])){
			$where['sales_account'] = $Acc;
			$string .= 'search[account]='.$Acc;
		}
    	if(isset($search_data['orderID']) && $orderID = trim($search_data['orderID'])){
			$where['erp_orders_id'] = $orderID;
			$string .= 'search[orderID]='.$orderID;
		}
    	if(isset($search_data['buyer_id']) && $buyer_id = trim($search_data['buyer_id'])){
			$like['buyer_id'] = $buyer_id;
			$string .= 'search[buyer_id]='.$buyer_id;
		}
       //发货开始时间
		if(isset($search_data['start_date']) && $start_date = trim($search_data['start_date'])){
			$where['orders_shipping_time >='] = $start_date;
			$string .= 'search[start_date]='.$start_date;
		}
		//发货结束时间
		if(isset($search_data['end_date']) && $end_date = trim($search_data['end_date'])){
			$where['orders_shipping_time <'] = $end_date;
			$string .= 'search[end_date]='.$end_date;
		}
		
       $option	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
		    'where'     => $where,
            'like'		=> $like,
       	    'order'     => 'orders_shipping_time asc'
		);
		
	   $data_list = $this->model->getAll($option, $return_arr); //查询所有信息

	   $account = $this->wish_user_tokens_model->getWishTokenList($option=array());
       $accountArr = array();
       foreach($account as $user){
         $accountArr[$user['account_name']] = $user['choose_code'];
       }
	   
	   $url = admin_base_url('wish/wish_monitor/back_no_mark?').$string;
		
	   $page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
	   
	   $search_data['account'] = $Acc;
	   $search_data['orderID'] = $orderID;
	   $search_data['buyer_id'] = $buyer_id;
	   $search_data['start_date']= $start_date;
	   $search_data['end_date']= $end_date;
	   
	   $data = array(
		  'data'     => $data_list,
		  'page'     => $page,
	      'account'  => $accountArr,
	   	  'search'   => $search_data,
	      'type'	 => 2
		);
		
	   $this->_template('admin/wish/shipping_no_mark_list',$data);
    }
    
    //手动标记发货
    public function shipping_orders(){
        $results['status'] = 0;
        $results['msg'] = '';
        $post = $this->input->post();
        $orderID = $post['orderID'];//要标记发货的内单号
        
        $msg = '';//要返回的信息
        
        $flag = true;//默认需要上传挂号码
        
        //走香港平邮的渠道，该渠道的挂号码不需要上传
        $special_shipmentID = array(324,325);
        
        //获取所有的wish供应商
        $wishLogisticArray = array();
        $wishLogisticArr = $this->wish_logistics_service_model->getAll2array(array());
        foreach($wishLogisticArr as $wA){
            $wishLogisticArray[$wA['logistics_id']] = $wA['logistics_name'];
        }

        //查找订单和物流信息
        $options = array();
        
        $where = array();
        
        $options['select'] = array('erp_orders.*','s.shipmentWishCodeID','s.shipmentTitle','s.shipmentID');
        
        $where['erp_orders_id'] = trim($orderID);
        
        $where['orders_is_join'] = 0;
        
        $options['where'] = $where;
        
        $join[] = array('erp_shipment s','erp_orders.shipmentAutoMatched=s.shipmentID');
        
        $options['join'] = $join;
        
        $data = $this->orders_model->getOne($options,true);
        
        //特殊的物流渠道
        $special_arr = $this->shipping_by_orders_old_shipping_code_model->getAllShipment();
       
        if(empty($data)){
            $result['msg'] = '订单信息不存在';
            echo json_encode($result);
            exit;
        }
        
        if($data['orders_type']!=13){
            $result['msg'] = '不是wish平台的订单，不允许执行标记操作';
            echo json_encode($result);
            exit;
        }
        
        //根据账号获取key值
        $accountInfo = $this->wish_user_tokens_model->getKeyByAccount($data['sales_account']);
   
        
        if($data['orders_is_join'] != 0){
            $result['msg'] = '该订单是母单不允许标记发货';
            echo json_encode($result);
            exit;
        }
        
        //判断如果物流是erp_shiping_by_orders_old_shipping_code表里的物流的话，上传的是orders_old_shipping_code
        if(in_array($data['shipmentID'],$special_arr)){
            $tracking_number = $data['orders_old_shipping_code'];//wish所需要的追踪码
        }else{
            $tracking_number = $data['orders_shipping_code'];//wish所需要的追踪码
        }
        
        if(empty($wishLogisticArray[$data['shipmentWishCodeID']])){
            $result['msg'] = '订单供应商名称不能为空';
            echo json_encode($result);
            exit;
        }
        
        $this->set_access_token($accountInfo['account_name']);
        
        //如果走香港平邮的渠道，不需要上传挂号码
        if(in_array($data['shipmentID'],$special_shipmentID)){
            $tracking_number = '';
            $flag = false;
        }
        
        $wishIDArr = explode('+',$data['transactionIDNew']);
 

        foreach($wishIDArr as $wd){
            
            $mark_data = array();//要标记发货的订单
            $mark_data['id']                = $wd;
            $mark_data['tracking_number']   = $tracking_number;
            $mark_data['tracking_provider'] = $wishLogisticArray[$data['shipmentWishCodeID']];
            $mark_data['key']               = $accountInfo['wish_key'];
            
            //标记发货前要判断erp的sku和wish线上的是否一致
            //标记发货之前，如果订单拆分过。。查询wish平台线上的数据和orders_product的数据,如果两者的sku不一样，退出循环
            if($data['orders_is_split'] == 1){
                
                $tof_split = false;
                 
                $tof_split=$this->dealWithSplitOrder($accountInfo['wish_key'],$wd,$data['erp_orders_id']);
           
                if(!$tof_split['success']){
                    $msg .= 'wish订单号'.$wd.'在wish线上不存在<br/>';
                    continue;
                }
                if(!$tof_split['status']){
                    $msg .= 'wish订单号'.$wd.'下的sku与erp的sku不一致<br/>';
                    continue;
                }
            }
            //发送标记发货请求
            $url = 'https://merchant.wish.com/api/v1/order/fulfill-one';//标记发货api
            $tof = $this->wish->postCurlHttpsData($url,$mark_data); 
            $result = json_decode($tof,true);
      
            if($result['message'] == 'This order has been fulfilled already'){
                
                $datas = array();
                $where = array();
                $datas['ebayStatusIsMarked'] = 1 ;
                $where = array('erp_orders_id'=>$orderID);
                $this->orders_model->update($datas,array('where'=>$where));
                
                $in = array();
                if(empty($data['orders_shipping_code'])){
                    $in['account']    = $data['sales_account'];
                    $in['orderID']    = $orderID;
                    $in['wishID']     = $wd;
                    if($flag){//需要上传挂号
                        $in['type']       = 2;
                        $in['status']     = 0;
                    }else{
                        $in['type']       = 1;
                        $in['status']     = 1;
                    }
                    
                    $in['createTime'] = date("Y-m-d H:i:s");
                    $in['updateTime'] = date("Y-m-d H:i:s");
                    $this->wish_shipping_model->add($in);
                }else{
                    $in['account']    = $data['sales_account'];
                    $in['orderID']    = $orderID;
                    $in['wishID']     = $wd;
                    $in['type']       = 1;
                    $in['status']     = 1;
                    $in['createTime'] = date("Y-m-d H:i:s");
                    $in['updateTime'] = date("Y-m-d H:i:s");
                    $this->wish_shipping_model->add($in);
                }
                
                $msg .= $result['message'].'<br/>';
                continue;
            }
            
            if($result['message'] == 'This order has already started processing. Wait until it finishes to try again'){
               
                $datas = array();
                $where = array();
                $datas['ebayStatusIsMarked'] = 1 ;
                $where = array('erp_orders_id'=>$orderID);
                $this->orders_model->update($datas,array('where'=>$where));
            
                $in = array();
                $in['account']    = $data['sales_account'];
                $in['orderID']    = $orderID;
                $in['wishID']     = $wd;
                $in['type']       = 1;
                $in['status']     = 1;
                $in['createTime'] = date("Y-m-d H:i:s");
                $in['updateTime'] = date("Y-m-d H:i:s");
                $this->wish_shipping_model->add($in);
                $msg .= $result['message'].'<br/>';
                continue;
            }
            
            if($result['message'] == 'The tracking number for this order is confirmed already, it cannot be changed.'){
                $datas = array();
                $where = array();
                $datas['ebayStatusIsMarked'] = 1 ;
                $where = array('erp_orders_id'=>$orderID);
                $this->orders_model->update($datas,array('where'=>$where));
                
                $in = array();
                $in['account']    = $data['sales_account'];
                $in['orderID']    = $orderID;
                $in['wishID']     = $wd;
                $in['type']       = 1;
                $in['status']     = 1;
                $in['createTime'] = date("Y-m-d H:i:s");
                $in['updateTime'] = date("Y-m-d H:i:s");
                $this->wish_shipping_model->add($in);
                $msg .= $result['message'].'<br/>';
                continue;         
            }
            
            //如果wishID在wish平台不存在
            if(substr($result['message'],0,2)=='No' && $result['code']==1004){
                $msg .='wishID'.$wd.'在wish平台上不存在<br/>';
                continue;
            }
            
            //如果掛號碼已經使用過了
            if($result['code']==1003){
                $msg .='erp订单号为'.$orderID.'的挂号码已经用过了<br/>';
                continue;
            }
            
            //如果访问token过期了
            if($result['code']==1016){
                $msg .=$data['sales_account'].'的访问token已经过期<br/>';
                continue;
            }
            
            //如果标记发货成功
            if($result['code'] == 0 && $result['data']['success'] === true){
                $datas = array();
                $where = array();
                $datas['ebayStatusIsMarked'] = 1 ;
                $where = array('erp_orders_id'=>$orderID);
                $this->orders_model->update($datas,array('where'=>$where));
                
                $in = array();
                if(empty($data['orders_shipping_code'])){
                    $in['account']    = $data['sales_account'];
                    $in['orderID']    = $orderID;
                    $in['wishID']     = $wd;
                    if($flag){//需要上传挂号
                        $in['type']       = 2;
                        $in['status']     = 0;
                    }else{
                        $in['type']       = 1;
                        $in['status']     = 1;
                    }
                
                    $in['createTime'] = date("Y-m-d H:i:s");
                    $in['updateTime'] = date("Y-m-d H:i:s");
                    $this->wish_shipping_model->add($in);
                }else{
                    $in['account']    = $data['sales_account'];
                    $in['orderID']    = $orderID;
                    $in['wishID']     = $wd;
                    $in['type']       = 1;
                    $in['status']     = 1;
                    $in['createTime'] = date("Y-m-d H:i:s");
                    $in['updateTime'] = date("Y-m-d H:i:s");
                    $this->wish_shipping_model->add($in);
                }
                $msg .= 'wishID为'.$wd.'的订单标记发货成功<br/>';
            }else{
                $msg .= '标记发货失败，错误原因'.$result['message'].'<br/>';
            }
            
        }
        $resutl['msg'] = $msg;
        echo json_encode($resutl);
        exit;
    }
    
    //根据账号名设置访问token和code
    public function set_access_token($account_name){
        $accountInfo = $this->wish_user_tokens_model->getKeyByAccount($account_name);
        $this->wish->code 		= $accountInfo['code'];
        $this->wish->access_token = $accountInfo['access_token'];
        $this->wish->account_info = $accountInfo;
    }
    
    //拆单处理,针对标记发货和修改挂号码
    public function dealWithSplitOrder($data,$varr,$orderID){
    
        $result = array();
    
        $result['status'] = false;
    
        $result['success'] = false;
    
        //根据wishId，找出wish平台的产品
        $online_products = $this->wish->get_one($data,$varr);

        if(!empty($online_products)){
            $result['success'] = true;
        }
    
        if(!$result['success']){
            return $result;
        }
    
        $products = array();
        foreach ($online_products['data'] as $k => $v) {
            //整合线上产品的sku信息
            $tem_product = $this->wish->filter_sku(array('sku'=>$v['sku']),2);
            //存储线上sku的数据
            foreach ($tem_product as $key => $p) {
                $products[] = trim($p['sku']);
            }
        }

        //根据订单号获取该订单下的sku
        $sql = "select erp_orders_id,orders_sku from erp_orders_products where erp_orders_id={$orderID}";
        $order_product= $total = $this->orders_products_model->result_array($sql);

        $tof_split = false;
    
        foreach ($order_product as $k => $va) {
            if(in_array(trim($va['orders_sku']), $products)){
                $tof_split = true;
                break;
            }
        }
    
        $result['status'] = $tof_split;
    
        return $result;
    }
}