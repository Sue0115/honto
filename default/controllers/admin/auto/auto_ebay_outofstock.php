<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-09-10
 * Time: 14:14
 */
set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");
class Auto_ebay_outofstock extends MY_Controller{
    function __construct(){
        parent::__construct();
        $this->load->library('MyEbayNew');
        $this->load->model(array(
            'ebay/Ebay_user_tokens_model',
            'sf_product_itemid_model'
        ));
        $this->userToken = $this->Ebay_user_tokens_model;
        $this->ebaytest = new MyEbayNew();
    }

    public function auto_outofstock()
    {
        $option =array();
        $option['where']['isOutofControl']=0;
        $option['where']['isEnded']=0;
        
        $result_array = $this->sf_product_itemid_model->getAll2Array($option);


        if(!empty($result_array))
        {
            foreach($result_array as $re)
            {
                //token_id
                $user_array =   $this->userToken->getInfoByTokenId($re['token_id']);

                $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>';
                $requestXmlBody .= '<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
                $requestXmlBody .= '<RequesterCredentials>';
                $requestXmlBody .= '<eBayAuthToken>' . $user_array ['user_token'] . '</eBayAuthToken>';
                $requestXmlBody .= '</RequesterCredentials>';
                $requestXmlBody .= '<ErrorLanguage>zh_CN</ErrorLanguage>';
                $requestXmlBody .= '<Version>923</Version>';
                $requestXmlBody .= '<WarningLevel>High</WarningLevel>';
                $requestXmlBody .= '<Item>';
                $requestXmlBody .= '<ItemID>' . $re['item_number'] . '</ItemID>';
                $requestXmlBody .= '<OutOfStockControl>true</OutOfStockControl>';
                $requestXmlBody .= '</Item>';
                $requestXmlBody .= '</ReviseItemRequest>';
                $this->ebaytest->setinfo($user_array['user_token'],$user_array['devid'],$user_array['appid'],$user_array['certid'],$re['siteID'],'ReviseItem');
                $info = $this->ebaytest->sendHttpRequest($requestXmlBody);
                $responseDoc = new DomDocument();
                $responseDoc->loadXML($info);
               $response = simplexml_import_dom($responseDoc);

                if($response->Ack=='Success'||$response->Ack=='Warning')//代表成功
                {
                    $option_array =array();
                    $option_array['where']['item_id'] =  $re['item_id'];
                    $data_array= array();
                    $data_array['isOutofControl'] = 1;
                    $this->sf_product_itemid_model->update($data_array,$option_array);
                }
            }
        }


    }


}