<?php
/**
 *    ambBOcustomizer Module : customize the prestashop back-office
 *
 *    @author    Ambris Informatique
 *    @copyright Copyright (c) 2013-2015 Ambris Informatique SARL
 *    @license   Commercial license
 *    @module    BO Customizer (AmbBoCustomizer)
 *    @file      controllers/admin/AdminAmbBoCustomizerParamsController.php
 *    @subject   Manages the configuration system of the module
 *
 *    Support by mail: support@ambris.com
 */

class AdminAmbBoCustomizerParamsController extends AdminController
{

    public $bootstrap = true;
    public static $module_name = 'ambbocustomizer';
    public $module;

    public function __construct()
    {
        $this->module = Module::getInstanceByName('ambbocustomizer');
        parent::__construct();
    }

    public function postProcess()
    {
        parent::postProcess();
    }

    public function renderList()
    {
        $name = Tools::getValue('name', 0);
        if ($name === 0) {
            return $this->renderCustomizableLists();
        } else {
            return $this->renderCustomizableList($name);
        }
    }

    protected function renderCustomizableLists()
    {
        $files = scandir(_PS_MODULE_DIR_ . self::$module_name . '/data/fields');
        $customizable_lists = array();

        foreach ($files as $file) {
            if (preg_match('/(.+)\.json/', $file, $matches)) {
                $controller_name = $matches[1];

                $amb_data = new AmbData($controller_name);
                $amb_data->checkParams();
                //die(print_r($this->context->controller));
                $customizable_lists[$amb_data->getHeaders()->category][] = array(
                    'controller' => $controller_name,
                    'amb_data' => $amb_data,
                    'show_link' => $this->context->link->getAdminLink($controller_name),
                    'edit_link' => $this->context->link->getAdminLink(
                        $this->context->controller->controller_name
                    ) . '&name=' . $controller_name,
                );
            }
        }

        $this->context->smarty->assign('link', $this->context->link);
        $this->context->smarty->assign('customizable_lists', $customizable_lists);
        $this->context->smarty->assign('compat', $this->module->compat);
        $this->context->smarty->assign('success_msg', Translate::getAdminTranslation('Update successful', 'AdminController'));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'ambbocustomizer/views/templates/admin/customizable_lists.tpl'
        ) . $this->module->getDebug();
    }

    protected function renderCustomizableList($name)
    {
        $fields = new AmbData($name);

        $fields->checkParams();

        $this->context->smarty->assign('link', $this->context->link);
        $this->context->smarty->assign('amb_data', $fields);
        $this->context->smarty->assign('fields', $fields->getOrderedFields());

        $this->context->smarty->assign('list_active', $fields->isActive());
        $this->context->smarty->assign('controller', $this);

        $this->context->smarty->assign('current_view_name', $fields->getViewName());
        $this->context->smarty->assign('view_names', $fields->getViewNames());

        $this->context->smarty->assign('compat', $this->module->compat);
        $this->context->smarty->assign('success_msg', Translate::getAdminTranslation('Update successful', 'AdminController'));

        $this->context->smarty->assign('url', $this->context->link->getAdminLink(
            $this->context->controller->controller_name
        ) . '&name=' . $name);
        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'ambbocustomizer/views/templates/admin/edit_customizable_list.tpl'
        );
    }

    public function getTranslation($what, $field, $force_translation = false)
    {
        if (property_exists($field, $what)) {
            if ($force_translation) {
                return htmlentities(
                    Translate::getModuleTranslation(self::$module_name, $field->{$what}, self::$module_name),
                    ENT_QUOTES
                );
            }

            if (AmbData::isCore($field)) {
                return Translate::getAdminTranslation($field->{$what}, Tools::getValue('name'), false, false);
            } elseif (property_exists($field, 'translator')) {
                return Translate::getAdminTranslation($field->{$what}, $field->translator);
            } else {
                return htmlentities(
                    Translate::getModuleTranslation(self::$module_name, $field->{$what}, self::$module_name),
                    ENT_QUOTES
                );
            }
        } else {
            return '';
        }
    }

    public function ajaxProcessUpdatePositions()
    {
        $name = Tools::getValue('name');
        $ordered_fields = Tools::getValue('fields');

        $fields = new AmbData($name);
        $fields->updatePositions($ordered_fields);
    }

    public function ajaxProcessUpdateActive()
    {

        $name = Tools::getValue('name');
        $field = Tools::getValue('field');
        $active = Tools::getValue('value', 0);

        $fields = new AmbData($name);
        $fields->updateSpecificParamsField($field, array('field_active' => $active));
        $this->context->cookie->{'resetFilterFor' . $name} = true;
    }

    public function ajaxProcessUpdateListActive()
    {
        $name = Tools::getValue('name');
        $active = Tools::getValue('value', 0);

        $fields = new AmbData($name);

        $fields->updateListActive($active);
    }

    public function ajaxProcessUpdateListDisplayName()
    {
        $name = Tools::getValue('name');
        $view_name = Tools::getValue('viewname');
        $value = Tools::getValue('value');

        $fields = new AmbData($name);

        $fields->updateDisplayName($view_name, $value);
    }
}
