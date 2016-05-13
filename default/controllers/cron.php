<?php
class Cron extends CI_Controller{

    function __construct(){
        parent::__construct();
        $this->load->model('amz/Slme_amz_product_image_model');
    }

    /**
     * 自动上传亚马逊产品图片
     */
    public function uploadAmzProductImage(){
        $this->Slme_amz_product_image_model->upload();
    }
}