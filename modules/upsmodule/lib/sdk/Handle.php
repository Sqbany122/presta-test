<?php

namespace Sdk;

/**
 * Created by UPS
 * Created at 03/05/2019
 */

class Handle
{
    protected static $locales = array('en_AT','de_AT','en_BE','nl_BE','fr_BE','en_CZ','cs_CZ','en_DK','da_DK','en_FI','fi_FI','en_FR','fr_FR','en_DE','de_DE','en_GR','el_GR','en_HU','hu_HU','en_IE','en_IT','it_IT','en_LU','fr_LU','en_NL','nl_NL','en_NO','no_NO','en_PL','pl_PL','en_PT','pt_PT','en_RU','ru_RU','en_SI','en_ES','es_ES','en_SE','sv_SE','en_CH','fr_CH','de_CH','en_GB','en_US');
    protected $sdk = null;
    protected $path;
    protected $endpoint;
    protected $e = null;
    protected $response = null;

    public function __invoke($arrParam)
    {
        if ($this->sdk == null) {
            $this->sdk = $arrParam['sdk'];
        }
    }

    protected function resolveParam($args)
    {
        return json_encode($args);
    }

    protected function responseHandler($response)
    {
        return json_decode($response);
    }

    public function writeLogDb($type, $script, $function, $content, $callback = 'nothing')
    {
        if (!LOG_WRITER) {
            return;
        }

        $sql = "INSERT INTO `log_data` VALUES ('".
                pSQL($type)
                ."','".
                pSQL($script)
                ."','".
                pSQL($function)
                ."','".
                date("Y-m-d H:i:s e")
                ."','".
                pSQL(json_encode($content))
                ."','".
                pSQL(json_encode($_SERVER['REMOTE_ADDR']))
                ."');";

        $this->sdk->dbInstance->execute($sql);

        if ($function == 'responseHandler') {
            $this->response = $content;
        }
    }

    protected function saveLogAPI($url, $request, $response)
    {
        $params = [];
        $params['Platform'] = '10';
        $params['CountryCode'] = $this->sdk->getCountryCode();
        $params['MerchantUrl'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $params['MerchantKey'] = $this->sdk->getMerchantKey();
        $params['LogApiUrl'] = $url;
        $params['LogApiRequest'] = $request;
        $params['LogApiResponse'] = $response;

        $arg = array(
            'sdk' => $this->sdk,
            'preToken' => $this->sdk->getRegisteredToken(),
            'data' => $params
        );
        $callApi = new \PluginManager\CollectionApi\CreateLogger();
        $callApi($arg);
    }

    // Function to get the client IP address
    public function getClientIP()
    {
        $ipaddress = '';
        $ipaddress = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        if (filter_var($ipaddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            // $ipaddress is a valid IPv6 address
            $ipaddress = '127.0.0.1';
        }
        $lastIndex = strpos($ipaddress, ",");
        if ($lastIndex > -1) {
            $ipArray = explode(",", $ipaddress);
            $countIP = count($ipArray);
            if ($countIP > 0) {
                $ipaddress = trim($ipArray[0]);
            }
        }
        return $ipaddress;
    }


    /**
     * Convert all fields in $item to ASCII.
     *
     * Do this by first normalizing the characters (á -> a, ñ -> n, etc.). If any
     * non-ASCII characters remain, replace with a default value.
     *
     * @param array|object $item Array or object containing fields to convert
     * @param array|object $template Contains template fields
     * @param string $default Value to use when field not present in $template
     * @param array $ignore Optional list of fields to ignore.
     */
    public static function transliterator(&$item, $template=null, $default='', array $ignore=null)
    {
        $helper;
        $useTransliterator = false;

        if (class_exists('Transliterator')) {
            $useTransliterator = true;
            $helper = \Transliterator::create('Any-Latin; Latin-ASCII;');
        } else {
            $helper = new \Helper\ConvertToAscii();
        }

        foreach ($item as $field => &$value) {
            // Skip fields in the $ignore array.
            if ($ignore && in_array($field, $ignore)) {
                continue;
            }

            if (!is_object($value) && !is_array($value)) {
                if ($useTransliterator) {
                    $value = $helper->transliterate($value);
                } else {
                    // Normalize non-ASCII characters with ASCII counterparts.
                    $value = $helper->squashCharacters($value);
                }

                // Replace fields that contain non-ASCII characters with a default.
                if (mb_convert_encoding($value, 'ascii') !== $value) {
                    // If template is provided, use the template field, if set.
                    if ($template) {
                        if (is_object($template) && isset($template->{$field})) {
                            $value = $template->{$field};
                        } elseif (is_array($template) && isset($template[$field])) {
                            $value = $template[$field];
                        } else {
                            $value = $default;
                        }
                    } else {
                        $value = $default;
                    }
                }
            }
        }
    }

    public static function getLocale($language, $country)
    {
        $locale = $language . '_' . $country;
        return in_array($locale, static::$locales) ? $locale : 'en_GB';
    }


    protected function formatReturn($data, $responseParamName, $key = '')
    {
        if ($data) {
            $result = array('Code' => '', 'Description' => '');
            $res = $data->$responseParamName;

            if (isset($res) && !empty($res)
                && isset($res->Response) && !empty($res->Response)) {

                $status = $res->Response->ResponseStatus;

                if (isset($status) && !empty($status)) {

                    if (isset($status->Code) && !empty($status->Code)) {
                        $result['Code'] = $status->Code;
                    }

                    if (isset($status->Description) && !empty($status->Description)) {
                        $result['Description'] = $status->Description;
                    }
                }

                switch ($key) {
                    case 'Rate':
                        $arr = $this->rateProcess($res);
                        $result = array_merge($result, $arr);
                        break;

                    case 'Tracking':
                        $result = $res;
                        break;

                    case 'OpenAccount':
                        $result['AccountNumber'] = isset($res->ShipperNumber) ? $res->ShipperNumber : '';
                        break;

                    default:
                        # code...
                        break;
                }
            }

            return $result;
        }
    }

    private function rateProcess($res)
    {
        $shippingFee = '';
        $currencyCode = '';
        $shippingService = array();
        $timeInTransit = array();

        if (isset($res->RatedShipment) && !empty($res->RatedShipment)) {
            $rateInfo = $res->RatedShipment;

            if (isset($rateInfo->TotalCharges) && !empty($rateInfo->TotalCharges)) {
                $totalCharges = $rateInfo->TotalCharges;

                if (isset($totalCharges->MonetaryValue) && !empty($totalCharges->MonetaryValue)) {
                    $shippingFee = $totalCharges->MonetaryValue;
                }

                if (isset($totalCharges->CurrencyCode) && !empty($totalCharges->CurrencyCode)) {
                    $currencyCode = $totalCharges->CurrencyCode;
                }

                // UAT 697
                if (isset($rateInfo->NegotiatedRateCharges->TotalCharge->MonetaryValue)) {
                    $shippingFee = $rateInfo->NegotiatedRateCharges->TotalCharge->MonetaryValue;
                    $currencyCode = $rateInfo->NegotiatedRateCharges->TotalCharge->CurrencyCode;
                }
            }

            // validate List Shipping Service
            if (is_array($rateInfo)) {
                $shippingService = $rateInfo;
            } else {
                $shippingService[] = $rateInfo;
            }

            // validate TiminTransit
            if (isset($rateInfo->TimeInTransit)) {
                $timeInTransit = $rateInfo->TimeInTransit;
            }

            return array(
                'ShippingFee' => $shippingFee,
                'CurrencyCode' => $currencyCode,
                'ShippingService' => $shippingService,
                'TimeInTransit' => $timeInTransit,
            );
        }
    }

    protected function formatFault($data)
    {
        $detail = $data->Fault->detail->Errors->ErrorDetail;
        if (is_array($detail))
        {
            return [
                'Code' => $detail[0]
                        ->PrimaryErrorCode
                        ->Code,
                'Description' => $detail[0]
                        ->PrimaryErrorCode
                        ->Description
            ];
        }

        return [
            'Code' => $detail
                    ->PrimaryErrorCode
                    ->Code,
            'Description' => $detail
                    ->PrimaryErrorCode
                    ->Description
        ];
    }
}
