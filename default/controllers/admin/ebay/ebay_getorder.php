<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/27
 * Time: 10:09
 */
set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");

class Ebay_getorder extends MY_Controller
{
    protected $ebay;
    protected $userToken;
    protected $model;

    function __construct()
    {
        parent::__construct();
        $this->load->library('MyEbayNew');
        $this->load->model(array(
            'ebay/Ebay_user_tokens_model'

        ));
        $this->ebaytest = new MyEbayNew();
        $this->userToken = $this->Ebay_user_tokens_model;
    }


    public function getOrder()
    {
        $result =   $this->userToken->getInfoByTokenId(6);
        $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],0,'GetOrders');

        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                <CreateTimeFrom>2015-03-01T20:34:44.000Z</CreateTimeFrom>
                <CreateTimeTo>2015-03-02T22:34:44.000Z</CreateTimeTo>
            <!--       <OrderIDArray  </OrderIDArray> <OrderID>390931488242</OrderID>-->
             <!--    <SellingManagerSalesRecordNumber>356298</SellingManagerSalesRecordNumber>-->
                <OrderRole>Seller</OrderRole>
               <OrderStatus>Completed</OrderStatus>
                <DetailLevel>ReturnAll</DetailLevel>';

        $xml .=' <RequesterCredentials>
                <eBayAuthToken>'.$result['user_token'].'</eBayAuthToken>
                </RequesterCredentials>';
        $xml .='</GetOrdersRequest>';

        $info = $this->ebaytest->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($info);
        $response = simplexml_import_dom($responseDoc);
          var_dump($response);exit;
    }
}