<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 亚马逊上传数据API
 * Class Feeds
 */
class Feeds extends Admin_Controller
{

    function __construct()
    {
        parent::__construct();

        //$this->load->library('MyAmz');
        //$this->amz = new MyAmz();

        $this->load->model(array(
            'amz/Amz_config_model',
            'amz/Slme_amz_product_image_model',
            'sharepage'
        ));
        $this->amzTokens     = $this->Amz_config_model;
        $this->productImages = $this->Slme_amz_product_image_model;
    }

    public function index()
    {
        $params = $this->input->get();

        $queryParams = array(); //查询参数

        $where = array();
        $like  = array();
        if (!empty($params['token_id'])) { //有选择账号
            $where['token_id']       = $params['token_id'];
            $queryParams['token_id'] = $params['token_id'];
        }

        if (!empty($params['sku'])) { //有选择SKU
            $like['sku']        = trim($params['sku']);
            $queryParams['sku'] = trim($params['sku']);
        }

        if (isset($params['callresult']) && $params['callresult'] != '') {
            $where['callresult']       = $params['callresult'];
            $queryParams['callresult'] = $params['callresult'];
        }else {

            $where['callresult']       = 0;
        }

        //每页条数
        $cupage = (int)$this->config->item('site_page_num');
        //页码
        $per_page = (int)$this->input->get_post('per_page');

        $options    = array(
            'page'     => $cupage,
            'per_page' => $per_page,
            'where'    => $where,
            'like'     => $like,
        );
        $return_arr = array('total_rows' => true); //返回分页信息
        $imageList  = $this->productImages->getAll($options, $return_arr);

        $url  = admin_base_url('amz/feeds/index') . '?' . http_build_query($queryParams);
        $page = $this->sharepage->showPage($url, $return_arr['total_rows'], $cupage);

        //上传状态定义
        $callResult = $this->productImages->defineUploadedStatus();

        //获取相应的账号列表
        $tokenList = $this->amzTokens->formatTokenList(array('method' => 'submitFeed', 'status' => 1, 'order_by' => 'id asc'));

        $data = array(
            'imageList' => $imageList,
            'page'      => $page,
            'params'    => $params,
            'callResult' => $callResult,
            'tokenList' => $tokenList
        );

        $this->_template('admin/amz/feeds/imageList', $data);
    }

    /**
     * 编辑或者新增
     */
    public function info()
    {
        parent::info();

        $id = $this->input->get_post('id');

        //查询出图片链接结果
        $productImages = $this->productImages->getOneProductImage($id);

        //查询出亚马逊站点列表
        $tokenList = $this->amzTokens->formatTokenList(array('method' => 'submitFeed', 'status' => 1, 'order_by' => 'id asc'));

        $data = array(
            'productImages' => $productImages,
            'tokenList'     => $tokenList
        );
        $this->_template('admin/amz/feeds/info', $data);
    }

    /**
     * 保存操作
     */
    protected function save()
    {
        $id = $this->input->get_post('id');

        //站点ID
        $data['token_id'] = $this->input->get_post('token_id');

        //AMZSKU
        $data['sku'] = trim($this->input->get_post('sku'));

        //Main pic
        $data['Main'] = trim($this->input->get_post('Main'));
        //Swatch pic
        $data['Swatch'] = trim($this->input->get_post('Swatch'));
        //PT1-8
        $data['PT1'] = trim($this->input->get_post('PT1'));
        $data['PT2'] = trim($this->input->get_post('PT2'));
        $data['PT3'] = trim($this->input->get_post('PT3'));
        $data['PT4'] = trim($this->input->get_post('PT4'));
        $data['PT5'] = trim($this->input->get_post('PT5'));
        $data['PT6'] = trim($this->input->get_post('PT6'));
        $data['PT7'] = trim($this->input->get_post('PT7'));
        $data['PT8'] = trim($this->input->get_post('PT8'));
        $data['callresult'] = 0;

        if ($id) {
            $data['id'] = $id;
            $affected   = $this->productImages->update($data);
            if ($affected) {
                echo json_encode(array(
                    'info'   => '保存成功',
                    'status' => 'y',
                    'id'     => $id
                ));
            } else {
                echo json_encode(array(
                    'info'   => '保存失败',
                    'status' => 'n'
                ));
            }
        } else {
            $data['created_at'] = time();
            $newId              = $this->productImages->add($data);
            if ($newId) {
                echo json_encode(array(
                    'info'   => '新增成功',
                    'status' => 'y',
                    'id'     => $newId
                ));
            } else {
                echo json_encode(array(
                    'info'   => '新增失败',
                    'status' => 'n'
                ));
            }
        }

        die();
    }

    //导入产品图片信息
    public function exportIn(){

        $error = '';
        $success = false;
        if ($_FILES){
            //header('Content-Type: text/html; Charset=utf-8');
            $name = $_FILES['file']['name'];
            $tmpname = $_FILES['file']['tmp_name'];

            //扩展名
            $ext = pathinfo($name, PATHINFO_EXTENSION);

            //允许上传的文件类型
            $allowExtension = array('xls', 'xlsx');

            if ($_FILES['file']['error'] > 0){
                $error = '文件错误';
            }elseif (!in_array($ext, $allowExtension)){
                $error = '文件类型错误';
            }else {
                $this->load->library('phpexcel/PHPExcel');
                $PHPReader = new PHPExcel_Reader_Excel2007();

                if (!$PHPReader->canRead($tmpname)){
                    $PHPReader = new PHPExcel_Reader_Excel5();
                }

                $objPHPExcel = $PHPReader->load($tmpname);

                $sheet = $objPHPExcel->getSheet(0);

                //总行数列数
                $rows  = $sheet->getHighestRow();
                //$cols  = $sheet->getHighestColumn();

                $data  = array(); //存放读取出来的数据,即产品图片信息
                for ($i = 2; $i <= $rows; $i++){
                    $data[$i]['A'] = trim($sheet->getCell('A' . $i)->getValue());
                    $data[$i]['B'] = trim($sheet->getCell('B' . $i)->getValue());
                    $data[$i]['C'] = trim($sheet->getCell('C' . $i)->getValue());
                    $data[$i]['D'] = trim($sheet->getCell('D' . $i)->getValue());
                    $data[$i]['E'] = trim($sheet->getCell('E' . $i)->getValue());
                    $data[$i]['F'] = trim($sheet->getCell('F' . $i)->getValue());
                    $data[$i]['G'] = trim($sheet->getCell('G' . $i)->getValue());
                    $data[$i]['H'] = trim($sheet->getCell('H' . $i)->getValue());
                    $data[$i]['I'] = trim($sheet->getCell('I' . $i)->getValue());
                    $data[$i]['J'] = trim($sheet->getCell('J' . $i)->getValue());
                    $data[$i]['K'] = trim($sheet->getCell('K' . $i)->getValue());
                    $data[$i]['L'] = trim($sheet->getCell('L' . $i)->getValue());
                }

                $colNumArray = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L');
                if ($data){
                    $pattern = '/^http:\/\/\w+(\.\w+)+.*\.((jpg)|(jpeg)|(gif)|(bmp)|(png))(\?.*)?$/i';
                    foreach ($data as $line => $row){
                        $errorFlag = false;
                        foreach ($colNumArray as $col) {
                            if (in_array($col, array('A', 'B', 'C', 'D'))){
                                if (empty($row[$col])){
                                    $error[] = '第'.$line.'行导入失败,单元格'.$col.$line.'为空';
                                    $errorFlag = true;
                                    break;
                                }
                            }

                            if (in_array($col, array('C', 'D'))){
                                if (!preg_match($pattern, $row[$col])){
                                    $error[] = '第'.$line.'行导入失败,单元格'.$col.$line.'必须是以http://开头的图片链接';
                                    $errorFlag = true;
                                    break;
                                }
                            }

                            if (in_array($col, array('E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'))){
                                if (!empty($row[$col]) && !preg_match($pattern, $row[$col])){
                                    $error[] = '第'.$line.'行导入失败,单元格'.$col.$line.'不为空时必须是以http://开头的图片链接';
                                    $errorFlag = true;
                                    break;
                                }
                            }
                        }
                        if ($errorFlag){
                            unset($data[$line]);
                        }
                    }

                    if ($data){//还有正确的数据，说明都是可以导入的
                        //查询出亚马逊站点列表
                        $tokenList = $this->amzTokens->formatTokenList(array('method' => 'submitFeed', 'status' => 1, 'order_by' => 'id asc'), true);
                        $tokenList = array_flip($tokenList);
                        foreach ($data as $line => $row){
                            //将账号替换成对应的ID
                            if (!array_key_exists(strtoupper($row['A']), $tokenList)){
                                $error[] = '第'.$line.'行导入失败,账号错误';
                                continue;
                            }

                            $option['token_id']   = $tokenList[strtoupper($row['A'])];
                            $option['sku']        = $row['B'];
                            $option['Main']       = $row['C'];
                            $option['Swatch']     = $row['D'];
                            $option['PT1']        = $row['E'];
                            $option['PT2']        = $row['F'];
                            $option['PT3']        = $row['G'];
                            $option['PT4']        = $row['H'];
                            $option['PT5']        = $row['I'];
                            $option['PT6']        = $row['J'];
                            $option['PT7']        = $row['K'];
                            $option['PT8']        = $row['L'];
                            $option['created_at'] = time();
                            $option['callresult'] = 0; //未上传状态

                            if (!$this->productImages->add($option)){
                                $error[] = '第'.$line.'行保存到数据库失败';
                            }else {
                                $success = true;
                            }
                        }
                    }

                    $error = $error ? implode('；', $error) : $error;
                }else {
                    $error = '没有读取到数据信息，请先下载模板文件';
                }
            }
        }
        $this->template('admin/amz/feeds/exportIn', array('error' => $error, 'success' => $success));
    }

    /**
     * 数据作废处理
     */
    public function trash(){
        $id = $this->input->get_post('id');

        if (empty($id)){
            ajax_return('作废失败', false, '传入的数据为空');
        }

        //循环判断吧
        $idArray = explode(',', $id);

        $flag = false;
        $errorID = array();
        $successID = array();
        foreach ($idArray as $pid){
            $info = $this->productImages->getOneProductImage($pid, 'callresult');
            if ($info->callresult == '0'){
                $successID[] = $pid;
            }else {
                $errorID[] = $pid;
            }
        }

        if ($successID){
            $option['callresult'] = 1;
            $res = $this->productImages->update($option, array('where_in' => array('id' => $successID)));
            $flag = $res ? true : false;
        }

        $massage = $errorID ? implode(',', $errorID).'状态错误,无需变更' : '';
        ajax_return('操作'.($flag ? '成功' : '失败'), $flag, $massage);
    }

    /**
     * 获取报告，测试使用
     */
    /*protected function getReport()
    {
        //查询配置文件
        $tokenArray = $this->amzTokens->getAllTokens(array('method' => 'submitFeed', 'status' => 1));

        if ($tokenArray) {
            foreach ($tokenArray as $token) {
                if ($token['place_name'] != 'US')
                    continue;
                //print_r($token);exit;
                $parameterArray = array(
                    'AWSAccessKeyId' => $token['access_key'], //账号
                    'AWSAccessKey'   => $token['secret_key'], //密码
                    'ServerUrl'      => $token['place_site'], //链接地址
                    'Merchant'       => $token['merchant_id'],
                    'Timestamp'      => $this->amz->getFormattedTimestamp(),
                    'Version'        => '2009-01-01',
                    'Action'         => 'GetReport', //操作
                    'ReportId'       => '23330955063',//报告ID
                );
                $response       = $this->amz->httpPost($parameterArray, $token['place_name']);
                print_r($response);
            }
        }
    }*/

    public function test(){
        $this->productImages->upload();
    }
}