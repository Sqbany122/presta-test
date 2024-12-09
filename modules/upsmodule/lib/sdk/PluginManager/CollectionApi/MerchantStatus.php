<?php

namespace PluginManager\CollectionApi;

/**
 * Created by UPS
 * Created at 27/07/2018
 */

use PluginManager\CollectionApi\CollectionHandle;

class MerchantStatus extends CollectionHandle
{
    const PATH = 'Merchant/UpdateMerchantStatus';

    public function __invoke($args = array())
    {
        $this->path = SELF::PATH;

        return parent::__invoke($args);
    }

    protected function resolveParam($args)
    {
        $result = parent::resolveParam(array($args['data']));

        $this->writeLogDb('MerchantStatusRequest', __FILE__, __FUNCTION__, json_decode($result), json_decode($this->path));

        return $result;
    }

    protected function responseHandler($response)
    {
        $result = parent::responseHandler($response);
        
        $this->writeLogDb('MerchantStatusRes', __FILE__, __FUNCTION__, $result);
        
        return $result;
    }
}
