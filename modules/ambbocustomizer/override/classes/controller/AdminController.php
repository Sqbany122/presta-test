<?php
/**
 *    ambBOcustomizer Module : customize the prestashop back-office
 *
 *    @author    Ambris Informatique
 *    @copyright Copyright (c) 2013-2015 Ambris Informatique SARL
 *    @license   Commercial license
 *    @module    BO Customizer (AmbBoCustomizer)
 *    @file      override/classes/controller/AdminController.php
 *    @subject   Overrides AdminController to insert hooks
 *
 *    Support by mail: support@ambris.com
 */

class AdminController extends AdminControllerCore
{

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        if (version_compare(_PS_VERSION_, 1.6, '<')) {
            Hook::exec('action' . $this->controller_name . 'ListingFieldsModifier', array(
                'select' => &$this->_select,
                'join' => &$this->_join,
                'where' => &$this->_where,
                'group_by' => &$this->_group,
                'order_by' => &$this->_orderBy,
                'order_way' => &$this->_orderWay,
                'fields' => &$this->fields_list,
            ));

            parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

            Hook::exec('action' . $this->controller_name . 'ListingResultsModifier', array(
                'list' => &$this->_list,
                'list_total' => &$this->_listTotal,
            ));
        } else {
            parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
        }
    }

    public function processResetFilters($list_id = null)
    {
        if (version_compare(_PS_VERSION_, 1.6, '<')) {
            if (version_compare(_PS_VERSION_, '1.5.4', '>=')) {
                $list_id = isset($this->list_id) ? $this->list_id : $this->table;

                $prefix = str_replace(array('admin', 'controller'), '', Tools::strtolower(get_class($this)));
                $filters = $this->context->cookie->getFamily($prefix . $list_id . 'Filter_');

                foreach ($filters as $cookie_key => $filter) {
                    if (strncmp($cookie_key, $prefix . $list_id . 'Filter_', 7 + Tools::strlen($prefix . $list_id)) == 0) {
                        $key = Tools::substr($cookie_key, 7 + Tools::strlen($prefix . $list_id));

                        //if (is_array($this->fields_list) && array_key_exists($key, $this->fields_list))
                        $this->context->cookie->$cookie_key = null;
                        unset($this->context->cookie->$cookie_key);
                    }
                }

                if (isset($this->context->cookie->{'submitFilter' . $list_id})) {
                    unset($this->context->cookie->{'submitFilter' . $list_id});
                }

                if (isset($this->context->cookie->{$prefix . $list_id . 'Orderby'})) {
                    unset($this->context->cookie->{$prefix . $list_id . 'Orderby'});
                }

                if (isset($this->context->cookie->{$prefix . $list_id . 'Orderway'})) {
                    unset($this->context->cookie->{$prefix . $list_id . 'Orderway'});
                }
            } else {
                $filters = $this->context->cookie->getFamily($this->table . 'Filter_');

                foreach ($filters as $cookie_key => $filter) {
                    if (strncmp($cookie_key, $this->table . 'Filter_', 7 + Tools::strlen($this->table)) == 0) {
                        $key = Tools::substr($cookie_key, 7 + Tools::strlen($this->table));
                        /* Table alias could be specified using a ! eg. alias!field */
                        $tmp_tab = explode('!', $key);
                        $key = (count($tmp_tab) > 1 ? $tmp_tab[1] : $tmp_tab[0]);

                        //if (array_key_exists($key, $this->fields_list))
                        $this->context->cookie->$cookie_key = null;
                        unset($this->context->cookie->$cookie_key);
                    }
                }

                if (isset($this->context->cookie->{'submitFilter' . $this->table})) {
                    unset($this->context->cookie->{'submitFilter' . $this->table});
                }

                if (isset($this->context->cookie->{$this->table . 'Orderby'})) {
                    unset($this->context->cookie->{$this->table . 'Orderby'});
                }

                if (isset($this->context->cookie->{$this->table . 'Orderway'})) {
                    unset($this->context->cookie->{$this->table . 'Orderway'});
                }
            }

            unset($_POST);
            $this->_filter = false;
            $this->filter = false;
            unset($this->_filterHaving);
            unset($this->_having);
        } else {
            parent::processResetFilters();
        }
    }
}
