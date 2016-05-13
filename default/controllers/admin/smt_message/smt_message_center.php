<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-10-05
 * Time: 16:01
 */


header("content-type:text/html; charset=utf-8");
class Smt_message_center extends Admin_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->library('MySmt');
        $this->load->library('Mybaidu_transapi');
        $this->load->model(array(
            'smt/Smt_user_tokens_model',
            'smt_message/smt_msg_detail_model',
            'smt_message/smt_msg_list_model',
            'smt_message/smt_msg_reply_model',
            'smt_message/email_mod_model',
            'smt_message/email_mod_class_model',
            'order/orders_model',
            'order/orders_products_model',
            'products/products_data_model',
            'shipment_model',
            'slme_user_model',
            'sharepage'
        ));
        $this->smt = new MySmt();
    }



    public function message_center()
    {



        $where    = array(); //查询条件
        $in       = array(); //in查询条件
        $like     = array(); //like查询条件
        $string   = array(); //URL参数
        $curpage  = (int)$this->config->item('site_page_num');
        $per_page = (int)$this->input->get_post('per_page');
        $search   = $this->input->get_post('search');
      //  var_dump($search);exit;
        if (isset($search['token_id']) && $search['token_id']) {
            $where['token_id'] = trim($search['token_id']);
            $string[]           = 'search[token_id]=' . trim($search['token_id']);
        }


        if (isset($search['messageType']) && $search['messageType']) {
            $where['messageType'] = trim($search['messageType']);
            $string[]           = 'search[messageType]=' . trim($search['messageType']);
        }
        else
        {
            $where['messageType']='order_msg';
        }

        if (isset($search['otherName']) && $search['otherName']) {
            $where['otherName'] = urldecode(trim($search['otherName']));
            $string[]           = 'search[otherName]=' . trim($search['otherName']);
        }

        if (isset($search['channelId']) && $search['channelId']) {
            $where['channelId'] = trim($search['channelId']);
            $string[]           = 'search[channelId]=' . trim($search['channelId']);
        }

        if (isset($search['import_start']) && $search['import_start']) {
            $where['messageTime >'] = trim($search['import_start']);
            $string[]           = 'search[import_start]=' . trim($search['import_start']);
        }

        if (isset($search['import_end']) && $search['import_end']) {
            $where['messageTime <'] = trim($search['import_end']);
            $string[]           = 'search[import_end]=' . trim($search['import_end']);
        }


        if (isset($search['start_export']) && $search['start_export']) {
            $where['exporttime >'] = trim($search['start_export']);
            $string[]           = 'search[start_export]=' . trim($search['start_export']);
        }

        if (isset($search['end_export']) && $search['end_export']) {
            $where['exporttime <'] = trim($search['end_export']);
            $string[]           = 'search[end_export]=' . trim($search['end_export']);
        }


        if (isset($search['chuli'])) {
            $where['isReturn'] = trim($search['chuli']);
            $string[]           = 'search[chuli]=' . trim($search['chuli']);
        }


        if (isset($search['isread'])) {
            $where['isRead'] = trim($search['isread']);
            $string[]           = 'search[isread]=' . trim($search['isread']);
        }


        if (isset($search['reply_no'])) {
            $where['reply_no'] = trim($search['reply_no']);
            $string[]           = 'search[reply_no]=' . trim($search['reply_no']);
        }
        else
        {
            $where['reply_no'] = 0;
        }


        $search = $search ? $search : array();
        $curpage = 50;
        $orderBy = 'messageTime DESC';
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
        $data_array   = $this->smt_msg_list_model->getAll($options, $return_data);

/*        echo $this->db->last_query();
exit;*/
//smt_message/smt_message_center/message_center
        $c_url = admin_base_url('smt_message/smt_message_center/message_center');
        $url   = $c_url . '?' . implode('&', $string);

        $page = $this->sharepage->showPage($url, $return_data['total_rows'], $curpage);


        $data = array(
            'data'           =>$data_array,
            'search'             => $search,

            'page'               => $page,
            'totals'             => $return_data['total_rows'],
            'c_url'              => $c_url,


        );

        $token_option = array();
        $token_option['where']['token_status'] = 0;
        $token_arr = $this->Smt_user_tokens_model->getAll2Array($token_option);




        $smt_user_options = array(
            'select' => 'token_id, seller_account,accountSuffix',
            'where'  => array('token_status' => 0),
        );
        //速卖通账号
        $token_array = $this->Smt_user_tokens_model->getSmtTokenList($smt_user_options);

        $data['token_account'] = $token_array;
        $data['token'] =$token_arr;
        $this->_template('admin/smt_message/smt_message_center',$data);
    }



    //show_detail

    public function show_detail()
    {

        $id = $_GET['id'];

        $option= array();
        $option['where']['id'] = $id;

        $result =$this->smt_msg_list_model->getOne($option,true);

        $channelId=$result['channelId'];
        $otherLoginId = $result['otherLoginId'];
        $messageType = $result['messageType'];
        $otherName = $result['otherName'];


      /*  $token_arr = $this->Smt_user_tokens_model->getOneTokenInfo($result['token_id']);
        $this->smt->setToken($token_arr);
        $method='api.updateMsgRead';
        $channelId_api = rawurldecode($result['channelId']);
        $msgSources = rawurldecode($result['messageType']);
        $para='channelId='.$channelId_api.'&msgSources='.$msgSources;
        $listArrJson_result =   $this->smt->getJsonData($method,$para);*/

        $updat_arr=array();
        $updat_option=array();
        $updat_option['where']['id'] = $id;

        $updat_arr['isRead'] = 1;
        $this->smt_msg_list_model->update($updat_arr,$updat_option);




        $data=array();

        $option = array();
        $option['where']['channelId'] =$channelId ;
        $option['order'] ='gmtCreate asc ';


        $result = $this->smt_msg_detail_model->getAll2Array($option);



        $token_option = array();
        $token_option['where']['token_status'] = 0;
        $token_arr = $this->Smt_user_tokens_model->getAll2Array($token_option);

        $token_account = array();

        foreach($token_arr as $v)
        {
            $token_account[$v['token_id']]['seller_account'] = $v['seller_account'];
            $token_account[$v['token_id']]['seller_account'] = $v['seller_account'];
        }


        //
        $user_option = array();
        $user_arr = $this->slme_user_model->getAll2Array($user_option);

        $user_info = array();

        foreach($user_arr as $v)
        {
            $user_info[$v['id']] = $v['nickname'];
        }

        unset($user_arr);

        // 获取模板信息
        $mod_option = array();
        $mod_option['where']['platform'] ='SMT';

        $result_mod = $this->email_mod_class_model->getAll2Array($mod_option);


        $data['data'] = $result;
        $data['channelId'] = $channelId;
        $data['otherLoginId'] = $otherLoginId;
        $data['messageType'] = $messageType;
        $data['list_id'] = $id;
        $data['user_info'] = $user_info;
        $data['result_mod'] = $result_mod;
        $data['otherName']=$otherName;

        unset($result_mod);


        if($messageType=='order_msg')
        {
            $last_order_info = array();

            $option = array();
            $option['where']['buyer_id'] = $channelId;
            $option['where']['orders_is_join'] = 0;
            $order_result = $this->orders_model->getAll2Array($option);

            foreach($order_result as $order)
            {

                $result =  $this->smt_msg_list_model->check_issue($order['buyer_id']);
                $order['issue'] =$result[0]['num'];
                unset($result);

                $result =   $this->smt_msg_list_model->track_info($order['orders_shipping_code']);
                $order['tracking_result'] = $result[0];

                unset($result);

                $result = $this->smt_msg_list_model->getOrderMoneyBackInfo($order['erp_orders_id']);
                $order['moneyBackInfo'] = $result;

                unset($result);
                $option =array();
                $option['where']['erp_orders_id'] = $order['erp_orders_id'];
                $product_result = $this->orders_products_model->getAll2Array($option);

                $product_result_new = array();
                foreach($product_result as  $product)
                {
                    $option = array();
                    $option['where']['products_sku'] = $product['orders_sku'];

                    $sku  = $this->products_data_model->getOne($option,true);

                    $product['products_name_cn'] = $sku['products_name_cn'];
                    $product['products_id'] = $sku['products_id'];

                    $product_result_new[] = $product;
                }

                unset($product_result);
                $order['product_result'] = $product_result_new;

                $last_order_info[] = $order;
            }

            $data['last_order_info'] =$last_order_info;
            unset($last_order_info);
        }

        //shipment_model
        $shipment_option = array();
        $shipment_info = array();
        $result_shipment = $this->shipment_model->getAll2Array($shipment_option);
        foreach($result_shipment as $shipment)
        {
            $shipment_info[$shipment['shipmentID']] = $shipment;
        }


        $data['shipment_info'] = $shipment_info;


        $orders_status_arr              = $this-> orders_status_select();
        $orderStatusArray               = $orders_status_arr; //orders_status_select()
        $statusArray                    = array( );
        foreach ( $orderStatusArray as $nus ) {
            $statusArray[$nus['key']] = $nus;
        }

        $data['statusArray'] =$statusArray;


        $this->template('admin/smt_message/smt_message_detail',$data);
    }


    public function addMsg()
    {


        $detail_id = $_POST['detail_id'];
        $id = $_POST['list_id'];
        $contentinfo = trim($_POST['content']);


        $option = array();
        $option['where']['id'] = $id;
        $result =$this->smt_msg_list_model->getOne($option,true);

        $token_arr = $this->Smt_user_tokens_model->getOneTokenInfo($result['token_id']);

      //  var_dump($token_arr);exit;
        $this->smt->setToken($token_arr);
        $method='api.updateMsgRead';
        $channelId = rawurldecode($result['channelId']);
        $msgSources = rawurldecode($result['messageType']);
        $para='channelId='.$channelId.'&msgSources='.$msgSources;
        $listArrJson_result =   $this->smt->getJsonData($method,$para);





        $content = rawurlencode($contentinfo);
        $channelId = rawurlencode($result['channelId']);
        $buyerId= rawurlencode($result['otherLoginId']);
        $msgSources = rawurlencode($result['messageType']);
        $method ='api.addMsg';
        $para ="channelId=$channelId&buyerId=$buyerId&msgSources=$msgSources&content=$content";

        $listArrJson =   $this->smt->getJsonData($method,$para);
        $listArr = json_decode($listArrJson,true);
        if(isset($listArr['result'])) // 调用api.addMsg成功 是否成功不确定
        {
            if($listArr['result']["isSuccess"]) // 增加信息成功
            {
                $uid = $this->user_info->id;//登录用户的信息
                $update_info = array();
                $update_info['where']['datail_id'] = $detail_id;
                $update_info['where']['channelId'] = $result['channelId'];
                $update_data = array();
                $update_data['reply_id'] = $uid;
                $this->smt_msg_detail_model->update($update_data,$update_info);

                unset($update_info);
                unset($update_data);

                $up_option = array();
                $up_data =array();
                $up_option['where']['id'] = $id;
                $up_data['isReturn'] = 1;
                $this->smt_msg_list_model->update($up_data,$up_option);

                unset($up_option);
                unset($up_data);

                $add_arr =array();

                $add_arr['user_id'] = $uid;
                $add_arr['datail_id'] = $detail_id;
                $add_arr['channelId'] = $result['channelId'];
                $add_arr['reply_time'] = date('Y-m-d H:i:s',time());

                $this->smt_msg_reply_model->add($add_arr);
                unset($add_arr);

                //$this->getMsgDetail($result['channelId'],$result['messageType'],$result['token_id']); 取消

                ajax_return('回复成功',1);

            }
            else //失败
            {
                $error ='';
                if(isset($listArr['result']['errorMsg']))
                {
                    $error = $listArr['result']['errorMsg'];
                }
                ajax_return('回复失败'.$error,2);
            }
        }
        else //调用apishib
        {
            ajax_return('回复失败,接口调用失败',2);
        }
    }

    public function getMsgDetail($channelId,$msgSources,$token_id)
    {
        $page1 =1; // 暂时设置1 。;
        $pageSize=100;
        $methodNew = 'api.queryMsgDetailList';

        $para ="currentPage=$page1&pageSize=$pageSize&msgSources=$msgSources&channelId=$channelId";
        $detailArrJson=$this->smt->getJsonData($methodNew,$para);
        $detailArr =json_decode($detailArrJson,true);


        if(isset($detailArr['result'])&&!empty($detailArr['result'])) // 不为空
        {
            foreach($detailArr['result'] as $detail)
            {
                $msg_detailarr = array();
                $msg_detailarr['summary'] = json_encode($detail['summary']);
                $msg_detailarr['content'] = addslashes($detail['content']);
                $msg_detailarr['datail_id'] = $detail['id'];
                $msg_detailarr['senderName'] = $detail['senderName'];
                $msg_detailarr['gmtCreate'] = $this->changetime($detail['gmtCreate']);
                $msg_detailarr['filePath'] = json_encode($detail['filePath']);
                $msg_detailarr['messageType'] = $detail['messageType'];
                $msg_detailarr['typeId'] = $detail['typeId'];
                $msg_detailarr['channelId'] = $channelId;
                $msg_detailarr['token_id'] =$token_id;

                $check_option = array();

                $check_option['where']['datail_id'] = $detail['id'];
                $check_option['where']['token_id'] = $token_id;

                $one_detail = $this->smt_msg_detail_model->getOne($check_option,true);

                if(empty($one_detail))
                {
                    $this->smt_msg_detail_model->add($msg_detailarr);
                }
            }
        }
    }

    public function getModDetail()
    {
        $option = array();
        $option['where']['modClassID'] = $_POST['email_mod_class_id'];
        $option['where']['modEnable'] = 1;

        $result = $this->email_mod_model->getAll2Array($option);
        $last_result = '<option value="">==选择常用模板==</option>';
        foreach($result as $v)
        {
            $last_result =$last_result.'<option value="'.$v['modID'].'" >'.$v['modTitle'].'</option>';
        }

        ajax_return($last_result,1);

    }

    public function getModDetailInfo()
    {
        $option = array();
        $option['where']['modID'] = $_POST['modID'];
        $result = $this->email_mod_model->getOne($option,true);
        ajax_return($result['modContent'],1);
    }
    //批量标记为已读
    public function batchUpdateMsgRead()
    {
        $ids = $_POST['ids'];
        $type =isset($_POST['type'])?$_POST['type']:0;

        $id_arr = explode(',',$ids);


        // 先更新为已读
        $update_option  =array();
        $update_date = array();
        $update_option['where_in']['id'] = $id_arr;
        $update_date['isRead'] = 1;

       $last_result = $this->updateMsgRead($id_arr);

        if($type==4)
        {
            $update_date['isReturn'] = 1;
        }

        if($type==5)
        {
            $update_date['isReturn'] = 1;
            $update_date['reply_no'] = 1;
        }
        $this->smt_msg_list_model->update($update_date,$update_option);

        ajax_return('操作完成',1);
    }
    //API 站内信/订单留言更新已读
    public function updateMsgRead($id_arr)
    {


        $method = 'api.updateMsgRead';

        $success=array();
        foreach($id_arr as $id)
        {

            $option_list = array();
            $option_list['where']['id'] = $id;

            $list_one = $this->smt_msg_list_model->getOne($option_list,true);

            $channelId = rawurldecode($list_one['channelId']);
            $msgSources = rawurldecode($list_one['messageType']);
            $para='channelId='.$channelId.'&msgSources='.$msgSources;
            $token_arr = $this->Smt_user_tokens_model->getOneTokenInfo($list_one['token_id']);
            $this->smt->setToken($token_arr);
            $listArrJson =   $this->smt->getJsonData($method,$para);
            $listArr = json_decode($listArrJson,true);
            if(isset($listArr['result'])&&$listArr['result']['isSuccess'])
            {
                $success[]=$id;
            }
            unset($option_list);
        }
        return $success;



    }

    public function changetime($time)
    {

        $time = date('Y-m-d H:i:s',substr($time,0,10));

        return $time;
    }


   public function orders_status_select() {
        $options[] = array(
            'key' => '1',
            'text' => '新录入',
            'color' => '#ff9900'
        );
        $options[] = array(
            'key' => '2',
            'text' => '不通过',
            'color' => '#ff0000'
        );
        $options[] = array(
            'key' => '3',
            'text' => '已通过',
            'color' => 'blue'
        );
        $options[] = array(
            'key' => '4',
            'text' => '已打印',
            'color' => '#000000'
        );
        $options[] = array(
            'key' => '5',
            'text' => '已发货',
            'color' => '#666666'
        );
        $options[] = array(
            'key' => '6',
            'text' => '已撤单',
            'color' => '#666fff'
        );
        $options[] = array(
            'key' => '7',
            'text' => '未付款',
            'color' => 'darksalmon'
        );
        $options[] = array(
            'key' => '8',
            'text' => '已发货[FBA]',
            'color' => 'darksalmon'
        );
        $options[] = array(
            'key' => '9',
            'text' => '预打印',
            'color' => '#FFFF00'
        );
        return $options;
    }


    public function getShipmentInfo(){
        $num = trim($_GET['num']);

        $client = new SoapClient('http://120.24.100.157:70/service/track.wsdl');
        $result = $client->Get($num);
        $re = json_decode($result,true);

        $result = array();//存放结果数据

        $data = array();

        $result = $re[0];

        $data['result'] = $result;
        $this->template('admin/smt_message/wish_message_shipmentCheck',$data);

    }


    public function testbaidu(){
        $text =$_POST['info'];
        $baidu = new Mybaidu_transapi();
        $result =  $baidu->translate($text);
        if(isset($result['trans_result'][0]['dst'])){
            ajax_return($result['trans_result'][0]['dst'],1);
        }else{
            ajax_return('',2);
        }

    }
}