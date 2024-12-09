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

class BackInStockDeleteModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $data = array();
        $data['email'] = Tools::getValue('email');
        $data['product_id'] = Tools::getValue('id');
        $data['attr'] = Tools::getValue('attribute_id');
        $data['shop_id'] = Tools::getValue('shop_id');
        
        $obj = new BackInStock();
        $result = $obj->deleteProduct($data);
        if ($result == 1) {
            Tools::redirect($this->context->link->getModuleLink('backinstock', 'success'));
        } else {
            Tools::redirect($this->context->link->getModuleLink('backinstock', 'error'));
        }
    }
}
