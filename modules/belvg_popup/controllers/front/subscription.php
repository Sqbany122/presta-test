<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Belvg_PopupSubscriptionModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $displayType = Configuration::get('BELVG_POPUP_DISPLAY_TYPE', null);

        if ($displayType === 'coupon') {
            $email = $_POST['email'];
            $cartRuleId = Configuration::get('BELVG_POPUP_CART_RULE', null);


            $cartRule = new CartRule($cartRuleId);
            $cartRuleCode = $cartRule->code;

            $mailSendFlag = Mail::send(
                $this->context->language->id,
                'popup_coupon',
                Mail::l('Coupon  mail'),
                [
                    '{cartRuleCode}' => $cartRuleCode
                ],
                $email,
                null,
                null,
                null,
                null,
                null,
                __DIR__ . '/../../mails/'
            );
            if($mailSendFlag){
                die(json_encode(['valid' => 'Coupon sent by email']));
            }
        } elseif ($displayType === 'newsletter') {
            $_POST['action'] = 0;
            $emailSubscriptionModule = Module::getInstanceByName('ps_emailsubscription');
            $emailSubscriptionModule->newsletterRegistration();

            if ($emailSubscriptionModule->error) {
                die(json_encode(['error' => $emailSubscriptionModule->error]));
            } elseif ($emailSubscriptionModule->valid) {
                die(json_encode(['valid' => $emailSubscriptionModule->valid]));
            }
        }
    }
}
