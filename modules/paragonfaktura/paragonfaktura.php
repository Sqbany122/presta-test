<?php

if (!defined('_PS_VERSION_'))
    exit;

class paragonfaktura extends Module {

    private $wi;
    public $d = 'fds';

    public function __construct() {
        $this->name = 'paragonfaktura';
        $this->tab = 'billing_invoicing';
        $this->version = 1.0;
        $this->author = 'Createin.pl';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Invoice or bill');
        $this->description = $this->l('This module allows choice invoice or bill in order.');
    }

    public function install() {
        Db::getInstance()->Execute('CREATE TABLE `' . _DB_PREFIX_ . 'pf` (id_cart int(10), choice int(1))');
        Configuration::updateValue('PF_DEFAULT', 2);
        return (parent::install()) && $this->registerHook('displayAdminOrder') && $this->registerHook('displayShoppingCartFooter');
    }

    public function uninstall() {
        Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'pf`');
        parent::uninstall();
    }

    public function hookDisplayAdminOrder($params) {
        global $smarty, $cookie;
        $id_order = (int) (Tools::getValue('id_order'));
        $order = new Order($id_order);

        $ch = Db::getInstance()->ExecuteS('SELECT choice FROM `' . _DB_PREFIX_ . 'pf` WHERE id_cart = ' . $order->id_cart);

        $type = '';
        if (isset($ch[0]))
            $choice = $ch[0]['choice'];
        else
            $choice = Configuration::get('PF_DEFAULT');

        if ($choice == 1)
            $type = $this->l('Invoice');
        else
            $type = $this->l('Bill');


        $this->context->smarty->assign(
                array(
                    'type' => $type
                )
        );
        $this->context->smarty->assign(
                array(
                    'id_order' => $id_order
                )
        );
        return $this->display(__FILE__, 'paragonfaktura.tpl');
    }

    public function hookdisplayShoppingCartFooter($params) {
        global $smarty, $cookie;
        $db = Db::getInstance();
        $this->context->controller->addJS(($this->_path) . 'save.js');
        $pf = Configuration::get('PF_DEFAULT');
        $ch = 0;
        if (isset($cookie->id_cart)) {

            $ch = $db->ExecuteS('SELECT choice FROM `' . _DB_PREFIX_ . 'pf` WHERE id_cart = ' . $cookie->id_cart);
        }

        if (count($ch) != 0)
            $pf = $ch[0]['choice'];
        $this->context->smarty->assign(
                array(
                    'type' => $pf,
                    'id_cart' => $cookie->id_cart
                )
        );
        return $this->display(__FILE__, 'cart.tpl');
    }

    public function getContent() {
        $output = null;
        $output = '<h2>' . $this->displayName . '</h2>';
        if (Tools::isSubmit('submit' . $this->name)) {
            Configuration::updateValue('PF_DEFAULT', $_POST['pf_default']);

            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        return $output . $this->displayForm();
    }

    public function displayForm() {
        $pf = Configuration::get('PF_DEFAULT');

        $output = '
		<form action="' . Tools::safeOutput($_SERVER['REQUEST_URI']) . '" method="post">
			<fieldset><legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />' . $this->l('Settings') . '</legend>
				<label>' . $this->l('default') . '</label>
				<div class="margin-form">
					<select name="pf_default">
						<option value="1"';
        if ($pf == 1)
            $output .= 'selected';
        $output .= '>' . $this->l('Invoice') . '</option>
						<option value="2" ';
        if ($pf == 2)
            $output .= 'selected';
        $output .= '>' . $this->l('Bill') . '</option>
					</select>
				</div>
				<center><input type="submit" name="submitparagonFaktura" value="' . $this->l('Save') . '" class="button" /></center>
			</fieldset>
		</form>';
        return $output;
    }

}

?>