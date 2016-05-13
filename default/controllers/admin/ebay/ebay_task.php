<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-08-24
 * Time: 13:38
 */
set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");

class Ebay_task extends MY_Controller
{

    protected $ebay;
    protected $userToken;
    public $model;

    function __construct()
    {
        parent::__construct();
        $this->load->library('MyEbayNew');
        $this->load->model(array(
            'ebay/Ebay_user_tokens_model',
            'ebay/Ebay_template_model',
            'ebay/Ebay_ebaysite_model',
            'ebay/Ebay_list_model',
            'ebay/Ebay_template_html_model',
            'ebay/Ebay_operationlog_model',
            'ebay/Ebay_task_list_model',
            //  'smt/Slme_smt_categorylist_model',
            // 'smt/Slme_smt_category_attribute_model',
        ));
        $this->ebaytest = new MyEbayNew();
        $this->userToken = $this->Ebay_user_tokens_model;
        $this->ebaytemplate = $this->Ebay_template_model;
        $this->ebaysite = $this->Ebay_ebaysite_model;
        $this->list = $this->Ebay_list_model;
        $this->ebaytemplatehtml = $this->Ebay_template_html_model;
        $this->operatelog = $this->Ebay_operationlog_model;

    }


    public function   do_ebay_task()
    {

        $option = array();
        $now_from = date('Y-m-d H:i:s', time() - 10 * 60); // 过去10分钟
        $new_to = date('Y-m-d H:i:s', time() + 10 * 60); // 将来十分钟 ， 一共20分钟
        $where['status'] = 1; //未执行
        $where['local_time  >'] = $now_from;
        $where['local_time  <'] = $new_to;
        $option['where'] = $where;

        $result = $this->Ebay_task_list_model->getAll2Array($option);

        if (!empty($result)) {
            foreach ($result as $task) {

                $result_type = '';// 1 拍卖重复刊登成功  2 刊登成功  3 刊登失败

                $arr = array();
                $arr['where']['id'] = $task['list_id'];

                $list_info = $this->list->getOne($arr,true);

                $account_result = $this->userToken->getInfoByAccount($list_info['ebayaccount']);


                if (!empty($list_info)) {

                    if (($list_info['status'] == 2) && ($list_info['ad_type'] != 'paimai')) // 排除已经刊登并且不是拍卖类型
                    {
                        continue;
                    }


                    $list_info = $this->reassemble_info($list_info); //信息重新组装

                    if($list_info['ad_type']=='duoshuxing')
                    {
                        $api = 'AddFixedPriceItem';
                    }
                    else
                    {
                        $api = 'AddItem';
                    }
                    $this->ebaytest->setinfo($account_result['user_token'],$account_result['devid'],$account_result['appid'],$account_result['certid'],$list_info['site'],$api);

                    if ($list_info['ad_type'] == 'paimai') //拍卖允许重复刊登
                    {
                        if ($list_info['status'] == 2) //该条拍卖是重复拍卖  需要插入一条新数据
                        {
                            $response = $this->ebaytest->publish($account_result['user_token'], $list_info);
                            if (isset($response->ItemID)) //成功
                            {
                                $result_type = 1;
                            } else {
                                $result_type = 3;
                            }
                        } else {
                            $response = $this->ebaytest->publish($account_result['user_token'], $list_info);
                            if (isset($response->ItemID)) //成功
                            {
                                $result_type = 2;
                            } else {
                                $result_type = 3;
                            }
                        }

                    } else {
                        $response = $this->ebaytest->publish($account_result['user_token'], $list_info);
                        if (isset($response->ItemID)) //成功
                        {
                            $result_type = 2;
                        } else {
                            $result_type = 3;
                        }
                    }
                    if($result_type==1)
                    {

                        unset($list_info['id']);
                        $list_info['itemid'] = (string)($response->ItemID);
                        $list_info['status'] = 2;
                        $list_info['failurereasons'] ='';
                        $return_id  =   $this->list->add($list_info);


                        $taskListArray = array();
                        $taskListArray['status'] = 2;
                        $taskListArray['note'] = (string)($response->ItemID);
                        $where['where']['id'] = $task['id'];

                        $this->Ebay_task_list_model->update($taskListArray,$where);

                        $operatelogaArray = array();
                        $operatelogaArray['userid'] = 0;
                        $operatelogaArray['listid'] = $return_id;
                        $operatelogaArray['createtime'] = date('Y-m-d H:i:s', time());
                        $operatelogaArray['specificissues'] = " 定时任务刊登这个条广告，改广告为 拍卖的重复刊登 新广告的Itemid 为" . $list_info['itemid'];
                        $this->operatelog->add($operatelogaArray);
                        unset($taskListArray);
                        unset($operatelogaArray);

                    }
                    if($result_type==2)
                    {

                        /*$arritem = array();
                        $arritem['itemid'] = (string)($response->ItemID);
                        $arritem['status'] = 2;
                        $arritem['failurereasons'] = '';
                        $this->list->updatelist($task['list_id'], $arritem);*/

                        $taskListArray = array();
                        $taskListArray['status'] = 2;
                        $taskListArray['note'] = (string)($response->ItemID);

                        $where['where']['id'] = $task['id'];
                        $this->Ebay_task_list_model->update($taskListArray, $where);
                        unset($where);

                        $operatelogaArray = array();
                        $operatelogaArray['userid'] = 0;
                        $operatelogaArray['listid'] = $task['list_id'];
                        $operatelogaArray['createtime'] = date('Y-m-d H:i:s', time());
                        $operatelogaArray['specificissues'] = " 定时任务刊登这个条广告，itemid 为" . (string)($response->ItemID);
                        $this->operatelog->add($operatelogaArray);
                        unset($arritem);
                        unset($taskListArray);
                        unset($operatelogaArray);


                    }
                    if($result_type==3)
                    {

                        //失败的话 不去改变list表信息  值改变task_list 信息。
                        $taskListArray = array();
                        $taskListArray['status'] = 2;
                        $taskListArray['note'] = (string)$response->Errors->LongMessage;

                        $where['where']['id'] = $task['id'];
                        $this->Ebay_task_list_model->update($taskListArray, $where);
                        unset($where);

                        $operatelogaArray = array();
                        $operatelogaArray['userid'] = 0;
                        $operatelogaArray['listid'] = $task['list_id'];
                        $operatelogaArray['createtime'] = date('Y-m-d H:i:s', time());
                        $operatelogaArray['specificissues'] = " 定时任务刊登失败，失败原因为" . $taskListArray['note'];
                        $this->operatelog->add($operatelogaArray);
                        unset($taskListArray);
                        unset($operatelogaArray);

                    }
                }
            }
        }


    }


    public function reassemble_info($info)  //将某些数据  重新组装一下
    {
        //  先获取对应站点英文代码
        $site['siteid'] = $info['site'];
        $siteinfo = $this->ebaysite->getEbaySiteOne($site);

        $info['sitename'] = $siteinfo['site'];  //站点
        $info['sitecurrency'] = $siteinfo['currency'];// 币种信息

        //组装描述信息

        $template['where']['id'] = $info['publication_template']; // 卖家描述
        $resulttemplate = $this->ebaytemplate->getOne($template,true);

        $templatehtml['where']['id'] = $info['publication_template_html']; //模板详情
        $resulttemplatehtml = $this->ebaytemplatehtml->getOne($templatehtml,true);


        $info['description_details_new'] = '';
        if (!empty($resulttemplatehtml)) {

            if (!empty($resulttemplate)) // 将固定字符替换成对应的信息
            {

                $resulttemplatehtml['template_html'] = str_replace('{{tittle}}', $info['template_title'], $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{paymentterms}}', $resulttemplate['payment'], $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{termsofsales}}', $resulttemplate['sales_policy'], $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{shippingterms}}', $resulttemplate['shipping'], $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{contactus}}', $resulttemplate['contact_us'], $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{aboutus}}', $resulttemplate['about_us'], $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{description}}', $info['description_details'], $resulttemplatehtml['template_html']);

            } else  // 卖家描述为空 将固定字符 替换成空字符
            {
                $resulttemplatehtml['template_html'] = str_replace('{{tittle}}', $info['template_title'], $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{paymentterms}}', '', $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{termsofsales}}', '', $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{shippingterms}}', '', $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{contactus}}', '', $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{aboutus}}', '', $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{description}}', $info['description_details'], $resulttemplatehtml['template_html']);
            }

            if (!empty($info['template_deteils']))  // 对模板里面的图片进行处理
            {
                $picarr = json_decode($info['template_deteils'], true);
                $picbig = '';
                $picsamll = '';
                for ($i = 0; $i < count($picarr); $i++) {
                    if ($i == 0) {
                        $picbig = '<div class="albumBigImgBox"><img width="600" src="' . $picarr[$i] . '" alt="" name="bigImg" id="bigImg"></div>';
                        $picsamll = $picsamll . '<div alt="" class="smallImgBox"><img width="92" height="92" alt=""  onmouseover="document.getElementById(' . "'bigImg'" . ').src=this.src"   src="' . $picarr[$i] . '"></div>';
                    } else {
                        $picsamll = $picsamll . '<div alt="" class="smallImgBox"><img width="92" height="92" alt=""  onmouseover="document.getElementById(' . "'bigImg'" . ').src=this.src"   src="' . $picarr[$i] . '"></div>';
                    }
                }

                $resulttemplatehtml['template_html'] = str_replace('{{Picture1}}', $picbig, $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{Picture2}}', $picsamll, $resulttemplatehtml['template_html']);
            } else {
                $resulttemplatehtml['template_html'] = str_replace('{{Picture1}}', '', $resulttemplatehtml['template_html']);
                $resulttemplatehtml['template_html'] = str_replace('{{Picture2}}', '', $resulttemplatehtml['template_html']);
            }
            $info['description_details_new'] = $resulttemplatehtml['template_html'];

        } else //没有选择模板  无法加入模板图片
        {
            if (!empty($resulttemplate)) {

                $templateinfo = '<h2><span class="Pa_head"><span class="Pa_headc">Payment Method <hr /></span></span></h2>' . $resulttemplate['payment'] . '<h2> <span class="Pa_head"><span class="Pa_headc">Shipping Detail<hr /> </span></span> </h2>' . $resulttemplate['shipping'] . '<h2> <span class="Pa_head"><span class="Pa_headc"> Sales Policy<hr /> </span></span> </h2>' . $resulttemplate['sales_policy'] . '<h2> <span class="Pa_head"><span class="Pa_headc">About Us<hr /> </span></span> </h2>' . $resulttemplate['about_us'] . '<h2> <span class="Pa_head"><span class="Pa_headc"> Contact Us <hr /> </span></span> </h2>' . $resulttemplate['contact_us'];
                $info['description_details_new'] = $info['description_details'] . $templateinfo;

            } else // 没有选择卖家描述 。
            {

                $info['description_details_new'] = $info['description_details'];
            }
        }
        return $info;
    }


}