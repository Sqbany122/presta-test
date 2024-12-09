<?php

namespace Ups\Api;

/**
 * Created by UPS
 * Created at 24/07/2018
 */

use Ups\Api\CommonHandle;

class TinT extends CommonHandle
{
    use InternationalTrait;

    const PATH = 'TimeInTransit';

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

        if (isset($data->Fault))
        {
            $this->writeLogDb('errorTint', __FILE__, __FUNCTION__, $data);

            return $this->formatFault($data);
        }

        $this->writeLogDb('TintRes', __FILE__, __FUNCTION__, $data);

        return $this->formatReturn($data, 'TimeInTransitResponse', 'TinT');
    }

    protected function resolveParam($args)
    {
        $data = $this->tintData($args);
        // Debug style for return data
        $return = parent::resolveParam($data);

        $this->writeLogDb('getTintData', __FILE__, __FUNCTION__, json_decode($return));

        return $return;
    }

    private function tintData($args)
    {
        $pickupTime = [
            "Date"=> $args['PickupDate'],
        ];

        if (isset($args['PickupTime']) && !empty($args['PickupTime']))
        {
            $pickupTime = [
                "Date"=> $args['PickupDate'],
                "Time"=> (isset($args['PickupTime']) ? $args['PickupTime'] : ''),
            ];
        }

        $this->setFromCountry($args['ShipToCountryCode']);
        $this->setToCountry($args['ShipFromCountryCode']);

        $body = [
            "TimeInTransitRequest"=> [
                "Request"=> [
                    "RequestOption"=> "TNT",
                    "TransactionReference"=> [
                        "CustomerContext"=> "",
                        "TransactionIdentifier"=> ""
                    ]
                ],
                "ShipFrom"=> [
                    "Address"=> [
                        "StateProvinceCode"=> $args['ShipFromStateProvinceCode'],
                        "PostalCode"=> $args['ShipFromPostalCode'],
                        "CountryCode"=> $args['ShipFromCountryCode'],
                    ]
                ],
                "ShipTo"=> [
                    "Address"=> [
                        "StateProvinceCode"=> $args['ShipToStateProvinceCode'],
                        "PostalCode"=> $args['ShipToPostalCode'],
                        "CountryCode"=> $args['ShipToCountryCode'],
                    ]
                ],
                "Pickup"=> $pickupTime,
                "ShipmentWeight"=> [
                    "UnitOfMeasurement"=> [
                        "Code"=> $args['PackageWeightCode'],
                        "Description"=> $args['PackageWeightDescription'],
                    ],
                    "Weight"=> $args['PackageWeightWeight'],
                ],
                "MaximumListSize"=> $args['MaximumListSize'],
                // "TotalPackagesInShipment" => '1',
            ]
        ];

        if ($this->shipInternational())
        {
            $body['TimeInTransitRequest'] = array_merge($body['TimeInTransitRequest'], ['InvoiceLineTotal' => [
                'CurrencyCode' => $args['CurrencyCode'],
                'MonetaryValue' => $args['MonetaryValue']
            ]]);
        }

        return $body;
    }
}
