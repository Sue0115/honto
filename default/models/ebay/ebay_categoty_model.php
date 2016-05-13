<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/27
 * Time: 15:21
 */
class Ebay_categoty_model  extends MY_Model{
    public function __construct()
    {
        parent::__construct('erp_ebay_category');
    }

    //插入分类信息
    public function categotyinfo($data)
    {
        $this->db->insert('erp_ebay_category', $data);
    }

    //插入condition信息
    public function categotyfeaturesinfo($data)
    {
        $this->db->insert('erp_ebay_condition', $data);
    }
    //插入condition信息
    public function categotyspecificsinfo($data)
    {
        $this->db->insert('erp_ebay_specifics', $data);
    }

    public function getCategotyOne($info)
    {
        $options['where'] =  $info;
        return $this->getOne($options, true);
    }

    public function getCategotyAll($info)
    {
        $options['where']=$info;
        return $this->getAll2Array($options);
    }

    public  function  selectCategoty($info)
    {
        $sql = "SELECT * FROM  erp_ebay_category WHERE CategorySiteID=".$info['CategorySiteID']." AND CategoryName LIKE '%".$info['CategoryName']."%'";
        return $this->db->query($sql)->result_array();
    }


    /** 递归获取分类的全称
     * @param $CategoryParentID 分类号
     * @param $site 站点
     * @param string $last_result 最后拼接的字符串
     * @return string
     */


    public function  getCategoryFullNameChilden($CategoryParentID,$site,$last_result=''){
        $option =array(
            'select'=>'id,CategoryName,CategoryParentID,CategoryLevel',
            'where'=>array(
              'CategoryID' =>$CategoryParentID,
               'CategorySiteID'=>$site,
               // 'Is_new'=>0
            ),
        );
        $result  =   $this->getOne($option,true);
        if(!empty($result)){
            if($result['CategoryLevel'] != 1){
                $last_result ='>>'.$result['CategoryName'].$last_result;
                $last_result=   $this->getCategoryFullNameChilden($result['CategoryParentID'],$site,$last_result);
            }else{
                $last_result =$result['CategoryName'].$last_result;
                return   $last_result;
            }
        }else{
            return '';
        }


        return $last_result;
    }










}