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

class Ets_livechatOauthModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        try
        {
            $hybridauth = new Hybridauth\Hybridauth($this->module->getLoginConfigs());
            if (!($providers = $hybridauth->getProviders()))
            {
                echo $this->module->closePopup();
                exit;
            }
            $storage = new Hybridauth\Storage\Session();
            if (($sProvider = $storage->get('provider')))
            {
                $hybridauth->disconnectAdapters($sProvider);
                $storage->clear();
            }
            if (($provider = Tools::getValue('provider', false)) && in_array($provider, $providers))
            {
                $storage->set('provider', $provider);
            }
            if (isset($this->context->cookie->soloProvider) && $this->context->cookie->soloProvider)
            {
                $hybridauth->disconnectAdapters($this->context->cookie->soloProvider);
            }
            elseif ($hybridauth->getConnectedProviders())
            {
                $hybridauth->disconnectAllAdapters();
            }
            if (($provider = $storage->get('provider')))
            {
                $hybridauth->authenticate($provider);
            }
        }
        catch (Hybridauth\Exception\Exception $exception)
        {
            die(Tools::jsonEncode($exception->getMessage()));
        }
        Tools::redirectLink($this->context->link->getModuleLink($this->module->name, 'oauth', array('provider' => $provider), Tools::usingSecureMode()? true : false));
    }
}