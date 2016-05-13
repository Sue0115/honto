<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/15
 * Time: 14:34
 */
set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");
class Ebaylist extends Admin_Controller{
    protected $ebay;
    protected $userToken;



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
            'ebay/Ebay_template_html_model',
            'ebay/Ebay_template_model',
            'sharepage'

            //  'smt/Slme_smt_categorylist_model',
            // 'smt/Slme_smt_category_attribute_model',
        ));
        $this->ebaytest = new MyEbayNew();
        $this->userToken = $this->Ebay_user_tokens_model;
        $this->category = $this->Ebay_categoty_model;
        $this->condition =$this->Ebay_condition_model;
        $this->ebaysite = $this->Ebay_ebaysite_model;
        $this->specifics =$this->Ebay_specifics_model;
        $this->details=$this->Ebay_ebaydetails_model;
        $this->ebaytemplate=$this->Ebay_template_model;
        $this->product = $this->Products_data_model;
        $this->country =$this->Ebay_country_model;
        $this->ebaylist = $this->Ebay_list_model;

        // $this->model     = $this->Slme_smt_categorylist_model;
    }


    public function ebaylistinfo()
    {
        $arr =array();
        $arr['where']['token_status'] =1;
        $data['token'] =  $this->userToken->getAll2Array($arr);

        $where    = array(); //查询条件
        $in       = array(); //in查询条件
        $like     = array(); //like查询条件
        $string   = array(); //URL参数
        $curpage  = (int)$this->config->item('site_page_num');
        $per_page = (int)$this->input->get_post('per_page');
        $search   = $this->input->get_post('search');


        if (isset($search['seller_account']) && $search['seller_account']) {
            $where['ebayaccount'] = trim($search['seller_account']);
            $string[]           = 'search[seller_account]=' . trim($search['seller_account']);
        }
        if (isset($search['site'])&&$search['site']!='999' ) {
            $where['site'] = trim($search['site']);
            $string[]           = 'search[site]=' . trim($search['site']);
        }
        if (isset($search['productStatusType']) && $search['productStatusType']) {
            $where['status'] = trim($search['productStatusType']);
            $string[]           = 'search[productStatusType]=' . trim($search['productStatusType']);
        }
        if (isset($search['itemId']) && $search['itemId']) {
            $where['itemid'] = trim($search['itemId']);
            $string[]           = 'search[itemId]=' . trim($search['itemId']);
        }
        if (isset($search['sku']) && $search['sku']) {
           // $like['sku'] = trim($search['sku']);
            $this->db->like('sku_search', $search['sku'], 'after');
            $string[]           = 'search[sku]=' . trim($search['sku']);
        }
        if (isset($search['subject']) && $search['subject']) {
            $where['title'] = trim($search['subject']);
            $string[]           = 'search[subject]=' . trim($search['subject']);
        }


        $search = $search ? $search : array();

        $orderBy = 'id DESC';
        //查询条件
        $options     = array(
            //'select'   => "{$this->model->_table}.*, s.*",
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
        $data_list   = $this->ebaylist->getAll2Array($options);
        $return_data['total_rows'] =$this->ebaylist->getTotal($options);


      // echo $this->db->last_query();exit;

        $c_url = admin_base_url('ebay/ebaylist/ebaylistinfo');
        $url   = $c_url . '?' . implode('&', $string);

        $page = $this->sharepage->showPage($url, $return_data['total_rows'], $curpage);


        $data = array(
            'infolist'           =>$data_list,
            'search'             => $search,

            'page'               => $page,
            'totals'             => $return_data['total_rows'],
            'c_url'              => $c_url,


        );


        $sitearr = array();
        $sitelast = array();
        $sitearr['where']['is_use'] = 0;
        $siteresult =$this->ebaysite->getAll2Array($sitearr);
        foreach($siteresult as  $re)
        {
            $sitelast[$re['siteid']]=$re['site'];
        }
        $data['sitearr'] = $sitelast;

        $arr =array();
        $arr['where']['token_status'] =1;
        $data['token'] =  $this->userToken->getAll2Array($arr);


        $this->_template('admin/ebay/ebaylist', $data);


    }

 


    public function ebaylistinfocheck()
    {



        $where    = array(); //查询条件
        $in       = array(); //in查询条件
        $like     = array(); //like查询条件
        $string   = array(); //URL参数
        $curpage  = (int)$this->config->item('site_page_num');
        $per_page = (int)$this->input->get_post('per_page');
        $search   = $this->input->get_post('search');


       // $where['status !='] = 2;


        if (isset($search['seller_account']) && $search['seller_account']) {
            $where['ebayaccount'] = trim($search['seller_account']);
            $string[]           = 'search[seller_account]=' . trim($search['seller_account']);
        }
        if (isset($search['site'])&&$search['site']!='999' ) {
            $where['site'] = trim($search['site']);
            $string[]           = 'search[site]=' . trim($search['site']);
        }
        if (isset($search['productStatusType']) && $search['productStatusType']) {
            $where['status'] = trim($search['productStatusType']);
            $string[]           = 'search[productStatusType]=' . trim($search['productStatusType']);
        }
        if (isset($search['itemId']) && $search['itemId']) {
            $where['itemid'] = trim($search['itemId']);
            $string[]           = 'search[itemId]=' . trim($search['itemId']);
        }
        if (isset($search['sku']) && $search['sku']) {
            $like['sku'] = trim($search['sku']);
            $string[]           = 'search[sku]=' . trim($search['sku']);
        }
        if (isset($search['subject']) && $search['subject']) {
            $where['title'] = trim($search['subject']);
            $string[]           = 'search[subject]=' . trim($search['subject']);
        }
        $search = $search ? $search : array();

        $orderBy = 'id DESC';
        //查询条件
        $options     = array(
            //'select'   => "{$this->model->_table}.*, s.*",
            'where'    => $where,
            'where_in' => $in,
            'page'     => 100,
            'per_page' => $per_page

        );
        if (!empty($like)){
            $options = array_merge($options, array('like' => $like));
        }


        $return_data = array('total_rows' => true);
        $data_list   = $this->ebaylist->getAll($options, $return_data);

        $c_url = admin_base_url('ebay/ebaylist/ebaylistinfocheck');
        $url   = $c_url . '?' . implode('&', $string);

        $page = $this->sharepage->showPage($url, $return_data['total_rows'], $curpage);


        $data = array(
            'infolist'           =>$data_list,
            'search'             => $search,

            'page'               => $page,
            'totals'             => $return_data['total_rows'],
            'c_url'              => $c_url,


        );


        $data['infolist'] = $data_list;
        $sitearr = array();
        $sitelast = array();
        $siteresult =$this->ebaysite->getEbaySiteAll($sitearr);
        foreach($siteresult as  $re)
        {
            $sitelast[$re['siteid']]=$re['site'];
        }
        $data['sitearr'] = $sitelast;

        $arr =array();
        $arr['where']['token_status'] =1;
        $data['token'] =  $this->userToken->getAll2Array($arr);

        $this->template('admin/ebay/ebay_list',$data);
    }
    public function batchdelete()
    {

        $ids=$_POST['productIds'];
        $idsarr  = explode(',',$ids);
        foreach($idsarr as $id)
        {

            $this->ebaylist->delect($id);
        }
        ajax_return('批量删除完成');
    }


    public function delete()
    {
       $id = $_POST['id'];
        $re = $this->ebaylist->delect($id);
        if($re)
        {
            echo json_encode(array('msg' => '删除成功', 'status' => 1));
        }
        else
        {
        echo json_encode(array('msg' => '删除失败', 'status' => 0));
         }

    }

    public function getListById()
    {
        $typename =array(
            'paimai'=>'拍卖',
            'guding'=>'固定',
            'duoshuxing'=>'多属性'
        );
        $sitearr = array();
        $sitelast = array();
        $siteresult =$this->ebaysite->getEbaySiteAll($sitearr);
        foreach($siteresult as  $re)
        {
            $sitelast[$re['siteid']]=$re['site'];
        }


        $ids = $_POST['ids'];
        $idsarr=  explode(',',$ids);

        array_filter($idsarr);
        $where_in = array();
        $where_in['id'] = $idsarr;
        $options['where_in'] = $where_in;
        $result = $this->ebaylist->getAll2Array($options);
        krsort($result);
        $i=0;
        $lastarr = array();
        foreach($result as $v)
        {
            $lastarr[$i]['id'] = $v['id'];
            $lastarr[$i]['name'] = $v['name'];
            $lastarr[$i]['ebayaccount'] = $v['ebayaccount'];
            $lastarr[$i]['site'] = $sitelast[$v['site']];
            $pic = json_decode($v['ebay_picture'],true);
            if(isset($pic[0]))
            {
                $lastarr[$i]['ebay_picture'] = $pic[0];
            }
            $lastarr[$i]['sku'] = $v['sku'];
            $lastarr[$i]['ad_type'] = $typename[$v['ad_type']];
            $lastarr[$i]['title'] = $v['title'];

            $i++;

        }
        ajax_return('',1,$lastarr);
    //   var_dump($lastarr);exit;

    }


    public function creatNewPublishTime()
    {
        $typename =array(
            'paimai'=>'拍卖',
            'guding'=>'固定',
            'duoshuxing'=>'多属性'
        );

        $sitearr = array();
        $sitelast = array();
        $siteresult =$this->ebaysite->getEbaySiteAll($sitearr);
        foreach($siteresult as  $re)
        {
            $sitelast[$re['siteid']]=$re['site'];
        }


        //各个站点 与中国的时间差
        $timearr=array(
            0=>13,// US
            2=>13,//Canada
            3=>8,//UK
            77=>7,//Germany
            15=>-2,//Australia
            71=>7,//France
            101=>7,//Italy
            186=>7 //Spain
        );

        $ids = $_POST['ids'];
        $dayfrequency = $_POST['dayfrequency'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];

        if($dayfrequency==1)
        {
            $day_start_date = $_POST['day_start_date'];
            $max_day =  $_POST['max_day'];
            $idsarr = explode(',', $ids);
            $idsarr = array_filter($idsarr);
            $idsarr = array_values($idsarr);

            $max_time = strtotime($end_time);
            $start_time = $start_time . ' ' . $day_start_date;
            for($i=0;$i<$max_day;$i++)
            {
                $every_time = strtotime($start_time) + $i * 24 * 60 * 60;
                if($every_time<$max_time){
                    $new_time[] = date('Y-m-d H:i:s', $every_time);
                }

            }

            $where_in = array();
            $where_in['id'] = $idsarr;
            $options['where_in'] = $where_in;
            $result = $this->ebaylist->getAll2Array($options);
            krsort($result);
            $lastarr = array();
            $i=0;
            foreach($result as $v)
            {
                foreach($new_time as $pub_time)
                {
                    $lastarr[$i]['id'] = $v['id'];
                    $lastarr[$i]['name'] = $v['name'];
                    $lastarr[$i]['ebayaccount'] = $v['ebayaccount'];
                    $lastarr[$i]['site'] = $sitelast[$v['site']];
                    $pic = json_decode($v['ebay_picture'], true);
                    if (isset($pic[0])) {
                        $lastarr[$i]['ebay_picture'] = $pic[0];
                    }
                    $lastarr[$i]['sku'] = $v['sku'];
                    $lastarr[$i]['ad_type'] = $typename[$v['ad_type']];
                    $lastarr[$i]['title'] = $v['title'];
                    $lastarr[$i]['site_publish_time'] = $pub_time;
                    $local_publish_time = strtotime($lastarr[$i]['site_publish_time']) + $timearr[$v['site']] * 60 * 60;
                    $lastarr[$i]['local_publish_time'] = date('Y-m-d H:i:s', $local_publish_time);
                    $i++;
                }
                $i++;
            }
        }

        if($dayfrequency==2) {
            $day_start_date = $_POST['day_start_date'];
            $interval_time = $_POST['interval_time'];
            $max_num = $_POST['max_num'];
            $start_time = $start_time . ' ' . $day_start_date;
            $idsarr = explode(',', $ids);
            $idsarr = array_filter($idsarr);
            $idsarr = array_values($idsarr);
            $mid_time = (strtotime($end_time) - strtotime($start_time)) / (24 * 60 * 60);
            $new_time = array();
            $every_time = 0;
            for ($i=0; $i < $mid_time; $i++) {
                for ($j=0; $j < $max_num; $j++) {
                    if ($j == 0) //每天一次
                    {
                        if ($i == 0) {
                            $every_time = strtotime($start_time) + $i * 24 * 60 * 60;
                            $new_time[] = date('Y-m-d H:i:s', $every_time);
                        } else {
                            $every_time = strtotime($start_time) + $i * 24 * 60 * 60;
                            $new_time[] = date('Y-m-d H:i:s', $every_time);
                        }

                    } else {
                        $every_time = $every_time + ($interval_time) * 60;
                        $new_time[] = date('Y-m-d H:i:s', $every_time);
                    }
                }
            }
            $frequency = ceil(count($new_time) / count($idsarr));
            $publish_time = array();
            $j = 0;
            foreach ($new_time as $key => $v) {
                if ($key == count($idsarr)) {
                    break;
                }
                $publish_time[$idsarr[$j]]['site_publish_time'] = $v;
                $j++;
            }
            unset($new_time);
            $where_in = array();
            $where_in['id'] = $idsarr;
            $options['where_in'] = $where_in;
            $result = $this->ebaylist->getAll2Array($options);
            krsort($result);
            $i = 0;
            $lastarr = array();
            foreach ($result as $v) {
                if (isset($publish_time[$v['id']])) {
                    $lastarr[$i]['id'] = $v['id'];
                    $lastarr[$i]['name'] = $v['name'];
                    $lastarr[$i]['ebayaccount'] = $v['ebayaccount'];
                    $lastarr[$i]['site'] = $sitelast[$v['site']];
                    $pic = json_decode($v['ebay_picture'], true);
                    if (isset($pic[0])) {
                        $lastarr[$i]['ebay_picture'] = $pic[0];
                    }
                    $lastarr[$i]['sku'] = $v['sku'];
                    $lastarr[$i]['ad_type'] = $typename[$v['ad_type']];
                    $lastarr[$i]['title'] = $v['title'];
                    $lastarr[$i]['site_publish_time'] = $publish_time[$v['id']]['site_publish_time'];
                    $local_publish_time = strtotime($lastarr[$i]['site_publish_time']) + $timearr[$v['site']] * 60 * 60;
                    $lastarr[$i]['local_publish_time'] = date('Y-m-d H:i:s', $local_publish_time);
                    $i++;
                }
            }
        }


        ajax_return('',1,$lastarr);

    }

    public function creatEbaylistTask()
    {
        $task = $_POST['task'];
        $local_publish_time = $_POST['local_publish_time'];
        $site_publish_time = $_POST['site_publish_time'];

        for($i=0;$i<count($task);$i++)
        {
            $taskinfo = array();
            $taskinfo['list_id'] = $task[$i];
            $taskinfo['local_time'] = $local_publish_time[$i];
            $taskinfo['publish_time'] = $site_publish_time[$i];
            $taskinfo['status'] = 1;
            $taskinfo['user_id']=$this->user_info->id;//登录用户id
            $this->Ebay_task_list_model->add($taskinfo);
        }
        ajax_return('',1);
    }

    //在线编辑

    public function modifyActiceListting()
    {

        $data= array();
        if(isset($_GET['type']))
        {
            $ids= $_GET['ids'];
            $data['ids'] = $ids;

            $option = array();
            $model_result = $this->Ebay_template_html_model->getAll2Array($option);
            $sell_detail  = $this->Ebay_template_model->getAll2Array($option);
            $data['model_result'] =$model_result;
            $data['sell_detail'] =$sell_detail;

        }


        $this->template('admin/ebay/ebay_batch_modify',$data);
    }

    public  function test()
    {
        $data =array();

            $datainfo = $_POST;
            $result = $this->modifyDescription($datainfo);
            $data['result'] = $result;


        $this->template('admin/ebay/ebay_batch_modify',$data);
    }

    public  function  modifyDescription($data)
    {
        $ids = $data['ids'];
        $idarray = explode(',',$ids);
        foreach($idarray as $id)
        {
            $option = array();
            $option['where']['id'] = $id;
            $list_one = $this->ebaylist->getOne($option,true);
            $result = $this->userToken->getInfoByAccount($list_one['ebayaccount']);
            $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$list_one['site'],'ReviseItem');


            if(!empty($list_one['publication_template_html'])){     //如果不为空 说明不是PA上传的产品 或者是没同步过的
                $list_one =  $this->reassemble_info($list_one);
                $list_one['description_details']= $list_one['description_details_new'];

            }
            $description_details = $list_one['description_details'];

            $first_1= stripos($description_details,'<div class="Pa_Demo">');
            $first_2= stripos($description_details,'<div class="Pa_BboxL">');
            $mid  =$first_2-$first_1;

            $new_details = '<div class="Pa_Demo"><span class="Pa_DemoT">'.$data['detail'].'</span></div><div class="Pa_MboxR"></div>';
            $description_details = substr_replace($description_details,$new_details,$first_1,$mid);

            $modify_result =  $this->ebaytest->modifyDescription($list_one['itemid'],$description_details);
            if(isset($modify_result->ItemID))
            {
                $updata_option = array();
                $updata_data = array();
                $updata_option['where']['id'] = $id;
                $updata_data['description_details'] = $description_details;
                $this->ebaylist->update($updata_data,$updata_option);
            }else{

            }

        }

    }


    public function reassemble_info($info)  //将某些数据  重新组装一下
    {

        //组装描述信息
        $template['where']['id'] = $info['publication_template']; // 卖家描述
        $resulttemplate = $this->ebaytemplate->getOne($template,true);

        $templatehtml['where']['id'] = $info['publication_template_html']; //模板详情
        $resulttemplatehtml = $this->Ebay_template_html_model->getOne($templatehtml,true);


        $info['description_details_new'] = '';
        if (!empty($resulttemplatehtml)) {

            if (!empty($resulttemplate)) // 将固定字符替换成对应的信息
            {

                $resulttemplatehtml['template_html'] = str_replace('{{tittle}}', $info['template_title'], $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{paymentterms}}', $resulttemplate['payment'], $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{termsofsales}}', $resulttemplate['sales_policy'], $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{shippingterms}}', $resulttemplate['shipping'], $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{contactus}}', $resulttemplate['contact_us'], $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{aboutus}}', $resulttemplate['about_us'], $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{description}}', $info['description_details'], $resulttemplatehtml['template_html']);

            } else  // 卖家描述为空 将固定字符 替换成空字符
            {
                $resulttemplatehtml['template_html'] = str_replace('{{tittle}}', $info['template_title'], $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{paymentterms}}', '', $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{termsofsales}}', '', $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{shippingterms}}', '', $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{contactus}}', '', $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{aboutus}}', '', $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{description}}', $info['description_details'], $resulttemplatehtml['template_html']);
            }

            if (!empty($info['template_deteils']))  // 对模板里面的图片进行处理
            {
                $picarr = json_decode($info['template_deteils'], true);
                $picbig = '';
                $picsamll = '';
                for ($i = 0; $i < count($picarr); $i++) {
                    if ($i == 0) {
                        $picbig = '<div class="albumBigImgBox"><img width="600" src="' . $picarr[$i] . '" alt="" name="bigImg" id="bigImg"></div>';
                        $picsamll = $picsamll . '<div alt="" class="smallImgBox"><img width="92" height="92" alt=""  onmouseover="document.getElementById(' . "'bigImg'" . ').src=this.src"   src="' . $picarr[$i] . '"></div>';
                    } else {
                        $picsamll = $picsamll . '<div alt="" class="smallImgBox"><img width="92" height="92" alt=""  onmouseover="document.getElementById(' . "'bigImg'" . ').src=this.src"   src="' . $picarr[$i] . '"></div>';
                    }
                }

                $resulttemplatehtml['template_html'] = str_replace('{{Picture1}}', $picbig, $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{Picture2}}', $picsamll, $resulttemplatehtml['template_html']);
            } else {
                $resulttemplatehtml['template_html'] = str_replace('{{Picture1}}', '', $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{Picture2}}', '', $resulttemplatehtml['template_html']);
            }
            $info['description_details_new'] = $resulttemplatehtml['template_html'];

        } else //没有选择模板  无法加入模板图片
        {
            if (!empty($resulttemplate)) {

                $templateinfo = '<h2><span class="Pa_head"><span class="Pa_headc">Payment Method <hr /></span></span></h2>' . $resulttemplate['payment'] . '<h2> <span class="Pa_head"><span class="Pa_headc">Shipping Detail<hr /> </span></span> </h2>' . $resulttemplate['shipping'] . '<h2> <span class="Pa_head"><span class="Pa_headc"> Sales Policy<hr /> </span></span> </h2>' . $resulttemplate['sales_policy'] . '<h2> <span class="Pa_head"><span class="Pa_headc">About Us<hr /> </span></span> </h2>' . $resulttemplate['about_us'] . '<h2> <span class="Pa_head"><span class="Pa_headc"> Contact Us <hr /> </span></span> </h2>' . $resulttemplate['contact_us'];
                $info['description_details_new'] = $info['description_details'] . $templateinfo;

            } else // 没有选择卖家描述 。
            {

                $info['description_details_new'] = $info['description_details'];
            }
        }
        return $info;
    }
}