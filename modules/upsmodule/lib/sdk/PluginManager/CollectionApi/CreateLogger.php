<?php

namespace PluginManager\CollectionApi;

/**
 * Created by UPS
 * Created at 16/05/2019
 */

use PluginManager\CollectionApi\CollectionHandle;

class CreateLogger extends CollectionHandle
{
    const PATH = 'Merchant/WriteLogger';

    public function __invoke($args = array())
    {
        $this->path = SELF::PATH;
        return parent::__invoke($args);
    }

    protected function resolveParam($args)
    {
        $result = parent::resolveParam(array($args['data']));
        
        /* $this->writeLogDb('CreateLoggerRequest', __FILE__, __FUNCTION__, json_decode($result), json_decode($this->path)); */

        return $result;
    }

    protected function responseHandler($response)
    {
        $result = parent::responseHandler($response);

        return $result;
    }
}
