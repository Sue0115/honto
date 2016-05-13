<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 速卖通广告、运费模板、服务模板、产品组同步及管理
 * @authors suwei
 * @date    2014-12-01 13:19:28
 * @version $Id$
 */
class Smt_product extends Admin_Controller
{
    protected $smt;
    protected $userToken;
    private $_product_statues_type = array(
        "onSelling",
        "offline",
        "auditing",
        "editingRequired"
    ); // 商品业务状态

    function __construct()
    {
        parent::__construct();
        $this->load->library('MySmt');
        $this->load->Model(array(
            'smt/Smt_user_tokens_model',
            'smt/Smt_product_list_model',
            'smt/Smt_product_skus_model',
            'smt/Smt_product_attributes_model',
            'smt/Slme_smt_freight_template_model',
            'smt/Slme_smt_service_template_model',
            'smt/Slme_smt_product_group_model',
            'smt/Smt_user_sale_code_model',
            'smt/Smt_product_detail_model',
            'sharepage',
            'smt/Slme_smt_product_module_model',
            'smt/Smt_auto_draft_list_model',
            'smt/Slme_smt_categorylist_model'
            //'smt/Slme_smt_product_list_draft_model',
            //'smt/Slme_smt_product_skus_draft_model',
            //'smt/Slme_smt_product_detail_draft_model'
        ));
        $this->smt       = new MySmt();
        $this->model     = $this->Smt_product_list_model;
        $this->userToken = $this->Smt_user_tokens_model;
    }
    
    /**
     * 同步单个产品 --新增和更新写一起了
     */
    public function synchronizationProduct(){
    	$productId = trim($this->input->get_post('productId'));
    	
    	if ($productId){
            $this->_handleSynchronizationData($productId);
    	}else {
    		ajax_return('产品ID:'.$productId.'不存在', false);
    	}
    }

    /**
     * 批量同步产品信息
     */
    public function batchSynchronizationProduct(){
        $productIds = trim($this->input->get_post('productIds'));

        if (!empty($productIds)){
            $productArr = explode(',', $productIds);
            $success = array();
            $error = array();
            foreach($productArr as $productId){
                $result = $this->_handleSynchronizationData($productId, false);
                if ($result['status']){
                    $success[] = "产品$productId 同步成功";
                }else {
                    $error[] = "产品$productId 同步失败,".$result['info'];
                }
            }
            $str = $success ? implode(';', $success).';' : '';
            $str .= implode(';', $error);
            ajax_return($str, true);
        }else {
            ajax_return('产品ID不存在,请先选择需要批量修改的产品', false);
        }
    }

    /**
     * 处理同步产品时的数据
     * @param $productId 产品ID
     * @param bool $isDieOut 是否结束退出
     * @return array 结束返回json信息，不然返回数组
     */
    private function _handleSynchronizationData($productId, $isDieOut=true){
        $rs = $this->model->getProductFields($productId, array('token_id'));
        $token_id = $rs['token_id'];

        $tokenInfo = $this->userToken->getOneTokenInfo($token_id);
        if (!$tokenInfo){
            if ($isDieOut) {
                ajax_return('没有找到产品:' . $productId . '对应的账号', false);
            }else {
                return array('status' => false, 'info' => '没有找到产品:' . $productId . '对应的账号');
            }
        }
        $this->smt->setToken($tokenInfo);


        $productInfo = $this->apiFindAeProductById($productId);
        if (!$productInfo){
            if ($isDieOut) {
                ajax_return('产品:' . $productId . '没有获取到对应的在线信息', false);
            }else {
                return array('status' => false, 'info' => '产品:' . $productId . '没有获取到对应的在线信息');
            }
        }

        //销售前缀
        $sale_code = $this->Smt_user_sale_code_model->getSalersPrefixList();

        //同步到数据库
        $list_data['product_url']         = "http://www.aliexpress.com/item/-/" . $productInfo['productId'] . ".html";
        $list_data['subject']             = addslashes(trim($productInfo['subject']));
        //$list_data['gmtCreate']           = $this->_formatSmtDateToDatetime($productInfo['gmtCreate']);
        //$list_data['gmtModified']         = $this->_formatSmtDateToDatetime($productInfo['gmtModified']);
        $list_data['wsOfflineDate']       = $this->_formatSmtDateToDatetime($productInfo['wsOfflineDate']);
        $list_data['multiattribute']      = (count($productInfo['aeopAeProductSKUs']) > 1 ? 1 : 0);
        $list_data['ownerMemberId']       = $productInfo['ownerMemberId'];
        $list_data['ownerMemberSeq']      = $productInfo['ownerMemberSeq'];
        $list_data['wsDisplay']           = $productInfo['wsDisplay'];
        $list_data['productStatusType']   = $productInfo['productStatusType'];
        $list_data['productPrice']        = $productInfo['productPrice'];
        $list_data['groupId']             = array_key_exists('groupId', $productInfo) ? $productInfo['groupId'] : 0; //产品分组
        $list_data['categoryId']          = $productInfo['categoryId'];
        $list_data['packageLength']       = $productInfo['packageLength'];
        $list_data['packageWidth']        = $productInfo['packageWidth'];
        $list_data['packageHeight']       = $productInfo['packageHeight'];
        $list_data['grossWeight']         = $productInfo['grossWeight'];
        $list_data['deliveryTime']        = $productInfo['deliveryTime'];
        $list_data['wsValidNum']          = $productInfo['wsValidNum'];
        $list_data['synchronizationTime'] = date('Y-m-d H:i:s'); //同步时间
        $list_data['isRemove']            = '0';
        $list_data['multiattribute']      = count($productInfo['aeopAeProductSKUs']) > 1 ? 1 : 0;

        //获取广告存在标识
        $pl_rs     = $this->model->getProductFields($productInfo['productId'], 'productId, user_id');
        $isExists  = false;
        $oldUserId = 0;
        if (!empty($pl_rs)){ //存在了
            $isExists = true;
            $oldUserId = $pl_rs['user_id'];
        }
        //$isExists        = $this->model->check_product_is_exists($productInfo['productId']);

        $old_smtsku_list = array(); //旧的smtSKU列表
        if ($isExists) { //存在的，就把对应的smtSKU读出来，看SKU是否有新增或删除
            $old_smtsku_list = $this->Smt_product_skus_model->getProductSkuList($productInfo['productId']);

            $onlineSmtSkuList = array();
            foreach ($productInfo['aeopAeProductSKUs'] as $sku_list) {
                $onlineSmtSkuList[]  = strtoupper(trim($sku_list['skuCode']));
            }

            //已删除但是本地还存在的SMTSKUCODE
            $deletedSmtSkuList = array_diff($old_smtsku_list, $onlineSmtSkuList);
            if ($deletedSmtSkuList){
                $condition = array(
                    'where' => array('productId' => (string)$productInfo['productId']),
                    'where_in' => array('smtSkuCode' => $deletedSmtSkuList)
                );
                $this->Smt_product_skus_model->delete($condition);
            }
        }

        $userId = 0; //新的销售ID，上边的是之前按账号的

        $maxPrice = 0;
        $minPrice = $productInfo['aeopAeProductSKUs'][0]['skuPrice']; //最小值
        $smtSkuCodeArr = array(); //skucode数组
        //判断广告所属销售人员
        foreach ($productInfo['aeopAeProductSKUs'] as $sku_list) {

            if (!$userId) { //直到获取到销售人员ID
                $prefix = get_skucode_prefix($sku_list['skuCode']);
                if ($prefix && array_key_exists($prefix, $sale_code)) {
                    $userId = $sale_code[$prefix]['user_id'];
                }
            }

            // 速卖通SKU取值
            $smtSkuCode        = trim($sku_list['skuCode']);

            $sku_arr           = array();
            if ($smtSkuCode) {
                $sku_arr = buildSysSku($smtSkuCode);
            }

            if ($sku_arr) {
                $smtSkuCodeArr[] = strtoupper($smtSkuCode);
                //判断是否存在海外仓属性SKU --海外仓属性ID， 0的话说明不是海外仓的吧
                $valId = checkProductSkuAttrIsOverSea($sku_list['aeopSKUProperty']);

                foreach ($sku_arr as $sku_new) {

                    //判断该广告的SKU是否存在
                    $isSkuExists = $this->Smt_product_skus_model->checkProductAndSmtSkuCodeIsExists($productInfo['productId'], $smtSkuCode, $sku_new, $valId);

                    $maxPrice = $maxPrice > $sku_list['skuPrice'] ? $maxPrice : $sku_list['skuPrice'];
                    $minPrice = $minPrice < $sku_list['skuPrice'] ? $maxPrice : $sku_list['skuPrice'];

                    // 统计利润率
                    $profitRate                              = 0; //暂时不计算
                    $sku_data                                = array();
                    $sku_data['skuMark']                     = $productInfo['productId'] . ($sku_new ? ":" . $sku_new : '');
                    $sku_data['skuCode']                     = $sku_new;
                    $sku_data['skuPrice']                    = $sku_list['skuPrice'];
                    $sku_data['skuStock']                    = $sku_list['skuStock'];
                    $sku_data['propertyValueId']             = (isset($sku_list['aeopSKUProperty'][0]['propertyValueId']) ? $sku_list['aeopSKUProperty'][0]['propertyValueId'] : 0);
                    $sku_data['skuPropertyId']               = (isset($sku_list['aeopSKUProperty'][0]['skuPropertyId']) ? $sku_list['aeopSKUProperty'][0]['skuPropertyId'] : 0);
                    $sku_data['propertyValueDefinitionName'] = (isset($sku_list['aeopSKUProperty'][0]['propertyValueDefinitionName']) ? $sku_list['aeopSKUProperty'][0]['propertyValueDefinitionName'] : null);
                    //$sku_data['profitRate']                  = $profitRate;
                    $sku_data['synchronizationTime']         = date('Y-m-d H:i:s');
                    $sku_data['isRemove']                    = '0';
                    $sku_data['aeopSKUProperty']             = $sku_list['aeopSKUProperty'] ? serialize($sku_list['aeopSKUProperty']) : '';
                    $sku_data['ipmSkuStock']                 = $sku_list['ipmSkuStock']; //库存
                    $sku_data['overSeaValId']                = $valId;
                    $sku_data['updated']                     = 1;

                    if ($isSkuExists) { //存在了就变更下吧
                        $where['where'] = array('productId' => (string)$productInfo['productId'], 'smtSkuCode' => $smtSkuCode, 'skuCode' => $sku_new, 'overSeaValId' => $valId);
                        $this->Smt_product_skus_model->update($sku_data, $where);
                    } else {
                        $sku_data['productId']  = $productInfo['productId'];
                        $sku_data['smtSkuCode'] = $smtSkuCode;
                        $this->Smt_product_skus_model->add($sku_data);
                    }
                    unset($sku_data);
                }
            }
        }

        //删除未更新的广告
        $this->Smt_product_skus_model->delete(array('where' => array('productId' => (string)$productInfo['productId'], 'updated' => 0)));

        //把修改的状态变更回来
        $newData            = array();
        $newData['updated'] = 0;
        $this->Smt_product_skus_model->update($newData, array('where' => array('productId' => (string)$productInfo['productId'])));


        $smtSkuCodeArr = array_unique($smtSkuCodeArr);
        if ($list_data['multiattribute'] == 1 && count($smtSkuCodeArr) == 1){ //本来是多属性，但是sku都是重复的，当作单属性处理
            $list_data['multiattribute'] = 0;
        }

        if ($oldUserId <= 0 && $userId > 0){ //旧的不存在 同时 新的用户ID存在
            $list_data['user_id'] = $userId; //新ID存在就用新的，不然就是旧的
        }

        $list_data['productMinPrice']     = $minPrice;
        $list_data['productMaxPrice']     = $maxPrice;
        //处理广告信息
        if ($isExists) {
            $list_where['where'] = array('productId' => (string)$productInfo['productId']);
            $this->model->update($list_data, $list_where);
        } else {
            $product_list_data['token_id']  = $token_id;
            $product_list_data['productId'] = $productInfo['productId'];
            $this->model->add($list_data);
        }

        //处理广告详情信息
        $detail_data['aeopAeProductPropertys'] = !empty($productInfo['aeopAeProductPropertys']) ? serialize($productInfo['aeopAeProductPropertys']) : '';
        $detail_data['imageURLs']              = $productInfo['imageURLs'];
        $detail_data['detail']                 = htmlspecialchars($productInfo['detail']);
        $detail_data['keyword']                = $productInfo['keyword'];
        //关键字1
        $detail_data['productMoreKeywords1'] = array_key_exists('productMoreKeywords1', $productInfo) ? $productInfo['productMoreKeywords1'] : '';

        //关键字2
        $detail_data['productMoreKeywords2'] = array_key_exists('productMoreKeywords2', $productInfo) ? $productInfo['productMoreKeywords2'] : '';

        //单位ID
        $detail_data['productUnit']             = $productInfo['productUnit'];
        //运费模板ID
        $detail_data['freightTemplateId']       = $productInfo['freightTemplateId'];
        $detail_data['isImageDynamic']          = $productInfo['isImageDynamic'] ? 1 : 0;
        $detail_data['isImageWatermark']        = $productInfo['isImageWatermark'] ? 1 : 0;
        //每包件数
        $detail_data['lotNum']                  = $productInfo['lotNum'];
        //批发最小数量
        $detail_data['bulkOrder']               = array_key_exists('bulkOrder', $productInfo) ? $productInfo['bulkOrder'] : 0;

        //打包销售
        $detail_data['packageType']             = $productInfo['packageType'] ? 1 : 0;
        //自定义记重
        $detail_data['isPackSell']              = $productInfo['isPackSell'] ? 1 : 0;
        //批发折扣
        $detail_data['bulkDiscount']            = array_key_exists('bulkDiscount', $productInfo) ? $productInfo['bulkDiscount'] : 0;
        //服务模板
        $detail_data['promiseTemplateId']       = $productInfo['promiseTemplateId'];
        //尺寸模板
        $detail_data['sizechartId']             = array_key_exists('sizechartId', $productInfo) ? $productInfo['sizechartId'] : 0;
        //产品来源
        $detail_data['src']                     = array_key_exists('src', $productInfo) ? $productInfo['src'] : '';

        /**拆分自定义模板开始**/
        /**拆分自定义模板结束**/

        //判断详情是否存在
        $detailIsExists = $this->Smt_product_detail_model->check_detail_is_exists($productInfo['productId']);
        if ($detailIsExists) {
            $detail_where['where'] = array('productId' => (string)$productInfo['productId']);
            $this->Smt_product_detail_model->update($detail_data, $detail_where);
        } else {
            $detail_data['productId'] = $productInfo['productId'];
            $this->Smt_product_detail_model->add($detail_data);
        }

        unset($list_data);
        unset($productInfo);
        unset($detail_data);

        if ($isDieOut) {
            ajax_return('产品:' . $productId . '同步成功', true);
        }else {
            return array('status' => true, 'info' => '产品:' . $productId . '同步成功');
        }
    }

    /**
     * 计算速卖通广告利润
     * @param [type] $productId [description]
     * @param [type] $sku       [description]
     * @param [type] $price     [description]
     */
    private function _setProfitRate($productId, $sku, $price)
    {
        $token_id = $this->_token_id;
        $skuCode  = $sku;
        if (!empty($sku)) {
            // 模糊匹配SKU
            $sql0    = 'SELECT `products_sku`,`products_value`, `products_weight`, `products_with_battery`, products_with_fluid, products_with_powder, `products_sort` FROM erp_products_data WHERE products_sku = "' . $sku . '"';
            $rs_skus = $this->_db->doSelect($sql0);
            if ($rs_skus) {
                // 对结果进行遍历
                foreach ($rs_skus as $val) {
                    // 计算利润率
                    if ($val['products_value']) {
                        $profitRate = $this->getProfitRate($price, $val['products_value'], $token_id, $val['products_weight'], $val['products_sort'], $val['products_with_battery'], $val['products_with_fluid'], $val['products_with_powder']);
                        break;
                    }
                }
            } else {
                $profitRate = NULL;
            }
        }

        return $profitRate;
    }

    /**
     * 速卖通时间处理方法
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    private function _formatSmtDateToDatetime($str)
    {
        return date('Y-m-d H:i:s', strtotime(substr($str, 0, 14)));
    }

    /**
     * SMT商品列表查询
     * @param  string $product_statues_type [description]
     * @param  integer $page_size [description]
     * @param  integer $current_page [description]
     * @return [type]                        [description]
     */
    public function findProductInfoListQuery($product_statues_type = "onSelling", $page_size = 100, $current_page = 1)
    {
        $product_json = $this->smt->getJsonData("api.findProductInfoListQuery", "productStatusType=" . $product_statues_type . "&pageSize=" . $page_size . "&currentPage=" . $current_page);
        $result       = json_decode($product_json, true);

        return $result['success'] ? $result : false;
    }

    /**
     * 获取一个产品的在线信息
     * @param  [type] $productId [description]
     * @return [type]            [description]
     */
    public function apiFindAeProductById($productId)
    {
        if ($productId) {
            $api    = 'api.findAeProductById';
            $result = $this->smt->getJsonData($api, 'productId=' . $productId);
            $data   = json_decode($result, true);

            return (array_key_exists('success', $data) && $data['success']) ? $data : false;
        }
    }

    /**
     * 速卖通listing列表管理
     * @return [type] [description]
     */
    public function productManage()
    {

        $key = $this->user_info->key;

        $uid = $this->user_info->id;

        //速卖通账号列表查询条件
        $smt_where = array('token_status' => 0);

        $smt_user_options = array(
            'select' => 'token_id, seller_account,accountSuffix',
            'where'  => $smt_where,
        );
        //速卖通账号
        $token_array = $this->userToken->getSmtTokenList($smt_user_options);
        //速卖通广告状态
        $smt_product_status = $this->_product_statues_type;

        $where    = array(); //查询条件
        $in       = array(); //in查询条件
        $like     = array(); //like查询条件
        $string   = array(); //URL参数
        $curpage  = (int)$this->config->item('site_page_num');
        $per_page = (int)$this->input->get_post('per_page');
        $search   = $this->input->get_post('search');

        $isRemove = 0; //默认没被删除的

        $group_list = array(); //产品分组列表
        //组装查询条件
        if ($search['token_id']) {
            $where['token_id'] = $search['token_id'];
            $string[]          = 'search[token_id]=' . $search['token_id'];

            //有选择账号，选下分组查询分组出来
            $group_list = $this->Slme_smt_product_group_model->getProductGroupList($search['token_id']);

            if (!empty($search['groupId'])){ //分组信息
                if ($search['groupId'] == 'none'){
                    $where['groupId'] = 0;
                }else {
                    //查询是否有子分组
                    $child_group = array(); //子分组ID
                    if (!empty($group_list[$search['groupId']]['child'])){ //说明是有子分组的
                        foreach ($group_list[$search['groupId']]['child'] as $row){
                            $child_group[] = $row['group_id'];
                        }
                    }
                    array_push($child_group, $search['groupId']);
                    $in['groupId'] = $child_group;
                }
                $string[]          = 'search[groupId]=' . $search['groupId'];
            }
        }
        if (isset($search['productId']) && $search['productId']) {
            $where['productId'] = trim($search['productId']);
            $string[]           = 'search[productId]=' . trim($search['productId']);
        }
        if ($search['productStatusType']) {
            if ($search['productStatusType'] == 'other'){
                $isRemove = 1; //查询被删除的
            }else {
                $where['productStatusType'] = $search['productStatusType'];
            }
            $string[]                   = 'search[productStatusType]=' . $search['productStatusType'];
        }else {
        	$in['productStatusType'] = $this->_product_statues_type;
        }
        if (isset($search['sku']) && trim($search['sku'])) { //按SKU查询
            $product_ids = $this->Smt_product_skus_model->getProductIdWithSku(trim($search['sku']), true);
            if ($product_ids) {
                $in['productId'] = $product_ids;
            } else {
                $in['productId'] = '0';
            }
            $string[] = 'search[sku]=' . trim($search['sku']);
        }

        //标题模糊查询
        if (!empty($search['subject']) && trim($search['subject'])){
            $like['subject'] = trim($search['subject']);
            $string[] = 'search[subject]=' . trim($search['subject']);
        }
        $search = $search ? $search : array();

        $where['isRemove'] = $isRemove; //默认只查询没被删除的

        //查询条件
        $options     = array(
            //'select'   => "{$this->model->_table}.*, s.*",
            'where'    => $where,
            'where_in' => $in,
            'page'     => $curpage,
            'per_page' => $per_page,
        );
        if (!empty($like)){
            $options = array_merge($options, array('like' => $like));
        }

        $return_data = array('total_rows' => true);
        $data_list   = $this->model->getAll($options, $return_data);

        //读取SKU出来
        $product_arr = array();
        foreach ($data_list as $item) {
            $product_arr[] = $item->productId;
        }
        $product_skus = $this->Smt_product_skus_model->getProductSkus($product_arr, true);

        $c_url = admin_base_url('smt/smt_product/productManage');
        $url   = $c_url . '?' . implode('&', $string);

        $page = $this->sharepage->showPage($url, $return_data['total_rows'], $curpage);

        $detail_list = $this->Smt_product_detail_model->getProductDetailsFields($product_arr, 'productId,imageURLs');

        $data = array(
            'token'              => $token_array,
            'smt_product_status' => $smt_product_status,
            'search'             => $search,
            'data_list'          => $data_list,
            'product_skus'       => $product_skus,
            'key'                => $key,
            'page'               => $page,
            'totals'             => $return_data['total_rows'],
            'c_url'              => $c_url,
            'detail_list'        => $detail_list,
            'group_list'         => $group_list
        );
        $this->_template('admin/smt/product_list', $data);
    }

    /**
     * 异步显示账号的产品分组信息
     */
    public function showAccountProductGroup(){
        $token_id = $this->input->get_post('token_id');
        if ($token_id){ //有账号信息
            //分组信息
            $group_list = $this->Slme_smt_product_group_model->getProductGroupList($token_id);
            $option_str = '<option value="">=所有分组=</option>';
            $option_str .= '<option value="none">未分组</option>';
            if (!empty($group_list)){
                foreach($group_list as $id => $item){
                    $option_str .= '<option value="'.$item['group_id'].'">'.$item['group_name'].'</option>';
                    if (!empty($item['child'])){

                        foreach ($item['child'] as $pid => $row){
                            $option_str .= '<option value="'.$row['group_id'].'">&nbsp;&nbsp;&nbsp;&nbsp;--'.$row['group_name'].'</option>';
                        }
                    }
                }
            }
            ajax_return('', true, $option_str);
        }

        ajax_return('请先选择账号', false);
    }

    /**
     * 复制产品到模板时显示的账号
     */
    public function showAccountToCopyProduct()
    {
        $options['select'] = array('token_id', 'seller_account','accountSuffix');
        $options['where']  = array('token_status' => 0);
        $account_list      = $this->userToken->getSmtTokenList($options);
        $data              = array(
            'account_list' => $account_list
        );
        $this->template('admin/smt/account_copy_product', $data);
    }

    /**
     * 复制广告成为草稿
     */
    public function copyListingToDraft()
    {
        //产品ID列表
        $productIds = $this->input->get_post('productIds');
        //账号列表
        $tokenIds = $this->input->get_post('tokenIds');

        $product_array = explode(',', $productIds);
        $token_array   = explode(',', $tokenIds);

        $flag  = false; //标识
        $error = array();
        foreach ($product_array as $productId) {
            //读取产品的数据
            //产品信息属性
            $list_info = $this->Smt_product_list_model->getProductListInfo($productId);
            
            if ($list_info) {
                //产品图片
                $detail_info = $this->Smt_product_detail_model->getProductDetailInfo($productId);

                //SKU属性 --2维数组
                //$sku_info = $this->Smt_product_skus_model->getProductSkuProperty($productId);
                $sku_info = $this->Smt_product_skus_model->getProductSkuInfoList($productId, 'productId, skuCode, smtSkuCode, skuPrice, skuStock, ipmSkuStock, aeopSKUProperty, overSeaValId');

                //各账号循环插入数据 --插入数据的时候，如果图片要保存的话，要把原tokenID保存下来
                foreach ($token_array as $token_id) {

                    $this->db->trans_begin();

                    /*********插入到草稿主表数据开始*********/
                    $newProductId = $list_info['productId'].'-'.$token_id.'-'.rand(10000, 99999);
                    $draft_product['token_id']      = $token_id;
                    $draft_product['old_token_id']  = $list_info['token_id'];
                    $draft_product['subject']       = $list_info['subject'];
                    $draft_product['productPrice']  = $list_info['productPrice'];
                    $draft_product['groupId']       = $list_info['groupId'];
                    $draft_product['categoryId']    = $list_info['categoryId'];
                    $draft_product['packageLength'] = $list_info['packageLength'];
                    $draft_product['packageWidth']  = $list_info['packageWidth'];
                    $draft_product['packageHeight'] = $list_info['packageHeight'];
                    $draft_product['grossWeight']   = $list_info['grossWeight'];
                    $draft_product['deliveryTime']  = $list_info['deliveryTime'];
                    $draft_product['wsValidNum']    = $list_info['wsValidNum'];
                    $draft_product['productStatusType']  = 'newData';
                    $draft_product['old_productId'] = $list_info['productId'];
                    $draft_product['productId']     = $newProductId;
                    /*********插入到草稿主表数据结束*********/
                    $id = $this->Smt_product_list_model->add($draft_product);

                    if (!$id) {
                        $error[] = $list_info['productId'] . ',tokenId:' . $token_id . '复制错误';
                        $this->db->trans_rollback();
                        continue;
                    }

                    ////变更下productId成ID-token
                    //$product['productId'] = $id.'-'.$token_id;
                    //$product['id'] = $id;
                    //$affacted = $this->Smt_product_list_model->update($product);
                    //if (!$affacted){ //没有变更成功
                    //	$error[] = $list_info['productId'].',tokenId:'.$token_id.'复制错误';
                    //	$this->db->trans_rollback();
                    //	continue;
                    //}
                    
                    /***************插入到草稿详情表数据开始******************/
                    $draft_detail['productId']              = $newProductId;
                    $draft_detail['aeopAeProductPropertys'] = $detail_info['aeopAeProductPropertys'];
                    $draft_detail['imageURLs']              = $detail_info['imageURLs'];
                    $detail                                 = htmlspecialchars_decode($detail_info['detail']);
                    $detail                                 = filterSmtRelationProduct($detail);//过滤关联产品
                    $draft_detail['detail']                 = htmlspecialchars($detail);
                    $draft_detail['keyword']                = $detail_info['keyword'];
                    $draft_detail['productMoreKeywords1']   = $detail_info['productMoreKeywords1'];
                    $draft_detail['productMoreKeywords2']   = $detail_info['productMoreKeywords2'];
                    $draft_detail['productUnit']            = $detail_info['productUnit'];
                    $draft_detail['isImageDynamic']         = $detail_info['isImageDynamic'];
                    $draft_detail['isImageWatermark']       = $detail_info['isImageWatermark'];
                    $draft_detail['lotNum']                 = $detail_info['lotNum'];
                    $draft_detail['bulkOrder']              = $detail_info['bulkOrder'];
                    $draft_detail['packageType']            = $detail_info['packageType'];
                    $draft_detail['isPackSell']             = $detail_info['isPackSell'];
                    $draft_detail['bulkDiscount']           = $detail_info['bulkDiscount'];
                    $draft_detail['promiseTemplateId']      = $detail_info['promiseTemplateId'];
                    $draft_detail['src']                    = 'isv';
                    $draft_detail['freightTemplateId']      = $detail_info['freightTemplateId'];
                    $draft_detail['templateId']             = $detail_info['templateId'];
                    $draft_detail['shouhouId']              = $detail_info['shouhouId'];
                    $draft_detail['detail_title']           = $detail_info['detail_title'];
                  //  $draft_detail['sizechartId']            = $detail_info['sizechartId'];
                    $draft_detail['detailPicList']          = $detail_info['detailPicList'];
                    $detailLocal                            = htmlspecialchars_decode($detail_info['detailLocal']);
                    $detailLocal                            = filterSmtRelationProduct($detailLocal);//过滤关联产品
                    $draft_detail['detailLocal']            = htmlspecialchars($detailLocal);

                    unset($detail);
                    /***************插入到草稿详情表数据结束******************/
                    $detail_id = $this->Smt_product_detail_model->add($draft_detail);
                    if (!$detail_id) {
                        $error[] = $list_info['productId'] . ',tokenId:' . $token_id . '详情复制错误';
                        $this->db->trans_rollback();
                        continue;
                    }

                    /***************插入到草稿SKU表数据开始******************/
                    $sku_flag = true;
                    foreach ($sku_info as $row) {
                        $draft_skus['productId']       = $newProductId;
                        $draft_skus['skuCode']         = $row['skuCode']; //这个需要处理下
                        $draft_skus['skuPrice']        = $row['skuPrice'];
                        $draft_skus['skuStock']        = $row['skuStock'];
                        $draft_skus['smtSkuCode']      = rebuildSmtSku($row['smtSkuCode']);
                        $draft_skus['skuMark']         = $draft_skus['productId'].':'.$row['skuCode'];
                        $draft_skus['aeopSKUProperty'] = $row['aeopSKUProperty']; //sku属性--注意可能含有图片
                        $draft_skus['ipmSkuStock']     = $row['ipmSkuStock'];
                        $draft_skus['overSeaValId']    = $row['overSeaValId'];
                        $sku_id                        = $this->Smt_product_skus_model->add($draft_skus);

                        if (!$sku_id) {
                            $sku_flag = false;
                            $error[] = $list_info['productId'] . ',tokenId:' . $token_id . 'SKU'.$draft_skus['skuCode'].'复制错误';
                            unset($draft_skus);
                            break;
                        }else {
                            unset($draft_skus);
                        }
                    }
                    /***************插入到草稿SKU表数据结束******************/

                    if ($sku_flag){
                    	$flag = true;
                        $this->db->trans_commit();
                    }else {
                        $this->db->trans_rollback();
                    }
                }
            }
            unset($detail_info);
            unset($sku_info);
            unset($list_info);
        }

        ajax_return('另存为草稿' . ($flag ? '成功' : '失败'), $flag, $error);
    }


    /**
     * 同步速卖通运费模板信息
     */
    public function getFreightTemplateList()
    {
        $token_id = $this->input->get_post('token_id');
        $return   = $this->input->get_post('return');
        $selected = $this->input->get_post('selected');
        if ($token_id) {
            $token_info  = $this->userToken->getOneTokenInfo($token_id);
            $token_array = array($token_info);
        } else {
            //速卖通账号列表查询条件
            $smt_user_options['where'] = array('token_status' => 0);
            $token_array               = $this->userToken->getSmtTokenList($smt_user_options);
        }

        $flag = false; //成功标识
        foreach ($token_array as $t) {
            $this->smt->setToken($t);

            $freight_list = $this->listFreightTemplate();

            if ($freight_list) { //模板信息
                foreach ($freight_list as $row) {
                    $freight_info = $this->getFreightSettingByTemplateQuery($row['templateId']);
                    $options      = array(
                        'token_id'           => $t['token_id'],
                        'templateId'         => trim($row['templateId']),
                        'templateName'       => trim($row['templateName']),
                        'default'            => ($row['default'] ? 1 : 0),
                        'freightSettingList' => serialize($freight_info['freightSettingList']),
                        'last_update_time'   => date('Y-m-d H:i:s')
                    );

                    $id = $this->Slme_smt_freight_template_model->checkTemplateIsExists($t['token_id'], $row['templateId']);
                    //判断下模板是否存在，存在暂时就PASS，不存在插入
                    if (!$id) {
                        $this->Slme_smt_freight_template_model->add($options);
                    } else {//更新下数据
                        $options['id'] = $id;
                        $this->Slme_smt_freight_template_model->update($options);
                    }

                    unset($freight_info);
                    unset($id);
                    unset($options);
                }
                //删除本地过期的运费模板
                $this->Slme_smt_freight_template_model->deleteExpiredFreightTemplate($t['token_id']);
                $flag = true;
            }
            unset($freight_list);
        }
        unset($token_array);
        if ($token_id && $flag && $return == 'data'){ //返回查询的数据，先决条件是同步成功
            $template_list = $this->Slme_smt_freight_template_model->getFreightTemplateList($token_id);
            $options = '';
            if ($template_list){
                foreach ($template_list as $template){
                    $options .= '<option value="'.$template['templateId'].'" '.($template['templateId'] == $selected ? 'selected="selected"' : '').'>'.$template['templateName'].'</option>';
                }
            }
            unset($template_list);
            ajax_return('', true, $options);
        }else {
            ajax_return('运费模板同步' . ($flag ? '成功' : '失败'), $flag);
        }
    }

    /**
     * 获取运费模板列表
     * @return [type] [description]
     */
    public function listFreightTemplate()
    {
        $api    = 'api.listFreightTemplate';
        $result = $this->smt->getJsonData($api, '', true); //现在需要签名了
        $data   = json_decode($result, true);

        return $data['success'] ? $data['aeopFreightTemplateDTOList'] : false;
    }

    /**
     * 获取运费模板详情
     * @param  [type] $templateId [description]
     * @return [type]             [description]
     */
    public function getFreightSettingByTemplateQuery($templateId)
    {
        $api    = 'api.getFreightSettingByTemplateQuery';
        $result = $this->smt->getJsonData($api, 'templateId=' . $templateId);
        $data   = json_decode($result, true);

        return $data['success'] ? $data : false;
    }

    /**
     * 运费服务模板管理
     * @return [type] [description]
     */
    public function freightManage()
    {
        $token_id = $this->input->get_post('token_id');

        $where = array();
        if ($token_id) {
            $where = array('token_id' => $token_id);
        }
        $option = array('where' => $where);
        $array  = null;
        //运费模板列表
        $freight = $this->Slme_smt_freight_template_model->getAll($option, $array, true);

        //速卖通账号列表查询条件
        $smt_where = array('token_status' => 0);

        $smt_user_options = array(
            'select' => 'token_id, seller_account,accountSuffix',
            'where'  => $smt_where,
        );
        $token_array      = $this->userToken->getSmtTokenList($smt_user_options);

        $data = array(
            'freight'  => $freight,
            'token'    => $token_array,
            'token_id' => $token_id
        );
        $this->_template('admin/smt/freight_list', $data);
    }

    /**
     * 同步产品服务模板
     * @return [type] [description]
     */
    public function getServiceTemplateList()
    {
        $token_id = $this->input->get_post('token_id');
        $return   = $this->input->get_post('return');
        $selected = $this->input->get_post('selected');
        if ($token_id) {
            $token_info  = $this->userToken->getOneTokenInfo($token_id);
            $token_array = array($token_info);
        } else {
            //速卖通账号列表查询条件
            $smt_user_options = array(
                'where' => array(
                    'token_status' => 0,
                ),
            );
            $token_array      = $this->userToken->getSmtTokenList($smt_user_options);
        }

        $flag = false;
        foreach ($token_array as $t) {
            $this->smt->setToken($t);
            $data = $this->queryPromiseTemplateById();
            if ($data) {
                //先把数据变更成过期的
                $oneData['last_update_time'] = date('Y-m-d H:i:s', strtotime('-2 day'));
                $this->Slme_smt_service_template_model->update($oneData, array('where' => array('token_id' => $t['token_id'])));
                foreach ($data as $row) {
                    $options['token_id']         = $t['token_id'];
                    $options['serviceID']        = $row['id'];
                    $options['serviceName']      = trim($row['name']);
                    $options['last_update_time'] = date('Y-m-d H:i:s');

                    $id = $this->Slme_smt_service_template_model->checkServiceTemplateIsExists($t['token_id'], $row['id']);
                    if (!$id) { //不存在就插入
                        $this->Slme_smt_service_template_model->add($options);
                    } else { //存在就UPDATE
                        $options['id'] = $id;
                        $this->Slme_smt_service_template_model->update($options);
                    }
                    unset($options);
                    unset($id);
                }
                //删除过期未同步的模板
                $this->Slme_smt_service_template_model->deleteExpiredServiceTemplate($t['token_id']);
                $flag = true;
            }
            unset($data);
        }
        unset($token_array);

        if ($token_id && $flag && $return == 'data'){
            $template_list = $this->Slme_smt_service_template_model->getServiceTemplateList($token_id);
            $options = '';
            if ($template_list){
                foreach ($template_list as $temp){
                    $options .= '<option value="'.$temp['serviceID'].'" '.($selected == $temp['serviceID'] ? 'selected="selected"' : '').'>'.$temp['serviceName'].'</option>';
                }
            }
            ajax_return('', true, $options);
        }else {
            ajax_return('服务模板同步' . ($flag ? '成功' : '失败'), $flag);
        }
    }

    /**
     * 获取产品服务模板
     * @param  [type] $templateId [description]
     * @return [type]             [description]
     */
    public function queryPromiseTemplateById($templateId = -1)
    {
        $api = 'api.queryPromiseTemplateById';

        $result = $this->smt->getJsonData($api, 'templateId=' . $templateId, true);
        $data   = json_decode($result, true);

        return $data['templateList'] ? $data['templateList'] : false;
    }

    /**
     * 服务模板列表管理
     * @return [type] [description]
     */
    public function serviceManage()
    {
        $token_id = $this->input->get_post('token_id');

        $where = array();
        if ($token_id) {
            $where = array('token_id' => $token_id);
        }
        $option = array('where' => $where);
        $array  = null;
        //服务模板列表
        $service = $this->Slme_smt_service_template_model->getAll($option, $array, true);

        //速卖通账号列表查询条件
        $smt_where = array('token_status' => 0);

        $smt_user_options = array(
            'select' => 'token_id, seller_account,accountSuffix',
            'where'  => $smt_where,
        );
        $token_array      = $this->userToken->getSmtTokenList($smt_user_options);

        $data = array(
            'service'  => $service,
            'token'    => $token_array,
            'token_id' => $token_id
        );
        $this->_template('admin/smt/service_list', $data);
    }

    //同步产品分组
    public function getProductGroup()
    {
        $token_id = $this->input->get_post('token_id');
        $return   = $this->input->get_post('return'); //用以判断需返回的数据
        $selected = $this->input->get_post('selected'); //选中的项
        if ($token_id) {
            $token_info  = $this->userToken->getOneTokenInfo($token_id);
            $token_array = array($token_info);
        } else {
            //速卖通账号列表查询条件
            $smt_user_options = array(
                'where' => array(
                    'token_status' => 0,
                ),
            );
            $token_array      = $this->userToken->getSmtTokenList($smt_user_options);
        }

        $flag = false; //是否同步成功
        foreach ($token_array as $t) {
            $this->smt->setToken($t);
            $data = $this->getProductGroupList();
            if ($data) {
                //先变更成过期的再说
                $oneData['last_update_time'] = date('Y-m-d H:i:s', strtotime('-2 day'));
                $this->Slme_smt_product_group_model->update($oneData, array('where' => array('token_id' => $t['token_id'])));

                foreach ($data as $row) {
                    $options['token_id']         = $t['token_id'];
                    $options['group_id']         = $row['groupId'];
                    $options['group_name']       = trim($row['groupName']);
                    $options['last_update_time'] = date('Y-m-d H:i:s');
                    //判断产品分组是否存在
                    $id = $this->Slme_smt_product_group_model->checkProductGroupIsExists($t['token_id'], $row['groupId']);
                    if (!$id) { //不存在插入
                        $this->Slme_smt_product_group_model->add($options);
                    } else { //存在就变更
                        $options['id'] = $id;
                        $this->Slme_smt_product_group_model->update($options);
                    }

                    if (array_key_exists('childGroup', $row)) { //含有子分组
                        foreach ($row['childGroup'] as $child) {
                            $rs['token_id']         = $t['token_id'];
                            $rs['group_id']         = $child['groupId'];
                            $rs['group_name']       = trim($child['groupName']);
                            $rs['parent_id']        = $row['groupId'];
                            $rs['last_update_time'] = date('Y-m-d H:i:s');

                            $cid = $this->Slme_smt_product_group_model->checkProductGroupIsExists($t['token_id'], $child['groupId']);
                            if (!$cid) {
                                $this->Slme_smt_product_group_model->add($rs);
                            } else {
                                $rs['id'] = $cid;
                                $this->Slme_smt_product_group_model->update($rs);
                            }
                            unset($rs);
                            unset($cid);
                        }
                    }
                    unset($options);
                    unset($id);
                }
                //删除过期的模板
                $this->Slme_smt_product_group_model->deleteExpiredProductGroup($t['token_id']);
                $flag = true;//同步成功
            }
            unset($data);
        }
        unset($token_array);

        if ($token_id && $flag && $return == 'data'){
            $groupList = $this->Slme_smt_product_group_model->getProductGroupList($token_id);
            $options = '';
            if ($groupList){
                foreach ($groupList as $group){
                    if (array_key_exists('child', $group)) {
                        $options .= '<optgroup label="'.$group['group_name'].'>';
                        foreach($group['child'] as $r):
                            $options .= '<option value="'.$r['group_id'].'" '.($selected == $r['group_id'] ? 'selected="selected"' : '').'>&nbsp;&nbsp;&nbsp;&nbsp;--'.$r['group_name'].'</option>';
                        endforeach;
                        $options .= '</optgroup>';
                    }else {
                        $options .= '<option value="'.$group['group_id'].'">'.$group['group_name'].'</option>';
                    }
                }
            }
            ajax_return('', true, $options);
        }else {
            ajax_return('产品分组同步'.($flag ? '成功' : '失败'), $flag);
        }
    }

    /**
     * 产品分组管理
     * @return [type] [description]
     */
    public function groupManage()
    {
        $token_id = $this->input->get_post('token_id');

        //产品分组
        $group = $this->Slme_smt_product_group_model->getProductGroupList($token_id);

        //速卖通账号列表查询条件
        $smt_where = array('token_status' => 0);

        $smt_user_options = array(
            'select' => 'token_id, seller_account,accountSuffix',
            'where'  => $smt_where,
        );
        $token_array      = $this->userToken->getSmtTokenList($smt_user_options);

        $data = array(
            'group'    => $group,
            'token'    => $token_array,
            'token_id' => $token_id
        );
        $this->_template('admin/smt/group_list', $data);
    }

    /**
     * 获取在线产品分组
     * @return [type] [description]
     */
    public function getProductGroupList()
    {
        $api    = 'api.getProductGroupList';
        $result = $this->smt->getJsonData($api, '', true);
        $data   = json_decode($result, true);

        return $data['success'] ? $data['target'] : false;
    }


    /**
     * 同步信息模板
     */
    public function getProductModuleList()
    {
        $token_id = $this->input->get_post('token_id');
        $return   = $this->input->get_post('return');
        $selected = $this->input->get_post('selected');
        if ($token_id) {
            $token_info  = $this->userToken->getOneTokenInfo($token_id);
            $token_array = array($token_info);
        } else {
            //速卖通账号列表查询条件
            $smt_user_options = array(
                'where' => array(
                    'token_status' => 0,
                ),
            );
            $token_array      = $this->userToken->getSmtTokenList($smt_user_options);
        }

        $flag         = false; //是否同步成功
        $moduleStatus = 'approved';
        foreach ($token_array as $t) {
            $this->smt->setToken($t);
            $data      = $this->findAeProductDetailModuleListByQurey($moduleStatus);
            $totalPage = $data['totalPage'];
            if ($data['success']) {
                //处理数据
                $this->_handleModuleList($data['aeopDetailModuleList'], $t['token_id']);
            }
            if ($totalPage > 1) { //循环处理后边的页
                for ($i = 2; $i <= $totalPage; $i++) {
                    $data2 = $this->findAeProductDetailModuleListByQurey($moduleStatus, $i);
                    if ($data2['success']) {
                        //处理数据
                        $this->_handleModuleList($data2['aeopDetailModuleList'], $t['token_id']);
                    }
                }
            }
            //删除过期的
            $this->Slme_smt_product_module_model->deleteExpiredModule($t['token_id']);
            $flag = true;
        }

        if ($token_id && $flag && $return == 'data'){
            $module_list = $this->Slme_smt_product_module_model->getModuleList($token_id, $moduleStatus);
            $options = '';
            if ($module_list){
                foreach ($module_list as $module){
                    $options .= '<option value="'.$module['module_id'].'" '.($module['module_id'] == $selected ? 'selected="selected"' : '').'>'.$module['module_name'].'</option>';
                }
            }
            ajax_return('', true, $options);
        }else {
            ajax_return('同步'.($flag ? '完成' : '失败').',若有疑问请联系IT', $flag);
        }
    }

    /**
     * 处理信息模板列表
     * @param unknown $moduleStatus
     * @param number $pageIndex
     * @param string $type
     */
    private function _handleModuleList($aeopDetailModuleList, $token_id)
    {
        if ($aeopDetailModuleList) {
            foreach ($aeopDetailModuleList as $row) {
                $options['token_id']         = $token_id;
                $options['module_id']        = $row['id'];
                $options['module_name']      = $row['name'];
                $options['module_type']      = $row['type'];
                $options['module_status']    = $row['status'];
                $options['aliMemberId']      = $row['aliMemberId'];
                $options['displayContent']   = htmlspecialchars($row['displayContent']);
                $options['moduleContents']   = htmlspecialchars($row['moduleContents']);
                $options['last_update_time'] = date('Y-m-d H:i:s');

                //判断是否存在，存在就变更，不存在就插入
                $id = $this->Slme_smt_product_module_model->checkModuleIsExists($token_id, $row['id']);
                if ($id) { //更新
                    $options['id'] = $id;
                    $this->Slme_smt_product_module_model->update($options);
                } else {
                    $this->Slme_smt_product_module_model->add($options);
                }
                unset($options);
                unset($id);
            }
        }
    }

    /**
     * 获取某个账号的信息模板列表
     * $moduleStatus:状态
     * $pageIndex:页码
     * $type:关联类型
     */
    protected function findAeProductDetailModuleListByQurey($moduleStatus, $pageIndex = 1, $type = '')
    {
        $api = 'api.findAeProductDetailModuleListByQurey';

        //信息模块状态
        $moduleStatus = $moduleStatus ? $moduleStatus : 'approved';
        //页码
        $pageIndex = $pageIndex > 1 ? $pageIndex : 1;

        $result = $this->smt->getJsonData($api, 'moduleStatus=' . $moduleStatus . '&pageIndex=' . $pageIndex . ($type ? '&type=' . $type : ''));
        $data   = json_decode($result, true);

        return $data;
    }

    /**
     * 根据模块id查询信息模块
     * @return mixed|multitype:
     */
    protected function findAeProductModuleById($moduleId)
    {
        $api = 'api.findAeProductModuleById';

        if ($moduleId) {
            $result = $this->smt->getJsonData($api, 'moduleId=' . $moduleId);

            return json_decode($result, true);
        } else {
            return array();
        }
    }

    /**
     * 产品信息模板管理
     */
    public function moduleManage()
    {
        $token_id = $this->input->get_post('token_id');
        $curpage  = (int)$this->config->item('site_page_num');
        $per_page = (int)$this->input->get_post('per_page');

        $where = array();
        $params = array();
        if ($token_id) {
            $where = array('token_id' => $token_id);
            $params['token_id'] = $token_id;
        }
        $option      = array(
            'where' => $where,
            'page'  => $curpage,
            'per_page' => $per_page
        );
        $return_data = array('total_rows' => true);
        //产品信息模板列表
        $module = $this->Slme_smt_product_module_model->getAll($option, $return_data, true);

        $c_url = admin_base_url('smt/smt_product/moduleManage');
        $url   = $c_url . '?' . http_build_query($params);
        $page  = $this->sharepage->showPage($url, $return_data['total_rows'], $curpage);

        //速卖通账号列表查询条件
        $smt_where = array('token_status' => 0);

        $smt_user_options = array(
            'select' => 'token_id, seller_account,accountSuffix',
            'where'  => $smt_where,
        );
        $token_array      = $this->userToken->getSmtTokenList($smt_user_options);

        $data = array(
            'module'   => $module,
            'token'    => $token_array,
            'token_id' => $token_id,
            'page'     => $page,
            'totals'   => $return_data['total_rows'],
            'c_url'    => $c_url
        );
        $this->_template('admin/smt/module_list', $data);
    }

    /**
     * 产品信息模板选择
     */
    public function moduleSelect(){
        $single      = $this->input->get_post('single');
        $token_id    = $this->input->get_post('token_id');
        $curpage     = (int)$this->config->item('site_page_num');
        $per_page    = (int)$this->input->get_post('per_page');
        $module_name = trim($this->input->get_post('module_name'));

        if ($token_id) {
            $where              = array('token_id' => $token_id);
            $params['single']   = $single;
            $params['token_id'] = $token_id;
            $like               = array();
            if ($module_name) {
                $like['like'] = array('module_name' => $module_name);
                $params['module_name'] = $module_name;
            }

            $option      = array(
                'where'    => $where,
                'page'     => $curpage,
                'per_page' => $per_page
            );
            $option      = array_merge($option, $like);
            $return_data = array('total_rows' => true);
            //产品信息模板列表
            $module = $this->Slme_smt_product_module_model->getAll($option, $return_data, true);

            $c_url = admin_base_url('smt/smt_product/moduleSelect');
            $url   = $c_url . '?' . http_build_query($params);
            $page  = $this->sharepage->showPage($url, $return_data['total_rows'], $curpage);

            $data = array(
                'token_id'    => $token_id,
                'module'      => $module,
                'page'        => $page,
                'module_name' => $module_name,
                'totals'      => $return_data['total_rows'],
                'c_url'       => $c_url,
                'single'      => $single
            );
            $this->template('admin/smt/module_select', $data);
        }
    }

    /**
     * 根据SKU模糊查询并推荐产品列表
     */
    public function recommendProductList(){
        $sku = trim($this->input->get_post('sku'));
        $data = array();
        if ($sku){
            $this->load->Model('products/Products_data_model');
            $data = $this->Products_data_model->getProductSkuList($sku);
        }

        ajax_return('', true, $data);
    }

    /**
     * 批量修改产品
     */
    public function batchModifyProducts(){
        $productIds = $this->input->get_post('operateProductIds');
        $from       = $this->input->get_post('from'); //判断数据来源

        $productList = array();
        $productDetail = array();
        if (!empty($productIds)) { //产品ID非空

            //获取产品信息并显示出来(图片，标题，关键词，单位，重量，尺寸，产品信息模块，服务模板，运费模板，零售价，产品id，分类id)
            $productIdArr = explode(',', $productIds);

            $token_id = $this->Smt_product_list_model->checkProductsInSameAccount($productIdArr);

            if (!$token_id){
                $data = array(
                    'error' => '选择的产品无账号或不在同一个账号，请重新选择'
                );
            }else {

                $productList   = $this->Smt_product_list_model->getProductsFields($productIdArr, 'productId, subject, categoryId, packageLength, packageWidth, packageHeight, grossWeight, productMinPrice, productMaxPrice, token_id');
                $productDetail = $this->Smt_product_detail_model->getProductDetailsFields($productIdArr, 'imageURLs, keyword, productMoreKeywords1, productMoreKeywords2, productUnit, freightTemplateId, promiseTemplateId, productId, packageType, lotNum');

                $this->load->model('smt/Slme_smt_unit_model');
                //单位列表
                $unitList = $this->Slme_smt_unit_model->getUnitList();
                //运费模板列表
                $freightList = $this->Slme_smt_freight_template_model->getFreightTemplateList($token_id);
                //服务模板列表
                $serveList = $this->Slme_smt_service_template_model->getServiceTemplateList($token_id);

                //产品分组
                $groupList = $this->Slme_smt_product_group_model->getProductGroupList($token_id);

                $data = array( //传过去的数据
                    'productIds'    => $productIds,
                    'productList'   => $productList,
                    'productDetail' => $productDetail,
                    'unitList'      => $unitList,
                    'freightList'   => $freightList,
                    'serveList'     => $serveList,
                    'groupList'     => $groupList,
                    'token_id'      => $token_id,
                    'from'          => $from
                );
            }
        }else {
            $data = array(
                'error' => '请先选择要修改的产品'
            );
        }

        $this->template('admin/smt/batch_modify', $data);
    }

    /**
     * 选择产品列表(用于关联产品)
     */
    public function selectRelationProducts(){

        //速卖通广告状态
        $smt_product_status = $this->_product_statues_type;

        $where    = array(); //查询条件
        $in       = array(); //in查询条件
        $string   = array(); //URL参数
        $curpage  = 40;
        $per_page = (int)$this->input->get_post('per_page');
        $group_id = $this->input->get_post('groupId');
        $token_id = $this->input->get_post('token_id');
        $productId = trim($this->input->get_post('productId'));
        $productStatusType = $this->input->get_post('productStatusType');
        $sku = trim($this->input->get_post('sku'));
        $subject = trim($this->input->get_post('subject'));


        $isRemove = 0; //默认没被删除的

        $group_list = array(); //产品分组列表

        $where['token_id']  = $token_id;
        $string['token_id'] = $token_id;

        //有选择账号，选下分组查询分组出来
        $group_list = $this->Slme_smt_product_group_model->getProductGroupList($token_id);

        if (isset($group_id) && $group_id != ''){ //分组信息
            if ($group_id == 'none'){
                $where['groupId'] = 0;
            }else {
                //查询是否有子分组
                $child_group = array(); //子分组ID
                if (!empty($group_list[$group_id]['child'])){ //说明是有子分组的
                    foreach ($group_list[$group_id]['child'] as $row){
                        $child_group[] = $row['group_id'];
                    }
                }
                array_push($child_group, $group_id);

                $in['groupId'] = $child_group;
            }
        }
        $string['groupId'] = $group_id;

        if (!empty($productId)) {
            $where['productId'] = $productId;
            $string['productId']           = $productId;
        }
        if (!empty($productStatusType)) {
            if ($productStatusType == 'other'){
                $isRemove = 1; //查询被删除的
            }else {
                $where['productStatusType'] = $productStatusType;
            }
            $string['productStatusType']                   = $productStatusType;
        }else {
            $in['productStatusType'] = $this->_product_statues_type;
        }
        if (!empty($sku)) { //按SKU查询
            $product_ids = $this->Smt_product_skus_model->getProductIdWithSku($sku, true);
            if ($product_ids) {
                $in['productId'] = $product_ids;
            } else {
                $in['productId'] = '0';
            }
            $string['sku'] = $sku;
        }
        $like = array();



        $where['isRemove'] = $isRemove; //默认只查询没被删除的

        //查询条件
        $options     = array(
            'select' => 'productId, subject, productStatusType',
            'where'    => $where,
            'where_in' => $in,
            'page'     => $curpage,
            'per_page' => $per_page,
        );
        // 新增的标题LIKE 查询
        if (!empty($subject)) { //按SKU查询
            $like['subject'] = $subject;
            $options['like']=$like;
            $string['subject'] = $subject;
        }

        $return_data = array('total_rows' => true);
        $data_list   = $this->model->getAll($options, $return_data);

        //var_dump($data_list);exit;

        //读取SKU出来
        $product_arr = array();
        foreach ($data_list as $item) {
            $product_arr[] = $item->productId;
        }

        $c_url = admin_base_url('smt/smt_product/selectRelationProducts');
        $url   = $c_url . '?' . http_build_query($string);

        $page = $this->sharepage->showPage($url, $return_data['total_rows'], $curpage);

        $detail_list = $this->Smt_product_detail_model->getProductDetailsFields($product_arr, 'productId,imageURLs');

        $data = array(
            'smt_product_status' => $smt_product_status,
            'data_list'          => $data_list,
            'page'               => $page,
            'totals'             => $return_data['total_rows'],
            'c_url'              => $c_url,
            'detail_list'        => $detail_list,
            'group_list'         => $group_list
        );


        $data = array_merge($data, $string);
        $this->template('admin/smt/relation_list', $data);
    }

    /**
     * 异步获取运费账号的运费模板列表
     */
    public function ajaxGetFreightTemplateList(){
        $token_id = $this->input->get_post('token_id');
        if (!empty($token_id)){
            $list = $this->Slme_smt_freight_template_model->getFreightTemplateList($token_id);
            if(!empty($list)){
                ajax_return('', true, $list);
            }
        }
        ajax_return('', false, '');
    }

    /**
     * 异步获取服务模板列表信息
     */
    public function ajaxGetServeTemplateList(){
        $token_id = $this->input->get_post('token_id');
        if (!empty($token_id)){
            $list = $this->Slme_smt_service_template_model->getServiceTemplateList($token_id);
            if(!empty($list)){
                ajax_return('', true, $list);
            }
        }
        ajax_return('', false, '');
    }

    /**
     * 异步获取除指定产品外的产品列表
     * 获取产品信息并显示出来(图片，标题，关键词，单位，重量，尺寸，产品信息模块，服务模板，运费模板，零售价，产品id，分类id， 是否打包销售，打包数量, 最小价格和最大价格)
     */
    public function ajaxGetProductListExceptProducts(){
        $params   = $this->input->post(); //传过去的数组信息
        $data     = array();
        $products = array();
        $pages    = 1;
        if (!empty($params)){
            $params['pageSize'] = 40;
            $returnArr = array('total_rows' => true);
            $fields      = 'productId, subject, categoryId, packageLength, packageWidth, packageHeight, grossWeight, productMinPrice, productMaxPrice';
            $productList = $this->Smt_product_list_model->getProductsList($params, $returnArr, $fields);

            if (!empty($productList)){
                $token_id = $params['token_id'];
                $this->load->model('smt/Slme_smt_unit_model');
                //单位列表
                $unitList = $this->Slme_smt_unit_model->getUnitList();
                //运费模板列表
                $freightList = $this->Slme_smt_freight_template_model->getFreightTemplateList($token_id);
                //服务模板列表
                $serveList = $this->Slme_smt_service_template_model->getServiceTemplateList($token_id);

                $detailFields = 'imageURLs, keyword, productMoreKeywords1, productMoreKeywords2, productUnit, freightTemplateId, promiseTemplateId, packageType, lotNum';
                foreach ($productList as $product){
                    $productDetail = $this->Smt_product_detail_model->getProductDetailInfo($product['productId'], $detailFields);

                    $img = '';
                    if (!empty($productDetail['imageURLs'])){
                        $temp = explode(';', $productDetail['imageURLs']);
                        $img = array_shift($temp);
                    }

                    $productDetail['img']              = $img;
                    $newProduct                        = array_merge($product, $productDetail);
                    $newProduct['templateName']        = $freightList[$productDetail['freightTemplateId']]['templateName'];
                    $newProduct['promiseTemplateName'] = $serveList[$productDetail['promiseTemplateId']]['serviceName'];
                    $newProduct['unitName'] = $unitList[$productDetail['productUnit']]['name'].' ('.$unitList[$productDetail['productUnit']]['name_en'].')';
                    $products[]                        = $newProduct;
                    unset($productDetail);
                }
            }
            $pages = ceil($returnArr['total_rows']/$params['pageSize']);
        }
        $data['products'] = $products;
        $data['pages']    = $pages;

        ajax_return('', true, $data);
    }

    /**
     * 异步提交修改的信息数据
     */
    public function ajaxEditPostProduct(){
        $posts = $this->input->post();

        if (!empty($posts) && $posts['changed'] == 'true') {
            $return = $this->Smt_product_list_model->postEditAeProduct($posts);
            echo json_encode($return);exit;
        }else {
            echo json_encode(array('status' => false, 'info' => '产品'.$posts['productId'].'无需变更'));exit;
        }
    }

    /**
     * 异步批量变更草稿数据，只会变更本地的产品信息
     */
    public function ajaxEditdraftData(){
        $posts = $this->input->post();

        if (!empty($posts) && $posts['changed'] == 'true') {
            $productId                 = trim($posts['productId']);
            $productInfo = $this->model->getProductFields($productId, 'productStatusType');
            //状态已变更
            if (empty($productInfo) || !in_array($productInfo['productStatusType'], array('newData', 'waitPost'))){
                ajax_return('产品'.$productId.'状态已变更，保存失败', false);
            }

            $minPrice = 0;
            $maxPrice = 0;
            $priceChanged = false;

            $this->db->trans_begin();

            if (!empty($posts['priceCreaseNum']) && isset($posts['priceCreaseType'])) {
                $priceChanged = true;
                $priceNumArr  = explode(',', $posts['priceCreaseNum']); //增加价格数组
                $priceTypeArr = explode(',', $posts['priceCreaseType']); //每次增加价格的方式

                //查找SKU列表
                $skus = $this->Smt_product_skus_model->getProductSkuInfoList($productId, 'smtSkuCode, skuPrice');
                if (!empty($skus)){
                    foreach ($skus as $skuRow){
                        //循环计算下价格
                        $price = $skuRow['skuPrice'];
                        foreach ($priceNumArr as $key => $val){
                            $type = $priceTypeArr[$key];
                            if ($type == 0){ //按数值加减
                                $price += $val;
                            }elseif ($type == 1){ //按百分比加减
                                $price *= (1 + $val / 100);
                            }
                        }

                        $price = sprintf('%01.2f', $price);

                        $skuData['skuPrice'] = $price;
                        $minPrice = $minPrice > 0 ? ($minPrice > $price ? $price : $minPrice) : $price; //最小价格
                        $maxPrice = $maxPrice > $price ? $maxPrice : $price; //最大价格
                        $where = array(
                            'smtSkuCode' => $skuRow['skuCode'],
                            'productId' => (string)$productId
                        );
                        //保存计算后的价格
                        $this->Smt_product_skus_model->update($skuData, array('where' => $where));
                        if ($this->db->_error_message()){
                            $this->db->trans_rollback();
                            ajax_return('产品'.$productId.'价格信息保存出错', false);
                        }
                    }
                }
            }

            $listData['subject']       = trim($posts['subject']);
            $listData['grossWeight']   = trim($posts['grossWeight']);
            $listData['packageLength'] = trim($posts['packageLength']);
            $listData['packageWidth']  = trim($posts['packageWidth']);
            $listData['packageHeight'] = trim($posts['packageHeight']);
            if ($priceChanged){
                $listData['productMinPrice'] = $minPrice;
                $listData['productMaxPrice'] = $maxPrice;
                $listData['productPrice']    = $maxPrice;
            }
            $where2 = array('productId' => (string)$productId);
            $this->model->update($listData, array('where' => $where2));

            if ($this->db->_error_message()){
                $this->db->trans_rollback();
                ajax_return('产品'.$productId.'列表信息保存出错', false);
            }

            $detailData['keyword']              = trim($posts['keywords']);
            $detailData['productMoreKeywords1'] = trim($posts['productMoreKeywords1']);
            $detailData['productMoreKeywords2'] = trim($posts['productMoreKeywords2']);
            list($packageType, $productUnit, $lotNum) = explode('-', $posts['packageWay']);
            $detailData['packageType']       = $packageType == 'true' ? 1 : 0;
            $detailData['productUnit']       = $productUnit;
            $detailData['lotNum']            = $lotNum;
            $detailData['promiseTemplateId'] = $posts['promiseTemplateId'];
            $detailData['freightTemplateId'] = $posts['freightTemplateId'];

            //要添加的产品信息模板
            $typeArray = array(
                'custom'   => 'customText',
                'relation' => 'relatedProduct'
            );

            //先查找产品的详情信息
            $detailInfo = $this->Smt_product_detail_model->getProductDetailInfo($productId, 'detail');
            $detail     = htmlspecialchars_decode($detailInfo['detail']);
            $detailModify = false; //详情信息有变更
            $detailLocalModify =false;
            $detailLocalInfo = $this->Smt_product_detail_model->getProductDetailInfo($productId, 'detailLocal');
            if(!empty($detailLocalInfo['detailLocal'])){
                $detailLocalModify = true;
                $detailLocal = htmlspecialchars_decode($detailLocalInfo['detailLocal']);

            }

            if ($posts['tModuleId'] && $posts['tModuleName'] && $posts['tModuleType']) {
                $kseWidget = '<kse:widget data-widget-type="' . $typeArray[$posts['tModuleType']] . '" id="' . $posts['tModuleId'] . '" title="' . $posts['tModuleName'] . '" type="' . $posts['tModuleType'] . '"></kse:widget>';
                $detail    = $kseWidget . $detail;
                $detailLocal = $kseWidget.$detailLocal;
                unset($kseWidget);
                $detailModify = true;
            }

            if ($posts['bModuleId'] && $posts['bModuleName'] && $posts['bModuleType']) {
                $kseWidget = '<kse:widget data-widget-type="' . $typeArray[$posts['bModuleType']] . '" id="' . $posts['bModuleId'] . '" title="' . $posts['bModuleName'] . '" type="' . $posts['bModuleType'] . '"></kse:widget>';
                $detail .= $kseWidget;
                $detailLocal = $kseWidget.$detailLocal;
                unset($kseWidget);
                $detailModify = true;
            }

            if ($detailModify){

                $detailData['detail'] = htmlspecialchars($detail);
                if($detailLocalModify){
                    $detailData['detailLocal'] = htmlspecialchars($detailLocal);
                }
            }
            $this->Smt_product_detail_model->update($detailData, array('where' => $where2));
            if ($this->db->_error_message()){
                $this->db->trans_rollback();
                ajax_return('产品'.$productId.'详情信息保存出错', false);
            }

            $this->db->trans_commit();
            ajax_return('', true);
        }else {
            ajax_return('产品'.$posts['productId'].'无需变更', false);
        }
    }

    public function getListInfoByaccount()
    {


        $token_id = $_POST['token_id'];
        $groupId = $_POST['groupId3'];
        $product_statues_type = array(
            "onSelling",
            "offline",
            "auditing",
            "editingRequired"
        );
        $option =array();
        if(!empty($groupId)){
            $option['where']['groupId'] = $groupId;
        }
        $option['where']['token_id'] = $token_id;
      //  $option['where']['synchronizationTime <'] =date('Y-m-d H:i:s',strtotime(" -2 day"));
        $updata =array();
        $updata['isRemove']=1;

        $this->Smt_product_list_model->update($updata,$option);
        
        foreach($product_statues_type as $type)
        {
            $tokenInfo = $this->userToken->getOneTokenInfo($token_id);
            if (!$tokenInfo){
                    ajax_return('没有找到对应的账号', false);
            }
            $this->smt->setToken($tokenInfo);



            $result = $this->findProductInfoListQueryByGroupId($type,100,1,$groupId);


            if(is_array($result))
            {
                foreach ($result as $re)
                {
                    if(is_array($re))
                    {
                        foreach($re as $v)
                        {
                            $this->_handleSynchronizationDataByAccount($v['productId'],$token_id,false);
                        }
                    }
                    else
                    {
                        $this->_handleSynchronizationDataByAccount($re['productId'],$token_id,false);
                    }

                }

                if($result['totalPage']>1) {
                    for ($i = 2; $i <= $result['totalPage']; $i++) {

                        $result = $this->findProductInfoListQueryByGroupId($type, 100, $i,$groupId);
                        if(is_array($result))
                        {
                            foreach ($result as $re)
                            {
                                if(is_array($re))
                                {
                                    foreach($re as $v)
                                    {
                                        $this->_handleSynchronizationDataByAccount($v['productId'],$token_id,false);
                                    }
                                }
                                else
                                {
                                    $this->_handleSynchronizationDataByAccount($re['productId'],$token_id,false);
                                }

                            }
                        }
                        else
                        {
                            $this->_handleSynchronizationDataByAccount($result['productId'],$token_id,false);
                        }

                    }

                }
            }
            else
            {
                $this->_handleSynchronizationDataByAccount($result['productId'],$token_id,false);
            }

        }
        ajax_return('同步完成');

    }

    private function _handleSynchronizationDataByAccount($productId,$token_id, $isDieOut=true){


        $tokenInfo = $this->userToken->getOneTokenInfo($token_id);
        if (!$tokenInfo){
            if ($isDieOut) {
                ajax_return('没有找到产品:' . $productId . '对应的账号', false);
            }else {
                return array('status' => false, 'info' => '没有找到产品:' . $productId . '对应的账号');
            }
        }
        $this->smt->setToken($tokenInfo);


        $productInfo = $this->apiFindAeProductById($productId);
        if (!$productInfo){
            if ($isDieOut) {
                ajax_return('产品:' . $productId . '没有获取到对应的在线信息', false);
            }else {
                return array('status' => false, 'info' => '产品:' . $productId . '没有获取到对应的在线信息');
            }
        }

        //销售前缀
        $sale_code = $this->Smt_user_sale_code_model->getSalersPrefixList();

        //同步到数据库
        $list_data['product_url']         = "http://www.aliexpress.com/item/-/" . $productInfo['productId'] . ".html";
        $list_data['subject']             = addslashes(trim($productInfo['subject']));
        //$list_data['gmtCreate']           = $this->_formatSmtDateToDatetime($productInfo['gmtCreate']);
        //$list_data['gmtModified']         = $this->_formatSmtDateToDatetime($productInfo['gmtModified']);
        $list_data['wsOfflineDate']       = $this->_formatSmtDateToDatetime($productInfo['wsOfflineDate']);
        $list_data['multiattribute']      = (count($productInfo['aeopAeProductSKUs']) > 1 ? 1 : 0);
        $list_data['ownerMemberId']       = $productInfo['ownerMemberId'];
        $list_data['ownerMemberSeq']      = $productInfo['ownerMemberSeq'];
        $list_data['wsDisplay']           = $productInfo['wsDisplay'];
        $list_data['productStatusType']   = $productInfo['productStatusType'];
        $list_data['productPrice']        = $productInfo['productPrice'];
        $list_data['groupId']             = array_key_exists('groupId', $productInfo) ? $productInfo['groupId'] : 0; //产品分组
        $list_data['categoryId']          = $productInfo['categoryId'];
        $list_data['packageLength']       = $productInfo['packageLength'];
        $list_data['packageWidth']        = $productInfo['packageWidth'];
        $list_data['packageHeight']       = $productInfo['packageHeight'];
        $list_data['grossWeight']         = $productInfo['grossWeight'];
        $list_data['deliveryTime']        = $productInfo['deliveryTime'];
        $list_data['wsValidNum']          = $productInfo['wsValidNum'];
        $list_data['synchronizationTime'] = date('Y-m-d H:i:s'); //同步时间
        $list_data['isRemove']            = '0';
        $list_data['multiattribute']      = count($productInfo['aeopAeProductSKUs']) > 1 ? 1 : 0;

        //获取广告存在标识
        $pl_rs     = $this->model->getProductFields($productInfo['productId'], 'productId, user_id');
        $isExists  = false;
        $oldUserId = 0;
        if (!empty($pl_rs)){ //存在了
            $isExists = true;
            $oldUserId = $pl_rs['user_id'];
        }
        //$isExists        = $this->model->check_product_is_exists($productInfo['productId']);

        $old_smtsku_list = array(); //旧的smtSKU列表
        if ($isExists) { //存在的，就把对应的smtSKU读出来，看SKU是否有新增或删除
            $old_smtsku_list = $this->Smt_product_skus_model->getProductSkuList($productInfo['productId']);

            $onlineSmtSkuList = array();
            foreach ($productInfo['aeopAeProductSKUs'] as $sku_list) {
                $onlineSmtSkuList[]  = strtoupper(trim($sku_list['skuCode']));
            }

            //已删除但是本地还存在的SMTSKUCODE
            $deletedSmtSkuList = array_diff($old_smtsku_list, $onlineSmtSkuList);
            if ($deletedSmtSkuList){
                $condition = array(
                    'where' => array('productId' => (string)$productInfo['productId']),
                    'where_in' => array('smtSkuCode' => $deletedSmtSkuList)
                );
                $this->Smt_product_skus_model->delete($condition);
            }
        }

        $userId = 0; //新的销售ID，上边的是之前按账号的

        $maxPrice = 0;
        $minPrice = $productInfo['aeopAeProductSKUs'][0]['skuPrice']; //最小值
        $smtSkuCodeArr = array(); //skucode数组
        //判断广告所属销售人员
        foreach ($productInfo['aeopAeProductSKUs'] as $sku_list) {

            if (!$userId) { //直到获取到销售人员ID
                $prefix = get_skucode_prefix($sku_list['skuCode']);
                if ($prefix && array_key_exists($prefix, $sale_code)) {
                    $userId = $sale_code[$prefix]['user_id'];
                }
            }

            // 速卖通SKU取值
            $smtSkuCode        = trim($sku_list['skuCode']);

            $sku_arr           = array();
            if ($smtSkuCode) {
                $sku_arr = buildSysSku($smtSkuCode);
            }

            if ($sku_arr) {
                $smtSkuCodeArr[] = strtoupper($smtSkuCode);
                //判断是否存在海外仓属性SKU --海外仓属性ID， 0的话说明不是海外仓的吧
                $valId = checkProductSkuAttrIsOverSea($sku_list['aeopSKUProperty']);

                foreach ($sku_arr as $sku_new) {

                    //判断该广告的SKU是否存在
                    $isSkuExists = $this->Smt_product_skus_model->checkProductAndSmtSkuCodeIsExists($productInfo['productId'], $smtSkuCode, $sku_new, $valId);

                    $maxPrice = $maxPrice > $sku_list['skuPrice'] ? $maxPrice : $sku_list['skuPrice'];
                    $minPrice = $minPrice < $sku_list['skuPrice'] ? $maxPrice : $sku_list['skuPrice'];

                    // 统计利润率
                    $profitRate                              = 0; //暂时不计算
                    $sku_data                                = array();
                    $sku_data['skuMark']                     = $productInfo['productId'] . ($sku_new ? ":" . $sku_new : '');
                    $sku_data['skuCode']                     = $sku_new;
                    $sku_data['skuPrice']                    = $sku_list['skuPrice'];
                    $sku_data['skuStock']                    = $sku_list['skuStock'];
                    $sku_data['sku_active_id']              = $sku_list['id'];
                    $sku_data['propertyValueId']             = (isset($sku_list['aeopSKUProperty'][0]['propertyValueId']) ? $sku_list['aeopSKUProperty'][0]['propertyValueId'] : 0);
                    $sku_data['skuPropertyId']               = (isset($sku_list['aeopSKUProperty'][0]['skuPropertyId']) ? $sku_list['aeopSKUProperty'][0]['skuPropertyId'] : 0);
                    $sku_data['propertyValueDefinitionName'] = (isset($sku_list['aeopSKUProperty'][0]['propertyValueDefinitionName']) ? $sku_list['aeopSKUProperty'][0]['propertyValueDefinitionName'] : null);
                    //$sku_data['profitRate']                  = $profitRate;
                    $sku_data['synchronizationTime']         = date('Y-m-d H:i:s');
                    $sku_data['isRemove']                    = '0';
                    $sku_data['aeopSKUProperty']             = $sku_list['aeopSKUProperty'] ? serialize($sku_list['aeopSKUProperty']) : '';
                    $sku_data['ipmSkuStock']                 = $sku_list['ipmSkuStock']; //库存
                    $sku_data['overSeaValId']                = $valId;
                    $sku_data['updated']                     = 1;

                    if ($isSkuExists) { //存在了就变更下吧
                        $where['where'] = array('productId' => (string)$productInfo['productId'], 'smtSkuCode' => $smtSkuCode, 'skuCode' => $sku_new, 'overSeaValId' => $valId);
                        $this->Smt_product_skus_model->update($sku_data, $where);
                    } else {
                        $sku_data['productId']  = $productInfo['productId'];
                        $sku_data['smtSkuCode'] = $smtSkuCode;
                        $this->Smt_product_skus_model->add($sku_data);
                    }
                    unset($sku_data);
                }
            }
        }

        //删除未更新的广告
        $this->Smt_product_skus_model->delete(array('where' => array('productId' => (string)$productInfo['productId'], 'updated' => 0)));

        //把修改的状态变更回来
        $newData            = array();
        $newData['updated'] = 0;
        $this->Smt_product_skus_model->update($newData, array('where' => array('productId' => (string)$productInfo['productId'])));


        $smtSkuCodeArr = array_unique($smtSkuCodeArr);
        if ($list_data['multiattribute'] == 1 && count($smtSkuCodeArr) == 1){ //本来是多属性，但是sku都是重复的，当作单属性处理
            $list_data['multiattribute'] = 0;
        }

        if ($oldUserId <= 0 && $userId > 0){ //旧的不存在 同时 新的用户ID存在
            $list_data['user_id'] = $userId; //新ID存在就用新的，不然就是旧的
        }

        $list_data['productMinPrice']     = $minPrice;
        $list_data['productMaxPrice']     = $maxPrice;


        //处理广告信息
        if ($isExists) {
            $list_where['where'] = array('productId' => (string)$productInfo['productId']);
            $this->model->update($list_data, $list_where);
        } else {
            $list_data['token_id']  = $token_id;
            $list_data['productId'] = $productInfo['productId'];
            $this->model->add($list_data);
        }

        //处理广告详情信息
        $detail_data['aeopAeProductPropertys'] = !empty($productInfo['aeopAeProductPropertys']) ? serialize($productInfo['aeopAeProductPropertys']) : '';
        $detail_data['imageURLs']              = $productInfo['imageURLs'];
        $detail_data['detail']                 = htmlspecialchars($productInfo['detail']);
        $detail_data['keyword']                = isset($productInfo['keyword'])?$productInfo['keyword']:"";
        //关键字1
        $detail_data['productMoreKeywords1'] = array_key_exists('productMoreKeywords1', $productInfo) ? $productInfo['productMoreKeywords1'] : '';

        //关键字2
        $detail_data['productMoreKeywords2'] = array_key_exists('productMoreKeywords2', $productInfo) ? $productInfo['productMoreKeywords2'] : '';

        //单位ID
        $detail_data['productUnit']             = $productInfo['productUnit'];
        //运费模板ID
        $detail_data['freightTemplateId']       = $productInfo['freightTemplateId'];
        $detail_data['isImageDynamic']          = $productInfo['isImageDynamic'] ? 1 : 0;
        $detail_data['isImageWatermark']        = isset($productInfo['isImageWatermark']) ? 1 : 0;
        //每包件数
        $detail_data['lotNum']                  = $productInfo['lotNum'];
        //批发最小数量
        $detail_data['bulkOrder']               = array_key_exists('bulkOrder', $productInfo) ? $productInfo['bulkOrder'] : 0;

        //打包销售
        $detail_data['packageType']             = $productInfo['packageType'] ? 1 : 0;
        //自定义记重
        $detail_data['isPackSell']              = $productInfo['isPackSell'] ? 1 : 0;
        //批发折扣
        $detail_data['bulkDiscount']            = array_key_exists('bulkDiscount', $productInfo) ? $productInfo['bulkDiscount'] : 0;
        //服务模板
        $detail_data['promiseTemplateId']       = $productInfo['promiseTemplateId'];
        //尺寸模板
        $detail_data['sizechartId']             = array_key_exists('sizechartId', $productInfo) ? $productInfo['sizechartId'] : 0;
        //产品来源
        $detail_data['src']                     = array_key_exists('src', $productInfo) ? $productInfo['src'] : '';

        /**拆分自定义模板开始**/
        /**拆分自定义模板结束**/

        //判断详情是否存在
        $detailIsExists = $this->Smt_product_detail_model->check_detail_is_exists($productInfo['productId']);

        if ($detailIsExists) {
            $detail_where['where'] = array('productId' => (string)$productInfo['productId']);
            $this->Smt_product_detail_model->update($detail_data, $detail_where);
        } else {
            $detail_data['productId'] = $productInfo['productId'];
            $this->Smt_product_detail_model->add($detail_data);
        }

        unset($list_data);
        unset($productInfo);
        unset($detail_data);

        if ($isDieOut) {
            ajax_return('产品:' . $productId . '同步成功', true);
        }else {
            return array('status' => true, 'info' => '产品:' . $productId . '同步成功');
        }
    }


    public  function copyAllAccountNew()
    {
        $accountfrom = $_POST['token_id_from'];
        $accountto = $_POST['token_id_to'];
        $groupId = $_POST['groupId1'];
        $groupId2 = $_POST['groupId2'];

        $checkecategory = $_POST["checkecategory"];
        $options = array();
        $where['token_id'] = $accountfrom;
        $where['productStatusType'] = 'onSelling';
        $where['isRemove'] = 0;
        $newAccountGroup =0;
        if (!empty($groupId)) {
            if($groupId=='none')
            {
                $where['groupId'] =0;
            }
            else
            {
                $where['groupId'] = $groupId;
            }

        }

        if(!empty($groupId2)&&$groupId2 !='none')
        {
            $newAccountGroup  = $groupId2;
        }

        if(!empty($checkecategory)) // 有选分类
        {

            //先判断是不是末节点啊
            $cateoption = array();
            $cateoption['where']['category_id'] = $checkecategory;
            $cateresult = $this->Slme_smt_categorylist_model->getOne($cateoption,true);
            if($cateresult['isleaf']==1) // 是末节点 才表示精确查找
            {
                $where['categoryId'] = $checkecategory;
            }
            else //不是。将该分类下的所有末节点都找出来
            {
                $cateArray =array();
                $cateArray = $this->getLastCategory($checkecategory,$cateArray);


                $options['where_in']['categoryId'] = $cateArray;


            }

        }




        $options['where'] = $where;

        $result = $this->Smt_product_list_model->getAll2Array($options);


        $flag  = false; //标识
        $error = array();
        if (!empty($result)) {
            foreach ($result as $v) {

                if ($v) {
                    $productId =$v['productId'];
                    //产品图片
                    $detail_info = $this->Smt_product_detail_model->getProductDetailInfo($productId);

                    //SKU属性 --2维数组
                 //   $sku_info = $this->Smt_product_skus_model->getProductSkuProperty($productId);
                    $sku_info = $this->Smt_product_skus_model->getProductSkuInfoList($productId, 'productId, skuCode, smtSkuCode, skuPrice, skuStock, ipmSkuStock, aeopSKUProperty, overSeaValId');


                    //各账号循环插入数据 --插入数据的时候，如果图片要保存的话，要把原tokenID保存下来


                        $this->db->trans_begin();

                        /*********插入到草稿主表数据开始*********/
                        $newProductId = $this->getNewproductid($v['productId'],$accountto);


                        $draft_product['token_id']      = $accountto;
                        $draft_product['old_token_id']  = $v['token_id'];
                        $draft_product['subject']       = $v['subject'];
                        $draft_product['productPrice']  = $v['productPrice'];
                        $draft_product['groupId']       = $newAccountGroup;  //重新生成草稿的产品分组id
                        $draft_product['categoryId']    = $v['categoryId'];
                        $draft_product['packageLength'] = $v['packageLength'];
                        $draft_product['packageWidth']  = $v['packageWidth'];
                        $draft_product['packageHeight'] = $v['packageHeight'];
                        $draft_product['grossWeight']   = $v['grossWeight'];
                        $draft_product['deliveryTime']  = $v['deliveryTime'];
                        $draft_product['wsValidNum']    = $v['wsValidNum'];
                        $draft_product['productStatusType']  = 'newData';
                        $draft_product['old_productId'] = $v['productId'];
                        $draft_product['productId']     = $newProductId;
                        /*********插入到草稿主表数据结束*********/


                        $id = $this->Smt_product_list_model->add($draft_product);

                        if (!$id) {
                            $error[] = $v['productId'] . ',tokenId:' . $accountto . '复制错误';
                            $this->db->trans_rollback();
                            continue;
                        }

                        ////变更下productId成ID-token
                        //$product['productId'] = $id.'-'.$token_id;
                        //$product['id'] = $id;
                        //$affacted = $this->Smt_product_list_model->update($product);
                        //if (!$affacted){ //没有变更成功
                        //	$error[] = $list_info['productId'].',tokenId:'.$token_id.'复制错误';
                        //	$this->db->trans_rollback();
                        //	continue;
                        //}

                        /***************插入到草稿详情表数据开始******************/
                        $draft_detail['productId']              = $newProductId;
                        $draft_detail['aeopAeProductPropertys'] = $detail_info['aeopAeProductPropertys'];
                        $draft_detail['imageURLs']              = $detail_info['imageURLs'];
                        $detail                                 = htmlspecialchars_decode($detail_info['detail']);
                        $detail                                 = filterSmtRelationProduct($detail);//过滤关联产品
                        $draft_detail['detail']                 = htmlspecialchars($detail);
                        $draft_detail['keyword']                = $detail_info['keyword'];
                        $draft_detail['productMoreKeywords1']   = $detail_info['productMoreKeywords1'];
                        $draft_detail['productMoreKeywords2']   = $detail_info['productMoreKeywords2'];
                        $draft_detail['productUnit']            = $detail_info['productUnit'];
                        $draft_detail['isImageDynamic']         = $detail_info['isImageDynamic'];
                        $draft_detail['isImageWatermark']       = $detail_info['isImageWatermark'];
                        $draft_detail['lotNum']                 = $detail_info['lotNum'];
                        $draft_detail['bulkOrder']              = $detail_info['bulkOrder'];
                        $draft_detail['packageType']            = $detail_info['packageType'];
                        $draft_detail['isPackSell']             = $detail_info['isPackSell'];
                        $draft_detail['bulkDiscount']           = $detail_info['bulkDiscount'];
                        $draft_detail['promiseTemplateId']      = $detail_info['promiseTemplateId'];
                        $draft_detail['src']                    = 'isv';
                        $draft_detail['freightTemplateId']      = $detail_info['freightTemplateId'];

                                $freightTemplateId = $this->getTemplateIdByToken_id($accountto);
                                if($freightTemplateId)
                                {
                                    $draft_detail['freightTemplateId']  = $freightTemplateId;
                                }

                        $draft_detail['templateId']             = $detail_info['templateId'];
                        $draft_detail['shouhouId']              = $detail_info['shouhouId'];
                        $draft_detail['detail_title']           = $detail_info['detail_title'];
                      //  $draft_detail['sizechartId']            = $detail_info['sizechartId'];
                        //复制的尺码ID 都设置成-1
                        $draft_detail['sizechartId']            = -1;
                        $draft_detail['detailPicList']          = $detail_info['detailPicList'];
                        $detailLocal                            = htmlspecialchars_decode($detail_info['detailLocal']);
                        $detailLocal                            = filterSmtRelationProduct($detailLocal);//过滤关联产品
                        $draft_detail['detailLocal']            = htmlspecialchars($detailLocal);

                        unset($detail);
                        /***************插入到草稿详情表数据结束******************/
                        $detail_id = $this->Smt_product_detail_model->add($draft_detail);
                        if (!$detail_id) {
                            $error[] = $v['productId'] . ',tokenId:' . $accountto . '详情复制错误';
                            $this->db->trans_rollback();
                            continue;
                        }

                        /***************插入到草稿SKU表数据开始******************/
                        $sku_flag = true;
                        foreach ($sku_info as $row) {
                            $draft_skus['productId']       = $newProductId;
                            $draft_skus['skuCode']         = $row['skuCode']; //这个需要处理下
                            $draft_skus['skuPrice']        = $row['skuPrice'];
                            $draft_skus['skuStock']        = $row['skuStock'];
                            $draft_skus['smtSkuCode']      = rebuildSmtSku($row['smtSkuCode']);
                            $draft_skus['skuMark']         = $draft_skus['productId'].':'.$row['skuCode'];
                            $draft_skus['aeopSKUProperty'] = $row['aeopSKUProperty']; //sku属性--注意可能含有图片
                            $draft_skus['ipmSkuStock']     = $row['ipmSkuStock'];
                            $draft_skus['overSeaValId']    = $row['overSeaValId'];
                            $sku_id                        = $this->Smt_product_skus_model->add($draft_skus);

                            if (!$sku_id) {
                                $sku_flag = false;
                                $error[] = $v['productId'] . ',tokenId:' . $accountto . 'SKU'.$draft_skus['skuCode'].'复制错误';
                                unset($draft_skus);
                                break;
                            }else {
                                unset($draft_skus);
                            }
                        }
                        /***************插入到草稿SKU表数据结束******************/

                        if ($sku_flag){
                            $flag = true;
                            $this->db->trans_commit();
                        }else {
                            $this->db->trans_rollback();
                        }

                }
                unset($detail_info);
                unset($sku_info);
                unset($list_info);

            }
        }

        ajax_return('另存为草稿' . ($flag ? '成功' : '失败'), $flag, $error);
    }

    //确保新生成是productid 是唯一的
    public function getNewproductid($product,$token_id)
    {
        $rand = mt_rand(1000,9999);
        $newProductid =$product.'-'.$token_id.'-'.$rand;

        $where['where']['productId']  = $newProductid;

        $re = $this->Smt_product_list_model->getOne($where,true);


        if(!empty($re))
        {
            $this->getNewproductid($product,$token_id);
        }
        else
        {
            return $newProductid;
        }



    }

    //根据 token_id查找出默认的运费模板  不存在返回false
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

    //根据得到的分类信息 获取分类

    public function getCategoryInfo()
    {
        $searchcategoryinfo = $_POST['searchcategoryinfo'];

        $where = array();
        $like = array();


        //先判断searchcategoryinfo 是不是存数字，如果是纯数字就指定分类。不是纯数字 就是模糊
        if(is_numeric($searchcategoryinfo))
        {
            $where['category_id'] = $searchcategoryinfo;
        }
        else
        {
            $like['category_name']  = $searchcategoryinfo;
        }

        $option= array(
            'where' => $where,
            'like'  => $like
        );

        $result = $this->Slme_smt_categorylist_model->getALL2Array($option);
        $string = "<option>--请选择--</option>";
        if(!empty($result))
        {
            foreach($result as $cate)
            {
                if($cate['isleaf']==1)
                {
                    $string = $string."<option class='red' value=".$cate['category_id'].">".$cate['category_id']."-".$cate['category_name']."<option>";
                }
                else
                {
                    $string = $string."<option value=".$cate['category_id'].">".$cate['category_id']."-".$cate['category_name']."<option>";
                }
            }
        }
        ajax_return($string,1);
    }

    public function getLastCategory($pid,$categoryArray)
    {

        $option = array();
        $option['where']['pid'] = $pid;
        $result = $this->Slme_smt_categorylist_model->getALL2Array($option);
        if(!empty($result))
        {
            foreach($result as $re)
            {
                if($re['isleaf']==1) //末分类
                {
                    $categoryArray[]=$re['category_id'];
                }
                else
                {
                    $categoryArray=  $this->getLastCategory($re['category_id'],$categoryArray);
                }
            }
        }

        return $categoryArray;

    }


    /**
     * SMT商品列表查询  带分组参数
     * @param  string $product_statues_type [description]
     * @param  integer $page_size [description]
     * @param  integer $current_page [description]
     * @return [type]                        [description]
     */
    public function findProductInfoListQueryByGroupId($product_statues_type = "onSelling", $page_size = 100, $current_page = 1,$groupId)
    {
        if(empty($groupId))
        {
           $para = "productStatusType=" . $product_statues_type . "&pageSize=" . $page_size . "&currentPage=" . $current_page;
        } else {
            $para = "productStatusType=" . $product_statues_type ."&groupId=".$groupId. "&pageSize=" . $page_size . "&currentPage=" . $current_page;

        }

        $product_json = $this->smt->getJsonData("api.findProductInfoListQuery",$para);
        $result       = json_decode($product_json, true);

        return $result['success'] ? $result : false;
    }



}