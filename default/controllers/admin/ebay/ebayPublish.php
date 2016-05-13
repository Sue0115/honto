<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/8
 * Time: 9:35
 */
set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");
class EbayPublish extends Admin_Controller{
    protected $ebay;
    protected $userToken;
    public $model;
    function __construct()
    {
        parent::__construct();
        $this->load->library('MyEbayNew');
        $this->load->model(array(
            'ebay/Ebay_user_tokens_model',
            'ebay/Ebay_template_model',
            'ebay/Ebay_ebaysite_model',
            'ebay/Ebay_list_model',
            'ebay/Ebay_template_html_model',
            'ebay/Ebay_operationlog_model',
            'ebay/Ebay_task_list_model'
            //  'smt/Slme_smt_categorylist_model',
            // 'smt/Slme_smt_category_attribute_model',
        ));
        $this->ebaytest = new MyEbayNew();
        $this->userToken = $this->Ebay_user_tokens_model;
        $this->ebaytemplate=$this->Ebay_template_model;
        $this->ebaysite = $this->Ebay_ebaysite_model;
        $this->list = $this->Ebay_list_model;
        $this->ebaytemplatehtml=$this->Ebay_template_html_model;
        $this->operatelog = $this->Ebay_operationlog_model;

    }

    public function publish()
    {

        $info = array();
        $info['name'] = trim($_POST['mingcheng']);
        $info['ebayaccount'] = trim($_POST['ebayaccount']);
        $info['site'] = $_POST['siteid'];
        $info['ad_type'] = $_POST['leixing'];
        $info['sku'] = strtoupper(trim($_POST['sku']));

        $n = strpos($info['sku'], '*');
        $sku_new = $n !== false ? substr($info['sku'], $n+1) : $info['sku'];

        // 去除sku的帐户代码
        $n = strpos($sku_new, '#');
        $sku_new = $n !== false ? substr($sku_new, 0, $n) : $sku_new;


        if ( strpos($sku_new, '(') !== false ) {
            $matches = array();
            preg_match_all("/(.*?)\([a-z]?([0-9]*)\)?/i", $sku_new, $matches);
            $sku_new = trim( $matches[1][0] );
        }
        $info['sku_search'] = $sku_new;


        $info['title'] = trim($_POST['biaoti']);
        $info['title1'] = trim($_POST['zibiaoti']);
        $info['categoty1'] = trim($_POST['diyifenlei']);
        $info['categoty1_all'] = trim($_POST['diyifenleimiaoshu']);
        $info['categoty2'] = trim($_POST['dierfenlei']);
        $info['categoty1_all'] = isset($_POST['diyifenleimiaoshu']) ? trim($_POST['diyifenleimiaoshu']) : '';
        $info['categoty2_all'] = isset($_POST['dierfenleimiaoshu']) ? trim($_POST['dierfenleimiaoshu']) : '';
        $info['item_specifics'] = isset($_POST['wupinmiaoshu']) ? json_encode($_POST['wupinmiaoshu']) : '';
        if (isset($_POST['wupinmiaoshuzhi'])) {
            $arr = array();
            for ($i = 0; $i < 3; $i++) {
                if (!empty($_POST['wupinmiaoshuzhi'][$i][$i + 1])) {
                    $arr[$_POST['wupinmiaoshuname'][$i][$i + 1]] = $_POST['wupinmiaoshuzhi'][$i][$i + 1];
                }
            }
        }

      //  var_dump($_POST['skuinfo']);exit;
        if (isset($_POST['skuinfo'])) {

            $sku_arr= array();
            $sku_arr_total =array();
            foreach($_POST['skuinfo'] as $key=> $sku){
                $k = 0;
                foreach($sku as $s){
                    $sku_arr[$k][$key] = $s;
                    $k++;
                }

            }
            $info['mul_info'] = json_encode($sku_arr);
           // var_dump( $info['mul_info']);exit;
          //  $info['mul_info'] = json_encode($_POST['skuinfo']);
        }
        if (isset($_POST['zidingyi'])) {

            $info['add_mul'] = json_encode($_POST['zidingyi']);
        }
        if (isset($_POST['mulpic'])) {
            $info['mul_picture'] = json_encode($_POST['mulpic']);
        }

      //  var_dump( $info['mul_picture'] );exit;
        $info['item_specifics_user'] = isset($arr) ? json_encode($arr) : '';
        $info['item_status'] = isset($_POST['wpms2']) ? $_POST['wpms2'] : '';
        $info['item_status_description'] = isset($_POST['wpms3']) ? trim($_POST['wpms3']) : '';
        $info['publication_template'] = $_POST['templateid'];
        $info['publication_template_html'] = $_POST['templatehtmlselect'];
        // $info['template_deteils']=trim($_POST['detail1']);

        $info['template_title'] = $_POST['template_title'];
        if (isset($_POST['imgLists'])) {
            if (count($_POST['imgLists']) > 12) {
                ajax_return('图片数量不能多余12张', false);
            }
        }

        // UPC 的加在这里吧
        $info['upc'] = isset($_POST['upc']) ? $_POST['upc'] : '';
        $info['isb'] = isset($_POST['isb']) ? $_POST['isb'] : '';
        $info['ean'] = isset($_POST['ean']) ? $_POST['ean'] : '';


        $info['ebay_picture'] = isset($_POST['imgLists']) ? json_encode($_POST['imgLists']) : '';
        $info['template_deteils'] = isset($_POST['tempimgLists']) ? json_encode($_POST['tempimgLists']) : '';

        $info['description_title'] = $_POST['description_title'];
        $info['description_details'] = trim($_POST['detail']);
        $info['auction_is_private'] = isset($_POST['sirenpaimai']) ? $_POST['sirenpaimai'] : '';
        $info['published_day'] = $_POST['paimaitianshu'];
        $info['price'] = $_POST['paimaijiage'];
        $info['reserve_price'] = isset($_POST['paimaibaoliujia']) ? $_POST['paimaibaoliujia'] : '';
        $info['price_noce'] = isset($_POST['paimaiyikoujia']) ? $_POST['paimaiyikoujia'] : '';
        $info['quantity'] = intval($_POST['kandengshuliang']);
        $info['paypal_account'] = $_POST['paypalaccount'];
        $info['payment_details'] = $_POST['shuoming'];
        $info['all_buyers'] = $_POST['yaoqiu'];
        $info['nopaypal'] = isset($_POST['nopaypal']) ? $_POST['nopaypal'] : '';
        $info['noti_trans'] = isset($_POST['yunshufangweizhiwai']) ? $_POST['yunshufangweizhiwai'] : '';
        $info['is_abandoned'] = isset($_POST['qibiao']) ? $_POST['qibiao'] : '';
        $info['abandoned_num'] = isset($_POST['qibiaonum']) ? $_POST['qibiaonum'] : '';
        $info['abandoned_day'] = isset($_POST['qibiaotianshu']) ? $_POST['qibiaotianshu'] : '';
        $info['is_report'] = isset($_POST['jianjv']) ? $_POST['jianjv'] : '';
        $info['report_num'] = isset($_POST['jianjvnum']) ? $_POST['jianjvnum'] : '';
        $info['report_day'] = isset($_POST['jianjvtianshu']) ? $_POST['jianjvtianshu'] : '';
        $info['is_trust_low'] = isset($_POST['xinyong']) ? $_POST['xinyong'] : '';
        $info['trust_low_num'] = isset($_POST['xinyongnum']) ? $_POST['xinyongnum'] : '';
        $info['already_buy'] = isset($_POST['goumai']) ? $_POST['goumai'] : '';
        $info['buy_num'] = isset($_POST['goumainum']) ? $_POST['goumainum'] : '';
        $info['buy_condition'] = isset($_POST['maijiaxinyong']) ? $_POST['maijiaxinyong'] : '';
        $info['buy_credit'] = isset($_POST['maijiaxinyongnum']) ? $_POST['maijiaxinyongnum'] : '';
        $info['returns_policy'] = $_POST['tuihuozhengce'];
        $info['returns_days'] = isset($_POST['tuihuotianshu']) ? $_POST['tuihuotianshu'] : '';
        $info['returns_type'] = isset($_POST['tuihuofangshi']) ? $_POST['tuihuofangshi'] : '';
        $info['returns_delay'] = isset($_POST['returns_delay']) ? $_POST['returns_delay'] : '';
        $info['returns_cost_by'] = isset($_POST['tuihuochengdang']) ? $_POST['tuihuochengdang'] : '';
        $info['return_details'] = isset($_POST['return_details']) ? trim($_POST['return_details']) : '';
        $info['item_location'] = $_POST['item_location'];
        $info['item_country'] = $_POST['country'];
        $info['item_post'] = $_POST['item_post'];


        $info['inter_process_day'] = $_POST['guoneichulishijian'];
        $info['inter_fast_send'] = isset($_POST['guoneikuaisu']) ? $_POST['guoneikuaisu'] : '';
        $info['inter_trans_type'] = $_POST['guoneiyunshu1'];
        $info['inter_trans_cost'] = $_POST['guoneiyunfei1'];
        $info['inter_free'] = isset($_POST['guoneimianfei1']) ? $_POST['guoneimianfei1'] : '';
        $info['inter_trans_extracost'] = $_POST['guoneiewaijiashou1'];
        $info['inter_trans_AK_extracost'] = isset($_POST['guoneiAKewaijiashou1']) ? $_POST['guoneiAKewaijiashou1'] : '';


        $info['international_type1'] = isset($_POST['yunshufangshi1']) ? $_POST['yunshufangshi1'] : '';//que
        $info['international_cost1'] = isset($_POST['yunfei1']) ? $_POST['yunfei1'] : '';
        $info['international_free1'] = isset($_POST['mianfei1']) ? $_POST['mianfei1'] : '';
        $info['international_extracost1'] = isset($_POST['ewai1']) ? $_POST['ewai1'] : '';
        $info['international_is_worldwide1'] = isset($_POST['Worldwide1']) ? $_POST['Worldwide1'] : '';//que1
        $info['international_is_country1'] = isset($_POST['guanjia1']) ? json_encode($_POST['guanjia1']) : '';

        $info['international_type2'] = isset($_POST['yunshufangshi2']) ? $_POST['yunshufangshi2'] : '';//que
        $info['international_cost2'] = isset($_POST['yunfei2']) ? $_POST['yunfei2'] : '';
        $info['international_free2'] = isset($_POST['mianfei2']) ? $_POST['mianfei2'] : '';
        $info['international_extracost2'] = isset($_POST['ewai2']) ? $_POST['ewai2'] : '';
        $info['international_is_worldwide2'] = isset($_POST['Worldwide2']) ? $_POST['Worldwide2'] : '';//que1
        $info['international_is_country2'] = isset($_POST['guanjia2']) ? json_encode($_POST['guanjia2']) : '';

        $info['international_type3'] = isset($_POST['yunshufangshi3']) ? $_POST['yunshufangshi3'] : '';//que
        $info['international_cost3'] = isset($_POST['yunfei3']) ? $_POST['yunfei3'] : '';
        $info['international_free3'] = isset($_POST['mianfei3']) ? $_POST['mianfei3'] : '';
        $info['international_extracost3'] = isset($_POST['ewai3']) ? $_POST['ewai3'] : '';
        $info['international_is_worldwide3'] = isset($_POST['Worldwide3']) ? $_POST['Worldwide3'] : '';//que1
        $info['international_is_country3'] = isset($_POST['guanjia3']) ? json_encode($_POST['guanjia3']) : '';

        $info['international_type4'] = isset($_POST['yunshufangshi4']) ? $_POST['yunshufangshi4'] : '';//que
        $info['international_cost4'] = isset($_POST['yunfei4']) ? $_POST['yunfei4'] : '';
        $info['international_free4'] = isset($_POST['mianfei4']) ? $_POST['mianfei4'] : '';
        $info['international_extracost4'] = isset($_POST['ewai4']) ? $_POST['ewai4'] : '';
        $info['international_is_worldwide4'] = isset($_POST['Worldwide4']) ? $_POST['Worldwide4'] : '';//que1
        $info['international_is_country4'] = isset($_POST['guanjia4']) ? json_encode($_POST['guanjia4']) : '';

        $info['international_type5'] = isset($_POST['yunshufangshi5']) ? $_POST['yunshufangshi5'] : '';//que
        $info['international_cost5'] = isset($_POST['yunfei5']) ? $_POST['yunfei5'] : '';
        $info['international_free5'] = isset($_POST['mianfei5']) ? $_POST['mianfei5'] : '';
        $info['international_extracost5'] = isset($_POST['ewai5']) ? $_POST['ewai5'] : '';
        $info['international_is_worldwide5'] = isset($_POST['Worldwide5']) ? $_POST['Worldwide5'] : '';//que1
        $info['international_is_country5'] = isset($_POST['guanjia5']) ? json_encode($_POST['guanjia5']) : '';

        $info['excludeship']  =  isset($_POST['excludeship'])?$_POST['excludeship']:'';
        if ($_POST['action'] == 'save') {

            if (isset($_POST['id']) && (!empty($_POST['id']))) {
                $arrinfo['id'] = $_POST['id'];
                $arrinfo['site'] = $_POST['siteid'];
                $reslut = $this->list->getEbayListOne($arrinfo);
                if (empty($reslut)) {
                    $info['createtime'] = date('Y-m-d H:i:s', time());
                    $re = $this->list->inserinfo($info);
                    unset($info);
                    unset($arr);
                    if ($re) {
                    $this->ebayoperationLog($re,'创建了这条广告');
                        ajax_return('新增成功', 1, $re);
                        die;
                    } else {
                        ajax_return('新增失败', false);
                        die;
                    }
                }
                $id = $_POST['id'];
                $re = $this->list->updatelist($id, $info);
                if ($re == 1) {
                    $this->ebayoperationLog($id,'修改了这条广告');
                    ajax_return('修改成功', 2);
                    die;
                } else {
                    ajax_return('保存失败', false);
                    die;
                }

            }
            $info['createtime'] = date('Y-m-d H:i:s', time());
            $re = $this->list->inserinfo($info);
            unset($info);
            unset($arr);
            if ($re) {
                $this->ebayoperationLog($re,'创建了这条广告');
                ajax_return('新增成功', 1, $re);
                die;
            } else {
                ajax_return('新增失败', false);
            }
        }

        if ($_POST['action'] == 'saveToPost') {
            if (isset($_POST['id']) && (!empty($_POST['id']))) {
                $arrinfo['id'] = $_POST['id'];
                $arrinfo['site'] = $_POST['siteid'];
                $reslut = $this->list->getEbayListOne($arrinfo);
                if (empty($reslut)) {
                    $info['createtime'] = date('Y-m-d H:i:s', time());
                    $re = $this->list->inserinfo($info);
                    $this->ebayoperationLog($re,'创建了这条广告');
                    unset($info);
                    unset($arr);
                    if ($re) {
                        $reinfo = $this->publishEbay($re);
                        if ($reinfo == 'Success') {
                            $this->ebayoperationLog($re,'成功刊登了这条广告');
                            ajax_return($reinfo, 1, $re);
                        } else {
                            ajax_return($reinfo, 4, $re);
                        }
                        //   ajax_return($reinfo, 1, $re);
                        die;
                    } else {
                        ajax_return('新增失败,无法刊登', false);
                        die;
                    }
                }
                $id = $_POST['id'];
                $re = $this->list->updatelist($id, $info);
                $this->ebayoperationLog($id,'跟新了这条广告');
                if ($re == 1) {
                    $reinfo = $this->publishEbay($id);
                    $this->ebayoperationLog($id,'成功刊登了这条广告');
                    ajax_return($reinfo, 2);
                    die;
                } else {
                    ajax_return('保存失败，无法刊登', false);
                    die;
                }

            }
            $info['createtime'] = date('Y-m-d H:i:s', time());
            $re = $this->list->inserinfo($info);
            $this->ebayoperationLog($re,'创建了这条广告');
            unset($info);
            unset($arr);
            if ($re) {
                $reinfo = $this->publishEbay($re);

                if ($reinfo == 'Success') {
                    $this->ebayoperationLog($re,'成功刊登了这条广告');
                    ajax_return($reinfo, 1, $re);
                } else {
                    ajax_return($reinfo, 4, $re);
                }

                die;
            } else {
                ajax_return('新增失败,无法刊登', false);
            }
        }
        if ($_POST['action'] == 'moDescription') {

            if (isset($_POST['id']) && (!empty($_POST['id']))) {
                $info['id'] = $_POST['id'];

                $re = $this->modifTemplate($info);
                if ($re) {
                    $id = $info['id'];
                    $uparr['publication_template_html'] = $info['publication_template_html'];
                    $uparr['publication_template'] = $info['publication_template'];
                    $uparr['description_details'] = $info['description_details'];
                    $uparr['title'] = $info['title'];
                    $this->list->updatelist($id, $uparr);
                    $this->ebayoperationLog($id,'修改了这条广告的线上描述');
                    ajax_return('修改线上描述成功', 2);
                    die;
                } else {
                    ajax_return('修改线上描述失败', false);
                }
            } else {
                ajax_return('请先保存或者刊登', false);
            }
        }
        if ($_POST['action'] == 'modifytrans') {

            if (isset($_POST['id']) && (!empty($_POST['id']))) {
                $info['id'] = $_POST['id'];

                $re = $this->modifyTrans($info, 1);
                if ($re) {
                    $id = $info['id'];
                    $uparr['item_location'] = $info['item_location'];
                    $uparr['item_country'] = $info['item_country'];
                    $uparr['item_post'] = $info['item_post'];


                    $uparr['inter_process_day'] = $info['inter_process_day'];
                    $uparr['inter_fast_send'] = $info['inter_fast_send'];
                    $uparr['inter_trans_type'] = $info['inter_trans_type'];
                    $uparr['inter_trans_cost'] = $info['inter_trans_cost'];
                    $uparr['inter_free'] = $info['inter_free'];
                    $uparr['inter_trans_extracost'] = $info['inter_trans_extracost'];
                    $uparr['inter_trans_AK_extracost'] = $info['inter_trans_AK_extracost'];


                    $uparr['international_type1'] = $info['international_type1'];
                    $uparr['international_cost1'] = $info['international_cost1'];
                    $uparr['international_free1'] = $info['international_free1'];
                    $uparr['international_extracost1'] = $info['international_extracost1'];
                    $uparr['international_is_worldwide1'] = $info['international_is_worldwide1'];
                    $uparr['international_is_country1'] = $info['international_is_country1'];


                    $uparr['international_type2'] = $info['international_type2'];
                    $uparr['international_cost2'] = $info['international_cost2'];
                    $uparr['international_free2'] = $info['international_free2'];
                    $uparr['international_extracost2'] = $info['international_extracost2'];
                    $uparr['international_is_worldwide2'] = $info['international_is_worldwide2'];
                    $uparr['international_is_country2'] = $info['international_is_country2'];


                    $uparr['international_type3'] = $info['international_type3'];
                    $uparr['international_cost3'] = $info['international_cost3'];
                    $uparr['international_free3'] = $info['international_free3'];
                    $uparr['international_extracost3'] = $info['international_extracost3'];
                    $uparr['international_is_worldwide3'] = $info['international_is_worldwide3'];
                    $uparr['international_is_country3'] = $info['international_is_country3'];


                    $uparr['international_type4'] = $info['international_type4'];
                    $uparr['international_cost4'] = $info['international_cost4'];
                    $uparr['international_free4'] = $info['international_free4'];
                    $uparr['international_extracost4'] = $info['international_extracost4'];
                    $uparr['international_is_worldwide4'] = $info['international_is_worldwide4'];
                    $uparr['international_is_country4'] = $info['international_is_country4'];


                    $uparr['international_type5'] = $info['international_type5'];
                    $uparr['international_cost5'] = $info['international_cost5'];
                    $uparr['international_free5'] = $info['international_free5'];
                    $uparr['international_extracost5'] = $info['international_extracost5'];
                    $uparr['international_is_worldwide5'] = $info['international_is_worldwide5'];
                    $uparr['international_is_country5'] = $info['international_is_country5'];


                    $uparr['excludeship'] = $info['excludeship'];



                    $this->list->updatelist($id, $uparr);
                    $this->ebayoperationLog($id,'修改了这条广告的线上物流方式');
                    ajax_return('修改线上物流方式成功', 2);
                    die;
                } else {
                    ajax_return('修改线上物流方式失败', false);
                }
            } else {
                ajax_return('请先刊登', false);
            }
        }
        if ($_POST['action'] == 'modifypictureurl') {
           if (isset($_POST['id']) && (!empty($_POST['id']))) {
            $info['id'] = $_POST['id'];

            $re = $this->modifPicture($info,1);
            if ($re) {
                $id = $info['id'];
                $uparr['ebay_picture'] = $info['ebay_picture'];
                $this->list->updatelist($id, $uparr);
                $this->ebayoperationLog($id,'修改了这条广告的线上橱窗图片');
                ajax_return('修改橱窗图片成功', 2);
                die;
            } else {
                ajax_return('修改橱窗图片失败', false);
            }
        } else {
            ajax_return('请先保存或者刊登', false);
        }
       }

        if ($_POST['action'] == 'verifyebay') {
            $reinfo = $this->verifypublishEbay($info);
            if(!is_array($reinfo))
            {
                ajax_return($reinfo,2);
            }
            else
            {
                $feed = '';
                foreach($reinfo as $k=> $ree)
                {
                    $feed = $feed.$k.':'.$ree.'       ';
                }
                ajax_return($feed,2);
            }

        }
        if ($_POST['action'] == 'modifyskuinfo') {
            $info['id'] = $_POST['id'];
            $arrid['id'] =$info['id'];
            $listinfo = $this->list->getEbayListOne($arrid);
            if($info['ad_type']=='paimai')
            {
               // $this->modifySkuInfo($info,1);
                ajax_return('暂不支持修改拍卖价格',2);
            }
            if($info['ad_type']=='guding')
            {
                $newinfo = array();
                $newinfo['quantity'] = $info['quantity'];
                $currency_option = array();
                $currency_option['where']['siteid'] = $listinfo['site'];
                $currency_result = $this->Ebay_ebaysite_model->getOne($currency_option,true);

                $newinfo['currency'] = $currency_result['currency'];
                $newinfo['price'] = $info['price'];
                $newinfo['sku'] = $info['sku'];
                $result = $this->userToken->getInfoByAccount($listinfo['ebayaccount']);
                $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$listinfo['site'],'ReviseItem');
                $modify_result =  $this->ebaytest->modifySku($listinfo['itemid'],$newinfo);
                if(isset($modify_result->ItemID))
                {
                    ajax_return('修改成功',2);
                }
                else
                {
                    ajax_return('修改失败',2);
                }
            }
            if($info['ad_type']=='duoshuxing')
            {
                $arr1 = json_decode($info['mul_info'],true);
                $arr2 = json_decode($listinfo['mul_info'],true);
                $resstring='';

                $modifyarr=  array(); // 修改已存在的SKU信息
                $addarr = array();  // 新增SKU的信息
                $deletearr = array(); //删除SKU的信息


                foreach($arr1  as $ar1) //判断是否有新增的sku
                {
                    $addmul = true;  //假设为新增sku
                    foreach($arr2 as $ar2)
                    {

                        if($ar1['sku'] == $ar2['sku'])//存在相等SKU
                        {
                          $arresult =   array_diff($ar1,$ar2);

                            if(!empty($arresult))
                            {
                                $modifyarr[$ar1['sku']] = $arresult; //获取最新sku的改变值

                            }
                            $addmul = false; //表示该SKU不是新增
                        }
                    }
                    if($addmul) //如果该SKU为新增
                    {
                        if(!empty($ar1['sku']))  //SKU 不为空
                        {

                        $addarr[] = $ar1;

                        }

                    }
                }

                foreach($arr2 as $ar2)
                {
                    $deletmul = true; //假设为删除的多属性
                    foreach($arr1 as $ar1)
                    {
                        if($ar2['sku'] == $ar1['sku'])//存在相等SKU
                        {
                            $deletmul = false;//假设失败
                        }
                    }

                    if($deletmul) //假设成立
                    {
                        $deletearr[] = $ar2['sku']; //获取SKU
                    }
                }

                    /*if(!empty($modifyarr))
                    {

                        $modifysku = false;
                        $info['modify_sku'] = $modifyarr;
                        $modiftresult1  =   $this->modifySkuInfo($info,3); //只修改价格，数量
                        var_dump($modiftresult1);exit;
                        if($modiftresult1 =='Success') //修改成功
                        {
                            $arr2 = json_decode($listinfo['mul_info'],true);

                            foreach ($arr2 as $key=> $ar2)
                            {
                                foreach($modifyarr as $k=>$midify)
                                {
                                    if($ar2['sku'] ==$k)
                                    {
                                        foreach($midify as $k=>$v)
                                        {
                                            $arr2[$key][$k]=$v;
                                        }
                                    }
                                }
                            }
                            $updatesku['mul_info'] = json_encode($arr2);
                            $this->list->updatelist($arrid['id'], $updatesku);
                            $modifysku = true;
                        }
                        else
                        {
                            ajax_return($modiftresult1,2);
                        }
                    }*/


                $modiftresult2 =  $this->modifySkuInfoLot($info,2);
                if( $modiftresult2 =='Success'){
                        $updata_opton = array();
                        $updata_data = array();
                    $updata_opton['where']['id'] = $info['id'];
                    $updata_data['mul_info'] = $info['mul_info'];

                    $this->list->update($updata_data,$updata_opton);
                    ajax_return("修改成功",2);


                }else{
                    ajax_return($modiftresult2,2);
                    die;
                }
            }
        }

    }

    public function publishEbay($id)
    {
        $arr['id'] =$id;

        $info = $this->list->getEbayListOne($arr);
        if($info['status']==2)
        {
            return '已经刊登过！';
        }
        $result = $this->userToken->getInfoByAccount($info['ebayaccount']);


        $xml ='';
        $xml .="<?xml version='1.0' encoding='utf-8'?>";
        if($info['ad_type']=='duoshuxing')
        {
            $api = 'AddFixedPriceItem';
            $xml .='<AddFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        }
        else
        {
            $api = 'AddItem';
            $xml .='<AddItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        }
        $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$info['site'],$api);


        $xml .="<ErrorLanguage>zh_CN</ErrorLanguage><WarningLevel>High</WarningLevel><Item>";
        $xml .=' <Title>'.$info['title'].'</Title>';
        if(!empty($info['title1']))
        {
            $xml .='<SubTitle>'.$info['title1'].'</SubTitle>';
        }
        $site['siteid'] = $info['site'];
        $siteinfo = $this->ebaysite->getEbaySiteOne($site);
        $sitename =$siteinfo['site'];
        $sitecurrency = $siteinfo['currency'];
        $xml .='<Site>'.$sitename.'</Site>';
        $xml .='<Currency>'.$sitecurrency.'</Currency>';
        $xml .=' <SKU>'.$info['sku'].'</SKU>';
        $xml .='<ListingDuration>'.$info['published_day'].'</ListingDuration>';
        $xml .='<CategoryMappingAllowed>true</CategoryMappingAllowed>';
        $xml .='<PrimaryCategory><CategoryID>'.$info['categoty1'].'</CategoryID></PrimaryCategory>';
        if(!empty($info['categoty2']))
        {
            $xml .='<SecondaryCategory><CategoryID>'.$info['categoty2'].'</CategoryID></SecondaryCategory>';
        }
        if(($info['item_status']!=1111)&&($info['item_status']!=''))
        {
            $xml .='<ConditionID>'.$info['item_status'].'</ConditionID>';
        }

        if(!empty($info['item_status_description']))
        {
            $xml .='<ConditionDescription>'.trim($info['item_status_description']).'</ConditionDescription>';
        }
        if($info['auction_is_private']=='true')
        {
            $xml .='<PrivateListing>ture</PrivateListing>';
        }
        if(!empty($info['ebay_picture']))
        {
            $xml .='<PictureDetails>';
            $pictrueinfo = json_decode($info['ebay_picture'],true);
            foreach($pictrueinfo as $pic)
            {
                $xml .='<PictureURL>'.$pic.'</PictureURL>';
            }
            $xml .='</PictureDetails>';
        }

        $xml .='<PaymentMethods>PayPal</PaymentMethods>';
        $xml .='<PayPalEmailAddress>'.$info['paypal_account'].'</PayPalEmailAddress>';
        if(!empty($info['item_country']))
        {
            $xml .='<Country>'.$info['item_country'].'</Country>';
        }
        if(!empty($info['item_location']))
        {
            $xml .='<Location>'.$info['item_location'].'</Location>';
        }
        if(!empty($info['item_post']))
        {
            $xml .='<PostalCode>'.$info['item_post'].'</PostalCode>';
        }
        if((!empty($info['upc']))||(!empty($info['isb']))||(!empty($info['ean'])))
        {
            $xml .='<ProductListingDetails>';

            if(!empty($info['upc']))
            {
                $xml .='<UPC>'.$info['upc'].'</UPC>';
            }

            if(!empty($info['ean']))
            {
                $xml .='<EAN>'.$info['ean'].'</EAN>';
            }

            if(!empty($info['isb']))
            {
                $xml .='<ISBN>'.$info['isb'].'</ISBN>';
            }

            $xml .='</ProductListingDetails>';
        }

        //<ItemSpecifics><NameValueList><Name>Color</Name><Value>White</Value></NameValueList></ItemSpecifics>
        if(!empty($info['item_specifics']))
        {
            $xml .='<ItemSpecifics>';
            $spe = json_decode($info['item_specifics'],true);

            foreach($spe as $k=>$s)
            {
                if(!empty($s))
                {
                    foreach($s as $s_1=>$s_2)
                    {
                        if(!empty($s_2))
                        {
                            $xml .='<NameValueList><Name>'.$s_1.'</Name><Value>'.$s_2.'</Value></NameValueList>';
                        }
                    }
                }
            }
            if(!empty($info['item_specifics_user']))
            {
                $divspe = json_decode($info['item_specifics_user'],true);
                foreach($divspe as $ke=> $div)
                {
                    if((!empty($ke))&&(!empty($div)))
                    {
                        $xml .='<NameValueList><Name>'.$ke.'</Name><Value>'.$div.'</Value></NameValueList>';
                    }
                }
            }
            $xml .='</ItemSpecifics>';
        }
        if($info['returns_policy'] =='ReturnsAccepted')
        {
            $xml .='<ReturnPolicy>';

            $xml .='<ReturnsAcceptedOption>'.$info['returns_policy'].'</ReturnsAcceptedOption>';
            if(!empty($info['returns_type']))
            {
            $xml .='<RefundOption>'.$info['returns_type'].'</RefundOption>';
            }

            if(!empty($info['returns_cost_by']))
            {
            $xml .='<ShippingCostPaidByOption>'.$info['returns_cost_by'].'</ShippingCostPaidByOption>';
            }
            if(!empty($info['returns_days']))
            {
            $xml .=' <ReturnsWithinOption>'.$info['returns_days'].'</ReturnsWithinOption>';
            }
            if($info['returns_delay']=='on')
            {
                $xml .='<ExtendedHolidayReturns>true</ExtendedHolidayReturns>';
            }
            if(!empty($info['return_details']))
            {
            $xml .='<Description>'.trim($info['return_details']).'</Description>';
            }

            $xml .='</ReturnPolicy>';
        }
        if(empty($info['inter_process_day']))
        {
            if($info['inter_fast_send']=='true')
            {
                $xml .='<DispatchTimeMax>0</DispatchTimeMax>';
            }
        }
        else
        {
            $xml .='<DispatchTimeMax>'.$info['inter_process_day'].'</DispatchTimeMax>';
        }

        $xml .='<ShippingDetails>';
        $xml .='<ShippingServiceOptions>';
        $xml .='<ShippingServicePriority>1</ShippingServicePriority>';
        $xml .=' <ShippingService>'.$info['inter_trans_type'].'</ShippingService>';

            if(!empty($info['inter_trans_cost']))
            {
                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'">'.$info['inter_trans_cost'].'</ShippingServiceCost>';
            }
            if(!empty($info['inter_trans_extracost']))
            {
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'">'.$info['inter_trans_extracost'].'</ShippingServiceAdditionalCost>';
            }

        $xml .='</ShippingServiceOptions>';


        if(!empty($info['international_type1']))
        {
            $xml .='<InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>1</ShippingServicePriority>';
            $xml .='<ShippingService>'.$info['international_type1'].'</ShippingService>';

                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'" >'.$info['international_cost1'].'</ShippingServiceCost>';
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'" >'.$info['international_extracost1'].'</ShippingServiceAdditionalCost>';

            if($info['international_is_worldwide1']=='on')
            {
               $xml .='<ShipToLocation>Worldwide</ShipToLocation>';
            }
            else
            {
                $country = json_decode($info['international_is_country1'],true);
                foreach($country as $co)
                {
                    $xml .='<ShipToLocation>'.$co.'</ShipToLocation>';
                }
            }
            $xml .='</InternationalShippingServiceOption>';
        }
        if(!empty($info['international_type2']))
        {
            $xml .='<InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>2</ShippingServicePriority>';
            $xml .='<ShippingService>'.$info['international_type2'].'</ShippingService>';

                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'" >'.$info['international_cost2'].'</ShippingServiceCost>';
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'" >'.$info['international_extracost2'].'</ShippingServiceAdditionalCost>';

            if($info['international_is_worldwide2']=='on')
            {
                $xml .='<ShipToLocation>Worldwide</ShipToLocation>';
            }
            else
            {
                $country = json_decode($info['international_is_country2'],true);
                foreach($country as $co)
                {
                    $xml .='<ShipToLocation>'.$co.'</ShipToLocation>';
                }
            }
            $xml .='</InternationalShippingServiceOption>';
        }
        if(!empty($info['international_type3']))
        {
            $xml .='<InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>3</ShippingServicePriority>';
            $xml .='<ShippingService>'.$info['international_type3'].'</ShippingService>';

                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'" >'.$info['international_cost3'].'</ShippingServiceCost>';
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'" >'.$info['international_extracost3'].'</ShippingServiceAdditionalCost>';

            if($info['international_is_worldwide3']=='on')
            {
                $xml .='<ShipToLocation>Worldwide</ShipToLocation>';
            }
            else
            {
                $country = json_decode($info['international_is_country3'],true);
                foreach($country as $co)
                {
                    $xml .='<ShipToLocation>'.$co.'</ShipToLocation>';
                }
            }
            $xml .='</InternationalShippingServiceOption>';
        }
        if(!empty($info['international_type4']))
        {
            $xml .='<InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>4</ShippingServicePriority>';
            $xml .='<ShippingService>'.$info['international_type4'].'</ShippingService>';

                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'" >'.$info['international_cost4'].'</ShippingServiceCost>';
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'" >'.$info['international_extracost4'].'</ShippingServiceAdditionalCost>';

            if($info['international_is_worldwide4']=='on')
            {
                $xml .='<ShipToLocation>Worldwide</ShipToLocation>';
            }
            else
            {
                $country = json_decode($info['international_is_country4'],true);
                foreach($country as $co)
                {
                    $xml .='<ShipToLocation>'.$co.'</ShipToLocation>';
                }
            }
            $xml .='</InternationalShippingServiceOption>';
        }
        if(!empty($info['international_type5']))
        {
            $xml .='</InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>5</ShippingServicePriority>';
            $xml .='<ShippingService>'.$info['international_type5'].'</ShippingService>';

                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'" >'.$info['international_cost5'].'</ShippingServiceCost>';
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'" >'.$info['international_extracost5'].'</ShippingServiceAdditionalCost>';

            if($info['international_is_worldwide5']=='on')
            {
                $xml .='<ShipToLocation>Worldwide</ShipToLocation>';
            }
            else
            {
                $country = json_decode($info['international_is_country5'],true);
                foreach($country as $co)
                {
                    $xml .='<ShipToLocation>'.$co.'</ShipToLocation>';
                }
            }
            $xml .='</InternationalShippingServiceOption>';
        }


        if(!empty($info['excludeship']))
        {
            $excludeshiparr = explode(',',$info['excludeship']);
            foreach($excludeshiparr as $v)
            {
                $xml .='<ExcludeShipToLocation>'.$v.'</ExcludeShipToLocation>';
            }
        }
        $xml .='</ShippingDetails>';

        if($info['all_buyers']=='notall')
        {
          $xml .='<BuyerRequirementDetails>';
            if($info['nopaypal']=='on')
            {
                $xml .='<LinkedPayPalAccount>true</LinkedPayPalAccount>';
            }
            if($info['noti_trans']=='on')
            {
                $xml .='<ShipToRegistrationCountry>true</ShipToRegistrationCountry>';
            }

            if($info['is_abandoned']=='on')
            {
                $xml .='<MaximumUnpaidItemStrikesInfo><Count>'.$info['abandoned_num'].'</Count><Period>'.$info['abandoned_day'].'</Period></MaximumUnpaidItemStrikesInfo>';
            }
            if($info['is_report']=='on')
            {
                $xml .='<MaximumBuyerPolicyViolations><Count>'.$info['report_num'].'</Count><Period>'.$info['report_day'].'</Period></MaximumBuyerPolicyViolations>';
            }
            if($info['is_trust_low']=='on')
            {
                $xml .='<MinimumFeedbackScore>'.$info['trust_low_num'].'</MinimumFeedbackScore>';
            }
            if($info['already_buy']=='on')
            {
                $xml .=' <MaximumItemRequirements><MaximumItemCount>'.$info['buy_num'].'</MaximumItemCount>';
                if($info['buy_condition']=='on')
                {
                    $xml .='<MinimumFeedbackScore>'.$info['buy_credit'].'</MinimumFeedbackScore></MaximumItemRequirements>';
                }
                else
                {
                    $xml .='</MaximumItemRequirements>';
                }
            }
            $xml .='</BuyerRequirementDetails>';
        }



        if($info['ad_type']=='paimai')
        {
            $xml .='<ListingType>Chinese</ListingType>';

            $xml .='<StartPrice currencyID="'.$sitecurrency.'">'.$info['price'].'</StartPrice>';

            if(!empty($info['reserve_price']))
            {
                $xml .='<ReservePrice  currencyID="'.$sitecurrency.'">'.$info['reserve_price'].'</ReservePrice>';
            }
            if(!empty($info['price_noce']))
            {
                $xml .= '<BuyItNowPrice currencyID="'.$sitecurrency.'">'.$info['price_noce'].'</BuyItNowPrice>';
            }

            $xml .='<Quantity>'.$info['quantity'].'</Quantity>';

        }
        if($info['ad_type']=='guding')
        {
            $xml .='<ListingType>FixedPriceItem</ListingType>';
            $xml .='<StartPrice currencyID="'.$sitecurrency.'">'.$info['price'].'</StartPrice>';
            $xml .='<Quantity>'.$info['quantity'].'</Quantity>';

        }
        if($info['ad_type']=='duoshuxing')
        {
            $xml .='<ListingType>FixedPriceItem</ListingType>';
            $mul_info = json_decode($info['mul_info'],true);
            $add_mul =json_decode($info['add_mul'],true);
            $xml .='<Variations>';
            $xml .='<VariationSpecificsSet>';
            foreach($add_mul as $add)
            {
                if(($add=='UPC')||($add=='EAN')){
                    continue;
                }
                $arrdif = array();
                $xml .='<NameValueList><Name>'.$add.'</Name>';
                foreach($mul_info as $mul)
                {
                    if(in_array($mul[$add],$arrdif))
                    {
                        continue;
                    }
                    $arrdif[] = $mul[$add];
                    $xml .='<Value>'.$mul[$add].'</Value>';
                }
                $xml .='</NameValueList>';
            }
            $xml .='</VariationSpecificsSet>';

            foreach($mul_info as $in)
            {

                $xml .='<Variation>';
                $xml .='<SKU>'.$in['sku'].'</SKU>';
                $xml .='<StartPrice >'.$in['price'].'</StartPrice>';
                $xml .='<Quantity>'.$in['qc'].'</Quantity>';
                if(isset($in['UPC'])||isset($in['EAN'])){
                    $xml .='<VariationProductListingDetails>';
                    if(isset($in['UPC'])){
                        $xml .='<UPC>'.$in['UPC'].'</UPC>';
                    }
                    if(isset($in['EAN'])){
                        $xml .='<EAN>'.$in['EAN'].'</EAN>';
                    }
                    $xml .='</VariationProductListingDetails>';
                }

                $xml .='<VariationSpecifics>';

                foreach($add_mul as $ad)
                {
                    if(($ad=='UPC')||($ad=='EAN')){
                        continue;
                    }
                    $xml .='<NameValueList>';
                    $xml .='<Name>'.$ad.'</Name>';
                    $xml .='<Value>'.$in[$ad].'</Value>';
                    $xml .='</NameValueList>';
                }
                $xml .='</VariationSpecifics>';
                $xml .='</Variation>';
            }

            if(!empty($info['mul_picture']))
            {
                $xml .='<Pictures>';
                $picinfo = json_decode($info['mul_picture'],true);
                foreach($picinfo as $key=>$p)
                {
                    $xml .='<VariationSpecificName>'.$key.'</VariationSpecificName>';
                    foreach($p as $v_1 => $v_2)
                    {
                        $xml .='<VariationSpecificPictureSet><VariationSpecificValue>'.$v_1.'</VariationSpecificValue>';
                        foreach($v_2 as $picmul)
                        {
                            $xml .=' <PictureURL>'.$picmul.'</PictureURL>';
                        }
                        $xml .='</VariationSpecificPictureSet>';
                    }
                }
                $xml .='</Pictures>';
            }
            $xml .='</Variations>';
        }
        $template['id'] = $info['publication_template'];
        $resulttemplate = $this->ebaytemplate->getTemplateAll($template);

        $templatehtml['id'] = $info['publication_template_html'];
        $resulttemplatehtml = $this->ebaytemplatehtml->getTemplateAll($templatehtml);

        if(isset($resulttemplate[0]))
        {
            if(isset($resulttemplatehtml[0]))
            {
                $resulttemplatehtml[0]['template_html'] =str_replace('{{tittle}}',$info['template_title'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{paymentterms}}',$resulttemplate[0]['payment'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{termsofsales}}',$resulttemplate[0]['sales_policy'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{shippingterms}}',$resulttemplate[0]['shipping'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{contactus}}',$resulttemplate[0]['contact_us'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{aboutus}}',$resulttemplate[0]['about_us'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{description}}',$info['description_details'],$resulttemplatehtml[0]['template_html']);
                if(!empty($info['template_deteils']))
                {
                    $picarr = json_decode($info['template_deteils'],true);
                    $picbig='';
                    $picsamll ='';
                    for($i=0;$i<count($picarr);$i++)
                    {
                        if($i==0)
                        {
                            $picbig = '<div class="albumBigImgBox"><img width="600" src="'.$picarr[$i].'" alt="" name="bigImg" id="bigImg"></div>';
                        }
                        else
                        {
                            $picsamll =$picsamll.'<div alt="" class="smallImgBox"><img width="92" height="92" alt=""  onmouseover="document.getElementById('."'bigImg'".').src=this.src"   src="'.$picarr[$i].'"></div>';
                        }
                    }

                    $resulttemplatehtml[0]['template_html'] =str_replace('{{Picture1}}',$picbig ,$resulttemplatehtml[0]['template_html']);
                    $resulttemplatehtml[0]['template_html'] =str_replace('{{Picture2}}',$picsamll,$resulttemplatehtml[0]['template_html']);
                }  else{
                    $resulttemplatehtml[0]['template_html'] =str_replace('{{Picture1}}','' ,$resulttemplatehtml[0]['template_html']);
                    $resulttemplatehtml[0]['template_html'] =str_replace('{{Picture2}}','',$resulttemplatehtml[0]['template_html']);
                }

                $xml .='<Description><![CDATA['.$resulttemplatehtml[0]['template_html'].']]></Description>';
            }
            else
            {
                $templateinfo = '<h2><span class="Pa_head"><span class="Pa_headc">Payment Method <hr /></span></span></h2>'.$resulttemplate[0]['payment'].'<h2> <span class="Pa_head"><span class="Pa_headc">Shipping Detail<hr /> </span></span> </h2>'.$resulttemplate[0]['shipping'].'<h2> <span class="Pa_head"><span class="Pa_headc"> Sales Policy<hr /> </span></span> </h2>'.$resulttemplate[0]['sales_policy'].'<h2> <span class="Pa_head"><span class="Pa_headc">About Us<hr /> </span></span> </h2>'.$resulttemplate[0]['about_us'].'<h2> <span class="Pa_head"><span class="Pa_headc"> Contact Us <hr /> </span></span> </h2>'.$resulttemplate[0]['contact_us'];
                $xml.='<Description><![CDATA['.trim($info['description_details']).$templateinfo.']]></Description>';

            }
       }
        else
        {
            $xml.='<Description><![CDATA['.trim($info['description_details']).']]></Description>';

        }
        $xml .='</Item>';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
        if($info['ad_type']=='duoshuxing')
        {
            $xml .='</AddFixedPriceItemRequest>';
        }
        else
        {
            $xml .='</AddItemRequest>';
        }
      //  var_dump($xml);exit;
        $info = $this->ebaytest->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($info);
        $response = simplexml_import_dom($responseDoc);


      // if((string)$response->Ack=='Success')
      if(isset($response->ItemID))
        {
            $arritem['itemid'] = (string)($response->ItemID);
            $arritem['status'] = 2;
            $arritem['failurereasons'] = '';

            $this->list->updatelist($id,$arritem);
            return $arritem['itemid'];
        }
        else
        {
            if(isset($response->Ack))
            {

                //Ebay_operationlog_model
                $data =array();
                $data['listid'] =$id;
                $data['specificissues'] =var_export($response, TRUE);
                $this->Ebay_operationlog_model->add($data);


                $er['failurereasons'] = (string)$response->Errors->LongMessage;
                $er['status'] =3;
                $this->list->updatelist($id,$er);
                return  $er['failurereasons'];
            }
        }

    }

    //批量广告下架
    public function batchDown()
    {

        $ids=$_POST['productIds'];
        $idsarr  = explode(',',$ids);
        foreach($idsarr as $id)
        {
            $this->deletepublish($id);
        }
        ajax_return('下架完成，刷新查看结果');
    }



    //广告下架

    public function deletepublish($id)
    {
            $arr['id'] =$id;
            $info = $this->list->getEbayListOne($arr);
        if(empty($info))
        {
            return false;
        }
            if($info['status']!=2)
            {
                return '该信息还未刊登，无法下架';
            }
            $result = $this->userToken->getInfoByAccount($info['ebayaccount']);
            $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$info['site'],'EndItem');
        $xml ='';
        $xml .='<?xml version="1.0" encoding="utf-8"?>
            <EndItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
      if(isset($info['itemid']))
      {
          $xml .='<ItemID>'.$info['itemid'].'</ItemID>';
      }
        $xml .='  <EndingReason>NotAvailable</EndingReason>';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
        $xml .='</EndItemRequest>';
        $info = $this->ebaytest->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($info);
        $response = simplexml_import_dom($responseDoc);
        if((string)$response->Ack=='Success')
        {
            $er['status'] =4;
            $this->list->updatelist($id,$er);
            return '下架成功';
        }
        else
        {
            return '操作失败';
        }
        //var_dump($response);
    }


    //批量刊登
    public function batchPublish()
    {
        $ids=$_POST['productIds'];
        $idsarr  = explode(',',$ids);
        foreach($idsarr as $id)
        {

            $this->publishEbay($id);
        }
        ajax_return('刊登完成，刷新查看结果');
    }


    //修改在线广告的描述
    public function modifTemplate($info)
    {
        $arr['id'] =$info['id'];
        $listinfo = $this->list->getEbayListOne($arr);
        if($listinfo['status']!=2)
        {
            return false;
        }
        $result = $this->userToken->getInfoByAccount($info['ebayaccount']);
        $xml ='';
        $xml .="<?xml version='1.0' encoding='utf-8'?>";
        $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$info['site'],'ReviseItem');
        $xml .='<ReviseItemRequest  xmlns="urn:ebay:apis:eBLBaseComponents">';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
        $xml .='<Item>';
        $xml .='<ItemID>'.$listinfo['itemid'].'</ItemID>';
        $xml .=' <Title>'.$listinfo['title'].'</Title>';
        $template['id'] = $info['publication_template'];
        $resulttemplate = $this->ebaytemplate->getTemplateAll($template);

        $templatehtml['id'] = $info['publication_template_html'];
        $resulttemplatehtml = $this->ebaytemplatehtml->getTemplateAll($templatehtml);

        if(isset($resulttemplate[0]))
        {
            if(isset($resulttemplatehtml[0]))
            {
                $resulttemplatehtml[0]['template_html'] =str_replace('{{tittle}}',$info['template_title'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{paymentterms}}',$resulttemplate[0]['payment'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{termsofsales}}',$resulttemplate[0]['sales_policy'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{shippingterms}}',$resulttemplate[0]['shipping'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{contactus}}',$resulttemplate[0]['contact_us'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{aboutus}}',$resulttemplate[0]['about_us'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{description}}',$info['description_details'],$resulttemplatehtml[0]['template_html']);
                if(!empty($info['template_deteils']))
                {
                    $picarr = json_decode($info['template_deteils'],true);
                    $picbig='';
                    $picsamll ='';
                    for($i=0;$i<count($picarr);$i++)
                    {
                        if($i==0)
                        {
                            $picbig = '<div class="albumBigImgBox"><img width="600" src="'.$picarr[$i].'" alt="" name="bigImg" id="bigImg"></div>';
                        }
                        else
                        {
                            $picsamll =$picsamll.'<div alt="" class="smallImgBox"><img width="92" height="92" alt=""  onmouseover="document.getElementById('."'bigImg'".').src=this.src"   src="'.$picarr[$i].'"></div>';
                        }
                    }

                    $resulttemplatehtml[0]['template_html'] =str_replace('{{Picture1}}',$picbig ,$resulttemplatehtml[0]['template_html']);
                    $resulttemplatehtml[0]['template_html'] =str_replace('{{Picture2}}',$picsamll,$resulttemplatehtml[0]['template_html']);
                }
                else{
                    $resulttemplatehtml[0]['template_html'] =str_replace('{{Picture1}}','' ,$resulttemplatehtml[0]['template_html']);
                    $resulttemplatehtml[0]['template_html'] =str_replace('{{Picture2}}','',$resulttemplatehtml[0]['template_html']);
                }
                $xml .='<Description><![CDATA['.$resulttemplatehtml[0]['template_html'].']]></Description>';
            }
            else
            {
                $templateinfo = '<h2><span class="Pa_head"><span class="Pa_headc">Payment Method <hr /></span></span></h2>'.$resulttemplate[0]['payment'].'<h2> <span class="Pa_head"><span class="Pa_headc">Shipping Detail<hr /> </span></span> </h2>'.$resulttemplate[0]['shipping'].'<h2> <span class="Pa_head"><span class="Pa_headc"> Sales Policy<hr /> </span></span> </h2>'.$resulttemplate[0]['sales_policy'].'<h2> <span class="Pa_head"><span class="Pa_headc">About Us<hr /> </span></span> </h2>'.$resulttemplate[0]['about_us'].'<h2> <span class="Pa_head"><span class="Pa_headc"> Contact Us <hr /> </span></span> </h2>'.$resulttemplate[0]['contact_us'];
                $xml.='<Description><![CDATA['.trim($info['description_details']).$templateinfo.']]></Description>';

            }
        }
        else
        {
            $xml.='<Description><![CDATA['.trim($info['description_details']).']]></Description>';

        }
      //  $xml .=' <Description><![CDATA['.'<div   style="background:url('."http://imgurl.moonarstore.com/upload/E3112/E3112-3.jpg".') no-repeat left top; ">'.trim($info['description_details']).$templateinfo.'</div>'.']]></Description>';
        $xml .=' <DescriptionReviseMode>Replace</DescriptionReviseMode></Item></ReviseItemRequest>';

      //  var_dump($xml);exit;
        $info = $this->ebaytest->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($info);
        $response = simplexml_import_dom($responseDoc);
       // var_dump($response);exit;
        if((string)$response->Ack=='Success'||(string)$response->Ack=='Warning')
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    //修改广告的橱窗图片信息，$type= 1:单个修改 type=2 ：批量修改
  public  function modifPicture($publishinfo,$type)
    //public  function modifPicture()
    {
     //   $publishinfo=105;
       // $type = 2;
      if($type==1)
      {
          $arr['id'] =$publishinfo['id'];
      }
      if($type==2)
      {
          $arr['id'] =$publishinfo;
      }
      $listinfo = $this->list->getEbayListOne($arr);
      if($listinfo['status']!=2)
      {
          return false;
      }
      if($type==1)
      {
          $result = $this->userToken->getInfoByAccount($publishinfo['ebayaccount']);
          $lastinfo = $publishinfo;
          $lastinfo['itemid'] =  $listinfo['itemid'];
      }
      if($type==2)
      {
          $result = $this->userToken->getInfoByAccount($listinfo['ebayaccount']);
          $lastinfo = $listinfo;
      }

      $xml ='';
      $xml .="<?xml version='1.0' encoding='utf-8'?>";
      $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$lastinfo['site'],'ReviseItem');
      $xml .='<ReviseItemRequest  xmlns="urn:ebay:apis:eBLBaseComponents">';
      $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
      $xml .='<Item>';
      $xml .='<ItemID>'.$lastinfo['itemid'].'</ItemID>';

      if(!empty($lastinfo['ebay_picture']))
      {
          $xml .='<PictureDetails>';
          $pictrueinfo = json_decode($lastinfo['ebay_picture'],true);
          foreach($pictrueinfo as $pic)
          {
              $xml .='<PictureURL>'.$pic.'</PictureURL>';
          }
          $xml .='</PictureDetails>';
      }

      $xml .='</Item></ReviseItemRequest>';

      $info = $this->ebaytest->sendHttpRequest($xml);
      $responseDoc = new DomDocument();
      $responseDoc->loadXML($info);
      $response = simplexml_import_dom($responseDoc);
      if(isset($response->ItemID))
      {
          return true;
      }
      else
      {
          //var_dump($response);
          return false;
      }

  }


    //修改国内外物流方式，及其物品所在地，所在国家，邮编  $type= 1:单个修改 type=2 ：批量修改
    public function modifyTrans($publishinfo,$type)
    {

        if($type==1)
        {
            $arr['id'] =$publishinfo['id'];
        }
        if($type==2)
        {
            $arr['id'] =$publishinfo;
        }
        $listinfo = $this->list->getEbayListOne($arr);
        if($listinfo['status']!=2)
        {
            return false;
        }
        if($type==1)
        {
            $result = $this->userToken->getInfoByAccount($publishinfo['ebayaccount']);
            $lastinfo = $publishinfo;
            $lastinfo['itemid'] =  $listinfo['itemid'];
        }
        if($type==2)
        {
            $result = $this->userToken->getInfoByAccount($listinfo['ebayaccount']);
            $lastinfo = $listinfo;
        }
        $site['siteid'] = $lastinfo['site'];
        $siteinfo = $this->ebaysite->getEbaySiteOne($site);
        $sitecurrency = $siteinfo['currency'];

        $xml ='';
        $xml .="<?xml version='1.0' encoding='utf-8'?>";
        $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$lastinfo['site'],'ReviseItem');
        $xml .='<ReviseItemRequest  xmlns="urn:ebay:apis:eBLBaseComponents">';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
        $xml .='<Item>';
        $xml .='<ItemID>'.$lastinfo['itemid'].'</ItemID>';
        $xml .='<ShippingDetails>';
        $xml .='<ShippingServiceOptions>';
        $xml .='<ShippingServicePriority>1</ShippingServicePriority>';
        $xml .=' <ShippingService>'.$lastinfo['inter_trans_type'].'</ShippingService>';

            if(!empty($lastinfo['inter_trans_cost']))
            {
                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'">'.$lastinfo['inter_trans_cost'].'</ShippingServiceCost>';
            }
            if(!empty($lastinfo['inter_trans_extracost']))
            {
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'">'.$lastinfo['inter_trans_extracost'].'</ShippingServiceAdditionalCost>';
            }

        $xml .='</ShippingServiceOptions>';


        if(!empty($lastinfo['international_type1']))
        {
            $xml .='<InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>1</ShippingServicePriority>';
            $xml .='<ShippingService>'.$lastinfo['international_type1'].'</ShippingService>';

                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'" >'.$lastinfo['international_cost1'].'</ShippingServiceCost>';
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'" >'.$lastinfo['international_extracost1'].'</ShippingServiceAdditionalCost>';

            if($lastinfo['international_is_worldwide1']=='on')
            {
                $xml .='<ShipToLocation>Worldwide</ShipToLocation>';
            }
            else
            {
                $country = json_decode($lastinfo['international_is_country1'],true);
                foreach($country as $co)
                {
                    $xml .='<ShipToLocation>'.$co.'</ShipToLocation>';
                }
            }
            $xml .='</InternationalShippingServiceOption>';
        }
        if(!empty($lastinfo['international_type2']))
        {
            $xml .='<InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>2</ShippingServicePriority>';
            $xml .='<ShippingService>'.$lastinfo['international_type2'].'</ShippingService>';

                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'" >'.$lastinfo['international_cost2'].'</ShippingServiceCost>';
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'" >'.$lastinfo['international_extracost2'].'</ShippingServiceAdditionalCost>';

            if($lastinfo['international_is_worldwide2']=='on')
            {
                $xml .='<ShipToLocation>Worldwide</ShipToLocation>';
            }
            else
            {
                $country = json_decode($lastinfo['international_is_country2'],true);
                foreach($country as $co)
                {
                    $xml .='<ShipToLocation>'.$co.'</ShipToLocation>';
                }
            }
            $xml .='</InternationalShippingServiceOption>';
        }
        if(!empty($lastinfo['international_type3']))
        {
            $xml .='<InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>3</ShippingServicePriority>';
            $xml .='<ShippingService>'.$lastinfo['international_type3'].'</ShippingService>';

                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'" >'.$lastinfo['international_cost3'].'</ShippingServiceCost>';
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'" >'.$lastinfo['international_extracost3'].'</ShippingServiceAdditionalCost>';

            if($lastinfo['international_is_worldwide3']=='on')
            {
                $xml .='<ShipToLocation>Worldwide</ShipToLocation>';
            }
            else
            {
                $country = json_decode($lastinfo['international_is_country3'],true);
                foreach($country as $co)
                {
                    $xml .='<ShipToLocation>'.$co.'</ShipToLocation>';
                }
            }
            $xml .='</InternationalShippingServiceOption>';
        }
        if(!empty($lastinfo['international_type4']))
        {
            $xml .='<InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>4</ShippingServicePriority>';
            $xml .='<ShippingService>'.$lastinfo['international_type4'].'</ShippingService>';

                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'" >'.$lastinfo['international_cost4'].'</ShippingServiceCost>';
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'" >'.$lastinfo['international_extracost4'].'</ShippingServiceAdditionalCost>';

            if($lastinfo['international_is_worldwide4']=='on')
            {
                $xml .='<ShipToLocation>Worldwide</ShipToLocation>';
            }
            else
            {
                $country = json_decode($lastinfo['international_is_country4'],true);
                foreach($country as $co)
                {
                    $xml .='<ShipToLocation>'.$co.'</ShipToLocation>';
                }
            }
            $xml .='</InternationalShippingServiceOption>';
        }
        if(!empty($lastinfo['international_type5']))
        {
            $xml .='</InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>5</ShippingServicePriority>';
            $xml .='<ShippingService>'.$lastinfo['international_type5'].'</ShippingService>';

                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'" >'.$lastinfo['international_cost5'].'</ShippingServiceCost>';
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'" >'.$lastinfo['international_extracost5'].'</ShippingServiceAdditionalCost>';

            if($lastinfo['international_is_worldwide5']=='on')
            {
                $xml .='<ShipToLocation>Worldwide</ShipToLocation>';
            }
            else
            {
                $country = json_decode($lastinfo['international_is_country5'],true);
                foreach($country as $co)
                {
                    $xml .='<ShipToLocation>'.$co.'</ShipToLocation>';
                }
            }
            $xml .='</InternationalShippingServiceOption>';
        }

        if(!empty($lastinfo['excludeship']))
        {
            $excludeshiparr = explode(',',$lastinfo['excludeship']);
            foreach($excludeshiparr as $v)
            {
                $xml .='<ExcludeShipToLocation>'.$v.'</ExcludeShipToLocation>';
            }
        }

        $xml .='</ShippingDetails>';

        $xml .='</Item></ReviseItemRequest>';

        $info = $this->ebaytest->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($info);
        $response = simplexml_import_dom($responseDoc);

        if(isset($response->ItemID))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

//测试获取刊登费用
    public function verifypublishEbay($info)
    {
        $result = $this->userToken->getInfoByAccount($info['ebayaccount']);


        $xml ='';
        $xml .="<?xml version='1.0' encoding='utf-8'?>";
        if($info['ad_type']=='duoshuxing')
        {
            $api = 'VerifyAddFixedPriceItem';
            $xml .='<VerifyAddFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        }
        else
        {
            $api = 'VerifyAddItem';
            $xml .='<VerifyAddItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        }
        $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$info['site'],$api);


        $xml .="<ErrorLanguage>zh_CN</ErrorLanguage><WarningLevel>High</WarningLevel><Item>";
        $xml .=' <Title>'.$info['title'].'</Title>';
        if(!empty($info['title1']))
        {
            $xml .='<SubTitle>'.$info['title1'].'</SubTitle>';
        }
        $site['siteid'] = $info['site'];
        $siteinfo = $this->ebaysite->getEbaySiteOne($site);
        $sitename =$siteinfo['site'];
        $sitecurrency = $siteinfo['currency'];
        $xml .='<Site>'.$sitename.'</Site>';
        $xml .='<Currency>'.$sitecurrency.'</Currency>';
        $xml .=' <SKU>'.$info['sku'].'</SKU>';
        $xml .='<ListingDuration>'.$info['published_day'].'</ListingDuration>';
        $xml .='<CategoryMappingAllowed>true</CategoryMappingAllowed>';
        $xml .='<PrimaryCategory><CategoryID>'.$info['categoty1'].'</CategoryID></PrimaryCategory>';
        if(!empty($info['categoty2']))
        {
            $xml .='<SecondaryCategory><CategoryID>'.$info['categoty2'].'</CategoryID></SecondaryCategory>';
        }
        if(($info['item_status']!=1111)&&($info['item_status']!=''))
        {
            $xml .='<ConditionID>'.$info['item_status'].'</ConditionID>';
        }

        if(!empty($info['item_status_description']))
        {
            $xml .='<ConditionDescription>'.trim($info['item_status_description']).'</ConditionDescription>';
        }
        if($info['auction_is_private']=='true')
        {
            $xml .='<PrivateListing>ture</PrivateListing>';
        }
     //   var_dump($xml);exit;
        if(!empty($info['ebay_picture']))
        {
            $xml .='<PictureDetails>';
            $pictrueinfo = json_decode($info['ebay_picture'],true);
            foreach($pictrueinfo as $pic)
            {
                $xml .='<PictureURL>'.$pic.'</PictureURL>';
            }
            $xml .='</PictureDetails>';
        }

        $xml .='<PaymentMethods>PayPal</PaymentMethods>';
        $xml .='<PayPalEmailAddress>'.$info['paypal_account'].'</PayPalEmailAddress>';
        if(!empty($info['item_country']))
        {
            $xml .='<Country>'.$info['item_country'].'</Country>';
        }
        if(!empty($info['item_location']))
        {
            $xml .='<Location>'.$info['item_location'].'</Location>';
        }
        if(!empty($info['item_post']))
        {
            $xml .='<PostalCode>'.$info['item_post'].'</PostalCode>';
        }

        if((!empty($info['upc']))||(!empty($info['isb']))||(!empty($info['ean'])))
        {
            $xml .='<ProductListingDetails>';

            if(!empty($info['upc']))
            {
                $xml .='<UPC>'.$info['upc'].'</UPC>';
            }

            if(!empty($info['ean']))
            {
                $xml .='<EAN>'.$info['ean'].'</EAN>';
            }

            if(!empty($info['isb']))
            {
                $xml .='<ISBN>'.$info['isb'].'</ISBN>';
            }

            $xml .='</ProductListingDetails>';
        }

        //<ItemSpecifics><NameValueList><Name>Color</Name><Value>White</Value></NameValueList></ItemSpecifics>
        if(!empty($info['item_specifics']))
        {
            $xml .='<ItemSpecifics>';
            $spe = json_decode($info['item_specifics'],true);

            foreach($spe as $k=>$s)
            {
                if(!empty($s))
                {
                    foreach($s as $s_1=>$s_2)
                    {
                        if(!empty($s_2))
                        {
                            $xml .='<NameValueList><Name>'.$s_1.'</Name><Value>'.$s_2.'</Value></NameValueList>';
                        }
                    }
                }
            }
            if(!empty($info['item_specifics_user']))
            {
                $divspe = json_decode($info['item_specifics_user'],true);
                foreach($divspe as $ke=> $div)
                {
                    if((!empty($ke))&&(!empty($div)))
                    {
                        $xml .='<NameValueList><Name>'.$ke.'</Name><Value>'.$div.'</Value></NameValueList>';
                    }
                }
            }
            $xml .='</ItemSpecifics>';
        }
        if($info['returns_policy'] =='ReturnsAccepted')
        {
            $xml .='<ReturnPolicy>';
            $xml .='<ReturnsAcceptedOption>'.$info['returns_policy'].'</ReturnsAcceptedOption>';
            if(!empty($info['returns_type']))
            {
                $xml .='<RefundOption>'.$info['returns_type'].'</RefundOption>';
            }
            if(!empty($info['returns_cost_by']))
            {
                $xml .='<ShippingCostPaidByOption>'.$info['returns_cost_by'].'</ShippingCostPaidByOption>';
            }
            if(!empty($info['returns_days']))
            {
                $xml .=' <ReturnsWithinOption>'.$info['returns_days'].'</ReturnsWithinOption>';
            }
            if($info['returns_delay']=='on')
            {
                $xml .='<ExtendedHolidayReturns>true</ExtendedHolidayReturns>';
            }
            if(!empty($info['return_details']))
            {
                $xml .='<Description>'.trim($info['return_details']).'</Description>';
            }
            $xml .='</ReturnPolicy>';
        }
        if(empty($info['inter_process_day']))
        {
            if($info['inter_fast_send']=='true')
            {
                $xml .='<DispatchTimeMax>0</DispatchTimeMax>';
            }
        }
        else
        {
            $xml .='<DispatchTimeMax>'.$info['inter_process_day'].'</DispatchTimeMax>';
        }

        $xml .='<ShippingDetails>';
        $xml .='<ShippingServiceOptions>';
        $xml .='<ShippingServicePriority>1</ShippingServicePriority>';
        $xml .=' <ShippingService>'.$info['inter_trans_type'].'</ShippingService>';
        if($info['inter_free']=='true')
        {
            $xml .='<FreeShipping>true</FreeShipping>' ;
        }
        else
        {
            if(!empty($info['inter_trans_cost']))
            {
                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'">'.$info['inter_trans_cost'].'</ShippingServiceCost>';
            }
            if(!empty($info['inter_trans_extracost']))
            {
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'">'.$info['inter_trans_extracost'].'</ShippingServiceAdditionalCost>';
            }
            /* if(!empty($info['inter_trans_AK_extracost']))
             {
                 $xml .='<ShippingSurcharge crenccuyID="'.$sitecurrency.'">'.$info['inter_trans_AK_extracost'].'</ShippingSurcharge>';
             }*/
        }
        $xml .='</ShippingServiceOptions>';


        if(!empty($info['international_type1']))
        {
            $xml .='<InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>1</ShippingServicePriority>';
            $xml .='<ShippingService>'.$info['international_type1'].'</ShippingService>';

                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'" >'.$info['international_cost1'].'</ShippingServiceCost>';
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'" >'.$info['international_extracost1'].'</ShippingServiceAdditionalCost>';

            if($info['international_is_worldwide1']=='on')
            {
                $xml .='<ShipToLocation>Worldwide</ShipToLocation>';
            }
            else
            {
                $country = json_decode($info['international_is_country1'],true);
                foreach($country as $co)
                {
                    $xml .='<ShipToLocation>'.$co.'</ShipToLocation>';
                }
            }
            $xml .='</InternationalShippingServiceOption>';
        }
        if(!empty($info['international_type2']))
        {
            $xml .='<InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>2</ShippingServicePriority>';
            $xml .='<ShippingService>'.$info['international_type2'].'</ShippingService>';

                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'" >'.$info['international_cost2'].'</ShippingServiceCost>';
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'" >'.$info['international_extracost2'].'</ShippingServiceAdditionalCost>';

            if($info['international_is_worldwide2']=='on')
            {
                $xml .='<ShipToLocation>Worldwide</ShipToLocation>';
            }
            else
            {
                $country = json_decode($info['international_is_country2'],true);
                foreach($country as $co)
                {
                    $xml .='<ShipToLocation>'.$co.'</ShipToLocation>';
                }
            }
            $xml .='</InternationalShippingServiceOption>';
        }
        if(!empty($info['international_type3']))
        {
            $xml .='<InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>3</ShippingServicePriority>';
            $xml .='<ShippingService>'.$info['international_type3'].'</ShippingService>';


                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'" >'.$info['international_cost3'].'</ShippingServiceCost>';
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'" >'.$info['international_extracost3'].'</ShippingServiceAdditionalCost>';

            if($info['international_is_worldwide3']=='on')
            {
                $xml .='<ShipToLocation>Worldwide</ShipToLocation>';
            }
            else
            {
                $country = json_decode($info['international_is_country3'],true);
                foreach($country as $co)
                {
                    $xml .='<ShipToLocation>'.$co.'</ShipToLocation>';
                }
            }
            $xml .='</InternationalShippingServiceOption>';
        }
        if(!empty($info['international_type4']))
        {
            $xml .='<InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>4</ShippingServicePriority>';
            $xml .='<ShippingService>'.$info['international_type4'].'</ShippingService>';


                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'" >'.$info['international_cost4'].'</ShippingServiceCost>';
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'" >'.$info['international_extracost4'].'</ShippingServiceAdditionalCost>';

            if($info['international_is_worldwide4']=='on')
            {
                $xml .='<ShipToLocation>Worldwide</ShipToLocation>';
            }
            else
            {
                $country = json_decode($info['international_is_country4'],true);
                foreach($country as $co)
                {
                    $xml .='<ShipToLocation>'.$co.'</ShipToLocation>';
                }
            }
            $xml .='</InternationalShippingServiceOption>';
        }
        if(!empty($info['international_type5']))
        {
            $xml .='</InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>5</ShippingServicePriority>';
            $xml .='<ShippingService>'.$info['international_type5'].'</ShippingService>';


                $xml .='<ShippingServiceCost crenccuyID="'.$sitecurrency.'" >'.$info['international_cost5'].'</ShippingServiceCost>';
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$sitecurrency.'" >'.$info['international_extracost5'].'</ShippingServiceAdditionalCost>';

            if($info['international_is_worldwide5']=='on')
            {
                $xml .='<ShipToLocation>Worldwide</ShipToLocation>';
            }
            else
            {
                $country = json_decode($info['international_is_country5'],true);
                foreach($country as $co)
                {
                    $xml .='<ShipToLocation>'.$co.'</ShipToLocation>';
                }
            }
            $xml .='</InternationalShippingServiceOption>';
        }

        if(!empty($info['excludeship']))
        {
            $excludeshiparr = explode(',',$info['excludeship']);
            foreach($excludeshiparr as $v)
            {
                $xml .='<ExcludeShipToLocation>'.$v.'</ExcludeShipToLocation>';
            }
        }
        $xml .='</ShippingDetails>';

        if($info['all_buyers']=='notall')
        {
            $xml .='<BuyerRequirementDetails>';
            if($info['nopaypal']=='on')
            {
                $xml .='<LinkedPayPalAccount>true</LinkedPayPalAccount>';
            }
            if($info['noti_trans']=='on')
            {
                $xml .='<ShipToRegistrationCountry>true</ShipToRegistrationCountry>';
            }

            if($info['is_abandoned']=='on')
            {
                $xml .='<MaximumUnpaidItemStrikesInfo><Count>'.$info['abandoned_num'].'</Count><Period>'.$info['abandoned_day'].'</Period></MaximumUnpaidItemStrikesInfo>';
            }
            if($info['is_report']=='on')
            {
                $xml .='<MaximumBuyerPolicyViolations><Count>'.$info['report_num'].'</Count><Period>'.$info['report_day'].'</Period></MaximumBuyerPolicyViolations>';
            }
            if($info['is_trust_low']=='on')
            {
                $xml .='<MinimumFeedbackScore>'.$info['trust_low_num'].'</MinimumFeedbackScore>';
            }
            if($info['already_buy']=='on')
            {
                $xml .=' <MaximumItemRequirements><MaximumItemCount>'.$info['buy_num'].'</MaximumItemCount>';
                if($info['buy_condition']=='on')
                {
                    $xml .='<MinimumFeedbackScore>'.$info['buy_credit'].'</MinimumFeedbackScore></MaximumItemRequirements>';
                }
                else
                {
                    $xml .='</MaximumItemRequirements>';
                }
            }
            $xml .='</BuyerRequirementDetails>';
        }



        if($info['ad_type']=='paimai')
        {
            $xml .='<ListingType>Chinese</ListingType>';

            $xml .='<StartPrice currencyID="'.$sitecurrency.'">'.$info['price'].'</StartPrice>';

            if(!empty($info['reserve_price']))
            {
                $xml .='<ReservePrice  currencyID="'.$sitecurrency.'">'.$info['reserve_price'].'</ReservePrice>';
            }
            if(!empty($info['price_noce']))
            {
                $xml .= '<BuyItNowPrice currencyID="'.$sitecurrency.'">'.$info['price_noce'].'</BuyItNowPrice>';
            }

            $xml .='<Quantity>'.$info['quantity'].'</Quantity>';

        }
        if($info['ad_type']=='guding')
        {
            $xml .='<ListingType>FixedPriceItem</ListingType>';
            $xml .='<StartPrice currencyID="'.$sitecurrency.'">'.$info['price'].'</StartPrice>';
            $xml .='<Quantity>'.$info['quantity'].'</Quantity>';

        }
        if($info['ad_type']=='duoshuxing')
        {
            $xml .='<ListingType>FixedPriceItem</ListingType>';

            $mul_info = json_decode($info['mul_info'],true);
            $add_mul =json_decode($info['add_mul'],true);
            $xml .='<Variations>';
            $xml .='<VariationSpecificsSet>';
            foreach($add_mul as $add)
            {
                if(($add=='UPC')||($add=='EAN')){
                    continue;
                }
                $arrdif = array();
                $xml .='<NameValueList><Name>'.$add.'</Name>';
                foreach($mul_info as $mul)
                {
                    if(in_array($mul[$add],$arrdif))
                    {
                        continue;
                    }
                    $arrdif[] = $mul[$add];
                    $xml .='<Value>'.$mul[$add].'</Value>';
                }
                $xml .='</NameValueList>';
            }
            $xml .='</VariationSpecificsSet>';

            foreach($mul_info as $in)
            {

                $xml .='<Variation>';
                $xml .='<SKU>'.$in['sku'].'</SKU>';
                $xml .='<StartPrice >'.$in['price'].'</StartPrice>';
                $xml .='<Quantity>'.$in['qc'].'</Quantity>';

                if(isset($in['UPC'])||isset($in['EAN'])){
                    $xml .='<VariationProductListingDetails>';
                    if(isset($in['UPC'])){
                        $xml .='<UPC>'.$in['UPC'].'</UPC>';
                    }
                    if(isset($in['EAN'])){
                        $xml .='<EAN>'.$in['EAN'].'</EAN>';
                    }
                    $xml .='</VariationProductListingDetails>';
                }

                $xml .='<VariationSpecifics>';

                foreach($add_mul as $ad)
                {
                    if(($ad=='UPC')||($ad=='EAN')){
                        continue;
                    }
                    $xml .='<NameValueList>';
                    $xml .='<Name>'.$ad.'</Name>';
                    $xml .='<Value>'.$in[$ad].'</Value>';
                    $xml .='</NameValueList>';
                }
                $xml .='</VariationSpecifics>';
                $xml .='</Variation>';
            }

            if(!empty($info['mul_picture']))
            {
                $xml .='<Pictures>';
                $picinfo = json_decode($info['mul_picture'],true);
                foreach($picinfo as $key=>$p)
                {
                    $xml .='<VariationSpecificName>'.$key.'</VariationSpecificName>';
                    foreach($p as $v_1 => $v_2)
                    {
                        $xml .='<VariationSpecificPictureSet><VariationSpecificValue>'.$v_1.'</VariationSpecificValue>';
                        foreach($v_2 as $picmul)
                        {
                            $xml .=' <PictureURL>'.$picmul.'</PictureURL>';
                        }
                        $xml .='</VariationSpecificPictureSet>';
                    }
                }
                $xml .='</Pictures>';
            }
            $xml .='</Variations>';
        }
        $template['id'] = $info['publication_template'];
        $resulttemplate = $this->ebaytemplate->getTemplateAll($template);

        $templatehtml['id'] = $info['publication_template_html'];
        $resulttemplatehtml = $this->ebaytemplatehtml->getTemplateAll($templatehtml);

        if(isset($resulttemplate[0]))
        {
            if(isset($resulttemplatehtml[0]))
            {
                $resulttemplatehtml[0]['template_html'] =str_replace('{{tittle}}',$info['template_title'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{paymentterms}}',$resulttemplate[0]['payment'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{termsofsales}}',$resulttemplate[0]['sales_policy'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{shippingterms}}',$resulttemplate[0]['shipping'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{contactus}}',$resulttemplate[0]['contact_us'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{aboutus}}',$resulttemplate[0]['about_us'],$resulttemplatehtml[0]['template_html']);
                $resulttemplatehtml[0]['template_html'] =str_replace('{{description}}',$info['description_details'],$resulttemplatehtml[0]['template_html']);
                if(!empty($info['template_deteils']))
                {
                    $picarr = json_decode($info['template_deteils'],true);
                    $picbig='';
                    $picsamll ='';
                    for($i=0;$i<count($picarr);$i++)
                    {
                        if($i==0)
                        {
                            $picbig = '<div class="albumBigImgBox"><img width="600" src="'.$picarr[$i].'" alt="" name="bigImg" id="bigImg"></div>';
                        }
                        else
                        {
                            $picsamll =$picsamll.'<div alt="" class="smallImgBox"><img width="92" height="92" alt=""  onmouseover="document.getElementById('."'bigImg'".').src=this.src"   src="'.$picarr[$i].'"></div>';
                        }
                    }

                    $resulttemplatehtml[0]['template_html'] =str_replace('{{Picture1}}',$picbig ,$resulttemplatehtml[0]['template_html']);
                    $resulttemplatehtml[0]['template_html'] =str_replace('{{Picture2}}',$picsamll,$resulttemplatehtml[0]['template_html']);
                }  else{
                    $resulttemplatehtml[0]['template_html'] =str_replace('{{Picture1}}','' ,$resulttemplatehtml[0]['template_html']);
                    $resulttemplatehtml[0]['template_html'] =str_replace('{{Picture2}}','',$resulttemplatehtml[0]['template_html']);
                }

                $xml .='<Description><![CDATA['.$resulttemplatehtml[0]['template_html'].']]></Description>';
            }
            else
            {
                $templateinfo = '<h2><span class="Pa_head"><span class="Pa_headc">Payment Method <hr /></span></span></h2>'.$resulttemplate[0]['payment'].'<h2> <span class="Pa_head"><span class="Pa_headc">Shipping Detail<hr /> </span></span> </h2>'.$resulttemplate[0]['shipping'].'<h2> <span class="Pa_head"><span class="Pa_headc"> Sales Policy<hr /> </span></span> </h2>'.$resulttemplate[0]['sales_policy'].'<h2> <span class="Pa_head"><span class="Pa_headc">About Us<hr /> </span></span> </h2>'.$resulttemplate[0]['about_us'].'<h2> <span class="Pa_head"><span class="Pa_headc"> Contact Us <hr /> </span></span> </h2>'.$resulttemplate[0]['contact_us'];
                $xml.='<Description><![CDATA['.trim($info['description_details']).$templateinfo.']]></Description>';

            }
        }
        else
        {
            $xml.='<Description><![CDATA['.trim($info['description_details']).']]></Description>';

        }
        $xml .='</Item>';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
        if($info['ad_type']=='duoshuxing')
        {
            $xml .='</VerifyAddFixedPriceItemRequest>';
        }
        else
        {
            $xml .='</VerifyAddItemRequest>';
        }
      //   var_dump($xml);exit;
        $info = $this->ebaytest->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($info);
        $response = simplexml_import_dom($responseDoc);


         if(isset($response->ItemID))
        {
            $feedarray=array();
            $feeds = $response->Fees;
            foreach($feeds as $feed)
            {

                foreach($feed as $fe)
                {
                    if((string)$fe->Fee !='0.0')
                    {
                          $feedarray[(string)$fe->Name] = (string)$fe->Fee;

                    }
                }

            }
          return $feedarray;
        }
        else
        {
            return (string)$response->Errors->LongMessage;
        }
    }



    // 只修改SKU价格数量(不影响商品的排名)  $type = 1;拍卖  $type = 2 固定 $type = 3 多属性
    public function  modifySkuInfo($info,$type)
    {
        $arr['id'] =$info['id'];
        $listinfo = $this->list->getEbayListOne($arr);
        if($listinfo['status']!=2)
        {
            return false;
        }
        $result = $this->userToken->getInfoByAccount($listinfo['ebayaccount']);
        $xml ='';
        $xml .="<?xml version='1.0' encoding='utf-8'?>";
        $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$listinfo['site'],'ReviseInventoryStatus');
        $xml .='<ReviseInventoryStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
        $xml .='<ErrorLanguage>en_US</ErrorLanguage><WarningLevel>High</WarningLevel>';
        if($type==1) //拍卖暂略
        {

        }
        if($type==2)//固定价格
        {
            $xml .='<InventoryStatus><ItemID>'.$listinfo['itemid'].'</ItemID><StartPrice>'.$info['price'].'</StartPrice><Quantity>'.$info['quantity'].'</Quantity></InventoryStatus>';
        }
        if($type==3)//多属性
        {


            foreach($info['modify_sku'] as $k=>$mul)
            {
                $xml .='<InventoryStatus><ItemID>'.$listinfo['itemid'].'</ItemID><SKU>'.$k.'</SKU>';
                if(isset($mul['price']))
                {
                    $xml.='<StartPrice>'.$mul['price'].'</StartPrice>';
                }
                if(isset($mul['qc']))
                {
                    $xml.='<Quantity>'.$mul['qc'].'</Quantity>';
                }

                $xml.='</InventoryStatus>';
            }
        }
        $xml .='</ReviseInventoryStatusRequest>';
        $info = $this->ebaytest->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($info);
        $response = simplexml_import_dom($responseDoc);
        if((string)$response->Ack=='Success')
        {
            return 'Success';
        }
        else
        {
            return    (string)$response->Errors->LongMessage;
        }
    }


    // 修改在线sku相关信息
    public function modifySkuInfoLot($info,$type)
    {
//var_dump($info['mul_info']);exit;
        $arr['id'] =$info['id'];
        $listinfo = $this->list->getEbayListOne($arr);
        if($listinfo['status']!=2)
        {
            return false;
        }
        $result = $this->userToken->getInfoByAccount($listinfo['ebayaccount']);
        $xml ='';
        $xml .="<?xml version='1.0' encoding='utf-8'?>";
        $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$listinfo['site'],'ReviseFixedPriceItem');
        $xml .='<ReviseFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                <ErrorLanguage>en_US</ErrorLanguage>
                <WarningLevel>High</WarningLevel>
                <Item>
                <ItemID>'.$listinfo['itemid'].'</ItemID>';
        $xml .='<Variations>';


        if((isset($info['delete_sku']))&&(!empty($info['delete_sku'])))
        {
            foreach($info['delete_sku'] as $ardelete)
            {
                $xml .='<Variation>';

                $xml .='<Delete>true</Delete>';
                $xml .='<SKU>'.$ardelete.'</SKU>';
                $xml .='</Variation>';
            }
        }


        $info['mul_info']  = json_decode($info['mul_info'],true);
                    foreach($info['mul_info'] as $aradd)
                    {
                        $isset_variationSpecifics = false;

                        $xml .='<Variation>';

                        foreach($aradd as $k=>$addsku)
                        {


                            if($k=='sku')
                            {
                                $xml .='<SKU>'.$addsku.'</SKU>';

                            } elseif($k=='qc') {

                                $xml .='<Quantity>'.$addsku.'</Quantity>';
                            } elseif($k=='price') {
                                $xml .='<StartPrice>'.$addsku.'</StartPrice>';
                            }
                         /*   else
                            {

                                if($isset_variationSpecifics)
                                {
                                    $xml .='<NameValueList>';
                                    $xml .='<Name>'.$k.'</Name>';
                                    $xml .='<Value>'.$addsku.'</Value>';
                                    $xml .='</NameValueList>';
                                }
                                else
                                {
                                    $isset_variationSpecifics = true;
                                    $xml .='<VariationSpecifics>';
                                    $xml .='<NameValueList>';
                                    $xml .='<Name>'.$k.'</Name>';
                                    $xml .='<Value>'.$addsku.'</Value>';
                                    $xml .='</NameValueList>';
                                }
                            }*/

                        }
                        if($isset_variationSpecifics)
                        {
                            $xml .='</VariationSpecifics>';
                        }

                        $xml .='</Variation>';


                    }

        $xml .='</Variations>';
        $xml .='</Item>';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
        $xml .='</ReviseFixedPriceItemRequest>';
     //   var_dump($xml);exit;
        $info = $this->ebaytest->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($info);
        $response = simplexml_import_dom($responseDoc);
      //  var_dump($response);exit;
        if(isset($response->ItemID))
        {
            return 'Success';
        }
        else
        {
            return    (string)$response->Errors->LongMessage;
        }
    }


    public function  ebayoperationLog($listid,$specificissues)
    {
       $arr = array();
        $arr['userid'] = $this->user_info->id;//登录用户id
        $arr['listid'] =$listid;
        $arr['specificissues'] = $specificissues;
        $arr['createtime']  = date('Y-m-d H:i:s', time());
        $this->operatelog->inserinfo($arr);
    }

}