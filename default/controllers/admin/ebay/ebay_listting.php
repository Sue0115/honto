<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-07-23
 * Time: 13:18
 */

set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");

class Ebay_listting extends MY_Controller{
    protected $ebay;
    protected $userToken;
    protected $model;

    function __construct()
    {
        parent::__construct();
        $this->load->library('MyEbayNew');
        $this->load->model(array(
            'ebay/Ebay_user_tokens_model',
            'ebay/Ebay_list_model',
            'ebay/Ebay_operationlog_model',
            'Sf_product_itemid_model',
            'ebay/Ebay_product_model',

        ));
        $this->ebaytest = new MyEbayNew();
        $this->userToken = $this->Ebay_user_tokens_model;
    }

//ActiveList.ItemArray.Item.Variations.Variation.VariationSpecifics.NameValueList



    function getEbayListting($page=1)
    {

        $pageSize = 1000;


        echo '开始时间 '.date('Y-m-d H:i:s');
        echo '</br>';
        $needlist = $this->Ebay_list_model->getactivelistting($page,$pageSize);

        $accountlist = $this->Ebay_user_tokens_model->getAll2Array($options=array());
        $accountarr=array();
        foreach($accountlist as $account)
        {
            $accountarr[$account['token_id']] = $account['seller_account'];
        }
        $SellerPrefix = $this->Ebay_list_model->defineEbaySellerPrefix();

        foreach($needlist as $list) {



            $update_option = array();
            $update_option['where']['item_number'] = $list['item_number'];
            $update_data = array();
            $update_data['getDetailsTime'] =date('Y-m-d H:i:s',time());

            $this->Sf_product_itemid_model->update($update_data,$update_option);

            unset($update_option);
            unset($update_data);
          //  $list['token_id'] = 15;
            $result = $this->userToken->getInfoByTokenId($list['token_id']);
            $site = 0;
            $this->ebaytest->setinfo($result['user_token'], $result['devid'], $result['appid'], $result['certid'], $site, 'GetItem');

            $requestXmlBody = '';
            $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>';
            $requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
            $requestXmlBody .= '<RequesterCredentials>';
            $requestXmlBody .= '<eBayAuthToken>' . $result['user_token'] . '</eBayAuthToken>';
            $requestXmlBody .= '</RequesterCredentials>';
            $requestXmlBody .= '<ItemID>'.$list['item_number'].'</ItemID>';
            $requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
            $requestXmlBody .= '<IncludeItemSpecifics>true</IncludeItemSpecifics>';
            $requestXmlBody .= '</GetItemRequest>	';
            $ItemInfo = $this->ebaytest->sendHttpRequest($requestXmlBody);

            $return_array = $this->XmlToArray($ItemInfo);

           //    var_dump($return_array);exit;
            $newarray = array();
            if(!isset($return_array['Item']))
            {
                continue;
            }
            //获取物流信息
            if (isset($return_array['Item']['ShippingDetails']['ShippingServiceOptions'])) {
                $newarray['inter_trans_type'] = isset($return_array['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingService'])?$return_array['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingService']:'';
                $newarray['inter_trans_cost'] = isset($return_array['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost'])?$return_array['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']:'';
                if (isset($return_array['Item']['ShippingDetails']['ShippingServiceOptions']['FreeShipping'])) {
                    if ($return_array['Item']['ShippingDetails']['ShippingServiceOptions']['FreeShipping'] == 'true') {
                        $newarray['inter_free'] = 'true';
                    }

                }

                $newarray['inter_trans_extracost'] = isset($return_array['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceAdditionalCost'])?$return_array['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceAdditionalCost']:'';
            }

            if (isset($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption'])) {

                if(isset($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption'][1]))
                {
                        foreach($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption'] as $key=>$v)
                        {
                                $num= $v['ShippingServicePriority'];
                            $newarray['international_type'.$num] = $v['ShippingService'];
                            $newarray['international_cost'.$num] = $v['ShippingServiceCost'];
                            $newarray['international_extracost'.$num] = $v['ShippingServiceAdditionalCost'];

                            if(isset($v['FreeShipping'])&&($v['FreeShipping']))
                            {
                                $newarray['international_free'.$num] ='true';
                            }
                            if(isset($v['ShipToLocation']))
                            {
                                if(($v['ShipToLocation']=='Worldwide'))
                                {
                                    $newarray['international_is_worldwide'.$num]='on';
                                }
                                else
                                {
                                    if(is_array($v['ShipToLocation']))
                                    {
                                        $newarray['international_is_country'.$num]=json_encode($v['ShipToLocation']);

                                    }
                                    else
                                    {
                                        $ShipToLocation= array();
                                        $ShipToLocation[] = $v['ShipToLocation'];
                                        //international_is_country1
                                        $newarray['international_is_country'.$num]=json_encode($ShipToLocation);
                                    }
                                }
                            }
                        }
                }
                else //单个国际运输选项的时候
                {

                    $newarray['international_type1'] = isset($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShippingService'])?$return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShippingService']:'';
                    $newarray['international_cost1'] = isset($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShippingServiceCost'])?$return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShippingServiceCost']:'';
                    if (isset($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['FreeShipping'])) {
                        if ($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['FreeShipping'] == 'true') {
                            $newarray['international_free1'] = 'true';
                        }

                    }

                    $newarray['international_extracost1'] = isset($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShippingServiceAdditionalCost'])?$return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShippingServiceAdditionalCost']:'';


                    if(isset( $return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShipToLocation']))
                    {
                        if(( $return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShipToLocation']=='Worldwide'))
                        {
                            $newarray['international_is_worldwide1']='on';
                        }
                        else
                        {
                            if(is_array($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShipToLocation']))
                            {


                                    $newarray['international_is_country1'] = json_encode($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShipToLocation']);

                            }
                            else
                            {
                                $ShipToLocation= array();
                                $ShipToLocation[] = $return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShipToLocation'];
                                $newarray['international_is_country1']=json_encode($ShipToLocation);

                                }
                        }

                    }
                }
            }

          // var_dump($newarray);exit;
            $newarray['title'] = isset($return_array['Item']['Title']) ? $return_array['Item']['Title'] : '';
            $newarray['itemid'] = isset($return_array['Item']['ItemID']) ? $return_array['Item']['ItemID'] : '';
            $newarray['sku'] = isset($return_array['Item']['SKU']) ? $return_array['Item']['SKU'] : '';
            $prefix =   explode('*', $newarray['sku']);
            $newarray['ad_user'] = isset($SellerPrefix[(string)$prefix[0]])?$SellerPrefix[(string)$prefix[0]]:0;
            $ItemSpecifics = isset($return_array['Item']['ItemSpecifics']) ? $return_array['Item']['ItemSpecifics'] : '';


            $ItemSpecificsArr = array();
            if (!empty($ItemSpecifics)) {

                foreach ($ItemSpecifics as $spe) {
                    foreach ($spe as $sp) {
                        $ItemSpecificsArr[][$sp['Name']] = $sp['Value'];
                    }

                }
            }
            $newarray['item_specifics'] = json_encode($ItemSpecificsArr); //SKU 的属性


            $mul_info = isset($return_array['Item']['Variations']) ? $return_array['Item']['Variations'] : '';
            // var_dump($mul_info);exit;

            $mul_infoArr = array();
            $add_mulArr = array();
            if (!empty($mul_info)) {
                $i = 0;
                foreach ($mul_info['Variation'] as $mul) {
                    if($i==0){
                        $prefix =   explode('*',$mul['SKU']);
                        $newarray['ad_user'] = isset($SellerPrefix[(string)$prefix[0]])?$SellerPrefix[(string)$prefix[0]]:0;
                    }
                    $mul_infoArr[$i]['sku'] = $mul['SKU'];
                    $mul_infoArr[$i]['qc'] = $mul['Quantity'];
                    $mul_infoArr[$i]['price'] = $mul['StartPrice'];
                    if (isset($mul['VariationSpecifics'])) {
                        foreach ($mul['VariationSpecifics'] as $m) {
                            if (isset($m[0])) {
                                foreach ($m as $v) {
                                    $add_mulArr[] = $v['Name'];
                                    $mul_infoArr[$i][$v['Name']] = $v['Value'];
                                }
                            } else {
                                $add_mulArr[] = $m['Name'];
                                $mul_infoArr[$i][$m['Name']] = $m['Value'];
                            }

                        }
                    }
                    $i++;
                }
            }


            $add_mulArr = array_unique($add_mulArr);
            if(!empty($mul_infoArr))
            {
                $newarray['mul_info'] = json_encode($mul_infoArr);
            }
           if(!empty($add_mulArr))
           {
               $newarray['add_mul'] = json_encode($add_mulArr);
           }

            $mul_picture = isset($return_array['Item']['Variations']['Pictures']) ? $return_array['Item']['Variations']['Pictures'] : '';
            //  var_dump($mul_picture);
            $mul_pictureArr = array();
            if (!empty($mul_picture)) {

                foreach ($mul_picture['VariationSpecificPictureSet'] as $pic) {
                    if (is_array($pic['PictureURL'])) {
                        $mul_pictureArr[$mul_picture['VariationSpecificName']][$pic['VariationSpecificValue']] = $pic['PictureURL'];
                    } else {
                        $mul_pictureArr[$mul_picture['VariationSpecificName']][$pic['VariationSpecificValue']][] = $pic['PictureURL'];
                    }

                }
            }

            $newarray['mul_picture'] = json_encode($mul_pictureArr); //多属性图片

            $ebay_picture = isset($return_array['Item']['PictureDetails']) ? $return_array['Item']['PictureDetails'] : '';
            //   var_dump($ebay_picture);exit;
            $ebay_pictureArr = array();
            if (!empty($ebay_picture)) {

                if (is_array($ebay_picture['PictureURL'])) {
                    $ebay_pictureArr = $ebay_picture['PictureURL'];
                } else {
                    $ebay_pictureArr[] = $ebay_picture['PictureURL'];
                }

            }
            $newarray['ebay_picture'] = json_encode($ebay_pictureArr);

            $newarray['published_day'] = isset($return_array['Item']['ListingDuration']) ? $return_array['Item']['ListingDuration'] : '';
            $newarray['price'] = isset($return_array['Item']['StartPrice']) ? $return_array['Item']['StartPrice'] : 0.00;
            $newarray['reserve_price'] = isset($return_array['Item']['ReservePrice']) ? $return_array['Item']['ReservePrice'] : 0.00;
            $newarray['price_noce'] = isset($return_array['Item']['BuyItNowPrice']) ? $return_array['Item']['BuyItNowPrice'] : 0.00;
            $newarray['quantity'] = isset($return_array['Item']['Quantity']) ? $return_array['Item']['Quantity'] : 0;
            $newarray['paypal_account'] = isset($return_array['Item']['PayPalEmailAddress']) ? $return_array['Item']['PayPalEmailAddress'] : '';

            $newarray['inter_process_day'] =isset($return_array['Item']['DispatchTimeMax'])?$return_array['Item']['DispatchTimeMax']:'';

            if($newarray['inter_process_day']==0)
            {
                $newarray['inter_fast_send'] = 'true';
            }



            //退货信息

            $newarray['returns_policy'] = isset($return_array['Item']['ReturnPolicy']['ReturnsAcceptedOption']) ? $return_array['Item']['ReturnPolicy']['ReturnsAcceptedOption'] : '';
            $newarray['returns_days'] = isset($return_array['Item']['ReturnPolicy']['ReturnsWithinOption']) ? $return_array['Item']['ReturnPolicy']['ReturnsWithinOption'] : '';
            $newarray['returns_type'] = isset($return_array['Item']['ReturnPolicy']['RefundOption']) ? $return_array['Item']['ReturnPolicy']['RefundOption'] : '';
            $newarray['returns_cost_by'] = isset($return_array['Item']['ReturnPolicy']['ShippingCostPaidByOption']) ? $return_array['Item']['ReturnPolicy']['ShippingCostPaidByOption'] : '';
           // $newarray['returns_delay'] = isset($return_array['Item']['ReturnPolicy']['ExtendedHolidayReturns']) ? $return_array['Item']['ReturnPolicy']['ExtendedHolidayReturns'] : '';


            $newarray['return_details'] = isset($return_array['Item']['ReturnPolicy']['Description']) ? $return_array['Item']['ReturnPolicy']['Description'] : '';

            if(isset($return_array['Item']['ReturnPolicy']['ExtendedHolidayReturns']))
            {
                $newarray['return_delay']='on';
            }

            $newarray['item_location'] = isset($return_array['Item']['Location']) ? $return_array['Item']['Location'] : '';
            $newarray['item_country'] = isset($return_array['Item']['Country']) ? $return_array['Item']['Country'] : '';
            $newarray['item_post'] = isset($return_array['Item']['PostalCode']) ? $return_array['Item']['PostalCode'] : 0;


            //卖家要求
            $newarray['all_buyers']='notall';
            $newarray['noti_trans']='on';
            $newarray['already_buy']='on';
            $newarray['buy_num'] = 5;


            $newarray['categoty1'] = isset($return_array['Item']['PrimaryCategory']['CategoryID']) ? $return_array['Item']['PrimaryCategory']['CategoryID'] : '';
            $newarray['categoty1_all'] = isset($return_array['Item']['PrimaryCategory']['CategoryName']) ? $return_array['Item']['PrimaryCategory']['CategoryName'] : '';
            $newarray['site'] = $list['siteID'];
            $newarray['ebayaccount'] = $accountarr[$list['token_id']];

            $itemStartTime = isset($return_array['Item']['ListingDetails']['StartTime'])?$return_array['Item']['ListingDetails']['StartTime']:'';
            if(!empty($itemStartTime))
            {
                $newarray['starttime'] = date('Y-m-d H:i:s',strtotime($itemStartTime));
            }
            $newarray['sellallnum'] = isset($return_array['Item']['SellingStatus']['QuantitySold'])?$return_array['Item']['SellingStatus']['QuantitySold']:'';

            if(!isset($return_array['Item']['ListingType']))
            {
                continue;
            }
            //ListingType
            if (($return_array['Item']['ListingType'] == 'FixedPriceItem') && (isset($return_array['Item']['Variations']))) {
                $newarray['ad_type'] = 'duoshuxing';
            }
            if (($return_array['Item']['ListingType'] == 'FixedPriceItem') && (!isset($return_array['Item']['Variations']))) {
                $newarray['ad_type'] = 'guding';
            }
            if ($return_array['Item']['ListingType'] == 'Chinese') {
                $newarray['ad_type'] = 'paimai';
            }
            //   $description_details  =isset($return_array['Item']['Description'])?htmlspecialchars_decode($return_array['Item']['Description']):'';
            $newarray['description_details'] = isset($return_array['Item']['Description']) ? htmlspecialchars_decode($return_array['Item']['Description']) : '';


            //在这里处理一下sku_search   mul_info // 如果是单属性，就已SKU 为基准， 如果是多属性，不能以sku为基准
            $newarray['sku_search'] ='';
            if($newarray['ad_type']=='duoshuxing'){

                $mid_array = json_decode($newarray['mul_info'],true);

                $newarray['sku_search'] =  $this->removeSaleTag($mid_array[0]['sku']);



            }else{
                $newarray['sku_search'] =  $this->removeSaleTag($newarray['sku']);
            }






            $newarray['status'] = 2;//已刊登
            $newarray['updatetime'] =  date("Y-m-d H:i:s",time());
            $check['where']['itemid'] = $newarray['itemid'];
            $checkarr= $this->Ebay_list_model->getOne($check,true);
            if(!empty($checkarr))
            {
                unset($newarray['itemid']);
                $this->Ebay_list_model->update($newarray,$check);
            }
            else
            {
                $this->Ebay_list_model->add($newarray);
            }

        }

        //处理一下 已经下线的listting
        $this->handleUnActiveListting();


        echo '结束时间 '.date('Y-m-d H:i:s');
        
    }


    public  function handleUnActiveListting()
    {
        $result = $this->Ebay_list_model->getUnActiveListting();
        if(!empty($result))
        {
            foreach($result as $re)
            {

                $option = array();
                $option['where']['itemid'] = $re['item_number'];

                $data = array();
                $data['status'] = 4;

               $modify_id =  $this->Ebay_list_model->update($data,$option);

                unset($option);
                unset($data);

                if($modify_id)
                {
                    $info = array();
                    $info['userid'] = 0;
                  //  $info['listid'] = $modify_id;
                    $info['specificissues'] = '系统将该条广告改为下架状态'.$re['item_number'];
                    $info['createtime'] =date('Y-m-d H:i:s',time());

                    $this->Ebay_operationlog_model->add($info);

                }
            }
        }
    }

    public function removeSaleTag($sku)
    {
        $n = strpos($sku, '*');
        $sku_new = $n !== false ? substr($sku, $n+1) : $sku;

        // 去除sku的帐户代码
        $n = strpos($sku_new, '#');
        $sku_new = $n !== false ? substr($sku_new, 0, $n) : $sku_new;


        if ( strpos($sku_new, '(') !== false ) {
            $matches = array();
            preg_match_all("/(.*?)\([a-z]?([0-9]*)\)?/i", $sku_new, $matches);
            $sku_new = trim( $matches[1][0] );
        }

        return $sku_new;
    }


    function XmlToArray($xml)
    {
        $array = (array)(simplexml_load_string($xml));
        foreach ($array as $key=>$item){

            $array[$key]  = $this->struct_to_array((array)$item);
        }
        return $array;
    }

    function struct_to_array($item)
    {
        if(!is_string($item)) {

            $item = (array)$item;
            foreach ($item as $key=>$val){

                $item[$key]  = $this->struct_to_array($val);
            }
        }
        return $item;
    }


    public function ajaxGetItem(){

        $accountlist = $this->Ebay_user_tokens_model->getAll2Array($options=array());
        $accountarr=array();
        foreach($accountlist as $account)
        {
            $accountarr[$account['token_id']] = $account['seller_account'];
        }

        $ids = $_POST['ids'];
        $ids = explode(',',$ids);
        $id = $ids[0];
        $result_one  =  $this->Ebay_product_model->getOne(array('where'=>array('id'=>$id)),true);

        $item_number = $result_one['itemid'];
      //  $item_number = 272234419784;

    //   $result_one  =  $this->Ebay_product_model->getOne(array('where'=>array('itemid'=>$item_number)),true);

        $result = $this->userToken->getInfoByTokenId($result_one['account_id']);
        $site = $result_one['site'];
        $this->ebaytest->setinfo($result['user_token'], $result['devid'], $result['appid'], $result['certid'], $site, 'GetItem');

        $requestXmlBody = '';
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>';
        $requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= '<RequesterCredentials>';
        $requestXmlBody .= '<eBayAuthToken>' . $result['user_token'] . '</eBayAuthToken>';
        $requestXmlBody .= '</RequesterCredentials>';
        $requestXmlBody .= '<ItemID>'.$item_number.'</ItemID>';
        $requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
        $requestXmlBody .= '<IncludeItemSpecifics>true</IncludeItemSpecifics>';
        $requestXmlBody .= '</GetItemRequest>	';
        $ItemInfo = $this->ebaytest->sendHttpRequest($requestXmlBody);

        $return_array = $this->XmlToArray($ItemInfo);
       // var_dump($return_array);exit;

        //    var_dump($return_array);exit;

        $SellerPrefix = $this->Ebay_list_model->defineEbaySellerPrefix();
        $newarray = array();
        if(!isset($return_array['Item']))
        {
            ajax_return('同步失败，未找到信心',2);
        }
        //获取物流信息
        if (isset($return_array['Item']['ShippingDetails']['ShippingServiceOptions'])) {
            $newarray['inter_trans_type'] = isset($return_array['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingService'])?$return_array['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingService']:'';
            $newarray['inter_trans_cost'] = isset($return_array['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost'])?$return_array['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']:'';
            if (isset($return_array['Item']['ShippingDetails']['ShippingServiceOptions']['FreeShipping'])) {
                if ($return_array['Item']['ShippingDetails']['ShippingServiceOptions']['FreeShipping'] == 'true') {
                    $newarray['inter_free'] = 'true';
                }

            }

            $newarray['inter_trans_extracost'] = isset($return_array['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceAdditionalCost'])?$return_array['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceAdditionalCost']:'';
        }

        if (isset($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption'])) {

            if(isset($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption'][1]))
            {
                foreach($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption'] as $key=>$v)
                {
                    $num= $v['ShippingServicePriority'];
                    $newarray['international_type'.$num] = $v['ShippingService'];
                    $newarray['international_cost'.$num] = $v['ShippingServiceCost'];
                    $newarray['international_extracost'.$num] = $v['ShippingServiceAdditionalCost'];

                    if(isset($v['FreeShipping'])&&($v['FreeShipping']))
                    {
                        $newarray['international_free'.$num] ='true';
                    }
                    if(isset($v['ShipToLocation']))
                    {
                        if(($v['ShipToLocation']=='Worldwide'))
                        {
                            $newarray['international_is_worldwide'.$num]='on';
                        }
                        else
                        {
                            if(is_array($v['ShipToLocation']))
                            {
                                $newarray['international_is_country'.$num]=json_encode($v['ShipToLocation']);

                            }
                            else
                            {
                                $ShipToLocation= array();
                                $ShipToLocation[] = $v['ShipToLocation'];
                                //international_is_country1
                                $newarray['international_is_country'.$num]=json_encode($ShipToLocation);
                            }
                        }
                    }
                }
            }
            else //单个国际运输选项的时候
            {

                $newarray['international_type1'] = isset($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShippingService'])?$return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShippingService']:'';
                $newarray['international_cost1'] = isset($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShippingServiceCost'])?$return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShippingServiceCost']:'';
                if (isset($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['FreeShipping'])) {
                    if ($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['FreeShipping'] == 'true') {
                        $newarray['international_free1'] = 'true';
                    }

                }

                $newarray['international_extracost1'] = isset($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShippingServiceAdditionalCost'])?$return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShippingServiceAdditionalCost']:'';


                if(isset( $return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShipToLocation']))
                {
                    if(( $return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShipToLocation']=='Worldwide'))
                    {
                        $newarray['international_is_worldwide1']='on';
                    }
                    else
                    {
                        if(is_array($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShipToLocation']))
                        {


                            $newarray['international_is_country1'] = json_encode($return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShipToLocation']);

                        }
                        else
                        {
                            $ShipToLocation= array();
                            $ShipToLocation[] = $return_array['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShipToLocation'];
                            $newarray['international_is_country1']=json_encode($ShipToLocation);

                        }
                    }

                }
            }
        }

        // var_dump($newarray);exit;
        $newarray['title'] = isset($return_array['Item']['Title']) ? $return_array['Item']['Title'] : '';
        $newarray['itemid'] = isset($return_array['Item']['ItemID']) ? $return_array['Item']['ItemID'] : '';
        $newarray['sku'] = isset($return_array['Item']['SKU']) ? $return_array['Item']['SKU'] : '';

            $prefix =   explode('*',$newarray['sku']);
            $newarray['ad_user'] = isset($SellerPrefix[(string)$prefix[0]])?$SellerPrefix[(string)$prefix[0]]:0;

        $ItemSpecifics = isset($return_array['Item']['ItemSpecifics']) ? $return_array['Item']['ItemSpecifics'] : '';


        $ItemSpecificsArr = array();
        if (!empty($ItemSpecifics)) {

            foreach ($ItemSpecifics as $spe) {
                foreach ($spe as $sp) {
                    $ItemSpecificsArr[][$sp['Name']] = $sp['Value'];
                }

            }
        }
        $newarray['item_specifics'] = json_encode($ItemSpecificsArr); //SKU 的属性


        $mul_info = isset($return_array['Item']['Variations']) ? $return_array['Item']['Variations'] : '';
        // var_dump($mul_info);exit;

        $mul_infoArr = array();
        $add_mulArr = array();
        if (!empty($mul_info)) {
            $i = 0;
            foreach ($mul_info['Variation'] as $mul) {
                if($i==0){
                 $prefix =   explode('*',$mul['SKU']);
                 $newarray['ad_user'] = isset($SellerPrefix[(string)$prefix[0]])?$SellerPrefix[(string)$prefix[0]]:0;
                }

                $mul_infoArr[$i]['sku'] = $mul['SKU'];
                $mul_infoArr[$i]['qc'] = $mul['Quantity'];
                $mul_infoArr[$i]['price'] = $mul['StartPrice'];
                if (isset($mul['VariationSpecifics'])) {
                    foreach ($mul['VariationSpecifics'] as $m) {
                        if (isset($m[0])) {
                            foreach ($m as $v) {
                                $add_mulArr[] = $v['Name'];
                                $mul_infoArr[$i][$v['Name']] = $v['Value'];
                            }
                        } else {
                            $add_mulArr[] = $m['Name'];
                            $mul_infoArr[$i][$m['Name']] = $m['Value'];
                        }

                    }
                }
                $i++;
            }
        }


        $add_mulArr = array_unique($add_mulArr);
        if(!empty($mul_infoArr))
        {
            $newarray['mul_info'] = json_encode($mul_infoArr);
        }
        if(!empty($add_mulArr))
        {
            $newarray['add_mul'] = json_encode($add_mulArr);
        }

        $mul_picture = isset($return_array['Item']['Variations']['Pictures']) ? $return_array['Item']['Variations']['Pictures'] : '';
        //  var_dump($mul_picture);
        $mul_pictureArr = array();
        if (!empty($mul_picture)) {

            foreach ($mul_picture['VariationSpecificPictureSet'] as $pic) {
                if (is_array($pic['PictureURL'])) {
                    $mul_pictureArr[$mul_picture['VariationSpecificName']][$pic['VariationSpecificValue']] = $pic['PictureURL'];
                } else {
                    $mul_pictureArr[$mul_picture['VariationSpecificName']][$pic['VariationSpecificValue']][] = $pic['PictureURL'];
                }

            }
        }

        $newarray['mul_picture'] = json_encode($mul_pictureArr); //多属性图片

        $ebay_picture = isset($return_array['Item']['PictureDetails']) ? $return_array['Item']['PictureDetails'] : '';
        //   var_dump($ebay_picture);exit;
        $ebay_pictureArr = array();
        if (!empty($ebay_picture)) {

            if (is_array($ebay_picture['PictureURL'])) {
                $ebay_pictureArr = $ebay_picture['PictureURL'];
            } else {
                $ebay_pictureArr[] = $ebay_picture['PictureURL'];
            }

        }
        $newarray['ebay_picture'] = json_encode($ebay_pictureArr);

        $newarray['published_day'] = isset($return_array['Item']['ListingDuration']) ? $return_array['Item']['ListingDuration'] : '';
        $newarray['price'] = isset($return_array['Item']['StartPrice']) ? $return_array['Item']['StartPrice'] : 0.00;
        $newarray['reserve_price'] = isset($return_array['Item']['ReservePrice']) ? $return_array['Item']['ReservePrice'] : 0.00;
        $newarray['price_noce'] = isset($return_array['Item']['BuyItNowPrice']) ? $return_array['Item']['BuyItNowPrice'] : 0.00;
        $newarray['quantity'] = isset($return_array['Item']['Quantity']) ? $return_array['Item']['Quantity'] : 0;
        $newarray['paypal_account'] = isset($return_array['Item']['PayPalEmailAddress']) ? $return_array['Item']['PayPalEmailAddress'] : '';

        $newarray['inter_process_day'] =isset($return_array['Item']['DispatchTimeMax'])?$return_array['Item']['DispatchTimeMax']:'';

        if($newarray['inter_process_day']==0)
        {
            $newarray['inter_fast_send'] = 'true';
        }



        //退货信息

        $newarray['returns_policy'] = isset($return_array['Item']['ReturnPolicy']['ReturnsAcceptedOption']) ? $return_array['Item']['ReturnPolicy']['ReturnsAcceptedOption'] : '';
        $newarray['returns_days'] = isset($return_array['Item']['ReturnPolicy']['ReturnsWithinOption']) ? $return_array['Item']['ReturnPolicy']['ReturnsWithinOption'] : '';
        $newarray['returns_type'] = isset($return_array['Item']['ReturnPolicy']['RefundOption']) ? $return_array['Item']['ReturnPolicy']['RefundOption'] : '';
        $newarray['returns_cost_by'] = isset($return_array['Item']['ReturnPolicy']['ShippingCostPaidByOption']) ? $return_array['Item']['ReturnPolicy']['ShippingCostPaidByOption'] : '';
        // $newarray['returns_delay'] = isset($return_array['Item']['ReturnPolicy']['ExtendedHolidayReturns']) ? $return_array['Item']['ReturnPolicy']['ExtendedHolidayReturns'] : '';


        $newarray['return_details'] = isset($return_array['Item']['ReturnPolicy']['Description']) ? $return_array['Item']['ReturnPolicy']['Description'] : '';

        if(isset($return_array['Item']['ReturnPolicy']['ExtendedHolidayReturns']))
        {
            $newarray['return_delay']='on';
        }

        $newarray['item_location'] = isset($return_array['Item']['Location']) ? $return_array['Item']['Location'] : '';
        $newarray['item_country'] = isset($return_array['Item']['Country']) ? $return_array['Item']['Country'] : '';
        $newarray['item_post'] = isset($return_array['Item']['PostalCode']) ? $return_array['Item']['PostalCode'] : 0;


        //卖家要求
        $newarray['all_buyers']='notall';
        $newarray['noti_trans']='on';
        $newarray['already_buy']='on';
        $newarray['buy_num'] = 5;


        $newarray['categoty1'] = isset($return_array['Item']['PrimaryCategory']['CategoryID']) ? $return_array['Item']['PrimaryCategory']['CategoryID'] : '';
        $newarray['categoty1_all'] = isset($return_array['Item']['PrimaryCategory']['CategoryName']) ? $return_array['Item']['PrimaryCategory']['CategoryName'] : '';
        $newarray['site'] = $site;
        $newarray['ebayaccount'] = $accountarr[$result_one['account_id']];

        $itemStartTime = isset($return_array['Item']['ListingDetails']['StartTime'])?$return_array['Item']['ListingDetails']['StartTime']:'';
        if(!empty($itemStartTime))
        {
            $newarray['starttime'] = date('Y-m-d H:i:s',strtotime($itemStartTime));
        }
        $newarray['sellallnum'] = isset($return_array['Item']['SellingStatus']['QuantitySold'])?$return_array['Item']['SellingStatus']['QuantitySold']:'';

        if(!isset($return_array['Item']['ListingType']))
        {
            //continue;
            ajax_return('同步失败，未知刊登类型',2);
        }
        //ListingType
        if (($return_array['Item']['ListingType'] == 'FixedPriceItem') && (isset($return_array['Item']['Variations']))) {
            $newarray['ad_type'] = 'duoshuxing';
        }
        if (($return_array['Item']['ListingType'] == 'FixedPriceItem') && (!isset($return_array['Item']['Variations']))) {
            $newarray['ad_type'] = 'guding';
        }
        if ($return_array['Item']['ListingType'] == 'Chinese') {
            $newarray['ad_type'] = 'paimai';
        }
        //   $description_details  =isset($return_array['Item']['Description'])?htmlspecialchars_decode($return_array['Item']['Description']):'';
        $newarray['description_details'] = isset($return_array['Item']['Description']) ? htmlspecialchars_decode($return_array['Item']['Description']) : '';


        //在这里处理一下sku_search   mul_info // 如果是单属性，就已SKU 为基准， 如果是多属性，不能以sku为基准
        $newarray['sku_search'] ='';
        if($newarray['ad_type']=='duoshuxing'){

            $mid_array = json_decode($newarray['mul_info'],true);

            $newarray['sku_search'] =  $this->removeSaleTag($mid_array[0]['sku']);



        }else{
            $newarray['sku_search'] =  $this->removeSaleTag($newarray['sku']);
        }

        $newarray['status'] = 2;//已刊登
        $newarray['updatetime'] =  date("Y-m-d H:i:s",time());
        $check['where']['itemid'] = $newarray['itemid'];
        $checkarr= $this->Ebay_list_model->getOne($check,true);
        if(!empty($checkarr))
        {
            unset($newarray['itemid']);
            $this->Ebay_list_model->update($newarray,$check);
        }
        else
        {
            $this->Ebay_list_model->add($newarray);
        }

        ajax_return('同步完成',2);
    }




}