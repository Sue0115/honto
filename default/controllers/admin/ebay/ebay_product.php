<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/4
 * Time: 16:10
 */
set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");
class Ebay_product extends Admin_Controller{
    protected $ebay;
    protected $userToken;

    function __construct()
    {
        parent::__construct();
        $this->load->library('MyEbayNew');

        $this->load->model(array(
            'ebay/Ebay_user_tokens_model',
            'ebay/Ebay_categoty_model',
            'ebay/Ebay_condition_model',
            'ebay/Ebay_ebaysite_model',
            'ebay/Ebay_specifics_model',
            'ebay/Ebay_ebaydetails_model',
            'ebay/Ebay_template_model',
            'products/Products_data_model',
            'ebay/Ebay_country_model',
            'ebay/Ebay_list_model',
            'ebay/Ebay_template_html_model',
            'ebay/Ebay_accountpaypal_model',
            'ebay/Ebay_transtemplate_model',
            'ebay/Ebay_specifics_new_model',
            'ebay/Ebay_product_model',
            'ebay/Ebay_store_with_category_model',
            'ebay/Ebay_store_category_model',
            'ebay/Ebay_operationlog_model'
            //  'smt/Slme_smt_categorylist_model',
            // 'smt/Slme_smt_category_attribute_model',
        ));
        $this->ebaytest = new MyEbayNew();
        $this->userToken = $this->Ebay_user_tokens_model;
        $this->category = $this->Ebay_categoty_model;
        $this->condition =$this->Ebay_condition_model;
        $this->ebaysite = $this->Ebay_ebaysite_model;
        $this->specifics =$this->Ebay_specifics_model;
        $this->details=$this->Ebay_ebaydetails_model;
        $this->ebaytemplate=$this->Ebay_template_model;
        $this->product = $this->Products_data_model;
        $this->country =$this->Ebay_country_model;
        $this->ebaylist = $this->Ebay_list_model;
        $this->ebaytemplatehtml=$this->Ebay_template_html_model;
        $this->paypallist = $this->Ebay_accountpaypal_model;
        // $this->model     = $this->Slme_smt_categorylist_model;
    }

    //新增或修改
    public function ebaylistting()
    {


        $data['account_list'] =  $this->details->getAccountList(); //全部PAYPAL

        if(isset($_GET['id']))
        {
            $list['id'] = $_GET['id'];
            $listinfo = $this->ebaylist->getEbayListAll($list);
            $category1['categoryid'] = $listinfo[0]['categoty1'];
            $category1['side']=$listinfo[0]['site'];
            if(($category1['side']==0)||($category1['side']==2)||($category1['side']==3)||($category1['side']==15)) //site 0.2.3.15 通用0site的数据
            {
                $category1['side']=0;
            }
            $category2['categoryid'] = $listinfo[0]['categoty1'];
            $category2['siteid']=$listinfo[0]['site'];
            if(( $category2['siteid']==0)||( $category2['siteid']==2)||( $category2['siteid']==3)||( $category2['siteid']==15))//site 0.2.3.15 通用0site的数据
            {
                $category2['siteid']=0;
            }
            $listinfo[0]['speinfo'] = $this->specifics->getSpecificsAll($category1);
            $listinfo[0]['item_status_option'] =  $this->condition->getConditionAll($category2);
            $template['id'] = $listinfo[0]['publication_template'];
            $resulttemplate = $this->ebaytemplate->getTemplateAll($template);
            if(isset($resulttemplate[0]))
            {
                $listinfo[0]['resulttemplate'] = '<h2><span class="Pa_head"><span class="Pa_headc">Payment Method <hr /></span></span></h2>'.$resulttemplate[0]['payment'].'<h2> <span class="Pa_head"><span class="Pa_headc">Shipping Detail<hr /> </span></span> </h2>'.$resulttemplate[0]['shipping'].'<h2> <span class="Pa_head"><span class="Pa_headc"> Sales Policy<hr /> </span></span> </h2>'.$resulttemplate[0]['sales_policy'].'<h2> <span class="Pa_head"><span class="Pa_headc">About Us<hr /> </span></span> </h2>'.$resulttemplate[0]['about_us'].'<h2> <span class="Pa_head"><span class="Pa_headc"> Contact Us <hr /> </span></span> </h2>'.$resulttemplate[0]['contact_us'];

            }
            $templatehtml['id'] = $listinfo[0]['publication_template_html'];
            $resulttemplatehtml  = $this->ebaytemplatehtml->getTemplateAll($templatehtml);
            if(isset($resulttemplatehtml[0])) {
                $listinfo[0]['resulttemplatehtml'] =  $resulttemplatehtml[0]['template_html'];
            }

            $trans = array();
            $trans['siteid'] =$listinfo[0]['site'];
            $trans['validforsellingflow'] ='true';
            $listinfo[0]['guonei_trans'] =  $this->details->getDetailsAll($trans);
            $trans['internationalservice'] ='true';
            $listinfo[0]['guowai_trans'] =  $this->details->getDetailsAll($trans);
            unset($trans);
            $data['listinfo']=$listinfo[0];

            $option = array();
            $option['where']['ebay_account'] = $listinfo[0]['ebayaccount'];
            $paypal_account = $this->Ebay_accountpaypal_model->getOne($option,true);
            if(!empty($paypal_account))
            {
                $last_account_list = array();
                $paypal = explode(',',$paypal_account['paypal_account']);
                foreach($paypal as $p)
                {
                    $last_account_list[]['paypal_email_address'] = $p;
                }

                $data['account_list'] = $last_account_list;
            }
        }
        $arr= array();
        $arrsite= array();
        $arrsite['where']['is_use'] =0;
        $result = $this->ebaysite->getAll2Array($arrsite);
        $data['side'] = $result;
        $data['userinfo'] = $this->userToken->getAllUser($arr);
        $data['mobanxinxi'] = $this->ebaytemplate->getTemplateAll($arr);
        $data['template'] = $this->ebaytemplatehtml->getTemplateAll($arr);
        $data['country'] = $this->country->getCountryAll($arr);
        $data['trans_list'] = $this->Ebay_transtemplate_model->getTemplateAll($arr);

        $this->_template('admin/ebay/ebaylistting',$data);
    }


 /*   public function ebaymodel()
    {
        $arr = array();
      $data['template_list']  = $this->ebaytemplate->getTemplateAll($arr);
       // var_dump($result);
        $this->_template('admin/ebay/ebayTemplate',$data);

    }*/






    //根据SKU获取图片服务器上的图片信息
    public function ajaxUploadDirImage(){
       $dirName = strtoupper($_POST['sku']);
        $opt = isset($_POST['opt'])?$_POST['opt']:'';

        if(!empty($opt)){
            $url = "http://imgurl.moonarstore.com/get_image.php?dirName=".$dirName."&dir=SP";//美国图片服务器脚本的路径
        }else{
            $url = "http://imgurl.moonarstore.com/get_image.php?dirName=".$dirName;//美国图片服务器脚本的路径
        }
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);



        $result = json_decode($result,true);


      /*  foreach($result as $k=>$re)
        {
            if(strpos($re,'size'))
            {
               $key = $k;
                $val = $re;
            }
        }
        if(isset($key))
        {
            array_splice($result, $key, 1);
            array_unshift($result,$val);
        }*/

        if(!strpos((string)$result,'文件夹'))
        {
            if(!empty($host_url)){
            foreach($result as $ke => $v){
                $result[$ke] = str_replace('imgurl.moonarstore.com',$host_url,$v);
            }
        }
            ajax_return('', 1, $result);

        }
        else
        {
            ajax_return('', 2, $result);
        }


    }

    //根据站点，及其分类的父类ID 获取对应的信息
    public function getCategory()
    {

        $arr= array();
        $arr['CategoryLevel'] = 1;
        $arr['CategorySiteID'] = intval($_POST['site']);
        if(isset($_POST['op2']))
        {
            $arr['CategoryLevel']=2;
            $arr['CategoryParentID'] = intval($_POST['op2']);
            $arr['CategorySiteID'] = intval($_POST['site']);
        }
        if(isset($_POST['op3']))
        {
            $arr['CategoryLevel']=3;
            $arr['CategoryParentID'] = intval($_POST['op3']);
            $arr['CategorySiteID'] = intval($_POST['site']);
        }
        if(isset($_POST['op4']))
        {
            $arr['CategoryLevel']=4;
            $arr['CategoryParentID'] = intval($_POST['op4']);
            $arr['CategorySiteID'] = intval($_POST['site']);
        }
        if(isset($_POST['op5']))
        {
            $arr['CategoryLevel']=5;
            $arr['CategoryParentID'] = intval($_POST['op5']);
            $arr['CategorySiteID'] = intval($_POST['site']);
        }
        if(isset($_POST['op6']))
        {
            $arr['CategoryLevel']=6;
            $arr['CategoryParentID'] = intval($_POST['op6']);
            $arr['CategorySiteID'] = intval($_POST['site']);
        }
            $result = $this->category->getCategotyAll($arr);
        $options = '';
        if ($result){
            foreach ($result as $row){
                if($row['LeafCategory']=='true')
                {
                    $options .= '<option value="'.$row['CategoryID'].'">'.$row['CategoryName'].'</option>';
                }
                else
                {
                    $options .= '<option value="'.$row['CategoryID'].'">'.$row['CategoryName'].'>'.'</option>';
                }

            }
        }
       ajax_return($options);
    }


    //在进行跟换站点，判断分类是否存在
    public function getCategoryIsSet()
    {
            $arr['CategoryID'] = $_POST['categoryid'];
            $arr['CategorySiteID'] = $_POST['site'];
        $result = $this->category->getCategotyAll($arr);
        if ($result){
            ajax_return('',1);
        }
        else
        {
            ajax_return('',2);
        }

    }


    //根据分类获取对应的物品描述选项
    public  function getCondition($categoryid="",$site="")
    {

        $arrinfo= array();
        $arrinfo['categoryid'] = isset($_POST['categoryid'])?($_POST['categoryid']):$categoryid;
        $arrinfo['siteid'] =isset($_POST['site'])?$_POST['site']:$site;
        if(($_POST['site']==0)||($_POST['site']==2)||($_POST['site']==3)||($_POST['site']==15))
        {
            $arr['siteid']=0;
        }
        $result =  $this->condition->getConditionAll($arrinfo);
        $options = '';
        $is_mul='';
        $upcenabled ='';
        $eanenabled ='';
        $isbnenabled = '';
        $refresh = true;

        if(isset($result[0]['update_time']) && (time()-strtotime($result[0]['update_time'])<7*24*60*60 ) )
        {
            /*if(strtotime($result[0]['update_time'])-time()<7*24*3600)
            {
                $refresh =false;
            }*/

            foreach ($result as $row){
                if($row['variationsenabled']=='')
                {
                    $is_mul = 1;
                }
                if($row['upcenabled']=='Required')
                {
                    $upcenabled = 1;
                }
                if($row['eanenabled']=='Required')
                {
                    $eanenabled = 1;
                }
                if($row['isbnenabled']=='Required')
                {
                    $isbnenabled = 1;
                }

                $options .= '<option value="'.$row['condition_id'].'">'.$row['displayname'].'</option>';
            }

            $lastarr =array();
            $lastarr['options']=$options;

            if($upcenabled == 1)
            {
                $lastarr['upcenabled']=1;
            }

            if($eanenabled == 1)
            {
                $lastarr['eanenabled']=1;
            }

            if($isbnenabled == 1)
            {
                $lastarr['isbnenabled']=1;
            }


            if($is_mul==1)
            {
                ajax_return('',2,$lastarr);
            }
            else
            {

                ajax_return('',1,$lastarr);
            }
        }
        else
        {


            $result =   $this->userToken->getInfoByTokenId(4);
            $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$arrinfo['siteid'],'GetCategoryFeatures');
            $condionResult  = $this->ebaytest->getCondition($result['user_token'],$arrinfo['categoryid'],$arrinfo['siteid']);

            $responseDoc = new DomDocument();
            $responseDoc->loadXML($condionResult);
            $response = simplexml_import_dom($responseDoc);


            if(isset($response->Category->ConditionValues->Condition))
            {
                $condition = $response->Category->ConditionValues->Condition;
                $arr = array();

                $arr['categoryid']=$arrinfo['categoryid'];
                $arr['siteid'] =$arrinfo['siteid'];
                $arr['variationsenabled'] = isset($response->Category->VariationsEnabled)?(string)$response->Category->VariationsEnabled:'';
                $arr['conditionenabled'] = isset($response->Category->ConditionEnabled)?(string)$response->Category->ConditionEnabled:'';
                $arr['upcenabled'] = isset($response->Category->UPCEnabled)?(string)$response->Category->UPCEnabled:'';
                $arr['eanenabled'] = isset($response->Category->EANEnabled)?(string)$response->Category->EANEnabled:'';
                $arr['isbnenabled'] = isset($response->Category->ISBNEnabled)?(string)$response->Category->ISBNEnabled:'';
                foreach($condition as $con)
                {
                    $arr['condition_id'] = intval($con->ID);
                    $arr['displayname'] = (string)$con->DisplayName;

                    $infocondition['categoryid'] =  $arr['categoryid'];
                    $infocondition['condition_id'] = $arr['condition_id'];
                    $infocondition['displayname'] =  $arr['displayname'];
                    $infocondition['siteid'] =  $arr['siteid'];

                    $conditionResult = $this->condition->getConditionOne($infocondition);
                    if(empty($conditionResult))
                    {
                        $arr['update_time'] = date('Y-m-d H:i:s',time());
                        $this->category->categotyfeaturesinfo($arr);
                    }
                    else
                    {
                        $this->condition->updateInfo($conditionResult['id'],$arr);
                    }
                }

                $this->getCondition( $arrinfo['categoryid'],$arrinfo['siteid']);
            }
            else
            {
                ajax_return("",3);
            }


        }

    }


    //根据分类及站点获取对应的分类的属性
    public  function getSpecifics($categoryid="",$side="")
    {
        $arrinfo= array();
        $arrinfo['categoryid'] = isset($_POST['categoryid'])?$_POST['categoryid']:$categoryid;
        $arrinfo['side']=isset($_POST['site'])?$_POST['site']:$side;
        if(($_POST['site']==0)||($_POST['site']==2)||($_POST['site']==3)||($_POST['site']==15))
        {
            $arr['side']=0;
        }
        $result = $this->specifics->getSpecificsAll($arrinfo);
        $options = '';


        if(isset($result[0]['updatetime']) && (time()-strtotime($result[0]['updatetime'])<7*24*3600)  )
        {

            $options .= '<tr><td>自定义属性1</td><td>名称：<input type="text"  name="wupinmiaoshuname[][1]" /> 值:<input type="text"  name="wupinmiaoshuzhi[][1]" /></td><td></td></tr>';
            $options .= '<tr><td>自定义属性2</td><td>名称：<input type="text"  name="wupinmiaoshuname[][2]" /> 值:<input type="text"  name="wupinmiaoshuzhi[][2]" /></td><td></td></tr>';
            $options .= '<tr><td>自定义属性3</td><td>名称：<input type="text"  name="wupinmiaoshuname[][3]" /> 值:<input type="text"  name="wupinmiaoshuzhi[][3]" /></td><td></td></tr>';
            $i = 0;
            foreach ($result as $ke=> $row){
                $specificvalue = explode('{@}',$row['specificvalue']);
                if($specificvalue){
                    $spe = ''; //name= "'.$row['name'].'"
                    if($row['minvalues']==1)
                    {
                        $spe .='<option>--必须选择--</option>';
                    }
                    else
                    {
                        $spe .='<option>--请选择--</option>';
                    }

                    foreach($specificvalue as $k=>$sp)
                    {
                        if($k==count($specificvalue)-1)
                        {
                            break;
                        }
                        $spe .= '<option value="'.$row['name'].'">'.$sp.'</option>';
                    }
                }
                $options .='<tr><td>'.$row['name'].'</td><td><input type="text"  name="wupinmiaoshu[]['.$row['name'].']" /><select onchange="wpsxxz(this.id)"  class="download dropdown-select"  id="selee'.$i.'">'.$spe.' </select></td><td></td></tr>';

                $i++;
            }
            ajax_return($options);

        }
        else
        {
            $arrinfo['categoryid'] = $_POST['categoryid'];

            $result =   $this->userToken->getInfoByTokenId(4);
            $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$arrinfo['side'],'GetCategorySpecifics');

            $info = $this->ebaytest->getGetCategorySpecifics($result['user_token'], $arrinfo['categoryid']);


            $responseDoc = new DomDocument();
            $responseDoc->loadXML($info);
            $response = simplexml_import_dom($responseDoc);

            $arr=array();
            $arr['categoryid'] = intval($response->Recommendations->CategoryID);
            $arr['side']= $arrinfo['side'];
            if(isset($response->Recommendations->NameRecommendation))
            {

                $info = $response->Recommendations->NameRecommendation;


                foreach($info as $v)
                {
                    $re='';
                    $arr['name'] = (string)$v->Name;
                    $arr['valuetype'] = isset($v->ValidationRules->ValueType)?(string)$v->ValidationRules->ValueType:'';
                    $arr['minvalues'] = isset($v->ValidationRules->MinValues)?(string)$v->ValidationRules->MinValues:'';
                    $arr['maxvalues'] = isset($v->ValidationRules->MaxValues)?(string)$v->ValidationRules->MaxValues:'';
                    $arr['selectionmode'] = isset($v->ValidationRules->SelectionMode)?(string)$v->ValidationRules->SelectionMode:'';
                    $arr['variationspecifics'] = isset($v->ValidationRules->VariationSpecifics)?(string)$v->ValidationRules->VariationSpecifics:'';
                    $infovalue = $v->ValueRecommendation;
                    foreach($infovalue as $i_v)
                    {
                        $re=(string)$i_v->Value.'{@}'.$re;
                    }
                    $arr['specificvalue']=$re;

                    $speinfo['categoryid']=$arr['categoryid'];
                    $speinfo['side']=$arr['side'];
                    $speinfo['name']=$arr['name'];

                    $returnSpeci = $this->specifics->getSpecificsOne($speinfo);
                    if(empty($returnSpeci))
                    {
                        $speinfo['updatetime'] =  date('Y-m-d H:i:s',time());
                        $this->category->categotyspecificsinfo($arr);
                    }
                    else
                    {
                        $this->specifics->update($arr,$returnSpeci['id']);
                    }
                }
                $this->getSpecifics($arr['categoryid'],$arr['side']);
            }
            else
            {
          
                ajax_return("",2);
            }


        }
    }


    //根据站点获取对应的国内和国外的运输选项
    public function getDetails()
    {
        $arr= array();
        $arr['internationalservice'] ='';
        $arr['validforsellingflow'] ='true';
        $arr['siteid'] = $_POST['siteid'];
        if(isset($_POST['internationalservice']))
        {
            $arr['internationalservice'] = 'true';
            $arr['siteid'] = $_POST['siteid'];
        }
        $result = $this->details->getDetailsAll($arr);
        $options = '';
        $options .='<option value="">--请选择--</option>';
        if($result)
        {
            foreach ($result as $row){
                $options .= '<option value="'.$row['shippingservice'].'">'.$row['description'].'('.$row['shippingtimemin'].'-'.$row['shippingtimemax'].')</option>';
            }
        }
        ajax_return($options);
    }

    //根据sku获取对应的英文描述
    public function getSkuhtmlmod()
    {
        $sku = isset($_POST['sku'])?$_POST['sku']:"";
        $result = $this->product->getSkuInfoLike(strtoupper($sku));
        if(isset($result[0]))
        {
            $skuinfo = htmlspecialchars_decode($result[0]['products_html_mod']);
            ajax_return('', 1, $skuinfo);

        }
        else
        {
            ajax_return('', 2, $result);
        }
    }

    //根据选择的模板id 获取对应的模板信息
    public function getTemplatedetails()
    {
        $arr = array();
        $arr['id'] = empty($_POST['id'])?'':$_POST['id'];
        if(!empty($arr['id']))
        {
            $result = $this->ebaytemplate->getTemplateAll($arr);
           if(isset($result[0]))
           {
               $infoall = '<h2><span class="Pa_head"><span class="Pa_headc">Payment Method <hr /></span></span></h2>'.$result[0]['payment'].'<h2> <span class="Pa_head"><span class="Pa_headc">Shipping Detail<hr /> </span></span> </h2>'.$result[0]['shipping'].'<h2> <span class="Pa_head"><span class="Pa_headc"> Sales Policy<hr /> </span></span> </h2>'.$result[0]['sales_policy'].'<h2> <span class="Pa_head"><span class="Pa_headc">About Us<hr /> </span></span> </h2>'.$result[0]['about_us'].'<h2> <span class="Pa_head"><span class="Pa_headc"> Contact Us <hr /> </span></span> </h2>'.$result[0]['contact_us'];
               ajax_return('', 1, htmlspecialchars_decode($infoall));
           }
        }
    }

    public function getTemplatedetailsHtml()
    {
        $arr = array();
        $arr['id'] = empty($_POST['id'])?'':$_POST['id'];
        if(!empty($arr['id']))
        {
            $result = $this->ebaytemplatehtml->getTemplateAll($arr);
            if(isset($result[0]))
            {
                ajax_return('', 1, htmlspecialchars_decode($result[0]['template_html']));
            }
        }
    }

    //根据站点获取对应的币种信息
    public function getCurrencyinfo()
    {
        $arr['siteid'] = $_POST['siteid'];
        $result = $this->ebaysite->getEbaySiteOne($arr);
        $info['returnswithin'] =empty($result['returnswithin'])?'':explode('{@}',$result['returnswithin']);
        $info['returnsaccepted'] =empty($result['returnsaccepted'])?'':explode('{@}',$result['returnsaccepted']);
        $info['shippingcostpaidby'] =empty($result['shippingcostpaidby'])?'':explode('{@}',$result['shippingcostpaidby']);
        $info['refund'] =    empty($result['refund'])?'':explode('{@}',$result['refund']);
        $info['currency']=$result['currency'];

        if($result)
        {
            ajax_return('', 1, $info);
        }
    }

    //在多属性的时候，根据sku进行模糊查询出子SKU
    public function getSkuLike()
    {

        $sku = strtoupper($_POST['sku']);
        if(!empty($sku))
        {
            $result = $this->details->getSkuLike($sku);
            if(!empty($result))
            {
                ajax_return($result);
            }
        }
    }
    public function selectCategory()
    {

        $returnarr =array();
        $arr['CategorySiteID'] = $_POST['site'];
        $arr['CategoryName']  = $_POST['caval'];
        $result =   $this->userToken->getInfoByTokenId(4);
        $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$arr['CategorySiteID'],'GetSuggestedCategories');

        $lastinfo = $this->ebaytest->getSuggestedCategories($result['user_token'],$arr['CategoryName']);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($lastinfo);
        $response = simplexml_import_dom($responseDoc);
        if(!isset($response->SuggestedCategoryArray->SuggestedCategory))
        {
            ajax_return('',2);
        }

        $suggestedCategoryArray  =  $response->SuggestedCategoryArray->SuggestedCategory;


        $catearr= array();
        $j=0;
        foreach($suggestedCategoryArray as $suggest)
        {

           // echo $suggest->Category->CategoryID;
            $catearr[$j]['CategoryID'] = intval($suggest->Category->CategoryID);
            $catearr[$j]['Match'] = intval($suggest->PercentItemFound);
            $j++;
          //  echo $suggest->
        }


     //  $result =  $this->category->selectCategoty($arr);
        $returnarr=array();
        $i=0;
        foreach($catearr as $v)
        {
            $option= array();
            $option['where']['CategoryID'] = $v['CategoryID'];
            $option['where']['CategorySiteID'] = $arr['CategorySiteID'];

            $result =  $this->category->getAll2Array($option);
            if($result)
            {
                foreach($result as $k=> $re)
                {
                    if($re['LeafCategory']=='true')
                    {
                        $returnarr[$i]['stringname'] =  $this->getInfoByLastCategory($re['CategorySiteID'],$re['CategoryParentID'],$re['CategoryName']);
                        $returnarr[$i]['categoryid'] = $re['CategoryID'];
                        $returnarr[$i]['match'] = $v['Match'];
                    }
                }

            }
            $i++;
        }

        $lastarr= array_values($returnarr);
        ajax_return('',1,$lastarr);

    }

    public function getInfoByLastCategory($site,$category,$string)
    {

        $last='';

        $arr['CategorySiteID'] = $site;
        $arr['CategoryID'] =$category;
        $reslut = $this->category->getCategotyOne($arr);
        if($reslut)
        {
            if($reslut['CategoryLevel']==1)
            {
                $last  = $reslut['CategoryName'].'>'.$string;


            }
            else
            {
                $string  = $reslut['CategoryName'].'>'.$string;
                $last  =  $this->getInfoByLastCategory($reslut['CategorySiteID'],$reslut['CategoryParentID'],$string);

            }
        }
        return $last;
    }

    public function getPaypalByAccount()
    {
        $arr['ebay_account'] =$_POST['account'];
        $re  = $this->paypallist->getinfoAll($arr);
      if($re)
      {
          $paypal = explode(',',$re[0]['paypal_account']);
          $paypalinfo = '';
          foreach($paypal as $pa)
          {
              $new_pa = $pa;
              $new_pa =  substr_replace($new_pa,'****','2','6');


              $paypalinfo=$paypalinfo.'<option value="'.$pa.'">'.$new_pa.'</option>';
          }
          ajax_return('',1,$paypalinfo);
      }
    }

    //测试运输模板页面
    public function transTemplateIndex()
    {
        $arr= array();
        $result = $this->ebaysite->getEbaySiteAll($arr);
        $data['side'] = $result;

        $translist = $this->Ebay_transtemplate_model->getTemplateAll($arr);

        $data['translist'] = $translist;
        $this->_template('admin/ebay/ebayTransTemplateList',$data);
    }


    //新增或修改运输模板页面判断
    public function transTemplateIndexAdd()
    {

        $arr= array();
        $result = $this->ebaysite->getEbaySiteAll($arr);
        $data['side'] = $result;
        unset($result);

        $arr= array();
        $arr['internationalservice'] ='';
        $arr['validforsellingflow'] ='true';
        $arr['siteid'] = 0;

        $result = $this->details->getDetailsAll($arr);
        $data['guoneitrans'] = $result;
        unset($result);

        $arr= array();
        $arr['internationalservice'] = 'true';
        $arr['validforsellingflow'] ='true';
        $arr['siteid'] = 0;
        $result = $this->details->getDetailsAll($arr);
        $data['guowaitrans'] = $result;
        unset($result);

        $arr =array();
        $data['country'] = $this->country->getCountryAll($arr);

        if(isset($_GET['id']))
        {
            $listarr = array();
            $listarr['id'] = $_GET['id'];
            $re =  $this->Ebay_transtemplate_model->getTemplateAll($listarr);
            $data['listinfo'] = $re[0];


            $arr= array();
            $arr['internationalservice'] ='';
            $arr['validforsellingflow'] ='true';
            $arr['siteid'] = $re[0]['siteid'];

            $result = $this->details->getDetailsAll($arr);
            unset( $data['guoneitrans']);
            $data['guoneitrans'] = $result;
            unset($result);

            $arr= array();
            $arr['internationalservice'] = 'true';
            $arr['validforsellingflow'] ='true';
            $arr['siteid'] = $re[0]['siteid'];;
            $result = $this->details->getDetailsAll($arr);
            unset( $data['guowaitrans']);
            $data['guowaitrans'] = $result;
            unset($result);
        }
        $this->_template('admin/ebay/ebayTemplateView',$data);
    }

    //新增或修改模板信息
    public function transTemplateAdd()
    {



        $info =array();

        $info['id'] = $_POST['id'];
        $info['transtemplatename']=$_POST['transname'];
        $info['siteid'] = $_POST['siteid'];

        $info['returns_policy'] = $_POST['tuihuozhengce'];
        $info['returns_days'] = isset($_POST['tuihuotianshu']) ? $_POST['tuihuotianshu'] : '';
        $info['returns_type'] = isset($_POST['tuihuofangshi']) ? $_POST['tuihuofangshi'] : '';
        $info['returns_delay'] = isset($_POST['returns_delay']) ? $_POST['returns_delay'] : '';
        $info['returns_cost_by'] = isset($_POST['tuihuochengdang']) ? $_POST['tuihuochengdang'] : '';
        $info['return_details'] = isset($_POST['return_details']) ? trim($_POST['return_details']) : '';

        $ReturnPolicy =array();
        $ReturnPolicy['ReturnsAcceptedOption'] = $info['returns_policy'];
        $ReturnPolicy['RefundOption'] = $info['returns_type'];
        $ReturnPolicy['ShippingCostPaidByOption'] = $info['returns_cost_by'];
        $ReturnPolicy['ReturnsWithinOption'] = $info['returns_days'] ;
        $ReturnPolicy['ExtendedHolidayReturns'] = $info['returns_delay'];
        $ReturnPolicy['Description'] = $info['return_details'];

        $info['ReturnPolicy'] = serialize($ReturnPolicy);
        unset($ReturnPolicy);


    //  $info['all_buyers'] = $_POST['yaoqiu'];

        $BuyerRequirementDetails =array();
        $BuyerRequirementDetails['all_buyers'] = isset($_POST['all_buyers']) ? $_POST['all_buyers'] : '';
        $BuyerRequirementDetails['LinkedPayPalAccount'] = isset($_POST['nopaypal']) ? $_POST['nopaypal'] : '';
        $BuyerRequirementDetails['ShipToRegistrationCountry'] = isset($_POST['yunshufangweizhiwai']) ? $_POST['yunshufangweizhiwai'] : '';
        $BuyerRequirementDetails['MaximumUnpaidItemStrikesInfo']['main'] = isset($_POST['qibiao']) ? $_POST['qibiao'] : '';
        $BuyerRequirementDetails['MaximumUnpaidItemStrikesInfo']['Count'] = isset($_POST['qibiaonum']) ? $_POST['qibiaonum'] : '';
        $BuyerRequirementDetails['MaximumUnpaidItemStrikesInfo']['Period'] = isset($_POST['qibiaotianshu']) ? $_POST['qibiaotianshu'] : '';

        $BuyerRequirementDetails['MaximumBuyerPolicyViolations']['main'] = isset($_POST['jianjv']) ? $_POST['jianjv'] : '';
        $BuyerRequirementDetails['MaximumBuyerPolicyViolations']['Count'] = isset($_POST['jianjvnum']) ? $_POST['jianjvnum'] : '';
        $BuyerRequirementDetails['MaximumBuyerPolicyViolations']['Period'] = isset($_POST['jianjvtianshu']) ? $_POST['jianjvtianshu'] : '';


        $BuyerRequirementDetails['MinimumFeedbackScore']['main'] = isset($_POST['xinyong']) ? $_POST['xinyong'] : '';
        $BuyerRequirementDetails['MinimumFeedbackScore']['Count'] = isset($_POST['xinyongnum']) ? $_POST['xinyongnum'] : '';


        $BuyerRequirementDetails['MaximumItemRequirements']['main'] = isset($_POST['goumai']) ? $_POST['goumai'] : '';
        $BuyerRequirementDetails['MaximumItemRequirements']['MaximumItemCount'] = isset($_POST['goumainum']) ? $_POST['goumainum'] : '';
        $BuyerRequirementDetails['MaximumItemRequirements']['main_score'] = isset($_POST['maijiaxinyong']) ? $_POST['maijiaxinyong'] : '';
        $BuyerRequirementDetails['MaximumItemRequirements']['MinimumFeedbackScore'] = isset($_POST['maijiaxinyongnum']) ? $_POST['maijiaxinyongnum'] : '';

        $info['BuyerRequirementDetails'] = serialize($BuyerRequirementDetails);
        unset($BuyerRequirementDetails);







        $info['item_location'] = $_POST['item_location'];

        $info['item_country'] = $_POST['country'];
        $info['item_post'] = $_POST['item_post'];


        $info['inter_process_day'] = $_POST['guoneichulishijian'];
        $info['inter_fast_send'] = isset($_POST['guoneikuaisu']) ? $_POST['guoneikuaisu'] : '';





        $info['inter_trans_type'] = $_POST['guoneiyunshu1'];
        $info['inter_trans_cost'] = $_POST['guoneiyunfei1'];
        $info['inter_free'] = isset($_POST['guoneimianfei1']) ? $_POST['guoneimianfei1'] : '';
        $info['inter_trans_extracost'] = $_POST['guoneiewaijiashou1'];
        $info['inter_trans_AK_extracost'] = isset($_POST['guoneiAKewaijiashou1']) ? $_POST['guoneiAKewaijiashou1'] : 0.00;






        for($i=1;$i<6;$i++){
            $info['international_type'.$i] = isset($_POST['yunshufangshi'.$i]) ? $_POST['yunshufangshi'.$i] : '';//que
            $info['international_cost'.$i] = isset($_POST['yunfei'.$i]) ? $_POST['yunfei'.$i] : 0.00;
            $info['international_free'.$i] = isset($_POST['mianfei'.$i]) ? $_POST['mianfei'.$i] : '';
            $info['international_extracost'.$i] = isset($_POST['ewai'.$i]) ? $_POST['ewai'.$i] : 0.00;
            $info['international_is_worldwide'.$i] = isset($_POST['Worldwide'.$i]) ? $_POST['Worldwide'.$i] : '';//que1
            $info['international_is_country'.$i] = isset($_POST['guanjia'.$i]) ? json_encode($_POST['guanjia'.$i]) : '';

        }




        $info['excludeship'] = isset($_POST['excludeship'])?$_POST['excludeship']:'';

        if(!empty($info['id']))
        {
            $id = $info['id'];
            unset($info['id']);
            $reinfo = $this->Ebay_transtemplate_model->update($info,$id);
            if($reinfo==1)
            {
                ajax_return('修改成功',2);
                die;
            }
            else
            {
                ajax_return('修改失败',2);
                die;
            }

        }

        $re=    $this->Ebay_transtemplate_model->add($info);

        if($re)
        {
            ajax_return('新增成功',1,$re);
            die;
        }else
        {
            ajax_return('新增失败',2);
        }
    }

    //删除运输模板信息
    public function deleteTransTemplate()
    {
        $id = $_POST['id'];
        $re = $this->Ebay_transtemplate_model->delect($id);
        if($re)
        {
            echo json_encode(array('msg' => '删除成功', 'status' => 1));
        }
        else
        {
            echo json_encode(array('msg' => '删除失败', 'status' => 0));
        }

    }

    //根据选择的运输模板 返回对应的信息；
    public function autosettrans()
    {
        $arr['id'] = $_POST['transvalue'];
        $re =$this->Ebay_transtemplate_model->getTemplateAll($arr);
        if(!empty($re))
        {
            if($re[0]['siteid'] != $_POST['site'])
            {
                ajax_return('该模板不适合该站点使用',2);
                die;
            }
            else
            {
                $arr= array();
                $arr['internationalservice'] = 'true';
                $arr['validforsellingflow'] ='true';

                $arr['siteid'] = $re[0]['siteid'];
                $result = $this->details->getDetailsAll($arr);
                $options = '';
                $options .='<option value="">--请选择--</option>';
                if($result)
                {
                    foreach ($result as $row){
                        $options .= '<option value="'.$row['shippingservice'].'">'.$row['description'].'</option>';
                    }
                }
                $data['guowaitrans'] = $options;

                for($i=1;$i<6;$i++)
                {
                    if($re[0]['international_is_country'.$i] !='')
                    {
                        $re[0]['international_is_country'.$i] = json_decode($re[0]['international_is_country'.$i],true);
                    }
                }

                $data['transinfo'] = $re[0];
                ajax_return('',1,$data);
                die;
            }
        }
        ajax_return('找不到该模板信息，设置失败',2);
        die;
    }


    public function copyEbayInfo()
    {
        $accountIds = $_POST['accountIds'];
        $ids= $_POST['ids'];
        $idarr = explode(',',$ids);
        $accountIdsArr = explode(',',$accountIds);

        $ebayaccountoption=array();
        $ebayaccountoption['where']['token_status'] =1;
        $result = $this->userToken->getAll2Array($ebayaccountoption);

        $accountresult = array();
        foreach($result as $re)
        {
            $accountresult[$re['token_id']] = $re['seller_account'];
        }





        foreach($idarr as $id)
        {
            $listioption = array();
            $listioption['where']['id'] = $id;
            $listinfo =  $this->Ebay_list_model->getOne($listioption,true);
            foreach($accountIdsArr as $account)
            {
                $newlistinfo = $listinfo;
                $newlistinfo['ebayaccount'] = $accountresult[$account];
                $accountpaypal = array();
                $accountpaypal['where']['ebay_account'] = $newlistinfo['ebayaccount'];

                $accountpaypalresulte = $this->Ebay_accountpaypal_model->getOne($accountpaypal,true);

                $newpaypal = explode(',',$accountpaypalresulte['paypal_account']);

                $newlistinfo['paypal_account'] = $newpaypal[0];

                $newlistinfo['status']=1;
                $newlistinfo['itemid']='';
                $this->Ebay_list_model->add($newlistinfo);
            }
        }
        ajax_return('',1);

    }


    public function modifyTitle()
    {


        $id  = $_POST['item'];

        $new_title = $_POST['textnewval'];

        $listoption =array();

        $listoption['where']['id'] = $id;

        $list_result =   $this->Ebay_list_model->getOne($listoption,true);


        $token_result = $this->userToken->getInfoByAccount($list_result['ebayaccount']);

        $this->ebaytest->setinfo($token_result['user_token'],$token_result['devid'],$token_result['appid'],$token_result['certid'],$list_result['site'],'ReviseItem');

        $api_result = $this->ebaytest->modifyTittle($token_result['user_token'],$list_result['itemid'],$new_title);


        $responseDoc = new DomDocument();
        $responseDoc->loadXML($api_result);
        $response = simplexml_import_dom($responseDoc);

        if(($response->Ack=='Success')||isset($response->ItemID)) //成功的时候
        {
            $new_optin = array();
            $new_optin['where']['id'] = $id;

            $new_info = array();

            $new_info['title'] =$new_title;


            $this->Ebay_list_model->update($new_info,$new_optin);
            ajax_return('',1);
        }
        else //失败的时候
        {
            ajax_return('',2);
        }

    }
    public function ajaxUploadDirImageNew(){
        $dirName = strtoupper(trim($_POST['sku']));
     //   $dirName='JJ1221';
        $url ='http://120.24.100.157:3000/api/sku/'.$dirName.'?include-sub=true&distinct=true';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        $result = json_decode($result,true);
        //var_dump($result);exit;

        $pic_array= array();
       if(!empty($result))
       {
           foreach($result as $re)
           {
               $pic_array[] = $re['url'];
           }
       }
       $return_pic_array=array();
        if(!empty($pic_array))
        {
            foreach($pic_array as $pic)
            {
              //  $mid= str_replace("image", "image-resize/1000x1000x100", $pic);
                $last = 'http://imgurl.moonarstore.com'.$pic;
                $return_pic_array[] = $last;
            }
            ajax_return('', 1, $return_pic_array);
        }

    }


    public function autopaypal(){
        $site  = $_POST['site'];
        $account  = $_POST['account'];
        $price = $_POST['price'];
        $option_site['where']['siteid'] = $site;
        $site_info = $this->Ebay_ebaysite_model->getOne($option_site,true);
        $paypal = array(
            'USD'=>10,
            'CAD'=>10,
            'AUD'=>10,
            'GBP'=>5.8,
            'EUR'=>13.5
        );
        if($price>= $paypal[$site_info['currency']]){
            $check = 1; // 选择第一个
        }else{
            $check = 2; //选择第二个
        }
        $option_account['where']['ebay_account'] = $account;
        $account_info  = $this->Ebay_accountpaypal_model->getOne($option_account,true);

        $account_array = explode(',',$account_info['paypal_account']);
        unset($account_info);
        unset($option_site);
        unset($option_account);
        $string = '';
        $i =1;

        foreach($account_array as $ac){
            $new_pa = $ac;
            $new_pa =  substr_replace($new_pa,'****','2','6');
            if($i==$check){
                $string=$string.'<option selected="selected"  value="'.$ac.'">'.$new_pa.'</option>';

            }else{
                $string=$string.'<option value="'.$ac.'">'.$new_pa.'</option>';

            }
            $i++;
        }
        ajax_return('',1,$string);

    }








    /*
     * 新增广告页面
     */
    public function add(){

        $category_id = $this->input->get_post('category_id');

        $site_id = $this->input->get_post('site_id');
        $category_id_second = $this->input->get_post('category_id_second');
        $id = $this->input->get_post('id');

        if($category_id){  //
            $data= array();
            $data['category_id'] =  $category_id;
            $data['site']  = $site_id;
            $data['site_name'] = $this->ebaysite->getSiteNameBySiteId($site_id);
            $in =array('token_id'=>array(5,6,7,8,9,11,12,14,13,15,16,17,18,19,20,21,22,24,25,43,45,46,47,51,56,57));
            $data['token_arr'] = $this->userToken->getAll2Array(array('where'=>array('token_status'=>1), 'where_in' => $in,));
            $data['ebay_country'] = $this->country->getAll2Array();
            $data['ebay_condition']  = $this->condition->getConditionBySite($category_id,$site_id);

            if(!$data['ebay_condition']){
                    $condition = $this->getConditionNew($category_id,$site_id);
                    if(!$condition){
                        $data['ebay_condition'] ='';
                        /*echo "获取出错";
                        exit;*/
                    }else{
                        $data['ebay_condition']  = $this->condition->getConditionBySite($category_id,$site_id);
                    }
            }
            if(isset( $data['ebay_condition'][0]['variationsenabled'])){
                $data['support_multi'] = $data['ebay_condition'][0]['variationsenabled']; //判断该分类是否支持多属性
            }else{
                $data['support_multi'] = true;
            }

            //getSpecificsNew
            $data['ebay_specifics'] =$this->Ebay_specifics_new_model->getSpecificsBySite($category_id,$site_id);
            if(!$data['ebay_specifics']){
                $specifics = $this->getSpecificsNew($category_id,$site_id);
                if(!$specifics){
                    echo "获取出错";
                    exit;
                }else{
                    $data['ebay_specifics'] =$this->Ebay_specifics_new_model->getSpecificsBySite($category_id,$site_id);
                }
            }

            $data['templatehtml'] = $this->ebaytemplatehtml->getAll2Array(array('select'=>'id,template_name',)); //描述模板

            $data['template'] = $this->ebaytemplate->getAll2Array(array('select'=>'id,name',)); //卖家描述


            $data['transtemplate'] = $this->Ebay_transtemplate_model->getAll2Array(array('where'=>array('siteid'=>$site_id)));


            $data['category_name']  =$this->category->getCategoryFullNameChilden($category_id,$site_id);
            if(!empty($category_id_second)){
                $data['category_id_second'] = $category_id_second;
                $data['category_name_second']  =$this->category->getCategoryFullNameChilden($category_id_second,$site_id);
            }

          if(!empty($id)){
              $product_info = $this->Ebay_product_model->getOne(array('where'=>array('id'=>$id)),true);
              $data['product_info']=$product_info;
          }
            $data['account_array'] = $this->Ebay_user_tokens_model->getAllAccount();

            $trans = array();
            $trans['siteid'] =$site_id;
            $trans['validforsellingflow'] ='true';
            $data['guoneitrans'] =  $this->details->getDetailsAll($trans);
            $trans['internationalservice'] ='true';
            $data['guowaitrans'] =  $this->details->getDetailsAll($trans);
            unset($trans);







            /* if(isset($this->input->get_post('token_id'))){

             }*/
            $this->_template('admin/ebay/ebay_add',$data);

        }else{
            $site_option =array(
                'where'=>array(
                    'is_use' =>0,
                )
            );

            $site_list =   $this->ebaysite->getAll2Array($site_option);
            $data          = array(
                'site_list'  => $site_list,
              /*  'token_list' => $token_list,
                'token_id'   => $token_id*/
            );
            $this->_template('admin/ebay/category_choose', $data);
        }



    }


    public function set_shipment(){
        $id= $this->input->get_post("id");

        $result = $this->Ebay_transtemplate_model->getOne(array('where'=>array('id'=>$id)),true);

        $data['inter_trans_type']  = $result['inter_trans_type'];
        $data['inter_trans_cost']  = $result['inter_trans_cost'];
        $data['inter_trans_extracost']  = $result['inter_trans_extracost'];

        $data['international_type1'] = $result['international_type1'];
        $data['international_cost1'] = $result['international_cost1'];
        $data['international_extracost1'] = $result['international_extracost1'];
        $data['international_is_worldwide1'] = $result['international_is_worldwide1'];
       if(!empty($result['international_is_country1'])){
           $international_is_country1 = $result['international_is_country1'];
           $international_is_country1 =json_decode($international_is_country1,true);
           $international_is_country1 = implode(',',$international_is_country1);
           $data['international_is_country1'] = $international_is_country1;

       }else{
           $data['international_is_country1'] = '';

       }




        $data['international_type2'] = $result['international_type2'];
        $data['international_cost2'] = $result['international_cost2'];
        $data['international_extracost2'] = $result['international_extracost2'];
        $data['international_is_worldwide2'] = $result['international_is_worldwide2'];
        $data['international_is_country2'] = $result['international_is_country2'];

        if(!empty($result['international_is_country2'])){
            $international_is_country2 = $result['international_is_country2'];
            $international_is_country2 =json_decode($international_is_country2,true);
            $international_is_country2 = implode(',',$international_is_country2);
            $data['international_is_country2'] = $international_is_country2;

        }else{
            $data['international_is_country2'] = '';

        }

        if(!empty($result['excludeship'])){
           // echo $result['excludeship'];
            $excludeship =    trim($result['excludeship']);
           // $excludeship =json_decode($excludeship,true);
           // $excludeship = implode(',',$excludeship);
            $data['excludeship'] = $excludeship;
        }else{
            $data['excludeship'] = '';
        }



        ajax_return('',1,$data);

    }

    /*
     * ajax选择分类
     */
    public function select_category(){
        $option =array();
        $CategorySiteID = $this->input->get_post('site');
        $CategoryLevel = $this->input->get_post("level");
        $CategoryParentID = $this->input->get_post("parentID");

        if($CategoryLevel=="start"){
            $option['CategoryLevel'] = 1;
        }else{
            $option['CategoryParentID'] = $CategoryParentID;
        }
        $option['CategorySiteID'] = $CategorySiteID;
      //  $option['Is_new'] = 0;
        $last_option = array(
            'select' =>'id,CategoryName,CategoryID,LeafCategory',
            'where' => $option

        );
        $site_result = $this->category->getAll2Array($last_option);
        if(!empty($site_result)){

            echo json_encode($site_result);
           // ajax_return('','1',$site_result);
        }else{
            ajax_return('','2');
        }


    }


    public function suggest_category(){
        $returnarr =array();
        $site = $this->input->post("site");
        $CategoryName = $this->input->post("keyword");

        if(is_numeric(trim($CategoryName))){
            $option = array();
            $option['where']['CategorySiteID'] = $site;
            $option['where']['CategoryID'] = trim($CategoryName);

            $result =$this->category->getOne($option,true);

            if(!empty($result)){
                $returnarr[0]['id']=$result['CategoryID'];
                //  echo $returnarr[$i]['id'];
                $returnarr[0]['category_name']  =$this->category->getCategoryFullNameChilden($result['CategoryID'],$site);;
                $returnarr[0]['percentItemfound']=100;
                ajax_return('',1,$returnarr);
            }else{
                ajax_return('',2);
            }

        }else{
            $result =   $this->userToken->getInfoByTokenId(4);
            $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$site,'GetSuggestedCategories');

            $lastinfo = $this->ebaytest->getSuggestedCategories($result['user_token'],$CategoryName);
            $responseDoc = new DomDocument();
            $responseDoc->loadXML($lastinfo);
            $response = simplexml_import_dom($responseDoc);
            if(!isset($response->SuggestedCategoryArray->SuggestedCategory))
            {
                ajax_return('',2);
            }
            $category =$response->SuggestedCategoryArray->SuggestedCategory;
            $i=0;
            foreach($category as $ac){
                // echo $ac->Category->CategoryID;
                $returnarr[$i]['id']=(int)$ac->Category->CategoryID;
                //  echo $returnarr[$i]['id'];
                $returnarr[$i]['category_name']  =$this->category->getCategoryFullNameChilden($returnarr[$i]['id'],$site);
                $returnarr[$i]['percentItemfound']=(int)$ac->PercentItemFound;
                //var_dump($ac);
                $i++;
            }
            ajax_return('',1,$returnarr);
        }

    }

    /*
     * 获取SKU信息
     */
    public function get_sku_info(){
        $sku = $this->input->post('sku');
        $type = $this->input->post('type');
        $option=array();
        $sku_array=array();
        $result_result =array();
        $option['select'] ="products_sku";
        $option['where']['productsIsActive']=1;
        $this->db->like('products_sku', trim($sku), 'after');
        if($type==3){

            $result = $this->Products_data_model->getAll2Array($option);
            if(!empty($result)){
                foreach($result as $re){
                    if(in_array($re['products_sku'],$sku_array)){
                        continue;
                    }
                    $sku_array[]=$re['products_sku'];
                }
            }
        }else{

            $result = $this->Products_data_model->getOne($option,true);


            if(!empty($result)){
                $sku_array[]=$result['products_sku'];
            }
        }

        $option['select'] ="products_html_mod";
        $this->db->like('products_sku', trim($sku), 'after');
        $products_html_mod = $this->Products_data_model->getOne($option,true);



        $result_result['sku'] = $sku_array;
        $result_result['products_html_mod'] = htmlspecialchars_decode($products_html_mod['products_html_mod']);


        if(!empty($result_result['sku'])){
            ajax_return('',1,$result_result);
        }else{
            ajax_return('未找到SKU信息',2);
        }

    }

    public function doAction(){

        $action = $this->input->get_post("action");
        //echo $action;exit;
        if($action=='save'){
            $this->save($action);
        }

        if($action =='editAndPost'){
         $result=     $this->save($action,true);

            $this->publish_new($result);
            var_dump($result);exit;
        }
       // $this->save();

      //  var_dump($_REQUEST);exit;
        $data = array();

       /* if(isset($this->input->get_post('token_id'))){

        }*/
        $this->_template('admin/ebay/ebay_add',$data);
    }
    public function save($action,$exit=false){

        header('Content-Type: text/html; Charset=utf-8');
        //提及数据
        $posts = $this->input->post();


        $account =$posts['choose_account'];


     //   var_dump($posts);exit;
        $return_result =array();
        $i =0;
        $id=isset($posts['id'])?$posts['id']:'';


        if(isset($posts['specify_image'])){ //说明存在 指定橱窗图
            $specify_image =$posts['specify_image']; //指定变量
        }


        foreach($account as $ac){


           $photo_url =   $this->userToken->getAccountPhotoUrl($ac);

            $is_add = true;
            $add_info  = array();
            $add_info['site'] = $posts['site'];

            $add_info['category_id'] = $posts['category_id'];
            $add_info['category_id_second'] = isset($posts['category_id_second'])?$posts['category_id_second']:'';

       //     $add_info['account_name'] = $posts['category_id'];
            $add_info['account_id'] = $ac;
            $add_info['ad_type'] = $posts['choose_type'];
            $add_info['title'] = isset($posts['account_tittle'][$ac]['tittle'])?$posts['account_tittle'][$ac]['tittle']:'';
            $add_info['subTitle'] = isset($posts['account_tittle'][$ac]['tittle_sub'])?$posts['account_tittle'][$ac]['tittle_sub']:'';

            $add_info['timeMax'] = $posts['publish_day'];


            if(isset($posts['skuSpe'])){
                $skuSpe_arr= array();
                foreach($posts['skuSpe'] as $key=> $skuSpe){
                    if(!empty($skuSpe)){
                        $skuSpe_arr[$key] = $skuSpe;
                    }
                }
            }


            $add_info['itemSpecifics'] = isset($skuSpe_arr)?json_encode($skuSpe_arr):'';





            $add_info['user_spe'] = isset($posts['user_spe'])?json_encode($posts['user_spe']):'';

            $add_info['upc'] = isset($posts['needupc'])?$posts['needupc']:'';
            $add_info['ean'] = isset($posts['needean'])?$posts['needean']:'';
            $add_info['isbn'] = isset($posts['needisbn'])?$posts['needisbn']:'';

            $add_info['condition'] = isset($posts['ebay_condition'])?$posts['ebay_condition']:'';
            $add_info['condition_detail'] = isset($posts['ebay_condition_detail'])?$posts['ebay_condition_detail']:'';

            $account_name = $this->userToken->getNameById($add_info['account_id']);
            $account_suffix = $this->Ebay_accountpaypal_model->getAccountSuffix($account_name);

            if(!$account_suffix){
                $return_result[$i]['is_success'] = false;
                $return_result[$i]['id'] = '';
                $return_result[$i]['info'] ='新增失败,未匹账号后缀';
                $return_result[$i]['account'] = $this->userToken->getNameById($ac);
                $i++;
                continue;
            }




            //防止 以前就带有[] 先去掉下【】
            if (stripos($posts['ebay_sku'], '#') !== false) {
               // $posts['ebay_sku'] = preg_replace('/\[.*\]/', '', $posts['ebay_sku']);
                $ebay_sku = explode('#',$posts['ebay_sku']);
                $posts['ebay_sku'] = $ebay_sku[0];
            }


            $add_info['ebay_sku'] = $posts['ebay_sku']."#".$account_suffix;

            $add_info['erp_sku'] = $this->Ebay_product_model->getNewSku($add_info['ebay_sku']);



            if($add_info['ad_type'] !=3 ){
                $add_info['privateListing'] = isset($posts['privateListing'])?$posts['privateListing']:'';
                $add_info['ebay_price'] = $posts['ebay_price'];
                if(isset($posts['all_price'][$ac])&&!empty($posts['all_price'][$ac])){
                    $add_info['ebay_price'] = $posts['all_price'][$ac];
                }
                $add_info['ebay_quantity'] = $posts['ebay_quantity'];
            }else{


                $add_info['ebay_price'] = isset($posts['skuinfo']['price'][0])?$posts['skuinfo']['price'][0]:'';
                $skuinfo = isset($posts['skuinfo'])?$posts['skuinfo']:'';

                    if(isset($posts['all_price'][$ac])&&!empty($posts['all_price'][$ac])){
                        $mid_price = $add_info['ebay_price'] - $posts['all_price'][$ac];

                        foreach($skuinfo['price'] as $k=>$v){
                            $skuinfo['price'][$k] = $skuinfo['price'][$k]-$mid_price;
                        }
                    }

                foreach($skuinfo['sku'] as $k=>$v){
                    //防止 以前就带有[] 先去掉下【】
                    if (stripos($skuinfo['sku'][$k], '#') !== false) {
                        $ebay_sku = explode('#',$skuinfo['sku'][$k]);
                        $skuinfo['sku'][$k] = $ebay_sku[0];
                       // $skuinfo['sku'][$k] = preg_replace('/\[.*\]/', '', $skuinfo['sku'][$k]);
                    }

                    $skuinfo['sku'][$k] = $skuinfo['sku'][$k]."#".$account_suffix;
                }

                $add_info['ebay_quantity'] = isset($posts['skuinfo']['quantity'][0])?$posts['skuinfo']['quantity'][0]:'';
                $add_info['skuinfo'] = isset($skuinfo)?json_encode($skuinfo):'';
                $add_info['zidingyi'] = isset($posts['zidingyi'])?json_encode($posts['zidingyi']):'';


                $detailPicListMul  = isset($posts['detailPicListMul'])?$posts['detailPicListMul']:'';

                if(!empty($detailPicListMul)&&$photo_url){
                    foreach($detailPicListMul as $key=> $pic){
                        $pic =  str_replace('imgurl.moonarstore.com',$photo_url,$pic);
                        $detailPicListMul[$key] = $pic;
                    }
                }


                $add_info['detailPicListMul'] = json_encode($detailPicListMul);
            }



            $detailPicList=isset($posts['detailPicList'])?$posts['detailPicList']:'';
            if(isset($specify_image)){
                if(!isset($specify_image[0])){
                    unset($specify_image);
                    $specify_image =$posts['specify_image'];


                }
               // var_dump($specify_image);exit;

                $un_key =  array_search($specify_image[0],$detailPicList);
                unset($detailPicList[$un_key]); //删除这个元素
                array_unshift($detailPicList,$specify_image[0]); //首部加个元素
                unset($specify_image[0]);//把这个变量的值也去除
                if(is_array($specify_image)){
                    $specify_image = array_values($specify_image);
                }


             /*   $rank = array_rand($specify_image);
                //echo $specify_image[$rank];

                 $un_key =  array_search($specify_image[$rank],$detailPicList);
                if($un_key){ //说明找到了 key 的位置
                    unset($detailPicList[$un_key]); //删除这个元素
                    array_unshift($detailPicList,$specify_image[$rank]); //首部加个元素
                    unset($specify_image[$rank]);//把这个变量的值也去除
                }*/

              //  var_dump($detailPicList);exit;

            }


            if($photo_url){
                foreach($detailPicList as $key=> $pic){
                    $pic =  str_replace('imgurl.moonarstore.com',$photo_url,$pic);
                    $detailPicList[$key] = $pic;
                }
            }



            $add_info['detailPicList'] = isset($detailPicList)?json_encode($detailPicList):'';

            $add_info['templatehtml'] =$posts['templatehtml'];

            $add_info['template'] = $posts['template'];
            $add_info['template_titlle'] = $posts['template_titlle'];

            $detailPicListDescription= isset($posts['detailPicListDescription'])?$posts['detailPicListDescription']:'';
            if(!empty($posts['detailPicListDescription'])&&$photo_url){
                foreach($detailPicListDescription as $key=> $pic){
                    $pic =  str_replace('imgurl.moonarstore.com',$photo_url,$pic);
                    $detailPicListDescription[$key] = $pic;
                }
            }


            $add_info['detailPicListDescription'] = json_encode($detailPicListDescription);

            $add_info['detail']  = htmlspecialchars($posts['detail']);

            $add_info['transtemplate'] = $posts['transtemplate'];

            //var_dump($posts);exit;

            $ShippingServiceOptions = array();
            $ShippingServiceOptions[1]['ShippingService'] = !empty($posts['guoneiyunshu1'])?$posts['guoneiyunshu1']:'';
            $ShippingServiceOptions[1]['ShippingServiceCost'] = !empty($posts['guoneiyunfei1'])?$posts['guoneiyunfei1']:0.00;
            $ShippingServiceOptions[1]['ShippingServiceAdditionalCost'] = $posts['guoneiewaijiashou1'];
            $add_info['shippingServiceOptions'] = serialize($ShippingServiceOptions);

            $InternationalShippingServiceOption = array();
            for($k=1;$k<3;$k++){
                $InternationalShippingServiceOption[$k]['ShippingService'] = $posts['yunshufangshi'.$k];
                $InternationalShippingServiceOption[$k]['ShippingServiceCost'] = !empty($posts['yunfei'.$k])?$posts['yunfei'.$k]:0.00;
                $InternationalShippingServiceOption[$k]['ShippingServiceAdditionalCost'] = !empty($posts['ewai'.$k])?$posts['ewai'.$k]:0.00;
                if((isset($posts['Worldwide'.$k]))&&($posts['Worldwide'.$k]=='on')){
                    $InternationalShippingServiceOption[$k]['ShipToLocation'] ="Worldwide";
                }else{
                    $ShipToLocation=explode(',',$posts['guanjia'.$k][0]);
                    $InternationalShippingServiceOption[$k]['ShipToLocation']= json_encode($ShipToLocation);
                }
            }
            $add_info['internationalShippingServiceOption'] = serialize($InternationalShippingServiceOption);
            $transtemplate_result = $this->Ebay_transtemplate_model->getOne(array('where'=>array('id'=>$add_info['transtemplate'])),true);


            if(!empty($transtemplate_result)){
                $add_info['returnPolicy'] = $transtemplate_result['ReturnPolicy'];
                $add_info['buyerRequirementDetails'] = $transtemplate_result['BuyerRequirementDetails'];
                $add_info['inter_process_day'] = $transtemplate_result['inter_process_day'];
                $product_info = array();
                $product_info['item_location'] =$transtemplate_result['item_location'];
                $product_info['item_country'] =$transtemplate_result['item_country'];
                $product_info['item_post'] =$transtemplate_result['item_post'];
                $add_info['product_info'] = serialize($product_info);

              //  $add_info['internationalShippingServiceOption'] = $transtemplate_result['InternationalShippingServiceOption'];
            }


            $add_info['un_ship'] = $posts['un_ship'];

            $add_info['creat_time'] = date('Y-m-d H:i:s',time());

            $currency = $this->ebaysite->getSignCurrency($add_info['site']);

            $account_name = $this->userToken->getNameById($add_info['account_id']);

            $paypal = $this->Ebay_accountpaypal_model->getPayPalByPrice($account_name,$add_info['ebay_price'],$currency);

            if(!$paypal){
                $return_result[$i]['is_success'] = false;
                $return_result[$i]['id'] = '';
                $return_result[$i]['info'] ='新增失败,未匹配到paypal';
                $return_result[$i]['account'] = $this->userToken->getNameById($ac);
                $i++;
                continue;
            }

            $un_set_store_category =array(15,43,47); // 这几个账号没有店铺分类 不验证
            if(!in_array($add_info['account_id'],$un_set_store_category)){
                $store_category_id = $this->Ebay_store_with_category_model->getStoreCategoryBySkuTokenId($add_info['ebay_sku'],$add_info['account_id']);

                if(!$store_category_id){
                    $return_result[$i]['is_success'] = false;
                    $return_result[$i]['id'] = '';
                    $return_result[$i]['info'] ='新增失败,未匹配到该账号对应的店铺分类';
                    $return_result[$i]['account'] = $this->userToken->getNameById($ac);
                    $i++;
                    continue;
                }
                $add_info['store_category_id'] =$store_category_id;
            }


            $add_info['payPalEmailAddress'] =$paypal;

            $add_info['add_user'] =  $this->user_info->id;//登录用户的信息;

            if(!empty($posts['id'])){
                $ids = explode(',',$id);
                foreach($ids as $v){
                    if(!empty($v)){
                        $search =array();
                        $search['where']['id'] = $v;
                        $search['where']['site'] = $add_info['site'];
                        $search['where']['account_id'] = $ac;
                        $mark_id =  $v;
                        $one_result = $this->Ebay_product_model->getOne($search);
                        unset($search);
                        if(!empty($one_result)){
                            $is_add=false;
                            break;
                        }
                    }
                }
            }



            if($is_add){
                $add_info['status']  = 1;
                $add_result = $this->Ebay_product_model->add($add_info);
                if($add_result){
                    $return_result[$i]['is_success'] = true;
                    $return_result[$i]['id'] = $add_result;
                    $return_result[$i]['info'] ='新增成功';
                    $return_result[$i]['account'] = $this->userToken->getNameById($ac);
                }else{
                    $return_result[$i]['is_success'] = false;
                    $return_result[$i]['id'] = $add_result;
                    $return_result[$i]['info'] ='新增失败';
                    $return_result[$i]['account'] =$this->userToken->getNameById($ac);
                }
            }else{
              $update_result =  $this->Ebay_product_model->update($add_info,array('where'=>array('id'=>$mark_id)));
                if($update_result){
                    $return_result[$i]['is_success'] = true;
                    $return_result[$i]['id'] = $mark_id;
                    $return_result[$i]['info'] ='更新成功';
                    $return_result[$i]['account'] = $this->userToken->getNameById($ac);
                }else{
                    $return_result[$i]['is_success'] = false;
                    $return_result[$i]['id'] = $mark_id;
                    $return_result[$i]['info'] ='更新失败';
                    $return_result[$i]['account'] = $this->userToken->getNameById($ac);
                }

            }

            $i++;
      //      var_dump($add_info);
        }
        $info ='';
        $data ='';
        foreach($return_result as $re){
            $info = $info.'<br>'.$re['account'].' :'.$re['info'];
            if($re['is_success']){
                $data = $re['id'].','.$data;
            }
        }
        if($exit){
            return $return_result;
        }else{
            ajax_return($info,1,$data);
        }

    }



    public function publish_new($data){
        $return_data =array();
        foreach($data as $v){
            if($v['is_success']){
                $info =  $this->Ebay_product_model->getOne(array('where'=>array('id'=>$v['id'])),true);

                if($info['status'] ==1){
                    $is_repal = $this->Ebay_product_model->checkProduct($info['site'],$info['account_id'],$info['erp_sku']);
                    if($is_repal){
                        $info['Currency'] =  $this->ebaysite->getSignCurrency($info['site']);
                        $info['sitename'] = $this->ebaysite->getSignName($info['site']);

                        $result =   $this->userToken->getInfoByTokenId($info['account_id']);
                        if($info['ad_type']==3){
                            $api ='AddFixedPriceItem';
                        }else{
                            $api ='AddItem';
                        }

                        $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$info['site'],$api);


                        $info_html = array();
                        $info_html['template_title'] = $info['template_titlle'];
                        $info_html['publication_template'] = $info['template'];
                        $info_html['publication_template_html'] = $info['templatehtml'];
                        $info_html['description_details'] = htmlspecialchars_decode($info['detail']);
                        $info_html['template_deteils'] = $info['detailPicList'];
                        $detail_info = $this->reassemble_info($info_html);
                        $info['detail'] = $detail_info['description_details_new'];
                        //    echo $info['detail'];exit;
                        if(!empty($info['store_category_id'])){
                            $info['store_category_name'] = $this->Ebay_store_category_model->getCategoryNameById($info['account_id'],$info['store_category_id']);
                        }

                        $result_ad = $this->ebaytest->publish_new($api,$info['ad_type'],$info);
                        if($result_ad['is_success']){
                            $updata_data =array();
                            $updata_data['itemid'] = $result_ad['info'];
                            $updata_data['status'] = 2;
                            $this->Ebay_product_model->update($updata_data,array('where'=>array('id'=>$v['id'])));
                            $v['info'] ='刊登成功    '.$result_ad['info'];
                        }else{

                            $data=array();
                            $data['publish_error'] = $result_ad['info'];
                            $this->Ebay_product_model->update($data,array('where'=>array('id'=>$v['id'])));

                            $data =array();
                            $data['listid'] =$v['id'];
                            $data['specificissues'] =$result_ad['info_all'];
                            $this->Ebay_operationlog_model->add($data);

                            $v['info'] ='刊登失败    '.$result_ad['info'];
                        }
                    }else{
                        $v['info'] ='刊登失败,检查为重复刊登   ';
                    }

                }else{
                    $v['info'] ='刊登失败,已经刊登    ';
                }

            }
            $return_data[] = $v;
        }

        $info='';
        $data='';
        foreach($return_data as $re){
            $info = $info.'<br>'.$re['account'].' :'.$re['info'];
            if($re['is_success']){
                $data = $re['id'].','.$data;
            }
        }
        ajax_return($info,1,$data);
       // var_dump($return_data);exit;
    }



    public function reassemble_info($info)  //将某些数据  重新组装一下
    {

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




    /**
     * @param $categoryid 分类id
     * @param $site 站点
     * @return bool
     */
    public function getConditionNew($categoryid,$site)
    {
        $result = $this->userToken->getInfoByTokenId(4);
        $this->ebaytest->setinfo($result['user_token'], $result['devid'], $result['appid'], $result['certid'], $site, 'GetCategoryFeatures');
        $condionResult = $this->ebaytest->getCondition($result['user_token'], $categoryid, $site);

        $responseDoc = new DomDocument();
        $responseDoc->loadXML($condionResult);
        $response = simplexml_import_dom($responseDoc);


        if (isset($response->Category->ConditionValues->Condition)) {
            $condition = $response->Category->ConditionValues->Condition;
            $arr = array();

            $arr['categoryid'] = $categoryid;
            $arr['siteid'] =$site;
            $arr['variationsenabled'] = isset($response->Category->VariationsEnabled) ? (string)$response->Category->VariationsEnabled : '';
            $arr['conditionenabled'] = isset($response->Category->ConditionEnabled) ? (string)$response->Category->ConditionEnabled : '';
            $arr['upcenabled'] = isset($response->Category->UPCEnabled) ? (string)$response->Category->UPCEnabled : '';
            $arr['eanenabled'] = isset($response->Category->EANEnabled) ? (string)$response->Category->EANEnabled : '';
            $arr['isbnenabled'] = isset($response->Category->ISBNEnabled) ? (string)$response->Category->ISBNEnabled : '';
            $arr['update_time'] = date('Y-m-d H:i:s', time());
            foreach ($condition as $con) {
                $arr['condition_id'] = intval($con->ID);
                $arr['displayname'] = (string)$con->DisplayName;
                $infocondition =array();
                $infocondition['where']['categoryid'] = $arr['categoryid'];
                $infocondition['where']['condition_id'] = $arr['condition_id'];
                $infocondition['where']['siteid'] = $arr['siteid'];

                $conditionResult = $this->condition->getOne($infocondition);
                if (empty($conditionResult)) {
                    $this->condition->add($arr);
                } else {
                    $this->condition->update($arr,array('where'=>array('id'=>$conditionResult->id)));
                }
            }


            return true;
        }else{
            return false;
        }
    }


    public function getSpecificsNew($categoryid,$site){
     //   $arrinfo['categoryid'] = $_POST['categoryid'];

        $result =   $this->userToken->getInfoByTokenId(4);
        $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$site,'GetCategorySpecifics');

        $info = $this->ebaytest->getGetCategorySpecifics($result['user_token'], $categoryid);


        $responseDoc = new DomDocument();
        $responseDoc->loadXML($info);
        $response = simplexml_import_dom($responseDoc);


        if(isset($response->Recommendations->NameRecommendation))
        {
            $arr=array();
            $arr['categoryid'] = intval($response->Recommendations->CategoryID);
            $arr['site']= $site;

            $info = $response->Recommendations->NameRecommendation;


            foreach($info as $v)
            {
                $arr['name'] = (string)$v->Name;
                $arr['valuetype'] = isset($v->ValidationRules->ValueType)?(string)$v->ValidationRules->ValueType:'';
                $arr['minvalues'] = isset($v->ValidationRules->MinValues)?(string)$v->ValidationRules->MinValues:'';
                $arr['maxvalues'] = isset($v->ValidationRules->MaxValues)?(string)$v->ValidationRules->MaxValues:'';
                $arr['selectionmode'] = isset($v->ValidationRules->SelectionMode)?(string)$v->ValidationRules->SelectionMode:'';
                $arr['variationspecifics'] = isset($v->ValidationRules->VariationSpecifics)?(string)$v->ValidationRules->VariationSpecifics:'';
                $arr['updatetime'] =  date('Y-m-d H:i:s',time());

                $specificvalue = array();
                foreach($v->ValueRecommendation  as $i_v)
                {
                    $specificvalue[] = (string)$i_v->Value;
                }
                $arr['specificvalue']=serialize($specificvalue);

                $speinfo = array();
                $speinfo['where']['categoryid']=$categoryid;
                $speinfo['where']['site']=$site;
                $speinfo['where']['name']=$arr['name'];


                $returnSpeci = $this->Ebay_specifics_new_model->getOne($speinfo);
                if(empty($returnSpeci))
                {
                    $this->Ebay_specifics_new_model->add($arr);
                }
                else
                {
                    $this->Ebay_specifics_new_model->update($arr,array('where'=>array('id'=>$returnSpeci->id)));
                }
            }

            return true;

        }
        else
        {

           return false;
        }
    }


    public function getSkuPic(){
        $dirName = $this->input->get_post('sku');
        $dirName=trim(strtoupper($dirName));
        $sp = $this->input->get_post('type');
        $result_pic =array();
        if($sp=='sp'){
               $url=   "http://imgurl.moonarstore.com/get_image.php?dirName=".$dirName."&dir=SP";

        }else{
            $url=    "http://imgurl.moonarstore.com/get_image.php?dirName=".$dirName;

        }

     //   $url=   "http://imgurl.moonarstore.com/get_image.php?dirName=".$dirName."&dir=SP";


            $result =  $this->picCurl($url);



            if(!strpos((string)$result,'文件夹'))
            {

                    foreach($result as $ke => $v){

                        $result_pic[] = $v;
                    }

            }
        if(empty($result_pic)){

            $url=    "http://imgurl.moonarstore.com/get_image.php?dirName=".$dirName;
            $result =  $this->picCurl($url);



            if(!strpos((string)$result,'文件夹'))
            {

                foreach($result as $ke => $v){

                    $result_pic[] = $v;
                }

            }

        }
/**
    if(empty($result_pic) || count($result_pic)<5){
                $url ='http://120.24.100.157:3000/api/sku/'.$dirName.'?include-sub=true&distinct=true';
              $result=   $this->picCurl($url);
                $pic_array= array();echo '<meta charset="utf-8" /><br>------<pre>File: '.__FILE__.' Line:'.__LINE__.' output:';print_r($result);exit;
                if(!empty($result))
                {
                    foreach($result as $re)
                    {
                        $pic_array[] = $re['url'];
                    }
                }
                if(!empty($pic_array))
                {
                    foreach($pic_array as $pic)
                    {
                       // $mid= str_replace("image", "image-resize/1000x1000x100", $pic);
                        $last = 'http://imgurl.moonarstore.com'.$pic;
                        $result_pic[] = $last;
                    }
                }

            }
            /**/
        
            if(empty($result_pic) || count($result_pic)<5){
                $url ='http://120.24.100.157:70/getSkuImageInfo/getSkuImageInfo.php?distinct=true&include_sub=true&sku='.$dirName;
              $result=   $this->picCurl($url);
                $pic_array= array();
                if(!empty($result))
                {
                    foreach($result as $re)
                    {
                        $pic_array[] = '/getSkuImageInfo-resize/sku/'.$re['filename'];
                    }
                }
                if(!empty($pic_array))
                {
                    foreach($pic_array as $pic)
                    {
                       // $mid= str_replace("image", "image-resize/1000x1000x100", $pic);
                        $last = 'http://imgurl.moonarstore.com'.$pic;
                        $result_pic[] = $last;
                    }
                }

            }
        if(!empty($result_pic)){
            $result_pic_return['pic'] = $result_pic;
            ajax_return('',1,$result_pic);
        }else{
            ajax_return('未找到图片信息',2);
        }
    //    var_dump($result_pic);exit;

    }

    public function picCurl($url){
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        $result = json_decode($result,true);
        return $result;
    }


}