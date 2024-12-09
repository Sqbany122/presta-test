<?php

class Hook extends HookCore
{
    public static function getHookModuleExecList($hook_name = null) {
        if ($hook_name == 'displayPayment') {
            $modules_list = parent::getHookModuleExecList($hook_name);

            $delivery_option = Context::getContext()->cart->getDeliveryOption();
            foreach ($delivery_option as $id_carrier) {
                $carrierObj = new Carrier((int)$id_carrier);
                $avaliable_payments = DB::getInstance()->executeS('
                    SELECT id_module
                    FROM `' . _DB_PREFIX_ . 'module_carrier`
                    WHERE id_carrier = ' . (int)$carrierObj->id_reference . ' AND `id_shop`=' . (int)Context::getContext()->shop->id
                );

                break;
            }

            $avaliable_payments_ids = array();
            foreach ($avaliable_payments as $avaliable_payment) {
                $avaliable_payments_ids[] = $avaliable_payment['id_module'];
            }

            foreach ($modules_list as $key => &$avaliable_payment) {
                if (!in_array($avaliable_payment['id_module'], $avaliable_payments_ids)) {
                    unset($modules_list[$key]);
                }
            }

            return $modules_list;
        } else {
            return parent::getHookModuleExecList($hook_name);
        }
    }
}