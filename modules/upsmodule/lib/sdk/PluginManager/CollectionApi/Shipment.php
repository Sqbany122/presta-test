<?php

namespace PluginManager\CollectionApi;

/**
 * Created by UPS
 * Created at 27/07/2018
 */

use PluginManager\CollectionApi\CollectionHandle;

class Shipment extends CollectionHandle
{
    const PATH = 'Shipment/TransferShipments';

    public function __invoke($args = array())
    {
        $this->path = SELF::PATH;

        return parent::__invoke($args);
    }

    protected function responseHandler($response)
    {
        $result = parent::responseHandler($response);
        
        $this->writeLogDb('ShipmentInfoRes', __FILE__, __FUNCTION__, $result);

        return $result;
    }

    protected function resolveParam($args)
    {
        $data = $args['data'];

        $result = parent::resolveParam(array(array(
                'merchantKey' => $data['merchantKey'],
                'accountNumber' => $data['accountNumber'],
                'shipmentId' => $data['shipmentId'],
                'fee' => $data['fee'],
                'revenue' => $data['revenue'],
                'orderDate' => $data['orderDate'],
                'address' => $data['address'],
                'postalCode' => $data['postalCode'],
                'city' => $data['city'],
                'country' => $data['country'],
                'serviceType' => $data['serviceType'],
                'serviceCode' => $data['serviceCode'],
                'serviceName' => $data['serviceName'],
                'isCashOnDelivery' => $data['isCashOnDelivery'],
                'packages' => $data['packages'],
                'products' => array($data['products']),
                'accessorials' => $data['accessorials']
            )
        ));

        $this->writeLogDb('ShipmentInfoRequest', __FILE__, __FUNCTION__, json_decode($result), json_decode($this->path));

        return $result;
    }

}
