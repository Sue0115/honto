<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/4
 * Time: 10:00
 */
set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");

class EbayDetails extends MY_Controller{
    protected $ebay;
    protected $userToken;
    protected $model;

    function __construct()
    {
        parent::__construct();
        $this->load->library('MyEbayNew');
        $this->load->model(array(
            'ebay/Ebay_user_tokens_model',
            'ebay/Ebay_ebaydetails_model',
            'ebay/Ebay_ebaysite_model',
            'ebay/Ebay_country_model'
        ));
        $this->ebaytest = new MyEbayNew();
        $this->userToken = $this->Ebay_user_tokens_model;
        $this->details = $this->Ebay_ebaydetails_model;
        $this->side = $this->Ebay_ebaysite_model;
        $this->country=$this->Ebay_country_model;
    }

    //获取对应站点的运输方式
    function getEbayDetails()
    {
        $result =   $this->userToken->getInfoByTokenId(4);
        $siteinfo = array();
        $siteresult = $this->side->getEbaySiteAll($siteinfo);
        foreach($siteresult as $si) {
            $site = $si['siteid'];
            $this->ebaytest->setinfo($result['user_token'], $result['devid'], $result['appid'], $result['certid'], $site, 'GeteBayDetails');
            $xml = '<?xml version="1.0" encoding="utf-8"?>
                <GeteBayDetailsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                  <DetailName>ShippingCarrierDetails</DetailName>
                  <DetailName>ShippingServiceDetails</DetailName>';

            $xml .= '<RequesterCredentials><eBayAuthToken>' . $result['user_token'] . '</eBayAuthToken></RequesterCredentials>';
            $xml .= '</GeteBayDetailsRequest>';

            $info = $this->ebaytest->sendHttpRequest($xml);
            $responseDoc = new DomDocument();
            $responseDoc->loadXML($info);
            $response = simplexml_import_dom($responseDoc);


            $arrinfo = $response->ShippingServiceDetails;
            foreach ($arrinfo as $arr) {
                $details = array();
                $details['siteid'] = $site;
                $details['description'] = isset($arr->Description) ? (string)$arr->Description : '';
                $details['internationalservice'] = isset($arr->InternationalService) ? (string)$arr->InternationalService : '';
                $details['shippingservice'] = isset($arr->ShippingService) ? (string)$arr->ShippingService : '';
                $details['shippingserviceid'] = isset($arr->ShippingServiceID) ? intval($arr->ShippingServiceID) : '';
                $details['shippingtimemax'] = isset($arr->ShippingTimeMax) ? intval($arr->ShippingTimeMax) : '';
                $details['shippingtimemin'] = isset($arr->ShippingTimeMin) ? intval($arr->ShippingTimeMin) : '';
                $details['validforsellingflow'] = isset($arr->ValidForSellingFlow) ? (string)$arr->ValidForSellingFlow : '';
                $details['shippingcarrier'] = isset($arr->ShippingCarrier) ? (string)$arr->ShippingCarrier : '';
                $details['weightrequired'] = isset($arr->WeightRequired) ? (string)$arr->WeightRequired : '';
                $details['shippingcategory'] = isset($arr->ShippingCategory) ? (string)$arr->ShippingCategory : '';
                $ServiceType = isset($arr->ServiceType) ? $arr->ServiceType : '';
                $ShippingPackage = isset($arr->ShippingPackage) ? $arr->ShippingPackage : '';
                if ($ServiceType != '') {
                    $para = '';
                    foreach ($ServiceType as $de) {
                        $para = ((string)$de) . '@' . $para;
                    }
                    $details['servicetype'] = $para;
                } else {
                    $details['servicetype'] = '';
                }

                if ($ShippingPackage != '') {
                    $para = '';
                    foreach ($ShippingPackage as $sh) {
                        $para = (string)$sh[0] . '@' . $para;

                    }
                    $details['shippingpackage'] = $para;
                } else {
                    $details['shippingpackage'] = '';
                }
                $op['shippingserviceid'] = $details['shippingserviceid'];
                $op['siteid']=$site;
                $isPresence = $this->details->getdetails($op);
                if (isset($isPresence['id'])) {
                    $this->details->updatedetails($op['shippingserviceid'], $details);

                } else {
                    $this->details->inserinfo($details);
                }

            }
        }
    }

    //获取ebay平台国家对应的SIDE
    public function getcountrysite()
    {
        $result =   $this->userToken->getInfoByTokenId(1);
        $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],0,'GeteBayDetails');

        $xml ='<?xml version="1.0" encoding="utf-8"?>
                <GeteBayDetailsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                  <DetailName>SiteDetails</DetailName>';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
        $xml .='</GeteBayDetailsRequest>';
        $info = $this->ebaytest->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($info);
        $response = simplexml_import_dom($responseDoc);
        $info =  $response->SiteDetails;
        foreach($info as $arr)
        {
            $siteinfo = array();
            $siteinfo['site']=isset($arr->Site)?(string)$arr->Site:'';
            $siteinfo['siteid']=isset($arr->SiteID)?intval($arr->SiteID):'';
            $siteinfo['detailversion']=isset($arr->DetailVersion)?intval($arr->DetailVersion):'';
            $op['siteid'] = $siteinfo['siteid'];
            $isPresence = $this->side->getEbaySiteOne($op);
            if(isset($isPresence['id']))
            {
                $this->side->updateInfo($op['siteid'],$siteinfo);
            }
            else
            {
                $this->side->inserinfo($siteinfo);
            }

        }
    }
    //获取退货详情
    public  function getReturnPolicy()
    {
        $result =   $this->userToken->getInfoByTokenId(1);
        $siteinfo = array();
        $siteresult = $this->side->getEbaySiteAll($siteinfo);
        foreach($siteresult as $si)
        {
            $site = $si['siteid'];
            $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$site,'GeteBayDetails');

            $xml ='<?xml version="1.0" encoding="utf-8"?>
                <GeteBayDetailsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                  <DetailName>ReturnPolicyDetails</DetailName>';
            $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
            $xml .='</GeteBayDetailsRequest>';
            $info = $this->ebaytest->sendHttpRequest($xml);
            $responseDoc = new DomDocument();
            $responseDoc->loadXML($info);
            $response = simplexml_import_dom($responseDoc);
            $returninfo = $response->ReturnPolicyDetails;
            $withdaty='';
            if(isset($returninfo->ReturnsWithin))
            {

                foreach($returninfo->ReturnsWithin as $day)
                {
                    $withdaty =(string)$day->ReturnsWithinOption.'{@}'.$withdaty;
                }
            }
            $accept='';
            if(isset($returninfo->ReturnsAccepted))
            {

                foreach($returninfo->ReturnsAccepted as $ac)
                {
                    $accept = (string)$ac->ReturnsAcceptedOption.'{@}'.$accept;
                }
            }
            $costpai ='';
            if(isset($returninfo->ShippingCostPaidBy))
            {

                foreach($returninfo->ShippingCostPaidBy as $by)
                {
                    $costpai = (string)$by->ShippingCostPaidByOption.'{@}'.$costpai;
                }
            }
            $refund ='';
            if(isset($returninfo->Refund))
            {

                foreach($returninfo->Refund as $by)
                {
                    $refund =(string)$by->RefundOption.'{@}'.$refund;
                }
            }
            $arr = array();
            $arr['returnswithin'] = $withdaty;
            $arr['returnsaccepted'] = $accept;
            $arr['shippingcostpaidby'] = $costpai;
            $arr['refund'] = $refund;
            $this->side->updateReturnPolicy($site,$arr);
        }

    }



    //获取国家相应的信息
    public  function getCountry()
    {
        $result =   $this->userToken->getInfoByTokenId(1);
        $site = 0;
        $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$site,'GeteBayDetails');

        $xml ='<?xml version="1.0" encoding="utf-8"?>
                <GeteBayDetailsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                  <DetailName>CountryDetails</DetailName>';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
        $xml .='</GeteBayDetailsRequest>';
        $info = $this->ebaytest->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($info);
        $response = simplexml_import_dom($responseDoc);
        $info =$response->CountryDetails;
        foreach($info as $co)
        {
            $arr = array();
            $arr['country']=isset($co->Country)?(string)$co->Country:'';
            $arr['country_en']=isset($co->Description)?(string)$co->Description:'';
            $this->country->inserinfo($arr);
        }

    }

    public function test()
    {
        $result =   $this->userToken->getInfoByTokenId(1);
        $site = 77;
        $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$site,'GeteBayDetails');

        $xml ='<?xml version="1.0" encoding="utf-8"?>
                <GeteBayDetailsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
        $xml .='</GeteBayDetailsRequest>';
        $info = $this->ebaytest->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($info);
        $response = simplexml_import_dom($responseDoc);
        var_dump($response);
    }
}