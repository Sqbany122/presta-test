<?php

namespace Ups\Api;

/**
 * Created by UPS
 * Created at 30/07/2018
 */

use Ups\Api\CommonHandle;

class VoidShipment extends CommonHandle
{
    const PATH = 'Void';

    public function __invoke($args = array())
    {
        if ($this->e == null)
        {
            $this->e = new \Exception;
        }

        $this->path = SELF::PATH;

        return parent::__invoke($args);
    }

    protected function responseHandler($response)
    {
        $data = parent::responseHandler($response);

        if (isset($data->VoidShipmentResponse))
        {
            $res = $this->formatReturn($data, 'VoidShipmentResponse');

            $this->writeLogDb(
                'voidResponse', __FILE__, __FUNCTION__, $data
            );
        }
        elseif (isset($data->Fault))
        {
            $this->writeLogDb(
                'voidError', __FILE__, __FUNCTION__, $data, $this->e->getTrace()
            );

            $res = [
                'Code' => $data
                        ->Fault
                        ->detail
                        ->Errors
                        ->ErrorDetail
                        ->PrimaryErrorCode
                        ->Code,
                'Description' => $data
                        ->Fault
                        ->detail
                        ->Errors
                        ->ErrorDetail
                        ->PrimaryErrorCode
                        ->Description
            ];

            /**
             * Only error code 190117 is treated as Void successfully.
             * All the other error codes mean Void failure
             */
            if ($res['Code'] === '190117') {
                $res['Code'] = '1';
            }
        }
        else
        {
            $this->writeLogDb(
                'voidHardErr', __FILE__, __FUNCTION__, $data, $this->e->getTrace()
            );

            $res = [
                'Code' => -1,
                'Description' => 'No internet connection'
            ];
        }

        return $res;
    }

    protected function resolveParam($args)
    {
        // if (DEVELOPMENT)
        // {
        //     $args['shipmentId'] = "1ZISDE016691676846";
        // }

        $data = [
            'VoidShipmentRequest' => [
                'Request' => [
                    'TransactionReference' => [
                        'CustomerContext' => "",
                    ]
                ],
                'VoidShipment' => [
                    'ShipmentIdentificationNumber' => $args['shipmentId'],
                ]
            ]
        ];

        $param = parent::resolveParam($data);

        $this->writeLogDb(
            'voidRequest', __FILE__, __FUNCTION__, $data, $this->e->getTrace()
        );

        return $param;
    }
}
