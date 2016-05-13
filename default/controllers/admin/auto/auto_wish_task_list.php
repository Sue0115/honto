<?php
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");
class auto_wish_task_list extends MY_Controller{
	
	private $wish;//实例化wish
	
    function __construct()
    {
        parent::__construct();
        
        $this->load->model(array(
                'wish_price_task_model','wish/wish_user_tokens_model','operate_log_model',
        		'order/pick_product_model','user_ship_statistical_model'
          )
        );
       $this->model = $this->wish_price_task_model;
	   $this->load->library('MyWish');
	   $this->wish = new MyWish;
    }
    public function wish_auto_price_data($per_page="",$account=""){
      //获取所有需要调价的任务
      
      $option = array();
      
      $where = array();
      $where['status'] = 1;
      
      if(!empty($account)){
        $where['account'] = $account;
      }
      
 	  if(empty($per_page)){
        $per_page=1;
      }
      
	  $cupage	= 50; //每页显示个数
	  $start = ($per_page-1)*$cupage;
	  
      $option = array(
        'where'     => $where,
      	'page'		=> $cupage,
		'per_page'	=> $start,
      );
      $data = $this->model->getAll2array($option);

      foreach($data as $d){
      	
      	$up_data = array();//更改价格需要的数据
      	
        //根据账号获取key值
        $keyInfo = $this->wish_user_tokens_model->getKeyByAccount($d['account']);
        
        $up_data['key']   = $keyInfo['wish_key'];//账号key值
        $up_data['sku']   = $d['original_sku'];  //要更改的sku
        $up_data['price'] = $d['price'];		 //要更改的价格
        
        $result = $this->wish->changeWishProductPrice($up_data);

        if(isset($result['code']) && $result['code']==0){
            //更改成功，更新erp_wish_price_task里，status状态=2；表示已经执行
            $options = array();
            $wh = array();
            $updata = array();//要更新的值
            $updata['status'] = 2;
            $updata['API_time'] = date('Y-m-d H:i:s');
            $wh = array(
              'id' 	   => $d['id'],
              'status' => 1
            );
            $options['where'] = $wh;
            $tof = $this->model->update($updata,$options);
            if($tof){
              echo "更改线上{$d['account']}账号的{$d['original_sku']}成功改为{$d['price']},erp数据更新成功！<br/>";
            }else{
              echo "更改线上{$d['account']}账号的{$d['original_sku']}成功改为{$d['price']},erp数据更新失败！<br/>";
            }
        }else{
             echo "更改线上{$d['account']}账号的{$d['original_sku']}改为{$d['price']}失败，原因".print_r($result)."<br/>";
             if(!empty($result['message'])){
	            $options = array();
	            $wh = array();
	            $updata = array();//要更新的值
	            $updata['status'] = 3;//状态异常
	            $updata['remark'] = $result['message'];
	            $updata['API_time'] = date('Y-m-d H:i:s');
	            $wh = array(
	              'id' 	   => $d['id']
	            );
	            $options['where'] = $wh;
	            $tof = $this->model->update($updata,$options);
             }
        }
        
      }

    }
    
    
}