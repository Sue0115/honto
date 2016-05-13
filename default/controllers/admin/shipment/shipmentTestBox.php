<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//物流测试匹配管理
class ShipmentTestBox extends Admin_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model(array(
							'sharepage',
							'shipment_model',
							'country_model',
							'shipment_category_model',
							'category_model',
							'orders_type_model',
							'shipment/shipping_method_default_model',
							'shipment_rule_model',
							'sangelfine_warehouse_model',
							'shipment/sf_user_tokens_model',
							'shipment/smt_user_tokens_model',)
							);
		$this->model = $this->shipment_model;
	}
	/**
	 * 测试物流匹配
	 */
	public function index(){
		
	  if($this->input->is_post()){
	        $this->dealTest();
	  }
		
	  $cateList=$this->category_model->getAllCate();//获取产品分类
	  
	  $ordertype=$this->orders_type_model->getAll2array();//获取订单类型
	  
	  $ebaySeller=$this->sf_user_tokens_model->getAllEbay();//获取ebay销售账号
	  
	  $smtSeller=$this->smt_user_tokens_model->getAllSmt();//获取smt销售账号

	  //获取amz销售账号
	  $amz_account = array(
        	array('seller_account' => 'Moo.us'),
        	array('seller_account' => 'Moo.ca'),
        	array('seller_account' => 'Moo.es'),
        	array('seller_account' => 'Moo.it'),
        	array('seller_account' => 'Moo.de'),
        	array('seller_account' => 'Moo.uk'),
        	array('seller_account' => 'Moo.fr'),
        	array('seller_account' => 'Moo.jp'),
        	array('seller_account' => 'Yt.us'),
        	array('seller_account' => 'Yt.es'),
        	array('seller_account' => 'Yt.it'),
        	array('seller_account' => 'Yt.de'),
        	array('seller_account' => 'Yt.uk'),
        	array('seller_account' => 'Yt.fr'),
        	array('seller_account' => 'Atoz.es'),
        	array('seller_account' => 'Atoz.it'),
        	array('seller_account' => 'Atoz.de'),
        	array('seller_account' => 'Atoz.uk'),
        	array('seller_account' => 'Atoz.fr'),
        	array('seller_account' => 'Etiger.us')
        );
        $sellerCount=array_merge($ebaySeller,$smtSeller,$amz_account);

      $countryList=$this->country_model->getAllCountry();//获取所有的发货国家
      
      $shipping_method_default=$this->shipping_method_default_model->getAllShipment();//获取所有的发货方式
	  
    
	  $data=array(
	    'categoryList' 	=> $cateList,
	    'ordertype'	   	=> $ordertype,
	  	'countryList'  	=> $countryList,
	  	'shippingMethod'=> $shipping_method_default,
	  	'sellerCount'	=> $sellerCount,
	  );
	  $this->_template('admin/shipment/shipmentManageTestBox',$data);
	}
	
	/**
	 * 处理匹配物流
	 */
	public function dealTest(){
	  //接受传递过来的数据post提交
	  $postData=$this->input->post();

	  $option['where']=array(
	  		'shipmentID >'=>0,
	  		'shipmentEnable'=>1,
	  );
	  $option['select']=array('shipmentRuleMatchArray','shipmentTitle');
	  $shipmentList=$this->model->getAll2array($option);

	  foreach($shipmentList as $eachShipment){
	    $result=$this->createMatchFunction($postData,$eachShipment['shipmentRuleMatchArray']);

	    if($result){
	     	echo '{"info":"'.$eachShipment['shipmentTitle'].'物流匹配测试成功","status":"y"}';
			die();
	    }
	  }
	  echo '{"info":"	物流匹配测试失败","status":"n"}';die(); 
	}
	public function createMatchFunction($ordersElement = '', $string = '', $debug = false){
		$checkString       = '';
        //反解析物流方式的 匹配规则  shipmentRuleMatchArray
        $matchArray        = unserialize( $string );
           
        //获取匹配规则列表
        $opti['where'] = array(
        		'ruleID >'=>0,
        );
        $shipmentRule      = $this->shipment_rule_model->getAll2array($opti);

        $shipmentRuleArray = array( );
        foreach ( $shipmentRule as $rs ) {
            if ( $rs['ruleEnable'] == 1 ) {
                $shipmentRuleArray[$rs['ruleID']] = $rs;
            } else {
                $shipmentRuleArray[$rs['ruleID']]['ruleFunction'] = false;
            }        
        }
       
        //$matchArray 物流方式  $mA['rule'] 物流方式规则ID  $shipmentRuleArray对应的匹配规则 
        foreach ( $matchArray as $mA ) {
        	if(!isset($mA['connectMethod'])){
        	  $mA['connectMethod']=' ';
        	}
            if (!empty($shipmentRuleArray[$mA['rule']]['ruleFunction'])) { //如果物流方式的匹配规则=匹配规则列表的规则
                $checkString .= ' ' . $mA['connectMethod'] . ' $this -> doShipmentMatch($ordersElement,\'' . $shipmentRuleArray[$mA['rule']]['ruleFunction'] . '\', $debug)' . ' ';
            }
        }

        //返回|| &&
        $prefix = trim( substr( $checkString, 0, 3 ) ); //当第一个匹配规则被停用后，需要将第二个规则的连接符去掉。
        if ( $prefix == '&&' || $prefix == '||' ) {
            $checkString = substr( $checkString, 3 );
        }
        //echo $checkString;
        //运行 php代码
        if($checkString){
        	eval( '$result = ' . $checkString . ';' );
        }

        return $result;
	}
	 /**
     * 执行物流匹配
     * @param array $ordersElement 订单信息
     * @param array $arrayString 匹配规则
     */
    function doShipmentMatch( $ordersElement, $arrayString = false, $debug ){
    	 if ( $arrayString ) {
            $matchResult      = false;
            $matchResultArray = array( );
          
            //匹配规则选中的参数
            $fieldArray       = unserialize( $arrayString );

            foreach ( $fieldArray as $key => $value ) {
                $matchResultArray[$key] = false;
                switch ( $key ) {
                //产品目录
                    
                    case 'productsCategory':
                        if ( in_array( $ordersElement['category'], $value ) ) {
                            $matchResultArray[$key] = true;
                        }
                        break;
                    //收件国家
                    
                    case 'ordersCountry':
                        if ( in_array( $ordersElement['country'], $value ) ) {
                            $matchResultArray[$key] = true;
                        }
                        break;
                    //包裹总重
                    
                    case 'packageWeight':
                        $weight = (int) ( $ordersElement['weight'] * 1000 );
                        $min    = (int) ( $value['min'] * 1000 );
                        $max    = (int) ( $value['max'] * 1000 );
                        if ( $weight >= $min && $weight <= $max ) {
                            $matchResultArray[$key] = true;
                        }
                        break;
                    //订单总成本
                    
                    case 'ordersCost':
                        $cost = (int) ( $ordersElement['cost'] * 100 );
                        $min  = (int) ( $value['min'] * 100 );
                        $max  = (int) ( $value['max'] * 100 );
                        if ( $cost >= $min && $cost <= $max ) {
                            $matchResultArray[$key] = true;
                        }
                        break;
                    //已选择的运输方式
                    case 'ordersSelectedShippingService':
                        if ( in_array( $ordersElement['shipmentSelected'], $value ) ) {
                            $matchResultArray[$key] = true;
                        }
                        break;
                    
                    //产品是否带电
                    case 'productsbattery':
                        unset($matchResultArray[$key]);
                        break;
                    //客户支付运费                       
                    case 'ordersShipFee':
                        $shippingFee = (int) ( $ordersElement['shippingFee'] * 100 );
                        $min         = (int) ( $value['min'] * 100 );
                        $max         = (int) ( $value['max'] * 100 );
                        if ( $shippingFee >= $min && $shippingFee <= $max ) {
                            $matchResultArray[$key] = true;
                        }
                        break;
                    //销售平台
                    
                    case 'sellingPlat':
                        unset($matchResultArray[$key]);
                        break;
                    //销售帐号
                    
                    case 'sellerAccount':
                        if ( in_array( $ordersElement['sellerAccount'], $value ) ) {
                            $matchResultArray[$key] = true;
                        }
                        break;
                    //订单总金额
                    
                    case 'ordersTotal':
                        $ordersTotal = (int) ( $ordersElement['ordersTotal'] * 100 );
                        $min         = (int) ( $value['min'] * 100 );
                        $max         = (int) ( $value['max'] * 100 );
                        if ( $ordersTotal > $min && $ordersTotal <= $max ) {
                            $matchResultArray[$key] = true;
                        }
                        break;
                    
                    case 'itemInThisWarehouse': //仓库
                        unset($matchResultArray[$key]);
                        break;

                    case 'ItemPackSize': //包装尺寸
                        unset($matchResultArray[$key]);
                        break;
                    
                    //粉末
                    case 'fenmo':
                        unset($matchResultArray[$key]);
                        break; 

                    //液体
                    case 'yeti':
                        unset($matchResultArray[$key]);
                        break;  
                    
                }
                
            }

            if ( $debug ) print_r($matchResultArray);

            //组装结果
            $resultArray = array_values( $matchResultArray );
            
            //返回结果
            if ( !in_array( false, $resultArray ) ) {
                $matchResult = true;
            }
            return $matchResult;
        } else {
            return 'disabled';
        }
	}
}