<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Products_attachment_model extends MY_Model {
    
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
     *查询SKU是否有附件
     * @param unknown $products_id
     * @return boolean
     */
    function getProductIsHaveAttachment($products_id = FALSE)
    {
        if ($products_id)
        {
        	$options = array(
        		'select'	=> array('count(*) AS num'),
        		'where'		=> array('products_id' => $products_id),
        	);
        	$result = $this->getOne($options, TRUE);
        	
        	if ($result['num'] > 0)
        	{
        		return TRUE;
        	}
        	else
        	{
        		return  FALSE;
        	}
        }
    }
}

/* End of file Products_attachment_model.php */
/* Location: ./defaute/models/Products_attachment_model.php */