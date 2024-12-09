<?php

namespace PluginManager\ToolApi;

/**
 * Created by UPS
 * Created at 03/05/2019
 */

use PluginManager\CommonHandle;

class ToolHandle extends CommonHandle
{
    public function __invoke($params = array())
    {
        $this->endpoint = TOOL_API_URI . $this->path;
        return parent::__invoke($params);
    }

    protected function resolveParam($args)
    {
        return parent::resolveParam($args);
    }
    
    protected function responseHandler($res)
    {
        $data = parent::responseHandler($res);

        return $data;
    }
}
