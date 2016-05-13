<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 亚马逊产品数据模板模型类
 */
class Slme_amz_data_template_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }

    /**
     * 获取AMZ数据列表
     * @param $params
     * @param $return_arr
     * @return mixed
     */
    public function getDataList($params, &$return_arr){
        $this->load->model(array('amz/Slme_amz_data_skus_model'));
        $join[]=array("{$this->Slme_amz_data_skus_model->_table} s","s.pid={$this->_table}.id");


        $like = array();
        if (!empty($params['sku']) && trim($params['sku'])){
            $like['s.sku'] = trim($params['sku']);
        }

        //每页条数
        $cupage = (int)$this->config->item('site_page_num');
        //页码
        $per_page = (int)$this->input->get_post('per_page');

        $options    = array(
            'select'   => "{$this->_table}.*, GROUP_CONCAT(s.`sku`) AS sku",
            'page'     => $cupage,
            'per_page' => $per_page,
            'like'     => $like,
            'join'     => $join,
            'group_by' => "{$this->_table}.id"
        );

        return $this->getAll($options, $return_arr, true);
    }

    /**
     * 获取一条数据模型详情信息
     * @param $id
     * @return Ambigous
     */
    public function getOneDataInfo($id){
        $option = array();

        $where = array();

        $where['id'] = $id;

        $option = array(
            'where'  =>  $where
        );

        return $this->getOne($option,true);
    }

    /**
     * 保存提交的信息
     * @return array
     */
    public function save(){
        $this->load->model('amz/Slme_amz_data_skus_model');

        $id = $this->input->get_post('id');
        //分类
        $category = $this->input->get_post('category');
        //推荐的节点 数组
        $nodes = $this->input->get_post('nodes');
        //标题 数组
        $title = $this->input->get_post('item_name');
        //划分依据
        $theme = $this->input->get_post('theme');
        //SKU 数组
        $item_sku = $this->input->get_post('item_sku');
        //售价 数组
        $item_price = $this->input->get_post('item_price');
        //折扣价 数组
        $discount_price = $this->input->get_post('discount_price');
        //图片列表 数组
        $picLists = $this->input->get_post('picLists');
        //品牌
        $brand_name = $this->input->get_post('brand_name');
        //厂商
        $manufacturer = $this->input->get_post('manufacturer');
        //关键词 数组
        $keyword = $this->input->get_post('keyword');
        //卖点 数组
        $bullet = $this->input->get_post('bullet');
        //描述
        $product_description = $this->input->get_post('product_description');
        //标准售价
        $standard_price = $this->input->get_post('standard_price');
        //打包数量
        $item_package_quantity = $this->input->get_post('item_package_quantity');
        //是否打折扣
        $discount = $this->input->get_post('discount');
        //折扣价
        $sale_price = $this->input->get_post('sale_price');
        //折扣起止时间
        $sale_from_date = $this->input->get_post('sale_from_date');
        $sale_end_date  = $this->input->get_post('sale_end_date');
        //发货周期
        $deliveryTime = $this->input->get_post('deliveryTime');
        //体积重
        $item_display_length = $this->input->get_post('item_display_length');
        $item_display_width  = $this->input->get_post('item_display_width');
        $item_display_height = $this->input->get_post('item_display_height');
        $item_weight         = $this->input->get_post('item_weight');
        //材料
        $material_type = $this->input->get_post('material_type1');

        //属性列表
        $attr          = $this->input->get_post('attr');
        //属性的值列表
        $attrValList   = $this->input->get_post('attrVal');

        //SKU或者图片不能为空
        if (empty($item_sku) || empty($picLists)){ //这个是不能为空
            return array('info' => 'SKU或者图片为空，请先正确录入', 'status' => false);
            exit();
        }

        $attrValArr = array();
        $attribute = '';
        //组装下属性
        if (!empty($attr) && !empty($attrValList)){ //这个是判断有属性
            foreach ($attrValList as $key => $row){
                foreach ($row as $k => $v){
                    $attrValArr[$k][$key] = $v;
                }
            }
            //属性数组也直接序列化一下，存下来
            $attribute = serialize($attr);
        }else {
            $theme = ''; //没有多属性直接不管划分依据字段
        }

        //重新组装下
        $skuList = array();
        $newProductArray = array();
        foreach ($item_sku as $key => $sku){
            if (!empty($sku)){
                $newProductArray[] = array(
                    'sku'           => trim($sku),
                    'picLists'      => $picLists[$key],
                    'isParent'      => ($key == 0) ? 1 : 0,
                    'property'      => !empty($attrValArr[($key - 1)]) ? serialize($attrValArr[($key - 1)]) : '',
                    'updated'       => 1,
                    'pid'           => $id,
                    'price'         => count($item_sku) == 1 ? $standard_price : (!empty($item_price[($key - 1)]) ? $item_price[($key - 1)] : 0),
                    'discountPrice' => !empty($discount_price[($key - 1)]) ? $discount_price[($key - 1)] : 0
                );
                $skuList[] = strtoupper(trim($sku));
            }
        }
        if (empty($newProductArray)){
            return array('info' => 'SKU或者图片为空，请先正确录入', 'status' => false);
            exit();
        }

        //加载SKU模型
        $this->load->model('amz/Slme_amz_data_skus_model');

        $data['category']         = $category;
        $data['nodes']            = implode(';', $nodes);
        $data['title']            = implode('-||-', $title);
        $data['theme']            = trim($theme);
        $data['brand']            = $brand_name;
        $data['manufacturer']     = $manufacturer;
        $data['keyword']          = implode(';', $keyword);
        $data['bullet']           = implode('-||-', $bullet);
        $data['description']      = $product_description;
        $data['price']            = $standard_price;
        $data['packageNum']       = $item_package_quantity;
        $data['discount']         = !empty($discount) ? 1 : 0;
        if ($discount) { //选择折扣，折扣时间会改变
            $data['discountPrice']    = $sale_price;
            $data['discountFromDate'] = $sale_from_date;
            $data['discountEndDate']  = $sale_end_date;
        }else {
            $data['discountPrice']    = 0;
            $data['discountFromDate'] = null;
            $data['discountEndDate']  = null;
        }
        $data['deliveryTime']     = $deliveryTime;
        $data['length']           = $item_display_length;
        $data['width']            = $item_display_width;
        $data['height']           = $item_display_height;
        $data['weight']           = $item_weight;
        $data['material']         = $material_type;
        $data['attribute']        = $attribute;

        if ($id > 0){ //到这是保存
            $data['id'] = $id;
            $rs = $this->update($data);
            if ($this->db->_error_message()){ //保存出错了
                $this->db->trans_rollback();
                return array('info' => '信息保存失败1', 'status' => false);
            }

            /**开始保存产品**/
            //获取已经存在的SKU产品列表(已大写处理)
            $existsSkuList = $this->Slme_amz_data_skus_model->formatTemplateDataSkuList($id);

            foreach ($newProductArray as $row){
                if (in_array(strtoupper($row['sku']), $existsSkuList)){
                    //存在了，直接更新
                    $options = array( //更新的where条件
                        'where' => array(
                            'sku' => $row['sku'],
                            'pid' => $id
                        )
                    );
                    $res = $this->Slme_amz_data_skus_model->update($row, $options);
                    if ($this->db->_error_message()){
                        $this->db->trans_rollback();
                        return array('info' => '信息保存失败2', 'status' => false);
                    }
                }else {
                    //还是继续添加
                    $res = $this->Slme_amz_data_skus_model->add($row);
                    if (!$res){
                        $this->db->trans_rollback();
                        return array('info' => '信息保存失败2', 'status' => false);
                    }
                }
            }

            //比较下，删除现在不在存在的SKU，条件中要注意pid
            $needDelSkuList = array_diff($existsSkuList, $skuList);
            if (!empty($needDelSkuList)){
                $options = array(
                    'where' => array('pid' => $id),
                    'where_in' => array('sku' => $needDelSkuList)
                );
                $res = $this->Slme_amz_data_skus_model->delete($options);
                if (!$res){
                    $this->db->trans_rollback();
                    return array('info' => '信息保存失败3', 'status' => false);
                }
            }

            //提交数据
            $this->db->trans_commit();
            return array('info' => '修改成功', 'status' => true,  'data' => array('id' => $id));
        }else { //这个是新增
            $this->db->trans_begin();
            $rs = $this->add($data);
            if (!$rs){
                $this->db->trans_rollback();
                return array('info' => '信息保存失败1', 'status' => false);
            }

            //保存SKU信息
            foreach ($newProductArray as $sku){
                $res = $this->Slme_amz_data_skus_model->add($sku);
                if (!$res){
                    $this->db->trans_rollback();
                    return array('info' => '信息保存失败2', 'status' => false);
                }
            }
            $this->db->trans_commit();
            return array('info' => '新增成功', 'status' => true,  'data' => array('id' => $rs));
        }
    }
}

/* End of file Slme_amz_data_template_model.php */
/* Location: ./defaute/models/amz/Slme_amz_data_template_model.php */