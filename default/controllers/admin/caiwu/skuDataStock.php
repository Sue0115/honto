<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//每日sku实际库存查看
class skuDataStock extends Admin_Controller{

	
	function __construct(){		
		parent::__construct();
		$this->load->model(array(
								'stock/sku_date_stock_model','sangelfine_warehouse_model','sharepage',
								)
							);

		$this->model = $this->sku_date_stock_model;
		
	}
	
	
    function index(){
    	
      $string      = '';
		
	  $per_page	   = (int)$this->input->get_post('per_page');
		
	  $cupage	   = 20; //每页显示个数
	  
	  $like		   = array();
	 
	  $return_arr  = array ('total_rows' => true );	
		
	  //搜索
	  $search_data = $this->input->get_post('search');
	  
	  //初始化
	  $sku='';
	  $where=array();
	  $start_date = '';//开始时间
	  $end_date = '';//结束时间
	  //根据sku查找数据
	  if(isset($search_data['sku']) && $sku = trim($search_data['sku'])){
			$where['sku'] = $sku;
			$string .= '&search[sku]='.$sku;
	  }	
	  //根据所属仓库查找数据
	  if(isset($search_data['warehouse']) && $warehouse = trim($search_data['warehouse'])){
	      $where['warehouse_id'] = $warehouse;
	      $string .= '&search[warehouse]='.$warehouse;
	  } 

	  //开始时间
	  if(isset($search_data['start_date']) && $start_date = trim($search_data['start_date'])){
	      $where['create_time >='] = $start_date;
	      $string .= 'search[start_date]='.$start_date;
	  }
	  //结束时间
	  if(isset($search_data['end_date']) && $end_date = trim($search_data['end_date'])){
	      $where['create_time <='] = $end_date;
	      $string .= 'search[end_date]='.$end_date;
	  }
     //查询所有的仓库信息并且组装仓库数组
	 $warehouse=$this->sangelfine_warehouse_model->get_all_warehouse();
	 foreach($warehouse as $va){
		$warehouseArr[$va['warehouseID']]=$va['warehouseTitle'];
	 }
	  
	  $search_data['sku'] = $sku;
 
	 $option=array(
	   'page'		=> $cupage,
	   'per_page'	=> $per_page,
  	   'where' 		=> $where,
	   'order'		=> 'create_time desc',
	   'like'		=> $like,
	 );
	
	 $data_list=$this->model->getAll($option,$return_arr,true);

	 $url = admin_base_url('caiwu/skuDataStock?').$string;
	 
	 $page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
	
      $returnData=array( 
        'data_list'	   => $data_list,
        'warehouse'    => $warehouseArr,//仓库
        'search'	   => $search_data,
        'page'		   => $page,
      );
	  $this->_template('admin/caiwu/skuDataStockList',$returnData);
		
    }
}
