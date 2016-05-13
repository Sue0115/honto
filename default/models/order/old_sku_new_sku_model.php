<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 
 */
class Old_sku_new_sku_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }

    public function replace_sku($sku){

    	$new_sku = $sku;

    	$options = array();

    	$options['where']['new_sku'] = $new_sku;

    	$data = $this->getOne($options,true);

    	if($data){
    		$new_sku = $data['old_sku'];
    	}

    	return $new_sku;
    }

    public function get_all_key_old_sku(){

        $resul = array();

        $data = $this->getAll2Array();

        if($data){
            foreach ($data as $key => $v) {
                $resul[$v['old_sku']] = $v['new_sku'];
            }
        }
        return $resul;
    }
}

/* End of file Old_sku_new_sku_model.php */
/* Location: ./defaute/models/order/Old_sku_new_sku_model.php */