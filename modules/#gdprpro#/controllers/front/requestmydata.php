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

use \PrestaChamps\GdprPro\Commands\GenerateDataPdfCommand;

/**
 * Class GdprProRequestMyDataModuleFrontController
 */
class GdprProRequestMyDataModuleFrontController extends ModuleFrontControllerCore
{
    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function initContent()
    {
        if (Tools::getValue('type', false) === 'pdf') {
            $this->asPdf();
            exit();
        }

        parent::initContent();
        $this->context->smarty->tpl_vars['page']->value['body_classes']['page-customer-account'] = true;
        $this->context->smarty->tpl_vars['page']->value['body_classes']['page-customer-account'] = true;
        $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $this->module->l('Request my data');
        $this->context->smarty->assign(
            array(
                'pdfLink' => $this->context->link->getModuleLink(
                    'gdprpro',
                    'requestmydata',
                    array('type' => 'pdf')
                ),
            )
        );

        if (GdprPro::isPs17()) {
            $this->setTemplate('module:gdprpro/views/templates/front/request-my-data.tpl');
        } else {
            $this->setTemplate('request-my-data-16.tpl');
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

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function asPdf()
    {
        $command = new GenerateDataPdfCommand(new Customer($this->context->customer->id), $this->context);
        $command->execute();
        die();
    }
}
