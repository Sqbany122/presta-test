<?php

 /**

 * Prestashop Modules & Themen End User License Agreement
 *
 * This End User License Agreement ("EULA") is a legal agreement between you and Presta-Apps ltd.
 * ( here in referred to as "we" or "us" ) with regard to Prestashop Modules & Themes
 * (herein referred to as "Software Product" or "Software").
 * By installing or using the Software Product you agree to be bound by the terms of this EULA.
 *
 * 1. Eligible Licensees. This Software is available for license solely to Software Owners,
 * with no right of duplication or further distribution, licensing, or sub-licensing.
 * A Software Owner is someone who legally obtained a copy of the Software Product via Prestashop Store.
 *
 * 2. License Grant. We grant you a personal/one commercial, non-transferable and non-exclusive right to use the copy
 * of the Software obtained via Prestashop Store. Modifying, translating, renting, copying, transferring or assigning
 * all or part of the Software, or any rights granted hereunder, to any other persons and removing any proprietary
 * notices, labels or marks from the Software is strictly prohibited. Furthermore, you hereby agree not to create
 * derivative works based on the Software. You may not transfer this Software.
 *
 * 3. Copyright. The Software is licensed, not sold. You acknowledge that no title to the intellectual property in the
 * Software is transferred to you. You further acknowledge that title and full ownership rights to the Software will
 * remain the exclusive property of Presta-Apps Mobile, and you will not acquire any rights to the Software,
 * except as expressly set forth above.
 *
 * 4. Reverse Engineering. You agree that you will not attempt, and if you are a corporation,
 * you will use your best efforts to prevent your employees and contractors from attempting to reverse compile, modify,
 * translate or disassemble the Software in whole or in part. Any failure to comply with the above or any other terms
 * and conditions contained herein will result in the automatic termination of this license.
 *
 * 5. Disclaimer of Warranty. The Software is provided "AS IS" without warranty of any kind. We disclaim and make no
 * express or implied warranties and specifically disclaim the warranties of merchantability, fitness for a particular
 * purpose and non-infringement of third-party rights. The entire risk as to the quality and performance of the Software
 * is with you. We do not warrant that the functions contained in the Software will meet your requirements or that the
 * operation of the Software will be error-free.
 *
 * 6. Limitation of Liability. Our entire liability and your exclusive remedy under this EULA shall not exceed the price
 * paid for the Software, if any. In no event shall we be liable to you for any consequential, special, incidental or
 * indirect damages of any kind arising out of the use or inability to use the software.
 *
 * 7. Rental. You may not loan, rent, or lease the Software.
 *
 * 8. Updates and Upgrades. All updates and upgrades of the Software from a previously released version are governed by
 * the terms and conditions of this EULA.
 *
 * 9. Support. Support for the Software Product is provided by Presta-Apps ltd. For product support, please send an
 * email to support at info@iniweb.de
 *
 * 10. No Liability for Consequential Damages. In no event shall we be liable for any damages whatsoever
 * (including, without limitation, incidental, direct, indirect special and consequential damages, damages for loss
 * of business profits, business interruption, loss of business information, or other pecuniary loss) arising out of
 * the use or inability to use the Software Product. Because some states/countries do not allow the exclusion or
 * limitation of liability for consequential or incidental damages, the above limitation may not apply to you.
 *
 * 11. Indemnification by You. You agree to indemnify, hold harmless and defend us from and against any claims or
 * lawsuits, including attorney's fees that arise or result from the use or distribution of the Software in violation
 * of this Agreement.
 *
 * @author    Presta-Apps Limited
 * @website   www.presta-apps.com
 * @contact   info@presta-apps.com
 * @copyright 2009-2016 Presta-Apps Ltd.
 * @license   Proprietary

 */


 require dirname(__FILE__) . '/inixframe/loader.php'; class infobariw extends Inix2Module { function __construct() { $this->name = 'infobariw'; $this->tab = 'others'; $this->version = '2.0.2'; $this->displayName = 'Info bar'; $this->description = 'Expandable infobar to tob or bottom of the page'; $this->need_instance = 0; $this->ps_versions_compliancy = array('min' => '1.5.1.0', 'max' => '1.7'); $this->author = 'presta-apps'; parent::__construct(); if (!$this->context->controller instanceof AdminController) { $this->bootstrap = 1; return; } $this->object_table = 'configuraiton'; $this->className = 'Configuration'; $this->tabs_options = true; $this->fields_options = array( 'general' => array( 'title' => $this->l('Info bar on top'), 'fields' => array( 'INFOBAR_AUTOEXPAND_TOP' => array( 'title' => $this->l('Auto expand'), 'type' => 'bool', ), 'INFOBAR_CLOSE_DISCARD_TOP' => array( 'title' => $this->l('Discard autoexpand on close'), 'type' => 'bool', 'desc' => $this->l('Inforbar wont be autoexpanded if once closed, until update if the text'), ), 'INFOBAR_COLOR_TOP' => array( 'title' => $this->l('Background color'), 'type' => 'color', 'validation' => 'isColor', 'defaultValue' => '#383838', 'name' => 'INFOBAR_COLOR_TOP', 'size' => 10 ), 'INFOBAR_TEXT_COLOR_TOP' => array( 'title' => $this->l('Text color'), 'type' => 'color', 'validation' => 'isColor', 'defaultValue' => '#dfdfdf', 'name' => 'INFOBAR_TEXT_COLOR_TOP', 'size' => 10, ), 'INFOBAR_TEXT_TOP' => array( 'type' => 'textareaLang', 'title' => $this->l('Infobar text'), 'autoload_rte' => true, 'html' => true, 'rows' => 10, 'cols' => 5, 'validation' => 'isCleanHtml', ), 'INFOBAR_TRANSPARANCY_TOP' => array( 'type' => 'text', 'title' => $this->l('Transparency'), 'suffix' => '%', 'size' => 4, 'required' => true, 'defaultValue' => 100, 'validation' => 'isUnsignedInt', ), 'INFOBAR_ENABLE_TOP' => array( 'title' => $this->l('Enable'), 'type' => 'bool', ), 'INFOBAR_POSITION_TOP' => array( 'type' => 'hidden', 'defaultValue' => 'top', ) ), 'submit' => array('title' => $this->l('Save'), 'class' => ''), ), 'bottom' => array( 'title' => $this->l('Info bar on bottom'), 'fields' => array( 'INFOBAR_AUTOEXPAND_BOTTOM' => array( 'title' => $this->l('Auto expand'), 'type' => 'bool', ), 'INFOBAR_CLOSE_DISCARD_BOTTOM' => array( 'title' => $this->l('Discard autoexpand on close'), 'type' => 'bool', 'desc' => $this->l('Inforbar wont be autoexpanded if once closed, until update if the text'), ), 'INFOBAR_COLOR_BOTTOM' => array( 'title' => $this->l('Background color'), 'type' => 'color', 'validation' => 'isColor', 'defaultValue' => '#383838', 'name' => 'INFOBAR_COLOR_BOTTOM', 'size' => 10 ), 'INFOBAR_TEXT_COLOR_BOTTOM' => array( 'title' => $this->l('Text color'), 'type' => 'color', 'validation' => 'isColor', 'defaultValue' => '#dfdfdf', 'name' => 'INFOBAR_TEXT_COLOR_BOTTOM', 'size' => 10, ), 'INFOBAR_TEXT_BOTTOM' => array( 'type' => 'textareaLang', 'title' => $this->l('Infobar text'), 'autoload_rte' => true, 'html' => true, 'rows' => 10, 'cols' => 5, 'validation' => 'isCleanHtml', ), 'INFOBAR_TRANSPARANCY_BOTTOM' => array( 'type' => 'text', 'title' => $this->l('Transparency'), 'suffix' => '%', 'size' => 4, 'required' => true, 'defaultValue' => 100, 'validation' => 'isUnsignedInt', ), 'INFOBAR_ENABLE_BOTTOM' => array( 'title' => $this->l('Enable'), 'type' => 'bool', ), 'INFOBAR_POSITION_BOTTOM' => array( 'type' => 'hidden', 'defaultValue' => 'bottom', 'value' => 'bottom' ) ), 'submit' => array('title' => $this->l('Save'), 'class' => ''), ), 'left' => array( 'title' => $this->l('Info bar on left'), 'fields' => array( 'INFOBAR_AUTOEXPAND_LEFT' => array( 'title' => $this->l('Auto expand'), 'type' => 'bool', ), 'INFOBAR_CLOSE_DISCARD_LEFT' => array( 'title' => $this->l('Discard autoexpand on close'), 'type' => 'bool', 'desc' => $this->l('Inforbar wont be autoexpanded if once closed, until update if the text'), ), 'INFOBAR_COLOR_LEFT' => array( 'title' => $this->l('Background color'), 'type' => 'color', 'validation' => 'isColor', 'defaultValue' => '#383838', 'name' => 'INFOBAR_COLOR_LEFT', 'size' => 10 ), 'INFOBAR_TEXT_COLOR_LEFT' => array( 'title' => $this->l('Text color'), 'type' => 'color', 'validation' => 'isColor', 'defaultValue' => '#dfdfdf', 'name' => 'INFOBAR_TEXT_COLOR_LEFT', 'size' => 10, ), 'INFOBAR_TEXT_LEFT' => array( 'type' => 'textareaLang', 'title' => $this->l('Infobar text'), 'autoload_rte' => true, 'html' => true, 'rows' => 10, 'cols' => 5, 'validation' => 'isCleanHtml', ), 'INFOBAR_TRANSPARANCY_LEFT' => array( 'type' => 'text', 'title' => $this->l('Transparency'), 'suffix' => '%', 'size' => 4, 'required' => true, 'defaultValue' => 100, 'validation' => 'isUnsignedInt', ), 'INFOBAR_ENABLE_LEFT' => array( 'title' => $this->l('Enable'), 'type' => 'bool', ), 'INFOBAR_POSITION_LEFT' => array( 'type' => 'hidden', 'defaultValue' => 'left', 'value' => 'left' ) ), 'submit' => array('title' => $this->l('Save'), 'class' => ''), ), 'right' => array( 'title' => $this->l('Info bar on right'), 'fields' => array( 'INFOBAR_AUTOEXPAND_RIGHT' => array( 'title' => $this->l('Auto expand'), 'type' => 'bool', ), 'INFOBAR_CLOSE_DISCARD_RIGHT' => array( 'title' => $this->l('Discard autoexpand on close'), 'type' => 'bool', 'desc' => $this->l('Inforbar wont be autoexpanded if once closed, until update if the text'), ), 'INFOBAR_COLOR_RIGHT' => array( 'title' => $this->l('Background color'), 'type' => 'color', 'validation' => 'isColor', 'defaultValue' => '#383838', 'name' => 'INFOBAR_COLOR_RIGHT', 'size' => 10 ), 'INFOBAR_TEXT_COLOR_RIGHT' => array( 'title' => $this->l('Text color'), 'type' => 'color', 'validation' => 'isColor', 'defaultValue' => '#dfdfdf', 'name' => 'INFOBAR_TEXT_COLOR_RIGHT', 'size' => 10, ), 'INFOBAR_TEXT_RIGHT' => array( 'type' => 'textareaLang', 'title' => $this->l('Infobar text'), 'autoload_rte' => true, 'html' => true, 'rows' => 10, 'cols' => 5, 'validation' => 'isCleanHtml', ), 'INFOBAR_TRANSPARANCY_RIGHT' => array( 'type' => 'text', 'title' => $this->l('Transparency'), 'suffix' => '%', 'size' => 4, 'required' => true, 'defaultValue' => 100, 'validation' => 'isUnsignedInt', ), 'INFOBAR_ENABLE_RIGHT' => array( 'title' => $this->l('Enable'), 'type' => 'bool', ), 'INFOBAR_POSITION_RIGHT' => array( 'type' => 'hidden', 'defaultValue' => 'right', 'value' => 'right' ) ), 'submit' => array('title' => $this->l('Save'), 'class' => ''), ), ); } public function install() { if(Tools::version_compare(_PS_VERSION_, '1.7.0')){ $this->install_hooks = array('displayTop'); } else{ $this->install_hooks = array('displayTop' , 'displayHeader'); } return parent::install(); } public function hookdisplayTop() { $this->context->controller->addCSS($this->getPathUri() . 'views/css/infobar.css'); $this->context->controller->addJS($this->getPathUri() . 'views/js/script.js'); $keys = array( 'INFOBAR_ENABLE_TOP', 'INFOBAR_ENABLE_BOTTOM', 'INFOBAR_ENABLE_LEFT', 'INFOBAR_ENABLE_RIGHT' ); $enable_bars = Configuration::getMultiple($keys); $positions = array(); if ($enable_bars['INFOBAR_ENABLE_TOP']) { $positions[] = 'TOP'; } if ($enable_bars['INFOBAR_ENABLE_BOTTOM']) { $positions[] = 'BOTTOM'; } if ($enable_bars['INFOBAR_ENABLE_LEFT']) { $positions[] = 'LEFT'; } if ($enable_bars['INFOBAR_ENABLE_RIGHT']) { $positions[] = 'RIGHT'; } $this->createBars($positions); return $this->display(__FILE__, 'infobar.tpl'); } public function hookDisplayHeader($param) { $this->context->controller->addCSS($this->getPathUri() . 'views/css/infobar.css'); $this->context->controller->addJS($this->getPathUri() . 'views/js/script.js'); } public function createBars($positions = array()) { $bars = array(); foreach ($positions as $position) { $transparancy = Configuration::get('INFOBAR_TRANSPARANCY_' . $position); if ($transparancy === false) { $transparancy = 100; } else if ($transparancy > 100) { $transparancy = 100; } else if ($transparancy < 0) { $transparancy = 0; } $hex = str_replace("#", "", Configuration::get('INFOBAR_COLOR_' . $position)); if (strlen($hex) == 3) { $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1)); $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1)); $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1)); } else { $r = hexdec(substr($hex, 0, 2)); $g = hexdec(substr($hex, 2, 2)); $b = hexdec(substr($hex, 4, 2)); } $auto_expand = (bool) Configuration::get('INFOBAR_AUTOEXPAND_' . $position); $discard_on_close = (bool) Configuration::get('INFOBAR_CLOSE_DISCARD_' . $position); if (isset($this->context->cookie->infobar_closed)) { $date_closed = $this->context->cookie->infobar_closed; $date_edited = (int) DB::getInstance()->getValue('SELECT UNIX_TIMESTAMP(cl.date_upd)


			FROM `' . _DB_PREFIX_ . 'configuration` c


			JOIN `' . _DB_PREFIX_ . 'configuration_lang` cl ON (cl.id_configuration = c.id_configuration AND cl.id_lang = ' . (int) $this->context->language->id . ' )


			WHERE c.name = \'INFOBAR_TEXT_' . $position . '\''); $date_closed = strtotime($date_closed); if ($date_closed < $date_edited) { $auto_expand = true; } else { $auto_expand = false; } } $bars[$position] = array( 'infobar_color' => Configuration::get('INFOBAR_COLOR_' . $position), 'infobar_color_rgba' => array( 'r' => $r, 'g' => $g, 'b' => $b, 'a' => $a = ($transparancy / 100) ), 'infobar_text_color' => Configuration::get('INFOBAR_TEXT_COLOR_' . $position), 'infobar_text' => Configuration::get('INFOBAR_TEXT_' . $position, $this->context->language->id), 'infobar_expanded' => $auto_expand, 'infobar_position' => Configuration::get('INFOBAR_POSITION_' . $position), 'infobar_buttons_color' => Tools::getBrightness(Configuration::get('INFOBAR_COLOR_' . $position)) > 128 ? 'black' : 'white', 'infobar_close_discard' => $discard_on_close, 'infobar_ajax_link' => $this->context->link->getModuleLink($this->name, 'ajax', array(), null, Configuration::get('PS_LANG_DEFAULT')), ); } $this->context->smarty->assign(array( 'bars' => $bars )); } } 