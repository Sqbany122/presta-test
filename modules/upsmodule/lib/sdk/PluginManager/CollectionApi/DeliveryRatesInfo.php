<?php

namespace PluginManager\CollectionApi;

/**
 * Created by UPS
 * Created at 28/07/2018
 */

use PluginManager\CollectionApi\CollectionHandle;

class DeliveryRatesInfo extends CollectionHandle
{
    const PATH = 'Merchant/TransferDeliveryRates';

    public function __invoke($args = array())
    {
        $this->path = SELF::PATH;

        return parent::__invoke($args);
    }

    protected function resolveParam($args)
    {
        $result = parent::resolveParam(array(array(
            'merchantKey' => $args['data']['merchantKey'],
            'deliveryRates' => $args['data']['deliveryRates']
        )));

        $this->writeLogDb('DeliveryRatesRequest', __FILE__, __FUNCTION__, json_decode($result), json_decode($this->path));

        return $result;
    }

    protected function responseHandler($response)
    {
        $result = parent::responseHandler($response);
        
        $this->writeLogDb('DeliveryRatesRes', __FILE__, __FUNCTION__, $result);
        
        return $result;
    }
}
