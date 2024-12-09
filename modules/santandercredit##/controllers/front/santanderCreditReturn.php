<?php

class SantandercreditSantanderCreditReturnModuleFrontController extends ModuleFrontController {

    public function __construct() {
        parent::__construct();
    }

    public function initContent() {
        parent::initContent();

        $returnTemplate = 'santanderCreditReturn.tpl';
        $errors = '';
        if (Tools::getValue('orderId') != 0 && Tools::getValue('id_wniosku') != '') {

            $order = new Order(Tools::getValue('orderId'));
            if ($order) {

                $orderPaymentCollection = $order->getOrderPaymentCollection();
                $payment = $orderPaymentCollection->getFirst();
                if ($payment) {
                    $payment->transaction_id = Tools::getValue('id_wniosku');
                    $payment->save();
                    $this->context->smarty->assign(
                            array('wniosekId' => preg_replace('#[^0-9/ZAG]#', '', $_GET['id_wniosku']),
                                'orderId' => (int) $_GET['orderId']
                            )
                    );
//                    $returnTemplate = 'santanderCreditReturn.tpl';
                } else {
                    $errors .= "Błąd w trakcie aktualizacji numeru transakcji (transactionId, wniosekId).";
                    $this->context->smarty->assign(array('errors' => $errors, 'wniosekId' => preg_replace('#[^0-9/ZAG]#', '', $_GET['id_wniosku']),
                        'orderId' => $_GET['orderId']));
                    $returnTemplate = 'paymentErrors.tpl';
                }
            } else {
                $errors .= "Błędny numer zamówienia w sklepie (orderId).";
                $this->context->smarty->assign(array('errors' => $errors, 'wniosekId' => preg_replace('#[^0-9/ZAG]#', '', $_GET['id_wniosku']),
                    'orderId' => $_GET['orderId']));
                $returnTemplate = 'paymentErrors.tpl';
            }
        } else {
            $errors .= "Nieokreślony numer wniosku lub numer zamówienia w odpowiedzi Banku (orderId, id_wniosku).";
            $this->context->smarty->assign(array('errors' => $errors));
            $returnTemplate = 'paymentErrors.tpl';
        }
        $this->setTemplate('module:santandercredit/views/templates/front/'.$returnTemplate);
    }

}
