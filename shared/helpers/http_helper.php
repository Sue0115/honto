<?php
/**
 * 抛出异常
 *
 * @return Exception
 */
function throw_exception($message)
{
	throw new Exception($message);
}

/**
 * CURL发送请求
 *
 * @param string $url
 * @param mixed $data
 * @param string $method
 * @param string $cookieFile
 * @param array $headers
 * @param int $connectTimeout
 * @param int $readTimeout
 */
function curlRequest($url,$data='',$method='POST',$cookieFile='',$headers='',$connectTimeout = 30,$readTimeout = 30)
{
    $method = strtoupper($method);
    if(!function_exists('curl_init')) return socketRequest($url, $data, $method, $cookieFile, $connectTimeout);

    $option = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER =>0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => $connectTimeout,
            CURLOPT_TIMEOUT => $readTimeout
    );

    if($headers)
    {
        $option[CURLOPT_HTTPHEADER] = $headers;
    }

    if($cookieFile)
    {
        $option[CURLOPT_COOKIEJAR] = $cookieFile;
        $option[CURLOPT_COOKIEFILE] = $cookieFile;
        //$option[CURLOPT_COOKIESESSION] = true;
        //$option[CURLOPT_COOKIE] = 'prov=42;city=1';
    }

    if($data && $method == 'POST')
    {
        $option[CURLOPT_POST] = 1;
        $option[CURLOPT_POSTFIELDS] = $data;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt_array($ch,$option);
    $response = curl_exec($ch);
    if(curl_errno($ch) > 0){
        throw_exception("CURL ERROR:$url ".curl_error($ch));
    }
    curl_close($ch);
    return $response;
}

/**
 * socket发送请求
 *
 * @param string $url
 * @param string $post_string
 * @param string $method
 * @param int $connectTimeout
 * @param int $readTimeout
 * @return string
 */
function socketRequest($url, $data, $method, $cookieFile, $connectTimeout) {
    $return = '';
    $matches = parse_url($url);
    !isset($matches['host']) && $matches['host'] = '';
    !isset($matches['path']) && $matches['path'] = '';
    !isset($matches['query']) && $matches['query'] = '';
    !isset($matches['port']) && $matches['port'] = '';
    $host = $matches['host'];
    $path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
    $port = !empty($matches['port']) ? $matches['port'] : 80;

    $conf_arr = array(
            'limit'=>0,
            'post'=>$data,
            'cookie'=>$cookieFile,
            'ip'=>'',
            'timeout'=>$connectTimeout,
            'block'=>TRUE,
    );

    foreach ($conf_arr as $k=>$v) ${$k} = $v;
    if($post) {
        if(is_array($post))
        {
            $postBodyString = '';
            foreach ($post as $k => $v) $postBodyString .= "$k=" . urlencode($v) . "&";
            $post = rtrim($postBodyString, '&');
        }
        $out = "POST $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        //$out .= "Referer: $boardurl\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= "User-Agent: ".$_SERVER['HTTP_USER_AGENT']."\r\n";
        $out .= "Host: $host\r\n";
        $out .= 'Content-Length: '.strlen($post)."\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cache-Control: no-cache\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
        $out .= $post;
    } else {
        $out = "GET $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        //$out .= "Referer: $boardurl\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "User-Agent: ".$_SERVER['HTTP_USER_AGENT']."\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
    }
    $fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
    if(!$fp) {
        return '';
    } else {
        stream_set_blocking($fp, $block);
        stream_set_timeout($fp, $timeout);
        @fwrite($fp, $out);
        $status = stream_get_meta_data($fp);
        if(!$status['timed_out']) {
            while (!feof($fp)) {
                if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
                    break;
                }
            }

            $stop = false;
            while(!feof($fp) && !$stop) {
                $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                $return .= $data;
                if($limit) {
                    $limit -= strlen($data);
                    $stop = $limit <= 0;
                }
            }
        }
        @fclose($fp);
        return $return;
    }
}
