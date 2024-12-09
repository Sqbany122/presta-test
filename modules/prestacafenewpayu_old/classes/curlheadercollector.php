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

class PayuCurlHeaderCollector
{
    private $headers = array();

    public function curloptHeaderFunction($ch, $header)
    {
        $consumed = Tools::strlen($header);
        $header = trim($header);
        if (strpos($header, "HTTP/") !== 0 && strpos($header, ": ")) {
            list($name, $value) = explode(": ", $header, 2);
            if (!empty($this->headers[$name])) {
                $this->headers[$name] = array($this->headers[$name]);
                $this->headers[$name][] = $value;
            } else {
                $this->headers[$name] = $value;
            }
        }
        return $consumed;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
