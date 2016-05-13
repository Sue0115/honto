<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Orders_products_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }

    public function get_product_by_order_id($order_id){

    	$options = array();
    	$options['erp_orders_id'] = $order_id;
    	$data = $this->getAll2Array($options);

    	return $data;
    }

    public function get_product_array($order_id){

    	$data = $this->get_product_by_order_id($order_id);

    	if(empty($data)){
    		return array();
    	}

    	$result = array();

    	foreach ($data as $k => $v) {
    		$result[$v['orders_sku']]['sku'] = $v['orders_sku'];
    		if(isset($result[$v['orders_sku']]['num'])){
    			$result[$v['orders_sku']]['num'] += $v['item_count'];
    		}else{
    			$result[$v['orders_sku']]['num'] = $v['item_count'];
    		}
    		
    	}

    	return $result;

    }
    
    //根据订单id实现erp_orders_products和erp_products_data表的联合查询
    public function getProductSkuByOrderId($orderId,$orders_warehouse_id){
      $option['select']=array("{$this->_table}.*","p.products_weight","p.products_name_cn","p.products_id","p.products_status_2","p.products_imgs","p.products_warring_string","p.pack_method","p.products_with_adapter");
      $option['where']=array("{$this->_table}.erp_orders_id"=>$orderId,"p.product_warehouse_id"=>$orders_warehouse_id);
      $join[]=array("erp_products_data p","{$this->_table}.orders_sku=p.products_sku");
      $option['join']=$join;
      return $this->getAll2Array($option);
    }
    
    //根据订单id获取订单产品表的sku信息
    public function getSkuByOrderID($orderID){
      $option = array();
      $select = array();
      $where = array();
      $select = array('orders_sku');
      $where['erp_orders_id'] = $orderID;
      $option = array(
        'where' => $where,
        'select'=> $select
      );
      return $this->getAll2Array($option);
    }

    public function insert_lazada($item,$erp_orders_id,$count)
    {
        $data['erp_orders_id']=$erp_orders_id;

        $data['ebay_orders_id']=$item['OrderId'];

        //$data[''] = $item['CustomerFirstName'];

        $tmpSku=$item['Sku'];

        $data_sku = array();
        /*
         * 因为sku不涉及到数量、价格，直接对SKU进行拆解
         * */
        //直接取*号后面的部分
        $tmp = explode('*', $tmpSku);
        $tmpSku = trim(array_pop($tmp));

            //忽略中括号内的信息
            if (stripos($tmpSku, '[') !== false) {
                $tmpSku = preg_replace('/\[.*\]/', '', $tmpSku);
            }

            //处理小括号及其单价数量
            if (stripos($tmpSku, '(') !== false) {
                $sku = trim($this->getStringBetween($tmpSku, '', '('));
                $data_sku['sku'] = $sku;
            }else {
                $data_sku['sku'] = trim($tmpSku);
            }

        $data['orders_sku'] = $data_sku['sku'];

        $data['item_price'] = $item['PaidPrice'];

        $data['item_count'] =$count;

        $data['comment_time'] = date("Y-m-d H:i:s",time());

        //$data[''] = $item['TaxAmount'];

        $data['item_cost']=$item['PaidPrice']+$item['ShippingAmount'];

        //$data[''] = $item['ShippingAmount'];

        //$data[''] = $item['VoucherAmount'];

        //$data[''] = $item['Status'];

        //$data[''] = $item['TrackingCode'];

        //$data[''] = $item['Reason'];

        //$data[''] = $item['ReasonDetail'];

        //$data[''] = $item['PurchaseOrderId'];

        //$data[''] = $item['PurchaseOrderNumber'];

        //$data[''] = $item['PackageId'];

        //$data[''] = $item['CreatedAt'];

        //$data[''] = $item['UpdatedAt'];

        $tof = $this->add($data);

        return $tof;
    }

    public function getStringBetween($string, $start = '', $end = '') //取从某个字符首次出现的位置开始到另一字符首次出现的位置之间的字符串
    {
        //$s = ($start != '') ? stripos($string,$start)+1 : 0 ;$e = ($end != '' ) ? stripos($string,$end) : strlen($string) ;
        //if($s <= $e){return substr($string,$s,$e-$s);}else{return false;}
        $s = ($start != '') ? stripos($string, $start) : 0;
        $e = ($end != '') ? stripos($string, $end) : strlen($string);
        if ($s <= $e) {
            $string = substr($string, $s, $e - $s);
            return str_replace($start, '', $string);
        } else {
            return false;
        }
    }
    /*
     * 根据内单号 获取 comment_text
     */

    public function getLazadaOrderItemId($erp_orders_id)
    {
        $options = array();

        $options['select'] = array('comment_text');

        $where['erp_orders_id ']=$erp_orders_id;

        $options['where']=$where;


        $data = $this->getAll2Array($options);

        return $data;
    }
    
    /**
     * 链表查询sku的价格和库存
     * 根据仓库
     */
    public function getSkuPriceStock($warehouse=""){
      $option = array();
      $option['select']=array("{$this->_table}.orders_sku","{$this->_table}.item_price","sd.actual_stock");
      if($warehouse!=""){
        $option['where']=array("sd.stock_warehouse_id"=>$warehouse);
      }
      $join[]=array("erp_stock_detail sd","{$this->_table}.orders_sku=sd.products_sku");
      $option['join']=$join;
      return $this->getAll2Array($option);
    }
}

/* End of file Orders_products_model.php */
/* Location: ./defaute/models/order/Orders_products_model.php */