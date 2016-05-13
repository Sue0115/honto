<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/27
 * Time: 10:09
 */
set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");

class Category extends MY_Controller{
    protected $ebay;
    protected $userToken;
    protected $model;

    function __construct()
    {
        parent::__construct();
        $this->load->library('MyEbayNew');
        $this->load->model(array(
            'ebay/Ebay_user_tokens_model',
            'ebay/Ebay_categoty_model',
            'ebay/Ebay_condition_model',
            'ebay/Ebay_specifics_model',
            'ebay/Ebay_store_category_model',
            'ebay/Ebay_operationlog_model'

        ));
        $this->ebaytest = new MyEbayNew();
        $this->userToken = $this->Ebay_user_tokens_model;
        $this->category = $this->Ebay_categoty_model;
        $this->condition =$this->Ebay_condition_model;
        $this->specifics =$this->Ebay_specifics_model;
    }


    //获取对应站点的分类信息
    function getCategoryList()
    {
        //$site =$_GET['site'];
        $result =   $this->userToken->getInfoByTokenId(4);
       $site =0;

        $option_set_new =array();
        $option_set_new['where']['CategorySiteID'] = $site;
        $update_data = array();
        $update_data['Is_new'] = 1;
        $this->category->update($update_data,$option_set_new);

        $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$site,'GetCategories');
        $aww['CategoryLevel'] = 1;
        $aww['CategorySiteID'] =$site;
        $resultall = $this->category->getCategotyAll($aww);
        if(empty($resultall))
        {
            $xml = "<?xml version='1.0' encoding='utf-8'?>
                <GetCategoriesRequest xmlns='urn:ebay:apis:eBLBaseComponents'>
                <DetailLevel>ReturnAll</DetailLevel>
                <LevelLimit>1</LevelLimit>";
            $xml.='<CategorySiteID>'.$site.'</CategorySiteID>';
            $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
            $xml .=' </GetCategoriesRequest>';
            $info = $this->ebaytest->sendHttpRequest($xml);
            $responseDoc = new DomDocument();
            $responseDoc->loadXML($info);
            $response = simplexml_import_dom($responseDoc);
            $entries = $response->CategoryArray->Category; //获取需要的信息

            foreach ($entries as $K=>$v ) {
                $CategoryID = intval($v->CategoryID);
                $arr['CategoryID']= $CategoryID;
                $arr['CategorySiteID']=$site;
                $v->CategorySiteID=$site;
                $is_setCategory = $this->category->getCategotyOne($arr);


                $infoall =array();
                $infoall['BestOfferEnabled']=isset($v->BestOfferEnabled)?(string)$v->BestOfferEnabled:'';
                $infoall['AutoPayEnabled']=isset($v->AutoPayEnabled)?(string)$v->AutoPayEnabled:'';
                $infoall['CategoryID']=isset($v->CategoryID)?intval($v->CategoryID):'';
                $infoall['CategoryLevel']=isset($v->CategoryLevel)?intval($v->CategoryLevel):'';
                $infoall['CategoryName']=isset($v->CategoryName)?(string)$v->CategoryName:'';
                $infoall['CategoryParentID']=isset($v->CategoryParentID)?intval($v->CategoryParentID):'';
                $infoall['LeafCategory']=isset($v->LeafCategory)?(string)$v->LeafCategory:'';
                $infoall['LSD']=isset($v->LSD)?(string)$v->LSD:'';
                $infoall['IntlAutosFixedCat']=isset($v->IntlAutosFixedCat)?(string)$v->IntlAutosFixedCat:'';
                $infoall['CategorySiteID']=$site;
                $infoall['Is_new'] = 0;

                if($is_setCategory)
                {
                    $option = array();
                    $option['where']['CategoryID']=$CategoryID;
                    $option['where']['CategorySiteID'] = $site;

                    $this->category->update($infoall,$option);


                    continue;
                }
                else
                {

                    $this->category->categotyinfo($infoall);
                }
            }
            $this->getCategoryList();
        }
        else
        {
                foreach($resultall as $all)
                {
                    $xml = "<?xml version='1.0' encoding='utf-8'?>
                <GetCategoriesRequest xmlns='urn:ebay:apis:eBLBaseComponents'>
                <DetailLevel>ReturnAll</DetailLevel>
                <LevelLimit>6</LevelLimit>";
                    $xml.=' <CategoryParent>'.$all['CategoryParentID'].'</CategoryParent>';
                    $xml.='<CategorySiteID>'.$site.'</CategorySiteID>';
                    $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
                    $xml .=' </GetCategoriesRequest>';
                    $info = $this->ebaytest->sendHttpRequest($xml);
                    $responseDoc = new DomDocument();
                    $responseDoc->loadXML($info);
                    $response = simplexml_import_dom($responseDoc);

                    $entries = $response->CategoryArray->Category; //获取需要的信息

                    foreach ($entries as $K=>$v ) {
                        $CategoryID = intval($v->CategoryID);
                        $arr['CategoryID']= $CategoryID;
                        $arr['CategorySiteID']=$site;
                        $v->CategorySiteID=$site;
                        $is_setCategory = $this->category->getCategotyOne($arr);

                        $infoall =array();
                        $infoall['BestOfferEnabled']=isset($v->BestOfferEnabled)?(string)$v->BestOfferEnabled:'';
                        $infoall['AutoPayEnabled']=isset($v->AutoPayEnabled)?(string)$v->AutoPayEnabled:'';
                        $infoall['CategoryID']=isset($v->CategoryID)?intval($v->CategoryID):'';
                        $infoall['CategoryLevel']=isset($v->CategoryLevel)?intval($v->CategoryLevel):'';
                        $infoall['CategoryName']=isset($v->CategoryName)?(string)$v->CategoryName:'';
                        $infoall['CategoryParentID']=isset($v->CategoryParentID)?intval($v->CategoryParentID):'';
                        $infoall['LeafCategory']=isset($v->LeafCategory)?(string)$v->LeafCategory:'';
                        $infoall['LSD']=isset($v->LSD)?(string)$v->LSD:'';
                        $infoall['IntlAutosFixedCat']=isset($v->IntlAutosFixedCat)?(string)$v->IntlAutosFixedCat:'';
                        $infoall['CategorySiteID']=$site;
                        $infoall['Is_new'] = 0;

                        if($is_setCategory)
                        {
                            $option = array();
                            $option['where']['CategoryID']=$CategoryID;
                            $option['where']['CategorySiteID'] = $site;
                            $this->category->update($infoall,$option);
                            echo  $CategoryID.' 已经存在';
                            echo '<br/>';
                            continue;
                        }
                        else
                        {

                            echo  '<span style="color: #d12723">'. $v->CategoryID.'新增'.'</span>';
                            echo '<br/>';

                            $this->category->categotyinfo($infoall);
                        }
                    }
                }
        }
    }


    // 获取对应站点，分类的物品描述信息选择，及其是否支持多属性
    function getCategoryFeatures()
    {
        if(isset($_POST['categoryid']))
        {
            $info['ebayaccount'] = $_POST['account'];
            $CategoryID =$_POST['categoryid'];
            $CategorySiteID = $_POST['site'];
            if(($_POST['site']==0)||($_POST['site']==2)||($_POST['site']==3)||($_POST['site']==15))
            {
                $CategorySiteID=0;
            }
            $arr['CategoryID'] = $CategoryID;
            $arr['CategorySiteID'] = $CategorySiteID;
            $result = $this->userToken->getInfoByAccount($info['ebayaccount']);
            $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$arr['CategorySiteID'],'GetCategoryFeatures');

        }
        else
        {
            $CategoryID =20349;
            $CategorySiteID = 0;
            $arr['CategoryID'] = $CategoryID;
            $arr['CategorySiteID'] = $CategorySiteID;
            $result =   $this->userToken->getInfoByTokenId(1);
            $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$arr['CategorySiteID'],'GetCategoryFeatures');
        }

        $categoryinfo = $this->category->getCategotyOne($arr);
        $xml = "<?xml version='1.0' encoding='utf-8'?>
        <GetCategoryFeaturesRequest xmlns='urn:ebay:apis:eBLBaseComponents'>
        <DetailLevel>ReturnAll</DetailLevel>
        <FeatureID>ConditionEnabled</FeatureID>
        <FeatureID>ConditionValues</FeatureID>
        <FeatureID>ItemSpecificsEnabled</FeatureID>
        <FeatureID>VariationsEnabled</FeatureID>
        <FeatureID>UPCEnabled</FeatureID>
        <FeatureID>EANEnabled</FeatureID>
        <FeatureID>ISBNEnabled</FeatureID>";
        $xml .='<CategoryID>'.$categoryinfo['CategoryID'].'</CategoryID>';
        $xml .=' <CategorySiteID>'.$CategorySiteID.'</CategorySiteID>';
       // $xml .=' <OutputSelector> Category.UPCEnabled </OutputSelector>';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
        $xml .=' </GetCategoryFeaturesRequest>';
        $info = $this->ebaytest->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($info);
        $response = simplexml_import_dom($responseDoc);

        if(isset($response->Category->ConditionValues->Condition))
        {
            $condition = $response->Category->ConditionValues->Condition;
            $arr = array();
            $arr['category_id'] = $categoryinfo['id'];
            $arr['categoryid']=$categoryinfo['CategoryID'];
            $arr['siteid'] = $categoryinfo['CategorySiteID'];
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
        }
   /*     else
        {
            $arr = array();
            $arr['category_id'] = $categoryinfo['id'];
            $arr['categoryid']=$categoryinfo['CategoryID'];
            $arr['siteid'] = $categoryinfo['CategorySiteID'];
            $arr['condition_id'] ='1111';
            $arr['displayname']='Disabled';
            $this->category->categotyfeaturesinfo($arr);
        }*/
    }

    //根据站点，分类 获取对应的物品属性
    public function getGetCategorySpecifics()
    {
        if(isset($_POST['categoryid']))
        {
            $info['ebayaccount'] = $_POST['account'];
            $CategoryID =$_POST['categoryid'];
            $CategorySiteID = $_POST['site'];
            if(($_POST['site']==0)||($_POST['site']==2)||($_POST['site']==3)||($_POST['site']==15))
            {
                $CategorySiteID=0;
            }
            $result = $this->userToken->getInfoByAccount($info['ebayaccount']);
            $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],$CategorySiteID,'GetCategorySpecifics');
        }
        else
        {
            $CategoryID='20349';
            $CategorySiteID=0;
              $result =   $this->userToken->getInfoByTokenId(1);
               $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],0,'GetCategorySpecifics');
        }

        $xml ='<?xml version="1.0" encoding="utf-8"?>
            <GetCategorySpecificsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
              <WarningLevel>High</WarningLevel>
              <CategorySpecific>';
        $xml .=' <CategoryID>'.$CategoryID.'</CategoryID>';
         $xml .=' </CategorySpecific>';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
        $xml .='</GetCategorySpecificsRequest>';
        $info = $this->ebaytest->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($info);
        $response = simplexml_import_dom($responseDoc);


        $arr['categoryid'] = intval($response->Recommendations->CategoryID);
        $arr['side']= $CategorySiteID;
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
                $this->specifics->update($returnSpeci['id'],$arr);
            }
            }
        }
        else
        {
            $speinfo['categoryid']=$arr['categoryid'];
            $speinfo['side']=$arr['side'];
            $speinfo['name']='Disabled';
            $returnSpeci = $this->specifics->getSpecificsOne($speinfo);
            if(empty($returnSpeci))
            {
                $speinfo['updatetime'] =  date('Y-m-d H:i:s',time());
                $this->category->categotyspecificsinfo($speinfo);
            }
            else
            {
                $this->specifics->update($returnSpeci['id'],$speinfo);
            }
        }
        ajax_return('改分类信息更新成功，请重新选择');
    }

    public function getStoreCategory(){
        $token_id=$this->input->get_post("token_id");
        $result =   $this->userToken->getInfoByTokenId($token_id);
        $this->ebaytest->setinfo($result['user_token'],$result['devid'],$result['appid'],$result['certid'],0,'GetStore');
        $xml ='';
        $xml .='<?xml version="1.0" encoding="utf-8"?><GetStoreRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $xml .='<RequesterCredentials><eBayAuthToken>'.$result['user_token'].'</eBayAuthToken></RequesterCredentials>';
        $xml .='<LevelLimit>3</LevelLimit></GetStoreRequest>';
        $info = $this->ebaytest->sendHttpRequest($xml);
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($info);
        $response = simplexml_import_dom($responseDoc);
        if(isset($response->Store->CustomCategories->CustomCategory)) {
            $category = $response->Store->CustomCategories->CustomCategory;
            //先删除以前的
            $option = array();
            $option['where']['token_id'] = $token_id;
            $this->Ebay_store_category_model->delete($option);
            foreach ($category as $v1) {

                $add_data=array();
                $add_data['category_id'] = (string)$v1->CategoryID;
                $add_data['update_time'] = date('Y-m-d H:i:s');
                $add_data['category_name'] = (string)$v1->Name;
                $add_data['token_id'] = $token_id;
                $add_data['level'] = 1;
                $add_data['category_parent'] = '';
                $this->Ebay_store_category_model->add($add_data);


                if(isset($v1->ChildCategory)){
                    foreach($v1->ChildCategory as $v2){

                        $add_data=array();
                        $add_data['category_id'] = (string)$v2->CategoryID;
                        $add_data['update_time'] = date('Y-m-d H:i:s');
                        $add_data['category_name'] = (string)$v2->Name;
                        $add_data['token_id'] = $token_id;
                        $add_data['level'] = 2;
                        $add_data['category_parent'] = (string)$v1->CategoryID;
                        $this->Ebay_store_category_model->add($add_data);


                        if(isset($v2->ChildCategory)){

                            foreach($v2->ChildCategory as $v3){

                                $add_data=array();
                                $add_data['category_id'] = (string)$v3->CategoryID;
                                $add_data['update_time'] = date('Y-m-d H:i:s');
                                $add_data['category_name'] = (string)$v3->Name;
                                $add_data['token_id'] = $token_id;
                                $add_data['level'] = 3;
                                $add_data['category_parent'] = (string)$v2->CategoryID;
                                $this->Ebay_store_category_model->add($add_data);

                            }
                        }



                    }

                }

            }
            ajax_return('同步完成',1);
        }else{
           ajax_return('同步失败',2);
        }
    }

    //Ebay_store_category_model
    function getLastChildCategory($array,$token_id){

        foreach($array as $v){
            if(isset($v->ChildCategory)){
                $this->getLastChildCategory($v->ChildCategory,$token_id);

            }else{

                $add_data=array();
                $add_data['category_id'] = (string)$v->CategoryID;
                $add_data['update_time'] = date('Y-m-d H:i:s');
                $add_data['category_name'] = (string)$v->Name;
                $add_data['token_id'] = $token_id;
                $this->Ebay_store_category_model->add($add_data);

            }
        }
    }


}