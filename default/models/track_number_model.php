<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Track_number_model extends MY_Model {

    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();


    }
    //查找可用的物流追踪号
    public function get_track_number($shiping_id)
    {
        $options = array();

        $where = array();

        $options['select'] ="erp_track_number.track_number";

        $where['m.shipment_id']=$shiping_id;

        $where['order_id']=0;

        $options['where']=$where;

        $join[] = array('erp_track_manage as m ','track_id=m.id');

        $options['join'] = $join;

        $data = $this->getOne($options);

        return $data;
    }

    //更新物流追踪号表字段
    public function update_track($track_number,$order_id)
    {
        $this->db->set('order_id',"'$order_id'",false);

        $this->db->where('track_number', $track_number);

        $tof = $this->db->update('erp_track_number');

        $tof=$this->db->affected_rows();

        return $tof;
        /*$data = array(
            'order_id' => $order_id,
        );
        $where="track_number=$track_number";
        $result=$this->db->update_string('erp_track_numbe', $data, $where);
        return $result;*/
    }
}

/* End of file Orders_model.php */
/* Location: ./defaute/models/order/Orders_model.php */

