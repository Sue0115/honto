<?php
class KindEditor extends MY_Controller{
    //定义允许上传的文件扩展名
    private $ext_arr;
    //最大文件大小
    private $max_size;

    function __construct(){
        parent::__construct();
        $this->load->library('MySmt');
        $this->smt = new MySmt();
        $this->load->model(array(
            'smt/Smt_user_tokens_model',
        ));
        $this->model = $this->Smt_user_tokens_model;
        $this->load->helper('common');
    }

    //$max_size大小限制，让速卖通来返回吧
    public function setControlParams($api, $max_size=2048000, $ext_arr=array()) {

        //定义允许上传的文件扩展名
        if (empty($ext_arr)) {
           $ext_arr = array(
                'image' =>array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
                'flash' => array('swf', 'flv'),
                'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
                'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
            );
        }
        $this->ext_arr = $ext_arr;

        //最大文件大小
        if (empty($max_size)){
            $max_size = $api == 'api.uploadTempImage' ? 200*1024 : 1000000;
        }
        $this->max_size = $max_size;
    }

    /**
     * API上传到速卖通
     */
    public function upload() {
        //新文件名
        $token_id = (int)$this->input->get_post('token_id');
        $target   = trim($this->input->get_post('target'));
        $token_id = $token_id ? $token_id : 36;
        $this->getTokenForThisCall($token_id);
        $api      = 'api.uploadImage'; //调用的API
        if ($target == 'temp') {
            $api  = 'api.uploadTempImage';
        }
        $this->setControlParams($api);

        //PHP上传失败
        if (!empty($_FILES['imgFile']['error'])) {
            switch($_FILES['imgFile']['error']){
                case '1':
                    $error = '超过php.ini允许的大小。';
                    break;
                case '2':
                    $error = '超过表单允许的大小。';
                    break;
                case '3':
                    $error = '图片只有部分被上传。';
                    break;
                case '4':
                    $error = '请选择图片。';
                    break;
                case '6':
                    $error = '找不到临时目录。';
                    break;
                case '7':
                    $error = '写文件到硬盘出错。';
                    break;
                case '8':
                    $error = 'File upload stopped by extension。';
                    break;
                case '999':
                default:
                    $error = '未知错误。';
            }
            $this->alert($error);
        }
        //有上传文件时
        if (empty($_FILES) === false) {
            //原文件名
            $file_name = $_FILES['imgFile']['name'];
            //服务器上临时文件名
            $tmp_name  = $_FILES['imgFile']['tmp_name'];
            //文件大小
            $file_size = $_FILES['imgFile']['size'];
            //检查文件名
            if (!$file_name) {
                $this->alert("请选择文件");
            }
            //检查是否已上传
            if (@is_uploaded_file($tmp_name) === false) {
                $this->alert("临时文件可能不是上传文件。");
            }
            //检查文件大小
            if ($file_size > $this->max_size) {
                $this->alert("上传文件大小超过限制。");
            }
            //获得文件扩展名
            $temp_arr = explode(".", $file_name);
            $file_ext = array_pop($temp_arr);
            $filename = array_pop(explode('/', $temp_arr[0]));
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);
            //检查扩展名
            if (in_array($file_ext, $this->ext_arr['image']) === false) {
                $this->alert("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $this->ext_arr['image']) . "格式。");
            }

            $data     = $this->smt->uploadBankImage($api, $tmp_name, $filename);

            header('Content-type: text/html; charset=UTF-8');
            if ($data['success']) {
                if ($api == 'api.uploadImage'){
                    if ($data['status'] == 'SUCCESS' || $data['status'] == 'DUPLICATE') {
                        //返回并插入到表单中--没返回iid，暂时不插入到数据库中
                        echo json_encode(array('error' => 0, 'url' => $data['photobankUrl']));
                    }elseif ($data['status'] == 'NOCAPACITY') {
                        $this->alert('图片空间不足');
                    }else {
                        $this->alert('未知错误');
                    }
                }else {
                    echo json_encode(array('error' => 0, 'url' => $data['url']));
                }
            }else{
                $this->alert('操作失败');
            }
            exit;
        }
    }

    public function alert($msg) {
        header('Content-type: text/html; charset=UTF-8');
        echo json_encode(array('error' => 1, 'message' => $msg));
        exit;
    }

    /**
     * 设置一个账号的信息，并同步过期的token信息 --调用smtAPI的话，直接先用这个吧
     * @param  [type] $token_id [description]
     * @return [type]           [description]
     */
    protected function getTokenForThisCall($token_id){
        $new_token = array();
        $token_arr = $this->model->getOne($token_id, true);

        if ($token_arr) {
            $new_token = $this->smt->setToken($token_arr);
        }
        //如果有返回新的数组，说明有token过期了，要同步到数据库
        if ($new_token) {
            $this->model->update($new_token);
        }
    }

    public function uploadToProject(){
        //上传到的目录名称
        $dir = trim($this->input->get_post('dir'));
        $dir_name = empty($dir) ? 'image' : $dir; //目录名称

        $this->setControlParams('');

        //PHP上传失败
        if (!empty($_FILES['imgFile']['error'])) {
            switch($_FILES['imgFile']['error']){
                case '1':
                    $error = '超过php.ini允许的大小。';
                    break;
                case '2':
                    $error = '超过表单允许的大小。';
                    break;
                case '3':
                    $error = '图片只有部分被上传。';
                    break;
                case '4':
                    $error = '请选择图片。';
                    break;
                case '6':
                    $error = '找不到临时目录。';
                    break;
                case '7':
                    $error = '写文件到硬盘出错。';
                    break;
                case '8':
                    $error = 'File upload stopped by extension。';
                    break;
                case '999':
                default:
                    $error = '未知错误。';
            }
            $this->alert($error);
        }
        //有上传文件时
        if (empty($_FILES) === false) {
            //原文件名
            $file_name = $_FILES['imgFile']['name'];
            //服务器上临时文件名
            $tmp_name  = $_FILES['imgFile']['tmp_name'];
            //文件大小
            $file_size = $_FILES['imgFile']['size'];
            //检查文件名
            if (!$file_name) {
                $this->alert("请选择文件");
            }
            //检查是否已上传
            if (@is_uploaded_file($tmp_name) === false) {
                $this->alert("临时文件可能不是上传文件。");
            }
            //检查文件大小
            if ($file_size > $this->max_size) {
                $this->alert("上传文件大小超过限制。");
            }
            //获得文件扩展名
            $temp_arr = explode(".", $file_name);
            $file_ext = array_pop($temp_arr);
            $filename = array_pop(explode('/', $temp_arr[0]));
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);
            //检查扩展名
            if (in_array($file_ext, $this->ext_arr['image']) === false) {
                $this->alert("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $this->ext_arr['image']) . "格式。");
            }

            $save_path = dirname(dirname(dirname(__DIR__))).'/attachments/upload/'; //保存路径
            $save_url = '/';

            //创建文件夹
            if ($dir_name !== '') {
                $save_path .= $dir_name . "/";
                $save_url .= $dir_name . "/";
                if (!file_exists($save_path)) {
                    mkdir($save_path, 0777, true);
                    //@chmod($save_path, 0777);
                }
            }
            $ymd = date("Ymd");
            $save_path .= $ymd . "/";
            $save_url .= $ymd . "/";

            if (!file_exists($save_path)) {
                mkdir($save_path, 0777, true);
                //@chmod($save_path, 0777);
            }
            //新文件名
            $new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
            //移动文件
            $file_path = $save_path . $new_file_name;
            if (move_uploaded_file($tmp_name, $file_path) === false) {
                $this->alert("上传文件失败。");
            }

            @chmod($file_path, 0644);
            $file_url = $save_url . $new_file_name;
            header('Content-type: text/html; charset=UTF-8');
            echo json_encode(array('error' => 0, 'url' => $file_url));

            exit;
        }
    }
}