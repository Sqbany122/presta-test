<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

class AdminRediconPaypoAjaxController extends ModuleAdminController
{
    /**
     * @var RediconPaypo
     */
    public $module;

    public function ajaxProcessRefund()
    {
        $token = Tools::getValue('ajax_token');

        if ($token !== $this->module::$KEY_ACCESS) {
            die('Unautorize');
        }

        $return = 'success';

        if (Tools::getValue('id_order') && Tools::getValue('amount') && Tools::getValue('id_transaction') && Tools::getValue('id_employee')) {

            $return = $this->module->addReturns(Tools::getAllValues());
        }

        $this->ajaxDie(json_encode($return));
    }
}
