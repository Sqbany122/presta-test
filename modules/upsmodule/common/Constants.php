<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

class Constants
{
    const DB_TABLE_SHIPMENT    = 'ups_shipment';
    const DB_TABLE_OPENORDER   = 'ups_openorder';
    const PACKAGE_TYPE_CODE    = '02';

    const NAME_LABEL_SHIPMENT = 'LabelShipment';
    const NAME_COD_PU_FORM    = 'CODRegistration_';
    const NAME_PICKUP_PU_FORM = 'PickUpRegistration_';

    const DUMMY_PHONE_NUMBER = '0000000000';
    const DUMMY_MAIL         = 'mail@mail.com';
    const PS_COD_MODULE      = PS_COD_MODULE;

    const RATE_API_REQ_OPT_SHOP = 'SHOP';
    const RATE_API_REQ_OPT_STIT = 'Shoptimeintransit';
    const RATE_API_REQ_OPT_RATE = 'RATE';
    const RATE_API_REQ_OPT_TIME = 'RATETIMEINTRANSIT';

    const PREFIX_CSV_OPEN_ORDER = 'Orders_data_';
    const PREFIX_CSV_SHIPMENT   = 'Shipments_data_';

    const PREG_POSTALCODE = '/[^A-Za-z0-9]/';

    const COUNTRY_CODE_EU = array(
        'BE', 'NL', 'FR', 'ES', 'PL', 'IT', 'DE', 'GB',
    );

    const POLAND_ISO = 'PL';
    const UNITED_STATE_ISO = 'US';

    const TO_BE_ARCHIVED = 90;
    const TO_BE_DELETED  = 90;

    const SERVICE_OFFERING_001 = '001';
    const SERVICE_OFFERING_011 = '011';

    const FORMAT_TIME_ESHOPER = 'g:i A';

    /**
     * CONDITION-UPDATING
     * Still UPDATING on the condition
     * UPS
     * #22-08-2018
     */
    const STATUS_OPEN_ORDER                 = 1;
    const STATUS_ARCHIVED_ORDERS            = 0;
    const STATUS_AWAITING_CHECK_PAYMENT     = 1;
    const STATUS_PAYMENT_ACCEPTED           = 2;
    const STATUS_PROCESSING_IN_PROGRESS     = 3;
    const STATUS_SHIPPED                    = 4;
    const STATUS_DELIVERED                  = 5;
    const STATUS_ORDER_CANCELED             = 6;
    const STATUS_REFUNDED                   = 7;
    const STATUS_PAYMENT_ERROR              = 8;
    const STATUS_ORDER_PAID                 = 9;
    const STATUS_AWAITING_BANK_WIRE_PAYMENT = 10;
    const STATUS_REMOTE_PAYMENT_ACCEPTED    = 11;
    const STATUS_ORDER_NOT_PAID             = 12;
    const STATUS_COD_VALIDATION             = 13;
    const STATUS_CREATE_SHIPMENT            = 14;

    const ALTERNATE_INDICATOR_FLAG     = 1;
    const ALTERNATE_INDICATOR_FLAG_OFF = 0;

    const PROMO_CODE = 'CO0Z3ERO1';

    const SEARCH_ADDRESS_POINT_URL  = 'https://www.ups.com/dropoff';
    const LINK_DOHANDSHAKE = 'index.php?controller=servicelink&fc=module&module=upsmodule&ajax=1&action=DoHandshake';

    const CONFIG_SCREEN_STATUS = array(
        array(
            'class_name' => 'AdminUpsAccount',
            'status'     => 0,
        ),
        array(
            'class_name' => 'AdminUpsShippingServices',
            'status'     => 0,
        ),
        array(
            'class_name' => 'AdminUpsCod',
            'status'     => 0,
        ),
        array(
            'class_name' => 'AdminUpsPkgDimension',
            'status'     => 0,
        ),
        array(
            'class_name' => 'AdminUpsDeliveryRates',
            'status'     => 0,
        ),
        array(
            'class_name' => 'AdminUpsBillPref',
            'status'     => 0,
        ),
    );

    // List Configuration
    const LIST_CONFIGURATION = array(
        'UPS_CONFIG_DONE'                         => 0,
        'UPS_CONFIG_SCREEN_STATUS'                => array(),
        // Carrier
        'UPS_SHIPING_METHOD_ID'                   => -1,
        'UPS_SHIPING_METHOD_REFERENCE_ID'         => -1,
        // Account
        'UPS_ACCOUNT_EXIST'                       => 0,
        // Country
        'UPS_COUNTRY_SELECTED'                    => 'PL',
        'UPS_READY_TO_GET_TC'                     => 0,
        // TC
        'UPS_TC_AGREED'                           => 0,
        'UPS_TC_FIRST_TIME_CONFIG'                => 0,
        // COD
        'UPS_MODULE_COD_ENABLE'                   => 0,
        'UPS_COD_ENABLE'                          => 0,
        // Shipping Service Other
        'UPS_SP_SERV_AP_DELIVERY'                 => 0,
        'UPS_SP_SERV_SET_DEFAULT_SHIPPING_OPTION' => 1,
        'UPS_SP_SERV_AP_CHOOSE_ACC'               => 1,
        'UPS_SP_SERV_AP_NUM_VISIBLE'              => 5,
        'UPS_SP_SERV_AP_RANGE_DISPLAY'            => 10,
        'UPS_SP_SERV_ADDRESS_DELIVERY'            => 0,
        'UPS_SP_SERV_ADDRESS_CHOOSE_ACC'          => 1,
        'UPS_SP_SERV_CUT_OFF_TIME'                => '17',
        'UPS_SP_SERV_EXIST'                       => 0,
        // Pkg Dimension
        'UPS_PKG_DIMENSION_COUNT'                 => array(),
        // Delivery Rates
        'UPS_DELI_CURRENCY'                       => 'EUR',
        'UPS_DELIVERYRATES_EXIST'                 => 0,
        // Accessorials
        'UPS_ACCESSORIALS_EXIST'                  => 0,
        // Merchant
        'UPS_MERCHANTINFO_EXIST'                  => 0,
        // Security
        'UPS_SEC_CLICKJACKING'                    => 1,
        'UPS_SEC_X_FRAME_OPTIONS'                 => 1,
        'UPS_SEC_FRAME_KILLER'                    => 1,
        'UPS_SEC_CONTENT_SEC_POLICY'              => 1,
        'UPS_SEC_CONTENT_SNIFFING'                => 0,
        'UPS_SEC_CROSS_SITE'                      => 0,
        'UPS_SEC_STRICT_TRANSPORT'                => 0,
        'UPS_SEC_FROM_CACHING'                    => 0,
        'UPS_SEC_APPLY'                           => 0,
        'REGISTERED_TOKEN' => null,
        'UPS_PASS' => null,
        'MERCHANT_KEY' => null,
        'SECURITY_TOKEN' => null,
        'MY_UPS_ID' => null,
        'UPS_LICENSE' => null,
        'PRE_KEY' => null,
        'SERVICE_DATA' => null,
        'BINGMAPS_KEY' => null
    );

    const LIST_CURRENCY = array(
        'AED' => 'Arab Emirates Dirham',
        'ARS' => 'Argentina Peso',
        'AUD' => 'Australian Dollar',
        'BBD' => 'Barbados Dollar',
        'BHD' => 'Bahrain Dinar',
        'BRL' => 'Brazilian Real',
        'BYN' => 'Belarus Ruble',
        'CAD' => 'Canadian Dollar',
        'CHF' => 'Swiss Franc',
        'CLP' => 'Chilean Peso',
        'CNY' => 'China Renminbi Yuan',
        'COP' => 'Colombian Peso',
        'CRC' => 'Costa Rican Colon',
        'CZK' => 'Czech Koruna',
        'DKK' => 'Danish Kroner',
        'DOP' => 'Dom Rep Peso',
        'EUR' => 'Euro',
        'GBP' => 'Pound Sterling',
        'HKD' => 'Hong Kong Dollar',
        'HUF' => 'Hungarian Forint',
        'IDR' => 'Indonesian Rupiah',
        'INR' => 'Indian Rupee',
        'JPY' => 'Japanese Yen',
        'KWD' => 'Kuwait Dinar',
        'KRW' => 'Korean Won',
        'KZT' => 'Kazakhstan Tenge',
        'MAD' => 'Morocco Dirham',
        'MOP' => 'Macau Pataca',
        'MXN' => 'Mexican Peso',
        'MYR' => 'Malaysian Ringgit',
        'NGN' => 'Nigerian Naira',
        'NOK' => 'Norway Kroner',
        'NZD' => 'New Zealand Dollar',
        'PAB' => 'Panamanian Balboa',
        'PHP' => 'Philippine Peso',
        'PLN' => 'Polish Zloty',
        'RON' => 'Romanian Leu',
        'RUB' => 'Russia Ruble',
        'SEK' => 'Swedish Kroner',
        'SGD' => 'Singapore Dollar',
        'THB' => 'Thailand Baht',
        'TRY' => 'Turkey',
        'TWD' => 'Taiwan Dollar',
        'VND' => 'Vietnam đồng',
        'UAH' => 'Ukraine Hyrvnya',
        'USD' => 'U.S. Dollar',
        'ZAR' => 'South African Rand',
    );
}
