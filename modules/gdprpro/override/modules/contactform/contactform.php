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

use DiDom\Document;

class ContactformOverride extends Contactform
{
    public function renderWidget($hookName = null, array $configuration = array())
    {
        if (Configuration::get(GdprProConfig::CONSENT_CHKBOX_CONTACT_ENABLE) && Module::isEnabled('gdprpro')) {
            $html = parent::renderWidget($hookName, $configuration);
            $this->context->smarty->assign(array(
                'label' => Configuration::get(
                    GdprProConfig::CONSENT_CHKBOX_CONTACT_TEXT,
                    $this->context->language->id
                ),
            ));
            $extra = $this->context->smarty->fetch(
                'module:gdprpro/views/templates/hook/contact-consent-checkbox.tpl'
            );

            $document = new Document($html, false);
            $documentElements = $document->find('form footer');
            foreach ($documentElements as $documentElement) {
                $element = new \DiDom\Element('div');
                $element->setInnerHtml($extra);
                $documentElement->prependChild($element);
            }
            return $document->html();
        }

        return parent::renderWidget($hookName, $configuration);
    }


    public function sendMessage()
    {
        if (Module::isEnabled('gdprpro') && Configuration::get(GdprProConfig::CONSENT_CHKBOX_CONTACT_ENABLE)) {
            if (Tools::getValue('gdpr_consent_chkbox') != '1') {
                $this->context->controller->errors[] = $this->l('You must accept the terms and conditions');
            } else {
                parent::sendMessage();
                \PrestaChamps\GdprPro\Models\ActivityLogFactory::makeContactFormConsent();
            }
        } else {
            parent::sendMessage();
        }
    }
}
