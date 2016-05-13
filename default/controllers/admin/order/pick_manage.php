<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//拣货单管理
class Pick_manage extends Admin_Controller{
	
	private $status_text = array(//拣货单状态
		'1' => '等待打印',
	    '2' => '等待包装',
		'3' => '正在包装',
		'4' => '包装完成',
		'5' => '已标记发货',
	);
	private $product_status = array(//包裹状态
		'0' => '拣货单包裹数',
		'1' => '等待包装',
	    '2' => '已扫描',
		'3' => '已包装',
		'4' => '已发货',
		'9' => '异常',
	);
	private $type_text = array(//拣货单类型
		'1' => '单品单件',
		'2' => '单品多件',
		'3' => '多品多件'
	);
	private $arrAdapter=array(
						'1'=>'美规',
						'2'=>'欧规',
						'3'=>'英规',
						'4'=>'澳规'
						);//转规头
	function __construct(){
		
		parent::__construct();

		$this->load->model(array(
								'sharepage','order/pick_model','products/pack_method_model',
								'order/pick_product_model','order/orders_model',
								'order/orders_products_model','shipment/shipment_model',
								'order/order_type_model','sangelfine_warehouse_model',
								'order/pick_print_model','print/products_data_model',
								'slme_user_model','stock/stock_detail_model','country_model','order/old_sku_new_sku_model'

								)
							);

		$this->load->model(array('operate_log_model'));
		
		$this->model = $this->pick_model;
		
	}

	function index(){
		
		$key = $this->user_info->key;//用户组key
		
		$uid = $this->user_info->id;//登录用户id
		
		$string = '';
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= intval($this->config->item('site_page_num')); //每页显示个数
		
		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		$like  = array();
		
		//搜索
		$search_data = $this->input->get_post('search');
		$shipmentID='';//物流
		$pick_id='';//拣货单号
		$pick_type='';//拣货单类型
		$pick_status='';//拣货单状态
		$pick_warehouse='';//拣货单所属仓库
		if(isset($search_data['shipmentID']) && $shipmentID = trim($search_data['shipmentID'])){
			$where['shipment_id'] = $shipmentID;
			$string .= 'search[shipmentID]='.$shipmentID;
		}
		if(isset($search_data['pick_id']) && $pick_id = trim($search_data['pick_id'])){
			$where[$this->model->_table.'.id'] = $pick_id;
			$string .= '&search[pick_id]='.$pick_id;
		}
		if(isset($search_data['pick_type']) && $pick_type = trim($search_data['pick_type'])){
			$where[$this->model->_table.'.type'] = $pick_type;
			$string .= '&search[pick_type]='.$pick_type;
		}
		if(isset($search_data['pick_status']) && $pick_status = trim($search_data['pick_status'])){
			$where[$this->model->_table.'.status'] = $pick_status;
			$string .= '&search[pick_status]='.$pick_status;
		}
		if(isset($search_data['pick_warehouse']) && $pick_warehouse = trim($search_data['pick_warehouse'])){
			$where[$this->model->_table.'.warehouse'] = $pick_warehouse;
			$string .= '&search[pick_warehouse]='.$pick_warehouse;
		}

		$where["{$this->model->_table}.status >="] = 1;

		if($key == 'root'){//超级管理员
			unset($where["{$this->model->_table}.status >="]);
		}else if($key == 'manager'){//管理员
			$where['warehouse'] = $this->user_info->warehouse_id;
		}else{
			
			$where['uid'] = $uid;
		}
        
		$shipmentList=$this->shipment_model->getAll2array(array('where'=>array('is_show'=>1,'shipmentEnable'=>1)));//查询所有物流信息
		foreach($shipmentList as $val){
		  $shipmentArr[$val['shipmentID']]=$val['shipmentTitle'];
		}
		$warehouse=$this->sangelfine_warehouse_model->get_all_warehouse();//查询所有的仓库信息并且组装仓库数组
		foreach($warehouse as $va){
		  $warehouseArr[$va['warehouseID']]=$va['warehouseTitle'];
		}

		
		$search_data['shipmentID'] = $shipmentID;
		$search_data['pick_id'] = $pick_id;
		$search_data['pick_type'] = $pick_type;
		$search_data['pick_status'] = $pick_status;
		$search_data['pick_warehouse'] = $pick_warehouse;
		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
			'where'		=> $where,
			'like'		=> $like,
			'order'		=> "id desc",
		);
		
		$options['select'] = array("{$this->model->_table}.*",'u.nickname');
		
		$join[] = array('erp_slme_user u',"u.id={$this->model->_table}.uid");
		
		$options['join'] = $join;
		
		$data_list = $this->model->getAll($options, $return_arr); //查询所有信息
		$pid = '';
		$pickid = '';
		//查询每个拣货单成功发货的订单数
		foreach($data_list as $k => $v){
		  $wheres['pick_id']=$v->id;
		  $wheres['status']=4;//抓取状态为4的数据
		  $opt['where']=$wheres;
		  $ordersArr=$this->pick_product_model->getAll2array($opt);
		  $data_list[$k]->count=count($ordersArr);
		  $data_list[$k]->total=$this->model->getorders_num($v->id);
		  $data_list[$k]->total=$data_list[$k]->total['0']['num'];//成功发货订单数
		  if($v->sku_num == $data_list[$k]->count && $data_list[$k]->count !== 0 && $v->create_time < strtotime("-3 day")){//自动结束全部包装完成的拣货单 过3天
			      $pickid = $data_list[$k]->id;
			      $pid .= $pickid.',';
			}
		}
		if($pid){
			$pid = rtrim($pid,',');
			$ret = $this->model->remove_repeat($pid);
		}
		$c_url='order/pick_manage';
		
		$url = admin_base_url('order/pick_manage?').$string;
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );

		//今日生成订单总数,今日已发货总数
		$data['total'] = 0;

		$data['has_shipped'] = 0;

		$data['need_pack'] = 0; //还需包装数

		$data['print_page'] = 0;//今天已打印拣货单

		$now_time = strtotime(date('Y-m-d'));
		
		$sql = "SELECT SUM(order_num) as num FROM erp_pick WHERE create_time>='".$now_time."'";

		$total = $this->model->query_array($sql);
		
		if(!empty($total['num'])){
			$data['total'] = $total['num'];
		}

		$sql = "SELECT COUNT(DISTINCT orders_id) as num FROM erp_pick_product WHERE STATUS=4 AND scan_time>='".$now_time."'";

		$total = $this->model->query_array($sql);

		if(!empty($total['num'])){
			$data['has_shipped'] = $total['num'];
		}

		$sql = "SELECT COUNT(DISTINCT orders_id) as num FROM erp_pick_product WHERE STATUS=1 ";

		$total = $this->model->query_array($sql);

		if(!empty($total['num'])){
			$data['need_pack'] = $total['num'];
		}

		//今日打印拣货单数
		$sql = "SELECT count(*) as num FROM erp_pick WHERE status>=2 and create_time>='".$now_time."'";

		$total = $this->model->query_array($sql);

		if(!empty($total['num'])){
			$data['print_page'] = $total['num'];
		}

		$data = array(
		    'c_url'              => $c_url, 
		    'key'                => $key,
			'data_list'	         => $data_list,
			'page'		         => $page,	
			'totals'	         => $return_arr ['total_rows'],	 //数据总数
			'search'    		 => $search_data,
		    'type_text'          => $this->type_text,
		    'status_text'        => $this->status_text,
			'shipmentList'		 => $shipmentArr,
			'warehouse'			 => $warehouseArr,
			'data'               => $data 
		); 

		$this->_template('admin/order/pick_list',$data); 
		
	}
	//已标记发货操作
	function pickcheck(){
		  header("Content-type:text/html;charset=utf-8");
		  
		  $uid = $this->user_info->id;//登录用户id

		  $pick_id=$this->input->get_post('pick_id');
		  
		  $warehouse=$this->input->get_post('warehouse');
		  
		  $type = $this->input->get_post('type')=='' ? '' : $this->input->get_post('type');
		  
		  if($pick_id){
				$pick_id = rtrim($pick_id,',');
				//事务开始
				  $this->db->trans_begin();
				$ret=$this->model->change_pick_id($pick_id);//将id下的拣货单改为已标记发货
			if($ret){
				$pick = $this->model->change_pick_status($pick_id);//拣货单详情表未拣货订单改为已通过
				$ordersid = $this->model->getorders_id($pick_id);
				$orders_id = '';
				if($ordersid){
					foreach($ordersid as $k=>$v){
						$orders_id .= $v['erp_orders_id'].',';
					}
				}
				if($orders_id){
					$orders_id = rtrim($orders_id,',');
					$pick = $this->model->check_pick($orders_id);//订单表erp_orders未拣货订单改为已通过
					if($pick){
						$this->db->trans_commit();//事务结束
						foreach($ordersid as $k=>$v){
					        $logData= array( //加入日志
						      				'operateUser' =>$uid,
						      				'operateTime '=>date('Y-m-d H:i:s',time()),
					                        'operateType' => 'update',
					                        'operateMod' => 'ordersManage',
					                        'operateKey' => $v['erp_orders_id'],
					                        'operateText' => '货找面单退回,订单状态变为已通过--欠货,拣货单号'.$v['pick_id'], 
					                    	);
					        $insertLog=$this->operate_log_model->add($logData);
						}
						echo "<script>alert('修改状态成功，未拣货订单改为已通过，请刷新页面！');history.back(-1);</script>";die;
					}else{
						$this->db->trans_rollback();
						echo "<script>alert('操作失败,请检查！');history.back(-1);</script>";die;
					}
				}else{
					$ret=$this->model->change_pick_id($pick_id);//将id下的拣货单改为已标记发货
					if($ret){
						$this->db->trans_commit();//事务结束
						echo "<script>alert('修改状态成功，未拣货订单改为已通过，请刷新页面！');history.back(-1);</script>";die;
					}else{
						$this->db->trans_rollback();
						echo "<script>alert('操作失败,请检查！');history.back(-1);</script>";die;
					}
					
				}
			}
		}
	}
	function info(){
		
		parent::info();//调用父类info方法，如果有post数据则调用save方法
		
		$key = $this->user_info->key;//用户组key
		
		$uid = $this->user_info->id;//登录用户id
		
		$id	=	intval($this->input->get_post('id'));

		$options = array();
		
		if($id>0){
			$options['where'] = array('id'=>$id);
		}
		
		$item = $this->model->getOne($options);

		//物流方式
		$shipment_options = array();
		
		$shipment_options['where']['shipmentEnable'] = '1';

		$shipment_options['where']['is_show'] = '1';

		$shipment_options['where']['shipment_warehouse_id'] = $this->user_info->warehouse_id;

		$shipment_options['order_by'] = 'shipmentCategoryID';
		 
		$shipment = $this->shipment_model->getAll2array($shipment_options);

		foreach ($shipment as $k => $v) {
			$shipment[$k]['is_check'] = '';
			if($v['is_check'] == 1){
				$shipment[$k]['is_check'] = 'checked';
			}
		}

		//仓库
		$warehouse = $this->sangelfine_warehouse_model->get_all_warehouse();
		//订单平台
		$order_type = $this->order_type_model->get_all_used_order_type();

		$data = array(
			'item'		  => $item,
			'shipment'    => $shipment,
			'order_type'  => $order_type,
			'warehouse'   => $warehouse,
			'type_text'   => $this->type_text
		);
		
		$this->_template('admin/order/pick_info',$data);
		
	}
	
	function save(){
		
		$key = $this->user_info->key;

		$uid = $this->user_info->id;//登录用户id
		
		$id = (int)$this->input->get_post('id');
		
		$data = array();

		$data['warehouse'] = (int)trim($this->input->get_post('warehouse'));

		$pick_type = $data['type'] = (int)trim($this->input->get_post('type'));
		
		$data['shipment_id'] = $this->input->get_post('shipment_id');

		$data['order_type'] = (int)trim($this->input->get_post('order_type'));
		
		$data['order_num'] = (int)trim($this->input->get_post('order_num'));
		
		if(empty($data['shipment_id'])){
			echo '{"info":"物流不允许为空","status":"n"}';exit();	
		}
		
		if($data['order_num'] <= 0){
			echo '{"info":"订单数必须大于0","status":"n"}';exit();
		}
			
		if($id){//修改
			
			echo '{"info":"拣货单已经生成不允许修改","status":"n"}';die;
			
		}else{//新增

			//判断物流面单尺寸是否都一致
			$template_array = $this->shipment_model->get_shipment_template($data['shipment_id']);
			
			if($template_array){
				$one_template = trim($template_array[0]['page_size']);
				foreach ($template_array as $t) {
					$tmp = trim($t['page_size']);
					if($one_template != $tmp){
						echo '{"info":"物流渠道中有不一致的面单尺寸，不允许生成拣货单","status":"n"}';die;
					}
				}
			}

			//查找符合条件的订单
			$order = $this->find_order($data,true);

			//print_r($order);
			if(empty($order)){
				echo '{"info":"没有找到符合条件的订单，请重新选择","status":"n"}';
				die;
			}
			
			//插入拣货单记录
			$data['uid'] = $uid;
			$data['create_time'] = time();
			$data['status'] = 1;
			unset($data['order_num']);

			if(is_array($data['shipment_id'])){
				$data['shipment_id'] = join(',',$data['shipment_id']);
			}

			$pick_id = $this->model->add($data);

			if(empty($pick_id)){
				echo '{"info":"拣货单生成失败，请重新操作","status":"n"}';die;
			}

			$pick_data = array();
			$pick_data['order_num'] = 0;//订单总数
			$pick_data['sku_num'] = 0;//sku总数
			$pick_data['num'] = 0;//货品总数

			foreach ($order as $key => $o) {
				
				$erp_orders_id = $o['erp_orders_id'];
				
				//查询订单状态
				$now_order = $this->orders_model->getOne(array('erp_orders_id'=>$erp_orders_id,'orders_status'=>'3','orders_is_join'=>'0'),true);

				if(empty($now_order)){//订单状态改变了
					continue;
				}

				//获取订单详情数据
				$order_product = array();
				$product_options['where']['erp_orders_id']= $erp_orders_id;
				$order_product = $this->orders_products_model->getAll2Array($product_options);
				
				if(empty($order_product)){//订单中没有sku
					continue;
				}

				$count = count($order_product);
				//单品单件,单品多件
				if($pick_type == 1 || $pick_type == 2){
					if($count !=1){
						continue;
					}
				}
				
				//多品多件
				if($pick_type == 3){
					if($count <1){
						continue;
					}
				}

				//事务开始
				$this->db->trans_begin();

				$product_tof = true;
				$product_num = 0;
				//把产品数据插入拣货单明细表中
				foreach ($order_product as $k => $product) {
					$product_data = array();
					$product_data['pick_id'] = $pick_id;
					$product_data['orders_id'] = $erp_orders_id;
					$product_data['product_sku'] = $product['orders_sku'];
					$product_data['product_num'] = $product['item_count'];
					$product_data['status'] = 1;
					$product_data['create_time'] = time();

					$product_data['basket_num'] = $pick_data['order_num']+1;

					$tof_pick = $this->pick_product_model->add($product_data);
					
					if(empty($tof_pick)){
						$this->db->trans_rollback();
						$product_tof = false;
						break;
					}

					$product_num += $product['item_count'];
				}

				//
				$order_tof = false;
				$log_tof = false;
				if($product_tof){
					//订单状态变为已打印
					$order_tof = $this->orders_model->change_order_status($erp_orders_id,4);
					
					//写入操作日志
					if($order_tof){
						$data = array();
						$data['operateUser'] = $uid;
						$data['operateKey'] = $erp_orders_id;
						$data['operateText'] = "生成拣货单（{$pick_id}），订单状态变为已打印";

						$log_tof = $this->operate_log_model->add_order_operate_log($data);
						
					}
				}

				if($product_tof && $order_tof && $log_tof && $this->db->trans_status() === TRUE){
					$pick_data['order_num']++;
					$pick_data['sku_num'] += count($order_product);
					$pick_data['num'] += $product_num; 
					$this->db->trans_commit();//事务结束
				}else{
					$this->db->trans_rollback();
				}

				
			}

			//生成结束,更新拣货单信息
			$options = array();
			$options['where']['id'] = $pick_id;
			if($pick_data['order_num'] == 0){//如果生成订单总数为零的，拣货单状态为-1
				$pick_data['status'] = -1;
			}

			$pick_tof = $this->model->update($pick_data,$options);
			
			if($pick_tof){
				echo '{"info":"成功生成拣货单：'.$pick_id.',订单总数：'.$pick_data['order_num'].',sku总数：'.$pick_data['sku_num'].',商品总数：'.$pick_data['num'].'","status":"n"}';
			}else{
				echo '{"info":"更新拣货单：'.$pick_id.'订单总数、sku总数、商品总数失败。请告知IT。","status":"n"}';
			}
			
		}
		
		die;
	}

	//查找符合条件的订单
	public function find_order($data){

		$options = array();

		$where = array();

		$where_in = array();

		//已通过
		$where['orders_status'] = 3;

		//不欠货
		$where['orders_is_backorder'] = 0;

		//有追踪码
		$where['orders_shipping_code !='] = '';

		//很重要的判断
		$where['orders_is_join'] = '0';

		//仓库
		if(isset($data['warehouse']) && !empty($data['warehouse'])){
			$where['orders_warehouse_id'] = $data['warehouse'];
		}

		//拣货单类型
		if(isset($data['type']) && !empty($data['type'])){
			
			$options['type'] = $type = $data['type'];

			if($type == 1){//单品单件
				//$where['isMixed'] = 0;
				$where['p.item_count'] = 1;
			}

			if($type == 2){//单品多件
				//$where['isMixed'] = 0;
				$where['p.item_count > '] = 1;
				$where['p.item_count <= '] =200;
			}

			if($type == 3){//多品多件
				//$where['isMixed'] = 1;
			}

		}

		//订单类型
		if(isset($data['order_type']) && !empty($data['order_type'])){
			$where['orders_type'] = $data['order_type'];
		}

		//物流
		if(isset($data['shipment_id']) && !empty($data['shipment_id'])){
			$where['shipmentAutoMatched'] = $data['shipment_id'];

			if(is_array($data['shipment_id'])){
				unset($where['shipmentAutoMatched']);
				$where_in['shipmentAutoMatched'] = $data['shipment_id'];
				$options['where_in'] = $where_in;
			}
		}

		//订单数量
		$options['per_page'] = 0;
		$options['page'] = 20;
		if(isset($data['order_num']) && !empty($data['order_num'])){
			$options['page'] = $data['order_num'];	
		}

		//多品多件默认60
		if($type == 3){
			$options['page'] = 60;
		}

		//$options['page'] = ($options['page'] >200) ? 200 : $options['page'];

		$options['where'] = $where;

		//按sku排序
		$options['order_by'] = 'p.orders_sku asc';

		$data = $this->orders_model->get_pick_order($options,true);

		return $data;

	}

	//没有包装完成的拣货单和已经包装完成的拣货单
	public function picking(){

		$options_text['title'] = '包装作业';

		$options_text['type'] = '3';//包装作业

		$statu = array(3,4);

		$this->pick_list($options_text,$statu);

	}

	//根据状态获取拣货单列表
	function pick_list($options_text = array(),$statu = ''){

		$key = $this->user_info->key;//用户组key
		
		$uid = $this->user_info->id;//登录用户id

		$status = $statu;//拣货单状态
		
		$string = '';
		
		
		$where = array();
		
		$like  = array();
		
		//搜索
		$search_data = $this->input->get_post('search');
		
		if(isset($search_data['channel_name']) && $channel_name = trim($search_data['channel_name'])){
			
			$like['channel_name'] = $channel_name;
			
			$string .= '&serach[channel_name]='.$channel_name;
			
		}
		
		if(isset($search_data['suppliers_id']) && $suppliers_id = $search_data['suppliers_id']){
			
			$where[$this->model->_table.'.suppliers_id'] = $suppliers_id;
			
			$string .= '&search[suppliers_id]='.$suppliers_id;
		}
		
		if($key == 'root'){//超级管理员
		
		}else if($key == 'manager'){//管理员
			
		}else{
			
			$where['pick_uid'] = $uid;
		}
		

		//正在包装的拣货单号
		$where3[$this->model->_table.'.status'] = $status[0];
		$where3[$this->model->_table.'.pick_uid'] = $uid;
		$options	= array(
			'where'		=> $where3,
			'like'		=> $like,
			'order'		=> "id desc",
		);
		$join[] = array('erp_slme_user u',"u.id={$this->model->_table}.uid");
		$options['select'] = array("{$this->model->_table}.*",'u.nickname');//正在包装的拣货单号
		$options['join'] = $join;
		$data_list = $this->model->getAll2array($options); //查询所有信息(正在拣货的单号)
		
		//包装完成的拣货单号
		$where4[$this->model->_table.'.status'] = $status[1];
		$where4[$this->model->_table.'.pick_uid'] = $uid;
		$option	= array(
			'where'		=> $where4,
			'like'		=> $like,
			'order'		=> "id desc",
		);
		$option['select'] = array("{$this->model->_table}.*",'u.nickname');//包装完成的拣货单号
		$option['join'] = $join;
		$result = $this->model->getAll2array($option); //查询所有信息(已经包装完成的拣货单号)
		foreach ($result as $key => $va) {
			$result[$key]['time'] = date_to_date($va['pick_start_time'],$va['pick_end_time']);
		}

		//数据处理
		foreach ($data_list as $key => $v) {
			//时长
			$data_list[$key]['time'] = date_to_date($v['pick_start_time'],$v['pick_end_time']);
			//订单进度，产品进度
			$orders_status = $this->pick_product_model->get_pick_order_status_count($v['id']);

			$un_pick_order_num = isset($orders_status[1]['order_num']) ? $orders_status[1]['order_num'] : 0;//等待包装的包裹数(订单数)

			$un_pick_product_num = isset($orders_status[1]['product_num']) ? $orders_status[1]['product_num'] : 0;//等待包装的商品品数

			$data_list[$key]['picked_order'] = $v['order_num'] - $un_pick_order_num;//已包装的包裹数=总得包裹数-等待包装的包裹数

			$data_list[$key]['picked_product'] = $v['num'] - $un_pick_product_num;
		}
		
		
		$c_url = 'order/pick_manage';

		
		$data = array(
		    'c_url'              => $c_url, 
		    'key'                => $key,
			'data_list'	         => $data_list,
			'result'			 => $result,
			'search'    		 => $search_data,
		    'type_text'          => $this->type_text,
		    'status_text'        => $this->status_text,
		    'options_text'        => $options_text
		); 
		
		$this->_template('admin/order/pick_list_status',$data); 
	}
	
    //扫入拣货单号
	public function scan_list(){

		$uid = $this->user_info->id;//登录用户id

		$key = $this->user_info->key;//用户组key

		$pick_id_arr = $this->input->get_post('pick_id');

		$p_arr = explode('_',$pick_id_arr);
		
		$pick_id = $p_arr[0];//拣货单id
		
		$basket_num = isset($p_arr[1]) ? $p_arr[1] : '';//篮子号

		if(empty($pick_id)){
			showmessage('非法请求');
		}

		//判断拣货单状态是否已完成
		$pick = $this->model->getOne(array('id'=>$pick_id),true);

		if(empty($pick)){
			showmessage('拣货单：'.$pick_id.' 不存在,请返回重新输入');
		}

		if($pick['status'] == 5){
			showmessage('拣货单：'.$pick_id.' 已标记发货,请返回输入新的拣货单','admin/order/pick_manage/picking');
		}

		if($pick['status'] == 4){
			showmessage('拣货单：'.$pick_id.' 已经包装完成,请返回输入新的拣货单','admin/order/pick_manage/picking');
		}

		if($pick['status'] == 1){
			showmessage('拣货单：'.$pick_id.' 状态为等待打印,请先打印拣货单');
		}

		//一个拣货单只能一个人包装
		/*
		if($pick['status'] == 3 && $pick['pick_uid'] != $uid){
			showmessage('拣货单：'.$pick_id.' 状态为正在包装,请选择其他拣货单进行包装');
		}

		if($pick['pick_uid'] != $uid && $pick['pick_uid'] > 0){
			showmessage('拣货单：'.$pick_id.' 拣货单包装人不是你,请选择其他拣货单进行包装');
		}
		*/
		$no_scan_num = 100;

		if($pick['status'] == 2){//更改拣货单的状态为正在包装

			$data = array();

			$data['status'] = 3;
			$data['pick_start_time'] = time();
			$data['pick_uid'] = $uid;

			$options = array();
			$options['where']['id'] = $pick_id;

			$tof_status = $this->model->update($data,$options);

			if(empty($tof_status)){
				showmessage('系统更改拣货单：'.$pick_id.' 状态为正在包装失败，请返回重新输入拣货单');
			}

		}

		$no_scan_num = 100;

		if($key =='manager'){
			//显示还未扫描订单数
			$no_all_num = $this->pick_product_model->getOne(array('select'=>'COUNT(DISTINCT orders_id) as num',
															'where'=>array('status'=>1,'pick_id'=>$pick_id)),											 
															true);
			$no_scan_num = $no_all_num['num'];
		}

		//显示当日已包装数
		$total = 0;
		/*
		$now_time = strtotime(date('Y-m-d'));
		$sql = "select count(DISTINCT orders_id) as total from erp_pick_product where ship_time>='".$now_time."' and ship_uid='".$uid."' and status=4";
		$now_total = $this->model->query_array($sql);
		if(!empty($now_total['total'])){
			$total = $now_total['total'];
		}
		*/
		//拣货单明细表
		//$list = $this->pick_product_model->get_product_by_pick_id($pick_id);
		
		//拣货单信息表
		//$pickInfo=$this->model->get_info_by_pick_id($pick_id);

		$data = array(
			'pick'            	 => $pick,
		    'type_text'          => $this->type_text,
		    'status_text'        => $this->status_text,
		    'no_scan_num'        => $no_scan_num,
		    'total'				 => $total,
		    'key'                => $key
		); 
		
		$template_name = 'pick_scan_list';

		$pick_product_info = array();
		if($pick['type'] == 3){//多品多件
			//$template_name = 'pick_scan_list_for_all_two';
			$template_name = 'pick_scan_list_mutil';
			//获取产品信息
			//$pick_product_info = $this->pick_product_model->get_pick_product_by_pick_id($pick['id']);
			//获取已包装的订单status=3
			$select = array('distinct(orders_id)');
			$where = array('status'=>4,'pick_id'=>$pick_id);
			$option = array(
			  'select' => $select,
			  'where'  => $where,
			  'order'  => 'scan_time'
			);
			$pick_product_info = $this->pick_product_model->getAll2array($option);
			
			$data['basket_num'] = $basket_num;
			//如果篮子号存在，改变该篮子号下的sku状态为3，并且扫描数==产品数
			if( isset($data['basket_num']) && !empty($data['basket_num']) ){
			  $this->change_status_by_basket_num($data['basket_num'],$pick_id);
			}
		}

		$data['pick_product_info']  = $pick_product_info;

		$this->_template('admin/order/'.$template_name,$data); 
		
	}
	
	//根据篮子号和拣货单号更改篮子下sku的状态为3，并且扫描数==产品数
	public function change_status_by_basket_num($basket_num,$pick_id){
		
	  if(!empty($basket_num)&&!empty($pick_id)){//都不为空才执行操作
   
	    $this->db->set('status',"3",false);
	    
	    $this->db->set('scan_num','product_num',false);

        $this->db->where('pick_id', $pick_id);
        
        $this->db->where('status <=', 3);
        
        $this->db->where('basket_num', $basket_num);

        $this->db->update('erp_pick_product');
        
	  }

	}

	//ajax 单个标记发货
	public function ajax_shipping_order(){

		$result = array();

		$result['status'] = 1; //1-标记发货失败,2-标记发货成功

		$result['info'] = '单个标记发货非法请求';

		$uid = $this->user_info->id;//登录用户id

		$pick_id	= (int)$this->input->get_post('pick_id');

		$orders_id	= (int)$this->input->get_post('orders_id');

		$sku	= trim($this->input->get_post('sku'));

		$warehouse	= (int)$this->input->get_post('warehouse');
		
		$type = (int)$this->input->get_post('type');
		
		if(empty($pick_id)){
			$result['info'] = '拣货单号为空，标记发货失败';
			echo json_encode($result);die;
		}

		if(empty($orders_id)){
			$result['info'] = '订单号为空，标记发货失败';
			echo json_encode($result);die;
		}

		if(empty($sku)){
			$result['info'] = 'SKU为空，标记发货失败';
			echo json_encode($result);die;
		}

		if(empty($warehouse)){
			$result['info'] = '仓库为空，标记发货失败';
			echo json_encode($result);die;
		}

		//查看订单的状态是否已经改变
		$order = $this->orders_model->getOne(array('erp_orders_id'=>$orders_id,'orders_is_join'=>'0'),true);

		if(empty($order)){
			$result['info'] = "订单：".$orders_id."被合并或拆分,请把SKU：【{$sku}】放回货架上。";
			$this->pick_product_model->update(array('status'=>'9','note'=>'订单被合并或拆分，请把SKU放回货架'),array('orders_id'=>$orders_id));
			echo json_encode($result);die;
		}

		//订单物流id
		$shipmentAutoMatched = $order['shipmentAutoMatched'];

		//订单状态不为已打印
		if($order['orders_status'] != '4'){
			//把拣货单该订单变为异常
			$this->pick_product_model->update(array('status'=>'9','note'=>'订单状态异常，请把SKU放回货架'),array('orders_id'=>$orders_id));
			$result['info'] = "订单：".$orders_id."状态异常,请把SKU：【{$sku}】放回货架上。";
			echo json_encode($result);die;
		}

		//根据order_id 查找拣货单
		$options = array();

		$options['select']=array($this->pick_product_model->_table.'.*','p.products_id');
		
		$join[]=array('erp_products_data p',"p.products_sku={$this->pick_product_model->_table}.product_sku");
		
		$options['join']=$join;
		
		$where = array();

		$where['orders_id'] = $orders_id;

		$where['pick_id'] = $pick_id;

		$where['product_warehouse_id'] = $warehouse;
		
		$options['where'] = $where;

		$sku_info = $this->pick_product_model->getAll2Array($options);

		if(empty($sku_info)){
			$result['info'] = "订单号{$orders_id}不在拣货单中，请把【{$sku}】放回货架";
			echo json_encode($result);die;
		}

		$tof_can_shipping = true;
		
		$scan_sku = array();

		$product_id = array();

		foreach ($sku_info as $k => $v) {
			if($type!=3){
				//状态判断
				if($v['status'] !=2){
					$result['info'] = '状态有误';
					$tof_can_shipping = false;
					break;
				}
			}

			//扫描数量判断
			if($v['product_num'] != $v['scan_num']){
				$result['info'] = '还有未扫描的';
				$tof_can_shipping = false;
				break;
			}

			//数据组装
			if(isset($scan_sku[$v['product_sku']])){
				$scan_sku[trim($v['product_sku'])] += $v['scan_num'];
			}else{
				$scan_sku[trim($v['product_sku'])] = $v['scan_num'];
			}

			$product_id[trim($v['product_sku'])] = $v['products_id'];
			
		}

		if(!$tof_can_shipping){
			echo json_encode($result);die;
		}

		//获取订单产品详情
		$order_sku_info = $this->orders_products_model->get_product_array($orders_id);

		if(empty($order_sku_info)){
			$result['info'] = "订单没有商品，请把【{$sku}】放回货架";
			echo json_encode($result);die;
		}

		foreach ($order_sku_info as $k => $v) {
			
			//比较sku
			$my_sku = trim($v['sku']);

			if(!array_key_exists($my_sku,$scan_sku)){
				$result['info'] = "商品信息不对，请把SKU放回货架";
				echo json_encode($result);die;
			}

			//比较数量
			if($v['num'] != $scan_sku[$my_sku]){
				$result['info'] = "商品数量不对，请把SKU放回货架";
				echo json_encode($result);die;
			}
		}

		//数据整理
		$data = array();

		$data['order_id'] = $orders_id;

		$data['pick_id'] = $pick_id;

		$data['uid'] = $uid;

		foreach ($scan_sku as $k => $v) {
			$data['sku'][$k]['sku'] = $k;
			$data['sku'][$k]['num'] = $v;
			$data['sku'][$k]['warehouse'] = $warehouse;
			$data['sku'][$k]['product_id'] = $product_id[$k];
			 
		}

		//开始标记发货
		$tof_ship = $this->orders_model->shipping_order_by_sku($data);

		if($tof_ship['status'] ==1){//发货成功,写发货日志
			
			//修改发货人
			$this->pick_product_model->update(array('ship_uid'=>$uid),array('orders_id'=>$orders_id));
			
			
			$result['status'] = 2;

			$log_data = array();
			$log_data['operateUser'] = $uid;
			$log_data['operateKey'] =  $orders_id;
			$log_data['operateText'] = "出库扣库存成功（拣货单号：{$pick_id}），订单状态变为已发货";
			$log_tof = $this->operate_log_model->add_order_operate_log($log_data);

			//写入物流发货统计
			$this->load->model('shipped_count_model');

			$now_day = date('Y-m-d');

			$uid = $this->user_info->id;

			$shipped_postoffice = $shipmentAutoMatched;

			$shipped_count = $this->shipped_count_model->getOne(array('shipped_date'=>$now_day,'user_id'=>$uid,'shipped_postoffice'=>$shipped_postoffice),true);

			$options = array();
			$where['shipped_date'] = $now_day;
			$where['user_id'] = $uid;
			$where['shipped_postoffice'] = $shipped_postoffice;
			
			//根据订单号计算，订单总重
			$product_weight = $this->orders_products_model->getProductSkuByOrderId($order['erp_orders_id'],$order['orders_warehouse_id']);

			$total_weight = 0;

			foreach ($product_weight as $k => $v) {
				$total_weight += $v['products_weight'];
			}

			if(empty($shipped_count)){
				$where['shipped_weight'] = $total_weight;
				$where['shipped_count'] = 1;
				$tof = $this->shipped_count_model->add($where);
			}else{
				$data = array();
				$data['shipped_count'] = $shipped_count['shipped_count']+1;
				$data['shipped_weight'] = $shipped_count['shipped_weight']+$total_weight;
				$tof = $this->shipped_count_model->update($data,$where);
			}

		}else{
			$result['info'] = $tof_ship['info'].", 请把{$sku}放回货架";
		}

		echo json_encode($result);die;
		
	}

    //标记发货逻辑处理和视图
	public function shipping_order($pickId="",$wareId=""){

		$uid = $this->user_info->id;//登录用户id

		$pick_id	= $this->input->get_post('pick_id')=='' ? $pickId : trim($this->input->get_post('pick_id'));

		$warehouse	= $this->input->get_post('warehouse')=='' ? $wareId : trim($this->input->get_post('warehouse'));
		
		$po['select']=array($this->pick_product_model->_table.'.*','p.products_id');
		
		$join[]=array('erp_products_data p',"p.products_sku={$this->pick_product_model->_table}.product_sku");
		
		$po['join']=$join;
		
		$po['where']=array($this->pick_product_model->_table.'.pick_id'=>$pick_id,'status'=>3,'p.product_warehouse_id'=>$warehouse);
		//找出所有已包装的
		$orders = $this->pick_product_model->getAll2Array($po);
		
		if(empty($orders)){
			$orders = array();
			//showmessage('拣货单：'.$pick_id.'没有需要发货的订单，请查看拣货单是否有已包装好的订单','admin/order/pick_manage/picking');
		}
		
		//数据整理
		$data = array();
		foreach ($orders as $k => $v) {
			$data[$v['orders_id']]['order_id'] = $v['orders_id'];
			$data[$v['orders_id']]['can_ship'] = true;
			$data[$v['orders_id']]['sku'][$v['product_sku']]['sku'] = $v['product_sku'];
			$data[$v['orders_id']]['sku'][$v['product_sku']]['product_id'] = $v['products_id'];
			if(isset($data[$v['orders_id']]['sku'][$v['product_sku']]['num'])){
				$data[$v['orders_id']]['sku'][$v['product_sku']]['num'] += $v['scan_num'];
			}else{
				$data[$v['orders_id']]['sku'][$v['product_sku']]['num'] = $v['scan_num'];
			}
			
			$data[$v['orders_id']]['sku'][$v['product_sku']]['warehouse'] = $warehouse;

		}

		$this->load->model('order/orders_products_model');

		$ship_result = array();
		
		$flag=0;//判断是否改变拣货单状态的标识（判断标准：数量与订单数量相等）
		
		//判断sku和数量是否一致
		foreach ($data as $k => $v) {
			
			$order_id = $v['order_id'];

			$ship_result[$order_id]['status'] = 0;

			//获取订单详情
			$order_info = $this->orders_products_model->get_product_array($v['order_id']);

			if(empty($order_info)){
				$data[$order_id]['can_ship'] = false;
				$ship_result[$order_id]['info'] = '订单：'.$order_id.'中没有商品';
			}

			$tof_num = true;
			$tof_sku = true;

			if($data[$order_id]['can_ship']){
				//比较产品和数量
				foreach ($order_info as $o_k => $o_v) {
					//比较sku
					if(!array_key_exists($o_v['sku'],$v['sku'])){
						$ship_result[$order_id]['info'] = '订单：'.$order_id.'中'.$o_v['sku'].'不存在';
						$tof_sku = false;
						break;
					}
					//比较sku 数量
					if($o_v['num'] != $v['sku'][$o_v['sku']]['num']){
						$ship_result[$order_id]['info'] = '订单：'.$order_id.'中'.$o_v['sku'].'数量不对';
						$tof_num = false;
						break;
					}
				}
			}

			if(!$tof_sku){
				$data[$order_id]['can_ship'] = false;
				
			}

			if(!$tof_num){
				$data[$order_id]['can_ship'] = false;
				
			}

			//开始标记发货
			if($data[$order_id]['can_ship']){
				$data[$order_id]['uid'] = $uid;
				$data[$order_id]['pick_id'] = $pick_id;
				$tof_ship = $this->orders_model->shipping_order_by_sku($data[$order_id]);
        
				if($tof_ship['status'] ==1){//发货成功,写发货日志
					$ship_result[$order_id]['status'] = 1;
					$ship_result[$order_id]['info'] = '发货成功';
					
					//修改发货人
					$this->pick_product_model->update(array('ship_uid'=>$uid),array('orders_id'=>$order_id));
					
					//写日志
					$log_data = array();
					$log_data['operateUser'] = $uid;
					$log_data['operateKey'] =  $order_id;
					$log_data['operateText'] = "出库扣库存成功（拣货单号：{$pick_id}），订单状态变为已发货";
					$log_tof = $this->operate_log_model->add_order_operate_log($log_data);

					$flag+=1;
					
				}else{
					$ship_result[$order_id]['status'] = 0;
					$ship_result[$order_id]['info'] = $tof_ship['info'];
					$this->pick_product_model->update(array('status'=>'9','note'=>$ship_result[$order_id]['info']),array('orders_id'=>$order_id));
				}

			}else{//拣货单详情表，状态变为异常
				$note = $ship_result[$order_id]['info'];
				$this->pick_product_model->update(array('status'=>'9','note'=>$note),array('orders_id'=>$order_id));
			}
		}
		//如果数量与订单数量相等，说明每个订单发货成功
		if($flag==count($data)){
		  //更改拣货单状态为5-已发货
		  $changData['status']=5;
		  $opt['where']=array('id'=>$pick_id);
		  $this->model->update($changData,$opt);
		}

		//找出该拣货单中所有的包裹
		$where = array();

		$where['pick_id'] = $pick_id;
	

		$optio['where'] = $where;

		$options['order'] = 'id desc';

	    $ordersinfo = $this->pick_product_model->getAll2Array($optio);
	    $returnData=array(
	     'ordersInfo'  => $ordersinfo,
	     'product_status'=> $this->product_status,
	     'ship_result' => $ship_result,
	    );
	    $this->template('admin/order/shipping_order',$returnData);
	  
	}


	public function do_scan(){
		
		$uid = $this->user_info->id;//登录用户id

		$result = array();

		$result['info'] = '非法请求';

		$result['status'] = 0;

		$result['can_print'] = false;

		$result['basket_num'] = 0;
		

		$pick_id	= (int)$this->input->get_post('pick_id');

		$type	= (int)$this->input->get_post('type');

		$sku	= trim($this->input->get_post('sku'));

		//SKU看是否有映射
		$sku = $this->old_sku_new_sku_model->replace_sku($sku);
		
		$last_sku = trim($this->input->get_post('last_sku'));

		//如果是单品多件，先查找是否有已扫描，且扫描数量少于应发数量的
		if($type == 2){
			$options = array();

			$options['where']['pick_id'] = $pick_id ;

			$options['where']['status'] = 2;

			$options['where']['scan_num < product_num'] = null;

			$options['where']['scan_uid'] = $uid;

			$sku_info = $this->pick_product_model->getOne($options,true);

			if(!empty($sku_info)){
				if(strtoupper($sku_info['product_sku']) != strtoupper($sku)){
					$result['info'] = "SKU有误,请再拿".($sku_info['product_num'] - $sku_info['scan_num'])." 个【".$sku_info['product_sku']."】来扫描";
					echo json_encode($result);die;
				}
			}
		}

		//查找订单
		$pick_sku = $this->pick_product_model->find_order_by_sku($pick_id,$sku);

		if(empty($pick_sku)){
			$result['info'] = "SKU：{$sku}有误,请检查SKU是否在拣货单：{$pick_id}中";
			echo json_encode($result);die;
		}
		
		$pick_package = $this->pick_product_model->get_pick_package($sku,$pick_sku['orders_id'],$pick_id);//订单状态有过更改 导致第二次拣货单生成包含第一个拣货单的数据  则第一个拣货单订单不进行出面单  

		if(!empty($pick_package)){
			$result['info'] = "SKU：{$sku}在另一个拣货单ID为：{$pick_package['pick_id']}生成,在此拣货单无法扫描出面单,原因可能是修改过订单状态！";
			echo json_encode($result);die;
		}

		//查看订单的状态是否已经改变
		$order = $this->orders_model->getOne(array('erp_orders_id'=>$pick_sku['orders_id'],'orders_is_join'=>'0'),true);

		if(empty($order)){
			$result['info'] = "订单：".$pick_sku['orders_id']."被合并或拆分,请把SKU：【{$sku}】放回货架上。";
			$this->pick_product_model->update(array('status'=>'9','note'=>'订单被合并或拆分，请把SKU放回货架'),array('orders_id'=>$pick_sku['orders_id']));
			echo json_encode($result);die;
		}

		//订单状态不为已打印
		if($order['orders_status'] != '4'){
			//把拣货单该订单变为异常
			$this->pick_product_model->update(array('status'=>'9','note'=>'订单状态异常，请把SKU放回货架'),array('orders_id'=>$pick_sku['orders_id']));
			$result['info'] = "订单：".$pick_sku['orders_id']."状态异常,请把SKU：【{$sku}】放回货架上。";
			echo json_encode($result);die;
		}

		//找到订单，改变商品为已扫描
		$data = array();
		
		if($pick_sku['status'] == 1){
			$data['status'] = 2;
		}
		
		$data['scan_uid'] = $uid;
		
		$data['scan_time'] = time();
		$data['scan_num'] = $pick_sku['scan_num'] + 1;

		$options = array();
		$options['where']['id'] = $pick_sku['id'];

		$tof_change = $this->pick_product_model->update($data,$options);

		if($tof_change){
			$result['status'] = 1;
			$result['orders_id'] = $pick_sku['orders_id'];
			$result['product_sku'] = $pick_sku['product_sku'];
			$result['scan_num'] = $data['scan_num'];
			$result['basket_num'] = $pick_sku['basket_num'];
			
			
			//是否可打印的标志
			$is_can_print = $this->pick_product_model->check_order_is_can_print($pick_sku['orders_id'],$pick_id);
			
			if(empty($is_can_print)){
				$result['can_print'] = true;
			}
			
			if($type == 3){
				if($last_sku!=$sku){//如果上一个sku和现在扫描的sku不一样
				//根据sku获取产品信息
					$productInfo = $this->products_data_model->getProductInfoBySku($sku);
					if(!empty($productInfo)){
					  $imgArr = explode('-||-',$productInfo['products_imgs']);//sku图片
					  $result['img_url'] = 'http://120.24.100.157:70/'.$imgArr[0];
					  $result['name_cn'] = $productInfo['products_name_cn'];
					}
					  $result['is_exit'] = 0;//表示跟上一个sku不一致
				}else{
					  $result['is_exit'] = 1;//表示跟上一个sku一致
				}
			}
			
			
			
		}else{
			$result['info'] = "系统出错，增加SKU：{$sku}扫描数量出错，请重新操作";
			
		}

		echo json_encode($result);die;

	}


    //标记为已包装
	function change_status_printed(){

		$uid = $this->user_info->id;//登录用户id

		$orders_id	= $this->input->get_post('orders_id');
 
		$pick_id = $this->input->get_post('pick_id');

		$result = array();

		$result['info'] = '标记订单为已包装失败';

		$result['status'] = 0;

		$tof = $this->pick_product_model->change_status_is_printed($orders_id);


		if($tof){
			$result['status'] = 1;
			//查看是否还有未包装的包裹,如果没有，更改拣货单状态为包装完成
			//$pof=$this->pick_product_model->check_order_is_no_packaging($pick_id);
			//$result['pof']=$pof;
		}

		echo json_encode($result);die;

	}

	function show_has_scan_list(){

		$uid = $this->user_info->id;//登录用户id

		$pick_id	= $this->input->get_post('pick_id');

		$warehouse	= $this->input->get_post('warehouse');

		$status = 2;

		$data_list = $this->pick_product_model->get_all_by_sattus($pick_id,$warehouse,$status,$uid);

		//数据整理，获取订单备注，注意事项，包装方式，等
		$data_list = $this->get_order_remark_and_order($data_list);
		
		$data = array(
			'data_list'	         => $data_list,
			'arrAdapter'		 => $this->arrAdapter,
		); 
		
		$this->template('admin/order/pick_product_list_scan',$data); 

	}


	function show_has_printed_list(){

		$uid = $this->user_info->id;//登录用户id

		$pick_id	= $this->input->get_post('pick_id');

		$warehouse	= $this->input->get_post('warehouse');

		$status = 4;

		$data_list = $this->pick_product_model->get_all_by_sattus($pick_id,$warehouse,$status,$uid);

		//数据整理，获取订单备注，注意事项，包装方式，等
		$data_list = $this->get_order_remark_and_order($data_list);

		$data = array(
			'data_list'	         => $data_list,
			'arrAdapter'		 => $this->arrAdapter,
		); 
		
		$this->template('admin/order/pick_product_list_printed',$data); 

	}
	
	/**
	 * 结束本次包装作业
	 * 当传参数type==3的时候，不改变拣货单状态，订单改变为已退回，拣货单详情中所述订单号的改为恢复为已通过并添加备注
	 * 不传参数的时候，拣货单类型为单品的时候，改变拣货单状态，订单改变为已退回，拣货单详情中所述订单号的改为恢复为已通过并添加备注
	 */
	function endPicking(){
		
	  $uid = $this->user_info->id;//登录用户id

	  $pick_id=$this->input->get_post('pick_id');
	  
	  $warehouse=$this->input->get_post('warehouse');
	  
	  $type = $this->input->get_post('type')=='' ? '' : $this->input->get_post('type');
	  
	  if( empty($type) && $type==''){//更改该拣货单状态为已发货
	     
	     $results=$this->model->change_pickStatus_is_deliver($pick_id);
	     
	  }else{//不需要改变拣货单的状态
	  	
	     $results = 0;
	     
	  }

	     //$ship=$this->shipping_order($pick_id,$warehouse);//标记发货

	     //查找异常和未扫描的订单
		  $where['pick_id'] = $pick_id;
		  //$where['status'] = 1;//标记发货时，没有扫描过的订单才回退为已通过
		  $where_in['status']=array(1,2,3);//1-等待包装,2-已扫描,3-已包装
		  $option=array(
		    'where'  => $where,
		  	'where_in'=> $where_in
		  );
		  $data=$this->pick_product_model->getAll2array($option);

		  if(!empty($data)){

			  foreach($data as $v){

			  	  //事务开始
				  $this->db->trans_begin();
				  
			   	    //订单表中的已打印状态变为已通过
				    $updata2['orders_status']=3;
				    $updata2['orders_is_backorder']='1';
				    $updata2['orders_print_time'] = null;
				    $wheres['erp_orders_id']=$v['orders_id'];
				    $wheres['orders_status']=4;
				     $opt=array(
				      'where' => $wheres,
				    );
				    $result2=false;
				    $result=false;
				    
				    $result2=$this->orders_model->update($updata2,$opt);

				    if($result2){//订单状态成功改变以后，改变拣货单详情表里的状态为已通过并添加注释

				    	$updata['result']='订单退回，状态变为已通过';

				    	$where_id = array();
				    	
				    	$where_id['orders_id'] = $v['orders_id'];
				    	
				    	$where_id['pick_id'] = $v['pick_id'];
				    	
				    	$options['where'] = $where_id;
				    	
					    if($v['status']!=9){//如果包裹状态不是异常的话
						  	$updata['status']=8;
				   	    }

				   	    $result=$this->pick_product_model->update($updata,$options);//拣货单中的包裹
				    }
				    
				  if( !empty($type) && $type==3 ){
				  	
					  if( $this->db->trans_status() === TRUE && $result && $result2 ){//操作成功，不需要改变拣货单的状态情况下
					  	
							$this->db->trans_commit();//事务结束
							
							//加入日志
					        $logData= array(
						      				'operateUser' =>$uid,
						      				'operateTime '=>date('Y-m-d H:i:s',time()),
					                        'operateType' => 'update',
					                        'operateMod' => 'ordersManage',
					                        'operateKey' => $v['orders_id'],
					                        'operateText' => '货找面单退回,订单状态变为已通过,拣货单号'.$v['pick_id'], 
					                    	);
					        $insertLog=$this->operate_log_model->add($logData);
	
					  }else{//操作失败
							
							$this->db->trans_rollback();
							
					  }
				  }else{
				  	
				  	 if( $this->db->trans_status() === TRUE && $result && $result2 && $results ){//操作成功，需要改变拣货单的状态的情况下
						
							$this->db->trans_commit();//事务结束
							
							//加入日志
					        $logData= array(
						      				'operateUser' =>$uid,
						      				'operateTime '=>date('Y-m-d H:i:s',time()),
					                        'operateType' => 'update',
					                        'operateMod' => 'ordersManage',
					                        'operateKey' => $v['orders_id'],
					                        'operateText' => '货找面单退回,订单状态变为已通过,拣货单号'.$v['pick_id'], 
					                    	);
					        $insertLog=$this->operate_log_model->add($logData);
	
					  }else{//操作失败
							
							$this->db->trans_rollback();
							
					  }
				  }
			      
			  }
		  }
	  
	}
	
	//打印拣货单
	function printPickOrder(){

	  $uid = $this->user_info->id;//登录用户id

	  $username=$this->user_info->nickname;//登录用户名,打单员
	  
	  $pick_id	= $this->input->get_post('pick_id');
	  
	  $status = $this->input->get_post('status');//包裹状态
	  
	  //获取该拣货单的详细信息
	  $options['select'] = array("{$this->model->_table}.*",'u.warehouseTitle');
	  $join[] = array('sangelfine_warehouse u',"u.warehouseID={$this->model->_table}.warehouse");
	  $options['join']=$join;
	  $options['where']=array('id' => $pick_id);
	  $pickInfo=$this->model->getOne($options,true);

	  $print_num=$pickInfo['print_num']+1;//该拣货单新的打印次数
	  //更改拣货单状态和打印次数,包装人
	  $data=array(
	    'status' => 2,
	  	'print_num'  =>$print_num,
	  );
	  $option['where']=array('id' => $pick_id,'status'=>1);
	  $pickResult=$this->model->update($data,$option);

	  //在拣货单打印表中添加打印记录
	  $dataPrint=array(
	    'pick_id' => $pick_id,
	  	'print_time'=>time(),
	  	'print_uid'	 => $uid,
	  );
	  $printResult=$this->pick_print_model->add($dataPrint);

	  //组装拣货单产品的数据
	  $optio['select'] = array($this->pick_product_model->_table.".*",'p.products_location','p.products_warring_string','p.products_name_cn');
	  $joins[] = array('erp_products_data p',"p.products_sku={$this->pick_product_model->_table}.product_sku");
	  $optio['join']=$joins;
	  if(isset($status)&&!empty($status)){
	    $optio['where']=array($this->pick_product_model->_table.'.pick_id' => $pick_id,$this->pick_product_model->_table.'.status'=>$status,'p.product_warehouse_id'=>$pickInfo['warehouse']);
	  }else{
	    $optio['where']=array($this->pick_product_model->_table.'.pick_id' => $pick_id,'p.product_warehouse_id'=>$pickInfo['warehouse']);
	  }
	  
	  $optio['order']='p.products_location asc';
	  $productInfo=$this->pick_product_model->getAll2array($optio);

	  //去掉重复的sku并数量加一组成新的数组
	  $newProduct=array();
	  foreach($productInfo as $key => $product){
		if(isset($newProduct[$product['product_sku']])){
		  $newProduct[$product['product_sku']]['product_num']+=$product['product_num'];

		}else{
		  $newProduct[$product['product_sku']]=$product;
		}
	  }

	 //获取重组后所有的产品数
	 $total=0;
	 foreach($newProduct as $val){
	   $total+=$val['product_num'];
	 }
	  
	  //根据拣货单中的uid获取用户名
	  $op=array(
	    'select'=>array('user_name'),
	  	'where' =>array('id'=>$pickInfo['uid']),
	  );	
	  $create_username=$this->slme_user_model->getOne($op,true);
	  
	  if(strpos($pickInfo['shipment_id'],',')){
	  	$shipmentTitle['shipmentTitle'] = "混合渠道(".$pickInfo['shipment_id'].")";
	  }else{
		  //根据拣货单中的物流id获取物流渠道名
		  $o=array(
		    'where'  => array('shipmentID' => $pickInfo['shipment_id']),
		  );
		  $shipmentTitle=$this->shipment_model->getOne($o,true);
	  }

	  $shipment_id_array = explode(',', $pickInfo['shipment_id']);
	  $print_template = $this->shipment_model->get_one_get_template($shipment_id_array[0]);
	  $pickInfo['template_size'] = $print_template['page_size'];

	  //
	  if($pickInfo['status'] == 1){
	  	$i = 1;
	  	$page_num = 1;
	  	foreach ($newProduct as $k => $v) {

	  		if($i/(33*$page_num) == 1){
	  			$page_num++;
	  		}
	  		
	  		$data = array();
	  		$data['page_num'] = $pickInfo['id'].'-'.$page_num;

	  		$options = array();
	  		$options['where']['product_sku'] = $v['product_sku'];
	  		$options['where']['pick_id'] = $pickInfo['id'];

	  		$i++;
	  		$tof = $this->pick_product_model->update($data,$options);
	  	}  	
	  }
	  
	  //映射SKU处理
	  $old_sku_new_sku = $this->old_sku_new_sku_model->get_all_key_old_sku();
	  foreach ($newProduct as $k => $v) {
	  	if(isset($old_sku_new_sku[$v['product_sku']])){
	  		$newProduct[$k]['product_sku'] = $old_sku_new_sku[$v['product_sku']];
	  	}
	  }
	  //
	  if($newProduct){   //排除已撤单的订单(不进行打印)
		  foreach($newProduct as $k=>$v){
			  if($v['orders_id']){
				  $ret = $this->model->get_orders_over($v['orders_id']);
				  if($ret){
					  unset($newProduct[$k]);   //已撤单订单  则不进行打印 释放
				  }
			  }
		  }
	  }
	  $resultData=array(
	    'pickInfo'=>$pickInfo,
	  	'type_text'=>$this->type_text,//拣货单类型
	  	'print_time'=>$dataPrint['print_time'],//打印时间
	  	'print_username'=>$username,//打单员
	  	'productInfo'=>$newProduct,//产品sku数据详情
	  	'create_username'=>$create_username['user_name'],//建单人
	    'shipmentTitle' => $shipmentTitle,//物流渠道名
	    'total_num' =>$total,//重组后的sku总数
	  );

	  $this->template('admin/order/createPickOrder',$resultData);
	}

	//查看拣货单
	function pickView(){
	  $pick_id	= $this->input->get_post('pick_id');
	  //和erp_shipment表联合查询获得erp_pick表中所有数据和物流名称
	  $options['select'] = array("{$this->model->_table}.*",'s.shipmentTitle');
	  $join[] = array('erp_shipment s',"s.shipmentID={$this->model->_table}.shipment_id");
	  $options['join']=$join;
	  $options['where']=array('id' => $pick_id);
	  $picksInfo=$this->model->getOne($options,true);
	  //根据拣货单中的uid获取用户名
	  $op=array(
	    'select'=>array('user_name'),
	  	'where' =>array('id'=>$picksInfo['uid']),
	  );	
	  $username=$this->slme_user_model->getOne($op,true);
	  //获取  1-等待包装,2-已扫描,3-已包装，4-已发货，9异常这几个状态的包裹数
	  $sql1="select count(orders_id) as num  from erp_pick_product where pick_id=$pick_id and status=1";
	  $sql2="select count(orders_id) as num  from erp_pick_product where pick_id=$pick_id and status=2";
	  $sql3="select count(distinct(orders_id)) as num  from erp_pick_product where pick_id=$pick_id and status=3";
	  $sql4="select count(distinct(orders_id)) as num  from erp_pick_product where pick_id=$pick_id and status=4";
	  $sql9="select count(orders_id) as num  from erp_pick_product where pick_id=$pick_id and status=9";
	  $statusProduct1=$this->pick_product_model->query_array($sql1);
	  $statusProduct2=$this->pick_product_model->query_array($sql2);
	  $statusProduct3=$this->pick_product_model->query_array($sql3);
	  $statusProduct4=$this->pick_product_model->query_array($sql4);
	  $statusProduct9=$this->pick_product_model->query_array($sql9);
	  $returnData=array(
	    'pickInfo'=>$picksInfo,
	  	'type_text'=>$this->type_text,//拣货单类型
	  	'status_text'=>$this->status_text,//拣货单状态
	  	'username'=>$username['user_name'],//拣货单创建人
	  	'status1'=>$statusProduct1['num'],//等待包装数
	    'status2'=>$statusProduct2['num'],//已扫描数
	  	'status3'=>isset($statusProduct3['num'])?$statusProduct3['num']:0,//已包装数
	  	'status4'=>isset($statusProduct4['num'])?$statusProduct4['num']:0,//已发货数
	  	'status9'=>$statusProduct9['num'],//异常数
	  );
	  $this->_template('admin/order/pick_view',$returnData);
	}
	
	//显示对应状态的包裹详情
	function packgetDetail(){
	  $pick_id=$this->input->get_post('pick_id');//拣货单id
	  $status=$this->input->get_post('status');//包裹状态，1-等待包装,2已扫描,3-已包装，4-已发货，5-已完成，8-恢复为已通过,9异常  0-创建包裹数
	  //根据筛选条件提取所需的数据
	  if($status==0){//status==0的话表示创建的包裹数，不用加状态筛选
	    $option['where']=array(
	     'pick_id' => $pick_id,
	    );
	  }else{
	    $option['where']=array(
	      'pick_id' => $pick_id,
	  	  'status'  => $status,
	    );
	  }
	  if($status==2){//2已扫描,按扫描时间倒序
	    $option['order_by'] = 'scan_time desc';
	  }
	  if($status==3){//3-已包装,按打印面单时间倒序
	    $option['order_by'] = 'print_time desc';
	  }
	  if($status==4){//4-已发货,按标记发货时间倒序
	    $option['order_by'] = 'ship_time desc';
	  }
	  
	  $result=$this->pick_product_model->getAll2array($option);
  
	  //已扫描状态的订单号不需要合并，其余合并订单号
	 $newResult=array();//重组数组并获取产品中文名称
	 foreach($result as $key => $v){
	 	$name=$this->products_data_model->getProductCnBySku($v['product_sku']);
	 	if($status==2){//包裹状态是已扫描的
		  $newResult[$v['orders_id']]=$v;
		  $newResult[$v['orders_id']]['product_name_cn']=$name['products_name_cn'];
	 	}else{//包裹处于其它状态
	 	  if($key>0){
	 	    if(!empty($newResult[$v['orders_id']])){//如果有重复的订单号，合并订单号
	 	      $num=count($newResult[$v['orders_id']]);
	 	      $newResult[$v['orders_id']][$num]=$v;
	 	      if(isset($name['products_name_cn'])){
	 	        $newResult[$v['orders_id']][$num]['product_name_cn']=$name['products_name_cn'];
	 	      }
	 	    }else{
	 	      $newResult[$v['orders_id']][0]=$v;
	 	      if(isset($name['products_name_cn'])){
			    $newResult[$v['orders_id']][0]['product_name_cn']=$name['products_name_cn'];
	 	      }
	 	    }
	 	  }else{
	 	    $newResult[$v['orders_id']][0]=$v;
	 	    if(isset($name['products_name_cn'])){
		      $newResult[$v['orders_id']][0]['product_name_cn']=$name['products_name_cn'];
	 	    }
	 	  }
	 	  
	 	} 
	 }

     //如果该拣货单已发货，根据拣货单号获取发货时间
     if($status==4){
      $select=array('ship_time');
       $where=array(
       		'pick_id'=> $pick_id,
       		'status' => $status,
       );
 	   $op=array(
 	     'select' => $select,
 	     'where'  => $where,
 	   );
       $ship_time=$this->pick_product_model->getOne($op,true);
     }
	   $returnData=array(
	    'prodcut' => $newResult,
	   	'status'  => $status,//包裹状态
	   	'product_status'=>$this->product_status,
	    'ship_time'=>isset($ship_time['ship_time']) ? $ship_time['ship_time'] : '',
	    );
	  $this->template('admin/order/packgetDetail',$returnData);
	}

	//数据整理，获取订单备注，注意事项，包装方式，等
	public function get_order_remark_and_order($data_list = array()){

		$this->load->model('products/pack_method_model');

		$this->load->model('order/orders_model');

		foreach($data_list as $k => $v){

			$data_list[$k]['pack_name'] = '';

			$data_list[$k]['remark'] = '';

			$data_list[$k]['adapter']=0;

			if($k <=2){
				//包装方式
				$data_list[$k]['pack_name'] = $this->pack_method_model->get_pack_name($v['pack_method']);

				//订单留言
				$data_list[$k]['remark'] = $this->orders_model->get_order_remark($v['orders_id']);

				//处理产品转规头  
				if($v['products_with_adapter']){
				  $adapters=$this->country_model->getAdapterByOrderId($v['orders_id']);
				  $data_list[$k]['adapter']=$adapters['adapter_spec'];
				}else{
				   $data_list[$k]['adapter']=0;
				}

			}

			//图片
			$img[0] = '';
			if(!empty($v['products_imgs'])){
				$img = explode('-||-', $v['products_imgs']);
			}

			$data_list[$k]['img'] = '';

			if(!empty($img[0])){
				$data_list[$k]['img'] = 'http://120.24.100.157:70/'.$img[0];
			}

		}

		return $data_list;
	}

	//多品多件-显示篮子
	function show_has_scan_one(){

		$pick_id	= $this->input->get_post('pick_id');

		$data = $this->pick_product_model->get_pick_product_by_pick_id($pick_id);

		$str = '';

		if($data){
			foreach ($data as $k => $v) {
				
				$bg_class = $v['shiped'] == 1 ? 'panel-success' : 'panel-primary';
				$print_url = "PrintOneURL('".admin_base_url('print/order_print/orderPrint?id=').$v['sku_info'][0]['orders_id']."')";
				$str .='<div class="col-sm-4">
				        <div class="panel '.$bg_class.'">
				            <div class="panel-heading">
				                <h1 class="panel-title text-center"><span class="font-18">'.$k.'号篮子</span><span>--'.$v['sku_info'][0]['orders_id'].'</span><span onclick="'.$print_url.'">--打印</span></h1>
				            </div>
				            <div class="panel-body">
				                <table class="table table-hover table-condensed">
				                  <thead>
				                    <tr>
				                      <th>SKU</th>
				                      <th>应发</th>
				                      <th>已扫</th>
				                    </tr>
				                  </thead>
				                  <tbody>';

				                  foreach ($v['sku_info'] as $sku_k => $sku) {
				                  	    //如果已扫描数量==应发数量，背景颜色变绿色
				                  		$bg_tr_class = ($sku['product_num'] == $sku['scan_num'])? 'success' : '';
				                  		$str .='<tr class="'.$bg_tr_class.'">
							                      <td>'.$sku['product_sku'].'</td>
							                      <td>'.$sku['product_num'].'</td>
							                      <td>'.$sku['scan_num'].'</td>
							                    </tr>';
				                  	}	

				$str .= '
				                  </tbody>
				                </table>
				            </div>
				        </div>
				    </div>';
			}
		}
		echo $str;
	}
	
	//多品多件显示界面---zeng
	function show_has_scan_ones(){
		$pick_id	= $this->input->get_post('pick_id');

		$data = $this->pick_product_model->get_pick_product_by_pick_id($pick_id);
		$str = '';
		$js = '';

		if($data){
		  $str.='<div class="row">';
		  foreach($data as $k => $v){
		  	
		  	$bg_class = $v['shiped'] == 1 ? 'label-success' : 'label-danger';
		  	$print_url = "PrintOneURL('".admin_base_url('print/order_print/orderPrint?id=').$v['sku_info'][0]['orders_id']."')";
		  	
		    // $str.='<div class="col-sm-2 baskets '.$bg_class.'"  >
			   //       <span class="bask_detail" onclick="showBasketInfo('.$k.','.$v['sku_info'][0]['orders_id'].');">'.$k.'号</span>--<span onclick="'.$print_url.'" class="print">打印</span>
			   //     </div>';
		    $str.='<div class="col-sm-2 baskets '.$bg_class.'"  >
			         <span class="bask_detail" onclick="showBasketInfo('.$k.','.$v['sku_info'][0]['orders_id'].');">'.$k.'号</span>
			       </div>';
		    
		    $js .='<script>';
		    if($v['shiped']==1){
		      $js .='Light.close('.$k.');';
		      $js .='Light.openGreen('.$k.');';
		    }
		    $js .='</script>';
		    
		  }
		  
		  $str.='</div>';
		
		}
		
		echo $str.$js;
	}
	//根据篮子号和拣货单号获取篮子信息
	public function getPickInfoByBaskBum(){
	  $baskNum = $this->input->get_post('bn');
	  $pick_id = $this->input->get_post('pick_id');
	  $option = array();
	  $where = array(
	    'basket_num' => $baskNum,
	    'pick_id'  => $pick_id
	  );
	  $option['where'] = $where;
	  $basketInfo = $this->pick_product_model->getAll2array($option);

	  $data = array(
	    'basketInfo' => $basketInfo
	  );
	  
	  $this->template('admin/order/show_basket_info',$data);
	}
	
	//显示订单中没有扫描全的的包裹
	public function showErrorPackget(){
	  $pick_id = $this->input->get_post('pick_id');
	  $option = array();
	  
	  //组装拣货单产品的数据
	  $option['select'] = array($this->pick_product_model->_table.".*",'p.products_location');
	  $joins[] = array('erp_products_data p',"p.products_sku={$this->pick_product_model->_table}.product_sku");
	  $option['join']=$joins;
	  $option['where']=array(
	  		$this->pick_product_model->_table.'.status <=' => 2,
	  		$this->pick_product_model->_table.'.product_num > '.$this->pick_product_model->_table.'.scan_num'=>null,
	  		$this->pick_product_model->_table.'.pick_id' => $pick_id
	  );

	  $data = $this->pick_product_model->getAll2array($option);

	  $data_list = array();
	  
	  foreach($data as $v){
	    if(isset($data_list[$v['product_sku']])){
	      $data_list[$v['product_sku']]['amount'] += $v['product_num']-$v['scan_num'];
	    }else{
	      $data_list[$v['product_sku']]['amount'] = $v['product_num']-$v['scan_num'];
	      $data_list[$v['product_sku']]['location'] = $v['products_location'];
	    }
	  }

	  $newData = array(
	    'data' => $data_list
	  );
	  $this->template('admin/order/show_no_scan',$newData);

	}
	

	//扫描sku获取订单商品信息
	public function ajax_get_orders__info($pick_id,$sku,$array = array(),$type = 1){

		$result = array();

		$result['order_id'] = '';

		$result['sku_info'] = '';

		$result['type'] = $type;

		//根据sku查找订单
		$options = array();

		$where = array();

		//单品单件，单品多件
		if($type == 1 || $type ==2){
			$where['status'] = 1;
		}

		//多品多件
		if($type == 3){
			$where['status'] = 3;
		}

		$where['orders_is_join'] = 0;//订单为不拆分

		$where['orders_status'] = 4; //订单状态为已打印

		$where['pick_id'] = $pick_id;

		$where['product_sku'] = $sku;

		$options['where'] = $where;

		$joins[] = array('erp_orders o',"o.erp_orders_id={$this->pick_product_model->_table}.orders_id");

		$options['join'] = $joins;

		$options['order_by'] = 'orders_paid_time asc';

		$data = array();
		
		if($type == 1 || $type ==2){
			$one_data = $this->pick_product_model->getOne($options);
		}

		if($type == 3){

			$where_in = array();

			if(isset($array['ids']) && !empty($array['ids'])){
				$where_in['orders_id'] = explode(',', $array['ids']);
			}

			
		} 
		


	}
	
	/**
	 * 通过ajax获取已经包装的订单
	 */
	public function ajax_get_shipped_order(){
		$uid = $this->user_info->id;//登录用户id
		$pick_id = $this->input->get_post('pick_id');
	    $select = array('distinct(orders_id)');
		$where = array('status'=>4,'pick_id'=>$pick_id,'ship_uid'=>$uid);
		$option = array(
			 'select' => $select,
			 'where'  => $where,
			 'order'  => 'ship_time desc'
		);
		$pick_product_info = $this->pick_product_model->getAll2array($option);
        $string = '';
		foreach($pick_product_info as $k=>$v){
		 $string.='
		   <tr>
		    <td>
		      <a class="orderInfo" data-id="'.$v['orders_id'].'" style="cursor:pointer;">
		         '.$v['orders_id'].'
		      </a>
		    </td>
		    <td>
		     '; 
		    if($k<3){
		      $string .='
			      <button class="btn btn-success btn-xs print" data-id="'.$v['orders_id'].'">
			        	 打印
			       </button>
			       <a href="/admin/order/pick_manage/check_order_product_info?orders_id='.$v['orders_id'].'" target="_blank">
			 			<button class="btn btn-success btn-xs check_info" data-id="'.$v['orders_id'].'">
				        	 查看包装方式
				    	</button>
			    	</a>
		      ';
		    } 
		 $string.='</td></tr>';
		}
		echo json_encode($string);
	}

	
	/**
	 * 根据订单号获取该订单产品的详细信息
	 */
	public function check_order_product_info(){
	  $orderID = $this->input->get_post('orders_id');
	  $ordersInfo = $this->orders_model->getOrderInfoByID($orderID);

	  //获取订单产品表详情
	  $result = $this->orders_products_model->getProductSkuByOrderId($ordersInfo['erp_orders_id'],$ordersInfo['orders_warehouse_id']);
	
	  foreach($result as $k => $va){
	      	
		 $newproductInfo[$k] = $va;
		  	
	     //获取要显示的图片，显示第一张
	     $imgs_arr = explode('-||-',$va['products_imgs']);
	     $newproductInfo[$k]['showImg'] = 'http://120.24.100.157:70/'.$imgs_arr[0];
	        
	     //根据包装id号获取包装方式
	     $packInfo = $this->pack_method_model->getPackInfo($va['pack_method']);
	     $newproductInfo[$k]['packArr'] = $packInfo;
	        
	     //根据订单 sku获得是否需要转接头以及转接头规格
	     $adpter = $this->country_model->getAdapterByOrderId($ordersInfo['erp_orders_id']);

	     //判断各个sku是否需要转接
	     if($va['products_with_adapter']>0){
	          $adpters = $adpter['adapter_spec'] ? $adpter['adapter_spec'] : 1;
	          $newproductInfo[$k]['adapter'] = $this->arrAdapter[$adpters];
	     }else{
	          $newproductInfo[$k]['adapter'] = '';
	     }
	  }

	  $data = array(
	    'ordersInfo'  => $ordersInfo,
	    'productsInfo'=> $newproductInfo
	  );
	  $this->_template('admin/order/check_order_product_info',$data);
	}
	
	/**
	 * 根据sku和仓库id获取包装方式
	 */
	public function ajax_get_pack_method(){
		$sku = $this->input->get_post('sku');
		//SKU看是否有映射
		$sku = $this->old_sku_new_sku_model->replace_sku($sku);
		
		$warehouse = $this->input->get_post('warehouse');
		$option = array();
		$select = array();
		$where = array();
		$select = array('products_sku','pack_method');
		$where = array(
		   'products_sku' => $sku,
		   'product_warehouse_id' => $warehouse
		);
		$option = array(
		  'select' => $select,
		  'where'  => $where
		);
		$result = $this->products_data_model->getOne($option,true);
		//根据包装id号获取包装方式
	    $packInfo = $this->pack_method_model->getPackInfo($result['pack_method']);
		echo json_encode($packInfo['title']);
	}

	/**
	 * 更加订单号和拣货单号获取该订单详情,是已包装且已经发货的订单
	 */
	public function getOrderInfo(){
	  $orderID = $this->input->get_post('orderID');
	  $pick_id = $this->input->get_post('pick_id');
	  $status = 4;
	  $option = array();
	  $where = array();
	  $where = array('orders_id'=>$orderID,'pick_id'=> $pick_id,'status'=>$status);
	  $option['where'] = $where;
	  $data['ordersInfo'] = $this->pick_product_model->getAll2array($option);
	  $this->template('admin/order/order_scan_info',$data);
	  
	}
	
	/**
	 * 显示订单配货
	 */
	public function multi_order_deal(){
	   $options_text['title'] = '包装作业';

		$options_text['type'] = '3';//包装作业

		$statu = array(3,4);

		$this->pick_list_mutil($options_text,$statu);
	}
	/**
	 * 显示订单配货
	 * 根据状态获取拣货单列表
	 */
	function pick_list_mutil($options_text = array(),$statu = ''){

		$key = $this->user_info->key;//用户组key
		
		$uid = $this->user_info->id;//登录用户id

		$status = $statu;//拣货单状态
		
		$string = '';
		
		
		$where = array();
		
		$like  = array();
		
		//搜索
		$search_data = $this->input->get_post('search');
		
		if(isset($search_data['channel_name']) && $channel_name = trim($search_data['channel_name'])){
			
			$like['channel_name'] = $channel_name;
			
			$string .= '&serach[channel_name]='.$channel_name;
			
		}
		
		if(isset($search_data['suppliers_id']) && $suppliers_id = $search_data['suppliers_id']){
			
			$where[$this->model->_table.'.suppliers_id'] = $suppliers_id;
			
			$string .= '&search[suppliers_id]='.$suppliers_id;
		}
		
		if($key == 'root'){//超级管理员
		
		}else if($key == 'manager'){//管理员
			
		}else{
			
			$where['pick_uid'] = $uid;
		}
		

		//正在包装的拣货单号
		$where3[$this->model->_table.'.status'] = $status[0];
		$where3[$this->model->_table.'.pick_uid'] = $uid;
		$options	= array(
			'where'		=> $where3,
			'like'		=> $like,
			'order'		=> "id desc",
		);
		$join[] = array('erp_slme_user u',"u.id={$this->model->_table}.uid");
		$options['select'] = array("{$this->model->_table}.*",'u.nickname');//正在包装的拣货单号
		$options['join'] = $join;
		$data_list = $this->model->getAll2array($options); //查询所有信息(正在拣货的单号)
		
		//包装完成的拣货单号
		$where4[$this->model->_table.'.status'] = $status[1];
		$where4[$this->model->_table.'.pick_uid'] = $uid;
		$option	= array(
			'where'		=> $where4,
			'like'		=> $like,
			'order'		=> "id desc",
		);
		$option['select'] = array("{$this->model->_table}.*",'u.nickname');//包装完成的拣货单号
		$option['join'] = $join;
		$result = $this->model->getAll2array($option); //查询所有信息(已经包装完成的拣货单号)
		foreach ($result as $key => $va) {
			$result[$key]['time'] = date_to_date($va['pick_start_time'],$va['pick_end_time']);
		}

		//数据处理
		foreach ($data_list as $key => $v) {
			//时长
			$data_list[$key]['time'] = date_to_date($v['pick_start_time'],$v['pick_end_time']);
			//订单进度，产品进度
			$orders_status = $this->pick_product_model->get_pick_order_status_count($v['id']);

			$un_pick_order_num = isset($orders_status[1]['order_num']) ? $orders_status[1]['order_num'] : 0;//等待包装的包裹数(订单数)

			$un_pick_product_num = isset($orders_status[1]['product_num']) ? $orders_status[1]['product_num'] : 0;//等待包装的商品品数

			$data_list[$key]['picked_order'] = $v['order_num'] - $un_pick_order_num;//已包装的包裹数=总得包裹数-等待包装的包裹数

			$data_list[$key]['picked_product'] = $v['num'] - $un_pick_product_num;
		}
		
		
		$c_url = 'order/pick_manage';

		
		$data = array(
		    'c_url'              => $c_url, 
		    'key'                => $key,
			'data_list'	         => $data_list,
			'result'			 => $result,
			'search'    		 => $search_data,
		    'type_text'          => $this->type_text,
		    'status_text'        => $this->status_text,
		    'options_text'        => $options_text
		); 
		
		$this->_template('admin/order/pick_list_status_mutil',$data); 
	}
	
    /**
     * 订单配货
     * 扫入拣货单号
     */
	public function scan_list_mutil(){

		$uid = $this->user_info->id;//登录用户id

		$pick_id	= (int)$this->input->get_post('pick_id');

		if(empty($pick_id)){
			showmessage('非法请求');
		}

		//判断拣货单状态是否已完成
		$pick = $this->model->getOne(array('id'=>$pick_id),true);

		if(empty($pick)){
			showmessage('拣货单：'.$pick_id.' 不存在,请返回重新输入');
		}

		if($pick['status'] == 5){
			showmessage('拣货单：'.$pick_id.' 已标记发货,请返回输入新的拣货单','admin/order/pick_manage/picking');
		}

		if($pick['status'] == 4){
			showmessage('拣货单：'.$pick_id.' 已经包装完成,请返回输入新的拣货单','admin/order/pick_manage/picking');
		}

		if($pick['status'] == 1){
			showmessage('拣货单：'.$pick_id.' 状态为等待打印,请先打印拣货单');
		}

		//一个拣货单只能一个人包装
		/*
		if($pick['status'] == 3 && $pick['pick_uid'] != $uid){
			showmessage('拣货单：'.$pick_id.' 状态为正在包装,请选择其他拣货单进行包装');
		}

		if($pick['pick_uid'] != $uid && $pick['pick_uid'] > 0){
			showmessage('拣货单：'.$pick_id.' 拣货单包装人不是你,请选择其他拣货单进行包装');
		}
		*/
		$no_scan_num = 100;

		if($pick['status'] == 2){//更改拣货单的状态为正在包装

			$data = array();

			$data['status'] = 3;
			$data['pick_start_time'] = time();
			$data['pick_uid'] = $uid;

			$options = array();
			$options['where']['id'] = $pick_id;

			$tof_status = $this->model->update($data,$options);

			if(empty($tof_status)){
				showmessage('系统更改拣货单：'.$pick_id.' 状态为正在包装失败，请返回重新输入拣货单');
			}

		}

		//显示还未扫描订单数
		$no_all_num = $this->pick_product_model->getOne(array('select'=>'COUNT(*) as num',
															'where'=>array('status'=>1,'pick_id'=>$pick_id)),											 
															true);
		$no_scan_num = $no_all_num['num'];

		//显示当日已包装数
		$total = 0;
		$now_time = strtotime(date('Y-m-d'));
		$sql = "select count(DISTINCT orders_id) as total from erp_pick_product where scan_time>='".$now_time."' and scan_uid='".$uid."' and status=4";
		$now_total = $this->model->query_array($sql);
		if(!empty($now_total['total'])){
			$total = $now_total['total'];
		}

		$data = array(
			'pick'            	 => $pick,
		    'type_text'          => $this->type_text,
		    'status_text'        => $this->status_text,
		    'no_scan_num'        => $no_scan_num,
		    'total'				 => $total
		); 
		
		$template_name = 'pick_scan_list';

		$pick_product_info = array();
		if($pick['type'] == 3){//多品多件
			$template_name = 'pick_scan_list_for_all_two';
			//获取产品信息
			$pick_product_info = $this->pick_product_model->get_pick_product_by_pick_id($pick['id']);
		}

		$data['pick_product_info']  = $pick_product_info;

		$this->_template('admin/order/'.$template_name,$data); 
		
	}
	/**
	 * 包装作业下，多品多久扫描的方法
	 */
	public function do_scan_mutil(){

		$result['status'] = 0;
		
		$data = $this->input->get_post('data');
		$pick_id = $this->input->get_post('pick_id');
		
		$basket_num = $this->input->get_post('basket');//如果篮子号不为空，则可以直接根据篮子号获取订单号，然后匹配sku

		//以，分隔获取sku和数量的数组
		$skuAndNum = explode(',',$data);
		
		//以sku为键名，数量为值重组数组
		$skuInfo = array();
		
		//存放sku数组
		$skuArr = array();
		
		//存放sku字符串
		$skuString = '';
		foreach($skuAndNum as $v){
		   $arr = explode('@',$v);
		   //SKU看是否有映射
		   $sku = $this->old_sku_new_sku_model->replace_sku(strtoupper($arr[0]));
		   $arr[0] = $sku;
		   $skuInfo[strtoupper($arr[0])] = $arr[1];
		   $skuString .=",".$arr[0];
		  
		   $skuArr[] = strtoupper($sku);
		}
		
		$skuString = substr($skuString,1);//传递过来的sku拼接的字符串
		$skuCount = count($skuInfo);//传过来的sku种类数量

		if(!empty($basket_num)){//根据拣货单id和篮子id获取订单id
			
		   $option = array();
		   $where = array();
		   $where = array('pick_id' => $pick_id,'basket_num' => $basket_num);
		   $option = array('where'=>$where);
		   $orders = $this->pick_product_model->getAll2array($option);

		   $new_orders  =  array();//存放处理后的数组
		   
		   //对相同订单号存在相同sku的订单合并sku和数量
		   foreach($orders as $os){
		     if(isset($new_orders[$os['product_sku']])){
		       $new_orders[$os['product_sku']]['product_num'] += $os['product_num'];
		       continue;
		     }
		     $new_orders[trim($os['product_sku'])] = $os;
		   }
		   
			if(empty($new_orders)){
			  $result['msg'] = '没有找到能够匹配这些sku的订单号';
			  echo json_encode($result);die;
			}
			
			//如果该订单下sku的种类数量与传递过来的不相符
		   if( count($new_orders)!=$skuCount ){
		   		$result['msg'] = '能找到对应sku的订单号，但sku的种类数量匹配不上';
		        echo json_encode($result);die;
		   }
		   
		   $flag = 0;//sku数量相符的个数
		   $string = '';
		   foreach($new_orders as $no){
			  if($skuInfo[strtoupper(trim($no['product_sku']))]==$no['product_num']){
		       $flag +=1;
		      }
		      $string .='<tr><td>'.$no['product_sku'].'</td><td>'.$no['product_num'].'</td></tr>';
		   }
		   if($flag==$skuCount){//如果sku的数量与传递过来的数量和种类相同，返回该订单号
		   	 $result['status'] = 1;
		   	 $result['msg'] = '订单匹配成功';
		   	 $result['orderid'] = $orders[0]['orders_id'];
		   	 $result['orderInfo'] = $string;
		   }else{
		     $result['msg'] = 'sku的数量匹配不成功';
		   }
		   echo json_encode($result);die;
		 }
		 
		 
		 //能匹配上的订单号Array([0] => Array ( [num] => 2 [orders_id] => 2660202 ) [1] => Array ( [num] => 2 [orders_id] => 2668832 )) 
		 $orderArr = $this->pick_product_model->get_pick_product($pick_id,$skuArr,$skuCount);

		if(empty($orderArr)){
		  $result['msg'] = '没有找到能够匹配这些sku的订单号';
		  echo json_encode($result);die;
		}
		
		
		//查找每个订单下的sku数量和种类是否相同，如果找到直接退出，返回该订单号
		foreach($orderArr as $o){
		   $data = $this->pick_product_model->get_info_by_pickid_orderid($pick_id,$o['orders_id']);
		   
		   //如果该订单下sku的种类数量与传递过来的不相符
		   if( count($data)!=$skuCount ){
		   		$result['msg'] = '能找到对应sku的订单号，但sku的种类数量匹配不上';
		       continue;
		   }
		   //判断该订单下的sku的产品数量与传递过来的数量是否相符
		   $flag = 0;//sku数量相符的个数
		   $string = '';//拼接显示匹配重构后的信息
		   foreach($data as $d){
		     if($skuInfo[strtoupper(trim($d['product_sku']))]==$d['product_num']){
		       $flag +=1;
		     }
		     $string .='<tr><td>'.$d['product_sku'].'</td><td>'.$d['product_num'].'</td></tr>';
		   }
		   if($flag==$skuCount){//如果sku的数量与传递过来的数量和种类相同，返回该订单号
		   	 $result['status'] = 1;
		   	 $result['msg'] = '订单匹配成功';
		   	 $result['orderid'] = $o['orders_id'];
		   	 $result['orderInfo'] = $string;
		     break;
		   }else{
		     $result['msg'] = 'sku的数量匹配不成功';
		   }
		   
		}

		echo json_encode($result);die;

	}
	
	//检查订单是否已经发货
	public function checkOrderStatusIsSent(){
		//$orders_id = $this->input->get_post('orders_id');
		
		$result = array('info'=>'','status'=>1);
		
		$pick_id	= (int)$this->input->get_post('pick_id');
		$sku	= trim($this->input->get_post('sku'));
		//SKU看是否有映射
		$sku = $this->old_sku_new_sku_model->replace_sku($sku);
		
		//查找订单
		$pick_sku = $this->pick_product_model->find_order_by_sku($pick_id,$sku);
		
		if(empty($pick_sku)){
			$result['info'] = "SKU：{$sku}有误,请检查SKU是否在拣货单：{$pick_id}中";
			echo json_encode($result);die;
		}
		
		$orders_id = $pick_sku['orders_id'];
		$order = $this->orders_model->getOne(array('erp_orders_id'=>$orders_id,'orders_is_join'=>'0'),true);
		
		if($order['orders_status'] == '5'){
			$result = array('info'=>'sucess','status'=>2,'orders_id'=>$orders_id);
		}else{
			$result['info'] = '订单状态出现错误，不允许此操作';
		}
		
		echo json_encode($result);die;
	}
	
	
}