<?php
namespace Ups;
/**
 * Created by UPS
 * Created at 19/06/2018
 *
 * Updated at 28-08-2018
 */
use Sdk\I18N\I18N;
use Ups\Api\CommonHandle;
use Ups\Api\Shipping;
use Ups\Api\Rate;
use Ups\Api\TinT;

class Sdk
{
    private $params;

    public static $language = 'en';
    public static $currencyCode = 'USD';
    private static $compatilbeVersions = null;
    private static $releaseDate = array('d'=>'1', 'm'=>'January', 'y'=>'1970');
    private static $version = null;

    public $weightUnitPrototypes = [
        'KGS' => 'Kg',
        'LBS' => 'Pounds',
    ];
    public $lengthUnitPrototypes = [
        'CM' => 'Cm',
        'IN' => 'Inch',
    ];

    public $dbQuery = null;
    public $dbInstance = null;
    public static $username = null;
    public static $password = null;
    public static $licenseKey = null;
    public static $countryCode = null;
    public static $merchantKey = null;
    public static $registeredToken = null;

    public function __construct(array $argv = [])
    {
        if (isset($argv['currencyCode'])) {
            static::$currencyCode = $argv['currencyCode'];
        }

        if (isset($argv['dbQuery'])) {
            $this->dbQuery = $argv['dbQuery'];
        }

        if (isset($argv['dbInstance'])) {
            $this->dbInstance = $argv['dbInstance'];
        }
    }


    public function setUsername($username)
    {
        static::$username = $username;
    }

    public function getUsername()
    {
        return static::$username;
    }

    public function setPassword($password)
    {
        static::$password = $password;
    }

    public function getPassword()
    {
        return static::$password;
    }

    public function setCountryCode($countryCode)
    {
        static::$countryCode = $countryCode;
    }

    public function getCountryCode()
    {
        return static::$countryCode;
    }

    public function setMerchantKey($merchantKey)
    {
        static::$merchantKey = $merchantKey;
    }

    public function getMerchantKey()
    {
        return static::$merchantKey;
    }

    public function setRegisteredToken($registeredToken)
    {
        static::$registeredToken = $registeredToken;
    }

    public function getRegisteredToken()
    {
        return static::$registeredToken;
    }

    public function setLicense($license)
    {
        static::$licenseKey = $license;
    }

    public function getLicense()
    {
        return static::$licenseKey;
    }

    public function setLanguage($lang)
    {
        static::$language = $lang;
    }

    public function setVersion($version)
    {
        static::$version = $version;
    }

    public function getVersion()
    {
        return static::$version;
    }

    public function setCompatibleVersions($versions)
    {
        if (is_null(static::$compatilbeVersions)) {
            static::$compatilbeVersions = $versions;
        }
    }

    public function getCompatibleVersions()
    {
        return implode(", ", static::$compatilbeVersions);
    }

    public function setReleaseDate($date)
    {
        static::$releaseDate = $date;
    }

    public function getReleaseDate()
    {
        $day = static::$releaseDate['d'];
        $month = $this->t('ups', static::$releaseDate['m']);
        $year = static::$releaseDate['y'];

        return "$day $month, $year";
    }

    public function t($category, $message, $params = [], $language = null)
    {
        if ($language == null)
        {
            $language = static::$language;
        }

        $lang = new I18N();
        return $lang->translate($category, $message, $params, $language);
    }

    public function callRateTintAPI($rateParams, $tintParams)
    {
        $rateParams['ShipToAddress1'] = mb_convert_encoding(
            $rateParams['ShipToAddress1'],
            'UTF-8',
            'HTML-ENTITIES'
        );

        $responseFee = $this->estimateFee($rateParams);
        $responseTime = $this->estimateTime($tintParams);

        if ($responseFee['Description'] == 'Success')
        {
            $arrReturnValue['error'] = '';
            $arrReturnValue['ShippingFee'] = $responseFee['ShippingFee'];
            $arrReturnValue['CurrencyCode'] = $responseFee['CurrencyCode'];
            $arrReturnValue['shippingDateArrival'] = $responseTime;
        }
        else
        {
            $arrReturnValue['error'] = $responseFee['Description'];
            $arrReturnValue['shippingFee'] = ' ';
            $arrReturnValue['CurrencyCode'] = ' ';
            $arrReturnValue['shippingDateArrival'] = ' ';
        }

        return $arrReturnValue;
    }

    /**
     * The function use for Front page and Back office site
     *
     * 1. Front page: Don't use Accessorial Service for estimate.
     * (It may not supported or Merchant has configured improperly)
     *
     * 2. Back office: Merchant can change another Accessorials.
     *
     * Updated by UPS
     * Updated at 27-08-2018
     */
    public function estimateFee($rateInfo)
    {
        $rateAPI = new Rate();
        $response = $rateAPI($rateInfo);
        return $response;
    }

    public function estimateTime($tintInfo)
    {
        $tint = new TinT();
        $response = $tint($tintInfo);
        $services = isset($response['ShippingTimeEstimatedArrival']) ?
            $response['ShippingTimeEstimatedArrival'] : null;

        $returnTime = '';

        if (empty($response) || !isset($services) || $services === '') {
            return $returnTime;
        }

        foreach ($services as $service) {
            $shippingServiceCode = $service->Service->Code;

            if ($tintInfo['ShippingServiceCode'] == $shippingServiceCode)
            {
                $date = $service
                        ->EstimatedArrival
                        ->Arrival
                        ->Date;

                $time = $service
                        ->EstimatedArrival
                        ->Arrival
                        ->Time;

                $shippingTimeEstimatedArrivalString = $date . $time;

                if (!empty($shippingTimeEstimatedArrivalString))
                {
                    $time = strtotime($shippingTimeEstimatedArrivalString);
                    return date('Y-m-d H:i:s', $time);
                }
            }
        }

    }
}
