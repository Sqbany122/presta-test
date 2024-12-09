<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 19.12.13
 * Time: 11:52
 *
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author Andreika
 *  @copyright  Andreika
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

include_once(_PS_MODULE_DIR_.'/productstatus/productstatus.php');

class AdminProductStatusController extends ModuleAdminController
{
    public $modules_list = array();

    public function __construct()
    {
        $cookie = context::getContext()->cookie;
        $this->context = Context::getContext();
        $this->id_employee = $cookie->__get('id_employee');
        $this->eployeeMail = $cookie->__get('email');
        $this->table = 'order_detail';
        $this->_select = 'sa.`quantity` AS `stock`,';
        /*
        $this->_select .= ' DATE_FORMAT(sd.`supplier_delivery`, \'%d.%m.%Y\') AS `supplier_delivery`,
                            DATE_FORMAT(sd.`scheduled_shipping`, \'%d.%m.%Y\') AS `scheduled_shipping`,';
        */

        $this->_select .= ' IF (sd.`supplier_delivery` IS NULL, \'\', sd.`supplier_delivery`) AS `supplier_delivery`,
                            IF (sd.`scheduled_shipping` IS NULL, \'\', sd.`scheduled_shipping`) AS `scheduled_shipping`,
                            IF (sd.`tracking_url` IS NULL, \'\', sd.`tracking_url`) AS `tracking_url`,
                            IF (sd.`comment` IS NULL, \'\', sd.`comment`) AS `comment`,';
        /*
        $this->_select .= ' IF (sd.`supplier_delivery` IS NULL, \'\', DATE_FORMAT(sd.`supplier_delivery`, \'%d.%m.%Y\')) AS `supplier_delivery`,
                            IF (sd.`scheduled_shipping` IS NULL, \'\', DATE_FORMAT(sd.`scheduled_shipping`, \'%d.%m.%Y\')) AS `scheduled_shipping`,';
        */

        /*
        $this->_select .= ' IFNULL (DATE_FORMAT(sd.`supplier_delivery`, \'%d.%m.%Y\'),
                                IF (sa.`quantity` = 0,
                                DATE_FORMAT( (DATE_ADD(o.date_add, INTERVAL pl.`available_later` DAY)), \'%d.%m.%Y\'),
                                DATE_FORMAT(o.date_add, \'%d.%m.%Y\'))) AS `supplier_delivery`,
                            IFNULL (DATE_FORMAT(sd.`scheduled_shipping`, \'%d.%m.%Y\'),
                                IF (sa.`quantity` = 0,
                                DATE_FORMAT((DATE_ADD(o.date_add, INTERVAL pl.`available_later` DAY)),  \'%d.%m.%Y\'),
                                DATE_FORMAT(o.date_add, \'%d.%m.%Y\'))) AS `scheduled_shipping`,';
        */
        /*
        $this->_select .= ' IFNULL (sd.`supplier_delivery`,
                                IF (sa.`quantity` = 0,
                                (DATE_ADD(o.date_add, INTERVAL pl.`available_later` DAY)),
                                o.date_add)) AS `supplier_delivery`,
                            IFNULL (sd.`scheduled_shipping`,
                                IF (sa.`quantity` = 0,
                                (DATE_ADD(o.date_add, INTERVAL pl.`available_later` DAY)),
                                o.date_add)) AS `scheduled_shipping`,';
        */
        $this->_select .= ' a.`id_order_detail` AS `id`,
                            o.`id_order` AS `order`,
                            o.`reference`, ps.`id_product_state`, osl.`name` AS `status_name`,
                            (a.`product_price` + (a.`product_price`/100 * tax.`rate`) - a.reduction_amount) AS `full_price`,
                            ((a.`product_price` + (a.`product_price`/100 * tax.`rate`) - a.reduction_amount) * a.`product_quantity`) AS `total_price`,
                            CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`, c.email AS email, os.`color` AS state_color, o.date_add AS `date_add` ';
        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'product_status_dates AS sd ON (sd.`id_order_detail` = a.`id_order_detail`)
                        LEFT JOIN '._DB_PREFIX_.'stock_available AS sa ON (sa.`id_product` = a.`product_id`)
                        LEFT JOIN '._DB_PREFIX_.'product_lang AS pl ON (pl.`id_product` = a.`product_id`)
                        LEFT JOIN '._DB_PREFIX_.'orders AS o ON (o.id_order = a.`id_order`)
                        LEFT JOIN '._DB_PREFIX_.'customer AS c ON (o.id_customer = c.id_customer)
                        LEFT JOIN '._DB_PREFIX_.'product_status AS ps ON (ps.`id_order_detail` = a.`id_order_detail`)
                        LEFT JOIN '._DB_PREFIX_.'order_state AS os ON (ps.`id_product_state` = os.`id_order_state`)
                        LEFT JOIN '._DB_PREFIX_.'order_state_lang AS osl ON (ps.`id_product_state` = osl.`id_order_state`)
                        LEFT JOIN '._DB_PREFIX_.'order_detail_tax AS detailtax ON (detailtax.`id_order_detail` = a.`id_order_detail`)
                        LEFT JOIN '._DB_PREFIX_.'tax AS tax ON (tax.`id_tax` = detailtax.`id_tax`)';

        $this->_where = ' AND o.`id_order` IS NOT NULL ';
        $this->_where .= ' AND osl.`id_lang` = '.$cookie->__get('id_lang');
        $this->_where .= ' AND pl.`id_lang` = '.$cookie->__get('id_lang');
        //$this->_where .= ' AND sa.`id_shop` = 1';
        $this->_where .= ' AND ps.`id_history` = (SELECT MAX(`id_history`) FROM `'._DB_PREFIX_.'product_status` AS mps WHERE mps.`id_order_detail` = a.`id_order_detail` GROUP BY mps.`id_order_detail`)';


        //$this->_where .= ' GROUP BY a.`id_order_detail`';
        $this->_group = 'GROUP BY a.`id_order_detail`';
        $this->className = 'productStatus';
        $this->bootstrap = true;
        $this->_orderWay = 'DESC';

       //$this->toolbar_btn = array();
        //$this->page_header_toolbar_btn = array();

        $this->lang = false;
        $this->view = false;
        $this->lang = false;
        $this->edit = false;
        $this->delete = false;
        $statusArray = array();
        $statuses = OrderState::getOrderStates((int)($cookie->__get('id_lang')));
        foreach ($statuses as $state)
        {
            $statusArray[$state['id_order_state']] = $state['name'];
        }

        $this->statusArray = $statusArray;
        $this->has_bulk_actions = false;
        $this->colorOnBackground = true;
        $productStatus = new productStatus();
        $this->fields_list = $productStatus->getArraySelectedFields();

        /*
        $this->fields_list = array(
            'product_id' => array(
                'title' => $this->l('ID product'),
                'align' => 'left',
                'width' => 25),
            'reference' => array(
                'title' => $this->l('Reference'),
                'align' => 'left',
                'width' => 100),
            'id_order' => array(
                'title' => $this->l('ID order'),
                'align' => 'left',
                'width' => 25),
            'customer' => array(
                'title' => $this->l('Client'),
                'width' => 100,
                'tmpTableFilter' => true),
            'product_reference' => array(
                'title' => $this->l('Product reference'),
                'width' => 100),
            'product_name' => array(
                'title' => $this->l('Product'),
                'width' => 200),
            'full_price' => array(
                'title' => $this->l('Price per item'),
                'width' => 50,
                'price' => true,
                'align' => 'right',
                'tmpTableFilter' => true),
            'product_quantity' => array(
                'title' => $this->l('quantity'),
                'width' => 25,
                'align' => 'center'),
            'total_price' => array(
                'title' => $this->l('Total price'),
                'width' => 50,
                'price' => true,
                'align' => 'right',
                'tmpTableFilter' => true,
                'prefix' => '<b>',
                'suffix' => '</b>'),
            'status_name' => array(
                'title' => $this->l('Status'),
                'type' => 'select',
                'list' => $statusArray,
                'width' => 200,
                'align' => 'left',
                'filter_key' => 'ps!id_product_state',
                'filter_type' => 'int',
                'remove_onclick' => false),

            'date_add' => array(
                'title' => $this->l('Date'),
                'width' => 35,
                'align' => 'right',
                'type' => 'datetime',
                'filter_key' => 'o!date_add'),

            'supplier_delivery' => array(
                'title' => $this->l('Supplier delivery'),
                'type' => 'date',
                'type2' => 'editable',
                'width' => 75,
                'align' => 'right',
                'tmpTableFilter' => true,
                'remove_onclick' => false),

            'scheduled_shipping' => array(
                'title' => $this->l('Scheduled shipping'),
                'type' => 'date',
                'type2' => 'editable',
                'width' => 75,
                'align' => 'right',
                'tmpTableFilter' => true,
                'remove_onclick' => false),

        );
        */
        $this->identifier = 'id_order';
        // отключил столбец МАГАЗАИН
        //$this->shopLinkType = 'shop';
        //$this->shopShareDatas = Shop::SHARE_ORDER;

        parent::__construct();

    }

    public function renderList()
    {
        $cookie = context::getContext()->cookie;
        if (!($this->fields_list && is_array($this->fields_list)))
            return false;

        $this->getList($this->context->language->id);
        //tools::dieObject($this->_listsql);
        $this->tpl_list_vars['statuses'] = OrderState::getOrderStates((int)($cookie->__get('id_lang')));
        $this->tpl_list_vars['has_bulk_actions'] = $this->has_bulk_actions;
        $this->tpl_list_vars['ps16'] = version_compare(_PS_VERSION_, '1.6.0', '>=');
        $helper = new HelperList();

        // Empty list is ok
        if (!is_array($this->_list))
        {
            $this->displayWarning($this->l('Bad SQL query', 'Helper').'<br />'.htmlspecialchars($this->_list_error));
            return false;
        }

        $this->setHelperDisplay($helper);
        $helper->tpl_vars = $this->tpl_list_vars;
        $helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;

        // For compatibility reasons, we have to check standard actions in class attributes
        foreach ($this->actions_available as $action)
        {
            if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action)
                $this->actions[] = $action;
        }
        $helper->is_cms = $this->is_cms;

        //tools::dieObject($this->_list);
        $list = $helper->generateList($this->_list, $this->fields_list);
        return $list;
    }

    public function display()
    {
        $cookie = context::getContext()->cookie;
        //if ($this->includeSubTab('display', array('submitAdd2', 'add', 'update', 'view'))){}
        if (tools::getIsset('addorder_detail'))
        {
            tools::redirectAdmin('index.php?controller=AdminOrders&addorder&token='.tools::getAdminTokenLite('AdminOrders'));
        }
        if (tools::getIsset('id_order'))
        {
            tools::redirectAdmin('index.php?controller=AdminOrders&id_order='.tools::getValue('id_order').'&vieworder&token='.tools::getAdminTokenLite('AdminOrders'));
        }

        $this->getList((int)($cookie->__get('id_lang')), !Tools::getValue($this->table.'Orderby') ? 'id_order' : NULL, !Tools::getValue($this->table.'Orderway') ? 'DESC' : NULL);

        $this->prepareListWithProductStatuses();

        return parent::display();

    }

    public function prepareListWithProductStatuses()
    {
        $cookie = context::getContext()->cookie;
        foreach ($this->_list as $key => $order_detail)
        {
            $state = productStatus::getLastState($order_detail['id_order_detail'], $cookie->__get('id_lang'));

            if (!$state) continue;
            $this->_list[$key]['id_order_state'] = $state['id_order_state'];
            $this->_list[$key]['state_color'] = $state['color'];
            $this->_list[$key]['status_name'] = $state['name'];
        }
    }

} 