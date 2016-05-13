<?php
/**
 * 捡货异常订单查询
 * 因为仓库库存不准，有些订单变为已打印状态后找不到货又会被退回已通过状态，然后反复在这两个状态之间变化
 * 2015-12-04
 */
set_time_limit(500);
header('Content-Type: text/html; Charset=utf-8');
class exportStockInfo extends Admin_Controller{
    
    
    function __construct(){
        parent::__construct();
        $this->load->model(
            array(
                'order/orders_model','sharepage'
            )
        );       

    }
    /**
     * 订单状态
     * @return multitype:number
     */
    static function statusEdit(){
        return array(
            '1' => '新录入',
            '2' => '不通过',
            '3' => '已通过',
            '4' => '已打印',
            '5' => '已发货',
            '6' => '已撤单',
            '7' => '未付款',
            '8' => '已发货[FBA]',
            '9' => '预打印'
        );
    }

    public function index(){
    	
    	
		$this->load->model(array('stock_model'));
		
		$search	= $this->input->get_post('search');
		$query_string    = array();
		$where = array();
		$is_export = $search['is_export'];
		
    	if($search['products_sku']){
			$where['products_sku'] = $search['products_sku'];
			$query_string['search[products_sku]'] = $search['products_sku'];
		}
    	if($search['product_warehouse_id']){
			$where['product_warehouse_id'] = (int)$search['product_warehouse_id'];
			$query_string['search[product_warehouse_id]'] = (int)$search['product_warehouse_id'];
		}
    	if($search['products_location']){
			$where['products_location'] = $search['products_location'];
			$query_string['search[products_location]'] = $search['products_location'];
		}
    	if($search['productsStauts']){
			$where['productsStauts'] = $search['productsStauts'];
			$query_string['search[productsStauts]'] = $search['productsStauts'];
		}
		
		
		$per_page	= (int)$this->input->get_post('per_page');
	  	$cupage	= config_item('site_page_num'); //每页显示个数
	  	$return_arr = array ('total_rows' => true);
	  	
	  	$where['per_page'] = $per_page;
	  	$where['cupage'] = $cupage;
	  	
	  	$rows = $this->stock_model->getStockList($where, $return_arr);
	  	
		if($is_export){
			
			$this->getExportData($where, $rows, $return_arr['total_rows']);
			
		}
		
		
		$url = admin_base_url('stock_manage/exportStockInfo/index?').http_build_query($query_string);
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$data['result'] = $rows;
		$data['page'] = $page;
		$data['search'] = $search;
		
		$this->_template('admin/stock_manage/index',$data);
		
		
    }
    
    private function getExportData($where, $first_page_rows, $total_rows = 0){
    	
    		$filename='sku_info_'.date('Y-m-d').'.csv';
    		
    	
    	
	    	$table = '';
	    	$per_page = $where['per_page'];
    		$cupage = $where['cupage'];
    	
    		$this->outputHeader($filename);
    		
			$output = fopen('php://output', 'w');
			
 			fwrite($fp,chr(0xEF).chr(0xBB).chr(0xBF)); 
			fputcsv($output, array('Sku',iconv('utf-8','gb2312','中文名'),iconv('utf-8','gb2312','仓位'),
								iconv('utf-8','gb2312','实际库存'),iconv('utf-8','gb2312','销售状态'),
								iconv('utf-8','gb2312','重量')));
    
	    	if($first_page_rows){
	    		$this->writeRows($output, $first_page_rows);
	    	}
	    	
			if($total_rows > 0){
				
				while( $total_rows > ($per_page + $cupage) ){
					$per_page = $per_page + $cupage;
					$where['per_page'] = $per_page;
					$rows = $this->stock_model->getStockList($where);
					
					$this->writeRows($output, $rows);
				}
			}
			
			 
			exit;
			
    }
    private function writeRows($output, $data){
    	
	     foreach($data as $key =>$val){
			
	     	fputcsv($output, array($val->sku,iconv('utf-8','gb2312',$val->products_name_cn),$val->products_location,
	     	$val->actual_stock,iconv('utf-8','gb2312',$val->sku_status),$val->products_weight,));
	     	
		 }
    }
    
    private function outputHeader($filename){
    	header('Content-Type: text/csv; charset=utf-8');
    	header('Content-Disposition: attachment; filename=' . $filename);
 
    }
   
	/*
	*导出异常订单
	*@author:he
	*/
	public function exportorder(){
		$sql = $this->orders_model->oddsql(); //获取异常订单sql
		$orderid = htmlspecialchars(isset($_GET['order_id']) && !empty($_GET['order_id']))?$_GET['order_id']:'';
		$oddall = htmlspecialchars(isset($_GET['oddall']) && !empty($_GET['oddall']))?$_GET['oddall']:'';
		if($orderid){
			$ordersku = rtrim($orderid,',');
			$sql = str_replace('ORDER BY',' AND ok.ok.product_sku IN ('.$ordersku.') ORDER BY ',$sql);  //将order by 替换成相应的条件进sql
			$data = $this->orders_model->getExceptionOrderInfo($sql);//执行结果集返回
		}else if($oddall){
			$data = $this->orders_model->getExceptionOrderInfo($sql);//执行结果集返回,导出所有数据
		}
		if(!$orderid&&!$oddall){
			echo "<script>alert('导出数据发生错误！');history.back(-1);</script>";die;
		}
		
		$filename='exceptionOrder'.date('Y-m-d');
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
            <td style="font-size:14px;font-weight:bold;padding:5px;">序号'.iconv("gb2312","UTF-8",'序号').'</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">SKU</td>
			<td style="font-size:14px;font-weight:bold;padding:5px;">仓库</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">库位</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">实库存</td>
            </tr>
            </thead>'.PHP_EOL;
			$i = 1;
        foreach($data as $key =>$val){
		if($val['orders_warehouse_id']==1000){
					$str =  "深圳一仓";
			}else if($val['orders_warehouse_id']==1025){
					$str =  "义乌仓";
			  }
        $table.='<tr><td>'.$i.'</td>
            		        <td>'.$val['product_sku'].'</td>
							<td>'.$str.'</td>
            		        <td>'.$val['products_location'].'</td>
            		        <td>'.$val['actual_stock'].'</td>'.PHP_EOL;
							$i++;    //序号自增
        }
        $table.='</table></html>'.PHP_EOL;
        echo $table;
		
	}
	
}