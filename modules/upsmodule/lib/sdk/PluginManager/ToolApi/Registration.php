<?php

namespace PluginManager\ToolApi;

/**
 * Created by UPS
 * Created at 05/07/2018
 */

use PluginManager\ToolApi\ToolHandle;

class Registration extends ToolHandle
{
    CONST PATH = 'UpsReadyProvider/Registration';
    private $withInvoice = false;
    private $invoiceOver = false;
    private $needToOpenAccount = false;
    private $saveAccountNumber = false;
    private $saveUpsId = false;
    private $flatResponse = '';

    public function __invoke($args = array())
    {
        $this->path = SELF::PATH;
        // Debug style $return
        $return = parent::__invoke($args);

        return $return;
    }

    protected function resolveParam($args)
    {
        ToolHandle::transliterator($args);
        if (isset($args['firstTimeFlag']) && $args['firstTimeFlag'])
        {
            $data = $this->registerRequest($args);
            $flag = 'RegisterRequest';
            $this->flatResponse = 'RegisterResponse';
        }
        else
        {
            $data = $this->manageAccount($args);
            $flag = 'ManageAccountRequest';
            $this->flatResponse = 'ManageAccountResponse';
        }

        $this->isShipperAccountExits($args, $data, $flag);

        // first time need use account
        if (isset($args['firstTimeFlag']) && $args['firstTimeFlag'])
        {
            $data['UPSSecurity'] = array();
            $return = json_encode($data);
        }
        else
        {
            $return = parent::resolveParam($data);
        }

        $this->writeLogDb('Registration', __FILE__, __FUNCTION__, json_decode($return));

        return $return;
    }

    protected function responseHandler($response)
    {
        $data = parent::responseHandler($response);
        $responseType = $this->flatResponse;

        if (isset($data->Fault) ||
            !isset($data->$responseType) ||
            empty($data->$responseType))
        {
            $this->writeLogDb('error', __FILE__, __FUNCTION__, $data);

            if (isset($data->error) && !is_null($data->error)) {
                return [
                    'Code' => $data->error->errorCode,
                    'Description' => $data->error->message
                ];
            } else {
                return [
                    'Code' => $data->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Code,
                    'Description' => $data->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description
                ];
            }
        }

        $this->writeLogDb('RegistrationRes', __FILE__, __FUNCTION__, $data);

        $res = $data->$responseType;
        $code = $res->Response->ResponseStatus->Code;
        $msg = $res->Response->ResponseStatus->Description;

        // API respond NOT SUCCESS
        if ($code != '1')
        {
            return [
                'Code' => $code,
                'Description' => $msg
            ];
        }

        $shiperStatuses = $res->ShipperAccountStatus;

        if (!is_array($shiperStatuses))
        {
            $this->checkStatus($shiperStatuses->Code);
        }
        else
        {
            foreach ($shiperStatuses as $status)
            {
                $this->checkStatus($status->Code);
            }
        }

        if ($this->saveAccountNumber || $this->saveUpsId)
        {
            $code = 2;
            $msg = 'Need to Save Account Number';
        }
        else
        {
            $code = -1;
            if (!is_array($shiperStatuses))
            {
                $msg = $shiperStatuses->Description;
            }
            else
            {
                $msg = $shiperStatuses[0]->Description;
            }
        }

        return [
            'Code' => $code,
            'Description' => $msg
        ];
    }

    private function checkStatus($statusCode)
    {
        /**
         * 010 - Account is added to users profile successfully.
         * 012 - Account is existing in your profile currently
         * 040 - Account with invoice is authorized and added to your valid account list successfully
         * 041 - Account is authorized but cannot be added to your valid account list due to system problem, try account management again
         *
         * 011 - Account cannot be added to users profile due to system problem, try account management later
         *
         * 013 - Account cannot be added to your profile due to exceeding the max number allowed
         * 042 - Account has already been authorized and in your valid account list before your request today
         * 043 - Authorization is not performed since the account is not on the EBS country list
         * 044 - Account cannot be added to your authenticated or valid list due to exceeding the maximum number of account allowed
         * 045- The invoice information entered does not match the UPS Account.
         *      To Ship with this UPS Account, the correct invoice information must be entered
         */
        switch ($statusCode)
        {
            case '000':
                $this->saveUpsId = true;
                break;

            case '040':
            case '010':
            case '012':
            case '042':
                $this->saveAccountNumber = true;
                break;

            case '011':
                $this->needToOpenAccount = true;
                break;

            default:
                break;

        }
    }

    private function isShipperAccountExits($args, &$data, $flag)
    {
        switch ($args['optradio'])
        {
            case 0:
                // TODO
                break;

            case 1:
                $this->withInvoice = true;
                break;
            case 2:
                $this->invoiceOver = true;
                break;

            default:
                # code...
                break;
        }

        if ($this->withInvoice || $this->invoiceOver
            && array_key_exists('AccountNumber', $args)
            && !empty($args['AccountNumber']))
        {
            $data[$flag]['ShipperAccount'] = [
                'AccountName'   => $args['AccountName'],
                'AccountNumber' => $args['AccountNumber'],
                'CountryCode'   => $args['CountryCode'],
                'PostalCode'    => $args['PostalCode'],
            ];

            if (array_key_exists('InvoiceNumber', $args)
                && !empty($args['InvoiceNumber']))
            {
                $data[$flag]['ShipperAccount']['InvoiceInfo'] = [
                    'InvoiceNumber' => $args['InvoiceNumber'],
                    'InvoiceDate'   => $args['InvoiceDate'],
                    'CurrencyCode'  => $args['CurrencyCode'],
                    'InvoiceAmount' => $args['InvoiceAmount'],
                ];
                if (!empty($args['ControlID'])) {
                    $data[$flag]['ShipperAccount']['InvoiceInfo']['ControlID'] =  $args['ControlID'];
                }
            }
        }
    }

    private function registerRequest($args)
    {
        $line1 = substr($args['AddressLine1'], 0, 35);
        $line2 = substr($args['AddressLine2'], 0, 35);
        $line3 = substr($args['AddressLine3'], 0, 35);

        $address = [];

        if (!empty($line1)) {
            $address[] = $line1;
        }

        if (!empty($line2)) {
            $address[] = $line2;
        }

        if (!empty($line3)) {
            $address[] = $line3;
        }

        return [
            'RegisterRequest' => [
                'Request' => [
                'RequestOption' => 'N',
                'TransactionReference' => [
                    'CustomerContext' => 'CustomerContext'
                    ]
                ],
                'Username' => $args['Username'],
                'Password' => $args['Password'],
                'CompanyName' => $args['CompanyName'],
                'CustomerName' => $args['CustomerName'],
                'EndUserIPAddress' => $this->getClientIP(),
                'Title' => $args['Title'],
                'Address' => [
                    'AddressLine' => $address,
                    'City' => $args['City'],
                    'StateProvinceCode' => isset($args['ProvinceCode']) ? $args['ProvinceCode'] : 'XX',
                    'PostalCode' => $args['PostalCode'],
                    'CountryCode' => $args['CountryCode']
                ],
                'PhoneNumber' => $args['PhoneNumber'],
                'EmailAddress' => $args['EmailAddress'],
                'NotificationCode' => '01',
                // 'EndUserIPAddress' => $this->getClientIP(),
                'DeviceIdentity' => $args['ioBlackBox'],
                'SuggestUsernameIndicator' => 'N',
            ]
        ];
    }

    private function manageAccount($args)
    {
        $accountExist = $args['UPSAccountMerchant'];
        $userName = isset($accountExist['Username']) ? $accountExist['Username'] : 'nousername';
        $password = isset($accountExist['Password']) ? $accountExist['Password'] : 'nopassword';

        return [
            "ManageAccountRequest" => [
                "Request" => [
                    "TransactionReference" => [
                        "CustomerContext" => "CustomerContext"
                    ]
                ],
                "Username" => $userName,
                "Password" => $password
            ]
        ];
    }
}
