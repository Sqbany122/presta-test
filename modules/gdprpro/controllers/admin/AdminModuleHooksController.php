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
 * Class AdminModuleHooksController
 */
class AdminModuleHooksController extends ModuleAdminController
{
    public $bootstrap = true;

    public static $ignorableModules = array(
        'gdprpro',
        'bankwire',
        'blocklayered',
        'blockcms',
        'blockcmsinfo',
        'blockcontact',
        'blockcontactinfos',
        'blockmanufacturer',
        'dashtrends',
        'dashgoals',
        'dashproducts',
        'graphnvd3',
        'productpaymentlogos',
        'pagesnotfound',
        'statsbestcategories',
        'statsbestcustomers',
        'statsbestvouchers',
        'statsbestsuppliers',
        'statscarrier',
        'statscatalog',
        'statscheckup',
        'statsequipment',
        'statsforecast',
        'statslive',
        'statsnewsletter',
        'statsorigin',
        'statspersonalinfos',
        'statsproduct',
        'statsregistrations',
        'statssales',
        'statssearch',
        'statsstock',
        'statsvisits',
        'cronjobs',
        'blocksearch',
        'blockspecials',
        'blockstore',
        'blocksupplier',
        'blocktags',
        'blocktopmenu',
        'blockuserinfo',
        'blockviewed',
        'cheque',
        'dashactivity',
        'dashtrends',
    );

    public function initToolbar()
    {
        $this->page_header_toolbar_btn['gdpr'] = array(
            'short' => $this->l('Back to config'),
            'href'  => $this->context->link->getAdminLink('AdminGdprConfig'),
            'desc'  => $this->l('Configuration'),
            'class' => 'icon-wrench',
        );
    }

    public function initContent()
    {
        $this->bootstrap = true;

        $modules_to_unhook = GdprProConfig::getModulesToUnload();
        $modules = Module::getModulesInstalled();
        $moduleImages = array();
        foreach (Module::getModulesOnDisk(true) as $moduleObj) {
            if (!empty($moduleObj->image)) {
                $moduleImages[$moduleObj->name] = $moduleObj->image;
            }
        }
        foreach ($modules as &$module) {
            if ($module['active'] != 1 || in_array($module['name'], self::$ignorableModules, true)) {
                continue;
            }

            $modules_to_unhook[$module['name']] = array(
                'checked'       => isset($modules_to_unhook[$module['name']]) &&
                    isset($modules_to_unhook[$module['name']]['enabled']) &&
                    $modules_to_unhook[$module['name']]['enabled'] == 1,
                'full_name'     => Module::getModuleName($module['name']),
                'name'          => $module['name'],
                'provider'      => isset($modules_to_unhook[$module['name']]['provider']) ?
                    $modules_to_unhook[$module['name']]['provider'] : "",
                'expiry'        => isset($modules_to_unhook[$module['name']]['expiry']) ?
                    $modules_to_unhook[$module['name']]['expiry'] : "",
                'category'      => isset($modules_to_unhook[$module['name']]['category']) ?
                    $modules_to_unhook[$module['name']]['category'] : "",
                'description'   => isset($modules_to_unhook[$module['name']]['description']) ?
                    $modules_to_unhook[$module['name']]['description'] : "",
                'frontend_name' => isset($modules_to_unhook[$module['name']]['frontend_name']) ?
                    $modules_to_unhook[$module['name']]['frontend_name'] : "",
            );
        }
        
        $this->context->controller->addCSS(
            $this->module->getTemplatePath('/views/css/admin/module-hooks.css')
        );
        
        $this->context->smarty->assign(
            array(
                'modules_to_unhook' => $modules_to_unhook,
                'languages'         => Language::getLanguages(true),
                'moduleImages'      => $moduleImages,
            )
        );

        try {
            $this->content = $this->context->smarty->fetch(
                $this->module->getTemplatePath('views/templates/admin/module-hooks.tpl')
            );
        } catch (Exception $exception) {
            $this->errors[] = $exception->getMessage() .
                "<br>File: {$exception->getFile()}<br>Line: {$exception->getLine()}";
        }
        $this->informations[] = $this->l('Pick and choose which module(s) can be disabled by your website\'s visitors. When you chek "Unhook" the module will be displayed in the pop-up with all the information entered and a checkbox where customers can choose if they want to continue on the website with or without the module being active. Please provide relevant information in order for customers to make an informed decision. Also, if disabling certain modules affects the functionality of the website, please make sure to add this information in the description box.');

        parent::initContent();
    }

    public function postProcess()
    {
        if (((bool)Tools::isSubmit('submit-gdpr-unhook')) == true && $_SERVER['REQUEST_METHOD'] === 'POST') {
            GdprProConfig::setModulesToUnload(Tools::getValue('modules_to_unload'));
            $this->confirmations[] = $this->l("Inactive hooks updated");
        }
    }
}
