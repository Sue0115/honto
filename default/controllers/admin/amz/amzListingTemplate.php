<?php
ini_set('memory_limit', '2048M');
set_time_limit(0);
header('Content-Type: text/html; Charset=utf-8');
class amzListingTemplate extends Admin_Controller{

    CONST NO_USE = 0;
    CONST IS_USE = 1;
    function __construct(){

        parent::__construct();
        $this->load->helper('http_helper');
        $this->load->model(
            array(
                'amz/slme_amz_listing_template_model','slme_amz_listing_data_model','Slme_amz_site_model','print/products_data_model',
                'amz/erpskutoamzsku_model','amz/amz_account_imgurl_model','sharepage','amz/slme_amz_listing_product_id_model'
            )
        );
        $this->load->library('phpexcel/PHPExcel','phpexcel/PHPExcel/Reader/Excel5.php');
    }

    /**
     * product ID使用状态
     * @return multitype:number
     */
    static function statusEdit(){
       return array(
            '0' => '未使用',
            '1' => '已使用',
        );        
    }
    
    /**
     * 类别(待添加)
     * @return multitype:number
     */
    static function categoryEdit(){
        return  array(
            '1' => '服装',
            '2' => '电子',
            '3' => '-其他待添加-'
        );
    }
    
    
    /**
     * 模版显示.新增
     */
    public function listingTemplateShow(){
     
       
       if(isset($_FILES['excelFile']["name"]) && !empty($_FILES['excelFile']["name"])){//导入
           $site     = htmlspecialchars($this->input->get_post("exportSite"));//站点
           $category = htmlspecialchars($this->input->get_post("exportCatagory"));//分类
           $phpExcel = new PHPExcel_Reader_Excel5;
           $extend = explode("." ,$_FILES['excelFile']["name"]);
           $val    = count($extend)-1;
           $extend = strtolower($extend[$val]);
           if($extend != 'xls' && $extend!='xlsx'){
               echo '<script language="javascript">alert("文件格式不正确，请上传EXCEL文件");history.go(-1);</script>';
               exit;
           }
           $filename = $_FILES['excelFile']["tmp_name"];
           $objPHPExcel = $phpExcel->load($filename);
           $sheet = $objPHPExcel->getSheet(0);
           //        $rows=$sheet->getHighestRow();//EXCEL行数
           $cols=$sheet->getHighestColumn();
           $i=0;
           $data_array=array();
           $indexArr  = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
               'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
               'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
               'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
               'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ',
               'EA','EB','EC','ED','EE','EF','EG','EH','EI','EJ','EK','EL','EM','EN','EO','EP','EQ','ER','ES','ET','EU','EV','EW','EX','EY','EZ',
               'FA','FB','FC','FD','FE','FF','FG','FH','FI','FJ','FK','FL','FM','FN','FO','FP','FQ','FR','FS','FT','FU','FV','FW','FX','FY','FZ'
           );
           for($j=1;$j<=3;$j++){
               if(trim($sheet->getCell("A".$j)->getValue())){
                   foreach($indexArr as $v){
                       if(trim($sheet->getCell("$v".$j)->getValue())){
                           $data_array[$j][$v.$j]=trim($sheet->getCell("$v".$j)->getValue());
                       }
                   }
               }
           }
           
           foreach($data_array as $key=>$val){//$site,$category
               foreach($val as $k=>$v){
                   $option = array();
                   $data = array();
                   $data['site']         = $site; //导入站点
                   $data['rows']         = $k; //位置
                   $data['title']        = trim($v);  //title
                   $data['category']     = $category;  //分类
                   $option['where']=$data;
                   $checkData     = $this->slme_amz_listing_template_model->getOne($option);//检测是否存在
                   if($checkData)continue;
                   $data['create_user']    = $this->user_info->id;//登录用户id
                   $data['create_time']    = date("Y-m-d H:i:s");
                   $this->slme_amz_listing_template_model->add($data);
                   unset($data);
               }
           }
       }
       
        $string   = '';
        $option   = array();
        $where    = array();
        $dataArr  = array();
        $site     = htmlspecialchars($this->input->get_post("site"));//搜索条件
        $category = htmlspecialchars($this->input->get_post("category"));
        $title = htmlspecialchars($this->input->get_post("title_search"));
        $rows     = htmlspecialchars($this->input->get_post("rows_search"));
        if(isset($site) && !empty($site)){
            $where['site']=$site;
            $string.="&site=".$site;
        }
        if(isset($category) && !empty($category)){
            $where['category']=$category;
            $string.="&category=".$category;
        }                   
        $return_arr  = array ('total_rows' => true );
        $per_page	 = (int)$this->input->get_post('per_page');
        $cupage	     = intval($this->config->item('site_page_num')); //每页显示个数
        $url         = admin_base_url('amz/amzListingTemplate/listingTemplateShow?').$string;
        $option	= array(
            'page'		   => $cupage,
            'per_page'	   => $per_page,
            'where'        => $where,
            'group_by'     => 'site,category',
            'order'		   => "id desc",
        );

        $dataArr      = $this->slme_amz_listing_template_model->getAll($option,$return_arr);
        $page         = $this->sharepage->showPage ($url, $return_arr['total_rows'], $cupage );
        $fields       =  $this->slme_amz_listing_data_model->getColumnsList();
        $sites        =  $this->Slme_amz_site_model->getAll();              
        $categoryEdit = self::categoryEdit();//类别
        $fieldsArr    = array();//获取数据表字段信息(为了模版对应数据字段)
        $siteArr      = array();//AMZ站点      
        foreach($fields['_type'] as $key=>$val){
            if($key=='id' || $key=="create_user" || $key=="create_time" || $key=="modify_user" || $key=="modify_time")continue;
            $fieldsArr[] = $key;
        }  
        foreach($sites as $key=>$val){
            $siteArr[$key]=$val->site;
        }
        $data = array();
        foreach($dataArr as $k=>$v){
            $data[$k]=(array)$v;
        }
        $this->_template('admin/export_data/amz_listing_template',array('data'=>$data,'fields'=>$fieldsArr,'site'=>$siteArr,'category'=>$categoryEdit,'page'=>$page,
            'siteSearch'=>$site,'categorySearch'=>$category,'rows_search'=>$rows,'title_search'=>$title));
        
    }
       
     

    /**
     * 模版详情页面
     */
    public function listingTemplateShowDetail(){
        //新增
        if(!empty($_REQUEST['site']) && $_REQUEST['insertTitle'] && $_REQUEST['insertRows'] &&$_REQUEST['insert_relation_field']){
            $site           = $_REQUEST['site'];
            $insertCategory = $_REQUEST['category'];
            $title          = $_REQUEST['insertTitle'];
            $rows           = $_REQUEST['insertRows'];
            $relation_field = $_REQUEST['insert_relation_field'];
            $parent_show    = $_REQUEST['insert_parent_show'];
            foreach($title as $key=>$val){
                $option = array();
                $data=array();
                $data['site']           = $site;
                $data['category']       = $insertCategory;
                $data['relation_field'] = $relation_field[$key];
                $data['rows']           = $rows[$key];
                $data['title']          = trim($val);
                $data['parent_show']    = $parent_show[$key];
                $option['where']=$data;
                $checkData     = $this->slme_amz_listing_template_model->getOne($option);//检测是否存在
                if($checkData)continue;
                $data['create_user']    = $this->user_info->id;//登录用户id
                $data['create_time']    = date("Y-m-d H:i:s");
                $a=$this->slme_amz_listing_template_model->add($data);
                unset($data);
            }
        }
        $string   = '';
        $option   = array();
        $where    = array();
        $dataArr  = array();
        $site     = htmlspecialchars($this->input->get_post("site"));//搜索条件
        $category = htmlspecialchars($this->input->get_post("category"));
        $title = htmlspecialchars($this->input->get_post("title_search"));
        $rows     = htmlspecialchars($this->input->get_post("rows_search"));
        if(isset($site) && !empty($site)){
            $where['site']=$site;
            $string.="&site=".$site;
        }
        if(isset($category) && !empty($category)){
            $where['category']=$category;
            $string.="&category=".$category;
        }
        if(isset($title) && !empty($title)){
            $where['title']=$title;
            $string.="&title=".$title;
        }
        if(isset($rows) && !empty($rows)){
            $where['rows']=$rows;
            $string.="&rows=".$rows;
        }
        $return_arr  = array ('total_rows' => true );
        $per_page	 = (int)$this->input->get_post('per_page');
        $cupage	     = intval($this->config->item('site_page_num')); //每页显示个数
        $url         = admin_base_url('amz/amzListingTemplate/listingTemplateShowDetail?').$string;
        $option	= array(
            'page'		   => $cupage,
            'per_page'	   => $per_page,
            'where'        => $where,
            'order'		   => "id asc",
        );
        
        $dataArr      = $this->slme_amz_listing_template_model->getAll($option,$return_arr);
        $page         = $this->sharepage->showPage ($url, $return_arr['total_rows'], $cupage );
        $fields       =  $this->slme_amz_listing_data_model->getColumnsList();
        $sites        =  $this->Slme_amz_site_model->getAll();
        $categoryEdit = self::categoryEdit();//类别
        $fieldsArr    = array();//获取数据表字段信息(为了模版对应数据字段)
        $siteArr      = array();//AMZ站点
        foreach($fields['_type'] as $key=>$val){
            if($key=='id' || $key=="create_user" || $key=="create_time" || $key=="modify_user" || $key=="modify_time")continue;
            $fieldsArr[] = $key;
        }
        foreach($sites as $key=>$val){
            $siteArr[$key]=$val->site;
        }
        $data = array();
        foreach($dataArr as $k=>$v){
            $data[$k]=(array)$v;
        }
        $this->_template('admin/export_data/amz_listing_template_detail',array('data'=>$data,'fields'=>$fieldsArr,'site'=>$siteArr,'category'=>$categoryEdit,'page'=>$page,
            'siteSearch'=>$site,'categorySearch'=>$category,'rows_search'=>$rows,'title_search'=>$title));
    }
    
    /**
     * ajax修改模版
     */
   public function ajaxEdit(){
       $data = array();
       $data['site']           = $_REQUEST['editSite'];
       $data['category']       = $_REQUEST['editCategory'];
       $data['title']          = $_REQUEST['editTitle'];
       $data['rows']           = $_REQUEST['editRows'];
       $data['relation_field'] = $_REQUEST['edit_relation_field'];
       $data['parent_show']    = $_REQUEST['edit_parent_show'];
       $data['modify_user']    = $this->user_info->id;//登录用户id
       $data['modify_time']    = date("Y-m-d H:i:s"); 
       $id                     = $_REQUEST['id'];
       $result = $this->slme_amz_listing_template_model->updateTemplateInfo($data,$id);
       if($result){
           echo 'success';
       }else{
           echo 'defeat';
       }die();
   }
   
   /**
    * 上架模版显示数据.新增.修改
    */
   public function listingDataShow(){
       
       $fields       =  $this->slme_amz_listing_data_model->getColumnsList();
       $sites        = $this->Slme_amz_site_model->getAll();
       $fieldsArr    = array();//获取数据表字段信息(为了模版对应数据字段)
       $siteArr      = array();//AMZ站点
       $imgUrlArr    = array();//帐号图片域名
       $categoryEdit = self::categoryEdit();//类别
       $imgUrls       = $this->amz_account_imgurl_model->getAll();
//        foreach($fields['_type'] as $key=>$val){
//            if($key=='id' || $key=="create_user" || $key=="create_time" || $key=="modify_user" || $key=="modify_time")continue;
//            $fieldsArr[] = $key;
//        }
       
       foreach($fields['_type'] as $key=>$val){
           if(!in_array($key,array('item_sku','item_name','brand_name','standard_price','currency','sale_price','sale_from_date','create_time')))continue;
           $fieldsArr[] = $key;
       }
       foreach($sites as $key=>$val){
           $siteArr[$key]=$val->site;
       }
       foreach($imgUrls as $key=>$val){
           $imgUrlArr[$val->img_url] = $val->account_name;
       }
       //新增或修改
       if(isset($_REQUEST['act']) && ($_REQUEST['act']=='add_update')){
           $itemSkuArr          = $_REQUEST['sku'];
           $color               = $_REQUEST['color'];
           $size                = $_REQUEST['size'];
           $external_product_id = $_REQUEST['external_product_id'];
           $parent_child        = $_REQUEST['parent_child'];
           $img_color           = $_REQUEST['img_color'];
           $imgArr              = (isset($_REQUEST['detailPicList'])&&!empty($_REQUEST['detailPicList']))?$_REQUEST['detailPicList']:array();
           $parentSku           = '';
           foreach($itemSkuArr as $k=>$v){               
               if($parent_child[$k]=='parent'){
                   $parentSku=$v;
               }
           } 
           if(empty($parentSku)){
               $parentSku = $_REQUEST['oriange_sku'];
           }
           foreach($itemSkuArr as $key=>$val){
               $data =array();              
               $data['item_sku']                      = trim($val);
               $data['external_product_id']           = $external_product_id[$key];
               $data['external_product_id_type']      = trim($_REQUEST['external_product_id_type']);
               $data['item_name']                     = trim($_REQUEST['subject']);
               $data['brand_name']                    = trim($_REQUEST['brand_name']);
               $data['product_subtype_item_type']     = trim($_REQUEST['item_type']);
               $data['product_description']           = htmlspecialchars(trim($_REQUEST['detail']));
               $data['update_delete']                 = '';
               $data['model']                         = '';
               $data['part_number']                   = '';
               $data['standard_price']                = trim($_REQUEST['standard_price']);
               $data['currency']                      = trim($_REQUEST['currency']);
               
               $data['quantity']                      = trim($_REQUEST['quantity']);
               $data['fulfillment_latency']           = '';
               $data['sale_price']                    = trim($_REQUEST['sale_price']);
               $data['sale_from_date']                = date('Y-m-d');
               $data['sale_end_date']                 = date('Y-m-d',strtotime('+10 year'));
               $data['recommended_browse_nodes']      = '';
               $data['generic_keywords1']             = trim($_REQUEST['generic_keywords1']);
               $data['generic_keywords2']             = trim($_REQUEST['generic_keywords2']);
               $data['generic_keywords3']             = trim($_REQUEST['generic_keywords3']);
               $data['generic_keywords4']             = trim($_REQUEST['generic_keywords4']);
               $data['generic_keywords5']             = trim($_REQUEST['generic_keywords5']);
               
               $data['bullet_point1']                 = trim($_REQUEST['bullet_point1']);
               $data['bullet_point2']                 = trim($_REQUEST['bullet_point2']);
               $data['bullet_point3']                 = trim($_REQUEST['bullet_point3']);
               $data['bullet_point4']                 = trim($_REQUEST['bullet_point4']);
               $data['bullet_point5']                 = trim($_REQUEST['bullet_point5']);
               if($parent_child[$key]=='parent'){
                   $data['main_image_url']                = strrpos($imgArr[0],'*')?trim(substr($imgArr[0],0,strrpos($imgArr[0],'*')).'*'.$img_color[0]):$imgArr[0].'*'.$img_color[0];
                   $data['other_image_url1']              = strrpos($imgArr[1],'*')?trim(substr($imgArr[1],0,strrpos($imgArr[1],'*')).'*'.$img_color[1]):$imgArr[1].'*'.$img_color[1];
                   $data['other_image_url2']              = strrpos($imgArr[2],'*')?trim(substr($imgArr[2],0,strrpos($imgArr[2],'*')).'*'.$img_color[2]):$imgArr[2].'*'.$img_color[2];
                   $data['other_image_url3']              = strrpos($imgArr[3],'*')?trim(substr($imgArr[3],0,strrpos($imgArr[3],'*')).'*'.$img_color[3]):$imgArr[3].'*'.$img_color[3];
                   $data['other_image_url4']              = strrpos($imgArr[4],'*')?trim(substr($imgArr[4],0,strrpos($imgArr[4],'*')).'*'.$img_color[4]):$imgArr[4].'*'.$img_color[4];
                   $data['other_image_url5']              = strrpos($imgArr[5],'*')?trim(substr($imgArr[5],0,strrpos($imgArr[5],'*')).'*'.$img_color[5]):$imgArr[5].'*'.$img_color[5];
                   $data['other_image_url6']              = strrpos($imgArr[6],'*')?trim(substr($imgArr[6],0,strrpos($imgArr[6],'*')).'*'.$img_color[6]):$imgArr[6].'*'.$img_color[6];
                   $data['other_image_url7']              = strrpos($imgArr[7],'*')?trim(substr($imgArr[7],0,strrpos($imgArr[7],'*')).'*'.$img_color[7]):$imgArr[7].'*'.$img_color[7];
                   $data['other_image_url8']              = strrpos($imgArr[8],'*')?trim(substr($imgArr[8],0,strrpos($imgArr[8],'*')).'*'.$img_color[8]):$imgArr[8].'*'.$img_color[8];
               }else{
                   $img_color_arr = array();
                   foreach($img_color as $img_k=>$img_v){
                    if(trim($img_v)==trim($color[$key])){
                        $img_color_arr[] = strrpos($imgArr[$img_k],'*')?(substr($imgArr[$img_k],0,strrpos($imgArr[$img_k],'*')).'*'.$color[$key]):($imgArr[$img_k].'*'.$color[$key]);                       
                    }   
                   }
                   $data['main_image_url']                = !empty($img_color_arr[0])?$img_color_arr[0]:'';
                   $data['other_image_url1']              = !empty($img_color_arr[1])?$img_color_arr[1]:'';
                   $data['other_image_url2']              = !empty($img_color_arr[2])?$img_color_arr[2]:'';
                   $data['other_image_url3']              = !empty($img_color_arr[3])?$img_color_arr[3]:'';
                   $data['other_image_url4']              = !empty($img_color_arr[4])?$img_color_arr[4]:'';
                   $data['other_image_url5']              = !empty($img_color_arr[5])?$img_color_arr[5]:'';
                   $data['other_image_url6']              = !empty($img_color_arr[6])?$img_color_arr[6]:'';
                   $data['other_image_url7']              = !empty($img_color_arr[7])?$img_color_arr[7]:'';
                   $data['other_image_url8']              = !empty($img_color_arr[8])?$img_color_arr[8]:'';
               }
               
               $data['parent_child']                  = $parent_child[$key];
               $data['parent_sku']                    = ($parent_child[$key]=='child')?$parentSku:'';
               $data['relationship_type']             = trim($_REQUEST['relationship_type']);
               $data['variation_theme']               = trim($_REQUEST['variation_theme']);
               $data['color_map']                     = '';
               $data['color_name']                    = trim($color[$key]);
               $data['size_map']                      = '';
               $data['size_name']                     = trim($size[$key]);
               
               $data['outer_material_type']           = '';
               $data['model_name']                    = '';
               $data['department_name']               = trim($_REQUEST['department_name']);
               $data['lifestyle']                     = '';
               $data['style_name']                    = '';
               $data['sleeve_type']                   = trim($_REQUEST['sleeve_type']);
               $data['item_length_description']       = '';
               $data['is_adult_product']              = '';
               $data['cup_size']                      = '';
               $data['band_size_num']                 = '';
               $data['band_size_num_unit_of_measure'] = '';
               
               $data['closure_type']                  = '';
               $data['waist_style']                   = '';
               $data['waist_size_unit_of_measure']    = '';
               $data['neck_style']                    = trim($_REQUEST['neck_style']);
               $data['collar_style']                  = '';
               $data['fit_type']                      = trim($_REQUEST['fit_type']);
               $data['item_type']                     = trim($_REQUEST['item_type']);
               $data['list_price']                    = trim($_REQUEST['list_price']);
               $data['material_composition'] = '';
               $data['same_product_id']               = trim($parentSku); //关联的sku              
               $data['create_user']                   = $this->user_info->id;//登录用户id
               $data['create_time']                   = date("Y-m-d H:i:s");
               $data['modify_user']                   = $this->user_info->id;//登录用户id
               $data['modify_time']                   = date("Y-m-d H:i:s");
               $checkOptions = array();
               $checkOptions['where']['item_sku'] = $data['item_sku'];
               $checkFlag = $this->slme_amz_listing_data_model->getOne($checkOptions,true);
//                $updateArr = array();
//                $updateStatusOptions = array();
//                $updateStatusOptions['where']['product_id'] = $data['external_product_id'];
//                $updateArr['status'] = self::IS_USE;
//                $updateArr['sku'] = $data['item_sku'];
//                $updateArr['use_user'] = $this->user_info->id;//登录用户id
//                $updateArr['use_time'] = date("Y-m-d H:i:s");
//                $this->slme_amz_listing_product_id_model->update($updateArr,$updateStatusOptions);//更新UPC 状态为已使用
               if($checkFlag){
                   $updateOptions = array();
                   $updateOptions['where']['item_sku'] = $data['item_sku'];
                   $this->slme_amz_listing_data_model->update($data,$updateOptions);
               }else{
                   $this->slme_amz_listing_data_model->add($data);
               }               
               unset($data);
           }
       }
       $string     = '';
       $option     = array();
       $where      = array();
       $item_sku   = htmlspecialchars($this->input->get_post("item_sku_search"));//搜索条件
       if(isset($item_sku) && !empty($item_sku)){
           $where['item_sku']=$item_sku;
           $string.="item_sku_search=".$item_sku;
          
       }
       $return_arr  = array ('total_rows' => true );
       $per_page	= (int)$this->input->get_post('per_page');  
       $cupage	    = intval($this->config->item('site_page_num')); //每页显示个数
       $url         = admin_base_url('amz/amzListingTemplate/listingDataShow?').$string;  
       $where['parent_child']='parent';//只显示主SKU
       $option	= array(
           'page'		   => $cupage,
           'per_page'	   => $per_page,
           'where'         => $where,
           'order'		   => "id desc",
           
       );    
       $dataArr  = $this->slme_amz_listing_data_model->getAll($option,$return_arr);
       $page = $this->sharepage->showPage ($url, $return_arr['total_rows'], $cupage );
       $data = array();
       foreach($dataArr as $k=>$v){
           $data[$k]=(array)$v;
       }
       
       $this->_template('admin/export_data/amz_listing_data',array('data'=>$data,'fields'=>$fieldsArr,'site'=>$siteArr,'category'=>$categoryEdit,'imgUrl'=>$imgUrlArr,'page'=>$page,'item_sku_search'=>$item_sku));        
   }
   
   
   
   
   /**
    * 添加/修改栏目数据
    */
   public function addEditData(){
       $id                     = (isset($_GET['ids']) && !empty($_GET['ids']))?$_GET['ids']:"";
       $dataArr = array();
       $option  = array();
       $amzSkuInfo=array();
       if(isset($id) && !empty($id)){
           $option['where']['same_product_id'] = $id;
           $dataArr    = $this->slme_amz_listing_data_model->getAll($option);
           foreach($dataArr as $key=>$val){
               if($val->parent_child=='parent'){
                   $dataArr[0]->searchProductId = $val->item_sku;//搜索的sku,取母SKU
               }
           }
       }
       $this->_template('admin/export_data/amz_add_update_listing_data',array('data'=>$dataArr));
   }
   
   
   /**
    * 模糊搜索sku
    */
   public function search_sku(){
       $result['status'] = 0;
       $sku = strtoupper(trim($this->input->post('sku_search')));
       $option = array();
       $where = array();
       //$like = array();
       $skuArr = array();//存放最后找到的sku数组
       // $like['products_sku'] = $sku;
       $this->db->like('products_sku', $sku, 'after');
       $where['product_warehouse_id'] = 1000;
       $where['products_status_2 !='] = 'sellWaiting';
       $option = array(
           //'like' => $like,
           'where'=> $where
       );
       $results = $this->products_data_model->getAll2array($option);
       foreach($results as $re){
           $skuArr[] = $re['products_sku'];
       }
       if(!empty($skuArr)){
           $result['status'] = 1;
       }
       $result['skus'] = $skuArr;
       echo json_encode($result);
       die;
   }
   
   
   
   /**
    * 搜索sku的英文介绍
    * @param unknown_type $sku
    */
   public function getSkuInfoLike(){
       $sku = strtoupper(trim($this->input->post('sku_search')));
       $rs = $this->db->select('products_sku,products_html_mod,products_declared_en')
       ->like('products_sku', $sku, 'after')->group_by('products_sku')
       ->get($this->products_data_model->_table)->result_array();
       $new_rs = array();
       foreach($rs as $k => $v){
           if($k<1){
               $newRs[] = htmlspecialchars_decode($v['products_html_mod']);//描述
               $itemNames[] = htmlspecialchars_decode($v['products_declared_en']);//item name
           }
       }
       $result['skuInfo'] = $newRs;
       $result['item_name'] = $itemNames;
       echo json_encode($result);
       die;
   }
   
   
   /**
    * ajax删除栏目数据
    */
   function ajaxDeleteData(){
       $id = $_REQUEST['id'];
       $result = $this->slme_amz_listing_data_model->deleteTemplateInfo($id);
       if($result){
           echo '删除成功';
       }else{
           echo '删除失败';
       }die();
   }
   
   
 /**
     * ajax读取美国图片服务器
     */
    public function ajaxUploadDirImage(){
    	$dirName  = strtoupper(trim($this->input->get_post('dirName')));
        $opt      = trim($this->input->get_post('opt'));

        if(!empty($opt)){
          $url = "http://amz.moonarstore.com/get_image.php?dirName=".$dirName."&dir=".$opt;//美国图片服务器脚本的路径
        }else{
          $url = "http://amz.moonarstore.com/get_image.php?dirName=".$dirName;//美国图片服务器脚本的路径
        }
        $get_data = curlRequest($url,'','GET');
        $result = json_decode($get_data,true);
//         if(!empty($host_url)){
//             foreach($result as $ke => $v){
//                 $result[$ke] = str_replace('imgurl.moonarstore.com',$host_url,$v);
//             }
//         }
        ajax_return('', true, $result);
    }
   
   
   /**
    * 导出excel、
    */
   public function exportTemplate(){
       $templateKeyRow1  = array('A1','B1','C1','D1','E1','F1','G1','H1','I1','J1','K1','L1','M1','N1','O1','P1','Q1','R1','S1','T1','U1','V1','W1','X1','Y1','Z1',
           'AA1','AB1','AC1','AD1','AE1','AF1','AG1','AH1','AI1','AJ1','AK1','AL1','AM1','AN1','AO1','AP1','AQ1','AR1','AS1','AT1','AU1','AV1','AW1','AX1','AY1','AZ1',
           'BA1','BB1','BC1','BD1','BE1','BF1','BG1','BH1','BI1','BJ1','BK1','BL1','BM1','BN1','BO1','BP1','BQ1','BR1','BS1','BT1','BU1','BV1','BW1','BX1','BY1','BZ1',
           'CA1','CB1','CC1','CD1','CE1','CF1','CG1','CH1','CI1','CJ1','CK1','CL1','CM1','CN1','CO1','CP1','CQ1','CR1','CS1','CT1','CU1','CV1','CW1','CX1','CY1','CZ1',
           'DA1','DB1','DC1','DD1','DE1','DF1','DG1','DH1','DI1','DJ1','DK1','DL1','DM1','DN1','DO1','DP1','DQ1','DR1','DS1','DT1','DU1','DV1','DW1','DX1','DY1','DZ1',
           'EA1','EB1','EC1','ED1','EE1','EF1','EG1','EH1','EI1','EJ1','EK1','EL1','EM1','EN1','EO1','EP1','EQ1','ER1','ES1','ET1','EU1','EV1','EW1','EX1','EY1','EZ1',
           'FA1','FB1','FC1','FD1','FE1','FF1','FG1','FH1','FI1','FJ1','FK1','FL1','FM1','FN1','FO1','FP1','FQ1','FR1','FS1','FT1','FU1','FV1','FW1','FX1','FY1','FZ1'
       );//定位索引数组
       $templateKeyRow2 = array('A2','B2','C2','D2','E2','F2','G2','H2','I2','J2','K2','L2','M2','N2','O2','P2','Q2','R2','S2','T2','U2','V2','W2','X2','Y2','Z2',
           'AA2','AB2','AC2','AD2','AE2','AF2','AG2','AH2','AI2','AJ2','AK2','AL2','AM2','AN2','AO2','AP2','AQ2','AR2','AS2','AT2','AU2','AV2','AW2','AX2','AY2','AZ2',
           'BA2','BB2','BC2','BD2','BE2','BF2','BG2','BH2','BI2','BJ2','BK2','BL2','BM2','BN2','BO2','BP2','BQ2','BR2','BS2','BT2','BU2','BV2','BW2','BX2','BY2','BZ2',
           'CA2','CB2','CC2','CD2','CE2','CF2','CG2','CH2','CI2','CJ2','CK2','CL2','CM2','CN2','CO2','CP2','CQ2','CR2','CS2','CT2','CU2','CV2','CW2','CX2','CY2','CZ2',
           'DA2','DB2','DC2','DD2','DE2','DF2','DG2','DH2','DI2','DJ2','DK2','DL2','DM2','DN2','DO2','DP2','DQ2','DR2','DS2','DT2','DU2','DV2','DW2','DX2','DY2','DZ2',
           'EA2','EB2','EC2','ED2','EE2','EF2','EG2','EH2','EI2','EJ2','EK2','EL2','EM2','EN2','EO2','EP2','EQ2','ER2','ES2','ET2','EU2','EV2','EW2','EX2','EY2','EZ2',
           'FA2','FB2','FC2','FD2','FE2','FF2','FG2','FH2','FI2','FJ2','FK2','FL2','FM2','FN2','FO2','FP2','FQ2','FR2','FS2','FT2','FU2','FV2','FW2','FX2','FY2','FZ2'
       );
       $templateKeyRow3 = array('A3','B3','C3','D3','E3','F3','G3','H3','I3','J3','K3','L3','M3','N3','O3','P3','Q3','R3','S3','T3','U3','V3','W3','X3','Y3','Z3',
           'AA3','AB3','AC3','AD3','AE3','AF3','AG3','AH3','AI3','AJ3','AK3','AL3','AM3','AN3','AO3','AP3','AQ3','AR3','AS3','AT3','AU3','AV3','AW3','AX3','AY3','AZ3',
           'BA3','BB3','BC3','BD3','BE3','BF3','BG3','BH3','BI3','BJ3','BK3','BL3','BM3','BN3','BO3','BP3','BQ3','BR3','BS3','BT3','BU3','BV3','BW3','BX3','BY3','BZ3',
           'CA3','CB3','CC3','CD3','CE3','CF3','CG3','CH3','CI3','CJ3','CK3','CL3','CM3','CN3','CO3','CP3','CQ3','CR3','CS3','CT3','CU3','CV3','CW3','CX3','CY3','CZ3',
           'DA3','DB3','DC3','DD3','DE3','DF3','DG3','DH3','DI3','DJ3','DK3','DL3','DM3','DN3','DO3','DP3','DQ3','DR3','DS3','DT3','DU3','DV3','DW3','DX3','DY3','DZ3',
           'EA3','EB3','EC3','ED3','EE3','EF3','EG3','EH3','EI3','EJ3','EK3','EL3','EM3','EN3','EO3','EP3','EQ3','ER3','ES3','ET3','EU3','EV3','EW3','EX3','EY3','EZ3',
           'FA3','FB3','FC3','FD3','FE3','FF3','FG3','FH3','FI3','FJ3','FK3','FL3','FM3','FN3','FO3','FP3','FQ3','FR3','FS3','FT3','FU3','FV3','FW3','FX3','FY3','FZ3'
       );
       $idStr     = (isset($_GET['ids'])&& !empty($_GET['ids']))?$_GET['ids']:'';//same_product_id
       $site      = (isset($_GET['site_type'])&& !empty($_GET['site_type']))?$_GET['site_type']:'';
       $category  = (isset($_GET['category_type'])&& !empty($_GET['category_type']))?$_GET['category_type']:'';
       $img_url   = (isset($_GET['img_url'])&& !empty($_GET['img_url']))?$_GET['img_url']:'';
       $sameProductIdArr = array();
       $sameProductIdArr = explode(",", $idStr); 
       $sameProductId    = '';
       foreach($sameProductIdArr as $k =>$v){
           $sameProductId.="'$v'";
           if(!empty($sameProductIdArr[$k+1]))
           $sameProductId.=',';
       }
       $templateOption   = array();
       $dataOption       = '';
       if(!empty($idStr) && !empty($site) && !empty($category)){
           $dataOption = "same_product_id in (".$sameProductId.") order by id asc ";
           $templateOption['where']['site']     = $site;
           $templateOption['where']['category'] = $category;
           $templateOption['order'] = 'id asc';
           $listingTemplate = $this->slme_amz_listing_template_model->getAll($templateOption);
           $listingData     = $this->slme_amz_listing_data_model->getAll($dataOption);
//            echo $this->db->last_query();exit;
           foreach($listingData as $key=>$val){
               $dataArr = array();
               $dataArr = (array)$val;
               $listingData[$key]->main_image_url = str_replace('imgurl.moonarstore.com',$img_url,substr($dataArr['main_image_url'],0,strrpos($dataArr['main_image_url'],'*')));
               for($i=1;$i<=8;$i++){
                   if($dataArr['other_image_url'.$i]){
                       $img = '';
                       $img = 'other_image_url'.$i;
                       $listingData[$key]->$img = str_replace('imgurl.moonarstore.com',$img_url,substr($listingData[$key]->$img,0,strrpos($listingData[$key]->$img,'*')));
                   }
               }
           }
           $filename=$site."/".$category."/".date('Y-m-d');
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel;charset=UTF-8");
            header("Content-Disposition:attachment;filename=".$filename.".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $table = '';
       
           $listingTemplateRow1    = array();
           $listingTemplateRow2    = array();
           $listingTemplateRow3    = array();
           $listingTemplateRow1Arr = array();
           $listingTemplateRow2Arr = array();
           $listingTemplateRow3Arr = array();
           if (!empty($listingTemplate)){
               $table.='<html  xmlns:x="urn:schemas-microsoft-com:office:excel" ><table cellpadding="0" cellspacing="0" border="1" style="br:mso-data-placement:same-cell;">'.PHP_EOL;
               foreach ($listingTemplate as $k => $v) {//第一行
                   if(substr($v->rows,strlen($v->rows)-1,1)==1){
                        $listingTemplateRow1[] = (array)$v;
                       
                   } 
                   if(substr($v->rows,strlen($v->rows)-1,1)==2){
                       $listingTemplateRow2[] = (array)$v;                     
                   } 
                   if(substr($v->rows,strlen($v->rows)-1,1)==3){
                       $listingTemplateRow3[] = (array)$v;                       
                   }                 
               }
               
               //第一行按顺序重组排序
               foreach($templateKeyRow1 as $k=>$v){
                   foreach($listingTemplateRow1 as $key=>$val){
                       if($val['rows']==$v){
                           $listingTemplateRow1Arr[] = $val;continue;
                       }                      
                   }
               }
               //第二行按顺序重组排序
               foreach($templateKeyRow2 as $k=>$v){
                   foreach($listingTemplateRow2 as $key=>$val){
                       if($val['rows']==$v){
                           $listingTemplateRow2Arr[] = $val;continue;
                       }
                   }
               }
               //第三行按顺序重组排序
               foreach($templateKeyRow3 as $k=>$v){
                   foreach($listingTemplateRow3 as $key=>$val){
                       if($val['rows']==$v){
                           $listingTemplateRow3Arr[] = $val;continue;
                       }
                   }
               }

//                var_dump($listingTemplateRow1Arr,$listingTemplateRow2Arr,$listingTemplateRow3Arr);exit;
               if(!empty($listingTemplateRow1Arr)){//第一行 存在才开始tr
                   $table.="<tr>";
               }
               foreach ($listingTemplateRow1Arr as $k => $v) {//第一行                
                       $table .='<th>'.$v['title'].'</th>';                
               }
               if(!empty($listingTemplateRow1Arr)){
                   $table.="</tr>".PHP_EOL;
               }
               if(!empty($listingTemplateRow2Arr)){//第二行 存在才开始tr
                   $table.="<tr>";
               }              
               foreach ($listingTemplateRow2Arr as $k => $v) {//第二行                  
                       $table .='<th>'.$v['title'].'</th>';                  
               }
               if(!empty($listingTemplateRow2Arr)){
                   $table.="</tr>".PHP_EOL;
               }
               if(!empty($listingTemplateRow3Arr)){//第三行 存在才开始tr
                   $table.="<tr>";
               }               
               foreach ($listingTemplateRow3Arr as $k => $v) {//第三行
                   
                       $table .='<th>'.$v['title'].'</th>';
                   
               }
               if(!empty($listingTemplateRow3Arr)){
                   $table.='</tr>'.PHP_EOL;
               }
               
           }

           if (!empty($listingData) && !empty($listingTemplateRow1Arr)){
               foreach($listingData as $key=>$v){
                   $val = (array)$v;
                   $table.=  '<tr>';
                   if(!empty($listingTemplateRow1Arr) && empty($listingTemplateRow2Arr) && empty($listingTemplateRow3Arr)){//title只有一行
                       foreach($listingTemplateRow1Arr as $k=>$field){//循环第1行title，最后一行有绑定需要显示的字段 
                           if($field['parent_show']=='不显示' && $val['parent_child']=='parent'){
                               $table.='<td x:str></td>';                               
                           }else{
                               $table.='<td x:str>'.(isset($val[$field['relation_field']])?$val[$field['relation_field']]:'').'</td>';                               
                           }
                           
                       }
                   }elseif (!empty($listingTemplateRow1Arr) && !empty($listingTemplateRow2Arr) && empty($listingTemplateRow3Arr)){//title有2行
                       foreach($listingTemplateRow2 as $k=>$field){//循环第2行title，最后一行有绑定需要显示的字段
                           if($field['parent_show']=='不显示' && $val['parent_child']=='parent'){
                               $table.='<td x:str></td>';                               
                           }else{
                               $table.='<td x:str>'.(isset($val[$field['relation_field']])?$val[$field['relation_field']]:'').'</td>';                               
                           }
                       }
                   }else{//title有三行
                       foreach($listingTemplateRow3Arr as $k=>$field){//循环第3行title，最后一行有绑定需要显示的字段                            
                           if($field['parent_show']=='不显示' && $val['parent_child']=='parent'){
                               $table.='<td x:str></td>';                               
                           }else{
                               $table.='<td x:str>'.(isset($val[$field['relation_field']])?$val[$field['relation_field']]:'').'</td>';                               
                           }
                       }  
                   }                  
                   $table.=  '</tr>'.PHP_EOL;           
               }           
           }

           $table .= '</table></html>'.PHP_EOL;
           echo $table;          
       }else{
           echo "导出失败";exit;
       }
       
       
   }
   
 public function productIdList(){
     
     if(isset($_REQUEST['act']) && ($_REQUEST['act']=='exportAdd')){
         $phpExcel = new PHPExcel_Reader_Excel5;
         $extend = explode("." ,$_FILES['excelFile']["name"]);
         $val    = count($extend)-1;
         $extend = strtolower($extend[$val]);
         if($extend != 'xls'){
             echo '<script language="javascript">alert("文件格式不正确，请上传EXCEL文件");history.go(-1);</script>';
             exit;
         }
         $filename = $_FILES['excelFile']["tmp_name"];
         $objPHPExcel = $phpExcel->load($filename);
         $sheet = $objPHPExcel->getSheet();
         $rows=$sheet->getHighestRow();//EXCEL行数
         //             $cols=$sheet->getHighestColumn();
     
         $i=0;
         $data_array=array();
         for($j=2;$j<=$rows;$j++){
             if(trim($sheet->getCell("A".$j)->getValue())){
                 $data_array[$i]['product_id']=trim($sheet->getCell("A".$j)->getValue());
                 $i++;
             }
         }    
         foreach($data_array as $key=>$val){
             $data                 = array();
             $checkOption          = array();
     
             $checkOption['where']['product_id'] = trim($val['product_id']);
             $checkData = $this->slme_amz_listing_product_id_model->getOne($checkOption);//检测是否存在
             if($checkData) continue;    
             $data['product_id']           = $val['product_id']; //挂号码(excel)
             $data['status']               = 0;
             $data['create_user']          = $this->user_info->id;//登录用户id
             $data['create_time']           = date("Y-m-d H:i:s");
             $this->slme_amz_listing_product_id_model->add($data);
             unset($data);
         }
     }
     $string   = '';
     $option   = array();
     $where    = array();
     $dataArr  = array();
     $productId     = htmlspecialchars($this->input->get_post("product_id"));//搜索条件
     $sku = htmlspecialchars($this->input->get_post("sku"));

     if(isset($productId) && !empty($productId)){
         $where['product_id']=$productId;
         $string.="&product_id=".$productId;
     }
     if(isset($sku) && !empty($sku)){
         $where['sku']=$sku;
         $string.="&sku=".$sku;
     }
     $return_arr  = array ('total_rows' => true );
     $per_page	 = (int)$this->input->get_post('per_page');
     $cupage	     = 10;//intval($this->config->item('site_page_num')); //每页显示个数
     $url         = admin_base_url('amz/amzListingTemplate/productIdList?').$string;
     $option	= array(
         'page'		   => $cupage,
         'per_page'	   => $per_page,
         'where'         => $where,
         'order'		   => "id asc",
     );
     
     $dataArr      = $this->slme_amz_listing_product_id_model->getAll($option,$return_arr);
     $page         = $this->sharepage->showPage ($url, $return_arr['total_rows'], $cupage );
     $status = self::statusEdit();
     $this->_template('admin/export_data/amz_listing_product_id_list',array('data'=>$dataArr,'productId'=>$productId,'sku'=>$sku,'status'=>$status,'page'=>$page));
 }

 /**
  * ajax自动匹配product ID
  */
 public function search_product_id(){
     $result  = array();
     $result['status'] = 0;
     $num = strtoupper(trim($this->input->post('num')));
     $option = array();
     $infoArr = array();
     $where['status'] = self::NO_USE;
     $option = array(
         'page'		   => $num,
         'per_page'	   => 0,
         'where'         => $where
     );
     $results = $this->slme_amz_listing_product_id_model->getAll2array($option);
//      echo $this->db->last_query();exit;
     foreach($results as $re){
         $infoArr[] = $re['product_id'];
     }
     if(!empty($infoArr)){
         $result['status'] = 1;
     }
     $result['info'] = $infoArr;
     echo json_encode($result);
     die;
 }
   
}