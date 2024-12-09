<?php

namespace Ups\Api;

/**
 * Created by UPS
 * Created at 20/07/2018
 *
 * Updated at 26-08-2018
 * Updated by UPS
 */

use Ups\Api\CommonHandle;

class Rate extends CommonHandle
{
    const PATH = 'Rate';
    private $flag = [];

    /**
     * Handle Accessorial Services
     * Updated at 26-08-2018
     * Updated by UPS
     */
    use ServiceOptionsTrait;

    public function __invoke($args = array())
    {
        $this->path = SELF::PATH;
        // Debug style $return
        $return = parent::__invoke($args);

        return $return;
    }

    protected function responseHandler($response)
    {
        $data = parent::responseHandler($response);

        if (is_null($data)) {
            $this->writeLogDb('errorShip', __FILE__, __FUNCTION__, $data);

            return [
                'Code' => 102,
                'Description' => 'There are errors connecting to the UPS API servers. Please try again.'
            ];
        } elseif (isset($data->Fault))
        {
            $this->writeLogDb('errorRate', __FILE__, __FUNCTION__, $data);

            return $this->formatFault($data);
        }

        $this->writeLogDb('responseRate', __FILE__, __FUNCTION__, $data);

        return $this->formatReturn($data, 'RateResponse', 'Rate');
    }

    protected function resolveParam($args)
    {
        $data = $this->rateData($args);
        // Debug style for return data
        $return = parent::resolveParam($data);

        $this->writeLogDb('getRateData', __FILE__, __FUNCTION__, json_decode($return));

        return $return;
    }

    private function rateData($args)
    {
        $isApCod = false;
        $args['RequestOption']              = isset($args['RequestOption']) ? $args['RequestOption'] : 'Rate';
        $args['ShippingServiceCode']        = isset($args['ShippingServiceCode']) ? $args['ShippingServiceCode'] : '';
        $args['ShippingServiceDescription'] = isset($args['ShippingServiceDescription']) ? $args['ShippingServiceDescription'] : '';

        $countryCodeData = new \stdClass();
        $countryCodeData->ShipFromCountryCode = $args['ShipFromCountryCode'];
        $countryCodeData->ShipToCountryCode = $args['ShipToCountryCode'];

        if ($args['ShipperCountryCode'] == 'PL')
        {
            $args['ShipperStateProvinceCode'] = 'XX';
        }

        if ($args['ShipToCountryCode'] == 'PL')
        {
            $args['ShipToStateProvinceCode'] = 'XX';
        }

        $args['ShipFromCountryCode'] == 'PL' ? ($args['ShipFromStateProvinceCode'] = 'XX') : '';

        /**
         * Handle Shipment Service Options
         * Updated at 26-08-2018
         * Updated by UPS
         */
        if (isset($args['CurrencyCode']))
        {
            $this->currency = $args['CurrencyCode'];
        }

        if (isset($args['order']))
        {
            $this->orderValue = (string) $args['MonetaryValue'];
            $isApCod = isset($args['isApCod']) ? $args['isApCod'] : false;
        }
        else
        {
            $args['order'] = '';
        }

        $args['packages'] = isset($args['packages']) ? $args['packages'] : '';
        $args['accessorialsService'] =
            empty($args['accessorialsService']) ?
                [] : $args['accessorialsService'];

        $shipmentServices =
            $this->shipmentServiceOptionsHandle(
                $args['accessorialsService'],
                $args['order'],
                $countryCodeData,
                'Rate',
                $args['ShipToCountryCode']
            );

        /**
         * Handle Shipment Service Options
         * Updated at 26-08-2018
         */
        $packageData =
            $this->packageHandle(
                $args['packages'],
                $args['accessorialsService'],
                $countryCodeData
            );

        $shipmentTypes = $this->checkShipmentType($args['order']);

        $shippingService =
            $this->checkShippingService(
                $args['RequestOption'],
                $args['ShippingServiceCode'],
                $args['ShippingServiceDescription']
            );

        $shipmentTotalWeight = $this->checkShipmentTotalWeight($args);
        $invoiceLineTotal = $this->checkInvoiceLineTotal($args);
        $deliveryTimeInfor = $this->checkTimeInformation($args);

        $apAddress = isset($args['AlternateDeliveryAddress'])
                        ? $this->checkDeliveryAddressAp($args['AlternateDeliveryAddress'])
                        : array();

        $apCountryCode = '';
        if (!empty($apAddress) && isset($apAddress['AlternateDeliveryAddress']['ApCountryCode'])) {
            $apCountryCode = $apAddress['AlternateDeliveryAddress']['ApCountryCode'];
        }

        $hasDutiesTax = $this->checkDutiesTax($apCountryCode, $isApCod);
        $paymentInformation = $this->getPaymentInformation($args['ShipperNumber'], $args['ShipToCountryCode'], $hasDutiesTax);

        $return = [
            "RateRequest" => [
                "Request" => [
                    "RequestOption" => $args['RequestOption'],
                    "TransactionReference" => [
                        "CustomerContext" => ""
                    ]
                ],
                "Shipment" => [
                    "Shipper" => [
                        "Name" => $args['ShipperName'],
                        "ShipperNumber" => $args['ShipperNumber'],
                        "Address" => [
                            "AddressLine" => [
                                $args['ShipperAddressLine1'],
                                $args['ShipperAddressLine2'],
                                $args['ShipperAddressLine3'],
                            ],
                            "City" => $args['ShipperCity'],
                            "StateProvinceCode" => $args['ShipperStateProvinceCode'],
                            "PostalCode" => $args['ShipperStatePostalCode'],
                            "CountryCode" => $args['ShipperCountryCode']
                        ]
                    ],
                    "ShipTo" => [
                        "Name" => $args['ShipToName'],
                        "Address" => [
                            "AddressLine" => [
                                $args['ShipToAddress1'],
                                $args['ShipToAddress2'],
                                $args['ShipToAddress3'],
                            ],
                            "City" => $args['ShipToCity'],
                            "StateProvinceCode" => $args['ShipToStateProvinceCode'],
                            "PostalCode" => $args['ShipToPostalCode'],
                            "CountryCode" => $args['ShipToCountryCode']
                        ]
                    ],
                    "ShipFrom" => [
                        "Name" => $args['ShipFromName'],
                        "Address" => [
                            "AddressLine" => [
                                $args['ShipFromAddress1'],
                                $args['ShipFromAddress2'],
                                $args['ShipFromAddress3'],
                            ],
                            "City" => $args['ShipFromCity'],
                            "StateProvinceCode" => $args['ShipFromStateProvinceCode'],
                            "PostalCode" => $args['ShipFromPostalCode'],
                            "CountryCode" => $args['ShipFromCountryCode']
                        ]
                    ],
                    "ShipmentRatingOptions" => [
                        "NegotiatedRatesIndicator" => ""
                    ],
                ]
            ]
        ];

        if (strtolower($args['RequestOption']) == 'shoptimeintransit') {
            $return['RateRequest']['Request']['SubVersion'] = '1801';
        }

        /**
         * ADD Payment inforamtion with and without Duties, Tax
         */
        $return['RateRequest']['Shipment'] =
            array_merge(
                $return['RateRequest']['Shipment'],
                ['PaymentDetails' => $paymentInformation]
            );

        /**
         * ADD Package List and Package Service Options
         * Updated at 26-08-2018
         */
        $return['RateRequest']['Shipment'] =
            array_merge(
                $return['RateRequest']['Shipment'],
                ['Package' => $packageData]
            );

        /**
         * ADD Shipping Service Options
         * Updated at 26-08-2018
         */
        if (strtolower($args['RequestOption']) != 'shoptimeintransit') {
            $return['RateRequest']['Shipment'] =
                array_merge(
                    $return['RateRequest']['Shipment'],
                    ['ShipmentServiceOptions' => $shipmentServices]
                );
        }

        /**
         * ADD Shipment Indicator Types
         * Updated at 24-09-2018
         */
        $return['RateRequest']['Shipment'] =
            array_merge(
                $return['RateRequest']['Shipment'],
                $shipmentTypes
            );

        if (!empty($shippingService))
        {
            $return['RateRequest']['Shipment'] =
                array_merge(
                    $return['RateRequest']['Shipment'],
                    ['Service' => $shippingService]
                );
        }

        if (!empty($shipmentTotalWeight))
        {
            $return['RateRequest']['Shipment'] =
                array_merge(
                    $return['RateRequest']['Shipment'],
                    ['ShipmentTotalWeight' => $shipmentTotalWeight]
                );
        }

        if (!empty($invoiceLineTotal))
        {
            $return['RateRequest']['Shipment'] =
                array_merge(
                    $return['RateRequest']['Shipment'],
                    ['InvoiceLineTotal' => $invoiceLineTotal]);
        }

        if (!empty($deliveryTimeInfor))
        {
            $return['RateRequest']['Shipment'] =
                array_merge(
                    $return['RateRequest']['Shipment'],
                    ['DeliveryTimeInformation' => $deliveryTimeInfor]);
        }

        if (!empty($apAddress))
        {
            $return['RateRequest']['Shipment'] =
            array_merge(
                $return['RateRequest']['Shipment'],
                $apAddress
            );
        }

        /**
         * Add more information
         */
        if (!empty($this->flag))
        {
            foreach ($this->flag as $key)
            {
                switch ($key)
                {
                    case 'ResidentialAddressIndicator':
                        $return['RateRequest']['Shipment']['ShipTo']['Address'] =
                            array_merge(
                                $return['RateRequest']['Shipment']['ShipTo']['Address'],
                                ['ResidentialAddressIndicator' => '1']
                            );
                    break;
                }
            }
        }

        return $return;
    }
}
