<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Operate_log_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }


    //订单操作日志
    public function add_order_operate_log($data = array()){

    	$data['operateTime'] = date('Y-m-d H:i:s');

    	$data['operateType'] = (isset($data['operateType']) && $data['operateType'] !='') ? $data['operateType'] : 'update';

    	$data['operateMod'] = 'ordersManage';

    	$tof = $this->add($data);

    	return $tof;
    }

    
	function getLogList($array)
	{
		$where = array(
			'operateID >' => 0,
			'operateDisable' => 0	//0为启用
		);
		
		foreach ($array as $k => $v){
			if (!empty($v) OR $v === '0')
			{
				$v = trim($v);
				if ($k == 'usetype')
				{
					if ($v == '9')	//成本价修改日志
					{
						$where['(usetype = '.$v.' or operateText like "%更新成本价%")'] = NULL;
					}
					else
					{
						$where[$k] = $v;
					}
				}
				else
				{
					$where[$k] = $v;
				}
				
			}
		}
		
		$options = array(
			'where' => $where,
			'order_by' => array(
				'operateTime' => 'desc'
			)
		);
		
		$rs = $this->getAll2Array($options);
		return $rs;
	}

}

/* End of file Operate_log_model.php */
/* Location: ./defaute/models/Operate_log_model.php */