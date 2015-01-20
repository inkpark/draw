<?php
class Curl
{
    public $cookieFile;
    public $timeout = 10;
    Public function __construct($dir){
        $this->cookieFile = $this->getTemporaryCookieFileName($dir);
    }

    public function request($url, $method = "get", $parms=array(), $needheader = false)
    {
        $ispost = false;
        $postfields = '';
        if (!empty($parms)) {
            foreach ($parms as $key => $val) {
                $postfields .= $key."=".$val."&";
            }
            $postfields = trim($postfields,'&');
            $url = $url.'?'.$postfields;
        }
        if ($method == 'post') {
            $ispost = true;
        }
        $curlOptions = array(
            CURLOPT_URL => $url, //访问URL
            CURLOPT_RETURNTRANSFER => true, //获取结果作为字符串返回
            CURLOPT_HTTPHEADER => array('X-FORWARDED-FOR:101.46.75.230', 'CLIENT-IP:127.0.0.1','Proxy-Client-IP:101.46.75.230','WL-Proxy-Client-IP:101.46.75.230' ),
            CURLOPT_HEADER => $needheader, //获取返回头信息
            //CURLOPT_SSL_VERIFYPEER => false, //支持SSL加密
            CURLOPT_POST => $ispost, //发送时带有POST参数
            CURLOPT_POSTFIELDS => $postfields, //请求的POST参数字符串
            CURLOPT_TIMEOUT => $this->timeout //等待响应的时间
        );
        return $this->getResponseText($curlOptions);
    }


    /**
     *
     * 设置CURL参数并发送请求，获取响应内容
     * @access private
     * @param $curlOptions array curl设置参数数组
     * @return string|false 访问成功，按字符串形式返回获取的信息；否则返回false
     */
    public function getResponseText($curlOptions) {
        /* 设置CURLOPT_RETURNTRANSFER为true */
        if(!isset($curlOptions[CURLOPT_RETURNTRANSFER]) || $curlOptions[CURLOPT_RETURNTRANSFER] == false) {
            $curlOptions[CURLOPT_RETURNTRANSFER] = true;
        }
        /* 初始化curl模块 */
        $curl = curl_init();
        /* 设置curl选项 */
        curl_setopt_array($curl, $curlOptions);
        /* 发送请求并获取响应信息 */
        $responseText = '';
        try {
            $responseText = curl_exec($curl);
            if(($errno = curl_errno($curl)) != CURLM_OK) {
                $errmsg = curl_error($curl);
                throw new Exception($errmsg, $errno);
            }
        } catch (Exception $e) {
            //exceptionDisposeFunction($e);
            //print_r($e);
            $responseText = false;
        }
        /* 关闭curl模块 */
        curl_close($curl);
        /* 返回结果 */
        return $responseText;
    }
    /**
     * 将Unicode字符串(u0000)转化为utf-8字符串，工具函数
     * @access private
     * @static
     * @param $string string Unicode字符串
     * @return string utf-8字符串
     */
    public function unicodeToUtf8($string) {
        $string = str_replace('u', '', strtolower($string));
        $length = strlen($string) / 4;
        $stringResult = '';
        for($i = 0; $i < $length; $i++) {
            $charUnicodeHex = substr($string, $i * 4, 4);
            $unicodeCode = hexdec($charUnicodeHex);
            $utf8Code = '';
            if($unicodeCode < 128) {
                $utf8Code = chr($unicodeCode);
            } else if($unicodeCode < 2048) {
                $utf8Code .= chr(192 + (($unicodeCode - ($unicodeCode % 64)) / 64));
                $utf8Code .= chr(128 + ($unicodeCode % 64));
            } else {
                $utf8Code .= chr(224 + (($unicodeCode - ($unicodeCode % 4096)) / 4096));
                $utf8Code .= chr(128 + ((($unicodeCode % 4096) - ($unicodeCode % 64)) / 64));
                $utf8Code .= chr(128 + ($unicodeCode % 64));
            }
            $stringResult .= $utf8Code;
        }
        return $stringResult;
    }
    private function getTemporaryCookieFileName($dir='.') {
        return (str_replace("", '/', tempnam($dir, 'tmp')));
    }
}
