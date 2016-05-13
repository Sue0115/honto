<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-04-22
 * Time: 10:07
 */
set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");
class Ebay_store_category extends Admin_Controller{
    protected $ebay;
    protected $userToken;
    function __construct()
    {
        parent::__construct();
     //   $this->load->library('MyEbayNew');
        $this->load->model(array(
            'ebay/Ebay_ebaysite_model',
            'ebay/Ebay_user_tokens_model',
            'ebay/Ebay_store_category_model',
            'ebay/Ebay_store_with_category_model',
            'shipment_model',
            'slme_user_model',
            'category_model',
            'sharepage'
        ));
      //  $this->ebay = new MyEbayNew();
        $this->category = $this->category_model;
        $this->list=$this->Ebay_store_with_category_model;
    }



    public function store_category_list(){

        $where    = array(); //查询条件
        $in       = array(); //in查询条件
        $like     = array(); //like查询条件
        $string   = array(); //URL参数
        $curpage  = (int)$this->config->item('site_page_num');
        $per_page = (int)$this->input->get_post('per_page');
        $search   = $this->input->get_post('search');
        //  var_dump($search);exit;
        if (isset($search['erp_category']) && $search['erp_category']) {
            $where['erp_category'] = trim($search['erp_category']);
            $string[]           = 'search[erp_category]=' . trim($search['erp_category']);
        }


        if (isset($search['token_id']) && $search['token_id']) {
            $string[]           = 'search[token_id]=' . trim($search['token_id']);
        }

        $search = $search ? $search : array();
        $curpage= 10;
        $orderBy = 'id DESC';
        //查询条件
        $options     = array(
            'where'    => $where,
            'where_in' => $in,
            'page'     => $curpage,
            'per_page' => $per_page,
            'order'              => $orderBy
        );

        if (!empty($like)){
            $options = array_merge($options, array('like' => $like));
        }


        $return_data = array('total_rows' => true);
        $data_array   =    $this->list->getAll($options, $return_data);
        //   var_dump($data_array);

        $c_url = admin_base_url('ebay/ebay_store_category/store_category_list');
        $url   = $c_url . '?' . implode('&', $string);

        $page = $this->sharepage->showPage($url, $return_data['total_rows'], $curpage);

        $data = array(
            'data'           =>$data_array,
            'search'             => $search,
            'page'               => $page,
            'totals'             => $return_data['total_rows'],
            'c_url'              => $c_url,
        );

        $data['user_array'] = $this->slme_user_model->get_all_user_info('user_name');
        $data['account'] = $this->Ebay_user_tokens_model->getAllAccount();
        $data['erp_category'] =  $this->category->defineProductsType();
        $data['all_store_category'] = $this->Ebay_store_category_model->getALLCategoryWithAccountID();


        $this->_template('admin/ebay/ebay_store_list.php',$data);
    }



    public function binding_category(){




        $id =$this->input->get_post("id");
        if($id){
            $data['one_info'] = $this->Ebay_store_with_category_model->getOne(array('where'=>array('id'=>$id)),true);
        }
        $data['erp_category'] =  $this->category->defineProductsType();
        //var_dump( $data['erp_category']);exit;
        $data['account'] = $this->Ebay_user_tokens_model->getAllAccount();

        foreach($data['account'] as $key => $ac){
            $result = $this->Ebay_store_category_model->getALLCategory($key);

            if(!empty($result)){
            //    var_dump($result);exit;
                $data['ebay_category'][$key]=$result;
            }
        }


        $this->_template('admin/ebay/ebay_store',$data);

    }

    public function delete(){
        $id = $this->input->get_post('id');

        if ($id){
            $this->Ebay_store_with_category_model->delete(array('where'=>array('id'=>$id)));
            echo json_encode(array('status' => true, 'msg' => '删除成功'));
        }else {
            echo json_encode(array('status' => false, 'msg' => '非法操作'));
        }
        exit;
    }

    public function add(){
        $id =$this->input->get_post("id");
        $erp_category = $this->input->get_post("erp_category");
        $ebay_category = $this->input->get_post("ebay_category");
        $data =array();
        $data['erp_category'] = $erp_category;
        $data['category_with_store'] = json_encode($ebay_category);
        $data['update_time'] = date('Y-m-d H:i:s');
        $data['user'] =  $this->user_info->id;
        if($id){
            $option=array();
            $option['where']['id'] = $id;
            $update_result = $this->Ebay_store_with_category_model->update($data,$option);
            if($update_result){
                ajax_return('更新成功',true,$id);
            }else{
                ajax_return('更新失败',false);
            }

        }else{
            $add_result = $this->Ebay_store_with_category_model->add($data);
            if($add_result){
                ajax_return('新增成功',true,$add_result);
            }else{
                ajax_return('新增失败',false);
            }

        }
    }

    public function test(){
       // echo 123;
       $result = $this->Ebay_store_with_category_model->getStoreCategoryBySkuTokenId('e3112',20);
    var_dump($result);exit;
    }

}