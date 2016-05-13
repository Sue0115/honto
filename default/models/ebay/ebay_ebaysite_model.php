<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/4
 * Time: 14:09
 */
class Ebay_ebaysite_model extends MY_Model{
    public function __construct()
    {
        parent::__construct('erp_ebay_site');
    }

    public function inserinfo($data)
    {
        $this->db->insert('erp_ebay_site', $data);
    }

    public function getEbaySiteOne($info)
    {
        $options['where'] =  $info;
        return $this->getOne($options, true);
    }

    public function updateInfo($siteid,$data)
    {
        $data['updatetime'] =date('Y-m-d H:i:s',time());
        $this->db->where('siteid', $siteid);

        $tof = $this->db->update('erp_ebay_site', $data);

        $tof=$this->db->affected_rows();

        return $tof;
    }

    public function getEbaySiteAll($info)
    {
        $options['where'] = $info;
        return $this->getAll2Array($options);
    }

    public function updateReturnPolicy($sitid,$data)
    {
        $data['updatetime'] =date('Y-m-d H:i:s',time());
        $this->db->where('siteid', $sitid);

        $tof = $this->db->update('erp_ebay_site', $data);

        $tof=$this->db->affected_rows();

        return $tof;
    }


    public  function getSiteNameBySiteId($site){
        $option['where']['siteid'] = $site;
        $result = $this->getOne($option,true);
        return $result['site'];
    }

    public function getAllSiteName(){
       $result= $this->getAll2Array();

        foreach($result as $re){
            $return[$re['siteid']] = $re['site'];

        }

        return $return;
    }


    public function getCurrency(){
        $option['where']['is_use'] = 0;
        $option['select'] = 'currency';

        $result =  $this->getAll2Array($option);

        foreach($result as $re){
            $retrun[$re['currency']] = $re['currency'];
        }
        return $retrun;
    }

    public function getSignCurrency($site){
        $option['where']['siteid'] = $site;
        $option['select'] = 'currency';
       $result =   $this->getOne($option,true);
        return $result['currency'];

    }

    public function getSignName($site){
        $option['where']['siteid'] = $site;
        $option['select'] = 'site';
        $result =   $this->getOne($option,true);
        return $result['site'];

    }

}