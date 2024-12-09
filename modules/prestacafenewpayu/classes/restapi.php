<?php
/**
 * 2009-2017 Blue Train Lukasz Kowalczyk / PrestaCafe
 *
 * NOTICE OF LICENSE
 *
 * Lukasz Kowalczyk is the owner of the copyright of this module.
 * All rights of any kind, which are not expressly granted in this
 * License, are entirely and exclusively reserved to and by Lukasz
 * Kowalczyk.
 *
 * You may not rent, lease, transfer, modify or create derivative
 * works based on this module.
 *
 * @author    Lukasz Kowalczyk <lkowalczyk@prestacafe.pl>
 * @copyright Lukasz Kowalczyk
 * @license   LICENSE.txt
 */

/**
 * Class PrestaCafePayuApi
 */
class PrestaCafePayuApi
{
    private $timeout = 10;
    private $connect_timeout = 10;
    private $client_id;
    private $client_secret;

    public function __construct($client_id, $client_secret)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }

    /**
     * @param array $order_struct
     * @return object
     */
    public function createOrder($order_struct)
    {
        $body = is_string($order_struct) ? $order_struct : Tools::jsonEncode($order_struct);
        $response = $this->performAuthenticatedHttp(
            'POST',
            "https://secure.payu.com/api/v2_1/orders",
            $body
        );
        PrestaCafeNewPayu::addLog(
            "POST https://secure.payu.com/api/v2_1/orders (client_id $this->client_id)"
            ."\nbody=".PrestaCafeNewPayu::logAddTrigraphs($body)
            ."\nresponse=".PrestaCafeNewPayu::logAddTrigraphs($response['content']),
            1
        );
        $result = Tools::jsonDecode($response['content']);
        if (!$result) {
            PrestaCafeNewPayu::addLog(
                "POST https://secure.payu.com/api/v2_1/orders (client_id $this->client_id)"
                ."\nRESPONSE CANNOT BE DECODED:"
                ."\nbody=".PrestaCafeNewPayu::logAddTrigraphs($body)
                ."\n => response=".PrestaCafeNewPayu::logAddTrigraphs($response['content']),
                2
            );
            throw new RuntimeException("PrestaCafePayuApi::createOrder: JSON response cannot be decoded");
        }
        if (!property_exists($result, 'status')
            || !property_exists($result->status, 'statusCode')
            || ($result->status->statusCode != 'SUCCESS'
                && Tools::substr(
                    $result->status->statusCode,
                    0,
                    Tools::strlen('WARNING_CONTINUE')
                ) != 'WARNING_CONTINUE')) {
            PrestaCafeNewPayu::addLog(
                "POST https://secure.payu.com/api/v2_1/orders (client_id $this->client_id)"
                ."\nINVALID RESPONSE STATUS:"
                ."\nbody=".PrestaCafeNewPayu::logAddTrigraphs($body)
                ."\n => response=".PrestaCafeNewPayu::logAddTrigraphs($response['content']),
                2
            );
            throw new RuntimeException(
                "PrestaCafePayuApi::createOrder: invalid status code in response: " . $result->status->statusCode
            );
        }
        return $result;
    }

    public function deleteOrder($order_id)
    {
        $response = $this->performAuthenticatedHttp('DELETE', "https://secure.payu.com/api/v2_1/orders/$order_id");
        $result = Tools::jsonDecode($response['content']);
        if (!$result) {
            throw new RuntimeException(
                "PrestaCafePayuApi::deleteOrder: JSON response cannot be decoded: " . $response['content']
            );
        }
        if (!property_exists($result, 'status')
            || !property_exists($result->status, 'statusCode')
            || $result->status->statusCode != 'SUCCESS') {
            throw new RuntimeException(
                "PrestaCafePayuApi::deleteOrder: invalid status code in response: " . $result->status->statusCode
            );
        }
        return true;
    }

    public function getOrder($order_id)
    {
        $response = $this->performAuthenticatedHttp('GET', "https://secure.payu.com/api/v2_1/orders/$order_id");
        $result = Tools::jsonDecode($response['content']);
        if (!$result) {
            throw new RuntimeException(
                "PrestaCafePayuApi::getOrder: JSON response cannot be decoded: " . $response['content']
            );
        }
        if (!property_exists($result, 'status')
            || !property_exists($result->status, 'statusCode')
            || $result->status->statusCode != 'SUCCESS') {
            throw new RuntimeException(
                "PrestaCafePayuApi::getOrder: invalid status code in response: " . $result->status->statusCode
            );
        }
        return $result;
    }

    /**
     * Returns all the payment methods as returned by the REST API. Only the value of payByLinks
     * is returned
     * @return array of objects
     */
    public function getPayMethods()
    {
        $response = $this->performAuthenticatedHttp('GET', 'https://secure.payu.com/api/v2_1/paymethods');
        $result = Tools::jsonDecode($response['content']);
        if (!is_object($result)
            || !property_exists($result, 'status')
            || !property_exists($result->status, 'statusCode')
            || !property_exists($result, 'payByLinks')
            || $result->status->statusCode != 'SUCCESS') {
                throw new RuntimeException("Cannot get pay methods");
        }
        return $result->payByLinks;
    }

    public function getPayMethodsAssoc()
    {
        $payMethods = $this->getPayMethods();
        $assoc = array();
        foreach ($payMethods as $method) {
            $assoc[$method->value] = $method;
        }
        return $assoc;
    }

    public function isPaymentAvailable($payment)
    {
        $payByLinks = $this->getPayMethods();
        foreach ($payByLinks as $pbl) {
            if ($pbl->value == $payment && $pbl->status == 'ENABLED') {
                return true;
            }
        }
        return false;
    }

    /**
     * @param bool $force_refresh get a new token from the REST API regardless if one is cached
     * @return array (access_token | null, http_code, errno, error)
     */
    private function getAccessToken($force_refresh = false)
    {
        $access_token = $this->getCachedAccessToken();
        if ($force_refresh || !$access_token) {
            $post_data = "grant_type=client_credentials&client_id={$this->client_id}"
                ."&client_secret={$this->client_secret}";
            $response = self::performRawHttp(
                'POST',
                'https://secure.payu.com/pl/standard/user/oauth/authorize',
                $post_data,
                array(),
                $this->timeout,
                $this->connect_timeout
            );
            if ($response['http_code'] != 200 || !$response['content']) {
                throw new RuntimeException("OAuth authorization failure");
            }
            $access_obj = Tools::jsonDecode($response['content']);
            if (!is_object($access_obj)
                || !property_exists($access_obj, 'access_token')
                || !property_exists($access_obj, 'expires_in')) {
                    throw new RuntimeException("Invalid OAuth token");
            }
            $this->cacheAccessToken($access_obj->access_token, time() + $access_obj->expires_in);
            $access_token = $access_obj->access_token;
        }
        return $access_token;
    }

    private function getCachedAccessToken()
    {
        $value = PrestaCafeNewPayu::getData("oauth_client_credentials_{$this->client_id}");
        if ($value) {
            list ($expiration, $access_token) = explode(';', $value);
            if ($expiration < time() - 60) {
                return null;
            }
            return $access_token;
        }
        return null;
    }

    private function cacheAccessToken($access_token, $expiration_time)
    {
        $value = "$expiration_time;$access_token";
        PrestaCafeNewPayu::setData("oauth_client_credentials_{$this->client_id}", $value);
    }

    private function performAuthenticatedHttp($method, $url, $body = null)
    {
        $access_token = $this->getAccessToken();
        $headers = array(
            'Content-Type: application/json',
            'Cache-Control: no-cache',
            "Authorization: Bearer $access_token"
        );
        $result = self::performRawHttp($method, $url, $body, $headers, $this->timeout, $this->connect_timeout);

        // if authentication failed, the same request is retried with a newly obtained access token
        if ($result['http_code'] == 401
            || (is_object($obj = Tools::jsonDecode($result['content']))
                && property_exists($obj, 'error')
                && $obj->error == 'invalid_token')) {
            $access_token = $this->getAccessToken(true);
            $headers = array(
                'Content-Type: application/json',
                'Cache-Control: no-cache',
                "Authorization: Bearer $access_token"
            );
            $result = self::performRawHttp($method, $url, $body, $headers, $this->timeout, $this->connect_timeout);
        }
        if ($result['http_code'] < 200 || $result['http_code'] > 399) {
            throw new RuntimeException("POST $url failed with http_code=".$result['http_code']);
        }
        return $result;
    }

    /**
     * @param string $method 'GET', 'POST', ...
     * @param string $url
     * @param string $body request body, if any
     * @param array $headers Headers suitable for CURLOPT_HTTPHEADER
     * @return array ('content' => ..., 'http_code' => ...)
     */
    public static function performRawHttp(
        $method,
        $url,
        $body = null,
        $headers = array(),
        $timeout = 10,
        $connect_timeout = 10
    ) {
        if (!function_exists('curl_init')) {
            self::logHttp(3, $method, $url, $body, $headers, null, null, null, -1, 'function curl_init missing');
            throw new RuntimeException("PrestaCafePayuApi::http_perform: curl_init not available");
        }
        $ch = curl_init($url);
        if (!$ch) {
            self::logHttp(3, $method, $url, $body, $headers, null, null, null, -1, 'function curl_init failed');
            throw new RuntimeException("PrestaCafePayuApi::http_perform: curl_init failed for url $url");
        }

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        } elseif ($method == 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } elseif ($method == 'HEAD') {
            curl_setopt($ch, CURLOPT_NOBODY, true);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        if ($body) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/curlheadercollector.php';
        $collector = new PayuCurlHeaderCollector;
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($collector, 'curloptHeaderFunction'));
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno) {
            self::logHttp(3, $method, $url, $body, $headers, null, null, null, $errno, $error);
            throw new RuntimeException("PrestaCafePayuApi::http_perform: errno=$errno ($error)");
        }

        if (PrestaCafeNewPayu::getData('log_http_all')) {
            if ($http_code < 200 || $http_code > 399) {
                self::logHttp(2, $method, $url, $body, $headers, $result, $collector->getHeaders(), $http_code);
            } else {
                self::logHttp(1, $method, $url, $body, $headers, $result, $collector->getHeaders(), $http_code);
            }
        }

        return array('content' => $result, 'http_code' => $http_code);
    }

    private static function logHttp(
        $severity,
        $method,
        $url,
        $request_body,
        $request_headers,
        $response_body,
        $response_headers,
        $http_status,
        $errno = null,
        $error = null
    ) {
        $message = "$method $url";
        if ($http_status) {
            $message .= "; HTTP $http_status";
        }
        if ($errno) {
            $message .= "; errno=$errno ($error)";
        }
        $data = array(
            'method' => $method,
            'url' => $url,
            'request_body' => $request_body,
            'request_headers' => $request_headers,
            'response_body' => $response_body,
            'response_headers' => $response_headers,
            'http_status' => $http_status,
            'errno' => $errno,
            'error' => $error
        );
        PrestaCafeNewPayu::addDebug($message, $severity, 'httpdebug', $data);
    }
}
