<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

require_once dirname(__FILE__).'/../../config/config.inc.php';
require_once dirname(__FILE__).'/rediconpaypo.php';

$token = Tools::getValue('token');

if ($token !== RediconPaypo::$KEY_ACCESS) {
    die('Unautorize');
}

$rediconpaypo = new RediconPaypo();
$return = 'success';

if (Tools::getValue('id_order') && Tools::getValue('amount') && Tools::getValue('id_transaction') && Tools::getValue('id_employee')) {
    $return = $rediconpaypo->addReturns(Tools::getAllValues());
}

echo "\n".json_encode($return);
exit();
