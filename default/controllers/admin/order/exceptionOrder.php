<?php
/**
 * 捡货异常订单查询
 * 因为仓库库存不准，有些订单变为已打印状态后找不到货又会被退回已通过状态，然后反复在这两个状态之间变化
 * 2015-12-04
 */
set_time_limit(500);
header('Content-Type: text/html; Charset=utf-8');
class exceptionOrder extends Admin_Controller{
    
    
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

    /*
	*author@he
     * 订单查询
	 *2015-12-17
     */
    public function orderList(){
		
		    $data['search']['ordersku'] = htmlspecialchars(isset($_GET['ordersku']) && !empty($_GET['ordersku']))?$_GET['ordersku']:'';
			
			$data['search']['house'] = htmlspecialchars(isset($_GET['house']) && !empty($_GET['house']))?$_GET['house']:'';
			
			$data['search']['location'] = htmlspecialchars(isset($_GET['location']) && !empty($_GET['location']))?$_GET['location']:'';
			
			$per_page = htmlspecialchars(isset($_GET['per_page']) && !empty($_GET['per_page']))?$_GET['per_page']:0;
			
			$sqls = $this->orders_model->oddsql();  //获取异常订单sql
			
			if($sqls){
				
				$ret = $this->orders_model->getExceptionOrderInfo($sqls);    //异常订单数据
			}
			$count_sku = count($ret);   //异常sku总数
			
			$cupage	= 20; //每页显示个数
			
			$str = '';
			
			$seek = '';
			
			$count = 0;
			
			if($data['search']['ordersku']){
				
				$str .= ' AND product_sku="'.$data['search']['ordersku'].'" ';//根据条件拼接sql
				
				$seek .= '&ordersku="'.$data['search']['ordersku'].'"';   //拼接分页搜索的条件
			}
			if($data['search']['house']){
				
				$str .= ' AND products_location="'.$data['search']['house'].'" ';
				
				$seek .= '&house="'.$data['search']['house'].'"';
			}
			if($data['search']['location']){
				
				$str .= ' AND orders_warehouse_id='.$data['search']['location'].' ';
				
				$seek .= '&location='.$data['search']['location'].'';
			}
			if($str){
				
				$sqls = str_replace('ORDER BY orders_warehouse_id, products_location',$str.'ORDER BY orders_warehouse_id, products_location',$sqls);  //将order by 替换成相应的条件进sql
			}
            $result = $this->orders_model->getExceptionOrderInfo($sqls);    //异常订单数据
			
			if($result){
				
				$count = count($result);  //搜索异常分页数
			}
			
			$result = array_slice($result,$per_page,$cupage);
			
			$i = 0; //初始化异常订单数
			
			if($result){   //列表与导出数据异常订单拼接
			
				$result = $this->abnormalorder($result);
				
				$i = $result['count_order'];//异常订单总数
				
				unset($result['count_order']);//释放数组键值
			}
			$return_arr = array ('total_rows' => true );
			
			$url = admin_base_url('order/exceptionOrder/orderList?').$seek;//拼接分页url
			
			$page = $this->sharepage->showPage ($url, $count, $cupage );
			
            $this->_template('admin/order/exception_order_list',array('count_order'=>$i,'ret'=>$data,'count'=>$count_sku,'data'=>$result,'page'=>$page,'per'=>$per_page+1));
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
		if($data){
			
			$data = $this->abnormalorder($data);    //列表与导出数据异常订单拼接
			
			unset($data['count_order']);//释放数组键值
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
			<td style="font-size:14px;font-weight:bold;padding:5px;">SKU下异常订单</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">库位</td>
            <td style="font-size:14px;font-weight:bold;padding:5px;">实库存</td>
			<td style="font-size:14px;font-weight:bold;padding:5px;">仓库</td>
            </tr>
            </thead>'.PHP_EOL;
			$i = 1;
        foreach($data as $key =>$val){
			
		if($val['warehouse']==1000){
			
					$str =  "深圳一仓";
					
			}else if($val['warehouse']==1025){
				
					$str =  "义乌仓";
			  }
        $table.='<tr><td>'.$i.'</td>
            		        <td>'.$val['product_sku'].'</td>
							<td>'.$val['orders_warehouse_id'].'</td>
            		        <td>'.$val['products_location'].'</td>
							<td>'.$val['actual_stock'].'</td>
            		        <td>'.$str.'</td>'.PHP_EOL;
							$i++;    //序号自增
        }
		
        $table.='</table></html>'.PHP_EOL;
		
        echo $table;
		
	}
	/*
	*列表与导出数据异常订单拼接
	*根据合并订单规则反向查出拆分订单并且符合条件为orders_is_join =0 and orders_is_split=1 (拆分之后的子订单)
	*     合并订单规则：1.B合并到A订单，规则为合并条件满足 根据erp_orders o 的erp_orders_id等于erp_orders_products p 表的erp_orders_id查出订单产品信息，
	*然后将 p 表erp_orders_id的值改为合并的主订单(A单)的erp_orders_id值，B订单修改orders_is_join=1(已合并)
	*2.buyer_id查询时只查询未合并的订单erp_is_join=0,则根据传过来的，判断订单是否是合并订单，
	*true则根据o.erp_orders_id=p.ebay_orders_id，查出o.erp_orders_id(主订单)即可
	*/
	public function abnormalorder($data = ''){   
	
		if($data){
			$orders_sql = $this->orders_model->abnormalorders(); //获取sku下所有异常订单sql
			
			$result = $this->orders_model->getExceptionOrderInfo($orders_sql);    //sku下所有异常订单数据
			
			$is_split = '';
			
			foreach($result as $k=>$v){
				
				$is_split .= $v['orders_id'].',';     
				
			}
			$is_split = rtrim($is_split,',');
			
			if($is_split){
				
				$ret_split = $this->orders_model->get_main_orders($is_split);     //一次性查出拆分订单的主订单  避免多次查询
				
				$split = '';    //初始化主订单号
				
				$ret = array();
				
				foreach($ret_split as $k=>$v){
					
					$split = $v['erp_orders_id'];
					
					if($split){
						
						$ret[] = $this->orders_model->get_split_orders($split);     //根据主订单合并规则反向查出拆分后的子订单号
						
					}
				}
				if($ret){   //根据主订单合并规则反向数组拼接拆分后的子订单号  确保反向查出拆分后的子订单号orders_is_join =0 and orders_is_split=1 (拆分之后的子订单)
					
					$i = 0;
					
					$rs_num = array();
					
					foreach($ret as $k=>$v){
						foreach($v as $key=>$val){   //数组降维
							$rs_num[$i]['erp_orders_id'] = $val['erp_orders_id'];
							$rs_num[$i]['ebay_orders_id'] = $val['ebay_orders_id'];
							$i++;
						}
					}
					$j = 0;
					
					$unset_result = array();
					
					foreach($rs_num as $key=>$val){    //将拆分订单的数据拼接到大数组输出
					   foreach($result as $k=>$v){
							if($val['ebay_orders_id'] == $v['orders_id']){
										$result[$v['product_sku'].$j]['product_sku'] =  $v['product_sku'];
										$result[$v['product_sku'].$j]['orders_id'] =  $val['erp_orders_id'];
										$unset_result[] = $v;
										$j++;
									}
						      }
					       }
						  
						}
					foreach($result as $k=>$v){     //拆分的主订单数据unset掉
						foreach($unset_result as $key=>$val){
							if($v['orders_id'] == $val['orders_id'] ){
								unset($result[$k]);
							}
						}
						
					}	
			    }
				
			foreach($data as $k=>$v){        //sku下所有异常订单拼接
				$data[$k]['orders_id'] = '';
				
				$data[$k]['warehouse'] = '';
				
				foreach($result as $key=>$val){       
					
					if($v['product_sku'] == $val['product_sku']){    //当sku相等时候
						
						$data[$k]['orders_id'] .= $val['orders_id'].',';    //逗号拼接sku到$data[$k]['orders_id']
					}
				}
				$data[$k]['orders_id'] = rtrim($data[$k]['orders_id'],',');
				
				$warehouse = $data[$k]['orders_warehouse_id'];
				
				$data[$k]['orders_warehouse_id'] = $data[$k]['orders_id'];    //交换数组位置
				
				$data[$k]['warehouse'] = $warehouse;
				
				unset($data[$k]['orders_id']);   //释放
			}
		}
		$data['count_order'] = count($result);   //异常订单总数
		
		return $data;
	}
	
}