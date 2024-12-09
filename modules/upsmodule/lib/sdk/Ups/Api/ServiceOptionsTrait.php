<?php

namespace Ups\Api;
/**
 * Trait for Shipment Service Options and Package Service Options
 * Created by UPS
 * Created at 26-08-2018
 */

trait ServiceOptionsTrait
{
    public $country;
    public $language;
    public $currency;
    public $orderValue;

    public $dialectLanguage = [
        'PL' => [
            'Language' => 'POL',
            'Dialect' => '97'
        ],
        'GB' => [
            'Language' => 'ENG',
            'Dialect' => 'GB'
        ]
    ];

    public $shipmentLvlSupportedCountries = ['PL', 'GB'];
    public $packageLvlSupportedCountries = ['GB', 'US'];

    // List Shipping Service Options DOESN'T EXISTS in Rate API.
    public $ignorList = ['012','6','372'];

    public $countryCodeEu = array(
        'BE', 'NL', 'FR', 'ES', 'PL', 'IT', 'DE', 'GB'
    );

    public function packageHandle($packages, $accessorials, $country = null)
    {
        $packageData = [];

        if (empty($packages))
        {
            return $packageData;
        }

        // Get Package Service Option.
        // Have to check empty 8:46 (26-08-2018 UPS)
        $accessorialOptions = $this->packageServiceOptionsHandle($accessorials, $country);

        foreach ($packages as $package)
        {
            $tmp["Dimensions"] = [
                "UnitOfMeasurement" => [
                    "Code" => strtoupper($package['lenghtUnit']),
                    "Description" => strtoupper($package['lenghtUnit']),
                ],
                "Length" => $package['lenght'],
                "Width" => $package['width'],
                "Height" => $package['height']
            ];
            $tmp["PackageWeight"] = [
                "UnitOfMeasurement" => [
                    "Code" => strtoupper($package['weightUnit']),
                    "Description" => strtoupper($package['weightUnit']),
                ],
                "Weight" => $package['weight']
            ];

            if ($package['weightUnit'] === 'KGS'
                && $package['weight'] >= '10')
            {
                if ($package['weight'] >= '25')
                {
                    $tmp["Packaging"] = ["Code" => "02"]; // 24
                    $tmp["PackagingType"] = ["Code" => "02"]; // 24
                } else {
                    $tmp["Packaging"] = ["Code" => "02"]; // 25
                    $tmp["PackagingType"] = ["Code" => "02"]; // 25
                }
            } else {
                $tmp["Packaging"] = ["Code" => "02"];
                $tmp["PackagingType"] = ["Code" => "02"];
            }

            $packageData[] = array_merge($tmp, $accessorialOptions);
        }

        return $packageData;
    }

    /**
     * ACCESSORIAL SERVICE - Shipment Service Options
     *
     * 1. To AP COD
     * 2. Saturday Delivery
     * 3. Carbon Neutural
     * 4. Direct Delivery
     *
     * Updated at 26-08-2018 by UPS
     */
    public function shipmentServiceOptionsHandle(
        $accessorials,
        $order,
        $country = null,
        $api = '',
        $countryCodeEshoper = ''
    ) {
        $services = [];

        if (empty($accessorials))
        {
            return $services;
        }

        /**
         * Required when use Ship To Access Point
         * Add Notification for Alternate Delivery Location
         */
        if (!empty($order))
        {
            $this->country = $order['country_code'];

            if ($order['ap_city'] !== '')
            {
                $accessorials[] = '012';
            }
        }
        else
        {
            $this->country = $countryCodeEshoper;
        }

        $locale = $this->mappingDialectLanguage($this->country);

        foreach ($accessorials as $code)
        {
            if ($api === 'Rate')
            {
                if (array_search($code, $this->ignorList) !== false)
                {
                    continue;
                }
            }

            switch ($code)
            {
                /**
                 * Shipment notification
                 *
                 * 012 - Alternate Delivery Location Notification
                 *
                 * 1 .Notification Language - Page 67
                 * 2. Language / Dialect Combinations - Page 431
                 *
                 */
                case '012':
                    $services['Notification'][] = [
                        'NotificationCode' => '012',
                        'EMail' => [
                            'EMailAddress' => $order['email']
                        ],
                        'Locale' => [
                            'Language' => $locale['Language'],
                            'Dialect' => $locale['Dialect']
                        ]
                    ];
                break;


                /**
                 * Shipment Service Option
                 *
                 * Access Point COD Indicates. Package COD is requested for a shipment.
                 * Valid only for 01 - Hold For Pickup at UPS AP
                 *
                 * Page 65?
                 */
                case '4':
                    $services = array_merge($services, $this->codServiceOption('AP', $country));
                break;


                case '500':
                    $services = array_merge($services, $this->codServiceOption('Home', $country));
                break;


                /**
                 * Quantum view Ship Notification
                 * Notification Codes: 6
                 * Page 66
                 */
                case '6':
                    $services['Notification'][] = [
                        'NotificationCode' => '6',
                        'EMail' => [
                            'EMailAddress' => $order['email']
                        ],
                        'Locale' => [
                            'Language' => $locale['Language'],
                            'Dialect' => $locale['Dialect']
                        ]
                    ];
                break;


                /**
                 * Quantum view notify delivery (Shipment Service Option)
                 *
                 * Was contained in Quantum View Notification (QVN)
                 * Notification Codes:
                 * 5 - QV In-transit Notification
                 * 6 - QV Ship Notification
                 * 7 - QV Exception Notification
                 * 8 - QV Delivery Notification
                 * 2 - Return Notification or Label Creation Notification
                 * 012 - Alternate Delivery Location Notification
                 * 013 - UAP Shipper Notification
                 *
                 * Page 66
                 */
                case '372':
                    $services['Notification'][] = [
                        'NotificationCode' => '8',
                        'EMail' => [
                            'EMailAddress' => $order['email']
                        ],
                        'Locale' => [
                            'Language' => $locale['Language'],
                            'Dialect' => $locale['Dialect']
                        ]
                    ];
                break;


                /**
                 * Residential Address Indicator
                 *
                 * This field is a flag to indicate
                 * if the receiver is a residential location.
                 *
                 * Page 44
                 */
                case '270':
                    // ['ShipTo']['Address']
                    $this->addFlag('ResidentialAddressIndicator');

                break;


                /**
                 * Saturday Delivery indicator.
                 * This is an empty tag, any value inside is ignored.
                 * Page 64.
                 */
                case '300':
                    $indicator = '';
                    if (!empty($country) && in_array(strtolower($country->ShipToCountryCode), array('us'))) {
                        $indicator = '1';
                    }
                    $services['SaturdayDeliveryIndicator'] = $indicator;
                break;


                /**
                 * Carbon Neutral
                 * Page 96
                 */
                case '441':
                    $services['UPScarbonneutralIndicator'] = '';
                break;


                /**
                 * Direct Delivery Only
                 * Any value inside is ignored.
                 *
                 * This accessorial is not valid with Shipment Indicatoion Type:
                 * 01 - Hold For Pickup at UPS AP and
                 * 02 - UPS AP Delivery
                 * Page 66
                 */
                case '541':
                    $services['DirectDeliveryOnlyIndicator'] = '';
                break;


                 /**
                 * Delivery Confirmation Signature Required.
                 * Page 96.
                 */
                case '2':
                    if (
                        !empty($country) &&
                        ($country->ShipFromCountryCode != 'US' || $country->ShipToCountryCode != 'US')
                    ) {
                        $services['DeliveryConfirmation']['DCISType'] = '1';
                    }
                break;


                /**
                 * Delivery Confirmation Adult Signature Required.
                 * Page 96.
                 */
                case '3':
                    if (
                        !empty($country) &&
                        ($country->ShipFromCountryCode != 'US' || $country->ShipToCountryCode != 'US')
                    ) {
                        $services['DeliveryConfirmation']['DCISType'] = '2';
                    }
                break;

                default:

                break;
            }
        }

        return $services;
    }

    /**
     * ACCESSORIAL SERVICE - Package Service Options
     *
     * 1. Delivery Confirmation Signature Required.
     * 2. Delivery Confirmation Adult Signature Required.
     * 3. To AP COD.
     * 4. Declared Value.
     * 5. Additional Handling Indicator.
     *
     * Updated at 26-08-2018 by UPS
     */
    public function packageServiceOptionsHandle($accessorials, $country)
    {
        $accessorialOptions = array();

        foreach ($accessorials as $code)
        {
            switch ($code)
            {
                /**
                 * Declared Value
                 * Page 105
                 */
                case '5':
                    $accessorialOptions['PackageServiceOptions']['DeclaredValue'] = [
                        'CurrencyCode' => $this->currency,
                        'MonetaryValue' => $this->orderValue
                    ];

                break;

                /**
                 * Additional Handling Indicator
                 * Page 104
                 */
                case '100':
                    $accessorialOptions['AdditionalHandlingIndicator'] = 'true';
                break;

                case '2':
                    if (
                        !empty($country) &&
                        ($country->ShipToCountryCode == 'US' && $country->ShipFromCountryCode == 'US')
                    ) {
                        $accessorialOptions['PackageServiceOptions']['DeliveryConfirmation']['DCISType'] = '2';
                    }
                break;

                case '3':
                    if (
                        !empty($country) &&
                        ($country->ShipToCountryCode == 'US' && $country->ShipFromCountryCode == 'US')
                    ) {
                        $accessorialOptions['PackageServiceOptions']['DeliveryConfirmation']['DCISType'] = '3';
                    }
                break;
                case '500':
                    if (!empty($country) &&
                        in_array(strtolower($country->ShipToCountryCode), array('us', 'ca', 'mx', 'pr'))) {
                        $accessorialOptions['PackageServiceOptions']['COD'] = [
                            'CODFundsCode' => "0",
                                'CODAmount' => [
                                    'CurrencyCode' => $this->currency,
                                    'MonetaryValue' => $this->orderValue
                                ]
                        ];
                    }
                break;

                default:

                break;

            }
        }

        return $accessorialOptions;
    }

    public function addFlag($flag)
    {
        $this->flag[] = $flag;
    }

    public function findKey($array, $keySearch)
    {
        foreach ($array as $key => $item)
        {
            if ($key == $keySearch)
            {
                return true;
            }
            elseif (is_array($item) && $this->findKey($item, $keySearch))
            {
                return true;
            }
        }
        return false;
    }

    public function mappingDialectLanguage($country)
    {
        return array_key_exists(
            $country, $this->dialectLanguage
        ) ? $this->dialectLanguage[$country] : ['Language' => 'ENG', 'Dialect' => 'GB'];
    }

    /**
     * Shipment & Package Service Option
     * Access Point COD Indicates. Package COD is requested for a shipment.
     * Valid only for 01 - Hold For Pickup at UPS AP
     * Page 106 65?
     */
    public function codServiceOption($to, $country = null)
    {
        $serviceOptions = [];

        switch ($to)
        {
            case 'AP':
                $serviceOptions = [
                    'AccessPointCOD' => [
                        'CurrencyCode' => $this->currency,
                        'MonetaryValue' => $this->orderValue
                    ]
                ];
            break;

            case 'Home':
                if (!empty($country) &&
                    !in_array(strtolower($country->ShipToCountryCode), array('us', 'ca', 'mx', 'pr'))) {
                    $serviceOptions = [
                        'COD' => [
                            'CODFundsCode' => "1",
                            'CODAmount' => [
                                'CurrencyCode' => $this->currency,
                                'MonetaryValue' => $this->orderValue
                            ]
                        ]
                    ];
                }
            break;
        }

        return $serviceOptions;
    }

    public function checkSupportedCountries($level)
    {
        $code = false;

        if ($level == static::SHIPMENT_LEVEL)
        {
            if (array_search($this->country, $this->shipmentLvlSupportedCountries) !== false)
            {
                $code = '1';
            }
        }
        elseif ($level == static::PACKAGE_LEVEL)
        {
            // No EU countries currently support Package level COD
            if (array_search($this->country, $this->packageLvlSupportedCountries) !== false)
            {
                $code = '0';
            }
        }

        return $code;
    }

    /**
     * Hold For Pickup at UPS AP.
     * Becareful with Shipping Service Option below. (Direct Delivery Only)
     *
     * Updated at 26-08-2018 / 24-09-2018 by UPS
     */
    public function checkShipmentType($order)
    {
        $shipmentTypes = [];
        if (!empty($order) && isset($order['ap_city']) && $order['ap_city'] !== '')
        {
            $apInfo = [
                'ApName' => $order['ap_name'],
                'firstname' => $order['firstname'],
                'lastname' => $order['lastname'],
                'ApAddressLine' => $order['ap_address1'],
                'ApCity' => $order['ap_city'],
                'ApStateProvinceCode' => $order['ap_state'],  //province_code -> sai
                'ApPostalCode' => $order['ap_postcode'],
                'ApCountryCode' => $order['country_code'],
            ];

            $shipmentTypes = $this->checkDeliveryAddressAp($apInfo);
        }

        return $shipmentTypes;
    }

    public function checkDeliveryAddressAp($apAddress)
    {
        //JIRA UAT 927
        $listCountry = ['US', 'CA', 'IE'];
        $stateProvinceCode = $apAddress['ApStateProvinceCode'];
        if (!in_array($apAddress['ApCountryCode'], $listCountry)) {
            $stateProvinceCode = 'XX';
        }
        //end jira

        $shipmentTypes = [
            'ShipmentIndicationType'   => ["Code" => "01"],
            'AlternateDeliveryAddress' => [
                'Name'    => $apAddress['ApName'],
                'Address' => [
                    'AddressLine'       => substr(mb_convert_encoding($apAddress['ApAddressLine'], 'UTF-8', 'HTML-ENTITIES'),0,35),
                    'City'              => $apAddress['ApCity'],
                    'StateProvinceCode' => $stateProvinceCode,
                    'PostalCode'        => preg_replace('/[^a-zA-Z0-9]/s','',$apAddress['ApPostalCode']),
                    'CountryCode'       => $apAddress['ApCountryCode'],
                ]
            ]
        ];

        if (isset($apAddress['firstname']) && isset($apAddress['lastname'])) {
            $shipmentTypes['AlternateDeliveryAddress']['AttentionName'] =
                $apAddress['firstname'] . ' ' . $apAddress['lastname'];
        }

        return $shipmentTypes;
    }

    public function checkShippingService($requestOption, $shippingServiceCode, $shippingServiceDesc)
    {
        $shippingService = array();

        if (
            $requestOption !== 'SHOP' &&
            strtolower($requestOption) !== 'shoptimeintransit' &&
            !empty($shippingServiceCode)
        ) {
            $shippingService = array(
                'Code' => $shippingServiceCode,
                'Description' => $shippingServiceDesc,
            );
        }

        return $shippingService;
    }

    public function checkShipmentTotalWeight($args)
    {
        $shippmentWeight = array();

        if (
            ($args['RequestOption'] == 'RATETIMEINTRANSIT' || strtolower($args['RequestOption']) == 'shoptimeintransit')
            && isset($args['PackageWeightCode'])
            && isset($args['PackageWeightDescription'])
            && isset($args['PackageWeightWeight'])
        ) {
            $shippmentWeight = array(
                "UnitOfMeasurement" => [
                    "Code"=> $args['PackageWeightCode'],
                    "Description"=> $args['PackageWeightDescription'],
                ],
                "Weight"=> $args['PackageWeightWeight'],
            );
        }

        return $shippmentWeight;
    }

    public function checkInvoiceLineTotal($args)
    {
        $invoiceLineTotal = array();

        if (isset($args['CurrencyCode']) && isset($args['MonetaryValue']))
        {
            $invoiceLineTotal = array(
                'CurrencyCode' => $args['CurrencyCode'],
                'MonetaryValue' => $args['MonetaryValue'],
            );
        }

        return $invoiceLineTotal;
    }

    public function checkTimeInformation($args)
    {
        $deliveryTimeInfor = array();

        if (
            ($args['RequestOption'] == 'RATETIMEINTRANSIT' || strtolower($args['RequestOption']) == 'shoptimeintransit')
            && isset($args['PickupDate'])
        ) {
            $deliveryTimeInfor = array(
                'PackageBillType' => '03',
                'Pickup' => array(
                    'Date' => $args['PickupDate']
                )
            );
        }

        return $deliveryTimeInfor;
    }

    public function getPaymentInformation($accountNumber, $shiptoCountry, $checkDutiesTax = false)
    {
        $paymentInformation = [
            'ShipmentCharge' => [
                [
                    'Type' => '01',
                    'BillShipper' => ['AccountNumber' => $accountNumber]
                ]
            ]
        ];

        /*if ($this->hasDutiesTax($shiptoCountry) || $checkDutiesTax)
        {
            $paymentInformation['ShipmentCharge'] = array_merge(
                $paymentInformation['ShipmentCharge'],
                [
                    [
                        'Type' => '02',
                        'BillShipper' => ['AccountNumber' => $accountNumber]
                    ]
                ]
            );
        }*/

        return $paymentInformation;
    }

    public function hasDutiesTax($shiptoCountry)
    {
        return (!in_array($shiptoCountry, static::COUNTRY_EU)) ? true : false;
    }

    public function checkDutiesTax($apCountryCode, $isApCod)
    {
        $isEuCountry = !in_array($apCountryCode, $this->countryCodeEu);

        return ($isApCod && $isEuCountry);
    }
}
