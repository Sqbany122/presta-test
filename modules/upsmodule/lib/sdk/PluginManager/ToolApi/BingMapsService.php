<?php

namespace PluginManager\ToolApi;

/**
 * Created by UPS
 * Created at 03/05/2019
 */

use PluginManager\ToolApi\ToolHandle;

class BingMapsService extends ToolHandle
{
    const PATH = 'SecurityService/UpsBingMapsKey';

    public function __invoke($args = array())
    {
        if ($this->e == null)
        {
            $this->e = new \Exception;
        }
        $this->path = SELF::PATH;

        return parent::__invoke($args);
    }

    protected function resolveParam($args)
    {
        $result = parent::resolveParam($args);

        $this->writeLogDb('BingMapsService', __FILE__, __FUNCTION__, json_decode($result), json_decode($this->path));

        return $result;
    }

    protected function responseHandler($response)
    {
        $result = parent::responseHandler($response);
        $this->writeLogDb('BingMapsServiceRes', __FILE__, __FUNCTION__, $result);
        return $result;
    }
}