<?php

namespace PluginManager\ToolApi;

/**
 * Created by UPS
 * Created at 19/06/2018
 */

use PluginManager\ToolApi\ToolHandle;

class License extends ToolHandle
{
    const PATH = 'UpsReadyProvider/License';
    private $flag = null;
    private $lang = 'EN';
    private $country = 'US';
    private $developerLicense = 'ED466785DB641E6C';

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
        ToolHandle::transliterator($args, null, '', array('LicenseText'));

        switch ($this->flag)
        {
            case 'AccessLicenseAgreementRequest';
                $data = $this->getAgreement($args);
                break;

            case 'AccessLicenseRequest':
                $data = $this->getLicenseKey($args);
                break;
            
            default:
                $data = $args;
                break;
        }

        $result = parent::resolveParam($data);

        $this->writeLogDb($this->flag, __FILE__, __FUNCTION__, json_decode($result));

        return $result;
    }    

    private function getAgreement($args)
    {
        $this->lang = isset($args['LanguageCode']) ?
            $args['LanguageCode'] : "EN";
        $this->country = isset($args['CountryCode']) ?
            $args['CountryCode'] : "US";

        return [
            "AccessLicenseAgreementRequest" => [
                "Request" => [
                    "RequestOption" => "",
                    "TransactionReference" => [
                        "CustomerContext" => "CutomerContext",
                        "TransactionIdentifier" => ""
                    ]
                ],
                "AccessLicenseProfile" => [
                    "CountryCode" => $this->country,
                    "LanguageCode" => $this->lang
                ]
            ]
        ];
    }

    public function getLanguageCode()
    {
        return $this->lang;
    }

    public function getCountryCode()
    {
        return $this->country;
    }

    private function getLicenseKey($args)
    {
        return [
            "AccessLicenseRequest" => [
                "Request" => [
                    "RequestOption" => "",
                    "TransactionReference" => [
                        "CustomerContext" => "CustomerContext",
                        "TransactionIdentifier" => ""
                    ],
                ],
                "CompanyName" => isset($args['CompanyName']) ? $args['CompanyName'] : '',
                "Address" => [
                    "AddressLine1" => isset($args['AddressLine1']) ? $args['AddressLine1'] : '',
                    "AddressLine2" => isset($args['AddressLine2']) ? $args['AddressLine2'] : '',
                    "AddressLine3" => isset($args['AddressLine3']) ? $args['AddressLine3'] : '',
                    "City" => isset($args['City']) ? $args['City'] : '',
                    "StateProvinceCode" => isset($args['ProvinceCode']) ? $args['ProvinceCode'] : '',
                    "PostalCode" => isset($args['PostalCode']) ? $args['PostalCode'] : '',
                    "CountryCode" => isset($args['CountryCode']) ? $args['CountryCode'] : '',
                ],
                "PrimaryContact" => [
                    "Name" => isset($args['CustomerName']) ? $args['CustomerName'] : '',
                    "Title" => isset($args['Title']) ? $args['Title'] : '',
                    "EMailAddress" => isset($args['EmailAddress']) ? $args['EmailAddress'] : '',
                    "PhoneNumber" => isset($args['PhoneNumber']) ? $args['PhoneNumber'] : '',
                    "FaxNumber" => isset($args['FaxNumber']) ? $args['FaxNumber'] : '',
                ],
                "SecondaryContact" => [
                    // "Name" => isset($args['CustomerNameSec']) ? $args['CustomerNameSec'] : '',
                    // "Title" => isset($args['Title']) ? $args['Title'] : '',
                    // "EMailAddress" => isset($args['EmailAddress']) ? $args['EmailAddress'] : '',
                    // "PhoneNumber" => isset($args['PhoneNumber']) ? $args['PhoneNumber'] : '',
                    // "FaxNumber" => isset($args['FaxNumber']) ? $args['FaxNumber'] : '',
                    "Name" => 'Hau',
                    "Title" => 'Mr',
                    "EMailAddress" => 'hautq@fsoft.com.vn',
                    "PhoneNumber" => '090909090',

                ],
                "CompanyURL" => $_SERVER['SERVER_NAME'],
                "DeveloperLicenseNumber" => $this->developerLicense,
                "AccessLicenseProfile" => [
                    "CountryCode" => $args['CountryCode'],
                    "LanguageCode" => $args['LanguageCode'],
                    "AccessLicenseText" => htmlspecialchars($args['LicenseText'])
                ],
                "ClientSoftwareProfile" => [
                    "SoftwareInstaller" => 'Prestashop',
                    "SoftwareProductName" => 'Prestashop module',
                    "SoftwareProvider" => 'Prestashop',
                    "SoftwareVersionNumber" => $args['PlatformVersion'],
                ]
            ]
        ];
    }

    protected function responseHandler($response)
    {
        $data = parent::responseHandler($response);

        if (isset($data->AccessLicenseAgreementResponse))
        {
            $res = $this->formatReturn($data, 'AccessLicenseAgreementResponse');
            
            $res['Content'] = htmlspecialchars_decode(
                                html_entity_decode(
                                    $data
                                        ->AccessLicenseAgreementResponse
                                        ->AccessLicenseText,
                                    ENT_XML1,
                                    'UTF-8'
                            ));
            $this->writeLogDb(
                'responseAgreement', __FILE__, __FUNCTION__, $data, $this->e->getTrace()
            );
        }
        elseif (isset($data->AccessLicenseResponse))
        {
            $res = $this->formatReturn($data, 'AccessLicenseResponse');
            $res['AccessLicenseNumber'] = $data->AccessLicenseResponse->AccessLicenseNumber;

            $this->writeLogDb(
                'reponseLisense', __FILE__, __FUNCTION__, $data, $this->e->getTrace()
            );
        }
        elseif (isset($data->Fault))
        {
            $this->writeLogDb(
                'error', __FILE__, __FUNCTION__, $data, $this->e->getTrace()
            );

            $res = [
                'Code' => $data
                        ->Fault
                        ->detail
                        ->Errors
                        ->ErrorDetail
                        ->PrimaryErrorCode
                        ->Code,
                'Description' => $data
                        ->Fault
                        ->detail
                        ->Errors
                        ->ErrorDetail
                        ->PrimaryErrorCode
                        ->Description
            ];
        }
        else
        {
            $this->writeLogDb(
                'hard', __FILE__, __FUNCTION__, $data, $this->e->getTrace()
            );

            $res = [
                'Code' => -1,
                'Description' => 'No internet connection'
            ];
        }

        return $res;
    }

    public function setAccessLicenseAgreementRequest()
    {
        $this->flag = 'AccessLicenseAgreementRequest';
    }

    public function setAccessLicenseRequest()
    {
        $this->flag = 'AccessLicenseRequest';
    }
}
