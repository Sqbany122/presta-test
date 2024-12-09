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
 * Class GdprProEraseMyDataModuleFrontController
 */
class GdprProEraseMyDataModuleFrontController extends ModuleFrontControllerCore
{
    /**
     * @throws PrestaShopException
     */
    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->tpl_vars['page']->value['body_classes']['page-customer-account'] = true;
        $this->context->smarty->tpl_vars['page']->value['body_classes']['page-customer-account'] = true;
        $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $this->module->l('Delete my data');
        if (GdprPro::isPs17()) {
            $this->setTemplate('module:gdprpro/views/templates/front/delete-my-data.tpl');
        } else {
//            $this->smarty->assign([
//
//            ]);
            $this->setTemplate('delete-my-data-16.tpl');
        }
    }

    /**
     * @return array
     */
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }
}
