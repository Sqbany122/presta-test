<?php
/**
*    2007-2017 PrestaShop
*
*    NOTICE OF LICENSE
*
*    This source file is subject to the Academic Free License (AFL 3.0)
*    that is bundled with this package in the file LICENSE.txt.
*    It is also available through the world-wide-web at this URL:
*    http://opensource.org/licenses/afl-3.0.php
*    If you did not receive a copy of the license and are unable to
*    obtain it through the world-wide-web, please send an email
*    to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*    @author    PrestaShop SA <contact@prestashop.com>
*    @copyright 2007-2017 PrestaShop SA
*    @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*/

include_once(_PS_MODULE_DIR_.'advancedordernotes/advancedordernotes.php');
include_once(_PS_MODULE_DIR_.'advancedordernotes/libraries/order_notes.php');


class AdminAdvancedordernotesDashboardController extends ModuleAdminControllerCore
{


    public function createTemplate($tpl_name) 
    {

        if ($this->viewAccess() && $this->override_folder) {
            if (file_exists($this->context->smarty->getTemplateDir(1) . DIRECTORY_SEPARATOR . $this->override_folder . $tpl_name))
                return $this->context->smarty->createTemplate($this->override_folder . $tpl_name, $this->context->smarty);
            elseif (file_exists($this->context->smarty->getTemplateDir(0) . 'controllers' . DIRECTORY_SEPARATOR . $this->override_folder . $tpl_name))
                return $this->context->smarty->createTemplate('controllers' . DIRECTORY_SEPARATOR . $this->override_folder . $tpl_name, $this->context->smarty);
        }
        return $this->context->smarty->createTemplate(_PS_MODULE_DIR_.'advancedordernotes\views\templates\admin\orderdashboard/'. $tpl_name, $this->context->smarty);
    }


    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->table = 'order_notes';
        $this->identifier = 'id_order_notes';
        $this->className = 'OrderNotesList';
        $this->lang = true;
        $this->tpl_folder = 'orderdashboard';



        $aon_token =  Configuration::get('aon_token');
        $page = (int)Tools::getValue('page');

        if($page <= 0)
            $page = 1;


        $total_pages = OrderNotesCore::get_pagination_count();

        $cookie = new Cookie('psAdmin', '', (int)Configuration::get('PS_COOKIE_LIFETIME_BO'));
        $token = Tools::getAdminToken('AdminAdvancedordernotesDashboard'.(int)(Tab::getIdFromClassName('AdminAdvancedordernotesDashboard')).(int)($cookie->id_employee));
        $token_orders =  Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee));


        $currentIndex ='index.php?controller=AdminAdvancedordernotesDashboard&token='.$token.'&page=';


        $notes  = OrderNotesCore::get_latest_notes($page);

        $this->context->smarty->assign(array(
            'aon_token' =>  $aon_token,
            'page' => $page,
            'token_orders' => $token_orders,
            'total_pages' => $total_pages,
            'current_index2' => $currentIndex,
            'notes' => $notes
        ));


        $this->setTemplate('order_dashboard.tpl');

    }
        


   
}
