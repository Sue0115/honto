<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/12
 * Time: 11:13
 */
set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");
class Ebay_template extends Admin_Controller{
    protected $ebay;
    protected $userToken;

    function __construct()
    {
        parent::__construct();
        $this->load->library('MyEbayNew');
        $this->load->model(array(
            'ebay/Ebay_user_tokens_model',
            'ebay/Ebay_template_model',
            'ebay/Ebay_template_html_model',
            'ebay/Ebay_accountpaypal_model',
            'ebay/Ebay_ebaysite_model',
            'ebay/Ebay_ebaydetails_model'
            //  'smt/Slme_smt_categorylist_model',
            // 'smt/Slme_smt_category_attribute_model',
        ));
        $this->ebaytest = new MyEbayNew();
        $this->userToken = $this->Ebay_user_tokens_model;
        $this->ebaytemplate=$this->Ebay_template_model;
        $this->ebaytemplatehtml=$this->Ebay_template_html_model;
        $this->paypallist = $this->Ebay_accountpaypal_model;
        // $this->model     = $this->Slme_smt_categorylist_model;
    }

    public function ebaymodel()
    {
        $arr = array();
        $data['template_list']  = $this->ebaytemplate->getTemplateAll($arr);
        // var_dump($result);
        $this->_template('admin/ebay/ebayTemplate',$data);
        // 	admin/ebay/  	ebay_product/ebaylistting 	admin
    }


    public function ebayTemplateinfo()
    {
        $this->_template('admin/ebay/ebayTemplateinfo');
    }

    public function  html()
    {
        $arr = array();
        $data['template_list']  = $this->ebaytemplatehtml->getTemplateAll($arr);
        $this->_template('admin/ebay/ebayTemplateHtml',$data);
    }

    public function accountpaypal()
    {
        $arr = array();
        $data['paypal_list']  = $this->paypallist->getinfoAll($arr);

        $this->_template('admin/ebay/ebayAccountPaypal',$data);
    }

    public function ebayTemplateHtmlInfo()
    {
        $this->_template('admin/ebay/ebayTemplateHtmlInfo');

    }

    public function htmlinfo()
    {
        $arr = array();
        $arr['id'] = $_GET['id'];
        $data['info'] = $this->ebaytemplatehtml->getTemplateAll($arr);
        $this->_template('admin/ebay/ebayTemplateHtmlInfo',$data);
    }

    public function info()
    {
        $arr = array();
        $arr['id'] = $_GET['id'];
        $data['info'] = $this->ebaytemplate->getTemplateAll($arr);
        $this->_template('admin/ebay/ebayTemplateinfo',$data);

    }


    public function htmladd()
    {

        $arr = array();
        $arr['template_name'] = empty($_POST['mobanname']) ? "" : $_POST['mobanname'];
        $arr['template_html'] = empty($_POST['content']) ? "" : $_POST['content'];
        $arr['id'] = empty($_POST['id']) ? "" : $_POST['id'];
        if (empty($arr['id'])) {
            $this->ebaytemplatehtml->add($arr);
            ajax_return('新增成功');


        }
        else
        {
            $this->ebaytemplatehtml->update($arr,$arr['id']);
            ajax_return('修改成功');
        }
    }


    public function infoadd()
    {
        $arr = array();
        $arr['name'] = empty($_POST['maijiamiaoshu']) ? "" : $_POST['maijiamiaoshu'];
        $arr['payment'] = empty($_POST['content']) ? "" : $_POST['content'];
        $arr['shipping'] = empty($_POST['content2']) ? "" : $_POST['content2'];
        $arr['sales_policy'] = empty($_POST['content3']) ? "" : $_POST['content3'];
        $arr['about_us'] = empty($_POST['content4']) ? "" : $_POST['content4'];
        $arr['contact_us'] = empty($_POST['content5']) ? "" : $_POST['content5'];
        $arr['id'] = empty($_POST['id']) ? "" : $_POST['id'];
        if (empty($arr['id'])) {
            $this->ebaytemplate->add($arr);
            ajax_return('新增成功');


        }
        else
        {
            $this->ebaytemplate->update($arr,$arr['id']);
            ajax_return('修改成功');
        }
    }
    public function deleteTemplateHtml()
    {
        $id = $_POST['id'];
        $re =  $this->ebaytemplatehtml->delect($id);
        if($re)
        {
            echo json_encode(array('msg' => '删除成功', 'status' => 1));
        }
        else
        {
            echo json_encode(array('msg' => '删除失败', 'status' => 0));
        }
    }

    public function deleteTemplate()
    {
        $id = $_POST['id'];
      $re =  $this->ebaytemplate->delect($id);
        if($re)
        {
            echo json_encode(array('msg' => '删除成功', 'status' => 1));
        }
        else
        {
            echo json_encode(array('msg' => '删除失败', 'status' => 0));
        }
    }

    public function add_paypal(){

        $data = array();
        $data['account']  =  $this->userToken->getAllAccount();
        $data['currency'] = $this->Ebay_ebaysite_model->getCurrency();
        $data['paypal'] = $this->Ebay_ebaydetails_model->getAccountList();
        $id = $this->input->get_post("id");

        if($id){
            $data['one_info'] = $this->paypallist->getOne(array('id'=>$id),true);

        }
      //  var_dump($_POST);exit;
        $this->_template('admin/ebay/ebay_paypal_add',$data);
    }

    public function save_paypal(){

        $posts = $this->input->post();
        $data['ebay_account'] = $posts['account'];
        $data['currency'] = json_encode($posts['currency']);
        $data['paypal_account'] = $posts['big_paypal'].','.$posts['small_paypal'];
        if(isset($posts['id'])&&!empty($posts['id'])){ //update
             $option['where']['id']= $posts['id'];
            $update_id = $this->paypallist->update($data,$option);
            if($update_id){
                ajax_return('修改成功',1,$posts['id']);
            }else{
                ajax_return('操作失败',false);
            }

        }else{ //add
            $add_id = $this->paypallist->add($data);
            if($add_id){
                ajax_return('新增成功成功',1,$add_id);
            }else{
                ajax_return('操作失败',false);
            }

        }
    }

    public function addAccounPaypalinfo()
    {
       if(!$_POST['id']=='')
       {
           $arr = array();
           $id = $_POST['id'];
           $arr['ebay_account'] =$_POST['account'];
           $arr['paypal_account'] = $_POST['paypal'];
           $re = $this->paypallist->update($arr,$id);
           if($re)
           {
               ajax_return('修改成功',1);
               die;
           }
           else
           {
               ajax_return('修改失败',2);
               die;
           }


       }
        else
        {
            $arr = array();
            $arr['ebay_account'] =$_POST['account'];
            $arr['paypal_account'] = $_POST['paypal'];
            $re = $this->paypallist->add($arr);
            if($re)
            {
                ajax_return('添加成功',1);
                die;
            }
            {
                ajax_return('新增失败',2);
                die;
            }
        }

    }

    public function deleteAccountPaypal()
    {
        $id = $_POST['id'];
        $re =  $this->paypallist->delect($id);
        if($re)
        {
            echo json_encode(array('msg' => '删除成功', 'status' => 1));
        }
        else
        {
            echo json_encode(array('msg' => '删除失败', 'status' => 0));
        }
    }

}