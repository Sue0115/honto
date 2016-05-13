<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-04-15
 * Time: 14:08
 */

header("content-type:text/html; charset=utf-8");
class Ebay_product_list extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('MyEbayNew');
        $this->load->model(array(
            'ebay/Ebay_product_model',
            'ebay/Ebay_ebaysite_model',
            'ebay/Ebay_user_tokens_model',
            'shipment_model',
            'slme_user_model',
            'sharepage'
        ));
        $this->ebay = new MyEbayNew();
        $this->list = $this->Ebay_product_model;
    }



    public function draft_center()
    {



        $where    = array(); //查询条件
        $in       = array(); //in查询条件
        $like     = array(); //like查询条件
        $string   = array(); //URL参数
        $curpage  = (int)$this->config->item('site_page_num');
        $per_page = (int)$this->input->get_post('per_page');
        $search   = $this->input->get_post('search');
        //  var_dump($search);exit;
        if (isset($search['account_id']) && $search['account_id']) {
            $where['account_id'] = trim($search['account_id']);
            $string[]           = 'search[account_id]=' . trim($search['account_id']);
        }

        if (isset($search['site']) && $search['site']!=999) {
            $where['site'] = trim($search['site']);
            $string[]           = 'search[site]=' . trim($search['site']);
        }

        if (isset($search['ad_type']) && $search['ad_type']) {
            $where['ad_type'] = trim($search['ad_type']);
            $string[]           = 'search[ad_type]=' . trim($search['ad_type']);
        }

        if (isset($search['add_user']) && $search['add_user']) {
            $where['add_user'] = trim($search['add_user']);
            $string[]           = 'search[add_user]=' . trim($search['add_user']);
        }





        if (isset($search['erp_sku']) && $search['erp_sku']) {
            $id = $this->list->getProductIdWithSku(trim($search['erp_sku']), false);
            if ($id) {
                $in['id'] = $id;
            } else {
                $in['id'] = '0';
            }
            $string[]           = 'search[erp_sku]=' . trim($search['erp_sku']);
        }

        $where['status'] = 1;

        $search = $search ? $search : array();
        $curpage= 10;
        $orderBy = 'id DESC';
        //查询条件
        $options     = array(
            'select'   => "id,status,site,category_id,category_id_second,account_id,ad_type,title,erp_sku,ebay_sku,ebay_price,add_user,publish_error,detailPicList,payPalEmailAddress,creat_time",
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
      //  echo $this->db->last_query();exit;
     //   var_dump($data_array);

        $c_url = admin_base_url('ebay/ebay_product_list/draft_center');
        $url   = $c_url . '?' . implode('&', $string);

        $page = $this->sharepage->showPage($url, $return_data['total_rows'], $curpage);

        $data = array(
            'data'           =>$data_array,
            'search'             => $search,
            'page'               => $page,
            'totals'             => $return_data['total_rows'],
            'c_url'              => $c_url,
        );

        $data['site_array'] = $this->Ebay_ebaysite_model->getAllSiteName();
        $data['account_array'] = $this->Ebay_user_tokens_model->getAllAccount();
        $data['user_array'] = $this->slme_user_model->get_all_user_info('user_name');
        $data['account'] = $this->Ebay_user_tokens_model->getAllAccount();
        $data['user'] = $this->list->getALLUser();

        $this->_template('admin/ebay/ebay_list_draft.php',$data);
    }




    public function product_center(){
        $where    = array(); //查询条件
        $in       = array(); //in查询条件
        $like     = array(); //like查询条件
        $string   = array(); //URL参数
        $curpage  = (int)$this->config->item('site_page_num');
        $per_page = (int)$this->input->get_post('per_page');
        $search   = $this->input->get_post('search');
        //  var_dump($search);exit;
        if (isset($search['account_id']) && $search['account_id']) {
            $where['account_id'] = trim($search['account_id']);
            $string[]           = 'search[account_id]=' . trim($search['account_id']);
        }

        if (isset($search['site']) && $search['site']!=999) {
            $where['site'] = trim($search['site']);
            $string[]           = 'search[site]=' . trim($search['site']);
        }

        if (isset($search['ad_type']) && $search['ad_type']) {
            $where['ad_type'] = trim($search['ad_type']);
            $string[]           = 'search[ad_type]=' . trim($search['ad_type']);
        }

        if (isset($search['add_user']) && $search['add_user']) {
            $where['add_user'] = trim($search['add_user']);
            $string[]           = 'search[add_user]=' . trim($search['add_user']);
        }

        if (isset($search['itemid']) && $search['itemid']) {
            $where['itemid'] = trim($search['itemid']);
            $string[]           = 'search[itemid]=' . trim($search['itemid']);
        }

        if (isset($search['start_date']) && $search['start_date']) {
            $where['creat_time >= '] = trim($search['start_date']);
            $string[]           = 'search[start_date]=' . trim($search['start_date']);
        }


        if (isset($search['end_date']) && $search['end_date']) {
            $where['creat_time <= '] = trim($search['end_date']);
            $string[]           = 'search[end_date]=' . trim($search['end_date']);
        }

        if (isset($search['erp_sku']) && $search['erp_sku']) {
            $id = $this->list->getProductIdWithSku(trim($search['erp_sku']), true);
            if ($id) {
                $in['id'] = $id;
            } else {
                $in['id'] = '0';
            }
            $string[]           = 'search[erp_sku]=' . trim($search['erp_sku']);
        }


        $where['status !='] = 1;

        $search = $search ? $search : array();
        $curpage= 10;
        $orderBy = 'id DESC';
        //查询条件
        $options     = array(
            'select'   => "id,status,itemid,site,category_id,category_id_second,account_id,ad_type,title,erp_sku,ebay_sku,ebay_price,add_user,publish_error,detailPicList,payPalEmailAddress,creat_time",
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

        $c_url = admin_base_url('ebay/ebay_product_list/product_center');
        $url   = $c_url . '?' . implode('&', $string);

        $page = $this->sharepage->showPage($url, $return_data['total_rows'], $curpage);

        $data = array(
            'data'           =>$data_array,
            'search'             => $search,
            'page'               => $page,
            'totals'             => $return_data['total_rows'],
            'c_url'              => $c_url,
        );

        $data['site_array'] = $this->Ebay_ebaysite_model->getAllSiteName();
        $data['account_array'] = $this->Ebay_user_tokens_model->getAllAccount();
        $data['user_array'] = $this->slme_user_model->get_all_user_info('user_name');
        $data['account'] = $this->Ebay_user_tokens_model->getAllAccount();
        $data['user'] = $this->list->getALLUser();


        $this->_template('admin/ebay/ebay_list_product.php',$data);
    }

    public function copy_to_draft(){

        $ids = $_POST['productIds'];
        $id_array = explode(',',$ids);
        foreach($id_array as $id){
           $result = $this->list->getOne(array('where'=>array('id'=>$id)),true);
            if(!empty($result)){
                unset($result['itemid']);
                $result['status'] = 1;
                $this->list->add($result);

            }


        }
        ajax_return('success',1);

    }












}
