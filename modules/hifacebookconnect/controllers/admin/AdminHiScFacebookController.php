<?php
/**
* 2013 - 2018 HiPresta
*
* MODULE Facebook Connect
*
* @author    HiPresta <suren.mikaelyan@gmail.com>
* @copyright HiPresta 2018
* @license   Addons PrestaShop license limitation
* @link      http://www.hipresta.com
*
* NOTICE OF LICENSE
*
* Don't use this module on several shops. The license provided by PrestaShop Addons
* for all its modules is valid only once for a single shop.
*/

class AdminHiScFacebookController extends ModuleAdminController
{
    public function __construct()
    {
        $this->ajax = Tools::getValue('ajax');
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        if ($this->ajax) {
            if (Tools::getValue('action') == 'delete_table') {
                $table_id = (int)Tools::getValue('table_id');
                Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'hifacebookusers WHERE id='.$table_id);
            }
            if (Tools::getValue('action') == 'delete_full') {
                $table_id = (int)Tools::getValue('table_id');
                $email = Db::getInstance()->ExecuteS('SELECT email FROM '._DB_PREFIX_.'hifacebookusers WHERE id='.$table_id);
                if (!empty($email)) {
                    $id_customer = Db::getInstance()->ExecuteS('SELECT id_customer FROM '._DB_PREFIX_.'customer WHERE email=\''.$email[0]['email'].'\'');
                    if (!empty($id_customer)) {
                        $customer = new Customer($id_customer[0]['id_customer']);
                        $customer->delete();
                    }
                }
                Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'hifacebookusers WHERE id='.$table_id);
            }
        } else {
            Tools::redirectAdmin($this->module->HiPrestaClass->getModuleUrl());
        }
    }
}
