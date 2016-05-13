<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 版权管理类
 * User: lidabiao
 * Date: 2016-4-27
 */
class CopyRight extends Admin_Controller
{
	public function __construct(){
		parent::__construct();
		$this->load->model(array('copyright_model','slme_user_model','smt/Smt_user_tokens_model','sharepage'));
		$this->copyright = $this->copyright_model;
        $this->userToken    = $this->Smt_user_tokens_model;		
		$this->load->library('phpexcel/PHPExcel');
		
	}
	public function lists(){
		$data='';
		$string= '';
		//接收条件参数
		$per_page	= (int)$this->input->get_post('per_page');	
		$account = trim($this->input->get_post('account'));
		$sku	= trim($this->input->get_post('sku'));
		$pro_id	= trim($this->input->get_post('pro_id'));
		$complainant	= trim($this->input->get_post('complainant'));
		$trademark	= trim($this->input->get_post('trademark'));
		$ip_number	= trim($this->input->get_post('ip_number'));
		$degree	= trim($this->input->get_post('degree'));
		$violatos_number= trim($this->input->get_post('violatos_number'));
		$violatos_big_type	= trim($this->input->get_post('violatos_big_type'));
		$violatos_small_type= trim($this->input->get_post('violatos_small_type'));
		$status	= trim($this->input->get_post('status'));
		$score1	= trim($this->input->get_post('score1'));
		$score2	= trim($this->input->get_post('score2'));
		$seller	=trim($this->input->get_post('seller'));


		$cupage	= 30; //每页显示个数
		
		$return_arr = array ('total_rows' => true );

		$where = '';
 		$tj = array();
		if($account != ''){
			$where['account'] = $account;
			$string .= 'account='.$account;
		}
		
		if($sku != ''){
			$where['sku'] = $sku;
			$string .= '&sku='.$sku;
		}

		if($pro_id != ''){
			$where['pro_id'] = $pro_id;
			$string .= '&pro_id='.$pro_id;
		}

		if($complainant != ''){
			$where['complainant'] = $complainant;
			$string .= '&complainant='.$complainant;
		}

		if($trademark != ''){
			$where['trademark'] = $trademark;
			$string .= '&trademark='.$trademark;
		}

		if($ip_number != ''){
			$where['ip_number'] = $ip_number;
			$string .= '&ip_number='.$ip_number;
		}

		if($degree != ''){
			$where['degree'] = $degree;
			$string .= '&degree='.$degree;
		}

		if($violatos_number != ''){
			$where['violatos_number'] = $violatos_number;
			$string .= '&violatos_number='.$violatos_number;
		}

		if($violatos_big_type != ''){
			$where['violatos_big_type'] = $violatos_big_type;
			$string .= '&violatos_big_type='.$violatos_big_type;
		}

		if($violatos_small_type != ''){
			$where['violatos_small_type'] = $violatos_small_type;
			$string .= '&violatos_small_type='.$violatos_small_type;
		}

		if($status != ''){
			$where['status'] = $status;
			$string .= '&status='.$status;
		}

		if($score1 != ''){
			$where['score >'] = $score1;
			$string .= '&score1='.$score1;
		}

		if($score2 != ''){
			$where['score <'] = $score2;
			$string .= '&score2='.$score2;
		}

		if($seller != ''){
			$where['seller'] = $seller;
			$string .= '&seller='.$seller;
		}

		$tj = $where;

		$where['no_is_del'] = 1;//默认选出没被删除的///////////////////////////////////
 		
		$order = 'id desc';
		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
			'where'		=> $where,
			'order'		=> $order
		);
		$whereall = $where;
		$whereall['status']=1;
		$options_account = array(
			'where'=>$whereall,
		);
		$data_list_account= $this->copyright->getAll2Array($options_account); //查询求和
		$addaccount = 0;
		foreach($data_list_account as $v){
			$addaccount+=(float)$v['score'];
		}

		$data_list= $this->copyright->getAll($options,$return_arr); //查询所有信息
        //速卖通账号列表查询条件
        $smt_user_options = array(
            'select' => 'token_id, seller_account,accountSuffix',
            'where'  => array('token_status' => 0),
        );
        //速卖通账号列表
        $token_array = $this->userToken->getSmtTokenList($smt_user_options);
       
        $url = admin_base_url('publish/copyright/lists?').$string;

        $page = $this->sharepage->showPage ($url,$return_arr ['total_rows'], $cupage );
		//myecho($tj);
		$data = array(
		  'data'     	  => $data_list,
		  'page'     	  => $page,
		  'smtuser' 	  =>$token_array,
		  'account'		  =>$account,
		  'addaccount'	=>$addaccount,
		  'tjdata'	=>$tj
		);		
		$this->_template('admin/publish/smt/copyright_lists',$data);
	}
	//详情
	public function detail(){
		$id = (int)$this->input->get_post('id');
		if($id == ''){echo '查询出错!';exit;}
		$where['id'] = $id;
		$options = array('where'=>$where);
		$data_list = $this->copyright->getOne($options);
		//获取导入用户
		$userwhere = array('id'=>$data_list->import_uid);
		$useroptions =array('where'=>$userwhere);
		$userdata = $this->slme_user_model->getOne($useroptions);
		$data_list->douser =$userdata->user_name;
		$data = array(
			'data'=>$data_list
		);

		$this->only_template('admin/publish/smt/copyright_detail',$data);
	}
	//删除
	public function del(){
		$id = $this->input->get_post('id');
		$where =array('id'=>$id);
		$option['where'] =$where;
		$data['no_is_del'] = 0;
		$res = $this->copyright->update($data,$option);
		if($res){
			ajax_return('删除成功');
		}else{
			ajax_return('删除失败',0);
		}
		
	}
	//导入订单收款数据
	public function deal_data(){
		if(!$_FILES){
  			header("location:".admin_base_url('publish/copyright/lists')."");exit;
		}
		set_time_limit ( 0 ); //页面不过时		
		ini_set('memory_limit', '1024M');
		$uid = $this->user_info->id;//登录用户的信息

		//权限验证
		if ($this->user_info->key != 'root') {
			$list        = explode(",",$this->user_info->items);
			if(!in_array('179', $list)){
				echo '<meta charset="utf-8"/><script language="javascript">alert("你没有权限操作数据导入");window.location.href="'.admin_base_url('publish/copyright/lists').'"</script>';exit;

			}
		}		

		$uploadFiles = $_FILES["excelFile"]["tmp_name"];	//临时存储目录
		$fileName = $_FILES["excelFile"]["name"];//上传文件的文件名
		$post = $this->input->post();
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
		++$allColumn;
		$data = array();//存放读取的表格数据
        for($rowIndex=1;$rowIndex<=$allRow;$rowIndex++){        //循环读取每个单元格的内容。注意行从1开始，列从A开始
                for($colIndex='A';$colIndex!=$allColumn;$colIndex++){
                        $addr = $colIndex.$rowIndex;
                        //不兼容时间写法
                        //$cell = $currentSheet->getCell($addr)->getValue();

                        //兼容时间写法
                        if($colIndex=='M'||$colIndex=='N') //M列和O列是时间
                        {
                        	$cell = gmdate("Y-m-d H:i:s", PHPExcel_Shared_Date::ExcelToPHP($PHPExcel->getActiveSheet()->getCell("$colIndex$rowIndex")->getValue()));   
                        }else{  
             				$cell = $PHPExcel->getActiveSheet()->getCell("$colIndex$rowIndex")->getValue(); 
             			}
						        
                        if($cell instanceof PHPExcel_RichText){ //富文本转换字符串
                                $cell = $cell->__toString();
                        }
                        $data[$rowIndex][$colIndex] = $cell;
                }
        }	
		//插入主表erp_CopyRight的数据
		$main_data = array();     
		$nameArr = explode('.',$fileName);
		$file_name = $nameArr[0];		
		$main_data['import_time'] = date('Y-m-d H:i:s');//导入时间
		$main_data['import_uid'] = $uid;

		//取出所有侵权列表
		$where = array('no_is_del'=>1,);
		$options = array('where'=>$where);
		$allcopy = $this->copyright->getAll2Array($options);

		foreach($data as $v){
			if((trim($v['A']) == '账号')){
				continue;
			}
			if((trim($v['A']) == '') && (trim($v['B']) == '')){
				continue;
			}
			//账号为M13时， 商标列相同，跳过 
			if(trim($v['A']) == 'M13'){
				foreach($allcopy as $q){
					if(($q['account'] == "M13") && (trim($q['trademark']) == $v['F'])){
						continue 2;
					}
				}
			}else{
				//其它账号，当ID一致时，跳过
				foreach($allcopy as $q){
					if(($q['account'] != "M13") && (trim($q['pro_id']) == $v['C'])){
						continue 2;
					}
				}
			}

			$sqldata = array();
			$status = 1;//1为有效，0为无效,默认为有效
			if(trim($v['O']) != '有效'){
				$status = 0;
			}
			$sqldata = array(
				'import_time' => $main_data['import_time'],
				'import_uid' => $main_data['import_uid'],
				'no_is_del'=>1,//默认都没删除
				'account' => $v['A'],//账号
				'sku' =>$v['B'],
				'pro_id' => $v['C'],//产品广告ID
				'complainant' =>$v['D'],//投诉人
				'reason' => $v['E'],//侵权原因
				'trademark' => $v['F'],//商标名
				'ip_number' => $v['G'],//知识产权编号
				'degree' => $v['H'],//严重程度
				'violatos_number' => $v['I'],//违规编号
				'violatos_big_type' => $v['J'],//违规大类
				'violatos_small_type' => $v['K'],//违规小类
				'score'=>$v['L'],
				'violatos_start_time'=>str_replace('-','/',$v['M']),
				'violatos_fail_time'=>str_replace('-','/',$v['N']),
				'status' => $status,
				'seller' => $v['P'],
				'remarks' => $v['Q']//备注
			);
			$this->copyright->add($sqldata);
		}
		echo '<meta charset="utf-8"/><script language="javascript">alert("数据导入成功");window.location.href="'.admin_base_url('publish/copyright/lists').'"</script>';
         exit;

	}
public function excelTime($date, $time = false) {

    if(function_exists('GregorianToJD')){

        if (is_numeric( $date )) {

        $jd = GregorianToJD( 1, 1, 1970 );

        $gregorian = JDToGregorian( $jd + intval ( $date ) - 25569 );

        $date = explode( '/', $gregorian );

        $date_str = str_pad( $date [2], 4, '0', STR_PAD_LEFT )

        ."-". str_pad( $date [0], 2, '0', STR_PAD_LEFT )

        ."-". str_pad( $date [1], 2, '0', STR_PAD_LEFT )

        . ($time ? " 00:00:00" : '');

        return $date_str;

        }

    }else{

        $date=$date>25568?$date+1:25569;

        /*There was a bug if Converting date before 1-1-1970 (tstamp 0)*/

        $ofs=(70 * 365 + 17+2) * 86400;

        $date = date("Y-m-d",($date * 86400) - $ofs).($time ? " 00:00:00" : '');

    }

  return $date;

}
	/*定时任务当美国时间和违规失效时间一致时，状态变更为过期
	*
	*/
	public function changeStatusBytime(){
		echo "<meta charset='utf-8'>";
		date_default_timezone_set('America/New_York');
		$atime = date('Y-m-d H:i:s');//美国时间
		echo '美国时间：'.$atime."<br/>";
		date_default_timezone_set('PRC');
		$where =array(
			'no_is_del'=>1,
			'status'=>1,
		);
		$options = array(
			'where'=>$where
		);
		$data_lists=$this->copyright->getAll2Array($options);
		$failid = array();
		foreach($data_lists as $v){
			if(strtotime($v['violatos_fail_time']) < strtotime($atime)){
				//变更为失效的ID
				$failid[] = $v['id'];
			}
		}
		if(count($failid) >0 ){
			//变更为失效
			$failid = implode(',',$failid);
			$sql = "update erp_copyright set status=0 where id in(".$failid.")";
			$this->copyright->query($sql);
			echo "此次变更为失效的ID为:".$failid;
		}
	}
}