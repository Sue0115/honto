<?php
ini_set('memory_limit', '2048M');
set_time_limit(0);
header('Content-Type: text/html; Charset=utf-8');
class smt_user_tokens extends Admin_Controller{

    function __construct(){

        parent::__construct();
        $this->load->model(
            array(
                'smt/smt_user_tokens_model'
            )
        );
    }
    
    /**
     * 模版显示.新增
     */
    public function listShow(){
        $fields       =  $this->smt_user_tokens_model->getColumnsList();
        $fieldsArr    = array();//获取数据表字段信息
        foreach($fields['_type'] as $key=>$val){
            if($key=='token_id')continue;
            $fieldsArr[] = $key;
        }
        //新增
        if(isset($_REQUEST['add']) && ($_REQUEST['add']=='add') && !empty($_REQUEST['seller_account'])){
            $sellerAccountArr = $_REQUEST['seller_account'];
            foreach($sellerAccountArr as $key=>$val){
                $data =array();
                foreach($fieldsArr as $k=>$v){
                    $data[$v]            = $_REQUEST[$v][$key];
                }
                $option=array();
                $option['where']['member_id']=$data['member_id'];//索引是unique,所以检测它
                $checkData     = $this->smt_user_tokens_model->getOne($option);//检测是否存在
                if($checkData)continue;
                $this->smt_user_tokens_model->add($data);
                unset($data);
            }
        }      
        $option   = array();
        $seller_account_search    = htmlspecialchars($this->input->get_post("seller_account_search"));//搜索条件
        if(isset($seller_account_search) && !empty($seller_account_search)){
            $option['where']['seller_account']=$seller_account_search;
        }
        $option['order'] = 'token_id asc';
        $data         = $this->smt_user_tokens_model->getAll2array($option);       
        $this->_template('admin/smt/smt_token_list',array('data'=>$data,'fields'=>$fieldsArr));
        
    }
    
   /**
    * ajax修改栏目数据
    */
   public function ajaxEditData(){
       $id                     = $_REQUEST['id'];
       $fields       =  $this->smt_user_tokens_model->getColumnsList();
       $fieldsArr    = array();//获取数据表字段信息(为了模版对应数据字段)
       foreach($fields['_type'] as $key=>$val){
           if($key=='token_id')continue;
           $fieldsArr[] = $key;
       }
       $data = array();
       foreach($fieldsArr as $k=>$v){
           $data[$v]               = $_REQUEST[$v];          
       }     
       $result = $this->smt_user_tokens_model->updateTemplateInfo($data,$id);
       if($result){
           echo 'success';
       }else{
           echo 'defeat';
       }die();
   }
   

    
   
   
   
   
   
   
   
   
}