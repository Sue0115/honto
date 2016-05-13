<?php
/**
 * wish自动撤单列表
 * @author Administrator
 *
 */
Class wish_cancle_order extends Admin_Controller{
	
	private $wish;
	
	function __construct(){
        parent::__construct();
        $this->load->Model(array(
           'wish_cancl_order_model','wish/wish_user_tokens_model','sharepage'
        ));
        $this->load->library('MyWish');
        $this->model = $this->wish_cancl_order_model;
        $this->wish = new MyWish();
    }
    
    public function index(){
    	
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
		
		//搜索
		$search_data = $this->input->get_post('search');

		$transactionId='';//交易号
		$Acc = '';//账号
		$wishID = '';//wishID
		//交易号筛选
		if(isset($search_data['transactionId']) && $transactionId = trim($search_data['transactionId'])){
			$where['transaction_id'] = $transactionId;
			$string .= 'search[transactionId]='.$transactionId;
		}
		//wishID筛选
		if(isset($search_data['wishID']) && $wishID = trim($search_data['wishID'])){
			$where['wish_order_id'] = $wishID;
			$string .= 'search[wishID]='.$wishID;
		}
		//账号筛选
		if(isset($search_data['account']) && $Acc = trim($search_data['account'])){
			$where['account'] = $Acc;
			$string .= 'search[account]='.$Acc;
		}
    	
		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
		    'where'     => $where,
			'order'		=> 'updateTime DESC'
		);
		
		$data_list = $this->model->getAll($options, $return_arr); //查询所有信息

		$c_url='order/wish_cancle_order';
		
		
	    $url = admin_base_url('order/wish_cancle_order/index?').$string;


		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$search_data['account'] = $Acc;
		$search_data['transactionId'] = $transactionId;
		$search_data['wishID'] = $wishID;
	
        $data = array(
	        'userInfo'     		=> $accountArr,
	        'data_list'   		=> $data_list,
	      	'page'    		    => $page,
          	'search'			=> $search_data
        );
        
       $this->_template('admin/order/wish_cancle_order_list',$data);
    }
    
}