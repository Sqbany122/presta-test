<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// namespace PrestaShop\Module\Rediconpaypo;

// use PrestaShop\Module\Rediconpaypo\ApiService;
// use PrestaShop\Module\Rediconpaypo\Helper\ApiHelper;
// use PrestaShop\Module\Rediconpaypo\Helper\PaypoLog;
// use PrestaShop\Module\Rediconpaypo\Payload\ConfigurationPayload;
// use PrestaShop\Module\Rediconpaypo\Payload\CustomerPayload;
// use PrestaShop\Module\Rediconpaypo\Payload\OrderPayload;
// use PrestaShop\Module\Rediconpaypo\Payload\RefundsPayload;
// use PrestaShop\Module\Rediconpaypo\Payload\RegisterTransactionPayload;
// use PrestaShop\Module\Rediconpaypo\Payload\UpdateTransactionPayload;
// use PrestaShop\Module\Rediconpaypo\Response\NotificationResponseObject;

require_once dirname(__FILE__) . '/../../../config/config.inc.php';

require_once _PS_MODULE_DIR_ . "rediconpaypo/api/ApiService.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Helper/ApiHelper.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Helper/PaypoLog.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Payload/ConfigurationPayload.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Payload/CustomerPayload.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Payload/OrderPayload.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Payload/RefundsPayload.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Payload/RegisterTransactionPayload.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Payload/UpdateTransactionPayload.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Response/NotificationResponseObject.php";

class Paypo
{
    private $module = 'paypo';
    private $order = null;
    private $settings = [
        'url' => null,
        'id' => null,
        'secret' => null,
    ];

    public function __construct(\Order $order, array $settings = [])
    {
        $this->order = $order;
        $this->settings = $settings;
    }

    public static function redirect($url, $base_uri = __PS_BASE_URI__, \Link $link = null)
    {
        if (!$link) {
            $link = \Context::getContext()->link;
        }

        if (\Tools::strpos($url, 'http://') === false && \Tools::strpos($url, 'https://') === false && $link) {
            if (strpos($url, $base_uri) === 0) {
                $url = \Tools::substr($url, \Tools::strlen($base_uri));
            }
            if (\Tools::strpos($url, 'index.php?controller=') !== false && \Tools::strpos($url, 'index.php/') == 0) {
                $url = \Tools::substr($url, \Tools::strlen('index.php?controller='));
                if (\Configuration::get('PS_REWRITING_SETTINGS')) {
                    $url = \Tools::strReplaceFirst('&', '?', $url);
                }
            }

            $explode = explode('?', $url);
            // don't use ssl if url is home page
            // used when logout for example
            $use_ssl = !empty($url);
            $url = $link->getPageLink($explode[0], $use_ssl);
            if (isset($explode[1])) {
                $url .= '?' . $explode[1];
            }
        }

        return $url;
    }

    public function createTransaction(int $module_id)
    {
        $api = $this->createService();

        $order = $this->order;
        $customer = new \Customer($order->id_customer);
        $context = \Context::getContext();

        $returnUrl = self::redirect('index.php?controller=order-confirmation&id_cart=' . (int) $order->id_cart . '&id_module=' . $module_id . '&id_order=' . $order->id . '&key=' . $customer->secure_key);

        $cancelUrl = $context->link->getModuleLink('rediconpaypo', 'clone', array('id_cart' => $order->id_cart), true);

        $nofityUrl = $context->link->getModuleLink('rediconpaypo', 'notification', array('order_id' => $order->id), true);

        //configuration
        $configurationPayload = (new ConfigurationPayload())
            ->setNotifyUrl($nofityUrl)
            ->setReturnUrl($returnUrl)
            ->setCancelUrl($cancelUrl)
            ->setInstallmentCount(\Configuration::get('REDICON_PAYPO_INSTALLMENT_COUNT'))
            ->setProductType(\Configuration::get('REDICON_PAYPO_PRODUCT_TYPE'));

        // //customer
        $customer = new \Customer($order->id_customer);
        $address = new \Address($order->id_address_invoice);
        $paymentAddress = ApiHelper::addressPayload($address);
        $deliveryAddress = ApiHelper::addressPayload(new \Address($order->id_address_delivery));

        $phone = ApiHelper::formatPhone(!empty($address->phone) ? $address->phone : $address->phone_mobile);

        $customerPayload = new CustomerPayload();
        $customerPayload->setName($customer->firstname)
            ->setSurname($customer->lastname)
            ->setEmail($customer->email)
            ->setPhone($phone);

        //order
        $orderPayload = new OrderPayload();
        $orderPayload->setAmount(round($order->getOrdersTotalPaid() * 100))
            ->setReferenceId((string) $order->reference)
            ->setBillingAddress($paymentAddress)
            ->setShippingAddress($deliveryAddress);

        //transaction
        $registerTransactionPayload = new RegisterTransactionPayload();
        $registerTransactionPayload->setMerchantId($api->getSetting('id'))
            ->setConfiguration($configurationPayload)
        // ->setShopId((string) $order->id_shop)
            ->setCustomer($customerPayload)
            ->setOrder($orderPayload);

        $jsonPayload = ApiHelper::toJson($registerTransactionPayload);

        // PaypoLog::log($jsonPayload, 'payload');

        $response = $api->registerTransaction($jsonPayload);

        return [
            'response' => $response,
            'payload' => $registerTransactionPayload,
            'json' => $jsonPayload,
        ];
    }

    public function updateTransaction(string $transactionId, string $status)
    {
        if (in_array($status, UpdateTransactionPayload::STATUSES)) {
            $update = new UpdateTransactionPayload();
            $update->setStatus($status);
            return $this->createService()->updateTransaction($transactionId, ApiHelper::toJson($update));
        }
        return false;
    }

    public function retrieveTransaction(string $transactionId)
    {
        return $this->createService()->retrieveTransaction($transactionId);
    }

    public function refundsTransaction(string $transactionId, int $amount)
    {
        $refund = new RefundsPayload();
        $refund->setAmount($amount);

        return $this->createService()->refundsTransaction($transactionId, ApiHelper::toJson($refund));
    }

    private function createService()
    {
        return new ApiService($this->settings);
    }

    public static function jsonDecode(string $response, $class = NotificationResponseObject::class)
    {
        $array = json_decode($response, true);

        try {
            return new $class(
                $array['merchantId'],
                $array['referenceId'],
                $array['transactionId'],
                $array['transactionStatus'],
                $array['amount'],
                $array['lastUpdate'],
                $array['message']
            );
        } catch (\TypeError $e) {
            throw new \Exception($e->getMessage());
        }

        return false;
    }
}
