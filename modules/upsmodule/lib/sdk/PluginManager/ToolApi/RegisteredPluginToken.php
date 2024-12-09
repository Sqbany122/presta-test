<?php

namespace PluginManager\ToolApi;

/**
 * Created by UPS
 * Created at 03/05/2019
 */

use PluginManager\ToolApi\ToolHandle;

class RegisteredPluginToken extends ToolHandle
{
    const PATH = 'SecurityService/RegisteredPluginToken';

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
        $args = $args['data'];
        $data = [
            "WebstoreMetadata"=> [
                "MerchantKey"=> $args['MerchantKey'],
                "WebstoreUrl"=> $args['WebstoreUrl'],
                "WebstoreUpsServiceLinkSecurityToken"=> $args['WebstoreUpsServiceLinkSecurityToken'],
                "WebstorePlatform"=> "PrestaShop",
                "WebstorePlatformVersion"=> $args['WebstorePlatformVersion'],
                "UpsReadyPluginName"=> "UPS Access Point and Shipping=> Official Module",
                "UpsReadyPluginVersion"=> $args['UpsReadyPluginVersion']
            ],
            "UPSSecurity"=> [
                "UsernameToken"=> [
                    "Username"=> $args['UPSAccountMerchant']['Username'],
                    "Password"=> $args['UPSAccountMerchant']['Password'],
                ],
                "ServiceAccessToken"=> [
                    "AccessLicenseNumber"=> $args['UPSAccountMerchant']['LicenseKey']
                ]
            ]
        ];
        
        $result = parent::resolveParam($data);

        $this->writeLogDb('RegisteredToken', __FILE__, __FUNCTION__, json_decode($result), json_decode($this->path));

        return $result;
    }

    protected function responseHandler($response)
    {
        $result = parent::responseHandler($response);
        $this->writeLogDb('RegisteredTokenRes', __FILE__, __FUNCTION__, $result);
        return $result;
    }
}