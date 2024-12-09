<?php

namespace Ups\Api;

/**
 * Created by UPS
 * Created at 18/07/2018
 */

use Ups\Api\CommonHandle;

class PromoAgreement extends CommonHandle
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
                'errorPromoAgreement', __FILE__, __FUNCTION__, $data, $this->e->getTrace()
            );

            $fault = $this->formatFault($data);

            return $fault;
        }

        if (isset($data->PromoDiscountAgreementResponse))
        {
            $res = $this->formatReturn($data, 'PromoDiscountAgreementResponse');
            $res['Agreement'] = $data;

            $this->writeLogDb(
                'promoAgreementResponse', __FILE__, __FUNCTION__, $data, $this->e->getTrace()
            );
        }
        else
        {
            $this->writeLogDb(
                'hard', __FILE__, __FUNCTION__, $data, $this->e->getTrace()
            );
        }

        return $res;
    }

    protected function resolveParam($args)
    {
        CommonHandle::transliterator($args);

        $data = [
            'PromoDiscountAgreementRequest' => [
                'PromoCode' => $args['promoCode'],
                'Locale' => [
                    'LanguageCode' => $args['LanguageCode'],
                    'CountryCode' => $args['CountryCode']
                ]
            ]
        ];

        // Debug style for return data
        $return = parent::resolveParam($data);

        $this->writeLogDb('getPromoData', __FILE__, __FUNCTION__, json_decode($return));

        return $return;
    }

}
