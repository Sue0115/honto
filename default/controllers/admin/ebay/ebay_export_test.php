<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-11-25
 * Time: 13:42
 */

ini_set('memory_limit', '2048M');
set_time_limit(0);
header('Content-Type: text/html; Charset=utf-8');

class Ebay_export extends Admin_Controller
{

    private $platfrom ;
    private $ebay_shipment;
    private $ebay_paypal;

    function __construct()
    {
        $this->platfrom =array(0=>14);
        $this->ebay_shipment =array(
            0=>array(
                1=>'ePacketHongKong',
                2=>'EconomyShippingFromOutsideUS',
                3=>'OtherInternational',
            )
        );
        $this->ebay_paypal =array(
            0=>array(
                1=>10,
                2=>'salamoers@126.com',
                3=>'flybird4545@126.com',
            )
        );

        parent::__construct();
        $this->load->model(
            array(
                'ebay/ebay_list_model',//'sharepage'
                'ebay/ebay_template_model',
                'ebay/ebay_template_html_model',
                'products/Products_data_model',
                'ebay/Ebay_user_tokens_model',
                'shipment_model',
                'sales_platform_model',
                'system_model'
            )
        );
        $this->load->library('MyEbayNew');
        $this->load->library('phpexcel/PHPExcel', 'phpexcel/PHPExcel/Reader/Excel5.php');
        $this->ebaytest = new MyEbayNew();
        $this->userToken = $this->Ebay_user_tokens_model;
    }


    public function index()
    {

        if (isset($_REQUEST['add']) && ($_REQUEST['add'] == 'add')) {

            $phpExcel = new PHPExcel_Reader_Excel5;
            $extend = explode(".", $_FILES['excelFile']["name"]);
            $val = count($extend) - 1;
            $extend = strtolower($extend[$val]);
            if ($extend != 'xls' && $extend != 'xlsx') {
                echo '<script>alert("文件格式不正确，请上传EXCEL文件");history.go(-1);</script>';
                exit;
            }
            $filename = $_FILES['excelFile']["tmp_name"];
            $main_name = substr($_FILES['excelFile']["name"], 0, strpos($_FILES['excelFile']["name"], '.'));
            $objPHPExcel = $phpExcel->load($filename);
            $sheet = $objPHPExcel->getSheet();
            $rows = $sheet->getHighestRow();//EXCEL行数

            //             $cols=$sheet->getHighestColumn();
            $i = 0;
            $data_array = array();
            for ($j = 2; $j <= $rows; $j++) {
                if (trim($sheet->getCell("A" . $j)->getValue())) {
                    $data_array[$i]['name'] = trim($sheet->getCell("A" . $j)->getValue());//模板名字
                    $data_array[$i]['sku_search'] = trim($sheet->getCell("B" . $j)->getValue());//SKU搜索
                    $data_array[$i]['ad_type'] = trim($sheet->getCell("C" . $j)->getValue());//刊登类型
                    $data_array[$i]['ebayaccount'] = trim($sheet->getCell("D" . $j)->getValue());//ebay 账号
                    $data_array[$i]['site'] = trim($sheet->getCell("E" . $j)->getValue());//站点
                    $data_array[$i]['sku'] = trim($sheet->getCell("F" . $j)->getValue());//sku
                    $data_array[$i]['title'] = trim($sheet->getCell("G" . $j)->getValue());//主标题
                    $data_array[$i]['categoty1'] = trim($sheet->getCell("H" . $j)->getValue());//第一分类
                    $data_array[$i]['item_specifics'] = trim($sheet->getCell("I" . $j)->getValue());//物品属性
                    $data_array[$i]['upc'] = trim($sheet->getCell("J" . $j)->getValue());//UPC
                    $data_array[$i]['ean'] = trim($sheet->getCell("K" . $j)->getValue());//EAN
                    $data_array[$i]['isb'] = trim($sheet->getCell("L" . $j)->getValue());//EAN
                    $data_array[$i]['publication_template_html'] = trim($sheet->getCell("M" . $j)->getValue());//描述模板名字
                    $data_array[$i]['publication_template'] = trim($sheet->getCell("N" . $j)->getValue());//卖家描述名字
                    $data_array[$i]['returns_days'] = trim($sheet->getCell("O" . $j)->getValue());//退货天数
                    $data_array[$i]['returns_delay'] = trim($sheet->getCell("P" . $j)->getValue());//提供节假日延期退货至12月31日
                    $data_array[$i]['returns_type'] = trim($sheet->getCell("Q" . $j)->getValue());//退款方式
                    $data_array[$i]['returns_cost_by'] = trim($sheet->getCell("R" . $j)->getValue());//退货运费由谁负担
                    $data_array[$i]['return_details'] = trim($sheet->getCell("S" . $j)->getValue());//退货政策详情

                    $i++;
                }
            }
            $result =   $this->userToken->getInfoByTokenId(4);






            foreach ($data_array as $list) {
                $list['published_day'] ='GTC';
                // 处理属性
                if (!empty($list['item_specifics'])) {
                    $new_item_specifics = $list['item_specifics'];
                    $new_item_specifics = explode(';', $new_item_specifics);
                    $item_specifics = array();
                    foreach ($new_item_specifics as $specifics) {
                        $sp = explode(',', $specifics);
                        $item_specifics[][$sp[0]] = $sp[1];
                    }

                    $list['item_specifics'] = json_encode($item_specifics);
                }
                // 处理一下模板
                if(!empty($list['publication_template_html'])){
                    $option=array();
                    $option['where']['template_name'] = $list['publication_template_html'];
                    $result_one = $this->ebay_template_html_model->getOne($option,true);
                    if(!empty($result_one)){
                        $list['publication_template_html'] = $result_one['id'];
                    }else{
                        $list['publication_template_html'] = '';
                    }

                }
                //处理卖家描述
                if(!empty($list['publication_template'])){
                    $option=array();
                    $option['where']['name'] = $list['publication_template'];
                    $result_one = $this->ebay_template_model->getOne($option,true);
                    if(!empty($result_one)){
                        $list['publication_template'] = $result_one['id'];
                    }else{
                        $list['publication_template'] = '';
                    }
                }

                // 更近SKU 找出erp的信息

                $option=array();
                $this->db->like('products_sku', $list['sku_search'], 'after');
                $product_info = $this->Products_data_model->getAll2array($option,true);
                if(isset($product_info[0]['products_html_mod'])){
                    $list['description_details'] =htmlspecialchars_decode($product_info[0]['products_html_mod']);

                }else{
                    echo $list['sku_search'].' 获取SKU 信息错误'.'<br/>';
                    continue;
                }

                $price = $this->calculateSkupriceMid(324,$product_info[0],$list['site']);

                if($price <10){
                    //小于10 重新计算售价 type =1 用小PP
                    $price = $this->calculateSkupriceMid(324,$product_info[0],$list['site'],1);
                }

                if($price>20){

                    $price = $this->calculateSkupriceMid(272,$product_info[0],$list['site']); // 平邮计算的价格大于20美金 就重新按eub去计算价格
                    $list['price'] = $price;
                    $list['inter_trans_type'] = $this->ebay_shipment[$list['site']][1];
                    $list['inter_trans_cost'] =0.00;
                    $list['inter_trans_extracost'] =0.00;


                    $list['international_type1'] =$this->ebay_shipment[$list['site']][3];
                    $list['international_cost1'] =0.00;
                    $list['international_extracost1'] =0.00;
                    $list['international_is_worldwide1'] ='on';


                }else{
                    if($price>5){ // 这是要计算出运费
                        $eub_price = $this->calculateSkupriceMid(272,$product_info[0],$list['site']); // eub 价格
                        $dif_price = $eub_price-$price; //  要额外加的运费

                        $list['price'] = $price;
                        $list['inter_trans_type'] = $this->ebay_shipment[$list['site']][1];
                        $list['inter_trans_cost'] =$dif_price;
                        $list['inter_trans_extracost'] =$dif_price;


                        $list['international_type1'] =$this->ebay_shipment[$list['site']][3];
                        $list['international_cost1'] =0.00;
                        $list['international_extracost1'] =0.00;
                        $list['international_is_worldwide1'] ='on';


                    }else{ // 不需要计算运费

                        $list['price'] = $price;
                        if($list['price'] <0.99)
                        {
                            $list['price'] = 0.99;
                        }
                        $list['inter_trans_type'] = $this->ebay_shipment[$list['site']][2];
                        $list['inter_trans_cost'] =0.00;
                        $list['inter_trans_extracost'] =0.00;


                        $list['international_type1'] =$this->ebay_shipment[$list['site']][3];
                        $list['international_cost1'] =0.00;
                        $list['international_extracost1'] =0.00;
                        $list['international_is_worldwide1'] ='on';

                    }
                }

                if($list['price'] >= $this->ebay_paypal[$list['site']][1]){
                    $list['paypal_account'] = $this->ebay_paypal[$list['site']][2];
                }else{
                    $list['paypal_account'] = $this->ebay_paypal[$list['site']][3];
                }


                // 获取图片信息

                $ebay_picture =    $this->getSkuPicture($list['sku_search']);
                $ebay_picture = array_slice($ebay_picture, 0, 11);
                $list['ebay_picture'] = json_encode($ebay_picture);
                $list['template_deteils'] = json_encode($ebay_picture);


                $list['item_status'] = 1500;

                $list['all_buyers'] ='notall';
                $list['nopaypal'] ='';
                $list['noti_trans'] ='on';
                $list['is_abandoned'] ='';
                $list['abandoned_num'] ='';
                $list['abandoned_day'] ='';
                $list['is_report'] ='';
                $list['report_num'] ='';
                $list['report_day'] ='';
                $list['is_trust_low'] ='';
                $list['already_buy'] ='on';
                $list['buy_num'] ='5';
                $list['buy_condition'] ='';
                $list['buy_credit'] ='';
                $list['returns_policy'] ='ReturnsAccepted';

                $list['quantity'] = 10;
                $list['item_location'] = 'Hongkong';
                $list['item_country'] = 'HK';

                $list['inter_process_day'] = 1;



                //
                if($list['categoty1']){
                    $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$list['site'],'GetSuggestedCategories');

                    $lastinfo = $this->ebaytest->getSuggestedCategories($result['user_token'],$list['categoty1']);
                    $responseDoc = new DomDocument();
                    $responseDoc->loadXML($lastinfo);
                    $response = simplexml_import_dom($responseDoc);
                    if(isset($response->SuggestedCategoryArray->SuggestedCategory))
                    {
                        $suggestedCategoryArray  =  $response->SuggestedCategoryArray->SuggestedCategory;

                        foreach($suggestedCategoryArray as $suggest)
                        {
                            $string ='';
                            $list['categoty1'] = intval($suggest->Category->CategoryID);
                            $string_arr = $suggest->Category->CategoryParentName;
                            foreach($string_arr as $str){
                                $string = $string.'>'.$str;
                            }

                            $string= $string.'>'.(string)$suggest->Category->CategoryName;
                            $list['categoty1_all'] = $string;
                            break; //第一个就是匹配度最高的l
                        }
                    }else{
                        $list['categoty1'] ='';
                    }
                }




                $this->ebay_list_model->add($list);

            }



            //   var_dump($data_array);


        }

        $this->_template('admin/ebay/ebay_export');


    }

    public function getSkuPicture($sku){

        $ebay_picture =array();

        $url ='http://120.24.100.157:3000/api/sku/'.$sku.'?include-sub=true&distinct=true';

        $result = $this->getCurl($url);
        $result = json_decode($result,true);
        if(!empty($result)){
            $i=1;
            foreach($result as $re){
                if($i==11){
                    break;
                }
                $mid= str_replace("image", "image-resize/1000x1000x100", $re['url']);
                $last = 'http://120.24.100.157:3000'.$mid;
                $ebay_picture[] = $last;
                $i++;
            }
            return $ebay_picture;
        }
        $url = "http://imgurl.moonarstore.com/get_image.php?dirName=".$sku;//美国图片服务器脚本的路径
        $result = $this->getCurl($url);
        $result = json_decode($result,true);
        if(!empty($result)){
            return $result;
        }

        $url = "http://imgurl.moonarstore.com/get_image.php?dirName=".$sku."&dir=SP";//美国图片服务器脚本的路径
        $result = $this->getCurl($url);
        $result = json_decode($result,true);
        if(!empty($result)){
            return $result;
        }
        return $ebay_picture;
    }

    public function getCurl($url)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);
        return $result;
    }




    public function calculateSkupriceMid($shipmend_id,$product_info,$platform_id,$type =0){

        $shipment_option = array();
        $shipment_option['where']['shipmentID'] = $shipmend_id;
        $shipment_one = $this->shipment_model->getOne($shipment_option,true);
        $shipmentCalculateMethod = $shipment_one['shipmentCalculateMethod'];
        $shipmentCalculateElementArray =  $shipment_one['shipmentCalculateElementArray'];
        $weight = $product_info['products_weight'];


        $shipFee =0;
        if($shipmentCalculateMethod=='weight'){
            $shipmentCalculateElementArray = unserialize($shipmentCalculateElementArray);
            //运费 = 首重费用 + {[总重 - 首重] ÷ 续重} * 续重费用 + 操作费
            $firstFee         = $shipmentCalculateElementArray['first']['feeTax'];
            $firstWeight      = $shipmentCalculateElementArray['first']['unit'];
            $additionalFee    = $shipmentCalculateElementArray['additional']['feeTax'];
            $additionalWeight = $shipmentCalculateElementArray['additional']['unit'];
            $operateFee       = $shipmentCalculateElementArray['operational'];
            $shipFee = $firstFee + ceil(($weight - $firstWeight) / $additionalWeight) * $additionalFee + $operateFee;

        }else{
            $arr = unserialize($shipmentCalculateElementArray);
            foreach($arr as $v1)
            {
                if($weight >$v1['start'] && $weight <= $v1['end'])
                {
                    $shipFee =   $v1['operational'];
                }
            }
        }


        $platform_option = array();
        $platform_option['where']['platID'] = $this->platfrom[$platform_id];

        $platform_one = $this->sales_platform_model->getOne($platform_option,true);

        $platFee = $platform_one['platOperateFee'];
        if($type == 1)
        {
            $platFee = $platform_one['platOperateFee']-0.24;
        }
        $platFeeRate = $platform_one['platFeeRate'];
        if($type == 1)
        {
            $platFeeRate = $platform_one['platFeeRate']+0.03;
        }

        $cost = $product_info['products_value'];
        $profitRate = 15;
        $exchangeRate = 6.2;
        $price= ((($cost + $shipFee) / $exchangeRate + $platFee)/(1- $profitRate/100-$platFeeRate/100));
        $price = round($price,2); // 得出售价

        return $price;
    }



    public function calculateSkuprice($shipmentCalculateMethod,$shipmentCalculateElementArray,$weight){
        $shipFee =0;
        if($shipmentCalculateMethod=='weight'){
            $shipmentCalculateElementArray = unserialize($shipmentCalculateElementArray);
            //运费 = 首重费用 + {[总重 - 首重] ÷ 续重} * 续重费用 + 操作费
            $firstFee         = $shipmentCalculateElementArray['first']['feeTax'];
            $firstWeight      = $shipmentCalculateElementArray['first']['unit'];
            $additionalFee    = $shipmentCalculateElementArray['additional']['feeTax'];
            $additionalWeight = $shipmentCalculateElementArray['additional']['unit'];
            $operateFee       = $shipmentCalculateElementArray['operational'];
            $shipFee = $firstFee + ceil(($weight - $firstWeight) / $additionalWeight) * $additionalFee + $operateFee;

        }else{
            $arr = unserialize($shipmentCalculateElementArray);
            foreach($arr as $v1)
            {
                if($weight >$v1['start'] && $weight <= $v1['end'])
                {
                    $shipFee =   $v1['operational'];
                }
            }
        }
        return $shipFee;

    }
}