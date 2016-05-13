<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 图片增加水印
 * @authors Lidabiao
 * @date    2016-3-28
 * 图片加水印（适用于png/jpg/gif格式）
 *
 * @author flynetcn
 *
 * @param $srcImg 原图片
 * @param $waterImg 水印图片
 * @param $savepath 保存路径
 * @param $savename 保存名字
 * @param $positon 水印位置
 * 1:顶部居左, 2:顶部居右, 3:居中, 4:底部局左, 5:底部居右
 * @param $alpha 透明度 -- 0:完全透明, 100:完全不透明
 *
 * @return 成功 -- 加水印后的新图片地址
 *          失败 -- -1:原文件不存在, -2:水印图片不存在, -3:原文件图像对象建立失败
 *          -4:水印文件图像对象建立失败 -5:加水印后的新图片保存失败
 */
class WaterMark extends Admin_Controller
{
     public function __construct(){
       parent::__construct();
        $this->load->library('MySmt');
        $this->smt = new MySmt();
        $this->load->model(array(
            'smt/Smt_user_tokens_model',
        ));
        $this->model = $this->Smt_user_tokens_model;
        $this->load->helper('common');
    }
    public function waterImgupload($makeurl,$imgtype){
        $token_id = (int)$this->input->get_post('token_id');
        $target   = 'temp';
        $token_id = $token_id ? $token_id : 36;
        $this->getTokenForThisCall($token_id);
        $api      = 'api.uploadImage'; //调用的API
        if ($target == 'temp') {
            $api  = 'api.uploadTempImage';
        }
        //缓存路径
        $tmpurl = './static/theme/common/makeImg/wimg.tmp';
        file_put_contents($tmpurl, file_get_contents($makeurl));//生成tmp图片文件
        $data     = $this->smt->uploadBankImage($api, $tmpurl, $imgtype);

        return $data;

    }
    public function imgwater(){
        $imgsrc=$this->input->get_post('imgsrc');
        $token_id=$this->input->get_post('token_id');
        $imgdata='';
        $newimg =array('url'=>'');
        $action =isset($_POST['action'])?$_POST['action']:'';
        if($action=='doup')
        {  
            $last=strrpos($imgsrc,'.')+1;
            $imgtype=substr($imgsrc,$last);//图片风格

            $uptypes=array('image/jpg','image/jpeg','image/pjpeg','image/gif');//上传图片文件类型列表
            //$wFile=$_FILES['upfile'];//取得文件路径
            $wFile=$imgsrc;//取得文件路径

            $waterimg=$_FILES['water11'];//水印图片路径
            //print_r($wFile);
            if(($imgtype =='jpg') || ($imgtype =='pjpeg') || ($imgtype =='jpeg'))
            {//检查文件类型，若上传的文件为jpg或gif图片则加水印
            if($imgtype =='png'){
                $im = imagecreatefrompng($wFile);
                $wfilew=imagesx($im2);//取得图片的宽
                 $wfileh=imagesy($im2);//取得图片的高
            }elseif(($imgtype =='jpg') || ($imgtype =='pjpeg') || ($imgtype =='jpeg')){
                $im = imageCreatefromjpeg($wFile);
                $wfilew=imagesx($im);//取得图片的宽
                $wfileh=imagesy($im);//取得图片的高
            }else{
                echo '图片类型不符合';exit;
            }
           if(strstr($waterimg['type'],"image/png"))
            { 
              $im2 = imageCreatefrompng($waterimg['tmp_name']);
              $waterw=imagesx($im2);//取得图片的宽
              $waterh=imagesy($im2);//取得图片的高
           }elseif(strstr($waterimg['type'],"jp")){
                $im2 = imageCreatefromjpeg($waterimg['tmp_name']);
                $waterw=imagesx($im2);//取得图片的宽
                $waterh=imagesy($im2);//取得图片的高
           }
           else
            {//否则若上传图片类型为gif，则用imagecreatefromgif读取目标文件
                $im2 = imageCreatefromgif($waterimg['tmp_name']);
                $waterw=imagesx($im2);//取得图片的宽
                $waterh=imagesy($im2);//取得图片的高
              }
         //设定混合模式
            imagealphablending($im2, true);
          //随机放水印到图片中
             $randval = rand(0,9);//在0-9之间产生随机数
            // if($randval==0||$randval==3||$randval==2||$randval==8||$randval==7){//此处还可完善放更多位置
            //   $wimgx=5;$wimgy=5;//放左上角
            // }else{
            //   $wimgx=$wfilew-5-$waterw;$wimgy=$wfileh-5-$waterh;//放右上角
            // }
              $wimgx=5;$wimgy=5;//放左上角
            //拷贝水印到目标文件
            imagecopy($im, $im2, $wimgx, $wimgy, 0, 0, $waterw,$waterh);
           
        //输出图片
           if(($imgtype =='jpg') || ($imgtype =='pjpeg') || ($imgtype =='jpeg')){ //同上
              imagejpeg($im,'./static/theme/common/makeImg/waterimg.'.$imgtype);
            }else{
              imagegif($im,'./static/theme/common/makeImg/waterimg.'.$imgtype);
            }
            imagedestroy($im);
            imagedestroy($im2);
            $newname='waterimg.'.$imgtype;
            $makeurl = './static/theme/common/makeImg/'.$newname;
            $newimg =$this->waterImgupload($makeurl,$imgtype);
            $imgdata = "<div style='margin:auto'><img src='".$newimg['url']."' style='width:100px;height:100px'></div>";
          }
        else echo "图片不符合！！";
        }
        $data=array('imgdata'=>$imgdata,'imgsrc'=>$imgsrc,'token_id'=>$token_id,'newimg'=>$newimg['url']);
        $this->only_template('admin/publish/smt/imgwater',$data);
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
	//Ajax增加水印,返回图片链接
	public function img_water_mark($srcImg, $waterImg, $savepath=null, $savename=null, $positon=1, $alpha=30)
    {
        $srcImg = $this->input->get_post('src');
        $waterImg = './static/theme/common/waterImg/11.png';
        $savepath = './static/theme/common/makeImg/';

        $temp = pathinfo($srcImg);
        $name = $temp['basename'];
        $path = $temp['dirname'];
        $exte = $temp['extension'];
        $savename = $savename ? $savename : $name;
        $savepath = $savepath ? $savepath : $path;
        $savefile = $savepath .'/'. $savename;
        $srcinfo = @getimagesize($srcImg);
        if (!$srcinfo) {
            return -1; //原文件不存在
        }
        $waterinfo = @getimagesize($waterImg);
        if (!$waterinfo) {
            return -2; //水印图片不存在
        }
        $srcImgObj = $this->image_create_from_ext($srcImg);
        if (!$srcImgObj) {
            return -3; //原文件图像对象建立失败
        }
        $waterImgObj = $this->image_create_from_ext($waterImg);
        if (!$waterImgObj) {
            return -4; //水印文件图像对象建立失败
        }
        switch ($positon) {
        //1顶部居左
        case 1: $x=$y=0; break;
        //2顶部居右
        case 2: $x = $srcinfo[0]-$waterinfo[0]; $y = 0; break;
        //3居中
        case 3: $x = ($srcinfo[0]-$waterinfo[0])/2; $y = ($srcinfo[1]-$waterinfo[1])/2; break;
        //4底部居左
        case 4: $x = 0; $y = $srcinfo[1]-$waterinfo[1]; break;
        //5底部居右
        case 5: $x = $srcinfo[0]-$waterinfo[0]; $y = $srcinfo[1]-$waterinfo[1]; break;
        default: $x=$y=0;
        }
        imagecopymerge($srcImgObj, $waterImgObj, $x, $y, 0, 0, $waterinfo[0], $waterinfo[1], $alpha);
        switch ($srcinfo[2]) {
        case 1: imagegif($srcImgObj, $savefile); break;
        case 2: imagejpeg($srcImgObj, $savefile); break;
        case 3: imagepng($srcImgObj, $savefile); break;
        default: return -5; //保存失败
        }
        imagedestroy($srcImgObj);
        imagedestroy($waterImgObj);

        //返回src
        ajax_return($savename);

}


public function image_create_from_ext($imgfile)
{
    $info = getimagesize($imgfile);
    $im = null;
    switch ($info[2]) {
    case 1: $im=imagecreatefromgif($imgfile); break;
    case 2: $im=imagecreatefromjpeg($imgfile); break;
    case 3: $im=imagecreatefrompng($imgfile); break;
    }
    return $im;
}

}