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

namespace PrestaChamps\GdprPro\Commands;

use Mail;
use Context;
use Configuration;

/**
 * Class SendDataCommand
 *
 * @package PrestaChamps\GdprPro\Commands
 */
class SendDataCommand extends DataRequestCommand
{
    public function execute()
    {
        Mail::send(
            1,
            'layout',
            "All Data about you on " . Context::getContext()->shop->name,
            array(
                'mailMainContentArea' => $this->renderMail(),
                'shopName'            => $this->context->shop->name,
                'shopUrl'             => $this->context->shop->getBaseURL(),
                'shopAddress'         => $this->addressDataFormatter($this->context->shop->getAddress()),
            ),
            $this->customer->email,
            $this->customer->firstname . " " . $this->customer->lastname,
            Configuration::get('PS_SHOP_EMAIL'),
            Configuration::get('PS_SHOP_NAME'),
            null,
            null,
            _PS_MODULE_DIR_ . 'gdprpro/mails/'
        );
    }

    protected function renderMail()
    {
        $this->context->smarty->assign(array('customerData' => $this->formatData()));
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'gdprpro/views/templates/front/mail/customer-data.tpl');
    }
}
