<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
*/

class BackInStockErrorModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $account_link = $this->context->link->getPageLink('index', 'true');
        $this->context->smarty->assign('acc_link', $account_link);
        $this->context->smarty->assign('pr_error_text', $this->module->l('Product Update Error'));
        $this->context->smarty->assign('pr_1_error', $this->module->l('There is 1 error.'));
        $this->context->smarty->assign('pr_failure', $this->module->l('Failed to unsubscribe.'));
        $this->context->smarty->assign('pr_go_to_home', $this->module->l('Go to Home Page'));
        $this->setTemplate('error.tpl');
    }
}
