<?php
/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */

namespace PrestaChamps\GdprPro\Models;

/**
 * Class DeletionRequest
 *
 * Holds the customer data deletion requests
 */
class DataRequest extends \CustomObjectModel
{

    /**
     * @var $id_data_request int Primary key
     */
    public $id_data_request;

    /**
     * @var $id_customer int Requester customer id
     */
    public $id_customer;

    /**
     * @var $id_guest int Requester guest id
     */
    public $id_guest;

    /**
     * @var $type int The status of the data request: new or fulfilled
     *
     * @see DataRequest::REQUEST_STATUS_NEW
     * @see DataRequest::REQUEST_STATUS_FULFILLED
     */
    public $status;

    /**
     * @var $type int The type of the data request: delete or export
     *
     * @see DataRequest::REQUEST_TYPE_DELETION
     * @see DataRequest::REQUEST_TYPE_EXPORT
     */
    public $type;

    /**
     * @var $created_at string Represents the datetime when the request was made
     */
    public $created_at;

    /**
     * @var $fulfilled_at string Represents the datetime when the request was fulfilled
     */
    public $fulfilled_at;

    const REQUEST_TYPE_DELETION = 0;
    const REQUEST_TYPE_EXPORT   = 1;

    const REQUEST_STATUS_FULFILLED = 1;
    const REQUEST_STATUS_NEW       = 0;

    public static $definition = array(
        'table'     => 'data_request',
        'primary'   => 'id_data_request',
        'multilang' => false,
        'fields'    => array(
            'id_data_request' => array(
                'type'     => self::TYPE_INT,
                'validate' => 'isInt',
                'lang'     => false,
            ),
            'id_customer'     => array(
                'type'     => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'db_type'  => 'int',
                'lang'     => false,
            ),
            'id_guest'        => array(
                'type'     => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'db_type'  => 'int', 'lang' => false,
            ),
            'type'            => array(
                'type'     => self::TYPE_BOOL,
                'validate' => 'isBool',
                'db_type'  => 'int',
                'lang'     => false,
            ),
            'status'          => array(
                'type'     => self::TYPE_BOOL,
                'validate' => 'isBool',
                'db_type'  => 'int',
                'lang'     => false,
            ),
            'created_at'      => array(
                'type'     => self::TYPE_DATE,
                'validate' => 'isDate',
                'db_type'  => 'datetime',
                'lang'     => false,
            ),
            'fulfilled_at'    => array(
                'type'     => self::TYPE_DATE,
                'validate' => 'isDate',
                'db_type'  => 'datetime',
                'lang'     => false,
            ),
        ),
    );

    /**
     * @throws \PrestaShopModuleException
     */
    public function getCustomer()
    {
        $customer = new \Customer($this->id_customer);

        if (!\Validate::isLoadedObject($customer)) {
            throw new \PrestaShopModuleException("Can't find the customer related to this request");
        }

        return $customer;
    }

    /**
     * Fulfill the request
     *
     * @return bool
     * @throws \PrestaShopException
     */
    public function fulfill()
    {
        $dataRequest = new \DataRequest($this->id);
        $dataRequest->status = 1;
        $dataRequest->fulfilled_at = date('Y-m-d H:i:s');
        return $dataRequest->update();
    }
}
