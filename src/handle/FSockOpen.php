<?php
/**
 * Created by PhpStorm.
 * User: yk
 * Date: 19-1-16
 * Time: 上午11:15
 */

namespace Yb\handle;


class FSockOpen
{
    protected $responseHeader;

    protected $responseBody;

    /**
     * get方式获取
     * @param string $url
     * @param string $contentType
     * @param string $charset
     * @return $this
     */
    public function get(
        string $url,
        string $contentType = 'application/x-www-form-urlencoded',
        string $charset = 'UTF-8')
    {
        $tempContent = $this->fSockOpen($url, $contentType, $charset);
        list($this->responseHeader, $this->responseBody) = explode("\r\n\r\n", $tempContent, 2);

        $this->responseBody = preg_replace_callback(
            '/(?:(?:\\r\\n|\\n)|^)([0-9A-F]+)(?:\\r\\n|\\n){1,2}(.*?)'. '((?:\\r\\n|\\n)(?:[0-9A-F]+(?:\\r\\n|\\n))|$)/si',
            function($matches){
                return hexdec($matches[1]) == strlen($matches[2]) ? $matches[2] : $matches[0];
            },
            $this->responseBody
        );

        return $this;
    }

    /**
     * post 方式获取
     * @param string $url
     * @param string $contentType
     * @param string $charset
     * @param array $postData
     * @return $this
     */
    public function post(
        string $url,
        string $contentType = 'application/x-www-form-urlencoded',
        string $charset = 'UTF-8',
        $postData = []
    )
    {
        $tempContent = $this->fSockOpen($url, $contentType, $charset, $postData);
        list($this->responseHeader, $this->responseBody) = explode("\r\n\r\n", $tempContent, 2);

        $this->responseBody = preg_replace_callback(
            '/(?:(?:\\r\\n|\\n)|^)([0-9A-F]+)(?:\\r\\n|\\n){1,2}(.*?)'. '((?:\\r\\n|\\n)(?:[0-9A-F]+(?:\\r\\n|\\n))|$)/si',
            function($matches){
                return hexdec($matches[1]) == strlen($matches[2]) ? $matches[2] : $matches[0];
            },
            $this->responseBody
        );

        return $this;
    }

    /**
     * 获取请求内容
     * @return mixed
     */
    public function getContent()
    {
        return $this->responseBody;
    }

    /**
     * 获取请求原始头部内容
     * @return mixed
     */
    public function getHeader()
    {
        return $this->responseHeader;
    }

    /**
     * 进行fsock的请求
     * @param $url
     * @param string $contentType
     * @param string $charset
     * @param array $postData
     * @param bool $isAsync 是否使用异步形式 异步形式则不处理结果
     * @return bool|string
     */
    private function fSockOpen(
        $url,
        $contentType = 'application/x-www-form-urlencoded',
        $charset = 'UTF-8',
        $postData = [],
        $isAsync = false
    )
    {
        $urlArr = parse_url($url);


        if($urlArr['scheme'] == 'https'){
            $urlArr['host'] = 'ssl://'.$urlArr['host'];
            $port = $urlArr['port'] ?? 443;
        } else {
            $port = $urlArr['port'] ?? 80;
        }
        $fp = fsockopen($urlArr['host'],$port,$errno,$errStr,30);
        if(!$fp) return false;

        $getPath = $urlArr['path'] ?? '/index.php';
        $getPath .= $urlArr['query'] ?? '';

        $header = "POST  $getPath  HTTP/1.1\r\n";
        $header .= "Host: ".$urlArr['host']."\r\n";

        if(!empty($postData)){  //传递post数据
//            $_post = array();
//            foreach($postData as $_k=>$_v){
//                $_post[] = $_k."=".urlencode($_v);
//            }
//            $_post = implode('&', $_post);
            $_post = http_build_query($postData);
            $postStr = "Content-Type:" . strtolower($contentType) . "; charset=" . strtoupper($charset) . "\r\n";
            $postStr .= "Content-Length: ".strlen($_post)."\r\n";  //数据长度
            $postStr .= "Connection:Close\r\n\r\n";
            $postStr .= $_post;  //传递post数据
            $header .= $postStr;
        }else{
            $header .= "Connection:Close\r\n\r\n";
        }
        fwrite($fp, $header);

        if(!$isAsync) {
            $res = '';
            while(!feof($fp)) {
                $res .= fgets($fp, 1024);
            }
            fclose($fp);

            return $res;
        }
        usleep(1000);
        fclose($fp);
        return true;
    }
}