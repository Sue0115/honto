<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/14
 * Time: 17:52
 */
class Ebay_country_model extends MY_Model{
    public function __construct()
    {
        parent::__construct('erp_ebay_country');
    }

    public function inserinfo($data)
    {
        $this->db->insert('erp_ebay_country', $data);
    }

    public function getCountryAll($info)
    {
        $option['where'] = $info;
        return $this->getAll2Array($option);
    }

}