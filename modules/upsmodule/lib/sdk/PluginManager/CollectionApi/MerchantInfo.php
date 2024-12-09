<?php

namespace PluginManager\CollectionApi;

/**
 * Created by UPS
 * Created at 16/05/2019
 */

use PluginManager\CollectionApi\CollectionHandle;

class MerchantInfo extends CollectionHandle
{
    const PATH = 'Merchant/TransferMerchantInfo';

    public function __invoke($args = array())
    {
        $this->path = SELF::PATH;

        return parent::__invoke($args);
    }

    protected function resolveParam($args)
    {
        $data = array();
        
        foreach ($args['data'] as $arg) {

            $tmp = array(
                'isFirstAccount' => (bool) $arg['isFirstAccount'],
                'accountNumber' => $arg['AccountNumber'],
                'joiningDate' => $arg['joiningDate'],
                'website' => $arg['website'],
                'currencyCode' => $arg['currencyCode'],
                'status' => $arg['status'],
                'platform' => $arg['platform'],
                'version' => $arg['version'],
                'postalCode' => $arg['PostalCode'],
                'city' => $arg['City'],
                'country' => $arg['CountryCode'],
            );

            if (isset($arg['merchantKey'])) {
                $tmp['merchantKey'] = $arg['merchantKey'];
            }

            if (isset($arg['defaultPackageName'])) {
                $tmp = array_merge($tmp, array(
                    'defaultPackageName' => $arg['defaultPackageName'] ,
                    'weight' => (float) $arg['weight'] ,
                    'weightUnit' => $arg['weightUnit'] ,
                    'length' => (float) $arg['length'] ,
                    'width' => (float) $arg['width'] ,
                    'height' => (float) $arg['height'] ,
                    'dimensionUnit'  => $arg['dimensionUnit'] ,
                ));
            }

            if (isset($arg['shippingServices'])) {
                $tmp = array_merge($tmp, array(
                    'shippingServices' => $arg['shippingServices'],
                    'deliveryRates' => $arg['deliveryRates']
                ));
            }

            if (isset($arg['accessorials'])) {
                $tmp['accessorials'] = array();
            }

            $data[] = $tmp;
        }

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
