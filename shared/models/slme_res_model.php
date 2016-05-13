<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Slme_res_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
        $this->load->model('slme_user_res_model');
    }
    
    public function get_all_by_uid($uid=0){
        $options = array('slme_user_res.uid'=>$uid);
        
        $options['select'] = array("$this->_table.*");
         
        $join[] 		    = array("slme_user_res" ,"slme_user_res.rid=$this->_table.id");
        $options['join'] = $join;
        
        return $this->getAll2Array($options);
    }

    /**
     * 判断图片是否在数据库中存在，并且是否和当前用户有绑定关系
     * @param string $hash
     * @param string $uid
     * @return Ambigous <unknown, Ambigous, StdClass, boolean, mixed>
     */
    public function res_exist($hash='',$uid=''){
            
    	$options['hash']   = $hash;
        	
        $item = parent::getOne($options);
        
        //如果资源存在,但没有跟当前用户有绑定关系,则创建一条绑定关系
        if($item && !$this->slme_user_res_model->getOne(array('uid'=>$uid,'rid'=>$item->id))){
            $this->slme_user_res_model->add(array('uid'=>$uid,'rid'=>$item->id));
        }
        
        return $item;
    }
    
    /**
     * (non-PHPdoc)
     * @see MY_Model::delete()
     */
    public function delete(Array $options=array()){
        if(!isset($options['id'])){
            return FALSE;
        }
        
        $ids = array_flip($options['id']);
        //开启事务
        $this->db->trans_start();
        if(isset($options['uid'])){
            
            $options2 = array('rid'=>$options['id'],'uid'=>$options['uid']);
            $this->slme_user_res_model->delete($options2);
            
            $options2['group_by'] = 'rid';
            unset($options2['uid']);
            
            foreach ($this->slme_user_res_model->getAll2Array($options2) as $ur){
                //找出有人还在使用的图片，剩下的都是 没有人用的了，待删除
                if(array_key_exists($ur['rid'], $ids)){
                    unset($ids[$ur['rid']]);
                }
            }
        }
        if($ids){
            $options['id'] = array_flip($ids);
            parent::delete($options);
        }
//         echo $this->db->last_query();
    	//关闭事务
    	$this->db->trans_complete();
        
    	return $ids;
    }
    /**
     * (non-PHPdoc)
     * @see MY_Model::add()
     */
    public function add(Array $data=array()){

        //开启事务
        $this->db->trans_start();
            $rid = parent::add($data);
            if(isset($data['user_id'])){
                $this->slme_user_res_model->add(array('user_id'=>$data['user_id'],'rid'=>$rid));
            }
    	//关闭事务
    	$this->db->trans_complete();
        return $rid;
    }
    
    
}

/* End of file Res_model.php */
/* Location: ./defaute/models/Res_model.php */