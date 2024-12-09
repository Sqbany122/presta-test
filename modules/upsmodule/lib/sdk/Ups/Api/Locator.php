<?php

namespace Ups\Api;

/**
 * Created by UPS
 * Created at 20/07/2018
 */

use Ups\Api\CommonHandle;

class Locator extends CommonHandle
{
    const PATH = 'Locator';

    public function __invoke($args = array())
    {
        $this->path = SELF::PATH;
        // Debug style $return
        $return = parent::__invoke($args);

        return $return;
    }

    protected function responseHandler($response)
    {
        $data = parent::responseHandler($response);

        if (isset($data->LocatorResponse->Response->ResponseStatusCode) && $data->LocatorResponse->Response->ResponseStatusCode == 0)
        {
            $this->writeLogDb('errorLocator', __FILE__, __FUNCTION__, $data
            ->LocatorResponse
            ->Response
            ->Error
            ->ErrorDescription);

            return array(
                'Code' => $data
                        ->LocatorResponse
                        ->Response
                        ->Error
                        ->ErrorCode,
                'Description' => $data
                                ->LocatorResponse
                                ->Response
                                ->Error
                                ->ErrorDescription,
            );
        }

        if (isset($data->LocatorResponse->SearchResults->DropLocation)) {
            $this->writeLogDb('responseLocator', __FILE__, __FUNCTION__,  $data
            ->LocatorResponse
            ->SearchResults
            ->DropLocation);
        } else { // handle $data null
            $this->writeLogDb('responseLocator', __FILE__, __FUNCTION__,  $data);

            return array(
                'Code' => 0,
                'Description' => 'Fail',
                'Data' => null,
            );
        }


        return array(
            'Code' => $data
                    ->LocatorResponse
                    ->Response
                    ->ResponseStatusCode,
            'Description' => $data
                            ->LocatorResponse
                            ->Response
                            ->ResponseStatusDescription,

            'Data' => $data
                    ->LocatorResponse
                    ->SearchResults
                    ->DropLocation
        );
    }

    protected function resolveParam($args)
    {
        $data = $this->locatorData($args);
        // Debug style for return data
        // return = parent::resolveParam($data);
        $return = json_encode($data);

        $this->writeLogDb('resolveLocator', __FILE__, __FUNCTION__, json_decode($return));

        return $return;
    }

    private function locatorData($args)
    {
        $request = array(
            "RequestAction"=> "Locator",
            "RequestOption"=> "64",
            "TransactionReference"=>""
        );
        $originAddress = array(
            "PhoneNumber"=> "",
            "AddressKeyFormat"=>array(
                "SingleLineAddress" => $args['fullAddress'],
                "CountryCode"=> $args['countryCode']
            )
        );
        $translate = array(
            "Locale"=> $args['locale'],
        );

        $unitOfMeasurement = array(
            "Code"=> $args['unitOfMeasurement'],
        );
        $optionType = array(
            "Code"=> "01"
        );
        $optionCode =array(
            "Code"=> "002"
        );
        $locationSearchCriteria = array(
            "MaximumListSize"=> $args['maximumListSize'],
            "SearchRadius"=> $args['nearby']
        );

        $accessRequest = array(
            "AccessLicenseNumber" => $args['LicenseKey'],
            "Username" => $args['Username'],
            "Password" => $args['Password']
        );

        return array(
            "AccessRequest" => $accessRequest,
            "LocatorRequest" => array(
                "Request" => $request,
                "OriginAddress" => $originAddress,
                "Translate" => $translate,
                "UnitOfMeasurement" => $unitOfMeasurement,
                "LocationSearchCriteria" => $locationSearchCriteria
            )
        );
    }
}
