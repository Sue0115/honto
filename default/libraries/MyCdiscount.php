<?php

header('content-type=text/html;charset=utf-8');
set_time_limit(0);

class MyCdiscount extends MY_Model {

    /**
     * 获取token
     */
    public function getTokenWithAccountPassword($info) {//https://wsvc.cdiscount.com/MarketplaceAPIService.svc?wsdl
    	
        $authentication = base64_encode(base64_decode($info['sales_account']) . ":" . base64_decode($info['pw']));
        $httpheader = array('Authorization: Basic ' . $authentication);
        $itemsFeedURL = "https://sts.cdiscount.com/users/httpIssue.svc/?realm=https://wsvc.cdiscount.com/MarketplaceAPIService.svc";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $itemsFeedURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        $response = curl_exec($ch);
        
//          print_r($response);exit;
        curl_close($ch);
        $xmlResponse = simplexml_load_string(trim($response));
        return $xmlResponse;
    }

    /**
     * 获取订单信息
     */
    public function getOrderLists($token_id) {
        /* soap获取
          $soap = new SoapClient('https://wsvc.cdiscount.com/MarketplaceAPIService.svc?wsdl');
          $params['headerMessage']=array('Context' =>array('CatalogID'=> 1,'CustomerPoolID' => 1,'SiteID'=> 100),'Security' => array('TokenId'=>'e71f4619ed2d4b339f5fa2e2f0af3a94'),'Version' => '1.0');
          $params['orderFilter']=array('FetchOrderLines'=>'true','States'=>array('OrderStateEnum'=>'WaitingForShipmentAcceptation'),'BeginCreationDate'=>'2015-08-15T00:00:00.00','BeginModificationDate'=>'2015-08-15T00:00:00.00',
          'EndCreationDate'=>'2015-08-19T00:00:00.00','EndModificationDate'=>'2015-08-19T00:00:00.00'
          );
          $a =  $soap->__getFunctions();
          $OrderList = $soap->GetOrderList($params);
          try{
          $OrderList = $soap->GetOrderList($params);
          }catch (SoapFault $fault) {
          if($fault->faultcode=='s:RequestTokenValidationError'){
          print_r($fault->faultstring);
          }else{
          print_r($fault->faultstring);
          }
          die;
          }print_r($OrderList);exit; */

        $BeginCreationDate = date('Y-m-d\TH:i:s', strtotime('-5 day'));
        $EndCreationDate = date('Y-m-d\TH:i:s');
        $BeginModificationDate = date('Y-m-d\TH:i:s', strtotime('-5 day'));
        $EndModificationDate = date('Y-m-d\TH:i:s');
        $data = '<?xml version="1.0" encoding="UTF-8"?>';
        $data .= '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">';
        $data .= '<s:Body>';
        $data .= '<GetOrderList xmlns="http://www.cdiscount.com">';
        $data .= '<headerMessage xmlns:a="http://schemas.datacontract.org/2004/07/Cdiscount.Framework.Core.Communication.Messages" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
        $data .= '<a:Context>';
        $data .= '<a:CatalogID>1</a:CatalogID>';
        $data .= '<a:CustomerPoolID>1</a:CustomerPoolID>';
        $data .= '<a:SiteID>100</a:SiteID>';
        $data .= '</a:Context>';
        $data .= '<a:Localization>';
        $data .= '<a:Country>Fr</a:Country>';
        $data .= '<a:Currency>Eur</a:Currency>';
        $data .= '<a:DecimalPosition>2</a:DecimalPosition>';
        $data .= '<a:Language>Fr</a:Language>';
        $data .= '</a:Localization>';
        $data .= '<a:Security>';
        $data .= '<a:DomainRightsList i:nil="true" />';
        $data .= '<a:IssuerID i:nil="true" />';
        $data .= '<a:SessionID i:nil="true" />';
        $data .= '<a:SubjectLocality i:nil="true" />';
        $data .= '<a:TokenId>' . $token_id . '</a:TokenId>';
        $data .= '<a:UserName i:nil="true" />';
        $data .= '</a:Security>';
        $data .= '<a:Version>1.0</a:Version>';
        $data .= '</headerMessage>';
        $data .= '<orderFilter xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
        $data .= '<BeginCreationDate>' . $BeginCreationDate . '</BeginCreationDate>';
        $data .= '<BeginModificationDate>' . $BeginModificationDate . '</BeginModificationDate>';
        $data .= '<EndCreationDate>' . $EndCreationDate . '</EndCreationDate>';
        $data .= '<EndModificationDate>' . $EndModificationDate . '</EndModificationDate>';
        $data .= '<FetchOrderLines>true</FetchOrderLines>';
        $data .= '<States>';
        $data .= '<OrderStateEnum>WaitingForShipmentAcceptation</OrderStateEnum>'; //待发货的订单
        $data .= '</States>';
        $data .= '</orderFilter>';
        $data .= '</GetOrderList>';
        $data .= '</s:Body>';
        $data .= '</s:Envelope>';
        $callHeaderHttp = array('Content-Type: text/xml;charset=UTF-8', 'SOAPAction: ' . '"http://www.cdiscount.com/IMarketplaceAPIService/GetOrderList"');
        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, "https://wsvc.cdiscount.com/MarketplaceAPIService.svc");
        curl_setopt($tuCurl, CURLOPT_VERBOSE, false);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($tuCurl, CURLOPT_HEADER, false);
        curl_setopt($tuCurl, CURLOPT_POST, true);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, $callHeaderHttp);
        $tuData = curl_exec($tuCurl);
        
        
        
        curl_close($tuCurl);
        $xml = simplexml_load_string($tuData);
        
        
        
        
        $body = $xml->xpath('s:Body');
        return $body;


//             if(!curl_errno($tuCurl))
//             {
//                 $info = curl_getinfo($tuCurl);
//                 echo '<p>A pris '.$info['total_time'].' secondes pour envoyer la requête sur '.$info['url'].'</p>';
//             }
//             else
//             {
//                 echo '<p>Curl error: '.curl_error($tuCurl).'</p>';
//             }
//             // Dump de la réponse SOAP
    }

    /**
     *  标记发货
     */
    public function uploadTrackNo($uploadArr) {
        $data = '<?xml version="1.0" encoding="UTF-8"?>';
        $data .= '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">';
        $data .='<s:Body>';
        $data .='<ValidateOrderList xmlns="http://www.cdiscount.com">';
        $data .='<headerMessage xmlns:a="http://schemas.datacontract.org/2004/07/Cdiscount.Framework.Core.Communication.Messages" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';

        $data .='<a:Context>';
        $data .='<a:CatalogID>1</a:CatalogID>';
        $data .='<a:CustomerPoolID>1</a:CustomerPoolID>';
        $data .='<a:SiteID>100</a:SiteID>';
        $data .='</a:Context>';

        $data .='<a:Localization>';
        $data .='<a:Country>Fr</a:Country>';
        $data .='<a:Currency>Eur</a:Currency>';
        $data .='<a:DecimalPosition>2</a:DecimalPosition>';
        $data .='<a:Language>Fr</a:Language>';
        $data .='</a:Localization>';

        $data .='<a:Security>';
        $data .='<a:DomainRightsList i:nil="true" />';
        $data .='<a:IssuerID i:nil="true" />';
        $data .='<a:SessionID i:nil="true" />';
        $data .='<a:SubjectLocality i:nil="true" />';
        $data .='<a:TokenId>' . $uploadArr['token_id'] . '</a:TokenId>';
        $data .='<a:UserName i:nil="true" />';
        $data .='</a:Security>';

        $data .='<a:Version>1.0</a:Version>';
        $data .='</headerMessage>';

        $data .='<validateOrderListMessage xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
        $data .='<OrderList>';
        $data .='<ValidateOrder>';
        $data .='<CarrierName>' . $uploadArr['CarrierName'] . '</CarrierName>';
        $data .='<OrderLineList>';
        foreach ($uploadArr['products_info'] as $val) {
            $data .='<ValidateOrderLine>';
            $data .='<AcceptationState>ShippedBySeller</AcceptationState>';
            $data .='<ProductCondition>New</ProductCondition>';
            
            if($val->comment_text==''){
                $val->comment_text = trim($val->orders_sku);
            }
            
            $data .='<SellerProductId>' . $val->comment_text . '</SellerProductId>';
            $data .='</ValidateOrderLine>';
        }
        $data .='</OrderLineList>';
        $data .='<OrderNumber>' . $uploadArr['OrderNumber'] . '</OrderNumber>';
        $data .='<OrderState>Shipped</OrderState>';
        $data .='<TrackingNumber>' . $uploadArr['TrackingNumber'] . '</TrackingNumber>';
        $data .='<TrackingUrl>' . $uploadArr['TrackingUrl'] . '</TrackingUrl>';
        $data .='</ValidateOrder>';
        $data .='</OrderList>';
        $data .='</validateOrderListMessage>';

        $data .='</ValidateOrderList>';
        $data .='</s:Body>';
        $data .='</s:Envelope>';
        $callHeaderHttp = array('Content-Type: text/xml;charset=UTF-8', 'SOAPAction: ' . '"http://www.cdiscount.com/IMarketplaceAPIService/ValidateOrderList"');
        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, "https://wsvc.cdiscount.com/MarketplaceAPIService.svc");
        curl_setopt($tuCurl, CURLOPT_VERBOSE, false);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($tuCurl, CURLOPT_HEADER, false);
        curl_setopt($tuCurl, CURLOPT_POST, true);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, $callHeaderHttp);
        $tuData = curl_exec($tuCurl);
        curl_close($tuCurl);
        $xml = simplexml_load_string($tuData);
        $flag = 'false';
        if ($xml !=null ) {
            $body = $xml->xpath('s:Body');
            $errorMSG = '';
            $errorType = '';
            $flag = $body[0]->ValidateOrderListResponse->ValidateOrderListResult->ValidateOrderResults->ValidateOrderResult->Validated;
            var_dump($body[0]);
            /*
              if($body[0]->ValidateOrderListResponse->ValidateOrderListResult->ValidateOrderResults->ValidateOrderResult->ValidateOrderLineResults->ValidateOrderLineResult->Errors->Error->Message){
              $errorMSG =  (string)$body[0]->ValidateOrderListResponse->ValidateOrderListResult->ValidateOrderResults->ValidateOrderResult->ValidateOrderLineResults->ValidateOrderLineResult->Errors->Error->Message;
              }
              if($body[0]->ValidateOrderListResponse->ValidateOrderListResult->ValidateOrderResults->ValidateOrderResult->ValidateOrderLineResults->ValidateOrderLineResult->Errors->Error->ErrorType){
              $errorType =  (string)$body[0]->ValidateOrderListResponse->ValidateOrderListResult->ValidateOrderResults->ValidateOrderResult->ValidateOrderLineResults->ValidateOrderLineResult->Errors->Error->ErrorType;
              }

              if($body[0]->ValidateOrderListResponse->ValidateOrderListResult->ValidateOrderResults->ValidateOrderResult->Errors->Error->Message){
              $errorMSG =  (string)$body[0]->ValidateOrderListResponse->ValidateOrderListResult->ValidateOrderResults->ValidateOrderResult->Errors->Error->Message;
              }
              if($body[0]->ValidateOrderListResponse->ValidateOrderListResult->ValidateOrderResults->ValidateOrderResult->Errors->Error->ErrorType){
              $errorType =  (string)$body[0]->ValidateOrderListResponse->ValidateOrderListResult->ValidateOrderResults->ValidateOrderResult->Errors->Error->ErrorType;
              }
              if($body[0]->ValidateOrderListResponse->ValidateOrderListResult->ErrorList->Error->Message){
              $errorMSG = (string)$body[0]->ValidateOrderListResponse->ValidateOrderListResult->ErrorList->Error->Message;
              }
              if($body[0]->ValidateOrderListResponse->ValidateOrderListResult->ErrorList->Error->ErrorType){
              $errorType = (string)$body[0]->ValidateOrderListResponse->ValidateOrderListResult->ErrorList->Error->ErrorType;
              }
              $re = (string)$flag;* */
            //$order_id = $val->erp_orders_id;
            //$sql = "UPDATE erp_orders set IsEbayStatusIsMarked=1 where erp_orders_id=$order_id";
            //$this->query($sql);
        }
        return (string) $flag;
    }

    /**
     * 获取在售SKU
     */
    public function getOnsellProducts($arr) {
        $data = '<s:Envelope xmlns:arr="http://schemas.microsoft.com/2003/10/Serialization/Arrays" xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">';
        $data .='<s:Header/>';
        $data .='<s:Body>';
        $data .='<GetOfferList xmlns="http://www.cdiscount.com">';
        $data .='<headerMessage xmlns:a="http://schemas.datacontract.org/2004/07/Cdiscount.Framework.Core.Communication.Messages" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
        $data .='<a:Context>';
        $data .='<a:CatalogID>1</a:CatalogID>';
        $data .='<a:CustomerPoolID>1</a:CustomerPoolID>';
        $data .='<a:SiteID>100</a:SiteID>';
        $data .='</a:Context>';
        $data .='<a:Localization>';
        $data .='<a:Country>Fr</a:Country>';
        $data .='<a:Currency>Eur</a:Currency>';
        $data .='<a:DecimalPosition>2</a:DecimalPosition>';
        $data .='<a:Language>Fr</a:Language>';
        $data .='</a:Localization>';
        $data .='<a:Security>';
        $data .='<a:DomainRightsList i:nil="true" />';
        $data .='<a:IssuerID i:nil="true" />';
        $data .='<a:SessionID i:nil="true" />';
        $data .='<a:SubjectLocality i:nil="true" />';
        $data .='<a:TokenId>' . $arr['token_id'] . '</a:TokenId>';
        $data .='<a:UserName i:nil="true" />';
        $data .='</a:Security>';
        $data .='<a:Version>1.0</a:Version>';
        $data .='</headerMessage>';
        $data .='<offerFilter>';
        $data .='<OfferPoolId>1</OfferPoolId>';
        $data .='</offerFilter>';
        $data .='</GetOfferList>';
        $data .='</s:Body>';
        $data .='</s:Envelope>';

        $callHeaderHttp = array('Content-Type: text/xml;charset=UTF-8', 'SOAPAction: ' . '"http://www.cdiscount.com/IMarketplaceAPIService/GetOfferList"');

        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, "https://wsvc.cdiscount.com/MarketplaceAPIService.svc");
        curl_setopt($tuCurl, CURLOPT_VERBOSE, false);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($tuCurl, CURLOPT_HEADER, false);
        curl_setopt($tuCurl, CURLOPT_POST, true);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, $callHeaderHttp);
        $tuData = curl_exec($tuCurl);
        curl_close($tuCurl);
        $xml = simplexml_load_string($tuData);
        $body = $xml->xpath('s:Body');
        return $body[0]->GetOfferListResponse->GetOfferListResult->OfferList;
    }

    /**
     *  修改SKU数量和价格(API文档有问题，等待cdiscount技术人员回复)
     */
    public function editOnsellProduct($arr, $skuInfo = array()) {
        $data = '';
        $data .='<?xml version="1.0"?>';
        $data .='<OfferPackage Name="cdiscountStockAndPrice" PurgeAndReplace="False" PackageType="StockAndPrice" xmlns="clr-namespace:Cdiscount.Service.OfferIntegration.Pivot;assembly=Cdiscount.Service.OfferIntegration" xmlns:x="http://schemas.microsoft.com/winfx/2006/xaml">';
        $data .='<OfferPackage.Offers>';
        $data .='<OfferCollection Capacity="1">';
        $data .='<Offer SellerProductId="MP01771941-0001" ProductEan="6922082473722" Price="66.66" Stock="666" />';
        $data .='</OfferCollection>';
        $data .='</OfferPackage.Offers>';
        $data .='</OfferPackage>';
        if (file_exists('cdiscountStockAndPrice.xml')) {
            unlink('cdiscountStockAndPrice.xml'); //删除
        }
        if (file_exists('cdiscountStockAndPrice.zip')) {
            unlink('cdiscountStockAndPrice.zip'); //删除
        }
        file_put_contents('cdiscountStockAndPrice.xml', $data);

        $zip = new ZipArchive();
        if ($zip->open('cdiscountStockAndPrice.zip', ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFile('cdiscountStockAndPrice.xml');
            $zip->close();
        }

        $data = '';
        $data .='<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">';
        $data .='<s:Body>';
        $data .='<SubmitOfferPackage xmlns="http://www.cdiscount.com">';
        $data .='<headerMessage xmlns:a="http://schemas.datacontract.org/2004/07/Cdiscount.Framework.Core.Communication.Messages" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
        $data .='<a:Context>';
        $data .='<a:CatalogID>1</a:CatalogID>';
        $data .='<a:CustomerPoolID>1</a:CustomerPoolID>';
        $data .='<a:SiteID>100</a:SiteID>';
        $data .='</a:Context>';
        $data .='<a:Localization>';
        $data .='<a:Country>Fr</a:Country>';
        $data .='<a:Currency>Eur</a:Currency>';
        $data .='<a:DecimalPosition>2</a:DecimalPosition>';
        $data .='<a:Language>Fr</a:Language>';
        $data .='</a:Localization>';
        $data .='<a:Security>';
        $data .='<a:DomainRightsList i:nil="true" />';
        $data .='<a:IssuerID i:nil="true" />';
        $data .='<a:SessionID i:nil="true" />';
        $data .='<a:SubjectLocality i:nil="true" />';
        $data .='<a:TokenId>' . $arr['token_id'] . '</a:TokenId>';
        $data .='<a:UserName i:nil="true" />';
        $data .='</a:Security>';
        $data .='<a:Version>1.0</a:Version>';
        $data .='</headerMessage>';
        $data .='<offerPackageRequest xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
        $data .='<ZipFileFullPath>' . base_url('cdiscountStockAndPrice.zip') . '</ZipFileFullPath>';
        $data .='</offerPackageRequest>';
        $data .='</SubmitOfferPackage>';
        $data .='</s:Body>';
        $data .='</s:Envelope>';

        $callHeaderHttp = array('Content-Type: text/xml;charset=UTF-8', 'SOAPAction: ' . '"http://www.cdiscount.com/IMarketplaceAPIService/SubmitOfferPackage"');
        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, "https://wsvc.cdiscount.com/MarketplaceAPIService.svc");
        curl_setopt($tuCurl, CURLOPT_VERBOSE, false);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($tuCurl, CURLOPT_HEADER, false);
        curl_setopt($tuCurl, CURLOPT_POST, true);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, $callHeaderHttp);
        $tuData = curl_exec($tuCurl);
//             $errno = curl_errno( $tuCurl );
//             var_dump($errno,$tuData);echo "<hr/>";
//             echo $tuData; exit;
        curl_close($tuCurl);
        $xml = simplexml_load_string($tuData);
        $body = $xml->xpath('s:Body');
        unlink('cdiscountStockAndPrice.xml'); //删除
        unlink('cdiscountStockAndPrice.zip'); //删除
    }

    /**
     * 解析SKU
     */
    function parseSku($string) {
        $stringHead = substr(trim($string), 0, 3);
        $stringThree = substr(trim($string), 3);
        if ((int) $stringHead > 0) {
            return trim($stringThree);
        } else {
            return trim($string);
        }
    }
	function pregSku($str){

		//处理MHM033b(2)类型,如果是则返回数组
		if(preg_match_all("/\([\d]+\)/",$str,$kh)){
			$str = str_replace($kh[0][0],'',$str);
			preg_match_all("/[\d]+/",$kh[0][0],$numb);
			$numb = $numb[0][0];//数量
			$arr = array('str'=>$str,'numb'=>$numb);
			return $arr;
		}
		return $str;
	}
	//处理005MHM033b(2)类型orders_item
	function pregitem($str){
		if(preg_match_all("/^0[\d]{2}/",$str,$header)){
			$str = substr($str,3);
		}
		if(preg_match_all("/\([\d]+\)/",$str,$kh)){
			$str = str_replace($kh[0][0],'',$str);
		}
		return $str;
	}

}
