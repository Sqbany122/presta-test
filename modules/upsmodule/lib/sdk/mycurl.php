<?php

namespace Sdk;

/**
 * Created by UPS
 * Created at 05/07/2018
 */

class mycurl {
    protected $_useragent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1';
    protected $_url;
    protected $_followlocation;
    protected $_timeout;
    protected $_maxRedirects;
    protected $_cookieFileLocation = './cookie.txt';
    protected $_post;
    protected $_postFields;
    protected $_referer = "https://www.google.com";
    protected $_session;
    protected $_webpage;
    protected $_includeHeader;
    protected $_noBody;
    protected $_status;
    protected $_binaryTransfer;
    protected $_err;
    public  $authentication = 0;
    public  $auth_name   = '';
    public  $auth_pass   = '';
    public function useAuth($use)
    {
        $this->authentication = 0;
        if ($use == true) $this->authentication = 1;
    }

    public function setName($name)
    {
        $this->auth_name = $name;
    }

    public function setPass($pass)
    {
        $this->auth_pass = $pass;
    }

    public function __construct($url,$followlocation = true,$timeOut = 30,$maxRedirecs = 4,$binaryTransfer = false,$includeHeader = false,$noBody = false)
    {
        $this->_url = $url;
        $this->_followlocation = $followlocation;
        $this->_timeout = $timeOut;
        $this->_maxRedirects = $maxRedirecs;
        $this->_noBody = $noBody;
        $this->_includeHeader = $includeHeader;
        $this->_binaryTransfer = $binaryTransfer;
        $this->_cookieFileLocation = dirname(__FILE__).'/cookie.txt';
    }

    public function setReferer($referer)
    {
        $this->_referer = $referer;
    }

    public function setCookiFileLocation($path)
    {
        $this->_cookieFileLocation = $path;
    }

    public function setPost ($postFields)
    {
        $this->_post = true;
        $this->_postFields = $postFields;
    }

    public function setUserAgent($userAgent)
    {
        $this->_useragent = $userAgent;
    }

    /**
     * call API for UPS
     */
    public function createCurl($content = array(), $url = 'nul')
    {
        if ($url != 'nul')
        {
            $this->_url = $url;
        }

        $header = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($this->_postFields),
        );

        if (!empty($content)) {
            $header = array_merge($content, $header);
        }

        ob_start();
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, $this->_url);
        curl_setopt($s, CURLOPT_HTTPHEADER, $header);
        curl_setopt($s, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($s, CURLOPT_MAXREDIRS, $this->_maxRedirects);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s, CURLOPT_FOLLOWLOCATION, $this->_followlocation);
        curl_setopt($s, CURLOPT_COOKIEJAR, $this->_cookieFileLocation);
        curl_setopt($s, CURLOPT_COOKIEFILE, $this->_cookieFileLocation);
        curl_setopt($s, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($s, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($s, CURLOPT_SSLVERSION, 6); //TLS 1.2

        if ($this->authentication == 1)
        {
            curl_setopt($s, CURLOPT_USERPWD, $this->auth_name.':'.$this->auth_pass);
        }

        if ($this->_post)
        {
            curl_setopt($s, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($s, CURLOPT_POST, true);
            curl_setopt($s, CURLOPT_POSTFIELDS, $this->_postFields);
        }

        if ($this->_includeHeader)
        {
            curl_setopt($s, CURLOPT_HEADER, true);
        }

        if ($this->_noBody)
        {
            curl_setopt($s, CURLOPT_NOBODY, true);
        }

        curl_setopt($s, CURLOPT_USERAGENT, $this->_useragent);
        curl_setopt($s, CURLOPT_REFERER, $this->_referer);
        $this->_webpage = curl_exec($s);
        ob_end_clean();
        // Fatal error: Maximum execution time of 30 seconds exceeded in lib\sdk\mycurl.php on line 123
        $this->_status = curl_getinfo($s, CURLINFO_HTTP_CODE);

        if ($this->_webpage === false)
        {
            $this->_err = [curl_error($s), curl_errno($s)];
        }
        curl_close($s);
        //Write Log
        if (LOG_WRITER) {
            $myfile = fopen(dirname(__FILE__) . "/Log_APIs.txt", "a") or die("Unable to open file!");
            $txt = "Time: " . date('d/m/Y H:i:s', time()) . "\n";
            $txt .= "URL: " . $this->_url . "\n";
            $txt .= "ToKen: " . json_encode($content) . "\n";
            $txt .= "Request: " . $this->_postFields . "\n";
            $txt .= "Response: " . $this->_webpage . "\n";
            fwrite($myfile, $txt);
            $txt = "-------------------------------------------------------------------------------------------------\n";
            
            fwrite($myfile, $txt);
            fclose($myfile);
        }
    }

    public function getHttpStatus()
    {
        return $this->_status;
    }

    public function __tostring()
    {
        return $this->_webpage;
    }

    public function getError()
    {
        return $this->_err;
    }

}
