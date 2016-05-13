<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Amz_config_model extends MY_Model
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据一定的条件查询账号信息，可自行扩展
     * @param array $condition
     * @return mixed
     */
    public function getAllTokens(array $condition = array())
    {
        $options = array();
        //操作方法
        if (array_key_exists('method', $condition)) {
            $where['method'] = $condition['method'];
        }

        //状态，1为活跃
        if (array_key_exists('status', $condition)) {
            $where['status'] = $condition['status'];
        }

        //ID
        if (array_key_exists('id', $condition)) {
            $where['id'] = $condition['id'];
        }

        //place_name
        if (array_key_exists('place_name', $condition)) {
            $where['place_name'] = $condition['place_name'];
        }

        //排序
        if (array_key_exists('order_by', $condition)){
            $options['order_by'] = $condition['order_by'];
        }

        $options['where'] = $where;
        $return           = null;
        return $this->getAll($options, $return, true);
    }

    /**
     * 格式化查询出的数组，变成键和值的形式
     * @param array $condition
     * @param boolean $toUpper :转换成大写
     * @return array
     */
    public function formatTokenList(array $condition = array(), $toUpper=false)
    {
        $rs             = array();
        $tokenList      = $this->getAllTokens($condition);
        $accountToPlace = $this->definePlaceNameAndSellerAccount();
        $accountToPlace = array_flip($accountToPlace);

        if (!empty($tokenList)) {
            foreach ($tokenList as $token) {
                $rs[$token['id']] = array_key_exists(strtoupper($token['place_name']), $accountToPlace) ? ($toUpper ? strtoupper($accountToPlace[strtoupper($token['place_name'])]) : $accountToPlace[strtoupper($token['place_name'])]) : strtoupper($token['place_name']);
            }
        }

        return $rs;
    }

    /**
     * 定义站点对应的账号名称
     * @return array
     */
    public function definePlaceNameAndSellerAccount()
    {
        return array(
            'Moo.us'    => 'US',
            'Moo.fr'    => 'FR',
            'Moo.de'    => 'DE',
            'Moo.es'    => 'ES',
            'Moo.it'    => 'IT',
            'Moo.ca'    => 'CA',
            'Moo.uk'    => 'GB',
            'Moo.jp'    => 'JP',

            'Yt.us'     => 'OUS',
            'Yt.es'     => 'OES',
            'Yt.it'     => 'OIT',
            'Yt.de'     => 'ODE',
            'Yt.uk'     => 'OGB',
            'Yt.fr'     => 'OFR',

            'Atoz.es'   => 'AES',
            'Atoz.it'   => 'AIT',
            'Atoz.de'   => 'ADE',
            'Atoz.uk'   => 'AGB',
            'Atoz.fr'   => 'AFR',

            'Etiger.us' => 'AUS',

            'Sun.es'    => 'SES',
            'Sun.it'    => 'SIT',
            'Sun.de'    => 'SDE',
            'Sun.uk'    => 'SGB',
            'Sun.fr'    => 'SFR',

            'Gogo.us'   => 'MUS',
            'Etiger.jp' => 'AJP',

            'Woo.uk'    => 'WGB',
            'Woo.us'    => 'WUS',
            'Woo.ca'    => 'WCA',
            'Woo.de'    => 'WDE',
            'Woo.fr'    => 'WFR',
            'Woo.IT'    => 'WIT',
            'Woo.es'    => 'WES',
            
            'Sell.us'   => 'SUS'
        );
    }
}