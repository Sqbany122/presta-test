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
 * Class GdprProStoreCookieModuleFrontController
 */
class GdprProStoreCookieModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        try {
            parent::initContent();
            $settings = Tools::getValue('gdprSettings', false);
            if ($settings !== false && is_array($settings)) {
                if ($this->context->customer->isLogged()) {
                    \PrestaChamps\GdprPro\Models\ActivityLogFactory::makeConsentAccept(json_encode($settings));
                }
                Context::getContext()->cookie->gdpr_conf = json_encode($settings);
                Context::getContext()->cookie->gdpr_windows_was_opened = true;
                Context::getContext()->cookie->write();
            }

            $this->ajaxDie(array('status' => 'ok'));
        } catch (Exception $exception) {
            $this->ajaxDie(array('status' => 500, 'error' => $exception->getMessage()));
        }
    }

    protected function ajaxDie($value = null, $controller = null, $method = null)
    {
        if (!is_scalar($value)) {
            $value = json_encode($value);
        }
        parent::ajaxDie($value, $controller, $method);
    }
}
