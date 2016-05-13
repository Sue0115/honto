<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-08-05
 * Time: 11:20
 */
set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");
class Ebay_timing extends Admin_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->library('MyEbayNew');
        $this->load->model(array(
            'ebay/Ebay_user_tokens_model',
            'ebay/Ebay_categoty_model',
            'ebay/Ebay_condition_model',
            'ebay/Ebay_ebaysite_model',
            'ebay/Ebay_specifics_model',
            'ebay/Ebay_ebaydetails_model',
            'ebay/Ebay_template_model',
            'products/Products_data_model',
            'ebay/Ebay_country_model',
            'ebay/Ebay_list_model',
            'ebay/Ebay_task_list_model',
            'slme_user_model',
            'sharepage'

            //  'smt/Slme_smt_categorylist_model',
            // 'smt/Slme_smt_category_attribute_model',
        ));

        $this->ebaytest = new MyEbayNew();
        $this->userToken = $this->Ebay_user_tokens_model;
    }

    public function setEbayTiming()
    {
        $this->_template('admin/ebay/ebayTiming');
    }

    public function addEbayTiming()
    {
        $this->_template('admin/ebay/ebayTimingAdd');
    }


    public function ebayTaskList()
    {
        // var_dump($_GET);exit;

        $string = '1=1';

        $return_arr = array ('total_rows' => true );
        $arr =array();
        $arr['where']['token_status'] =1;
       $data['token'] =  $this->userToken->getAll2Array($arr);
        $where = array();

        $like  = array();

        $where_not_in = array();

        $search = $this->input->get_post('search');



        if (isset($search['seller_account']) && $search['seller_account']) {
            $where['ebayaccount'] = trim($search['seller_account']);
            $string           .= '&search[seller_account]=' . trim($search['seller_account']);
        }
        if (isset($search['site'])&&$search['site']!='999' ) {
            $where['site'] = trim($search['site']);
            $string           .= '&search[site]=' . trim($search['site']);
        }
        if (isset($search['productStatusType']) && $search['productStatusType']) {
            $where['status'] = trim($search['productStatusType']);
            $string           .= '&search[productStatusType]=' . trim($search['productStatusType']);
        }
        if (isset($search['itemId']) && $search['itemId']) {
            $where['itemid'] = trim($search['itemId']);
            $string           .= '&search[itemId]=' . trim($search['itemId']);
        }
        if (isset($search['sku']) && $search['sku']) {
            $like['sku'] = trim($search['sku']);
            $string           .= '&search[sku]=' . trim($search['sku']);
        }
        if (isset($search['subject']) && $search['subject']) {
            $where['title'] = trim($search['subject']);
            $string           .= '&search[subject]=' . trim($search['subject']);
        }

        if (isset($search['user_id']) && $search['user_id']) {
            $where['user_id'] = trim($search['user_id']);
            $string           .= '&search[user_id]=' . trim($search['user_id']);
        }

        $search = $search ? $search : array();
        $per_page	= (int)$this->input->get_post('per_page');

        $url = admin_base_url('ebay/ebay_timing/ebayTaskList?').$string;

        $cupage	= intval($this->config->item('site_page_num')); //每页显示个数
        //  $cupage =2;
        $orderBy = 'id DESC';

        $select = array("{$this->Ebay_task_list_model->_table}.*",'p.ad_type','p.name','p.site','p.ebayaccount','p.sku','p.title','p.ebay_picture','p.itemid','p.failurereasons');

        $join[] = array($this->Ebay_list_model->_table.' p',"p.id={$this->Ebay_task_list_model->_table}.list_id");


        $options	= array(
            'select' =>$select,
            'page'		   => $cupage,
            'per_page'	   => $per_page,
            'where'		   => $where,
            'where_not_in' => $where_not_in,
            'like'		   => $like,
            'join' => $join
          //  'order' => $orderBy,

        );


        $result  = $this->Ebay_task_list_model->getAll($options, $return_arr);

        $data['search'] = $search;
        $data['infolist'] = $result;
        $sitearr = array();
        $sitelast = array();
        $siteresult =$this->Ebay_ebaysite_model->getEbaySiteAll($sitearr);
        foreach($siteresult as  $re)
        {
            $sitelast[$re['siteid']]=$re['site'];
        }
        $data['sitearr'] = $sitelast;

        $user_option = array();
        $user_option['where']['status'] =1;
        $user_last =array();
        $user_result = $this->slme_user_model->getAll2Array($user_option);
        foreach($user_result as $v)
        {
            $user_last[$v['id']]  = $v['nickname'];
        }
        $data['user_last'] = $user_last;
        unset($user_result);

        $task_user_option = array();
        $select = array("  user_id");
        $task_user_option	= array(
            'select' =>$select,
            'distinct'=> 'distinct'
        );
        $data['all_user'] = $this->Ebay_task_list_model->getAll2Array($task_user_option);

        //$return_arr ['total_rows'] =count($result);

        $data['page'] = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
        $this->_template('admin/ebay/ebay_task_list',$data);
    }


    public function delete()
    {
        $option['where']['id'] = $_POST['id'];
        $re = $this->Ebay_task_list_model->delete($option);
        if($re)
        {
            echo json_encode(array('msg' => '删除成功', 'status' => 1));
        }
        else
        {
            echo json_encode(array('msg' => '删除失败', 'status' => 0));
        }
    }

}