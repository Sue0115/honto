<?php

/**
 * 亚马逊操作类
 * Class MyAmz
 */
class MyAmz
{

    public  $apiConfig;
    //private $apiLogPath = 'd:/data';
    private $apiLogPath = '/data/amzApiLog';

    //再定义下请求类型，根据Action判断是上传还是下载
    const POST_UPLOAD   = 'POST_UPLOAD';
    const POST_DOWNLOAD = 'POST_DOWNLOAD';
    const POST_DEFAULT  = 'POST_DEFAULT';
    const UNKNOWN       = 'UNKNOWN';

    function __construct()
    {
        $this->apiConfig = array('SignatureVersion' => 2, 'SignatureMethod' => 'HmacSHA256', 'Version' => '2013-09-01');
    }

    //计算
    function createSignatureString($parameterArray, $serverUrl)
    {
        $data = 'POST';
        $data .= "\n";
        $endpoint = parse_url($serverUrl);
        $data .= $endpoint['host'];
        $data .= "\n";
        $uri = array_key_exists('path', $endpoint) ? $endpoint['path'] : null;
        if (!isset ($uri)) {
            $uri = "/";
        }
        $uriencoded = implode("/", array_map(array($this, "_urlencode"), explode("/", $uri)));
        $data .= $uriencoded;
        $data .= "\n";
        uksort($parameterArray, 'strcmp');
        $tmp = $this->createEncodeString($parameterArray);
        $data .= $tmp;
        return $data;
    }

    function createEncodeString(array $parameterArray)
    {
        $queryParameters = array();
        foreach ($parameterArray as $key => $value) {
            $queryParameters[] = $key . '=' . $this->_urlencode($value);
        }
        return implode('&', $queryParameters);
    }

    function _urlencode($value)
    {
        return str_replace('%7E', '~', rawurlencode($value));
    }

    //计算出Signature字串
    function calculateSignature($signatureString, $key)
    {
        return base64_encode(hash_hmac('sha256', $signatureString, $key, true));
    }

    /* httpPost中的参数格式
    array(
     'AWSAccessKeyId' => $token['access_key'], //账号-必须
     'AWSAccessKey'   => $token['secret_key'], //密码-必须
     'ServerUrl'      => $token['place_site'], //链接地址-必须
     'Merchant'       => $token['merchant_id'], //卖家ID-必须
     'Timestamp'      => $this->amz->getFormattedTimestamp(), //时间戳-必须
     'Version'        => '2009-01-01', //版本，如同配置中一样，可以不写
     'Action'         => 'GetReport',  //操作，执行哪个操作-必须
     'ReportId'       => '23330955063',//报告ID
     );*/
    /**
     * CURL POST操作
     * @param $parametersArray
     * @param $placeName
     * @param $header :头信息数组 --submitfeed需要的
     * @param $streamHandle :文件流句柄
     * @return string
     */
    function httpPost($parametersArray, $placeName, $header = array(), $streamHandle = null)
    {
        $key = $parametersArray['AWSAccessKey'];
        unset($parametersArray['AWSAccessKey']); //去除密码
        $parametersArray = array_merge($this->apiConfig, $parametersArray);

        $headerArray = array('Content-Type: application/x-www-form-urlencoded; charset=utf-8', 'CustomInfo: Leego');

        $serverUrl = $parametersArray['ServerUrl'];
        unset($parametersArray['ServerUrl']);
        $stringForSignature           = $this->createSignatureString($parametersArray, $serverUrl);
        $signature                    = $this->calculateSignature($stringForSignature, $key);
        $parametersArray['Signature'] = $signature;

        $action      = $parametersArray['Action'];
        $requestType = self::getRequestType($action); //请求类型

        $postBody = $this->createEncodeString($parametersArray);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 150);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHPApplication/1.0 (Language=PHP)');
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        if ($requestType == self::POST_UPLOAD) {//上传
            if (is_null($streamHandle) || !is_resource($streamHandle)) { //上传的数据不存在
                $this->setCurlLog($placeName, $parametersArray['Action'], '上传的数据信息不存在', true);
                exit();
            }

            //组装上传地址
            if (!(substr($serverUrl, strlen($serverUrl) - 1) === '/')) {
                $serverUrl .= '/';
            }
            $serverUrl .= '?' . $postBody;
            $curlOptions[CURLOPT_URL] = $serverUrl;

            //上传的头信息
            $headers[] = 'Expect: ';
            $headers[] = 'Accept: ';
            $headers[] = 'Transfer-Encoding: chunked';
            $headers[] = 'Content-Type: text/xml'; //这句必须

            $curlOptions[CURLOPT_HTTPHEADER] = array_merge($headers, $header);

            rewind($streamHandle);
            $curlOptions[CURLOPT_VERBOSE] = true;
            $curlOptions[CURLOPT_INFILE]  = $streamHandle;

            $curlOptions[CURLOPT_UPLOAD] = true;

            $curlOptions[CURLOPT_CUSTOMREQUEST] = 'POST';

            curl_setopt_array($ch, $curlOptions);

        } elseif (!($requestType === self::UNKNOWN)) {

            curl_setopt($ch, CURLOPT_URL, $serverUrl);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        } else { //请求错误

        }

        $response = "";
        $response = curl_exec($ch);

        if (curl_error($ch)) { //错误信息保存下
            $this->setCurlLog($placeName, $parametersArray['Action'], curl_error($ch), true);
        }

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') { //成功的操作日志
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $body       = substr($response, $headerSize);
            if ($body) {
                $this->setCurlLog($placeName, $parametersArray['Action'], $body);
            }
        }
        curl_close($ch);

        return $body;
    }

    function getFormattedTimestamp($timestamp = '')
    {
        $timestamp = (trim($timestamp) != '') ? $timestamp : time();
        return gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", $timestamp);
    }

    /**
     * 获取请求的类型，看是上传还是下载
     * @param $action :方法
     * @return null|string
     */
    public static function getRequestType($action)
    {
        $requestType = null;
        switch ($action) {
            case 'SubmitFeed':
                $requestType = self::POST_UPLOAD;
                break;
            case 'GetFeedSubmissionResult':
            case 'GetReport':
                $requestType = self::POST_DOWNLOAD;
                break;
            case 'GetFeedSubmissionList':
            case 'GetFeedSubmissionListByNextToken':
            case 'GetFeedSubmissionCount':
            case 'CancelFeedSubmissions':
            case 'RequestReport':
            case 'GetReportRequestList':
            case 'GetReportRequestListByNextToken':
            case 'GetReportRequestCount':
            case 'CancelReportRequests':
            case 'GetReportList':
            case 'GetReportListByNextToken':
            case 'GetReportCount':
            case 'ManageReportSchedule':
            case 'GetReportScheduleList':
            case 'GetReportScheduleListByNextToken':
            case 'GetReportScheduleCount':
            case 'UpdateReportAcknowledgements':
                $requestType = self::POST_DEFAULT;
                break;
            default:
                $requestType = self::UNKNOWN;
                break;
        }

        return $requestType;
    }

    /**
     * 日志写入函数
     * @param $placeName
     * @param $callName
     * @param $error :是否错误日志
     * @param $str
     */
    public function setCurlLog($placeName, $callName, $str, $error = false)
    {
        $date    = date('Y-m-d');
        $time    = date('His');
        $logPath = $this->apiLogPath . "/$callName/$date";
        @mkdir($logPath, 0777, true);
        $logPathUrl = "$logPath/$placeName-$time" . ($error ? '-failure' : '') . ".log";
        file_put_contents($logPathUrl, $str . PHP_EOL, FILE_APPEND);
    }
}

