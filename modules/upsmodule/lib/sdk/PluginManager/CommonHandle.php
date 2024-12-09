<?php

namespace PluginManager;

/**
 * Created by UPS
 * Created at 03/05/2019
 */

use Sdk\mycurl;
use Sdk\Handle;

class CommonHandle extends Handle
{
    public function __invoke($params = array())
    {
        parent::__invoke($params);

        $auth = array();
        if (isset($params['preToken'])) {
            $token = $params['preToken'];
            $auth = array("Authorization: Bearer $token");
        }

        $params = $this->resolveParam($params);
        $mycurl = new mycurl($this->endpoint);
        $mycurl->setPost($params);
        $mycurl->createCurl($auth);

        $responseFormat = $this->responseHandler($mycurl->__tostring());

        if (is_object($this->response) && property_exists($this->response, 'error') && !empty($this->response->error)) {
            $this->saveLogAPI($this->endpoint, $params, json_encode($this->response));
        }

        return($responseFormat);
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
