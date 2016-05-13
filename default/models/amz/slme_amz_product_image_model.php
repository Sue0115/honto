<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 亚马逊产品图片管理模型
 */
class Slme_amz_product_image_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }

    /**
     * 定义产品图片的上传状态
     * @return array
     */
    public function defineUploadedStatus(){
        return array(
            '0' => '未上传',
            '1' => '已作废',
            '2' => '已上传',
            '3' => '上传失败',
            //'4' => '上传成功'
        );
    }

    /**
     * 获取一个产品的图片
     * @param $id
     * @param $select :选择项
     * @return Ambigous
     */
    public function getOneProductImage($id, $select=''){
        $options = array();
        if ($select) {
            if (is_array($select)) {
                $select = implode(',', $select);
            }

            $options['select'] = $select;
        }

        if (!empty($id)){
            $options['where'] = array('id' => $id);
        }
        return $this->getOne($options);
    }

    /**
     * 获取一个站点需要上传的数据
     * @return mixed
     */
    public function getOnePlaceToUpload()
    {
        $where['callresult'] = 0;
        $options['where']    = $where;

        $imageList = $this->getAll2Array($options);
        $data      = array();
        if ($imageList) {
            foreach ($imageList as $row) {
                $data[$row['token_id']][] = $row;
            }
        }

        if ($data) {
            $keys     = array_keys($data);
            $firstKey = array_shift($keys);
            return array('token_id' => $firstKey, 'data' => $data[$firstKey]);
        } else {
            return array();
        }
    }

    public function createAmzSubmitXml($data, $place_id){
        $xml = '<?xml version="1.0" encoding="utf-8" ?>';
        $xml .= '<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">';
        $xml .= '<Header>';
        $xml .= '<DocumentVersion>1.01</DocumentVersion>';
        $xml .= '<MerchantIdentifier>'.$place_id.'</MerchantIdentifier>';
        $xml .= '</Header>';
        $xml .= '<MessageType>ProductImage</MessageType>';

        $messageXml = '';
        $imgUrlArray = array('Main', 'Swatch', 'PT1', 'PT2', 'PT3', 'PT4', 'PT5', 'PT6', 'PT7', 'PT8');
        $i = 1;
        foreach ($data as $row){
            foreach ($imgUrlArray as $urlField){ //看每个SKU多少图片来着
                if (!empty($row[$urlField])){
                    $messageXml .= '<Message>';
                    $messageXml .= '<MessageID>' . $i . '</MessageID>';
                    $messageXml .= '<OperationType>Update</OperationType>';
                    $messageXml .= '<ProductImage>';
                    $messageXml .= '<SKU>' . $row['sku'] . '</SKU>';
                    $messageXml .= '<ImageType>' . $urlField . '</ImageType>';
                    $messageXml .= '<ImageLocation>' . $row[$urlField] . '</ImageLocation>';
                    $messageXml .= '</ProductImage>';
                    $messageXml .= '</Message>';

                    $i++;
                }
            }
        }

        $xml .= $messageXml;
        $xml .= '</AmazonEnvelope>';

        return $xml;
    }

    /**
     * 需上传的图片上传
     */
    public function upload()
    {
        //获取需要上传的数据
        $dataList = $this->getOnePlaceToUpload();

        if (!empty($dataList) && $dataList['token_id']) { //有数据，且需要上传
            $token_id = $dataList['token_id']; //站点对应的账号ID

            $this->load->library('MyAmz');
            $amz = new MyAmz();

            $this->load->model('amz/Amz_config_model');
            $this->amzTokens = $this->Amz_config_model;

            $tokenArray = $this->amzTokens->getAllTokens(array('id' => $token_id));
            $tokenInfo  = array_shift($tokenArray); //账号信息--一维数组

            //组装XML数据
            $data     = $dataList['data']; //需要组装的数据
            $place_id = $tokenInfo['place_id']; //这个再确认下，都有点忘记了

            $content = $this->createAmzSubmitXml($data, $place_id);

            //创建文件头
            $feedHandle = @fopen('php://temp', 'rw+');
            fwrite($feedHandle, $content);
            rewind($feedHandle);

            //开始组装上传的资料
            $parameterArray = array(
                'AWSAccessKeyId'         => $tokenInfo['access_key'], //账号
                'AWSAccessKey'           => $tokenInfo['secret_key'], //密码
                'ServerUrl'              => $tokenInfo['place_site'], //链接地址
                'Merchant'               => $tokenInfo['merchant_id'],
                'Timestamp'              => $amz->getFormattedTimestamp(),
                'Version'                => '2009-01-01',
                'Action'                 => 'SubmitFeed',               //操作
                'FeedType'               => '_POST_PRODUCT_IMAGE_DATA_',//提交操作的类型
                'MarketplaceIdList.Id.1' => $place_id,                  //站点
                'PurgeAndReplace'        => 'false',
            );

            //xml数据信息，要组装到头信息中
            $header = array(
                'Content-MD5:' . base64_encode(md5(stream_get_contents($feedHandle), true))
            );

            rewind($feedHandle);

            $response = $amz->httpPost($parameterArray, $tokenInfo['place_name'], $header, $feedHandle);
            @fclose($feedHandle);

            if (!$response) { //没有返回信息
                exit('no-response');
            }

            //获取FeedSubmissionId节点的值，来判断是否上传成功
            $xml = str_replace('xmlns=', 'ns=', $response);
            $doc = simplexml_load_string($xml);

            //提交的产品id
            foreach ($data as $row) {
                $where_in[] = $row['id'];
            }
            $where['id'] = $where_in;


            $FeedSubmissionIds = $doc->xpath('//FeedSubmissionId');
            if (count($FeedSubmissionIds) > 0) { //说明这个节点至少是存在的
                foreach ($FeedSubmissionIds as $value) {
                    $FeedSubmissionId = $value;
                }


                $option['callresult'] = 2; //已上传
                $option['remark']     = $FeedSubmissionId; //返回的节点

                //批量更新下状态
                $this->update($option, array('where_in' => $where));
            }else {
                //不是链接超时就是上传失败的，失败的话，先把信息都保存进去吧
                $option['callresult'] = 3; //上传失败
                $option['remark']     = $response; //返回的节点

                //批量更新下状态
                $this->update($option, array('where_in' => $where));
            }
        }
    }
}

/* End of file Slme_amz_product_image_model.php */
/* Location: ./defaute/models/amz/Slme_amz_product_image_model.php */