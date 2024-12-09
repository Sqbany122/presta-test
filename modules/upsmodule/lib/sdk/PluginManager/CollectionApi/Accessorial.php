<?php

namespace PluginManager\CollectionApi;

/**
 * Created by UPS
 * Created at 16/05/2019
 */

use PluginManager\CollectionApi\CollectionHandle;

class Accessorial extends CollectionHandle
{
    const PATH = 'Merchant/TransferAccessorials';

    public function __invoke($args = array())
    {
        $this->path = SELF::PATH;

        return parent::__invoke($args);
    }

    protected function resolveParam($args)
    {
        $data = array();
        
        $result = parent::resolveParam($data);

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
