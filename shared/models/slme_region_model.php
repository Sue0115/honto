<?php
class Slme_region_model extends MY_Model{
	
	private  $_table_field = array('*');
	
    public function __construct(){
    	parent::__construct();
    	
    }
    
    /**
     * 获取所有省份
     *
     */
    public function getAllProvince($is_json=false){
    	
    	$options = array();
    	
    	$options['select'] = $this->_table_field;//查询的字段
    	
    	$options['where'] = array('level'=>1);
    	
    	$total_rows = FALSE;
    	 
    	$data = $this ->getAll($options, $total_rows,TRUE);
    	
    	return $is_json ? json_encode($data) : $data;
    	
    }
    
    /**
     * 
     * 根据省份ID获取该省份下的城市列表
     * @param $id
     */
    public  function getAllCityByProvince($id,$is_json=false){
    	
    	
    	$options = array();
    	
    	$options['select'] = $this->_table_field;//查询的字段
    	
    	$options['where'] = array('level'=>2,'parent_id'=>$id);
    	
    	$total_rows = FALSE;
    	 
    	$data = $this ->getAll($options, $total_rows,TRUE);
    	
    	return $is_json ? json_encode($data) : $data;
    	
    }
    
    /**
     * 
     * 根据城市ID获取该城市所在的区列表
     * @param $id
     */
    public function getALLRegionByCity($id,$is_json=false){
    	
    	
    	$options = array();
    	
    	$options['select'] = $this->_table_field;//查询的字段
    	
    	$options['where'] = array('level'=>3,'parent_id'=>$id);
    	
    	$total_rows = FALSE;
    	 
    	$data = $this ->getAll($options, $total_rows,TRUE);
    	
    	return $is_json ? json_encode($data) : $data;
    	
    }
    
    /**
     * 
     *根据ID获取该ID的所有父类ID，返回数组
     * @param $id
     */
    function getParentID($id){
    	
    	$options           = array();
    	$options['select'] = $this->_table_field;//查询的字段
    	$options['where']  = array('id'=>$id);
    	
    	$result            = $this->getOne($options,TRUE);
    	
    	$data   = array();
    	if($result){
    	    $data[] = $result['id'];
        	if($result['parent_id'] !== '1' && $result['parent_id']){
    		    $data = array_merge($data,$this->getParentID($result['parent_id']));
        	}
    	}
    	
    	return $data;
    }
    
    /**
     * 
     *根据ID获取该ID的所有父类姓名
     * @param $id
     */
    function getParentName($id){
    	
    	$options = array();
    	
    	$options['select'] = $this->_table_field;//查询的字段
    	
    	$options['where'] = array('id'=>$id);
    	
    	$result = array();
    	
    	$result = $this ->getOne($options,TRUE);
    	
    	$data   = array();
    	if($result){
    	    
    	    $data[] = $result['name'];
    	    if($result['parent_id'] !== '1' && $result['parent_id']){
    	        $data = array_merge($data,$this->getParentName($result['parent_id']));
    	    }
    	}
    	
    	return $data;
    	
    }
    
    function get_rsort_pname($id){
        $data = array_reverse($this->getParentName($id));
        
        return $data;
    }
    
    function get_rsort_pname_string($id){
    	
    	$result = '';
    	
    	$data = $this->get_rsort_pname($id);
    	
    	if($data){
    		$result = implode(' ', $data);
    	}
    	
    	return $result;
    }
    
}