<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 亚马逊数据模板类
 */
class Template extends Admin_Controller{

    function __construct(){

        parent::__construct();

        $this->load->model(array(
            'amz/Slme_amz_data_template_model',
            'amz/Slme_amz_category_model',
            'amz/Slme_amz_data_skus_model',
            'sharepage'
        ));
    }

    /**
     * 获取模板数据列表
     */
    public function index(){

        $params = $this->input->get();
        //每页的记录条数
        $cupage = (int)$this->config->item('site_page_num');

        //删除分页记录参数
        if (!empty($params) && array_key_exists('per_page', $params)) unset($params['per_page']);

        $return_arr = array('total_rows' => true); //返回分页信息
        //获取数据列表信息
        $dataList = $this->Slme_amz_data_template_model->getDataList($params, $return_arr);

        //组装分页的URL信息
        $url  = admin_base_url('amz/template/index') .'?'.(!empty($params) ? http_build_query($params) : '');
        $page = $this->sharepage->showPage($url, $return_arr['total_rows'], $cupage);

        $data = array(
            'dataList'  => $dataList,
            'params'    => $params,
            'page'      => $page
        );
        $this->_template('admin/amz/template/list', $data);
    }

    /**
     * 添加或修改功能
     */
    public function info(){
        parent::info();

        $id = $this->input->get_post('id');

        //获取自己定义的分类信息
        $returnArr = array('total_rows' => null);
        $myCateList = $this->Slme_amz_category_model->getCategoryList(array(), $returnArr, 'id, category_us, attribute');

        //获取列表页的详情信息
        $templateInfo = $this->Slme_amz_data_template_model->getOneDataInfo($id);

        //获取SKU详细信息
        $skuList = $this->Slme_amz_data_skus_model->getTemplateDataSkuList($id);

        $data = array(
            'id'           => $id,
            'myCateList'   => $myCateList,
            'templateInfo' => $templateInfo,
            'skuList'      => $skuList
        );
        $this->_template('admin/amz/template/info', $data);
    }

    /**
     * 重写的保存方法
     */
    protected function save(){
        $rs = $this->Slme_amz_data_template_model->save();
        echo json_encode($rs);
        exit();
    }

    /**
     * 异步联想子SKU列表
     */
    public function ajaxGetChildSkus(){
        //父SKU
        $parentSku = trim($this->input->get_post('parentSku'));

        $this->load->model('products/Products_data_model');

        $skuList = $this->Products_data_model->getSkuListPartialLike($parentSku);

        if (!empty($skuList)){
            $data = array();
            foreach ($skuList as $row){
                $products_volume = unserialize($row['products_volume']);
                $data[] = array(
                    'products_sku' => $row['products_sku'],
                    'length'       => $products_volume['ap']['length'],
                    'width'        => $products_volume['ap']['width'],
                    'height'       => $products_volume['ap']['height'],
                    'weight'       => $row['products_weight']
                );
                unset($products_volume);
            }
            ajax_return('', true, $data);
        }else {
            ajax_return('', false);
        }
    }

    public function ajaxGetPicUrls(){
        //SKU组合字符串
        $skuStr = trim($this->input->get_post('skuStr'));

        if (empty($skuStr)){
            ajax_return('请先输入SKU信息', false);
        }

        $skuList = explode(',', $skuStr);

        $sku = array_shift($skuList); //先只判断第一个SKU的信息吧
        $sku = trim(strtoupper($sku));
        //服务器连接
        $imgServerUrl = 'http://oc.moonarstore.com/upload/';

        //$html = getCurlData($imgServerUrl);

        //图片后缀列表
        $picExtendList = defineSmtImageExd();

        //满足条件的图片列表
        $picList = array();

        //先直接获取下，获取不到再正则匹配
        $picDir = $imgServerUrl.$sku.'/';

        $rs = $this->_getRemotePicList($picDir, $picExtendList);

        if ($rs === false){ //基本上可以说明是404错误 --说明没有匹配到图片
            $htmlStr = getCurlData($imgServerUrl);

            $pattern = '/<a\s*href\s*=\s*\"(\s*'.$sku.'\s*)\/\"\s*?>.*?<\/a>/i'; //从服务器根目录开始匹配
            preg_match_all($pattern, $htmlStr, $matches);

            if (!empty($matches) && !empty($matches[1])){ //匹配到图片了
                $newSku = array_shift($matches[1]); //应该只会匹配到一个的吧
                $newPicDir = $imgServerUrl.$newSku.'/';
                $rs2 = $this->_getRemotePicList($newPicDir, $picExtendList);

                if ($rs2 === false){
                    ajax_return('没有找到相应SKU图片目录', false);
                }else {
                    if (count($rs2) > 0){
                        ajax_return('', true, $rs2);
                    }else {
                        ajax_return('没有找到SKU图片', false);
                    }
                }

            }else {
                ajax_return('没找到相应SKU图片目录', false);
            }

        } else { //没有匹配到图片
            if (count($rs) > 0){
                ajax_return('', true, $rs);
            }else {
                ajax_return('没有找到图片', false);
            }
        }
    }

    /**
     * 获取远程图片信息
     * @param $remoteUrl
     * @param $picExtendList
     * @return array|bool
     */
    private function _getRemotePicList($remoteUrl, $picExtendList){
        $skuHtml = getCurlData($remoteUrl);

        $picPattern = '/<a\s*href\s*=\s*\"(.*?)\"\s*?>.*?<\/a>/i';
        preg_match_all($picPattern, $skuHtml, $matches);
        if (!empty($matches) && !empty($matches[1])){ //匹配到图片了
            $picList = array();
            foreach ($matches[1] as $fileName){
                //扩展名
                $extend = getFileExtendName($fileName);
                if (!empty($extend) && in_array(strtolower($extend), $picExtendList)){
                    $picList[] = $remoteUrl.$fileName;
                }
            }
            return $picList;
        }else {
            return false;
        }
    }
}