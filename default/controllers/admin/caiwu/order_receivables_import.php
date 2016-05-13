<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
date_default_timezone_set('PRC');
set_time_limit ( 0 ); //页面不过时
ini_set('memory_limit', '2024M');
header('Content-Type: text/html; Charset=utf-8');

/**
 * 订单收款导入
 * @author Administrator
 *
 */
class order_receivables_import extends Admin_Controller{
	
	//业会核对结果说明数组
	private $resultArr = array(
	  1 =>'订单核对正常',
	  2 =>'平台总订单金额不等于erp订单总金额',
	  3 =>'平台总费用高出erp平台费20%',
	  4 =>'找不到该订单的收款信息',
	);
	
	function __construct(){
		
		parent::__construct();
		$this->load->model(
							array(
								'order/orders_model','shipment_model','sangelfine_warehouse_model',
								'orders_type_model','category_model','products/products_data_model',
								'orders_receivable_model','order/currency_info_model','orders_receivable_detail_model',
								'sharepage','slme_user_model','order/order_check_result_model','order/orders_products_model',
								'order_check_status_model','orders_type_model'
							)
		);
		$this->load->library('phpexcel/PHPExcel');
		$this->model = $this->orders_model;
		
	}
	
	function order_view(){
	
		$string = '';
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= intval($this->config->item('site_page_num')); //每页显示个数
		
		
		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		//搜索
		$search_data = $this->input->get_post('search');

		$pch='';//批次号
		$start_date = '';//开始时间
		$end_date = '';//结束时间

		if(isset($search_data['pch']) && $pch = trim($search_data['pch'])){
			$where['import_name'] = $pch;
			$string .= 'search[pch]='.$pch;
		}
		
		//导入开始时间
		if(isset($search_data['start_date']) && $start_date = trim($search_data['start_date'])){
			$where['import_time >='] = $start_date;
			$string .= 'search[start_date]='.$start_date;
		}
		//导入结束时间
		if(isset($search_data['end_date']) && $end_date = trim($search_data['end_date'])){
			$where['import_time <'] = $end_date;
			$string .= 'search[end_date]='.$end_date;
		}
		
		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
			'where'		=> $where
		);
		
		$data_list = $this->orders_receivable_model->getAll($options, $return_arr); //查询所有信息

		$url = admin_base_url('caiwu/order_receivables_import/order_view?').$string;
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$search_data['pch'] = $pch;
		$search_data['start_date']= $start_date;
		$search_data['end_date']= $end_date;
		
		$orders_type_arr = $this->orders_type_model->getOrdersType();//获取平台类型
		
		$userArr = $this->slme_user_model->get_all_user_info('nickname');//获取所有的用户

		//获取每批次的平台订单总金额、总扣除平台费、总扣除联盟费
		$sql = "SELECT pid,sum(orders_total) as orders_total,SUM(plat_amount) as plat_amount,sum(union_amount) as union_amount FROM erp_orders_receivable_detail group by pid";
     
   		$total_fee = $this->orders_receivable_detail_model->result_array($sql);
   		
   		$fee = array();
   		foreach($total_fee as $te){
   		  $fee[$te['pid']] = $te;
   		}

		$data = array(
		  'data'     	  => $data_list,
		  'page'     	  => $page,
		  'search'   	  => $search_data,
		  'orderTypeArr'  => $orders_type_arr,
		  'userInfo'	  => $userArr,
		  'fee'			  => $fee
		);

	    $this->_template('admin/caiwu/order_receivable_view',$data);
	}
	
	//导入订单收款数据
	function deal_data(){
		
		set_time_limit ( 0 ); //页面不过时
		
		ini_set('memory_limit', '1024M');
		
		$uid = $this->user_info->id;//登录用户的信息
		
		$this->load->library(array('phpexcel/PHPExcel'));//载入excel类
        
        $uploadFiles = $_FILES["excelFile"]["tmp_name"];	//临时存储目录

        $fileName = $_FILES["excelFile"]["name"];//上传文件的文件名

        $post = $this->input->post();
        
        //获取币种英文和中文数据
        $curr_info = $this->currency_info_model->getAllInfo();
        
        $curr_info_value = $this->currency_info_model->getAllInfo('currency_value');

        //开始读取excel文件的数据
		$PHPExcel = new PHPExcel(); 
		/**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/ 
		$PHPReader = new PHPExcel_Reader_Excel2007(); 
		if(!$PHPReader->canRead($uploadFiles)){ 
			$PHPReader = new PHPExcel_Reader_Excel5(); 
			if(!$PHPReader->canRead($uploadFiles)){ 
				echo 'no Excel'; 
				return ; 
			} 
		} 
		$PHPExcel = $PHPReader->load($uploadFiles); 
		
		/**读取excel文件中的第一个工作表*/ 
		$currentSheet = $PHPExcel->getSheet(0); 
		
		/**取得最大的列号*/ 
		$allColumn = $currentSheet->getHighestColumn(); 
		
		/**取得一共有多少行*/ 
		$allRow = $currentSheet->getHighestRow(); 
		
		$data = array();//存放读取的表格数据
		
		for($currentRow = 4;$currentRow <= $allRow;$currentRow++){ 
		/**从第A列开始输出*/ 
			for($currentColumn= 66;$currentColumn<= 79; $currentColumn++){ 
				
				$pronum= $currentSheet->getCellByColumnAndRow($currentColumn - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
				if($pronum instanceof PHPExcel_RichText){     //富文本转换字符串  
            	  $pronum = $pronum->__toString();  
				}
				$data[$currentRow][] = $pronum;
			
			}
		}
		
		//当导入类型是订单退款导入并且是smt平台的时候，检查H3标题是否等于本次订单退款总额，不是不执行导入，是的话更新表erp_orders_receivable_detail
		if($post['orders_type']==6 && $post['import_type']==2){
		   $title = $currentSheet->getCellByColumnAndRow(ord('H') - 65,3)->getValue();/**ord()将字符转为十进制数*/
		   if($title=='本次订单退款总额'){
		      //调用执行退款的方法，用于更新表erp_orders_receivable_detail
		      $this->deal_return_data($data);
		   }else{
		    echo '<script language="javascript">alert("操作错误，导入数据非退款数据，请检查！");window.location.href="'.admin_base_url('caiwu/order_receivables_import/order_view').'"</script>';
            exit;
		   }
		}

		//插入主表erp_orders_receivable的数据
		$main_data = array();
		
		//获取文件名，去掉后缀
		$nameArr = explode('.',$fileName);
		$file_name = $nameArr[0];
		
		$main_data['import_name'] = $file_name;
		$main_data['orders_type'] = $post['orders_type'];
		$main_data['import_count'] = count($data);
		$main_data['import_uid'] = $uid;
		$main_data['import_time'] = date('Y-m-d H:i:s');
		$main_ID = $this->orders_receivable_model->add($main_data);
	
		//插入附表erp_orders_receivable_detail的数据
		foreach($data as $da){
		
		   $fu_data = array();
		   
		   //该订单币种英文名称
		   $curr_type = array_search($da[10],$curr_info);

		   if($curr_type=='RMB'){//人民币的特殊处理
		       $curr_type = 'CNY';
		   }
		   //获取币率
		    $currency_value = 1;//默认1
		    if($curr_type=='CNY'){
		       $currency_value = $curr_info_value['RMB'];
		    }else{
		       $currency_value = $curr_info_value[$curr_type];
		    }
		   
		   $suffix = '';//订单金额前的前缀
		   if($post['orders_type']==6){
		     $suffix='USD';
		   }
		   $orders_total = str_replace($suffix,'',$da[5]);

		   //销售账号
		   $accounts = '';
		   $ordersInfo = array();
		   $ordersInfo = $this->model->getOrderInfoByID($da[1]);

		   if(!empty($ordersInfo)){
		     $accounts = $ordersInfo['sales_account'];
		   }
		   
		   $return_amount = trim(str_replace($curr_type,'',$da[6]));//包含退款金额
		   $plat_amount = abs(trim(str_replace($curr_type,'',$da[7])));//扣除平台佣金
		   $union_amount = abs(trim(str_replace($curr_type,'',$da[8])));//扣除联盟佣金
		   $loan_amount = trim(str_replace($curr_type,'',$da[9]));//放款金额

		   
		   $fu_data['pid'] 				= $main_ID;//批次号
		   $fu_data['times'] 			= $da[0];//发生时间
		   $fu_data['erp_buyer_id'] 	= $da[1];//订单号
		   $fu_data['order_paid_time'] 	= $da[2];//订单支付时间
		   $fu_data['products_id'] 		= $da[3];//商品ID
		   $fu_data['products_title'] 	= $da[4];//商品名称
		   $fu_data['orders_total'] 	= trim($orders_total);//订单金额  
		   $fu_data['return_amount'] 	= ($return_amount)>0 ? round($return_amount/$currency_value,2) : 0;//包含退款金额
		   $fu_data['plat_amount']	 	= ($plat_amount)>0 ? round($plat_amount/$currency_value,2) : 0;//扣除平台佣金
		   $fu_data['union_amount'] 	= ($union_amount)>0 ? round($union_amount/$currency_value,2): 0;//扣除联盟佣金
		   $fu_data['loan_amount'] 		= ($loan_amount)>0 ? round($loan_amount/$currency_value,2): 0;//放款金额
		   $fu_data['currency_type'] 	= $curr_type;//币种
		   $fu_data['is_special_order'] = $da[11];//是否特别放款订单
		   $fu_data['baozheng_rate'] 	= $da[12];//保证金冻结比例
		   $fu_data['remark'] 			= $da[13];//保证金冻结比例
		   $fu_data['orders_type']	 	= $post['orders_type'];//平台id
		   $fu_data['account'] 			= $accounts;//销售账号

		   $fu_ID = $this->orders_receivable_detail_model->add($fu_data);
		   
		}
		
		 echo '<script language="javascript">alert("数据导入成功");window.location.href="'.admin_base_url('caiwu/order_receivables_import/order_view').'"</script>';
         exit;
		
	}
	
	/**
	 * 执行导入退款订单数据的操作
	 * 更新表erp_orders_receivable_detail
	 */
	public function deal_return_data($data){
		
		$flag = 0;//成功插入的条数，默认0
		
		 foreach($data as $da){
		  
		   $updata = array();//要更新的数据
		   
		   $option = array();
		   
		   $updata['return_amount'] = $da[6];//本次订单退款总额
		   
		   $updata['seller_return_momey'] = $da[9];//本次卖家退款金额
		   
		   $option['where'] = array(
		     'erp_buyer_id' => $da[1],
		   	 'products_id'   => $da[3]
		   );
		   
		   $id = $this->orders_receivable_detail_model->update($updata,$option);
		   
		   if($id>0){
		      $flag += 1;
		      //更新erp_order_check_status 表的is_check=0
		      $up_data = array();
		      $op = array();
		      $up_data['is_check'] = 0;
		      $op['where'] = array('buyer_id'=>$da[1]);
		      $this->order_check_status_model->update($up_data,$op);
		   }
		 }
		 
		 if($flag==count($data)){
		    echo '<script language="javascript">alert("全部数据导入成功");window.location.href="'.admin_base_url('caiwu/order_receivables_import/order_view').'"</script>';
		 }else{
		   echo '<script language="javascript">alert("部分数据导入成功，部分失败！");window.location.href="'.admin_base_url('caiwu/order_receivables_import/order_view').'"</script>';
		 }
	  exit;
	}
	
	/**
	 * 未核对erp订单号导出
	 * is_check=0
	 */
	public function out_orders_ids(){
	   $options = array();
	   $options['select'] = array('erp_orders_id');
	   $options['where'] = array('is_check'=>0);
	   $result = $this->order_check_status_model->getAll2array($options);
	   $filename=date("Y-m-d").'-未核对订单号';
	    header("Content-type:application/octet-stream");
	    header("Accept-Ranges:bytes");
	    header("Content-type:application/vnd.ms-excel;charset=UTF-8");
	    header("Content-Disposition:attachment;filename=".$filename.".xls");
	    header("Pragma: no-cache");
	    header("Expires: 0");
	    $table = '';
	    $table.='<html  xmlns:x="urn:schemas-microsoft-com:office:excel" ><table cellpadding="0" cellspacing="0" border="1" style="br:mso-data-placement:same-cell;">'.PHP_EOL;
	    $table.='<thead>
	            <tr>
	              <td>'.iconv("utf-8","gb2312",'ERP订单号').'</td>                                             
	            </tr>
	            </thead>'.PHP_EOL;
	    foreach($result as $r){
	      $table .='<tr><td>'.$r['erp_orders_id'].'</td></tr>';
	    }
	    echo $table;
	}
	
	
	/**
	 * 根据批次号查看详情
	 * id为0查看所有详情
	 */
	function import_detail(){
		
		$id = 0;
		
		$id = $this->input->get_post('id');

		$string = '';
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= intval($this->config->item('site_page_num')); //每页显示个数
		
		
		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		if($id!=0){
		  $where['pid'] = $id;
		}
		
		//搜索
		$search_data = $this->input->get_post('search');

		$erp_orders_id='';//订单号
		$f_start_date = '';//发生的开始时间
		$f_end_date = '';//发生的结束时间
		$z_start_date = '';//订单支付的开始时间
		$z_end_date = '';//订单支付的结束时间
		$account = '';//账号

	    //订单号筛选
		if(isset($search_data['erp_buyer_id']) && $erp_orders_id = trim($search_data['erp_buyer_id'])){
			$where['erp_buyer_id'] = $erp_orders_id;
			$string .= '&search[erp_buyer_id]='.$erp_orders_id;
		}
		
		//账号筛选
		if(isset($search_data['account']) && $account = trim($search_data['account'])){
			$where['account'] = $account;
			$string .= 'search[account]='.$account;
		}
		
		//发生时间筛选的开始时间
		if(isset($search_data['f_start_date']) && $f_start_date = trim($search_data['f_start_date'])){
			$where['times >='] = $f_start_date;
			$string .= '&search[f_start_date]='.$f_start_date;
		}
		//发生时间筛选的结束时间
		if(isset($search_data['f_end_date']) && $f_end_date = trim($search_data['f_end_date'])){
			$where['times <'] = $f_end_date;
			$string .= '&search[f_end_date]='.$f_end_date;
		}
		
		//订单支付时间筛选的开始时间
		if(isset($search_data['z_start_date']) && $z_start_date = trim($search_data['z_start_date'])){
			$where['order_paid_time >='] = $z_start_date;
			$string .= '&search[z_start_date]='.$z_start_date;
		}
		//订单支付时间筛选的结束时间
		if(isset($search_data['z_end_date']) && $z_end_date = trim($search_data['z_end_date'])){
			$where['order_paid_time <'] = $z_end_date;
			$string .= '&search[z_end_date]='.$z_end_date;
		}
		
		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
			'where'		=> $where
		);
		
		$data_list = $this->orders_receivable_detail_model->getAll($options, $return_arr); //查询所有信息

		if($id==0){
		  $url   = admin_base_url('caiwu/order_receivables_import/import_detail?').$string;
		  $c_url = admin_base_url('caiwu/order_receivables_import/import_detail');
		}else{
		  $url   = admin_base_url('caiwu/order_receivables_import/import_detail?id='.$id).$string;
		  $c_url = admin_base_url('caiwu/order_receivables_import/import_detail?id='.$id);
		}
		
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$search_data['erp_orders_id'] = $erp_orders_id;
		$search_data['f_start_date']	  = $f_start_date;
		$search_data['f_end_date']	  = $f_end_date;
		$search_data['z_start_date']	  = $z_start_date;
		$search_data['z_end_date']	  = $z_end_date;
		$search_data['account']		  = $account;
		
		$orders_type_arr = $this->orders_type_model->getOrdersType();//获取平台类型
		
		//获取所有的批次号，并组合数组
		$import_name_arr = $this->orders_receivable_model->getAllInfo();

		$data = array(
		  'data'     	  => $data_list,
		  'page'     	  => $page,
		  'search'   	  => $search_data,
		  'orderTypeArr'  => $orders_type_arr,
		  'import_name_arr' => $import_name_arr,
		  'id'			  => $id,
		  'c_url'		  => $c_url
		);

	    $this->_template('admin/caiwu/order_receivable_detail_view',$data);
	}
	
	/**
	 * 根据批次号删除记录
	 * 先删除主表，后删除附表
	 */
	public function delete_import_detail(){
	  $id = $this->input->get_post('id');
	  $option = array();
	  $op = array();//主表删除条件
	  $op['where'] = array('id'=>$id);
	  $option['where'] = array('pid'=>$id);
	  $id = $this->orders_receivable_detail_model->delete($option);
	  if($id>0){
	  	$this->orders_receivable_model->delete($op);
	    echo '<script language="javascript">alert("数据删除成功");window.location.href="'.admin_base_url('caiwu/order_receivables_import/order_view').'"</script>';
	  }else{
	    echo '<script language="javascript">alert("数据删除失败");window.location.href="'.admin_base_url('caiwu/order_receivables_import/order_view').'"</script>';
	  }
	  exit;
	}
	
	/**
	 * 查看未核对的收款信息
	 */
	function no_check_detail(){
		
		$string = '';
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= intval($this->config->item('site_page_num')); //每页显示个数
		
		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		//搜索
		$search_data = $this->input->get_post('search');

		$erp_orders_id='';//订单号
		$f_start_date = '';//发生的开始时间
		$f_end_date = '';//发生的结束时间
		$z_start_date = '';//订单支付的开始时间
		$z_end_date = '';//订单支付的结束时间
		$account = '';//账号

	    //订单号筛选
		if(isset($search_data['erp_buyer_id']) && $erp_orders_id = trim($search_data['erp_buyer_id'])){
			$where['erp_buyer_id'] = $erp_orders_id;
			$string .= '&search[erp_buyer_id]='.$erp_orders_id;
		}
		
		//账号筛选
		if(isset($search_data['account']) && $account = trim($search_data['account'])){
			$where['account'] = $account;
			$string .= 'search[account]='.$account;
		}
		
		//发生时间筛选的开始时间
		if(isset($search_data['f_start_date']) && $f_start_date = trim($search_data['f_start_date'])){
			$where['times >='] = $f_start_date;
			$string .= '&search[f_start_date]='.$f_start_date;
		}
		//发生时间筛选的结束时间
		if(isset($search_data['f_end_date']) && $f_end_date = trim($search_data['f_end_date'])){
			$where['times <'] = $f_end_date;
			$string .= '&search[f_end_date]='.$f_end_date;
		}
		
		//订单支付时间筛选的开始时间
		if(isset($search_data['z_start_date']) && $z_start_date = trim($search_data['z_start_date'])){
			$where['order_paid_time >='] = $z_start_date;
			$string .= '&search[z_start_date]='.$z_start_date;
		}
		//订单支付时间筛选的结束时间
		if(isset($search_data['z_end_date']) && $z_end_date = trim($search_data['z_end_date'])){
			$where['order_paid_time <'] = $z_end_date;
			$string .= '&search[z_end_date]='.$z_end_date;
		}
		
		$where['status'] = 1;
		
		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
			'where'		=> $where
		);
		
		$data_list = $this->orders_receivable_detail_model->getAll($options, $return_arr); //查询所有信息

		
		$url   = admin_base_url('caiwu/order_receivables_import/no_check_detail?').$string;
		$c_url = admin_base_url('caiwu/order_receivables_import/no_check_detail');
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$search_data['erp_buyer_id'] = $erp_orders_id;
		$search_data['f_start_date']	  = $f_start_date;
		$search_data['f_end_date']	  = $f_end_date;
		$search_data['z_start_date']	  = $z_start_date;
		$search_data['z_end_date']	  = $z_end_date;
		$search_data['account']		  = $account;
		
		$orders_type_arr = $this->orders_type_model->getOrdersType();//获取平台类型
		
		//获取所有的批次号，并组合数组
		$import_name_arr = $this->orders_receivable_model->getAllInfo();

		$data = array(
		  'data'     	  => $data_list,
		  'page'     	  => $page,
		  'search'   	  => $search_data,
		  'orderTypeArr'  => $orders_type_arr,
		  'import_name_arr' => $import_name_arr,
		  'c_url'		  => $c_url
		);

	    $this->_template('admin/caiwu/no_check_detail_view',$data);
	}
    
	/**
	 * 订单收款导入和erp订单信息比对结果视图
	 * 从erp_order_check_result表中获取数据
	 * 业会核对结果
	 */
	function orderInfocomparison(){
		
	    $string = '';
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= intval($this->config->item('site_page_num')); //每页显示个数
		
		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		//搜索
		$search_data = $this->input->get_post('search');

		$orders_id ='';//erp订单相连接字符串
		
		$platform_orders_id = '';//平台订单号相连接字符串
		
		$start_date = '';//开始时间
		
		$end_date = '';//结束时间
		
		$data_status = '';//核对状态
		
		$orders_type = '';//平台类型
		
		$ship_date = '';//发货日期
		
		$select_type=$this->input->get_post('select_type');//筛选类型

		//erp订单号筛选
		if(isset($search_data['orders_id']) && $orders_id = trim($search_data['orders_id'])){
			$where['orders_id'] = $orders_id;
			$string .= 'search[orders_id]='.$orders_id;
		}
		
		//平台订单号筛选
		if(isset($search_data['platform_orders_id']) && $platform_orders_id = trim($search_data['platform_orders_id'])){
			$where['platform_orders_id'] = $platform_orders_id;
			$string .= 'search[platform_orders_id]='.$platform_orders_id;
		}
		
		//平台类型筛选
		if(isset($search_data['orders_type']) && $orders_type = trim($search_data['orders_type'])){
			$where['orders_type'] = $orders_type;
			$string .= 'search[orders_type]='.$orders_type;
		}
		
		//发货年月筛选
		if(isset($search_data['ship_date']) && $ship_date = trim($search_data['ship_date'])){
			$s_d = explode('-',$ship_date);
			$where['year_num'] = $s_d[0];
			$where['mouth_num'] = $s_d[1];
			$string .= 'search[ship_date]='.$ship_date;
		}
		
		//核对状态修改
		if(isset($search_data['data_status']) && $data_status = trim($search_data['data_status'])){
			if($data_status==1){
			  $where['data_status'] = $data_status;
			}elseif($data_status>1){
			   $where['data_status >'] = 1;
			}
			
			$string .= 'search[data_status]='.$data_status;
		}
		
		//导入开始时间
		if(isset($search_data['start_date']) && $start_date = trim($search_data['start_date'])){
			$where['check_time >='] = $start_date;
			$string .= 'search[start_date]='.$start_date;
		}
		//导入结束时间
		if(isset($search_data['end_date']) && $end_date = trim($search_data['end_date'])){
			$where['check_time <'] = $end_date;
			$string .= 'search[end_date]='.$end_date;
		}
		
		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
			'where'		=> $where
		);
		
		
		
		if($select_type==2){//导出异常数据
			
		   if($data_status<2){
		     echo "<script>alert('请筛选异常数据');window.location.href='".$_SERVER['HTTP_REFERER']."'</script>";
		     exit;
		   }
		   
		   $data_list = $this->order_check_result_model->getAll2array($options); //查询所有信息

		   $this->export_err_data($data_list);
		   
		}elseif($select_type==3){
		   $data_list = $this->order_check_result_model->getAll2array($options); //查询所有信息
		   $this->update_platFee_all($data_list);
		}else{
		   $data_list = $this->order_check_result_model->getAll($options, $return_arr); //查询所有信息
		}

		//获取列表单列总和
		$erp_orders_total = 0;//erp订单总金额
		$erp_plat_total = 0;//ERP订单总平台佣金
		$plat_total_order = 0;//平台总订单金额
		$plat_total_fee = 0;//平台总费用
		$plat_yong_fee = 0;//平台总佣金
		$plat_lianmeng_yong_fee = 0;//平台总联盟佣金
		$other = 0;//其它
		$plat_fk_fee = 0;//平台放款总金额
		$plat_tk_fee = 0;//平台退款总金额
		$ya_plat_fee = 0;//压在平台的总金额
		
		foreach($data_list as $dl){
		   $erp_orders_total 		+= $dl->erp_total;
		   $erp_plat_total 			+= $dl->erp_fee;
		   $plat_total_order 		+= $dl->platform_total;
		   $plat_total_fee 			+= $dl->platform_total_fee;
		   $plat_yong_fee 		    += $dl->platform_fee;
		   $plat_lianmeng_yong_fee  += $dl->platform_lianmen_fee;
		   $other 					+= $dl->plat_other;
		   $plat_fk_fee 			+= $dl->loan_amount;
		   $plat_tk_fee				+= $dl->refund_amount;
		   $ya_plat_fee 			+= $dl->residual_amount;
		}
		//获取平台类型
		$orders_type_arr = $this->orders_type_model->getOrdersType();//获取平台类型

		$url = admin_base_url('caiwu/order_receivables_import/orderInfocomparison?').$string;
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$search_data['orders_id'] = $orders_id;
		$search_data['platform_orders_id'] = $platform_orders_id;
		$search_data['start_date']= $start_date;
		$search_data['end_date']= $end_date;
		$search_data['data_status']= $data_status;
		$search_data['orders_type']= $orders_type;
		
		$orders_type_arr = $this->orders_type_model->getOrdersType();//获取平台类型

		$data = array(
		    'data'     	 			 => $data_list,
		    'page'     	 			 => $page,
		    'search'   	 			 => $search_data,
		    'orders_type'	  		 => $orders_type_arr,
		    'resultArr'     		 => $this->resultArr,
		    'orders_type'	  		 => $orders_type_arr,
		    'erp_orders_total' 		 => $erp_orders_total,
		    'erp_plat_total' 		 => $erp_plat_total,
		    'plat_total_order' 		 => $plat_total_order,
			'plat_total_fee' 		 => $plat_total_fee,
			'plat_yong_fee' 		 => $plat_yong_fee,
			'plat_lianmeng_yong_fee' => $plat_lianmeng_yong_fee,
			'other'					 => $other,
			'plat_fk_fee'			 => $plat_fk_fee,
			'plat_tk_fee'			 => $plat_tk_fee,
			'ya_plat_fee' 			 => $ya_plat_fee,
		);

	    $this->_template('admin/caiwu/orderInfocomparison_view',$data);
	}
	
	/**
	 * 全部更新平台费
	 */
	function update_platFee_all($data){
	   $msg = '';
	   foreach($data as $d){
	     $option = array();
	   	 $up_data = array();
	     $up_data['platFeeTotal'] = $d['platform_total_fee'];
	     $where['buyer_id'] = $d['platform_orders_id'];
	     $where['orders_is_join'] = 0;
	     $where['orders_is_split'] = 0;
	     $option['where'] = $where;
	     $id = $this->orders_model->update($up_data,$option);
	     if($id>0){
	       $msg .='平台号为'.$d['platform_orders_id'].'的平台费更新成功\n';
	     }else{
	       $msg .='平台号为'.$d['platform_orders_id'].'的平台费更新失败\n';
	     }
	   }
	   echo "<script>alert('".$msg."');window.location.href='".$_SERVER['HTTP_REFERER']."'</script>";
	   die;
	}
	
	/**
	 * 导出异常数据
	 */
   function export_err_data($data){
   	  $orders_type_arr = $this->orders_type_model->getOrdersType();//获取平台类型
   	  $phpExcel = new PHPExcel(); 
   	  $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	  $cacheSettings = array ('memoryCacheSize' => '512MB' );
	  PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );
	
	   //设置标题
	   $phpExcel->getProperties()->setCreator("Moonar")->setLastModifiedBy("Moonar");
       $phpExcel->getActiveSheet()
        ->setCellValue('A1', 'ERP订单号')
        ->setCellValue('B1', '平台订单号')
        ->setCellValue('C1', '平台交易号')
        ->setCellValue('D1', 'ERP订单总金额')
        ->setCellValue('E1', 'ERP订单总平台佣金')
        ->setCellValue('F1', '平台总订单金额')
        ->setCellValue('G1', '平台总费用')
		->setCellValue('H1', '平台总佣金')
        ->setCellValue('I1', '平台总联盟佣金')
        ->setCellValue('J1', '其它')
        ->setCellValue('K1', '平台放款总金额')
        ->setCellValue('L1', '平台退款总金额')
        ->setCellValue('M1', '压在平台的总金额')
        ->setCellValue('N1', '核对时间')
        ->setCellValue('O1', '订单类型')
        ->setCellValue('P1', '账号')
		->setCellValue('Q1', '订单发货月份')
        ->setCellValue('R1', '订单发货年份')
        ->setCellValue('S1', '导入备注')
        ->setCellValue('T1', '核对备注');
        foreach($data as $k => $d){
           $i = $k+2;
           $phpExcel->setActiveSheetIndex( 0 )
		        ->setCellValue( 'A' . $i, $d['orders_id'] )
                ->setCellValue( 'B' . $i, $d['platform_orders_id']) //中文标题 --改成申报名称 suwei20140919
                ->setCellValue( 'C' . $i, $d['trading_number'] )
                ->setCellValue( 'D' . $i, $d['erp_total'] )
                ->setCellValue( 'E' . $i, $d['erp_fee'])
                ->setCellValue( 'F' . $i, $d['platform_total'] ) 
                ->setCellValue( 'G' . $i, $d['platform_total_fee'] ) 
                ->setCellValue( 'H' . $i, $d['platform_fee'] )
                ->setCellValue( 'I' . $i, $d['platform_lianmen_fee'] )
                ->setCellValue( 'J' . $i, $d['plat_other'] )
                ->setCellValue( 'K' . $i, $d['loan_amount'] )
                ->setCellValue( 'L' . $i, $d['refund_amount']) 
                ->setCellValue( 'M' . $i, $d['residual_amount']) 
                ->setCellValue( 'N' . $i, $d['check_time'])
                ->setCellValue( 'O' . $i, $orders_type_arr[$d['orders_type']]['typeName'] ) 
                ->setCellValue( 'P' . $i, $d['sales_account'])
                ->setCellValue( 'Q' . $i, $d['mouth_num'] )
                ->setCellValue( 'R' . $i, $d['year_num'] ) //手动
                ->setCellValue( 'S' . $i, $d['note'])
                ->setCellValue( 'T' . $i, $this->resultArr[$d['data_status']] );
        }
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	  header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
	  header('Cache-Control: max-age=0');
	  $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
	  $objWriter->save('php://output');
	  die;
   }
   
   //批量更新和全部更新平台费
   public function update_plat_fee(){

       $post = $this->input->post();
	
	   $ids = $post['pID'];
	   $idArr = explode(',',$ids);
	   $up_count = 0;//更新成功的次数
	   $meg = '';
	   foreach($idArr as $id){
	   	 $option = array();
	   	 $up_data = array();
	     $data = explode('-',$id);
	     $ID = $data[0];//要更新的ID
	     $fee = $data[1];//要更新的平台费
	     $buyer_id = $data[2];//erp买家ID
	     $up_data['platFeeTotal'] = $fee;
	     $where['buyer_id'] = $buyer_id;
	     $where['orders_is_join'] = 0;
	     $option['where'] = $where;
	     $id = $this->orders_model->update($up_data,$option);
	     if($id>0){
	      $meg='平台订单号为'.$buyer_id.'的订单更新成功';
	     }else{
	       $meg='平台订单号为'.$buyer_id.'的订单更新失败';
	     }
	     $result[]=$meg;
	   }

	   echo json_encode($result);
	   die;

   }
	
	/**
	 * 业会核对结果点击详情显示
	 */
	function orderInfocomparison_detail(){
	  $erp_ids  = $this->input->get_post('id');//erp订单id字符串
	  $plat_ids = $this->input->get_post('pid');//平台订单id字符串
	  $erp_idArr = explode('+',$erp_ids);
	  $plat_idArr = explode('+',$plat_ids);
	  $orderArr = array();//存放订单信息数据
	  $plateArr = array();//存放导入收款详情的数据
	  
	  //获取订单信息数组
	  foreach($erp_idArr as $k => $erpID){
	    $orderInfo = array();
	    $orders_sku = array();
	    $orderInfo = $this->orders_model->get_orders_info($erpID);
	    $orders_sku = $this->orders_products_model->get_product_by_order_id($erpID);
	    $orderArr[$k]['order'] = $orderInfo;
	    $orderArr[$k]['sku'] 	 = $orders_sku;
	  }

	  //获取收款详情的数据
	  foreach($plat_idArr as $pid){
	    $platInfo = array();
	    $plateInfo = $this->orders_receivable_detail_model->getPlatInfoByID($pid);
	    $plateArr = array_merge($plateArr,$plateInfo);
	  }

	  $data = array(
	    'ordersArr' => $orderArr,
	    'platesArr' => $plateArr
	  );
	  $this->template('admin/caiwu/orderInfofocomparison_view',$data);
	}
}
