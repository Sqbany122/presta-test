<?php

namespace Ups\Api;

/**
 * Created by UPS
 * Created at 18/07/2018
 */

use Ups\Api\CommonHandle;

class PromoRequest extends CommonHandle
{
    const PATH = 'PromoDiscount';

    public function __invoke($args = array())
    {
        if ($this->e == null)
        {
            $this->e = new \Exception;
        }
        $this->path = SELF::PATH;
        return parent::__invoke($args);
    }

    protected function responseHandler($response)
    {
        $data = parent::responseHandler($response);

        if ( isset($data->Fault) )
        {
            $this->writeLogDb(
                'errorPromoResponse', __FILE__, __FUNCTION__, $data, $this->e->getTrace()
            );

            $fault = $this->formatFault($data);

            return $fault;
        }

        if (isset($data->PromoDiscountResponse))
        {
            $res = $this->formatReturn($data, 'PromoDiscountResponse');

            $this->writeLogDb(
                'promoResponse', __FILE__, __FUNCTION__, $data, $this->e->getTrace()
            );
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

    protected function resolveParam($args)
    {
        CommonHandle::transliterator($args);

        $data = [
            'PromoDiscountRequest' => [
                // 'Request' => [
                    'AgreementAcceptanceCode' => $args['AcceptanceCode'],
                    'PromoCode' => $args['promoCode'],
                    'Locale' => [
                        'LanguageCode' => $args['LanguageCode'],
                        'CountryCode' => $args['CountryCode']
                    ],
                // ],
                'AccountInfo' => [
                    'AccountNumber' => $args['AccountNumber'],
                    // 'AccountNotNeededIndicator' => $args['AccountNumber']
                ]
            ]
        ];

        // Debug style for return data
        $return = parent::resolveParam($data);

        $this->writeLogDb('promoRequest', __FILE__, __FUNCTION__, json_decode($return));

        return $return;
    }
}
