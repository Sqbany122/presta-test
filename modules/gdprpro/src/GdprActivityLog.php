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

/**
 * Class GdprActivityLog
 *
 * These objects hold the logs whatever a customer has made some kind of activity related to the GDPR regulation
 */
class GdprActivityLog extends CustomObjectModel
{
    const ACTIVITY_TYPE_COOKIE_ACCEPT  = 0;
    const ACTIVITY_TYPE_REGISTRATION   = 1;
    const ACTIVITY_TYPE_PROFILE_UPDATE = 2;
    const ACTIVITY_TYPE_CONTACT_ACCEPT = 3;
    const ACTIVITY_TYPE_NEWSLETTER_ACCEPT = 4;

    public static $definition = array(
        'table'     => 'gdpr_activity_log',
        'primary'   => 'id_gdpr_activity_log',
        'multilang' => false,
        'fields'    => array(
            'id_gdpr_activity_log' => array(
                'type'     => self::TYPE_INT,
                'validate' => 'isInt',
                'lang'     => false,
            ),
            'id_customer'          => array(
                'type'     => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'db_type'  => 'int',
                'lang'     => false,
            ),

            'id_guest'         => array(
                'type'     => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'db_type'  => 'int',
                'lang'     => false,
            ),
            'activity_type'    => array(
                'type'     => self::TYPE_INT,
                'validate' => 'isInt',
                'db_type'  => 'int',
                'lang'     => false,
            ),
            'activity_subject' => array(
                'type'     => self::TYPE_STRING,
                'validate' => 'isString',
                'db_type'  => 'text',
                'lang'     => false,
            ),
            'activity_data'    => array(
                'type'     => self::TYPE_STRING,
                'validate' => 'isString',
                'db_type'  => 'text',
                'lang'     => false,
            ),
            'date_add'         => array(
                'type'     => self::TYPE_DATE,
                'validate' => 'isDate',
                'lang'     => false,
            ),
        ),
    );

    /**
     * @var $id_gdpr_activity_log int
     */
    public $id_gdpr_activity_log;

    /**
     * @var $id_customer int
     */
    public $id_customer;

    /**
     * @var $id_guest int
     */
    public $id_guest;

    /**
     * @var $type int
     */
    public $activity_type;

    /**
     * @var $subject string
     */
    public $activity_subject;

    /**
     * @var $data string
     */
    public $activity_data;

    /**
     * @var $date_add string
     */
    public $date_add;

    /**
     * @var $data boolean
     */
    public $isJson;

    /**
     * @var $data string
     */
    public $jsonHTMLTable;

    /**
     * @param $customerId
     *
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function activitiesByCustomer($customerId)
    {
        $customerId = pSQL($customerId);
        $query = new DbQuery();
        $query->select('id_gdpr_activity_log');
        $query->from('gdpr_activity_log');
        $query->where("id_customer = {$customerId}");
        $result = Db::getInstance()->executeS($query);
        $return = array();
        foreach ($result as $item) {
            $activity_log = new GdprActivityLog($item['id_gdpr_activity_log']);
            
            $activity_log->isJson = $activity_log->isJSON($activity_log->activity_data);
            
            if ($activity_log->isJson) {
                $activity_log->jsonHTMLTable = $activity_log->createTable($activity_log->activity_data);
            }

            $return[] = $activity_log;
        }
        return $return;
    }

    public function isJSON($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    public function createTable($json)
    {
        $jsonData = json_decode($json, true);

        $rows = '';

        $tr = '<tr><td class=json-key>';
        $tr_end = '<td class=json-string>';
        $check = '&#10004;';
        $cross = '&#10006;';

        foreach ($jsonData as $key => $value) {
            if ($value == 'true') {
                $rows .= $tr . $key . '</td>' . $tr_end . $check . '</td></tr>';
            } else {
                $rows .= $tr . $key . '</td>' . $tr_end . $cross . '</td></tr>';
            }
        }
        return $rows;
    }
}
