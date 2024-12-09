<?php

namespace Ups\Api;

/**
 * Created by UPS
 * Created at 20/07/2018
 *
 * Updated at 28/08/2018
 */

use Ups\Api\CommonHandle;

class Shipping extends CommonHandle
{
    CONST PATH = 'Ship';
    private $shipment;
    private $flag = [];

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
        } elseif (isset($data->Fault) ||
            !isset($data->ShipmentResponse) ||
            empty($data->ShipmentResponse))
        {
            $this->writeLogDb('errorShip', __FILE__, __FUNCTION__, $data);

            return [
                'Code' => $data->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Code,
                'Description' => $data->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description
            ];
        }

        if ( isset($data->ShipmentResponse) )
        {
            $res = $data->ShipmentResponse;

            $code = $res->Response->ResponseStatus->Code;
            $msg = $res->Response->ResponseStatus->Description;
            $result = $res->ShipmentResults;
            $shipmentId = $result->ShipmentIdentificationNumber;
            $packageResult = $result->PackageResults;
            $shippingFee = $result->ShipmentCharges->TotalCharges->MonetaryValue;

            // UAT 697
            if (isset($result->NegotiatedRateCharges->TotalCharge->MonetaryValue)) {
                $shippingFee = $result->NegotiatedRateCharges->TotalCharge->MonetaryValue;
            }

            $this->writeLogDb('responseShip', __FILE__, __FUNCTION__, $data);

            return [
                'Code'        => $code,
                'Description' => $msg,
                'PackageResult' => $packageResult,
                'ShipmentId' => $shipmentId,
                // 'OrderId'     => $orderId,
                // 'Country'     => $country,
                'ShippingFee' => $shippingFee
            ];
        }
    }

    protected function resolveParam($args)
    {
        $data = $this->publishedRateShipment($args);
        // Debug style for return data
        $return = parent::resolveParam($data);
        $this->writeLogDb('getShipData', __FILE__, __FUNCTION__, json_decode($return));

        return $return;
    }

    private function publishedRateShipment($args)
    {
        /**
         * Handle Shipment Service Options
         * Updated at 28-08-2018
         * Updated by UPS
         */
        $this->currency = $args['currency'];

        $this->orderValue = (string) $args['totalOrderValue'];

        $order = $args['firstOrder'];
        $shiptoCountry = $order['country_code'];
        $shipToStateCode = 'XX';
        if (in_array(strtolower($shiptoCountry), ['us', 'ca', 'ie'])) {
            $shipToStateCode = (!empty($order['state'])) ? $order['state'] :
                ((!empty($order['state_code'])) ? $order['state_code'] : '');
        }
        $accountInfo = $args['accountInfo'];
        $primaryInfo = $args['primaryInfo'];
        $accountNumber = $accountInfo['AccountNumber'];
        $shipFromCountryCode = $accountInfo['CountryCode'];
        $postalcode = preg_replace('/[^a-zA-Z0-9]/s','',$accountInfo['PostalCode']);

        $countryCodeData = new \stdClass();
        $countryCodeData->ShipFromCountryCode = $shipFromCountryCode;
        $countryCodeData->ShipToCountryCode = $shiptoCountry;

        $apCountryCode = '';
        if (!empty($order) && isset($order['ap_city']) && $order['ap_city'] !== '') {
            $apCountryCode = $order['country_code'];
        }

        $request = [
            'ShipmentRequest' => [
                'Request' => [
                    'RequestOption' => 'validate',
                    'SubVersion' => '1801'
                ],
                'Shipment' => [
                    'Description' => 'Description',
                    'Shipper' => [
                        'Name' => $primaryInfo['CustomerName'],
                        'AttentionName' => $primaryInfo['CompanyName'],
                        'ShipperNumber' => $accountInfo['AccountNumber'],
                        'Phone' => [
                            'Number' => $primaryInfo['PhoneNumber'],
                        ],
                        'Address' => [
                            'AddressLine' => [
                                substr($accountInfo['AddressLine1'],0,35),
                                substr($accountInfo['AddressLine2'],0,35),
                                substr($accountInfo['AddressLine3'],0,35),
                            ],
                            'City' => $accountInfo['City'],
                            'StateProvinceCode' => $accountInfo['ProvinceCode'],
                            'PostalCode' => $postalcode,
                            'CountryCode' => $shipFromCountryCode
                        ],
                    ],
                    'ShipTo' => [
                        'Name' => $order['firstname'] . ' ' . $order['lastname'],
                        'AttentionName' => $order['firstname'] . ' ' . $order['lastname'],
                        'Phone' => [
                            'Number' => $order['phone']
                        ],
                        'Address' => [
                            'AddressLine' => [
                                substr($order['address_delivery1'],0,35),
                                substr($order['address_delivery2'],0,35),
                                // $args['AddressLine3']
                            ],
                            'City' => $order['city'],
                            'StateProvinceCode' => $shipToStateCode,
                            'PostalCode' => $order['postcode'],
                            'CountryCode' => $shiptoCountry
                        ],
                    ],
                    'ShipFrom' => [
                        'Name' => $primaryInfo['CustomerName'],
                        'AttentionName' => $primaryInfo['CompanyName'],
                        'Phone' => [
                            'Number' => $primaryInfo['PhoneNumber']
                        ],
                        'Address' => [
                            'AddressLine' => [
                                substr($accountInfo['AddressLine1'],0,35),
                                substr($accountInfo['AddressLine2'],0,35),
                                substr($accountInfo['AddressLine3'],0,35),
                            ],
                            'City' => $accountInfo['City'],
                            'StateProvinceCode' => $accountInfo['ProvinceCode'],
                            'PostalCode' => $postalcode,
                            'CountryCode' => $shipFromCountryCode
                        ],
                    ],
                    'Service' => [
                        'Code' => $args['shippingServiceInfo']['Ratecode'],
                        'Description' => $args['shippingServiceInfo']['name']
                    ],
                    "ShipmentRatingOptions" => [
                        "NegotiatedRatesIndicator" => ""
                    ],
                    'LabelSpecification' => [
                        'LabelImageFormat' => [
                            'Code' => 'GIF',
                            'Description' => 'GIF'
                        ],
                        'HTTPUserAgent' => 'Mozilla/4.5'
                    ],
                ]
            ]
        ];

        $shipmentServices = $this->shipmentServiceOptionsHandle($args['accessorialsService'], $order, $countryCodeData);
        $packageData = $this->packageHandle($args['packages'], $args['accessorialsService'], $countryCodeData);
        $shipmentTypes = $this->checkShipmentType($order);
        $hasDutiesTax = $this->checkDutiesTax($apCountryCode, $args['isApCod']);
        $paymentInformation = $this->getPaymentInformation($accountNumber, $shiptoCountry, $hasDutiesTax);
        $invoiceLineTotal = $this->checkInvoiceLineTotal($args);

        /**
         * ADD Payment inforamtion with and without Duties, Tax
         */
        $request['ShipmentRequest']['Shipment'] =
            array_merge(
                $request['ShipmentRequest']['Shipment'],
                ['PaymentInformation' => $paymentInformation]
            );

        /**
         * ADD Shipping Service Options
         */
        $request['ShipmentRequest']['Shipment'] =
            array_merge(
                $request['ShipmentRequest']['Shipment'],
                ['ShipmentServiceOptions' => $shipmentServices]
            );

        /**
         * ADD Package List and Package Service Options
         */
        $request['ShipmentRequest']['Shipment'] =
            array_merge(
                $request['ShipmentRequest']['Shipment'],
                ['Package' => $packageData]
            );

        /**
         * ADD Shipment Indicator Types
         */
        $request['ShipmentRequest']['Shipment'] =
            array_merge(
                $request['ShipmentRequest']['Shipment'],
                $shipmentTypes
            );

        if (!empty($invoiceLineTotal))
        {
            $request['ShipmentRequest']['Shipment'] =
                array_merge(
                    $request['ShipmentRequest']['Shipment'],
                    ['InvoiceLineTotal' => $invoiceLineTotal]);
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
                        $request['ShipmentRequest']['Shipment']['ShipTo']['Address'] =
                            array_merge(
                                $request['ShipmentRequest']['Shipment']['ShipTo']['Address'],
                                ['ResidentialAddressIndicator' => '1']
                            );
                    break;
                }
            }
        }
        return $request;
    }
}
