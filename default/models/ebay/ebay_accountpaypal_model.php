<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/31
 * Time: 18:20
 */

class Ebay_accountpaypal_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct('erp_ebay_paylaybind');
    }

    public function getinfoAll($info)
    {
        $option['where'] = $info;
        return $this->getAll2Array($option);
    }

 /*   public function add($info)
    {
      return   $this->db->insert('erp_ebay_paylaybind', $info);

    }*/

   /* public function update($info, $id)
    {
        // $info['updatetime'] = date('Y-m-d H:i:s', time());
        $this->db->where('id', $id);

        $tof = $this->db->update('erp_ebay_paylaybind', $info);

        $tof = $this->db->affected_rows();

        return $tof;
    }*/

    public function delect($id)
    {
        $sql = "DELETE FROM erp_ebay_paylaybind WHERE id = $id";
        return $this->query($sql);
    }

    /**
     * @param $account 账号
     * @param $price 价格
     * @param $currency 币种
     */
    public function getPayPalByPrice($account,$price,$currency){

        $option['where']['ebay_account'] = $account;
        $result = $this->getOne($option,true);

        if(!empty($result['currency'])&&(!empty($result['paypal_account']))){
         $currency_array =    json_decode($result['currency'],true);

            if(isset($currency_array[$currency])){
                $paypal_account_array = explode(',',$result['paypal_account']);

                if($price <=$currency_array[$currency]){
                    return isset($paypal_account_array[1])?$paypal_account_array[1]:false;
                }else{
                    return isset($paypal_account_array[0])?$paypal_account_array[0]:false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }



    public function getAccountSuffix($ebay_account){
        $option = array();
        $option['where']['ebay_account'] = $ebay_account;
        $result =  $this->getOne($option,true);
        if(isset($result['account_suffix'])&&!empty($result['account_suffix'])){
            return $result['account_suffix'];
        }else{
           return false;
        }
    }
}

