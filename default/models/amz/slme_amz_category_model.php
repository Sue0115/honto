<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 自定义AMZ分类模型类
 */
class Slme_amz_category_model extends MY_Model {
    
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
     * 获取AMZ自定义分类列表
     * @param $params
     * @param $return_arr
     * @param $fields: 字段信息
     * @return mixed
     */
    public function getCategoryList($params, &$return_arr, $fields='*'){

        $fields = !empty($fields) ? $fields : '*';
        if (is_array($fields)){
            $fields = implode(', ', $fields);
        }elseif (is_string($fields)){
            $fields = $fields;
        }else {
            $fields = '*';
        }

        $options    = array(
            'select'   => $fields
        );

        if (array_key_exists('total_rows', $return_arr) && $return_arr['total_rows']) { //是要分页的

            $like = array();
            if (!empty($params['category']) && trim($params['category'])) {
                $like['category_us'] = trim($params['category']);
                $like['category_ca'] = trim($params['category']);
                $like['category_uk'] = trim($params['category']);
                $like['category_fr'] = trim($params['category']);
                $like['category_de'] = trim($params['category']);
                $like['category_it'] = trim($params['category']);
                $like['category_es'] = trim($params['category']);
                $like['category_jp'] = trim($params['category']);
            }

            //每页条数
            $cupage = (int)$this->config->item('site_page_num');
            //页码
            $per_page = (int)$this->input->get_post('per_page');

            $options = array_merge($options, array('page' => $cupage,
                                                   'per_page' => $per_page,
                                                   'or_like'  => $like
            ));
        }

        return $this->getAll($options, $return_arr, true);
    }

    /**
     * 根据分类ID获取分类详情信息
     * @param $id
     * @return Ambigous
     */
    public function getCategoryInfo($id){
        $options = array();
        if ($id){
            $options['where'] = array('id' => $id);
        }
        return $this->getOne($options);
    }

    /**
     * 保存处理
     */
    public function save(){
        $data = $this->input->post();
        $id = $data['id'];

        //属性处理下，要是有的话，直接序列化处理
        $newAttr = array();
        $attr = $data['attr'];
        if (!empty($attr)){
            foreach ($attr as $at){
                if (trim($at)){
                    $newAttr[] = $at;
                }
            }
        }

        unset($data['attr']);
        $data['attribute'] = !empty($newAttr) ? serialize($newAttr) : '';

        if ($data['id'] > 0){
            $rs = $this->update($data);
        }else {
            unset($data['id']);
            $rs = $this->add($data);
            $id = $rs;
        }
        echo json_encode(array('status' => ($rs ? 'y' : 'n'), 'info' => '保存'.($rs ? '成功' : '失败'), 'id' => $id));
        exit;
    }
}

/* End of file Slme_amz_category_model.php */
/* Location: ./defaute/models/amz/Slme_amz_category_model.php */