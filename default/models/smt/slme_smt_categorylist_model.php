<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 速卖通分类模型
 * suwei 20141119
 */
class Slme_smt_categorylist_model extends MY_Model {

	public $my_name = array();
    
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
     * 判断分类是否存在
     * @param unknown $categoryId
     * @return boolean
     */
    public function checkCategoryIsExists($categoryId){
        $options['where'] = array('category_id' => $categoryId);
        $data             = $this->getOne($options);
        return $data ? $data->id : false;
    }

    /**
     * 获取子类及所有父类的中文名称
     * @param  [type] $category_id [description]
     * @return [type]              名称数组，看怎么组合下
     */
    public function getCateroryAndParentName($category_id){
    	$this->my_name = array();
    	$this->getCategoryPid($category_id);

    	$rs = $this->my_name;
    	unset($this->my_name);
    	$rs = array_reverse($rs);
    	return implode('>>', $rs);
    }

    /**
     * 根据子类ID递归的获取父类的中文名称
     * @param  [type] $category_id [description]
     * @return [type]              [description]
     */
    public function getCategoryPid($category_id){
    	$optios = array(
			'select'      => array('category_id', 'pid', 'category_name'),
			'category_id' => $category_id
    	);

    	$tmp    = $this->getOne($optios, true);

    	array_push($this->my_name, $tmp['category_name']);

    	if ($tmp['pid'] > 0){
    		$this->getCategoryPid($tmp['pid']);
    	}
    }
    
    /**
     * 根据父分类获取子分类
     * @param number $pid
     * @param string $is_array
     * @return unknown
     */
    public function getCategoryListByPid($pid=0, $is_array=false){
        $options['where'] = array('pid' => $pid);
        $return_arr       = null;
        $result           = $this->getAll($options, $return_arr, $is_array);
        return $result;
    }
    
    /**
     * 删除本地过期的分类
     */
    public function deleteExpiredCategory(){
        $sql = "DELETE FROM erp_slme_smt_categorylist where last_update_time < NOW() - INTERVAL 1 DAY";
        $this->query($sql);
    }
}

/* End of file Slme_smt_categorylist_model.php */
/* Location: ./defaute/models/smt/Slme_smt_categorylist_model.php */