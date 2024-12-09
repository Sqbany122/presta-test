<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// use PrestaShop\Module\Rediconpaypo\Helper\PaypoLog;
// use PrestaShop\Module\Rediconpaypo\Helper\SettingsPaypo;
// use PrestaShop\Module\Rediconpaypo\Paypo;

require_once _PS_MODULE_DIR_ . "rediconpaypo/rediconpaypo.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Paypo.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Helper/PaypoLog.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Helper/SettingsPaypo.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/classes/PaypoTransaction.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/classes/Statustransaction.php";

class RediconPaypoNotificationModuleFrontController extends ModuleFrontController
{
    private $postData = [];
    private $paypo = null;
    private $path = '';
    private $fake = false;
    private function setFakePost()
    {
        if (Validate::isLoadedObject($order = new Order(Tools::getValue('order_id')))) {

            $transaction = PaypoTransaction::getByCartId($order->id_cart);
            $transactionId = isset($transaction['transactionId']) ? $transaction['transactionId'] : null;
            // /index.php?order_id=7&fc=module&module=rediconpaypo&controller=notification&id_lang=1
            $tmp = [
                "merchantId" => "eab975bb-43eb-48a0-b08e-f889c012d59e",
                "referenceId" => $order->reference,
                "transactionId" => $transactionId,
                "transactionStatus" => "COMPLETED",//PENDING ,ACCEPTED, COMPLETED
                "amount" => $order->id,
                "lastUpdate" => "2021-03-25T08:45:15+01:00",
                "message" => "Payment request awaiting confirmation",
            ];

            return json_encode($tmp);
        }
        exit('BAD ID');
    }
    public function postProcess()
    {
        $this->postData = $this->fake ? $this->setFakePost() : file_get_contents('php://input');
        // dd($this->postData);
        PaypoLog::log('[POST DATA] - ' . $this->postData, 'post_data');

        try {
            //https://github.com/PrestaShop/PrestaShop/issues/15503
            // global $kernel;
            // if (!$kernel) {
            //     require_once _PS_ROOT_DIR_ . '/app/AppKernel.php';
            //     $kernel = new \AppKernel('prod', false);
            //     $kernel->boot();
            // }
            //koniec

            if (empty($this->postData)) {
                header("HTTP/1.1 203 Non-Authoritative Information");
                exit;
            }

            $transactionValueObject = Paypo::jsonDecode($this->postData);

            if ($transactionValueObject && $transaction = PaypoTransaction::getTransactionByTransactionid($transactionValueObject->getTransactionId())) {
                $status = new Statustransaction();

                $status->create((int) $transaction['id_transaction'], $transactionValueObject, $this->postData);

                if (method_exists('Order', 'getIdByCartId')) {
                    //wersja 1.7+
                    $order_id = Order::getIdByCartId((int) $transaction['id_cart']);
                } else {
                    // wersja 1.6
                    $order_id = Order::getOrderByCartId((int) $transaction['id_cart']);
                }

                if (Validate::isLoadedObject($order = new Order($order_id))) {

                    $responseStatus = $transactionValueObject->getTransactionStatus();

                    if ($responseStatus === 'CANCELED') { //była literówka w pdf api
                        $responseStatus = 'CANCELLED';
                    }

                    if ($statusOrder = Configuration::get('REDICON_PAYPO_STATUS_' . $responseStatus)) {

                        //Automatyczne potwierdzenie transakcji bez zmiany statusu
                        if (Configuration::get('REDICON_PAYPO_AUTO_COMPLETED') == '1' && $responseStatus == SettingsPaypo::COMPLETED) {
                            header("HTTP/1.1 200 OK");
                            exit;
                        }

                        //Aktualne statusy przy ktorych można zmienić status zamówienia
                        $accepted_statuses = [
                            Configuration::get('REDICON_PAYPO_STATUS_NEW'),
                            Configuration::get('REDICON_PAYPO_STATUS_PENDING'),
                            Configuration::get('PS_OS_OUTOFSTOCK_PAID'),
                            Configuration::get('PS_OS_OUTOFSTOCK_UNPAID'),
                        ];

                        if (in_array($order->current_state, $accepted_statuses) && $order->current_state != $statusOrder) {
                            $order->setCurrentState($statusOrder);
                        }

                        if (Configuration::get('REDICON_PAYPO_AUTO_COMPLETED') == '1') {
                            if ($order->current_state == Configuration::get('REDICON_PAYPO_STATUS_ACCEPTED') && $responseStatus == SettingsPaypo::ACCEPTED) {
                                RediconPaypo::transactionConfirm($order, $transaction['transactionId']);
                            }
                        }

                    } else {
                        header("HTTP/1.1 400 Bad request");
                        PaypoLog::log('[STATUS NOT IN LIST] - ' . $this->postData, 'errors');
                        exit;
                    }
                }
                header("HTTP/1.1 200 OK");
                exit;
            }

        } catch (Exception $e) {
            PaypoLog::log('[ERROR] - ' . $this->postData . $e->getMessage(), 'errors');
            header("HTTP/1.1 400 Bad request");
            exit;
        }

        header("HTTP/1.1 400 Bad request");
        exit;
    }
}
