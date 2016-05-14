<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/27
 * Time: 10:01
 */
class Ebay_user_tokens_model extends MY_Model{

    public function __construct()
    {
        parent::__construct('sf_user_tokens');
    }

    //获取某个账号的信息
    public function getOneTokenInfo($token_id){
        $options['where'] = array(
            'token_id' => $token_id,
        );
        return $this->getOne($options, true);
    }
    public function getOneTokenByAccount($account)
    {
        $options['where'] = array(
            'seller_account' => $account,
        );
        return $this->getOne($options, true);
    }

    public function getUserTokens()
    {
        $options = array();
    /*    $rs = array();
        $array   = null;
        $result  = $this->getAll($options, $array, true);
        return $result;*/
        return $this->getAll($options);
    }
    public function getAllUser($info)
    {
        $option['where'] =$info;
        return $this->getAll2Array($option);
    }



    public function getNameById($token_id){
        $option =array();
        $option['where']['token_id'] = $token_id;
        $result = $this->getOne($option,true);
        if(!empty($result)){
            return  $result['seller_account'];
        }else{
            return '';
        }

    }

    public function getAllAccount(){
        $option =array();
        $option['where']['token_status'] = 1;
        $result = $this->getAll2Array($option);
        foreach($result as $re){
            $return[$re['token_id']] = $re['seller_account'];
        }

        return $return;

    }

    public function getInfoByTokenId($token_id)
    {
        $options = array();

        $where = array();

        $options['select'] = array($this->_table.'.*','s.*');

        $where['token_id'] = trim($token_id);

        $options['where'] = $where;

        $join[] = array('sf_ebay_api_developer_account s',$this->_table.'.developer_id=s.account_id');

        $options['join'] = $join;

        $data = $this->getOne($options,true);

        return $data;
    }

    public function getInfoByAccount($account)
    {
        $options = array();

        $where = array();

        $options['select'] = array($this->_table.'.*','s.*');

        $where['seller_account'] = trim($account);

        $options['where'] = $where;

        $join[] = array('sf_ebay_api_developer_account s',$this->_table.'.developer_id=s.account_id');

        $options['join'] = $join;

        $data = $this->getOne($options,true);

        return $data;
    }

    public function getAccountPhotoUrl($token_id){
        $option =array();
        $option['where']['token_id']= $token_id;
        $option['select'] = 'photo_url';

        $result = $this->getOne($option,true);
        if(isset($result['photo_url'])){
            return trim($result['photo_url']);
        }else{
            return false;
        }
    }




}