<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Smt_product_list_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct('smt_product_list');
        
    }

    /**
     * 判断广告是否已存在
     * @param  [type] $productId [description]
     * @return [type]            [description]
     */
    public function check_product_is_exists($productId){
        $options['where'] = array('productId' => (string)$productId);
        $result  = $this->getOne($options, true);
    	return $result ? true : false;
    }

    /**
     * 根据产品ID获取产品基本信息
     * @param $productId
     * @return Ambigous
     */
    public function getProductListInfo($productId){
        $options['where'] = array('productId' => (string)$productId);
        return $this->getOne($options, true);
    }
    
    /**
     * 获取产品的字段信息
     * @param unknown $productId
     * @param string $fields
     * @return multitype:
     */
    public function getProductFields($productId, $fields='*'){
    	$productId = trim($productId);
    	$rs = array();
    	if ($productId){
    		$option['select'] = $fields ? $fields : '*';
    		$option['where']  = array('productId' => (string)$productId);
    		$rs = $this->getOne($option, true);
    	}
    	
    	return $rs;
    }

    /**
     * 获取产品列表的字段
     * @param $productIds
     * @param string $fields
     * @return array
     */
    public function getProductsFields($productIds, $fields='*'){
        if ($productIds){

            $option = array();
            if (is_array($productIds)){
                $temp = array();
                foreach ($productIds as $p){
                    $temp[] = (string)trim($p);
                }
                $option['where_in'] = array('productId' => $temp);
            }else {
                $option['where'] = (string)trim($productIds);
            }

            $option['select'] = $fields ? $fields : '*';

            $data =  $this->getAll2Array($option);
            $rs = array();
            if (!empty($data)){
                foreach ($data as $row){
                    $rs[$row['productId']] = $row;
                }
            }
            return $rs;
        }else {
            return array();
        }
    }

    /**
     * 判断产品是不是在同一个账号中
     * @param $productIds
     * @return bool
     */
    public function checkProductsInSameAccount($productIds){
        if (!empty($productIds)){
            $options = array();
            $options['where_in'] = array('productId' => $productIds);
            $options['select'] = 'token_id';
            $rs = $this->getAll2Array($options);
            if (!empty($rs)){
                $temp = array();
                foreach ($rs as $r){
                    $temp[] = $r['token_id'];
                }
                $temp = array_unique($temp);
                return count($temp) == 1 ? array_shift($temp) : false;
            }
        }
        return false;
    }

    /**
     * 获取产品列表
     * @param $exceptIds：要排除的产品ID
     * @param $returnArr: 返回分页信息
     * @param $fields: 要查询的字段
     * @return array: 返回查询的结果数组
     */
    public function getProductsList($params, &$returnArr, $fields){

        $dataList = array();
        if (!empty($params['token_id'])) {
            if (!empty($params['productIds'])) {
                //要排除掉的产品ID
                $exceptIds                            = explode(',', $params['productIds']);
                $options['where_not_in']['productId'] = $exceptIds;
            }

            if (!empty($fields)) {
                $options['select'] = $fields;
            }

            $options['where']['token_id'] = $params['token_id']; //账号还是必须要查询的

            //标题
            if (!empty($params['subject'])) {
                $options['like']['subject'] = $params['subject'];
            }

            //产品分组
            if (!empty($params['productGroup']) && $params['productGroup'] != -2){
                $options['where']['groupId'] = $params['productGroup'];
            }

            if (!empty($params['from']) && $params['from'] == 'draft'){
                //状态限制，这个必须
                $options['where_in']['productStatusType'] = array('newData', 'waitPost');
            }else {
                //到期时间
                if (!empty($params['offLineTime']) && !in_array($params['offLineTime'], array(0, -1))) {
                    $wsOfflineDate                         = date('Y-m-d H:i:s', strtotime('+' . $params['offLineTime'] . ' day'));
                    $options['where']['wsOfflineDate < ']  = $wsOfflineDate;
                    $options['where']['wsOfflineDate >= '] = date('Y-m-d H:i:s');
                }

                //状态限制，这个必须
                $options['where_in']['productStatusType'] = array('auditing', 'editingReq', 'offline', 'onSelling');
            }

            //是否删除限制,同样必须
            $options['where']['isRemove'] = 0;

            $page                = $params['page'];
            $pageSize            = $params['pageSize'];
            $options['page']     = $pageSize;
            $options['per_page'] = ($page - 1) * $pageSize;

            $dataList = $this->getAll($options, $returnArr, true);
        }
        return $dataList;
    }

    /**
     * 修改SMT在线产品
     * @param $params
     * @return array
     */
    public function postEditAeProduct($params){

//sleep(1);//这几行后边再进行删除处理
//        return array('status' => true, 'info' => 'error21');

        //加载一些需要用到的类
        $this->load->library('MySmt');
        $this->load->model(array('smt/Smt_user_tokens_model', 'smt/Smt_product_skus_model', 'smt/Smt_product_detail_model'));
        $this->tokenModel = $this->Smt_user_tokens_model;
        $this->skuModel = $this->Smt_product_skus_model;
        $this->detailModel = $this->Smt_product_detail_model;
        $smt = new MySmt();

        if (!empty($params['token_id']) && !empty($params['productId'])) {
            $tokenInfo = $this->tokenModel->getOneTokenInfo($params['token_id']);
            $smt->setToken($tokenInfo);

            $productId   = $params['productId'];
            //查找详情信息
            $productInfo = $this->getProductFields($productId);
            if (empty($productInfo) || $productInfo['isRemove'] == 1){
                return array('status' => false, 'info' => '产品'.$productId.'已删除，修改失败');
            }

            /***开始查询并提交产品信息，提交成功的话就保存变更的信息，不成功的话，直接提示下就好了***/
            //查询SKU信息
            $skusInfo = $this->skuModel->getProductSkuProperty($productId);
            if (empty($skusInfo)){
                return array('status' => false, 'info' => '产品'.$productId.'SKU信息不存在，修改失败');
            }

            //查询详情信息
            $detailInfo = $this->detailModel->getProductDetailInfo($productId);
            if (empty($detailInfo)){
                return array('status' => false, 'info' => '产品'.$productId.'详情信息不存在，修改失败');
            }

            /*开始组装产品信息*/
            $productData              = array();
            $productData['productId'] = $productId;
            $detail                   = htmlspecialchars_decode($detailInfo['detail']);
            $detailModify = false;
            //要添加的产品信息模板
            $typeArray = array(
                'custom'   => 'customText',
                'relation' => 'relatedProduct'
            );
            if (!empty($params['tModuleId']) && !empty($params['tModuleName']) && !empty($params['tModuleType'])){
                $kseWidget = '<kse:widget data-widget-type="'.$typeArray[$params['tModuleType']].'" id="'.$params['tModuleId'].'" title="'.$params['tModuleName'].'" type="'.$params['tModuleType'].'"></kse:widget>';
                $detail = $kseWidget.$detail;
                unset($kseWidget);
                $detailModify = true;
            }
            //要追加的产品信息模板
            if (!empty($params['bModuleId']) && !empty($params['bModuleName']) && !empty($params['bModuleType'])){
                $kseWidget = '<kse:widget data-widget-type="'.$typeArray[$params['bModuleType']].'" id="'.$params['bModuleId'].'" title="'.$params['bModuleName'].'" type="'.$params['bModuleType'].'"></kse:widget>';
                $detail = $detail.$kseWidget;
                unset($kseWidget);
                $detailModify = true;
            }
            $productData['detail']    = $detail;

            $priceCreaseNum  = $params['priceCreaseNum'];
            $priceCreaseType = $params['priceCreaseType'];

            /**计算价格信息**/
            if (!empty($priceCreaseNum)){
                $numList  = explode(',', $priceCreaseNum);
                $typeList = explode(',', $priceCreaseType);

                foreach ($skusInfo as $skuRow){
                    //循环计算下价格
                    $temp_property = array();
                    if (!empty($skuRow['aeopSKUProperty'])){
                        $temp_property = unserialize($skuRow['aeopSKUProperty']);
                    }

                    $price = $skuRow['skuPrice'];
                    foreach ($numList as $key => $val){
                        $type = $typeList[$key];
                        if ($type == 0){ //按数值加减
                            $price += $val;
                        }elseif ($type == 1){ //按百分比加减
                            $price *= (1 + $val / 100);
                        }
                    }
                    //$price = round($price, 2); //四舍五入保留2位小数
                    $price = sprintf('%01.2f', $price);
                    $aeopAeProductSKUs[] = array(
                        'skuPrice'        => $price,
                        'skuCode'         => $skuRow['smtSkuCode'],
                        'ipmSkuStock'     => $skuRow['ipmSkuStock'],
                        'aeopSKUProperty' => $temp_property
                    );
                }
            }else {
                foreach ($skusInfo as $skuRow){
                    $temp_property = array();
                    if (!empty($skuRow['aeopSKUProperty'])){
                        $temp_property = unserialize($skuRow['aeopSKUProperty']);
                    }
                    $aeopAeProductSKUs[] = array(
                        'skuPrice'        => $skuRow['skuPrice'],
                        'skuCode'         => $skuRow['smtSkuCode'],
                        'ipmSkuStock'     => $skuRow['ipmSkuStock'],
                        'aeopSKUProperty' => $temp_property
                    );
                }
            }
            $productData['aeopAeProductSKUs'] = json_encode($aeopAeProductSKUs); //要组装下
            if (count($aeopAeProductSKUs) == 1) {
                $productData['productPrice'] = $aeopAeProductSKUs[0]['skuPrice']; //商品一口价，一个SKU就写，不然就不写...
            }

            $productData['deliveryTime']         = $productInfo['deliveryTime'];
            $productData['categoryId']           = $productInfo['categoryId'];
            $productData['subject']              = trim($params['subject']);
            $productData['keyword']              = trim($params['keywords']);
            if (!empty($params['productMoreKeywords1'])) {
                $productData['productMoreKeywords1'] = trim($params['productMoreKeywords1']); //可选
            }
            if (!empty($params['productMoreKeywords2'])) {
                $productData['productMoreKeywords2'] = trim($params['productMoreKeywords2']); //可选
            }

            if (!empty($productInfo['groupId'])) {
                $productData['groupId'] = $productInfo['groupId']; //可选
            }
            $productData['promiseTemplateId']    = $params['promiseTemplateId']; //可选

            $productData['freightTemplateId'] = $params['freightTemplateId'];
            $productData['isImageDynamic']    = $detailInfo['isImageDynamic'] ? 'true' : 'false';//可选
            $productData['imageURLs']         = $detailInfo['imageURLs'];
            $productData['isImageWatermark']  = 'false'; //可选，不过暂时都不加
            $packageWay                       = $params['packageWay'];
            list($packageType, $unit, $lotNum) = explode('-', $packageWay);
            $productData['productUnit']   = $unit;
            $productData['packageType']   = $packageType;
            $productData['lotNum']        = $lotNum;
            $productData['packageLength'] = $params['packageLength'];
            $productData['packageWidth']  = $params['packageWidth'];
            $productData['packageHeight'] = $params['packageHeight'];
            $productData['grossWeight']   = $params['grossWeight'];
            $productData['isPackSell']    = $detailInfo['isPackSell'] ? 'true' : 'false';
            if ($detailInfo['isPackSell']) { //自定义记重时必填的
                $productData['baseUnit']  = $detailInfo['baseUnit'];
                $productData['addUnit']   = $detailInfo['addUnit'];
                $productData['addWeight'] = $detailInfo['addWeight'];
            }
            $productData['wsValidNum'] = $productInfo['wsValidNum']; //有效天数
            $productData['src']        = $detailInfo['src'];

            if (!empty($detailInfo['aeopAeProductPropertys'])) { //产品属性
                $aeopAeProductPropertys                = unserialize($detailInfo['aeopAeProductPropertys']);
                $productData['aeopAeProductPropertys'] = json_encode($aeopAeProductPropertys); //属性
            }

            $productData['bulkOrder']    = $detailInfo['bulkOrder']; //批发最小数量
            $productData['bulkDiscount'] = $detailInfo['bulkDiscount']; //批发折扣
//print_r($productData);exit;
            $api = 'api.editAeProduct';
            $return = $smt->getJsonDataUsePostMethod($api, $productData);
            $result = json_decode($return, true);
            /***操作完成了***/

            if (empty($result)) {
                return array('status' => false, 'info' => '产品'.$productId.'没有返回数据信息，请联系IT');
            }elseif (array_key_exists('success', $result) && $result['success']){ //修改成功

                //修改成功后，事务保存修改后的信息
                $newProductInfo                  = array();
                $newProductInfo['subject']       = $params['subject'];
                $newProductInfo['grossWeight']   = $params['grossWeight'];
                $newProductInfo['packageLength'] = $params['packageLength'];
                $newProductInfo['packageWidth']  = $params['packageWidth'];
                $newProductInfo['packageHeight'] = $params['packageHeight'];
                $newProductInfo['productStatusType'] = 'onSelling'; //修改成功后会变成在售状态

                $this->db->trans_begin();

                $minPrice = 0;
                $maxPrice = 0;
                foreach ($aeopAeProductSKUs as $skuOne){
                    $newProductSku['skuPrice'] = $skuOne['skuPrice'];
                    $valId = checkProductSkuAttrIsOverSea($skuOne['aeopSKUProperty']); //海外仓属性ID
                    $where = array(
                        'smtSkuCode'   => $skuOne['skuCode'],
                        'productId'    => (string)$productId,
                        'overSeaValId' => $valId
                    );
                    $minPrice = ($minPrice == 0) ? $skuOne['skuPrice'] : ($minPrice < $skuOne['skuPrice'] ? $minPrice : $skuOne['skuPrice']);
                    $maxPrice = $maxPrice > $skuOne['skuPrice'] ? $maxPrice : $skuOne['skuPrice'];
                    $this->skuModel->update($newProductSku, array('where' => $where));
                    if ($this->db->_error_message()){
                        $this->db->trans_rollback();
                        return array('status' => true, 'info' => '产品'.$productId.'修改成功,同步本地SKU数据出错');
                    }
                }

                $newProductInfo['productMinPrice'] = $minPrice; //最小价格
                $newProductInfo['productMaxPrice'] = $maxPrice; //最大价格
                $newProductInfo['productPrice']    = $maxPrice;
                $this->update($newProductInfo, array('where' => array('productId' => (string)$productId)));
                if ($this->db->_error_message()){
                    $this->db->trans_rollback();
                    return array('status' => true, 'info' => '产品'.$productId.'修改成功,同步本地列表数据出错');
                }

                $newProductDetail['keyword']              = trim($params['keywords']);
                $newProductDetail['productMoreKeywords1'] = trim($params['productMoreKeywords1']);
                $newProductDetail['productMoreKeywords2'] = trim($params['productMoreKeywords2']);
                $newProductDetail['promiseTemplateId']    = trim($params['promiseTemplateId']);
                $newProductDetail['freightTemplateId']    = trim($params['freightTemplateId']);
                $newProductDetail['productUnit']          = $unit;
                $newProductDetail['packageType']          = $packageType == 'true' ? 1 : 0;
                $newProductDetail['lotNum']               = $lotNum;
                if ($detailModify) {
                    $newProductDetail['detail'] = htmlspecialchars($productData['detail']); //就怕添加了detail
                }
                $this->detailModel->update($newProductDetail, array('where' => array('productId' => (string)$productId)));
                if ($this->db->_error_message()){
                    $this->db->trans_rollback();
                    return array('status' => true, 'info' => '产品'.$productId.'修改成功,同步本地详情数据出错');
                }

                $this->db->trans_commit(); //事务提交
                return array('status' => true, 'info' => '');
            }else{ //修改失败
                $errorList = $this->defineErrorMsg(); //编辑产品的休息列表
                return array('status' => false, 'info' => !empty($errorList[$result['error_code']]) ? $errorList[$result['error_code']] : 'error_code:'.$result['error_code'].','.$result['error_message']);
            }
        }else {
            return array('status' => false, 'info' => '产品或账号错误，修改失败');
        }
    }

    /**
     * 定义错误属性
     * @return array
     */
    public function defineErrorMsg(){
        $errorMsg = array(
            '13004020' => '当前用户不在海外仓白名单内, 不允许编辑带海外仓属性的商品。',
            '13001042' => '拷贝详情中的图片到图片银行失败，请确保详情中的图片都是有效的。',
            '13002002' => '必填sku属性未填。',
            '13001002' => '对应的运费模板不存在; 包装毛重grossWeight>2',
            '13001003' => '如果包装毛重grossWeight>2,但运费模板中不含有除CPAM,GELS,HKPAM,EMS_SH_ZX_US以外任何物流公司',
            '13001004' => '搜索关键词以及多关键词含有分号或者逗号',
            '13001005' => 'keyword+productMoreKeywords1 +productMoreKeywords2总长度超过255',
            '13001007' => '类目不是叶子类目',
            '13001008' => '类目不存在',
            '13001011' => '多图产品主图url size不能小于2且大于6，单图产品主图url size必须是1',
            '13001013' => '取值范围不在1-60天',
            '13001014' => '批发折扣不在1-99区间',
            '13001015' => '批发最小数量取值范围不在2-100000区间',
            '13001016' => '产品组id该公司下不存在',
            '13001017' => '主图url不符合要求。',
            '13001018' => 'url地址图片不存在（图片必须是自己公司的本网站图片）',
            '13001019' => 'url地址图片不存在（图片必须是自己公司的本网站图片）',
            '13001021' => 'bulkOrder和bulkDiscount须同时有值或无值',
            '13001022' => '一口价为空，或者取值范围不在0-100000美元',
            '13001024' => '产品不存在。',
            '13001025' => '没有权限操作该产品。',
            '13001028' => '产品图片不存在。',
            '13001028' => '该产品不能编辑。',
            '13001028' => '主图不存在。',
            '13001029' => '该商品不能编辑，可能处于审核中',
            '13001030' => '该产品在活动中，不能操作。',
            '13001032' => '没有加入假一赔三服务，不能发布该类目',
            '13002003' => 'skuPropertyId为不属于该类目的属性。',
            '13002004' => 'propertyValueId不属于对应属性下的属性值',
            '13002005' => 'SKU属性顺序错误	sku属性是有顺序的，请按类目sku属性的顺序放置。',
            '13002006' => 'aeopAeProductSKUs不能为空，且不能大于3',
            '13002007' => 'skuPropertyId为null',
            '13002008' => 'propertyValueId为null',
            '13002009' => 'propertyValueDefinitionName不为空，但类目属性不允许自定义名称',
            '13002010' => 'skuImage不为空，但类目属性不允许自定义图片',
            '13002011' => 'propertyValueDefinitionName不为空，类目属性允许自定义名称，但自定义名称不符合规则',
            '13002012' => 'skuImage不为空，类目属性允许自定义图片，但自定义图片不符合规则',
            '13002013' => '商家编码格式不符合要求。',
            '13002014' => 'aeopAeProductSKUs对象未按照类目定义顺序。',
            '13002015' => '所有的sku数据Stock都无库存',
            '13002017' => 'aeopAeProductSKUs.size不符合sku属性要求。',
            '13002022' => 'skuPrice为空，不在1～10000000美分之间',
            '13003001' => '含有站内非本公司图片URL(即认为盗图)',
            '13003003' => 'detail内容为空，或者包含危险标签代码。',
            '13004001' => 'lotNum为空。 打包销售，lotNum<=1,非打包销售,lotNum!=1',
            '13004002' => 'packageHeight为空；不在取值范围:1-700',
            '13004003' => 'packageLength为空；不在取值范围:1-700',
            '13004004' => 'packageWidth不在取值范围:1-700',
            '13004005' => '产品包装尺寸的最大值+2×（第二大值+第三大值）不能超过2700厘米.',
            '13004006' => 'grossWeight不在取值范围:0.001-500.000',
            '13004007' => 'aeProductPropertys.attrNameId无效属性',
            '13004008' => 'sPackSell为true,addUnit为空;addUnit不在值范围1-1000',
            '13004009' => 'sPackSell为true,baseUnit为空;baseUnit不在值范围1-1000',
            '13004009' => 'sPackSell为true,addWeight为空;addWeight不在值范围0.001-500.000',
            '13004011' => '商品单位编号不正确',
            '13004013' => 'aeProductPropertys.attrNameId被判定为失效的属性ID,',
            '13004014' => '无效用户自定义属性',
            '13004015' => '存在空类目属性或重复类目属性。',
            '13004016' => '必填系统属性未填写。',
            '13004017' => '系统属性过长。	适当减少系统属性。',
            '13004018' => '自定义属性过长。	适当减少自定义属性。',
            '13005001' => '用户没有进行实名认证。',
            '13005002' => '用户没有设置收款帐号。',
            '13005003' => '用户被网规，限制操作产品。',
            '13005004' => '超过动态图片产品最大发布限制数据。',
            '13200021' => '备货期deliveryTime不能为空',
            '13200051' => '运费模版ID为空',
            '13200061' => '商品标题含有中文字符',
            '13200062' => '商品标题含有非ascii码字符;',
            '13200063' => '商品标题为空;',
            '13200064' => '商品标题长度不在1-128之间;',
            '13200071' => '搜索关键词为空;',
            '13200072' => '搜索关键词含有中文字符;',
            '13200073' => '搜索关键词含有非ascii码字符;',
            '13200074' => '搜索关键词长度不在0-50之间;',
            '13200081' => 'productMoreKeywords1含有中文字符',
            '13200082' => 'productMoreKeywords1含有非ascii码字符',
            '13200083' => 'productMoreKeywords1长度不在0-50之间',
            '13200091' => 'productMoreKeywords2含有中文字符',
            '13200092' => 'productMoreKeywords2含有中文字符',
            '13200093' => 'productMoreKeywords2长度不在0-50之间',
            '13200111' => '简要描述含有中文字符',
            '13200112' => '简要描述含有非ascii码字符',
            '13200113' => '简要描述含有email或者http网址',
            '13200114' => '简要描述长度不在0-128之间',
            '13201001' => 'detail内容为空',
            '13200001' => 'detail内容为空',
            '13201002' => 'detail含有中文字符',
            '13201003' => '含有email信息、或者图片img src非alibaba.com或者aliimg.com',
            '13201011' => '商品类目属性为空',
            '13201021' => 'aeProductPropertys.attrName含有中文字符;',
            '13201022' => 'aeProductPropertys.attrName含有非ascii码字符',
            '13201023' => 'aeProductPropertys.attrName长度不在0-128之间',
            '13201031' => 'aeProductPropertys.attrValue含有中文字符',
            '13201032' => 'aeProductPropertys.attrValue含有非ascii码字符',
            '13201033' => 'aeProductPropertys.attrValue长度不在0-128之间',
            '13202001' => '商品单位为空',
            '13202021' => 'grossWeight为空；',
            '13205001' => 'wsValidNum为空',
            '13205002' => 'wsValidNum不在取值范围:1-30',
            '13null'   => '拷贝主图或者详情中的图片失败。现在暂时为这个错误码，我们会在下一版中给出具体的错误码。'
        );
        return $errorMsg;
    }
}

/* End of file Smt_product_list_model.php */
/* Location: ./defaute/models/smt/Smt_product_list_model.php */