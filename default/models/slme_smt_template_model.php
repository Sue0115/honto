<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 速卖通模板模型类
 */
class Slme_smt_template_model extends MY_Model
{

    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * 获取一个模板的详情信息
     * @param $id
     * @return Ambigous
     */
    public function getTemplateInfo($id)
    {
    	$options = array();
    
    	if ($id){
    		$options['where'] = array('id' => $id);
    	}
    	
    	return $this->getOne($options, true);
    }

    /**
     * 获取模板列表
     * @param $array 选项、平台等参数
     * @return array 查询结果数组
     */
    public function getTemplateList($array)
    {
        $options = array();
        $where   = array();
        if (array_key_exists('select', $array) && $array['select']) {
            $select = $array['select'];
        } else {
            $select = '*';
        }

        //条件设置
        if (isset($array['plat']) && (int)$array['plat'] > 0) {
            $where['plat'] = (int)$array['plat'];
        }

        $options     = array(
            'select' => $select,
            'where'  => $where
        );
        $return_data = null;
        $rs          = array();

        $rs = $this->getAll($options, $return_data, true);

        return $rs;
    }
}

/* End of file Slme_smt_template_model.php */
/* Location: ./defaute/models/Slme_smt_template_model.php */