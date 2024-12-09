<?php
/**
 * 2007-2019 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2019 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
	exit;

class Ets_livechatCallbackModuleFrontController extends ModuleFrontController
{
	public $errors = array();
	public function __construct()
	{	
       parent::__construct();
        if (!$this->module->is17)
        {
            if (isset($this->display_column_right)) $this->display_column_right = false;
            if (isset($this->display_column_left)) $this->display_column_left = false;
        }
	}
	public function initContent()
	{
		parent::initContent();
		try
        {
            $hybridauth = new Hybridauth\Hybridauth($this->module->getLoginConfigs());
            $storage = new Hybridauth\Storage\Session();
            if (($provider = $storage->get('provider')))
            {
                if (!(isset($this->context->cookie->soloProvider)) || !$this->context->cookie->soloProvider || $this->context->cookie->soloProvider != $provider)
                {
                    $this->context->cookie->soloProvider = $provider;
                    $this->context->cookie->write();
                }
                $adapter = $hybridauth->getAdapter($provider);
                
                $adapter->authenticate();
                
                //$accessToken = $adapter->getAccessToken();
                
                $userProfile = $adapter->getUserProfile();
                return $this->etsProsessProfile($userProfile,$storage,$provider);
            }
        }
        catch (Hybridauth\Exception\Exception $exception)
        {
            die(Tools::jsonEncode($exception->getMessage()));
        }
        if (!$this->context->customer->isLogged()){
            Tools::redirectLink($this->context->link->getPageLink('index', Tools::usingSecureMode()? true : false));
        }
	}
    
    public function etsProsessProfile($userProfile = false, $storage = false, $provider = false){
        if (empty($userProfile->email)){
            if (($identifierEmail = Db::getInstance()->getValue('SELECT email FROM '._DB_PREFIX_.'ets_livechat_social_customer WHERE identifier="'.pSQL($userProfile->identifier).'"')))
            {
                $userProfile->email = $identifierEmail;
            }
            if (($registerEmail = Tools::getValue('email', null))) {
                if (!Validate::isEmail($registerEmail)) {
                    $this->errors[] = $this->l('Email is invalid');
                } elseif(Customer::customerExists($registerEmail)) {
                    $this->errors[] = $this->l('Email is exist. Please input email other.');
                } else {
                    $userProfile->email = $registerEmail;
                }
            }
        }
        if (empty($userProfile->email)){
            $this->context->smarty->assign(array(
                'action' => $this->context->link->getModuleLink($this->module->name, 'callback', array('provider' => $provider), true),
                'errors' => $this->errors? $this->module->displayError($this->errors) : false,
                'userProfile' =>$userProfile,
            ));
            return $this->setTemplate(($this->module->is17?'module:'.$this->module->name.'/views/templates/front/' :'').'register.tpl');
        }
        else{
            if (($id_customer = Customer::customerExists($userProfile->email, true, true))) {
                $customer = new Customer($id_customer);
                if ($this->module->is17)
                    $this->context->updateCustomer($customer);
                else
                    $this->module->updateContext($customer);
                $this->module->trackingLogin($customer, $provider);
            } else {
                $this->module->createUser($userProfile, $provider);
            }
            if ($storage){
                $storage->set('provider', null);
            }
            if (!$this->errors) {
                echo $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/hook/frontJs.tpl');
                exit;
            }
        }
    }
}
