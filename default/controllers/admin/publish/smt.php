<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 速卖通广告操作及刊登功能
 * @authors suwei
 * @date    2014-12-03
 * @version $Id$
 */
header("Content-type:text/html;charset=utf-8");
class Smt extends Admin_Controller
{
    private $price_status = array('1'=>'未调价','2'=>'已调价','3'=>'异常');

    private $price_type = array('1'=>'上调','2'=>'下调');//调价类型

    function __construct()
    {
        parent::__construct();
        $this->load->library('MySmt');
        $this->load->library('phpexcel/PHPExcel');
        $this->load->Model(array(
            'smt/Smt_user_tokens_model',
            'smt/Smt_product_list_model',
            'smt/Smt_product_skus_model',
            'smt/Slme_smt_freight_template_model',
            'smt/Slme_smt_service_template_model',
            'smt/Slme_smt_product_group_model',
            'smt/Smt_user_sale_code_model',
            'smt/Smt_product_detail_model',
            'sharepage',
            'smt/Slme_smt_categorylist_model',
            'smt/Slme_smt_category_attribute_model',
            'smt/Slme_smt_unit_model',
            'smt/Smt_auto_draft_list_model',
            'smt_price_task_model',
            'smt_price_task_main_model',
            'Shipment_model',
            'products/Products_data_model',
            'copyright_model'
            //'smt/Slme_smt_product_list_draft_model',
            //'smt/Slme_smt_product_skus_draft_model',
            //'smt/Slme_smt_product_detail_draft_model'
        ));
        $this->smt          = new MySmt();
        $this->userToken    = $this->Smt_user_tokens_model;
        $this->smt_category = $this->Slme_smt_categorylist_model;
    }

    /**
     * 需要刊登的产品列表--即草稿列表
     * @return [type] [description]
     */
    public function index()
    {
        $this->draftSearch();
    }
    //测试
    public function wwtest(){
        echo '333'."<br/>";
        var_dump($_POST);exit;
    }
    public function imgWater(){
        $imgurl = $this->input->get_post('imgurl');//生成的图片链接
        $data=array(
                'imgurl'=>$imgurl
            );
        $this->only_template('admin/publish/smt/imgwater', $data);
    }
    /**
     * 待发布产品列表
     */
    public function waitPost(){
        $this->draftSearch('waitPost');
    }

    /**
     * 速卖通产品搜索列表
     * @param string $productStatusType
     */
    protected function draftSearch($productStatusType='newData'){
        //速卖通账号列表查询条件
        $smt_user_options = array(
            'select' => 'token_id, seller_account,accountSuffix',
            'where'  => array('token_status' => 0),
        );
        //速卖通账号列表
        $token_array = $this->userToken->getSmtTokenList($smt_user_options);


        $where    = array(); //查询条件
        $where_in = array(); //IN条件查询
        $like     = array(); //like条件
        $string   = array(); //URL参数
        $cupage   = (int)$this->config->item('site_page_num');
        $per_page = (int)$this->input->get_post('per_page');
        $token_id = $this->input->get_post('token_id');
        $sku      = trim($this->input->get_post('sku')); //SKU查询
        $subject  = trim($this->input->get_post('subject'));
        $productId= trim($this->input->get_post('productId'));
        $groupId= trim($this->input->get_post('groupId'));

        $group_list=array();

        //组装查询条件
        if ($token_id) {
            $where['token_id'] = $token_id;
            $string[]          = 'token_id=' . $token_id;
            $group_list = $this->Slme_smt_product_group_model->getProductGroupList($token_id);

        }
        //SKU数据存在
        if ($sku) {
            //查询出SKU对应的ID
            $product_ids = $this->Smt_product_skus_model->getProductIdWithSku($sku);
            if ($product_ids) { //有这个SKU
                $where_in['productId'] = $product_ids;
            } else {
                $where_in['productId'] = '';
            }
            $string[] = 'sku=' . $sku;
        }

        //标题模糊查询
        if (!empty($subject)){
            $like['subject'] = $subject;
            $string[]        = 'subject=' . $subject;
        }

        //productId模糊查询
        if (!empty($productId)){
            $like['productId'] = $productId;
            $string[]          = 'productId=' . $productId;
        }
        if(!empty($groupId)){
            $where['groupId'] = $groupId;
            $string[]          = 'groupId=' . $groupId;
        }

        $where['productStatusType'] = $productStatusType;


        //查询条件
        $options     = array(
            'where'    => $where,
            'where_in' => $where_in,
            'page'     => $cupage,
            'per_page' => $per_page,
        );

        if (!empty($like)){ //有模糊查询的就模糊查询吧
            $options = array_merge($options, array('like' => $like));
        }
        $return_data = array('total_rows' => true);
        $data_list   = $this->Smt_product_list_model->getAll($options, $return_data);

        //查询SKU列表信息
        $ids_array = array();
        foreach ($data_list as $item) {
            $ids_array[] = $item->productId;
        }
        $draft_skus = $this->Smt_product_skus_model->getProductSkus($ids_array);
        $detail_list = $this->Smt_product_detail_model->getProductDetailsFields($ids_array, 'productId,imageURLs,keyword,productMoreKeywords1,productMoreKeywords2');

        $action = $productStatusType == 'waitPost' ? 'waitPost' : 'index';
        $c_url = admin_base_url('publish/smt/'.$action);
        $url   = $c_url . '?' . implode('&', $string);

        $page = $this->sharepage->showPage($url, $return_data['total_rows'], $cupage);

        $statusTypeList = $this->defineDraftStatus();
        $data = array(
            'token'      => $token_array,
            'token_id'   => $token_id,
            'sku'        => $sku,
            'productId'  => $productId,
            'subject'    => $subject,
            'groupId'   =>$groupId,
            'group_list' =>$group_list,
            'data_list'  => $data_list,
            'page'       => $page,
            'totals'     => $return_data['total_rows'],
            'draft_skus' => $draft_skus,
            'detail_list' => $detail_list,
            'productStatusType' => $productStatusType,
            'statusTypeList' => $statusTypeList
        );

        $this->_template('admin/publish/smt/draft_list', $data);
    }
    

    /**
     * 定义产品状态
     * @return array
     */
    public function defineDraftStatus(){
        return array(
            'newData' => '草稿',
            'waitPost' => '待发布',
            'failure' => '发布失败'
        );
    }

    /**
     * 广告新增页面添加页面
     */
    public function add()
    {
        //分类ID
        $category_id = $this->input->get_post('category_id');

        //账号ID
        $token_id = $this->input->get_post('token_id');

        if ($category_id) {
            //已选择的分类
            $category_info = array('id' => $category_id, 'name' => $this->smt_category->getCateroryAndParentName($category_id));

            //查询选择的分类的属性
            $attributes = $this->Slme_smt_category_attribute_model->getCategoryAttributes($category_id);

            $category_attributes = array();
            if (!$attributes){ //属性直接不存在
                $return = $this->Slme_smt_category_attribute_model->getChildAttributesResultByPostCateIdAndPath($token_id, $category_id);
                if ($return)
                    $category_attributes = $return;
            }else { //属性存在但不是最新的
                $category_attributes = unserialize($attributes->attribute);
                //这个属性今天还没更新呢，更新下吧
                if (!$attributes->last_update_time || date('Y-m-d') != date('Y-m-d', strtotime($attributes->last_update_time))) {
                    $return = $this->Slme_smt_category_attribute_model->getChildAttributesResultByPostCateIdAndPath($token_id, $category_id);
                    if ($return)
                        $category_attributes = $return;
                }
            }

            //对属性进行排序处理
            $category_attributes = sortAttribute($category_attributes);

            //运费模板
            $freight = $this->Slme_smt_freight_template_model->getFreightTemplateList($token_id);

            //服务模板
            $service = $this->Slme_smt_service_template_model->getServiceTemplateList($token_id);

            //产品分组
            $group = $this->Slme_smt_product_group_model->getProductGroupList($token_id);

            //单位列表
            $unit = $this->Slme_smt_unit_model->getUnitList();

            //产品模板
            $this->load->model('smt/Slme_smt_product_module_model');
            $module = $this->Slme_smt_product_module_model->getModuleList($token_id);

            //速脉通模板及售后模板
            $plat = 6;
            $this->load->model(array('Slme_smt_template_model', 'Slme_after_sales_service_model'));
            $template_list = $this->Slme_smt_template_model->getTemplateList(array('select' => 'id, name', 'plat' => $plat));
            $shouhou_list  = $this->Slme_after_sales_service_model->getTemplateList(array('select' => 'id, name', 'plat' => $plat, 'token_id' => $token_id));


            $data = array(
                'category_info'   => $category_info,
                'attributes'      => $category_attributes,
                'freight'         => $freight,
                'service'         => $service,
                'group'           => $group,
                'unit'            => $unit,
                'module'          => $module,
                'token_id'        => $token_id,
                'productTemplate' => array(),
                'action'          => 'add',
                'draft_info'      => array(),
                'draft_skus'      => array(),
                'draft_detail'    => array(),
                'template_list'   => $template_list,
                'shouhou_list'    => $shouhou_list,
            	'api'             => 'post'
            );
            $this->_template('admin/publish/smt/add', $data);
        } else {
            //smt账号列表
            $token_options = array('where' => array('token_status' => 0));
            $token_list    = $this->userToken->getSmtTokenList($token_options);

            $category_list = $this->smt_category->getCategoryListByPid();
            $data          = array(
                'data_list'  => $category_list,
                'token_list' => $token_list,
                'token_id'   => $token_id
            );
            $this->_template('admin/publish/smt/category_choose', $data);
        }
    }

    /**
     * post提交的操作处理
     */
    public function doAction()
    {
        //判断操作类型
        $action = $this->input->post('action');
// echo $action;exit();
        if ($action == 'save') { //保存

            $this->save($action);
        } elseif ($action == 'post') { //发布
        	
            $this->post();
        } elseif ($action == 'saveToPost') { //保存为待发布

            $this->save($action);
        }elseif ($action == 'editAndPost'){ //编辑并发布
        	
        	$this->editAndPost(); //修改在线广告
        } else { //都不是以上操作
            ajax_return('非法操作', 'false');
        }
    }

    /**
     * 保存 及 保存为待发布
     * @param string $action:操作选择
     * @param bool $exit:是否退出执行,即exit
     * @return array
     */
    public function save($action='save', $exit=true)
    {
        header('Content-Type: text/html; Charset=utf-8');
        //提及数据
        $posts = $this->input->post();

        //根据ID来判断是add的还是update

        //草稿主表数据信息
        $draft_product['token_id']      = $posts['token_id'];
        $draft_product['subject']       = trim($posts['subject']);
        $draft_product['groupId']       = $posts['groupId'];
        $draft_product['categoryId']    = $posts['categoryId'];
        $draft_product['packageLength'] = $posts['packageLength'];
        $draft_product['packageWidth']  = $posts['packageWidth'];
        $draft_product['packageHeight'] = $posts['packageHeight'];
        $draft_product['grossWeight']   = $posts['grossWeight'];
        $draft_product['deliveryTime']  = $posts['deliveryTime'];
        $draft_product['wsValidNum']    = $posts['wsValidNum'];

        if ($action == 'saveToPost') { //保存为待发布
        	$draft_product['productStatusType'] = 'waitPost';
        }
        /*******************产品属性封装开始***************************/
        $aeopAeProductPropertys = array();
        //select和checkbox类型
        $sysAttrValueIdAndValue = array_key_exists('sysAttrValueIdAndValue', $posts) ? $posts['sysAttrValueIdAndValue'] : array();
        $otherAttributeTxt      = array_key_exists('otherAttributeTxt', $posts) ? $posts['otherAttributeTxt'] : array();
        if ($sysAttrValueIdAndValue){
            foreach ($sysAttrValueIdAndValue as $attrId => $value) {
                if (!empty($value)) {
                    if (is_array($value)){
                        foreach ($value as $v){ //checkbox类型的会写成数组
                            list($attrValueId,) = explode('-', $v);
                            $aeopAeProductPropertys[] = array(
                                'attrNameId'  => $attrId,
                                'attrValueId' => $attrValueId
                            );
                        }
                    }else {
                        list($attrValueId,) = explode('-', $value);
                        $aeopAeProductPropertys[] = array(
                            'attrNameId'  => $attrId,
                            'attrValueId' => $attrValueId
                        );
                    }
                    if ($otherAttributeTxt && array_key_exists($attrId, $otherAttributeTxt)) { //其它属性
                        $aeopAeProductPropertys[] = array(
                            'attrNameId' => $attrId,
                            'attrValue'  => $otherAttributeTxt[$attrId]
                        );
                    }
                }
            }
        }

        //input类型的
        $sysAttrIdAndValueName = array_key_exists('sysAttrIdAndValueName', $posts) ? $posts['sysAttrIdAndValueName'] : array();
        //input类型的要考虑选择的单位
        $sysAttrIdAndUnit = array_key_exists('sysAttrIdAndUnit', $posts) ? $posts['sysAttrIdAndUnit'] : array();
        if ($sysAttrIdAndValueName){
            foreach ($sysAttrIdAndValueName as $attrId => $value) {
                if (trim($value)) {
                    //有输入值，并且有单位，把单位组合进去吧
                    $value = !empty($sysAttrIdAndUnit[$attrId]) ? trim($value).' '.$sysAttrIdAndUnit[$attrId] : trim($value);
                    $aeopAeProductPropertys[] = array(
                        'attrNameId' => $attrId,
                        'attrValue'  => $value,
                    );
                }
            }
        }


        //自定义属性
        $custom = array_key_exists('custom', $posts) ? $posts['custom'] : array();
        if ($custom){
            foreach ($custom['attrName'] as $k => $attrName) {
                $aeopAeProductPropertys[] = array(
                    'attrName'  => $attrName,
                    'attrValue' => $custom['attrValue'][$k]
                );
            }
        }

        /***********************产品属性封装结束**********************/

        //详情表数据
        $draft_detail['aeopAeProductPropertys'] = serialize($aeopAeProductPropertys);
        
        //图片
        $imageURLs = array_key_exists('imgLists', $posts) ? $posts['imgLists'] : array();
        if (!$imageURLs){
        	ajax_return('保存失败，主图信息不存在,请先上传', false);
        }
        if (count($imageURLs) > 6){
        	ajax_return('主图不能超过6张', false);
        }
        $draft_detail['imageURLs']              = implode(';', $imageURLs);   //图片
        $draft_detail['isImageDynamic']         = count($imageURLs) > 1 ? 1 : 0; //是否动态图

        /**自定义产品关联信息开始**/
        //自定义关联产品
        $relationProductArr = array_key_exists('relationProduct', $posts) ? $posts['relationProduct'] : array();
        //$relationStr        = '';
        $relationProductIds = '';
        if ($relationProductArr) {
            $relationProductIds = implode(';', $relationProductArr);
            //$relationStr = $this->createRelationTemplate($relationProductIds);
        }
        //关联产品ID字符串
        $draft_detail['relationProductIds'] = $relationProductIds;

        //关联产品的位置
        //$relationLocation = $posts['relation_loction'];
        $draft_detail['relationLocation'] = $posts['relation_loction'];

        //详情信息
        $detail_str = trim($posts['detail']);



        //if ($relationLocation == 'header') {
        //    $detail_str = $relationStr . $detail_str;
        //} elseif ($relationLocation == 'footer') {
        //    $detail_str .= $relationStr;
        //}
        /**自定义产品关联信息结束**/

        $draft_detail['detail']                 = htmlspecialchars($detail_str); //详情
        $draft_detail['detailLocal']            = $draft_detail['detail'];
        $draft_detail['keyword']                = filterForSmtProduct($posts['keyword']);      //关键字
        $draft_detail['productMoreKeywords1']   = filterForSmtProduct($posts['productMoreKeywords1']);
        $draft_detail['productMoreKeywords2']   = filterForSmtProduct($posts['productMoreKeywords2']);
        
        if ((strlen($draft_detail['keyword']) + strlen($draft_detail['productMoreKeywords1']) + strlen($draft_detail['productMoreKeywords2'])) > 255){
        	ajax_return('三个关键字加起来长度不能超过255个字符', false);
        }
        
        $draft_detail['productUnit']            = $posts['productUnit'];
        $draft_detail['freightTemplateId']      = $posts['freightTemplateId'];
        $draft_detail['isImageWatermark']       = 0;
        $draft_detail['packageType']            = array_key_exists('packageType', $posts) ? 1 : 0; //是否打包
        $draft_detail['lotNum']                 = $draft_detail['packageType'] ? $posts['lotNum'] : 1; //打包数量
        if (array_key_exists('isPackSell', $posts)) {
            $draft_detail['isPackSell'] = $posts['isPackSell']; //自定义记重
        }
        //批发订单
        if (array_key_exists('wholesale', $posts)) {
            $draft_detail['bulkOrder']    = $posts['bulkOrder'];
            $draft_detail['bulkDiscount'] = $posts['bulkDiscount'];
        }else { //没提交的话，直接赋值为0，不然的话还是会提交的
            $draft_detail['bulkOrder']    = 0;
            $draft_detail['bulkDiscount'] = 0;
        }
        $draft_detail['promiseTemplateId'] = $posts['promiseTemplateId']; //服务模板

        //自定义刊登模板详情
        $draft_detail['templateId']   = $posts['templateId'];
        $draft_detail['shouhouId']    = $posts['shouhouId'];
        $draft_detail['detail_title'] = htmlspecialchars(trim($posts['detail_title']));
        
        if (array_key_exists('detailPicList', $posts)){ //有描述图片详情
        	$draft_detail['detailPicList'] = implode(';', $posts['detailPicList']);
        }


        /***************多属性SKU组装信息--组装开始*******************/
        $aeopAeProductSKUs = array(); //需要组装下SKU信息
        $skuPrice          = array_key_exists('skuPrice', $posts) ? $posts['skuPrice'] : array();
        $ipmSkuStock       = array_key_exists('skuStock', $posts) ? $posts['skuStock'] : array();
        $skuCode           = array_key_exists('skuCode', $posts) ? $posts['skuCode'] : array();

        //自定义SKU属性名称
        $customizedName = array_key_exists('customizedName', $posts) ? $posts['customizedName'] : array();
        //自定义SKU图片信息
        $customizedPic = array_key_exists('customizedPic', $posts) ? $posts['customizedPic'] : array();

        //最小价格
        $productMinPrice = 0;
        //最大价格
        $productMaxPrice = 0;

        if ($skuPrice && $ipmSkuStock) { //单价和库存都存在，应该会存在SKU来着
            foreach ($skuPrice as $key => $price) {
                if (!trim($skuCode[$key])) { //sku不存在的话直接pass掉
                    continue;
                }

                $attList         = explode('-', $key);
                $aeopSKUProperty = array();
                foreach ($attList as $at) { //处理下属性，找下自定义的属性信息
                    list($skuPropertyId, $propertyValueId) = explode('_', $at);
                    if ($propertyValueId) { //有属性值才行，没属性值不管
                        $array = array(
                            'skuPropertyId'   => $skuPropertyId,
                            'propertyValueId' => $propertyValueId,
                        );
                        if (array_key_exists($at, $customizedName) && $customizedName[$at]) { //有自定义属性
                            $array = array_merge($array, array('propertyValueDefinitionName' => $customizedName[$at]));
                        }
                        if (array_key_exists($at, $customizedPic) && $customizedPic[$at]) { //有自定义图片
                            $array = array_merge($array, array('skuImage' => $customizedPic[$at]));
                        }
                        $aeopSKUProperty[] = $array;
                        unset($array);
                    }
                }
                $aeopAeProductSKUs[] = array(
                    'skuPrice'        => $price,
                    'skuCode'         => $skuCode[$key],
                    'ipmSkuStock'     => $ipmSkuStock[$key],
                    'aeopSKUProperty' => $aeopSKUProperty
                );
                //最小单价
                $productMinPrice = $productMinPrice == 0 ? $price : ($productMinPrice < $price ? $price : $productMinPrice);
                //最大单价
                $productMaxPrice = $productMaxPrice > $price ? $productMaxPrice : $price;
            }

        } else {
            //单属性产品组装--和多属性互斥
            $productPrice        = $posts['productPrice'];
            $productStock        = $posts['productStock'];
            $productCode         = $posts['productCode'];
            $aeopAeProductSKUs[] = array(
                'skuPrice'        => $productPrice,
                'skuCode'         => trim($productCode),
                'ipmSkuStock'     => $productStock,
                'aeopSKUProperty' => array(), //这个字段必需
            );
            //最小单价
            $productMinPrice = $productPrice;
            $productMaxPrice = $productPrice; ///最大单价
        }
        //单价或者一口价
        $draft_product['productPrice'] = $aeopAeProductSKUs[0]['skuPrice'];
        $draft_product['productMinPrice'] = $productMinPrice;
        $draft_product['productMaxPrice'] = $productMaxPrice;
        
        //这个账号的token信息
        $token_id = $posts['token_id'];
        $token_info = $this->userToken->getOneTokenInfo($token_id);

        /****************多属性SKU组装信息--组装结束*******************/
        $result_flag = true;
        $info        = '';

            //用户对应的老用户表ID
            $manage_id = $this->user_info->old_id;
            $code = ''; //账号前缀
            if ($manage_id){
                $saleCode = $this->Smt_user_sale_code_model->getOne(array(
                    'select' => 'sale_code',
                    'where' => array(
                        'user_id' => $manage_id
                    )
                ), true);
                $code = $saleCode['sale_code'] ? $saleCode['sale_code'] : '';
            
        }
        
        if ($posts['id']) { //草稿ID存在，还是直接更新

            $this->db->trans_begin();
            $this->Smt_product_list_model->update($draft_product, array('where' => array('productId' => $posts['id'])));
            if ($this->db->_error_message()){
                $this->db->trans_rollback();
                $result_flag = false;
                $info        = '保存产品列表信息出错';
            }

            if ($result_flag) {
                $this->Smt_product_detail_model->update($draft_detail, array('where' => array('productId' => $posts['id'])));
                if ($this->db->_error_message()){
                    $this->db->trans_rollback();
                    $result_flag = false;
                    $info        = '保存产品详情信息出错';
                }
            }

            /*********************账号产品列表处理开始*************************/
            if ($result_flag) {
                //存在就更新，不存在就insert
                $exist_skuList  = $this->Smt_product_skus_model->getProductSkuInfoList($posts['id'], 'skuMark');
                $exist_skuLists = array();
                if ($exist_skuList) {
                    foreach ($exist_skuList as $row) {
                        $exist_skuLists[] = $row['skuMark'];
                    }
                }

                foreach ($aeopAeProductSKUs as $per_sku) {

                    $valId                      = checkProductSkuAttrIsOverSea($per_sku['aeopSKUProperty']); //海外仓属性ID
                    $per_sku['aeopSKUProperty'] = $per_sku['aeopSKUProperty'] ? serialize($per_sku['aeopSKUProperty']) : '';
                    $per_sku['skuStock']        = $per_sku['ipmSkuStock'] > 0 ? 1 : 0;
                    $per_sku['smtSkuCode']      = ($code ? $code . '*' : '') .(($valId > 0 && $valId != 201336100) ? '{YY}' : ''). $per_sku['skuCode'] . ($token_info['accountSuffix'] ? '#' . $token_info['accountSuffix'] : '');
                    $per_sku['updated']         = 1; //这些都是修改过的
                    $per_sku['isRemove']        = 0; //未被删除的
                    $per_sku['overSeaValId']    = $valId;

                    $newSkus = buildSysSku($per_sku['skuCode']);

                    $withErr = false; //循环中是否出错
                    foreach ($newSkus as $sku){

                        $per_sku['skuCode'] = (($valId > 0 && $valId != 201336100) ? '{YY}' : '').$sku;
                        $isSkuExists = $this->Smt_product_skus_model->checkProductAndSmtSkuCodeIsExists($posts['id'], $per_sku['smtSkuCode'], $per_sku['skuCode'], $valId);
                        if ($isSkuExists) {//更新
                            $where = array();
                            $where['productId']    = $posts['id'];
                            $where['smtSkuCode']   = $per_sku['smtSkuCode'];
                            $where['skuCode']      = $per_sku['skuCode'];
                            $where['overSeaValId'] = $valId;
                            $option = array(
                                'where' => $where
                            );
                            $this->Smt_product_skus_model->update($per_sku, $option);

                            if ($this->db->_error_message()){
                                $this->db->trans_rollback();
                                $result_flag = false;
                                $info        = '保存产品SKU信息出错';
                                $withErr = true;
                                break;
                            }
                        } else { //增加
                            $per_sku['productId'] = $posts['id'];
                            $per_sku['skuMark']   = $posts['id'] . ':' . $per_sku['skuCode'];
                            $this->Smt_product_skus_model->add($per_sku);
                            if ($this->db->_error_message()){
                                $this->db->trans_rollback();
                                $result_flag = false;
                                $info        = '保存产品SKU信息出错';
                                $withErr = true;
                                break;
                            }
                        }
                    }
                    unset($newSkus);
                    if ($withErr) break;
                }

                //没有变更的数据直接删除吧，说明已经变了，只使用最新的就好了
                $this->Smt_product_skus_model->delete(array('where' => array('productId' => $posts['id'], 'updated' => 0)));
                if ($this->db->_error_message()){
                    $this->db->trans_rollback();
                    $result_flag = false;
                    $info        = '保存产品SKU信息出错D';
                }

                //把修改的状态变更回来
                $newData            = array();
                $newData['updated'] = 0;
                $this->Smt_product_skus_model->update($newData, array('where' => array('productId' => $posts['id'])));
                if ($this->db->_error_message()){
                    $this->db->trans_rollback();
                    $result_flag = false;
                    $info        = '保存产品SKU信息出错D';
                }
            }
            if ($result_flag){
                $this->db->trans_commit();
            }

            if ($exit){
            	ajax_return('保存'.($result_flag ? '成功' : '失败').$info, $result_flag);
            }else {
            	return array('status' => $result_flag, 'info' => '保存'.($result_flag ? '成功' : '失败').$info, 'id' => $posts['id']);
            }
            
            /*********************账号产品列表处理结束*************************/
            
        } else { //add
            $this->db->trans_begin();
			$productId = date('ymdHis').rand(1000, 9999).'-'.$posts['token_id']; //临时的产品ID
			
			$draft_product['productId'] = $productId;
			//$draft_product['user_id'] = $token_info['customerservice_id'];
			//状态
			$draft_product['productStatusType'] = ($action == 'saveToPost') ? 'waitPost' : 'newData';
			
            //返回的ID，主表ID
            $main_id     = $this->Smt_product_list_model->add($draft_product);
            
            if (!$main_id){
            	$this->db->trans_rollback();
            	ajax_return('保存到产品列表出错', false);
            }

            //保存到详情表中
            $draft_detail['productId'] = $productId;
            $detail_id                 = $this->Smt_product_detail_model->add($draft_detail);
            if (!$detail_id){
            	$this->db->trans_rollback();
            	ajax_return('保存到产品详情表出错', false);
            }

            $sku_flag = true;
            $withErr = false;
            //保存到SKU明细表中
            foreach ($aeopAeProductSKUs as $per_sku) {
                $valId                      = checkProductSkuAttrIsOverSea($per_sku['aeopSKUProperty']); //发货地属性ID 值ID：201336100为中国
                $per_sku['overSeaValId']    = $valId;
                $per_sku['aeopSKUProperty'] = $per_sku['aeopSKUProperty'] ? serialize($per_sku['aeopSKUProperty']) : '';
                $per_sku['productId']       = $productId;
                $per_sku['skuStock']        = $per_sku['ipmSkuStock'] > 0 ? 1 : 0;
                $per_sku['smtSkuCode']      = ($code ? $code . '*' : '') .(($valId > 0 && $valId != 201336100) ? '{YY}' : '').$per_sku['skuCode'] . ($token_info['accountSuffix'] ? '#' . $token_info['accountSuffix'] : '');

                $newSkus = buildSysSku($per_sku['skuCode']);
                foreach($newSkus as $sku){
                    $per_sku['skuCode'] = (($valId > 0 && $valId != 201336100) ? '{YY}' : '').$sku;
                    $per_sku['skuMark'] = $productId . ':' . $per_sku['skuCode']; //这个还是要要的
                    $sku_id = $this->Smt_product_skus_model->add($per_sku);
                    if (!$sku_id){
                        $sku_flag = false;
                        $withErr = true;
                        break;
                    }
                }
                if ($withErr) break;
            }

            if ($sku_flag) { //都插入成功了才进行提交
                $this->db->trans_commit();
                if ($exit){
                	ajax_return('保存成功', true, array('id' => $productId)); //这个productId还是返回吧
                }else {
                	return array('status' => true, 'info' => '保存成功', 'id' => $productId);
                }
            } else {
                $this->db->trans_rollback();
                ajax_return('保存到SKU列表出错', false);
            }
        }
    }

    /**
     * 直接发布，但同时也要先保存下来
     */
    public function post(){

        $return = $this->save('saveToPost', false);
        
        if ($return && $return['status']){
        	
            //主表ID
            $main_id = $return['id'];
            //发布
            $this->postAeProduct($main_id, true);
        }else {
        	ajax_return('保存失败,未上传', false);
        }
    }

    /**
     * 编辑并上传
     */
    public function editAndPost(){
    	$return = $this->save('save', false);
    	
    	if ($return && $return['status']){
    		
    		$productId = $return['id'];
    		
    		$this->postAeProduct($productId, false);
    	}else {
    		ajax_return('编辑时保存失败', false);
    	}
    }
    public function copytest(){
        $where = array('no_is_del'=>1);
        $option = array(
                'where'=>$where
            );
        $copyright= $this->copyright_model->getAll2Array($option);
        $copyright[] = array('trademark'=>'qwq');
        $id='32227587636-83-19839';
        $draft_info = $this->Smt_product_list_model->getProductListInfo($id);
        $draft_detail = $this->Smt_product_detail_model->getProductDetailInfo($id);
        //价格属性
        $where = array('productId'=>$id);
        $options = array(
        		'where'=>$where
        	);
        $proskudata = $this->Smt_product_skus_model->getAll2Array($options);
        $skudatastr =array();
        foreach($proskudata as $v){
        	$skus =unserialize($v['aeopSKUProperty']);
        	$skudatastr[] = $skus[0]['propertyValueDefinitionName'];
        }
        $skudatastr = implode( $skudatastr,',');
       /////////////
        $copyworld =$productProperties= array();
        $productpropertys=unserialize($draft_info);

            foreach($productpropertys as $pro)
            {
                foreach($pro as $v)
                {   
                    if(is_string($v['attrValue']))
                    {  
                        $productProperties[]=$v;

                    }
                }
            }
        myecho($skudatastr);
        $copyworld[]=$draft_info['subject'].'qwqwq qwq jjlk';
        //$copyworld[]=$draft_detail['detail'];
        echo "<pre/>" ;var_dump(unserialize($draft_info['subject']));
        //var_dump($draft_info['subject']);
        foreach($copyright as $v){
            $world = trim($v['trademark']);
            $reg = "/\b".$world."\b/i";
            if(is_array($copyworld)){
                foreach($copyworld as $vx){
                    if(preg_match($reg,$vx)){
                       echo '('.$v['trademark'].')';exit;
                   }
               }
            }
        }
        echo 'success';exit;
    }
    public function copycheck($copyworld){
        $where = array('no_is_del'=>1);
        $option = array(
                'where'=>$where
            );
        $copyright= $this->copyright_model->getAll2Array($option);
        foreach($copyright as $v){
            $world = trim($v['trademark']);
            $reg = "/\b".$world."\b/i";
            if(is_array($copyworld)){
                foreach($copyworld as $vx){
                    if(preg_match($reg,$vx)){

                       return '('.$v['trademark'].')';
                   }
               }
            }
        }
        return 'success';
    }
    /**
     * 发布产品
     * @return array
     * @param $id:产品ID
     * @param bool $isAdd:是否新添加
     * @param bool $auto:为false的时候，不管草稿的状态;只有待发布的才自动发布
     * @return array
     */
    public function postAeProduct($id, $isAdd = true, $auto = false)
    {
        if ($id) {
            $product = array();
            //读取待发布产品列表信息
            $draft_info = $this->Smt_product_list_model->getProductListInfo($id);
            if (!$draft_info) {
                if ($auto){
                    return array('status' => false, 'info' => '产品:' . $id . '产品信息获取失败');
                }else {
                    return json_encode(array('status' => false, 'info' => '产品:' . $id . '产品信息获取失败'));
                }
            }

         /*   if ($auto) { //自动发布
                if (!in_array($draft_info['productStatusType'], array('waitPost'))) {
                    return array('status' => false, 'info' => '产品:' . $id . '状态错误，只有待发布的才可进行此操作');
                }
            }*/

            //读取待发布产品详情信息
            $draft_detail = $this->Smt_product_detail_model->getProductDetailInfo($id);
            if (!$draft_detail) {
                if ($auto) {
                    return array('status' => false, 'info' => '产品:' . $id . '详情信息获取失败');
                }else {
                    return json_encode(array('status' => false, 'info' => '产品:' . $id . '详情信息获取失败'));
                }
            }

            //读取待发布产品SKU信息
            $draft_skus = $this->Smt_product_skus_model->getProductSkuProperty($id);
            if (!$draft_skus) {
                if ($auto) {
                    return array('status' => false, 'info' => '产品:' . $id . 'SKU信息获取失败');
                }else {
                    return json_encode(array('status' => false, 'info' => '产品:' . $id . 'SKU信息获取失败'));
                }
            }

            $firstSku = rebuildSmtSku($draft_skus[0]['smtSkuCode']); //简单解析下第一个SKU

            //账号ID
            $token_id = $draft_info['token_id'];
            if (!$token_id) {
                if ($auto) {
                    return array('status' => false, 'info' => '产品:' . $id . '刊登账号不存在');
                }else {
                    return json_encode(array('status' => false, 'info' => '产品:' . $id . '刊登账号不存在'));
                }
            }

	        //价格属性 add by lidabiao 
	        $where = array('productId'=>$id);
	        $options = array(
	        		'where'=>$where
	        	);
	        $proskudata = $this->Smt_product_skus_model->getAll2Array($options);
	        $skudatastr =array();
	        foreach($proskudata as $v){
	        	$skus =unserialize($v['aeopSKUProperty']);
	        	$skudatastr[] = $skus[0]['propertyValueDefinitionName'];
	        }
	        $skudatastr = implode( $skudatastr,',');//价格属性字符串


            $checkeinfo['token_id'] = $token_id;   //
            $checkeinfo['categoryId']=$draft_info['categoryId']; //获取分类ID
            $checkeinfo['subject']=$draft_info['subject'];  //获取标题
            $checkeinfo['aeopAeProductPropertys']=$draft_detail['aeopAeProductPropertys']; //获取属性
            $checkeinfo['keyword']=$draft_detail['keyword']; //获取关键字1
            $checkeinfo['productMoreKeywords1']=$draft_detail['productMoreKeywords1']; //获取关键字2
            $checkeinfo['productMoreKeywords2']=$draft_detail['productMoreKeywords2']; //获取关键字3
            $checkeinfo['detail']=$draft_detail['detail'];//获取详情
            $re = $this->findAeProductProhibitedWords($checkeinfo);   //把验证是不是侵权的放在这里吧

            if($re!='success')
            {

                $re =   str_replace("FORBIDEN_TYPE", "禁用", $re);
                $re =   str_replace("RESTRICT_TYPE", "限定", $re);
                $re =   str_replace("BRAND_TYPE", "品牌", $re);
                $re =   str_replace("TORT_TYPE", "侵权", $re);

                $re =   str_replace("titleProhibitedWords", "商品的标题", $re);
                $re =   str_replace("keywordsProhibitedWords", "商品的关键字列表", $re);
                $re =   str_replace("productPropertiesProhibitedWords", "商品的属性", $re);
                $re =   str_replace("detailProhibitedWords", "商品的详细描述", $re);

                if ($auto) {

                    return array('status' => false, 'info' => '产品:' . $id . $re);
                }else {

                    return ajax_return( '产品:' . $id.$re,false);
                }
            }

            //对价格属性进行检测/////////add by lidabiao////////////////////////////
            $checkeinfoss = $checkeinfo;
            $checkeinfoss['detail'] = $skudatastr;
            $re = $this->findAeProductProhibitedWords($checkeinfoss);
            if($re!='success')
            {

                $re =   str_replace("FORBIDEN_TYPE", "禁用", $re);
                $re =   str_replace("RESTRICT_TYPE", "限定", $re);
                $re =   str_replace("BRAND_TYPE", "品牌", $re);
                $re =   str_replace("TORT_TYPE", "侵权", $re);

                $re =   str_replace("titleProhibitedWords", "商品的标题", $re);
                $re =   str_replace("keywordsProhibitedWords", "商品的关键字列表", $re);
                $re =   str_replace("productPropertiesProhibitedWords", "商品的属性", $re);
                $re =   str_replace("detailProhibitedWords", "商品的价格属性", $re);

                if ($auto) {

                    return array('status' => false, 'info' => '产品:' . $id . $re);
                }else {

                    return ajax_return( '产品:' . $id.$re,false);
                }
            }            
            
            

            //////////////////////////ERP违禁商标名检测///////add by lidabiao 2016-4-29///////////////////////
            $copyworld = array();
            $copyworld[] = $draft_info['subject'];  //获取标题
            $copyworld[] = $draft_detail['detail'];//获取详情
            $copyworld[] = $draft_detail['aeopAeProductPropertys']; //获取属性
            $copyworld[] = $skudatastr;//价格属性
            $checkres = $this->copycheck($copyworld);
            $copysure = $this->input->get_post('copysure');//是否忽略ERP违禁商标名检测
            if($checkres != 'success'){
                $resmsg = '的数据含有违禁的商标名'.$checkres;
                if($copysure != 'yes'){
                    //检查结果含有ERP违禁商标名,但是操作者选择不忽略，禁止发布提示发布失败
                    if ($auto) {
                        return array('status' => 'copyright', 'info' => '产品:' . $id .$resmsg );
                    }else {

                        return ajax_return( '产品:' . $id.$resmsg,'copyright');
                    }                    
                }

            }

            $token_info = $this->userToken->getOneTokenInfo($token_id);
            $this->smt->setToken($token_info);
            $replace_flag = false; //是否替换图片的标识

            if ($isAdd) { //新增的话，该替换的还是要替换
                //旧账号ID存在，同时(2个账号不一致或者原产品ID存在)
                if ($draft_info['old_token_id'] && ($draft_info['old_token_id'] <> $draft_info['token_id'] || $draft_info['old_productId'])) {
                    $replace_flag = true;
                }
            }

            $product_arr = array(); //要上传的产品信息
            //组装产品信息

            /**************产品详情信息开始**************/

            //看是否有关联产品的图片，有的话，直接上传并替换到里边去
            $relationFlag = false; //关联产品标识
            if (!empty($draft_detail['relationProductIds'])){
                $relationFlag = true;
                $relationHtml = $this->createRelationTemplate($draft_detail['relationProductIds']);
                $top_pic = site_url("attachments/images/relation_header.jpg");
                if (strstr($relationHtml, $top_pic) !== false){
                    $res1 = $this->smt->uploadBankImage('api.uploadImage', $top_pic, 'relation_banner');
                    if ($res1['status'] == 'SUCCESS' || $res1['status'] == 'DUPLICATE') {
                        $url1 = $res1['photobankUrl']; //返回的url链接
                        $relationHtml = str_replace($top_pic, $url1, $relationHtml);
                    }else {
                        $relationHtml = str_replace('<img src="'.$top_pic.'" style="width: 100.0%;">', '', $relationHtml);
                    }
                }
            }

            $detail = htmlspecialchars_decode($draft_detail['detail']);
            //替换产品模型
            $detail = replaceSmtImgToModule($detail);

            if ($replace_flag) {
                $detail = $this->replaceDetailPics($detail, $firstSku, $draft_info['productId']);
            }
            $product['detail'] = $detail; //用来更新本地数据

            //把模板，标题，售后模板等套进来
            //$templateId = $this->input->get_post('templateId');
            $templateId = $draft_detail['templateId'];
            $this->load->model(array('Slme_smt_template_model', 'Slme_after_sales_service_model'));
            $templateInfo = $this->Slme_smt_template_model->getTemplateInfo($templateId);

            if ($templateInfo && $templateInfo['id']) {
                //位置调整下，没模板的话就不要传了，不然也是浪费
                $picStr = '';
                if ($draft_detail['detailPicList']) { //描述图片信息
                    $tempPicList = explode(';', $draft_detail['detailPicList']);
                    foreach ($tempPicList as $imgPath) {
                        if ($replace_flag) { //需要替换才进行替换，不然才不管，都是上传到图片银行的
                            $newPath = $this->uploadOnePicToBank($imgPath, $firstSku, $draft_info['productId']);
                            if ($newPath) {
                                $picStr .= '<img src="' . $newPath . '" alt="aeProduct.getSubject()" title="aeProduct.getSubject()" />';
                                $product['detailPicList'][] = $newPath;
                            }
                        } else {
                            $picStr .= '<img src="'.$imgPath.'" alt="aeProduct.getSubject()" title="aeProduct.getSubject()" />';
                        }

                    }
                }

                $layout = htmlspecialchars_decode($templateInfo['content']);
                //替换模板ID
                $layout = str_replace('{my_template_id}', $templateId, $layout);


                $detail_title = $draft_detail['detail_title'];///$this->input->get_post('detail_title');
                //替换标题
                $layout = str_replace('{my_layout_title}', $detail_title, $layout);

                //售后模板
                $shouhouId    = $draft_detail['shouhouId'];//$this->input->get_post('shouhouId');
                $shouhouInfo  = $this->Slme_after_sales_service_model->getTemplateInfo($shouhouId);
                $layout       = str_replace('{my_shouhou_id}', $shouhouId, $layout);
                $shouhou_html = $shouhouInfo ? htmlspecialchars_decode($shouhouInfo['content']) : '';
                $layout       = str_replace('{my_layout_shouhou}', $shouhou_html, $layout);

                //替换描述
                $layout = str_replace('{my_layout_detail}', $detail, $layout);

                //替换描述图片
                $layout = str_replace('{my_layout_pic}', $picStr, $layout);

                if ($relationFlag){
                    if ($draft_detail['relationLocation'] == 'header'){ //在前边就加在最前
                        $layout = str_replace('{my_layout_relation}', '', $layout); //加在最前也得把这个标识去掉
                        $layout = $relationHtml.$layout;
                    }else { //在后边就替换下
                        $layout = str_replace('{my_layout_relation}', $relationHtml, $layout);
                    }
                }else { //不需要替换也得把这个标识去掉
                    $layout = str_replace('{my_layout_relation}', '', $layout);
                }

                $html = $layout;
                unset($detail);
                unset($layout);
            } else {
                //这是没有使用自定义售后模板的情况
                if ($relationFlag){
                    if ($draft_detail['relationLocation'] == 'header'){
                        $detail = $relationHtml.$detail;
                    }else {
                        $detail .= $relationHtml;
                    }
                }
                $html = $detail;
                unset($detail);
            }
            unset($relationHtml);

            $product_arr['detail'] = $html;
            unset($html);
            /**************产品详情信息结束**************/


            /*************产品主图信息开始**************/
            if ($replace_flag) { //需要上传主图

                $imgLists    = explode(';', $draft_detail['imageURLs']);
                $newImgLists = array();
                foreach ($imgLists as $k => $img) {
                    //$newImgLists[] = $this->replaceTempPics($img, $draft_skus[0]['skuCode'] . '-logo' . ($k + 1), $draft_info['productId']);
                    //现在上传到图片银行
                    $newImgLists[] = $this->uploadOnePicToBank($img, $firstSku.'-logo' . ($k + 1), $draft_info['productId']);
                }
                unset($imgLists);
                $product_arr['imageURLs']      = implode(';', $newImgLists);
                $product_arr['isImageDynamic'] = count($newImgLists) > 1 ? 'true' : 'false';
                $product['imageURLs']          = $product_arr['imageURLs'];
                $product['isImageDynamic']     = $product_arr['isImageDynamic'];
            } else {
                //主图列表,用';'连接上传
                $product_arr['imageURLs'] = $draft_detail['imageURLs'];

                //商品主图类型 --多图用动态，单图静态
                $product_arr['isImageDynamic'] = stripos($draft_detail['imageURLs'], ';') !== false ? 'true' : 'false';
            }
            /*************产品主图组装结束*************/


            /*************产品SKU属性组装开始***********/
            $aeopAeProductSKUs = array(); //需要组装下SKU信息

            foreach ($draft_skus as $sku) {

                $temp_property = array();
                if ($sku['aeopSKUProperty']) {
                    $temp = unserialize($sku['aeopSKUProperty']);
                    if ($replace_flag && $temp) { //图片等自定义信息存在，同时需要替换

                        foreach ($temp as $j => $t) {
                            if (array_key_exists('skuImage', $t) && $t['skuImage']) {
                                //上传图片处理 --还是上传到临时图片
                                $pic = $this->replaceTempPics($t['skuImage'], $firstSku . '-cust'.$j, $id);

                                $t['skuImage'] = $pic;

                            }
                            $temp_property[$j] = $t;
                        }
                        unset($temp);
                    } else {
                        $temp_property = $temp;
                    }
                }

                $aeopAeProductSKUs[] = array(
                    'skuPrice'        => $sku['skuPrice'],
                    'skuCode'         => $sku['smtSkuCode'],
                    'ipmSkuStock'     => $sku['ipmSkuStock'],
                    'aeopSKUProperty' => $temp_property
                );
                unset($temp_property);
            }

            $product_arr['aeopAeProductSKUs'] = json_encode($aeopAeProductSKUs);
            $product['aeopAeProductSKUs']     = $aeopAeProductSKUs; //直接就用数组

            if (count($aeopAeProductSKUs) == 1) { //只有一个是要传一口价的
                $product_arr['productPrice'] = $aeopAeProductSKUs[0]['skuPrice'];
            }
            unset($aeopAeProductSKUs);
            /*************产品SKU属性组装结束***********/


            //分类ID
            $product_arr['categoryId'] = $draft_info['categoryId'];

            $product_arr['deliveryTime'] = $draft_info['deliveryTime'];   //备货期


            if ($draft_detail['promiseTemplateId']) {
                $product_arr['promiseTemplateId'] = $draft_detail['promiseTemplateId']; //服务模板ID
            }

            //标题
            $product_arr['subject'] = $draft_info['subject'];

            //关键词 --过滤下';'和','
            $product_arr['keyword'] = filterForSmtProduct($draft_detail['keyword']);

            //更多关键词
            $productMoreKeywords1 = filterForSmtProduct($draft_detail['productMoreKeywords1']);
            if ($productMoreKeywords1) {
                $product_arr['productMoreKeywords1'] = $productMoreKeywords1;
            }
            $productMoreKeywords2 = filterForSmtProduct($draft_detail['productMoreKeywords2']);
            if ($productMoreKeywords2) {
                $product_arr['productMoreKeywords2'] = $productMoreKeywords2;
            }

            if ($draft_info['groupId']) {
                //产品组ID
                $product_arr['groupId'] = $draft_info['groupId'];
            }

            //运费模板ID
            $product_arr['freightTemplateId'] = $draft_detail['freightTemplateId'];

            //是否添加水印
            $product_arr['isImageWatermark'] = 'false';

            //单位
            $product_arr['productUnit'] = $draft_detail['productUnit'];

            //是否打包
            if ($draft_detail['packageType']) {
                //每包件数
                $lotNum                = $draft_detail['lotNum'];
                $product_arr['lotNum'] = intval($lotNum) > 1 ? intval($lotNum) : 2;
            }
            $product_arr['packageType'] = $draft_detail['packageType'] ? 'true' : 'false';

            //包装长宽高
            $product_arr['packageLength'] = (int)$draft_info['packageLength'];
            $product_arr['packageWidth']  = (int)$draft_info['packageWidth'];
            $product_arr['packageHeight'] = (int)$draft_info['packageHeight'];

            //商品毛重
            $product_arr['grossWeight'] = $draft_info['grossWeight'];

            //是否自定义记重 -- 自定义记重暂时未作
            $isPackSell = $draft_detail['isPackSell'];
            $isPackSell = $isPackSell == '1' ? 'true' : 'false';
            $baseUnit   = '';
            $addUnit    = '';
            $addWeight  = '';
            $product_arr['isPackSell'] = false; // api变动 暂时都设置成false
            //有效期
            $product_arr['wsValidNum'] = $draft_info['wsValidNum'];

            if ($isAdd) {
                //商品来源 --固定死
                $api                = 'api.postAeProduct';
                $product_arr['src'] = 'isv';
            } else {
                //调用edit的方法
                $api                      = 'api.editAeProduct';
                $product_arr['productId'] = $id;

                if ($draft_detail['src']) { //修改的
                    //修改用原样的
                    $product_arr['src'] = $draft_detail['src'];
                }
            }


            /*******************产品属性封装开始***************************/
            $aeopAeProductPropertys                = unserialize($draft_detail['aeopAeProductPropertys']);
            $product_arr['aeopAeProductPropertys'] = json_encode($aeopAeProductPropertys);
            unset($aeopAeProductPropertys);
            /***********************产品属性封装结束**********************/

            if ($draft_detail['bulkOrder'] && $draft_detail['bulkDiscount']) {
                //最小批发数量
                $product_arr['bulkOrder'] = (int)$draft_detail['bulkOrder'];
                //批发折扣
                $product_arr['bulkDiscount'] = (int)$draft_detail['bulkDiscount'];
            }

            //尺码表模板ID
            if (!empty($draft_detail['sizechartId']) && $draft_detail['sizechartId'] > 0) {
                $product_arr['sizechartId'] = $draft_detail['sizechartId'];
            }
// print_r($product_arr);
            //发布或者修改
            $result = $this->smt->getJsonDataUsePostMethod($api, $product_arr);
            $data   = json_decode($result, true);
            // var_dump($data);exit;
            //不管成功还是失败，都把数据保存下来
            $product_arr['productId'] = $draft_info['productId'];
            $product['productId']     = $draft_info['productId'];
            $return                   = $this->hanleProductData($product);
            if (!$return['status']) {
                //写错误日志
            }

            unset($draft_info);
            unset($draft_detail);

            if (array_key_exists('success', $data) && $data['success']) { //操作成功了
                if ($isAdd) { //是新刊登的产品
                    $realProductId = $data['productId'];

                    $where = array('productId' => (string)$id);

                    $newListData['productId'] = $realProductId;

                    $newListData['productStatusType'] = 'onSelling';
                    $newListData['old_token_id']      = 0;
                    $newListData['old_productId']     = '';
                    $newListData['product_url']       = 'http://www.aliexpress.com/item/-/'.$realProductId.'.html';
                    $newListData['ownerMemberId']     = $token_info['member_id'];

                    //更新详情表的产品ID
                    $newData['productId'] = $realProductId;
                    $this->Smt_product_detail_model->update($newData, array('where' => $where));

                    $plat_info = getDefinedPlatInfo(); //SMT平台信息

                    //SMT销售前缀列表
                    $this->load->model('smt/Smt_user_sale_code_model');
                    $sale_code = $this->Smt_user_sale_code_model->getSalersPrefixList();

                    $user_id = 0; //解析前缀获取广告对应的用户

                    //更新SKU列表信息
                    foreach ($draft_skus as $sku) {
                        $skus = buildSysSku($sku['smtSkuCode']); //还是会带{YY}
                        foreach ($skus as $skuCode) { //发布成功了，更新现有的数据
                            $newData['skuMark'] = $realProductId . ':' . $skuCode;
                            $oldMark            = $id . ':' . $skuCode;
                            $options = array(
                                'where' => array(
                                    'productId'    => $id,
                                    'skuMark'      => $oldMark,
                                    'overSeaValId' => $sku['overSeaValId']
                                )
                            );
                            $this->Smt_product_skus_model->update($newData, $options);
                            unset($options);

                            $prefix = get_skucode_prefix($sku['smtSkuCode']); //产品的前缀
                            if ($prefix) {
                                $user_id = $user_id ? $user_id : (array_key_exists($prefix, $sale_code) ? $sale_code[$prefix]['user_id'] : $user_id); //对应的账号ID
                            }

                            /****插入一条记录到刊登记录内开始****/
                            $publishRecord = array(
                                'SKU'            => rebuildSmtSku($skuCode, true), //到这就是ERP内的SKU
                                'userID'         => $user_id,
                                'publishTime'    => date('Y-m-d H:i:s'),
                                'platTypeID'     => $plat_info['platTypeID'],
                                'publishPlat'    => $plat_info['platID'],
                                'sellerAccount'  => $this->smt->_seller_account, //账号，通过tokenid来吧
                                'itemNumber'     => $realProductId,
                                'publishViewUrl' => 'http://www.aliexpress.com/item/-/' . $realProductId . '.html' //链接，处理下吧
                            );

                            $this->load->model('Sku_publish_record_model');
                            $this->Sku_publish_record_model->add($publishRecord);
                            /****插入一条记录到刊登记录内结束****/
                        }

                        //更新列表页的产品信息
                        $newListData['user_id'] = $user_id; //用老账号的用户id --不要通过session处理，怕以后会跑计划任务
                        $this->Smt_product_list_model->update($newListData, array('where' => $where));
                    }
                    unset($newListData);
                    unset($newData);
                }
                unset($product_arr);
                unset($draft_skus);
                if ($auto) {
                    return array('status' => true, 'info' => '产品:' . $id . ($isAdd ? '发布成功，新产品ID为:' . $realProductId : '修改成功'));
                }else {
                    ajax_return('产品:' . $id . ($isAdd ? '发布成功，新产品ID为:' . $realProductId : '修改成功'), true);
                }
            } else {
                unset($product_arr);
                unset($draft_skus);
                if ($auto){
                    return array('info' => '产品:' . $id . ($isAdd ? '发布' : '修改') . '失败,'.(isset($data['error_code']) ? $data['error_code'] : '').$data['error_message'], 'status' => false);
                }else {
                    ajax_return('产品:' . $id . ($isAdd ? '发布' : '修改') . '失败,'.(isset($data['error_code']) ? $data['error_code'] : '') . $data['error_message'], false);
                }
            }
        } else {
            if ($auto){
                return array('status' => false, 'info' => '产品:' . $id . '不存在');
            }else {
                ajax_return('产品:' . $id . '不存在', false);
            }
        }
    }

    /**
     * 产品批量发布
     */
    public function batchPost(){
        //产品ID列表
        $productIds = $this->input->get_post('productIds');

        if (!$productIds){
            $return[] = array('status' => false, 'info' => '请传入产品数据');
        }else {
            $products = explode(',', $productIds);
            $return   = array();
            foreach ($products as $productId) {
                $return[] = $this->postAeProduct($productId, true, true);
                //$return[] = array('status' => true, 'info' => $productId . ' is ok');
            }
        }
        $this->template('admin/publish/smt/batch_post', array('return' => $return));
    }

    /**
     * 解析并保存产品数据 --只要解析变更的那部分，其他的基本不用管
     * @param $product
     * @return array
     */
    protected function hanleProductData($product){

    	//产品ID
    	$productId = $product['productId'];

        $this->db->trans_begin();
        $product_list_data                        = array();
        $product_list_data['gmtCreate']           = date('Y-m-d H:i:s');
        $aeopAeProductSKUs                        = $product['aeopAeProductSKUs'];
        $product_list_data['multiattribute']      = (count($aeopAeProductSKUs) > 1 ? 1 : 0);
        $product_list_data['isRemove']            = '0';

        //处理广告详情信息
        $detail_data = array();
        if (array_key_exists('imageURLs', $product)){
            $detail_data['imageURLs']              = $product['imageURLs'];
        }
        if (array_key_exists('detailPicList', $product)){
            $detail_data['detailPicList']          = implode(';', $product['detailPicList']);
        }
        if (array_key_exists('detail', $product)){
            $detail_data['detail']                 = htmlspecialchars($product['detail']);
            $detail_data['detailLocal']            = htmlspecialchars($product['detail']);
        }
        if (array_key_exists('isImageDynamic', $product)){
            $detail_data['isImageDynamic'] = $product['isImageDynamic'];
        }



        //直接更新
        $this->Smt_product_detail_model->update($detail_data, array('where' => array('productId' => (string)$productId)));
        if ($this->db->_error_message()){
            $this->db->trans_rollback();
            return array('status' => false, 'info' => '保存产品详情信息出错');
        }

        $smtSkuCodeArr = array();
        //处理广告SKU信息 --这要处理下
        foreach ($aeopAeProductSKUs as $sku){
            $smtSkuCodeArr[] = strtoupper(trim($sku['skuCode']));
            $sku_data['aeopSKUProperty'] = $sku['aeopSKUProperty'] ? serialize($sku['aeopSKUProperty']) : '';
            $where['skuMark'] = $productId.':'.filterSmtProductSku($sku['skuCode']);
            $this->Smt_product_skus_model->update($sku_data, array('where' => $where));
            if ($this->db->_error_message()){
                $this->db->trans_rollback();
                return array('status' => false, 'info' => '保存产品SKU信息出错');
            }
        }

        $smtSkuCodeArr = array_unique($smtSkuCodeArr);
        if ($product_list_data['multiattribute'] == 1 && count($smtSkuCodeArr) == 1){
            $product_list_data['multiattribute'] = 0; //单属性设置
        }
        unset($smtSkuCodeArr);

        $this->Smt_product_list_model->update($product_list_data, array('where' => array('productId' => (string)$productId)));
        if ($this->db->_error_message()){
            $this->db->trans_rollback();
            return array('status' => false, 'info' => '保存产品列表信息出错');
        }

        $this->db->trans_commit();
        return array('status' => true);
    }

    /**
     * 替换详情中的图片为相应图片银行的图片
     * @param $detail
     * @param $skuCode
     * @param int $id
     * @return mixed
     */
    public function replaceDetailPics($detail, $skuCode, $id=0){
        $api2   = 'api.uploadImage';
        preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/', $detail, $matches);
        if ($matches[2]) {
            foreach ($matches[2] as $k => $src) {
                if (!$src){
                    continue;
                }
                $return_data = $this->smt->uploadBankImage($api2, $src, $skuCode . '-' . $k);
                if ($return_data['success']) {
                    $detail = str_replace($src, $return_data['photobankUrl'], $detail);
                } else { //替换失败的话，看是否需要写点日志

                }
            }
        }
        return $detail;
    }
    
    /**
     * 上传一张图片到图片银行
     * @param unknown $src
     * @param unknown $skuCode
     * @param number $id
     * @return Ambigous <string, unknown>
     */
    public function uploadOnePicToBank($src, $skuCode, $id=0){
    	$api2   = 'api.uploadImage';
    	$return_data = $this->smt->uploadBankImage($api2, $src, $skuCode .rand(1000, 9999));
    	if (!$return_data['success']) {//没上传成功，写下错误日志
    		
    	}
    	return $return_data['success'] ? $return_data['photobankUrl'] : '';
    }

    /**
     * 调用上传临时图片接口替换图片
     * @param $img
     * @param $skuCode
     * @param $id:错误日志时需要用到
     * @return string
     */
    public function replaceTempPics($img, $skuCode, $id=0){
        $api      = 'api.uploadTempImage';
        $new_pic = '';
        //循环替换图片吧
        $img_return = $this->smt->uploadBankImage($api, $img, $skuCode);
        if ($img_return && $img_return['success']) {
            $new_pic = $img_return['url'];
        } else { //失败了，写下日志吧

        }
        return $new_pic;
    }


    /**
     * 异步显示本地子分类信息
     */
    public function showChildCategory()
    {
        $category_id   = (int)$this->input->get_post('category_id');
        $category_list = $this->smt_category->getCategoryListByPid($category_id, true);
        echo json_encode($category_list);
    }

    /**
     * 获取关键字推荐类目的分类名称信息--父分类也显示出去吧
     */
    public function showCommandCategoryList()
    {

        $keyword  = trim($this->input->get_post('keyword'));
        $token_id = trim($this->input->get_post('token_id'));
        $category_list = $this->getOnlineCategoryId($token_id, $keyword);
        //$category_list = array('200000174', '200000236', '3010');
        $rs            = array();
        if ($category_list) {
            foreach ($category_list as $category_id) {
                //显示推荐的类目信息
                $rs[] = array('id' => $category_id, 'name' => $this->smt_category->getCateroryAndParentName($category_id));
            }
        }
        echo json_encode($rs);
    }

    /**
     * 根据关键词返回推荐子叶类目信息--在线获取的
     * @param $token_id
     * @param $keyword
     * @return array
     */
    public function getOnlineCategoryId($token_id, $keyword)
    {
        $result = '';
        $rs     = array(); //返回的数组
        $api    = 'api.recommendCategoryByKeyword';

        if ($token_id && $keyword) {
            $token_info = $this->userToken->getOneTokenInfo($token_id);
            $this->smt->setToken($token_info);

            $result = $this->smt->getJsonData($api, 'keyword=' . rawurlencode($keyword));
        }
        $data = json_decode($result, true);
        if ($data['success'] && $data['total'] > 0) {
            $rs = $data['cateogryIds'];
        }

        return $rs;
    }

    /**
     * 编辑草稿详情信息
     */
    public function edit()
    {
        $id = $this->input->get_post('id');
        if ($id) {

            //查询草稿数据
            $draft_info = $this->Smt_product_list_model->getProductListInfo($id);

            if (in_array($draft_info['productStatusType'], array('newData', 'waitPost', 'failure'))) { //可以直接编辑的状态
                //会调用刊登API
                $api = 'post';
            } else {
                //调用修改API
                $api = 'edit';
            }

            //查询草稿SKU信息
            $draft_skus = $this->Smt_product_skus_model->getProductSkuProperty($id);

            //查询草稿详情
            $draft_detail = $this->Smt_product_detail_model->getProductDetailInfo($id);

            //已选择的分类
            $category_info = array('id' => $draft_info['categoryId'], 'name' => $this->smt_category->getCateroryAndParentName($draft_info['categoryId']));

            $token_id = $draft_info['token_id'];
            //查询选择的分类的属性
            $category_attributes = array();
            $attributes = $this->Slme_smt_category_attribute_model->getCategoryAttributes($draft_info['categoryId']);
            if (!$attributes){ //属性直接不存在
                $return = $this->Slme_smt_category_attribute_model->getChildAttributesResultByPostCateIdAndPath($token_id, $draft_info['categoryId']);
                if ($return)
                    $category_attributes = $return;
            }else { //属性存在但不是最新的
                $category_attributes = unserialize($attributes->attribute);
                if (!$attributes->last_update_time || date('Y-m-d') != date('Y-m-d', strtotime($attributes->last_update_time))) {
                    $return = $this->Slme_smt_category_attribute_model->getChildAttributesResultByPostCateIdAndPath($token_id, $draft_info['categoryId']);
                    if ($return)
                        $category_attributes = $return;
                }
            }

            //对属性进行排序处理
            $category_attributes = sortAttribute($category_attributes);

            //运费模板
            $freight = $this->Slme_smt_freight_template_model->getFreightTemplateList($token_id);

            //服务模板
            $service = $this->Slme_smt_service_template_model->getServiceTemplateList($token_id);

            //产品分组
            $group = $this->Slme_smt_product_group_model->getProductGroupList($token_id);

            //单位列表
            $unit = $this->Slme_smt_unit_model->getUnitList();

            //产品信息模块
            $this->load->model('smt/Slme_smt_product_module_model');
            $module = $this->Slme_smt_product_module_model->getModuleList($token_id);

            //速卖通模板及售后模板
            $plat = 6;
            $this->load->model(array('Slme_smt_template_model', 'Slme_after_sales_service_model'));
            $template_list = $this->Slme_smt_template_model->getTemplateList(array('select' => 'id, name', 'plat' => $plat));
            $shouhou_list  = $this->Slme_after_sales_service_model->getTemplateList(array('select' => 'id, name', 'plat' => $plat, 'token_id' => $token_id));

            //获取关联产品的信息
            $relationDetailInfo = array();
            if (!empty($draft_detail['relationProductIds'])){
                $relationProductIds = $draft_detail['relationProductIds'];
                //产品详情列表
                $relationProductArr = explode(';', $relationProductIds);
                $relationDetailInfo = $this->Smt_product_detail_model->getProductDetailsFields($relationProductArr, 'productId, imageURLs');
            }

            //产品信息不组合成一条数据了
            $data = array(
                'draft_info'    => $draft_info,
                'draft_skus'    => $draft_skus,
                'draft_detail'  => $draft_detail,
                'category_info' => $category_info,
                'freight'       => $freight,
                'service'       => $service,
                'group'         => $group,
                'module'        => $module,
                'unit'          => $unit,
                'action'        => 'edit',
                'attributes'    => $category_attributes,
                'token_id'      => $draft_info['token_id'],
                'template_list' => $template_list,
                'shouhou_list'  => $shouhou_list,
                'api'           => $api,
                'relationDetailInfo' => $relationDetailInfo
            );
            $this->_template('admin/publish/smt/add', $data);
        } else { //没有ID--直接跳转到列表页吧
            redirect('admin/publish/smt/index');
        }
    }

    /**
     * 删除草稿数据
     * @see Admin_Controller::delete()
     */
    public function delete(){
        $id = $this->input->get_post('id');

        if ($id){
            $result = $this->_handleDelProductDraftData($id);
            echo json_encode($result);
        }else {
            echo json_encode(array('status' => false, 'msg' => '非法操作'));
        }
        exit;
    }

    /**
     * 删除草稿数据
     * @param $id 产品ID
     * @return array
     */
    private function _handleDelProductDraftData($id){
        try{
            $info = $this->Smt_product_list_model->getProductListInfo($id);
            //新录入、失败或者等待的才让在这修改
            if ($info && in_array($info['productStatusType'], array('newData', 'failure', 'waitPost'))){
                unset($info);
                $this->db->trans_begin();

                $this->Smt_product_list_model->delete(array('where' => array('productId' => $id)));
                if ($this->db->_error_message()){
                    $this->db->trans_rollback();
                    return array('status' => false, 'info' => '删除产品列表信息出错');
                }
                $this->Smt_product_detail_model->delete(array('where' => array('productId' => $id)));
                if ($this->db->_error_message()){
                    $this->db->trans_rollback();
                    return array('status' => false, 'info' => '删除产品详情信息出错');
                }
                $this->Smt_product_skus_model->delete(array('where' => array('productId' => $id)));
                if ($this->db->_error_message()){
                    $this->db->trans_rollback();
                    return array('status' => false, 'info' => '删除产品SKU信息出错');
                }
                $this->db->trans_commit();
                return array('status' => true, 'msg' => '删除成功');
            }else {
                return array('status' => false, 'msg' => '没有找到数据或数据状态错误');
            }
        }catch (Exception $e){
            return array('status' => false, 'msg' => $e->getMessage());
        }
    }

    /**
     * 批量删除草稿数据
     */
    public function batchDel(){
        $productIds = $this->input->get_post('id');
        if (!empty($productIds)){
            $productIdArr = explode(',', $productIds);
            //循环删除草稿
            $success = array();
            $error = array();
            foreach ($productIdArr as $id){
                $rs = $this->_handleDelProductDraftData($id);
                if ($rs['status']){
                    $success[] = "草稿$id 删除成功";
                }else{
                    $error[] = "草稿$id 删除失败，".$rs['msg'];
                }
            }
            $msg = !empty($success) ? implode(';', $success) : '';
            $msg .= !empty($error) ? implode(';', $error) : '';
            echo json_encode(array('status' => true, 'msg' => $msg));
        }else {
            echo json_encode(array('status' => false, 'msg' => '非法操作'));
        }
        exit;
    }

    /**
     * 改变产品的状态--目前是到待发布
     */
    public function changeToWaitPost(){
        $productIds = $this->input->get_post('ids');
        if (!$productIds){
            ajax_return('产品ID不能为空', false);
        }

        $productArray = explode(',', $productIds);

        $list_data = $this->Smt_product_list_model->getProductsFields($productArray, 'productId, productStatusType');

        if (!$list_data){
            ajax_return('产品ID:'.$productIds.'没有找到数据', false);
        }

        $error = array();
        $success = array();
        foreach ($list_data as $row){
            if (in_array($row['productStatusType'], array('newData', 'failure'))){
                $success[] = (string)$row['productId'];
            }else {
                $error[] = $row['productId'];
            }
        }

        $data['productStatusType'] = 'waitPost';
        $affected = $this->Smt_product_list_model->update($data, array('where_in' => array('productId' => $success)));

        if ($affected){
            ajax_return(($error ? implode(',', $error).'失败了' : ''), true);
        }else {
            ajax_return('产品ID:'.$productIds.'保存为待发布失败', false);
        }
    }

    /**
     * 详情预览功能
     */
    public function detailView(){
        //模板ID
        $templateId = $this->input->get_post('templateId');

        //售后模板ID
        $shouhouId = $this->input->get_post('shouhouId');

        //描述标题
        $detailTitle = $this->input->get_post('detailTitle');

        //描述图片
        $detailPicList = $this->input->get_post('detailPicList');

        //关联产品id
        $relation_id = $this->input->get_post('relation_id');
        $relation_html = '';
        if ($relation_id){
            $relation_html = $this->createRelationTemplate($relation_id);
        }

        //关联的位置
        $relationLocation = $this->input->get_post('relationLocation');

        //描述文字
        $detail = $this->input->get_post('detail');
        /***描述中的kse:widget标签要替换下,这个必须要在前边，不然下边的图片替换比较麻烦***/
        $detail = replaceSmtImgToModule($detail);

        //获取产品信息模块ID
        preg_match_all('/<kse:widget.*id="(\d+)".*><\/kse:widget>/i', $detail, $matches);

        if ($matches && $matches[1]){
            $this->load->model('smt/Slme_smt_product_module_model');

            foreach($matches[1] as $k => $moduleId){

                $moduleInfo = $this->Slme_smt_product_module_model->getModuleFields($moduleId, 'displayContent');
                if ($moduleInfo){
                    $display    = htmlspecialchars_decode($moduleInfo['displayContent']);
                    //替换产品信息模块
                    $detail = str_replace($matches[0][$k], $display, $detail);
                }
            }
        }


        $html = '';
        if ($templateId){ //模板存在就套模板

            //替换下模板和产品信息模块
            $this->load->model(array('Slme_smt_template_model', 'Slme_after_sales_service_model'));
            //模板信息
            $templateInfo = $this->Slme_smt_template_model->getTemplateInfo($templateId);

            if ($templateInfo){
                $layout = htmlspecialchars_decode($templateInfo['content']);

                //替换标题
                $layout = str_replace('{my_layout_title}', $detailTitle, $layout);

                //售后模板
                $shouhouInfo = $this->Slme_after_sales_service_model->getTemplateInfo($shouhouId);

                $shouhou_html = htmlspecialchars_decode($shouhouInfo['content']);
                $layout = str_replace('{my_layout_shouhou}', $shouhou_html, $layout);

                $pic = '';
                if ($detailPicList){
                    $picList = explode(';', $detailPicList);
                    foreach ($picList as $picPath){
                        $pic .= '<img src="'.$picPath.'"/>';
                    }
                }
                //替换描述图片
                $layout = str_replace('{my_layout_pic}', $pic, $layout);

                //替换描述
                $layout = str_replace('{my_layout_detail}', $detail, $layout);

                if ($relationLocation == 'header'){ //添加下位置
                    $layout = str_replace('{my_layout_relation}', '', $layout); //模板位置的就替换成空的吧
                    $layout = $relation_html.$layout;
                }elseif ($relationLocation == 'footer'){
                    $layout = str_replace('{my_layout_relation}', $relation_html, $layout);
                }

                $html = $layout;

                unset($layout);
            }else {
                //选择了，但是模板信息已经不存在了
                if ($relationLocation == 'header'){ //添加下位置
                    $detail = $relation_html.$detail;
                }elseif ($relationLocation == 'footer'){
                    $detail .= $relation_html;
                }
                $html = $detail;
            }
            ajax_return('', true, $html);
        }else {
            //没选择模板
            if ($relationLocation == 'header'){ //添加下位置
                $detail = $relation_html.$detail;
            }elseif ($relationLocation == 'footer'){
                $detail .= $relation_html;
            }

            $html = $detail;
            //替换下产品信息模块 --不存在，直接用模板吧
            ajax_return('', true, $html);
        }
    }

    /**
     * 创建关联产品模板
     * @param $relationIds 关联的产品id
     * @return string
     */
    private function createRelationTemplate($relationIds){
        $html = '';

        //获取产品单价，标题，图片信息
        if ($relationIds){
            $productIds = explode(';', $relationIds);
            //标题
            $product_list = $this->Smt_product_list_model->getProductsFields($productIds, 'productId, subject');
            if ($product_list){
                //单价
                $price_list = $this->Smt_product_skus_model->getProductsMinPriceList($productIds);

                //图片
                $detail_list = $this->Smt_product_detail_model->getProductDetailsFields($productIds, 'productId,imageURLs');

                //注意变更下里边的图片链接地址
                $pic_top = site_url("attachments/images/relation_header.jpg");
                $html_header = <<<html
<div style="background: #d00000;width: 775.0px;margin: 10.0px auto;">
    <img src="$pic_top" style="width: 100.0%;">
    <div style="background: #d00000;padding: 0px;font-size: 0.0px;">
html;
                $html_footer = <<<html
    </div>
</div>
html;
                $html_body = '';
                //先格式化下产品列表
                $productsArr = array();
                foreach ($product_list as $row){
                    $productsArr[$row['productId']] = $row;
                }
                unset($product_list);
                //按照传过来的产品id顺序进行排序
                foreach ($productIds as $productId){
                    if (!empty($productsArr[$productId])){
                        $row       = $productsArr[$productId]; //产品信息
                        $imageURLs = $detail_list[$productId]['imageURLs']; //图片链接
                        $imgList   = explode(';', $imageURLs); //图片列表
                        $firstImg  = array_shift($imgList); //要显示的第一张图片
                        $html_body .= '<div style="display: inline-block;width: 187.5px;background: #ffffff;margin: 0 0px 5.0px 5.0px;text-align: center;">
                                <table title="'.$row['subject'].'" border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div style="width: 187.0px;height: 187.0px;border-bottom: 1.0px solid #cccccc;">
                                                    <a href="http://www.aliexpress.com/item/xxx/'.$productId.'.html" target="_blank">
                                                        <img alt="'.$row['subject'].'" src="'.$firstImg.'" height="100%" width="100%"></a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div style="height: 107.0px;">
                                    <h5 style="height: 50.0px;font-size: 16.0px;">'.showSubject($row['subject']).'</h5>
                                    <h5 style="display: inline;font-size: 16.0px;">Price:</h5>
                                    <h5 style="font-size: 20.0px;display: inline;color: #ff6800;"> <b>$'.(isset($price_list[$productId]) ? $price_list[$productId] : 0).'</b>
                                    </h5>
                                </div>
                                <a href="http://www.aliexpress.com/item/xxx/'.$productId.'.html" target="_blank">
                                    <img src="http://g02.a.alicdn.com/kf/HTB14_BtHpXXXXaXXpXXq6xXFXXXC/222299605/HTB14_BtHpXXXXaXXpXXq6xXFXXXC.jpg">
                                </a>
                            </div>';
                    }
                }
                $html = $html_header.$html_body.$html_footer;
            }
        }

        return $html;
    }


    /**
     * 异步上传SKU目录
     */
    public function ajaxUploadDirImage(){
        $token_id = $this->input->get_post('token_id');
        $dirName  = trim($this->input->get_post('dirName'));
        $opt      = trim($this->input->get_post('opt'));

        if (empty($token_id) || empty($dirName)) {
            ajax_return('账号或者SKU不能为空', false);
        }

        //本程序的上级目录
        $topDir = str_replace('\\', '/', dirname(ROOTPATH));
        //图片库中ebay图片的位置
        $ebayPicDir = $topDir . '/erp/imgServer/upload/SMT';

        $skuDir = $ebayPicDir . '/' . $dirName;
        if (strtoupper($opt) == 'SP'){
            $spArray = array('SP', 'sp', 'Sp', 'sP');
            $hasFlag = false;
            foreach ($spArray as $sp){
                $skuDir = $ebayPicDir . '/' . $dirName.'/'.$sp;
                if (file_exists($skuDir)){ //文件夹还是存在的
                    $hasFlag = true;
                    break;
                }
            }
            if (!$hasFlag){
                ajax_return('SKU对应的文件夹不存在(若发现该SKU目录的名称含有小写，请让修改)', false);
            }
        }else {
            if (!file_exists($skuDir)) { //SKU对应的文件夹不存在
                ajax_return('SKU对应的文件夹不存在(若发现该SKU目录的名称含有小写，请让修改)', false);
            }
        }

        if (!is_dir($skuDir)) {
            ajax_return('SKU对应的信息不是文件夹，请检查路径', false);
        }

        $dh = opendir($skuDir);

        //图片扩展列表
        $imageExt = defineSmtImageExd();

        $api = 'api.uploadImage'; //上传到哪个图片接口

        $success = array();
        $error   = array();

        //获取要上传到的账号信息
        $tokenInfo = $this->userToken->getOneTokenInfo($token_id);
        if (empty($tokenInfo)) {
            ajax_return('没有查找到账号信息', false);
        }

        $this->smt->setToken($tokenInfo);

        while ($file = readdir($dh)) {
            if ($file != '.' && $file != '..' && !is_dir($skuDir . '/' . $file)) {
                $exd = strtolower(getFileExtendName($file));
                if (in_array($exd, $imageExt)) { //是速卖通的图片
                    $temp      = explode('.', $file);
                    $fileName  = array_shift($temp);
                    $imagePath = $skuDir . '/' . $file; //真实的图片路径
                    $result    = $this->smt->uploadBankImage($api, $imagePath, $fileName); //返回的图片结果
                    //print_r($result);exit;
//$result = array();
                    if (array_key_exists('status', $result) && ($result['status'] == 'SUCCESS' || $result['status'] == 'DUPLICATE')) {
                        $url       = $result['photobankUrl']; //返回的url链接
                        $success[] = $url;
                    } else {
                        $msg = $file;
                        if (array_key_exists('error_code', $result)){
                            $msg .= ',error_code:'.$result['error_code'].','.$result['error_message'];
                        }
                        $error[] = $msg; //失败的图片名称
                    }
                }
            }
        }
        closedir($dh);

        ajax_return($error, true, $success);
    }

    /**
     * 异步上传一张图片到临时文件以添加自定义属性图片
     */
    public function ajaxUploadOneCustomPic(){
        $token_id = $this->input->get_post('token_id');
        $oldImg   = trim($this->input->get_post('img'));

        if (empty($token_id)) {
            ajax_return('上传失败，账号为空', false);
        }

        if (empty($oldImg)) {
            ajax_return('上传失败，需要上传的图片链接为空', false);
        }

        //开始正式上传
        $tokenInfo = $this->userToken->getOneTokenInfo($token_id);
        if (empty($tokenInfo)) {
            ajax_return('上传失败,没有查到对应的账号信息', false);
        }
        $this->smt->setToken($tokenInfo);

        $api = 'api.uploadTempImage';

        //上传图片到临时目录
        $result = $this->smt->uploadBankImage($api, $oldImg);
        if ($result && array_key_exists('success', $result) && $result['success']) {
            ajax_return('', true, $result['url']);
        } else { //失败了，写下日志吧
            ajax_return('error_code:' . $result['error_code'] . ',' . $result['error_message'], false);
        }
    }


    //获取SKU的必填关键字，及其选填关键字
    public  function  getSkuKeyWord()
    {

        $sku  = $this->input->get_post('SKU');
        $skuarr = explode(',',$sku);
        $skuOne = $skuarr[0];

        $result = $this->Smt_auto_draft_list_model->getSkuKeyWord($skuOne);
     //   var_dump($result);exit;
        $mustword = '';
        $optionword = '';
        if(!empty($result))
        {
            if(!empty($result['must_keyword']))
            {

                $must_keyword_string = $result['must_keyword'];
                $must_keyword = explode(',',$must_keyword_string);
                foreach($must_keyword as  $word)
                {
                    if(!empty($word))
                    {
                        $mustword = $mustword.$word.': <input type="checkbox" name="mustword" value="'.$word.'" /><br/> ';

                    }
                }
            }

            if(!empty($result['option_keyword']))
            {
                $option_keyword_string  = $result['option_keyword'];
                $option_keyword = explode(',',$option_keyword_string);
                foreach($option_keyword as  $word)
                {
                        if(!empty($word))
                        {
                            $optionword = $optionword.$word.': <input type="checkbox" name="optionword" value="'.$word.'" /><br/> ';

                        }
                }
            }
        }

        $skuWord[0] = $mustword;
        $skuWord[1] = $optionword;
        $skuWord[2] = $skuOne;
        ajax_return('',1,$skuWord);
    }

    //获取SMT账号
    public function getStmAccount()
    {
        $account = $this->input->get_post('account');

        $result = $this->Smt_auto_draft_list_model->getSmtAccount();
        $str = '';
        foreach($result as $v)
        {
            if($v['seller_account'] == $account)
            {
                $str=$str.'<option  selected value='.$v['token_id'].'>'.$v['token_id'].'-'.$v['seller_account'].'</option>';
            }
            else
            {
                $str=$str.'<option value='.$v['token_id'].'>'.$v['token_id'].'-'.$v['seller_account'].'</option>';
            }

        }
       ajax_return($str,1);
    }
    //更新账号
    public  function  updateStmAccount()
    {
        $productid =  $this->input->get_post('productid');
        $arr['token_id']  =  $this->input->get_post('token_id');
        $result = $this->Smt_auto_draft_list_model->updateListInfo($productid,$arr);

        if($result==1)
        {
            ajax_return('修改账号成功',1);

        }
        else
        {
            ajax_return('修改失败',2);
        }
    }
    // 跟新标题
    public  function  updateListSubject()
    {
        $productid =  $this->input->get_post('productid');
        $arr['subject']  =  $this->input->get_post('subject');

        $this->db->trans_begin();

        $result = $this->Smt_auto_draft_list_model->updateListInfo($productid,$arr);

        $arr2['detail_title'] =  $arr['subject'];
        $result2 = $this->Smt_auto_draft_list_model->updateInfoByProductid($productid,$arr2);
        if(($result==1)&&($result2==1))
        {
            $this->db->trans_commit();
            ajax_return('修改标题成功',1);

        }
        else
        {
            $this->db->trans_rollback();
            ajax_return('修改失败',2);
        }
    }


    //生成对应的草稿
    public function auto_draft_list()
    {

        $sku  = $this->input->get_post('sku');
        $addmustword  = $this->input->get_post('addmustword');
        $addoptionword = $this->input->get_post('addoptionword');
        $productid  = $this->input->get_post('productid');
       // $perfectnum  = $this->input->get_post('perfectnum');
        $mustword  = $this->input->get_post('mustword');
        $optionword = $this->input->get_post('optionword');
        $empty_biaoti = $this->input->get_post('empty_biaoti');
        $accounttext = $this->input->get_post('accounttext');
        $sales_prefix = $this->input->get_post('perfectnum');

     //   var_dump($_POST);exit;

        $accountarr = explode(',',$accounttext);


        if($empty_biaoti =='yes') //如果标题为空
        {
            $restring = $this->autoEmptyDraft($productid,$accountarr,$sales_prefix);
            ajax_return($restring,1);
        }

        $mustwordarr = explode(',',$mustword);
            if($addmustword !='')
            {

                $this->Smt_auto_draft_list_model->updateWord('must_keyword',$addmustword,$sku);
                $addmustwordarr = explode(',',$addmustword);
                foreach($addmustwordarr as $add)
                {
                    array_push($mustwordarr,$add);
                }
            }
        $mustwordarr=   array_filter($mustwordarr);
        $mustwordarr=   array_values($mustwordarr);



        $optionwordarr = explode(',',$optionword);

            if($addoptionword !='')
            {
                $this->Smt_auto_draft_list_model->updateWord('option_keyword',$addoptionword,$sku);

                $addoptionwordarr = explode(',',$addoptionword);
                foreach($addoptionwordarr as $add)
                {
                    array_push($optionwordarr,$add);
                }
            }
        $optionwordarr=   array_filter($optionwordarr);
        $optionwordarr=   array_values($optionwordarr);

        $mustleng = count($mustwordarr);
        $optionleng = count($optionwordarr);

            //根据账号生成对应的数据
        foreach($accountarr as  $newaccount)
        {
            if(!empty($newaccount))
            {

                $mid_arr = $mustwordarr;

                $rand_num = mt_rand(2,$optionleng); //  插入次数


                $rand_keys = array_rand($optionwordarr, $rand_num);

                for($j=0;$j<$rand_num;$j++)
                {
                    $shishileng = count($mid_arr); //实时长度
                    $weizi  =  mt_rand(1,$shishileng); // 插入位置
                    array_splice($mid_arr,$weizi,0,$optionwordarr[$rand_keys[$j]]); //在指定位置插入值
                }


                $mid_arr = $mustwordarr;

                $rand_num = mt_rand(2,$optionleng); //  插入次数


                $rand_keys = array_rand($optionwordarr, $rand_num);

                for($j=0;$j<$rand_num;$j++)
                {
                    $shishileng = count($mid_arr); //实时长度
                    $weizi  =  mt_rand(1,$shishileng); // 插入位置
                    array_splice($mid_arr,$weizi,0,$optionwordarr[$rand_keys[$j]]); //在指定位置插入值
                }

                //   $lastTittleArr[] = $mid_arr;



                $sku_info =  $this->Smt_auto_draft_list_model->get_skus_by_productId($productid);


                $list_info =  $this->Smt_auto_draft_list_model->get_list_by_productId($productid);
                $list_info['token_id'] = $newaccount;
                array_shift($list_info);

                $detail_info =  $this->Smt_auto_draft_list_model->get_detail_by_productId($productid);
                $newfreightTemplateId = $this->getTemplateIdByToken_id($newaccount);
                if($newfreightTemplateId)
                {
                    $detail_info['freightTemplateId'] = $newfreightTemplateId;
                }

                if(count($mustwordarr)>2)
                {
                    $newmustword = array_rand($mustwordarr,2);

                    $detail_info['keyword'] = trim($mustwordarr[$newmustword[0]]);
                    $detail_info['productMoreKeywords1'] = trim($mustwordarr[$newmustword[1]]);
                }
                if(count($optionwordarr)>1)
                {
                    $newoptinword = array_rand($optionwordarr,1);
                    $detail_info['productMoreKeywords2'] = trim($optionwordarr[$newoptinword]);

                }

                $detail_info['relationProductIds']='';  //将自定义关联产品去除
                $detail_info['relationLocation']='';//将自定义关联产品去除
                $detail_info['sizechartId'] = -1; //复制的尺码都设置成1


                array_shift($detail_info);

                $productidold = $list_info['productId'];
                $num ='';

                $tittle = '';

                foreach($mid_arr as $v1)
                {
                    if(strlen($tittle)>115)
                    {
                        break;
                    }
                    $tittle = $tittle.' '.$v1;
                }
                $detail_info['detail_title'] = trim($tittle); //把新生成的标题放在自定义描述标题
                $list_info['subject'] = trim($tittle); //新的标题
                $suijishu = rand(1000, 9999);
                $list_info['productId'] = $productidold.'-'.$suijishu; //生成新的productid

                $checkresult =   $this->Smt_auto_draft_list_model->get_list_by_productId( $list_info['productId']);  //检查新生成的productid是否重复

                if(!empty($checkresult)) //不为空:及重复
                {
                    continue;
                }

                $detail_info['productId'] = $productidold.'-'.$suijishu;
                $this->db->trans_begin();

                foreach($sku_info as $v_sku )
                {
                    array_shift($v_sku);
                    $v_sku['productId'] = $productidold.'-'.$suijishu;
                    $v_sku['skuMark'] = $v_sku['productId'].':'.$v_sku['skuCode'];
                    if($sales_prefix !='')
                    {
                        if(strpos($v_sku['smtSkuCode'], '*'))
                        {
                            $mid =  explode('*',$v_sku['smtSkuCode']);
                            $v_sku['smtSkuCode'] =$sales_prefix.'*'.$mid[1];
                        }
                        else
                        {
                            $v_sku['smtSkuCode'] = $sales_prefix.'*'.$v_sku['smtSkuCode'];
                        }
                    }


                    $id1[] =    $this->Smt_auto_draft_list_model->inset_sku_info($v_sku);
                }

                $id2 =   $this->Smt_auto_draft_list_model->inset_list_info($list_info);

                $id3 =     $this->Smt_auto_draft_list_model->inset_datail_info($detail_info);


                if($id1&&$id2&&$id3)
                {
                    $this->db->trans_commit();
                }
                else
                {
                    $this->db->trans_rollback();
                }
            }
        }

        ajax_return('执行完成',1);

    }
    //保存必填，选填关键字
    public  function  updateListSkuWordInfo()
    {
        $productid = $this->input->get_post('productid');
        $type  = $this->input->get_post('type');
        $info  = $this->input->get_post('wordinfo');
        $arr[$type] = $info;
        $re = $this->Smt_auto_draft_list_model->updateInfoByProductid($productid,$arr);
        if($re==1)
        {
            ajax_return('更新成功',1);
        }
        else
        {
            ajax_return('更新失败',2);
        }
    }

    //生成空标题的草稿
    public function autoEmptyDraft($productid,$accountarr,$sales_prefix)
    {
        foreach($accountarr as $newaccount ) {
            if (!empty($newaccount))
            {

                $sku_info =  $this->Smt_auto_draft_list_model->get_skus_by_productId($productid);


                $list_info =  $this->Smt_auto_draft_list_model->get_list_by_productId($productid);
                $list_info['token_id'] = $newaccount;
                array_shift($list_info);

                $detail_info =  $this->Smt_auto_draft_list_model->get_detail_by_productId($productid);
                $newfreightTemplateId = $this->getTemplateIdByToken_id($newaccount);
                if($newfreightTemplateId)
                {
                    $detail_info['freightTemplateId'] = $newfreightTemplateId;
                }
                $detail_info['relationProductIds']='';  //将自定义关联产品去除
                $detail_info['relationLocation']='';//将自定义关联产品去除
                $detail_info['sizechartId'] = -1; //复制的尺码都设置成1
                array_shift($detail_info);

                $productidold = $list_info['productId'];

                $list_info['subject'] = ''; //新的标题
                $suijishu = rand(1000, 9999);
                $list_info['productId'] = $productidold.'-'.$suijishu; //生成新的productid

                $checkresult =   $this->Smt_auto_draft_list_model->get_list_by_productId( $list_info['productId']);  //检查新生成的productid是否重复

                if(!empty($checkresult)) //不为空:及重复
                {
                    continue;
                }

                $detail_info['productId'] = $productidold.'-'.$suijishu;
                $this->db->trans_begin();

                foreach($sku_info as $v_sku )
                {
                    array_shift($v_sku);
                    $v_sku['productId'] = $productidold.'-'.$suijishu;
                    $v_sku['skuMark'] = $v_sku['productId'].':'.$v_sku['skuCode'];
                    if($sales_prefix !='')
                    {
                        if(strpos($v_sku['smtSkuCode'], '*'))
                        {
                            $mid =  explode('*',$v_sku['smtSkuCode']);
                            $v_sku['smtSkuCode'] =$sales_prefix.'*'.$mid[1];
                        }
                        else
                        {
                            $v_sku['smtSkuCode'] = $sales_prefix.'*'.$v_sku['smtSkuCode'];
                        }
                    }


                    $id1[] =    $this->Smt_auto_draft_list_model->inset_sku_info($v_sku);
                }

                $id2 =   $this->Smt_auto_draft_list_model->inset_list_info($list_info);

                $id3 =     $this->Smt_auto_draft_list_model->inset_datail_info($detail_info);


                if($id1&&$id2&&$id3)
                {
                    $this->db->trans_commit();
                }
                else
                {
                    $this->db->trans_rollback();
                }
            }
        }
        return '生成了标题为空的新草稿，请重新筛选查看';

    }


    //检测相关词汇是否违规
    public function findAeProductProhibitedWords($checkeinfo)
    {

        $productProperties =array();
        if(!empty($checkeinfo['aeopAeProductPropertys']))
        {
            $productpropertys = unserialize($checkeinfo['aeopAeProductPropertys']);
            foreach($productpropertys as $pro)
            {
                foreach($pro as $v)
                {
                    if(is_string($v['attrValue']))
                    {
                        $productProperties[]=$v;
                    }
                }
            }
        }

        $keyword[]=$checkeinfo['keyword'];
        $keyword[]= $checkeinfo['productMoreKeywords1'];
        $keyword[]= $checkeinfo['productMoreKeywords2'];

        $categoryId = $checkeinfo['categoryId'];


        $title =  $checkeinfo['subject'];

        $detail =htmlspecialchars_decode($checkeinfo['detail']);
        $detail = trim(strip_tags($detail));


        $productProperties = json_encode($productProperties);

        $keywords=json_encode($keyword);
        $smt    = new MySmt();
        //获取账号的信息
        $tokenInfo = $this->Smt_user_tokens_model->getOneTokenInfo($checkeinfo['token_id']);
        $smt->setToken($tokenInfo);

        $api='api.findAeProductProhibitedWords';

        $pare ='categoryId='.rawurlencode($categoryId).'&title='.rawurlencode($title).'&keywords='.rawurlencode($keywords).'&productProperties='.rawurlencode($productProperties).'&detail='.rawurlencode($detail);


        $result = $smt->getJsonData($api,$pare);

        $rs = json_decode($result, true);


        if(isset($rs['productPropertiesProhibitedWords']))
        {
            $noproblem = true;
            $stringinfo = '';
            foreach($rs as $k=> $v)
            {
                if(!empty($v))
                {
                    foreach($v as $key=>$value)
                    {
                        $stringinfo=$stringinfo.'--'.$k.':'.$value['primaryWord'];
                        foreach($value['types'] as $v2)
                        {
                            $stringinfo=$stringinfo.':'.$v2;
                        }
                    }
                    $noproblem =false;
                }
            }
            if($noproblem)
            {
                return 'success';
            }
            else
            {
                return $stringinfo;
            }
        }
        else
        {
           if(isset($rs['error_message']))
           {
               return $rs['error_message'];
           }
            else
            {
                return '检查违禁词查失败';
            }
        }
    }

    //重新组装下产品属性 api有变化
    public function changeAeopAeProductPropertys($info){
        //  $info='a:11:{i:0;a:2:{s:10:"attrNameId";i:284;s:11:"attrValueId";s:9:"100006040";}i:1;a:2:{s:10:"attrNameId";i:200000221;s:11:"attrValueId";s:9:"200000579";}i:2;a:2:{s:10:"attrNameId";i:100005859;s:11:"attrValueId";s:3:"400";}i:3;a:2:{s:10:"attrNameId";i:200000171;s:11:"attrValueId";s:9:"200000449";}i:4;a:2:{s:10:"attrNameId";i:10;s:11:"attrValueId";s:4:"1523";}i:5;a:2:{s:10:"attrNameId";i:2;s:11:"attrValueId";s:1:"4";}i:6;a:2:{s:10:"attrNameId";i:2;s:9:"attrValue";s:3:"OEM";}i:7;a:2:{s:10:"attrNameId";i:326;s:11:"attrValueId";s:9:"200572192";}i:8;a:2:{s:10:"attrNameId";i:200000784;s:11:"attrValueId";s:4:"1534";}i:9;a:2:{s:10:"attrNameId";i:200000639;s:9:"attrValue";s:4:"45cm";}i:10;a:2:{s:10:"attrNameId";i:3;s:9:"attrValue";s:10:"Rhinestone";}}';
        $old_arr = unserialize($info); //
        $excessive_arr = array();
        foreach($old_arr as $k=>$v)
        {
            if(isset($v['attrNameId'])&&(isset($v['attrValueId'])))
                $excessive_arr[$k] = $v['attrNameId'];
        }
        if(!empty($excessive_arr))
        {
            foreach($excessive_arr as $k2=>$v2)
            {
                foreach($old_arr as $k=>$v)
                {
                    if($v2==$v['attrNameId']&&(isset($v['attrValue'])))
                    {
                        $old_arr_key = $k;
                        $old_arr[$k2]['attrValue'] = $old_arr[$old_arr_key]['attrValue'];
                        unset($old_arr[$old_arr_key]);
                    }
                }

            }
        }
        $old_arr = array_values($old_arr);
        return serialize($old_arr);
    }


    //待完善
    public function getSkuPirceByProductId()
    {
        $productid = $_POST['productid'];
     //   $productid = '32227159816-1-66063';
        $reinfo = array();
        $sku_info =  $this->Smt_auto_draft_list_model->get_skus_by_productId($productid);
        if(!empty($sku_info))
        {
            foreach($sku_info as $key=> $sku)
            {
                $reinfo[$key]['skuCode'] = $sku['skuCode'];
                $reinfo[$key]['skuPrice'] = $sku['skuPrice'];

            }
        }
        ajax_return('',1,$reinfo);

    }

    /**
     * smt调价任务列表
     */
    public function price_task_list(){

        $userInfo = $this->userToken->getSmtTokenList($option=array());//获取所有账号的用户信息

        $shipment = $this->Shipment_model-> getAll2Array($options=array());

        $shipmentarr= array();
        foreach($shipment as $ship)
        {
            $shipmentarr[$ship['shipmentID']] = $ship['shipmentTitle'];
        }
        $userInfoarr = array();
        foreach($userInfo as $us)
        {
            $userInfoarr[$us['token_id']] = $us['accountSuffix'];
        }



        $string = '';

        $per_page	= (int)$this->input->get_post('per_page');

        $cupage	= intval($this->config->item('site_page_num')); //每页显示个数

        $return_arr = array ('total_rows' => true );

        $where = array();

        $like = array();

        $orderBy = 'id DESC';

        //搜索
        $search_data = $this->input->get_post('search');

        $price_status='';//调价状态
        $Acc = '';//账号
        $productID = '';//产品ID
        $is_upload = '';//刊登状态

        //审核状态筛选
        if(isset($search_data['stauts']) && $price_status = trim($search_data['stauts'])){
            if($price_status !=1){
                $orderBy = 'create_time DESC';
            }
            $where['stauts'] = $price_status;
            $string .= '&search[stauts]='.$price_status;
        }

        //账号筛选
        if(isset($search_data['token_id']) && $Acc = trim($search_data['token_id'])){
            $where['token_id'] = $Acc;
            $string .= '&search[token_id]='.$Acc;
        }



        $options	= array(
            'page'		=> $cupage,
            'per_page'	=> $per_page,
            'where'     => $where,
            'like'		=> $like,
            'order'		=> $orderBy
        );

     //   var_dump($options);exit;
        $data_list = $this->smt_price_task_main_model->getAll($options, $return_arr); //查询所有信息

        $c_url='publish/smt';

        $url = admin_base_url('publish/smt/price_task_list?').$string;

        $page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );

        $search_data['account'] = $Acc;
        $search_data['price_status'] = $price_status;
     //   $search_data['productID'] = $productID;
     //   $search_data['parent_sku'] = $sku_search;
        $data = array(
            'userInfo'     		=> $userInfo,
            'data_list'   		=> $data_list,
            'page'    		    => $page,
            'price_status' 		=> $this->price_status,
            'price_type'		=> $this->price_type,
            'userInfo'			=> $userInfo,
            'shipment'         =>$shipment,
            'shipmentarr'    =>$shipmentarr,
            'userInfoarr'   =>$userInfoarr,
            'search'			=> $search_data
        );

        $this->_template('admin/publish/smt/price_task_list',$data);
    }

    public function creat_pirce_task()
    {

            $token_id =   $this->input->post('token_id');
            $selectshipment_id =   $this->input->post('selectshipment_id');
            $selectshipment_op = $this->input->post('shipment_op');
            $percentage =   $this->input->post('percentage');

            $is_re_pirce =   $this->input->post('is_re_pirce');
            $re_pirce =   $this->input->post('re_pirce');

            $groupId2= $this->input->post("groupId2");
     

            $task_main = array();
            $task_main['token_id'] =$token_id;
            $task_main['shipment_id'] =$selectshipment_id;
            $task_main['type'] =1;
            $task_main['shipment_id_op'] =$selectshipment_op;
            $task_main['percentage'] =$percentage;
            $task_main['re_pirce'] =$re_pirce;
            $task_main['stauts'] = 1;
            $task_main['group'] = $groupId2;
            $task_main['create_time'] =date('Y-m-d H:i:s',time());


      //  var_dump($task_main);exit;

        $main_id= $this->smt_price_task_main_model->add($task_main);

       if($main_id)
       {

           $options=array();
           $where['token_id'] =$token_id;
           $where['isRemove'] = 0;

           if(!empty($groupId2))
           {
               $where['groupId'] = $groupId2;
           }
           $where['productStatusType'] ='onSelling';
           $options['where'] = $where;

           $result = $this->Smt_product_list_model-> getAll2Array($options);



           if(!empty($result))
           {
               $i = 0;
               foreach($result as $re)
               {
                /*   if($i==10)
                   {
                       break;
                   }*/
                   $info = array();
                   $info['productID'] = $re['productId'];
                   $info['account'] = $token_id;
                   $info['status'] = 1;
                   $info['shipment_id'] = 0;
                   $info['main_id'] =$main_id;
                   $info['create_time'] = date('Y-m-d H:i:s',time());
                   $this->smt_price_task_model->save($info);
                   $i++;
                   // break;
               }
           }
       }

     //  echo $token_id.':'.$selectshipment_id.':'.$percentage.':'.$is_re_pirce.':'.$re_pirce;

        ajax_return('',1);

    }







    public  function getSmtPriceTask()
    {

        $ids = $this->input->post('Ids');
        $idarr = explode(',',$ids);

        foreach($idarr as $id)
        {
            $task_main_option= array();
            $task_main_option['where']['id'] = $id;
            $task_main_result =  $this->smt_price_task_main_model->getOne($task_main_option,true);

            $task_list_option = array();
            $task_list_option['where']['main_id'] =$id;
            $task_list_option['where']['status'] =1;
            $task_list_result = $this->smt_price_task_model->getAll2Array($task_list_option);
            if(!empty($task_list_result))
            {
                $smt    = new MySmt();


                foreach($task_list_result as $task)
                {
                    $token_info = $this->userToken->getOneTokenInfo($task_main_result['token_id']); //每次调用API 重新构造下，防止中途过期
                    $smt->setToken($token_info);

                    $api='api.findAeProductById';

                    $pare ='productId='.rawurlencode($task['productID']);

                    $result = $smt->getJsonData($api,$pare);

                    $rs = json_decode($result,true);
                    if (isset($rs['error_code'])) {   // 获取list的信息出问题
                        $error= array();
                        $optionarr = array();
                        $optionarr['id'] = $task['id'];
                        $error['status'] = 3;
                        $error['remark'] = $rs['error_message'];
                        $error['api_time'] = date('Y-m-d H:i:s',time());
                        $this->smt_price_task_model->update($error,$optionarr);

                        continue;
                    }


                    $aeopAeProductSKUs = array();

                    $is_break = false; //是不是跳过这个广告
                    foreach ($rs['aeopAeProductSKUs'] as $key => $v) {  //循环listing的SKU

                        $old_price =   $v['skuPrice'];
                        $sku = $this->removePrefix($v['skuCode']);

                        $newSkuPrice = $this->getPriceByProfit($sku,$task_main_result['percentage'],$task_main_result['shipment_id'],$task_main_result['shipment_id_op']);

                        if(isset($newSkuPrice['error'])&&($newSkuPrice['error']=='error'))
                        {
                            $is_break= true;
                            break;
                        }
                        $v['skuPrice'] =$newSkuPrice['price'];


                        if( $v['skuPrice']<$task_main_result['re_pirce'])
                        {
                            $v['skuPrice']=$task_main_result['re_pirce'];
                        }

                        /*if($v['skuPrice'] < $old_price*0.1) //  新的价格比原销售价格90% 还小。跳过执行这个
                        {
                            $is_break= true;
                        }*/
                        $v['skuPrice'] = (string)$v['skuPrice'];
                        $aeopAeProductSKUs[] = $v;
                    }

                    if($is_break) //跳过这个广告
                    {
                        $arr= array();
                        $optionarr = array();
                        $optionarr['id'] = $task['id'];
                        $arr['status'] = 3;
                        $arr['shipment_id'] = $newSkuPrice['shipment_id'];
                        $arr['re_pirce']  = $newSkuPrice['price'];
                        $arr['api_time'] = date('Y-m-d H:i:s',time());
                        $arr['remark'] = '新售价小于原售价10%,或者SKU有问题，未执行调价';
                        $this->smt_price_task_model->update($arr,$optionarr);
                        continue;
                    }
                    $rs['aeopAeProductSKUs'] = $aeopAeProductSKUs;


                    $arr = array();
                    $arr['productId']              = $rs['productId']; //*
                    $arr['subject']                = $rs['subject']; //*
                    $arr['categoryId']             = $rs['categoryId']; //*
                    $arr['detail']                 = $rs['detail']; //*
                    $arr['deliveryTime']           = $rs['deliveryTime']; //*
                    $arr['keyword']                = $rs['keyword']; //*
                    $arr['productMoreKeywords1']   = $rs['productMoreKeywords1'];
                    $arr['productMoreKeywords2']   = $rs['productMoreKeywords2'];
                    $arr['productPrice']           = $rs['productPrice']; //*
                    $arr['freightTemplateId']      = $rs['freightTemplateId']; //*
                    $arr['isImageDynamic']         = $rs['isImageDynamic'] == 1 ? 'true' : 'false';
                    $arr['imageURLs']              = $rs['imageURLs']; //*
                    $arr['productUnit']            = $rs['productUnit']; //*
                    $arr['packageType']            = ($rs['packageType'] ? 'true' : 'false'); //*
                    if($arr['packageType'])
                    {
                        $arr['lotNum']   = $rs['lotNum'];
                    }
                    $arr['packageLength']          = $rs['packageLength']; //*
                    $arr['packageWidth']           = $rs['packageWidth']; //*
                    $arr['packageHeight']          = $rs['packageHeight']; //*
                    $arr['grossWeight']            = $rs['grossWeight']; //*
                    $arr['wsValidNum']             = $rs['wsValidNum']; //*
                    $arr['isPackSell']             = $rs['isPackSell'];//新增的必要参数
                    $arr['reduceStrategy']        = $rs['reduceStrategy'];//
                    $arr['currencyCode']           = $rs['currencyCode']; //又增加需要的参数
                    $arr['aeopAeProductSKUs']      = json_encode( $rs['aeopAeProductSKUs']); //*
                    $arr['aeopAeProductPropertys'] = json_encode($rs['aeopAeProductPropertys']); //*



                    $product_json = $smt->getJsonDataUsePostMethod( "api.editAeProduct", $arr );

                    // json转数组
                    $updateresult = json_decode($product_json,true);
                  //  $updateresult= array();
                  //  $updateresult['success'] ='success';
                 //   var_dump($updateresult);
                    if(isset($updateresult['success']))
                    {
                        $arr= array();
                        $optionarr = array();
                        $optionarr['id'] = $task['id'];
                        $arr['status'] = 2;
                        $arr['shipment_id'] = $newSkuPrice['shipment_id'];
                        $arr['re_pirce']  = $newSkuPrice['price'];
                        $arr['api_time'] = date('Y-m-d H:i:s',time());
                        $this->smt_price_task_model->update($arr,$optionarr);
                    }
                    else{
                        $arr= array();
                        $optionarr = array();
                        $optionarr['id'] = $task['id'];
                        $arr['status'] = 3;
                        $arr['shipment_id'] = $newSkuPrice['shipment_id'];
                        $arr['re_pirce']  = $newSkuPrice['price'];
                        $arr['api_time'] = date('Y-m-d H:i:s',time());
                        $arr['remark'] = '未知错误';
                        if(isset($updateresult['error_message']))
                        {
                            $arr['remark'] = $updateresult['error_message'];
                        }
                        $this->smt_price_task_model->update($arr,$optionarr);
                    }
                }
            }
            $arr= array();
            $optionarr = array();
            $optionarr['id'] = $id;
            $arr['stauts'] =2;
            $this->smt_price_task_main_model->update($arr,$optionarr);
        }
        ajax_return('执行完成',1);
    }

    // pFit【利润率】= (1- ( ((cost【成本价】 + shipFee【运费】) / exchangeRate【汇率1】 + platFee【固定费0】)/price + platFeeRate【费率5%】 / 100 ) ) * 100

    public function getPriceByProfit($sku,$profitRate,$shipmentId,$shipment_id_op)
   // public function getPriceByProfit()
    {
       // $sku = 'E3205A4';

     //   $profitRate = 20;

      //  $shipmentId= 182;
        $skuinfo = $this->smt_price_task_model->getSkuInfo($sku);

        if(empty($skuinfo)) //没有找到这个SKU 的信息
        {
            $arr=array();
            $arr['error'] = 'error';
            return $arr;
        }

        $currency_value = $this->smt_price_task_model->getExchangeRateByType();

        $shipment_id = $shipmentId;

        if(($skuinfo['products_with_battery']==1)||($skuinfo['products_with_fluid']==1)||($skuinfo['products_with_powder']==1))
        {
            $shipment_id = $shipment_id_op;
        }

        $shipmentInfo = $this->smt_price_task_model->getShipFee($shipment_id);

        $platFeeRate  =  $this->smt_price_task_model->getProductsSalePlatList(5);

        $weight =$skuinfo['products_weight'];

        $cost = $skuinfo['products_value'];

        if($shipmentInfo['shipmentCalculateMethod'] =='sangeweight' )
        {
            $arr = unserialize($shipmentInfo['shipmentSangeCalculateElementArray']);

            foreach($arr as $v1)
            {
                if($weight >$v1['start'] && $weight <= $v1['end'])
                {

                    $shipFee =   $v1['operational'];

                }
            }

        }
        else
        {
            $shipmentCalculateElementArray = unserialize($shipmentInfo['shipmentCalculateElementArray']);


            //运费 = 首重费用 + {[总重 - 首重] ÷ 续重} * 续重费用 + 操作费
            $firstFee         = $shipmentCalculateElementArray['first']['feeTax'];
            $firstWeight      = $shipmentCalculateElementArray['first']['unit'];
            $additionalFee    = $shipmentCalculateElementArray['additional']['feeTax'];
            $additionalWeight = $shipmentCalculateElementArray['additional']['unit'];
            $operateFee       = $shipmentCalculateElementArray['operational'];
            $shipFee = $firstFee + ceil(($weight - $firstWeight) / $additionalWeight) * $additionalFee + $operateFee;
        }

        $exchangeRate = 1; // 速卖通是美元，汇率为1

        $platFee = 0;

        $price= ((($cost + $shipFee) / $exchangeRate + $platFee)/(1- $profitRate/100-$platFeeRate/100))/$currency_value;

        $arr = array();
        $arr['price'] = round($price, 2);
        $arr['shipment_id']  = $shipment_id;

        return $arr;
    }

    public  function  removePrefix($sku)
    {

        if(strpos($sku,'*'))
        {
            $mod = explode('*',$sku);
            if(strpos($mod[1],'#'))
            {
                $last = explode('#',$mod[1]);
                $sku = $last[0];
            }
            else
            {
                $sku = $mod[1];
            }
        }
        else
        {
            $mod = explode('#',$sku);
            $sku = $mod[0];
        }
        return $sku;
    }

    /**
     * 批量删除
     */
    public function batch_delete_price(){
        $IDs = $this->input->post('Ids');
        $IDArr = array();//存放要删除的ID
        $IDArr = explode(',',$IDs);
        $msg = '';
        foreach($IDArr as $id){
            $option = array();
            $option['where'] = array('id'=>$id);
            $data = $this->smt_price_task_main_model->getOne($option,true);
            if($data['stauts']!=1){
                $msg .= "{$data['id']}的记录已经执行，不允许删除<br/>";
                continue;
            }
            $result = $this->smt_price_task_main_model->delete($option);
            if($result){
                $msg .= "{$data['id']}的记录已经删除<br/>";
            }else{
                $msg .= "{$data['id']}的记录删除失败<br/>";
            }
        }

        ajax_return($msg,true);

    }

    public  function  getskuinfo()
    {
        $sku = $this->input->post('sku');
        $option=array();
        $option['where']['productsIsActive']=1;
        $this->db->like('products_sku', $sku, 'after');
      //  $option['like']['products_sku'] = $sku;
        $resukt = $this->Products_data_model->getAll2Array($option);
        $returnarr = array();
        $returnarr['weight'] = $resukt[0]['products_weight'];
        $returnarr['length'] = 0;
        $returnarr['width'] = 0;
        $returnarr['height'] = 0;
        if(!empty($resukt[0]['products_volume']))
        {
            $volume = unserialize($resukt[0]['products_volume']);
            $returnarr['length'] = $volume['ap']['length'];
            $returnarr['width'] = $volume['ap']['width'];
            $returnarr['height'] = $volume['ap']['height'];
        }
        $returnarr['products_html_mod'] = htmlspecialchars_decode($resukt[0]['products_html_mod']);

        ajax_return('',1,$returnarr);

    }
    public function export_task()
    {

        $ids = $this->input->post('selectid');
        $idarr = explode(',',$ids);

        $option['where']['main_id'] = $idarr[0];

        $result = $this->smt_price_task_model->getAll2Array($option);

        $userInfo = $this->userToken->getSmtTokenList($option=array());//获取所有账号的用户信息

        $shipment = $this->Shipment_model-> getAll2Array($options=array());

        $shipmentarr= array();
        foreach($shipment as $ship)
        {
            $shipmentarr[$ship['shipmentID']] = $ship['shipmentTitle'];
        }
        $shipmentarr[0]='异常';
        $userInfoarr = array();
        foreach($userInfo as $us)
        {
            $userInfoarr[$us['token_id']] = $us['seller_account'];
        }




        $now_time = date('Y-m-d H:i:s',time());
        $filename ='smt调价任务'.$now_time;

        $phpExcel=new PHPExcel();

        $phpExcel->getProperties()->setCreator("ctos")
            ->setLastModifiedBy("ctos")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");


        $phpExcel->setActiveSheetIndex(0)->setCellValue('A1', 'productID');
        $phpExcel->setActiveSheetIndex(0)->setCellValue('B2', '账号');
        $phpExcel->setActiveSheetIndex(0)->setCellValue('C1', '物流渠道');
        $phpExcel->setActiveSheetIndex(0)->setCellValue('D1', '新售价');
        $phpExcel->setActiveSheetIndex(0)->setCellValue('E1', '状态（1-未执行，2-已执行，3-执行异常）');
        $phpExcel->setActiveSheetIndex(0)->setCellValue('F1', '执行时间');
        $phpExcel->setActiveSheetIndex(0)->setCellValue('G1', '失败原因');
        $i=2;
        foreach($result as $re)
        {
            $phpExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $re['productID']);
            $phpExcel->setActiveSheetIndex(0)->setCellValue('B'.$i,  $userInfoarr[$re['account']]);
            $phpExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $shipmentarr[$re['shipment_id']]);
            $phpExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $re['re_pirce'] );
            $phpExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $re['status']);
            $phpExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $re['api_time']);
            $phpExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, $re['remark']);
            $i++;
        }
        $phpExcel->getActiveSheet ()->setTitle ( 'smt_price_task' );

        $phpExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        header("Content-type:text/html;charset=utf-8");
        $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');

        $objWriter->save('php://output');

        exit;
    }

    public function draft_postProduct()
    {
        $productIds = $this->input->post('productIds');
        $productid = explode(',',$productIds);

        $this->postAeProduct($productid[0],true);

    }


    //根据
    public function getTemplateIdByToken_id($token_id)
    {
        $option =array();
        $option['where']['token_id'] = $token_id;
        $option['where']['default'] = 1;
        $result =   $this->Slme_smt_freight_template_model->getAll2Array($option);
        if(empty($result))
        {
            return false;
        }
        return $result[0]['templateId'];
    }
    //
    public function modifyActiveListtingFixesView()
    {
        $userInfo = $this->userToken->getSmtTokenList($option=array());//获取所有账号的用户信息
        $data=array(
            'token'=>$userInfo,
        );
        $this->_template('admin/smt/modify_fixes',$data);
    }

    public function modifyActiveListtingFixes()
    {
        $token_id = $this->input->post("token_id");
        $qianzhui = $this->input->post("qianzhui");
        $houzhui = $this->input->post("houzhui");
        $returnarr= array();
        $options=array();
        $where['token_id'] =$token_id;
        $where['productStatusType'] ='onSelling';
        $options['where'] = $where;
        $smt    = new MySmt();
        $token_info = $this->userToken->getOneTokenInfo($token_id);
        $smt->setToken($token_info);
        $result = $this->Smt_product_list_model-> getAll2Array($options);

        foreach($result as $re) {
            $api = 'api.findAeProductById';
            $pare = 'productId=' . rawurlencode($re['productId']);

            $result = $smt->getJsonData($api, $pare);

            $rs = json_decode($result, true);
            if (isset($rs['error_code'])) {
                $returnarr[] = $re['productId'];
                continue;
            }

            $aeopAeProductSKUs = array();
            foreach ($rs['aeopAeProductSKUs'] as $key => $v) {  //循环listing的SKU
                $sku =  explode("#",$v['skuCode']);
                $sku2 = explode("*",$sku[0]);
                if(isset($sku2[1]))
                {
                    $newsku = $sku2[1];
                }
                else
                {
                    $newsku = $sku2[0];
                }
                $lastsku = $qianzhui.'*'.trim($newsku).'#'.$houzhui;
                $v['skuCode'] =trim($lastsku);
                // $v['skuCode'] =substr($v['skuCode'], 1,strlen($v['skuCode'])-2);
                // $v['skuCode'] = $v['skuCode'].'#M7';
                $aeopAeProductSKUs[] = $v;
            }

            $rs['aeopAeProductSKUs'] = $aeopAeProductSKUs;



            $arr = array();
            $arr['productId']              = $rs['productId']; //*
            $arr['subject']                = $rs['subject']; //*
            $arr['categoryId']             = $rs['categoryId']; //*
            $arr['detail']                 = $rs['detail']; //*
            $arr['deliveryTime']           = $rs['deliveryTime']; //*
            $arr['keyword']                = $rs['keyword']; //*
            $arr['productMoreKeywords1']   = $rs['productMoreKeywords1'];
            $arr['productMoreKeywords2']   = $rs['productMoreKeywords2'];
            $arr['productPrice']           = $rs['productPrice']; //*
            $arr['freightTemplateId']      = $rs['freightTemplateId']; //*
            $arr['isImageDynamic']         = $rs['isImageDynamic'] == 1 ? 'true' : 'false';
            $arr['imageURLs']              = $rs['imageURLs']; //*
            $arr['productUnit']            = $rs['productUnit']; //*
            $arr['packageType']            = ($rs['packageType'] ? 'true' : 'false'); //*
            if($arr['packageType'])
            {
                $arr['lotNum']   = $rs['lotNum'];
            }
            $arr['packageLength']          = $rs['packageLength']; //*
            $arr['packageWidth']           = $rs['packageWidth']; //*
            $arr['packageHeight']          = $rs['packageHeight']; //*
            $arr['grossWeight']            = $rs['grossWeight']; //*
            $arr['wsValidNum']             = $rs['wsValidNum']; //*
            $arr['isPackSell']             = $rs['isPackSell'];//新增的必要参数
            $arr['currencyCode']           = $rs['currencyCode'];
            if(isset($rs['reduceStrategy']))
            {
                $arr['reduceStrategy']        = $rs['reduceStrategy'];//
            }
            $arr['aeopAeProductSKUs']      = json_encode( $rs['aeopAeProductSKUs']); //*
            $arr['aeopAeProductPropertys'] = json_encode($rs['aeopAeProductPropertys']); //*



                $product_json = $smt->getJsonDataUsePostMethod( "api.editAeProduct", $arr );

              $updateresult = json_decode($product_json,true);


            if(isset($updateresult['success']))
            {

            }
            else
            {
                $returnarr[] = $re['productId'];
            }

        }
        ajax_return('',1,$returnarr);
    }




    public function picCurl($url)
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
    // $type= 1 取实拍图片 $type=2  取链接图
    public function uploadBankImageNewAll($dirName,$type)
    {
        $url='';
        if($type==1)
        {
            //$url ='http://120.24.100.157:3000/api/sku/'.$dirName.'?include-sub=true&distinct=true&tags=photo';
            $url = 'http://120.24.100.157:70/getSkuImageInfo/getSkuImageInfo.php?tags=photo&distinct=true&include_sub=true&sku='.$dirName;
        }
        if($type==2)
        {
            //$url ='http://120.24.100.157:3000/api/sku/'.$dirName.'?include-sub=true&distinct=true&tags=link';
            $url = 'http://120.24.100.157:70/getSkuImageInfo/getSkuImageInfo.php?tags=link&distinct=true&include_sub=true&sku='.$dirName;

        }
        
/**/
        $result =$this->picCurl($url);

        $result = json_decode($result,true);

		$return_pic_array=array();
		
        if(!empty($result)){
        	foreach($result as $ke => $v){
        		//added by andy.
	          	$photo_name = $v['filename'];
	          	$s_url = 'http://120.24.100.157:70/getSkuImageInfo/getSkuImage.php?id='.$photo_name;
	            $return_pic_array[$ke]['url'] = $s_url;
	            $return_pic_array[$ke]['name'] = $s_url;
        	}
        }
        
        
        return $return_pic_array;
        /**/
        
        
        
        
        
        
        $pic_array= array();
        if(!empty($result))
        {
            $i=0;
            foreach($result as $re)
            {
                $pic_array[$i]['url'] = $re['url'];
                $pic_array[$i]['name'] = $re['sku'];
                $i++;
            }
        }
        $return_pic_array=array();
        if(!empty($pic_array))
        {
            $j=0;
            foreach($pic_array as $pic)
            {
                $mid= str_replace("image", "image-resize/800x800x75", $pic['url']);
                $last = 'http://120.24.100.157:3000'.$mid;
                $return_pic_array[$j]['url'] = $last;
                $return_pic_array[$j]['name'] = $last;
                $j++;
            }
        }

        return $return_pic_array;
    }
    /**
     *  循环上传SKU图片
     * @param $img
     * @param $skuCode
     * @param $id:错误日志时需要用到
     * @return string
     */
    public function uploadBankImageNew($api,$img,$skuCode){

        $img_return = $this->smt->uploadBankImage($api, $img, $skuCode);
        $new_pic='';
        if (isset($img_return['success'])&& $img_return['success']=='true') {
            $new_pic = $img_return['photobankUrl'];
        } else { //失败了，写下日志吧

        }
        return $new_pic;
    }

    public function ajaxUploadDirImageByNewSys(){
        $token_id = $this->input->get_post('token_id');
        $dirName  = trim($this->input->get_post('dirName'));
        $type = $_GET['type'];

        if (empty($token_id) || empty($dirName)) {
            ajax_return('账号或者SKU不能为空', false);
        }

       $result =  $this->uploadBankImageNewAll($dirName,$type);
        if(empty($result))
        {
            ajax_return('未找到改SKU图片信息', false);
        }

        $api = 'api.uploadImage'; //上传到哪个图片接口

        $error   = array();

        //获取要上传到的账号信息
        $tokenInfo = $this->userToken->getOneTokenInfo($token_id);
        if (empty($tokenInfo)) {
            ajax_return('没有查找到账号信息', false);
        }

        $this->smt->setToken($tokenInfo);
        $last_array = array();
        foreach($result as $re)
        {
            $mid = $this->uploadBankImageNew($api,$re['url'],$re['name']);
            if(!empty($mid))
            {
                $last_array[] = $mid;
            }
            else
            {
                ajax_return('检查账号图片银行空间是否还有空余', false, $last_array);
            }
        }
        ajax_return($error, true, $last_array);
    }

}