<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/15
 * Time: 13:20
 */
class Ebay_list_model extends MY_Model{
    public function __construct()
    {
        parent::__construct('erp_ebay_list');
    }

    public function inserinfo($data)
    {
        $this->db->insert('erp_ebay_list', $data);
        $this->cache->clean($this->_table);

        return $this->db->insert_id();
    }

    public function getEbayListAll($info)
    {
        $options['where'] = $info;
        return $this->getAll2Array($options);
    }

    public function getEbayListOne($info)
    {
        $options['where'] =  $info;
        return $this->getOne($options, true);
    }

    public function updatelist($id,$data)
    {
        $data['updatetime'] =date('Y-m-d H:i:s',time());
        $this->db->where('id', $id);

        $this->db->update('erp_ebay_list', $data);

        $tof=$this->db->affected_rows();

        return $tof;
    }
    public function delect($id)
    {
        $sql = "DELETE FROM erp_ebay_list WHERE id = $id";
      return  $this->query($sql);
    }


    public function getactivelistting($page,$pageSize)
    {
        $get_details_info_time = date("Y-m-d H:i:s",strtotime('-1 day'));
        $sql="SELECT item_number,token_id,siteID FROM sf_product_itemid WHERE isEnded=0 AND getDetailsTime <'".$get_details_info_time."'  limit ". ($page - 1) * $pageSize . ',' . $pageSize;

        $result = $this->query($sql)->result_array();


        return $result;
    }


    // 获取近一个月
    public function getUnActiveListting()
    {
        //一个月以内的
        $time_option = date("Y-m-d H:i:s",strtotime('-30 day'));



        $sql  ="SELECT item_number FROM  sf_product_itemid WHERE  isEnded=1 AND itemUpdateTime >'".$time_option."'";


        $result = $this->query($sql)->result_array();
        return $result;

    }




    public function changepicture(){
        $sql  ="SELECT   id,ebayaccount,site,template_title,itemid,ad_type,publication_template_html,publication_template,template_deteils,template_title,description_title,ebay_picture,description_details,sku_search,starttime FROM `erp_ebay_list` WHERE `status` =2 AND publication_template_html IS NOT NULL AND `template_deteils` IS NOT NULL AND template_deteils <> ''";

     /*   $sql  ="SELECT   id,ebayaccount,site,template_title,mul_picture,add_mul,mul_info,itemid,ad_type,publication_template_html,publication_template,template_deteils,template_title,description_title,ebay_picture,description_details,sku_search,starttime FROM `erp_ebay_list` WHERE `itemid` in(
)  order by `starttime` asc";*/

        $result = $this->query($sql)->result_array();
        return $result;
    }


    public function defineEbaySellerPrefix($type = 1){
        $sellerPrefix = array(
            array('prefix' => '001', 'userId' => 48),  //wangfei
            array('prefix' => '002', 'userId' => 22),  //chenlixia
            array('prefix' => '003', 'userId' => 29),  //chenjing
            array('prefix' => '004', 'userId' => 105), //wangxian
            array('prefix' => '005', 'userId' => 27),  //liufei
            array('prefix' => '006', 'userId' => 123), //wuqun
            array('prefix' => '007', 'userId' => 144), //yanghong
            array('prefix' => '008', 'userId' => 148),  //maonuosha
            array('prefix' => '009', 'userId' => 107),  //yangyang
            array('prefix' => '010', 'userId' => 150),  //taoling
            array('prefix' => '011', 'userId' => 132),  //liulian
            array('prefix' => '012', 'userId' => 128),  //sujing
            array('prefix' => '013', 'userId' => 161),  //xuhao
            array('prefix' => '014', 'userId' => 160),  //xuzhidan
            array('prefix' => '015', 'userId' => 163),  //jiangling
            array('prefix' => '016', 'userId' => 164),  //zhangsusu
            array('prefix' => '017', 'userId' => 162),  //zhaowenjin
            array('prefix' => '018', 'userId' => 165),  //lijiemeng
            array('prefix' => '019', 'userId' => 151),  //daiqi
            array('prefix' => '020', 'userId' => 152),  //liushuang
            array('prefix' => '021', 'userId' => 175),  //chenbowen
            array('prefix' => '022', 'userId' => 174),  //liulinlin
            array('prefix' => '023', 'userId' => 181),  //lihui
            array('prefix' => '024', 'userId' => 177),  //chendan
            array('prefix' => '025', 'userId' => 176),  //zhouzhixu
            array('prefix' => '027', 'userId' => 418),  //huchunyu
            array('prefix' => '028', 'userId' => 320),  //huangrong
            array('prefix' => '029', 'userId' => 470),  //yangleen
            array('prefix' => '030', 'userId' => 302),  //heyanhua
            array('prefix' => '031', 'userId' => 477),  //liuxiaoqing
            array('prefix' => '032', 'userId' => 407), //malin
            array('prefix' => '033', 'userId' => 492), // chenmeilin
            array('prefix' => '034', 'userId' => 504), // guohui
            array('prefix' => '035', 'userId' => 509), // liangguixiang
            array('prefix' => '036', 'userId' => 414), // caili
            array('prefix' => '037', 'userId' => 516), //yufang
            array('prefix' => '040', 'userId' => 567),//chengqiao
            array('prefix' => '042', 'userId' => 570),//boziwei
            array('prefix' => '330', 'userId' => 48), //wangfei2
            array('prefix' => '331', 'userId' => 22),//chenlixia2
            array('prefix' => '332', 'userId' => 107),//yangyang2
            array('prefix' => '333', 'userId' => 176),//zhouzhixu2
            array('prefix' => '334', 'userId' => 181),//lihuo2
            array('prefix' => '335', 'userId' => 320),//huangrong2
            array('prefix' => '336', 'userId' => 477),//liuxiaoqing2
            array('prefix' => '337', 'userId' => 302),//heyanhua2
            array('prefix' => '338', 'userId' => 492),//chenmeilin2
            array('prefix' => '339', 'userId' => 470),//yangleen2
            array('prefix' => '340', 'userId' => 516),//yufang2
            array('prefix' => '360', 'userId' => 567),//chengqiao2
            array('prefix' => '366', 'userId' => 570),//boziwei2
            array('prefix' => '350', 'userId' => 572),
            array('prefix' => '351', 'userId' => 583 ),
            array('prefix' => '352', 'userId' => 585),
            array('prefix' => '353', 'userId' => 584),
        );

        if($type ==1){
            $userId =array();
            foreach($sellerPrefix as $user){
                $userId[(string)$user['prefix']] = $user['userId'];
            }

            return  $userId;
        }else{
            $userId =array();
            foreach($sellerPrefix as $user){
                $userId[$user['userId']] = $user['userId'];
            }
            return $userId;
        }
    }

















}