<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 新發貨地址模型类
 */
class Gz_address_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
     
	//获取一条符合要求的地址
	public function getSenderInfo(){
		//第一次则更新今天地址使用数据
		$now = date('Y-m-d');
		$newUT=$this->getOneAdress()->updateTime;
		if($now !=$newUT){
			//更新数据
			$data = array('useNumber' => 0,'updateTime' => $now);
			$option['where'] = array('id >' => 0);
     		if(!$this->update($data,$option)){
     			return false;
     		};
		}
       $option['where'] = array('useNumber <' => 50,'updateTime' => date('Y-m-d'));
       $add = $this->getOne($option,true);
	   if($add){
	   		//获取到地址，地址使用次数加1;
	   		$this->adduseNumber($add['id'],$add['useNumber']);
			return $add;
	   }
	   return false;
    }
	public function adduseNumber($id,$useNumber){
		$useNumber++;
		$data = array('useNumber' => $useNumber);
        $option['where'] = array('id =' => $id);
		return $this->update($data,$option);
	}
   //获取一条数据
    public function getOneAdress(){
    	$option['where'] = array('id'=>1);
      return $this->getOne($option);
    }
}

/* End of file Guangzhou_address_model.php */
/* Location: ./defaute/models/Guangzhou_address_model.php */