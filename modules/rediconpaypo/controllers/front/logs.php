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

require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Helper/ApiHelper.php";

class RediconPaypoLogsModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {

        $token = Tools::getValue('token');
        $date = Tools::getValue('date', date('Y-m-d'));
        if ($this->module::$KEY_ACCESS !== $token || !Validate::isDate($date)) {
            header("HTTP/1.1 403 Bad request");
            exit;
        }

        ApiHelper::logsDb($date);
    }
}
