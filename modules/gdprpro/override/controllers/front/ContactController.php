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
 * Class ContactController
 */
class ContactController extends ContactControllerCore
{
    public function postProcess()
    {
        if (Tools::isSubmit('submitMessage') && Module::isEnabled('gdprpro') && Configuration::get(GdprProConfig::CONSENT_CHKBOX_CONTACT_ENABLE)) {
            if (Tools::getValue('gdpr_consent_chkbox', false) == 1) {
                parent::postProcess();
                \PrestaChamps\GdprPro\Models\ActivityLogFactory::makeContactFormConsent();
            } else {
                $this->context->controller->errors[] = 'You must accept the terms and conditions';
            }
        }
        parent::postProcess();
    }
}
