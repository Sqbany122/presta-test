<?php

namespace PluginManager\CollectionApi;

/**
 * Created by UPS
 * Created at 27/07/2018
 */

use PluginManager\CollectionApi\CollectionHandle;

class ShipmentStatus extends CollectionHandle
{
    const PATH = 'Shipment/UpdateShipmentStatus';

    public function __invoke($args = array())
    {
        $this->path = SELF::PATH;

        return parent::__invoke($args);
    }

    protected function resolveParam($args)
    {
        $result = parent::resolveParam($args['data']);
        
        $this->writeLogDb('ShipmentStatusReq', __FILE__, __FUNCTION__, json_decode($result));

        return $result;
    }

    protected function responseHandler($response)
    {
        $result = parent::responseHandler($response);

        $this->writeLogDb('ShipmentStatusRes', __FILE__, __FUNCTION__, $result, $this->path);

        return $result;
    }
}
