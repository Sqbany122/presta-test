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
 * Class AdminGdprLogController
 */
class AdminGdprLogController extends ModuleAdminController
{
    public $actions_available = array();

    public function __construct()
    {
        parent::__construct();
        $this->context->controller->addJS($this->module->getLocalPath() . '/views/js/consent-log.js');
        $this->context->controller->addCSS($this->module->getLocalPath() . '/views/css/consent-log.css');
        $this->noLink = true;
        $this->list_no_link = true;
        $this->bootstrap = true;
        $this->table = GdprActivityLog::$definition['table'];
        $this->className = GdprActivityLog::class;
        $this->identifier = GdprActivityLog::$definition['primary'];
        $this->_orderBy = GdprActivityLog::$definition['primary'];
        $this->_orderWay = 'DESC';
        $this->_select = "
		a.*, IF(a.id_customer > 0, 'customer','guest') as 'type',
		CONCAT(LEFT(c.`firstname`, 1), '. ', c.`lastname`) AS `customer`";
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );
        $this->_join = '
		LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)';
        $this->fields_list = array(
            'id_gdpr_activity_log' => array(
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'title' => 'ID',
                'type'  => 'text',
            ),
            'type'             => array(
                'title'        => $this->l('Type'),
                'list'         => array(
                    'guest'  => $this->l("Guest"),
                    'customer'   => $this->l("Customer"),
                ),
                'filter_key'   => 'type',
                'havingFilter' => true,
                'type'         => 'select',
            ),
            'id_guest'             => array(
                'title'        => $this->l('Guest'),
                'havingFilter' => true,
            ),
            'customer'             => array(
                'title'        => $this->l('Customer'),
                'havingFilter' => true,
            ),
            'activity_subject'     => array(
                'title'        => $this->l('Subject'),
                'filter_key'   => 'activity_type',
                'list'         => array(
                    GdprActivityLog::ACTIVITY_TYPE_COOKIE_ACCEPT  => $this->l("Cookie accepted"),
                    GdprActivityLog::ACTIVITY_TYPE_REGISTRATION   => $this->l("Signup form consent accepted"),
                    GdprActivityLog::ACTIVITY_TYPE_PROFILE_UPDATE => $this->l("Profile update form consent accepted"),
                ),
                'havingFilter' => true,
                'type'         => 'select',
            ),
            'activity_data'        => array(
                'title'        => $this->l('Data'),
                'havingFilter' => false,
            ),
            'date_add'             => array(
                'title' => $this->l('Created at'),
                'type'  => 'date',
            ),
        );
    }

    public function initToolbar()
    {
    }
}
