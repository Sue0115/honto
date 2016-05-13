<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 产品管理模型类
 */
class Products_data_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->load->model(array('sharepage','shipment_suppliers_model','slme_shipment_channel_model', 'sku_publish_record_model',
        	'suppliers_model', 'smt/Smt_user_tokens_model'));
    }
    
    public function getProductsListTmp($array = '')
    {
        $options = array();
        
        $array['keywd'] = (isset($array['keywd'])) ? trim($array['keywd']) : '';
        
        $punishtype = (isset($array ['productPunishType'])) ? $array ['productPunishType'] : '';
        
        $array['saler'] = (isset($array['saler'])) ? trim($array['saler']) : '';
        
        $array['timeSearchType'] = (isset($array['timeSearchType'])) ? trim($array['timeSearchType']) : '';
        
        $decriptionType = (isset($array['productsDescriptionType'])) ? trim($array['productsDescriptionType']) : '';
        
        $array['productIsPunish'] = (isset($array['productIsPunish'])) ? trim($array['productIsPunish']) : '';
        
        if ($punishtype != '')	//刊登平台
        {
        	if ($punishtype == 6 && $array['saler'] != '')	//速卖通平台,(与销售负责人关联),选了刊登平台同时销售负责人存在
        	{
        			/*$options_seller_account = array(
        				'select' => array(
        					'seller_account'
        				),
        				'where' => array(
        					'customerservice_id' => $array['saler'],
        				),
        			);
        			$salerAccountArray = $this->smt_user_tokens_model->getAll2Array($options_seller_account);
        			$salerAccount = array();//销售负责人
        			foreach ($salerAccountArray as $v){
						$salerAccount[] = $v['seller_account'];
					}
					
					if ($array ['productIsPunish'] === '0'){}*/
        	}
        	else
        	{
        		if ($array['productIsPunish'] != ''){}
        		else
        		{
        			$options = array(
        				'where' => array(
        					'products_sku IN (SELECT SKU FROM erp_sku_publish_record WHERE platTypeID = '.$punishtype.')' => NULL,
        				),
        			);
        		}
        	}
        }
        else
        {
        	$where = array();
        	$where['productsIsActive'] = 1;
        	$where['products_father_sku'] = '';
        	$options = array(
        		'where'	=> $where,
        	);
        	
        	/*
        	 * SELECT *
				FROM (`erp_products_data`)
				WHERE `productsIsActive` =  1
				AND (products_father_sku =  '' OR `products_father_sku` is null )
        	 */
        }
        
        if ($array['keywd'] != '')	//关键字 SKU等
        {
        	$options['like'][$array['searchFrom']] = $array['keywd'];
        	//AND  `$array['searchFrom']`  LIKE '%$array['keywd']%'
        }
        
        if (!empty($array['productsType']))	//物品分类
        {
        	$options['where']['products_sort'] = $array['productsType'];
//           and products_sort = '" . $array['productsType'] . "'
        }
        
		if(!empty($array['whNameList']))	//添加多仓库筛选
		{
			$options['where']['product_warehouse_id'] = $array['whNameList'];
//			 " and product_warehouse_id = ".$array['whNameList']." 
		}
		
    	//物品状态
        if (!empty($array['productsStauts']))
        {
            if ($array['productsStauts'] == 'unSalable_ge')
            {
            	$options['where']['saleCountInMinCycle'] = 0;
            	$options['where']['products_unreal >'] = 0;
            	$options['where']['products_unreal <='] = 20;
//            	and saleCountInMinCycle = 0 and products_unreal > 0 and products_unreal <= 20
            }
            elseif ($array['productsStauts'] == 'unSalable_se')
            {
            	$options['where']['saleCountInMinCycle'] = 0;
            	$options['where']['products_unreal >'] = 0;
            	$options['where']['products_unreal >'] = 20;
//            	and saleCountInMinCycle = 0 and products_unreal > 0 and products_unreal > 20
            }
            else
            {
            	$options['where']['products_status_2'] = $array['productsStauts'];
//            	and products_status_2 = '" . $array['productsStauts'] . "'
            }
        }
        
    	if (!empty($array['user_id']))	//采购
    	{
    		if ($array['user_id'] == 'none')
    		{
    			$options['where']['products_suppliers_id'] = 0;
//            	and products_suppliers_id = 0
    		}
    		else
    		{
    			$options_user_id = array(
    				'select' => array(
    					'suppliers_id'
    				),
    				'where' => array(
    					'user_id' => $array['user_id'],
    				),
    			);
//    			select suppliers_id from erp_suppliers where user_id = '" . $array['user_id']
    			$user_id = $this->suppliers_model->getAll2Array($options_user_id);
    			$tmp_arr = array();
    			foreach ($user_id as $sar)
    			{
    				$tmp_arr[] = $sar['suppliers_id'];
    			}
    			if (!empty($tmp_arr[0]))
    			{
    				$options['where_in']['products_suppliers_id'] = $tmp_arr;
    			}
    			else
    			{
    				$options['where']['products_suppliers_id']	=	null;
    			}
//    			and (  ' or ', $tmp_arr ) . " )
    		}
        }
        
    	if ($array['saler'] != '')	//销售负责人不为空
    	{
    		if ($array['saler'] == 'none')
    		{
    			$options['where']['salers'] = '';
    		}
    		else
    		{
    			$options['like']['salers'] = $array['saler'];
    		}
        	/*if (trim($array['saler']) == 'none') {
        		$sql .= " and (salers is null or salers = '' or salers = ',,') ";
        	}else {
            	$sql .= " and salers like '%," . $array['saler'] . ",%' ";
        	}*/
        }
        
		if ($array['timeSearchType'] != '')	//时间类型
		{
            $dateFrom	= ($array['dateFrom'] != '') ? $array['dateFrom'] : date('Y-m-d', time() - (60*60*24*1));
            $dateTo		= ($array['dateTo'] != '') ? $array['dateTo'] : date('Y-m-d');
            
            if ($array['timeSearchType'] == 'createTime')	//开发时间
            {
            	$options['where']['products_create_time >='] = $dateFrom;
            	$options['where']['products_create_time <'] = $dateTo . ' 23:59:59';
//            	and products_create_time >= '" . $dateFrom . ' 00:00:00' . "' and products_create_time <= '" . $dateTo . ' 23:59:59
            }
            else if ($array['timeSearchType'] == 'publishTime')	//刊登时间
            {
            	$options_sku = array(
            		'select' => array(
            			'sku',
            		),
            		'where' => array(
            			'recordID >' => 0,
            			'publishTime >=' => $dateFrom,
            			'publishTime <=' => $dateTo . ' 23:59:59'
            		),
            	);
            	$skuArray = $this->sku_publish_record_model->getAll2Array($options_sku);
            	$tmpArr   = array( );
                foreach ($skuArray as $sA) {
                	$tmpArr[] = $sA['sku'];
                }
            	if (!empty($tmpArr))
            	{
            		$options['where_in']['products_sku'] = $tmpArr;
            	}
//            	$sql .= " and ( " . join( ' or ', $tmpArr ) . " )";
            }
		}
		
		if ($decriptionType != '')	//资料
		{
			if ($decriptionType == 1)
			{
				$options['where']['productsDescriptionFR IS NOT NULL'] = NULL;
			}
			else if ($decriptionType == 2)
			{
				$options['where']['productsDescriptionDE IS NOT NULL'] = NULL;
			}
			else
			{
				$options['where']['productsDescriptionFR IS NOT NULL'] = NULL;
				$options['where']['productsDescriptionDE IS NOT NULL'] = NULL;
			}
		}
		
        if (!empty($array['affiliated']))	//附属品
        {
        	$affiliated_ay	= explode('/', $array['affiliated']);	//分割字符串 得到字段和状态
        	if (!empty($affiliated_ay)){
//        		$sql .= ' and '.$affiliated_ay[0].' = '.$affiliated_ay[1];
        		$options['where'][$affiliated_ay[0]] = $affiliated_ay[1];
        	}
        }
    
	    $array['per_page'] = isset($array['per_page']) ? $array['per_page'] : 0;
	    $per_page = (int)$array['per_page'];
        
	    /*
        $options_total = $options;
        $options_total['select'] = array('count(products_sku) AS geshu');
        $total = $this->getOne($options_total, true);
        $recordCount = $total['geshu']; //数据总数
        */
	    
        $pageSize = isset($array['pageSize']) ? $array['pageSize'] : 0;
        $pageSize = $pageSize > 0 ? $pageSize : 10;
        
        $order_by = array(
        	'productsLastModify' => 'desc',
        	'products_sku' => 'desc',
        );
		$options['order_by'] = $order_by;
        $options['page'] = $pageSize;	//查询N行
        $options['per_page'] = $per_page;	//跳过N行
        
        $return_arr = array ('total_rows' => true );
        
        $rs = $this->getAll($options,$return_arr,true);
        
        $recordCount = $return_arr ['total_rows'];
        
       	//$rs = $this->getAll2Array($options);
   		
        return array(
        	'recordSet' => $rs,
        	'recordCount' => $recordCount 
        );
    }
    
	/**
     * 统计分类的产品个数
     */
    function getProductSortNum()
    {
    /*SELECT products_sort,count(products_sku) as num from  erp_products_data  
WHERE  products_father_sku = '' or products_father_sku is null GROUP BY products_sort*/
        $options = array(
        	'select' => array(
        		'products_sort', 'count(products_sku) as num'
        	),
        	
        	'where' => array(
        		'products_father_sku' => '',
        	),
        	
        	'or_where' => array(
        		'products_father_sku is NULL' => NULL,
        	),
        	
        	'group_by' => 'products_sort',
        );
        
        $result = $this->getAll2Array($options);
        
        $productSortArr = array( );
        
        foreach ($result as $v)
        {
            $productSortArr[$v["products_sort"]] = $v["num"];
        }
        return $productSortArr;
    }
    
	/**
	 * 根据SKU查找产品信息，可选择字段
	 * @param unknown $sku
	 * @param string $fields
	 * @return multitype:multitype:
	 */
	function getProductsInfo($sku, $fields=FALSE)
	{
		$sku = trim($sku);
		$options = array(
			'where' => array(
				'products_sku' => $sku,
				'productsIsActive' => 1
			),
		);
   		
		if ($fields)
		{
			$options['select'] = $fields;
		}
		$rs = $this->getAll2Array($options);
		return $rs;
	}
	
	/**
	 * 根据产品ID获取采购负责人
	 * @param unknown_type $productID
	 * auth:su
	 */
	function getusername($productID)
	{
		$name = '';
		if (is_numeric($productID) && $productID != 0)
		{
			$select = array('s.user_id');
			
			$join = array(
				array(
					'erp_suppliers s', 's.suppliers_id = erp_products_data.products_suppliers_id'
				),
			);
			
			$where = array(
				'erp_products_data.products_id' => $productID,
			);
			
			$options = array(
				'select' => $select,
				'join' => $join,
				'where' => $where,
			);
			
			$rs = $this->getOne($options, true);
			
			$this->load->model(array('manages_model'));
			
			$name = $this->manages_model->getmanagefields('name',$rs['user_id']);
		}
		return $name;
	}
	
	/**
	 * 获取产品销售负责人
	 * @param unknown_type $productID
	 */
	function getSaler($productID){
		$options = array(
			'select' => array(
				'salers'
			),
			'where' => array(
				'products_id' => $productID,
			),
		);
		
		$result = $this->getOne($options, true);
		return $result;
	}
	
	function getProductsInfoWithPID($pID)
	{
	    $options = array(
	    	'where' => array(
	    		'products_id' => $pID
	    	),
	    );
	    $rs = $this->getOne($options, true);
	    return empty($rs) ? FALSE : $rs;
	}
	
	function getSuppliersInfoForProductsList()
	{
        $newArray = array();
        
        $options = array(
        	'select' => array(
        		'erp_products_data.products_sku as sku', 's.suppliers_company as suppliers_company', 's.user_id as user_id'
        	),
        	
        	'join' => array(
        		array('erp_suppliers as s', 'erp_products_data.products_suppliers_id = s.suppliers_id', 'inner')
        	),
        );
        
        $rsArr = $this->getAll2Array($options);
        
        foreach ($rsArr as $rs) {
            $newArray[strtolower($rs['sku']) ] = $rs;
        }
        return $newArray;
    }
    
    public function createTempTable($array = array(), $paging = FALSE, & $total = FALSE)
    {
    	$arrivel_options = array();	//type=6 到货未入
    	$orders_options = array();	//type=4 销售(出)
    	$record_options = array();	//其他
    	
    	//时间倒序
    	$arrivel_options['order_by'] = array('(erp_procurement_arrivel.arrival_times)' => 'desc');
    	$orders_options['order_by'] = array('(o.orders_shipping_time)' => 'desc');
    	$record_options['order_by'] = array('(r.orders_record_time)' => 'desc');
    	
    	if ($paging)	//分页
    	{
    		$arrivel_options['page'] = $paging['cupage'];
    		$arrivel_options['per_page'] = $paging['per_page'];
    		
    		$orders_options['page'] = $paging['cupage'];
    		$orders_options['per_page'] = $paging['per_page'];
    		
    		$record_options['page'] = $paging['cupage'];
    		$record_options['per_page'] = $paging['per_page'];
    	}
    	
    	if(!empty($array['sku']) OR $array['sku'] === '0')	//SKU查询
    	{
    		if ( $array['matchMethod'] == 'equal' )
    		{
    			$arrivel_options['where']['erp_procurement_arrivel.arrival_sku'] = $array['sku'];
    			
    			$orders_options['where']['erp_orders_products.orders_sku'] = $array['sku'];
    			
    			$record_options['where']['(r.products_sku)'] = $array['sku'];
    		}
    		else {
    			$arrivel_options['like']['erp_procurement_arrivel.arrival_sku'] = $array['sku'];
    			
    			$orders_options['like']['erp_orders_products.orders_sku'] = $array['sku'];
    			
    			$record_options['like']['(r.products_sku)'] = $array['sku'];
    		}
    	}
    	
    	if (!empty($array['date_from']))	//开始时间
    	{
    		$arrivel_options['where']['erp_procurement_arrivel.arrival_times >='] = $array['date_from'] . '00:00:00';
    			
    		$orders_options['where']['o.orders_shipping_time >='] = $array['date_from'] . '00:00:00';
    		
    		$record_options['where']['(r.orders_record_time) >='] = $array['date_from'] . '00:00:00';
    	}
    	
    	if (!empty($array['date_to']))	//结束时间
    	{
    		$arrivel_options['where']['erp_procurement_arrivel.arrival_times <='] = $array['date_to'] . '23:59:59';
    			
    		$orders_options['where']['o.orders_shipping_time <='] = $array['date_to'] . '23:59:59';
    		
    		$record_options['where']['(r.orders_record_time) <='] = $array['date_to'] . '23:59:59';
    	}
    	
    	if (!empty($array['method']) && in_array(6, $array['method']))
    	{
    		
    		$arrivel_options['select'] = array(
    			'erp_procurement_arrivel.arrival_sku as sku', 'erp_procurement_arrivel.arrival_count as count',
    			'erp_procurement_arrivel.accept_user as user_id', 'erp_procurement_arrivel.arrival_times as time',
    			'erp_procurement_arrivel.erp_procurement_id as id',
    			'pa.products_suppliers_id as supplierID', 'pa.products_name_cn as cn', 'pa.products_value as value'
    		);
    		
    		$arrivel_options['join'][] = array('erp_products_data as pa', 'erp_procurement_arrivel.arrival_sku = pa.products_sku', 'left');
    		
    		$arrivel_options['where']['erp_procurement_arrivel.arrivalIsChecked'] = 0;	//是否质检 0 否 1 是
    		
    		$this->load->model(array('procurement/procurement_arrivel_model'));
    		
    		
    		if ($total)
    		{
    			$rs = $this->procurement_arrivel_model->getAll($arrivel_options, $total, true);
    		}
    		else {
    			$rs = $this->procurement_arrivel_model->getAll2Array($arrivel_options);
    		}
		    		
    		
    	}else {
    		
    		if (!empty($array['method']))
    		{
	    		$method = array();
	    		foreach ($array['method'] as $v){
	    			if (!empty($v))
	    			{
	    				$method[] = $v;
	    			}
	    		}
	    		if (!empty($method))
	    		{
	    			$record_options['where_in']['(r.orders_record_type)'] = $method;
	    		}
    		}
    		
	    	$record_options['where']['orders_record_count >'] = 0;
    		
    		$record_options['select'] = array(
    			'r.procurement_id as id', 'r.products_sku as sku', 'r.orders_record_count as count', 'r.user_id',
    			'r.supplierID', 'r.orders_record_time as time', 'r.orders_record_reason as reason', 'r.orders_record_type as type',
    			'erp_products_data.products_name_cn as cn', 'erp_products_data.products_value as value'
    		);
    		$record_options['join'] = array(
    			array('erp_orders_record as r', 'r.products_sku = erp_products_data.products_sku', 'INNER')
    		);
    		
    		if ($total)
    		{
    			$rs1 = $this->getAll($record_options, $total, true);
    		}
    		else {
    			$rs1 = $this->getAll2Array($record_options);
    		}
    		
    		if (!empty($array['method']) && in_array(4, $array['method']))
    		{
    			
    			$orders_options['select'] = array(
    				'erp_orders_products.orders_sku as sku', 'SUM(erp_orders_products.item_count) as count',
    				'o.erp_user_id as user_id', 'o.orders_type as orders_type',
    				'o.orders_shipping_time as time', 'o.erp_orders_id as id',
    			);
    			
    			$orders_options['join'][] = array(
    				'erp_orders as o', 'o.erp_orders_id = erp_orders_products.erp_orders_id', 'INNER'
    			);
    			
    			$orders_options['where']['o.orders_is_join'] = 0;	//订单是否合并或拆分过
    			$orders_options['where']['o.orders_status'] = 5;	//订单状态 5为已发货
//    			$orders_options['where']['o.orders_type < 8 OR o.orders_type=10'] = NULL;	//订单类型
    			
    			$orders_options['group_by'] = array('o.erp_orders_id' => NULL, 'erp_orders_products.orders_sku' => NULL);
    			
    			$this->load->model(array('orders/orders_products_model'));
    			
    			
	    		if ($total && empty($rs1))
	    		{
	    			$rs2 = $this->orders_products_model->getAll($orders_options, $total, true);
	    		}
	    		else {
	    			$rs2 = $this->orders_products_model->getAll2Array($orders_options);
	    		}
    			
    			$rs  = array_merge($rs1, $rs2);
    		}else {
    			$rs = $rs1;
    		}
    		
    	}
    	
    	return $rs;
    }
    
    public function get_products_data($selsect, $where)
    {
    	$options = array(
    		'select' => $selsect,
    		'where' => $where
    	);
    	
    	$result = $this->getOne($options, true);
    	
    	return $result;
    }
    
    //库存销量
    public function category_products($get = array(), $paging = FALSE, & $total = FALSE)
    {
    	$where = array(
    		'productsIsActive' => 1,	//产品是否启用 1为启用
    		'(products_father_sku is NULL or products_father_sku = "")' => null	//产品父SKU为null
    	);
    	
    	if (!empty($get))
    	{
    		foreach ($get as $k => $v) {
    			if ($k != 'search_from' && $k != 'per_page')
    			{
	    			if (!empty($v))
	    			{
	    				if ($k == 'products_unreal')
	    				{
	    					switch ($v)
	    					{
	    						case 'x':	//负值(欠货)
	    							$where['products_unreal <'] = 0;
	    							break;
	    							
    							case 'y':	//无货
    								$where['products_unreal'] = 0;
    								break;
    								
    							case 'z':	//有货
    								$where['products_unreal >'] = 0;
    								break;
	    					}
	    					
	    				}
	    				else if ($k == 'keywd')
	    				{
	    					$like = array();
	    					
	    					switch ($get['search_from'])
	    					{
	    						case 'products_sku':	//SKU查询
	    							
	    							//是否模糊搜索
	    							$is_fuzzy = strpos($v, '%');
	    							if (is_int($is_fuzzy))
	    							{
	    								$like['erp_products_data.'.$get['search_from']] = trim($v, '%');
	    							}else {
	    								$where['erp_products_data.'.$get['search_from']] = $v;
	    							}
	    							
	    							break;
	    							
	    						case 'products_name_en':	//产品英文名查询
	    							$like['erp_products_data.'.$get['search_from']]	= $v;
	    							break;
	    							
	    						case 'products_name_cn':	//产品中文名查询
	    							$like['erp_products_data.'.$get['search_from']]	= $v;
	    							break;
	    					}
	    					
	    					
	    				}else {
	    					$where[$k] = $v;
	    				}
	    			}
    			}
    		}
    	}
    	
    	$options = array(
    		//产品ID 中文名 SKU 仓库 实库存 是否欠货
    		'select' => array('erp_products_data.products_id', 'erp_products_data.products_name_cn',
    							'erp_products_data.products_sku', 'erp_products_data.product_warehouse_id',
    							'erp_stock_detail.actual_stock', 'erp_products_data.products_status_2'),
    		'where' => $where,
    		'join'	=> array(
		    				array(
								'erp_stock_detail', 'erp_products_data.products_id = erp_stock_detail.products_id', 'left'
							)
    					)
    	);
    	
    	if (!empty($like))
    	{
    		$options['like'] = $like;
    	}
    	
    	if ($paging)
    	{
    		$options['page'] = $paging['cupage'];
    		$options['per_page'] = $paging['per_page'];
    	}
    	
    	if ($total)
    	{
    		$rsArr = $this->getAll($options, $total, true);
    	}else {
    		$rsArr = $this->getAll2Array($options);
    	}
    	
    	return $rsArr;
    	
//     	$sql = $this->db->last_query();
//     	echo $sql;exit;
//     	echo '<pre>';print_r($rsArr);exit;
    }
    
    //供应商管理->展开详情->物品信息
    function getAllProducts ($id)
    {

    	$options = array(
    		'where' => array(
    			'productsIsActive' => 1,
    		),
    		'where_in' => array(
    			'products_suppliers_ids'	=> $id
    		)
    	);
    	
    	$rsArr = $this->getAll2Array($options);
    	return $rsArr;
    }

	/**
	 * 模糊查询SKU列表
	 * @param $sku
	 * @return array
	 */
	public function getProductSkuList($sku){
		$sku = trim($sku);
		$rs  = array();
		if ($sku) {
			$like['products_sku'] = $sku;
			$options              = array(
				'like'    => $like,
				'groupBy' => 'products_sku',
			);

			$result = $this->getAll($options);
			foreach ($result as $row) {
				$rs[] = trim($row->products_sku);
			}
		}

		return $rs;
	}

	/**
	 * 模糊查询SKU列表,排除
	 * @param $sku
	 * @return mixed
	 */
	public function getSkuListPartialLike($sku){
		$rs = $this->db->select('products_sku, products_volume, products_weight')
			->like('products_sku', $sku, 'after')->group_by('products_sku')
			->get($this->_table)->result_array();
		return $rs;
	}

	/**
	 * 统计采购入库(用到货记录表)和销售出库(用发货数量)
	 * @return array
	 */
	public function countPurchaseArrivalAndOrderShipped($start_date, $end_date){
		$rs = array();
		if (!empty($start_date) && !empty($end_date)){
			$dateFrom = date('Y-m-d', strtotime($start_date));
			$dateTo = date('Y-m-d', strtotime('+1 day', strtotime($end_date)));

			$sql = "SELECT
	SUM(orders_record_count) AS recordCount,
	'采购入库' AS `type`
FROM erp_orders_record WHERE orders_record_count > 0  AND (  orders_record_type = 1  )
AND orders_record_time >= '$dateFrom' AND orders_record_time < '$dateTo'
UNION ALL
SELECT  SUM(shipped_count) AS recordCount, '销售出库' AS `type`
FROM `erp_shipped_count`
WHERE shipped_date >= '$dateFrom' AND shipped_date < '$dateTo'";

			//如要按操作人员来处理，则sql如下操作
			/*SELECT
	DATE_FORMAT(orders_record_time, '%Y-%m-%d') AS recordTime,
	SUM(orders_record_count) AS recordCount,
	'采购入库' AS `type`,
	user_id
FROM erp_orders_record WHERE orders_record_count > 0  AND (  orders_record_type = 1  )
			AND orders_record_time >= '$dateFrom' AND orders_record_time < '$dateTo'
GROUP BY recordTime,user_id
UNION ALL
SELECT shipped_date AS recordTime, SUM(shipped_count) AS recordCount, '销售出库' AS `type`, user_id
FROM `erp_shipped_count`
WHERE shipped_date >= '$dateFrom' AND shipped_date < '$dateTo'
GROUP BY shipped_date, user_id*/
			$rs = $this->query($sql)->result_array();
		}
		return $rs;
	}

	/**
	 * 模糊查询SKU列表,排除
	 * @param $sku
	 * @return mixed
	 */
	public function getSkuInfoLike($sku){
		$rs = $this->db->select('products_sku,products_html_mod')
			->like('products_sku', $sku, 'after')->group_by('products_sku')
			->get($this->_table)->result_array();
		return $rs;
	}
}

/* End of file Products_data_model.php */
/* Location: ./defaute/models/Products_data_model.php */