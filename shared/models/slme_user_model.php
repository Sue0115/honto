<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户管理模型类
 *
 * @author  <xuebingwang2010@gmail.com>
 * @links 
 *
 */
class Slme_user_model extends MY_Model {
	
    private $_table_field = array('*');
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
     * (non-PHPdoc)
     * @see MY_Model::getAll()
     */
    public function getAllLc($options=array(),&$total_rows=array(),$is_array=false){
    	 
    	$options['select'] = array("$this->_table.*",'g.title as group_name',);
    
    	$join[] 		   = array("{$this->_table_pre}slme_group g" ,"g.gid=$this->_table.gid");
    
    	$options['join'] = $join;
    
    	return parent::getAll($options,$total_rows,$is_array);
    }
    
    /**
     * 根据条件查找用户，如果找不到，直接根据查找条件插入一个，并返回
     * @param string $options
     * @return array
     */
    public function findsert(Array $options=array()){
    	if(!$options){
    		return array();
    	}
    	$user = $this->getOne($options,TRUE);
    	if(!$user){
    		$options['create_time'] = time(); 
    		$options['regtime'] = time(); 
    		$options['regip'] = $this->session->userdata['ip_address'];
    		$options['uid'] = $this->add($options);
    		$user = $options;
    	}else {
    	    unset($user['password']);
    	}
    	return $user;
    }
    
    /**
     * 获取所有用户信息
     * 返回数组ID为键 name为值
     */
    public function get_all_user_info($type="user_name")
    {
    	$options = array(
    			'select' => array(
    					'id', 'user_name','nickname'
    			)
    	);
    	 
    	$result = $this->getAll2Array($options);
    	 
    	$data = array();
    	foreach ($result as $v){
            if($type=='nickname'){
              $data[$v['id']] = $v['nickname'];
              continue;
            }
    		$data[$v['id']] = $v['user_name'];
    	}
    	 
    	return $data;
    }
    
    //根据用户id获取用户信息
    public function getInfoByUid($uid){
      $option = array();
      
      $where = array();
      
      $where['id'] = $uid;
      
      $option = array(
        'where'  =>  $where
      );
      
      return $this->getOne($option,true);
    }
    
    //根据组key获取用户信息
    public function getUserBykey($key){
      $select = array();
      $option = array();
      $where = array();
      $select = array($this->_table.".*","g.title","g.key");
      if($key!=''){
       $where = array("g.key"=>$key);
      }
      $join[] = array("erp_slme_group g","g.gid={$this->_table}.gid");
      $option = array(
        'join'  => $join,
        'where' => $where,
        'select'=> $select
      );
     $user = $this->getAll2Array($option);
     $newUser = array();
     foreach($user as $u){
       $newUser[$u['id']] = $u['nickname'];
     }
     return $newUser;
    }

    //统计某个分组有多少个人
    public function get_totoal_by_pid($gid){

        $total = 0;

        $options = array();

        $where = array();

        $where['gid'] = trim($gid);

        $where['status'] = 1;

        $options['where'] = $where;

        $total = $this->getTotal($options);

        return $total;
    }
}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */