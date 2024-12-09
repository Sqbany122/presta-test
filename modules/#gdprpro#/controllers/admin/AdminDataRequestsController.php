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

use PrestaChamps\GdprPro\Models\DataRequest;
use \PrestaChamps\GdprPro\Commands\AnonymizeDataCommand;

/**
 * Class AdminDataRequestsController
 *
 * Handle customer data requests
 */
class AdminDataRequestsController extends ModuleAdminController
{
    use \PrestaChamps\GdprPro\Traits\CollectCustomerDataTrait;

    public $action = array(
        'fulfill',
    );

    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;
        $this->table = DataRequest::$definition['table'];
        $this->className = DataRequest::class;

        $this->actions = array('edit');
        $this->identifier = 'id_data_request';
//        $this->bulk_actions = [
//            'fulfill' => [
//                'text'    => $this->module->l('Fulfill selected'),
//                'icon'    => 'icon-trash',
//                'confirm' => $this->module->l('Fulfill selected items?'),
//            ],
//        ];
        $this->_orderBy = DataRequest::$definition['primary'];
        $this->_orderWay = 'DESC';
        $this->_select = "
		a.*,
		IF(type = 1, '#4169E1', '#DC143C') AS color,
		IF(type = 1, '{$this->module->l('Request my data')}', '{$this->module->l('Delete my data')}') AS type_string,
		CONCAT(LEFT(c.`firstname`, 1), '. ', c.`lastname`) AS `customer`";

        $this->_join = '
		LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)';
        $this->fields_list = array(
            'id_data_request' => array(
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'title' => 'ID',
                'type'  => 'text',
            ),
            'customer'        => array(
                'title'        => $this->module->l('Customer'),
                'havingFilter' => true,
            ),
            'id_customer'     => array(
                'title'        => $this->module->l('Customer ID'),
                'havingFilter' => true,
            ),
            'created_at'      => array(
                'title' => $this->module->l('Created at'),
                'type'  => 'date',
            ),
            'type_string'     => array(
                'title'      => $this->module->l('Request type'),
                'type'       => 'select',
                'list'       => array(
                    DataRequest::REQUEST_TYPE_EXPORT   => $this->module->l('Export'),
                    DataRequest::REQUEST_TYPE_DELETION => $this->module->l('Deletion'),
                ),
                'filter_key' => 'type',
                'color'      => 'color',
                'class'      => 'fixed-width-xs',
                'align'      => 'center',
            ),
            'status'          => array(
                'title' => $this->module->l('Fulfillment status'),
                'type'  => 'bool',
                'align' => 'center',
            ),
        );
    }

    /**
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws PrestaShopModuleException
     * @throws SmartyException
     */
    public function renderForm()
    {
        $parent = parent::renderForm();
        /**
         * @var $object DataRequest
         */
        $object = $this->object;
        try {
            $this->customer = $object->getCustomer();
        } catch (\PrestaShopModuleException $exception) {
            $this->errors[] = $this->l("Can't find customer");
            Tools::displayError("Can't find customer. It has been deleted?");
            die();
        }
        $this->collectData();
        $customerData = array();
        $customerData['orders'] = Order::getCustomerOrders($this->customer->id);
        $customerData = $this->formatData();

        $this->context->smarty->assign(array(
            'customer'             => $this->customer,
            'request'              => $object,
            'deleteLink'           => $this->getAdminLink(
                'AdminDataRequests',
                true,
                array(),
                array(
                    'action'           => 'fulfill',
                    'requestToFulfill' => $object->id,
                )
            ),
            'downloadInvoicesLink' => $this->getAdminLink(
                'AdminDataRequests',
                true,
                array(),
                array(
                    'action'   => 'downloadInvoices',
                    'customer' => $object->id_customer,
                )
            ),
            'numberOfInvoices'     => $this->getNumberOfInvoices(),
            'customerData'         => $customerData,
            'activities'           => $this->getActs($this->customer->id),
        ));
        if ($object->type == DataRequest::REQUEST_TYPE_DELETION) {
            $parent .= $this->context->smarty->fetch($this->getTemplatePath() . 'delete-request-view.tpl');
        } else {
            $parent .= $this->context->smarty->fetch($this->getTemplatePath() . 'export-request-view.tpl');
        }
        return $parent;
    }

    public function processFulfill()
    {
        try {
            $object = new DataRequest(Tools::getValue('requestToFulfill'));
            if (!Validate::isLoadedObject($object)) {
                throw new Exception('Unknown object');
            }
            $command = new AnonymizeDataCommand(new Customer($object->getCustomer()->id), $this->context);
            $command->execute();
            $object->fulfill();
            $this->confirmations[] = "Client anonymised";
        } catch (Exception $exception) {
            $this->errors[] = $exception->getMessage();
        }
    }

    public function processDownloadInvoices()
    {
        try {
            $customerId = Tools::getValue('customer');
            $customer = new Customer((int)$customerId);
            $command = new \PrestaChamps\GdprPro\Commands\GenerateInvoicesPdfCommand($customer, $this->context);
            $command->execute();
        } catch (Exception $exception) {
            $this->errors[] = $exception->getMessage();
        }
    }

    /**
     * @return int
     */
    protected function getNumberOfInvoices()
    {
        // Number of invoices
        $id_customer = pSQL($this->customer->id);
        $query = new \DbQuery();
        $query->select('count(*)');
        $query->from('order_invoice', 'order_invoice');
        $query->leftJoin('orders', 'orders', 'orders.id_order = order_invoice.id_order');
        $query->where("orders.id_customer = {$id_customer} AND order_invoice.number > 0");

        return (int)\Db::getInstance()->getValue($query);
    }


    /**
     * Create admin link with params
     *
     * The ps 16 version of link->getAdmin link doesn't support parameters, so instead of that you can use this
     *
     * @param       $controller
     * @param array $params
     *
     * @return string
     * @throws PrestaShopException
     */
    protected function getAdminLink($controller, $withToken = true, $sfRouteParams = array(), $params = array())
    {
        if (GdprPro::isPs17()) {
            return $this->context->link->getAdminLink($controller, $withToken, $sfRouteParams, $params);
        }
        $params['token'] = Tools::getAdminTokenLite($controller);
        return Dispatcher::getInstance()->createUrl(
            $controller,
            Context::getContext()->language->id,
            $params,
            false
        );
    }

    public function getActs($customerId)
    {
        $customerId = pSQL($customerId);
        $query = new DbQuery();
        $query->select('id_gdpr_activity_log');
        $query->from('gdpr_activity_log');
        $query->where("id_customer = {$customerId}");
        $result = Db::getInstance()->executeS($query);
        $return = array();
        foreach ($result as $item) {
            $return[] = new GdprActivityLog($item['id_gdpr_activity_log']);
        }
        return $return;
    }

    public function initToolbar()
    {
    }
}
