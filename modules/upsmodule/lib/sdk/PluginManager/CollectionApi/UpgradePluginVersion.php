<?php

namespace PluginManager\CollectionApi;

/**
 * Created by UPS
 * Created at 30/07/2018
 */

use PluginManager\CollectionApi\CollectionHandle;

class UpgradePluginVersion extends CollectionHandle
{
    const PATH = 'Merchant/UpgradePluginVersion';

    public function __invoke($args = array())
    {
        $this->path = SELF::PATH;

        return parent::__invoke($args);
    }

    protected function resolveParam($args)
    {
        $result = parent::resolveParam(array($args['data']));

        $this->writeLogDb('MerchantInfoRequest', __FILE__, __FUNCTION__, json_decode($result), json_decode($this->path));

        return $result;
    }

    protected function responseHandler($response)
    {
        $result = parent::responseHandler($response);
        
        $this->writeLogDb('MerchantInfoRes', __FILE__, __FUNCTION__, $result);
        
        return $result;
    }
}
