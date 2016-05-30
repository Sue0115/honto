<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/27
 * Time: 10:40
 */
class MyEbayNew
{
    private $requestToken;
    private $devID;
    private $appID;
    private $certID;
    private $serverUrl;
    private $compatLevel;
    private $siteID;
    private $verb;

    public function __construct()
    {
        $this->_CI = &get_instance();
    }

    public function setinfo($userRequestToken, $developerID, $applicationID, $certificateID, $siteToUseID = 0, $callName)
    {
        $this->requestToken = $userRequestToken;
        $this->devID = $developerID;
        $this->appID = $applicationID;
        $this->certID = $certificateID;
        $this->compatLevel = 923;
        $this->siteID = $siteToUseID;
        $this->verb = $callName;
        $this->serverUrl = 'https://api.ebay.com/ws/api.dll';
    }

    public function sendHttpRequest($requestBody)
    {
        //exit;
        $headers = $this->buildEbayHeaders();
        //print_r($headers);

        $connection = curl_init();

        curl_setopt($connection, CURLOPT_VERBOSE, 0);
        curl_setopt($connection, CURLOPT_URL, $this->serverUrl);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($connection, CURLOPT_POST, 1);
        curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($connection, CURLOPT_TIMEOUT, 90);
        //proxy
        /*
        curl_setopt($connection, CURLOPT_HTTPPROXYTUNNEL, 0);
        curl_setopt($connection, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        curl_setopt($connection, CURLOPT_PROXY, '127.0.0.1:8000');
        */
        $response = curl_exec($connection);
        curl_close($connection);
        return $response;
    }

    private function buildEbayHeaders()
    {
        $headers = array(
            'Content-type: text/xml',
            'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->compatLevel,
            'X-EBAY-API-DEV-NAME: ' . $this->devID,
            'X-EBAY-API-APP-NAME: ' . $this->appID,
            'X-EBAY-API-CERT-NAME: ' . $this->certID,
            'X-EBAY-API-CALL-NAME: ' . $this->verb,
            'X-EBAY-API-SITEID: ' . $this->siteID,
        );
        return $headers;
    }

    public function getSuggestedCategories($eBayAuthToken, $query)
    {
        $xml = '';
        $xml .= '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<GetSuggestedCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $xml .= '<RequesterCredentials>';
        $xml .= '<eBayAuthToken>' . $eBayAuthToken . '</eBayAuthToken>';
        $xml .= '</RequesterCredentials>';
        $xml .= '<Query>' . $query . '</Query>';
        $xml .= '</GetSuggestedCategoriesRequest>';
        $result = $this->sendHttpRequest($xml);
        return $result;
    }


    //获得分类支持的属性
    public function getGetCategorySpecifics($eBayAuthToken, $CategoryID)
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <GetCategorySpecificsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
              <WarningLevel>High</WarningLevel>
              <CategorySpecific>';
        $xml .= ' <CategoryID>' . $CategoryID . '</CategoryID>';
        $xml .= ' </CategorySpecific>';
        $xml .= '<RequesterCredentials><eBayAuthToken>' . $eBayAuthToken . '</eBayAuthToken></RequesterCredentials>';
        $xml .= '</GetCategorySpecificsRequest>';
        $result = $this->sendHttpRequest($xml);
        return $result;

    }


    //获取分类的一些限制
    public function getCondition($eBayAuthToken, $CategoryID, $CategorySiteID)
    {

        $xml = "<?xml version='1.0' encoding='utf-8'?>
        <GetCategoryFeaturesRequest xmlns='urn:ebay:apis:eBLBaseComponents'>
        <DetailLevel>ReturnAll</DetailLevel>
        <FeatureID>ConditionEnabled</FeatureID>
        <FeatureID>ConditionValues</FeatureID>
        <FeatureID>ItemSpecificsEnabled</FeatureID>
        <FeatureID>VariationsEnabled</FeatureID>
        <FeatureID>UPCEnabled</FeatureID>
        <FeatureID>EANEnabled</FeatureID>
        <FeatureID>ISBNEnabled</FeatureID>";
        $xml .= '<CategoryID>' . $CategoryID . '</CategoryID>';
        $xml .= ' <CategorySiteID>' . $CategorySiteID . '</CategorySiteID>';
        $xml .= '<RequesterCredentials><eBayAuthToken>' . $eBayAuthToken . '</eBayAuthToken></RequesterCredentials>';
        $xml .= ' </GetCategoryFeaturesRequest>';
        $result = $this->sendHttpRequest($xml);
        return $result;
    }

    /**
     * @param $api 调用的api
     * @param $type 1 ：拍卖  2：固定 3：多属性
     * @param $info 上传的基本信息
     */
    public function publish_new($api,$type, $info){
        $xml = '';
        $xml .="<?xml version='1.0' encoding='utf-8'?>";
        $xml .='<'.$api.'Request xmlns="urn:ebay:apis:eBLBaseComponents">';
        $xml .= "<ErrorLanguage>zh_CN</ErrorLanguage><WarningLevel>High</WarningLevel><Item>";
        $xml .= ' <Title>' . htmlspecialchars($info['title']) . '</Title>';
        $xml .= '<SubTitle>'.htmlspecialchars($info['subTitle']) .'</SubTitle>';
        $xml .= '<Site>'.$info['sitename'].'</Site>';
        $xml .= '<Currency>'.$info['Currency'].'</Currency>';
        $xml .= '<SKU>'.$info['ebay_sku'].'</SKU>';
        $xml .= '<ListingDuration>'.$info['timeMax'].'</ListingDuration>'; //Days_7
      //  $xml .= '<ListingDuration>Days_7</ListingDuration>'; //Days_7

        $xml .= '<CategoryMappingAllowed>true</CategoryMappingAllowed>'; //是否允许多分类
        $xml .='<PrimaryCategory><CategoryID>'.$info['category_id'].'</CategoryID></PrimaryCategory>';
        if(!empty( $info['category_id_second'])){
            $xml .= '<SecondaryCategory><CategoryID>' . $info['category_id_second'] . '</CategoryID></SecondaryCategory>';
        }


        $xml .= '<ConditionID>' . $info['condition'] . '</ConditionID>';
        if(!empty($info['condition_detail'])){
            $xml .= '<ConditionDescription>'.trim($info['condition_detail']).'</ConditionDescription>';
        }
        if($info['privateListing']=='on'){
            $xml .= '<PrivateListing>ture</PrivateListing>';
        }
        $xml .= '<PaymentMethods>PayPal</PaymentMethods>';  // 付款方式 - 暂时只支持paypal
        $xml .= '<PayPalEmailAddress>'.$info['payPalEmailAddress'] .'</PayPalEmailAddress>'; //

        if (!empty($info['detailPicList'])) //ebay图片
        {
            $xml .= '<PictureDetails>';
            $pictrueinfo = json_decode($info['detailPicList'], true);
            $i=0;
            foreach ($pictrueinfo as $pic) {
                if($i==12){
                    break;
                }

                $pic =  str_replace(" ", "%20",$pic);
                $xml .= '<PictureURL>'.$pic.'</PictureURL>';
                $i++;
            }
            $xml .= '</PictureDetails>';
        }

        /*if($info['ebay_sku']=='352*JJ0283W#win'){
            var_dump($xml);exit;
        }*/

        if(!empty($info['product_info'])){
            $product_info = unserialize($info['product_info']);
            if (!empty($product_info['item_country'])&&isset($product_info['item_country'])) //产品所在的国家
            {
                $xml .= '<Country>' . $product_info['item_country'] . '</Country>';
            }
            if (!empty($product_info['item_location'])&&isset($product_info['item_location']))  //地点
            {
                $xml .= '<Location>' . $product_info['item_location'] . '</Location>';
            }
            if (!empty($product_info['item_post'])&&isset($product_info['item_post']))  //邮编
            {
                $xml .= '<PostalCode>' . $product_info['item_post'] . '</PostalCode>';
            }
        }
        if($type !=3){
            if ((!empty($info['upc'])) || (!empty($info['isbn'])) || (!empty($info['ean']))) {
                $xml .= '<ProductListingDetails>';

                if (!empty($info['upc']))        //有UPC 就传上去
                {
                    $xml .= '<UPC>' . $info['upc'] . '</UPC>';
                }

                if (!empty($info['ean']))  //有ean 就传上去
                {
                    $xml .= '<EAN>' . $info['ean'] . '</EAN>';
                }

                if (!empty($info['isbn']))  //有ean 就传上去
                {
                    $xml .= '<ISBN>' . $info['isbn'] . '</ISBN>';
                }

                $xml .= '</ProductListingDetails>';
            }else{
                /*美国加拿大是UPC  澳大利亚是UPC
                  英国是EAN 法国德国意大利西班牙EAN */
                //现在没有UPC 或者EAN 就上不去。 通过分类获取属性的时候 UPC 或EAN 可能获取不出来 所现在自动填充
                $xml .= '<ProductListingDetails>';
                if($info['sitename']=='US'||$info['sitename']=="Canada"||$info['sitename']=="Australia"){
                    $xml .= '<UPC>Does Not Apply</UPC>';
                }
                if($info['sitename']=='UK'||$info['sitename']=="Germany"||$info['sitename']=="France"||$info['sitename']=="Italy"||$info['sitename']=="Spain"){
                    $xml .= '<EAN>Does Not Apply</EAN>';
                }
                $xml .= '</ProductListingDetails>';


            }
        }


        if (!empty($info['itemSpecifics']))  //产品的属性
        {
            $xml .= '<ItemSpecifics>';
            $spe = json_decode($info['itemSpecifics'], true);

            foreach ($spe as $k => $s) {
                if (!empty($s)) {
                            $xml .= '<NameValueList><Name>' . htmlspecialchars($k) . '</Name><Value>' . htmlspecialchars($s) . '</Value></NameValueList>';  //这里是系统可选的的属性
                }
            }
            if (!empty($info['user_spe'])) {
                $divspe = json_decode($info['user_spe'], true);
                foreach ($divspe as $ke => $div) {
                    if ((!empty($ke)) && (!empty($div))) {
                        $xml .= '<NameValueList><Name>' . $ke . '</Name><Value>' . $div . '</Value></NameValueList>'; //这个是自己增加的属性
                    }
                }
            }
            $xml .= '</ItemSpecifics>';
        }

        $buyerRequirementDetails = unserialize($info['buyerRequirementDetails']);

        //买家要求
        if (isset($buyerRequirementDetails['all_buyers']))  //对于买家的要求
        {
            $xml .= '<BuyerRequirementDetails>';
            if ($buyerRequirementDetails['LinkedPayPalAccount'] == 'on') {
                $xml .= '<LinkedPayPalAccount>true</LinkedPayPalAccount>'; //只支持PAYPAL付款
            }
            if ($buyerRequirementDetails['ShipToRegistrationCountry'] == 'on') {
                $xml .= '<ShipToRegistrationCountry>true</ShipToRegistrationCountry>'; //排除运输范围之外的国家
            }

            if ($buyerRequirementDetails['MaximumUnpaidItemStrikesInfo']['main'] == 'on') {
                $xml .= '<MaximumUnpaidItemStrikesInfo><Count>'.$buyerRequirementDetails['MaximumUnpaidItemStrikesInfo']['Count']. '</Count><Period>' . $buyerRequirementDetails['MaximumUnpaidItemStrikesInfo']['Period']. '</Period></MaximumUnpaidItemStrikesInfo>';
            }
            if ($buyerRequirementDetails['MaximumBuyerPolicyViolations']['main'] == 'on') {
                $xml .= '<MaximumBuyerPolicyViolations><Count>' .$buyerRequirementDetails['MaximumBuyerPolicyViolations']['Count']. '</Count><Period>' .$buyerRequirementDetails['MaximumBuyerPolicyViolations']['Period'] . '</Period></MaximumBuyerPolicyViolations>';
            }
            if ($buyerRequirementDetails['MinimumFeedbackScore']['main'] == 'on') {
                $xml .= '<MinimumFeedbackScore>'.$buyerRequirementDetails['MinimumFeedbackScore']['Count'].'</MinimumFeedbackScore>';  //信用低于
            }
            if ($buyerRequirementDetails['MaximumItemRequirements']['main'] == 'on') {
                $xml .= '<MaximumItemRequirements><MaximumItemCount>'.$buyerRequirementDetails['MaximumItemRequirements']['MaximumItemCount']. '</MaximumItemCount>';
                if ($buyerRequirementDetails['MaximumItemRequirements']['main_score'] == 'on') {
                    $xml .= '<MinimumFeedbackScore>'.$buyerRequirementDetails['MaximumItemRequirements']['MinimumFeedbackScore'].'</MinimumFeedbackScore></MaximumItemRequirements>';
                } else {
                    $xml .= '</MaximumItemRequirements>';
                }
            }
            $xml .= '</BuyerRequirementDetails>';
        }

        $returnPolicy = unserialize($info['returnPolicy']);
        //退货政策
        if ($returnPolicy['ReturnsAcceptedOption'] == 'ReturnsAccepted')   //退货政策  接受的情况下，
        {
            $xml .= '<ReturnPolicy>';

            $xml .= '<ReturnsAcceptedOption>'.$returnPolicy['ReturnsAcceptedOption'].'</ReturnsAcceptedOption>';
            if (!empty($info['RefundOption'])) {
                $xml .= '<RefundOption>' . $returnPolicy['RefundOption'] . '</RefundOption>';  //退货方式  一些站点没有这个标签
            }

            if (!empty($returnPolicy['ShippingCostPaidByOption'])) {
                $xml .= '<ShippingCostPaidByOption>' . $returnPolicy['ShippingCostPaidByOption'] . '</ShippingCostPaidByOption>'; // 退货费用谁承担
            }
            if (!empty($returnPolicy['ReturnsWithinOption'])) {
                $xml .= ' <ReturnsWithinOption>' . $returnPolicy['ReturnsWithinOption'] . '</ReturnsWithinOption>'; //退货天数
            }
            if ($returnPolicy['ExtendedHolidayReturns'] == 'on') {
                $xml .= '<ExtendedHolidayReturns>true</ExtendedHolidayReturns>'; //节假日延迟
            }
            if (!empty($returnPolicy['Description'])) {
                $xml .= '<Description>' . trim($returnPolicy['Description']) . '</Description>'; // 退货详情
            }
            $xml .= '</ReturnPolicy>';
        }

        if (!empty($info['inter_process_day']))  //是否快速发货
        {
            $xml .= '<DispatchTimeMax>' . $info['inter_process_day'] . '</DispatchTimeMax>'; //  对应的发货天数
        }else{
            $xml .= '<DispatchTimeMax>0</DispatchTimeMax>'; //  对应的发货天数
        }

        $xml .= '<ShippingDetails>';


        $shippingServiceOptions = unserialize($info['shippingServiceOptions']);
        //var_dump($shippingServiceOptions);exit;
        foreach($shippingServiceOptions as $key=>$ship){
            $xml .= '<ShippingServiceOptions>';
            $xml .= '<ShippingServicePriority>'.$key.'</ShippingServicePriority>';
            $xml .= ' <ShippingService>' .$ship['ShippingService'] . '</ShippingService>';  //国内运输方式
            if(empty($ship['ShippingServiceCost'])&&(empty($ship['ShippingServiceAdditionalCost']))){
                $xml .= '<FreeShipping>true</FreeShipping>'; //是否免费
            }else{
                if (!empty($ship['ShippingServiceCost'])) {
                    $xml .= '<ShippingServiceCost crenccuyID="' . $info['Currency'] . '">' .$ship['ShippingServiceCost']. '</ShippingServiceCost>';//基本运费
                }
                if (!empty($ship['ShippingServiceAdditionalCost'])) {
                    $xml .= '<ShippingServiceAdditionalCost crenccuyID="' . $info['Currency'] . '">' .$ship['ShippingServiceAdditionalCost']. '</ShippingServiceAdditionalCost>'; //额外加收
                }
            }
            $xml .= '</ShippingServiceOptions>';

        }
        $internationalShippingServiceOption  = unserialize($info['internationalShippingServiceOption']);

        foreach($internationalShippingServiceOption as $key=>$ship){
            if( empty($ship['ShippingService'])){
                continue;
            }
            $xml .= '<InternationalShippingServiceOption>';
            $xml .= '<ShippingServicePriority>'.$key.'</ShippingServicePriority>'; //国际运输 顺序
            $xml .= '<ShippingService>'.$ship['ShippingService'].'</ShippingService>'; //国际运输方式
                //$ship['ShippingServiceCost']
            $xml .= '<ShippingServiceCost crenccuyID="' . $info['Currency'] . '" >'.$ship['ShippingServiceCost'].'</ShippingServiceCost>'; // 费用
            //$ship['ShippingServiceAdditionalCost']
            $xml .= '<ShippingServiceAdditionalCost crenccuyID="' . $info['Currency'] . '" >'.$ship['ShippingServiceAdditionalCost'].'</ShippingServiceAdditionalCost>';// 额外加收

            if ($ship['ShipToLocation'] == 'Worldwide') //是否运输到全球
            {
                $xml .= '<ShipToLocation>Worldwide</ShipToLocation>';// 运输到全球
            } else {
                $country = json_decode($ship['ShipToLocation'], true); // 运输到指定国家
                foreach ($country as $co) {
                    $xml .= '<ShipToLocation>'.$co . '</ShipToLocation>'; //国家英文代码
                }
            }
            $xml .= '</InternationalShippingServiceOption>';
        }
        if (!empty($info['un_ship'])) //排除的国家
        {
            $excludeshiparr = explode(',', $info['un_ship']);
            foreach ($excludeshiparr as $v) {
                $v =trim($v);
                if(!empty($v)){
                    $xml .= '<ExcludeShipToLocation>' . $v . '</ExcludeShipToLocation>';
                }
            }
        }
        $xml .= '</ShippingDetails>';

    //    echo $xml;exit;
        if(!empty($info['store_category_id'])){
            $xml .='<Storefront>';
            $xml .='<StoreCategoryID>'.$info['store_category_id'].'</StoreCategoryID>';
            /*if(!empty($info['store_category_name'])){
                $xml .='<StoreCategoryName>'.$info['store_category_name'].'</StoreCategoryName>';

            }*/
            $xml .='</Storefront>';
        }



        if ($type == 1) {
            $xml .= '<ListingType>Chinese</ListingType>';

            $xml .= '<StartPrice currencyID="' . $info['Currency'] . '">' . $info['ebay_price'] . '</StartPrice>';

                $xml .= '<ReservePrice  currencyID="' . $info['Currency'] . '">0.00</ReservePrice>';


                $xml .= '<BuyItNowPrice currencyID="' . $info['Currency'] . '">0.00</BuyItNowPrice>';


            $xml .= '<Quantity>'.$info['ebay_quantity'].'</Quantity>';

        }
        if ($type == 2) {
            $xml .= '<ListingType>FixedPriceItem</ListingType>';
            $xml .= '<StartPrice currencyID="' . $info['Currency'] . '">' . $info['ebay_price'] . '</StartPrice>';
            $xml .= '<Quantity>' . $info['ebay_quantity'] . '</Quantity>';
        }

        if($type ==3) {
            $xml .= '<ListingType>FixedPriceItem</ListingType>';
            $mul_info = json_decode($info['skuinfo'], true);
            $add_mul = json_decode($info['zidingyi'], true);
            $xml .= '<Variations>';
            $xml .= '<VariationSpecificsSet>';

            foreach ($add_mul as $add) {
                if(($add=='UPC')||($add=='EAN')){
                    continue;
                }

                $arrdif = array();
                $xml .= '<NameValueList><Name>' . $add . '</Name>';
                foreach ($mul_info[$add] as $mul) {
                    if (in_array($mul, $arrdif)) {
                        continue;
                    }
                    $arrdif[] = $mul;
                    $xml .= '<Value>' . $mul . '</Value>';
                }
                $xml .= '</NameValueList>';
            }
            $xml .= '</VariationSpecificsSet>';

            for ($i = 0; $i < count($mul_info['sku']); $i++) {
                $xml .= '<Variation>';
                $xml .= '<SKU>' . $mul_info['sku'][$i] . '</SKU>';
                $xml .= '<StartPrice >' . $mul_info['price'][$i] . '</StartPrice>';
                $xml .= '<Quantity>' . $mul_info['quantity'][$i] . '</Quantity>';

                if (isset($mul_info['UPC'][$i]) || isset($mul_info['EAN'][$i])) {
                    $xml .= '<VariationProductListingDetails>';
                    if (isset($mul_info['UPC'][$i])) {
                        $xml .= '<UPC>' . $mul_info['UPC'][$i] . '</UPC>';
                    }
                    if (isset($mul_info['EAN'][$i])) {
                        $xml .= '<EAN>' . $mul_info['EAN'][$i] . '</EAN>';
                    }
                    $xml .= '</VariationProductListingDetails>';
                }

               /* $xml .= '<VariationProductListingDetails>';
                $xml .= '<UPC>does not apply</UPC>';
                $xml .= '</VariationProductListingDetails>';*/

                $xml .= '<VariationSpecifics>';
                foreach ($add_mul as $ad) {
                    if (($ad == 'UPC') || ($ad == 'EAN')) {
                        continue;
                    }
                    $xml .= '<NameValueList>';
                    $xml .= '<Name>' . $ad . '</Name>';
                    $xml .= '<Value>' . $mul_info[$ad][$i] . '</Value>';
                    $xml .= '</NameValueList>';
                }
                $xml .= '</VariationSpecifics>';
                $xml .= '</Variation>';
            }


            if (!empty($info['detailPicListMul'])) {

                $detailPicListMul = json_decode($info['detailPicListMul'], true);
                $is_pass = false;
                foreach ($detailPicListMul as $key => $mul) {
                    foreach ($add_mul as $ad) {
                        foreach ($mul_info[$ad] as $v) {
                            if ($v == $key) {
                                $set_type = $ad;
                                $is_pass = true;
                                break;

                            }
                        }
                        if ($is_pass) {
                            break;
                        }
                    }
                    if ($is_pass) {
                        break;
                    }
                }
                $xml .= '<Pictures>';

                $xml .= '<VariationSpecificName>' . $set_type . '</VariationSpecificName>';
                foreach ($detailPicListMul as $v_1 => $v_2) {
                    $xml .= '<VariationSpecificPictureSet><VariationSpecificValue>' . $v_1 . '</VariationSpecificValue>';

                    $v_2 =  str_replace(" ", "%20",$v_2);
                    $xml .= ' <PictureURL>' . ($v_2) . '</PictureURL>';
                    $xml .= '</VariationSpecificPictureSet>';
                }

                $xml .= '</Pictures>';
            }
            $xml .= '</Variations>';
        }



        $xml .= '<Description><![CDATA[' . (trim($info['detail'])) .']]></Description>';  //将描述部分 设置完了再传进来
        $xml .= '</Item>';
        $xml .= '<RequesterCredentials><eBayAuthToken>' . $this->requestToken . '</eBayAuthToken></RequesterCredentials>';

        $xml .= '</'.$api.'Request>';

        $result = $this->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($result);
        $response = simplexml_import_dom($responseDoc);
        $result_all =array();
        if(isset($response->ItemID)){
            $result_all['is_success'] =true;

            $result_all['info'] =(string)$response->ItemID;
        }else{
            $result_all['is_success'] =false;

            $result_all['info'] =(string)$response->Errors->LongMessage;
            $result_all['info_all'] =var_export($response,true);
        }


      //  var_dump($response);



        return $result_all;


    }




    /**
     * 站点要转换成英文代码
     * 获取对应站点的币种
     *
     *
     */
    //$tpye =1  AddItem   $tpye =2 AddFixedPriceItem
    public function publish($eBayAuthToken, $info)
    {
        $xml = '';
        $xml .="<?xml version='1.0' encoding='utf-8'?>";
        if ($info['ad_type'] == 'duoshuxing') {
            $xml .='<AddFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        } else {
            $xml .='<AddItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        }

        $xml .= "<ErrorLanguage>zh_CN</ErrorLanguage><WarningLevel>High</WarningLevel><Item>";
        $xml .= ' <Title>' . $info['title'] . '</Title>';
        if (!empty($info['title1'])) {
            $xml .= '<SubTitle>' . $info['title1'] . '</SubTitle>';
        }
        //  $site['siteid'] = $info['site'];

        $xml .= '<Site>' . $info['sitename'] . '</Site>';
        $xml .= '<Currency>' . $info['sitecurrency'] . '</Currency>';
        $xml .= ' <SKU>' . $info['sku'] . '</SKU>';
        $xml .= '<ListingDuration>' . $info['published_day'] . '</ListingDuration>';

        $xml .= '<CategoryMappingAllowed>true</CategoryMappingAllowed>'; //是否允许多分类
        $xml .='<PrimaryCategory><CategoryID>'.$info['categoty1'].'</CategoryID></PrimaryCategory>';
        if (!empty($info['categoty2']))  // 如果有第二分类
        {
            $xml .= '<SecondaryCategory><CategoryID>' . $info['categoty2'] . '</CategoryID></SecondaryCategory>';
        }
        if (($info['item_status'] != 1111) && ($info['item_status'] != ''))  //物品状态
        {
            $xml .= '<ConditionID>' . $info['item_status'] . '</ConditionID>';
        }

        if (!empty($info['item_status_description'])) // 物品状态不是一个选项的时候  可以填写的信息
        {
            $xml .= '<ConditionDescription>' . trim($info['item_status_description']) . '</ConditionDescription>';
        }
        if ($info['auction_is_private'] == 'true')  // 是不是私人刊登
        {
            $xml .= '<PrivateListing>ture</PrivateListing>';
        }


        if (!empty($info['ebay_picture'])) //ebay图片
        {
            $xml .= '<PictureDetails>';
            $pictrueinfo = json_decode($info['ebay_picture'], true);
            foreach ($pictrueinfo as $pic) {
                $xml .= '<PictureURL>' . $pic . '</PictureURL>';
            }
            $xml .= '</PictureDetails>';
        }

        $xml .= '<PaymentMethods>PayPal</PaymentMethods>';  // 付款方式 - 暂时只支持paypal
        $xml .= '<PayPalEmailAddress>' . $info['paypal_account'] . '</PayPalEmailAddress>'; //
        if (!empty($info['item_country'])) //产品所在的国家
        {
            $xml .= '<Country>' . $info['item_country'] . '</Country>';
        }
        if (!empty($info['item_location']))  //地点
        {
            $xml .= '<Location>' . $info['item_location'] . '</Location>';
        }
        if (!empty($info['item_post']))  //邮编
        {
            $xml .= '<PostalCode>' . $info['item_post'] . '</PostalCode>';
        }

        if ((!empty($info['upc'])) || (!empty($info['isb'])) || (!empty($info['ean']))) {
            $xml .= '<ProductListingDetails>';

            if (!empty($info['upc']))        //有UPC 就传上去
            {
                $xml .= '<UPC>' . $info['upc'] . '</UPC>';
            }

            if (!empty($info['ean']))  //有ean 就传上去
            {
                $xml .= '<EAN>' . $info['ean'] . '</EAN>';
            }

            if (!empty($info['isb']))  //有ean 就传上去
            {
                $xml .= '<ISBN>' . $info['isb'] . '</ISBN>';
            }

            $xml .= '</ProductListingDetails>';
        }

        if (!empty($info['item_specifics']))  //产品的属性
        {
            $xml .= '<ItemSpecifics>';
            $spe = json_decode($info['item_specifics'], true);

            foreach ($spe as $k => $s) {
                if (!empty($s)) {
                    foreach ($s as $s_1 => $s_2) {
                        if (!empty($s_2)) {
                            $xml .= '<NameValueList><Name>' . $s_1 . '</Name><Value>' . $s_2 . '</Value></NameValueList>';  //这里是系统可选的的属性
                        }
                    }
                }
            }
            if (!empty($info['item_specifics_user'])) {
                $divspe = json_decode($info['item_specifics_user'], true);
                foreach ($divspe as $ke => $div) {
                    if ((!empty($ke)) && (!empty($div))) {
                        $xml .= '<NameValueList><Name>' . $ke . '</Name><Value>' . $div . '</Value></NameValueList>'; //这个是自己增加的属性
                    }
                }
            }
            $xml .= '</ItemSpecifics>';
        }


        if ($info['returns_policy'] == 'ReturnsAccepted')   //退货政策  接受的情况下，
        {
            $xml .= '<ReturnPolicy>';

            $xml .= '<ReturnsAcceptedOption>' . $info['returns_policy'] . '</ReturnsAcceptedOption>';
            if (!empty($info['returns_type'])) {
                $xml .= '<RefundOption>' . $info['returns_type'] . '</RefundOption>';  //退货方式  一些站点没有这个标签
            }

            if (!empty($info['returns_cost_by'])) {
                $xml .= '<ShippingCostPaidByOption>' . $info['returns_cost_by'] . '</ShippingCostPaidByOption>'; // 退货费用谁承担
            }
            if (!empty($info['returns_days'])) {
                $xml .= ' <ReturnsWithinOption>' . $info['returns_days'] . '</ReturnsWithinOption>'; //退货天数
            }
            if ($info['returns_delay'] == 'on') {
                $xml .= '<ExtendedHolidayReturns>true</ExtendedHolidayReturns>'; //节假日延迟
            }
            if (!empty($info['return_details'])) {
                $xml .= '<Description>' . trim($info['return_details']) . '</Description>'; // 退货详情
            }

            $xml .= '</ReturnPolicy>';
        }


        if (empty($info['inter_process_day']))  //是否快速发货
        {
            if ($info['inter_fast_send'] == 'true') {
                $xml .= '<DispatchTimeMax>0</DispatchTimeMax>';  //选择快速发货就设置为0
            }
        } else {
            $xml .= '<DispatchTimeMax>' . $info['inter_process_day'] . '</DispatchTimeMax>'; //  对应的发货天数
        }

        $xml .= '<ShippingDetails>';
        $xml .= '<ShippingServiceOptions>';
        $xml .= '<ShippingServicePriority>1</ShippingServicePriority>';
        $xml .= ' <ShippingService>' . $info['inter_trans_type'] . '</ShippingService>';  //国内运输方式
        if ($info['inter_free'] == 'true') {
            $xml .= '<FreeShipping>true</FreeShipping>'; //是否免费
        } else {
            if (!empty($info['inter_trans_cost'])) {
                $xml .= '<ShippingServiceCost crenccuyID="' . $info['sitecurrency'] . '">' . $info['inter_trans_cost'] . '</ShippingServiceCost>';//基本运费
            }
            if (!empty($info['inter_trans_extracost'])) {
                $xml .= '<ShippingServiceAdditionalCost crenccuyID="' . $info['sitecurrency'] . '">' . $info['inter_trans_extracost'] . '</ShippingServiceAdditionalCost>'; //额外加收
            }
            /* if(!empty($info['inter_trans_AK_extracost']))
             {
                 $xml .='<ShippingSurcharge crenccuyID="'.$sitecurrency.'">'.$info['inter_trans_AK_extracost'].'</ShippingSurcharge>';
             }*/
        }
        $xml .= '</ShippingServiceOptions>';


        if (!empty($info['international_type1'])) {
            $xml .= '<InternationalShippingServiceOption>';
            $xml .= '<ShippingServicePriority>1</ShippingServicePriority>'; //国际运输 顺序
            $xml .= '<ShippingService>' . $info['international_type1'] . '</ShippingService>'; //国际运输方式

            $xml .= '<ShippingServiceCost crenccuyID="' . $info['sitecurrency'] . '" >' . $info['international_cost1'] . '</ShippingServiceCost>'; // 费用
            $xml .= '<ShippingServiceAdditionalCost crenccuyID="' . $info['sitecurrency'] . '" >' . $info['international_extracost1'] . '</ShippingServiceAdditionalCost>';// 额外加收

            if ($info['international_is_worldwide1'] == 'on') //是否运输到全球
            {
                $xml .= '<ShipToLocation>Worldwide</ShipToLocation>';// 运输到全球
            } else {
                $country = json_decode($info['international_is_country1'], true); // 运输到指定国家
                foreach ($country as $co) {
                    $xml .= '<ShipToLocation>' . $co . '</ShipToLocation>'; //国家英文代码
                }
            }
            $xml .= '</InternationalShippingServiceOption>';
        }
        if (!empty($info['international_type2'])) {
            $xml .= '<InternationalShippingServiceOption>';
            $xml .= '<ShippingServicePriority>2</ShippingServicePriority>';
            $xml .= '<ShippingService>' . $info['international_type2'] . '</ShippingService>';

            $xml .= '<ShippingServiceCost crenccuyID="' . $info['sitecurrency'] . '" >' . $info['international_cost2'] . '</ShippingServiceCost>';
            $xml .= '<ShippingServiceAdditionalCost crenccuyID="' . $info['sitecurrency'] . '" >' . $info['international_extracost2'] . '</ShippingServiceAdditionalCost>';

            if ($info['international_is_worldwide2'] == 'on') {
                $xml .= '<ShipToLocation>Worldwide</ShipToLocation>';
            } else {
                $country = json_decode($info['international_is_country2'], true);
                foreach ($country as $co) {
                    $xml .= '<ShipToLocation>' . $co . '</ShipToLocation>';
                }
            }
            $xml .= '</InternationalShippingServiceOption>';
        }
        if (!empty($info['international_type3'])) {
            $xml .= '<InternationalShippingServiceOption>';
            $xml .= '<ShippingServicePriority>3</ShippingServicePriority>';
            $xml .= '<ShippingService>' . $info['international_type3'] . '</ShippingService>';

            $xml .= '<ShippingServiceCost crenccuyID="' . $info['sitecurrency'] . '" >' . $info['international_cost3'] . '</ShippingServiceCost>';
            $xml .= '<ShippingServiceAdditionalCost crenccuyID="' . $info['sitecurrency'] . '" >' . $info['international_extracost3'] . '</ShippingServiceAdditionalCost>';

            if ($info['international_is_worldwide3'] == 'on') {
                $xml .= '<ShipToLocation>Worldwide</ShipToLocation>';
            } else {
                $country = json_decode($info['international_is_country3'], true);
                foreach ($country as $co) {
                    $xml .= '<ShipToLocation>' . $co . '</ShipToLocation>';
                }
            }
            $xml .= '</InternationalShippingServiceOption>';
        }
        if (!empty($info['international_type4'])) {
            $xml .= '<InternationalShippingServiceOption>';
            $xml .= '<ShippingServicePriority>4</ShippingServicePriority>';
            $xml .= '<ShippingService>' . $info['international_type4'] . '</ShippingService>';

            $xml .= '<ShippingServiceCost crenccuyID="' . $info['sitecurrency'] . '" >' . $info['international_cost4'] . '</ShippingServiceCost>';
            $xml .= '<ShippingServiceAdditionalCost crenccuyID="' . $info['sitecurrency'] . '" >' . $info['international_extracost4'] . '</ShippingServiceAdditionalCost>';

            if ($info['international_is_worldwide4'] == 'on') {
                $xml .= '<ShipToLocation>Worldwide</ShipToLocation>';
            } else {
                $country = json_decode($info['international_is_country4'], true);
                foreach ($country as $co) {
                    $xml .= '<ShipToLocation>' . $co . '</ShipToLocation>';
                }
            }
            $xml .= '</InternationalShippingServiceOption>';
        }
        if (!empty($info['international_type5'])) {
            $xml .= '</InternationalShippingServiceOption>';
            $xml .= '<ShippingServicePriority>5</ShippingServicePriority>';
            $xml .= '<ShippingService>' . $info['international_type5'] . '</ShippingService>';

            $xml .= '<ShippingServiceCost crenccuyID="' . $info['sitecurrency'] . '" >' . $info['international_cost5'] . '</ShippingServiceCost>';
            $xml .= '<ShippingServiceAdditionalCost crenccuyID="' . $info['sitecurrency'] . '" >' . $info['international_extracost5'] . '</ShippingServiceAdditionalCost>';

            if ($info['international_is_worldwide5'] == 'on') {
                $xml .= '<ShipToLocation>Worldwide</ShipToLocation>';
            } else {
                $country = json_decode($info['international_is_country5'], true);
                foreach ($country as $co) {
                    $xml .= '<ShipToLocation>' . $co . '</ShipToLocation>';
                }
            }
            $xml .= '</InternationalShippingServiceOption>';
        }


        if (!empty($info['excludeship'])) //排除的国家
        {
            $excludeshiparr = explode(',', $info['excludeship']);
            foreach ($excludeshiparr as $v) {
                $xml .= '<ExcludeShipToLocation>' . $v . '</ExcludeShipToLocation>';
            }
        }
        $xml .= '</ShippingDetails>';


        if ($info['all_buyers'] == 'notall')  //对于买家的要求
        {
            $xml .= '<BuyerRequirementDetails>';
            if ($info['nopaypal'] == 'on') {
                $xml .= '<LinkedPayPalAccount>true</LinkedPayPalAccount>'; //只支持PAYPAL付款
            }
            if ($info['noti_trans'] == 'on') {
                $xml .= '<ShipToRegistrationCountry>true</ShipToRegistrationCountry>'; //排除运输范围之外的国家
            }

            if ($info['is_abandoned'] == 'on') {
                $xml .= '<MaximumUnpaidItemStrikesInfo><Count>' . $info['abandoned_num'] . '</Count><Period>' . $info['abandoned_day'] . '</Period></MaximumUnpaidItemStrikesInfo>';
            }
            if ($info['is_report'] == 'on') {
                $xml .= '<MaximumBuyerPolicyViolations><Count>' . $info['report_num'] . '</Count><Period>' . $info['report_day'] . '</Period></MaximumBuyerPolicyViolations>';
            }
            if ($info['is_trust_low'] == 'on') {
                $xml .= '<MinimumFeedbackScore>' . $info['trust_low_num'] . '</MinimumFeedbackScore>';  //信用低于
            }
            if ($info['already_buy'] == 'on') {
                $xml .= ' <MaximumItemRequirements><MaximumItemCount>' . $info['buy_num'] . '</MaximumItemCount>';
                if ($info['buy_condition'] == 'on') {
                    $xml .= '<MinimumFeedbackScore>' . $info['buy_credit'] . '</MinimumFeedbackScore></MaximumItemRequirements>';
                } else {
                    $xml .= '</MaximumItemRequirements>';
                }
            }
            $xml .= '</BuyerRequirementDetails>';
        }

        if ($info['ad_type'] == 'paimai') {
            $xml .= '<ListingType>Chinese</ListingType>';

            $xml .= '<StartPrice currencyID="' . $info['sitecurrency'] . '">' . $info['price'] . '</StartPrice>';

            if (!empty($info['reserve_price'])) {
                $xml .= '<ReservePrice  currencyID="' . $info['sitecurrency'] . '">' . $info['reserve_price'] . '</ReservePrice>';
            }
            if (!empty($info['price_noce'])) {
                $xml .= '<BuyItNowPrice currencyID="' . $info['sitecurrency'] . '">' . $info['price_noce'] . '</BuyItNowPrice>';
            }

            $xml .= '<Quantity>' . $info['quantity'] . '</Quantity>';

        }
        if ($info['ad_type'] == 'guding') {
            $xml .= '<ListingType>FixedPriceItem</ListingType>';
            $xml .= '<StartPrice currencyID="' . $info['sitecurrency'] . '">' . $info['price'] . '</StartPrice>';
            $xml .= '<Quantity>' . $info['quantity'] . '</Quantity>';

        }
        if ($info['ad_type'] == 'duoshuxing') {
            $xml .= '<ListingType>FixedPriceItem</ListingType>';
            $mul_info = json_decode($info['mul_info'], true);
            $add_mul = json_decode($info['add_mul'], true);
            $xml .= '<Variations>';
            $xml .= '<VariationSpecificsSet>';
            foreach ($add_mul as $add) {
                $arrdif = array();
                $xml .= '<NameValueList><Name>' . $add . '</Name>';
                foreach ($mul_info as $mul) {
                    if (in_array($mul[$add], $arrdif)) {
                        continue;
                    }
                    $arrdif[] = $mul[$add];
                    $xml .= '<Value>' . $mul[$add] . '</Value>';
                }
                $xml .= '</NameValueList>';
            }
            $xml .= '</VariationSpecificsSet>';

            foreach ($mul_info as $in) {

                $xml .= '<Variation>';
                $xml .= '<SKU>' . $in['sku'] . '</SKU>';
                $xml .= '<StartPrice >' . $in['price'] . '</StartPrice>';
                $xml .= '<Quantity>' . $in['qc'] . '</Quantity>';

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
                $xml .= '<VariationSpecifics>';
                foreach ($add_mul as $ad) {
                    if(($ad=='UPC')||($ad=='EAN')){
                        continue;
                    }
                    $xml .= '<NameValueList>';
                    $xml .= '<Name>' . $ad . '</Name>';
                    $xml .= '<Value>' . $in[$ad] . '</Value>';
                    $xml .= '</NameValueList>';
                }
                $xml .= '</VariationSpecifics>';
                $xml .= '</Variation>';
            }

            if (!empty($info['mul_picture'])) {
                $xml .= '<Pictures>';
                $picinfo = json_decode($info['mul_picture'], true);
                foreach ($picinfo as $key => $p) {
                    $xml .= '<VariationSpecificName>' . $key . '</VariationSpecificName>';
                    foreach ($p as $v_1 => $v_2) {
                        $xml .= '<VariationSpecificPictureSet><VariationSpecificValue>' . $v_1 . '</VariationSpecificValue>';
                        foreach ($v_2 as $picmul) {
                            $xml .= ' <PictureURL>' . $picmul . '</PictureURL>';
                        }
                        $xml .= '</VariationSpecificPictureSet>';
                    }
                }
                $xml .= '</Pictures>';
            }
            $xml .= '</Variations>';
        }

        $xml .= '<Description><![CDATA[' . trim($info['description_details_new']) .']]></Description>';  //将描述部分 设置完了再传进来
        $xml .= '</Item>';
        $xml .= '<RequesterCredentials><eBayAuthToken>' . $eBayAuthToken . '</eBayAuthToken></RequesterCredentials>';
        if ($info['ad_type'] == 'duoshuxing') {
            $xml .= '</AddFixedPriceItemRequest>';
        } else {
            $xml .= '</AddItemRequest>';
        }
        $result = $this->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($result);
        $response = simplexml_import_dom($responseDoc);
        return $response;

    }


    public function modifyActiveListting($eBayAuthToken,$lastinfo)
    {
        $xml ='';
        $xml .="<?xml version='1.0' encoding='utf-8'?>";
        $xml .='<ReviseItemRequest  xmlns="urn:ebay:apis:eBLBaseComponents">';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$eBayAuthToken.'</eBayAuthToken></RequesterCredentials>';
        $xml .='<Item>';

        $xml .='<ItemID>'.$lastinfo['itemid'].'</ItemID>';

        //物品所在地


        if(!empty($lastinfo['item_country']))
        {
            $xml .='<Country>'.$lastinfo['item_country'].'</Country>';
        }
        if(!empty($lastinfo['item_location']))
        {
            $xml .='<Location>'.$lastinfo['item_location'].'</Location>';
        }
        if(!empty($lastinfo['item_post']))
        {
            $xml .='<PostalCode>'.$lastinfo['item_post'].'</PostalCode>';
        }




        // 修改运输信息
        $xml .='<ShippingDetails>';
        $xml .='<ShippingServiceOptions>';
        $xml .='<ShippingServicePriority>1</ShippingServicePriority>';
        $xml .=' <ShippingService>'.$lastinfo['inter_trans_type'].'</ShippingService>';
        if($lastinfo['inter_free']=='true')
        {
            $xml .='<FreeShipping>true</FreeShipping>' ;
        }
        else
        {
            if(!empty($lastinfo['inter_trans_cost']))
            {
                $xml .='<ShippingServiceCost crenccuyID="'.$lastinfo['sitecurrency'].'">'.$lastinfo['inter_trans_cost'].'</ShippingServiceCost>';
            }
            if(!empty($lastinfo['inter_trans_extracost']))
            {
                $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$lastinfo['sitecurrency'].'">'.$lastinfo['inter_trans_extracost'].'</ShippingServiceAdditionalCost>';
            }
        }
        $xml .='</ShippingServiceOptions>';


        if(!empty($lastinfo['international_type1']))
        {
            $xml .='<InternationalShippingServiceOption>';
            $xml .='<ShippingServicePriority>1</ShippingServicePriority>';
            $xml .='<ShippingService>'.$lastinfo['international_type1'].'</ShippingService>';

            $xml .='<ShippingServiceCost crenccuyID="'.$lastinfo['sitecurrency'].'" >'.$lastinfo['international_cost1'].'</ShippingServiceCost>';
            $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$lastinfo['sitecurrency'].'" >'.$lastinfo['international_extracost1'].'</ShippingServiceAdditionalCost>';

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

            $xml .='<ShippingServiceCost crenccuyID="'.$lastinfo['sitecurrency'].'" >'.$lastinfo['international_cost2'].'</ShippingServiceCost>';
            $xml .='<ShippingServiceAdditionalCost crenccuyID="'.$lastinfo['sitecurrency'].'" >'.$lastinfo['international_extracost2'].'</ShippingServiceAdditionalCost>';

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


        if(!empty($lastinfo['excludeship']))
        {
            $excludeshiparr = explode(',',$lastinfo['excludeship']);
            foreach($excludeshiparr as $v)
            {
                $xml .='<ExcludeShipToLocation>'.$v.'</ExcludeShipToLocation>';
            }
        }

        $xml .='</ShippingDetails>';




        //修改图片

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


        if(!empty($lastinfo['sku'])) //SKU
        {
            $xml .=' <SKU>'.$lastinfo['sku'].'</SKU>';
        }

        if(!empty($lastinfo['published_day'])) // 刊登天数
        {
            $xml .='<ListingDuration>'.$lastinfo['published_day'].'</ListingDuration>';
        }


        //分类1
        if(!empty($lastinfo['categoty1']))
        {
            $xml .='<PrimaryCategory><CategoryID>'.$lastinfo['categoty1'].'</CategoryID></PrimaryCategory>';
        }
        //分类2
        if(!empty($lastinfo['categoty2']))
        {
            $xml .='<CategoryMappingAllowed>true</CategoryMappingAllowed>';
            $xml .='<SecondaryCategory><CategoryID>'.$lastinfo['categoty2'].'</CategoryID></SecondaryCategory>';
        }

        //标题


        if(!empty($lastinfo['title']))
        {
            $xml .=' <Title>'.$lastinfo['title'].'</Title>';
        }
        //子标题
        if(!empty($lastinfo['title1']))
        {
            $xml .='<SubTitle>'.$lastinfo['title1'].'</SubTitle>';
        }

        //修改数量    价格  要考虑到刊登类型。

        //
        if($lastinfo['ad_type'] == 'paimai')
        {

        }






        //





    }

    public function modifyTittle($eBayAuthToken,$itemid,$title)
    {
        $xml ='';
        $xml .="<?xml version='1.0' encoding='utf-8'?>";
        $xml .='<ReviseItemRequest  xmlns="urn:ebay:apis:eBLBaseComponents">';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$eBayAuthToken.'</eBayAuthToken></RequesterCredentials>';
        $xml .='<Item>';
        $xml .='<ItemID>'.$itemid.'</ItemID>';
        $xml .=' <Title>'.$title.'</Title>';
        $xml .='</Item></ReviseItemRequest>';
        $result = $this->sendHttpRequest($xml);
        return $result;
    }



    public function modifyDescription($itemid,$description)
    {
        $xml ='';
        $xml .="<?xml version='1.0' encoding='utf-8'?>";
        $xml .='<ReviseItemRequest  xmlns="urn:ebay:apis:eBLBaseComponents">';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$this->requestToken.'</eBayAuthToken></RequesterCredentials>';
        $xml .='<Item>';
        $xml .='<ItemID>'.$itemid.'</ItemID>';
        $xml.='<Description><![CDATA['.trim($description).']]></Description>';
        $xml .=' <DescriptionReviseMode>Replace</DescriptionReviseMode></Item></ReviseItemRequest>';
        $result = $this->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($result);
        $response = simplexml_import_dom($responseDoc);
        return $response;
    }

    public function modifySku($itemid,$info)
    {
        $xml ='';
        $xml .="<?xml version='1.0' encoding='utf-8'?>";
        $xml .='<ReviseItemRequest  xmlns="urn:ebay:apis:eBLBaseComponents">';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$this->requestToken.'</eBayAuthToken></RequesterCredentials>';
        $xml .='<Item>';
        $xml .='<ItemID>'.$itemid.'</ItemID>';
        $xml .='<SKU>'.$info['sku'].'</SKU>';
        $xml .='<StartPrice currencyID="'.$info['currency'].'">'.$info['price'].'</StartPrice>';
        $xml .='<Quantity>'.$info['quantity'].'</Quantity>';
        $xml .='</Item></ReviseItemRequest>';
        $result = $this->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($result);
        $response = simplexml_import_dom($responseDoc);
        return $response;
    }

        //http://192.168.1.6:3000/image/SS4039-56495041b68f2985479179d4.jpg
    public function upLoadPicture($multiPartImageData){
        $xml ='';
        $xml .="<?xml version='1.0' encoding='utf-8'?>";
        $xml .='<UploadSiteHostedPicturesRequest   xmlns="urn:ebay:apis:eBLBaseComponents">';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$this->requestToken.'</eBayAuthToken></RequesterCredentials>';
        $xml .='</UploadSiteHostedPicturesRequest>';
        $boundary = "MIME_boundary";
        $CRLF = "\r\n";

        // The complete POST consists of an XML request plus the binary image separated by boundaries
        $firstPart   = '';
        $firstPart  .= "--" . $boundary . $CRLF;
        $firstPart  .= 'Content-Disposition: form-data; name="XML Payload"' . $CRLF;
        $firstPart  .= 'Content-Type: text/xml;charset=utf-8' . $CRLF . $CRLF;
        $firstPart  .= $xml;
        $firstPart  .= $CRLF;

        $secondPart ='';
        $secondPart .= "--" . $boundary . $CRLF;
        $secondPart .= 'Content-Disposition: form-data; name="dummy"; filename="dummy"' . $CRLF;
        $secondPart .= "Content-Transfer-Encoding: binary" . $CRLF;
        $secondPart .= "Content-Type: application/octet-stream" . $CRLF . $CRLF;
        $secondPart .= $multiPartImageData;
        $secondPart .= $CRLF;
        $secondPart .= "--" . $boundary . "--" . $CRLF;

        $fullPost = $firstPart . $secondPart;



    //    $xml .='</UploadSiteHostedPicturesRequest>';
      //  echo $xml;exit;
        $result = $this->sendHttpRequesttest($fullPost);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($result);
        $response = simplexml_import_dom($responseDoc);
        return $response;
    }

    public function sendHttpRequestPic($requestBody)
    {
        $headers = array (
            'Content-Type: multipart/form-data; boundary=MIME_boundary',
            'Content-Length: ' . strlen($requestBody),
            'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->compatLevel,  // API version

            'X-EBAY-API-DEV-NAME: ' . $this->devID,     //set the keys
            'X-EBAY-API-APP-NAME: ' . $this->appID,
            'X-EBAY-API-CERT-NAME: ' . $this->certID,

            'X-EBAY-API-CALL-NAME: ' . $this->verb,		// call to make
            'X-EBAY-API-SITEID: ' . $this->siteID,      // US = 0, DE = 77...
        );
        //initialize a CURL session - need CURL library enabled
        $connection = curl_init();
        curl_setopt($connection, CURLOPT_URL, $this->serverUrl);
        curl_setopt($connection, CURLOPT_TIMEOUT,60 );
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($connection, CURLOPT_POST, 1);
        curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($connection, CURLOPT_FAILONERROR, 0 );
        curl_setopt($connection, CURLOPT_FOLLOWLOCATION, 1 );
        //curl_setopt($connection, CURLOPT_HEADER, 1 );           // Uncomment these for debugging
        //curl_setopt($connection, CURLOPT_VERBOSE, true);        // Display communication with serve
        curl_setopt($connection, CURLOPT_USERAGENT, 'ebatns;xmlstyle;1.0' );
        curl_setopt($connection, CURLOPT_HTTP_VERSION, 1 );       // HTTP version must be 1.0
        $response = curl_exec($connection);
        var_dump($response);exit;

        if ( !$response ) {
            print "curl error " . curl_errno($connection ) . "\n";
        }
        curl_close($connection);
        return $response;
    }

    public function upLoadPicture2()
    {
        //http://imgurl.moonarstore.com/upload/E3112/E3112-2.jpg
        $xml ='';
        $xml .="<?xml version='1.0' encoding='utf-8'?>";
        $xml .='<UploadSiteHostedPicturesRequest   xmlns="urn:ebay:apis:eBLBaseComponents">';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$this->requestToken.'</eBayAuthToken></RequesterCredentials>';
       // $xml .='<ExternalPictureURL>http://imgurl.moonarstore.com/upload/E3112/E3112-2.jpg</ExternalPictureURL>';
        //http://imgurl.moonarstore.com/image/DA3685-5674c0fe6bb83d9776834d46.jpg
        //http://imgurl.moonarstore.com:3000/image-resize/1000x1000x100/DA3685-5674c0fe6bb83d9776834d46.jpg
        $xml .='<ExternalPictureURL>http://imgurl.moonarstore.com:3000/image-resize/1000x1000x100/DA3685-5674c0fe6bb83d9776834d46.jpg</ExternalPictureURL>';
        $xml .='</UploadSiteHostedPicturesRequest>';
        $result = $this->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($result);
        $response = simplexml_import_dom($responseDoc);
        return $response;
    }

    public function upLoadPicture3($multiPartImageData)
    {
        $xmlReq = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $xmlReq .= '<UploadSiteHostedPicturesRequest xmlns="urn:ebay:apis:eBLBaseComponents">' . "\n";
       // $xmlReq .= "<Version>923</Version>\n";
       // $xmlReq .= "<PictureName>tes111t</PictureName>\n";
        $xmlReq .= "<RequesterCredentials><eBayAuthToken>'.$this->requestToken.'</eBayAuthToken></RequesterCredentials>\n";
        $xmlReq .= '</UploadSiteHostedPicturesRequest>';

        $boundary = "MIME_boundary";
        $CRLF = "\r\n";

        // The complete POST consists of an XML request plus the binary image separated by boundaries
        $firstPart   = '';
        $firstPart  .= "--" . $boundary . $CRLF;
        $firstPart  .= 'Content-Disposition: form-data; name="XML Payload"' . $CRLF;
        $firstPart  .= 'Content-Type: text/xml;charset=utf-8' . $CRLF . $CRLF;
        $firstPart  .= $xmlReq;
        $firstPart  .= $CRLF;

        $secondPart ='';
        $secondPart .= "--" . $boundary . $CRLF;
        $secondPart .= 'Content-Disposition: form-data; name="dummy"; filename="dummy"' . $CRLF;
        $secondPart .= "Content-Transfer-Encoding: binary" . $CRLF;
        $secondPart .= "Content-Type: application/octet-stream" . $CRLF . $CRLF;
        $secondPart .= $multiPartImageData;
        $secondPart .= $CRLF;
        $secondPart .= "--" . $boundary . "--" . $CRLF;

        $fullPost = $firstPart . $secondPart;

        $respXmlStr = $this->sendHttpRequestPic($fullPost);

        var_dump($respXmlStr);exit;
    }


}