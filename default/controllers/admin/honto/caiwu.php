<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 财务管理
 * User: lidabiao
 * Date: 2016-5-16
 */
class Caiwu extends Admin_Controller
{
	public function __construct(){
		parent::__construct();
		$this->load->model(array('honto_caiwu_model','slme_user_model','smt/Smt_user_tokens_model','sharepage'));
		$this->copyright = $this->copyright_model;
        $this->userToken    = $this->Smt_user_tokens_model;		
		$this->load->library('phpexcel/PHPExcel');
		
	}
	public function lists(){
		$cupage	= 2; //每页显示个数
		$return_arr = array ('total_rows' => true );
		//接收条件参数
		$per_page	= (int)$this->input->get_post('per_page');
		$type	= (int)$this->input->get_post('type');
		$inputtime1	= $this->input->get_post('inputtime1');
		$inputtime2	= $this->input->get_post('inputtime2');
		$activetime1= $this->input->get_post('activetime1');
		$activetime2= $this->input->get_post('activetime2');


		$where = array();
		$string='';//页码连接参数

		$tj = array();//查询的数据
		if($type != ''){
			$where['type'] = $type;
			$string .= 'type='.$type;
		}
		if($inputtime1 != ''){
			$where['input_time >'] = $inputtime1;
			$string .= 'inputtime1='.$inputtime1;
		}
		if($inputtime2 != ''){
			$where['input_time <'] = $inputtime2;
			$string .= 'inputtime2='.$inputtime2;
		}
		if($activetime1 != ''){
			$where['active_time >'] = $activetime1;
			$string .= 'activetime1='.$activetime1;
		}
		if($activetime2 != ''){
			$where['active_time <'] = $activetime2;
			$string .= 'activetime2='.$activetime2;
		}
		$tjdata=$where;
		$tjdata['inputtime1']=$inputtime1;
		$tjdata['inputtime2']=$inputtime2;
		$tjdata['activetime1']=$activetime1;
		$tjdata['activetime2']=$activetime2;
		$order = 'id desc';
		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
			'order'		=> $order,

		);		
		if($where != ''){
			$options['where'] =$where;
		}

		$data_list= $this->honto_caiwu_model->getAll($options,$return_arr); //查询所有信息

		$money = array();
		//获取注入资金
		$zrsql = "select sum(money) as totall from erp_honto_caiwu where type=2";
		$zrmoney = $this->honto_caiwu_model->query_array($zrsql);
		$money['zrmoney'] = $zrmoney['totall'];
		//获取支出资金
		$zcsql = "select sum(money) as totall from erp_honto_caiwu where type=1";
		$zcmoney = $this->honto_caiwu_model->query_array($zcsql);
		$money['zcmoney'] = $zcmoney['totall'];
		//剩余金额
		$money['symoney'] = $money['zrmoney'] - $money['zcmoney'];
		$money['symoney'] = number_format($money['symoney'],2);


		$url = admin_base_url('honto/caiwu/lists?').$string;
		$page = $this->sharepage->showPage ($url,$return_arr ['total_rows'], $cupage );
		$data = array(
		  'data'    => $data_list,
		  'page'    => $page,
		  'tjdata'	=>$tj,
		  'money'	=>$money,
		  'tjdata'	=>$tjdata
		);	
		$this->_template('admin/honto/caiwu/lists',$data);
		//myecho($data_list);
	}
	public function info(){
		$data = array();
		if($this->input->is_post()){
	        $this->save();
	    }
		$this->_template('admin/honto/caiwu/add',$data);
	}
	public function save(){

		$data['remarks'] = $this->input->get_post('remarks');
		$data['money'] = $this->input->get_post('money');
		$data['active_time'] = $this->input->get_post('active_time');
		$data['type'] = (int)$this->input->get_post('type');

		$data['add_ip']=$this->egetip();
		$data['input_time']=date('Y-m-d H:i:s');
		$data['user_name']=$this->user_info->user_name;
		$result=$this->honto_caiwu_model->add($data);
		if($result){
			echo '{"info":"添加成功","status":"y","id":"'.$val.'"}';
		}else{
			echo '{"info":"添加失败","status":"n"}';
		}
		die;
	}
		//取得IP
	public function egetip(){
		if(getenv('HTTP_CLIENT_IP')&&strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown')) 
		{
			$ip=getenv('HTTP_CLIENT_IP');
		} 
		elseif(getenv('HTTP_X_FORWARDED_FOR')&&strcasecmp(getenv('HTTP_X_FORWARDED_FOR'),'unknown'))
		{
			$ip=getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif(getenv('REMOTE_ADDR')&&strcasecmp(getenv('REMOTE_ADDR'),'unknown'))
		{
			$ip=getenv('REMOTE_ADDR');
		}
		elseif(isset($_SERVER['REMOTE_ADDR'])&&$_SERVER['REMOTE_ADDR']&&strcasecmp($_SERVER['REMOTE_ADDR'],'unknown'))
		{
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		$ip=trim(preg_replace("/^([\d\.]+).*/","\\1",$ip));
		return $ip;
	}
}