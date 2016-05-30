<?php
/**
 * 黑名单客户管理中心
 * @author:hejiancheng  
 * 2016-2-24
 */
set_time_limit(500);
header('Content-Type: text/html; Charset=utf-8');
class orders_blacklist extends Admin_Controller{
	
    function __construct(){
        parent::__construct();
        $this->load->model(
            array(
                'order/orders_model','sharepage','order/orders_blacklist_model'
            )
        );       
		$this->load->library('phpexcel/PHPExcel','phpexcel/PHPExcel/Reader/Excel5.php');
    }
	function orders_type(){
		
		$orders_type = array(     //id对应的平台
		        '0' => '导入数据',
				'1' => 'eBay',
				'2' => 'B2C商城',
				'3' => '线下交易',
				'5' => '补货',
				'6' => '速卖通',
				'7' => 'AMZ亚马逊',
				'8' => 'FBA头程',
				'9' => '淘宝仓订单',
				'10' => '海外仓头程',
				'11' => '新蛋网',
				'12' => '德国共享仓',
				'13' => 'wish',
				'14' => 'AllBuy'
			
			);
			return $orders_type;
	}
    /*
	*author@he
     * 黑名单客户管理列表
	 *2016-02-24
     */
    public function index(){
		    $data['buyer_name'] = htmlspecialchars(isset($_GET['buyer_name']) && !empty($_GET['buyer_name']))?$_GET['buyer_name']:'';
		    $data['buyer_id'] =   htmlspecialchars(isset($_GET['buyer_id']) && !empty($_GET['buyer_id']))?$_GET['buyer_id']:'';
			$data['buyer_zip'] =  htmlspecialchars(isset($_GET['buyer_zip']) && !empty($_GET['buyer_zip']))?$_GET['buyer_zip']:'';
		    $data['orderstype'] = htmlspecialchars(isset($_GET['orders_type']) && !empty($_GET['orders_type']))?$_GET['orders_type']:'';
			$data['status'] =     htmlspecialchars(isset($_GET['status']) && !empty($_GET['status']))?$_GET['status']:'';
			$per_page =   htmlspecialchars(isset($_GET['per_page']) && !empty($_GET['per_page']))?$_GET['per_page']:0;
			
			$key = $this->user_info->key;//用户组key
			
			$uid = $this->user_info->id;//登录用户id
			
			$orders_type = $this->orders_type();
			
			$ret = $this->orders_blacklist_model->get_allorders_blacklist();
			
			$cupage	= 30; //每页显示个数
			
			$seek = '';
			
			$options = '';
			
			$count_page = '';
			
			if($data['buyer_name']){
				
				$options['where']['buyer_name'] = $data['buyer_name'];
				
				$seek .= '&buyer_name='.$data['buyer_name'].'';   //拼接分页搜索的条件   
			}
			if($data['buyer_id']){
				
				$options['where']['buyer_id'] = $data['buyer_id'];
				
				$seek .= '&buyer_id='.$data['buyer_id'].'';   
			}
			if($data['buyer_zip']){
				
				$options['where']['buyer_email'] = $data['buyer_zip'];
				
				$seek .= '&buyer_zip='.$data['buyer_zip'].'';  
			}
			if($data['orderstype']){
				
				$options['where']['orders_type'] = $data['orderstype'];
				
				$seek .= '&orders_type='.$data['orderstype'].'';   
			}
			if($data['status']){
				if($data['status'] == 3){
					$data['status'] = 0;
				}
				$options['where']['status'] = $data['status'];
				
				$seek .= '&status='.$data['status'].'';  
			}else{
				$options['where']['status'] = 0;
				
				$seek .= '&status=3'; 
				
			}
			if($options){
				
				$result = $this->orders_blacklist_model->get_allorders_blacklist($options);   //搜索
			}else{
				
				$result = $this->orders_blacklist_model->get_allorders_blacklist();    //总黑名单列表数
			}
			
			if($result){
				
				$count_page = count($result);
			}
			
			$result = array_slice($result,$per_page,$cupage);//数组分页
			
			$count_black = $this->orders_blacklist_model->allorders_blacklist();    //处理的黑名单数量
			
			$j = '';
			
			$i = '';
			
			$pending_blacklist = array();//待处理
			
			$verify_blacklist = array();  //确认为
			
			foreach($count_black as $k=>$v){
				
				if($v['status'] == 1){
					
					$pending_blacklist[] = $v;
					
					$i += $v['times'];
				}else{
					
					$verify_blacklist[] =$v;

                    $j += $v['times'];					
				}
			}
			
			$return_arr = array ('total_rows' => true );
			
			$url = admin_base_url('export_data/orders_blacklist?').$seek;//拼接分页url
			
			$page = $this->sharepage->showPage ($url, $count_page, $cupage );
			
            $this->_template('admin/export_data/orders_blacklist',array('uid'=>$uid,'key'=>$key,'orders_type'=>$orders_type,'count_order'=>count($pending_blacklist),'ret'=>$data,'count'=>$i,'j'=>$j,'k'=>count($verify_blacklist),'data'=>$result,'page'=>$page,'per'=>$per_page+1));
    }
	/*
	*导出异常订单
	*@author:he
	*/
	public function exportorder(){
		
		$orderid = htmlspecialchars(isset($_GET['erp_orders_id']) && !empty($_GET['erp_orders_id']))?$_GET['erp_orders_id']:'';
		
		$orders_type = htmlspecialchars(isset($_GET['orders_type']) && !empty($_GET['orders_type']))?$_GET['orders_type']:'';
		
		$status = htmlspecialchars(isset($_GET['status']) && !empty($_GET['status']))?$_GET['status']:'';
		
		if($orderid){
			
			$orderid = rtrim($orderid,',');
			
			$data = $this->orders_blacklist_model->get_orders_id($orderid,$orders_type = '',$status = '');  //获得指定导出的数据
			
		}else if($orders_type || $status){
			
			if($status == 3){
			    $status = 0;
		     }
			$data = $this->orders_blacklist_model->get_orders_id($orderid = '',$orders_type,$status);  //获得指定导出的数据
		}
		
		if(!$orderid&&!$orders_type&&!$status){
			
			echo "<script>alert('导出数据发生错误！');history.back(-1);</script>";die;
		}
		
		$filename='orders_blacklist'.date('Y-m-d');
		
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
            <td style="font-size:14px;font-weight:bold;padding:5px;">平台</td>
			<td style="font-size:14px;font-weight:bold;padding:5px;">内单号</td>
			<td style="font-size:14px;font-weight:bold;padding:5px;">收货人ID</td>
			<td style="font-size:14px;font-weight:bold;padding:5px;">收货人</td>
			<td style="font-size:14px;font-weight:bold;padding:5px;">邮箱</td>
			<td style="font-size:14px;font-weight:bold;padding:5px;">邮编</td>
			<td style="font-size:14px;font-weight:bold;padding:5px;">退款订单数</td>
			<td style="font-size:14px;font-weight:bold;padding:5px;">客户总订单数</td>
			<td style="font-size:14px;font-weight:bold;padding:5px;">备注</td>
			<td style="font-size:14px;font-weight:bold;padding:5px;">销售账号</td>
            </tr>
            </thead>'.PHP_EOL;
			$i = 1;
		$orders_type = $this->orders_type();
        foreach($data as $key =>$val){
        $table.='<tr><td>'.$i.'</td>
		                    <td>'.$orders_type[$val['orders_type']].'</td>
							<td>'.$val['erp_orders_id'].'</td>
							<td>'.$val['buyer_id'].'</td>
							<td>'.$val['buyer_name'].'</td>
							<td>'.$val['buyer_email'].'</td>
							<td>'.$val['buyer_zip'].'</td>
							<td>'.$val['times'].'</td>
							<td>'.$val['orders_count'].'</td>
							<td>'.$val['remark'].'</td>
            		        <td>'.$val['sales_account'].'</td>'.PHP_EOL;
							$i++;    //序号自增
        }
		
        $table.='</table></html>'.PHP_EOL;
        echo $table;
		
	}
	/*
	*确认为黑名单操作
	*/
	public function true_black(){   
	
	    $orderid = htmlspecialchars(isset($_GET['erp_orders_id']) && !empty($_GET['erp_orders_id']))?$_GET['erp_orders_id']:'';
		
		if($orderid){
			
			$orderid = rtrim($orderid,',');
			
			$ret = $this->orders_blacklist_model->change_black_status($orderid);    //执行更改操作
			
			if($ret){
				
				echo "<script>alert('客户".$orderid."确认为黑名单成功！');history.back(-1);</script>";die;
			}else{
				
				echo "<script>alert('客户".$orderid."确认为黑名单失败！');history.back(-1);</script>";die;
			}
		}
	
	}
	/*
	*删除黑名单客户操作(逻辑删除，状态为3)
	*/
	public function deldata(){   
	
	    $orderid = htmlspecialchars(isset($_GET['orders_id']) && !empty($_GET['orders_id']))?$_GET['orders_id']:'';
		
		if($orderid){
			
			$orderid = rtrim($orderid,',');
			
			$ret = $this->orders_blacklist_model->change_black_status($orderid,3);    //执行更改操作
			
			if($ret){
				
				echo "<script>alert('黑名单客户".$orderid."删除成功！');history.back(-1);</script>";die;
			}else{
				
				echo "<script>alert('黑名单".$orderid."删除失败！');history.back(-1);</script>";die;
			}
		}
	
	}
	
	
	/*
	*导入黑名单订单操作
	*/
	public function import_data(){
		
		//导入新增
        if(isset($_REQUEST['add']) && ($_REQUEST['add']=='add')){
			
			$orders_type = $this->orders_type();
			
            $url = admin_base_url('export_data/orderMemo/mainList');
			
            $phpExcel = new PHPExcel_Reader_Excel5;
			
            $extend = explode("." ,$_FILES['excelFile']["name"]);
			
            $val    = count($extend)-1;
			
            $extend = strtolower($extend[$val]);
			
            if($extend != 'xls' && $extend !='xlsx'){
				
                echo '<script>alert("文件格式不正确，请上传EXCEL文件");history.go(-1);</script>';
                exit;
            }
            $filename = $_FILES['excelFile']["tmp_name"];
			
            $main_name= substr($_FILES['excelFile']["name"],0,strpos($_FILES['excelFile']["name"],'.'));
			
            $objPHPExcel = $phpExcel->load($filename);
			
            $sheet = $objPHPExcel->getSheet();
			
            $rows=$sheet->getHighestRow();//EXCEL行数
            
            $i=0;
            $data_array=array();
			
			$str = '';
			
            for($j=2;$j<=$rows;$j++){
				
                if(trim($sheet->getCell("A".$j)->getValue())){
					
					$a = $sheet->getCell("A".$j)->getValue();
					
					$b = trim($sheet->getCell("B".$j)->getValue());   //buyer_id
					
					$c = trim($sheet->getCell("C".$j)->getValue());
					
					$info = array();
					
					if($b){
						
						$info = $this->orders_blacklist_model->get_buyerid_info($b);   //根据buyer_id查出订单表对应的黑名单订单信息
						
					}
					
					$data_array[$i]['erp_orders_id']  = $info['0']['erp_orders_id'];//内单号
					
					$data_array[$i]['orders_type']    = array_search($a,$orders_type); ;//平台
					
                    $data_array[$i]['buyer_id']  = trim($sheet->getCell("B".$j)->getValue());//收货人id
					
                    $data_array[$i]['remark'] = trim($sheet->getCell("C".$j)->getValue());//备注
					
                    $data_array[$i]['status']    = 2;//状态
					
					$data_array[$i]['buyer_name'] = $info['0']['buyer_name'];//收货人名字
					
					$data_array[$i]['buyer_email'] = $info['0']['buyer_email'];//收货人邮箱
					
					$data_array[$i]['buyer_zip'] = $info['0']['buyer_zip'];//收货人邮编
					
					$data_array[$i]['sales_account'] = $info['0']['sales_account'];//收货人邮编
					
					//计算导入黑名单订单总订单数和退款订单数  取buyer_name或buyer_email搜索订单大的数据
						   $num_buyer_name = $this->orders_blacklist_model->get_count_buyer($email = '',$buyer_id = '',$zip = '',$data_array[$i]['buyer_name']);
							
							$num_buyer_email = $this->orders_blacklist_model->get_count_buyer($data_array[$i]['buyer_email'],$buyer_id = '',$zip = '',$buyer_name = '');
							
							if(!isset($num_buyer_name['0']['num'])){//当搜索条件为空时  不执行sql  返回空数组  赋0
								
								$num_buyer_name['0']['num'] = 0;
							}
							if(!isset($num_buyer_email['0']['num'])){
								
								$num_buyer_email['0']['num'] = 0;
							}
							
							if($num_buyer_name['0']['num'] > $num_buyer_email['0']['num'] || $num_buyer_name['0']['orders_type'] == 13){  //当订单中buyer_name搜索的黑名单订单大时  wish 只根据buyer_name判断黑名单数量
								
								$data_array[$i]['color_type'] = 1;   //为buyer_name搜索的黑名单客户
								
								$data_array[$i]['orders_count'] = $num_buyer_name['0']['num'];
								
								$refund = $this->orders_blacklist_model->get_refund_buyer($email = '',$id = '',$zip = '',$data_array[$i]['buyer_name']); //该buyer_name下的退款订单计算
								
								$data_array[$i]['times'] = $refund['0']['num'];
							}else if($num_buyer_name['0']['num'] < $num_buyer_email['0']['num']){//当订单中buyer_email搜索的黑名单订单大时
								
								$data_array[$i]['color_type'] = 2;  //为buyer_email搜索的黑名单客户
								
								$data_array[$i]['orders_count'] = $num_buyer_email['0']['num'];
								
								$refund = $this->orders_blacklist_model->get_refund_buyer($data_array[$i]['buyer_email'],$id = '',$zip = '',$name = '');//该buyer_email下的退款订单计算
								
								$data_array[$i]['times'] = $refund['0']['num'];
							}else{  //不存在时
								$data_array[$i]['color_type'] = 0;
								
								$data_array[$i]['orders_count'] = 0;
								
								$data_array[$i]['times'] = 0;
							}
						
					if($data_array[$i]['buyer_id']){
						
						$identical = $this->orders_blacklist_model->get_identical_list($data_array[$i]['buyer_id']);  //是否重复黑名单数据
						   
						if($identical){//将数据替换到黑名单客户订单对应的信息中
							
							$tof = $this->orders_blacklist_model->edit_identical_list($data_array[$i]['erp_orders_id'],$data_array[$i]['buyer_id'],$c,$data_array[$i]['buyer_name'],$data_array[$i]['buyer_email'],$data_array[$i]['buyer_zip'],$data_array[$i]['sales_account'],$data_array[$i]['color_type'],$data_array[$i]['orders_count'],$data_array[$i]['times']);
							
							
							if($tof){
								
								unset($data_array[$i]);   //update成功  则释放当前数据  不进行insert
								
								$str .= "收货人id为：".$identical['0']['buyer_id']."，数据覆盖成功！<br>";
							}
						}
					}
					      
                    $i++;
                }
            }
			
			$add_values = '';
			
            if(isset($data_array) && !empty($data_array)){//导入的excel有数据
			
			 $data = $data_array;
			 
			 unset($data_array);  //释放
			 
                if($data){
				
				foreach($data as $key=>$val){
					
					$add_values .= "('".$val['erp_orders_id']."',".$val['orders_type'].",'".$val['buyer_id']."','".$val['remark']."','".$val['status']."','".$val['buyer_name']."','".$val['buyer_email']."','".$val['buyer_zip']."',".$val['times'].",".$val['color_type'].",".$val['orders_count']."),";
					
					if(!($j%50)){     //大于50条时   批量insert 能被50整除
					
						$add_values = rtrim($add_values,',');
						
						$result = $this->orders_blacklist_model->import_black_list($add_values);    //执行insert
						
						if($result !== false){
							
							unset($add_values);    //释放
							
							$add_values = '';
						}
					}
					$j++;
				}
				$add_values = rtrim($add_values,',');
				
				$ret = $this->orders_blacklist_model->import_black_list($add_values);    //执行最后的insert
				
				if($ret){
					
					foreach($data as $key=>$val){
						
						$str .= "收货人ID为".$val["buyer_id"]."数据导入成功<br>";
						
					}
					$ret_export = $this->orders_blacklist_model->return_array("select buyer_id from erp_orders_blacklist where `status`=2 and buyer_id in(select buyer_id from erp_orders_blacklist where `status`=1)
");  //查出重复数据  已确认情况下  导入了未确认的重复数据(一次性查询避免插入查询耗性能)
                     $buyer_name = '';
					 
					 if($ret_export){
						 
					 
					 foreach($ret_export as $k=>$v){
						 
						 $buyer_name .= "'".$v["buyer_id"]."'".',';
						 
						 $str .= "收货人ID为".$v["buyer_id"]."数据导入失败，原因：收货人已存在<br>";
						 
					 }
					 $buyer_name = rtrim($buyer_name,',');
					 
					 if($buyer_name){
						 
						 $result = $this->orders_blacklist_model->return_query("DELETE FROM erp_orders_blacklist WHERE status=2 and buyer_id in (".$buyer_name.") ");//去除导入未确认与已确认重复数据  确保数据不重复
					 }
                    
					}
					
				}
			}
            }else{
				
				if($str == ''){
					
					$str .= '导入数据为空';
				}
				
            }           
			echo $str;
           echo "<a class=‘btn btn-primary btn-sm’ href='http://v2.erp.moonarstore.com/admin/export_data/orders_blacklist/orders_blacklist'>点击返回</a>";
        }
       
		
	}
	
	
	
}