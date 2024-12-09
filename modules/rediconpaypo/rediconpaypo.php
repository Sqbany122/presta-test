<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/vendor/autoload.php';

// przerobienie na potrzeby presty1.6(<php5.3)
// use PrestaShop\Module\Rediconpaypo\Helper\ApiHelper;
// use PrestaShop\Module\Rediconpaypo\Helper\PaypoLog;
// use PrestaShop\Module\Rediconpaypo\Helper\SettingsPaypo;
// use PrestaShop\Module\Rediconpaypo\Paypo;
// use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Helper/ApiHelper.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Helper/PaypoLog.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Helper/SettingsPaypo.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Paypo.php";

require_once _PS_MODULE_DIR_ . "rediconpaypo/classes/PaypoTransaction.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/classes/Statustransaction.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/classes/ReturnTransaction.php";

class RediconPaypo extends PaymentModule
{
    public static $MODULE_NAME = 'rediconpaypo';
    public static $KEY_ACCESS = '68d154934f253g8760h199jjkhgi00138';
    /* @var boolean error */
    protected $_errors = false;

    public $settings = [
        'url' => null,
        'id' => null,
        'secret' => null,
    ];

    public $STATUSES = [];

    private $notOK = false;

    private $tab_name = 'AdminRediconPaypoAjax';

    public $is_eu_compatible;

    public function __construct()
    {
        $this->name = 'rediconpaypo';
        $this->tab = 'payments_gateways';
        $this->version = '1.1.1';
        $this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);
        $this->author = 'Patryk Pawlicki - redicon.pl';
        $this->controllers = array('payment', 'validation', 'clone', 'notification', 'AdminRediconPaypoAjax');
        $this->is_eu_compatible = 1;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        $this->need_instance = 1;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Płatność Paypo');
        $this->description = $this->l('Kupujesz teraz, płacisz za 30 dni z PayPo - bez dodatkowych kosztów.');

        $this->STATUSES = [
            SettingsPaypo::_NEW => $this->l('Paypo - nowy'),
            SettingsPaypo::PENDING => $this->l('Paypo - procesowany'),
            SettingsPaypo::CANCELLED => $this->l('Paypo - anulowany'),
            SettingsPaypo::REJECTED => $this->l('Paypo - odrzucony'),
            SettingsPaypo::ACCEPTED => $this->l('Paypo - zaakceptowany'),
            SettingsPaypo::COMPLETED => $this->l('Paypo - zakończony'),
        ];

        $this->settings = $this->getModuleSettings(Configuration::get('REDICON_PAYPO_CURRENCY'));
        $this->notOK = $this->checkCurrency();
    }

    public function getModuleSettings($cart_iso_code = null)
    {
        if (!in_array($cart_iso_code, SettingsPaypo::ACCEPTED_CURRNCIES)) {
            return [];
        }

        if (Configuration::get('REDICON_PAYPO_ENVIROMENT') === 'sandbox') {
            $settings = [
                'url' => SettingsPaypo::PAYPO_URL[$cart_iso_code]['sandbox'],
                'id' => Configuration::get('REDICON_PAYPO_SANDBOX_ID'),
                'secret' => Configuration::get('REDICON_PAYPO_SANDBOX_SECRET'),
            ];
        } else {
            $settings = [
                'url' => SettingsPaypo::PAYPO_URL[$cart_iso_code]['production'],
                'id' => Configuration::get('REDICON_PAYPO_PRODUCTION_ID'),
                'secret' => Configuration::get('REDICON_PAYPO_PRODUCTION_SECRET'),
            ];
        }

        return $settings;
    }

    private function checkCurrency()
    {
        $currency = new Currency(Configuration::get('REDICON_PAYPO_CURRENCY'));

        return !in_array($currency->iso_code, SettingsPaypo::ACCEPTED_CURRNCIES);
    }

    public static function errorMsg($text)
    {
        return sprintf('[%s] - %s', date("Y-m-d"), $text) . "\n";
    }

    public static function settings($id_currency = null)
    {

        $currency = new Currency($id_currency ? $id_currency : Configuration::get('REDICON_PAYPO_CURRENCY'));
        $obj = new RediconPaypo();
        return $obj->getModuleSettings($currency->iso_code);
    }

    private function getConfirmedUrl($query)
    {
        $ssl = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://';
        $url = $ssl . $this->context->shop->domain . '/modules/rediconpaypo/ajax.php' . $query;
        return $url;
    }

    private function getAdminLink($controller, $withToken = true, $sfRouteParams = [], $params = [])
    {
        if (defined('_PS_VERSION_') && version_compare(_PS_VERSION_, '1.7.0.0', '<=')) {

            if (isset($params['token'])) {
                unset($params['token']);
            }

            $query = http_build_query($params, '', '&');

            $link = $this->context->link->getAdminLink($controller, $withToken);

            return $link . (strpos($link, '?') === false ? '?' : '&') . $query;
        } else {
            return $this->context->link->getAdminLink($controller, $withToken, $sfRouteParams, $params);
        }
    }

    public static function transactionConfirm(Order $order, string $transactionId)
    {

        try {
            $response = (new Paypo($order, self::settings($order->id_currency)))->updateTransaction($transactionId, SettingsPaypo::COMPLETED);

            return $response;
        } catch (Exception $e) {
            PaypoLog::log($e->getMessage());
        }
        return false;
    }

    public static function transactionRefound(Order $order, string $transactionId, int $amount)
    {
        try {
            $response = (new Paypo($order, self::settings($order->id_currency)))->refundsTransaction($transactionId, $amount);

            return $response;
        } catch (Exception $e) {
            PaypoLog::log($e->getMessage());
        }
        return false;
    }

    public function hookPaymentReturn($params)
    {
        if (isset($params['objOrder'])) {
            $order = $params['objOrder'];
        }
        if (isset($params['order'])) {
            $order = $params['order'];
        }

        if (isset($order->id) && $order->id) {
            $carrier = new Carrier($order->id_carrier);

            $replace = [
                '[ORDER_ID]' => $order->id,
                '[REFERENCE]' => $order->reference,
                '[TOTALS]' => Tools::ps_round($order->getOrdersTotalPaid(), 2),
                '[PAYMENT]' => $order->payment,
                '[SHIPPING]' => $carrier->name,
            ];

            $text = Configuration::get('REDICON_PAYPO_CONFIRMATION');
            $text = str_replace(array_keys($replace), array_values($replace), $text);

            return $text;
        }
        return '';
    }

    public function hookDisplayAdminOrderMainBottom($params)
    {
        return $this->hookDisplayAdminOrder($params);
    }

    public function hookDisplayAdminOrder($params)
    {
        /**
         * id_order w wersji 1.6 i 1.7
         */
        if (isset($params['id_order'])) {
            $order = new Order($params['id_order']);

            if ($order->id && $order->module === $this->name) {
                $transaction = PaypoTransaction::getByCartId($order->id_cart);

                $transactionId = isset($transaction['transactionId']) ? $transaction['transactionId'] : null;
                $id_transaction = isset($transaction['id_transaction']) ? $transaction['id_transaction'] : null;

                $notifications = Statustransaction::getByTransactionId($transactionId);
                $max = ((int) PaypoTransaction::getTotalByOrderId($order->id)) / 100;
                $id_employee = $this->context->employee->id;

                $this->context->smarty->assign([
                    'title_payment' => $this->l('PayPo - transakcje'),
                    'title_return' => $this->l('Zwrócone kwoty:'),
                    'text_return' => $this->l('Zwrot paypo'),
                    'text_save' => $this->l('Zapisz'),
                    'text_return_modal' => $this->l('Podaj kwotę zwrotu'),
                    'text_return_label' => $this->l('Kwota zwtoru - max: ') . $max,
                    'text_before' => $this->l('kwota przed: '),
                    'text_after' => $this->l('zwrócono: '),
                    'returns' => ReturnTransaction::getReturns($id_transaction),
                    'transaction' => $transaction,
                    'notifications' => $notifications,
                    'id_order' => $order->id,
                    'id_transaction' => $id_transaction,
                    'max_value' => number_format($max, 2, '.', ''),
                    'show_returns' => Configuration::get('REDICON_PAYPO_RETURNS') == '0',
                    'return_ajax_url' => $this->getAdminLink('AdminRediconPaypoAjax', true, [], [
                        'token' => Tools::getAdminTokenLite('AdminRediconPaypoAjax'),
                        'ajax_token' => self::$KEY_ACCESS,
                        'ajax' => 1,
                        'action' => 'Refund',
                        'id_employee' => $id_employee,
                        'id_order' => $order->id,
                        'id_transaction' => $id_transaction,
                    ])
                ]);


                return $this->display(__FILE__ . '', 'views/templates/admin/order.tpl');
            }
        }
        return '';
    }

    public function hookActionOrderStatusUpdate($params)
    {
        $order = new Order($params['id_order']);

        if ($order->module == $this->name) {
            /**
             * Statusy do anulowania transakcji
             */
            $statuses = [
                (int) Configuration::get('REDICON_PAYPO_STATUS_CANCELLED'),
                (int) Configuration::get('REDICON_PAYPO_STATUS_REJECTED'),
                (int) Configuration::get('PS_OS_CANCELED'),
            ];
            /**
             * Dopuszczalne statusy
             */
            $statusAccepted = [(int) Configuration::get('REDICON_PAYPO_STATUS_COMPLETED')];
            $allStatuses = array_merge($statuses, $statusAccepted);

            $newStatus = (int) $params['newOrderStatus']->id;
            $cartId = (int) $order->id_cart;
            /**
             * Jeśli aktualny status jest w liście
             *
             */
            if (in_array($newStatus, $allStatuses)) {
                $transactionId = PaypoTransaction::getTransactionIdByCartId($cartId);

                $paypoStatus = false;

                /**
                 * Potwierdzenie statusu w paypo po stworzeniu zamówienia
                 */
                if (Configuration::get('REDICON_PAYPO_AUTO_COMPLETED') == '0' && in_array($newStatus, $statusAccepted)) {
                    /**
                     * Akceptacja transakcji dopiero kiedy ma status accepted
                     */
                    $transactionIsAccepted = Statustransaction::getByTransactionIdAndStatus($transactionId, SettingsPaypo::ACCEPTED);
                    if ($transactionIsAccepted) {
                        $paypoStatus = SettingsPaypo::COMPLETED;
                    }
                }

                /**
                 * anulowanie zamówienia w paypo po anulowaniu w prescie
                 */
                if (in_array($newStatus, $statuses)) {
                    $paypoStatus = SettingsPaypo::CANCELED;
                }

                if ($paypoStatus) {
                    try {

                        (new Paypo($order, RediconPaypo::settings($order->id_currency)))
                            ->updateTransaction((string) $transactionId, $paypoStatus);
                            
                    } catch (Exception $e) {
                        PaypoLog::log($e->getMessage());
                    }
                }
            }
        }
        return false;
    }

    public function hookActionValidateCustomerAddressForm($params)
    {
        $form = $params['form'];
        $phoneField = $form->getField("phone");
        $phoneMobileField = $form->getField("phone_mobile");
        $message = $this->l('Poprawny format nr telefonu to 000000000');

        if (is_null($phoneField) && is_null($phoneMobileField)) {
            $message = $this->l('Pole nr telefonu jest wymagane');
            $this->context->controller->errors[] = $message;
            return "0";
        }

        $phone = $phoneField ? $phoneField->getValue() : '';

        if (empty($phone) && !is_null($phoneMobileField)) {
            $phoneField = $form->getField("phone_mobile");
            $phone = $phoneField ? $phoneField->getValue() : '';
        }

        if ($phone && !ApiHelper::formatPhone($phone)) {
            if ($phoneField) {
                $phoneField->addError($message);
            }

            $this->context->controller->errors[] = $message;
            return "0";
        }

        return "1";
    }

    //wersja 1.7+
    public function hookPaymentOptions($params)
    {

        $currency = new Currency($params['cart']->id_currency);

        if (((int) $currency->id !== (int) Configuration::get('REDICON_PAYPO_CURRENCY')) || !in_array($currency->iso_code, SettingsPaypo::ACCEPTED_CURRNCIES)) {
            return false;
        }

        $errors = Tools::getValue('message', '');
        $message = '';
        if ($errors) {
            $message = sprintf('<strong class="text-danger">%s</strong>', $errors);
        }

        $paymentOptions = [];
        $paymentOption = new \PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $paymentOptions[] = $paymentOption
            ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/logo.png'))
            ->setCallToActionText($this->l('Zapłać z PayPo - Kup teraz, zapłać później'))
            ->setModuleName($this->name)
            ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
            ->setAdditionalInformation($message . Configuration::get('REDICON_PAYPO_DESCRIPTION'));
        return $paymentOptions;
    }

    //wersja 1.6 - 1.7
    public function hookPayment($params)
    {

        $currency = new Currency($params['cart']->id_currency);

        if (((int) $currency->id !== (int) Configuration::get('REDICON_PAYPO_CURRENCY')) || !in_array($currency->iso_code, SettingsPaypo::ACCEPTED_CURRNCIES)) {
            return false;
        }

        $message = '';
        if ($errors = Tools::getValue('message', '')) {
            $message = sprintf('<strong class="text-danger">%s</strong>', $errors);
        }

        $this->smarty->assign(
            array(
                'this_logo' => Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/logo.png'),
                'this_path_pp' => $this->name,
                'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/',
                'this_title' => $this->l('Zapłać z PayPo - Kup teraz, zapłać później'),
                'this_desc' => $message . Configuration::get('REDICON_PAYPO_DESCRIPTION'),
            )
        );

        return $this->display(__FILE__, 'payment.tpl');
    }
    public function addReturns($params)
    {
        $order = new Order($params['id_order']);

        if ($order->id) {
            $id_order = (int) $order->id;

            $current_total = (int) PaypoTransaction::getTotalByOrderId($id_order);
            $transaction = PaypoTransaction::getByOrderId($id_order);
            $return_amount = round($params['amount'] * 100);

            if ($current_total >= $return_amount) {
                $save_total = $current_total - $return_amount;

                PaypoLog::log("return_amount: $return_amount, before:$save_total,save_total:$save_total", "total");

                if (isset($transaction['transactionId']) && isset($transaction['id_transaction'])) {
                    $response = self::transactionRefound($order, $transaction['transactionId'], $return_amount);

                    if ($response && $response->getCode()) {
                        $id_employee = $params['id_employee'];

                        if ($status = Configuration::get('REDICON_PAYPO_STATUS_STATUS_RETURN')) {
                            $order->setCurrentState($status);
                        }

                        $save_return = new ReturnTransaction();
                        $save_return->create((int) $transaction['id_transaction'], $current_total, $return_amount, $id_employee);

                        /**
                         * Aktualizowanie nowej wartości koszyka jeśli zwrot został przyjęty
                         */
                        $trans = new PaypoTransaction($transaction['id_transaction']);
                        $trans->total = $save_total;
                        $trans->transactionId = $transaction['transactionId'];
                        $trans->id_order = $id_order;
                        $trans->updated_at = date('Y-m-d H:i:s');
                        $trans->update();

                        return 'success';
                    }
                }
            }

            return ($current_total ? $current_total / 100 : 0);
        }

        return false;
    }
    public function hookActionObjectUpdateAfter($params)
    {
        if (($params['object'] instanceof Order) && Validate::isLoadedObject($order = $params['object'])) {
            if (Configuration::get('REDICON_PAYPO_RETURNS')) {
                if ($order->id && $order->module === $this->name) {
                    $id_order = (int) $order->id;

                    $save_total = (int) PaypoTransaction::getTotalByOrderId($id_order);
                    $transaction = PaypoTransaction::getByOrderId($id_order);
                    $current_total = round($order->getOrdersTotalPaid() * 100);

                    /**
                     * Jeśli się zmieni kwota zamówienia na mniejszą wysłać info do paypo
                     */
                    if ($current_total < $save_total) {
                        $spread = (int) ($save_total - $current_total);

                        PaypoLog::log("spread: $spread,current_total:$current_total,save_total:$save_total", "total");

                        if ($spread > 0) {
                            if (isset($transaction['transactionId'])) {
                                $response = self::transactionRefound($order, $transaction['transactionId'], $spread);
                                if ($response && $response->getCode()) {
                                    /**
                                     * Aktualizowanie nowej wartości koszyka jeśli zwrot został przyjęty
                                     */
                                    $trans = new PaypoTransaction($transaction['id_transaction']);
                                    $trans->total = $current_total;
                                    $trans->transactionId = $transaction['transactionId'];
                                    $trans->id_order = $id_order;
                                    $trans->updated_at = date('Y-m-d H:i:s');
                                    $trans->update();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function getContent()
    {
        $this->savePost();
        return $this->renderForm();
    }

    private function script()
    {
        return '$(document).ready(function(){if($(\'[name="REDICON_PAYPO_PRODUCT_TYPE"]\').val()==\'CORE\'){
                $(\'[name="REDICON_PAYPO_INSTALLMENT_COUNT"]\').closest(\'.form-group\').hide()
                $(\'[name="REDICON_PAYPO_INSTALLMENT_COUNT"]\').val(4)
            }
        $(\'[name="REDICON_PAYPO_PRODUCT_TYPE"]\').change(function(){
            if($(this).val()==\'CORE\'){
                $(\'[name="REDICON_PAYPO_INSTALLMENT_COUNT"]\').closest(\'.form-group\').hide()
                $(\'[name="REDICON_PAYPO_INSTALLMENT_COUNT"]\').val(4)
            }else{
                $(\'[name="REDICON_PAYPO_INSTALLMENT_COUNT"]\').closest(\'.form-group\').show()
                $(\'[name="REDICON_PAYPO_INSTALLMENT_COUNT"]\').val(1)
            }
        });});';
    }

    protected function renderForm()
    {
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $desc = $this->l('Wymagane ustawienie statusów według schematu');
        $country_ro = Country::getByIso('RO');
        $countryLink = $this->getAdminLink('AdminCountries', true, [], ['updatecountry' => '', 'id_country' => $country_ro]);

        if ($this->STATUSES) {
            foreach ($this->STATUSES as $key => $s) {
                $desc .= "<br/>Status-$key - $s";
            }
        }

        $tags = '<br/> [ORDER_ID] - ' . $this->l('id zamówienia');
        $tags .= '<br/> [TOTALS] - ' . $this->l('kwota do zapłaty');
        $tags .= '<br/> [REFERENCE] - ' . $this->l('ID referencyjny');
        $tags .= '<br/> [PAYMENT] - ' . $this->l('metoda płatności');
        $tags .= '<br/> [SHIPPING] - ' . $this->l('metoda wysyłki');

        $currency_alert = $this->notOK ? '<strong class="text-danger font-weight-700">' . $this->l(' - WALUTA Z POZA LISTY!') . '</strong>' : '';

        $fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Ustawienia płatności PayPo') . $currency_alert,
            ),
            'input' => array(
                'currencies' => array(
                    'type' => 'select',
                    'lang' => true,
                    'label' => $this->l('Waluta obsługiwana w PayPo'),
                    'name' => 'REDICON_PAYPO_CURRENCY',
                    'desc' => $this->l('Aktualnie obsługiwane waluty: ') . implode(', ', SettingsPaypo::ACCEPTED_CURRNCIES) . '<br/>' .
                    $this->l('UWAGA! Waluta RON wymaga dodatkowego pola w ustawieniu adresów') . '<br/>' .
                    $this->l('W ustawieniach kraju Rumunia wymagane jest dodanie "State:name" v1.7+ / "Customer:siret" v1.7 >.') .
                    ($country_ro ? ' <a href="' . $countryLink . '">' . $this->l('ustawienia kraju') . '</a>' : ''),
                    'options' => array(
                        'query' => Currency::getPaymentCurrencies($this->id),
                        'id' => 'id_currency',
                        'name' => 'iso_code',
                    ),
                ),
                'description' => array(
                    'type' => 'textarea',
                    'label' => $this->l('Opis / instrukcja płatności'),
                    'name' => 'REDICON_PAYPO_DESCRIPTION',
                    'cols' => 40,
                    'rows' => 10,
                    'autoload_rte' => 'rte',
                ),
                'confirmation' => array(
                    'type' => 'textarea',
                    'label' => $this->l('Podsumowaniem zamówienia'),
                    'name' => 'REDICON_PAYPO_CONFIRMATION',
                    'cols' => 40,
                    'rows' => 10,
                    'autoload_rte' => 'rte',
                    'desc' => $this->l('Użyj tagi aby dynamicznie podmienić dane w tekście:') . $tags,
                ),
                'enviroment' => array(
                    'type' => 'select',
                    'lang' => true,
                    'label' => $this->l('Tryb sandbox / produkcja'),
                    'name' => 'REDICON_PAYPO_ENVIROMENT',
                    'desc' => $this->l('sandbox / produkcja'),
                    'options' => array(
                        'query' => [
                            [
                                'id_option' => 'sandbox',
                                'name' => 'Sandbox',
                            ],
                            [
                                'id_option' => 'production',
                                'name' => 'Produkcja',
                            ],
                        ],
                        'id' => 'id_option',
                        'name' => 'name',
                    ),
                ),
                'AUTO_COMPLETED' => [
                    'type' => 'switch',
                    'lang' => true,
                    'label' => $this->l('Automatyczne zatwierdzanie transakcji w paypo'),
                    'hint' => $this->l('Wymagane jest ustawienie wszystkich statusów zgodnie z instrukcją.'),
                    'name' => 'REDICON_PAYPO_AUTO_COMPLETED',
                    'desc' => $desc,
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'REDICON_PAYPO_AUTO_COMPLETED_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'REDICON_PAYPO_AUTO_COMPLETED_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ],
                'REDICON_PAYPO_RETURNS' => array(
                    'type' => 'switch',
                    'label' => $this->l('Automatyczne zwroty produktów'),
                    'name' => 'REDICON_PAYPO_RETURNS',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'REDICON_PAYPO_RETURNS_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'REDICON_PAYPO_RETURNS_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                    'desc' => $this->l('Przy ustawionej opcji TAK zwrot/edycja ilości produktu będzie przekazana do paypo'),
                ),
                'REDICON_PAYPO_PRODUCT_TYPE' => array(
                    'type' => 'select',
                    'lang' => true,
                    'label' => $this->l('Typ produktu'),
                    'name' => 'REDICON_PAYPO_PRODUCT_TYPE',
                    'desc' => '<script>' . $this->script() . '</script>',
                    'options' => array(
                        'query' => [
                            [
                                "id_option" => "CORE",
                                "name" => "CORE",
                            ],
                            [
                                "id_option" => "PNX",
                                "name" => "PNX",
                            ],
                        ],
                        'id' => 'id_option',
                        'name' => 'name',
                    ),
                ),
                'REDICON_PAYPO_INSTALLMENT_COUNT' => array(
                    'type' => 'text',
                    'label' => $this->l('Ilość rat'),
                    'name' => 'REDICON_PAYPO_INSTALLMENT_COUNT',
                    'class' => 'form-control',
                    'desc' => $this->l('liczba większa od 1!'),
                ),
                //PS_ORDER_RETURN
                'label_production' => array(
                    'type' => 'free',
                    'label' => '',
                    'name' => 'PRODUCTION',
                ),
                'production_id' => array(
                    'type' => 'text',
                    'label' => $this->l('Identyfikator (produkcja)'),
                    'name' => 'REDICON_PAYPO_PRODUCTION_ID',
                    'class' => 'form-control',
                    'desc' => $this->l('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'),
                ),
                'production_secret' => array(
                    'type' => 'text',
                    'label' => $this->l('Secret (producja)'),
                    'name' => 'REDICON_PAYPO_PRODUCTION_SECRET',
                    'class' => 'form-control',
                    'desc' => $this->l('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
                ),
                'label_sandbox' => array(
                    'type' => 'free',
                    'label' => '',
                    'name' => 'SANDBOX',
                ),
                'sandbox_id' => array(
                    'type' => 'text',
                    'label' => $this->l('Identyfikator (sandbox)'),
                    'name' => 'REDICON_PAYPO_SANDBOX_ID',
                    'class' => 'form-control',
                    'desc' => $this->l('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'),
                ),
                'sandbox_secret' => array(
                    'type' => 'text',
                    'label' => $this->l('Secret (sandbox)'),
                    'name' => 'REDICON_PAYPO_SANDBOX_SECRET',
                    'class' => 'form-control',
                    'desc' => $this->l('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
                ),
                'label_url' => array(
                    'type' => 'free',
                    'label' => '',
                    'name' => 'STATUS',
                ),
            ),
            'submit' => array(
                'title' => $this->l('zapisz'),
            ),

        );

        $statuses = $this->getStatuses();

        $fields_form['input']['STATUS_RETURN'] = [
            'type' => 'select',
            'lang' => true,
            'label' => $this->l('Status-RETURN'),
            'name' => 'REDICON_PAYPO_STATUS_STATUS_RETURN',
            'desc' => $this->l('Status po ręcznym zwrocie transakcji do paypo'),
            'options' => array(
                'query' => array_merge([
                    [
                        "id_option" => "0",
                        "name" => "Brak",
                    ],
                ], $statuses),
                'id' => 'id_option',
                'name' => 'name',
            ),
        ];

        foreach (SettingsPaypo::TRANSACTION_STATUSES as $s) {
            $desc = ($s === SettingsPaypo::COMPLETED ? $this->l('Status przy którym wysyłamy potwierdzenie transakcji do paypo') : '');
            $desc = ($s === SettingsPaypo::CANCELLED ? $this->l('Ustawienie tego statusu w zamówieniu spowoduje anulowanie transakcji w paypo') : $desc);

            $fields_form['input']['STATUS_' . $s] = [
                'type' => 'select',
                'lang' => true,
                'label' => $this->l('Status - ') . $s,
                'name' => 'REDICON_PAYPO_STATUS_' . $s,
                'desc' => $desc,
                'options' => array(
                    'query' => $statuses,
                    'id' => 'id_option',
                    'name' => 'name',
                ),
            ];
        }

        if (Shop::isFeatureActive() && Tools::getValue('id_info') == false) {
            $fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso_theme',
            );
        }

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->toolbar_scroll = true;
        $helper->title = $this->displayName;
        $helper->submit_action = 'save' . $this->name;

        $helper->fields_value = [
            'PRODUCTION' => '<br/><h3>' . $this->l('Ustawienia produkcja') . '</h3>',
            'SANDBOX' => '<br/><h3>' . $this->l('Ustawienia sandbox') . '</h3>',
            'STATUS' => '<br/><h3>' . $this->l('Ustawienia statusów') . '</h3>',
            'REDICON_PAYPO_PRODUCT_TYPE' => Configuration::get('REDICON_PAYPO_PRODUCT_TYPE'),
            'REDICON_PAYPO_INSTALLMENT_COUNT' => Configuration::get('REDICON_PAYPO_INSTALLMENT_COUNT'),
            'REDICON_PAYPO_RETURNS' => Configuration::get('REDICON_PAYPO_RETURNS'),
            'REDICON_PAYPO_DESCRIPTION' => Configuration::get('REDICON_PAYPO_DESCRIPTION'),
            'REDICON_PAYPO_CONFIRMATION' => Configuration::get('REDICON_PAYPO_CONFIRMATION'),
            'REDICON_PAYPO_ENVIROMENT' => Configuration::get('REDICON_PAYPO_ENVIROMENT'),
            'REDICON_PAYPO_AUTO_COMPLETED' => Configuration::get('REDICON_PAYPO_AUTO_COMPLETED'),
            'REDICON_PAYPO_PRODUCTION_ID' => Configuration::get('REDICON_PAYPO_PRODUCTION_ID'),
            'REDICON_PAYPO_PRODUCTION_SECRET' => Configuration::get('REDICON_PAYPO_PRODUCTION_SECRET'),
            'REDICON_PAYPO_SANDBOX_ID' => Configuration::get('REDICON_PAYPO_SANDBOX_ID'),
            'REDICON_PAYPO_SANDBOX_SECRET' => Configuration::get('REDICON_PAYPO_SANDBOX_SECRET'),
            'REDICON_PAYPO_CURRENCY' => Configuration::get('REDICON_PAYPO_CURRENCY'),
        ];

        $helper->fields_value['REDICON_PAYPO_STATUS_STATUS_RETURN'] = Configuration::get('REDICON_PAYPO_STATUS_STATUS_RETURN');

        foreach (SettingsPaypo::TRANSACTION_STATUSES as $s) {
            $key = 'REDICON_PAYPO_STATUS_' . $s;
            $helper->fields_value['REDICON_PAYPO_STATUS_' . $s] = Configuration::get($key);
        }

        $url = $this->getModuleUrl('&logs=' . date('Y-m-d'));

        $errors_file = sprintf('<a href="%s">logs:</a>', $url);

        if ($date = Tools::getValue('log_data')) {
            ApiHelper::logsDb($date);
        }

        if (Tools::getValue('logs')) {
            $errors_file .= '<pre>' . json_encode($this->logsDb(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>';
        }

        return $helper->generateForm(array(array('form' => $fields_form))) . $errors_file;
    }
    public function logsDb($date = null)
    {
        $result = [];
        if ($rows = Db::getInstance()->executeS("SELECT DISTINCT DATE_FORMAT(date_add, '%Y-%m-%d') as date_form FROM `" . _DB_PREFIX_ . "paypo_logs`")) {
            foreach ($rows as $row) {
                $_date = date('Y-m-d', strtotime($row['date_form']));
                $result[] = "<a href='" . $this->getModuleUrl('&log_data=' . $_date) . "' target='_blank'>logi z " . $_date . "</a>";
            }
        }

        return $result;
    }
    public static function dirToArray($dir)
    {

        $result = array();

        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", "..", "index.php"))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = self::dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    $file = str_replace(_PS_ROOT_DIR_, '', $dir);
                    $result[] = "<a href='" . $file . DIRECTORY_SEPARATOR . $value . "' target='_blank'>" . $value . "</a>";
                }
            }
        }

        return $result;
    }
    private function defaultDescription()
    {
        return '<p><img src="' . Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/PayPo_baner.png') . '" width="100%" alt="rediconpaypo.logo.png" /></p>
        <p>' . $this->l('Dzięki tej metodzie płatności możesz kupić produkt teraz, a zapłacić za niego w późniejszym terminie.') . 
        ' ' . $this->l('Jeśli korzystasz z PayPo po raz pierwszy, podajesz swoje podstawowe dane, na podstawie których dostajesz decyzję o odroczeniu.') . 
        ' ' . $this->l('PayPo opłaca Twój rachunek w sklepie, a Ty otrzymujesz produkty, sprawdzasz je i płacisz tylko za te, które decydujesz się zatrzymać.') . 
        '</p>';
    }
    private function getStatuses()
    {
        return array_map(function ($t) {
            return [
                'id_option' => $t['id_order_state'],
                'name' => $t['name'],
            ];
        }, OrderState::getOrderStates(Configuration::get('PS_LANG_DEFAULT')));
    }

    private function savePost()
    {
        if (Tools::isSubmit('save' . $this->name)) {
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (Tools::isSubmit('save' . $this->name)) {
                foreach ($_POST as $key => $value) {
                    if (strpos($key, 'REDICON') !== false) {
                        Configuration::updateValue($key, is_array($value) ? implode(',', $value) : $value, strpos($key, 'DESCRIPTION') || strpos($key, 'CONFIRMATION'));
                    }
                }

                if ($country_ro = Country::getByIso('RO')) {
                    if (Configuration::get('REDICON_PAYPO_CURRENCY') == Currency::getIdByIsoCode(SettingsPaypo::CURRENCY_RON)) {

                        Db::getInstance()->execute(
                            'UPDATE ps_address_format SET
                                `format` = REPLACE(`format`,"Country:name","Country:name State:name")
                                WHERE `format` NOT LIKE "%State:name%" AND id_country=' . $country_ro);

                    }
                }

                Tools::redirectAdmin($this->getModuleUrl());
            }
        }
    }

    private function getModuleUrl($uri = '')
    {
        return 'index.php?controller=AdminModules&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . $uri;
    }

    public function install()
    {

        Configuration::updateValue('REDICON_PAYPO_PRODUCT_TYPE', 'core');
        Configuration::updateValue('REDICON_PAYPO_INSTALLMENT_COUNT', '1');
        Configuration::updateValue('REDICON_PAYPO_RETURNS', '1');
        Configuration::updateValue('REDICON_PAYPO_DESCRIPTION', $this->defaultDescription(), true);
        Configuration::updateValue('REDICON_PAYPO_CONFIRMATION', '');
        Configuration::updateValue('REDICON_PAYPO_ENVIROMENT', '');
        Configuration::updateValue('REDICON_PAYPO_AUTO_COMPLETED', '1');
        Configuration::updateValue('REDICON_PAYPO_PRODUCTION_ID', '');
        Configuration::updateValue('REDICON_PAYPO_PRODUCTION_SECRET', '');
        Configuration::updateValue('REDICON_PAYPO_SANDBOX_ID', '');
        Configuration::updateValue('REDICON_PAYPO_SANDBOX_SECRET', '');
        Configuration::updateValue('REDICON_PAYPO_CURRENCY', Configuration::get('PS_CURRENCY_DEFAULT'));

        $this->installTab();

        return parent::install() &&
        $this->sqlInstall() &&
        $this->installOrderState() &&
        $this->registerHook('payment') &&
        $this->registerHook('paymentOptions') &&
        $this->registerHook('actionOrderStatusUpdate') &&
        $this->registerHook('actionValidateCustomerAddressForm') &&
        $this->registerHook('paymentReturn') &&
        $this->registerHook('actionObjectUpdateAfter') &&
        // $this->registerHook('displayAdminOrderMainBottom') &&
        $this->registerHook('displayAdminOrder');
    }

    public function uninstall()
    {
        $sql1 = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'paypo_transactions`';
        $sql2 = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'paypo_status_transaction`';
        $sql3 = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'paypo_returns_transaction`';
        $sql4 = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'paypo_logs`';

        Configuration::deleteByName('REDICON_PAYPO_PRODUCT_TYPE');
        Configuration::deleteByName('REDICON_PAYPO_INSTALLMENT_COUNT');
        Configuration::deleteByName('REDICON_PAYPO_RETURNS');
        Configuration::deleteByName('REDICON_PAYPO_DESCRIPTION');
        Configuration::deleteByName('REDICON_PAYPO_CONFIRMATION');
        Configuration::deleteByName('REDICON_PAYPO_ENVIROMENT');
        Configuration::deleteByName('REDICON_PAYPO_AUTO_COMPLETED');
        Configuration::deleteByName('REDICON_PAYPO_PRODUCTION_ID');
        Configuration::deleteByName('REDICON_PAYPO_PRODUCTION_SECRET');
        Configuration::deleteByName('REDICON_PAYPO_SANDBOX_ID');
        Configuration::deleteByName('REDICON_PAYPO_SANDBOX_SECRET');
        Configuration::deleteByName('REDICON_PAYPO_CURRENCY');

        foreach ($this->STATUSES as $code => $text) {
            Configuration::deleteByName('REDICON_PAYPO_STATUS_' . $code);
        }

        if ($tab = Tab::getInstanceFromClassName($this->tab_name)) {
            $tab->delete();
        }

        if (!parent::uninstall() or
            !Db::getInstance()->execute($sql1) or
            !Db::getInstance()->execute($sql2) or
            !Db::getInstance()->execute($sql3) or
            !Db::getInstance()->execute($sql4)) {
            return false;
        }

        return true;
    }

    private function sqlInstall()
    {
        $sqls = [
            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "paypo_transactions` (
                `id_transaction` int(11) NOT NULL AUTO_INCREMENT,
                `referenceId` varchar(40) DEFAULT NULL,
                `transactionId` varchar(36) DEFAULT NULL,
                `redirectUrl` varchar(500) DEFAULT NULL,
                `id_cart` int(11) NOT NULL DEFAULT '0',
                `id_order` int(11) NOT NULL DEFAULT '0',
                `total` int(11) NOT NULL DEFAULT '0',
                `json` text,
                `completed` tinyint(4) NOT NULL DEFAULT '0',
                `created_at` datetime DEFAULT NULL,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id_transaction`)
              ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;",
            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "paypo_status_transaction` (
                `id_status` int(11) NOT NULL AUTO_INCREMENT,
                `id_transaction` int(11) NOT NULL,
                `referenceId` varchar(40) DEFAULT NULL,
                `transactionId` varchar(36) DEFAULT NULL,
                `transactionStatus` varchar(20) NOT NULL,
                `amount` int(11) NOT NULL,
                `message` varchar(500) DEFAULT NULL,
                `json` text,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id_status`)
              ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;",
            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "paypo_returns_transaction` (
                `id_return` int(11) NOT NULL AUTO_INCREMENT,
                `id_transaction` int(11) NOT NULL,
                `before_amount` int(11) NOT NULL,
                `amount` int(11) NOT NULL,
                `id_employee` int(11) NOT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id_return`)
              ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;",

            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "paypo_logs` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `type` varchar(50) DEFAULT NULL,
                  `message` text,
                  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`)
                ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;",
        ];

        $result = true;
        foreach ($sqls as $sql) {
            if (!Db::getInstance()->execute($sql)) {
                return false;
            }
        }
        return $result;
    }

    public function installOrderState()
    {
        foreach ($this->STATUSES as $code => $text) {
            if (!Configuration::get('REDICON_PAYPO_STATUS_' . $code) || !Validate::isLoadedObject(new OrderState(Configuration::get('REDICON_PAYPO_STATUS_' . $code)))) {
                $order_state = new OrderState();
                $order_state->name = array();
                foreach (Language::getLanguages() as $language) {
                    $order_state->name[$language['id_lang']] = $text;
                }
                $order_state->send_email = false;
                $order_state->color = '#454545';
                $order_state->hidden = false;
                $order_state->delivery = false;
                $order_state->logable = false;
                $order_state->invoice = false;
                $order_state->module_name = $this->name;

                if ($order_state->add()) {
                }

                if (Shop::isFeatureActive()) {
                    $shops = Shop::getShops();
                    foreach ($shops as $shop) {
                        Configuration::updateValue('REDICON_PAYPO_STATUS_' . $code, (int) $order_state->id, false, null, (int) $shop['id_shop']);
                    }
                } else {
                    Configuration::updateValue('REDICON_PAYPO_STATUS_' . $code, (int) $order_state->id);
                }
            }
        }

        return true;
    }

    public function installTab()
    {
        if (!Tab::getIdFromClassName($this->tab_name)) {
            $tab = new Tab();
            $tab->class_name = $this->tab_name;
            $tab->module = $this->name;

            if ($languages = Language::getLanguages(false)) {
                foreach ($languages as $l) {
                    $tab->name[$l['id_lang']] = 'Paypo';
                }
            }

            $tab->id_parent = -1;
            $tab->active = 1;
            if (!$tab->save()) {
                return false;
            }
        }
        return true;
    }
}
