<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-01-04
 * Time: 09:45
 */

header("content-type:text/html; charset=utf-8");

class Out_of_stock_manage extends Admin_Controller
{
    public $user_info;

    private $status = array(
        1 => '新申请',
        2 => '已确定',
        3 => '驳回',
        4 => '重新申请'
    );

    private $reason = array(
        1 => '采不到',
        2 => '有起订量',
        3 => '暂时涨价',
        4 => '其他'
    );


    function __construct()
    {
        parent::__construct();
        $this->load->library('phpexcel/PHPExcel', 'phpexcel/PHPExcel/Reader/Excel5.php');
        $this->load->model(array(
            'sharepage',
            'Out_of_stock_sku_model',
            'Out_of_stock_sku_log_model',
            'order/orders_model',
            'products/products_data_model',
            'slme_user_model'
        ));
    }

    public function index()
    {
      //  var_dump($_POST);
        if (isset($_POST['getorder'])) {
            $this->cancel_order($_POST);

        } else {
            if (isset($_POST['export'])) {
                $this->export();

            } else {

                if(isset($_POST['exportfirst']))
                {
                    $this->exopotOutFrist($_POST);
                }
                $where = array(); //查询条件
                $in = array(); //in查询条件
                $like = array(); //like查询条件
                $string = array(); //URL参数
                $curpage = (int)$this->config->item('site_page_num');
                $per_page = (int)$this->input->get_post('per_page');
                $search = $this->input->get_post('search');
                if (isset($search['sku']) && $search['sku']) {
                    $where['sku'] = trim($search['sku']);
                    $string[] = 'search[sku]=' . trim($search['sku']);
                }
                if (isset($search['reason']) && $search['reason']) {
                    $where['reason'] = trim($search['reason']);
                    $string[] = 'search[reason]=' . trim($search['reason']);
                }
                if (isset($search['status']) && $search['status']) {
                    $where['status'] = trim($search['status']);
                    $string[] = 'search[status]=' . trim($search['status']);
                } else {
                    $where['status'] = 1;
                    $string[] = 'search[status]=1';
                }
                if (isset($search['products_status_2']) && $search['products_status_2']) {
                    $where['products_status_2']  = trim($search['products_status_2']);
                    $string[] = 'search[products_status_2]=' . trim($search['products_status_2']);
                }

                $join[] = array($this->products_data_model->_table.' p',"p.products_sku=sku");

                //    $curpage = 3;
                $options = array(
                    'select' =>$this->Out_of_stock_sku_model->_table.'.* , p.products_status_2 ',
                    'where' => $where,
                    //  'where_in' => $in,
                    'page' => $curpage,
                    'per_page' => $per_page,
                    'join'=>$join,
                    'group_by' =>'sku'
                    //   'order' => $orderBy
                );

               // $options['join'] = $join;


                $return_data = array('total_rows' => true);
                //
                $result = $this->Out_of_stock_sku_model->getAll($options, $return_data);
                //echo $this->db->last_query();exit;
              //  var_dump($result);exit;
                $result_mid =array();
                foreach($result as $re){

                    $sku_result =     $this->Out_of_stock_sku_model->getSkuCount($re->sku);
                    $re->skuCount = $sku_result['num'];
                    $result_mid[] = $re;
                }
                $result = $result_mid;

                $c_url = admin_base_url('out_of_stock_sku/out_of_stock_manage/index');
                $url = $c_url . '?' . implode('&', $string);

                $page = $this->sharepage->showPage($url, $return_data['total_rows'], $curpage);


                $data = array(
                    'data' => $result,
                    'search' => $search,

                    'page' => $page,
                    'totals' => $return_data['total_rows'],
                    'c_url' => $c_url,
                );
                //  $data['data'] = $result;
                $data['status'] = $this->status;
                $data['reason'] = $this->reason;
                $data['fenzu'] = $this->user_info->gid;

                $this->_template('admin/out_of_stock_sku/index', $data);
            }
        }


    }

    public function export()
    {

        $phpExcel = new PHPExcel_Reader_Excel5;
        $extend = explode(".", $_FILES['excelFile']["name"]);
        $val = count($extend) - 1;
        $extend = strtolower($extend[$val]);
        if ($extend != 'xls' && $extend != 'xlsx') {
            echo '<script>alert("文件格式不正确，请上传EXCEL文件");history.go(-1);</script>';
            exit;
        }
        $filename = $_FILES['excelFile']["tmp_name"];
        $main_name = substr($_FILES['excelFile']["name"], 0, strpos($_FILES['excelFile']["name"], '.'));
        $objPHPExcel = $phpExcel->load($filename);
        $sheet = $objPHPExcel->getSheet();
        $rows = $sheet->getHighestRow();//EXCEL行数

        $i = 0;
        $data_array = array();
        for ($j = 2; $j <= $rows; $j++) {
            if (trim($sheet->getCell("A" . $j)->getValue())) {
                $data_array[$i]['sku'] = trim($sheet->getCell("A" . $j)->getValue());//SKU
                $data_array[$i]['reason'] = trim($sheet->getCell("B" . $j)->getValue());//原因
                $data_array[$i]['remark'] = trim($sheet->getCell("C" . $j)->getValue());//备注
                $data_array[$i]['status'] = 1;
                $i++;
            }
        }
        $result = array();
        if(!empty($data_array))
        {
            foreach($data_array as $v)
            {
                $v['sku']  = preg_replace("/\W+/",'',$v['sku']);
                $option = array();
                $option['where']['sku'] = $v['sku'];
                $result_one = $this->Out_of_stock_sku_model->getOne($option,true);
                if(empty($result_one)) {

                    $add = $v;
                    $add['exort_time'] = date('Y-m-d H:i:s', time());
                    $reason = $add['reason'];
                    if(strstr($reason,"采不到")){
                        $add['reason'] = 1;
                    }elseif(strstr($reason,"有起订量")) {
                        $add['reason'] = 2;
                    }elseif(strstr($reason,"暂时涨价")){
                        $add['reason'] = 3;
                    }else{
                        $add['reason'] = 4;
                    }
                    $add_result = $this->Out_of_stock_sku_model->add($add);
                    if ($add_result) {
                        $result[$v['sku']] = 1; //成功

                        $log =array();
                        $log['uers_id'] = $this->user_info->id;
                        $log['sku'] = $v['sku'];
                        $log['export_time'] = date('Y-m-d H:i:s', time());
                        $log['note'] = '导入该条SKU信息';

                        $this->Out_of_stock_sku_log_model->add($log);
                    } else {
                        $result[$v['sku']] = 2; // 失败
                    }
                }
                else
                {
                    $result[$v['sku']] = 3; // 重复
                }

            }

        }
        $status = array(
            1=>'成功',
            2=>'失败',
            3=>'重复'
        );
        $data = array();
        $data['status'] = $status;
        $data['data'] = $result;

        $this->_template('admin/out_of_stock_sku/export', $data);

    }

    public function cancel_order($post){

        $sku  =isset($post['search']['sku'])?$post['search']['sku']:"";
        $reason = isset($post['search']['reason'])?$post['search']['reason']:"";
        $platform = isset($post['search']['platform'])?$post['search']['platform']:"";
        $back_day = isset($post['search']['back_day'])?$post['search']['back_day']:10;
        $products_status_2 = isset($post['search']['products_status_2'])?$post['search']['products_status_2']:"";
        $option = array();
        $option['sku'] = $sku;
        $option['reason'] = $reason;
        $option['platform'] = $platform;
        $option['back_day'] = $back_day;
        $option['products_status_2'] = $products_status_2;
        $is_mix = 1;
        if(isset($post['search']['is_mix'])&&$post['search']['is_mix']==2)
        {
            $is_mix = 2;
        }
        $is_split=1;
        if(isset($post['search']['is_split'])&&$post['search']['is_split']==2)
        {
            $option['is_split'] = 2;
            $is_split=2;
        }

        $result =  $this->Out_of_stock_sku_model->getOrders($option,$is_mix);


        if(isset($post['cancel'])){
           // if($is_mix==2)
           // {
         //       echo '<script>alert("不支持混合订单撤单");history.go(-1);</script>';
           //     exit;
          //  }else{
                $this->start_cancel_orders($result);
           // }

        }elseif(isset($post['exportout'])){
            $this->exopotOut($result);
        }else{
            $data = array();
            $data['sku'] = $sku;
            $data['reason']  = $reason;
            $data['platform'] = $platform;
            $data['back_day'] = $back_day;
            $data['products_status_2'] = $products_status_2;
        //    echo  $data['products_status_2'] ;exit;
            $data['is_mix'] =$is_mix;
            $data['is_split'] =$is_split;
            $data['order_type'] =  $this->Out_of_stock_sku_model->getPalt();

            $data['result'] = $result;
            $this->_template('admin/out_of_stock_sku/cancel_order', $data);
        }
      //  var_dump($result);

    }


    public function start_cancel_orders($result)
    {
        $return_array  =array();
        if(!empty($result))
        {
            $updata_data = array();
            $updata_data['orders_status'] = 6;

            foreach($result['last_result'] as $re){
                $option = array();
                $option['where']['erp_orders_id'] = $re['erp_orders_id'];

                $updata_result =  $this->orders_model->update($updata_data,$option);

                if($updata_result){
                    $return_array[$re['erp_orders_id']] = '撤单成功';
                    $user = $this->user_info->old_id;
                    mysql_query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText)
VALUES('$user','update','ordersManage','".$re['erp_orders_id']."','通过缺货管理-缺货撤单')");

                }else{
                    $return_array[$re['erp_orders_id']] = '撤单失败';
                }

            }
        }

        $data = array();
        $data['data'] = $return_array;

        $this->_template('admin/out_of_stock_sku/cancel_result', $data);
    }

    public function change_stauts(){

        $return_result= array();
        $sku = trim($_POST['sku']);
        $type =trim($_POST['type']);
        $option =array();
        $updata_array =array();
        $option['where']['sku'] = $sku;
        if($type==1)
        {

           $delete_result = $this->Out_of_stock_sku_model->delete($option);
            if($delete_result){
                $return_result['type'] = 1;
                $return_result['info'] = "删除成功";


                $log = array();
                $log['uers_id'] = $this->user_info->id;
                $log['sku'] = $sku;
                $log['export_time'] = date('Y-m-d H:i:s', time());
                $log['note'] = '将该SKU信息删除';
                $this->Out_of_stock_sku_log_model->add($log);


                ajax_return('',1,$return_result);
            }else{
                $return_result['type'] = 1;
                $return_result['info'] = "删除失败";
                ajax_return('',2,$return_result);
            }
        }else{
            $updata_array['status'] = intval($type);
            $updata_result =   $this->Out_of_stock_sku_model->update($updata_array,$option);

            if($updata_result){

                $log = array();
                $log['uers_id'] = $this->user_info->id;
                $log['sku'] = $sku;
                $log['export_time'] = date('Y-m-d H:i:s', time());
                $log['note'] = '将该SKU状态变更为'.$this->status[$type];
                $this->Out_of_stock_sku_log_model->add($log);
                $return_result['type'] = $type;
                $return_result['info'] = "成功";
                ajax_return('',1,$return_result);

            }else{
                $return_result['type'] = $type;
                $return_result['info'] = "失败";
                ajax_return('',2,$return_result);
            }
        }
    }

    public function showlog(){

        $sku =$_GET['sku'];

        $option = array();
        $option['where']['sku'] = trim($sku);
        $option['order'] ="export_time DESC";

        $result = $this->Out_of_stock_sku_log_model->getAll2Array($option);
      //  echo $this->db->last_query();exit;
        $user_info  =  $this->slme_user_model->get_all_user_info('nickname');
       // var_dump($user_info);

        $data = array();
        $data['data'] = $result;
        $data['user'] = $user_info;
        $this->template('admin/out_of_stock_sku/show_log', $data);
    }


    public function exopotOut($result){

      //  var_dump($result);exit;
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array ('memoryCacheSize' => '512MB' );
        PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );
        $phpExcel=new PHPExcel();
        //设置标题
        $phpExcel->getProperties()->setCreator("Moonar")->setLastModifiedBy("Moonar");
        $phpExcel->getActiveSheet()
            ->setCellValue('A1', '内单号')
            ->setCellValue('B1', 'buyer_id')
            ->setCellValue('C1', '账号')
            ->setCellValue('D1', 'sku')
            ->setCellValue('E1', '平台')
            ->setCellValue('F1', '欠货时间');
        $i=2;
        if(!empty($result['last_result']))
        {
           $orders_palt =  $this->Out_of_stock_sku_model->getPalt();
            foreach($result['last_result'] as $k=>$re){
              $day =   (time()-strtotime($re['orders_export_time']))/(24*60*60);
                $phpExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $re['erp_orders_id'])
                    ->setCellValue('B' . $i, $re['buyer_id'])
                    ->setCellValue('C' . $i, $re['sales_account'])
                    ->setCellValue('D' . $i, $re['mix_sku'])
                    ->setCellValue('E' . $i, $orders_palt[$re['orders_type']])
                    ->setCellValue('F' . $i, intval($day));
                $i++;
            }
        }
        $phpExcel->getActiveSheet ()->setTitle ( '订单信息' );
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
        $objWriter->save('php://output');
        die;

    }

    public function exopotOutFrist(){

        $product_stauts= array(
            'selling'=>'在售',
            'sellWaiting'=>'待售',
            'stopping'=>'停产',
            'saleOutStopping'=>'卖完下架',
            'unSellTemp'=>'货源待定',
            'trySale'=>'试销(卖多少采多少)',
        );

        $search = $this->input->get_post('search');
        if (isset($search['sku']) && $search['sku']) {
            $where['sku'] = trim($search['sku']);
        }
        if (isset($search['reason']) && $search['reason']) {
            $where['reason'] = trim($search['reason']);
        }
        if (isset($search['status']) && $search['status']) {
            $where['status'] = trim($search['status']);
        } else {
            $where['status'] = 1;
        }

        $join[] = array($this->products_data_model->_table.' p',"p.products_sku=sku");
        $options = array(
            'select' =>$this->Out_of_stock_sku_model->_table.'.* , p.products_status_2 ',
            'where' => $where,
            'join'=>$join,
            'group_by' =>'sku'
        );

        $result = $this->Out_of_stock_sku_model->getAll2Array($options);
        foreach($result as $re){
            $sku_result =     $this->Out_of_stock_sku_model->getSkuCount($re['sku']);
            $re['skuCount'] = $sku_result['num'];
            $result_mid[] = $re;
        }
        $result = $result_mid;

        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array ('memoryCacheSize' => '512MB' );
        PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );
        $phpExcel=new PHPExcel();
        //设置标题
        $phpExcel->getProperties()->setCreator("Moonar")->setLastModifiedBy("Moonar");
        $phpExcel->getActiveSheet()
            ->setCellValue('A1', 'SKU')
            ->setCellValue('B1', '欠货数量')
            ->setCellValue('C1', '状态')
            ->setCellValue('D1', '缺货原因')
            ->setCellValue('E1', '录入时间')
            ->setCellValue('F1', '状态')
            ->setCellValue('G1', '备注');
        $i=2;
        if(!empty($result))
        {
            foreach($result as $re){
                $phpExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $re['sku'])
                    ->setCellValue('B' . $i, $re['skuCount'])
                    ->setCellValue('C' . $i, $product_stauts[$re['products_status_2']])
                    ->setCellValue('D' . $i, $this->status[$re['reason']])
                    ->setCellValue('E' . $i, $re['exort_time'])
                    ->setCellValue('F' . $i, $this->reason[$re['status']])
                    ->setCellValue('G' . $i, $re['remark']);//remark

                $i++;
            }
        }
        $phpExcel->getActiveSheet ()->setTitle ( '订单信息' );
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
        $objWriter->save('php://output');
        die;

    }


    function arrayToObject($e)
    {
        if (gettype($e) != 'array') return;
        foreach ($e as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object')
                $e[$k] = (object)$this->arrayToObject($v);
        }
        return (object)$e;
    }

    function objectToArray($e)
    {
        $e = (array)$e;
        foreach ($e as $k => $v) {
            if (gettype($v) == 'resource') return;
            if (gettype($v) == 'object' || gettype($v) == 'array')
                $e[$k] = (array)$this->objectToArray($v);
        }
        return $e;
    }


    public function batch_confirm(){
        $skus = $_POST['skus'];
        $skus = explode(',',$skus);
        foreach($skus as $sku){
            $option=array(
                'sku'=>$sku,
            );
            $updata_array['status'] = 2;
            $updata_result =   $this->Out_of_stock_sku_model->update($updata_array,$option);
            if($updata_result){
                $log = array();
                $log['uers_id'] = $this->user_info->id;
                $log['sku'] = $sku;
                $log['export_time'] = date('Y-m-d H:i:s', time());
                $log['note'] = '将该SKU状态变更为'.$this->status[2];
                $this->Out_of_stock_sku_log_model->add($log);
            }
        }
        ajax_return('操作完成',2);
    }

    public function batch_delete(){
        $skus = $_POST['skus'];
        $skus = explode(',',$skus);
        foreach($skus as $sku){
          $this->delete_sku($sku);
        }
        ajax_return('操作完成',2);
    }
    public function all_confirm(){
        $option =array();
        $option['where']['status'] = 1;
        $result = $this->Out_of_stock_sku_model->getAll2Array($option);
        if(!empty($result)){
            foreach($result as $re){
                $option =array();
                $option['where']['id'] = $re['id'];
                $updata_data =array();
                $updata_data['status'] = 2;
                $updata_result =   $this->Out_of_stock_sku_model->update($updata_data,$option);
                if($updata_result){
                    $log = array();
                    $log['uers_id'] = $this->user_info->id;
                    $log['sku'] = $re['sku'];
                    $log['export_time'] = date('Y-m-d H:i:s', time());
                    $log['note'] = '将该SKU状态变更为'.$this->status[2];
                    $this->Out_of_stock_sku_log_model->add($log);
                }
            }
        }
        ajax_return('操作完成',2);
    }

    public function all_detele(){
        $option =array();
        $option['sku'] = isset($_POST['sku'])?$_POST['sku']:"";
        $option['reason'] = isset($_POST['reason'])?$_POST['reason']:"";
        $option['status'] = isset($_POST['status'])?$_POST['status']:"";
        $option['products_status_2'] = isset($_POST['products_status_2'])?$_POST['products_status_2']:"";
        $result = $this->Out_of_stock_sku_model->get_detele_sku($option);
        if(!empty($result)){
            foreach($result as $re){
                $this->delete_sku($re['sku']);
            }
        }

        ajax_return('操作完成',2);




    }

    public function delete_sku($sku){
        $option=array(
            'sku'=>$sku,
        );
        $delete_result = $this->Out_of_stock_sku_model->delete($option);
        if($delete_result){
            $log = array();
            $log['uers_id'] = $this->user_info->id;
            $log['sku'] = $sku;
            $log['export_time'] = date('Y-m-d H:i:s', time());
            $log['note'] = '将该SKU信息删除';
            $this->Out_of_stock_sku_log_model->add($log);
        }
    }



/*    public function test(){
        $option = array();
        $result = $this->Out_of_stock_sku_model->getAll2Array($option);
        foreach($result as $re){
            $re['sku']  = preg_replace("/\W+/",'',$re['sku']);
            $updata_data = array();
            $updata_option  = array();
            $updata_option['where']['id'] = $re['id'];
            $updata_data['sku'] = trim($re['sku']);
            $this->Out_of_stock_sku_model->update($updata_data,$updata_option);
            echo $this->db->last_query();
            echo '<br/>';
        }

    }*/



}