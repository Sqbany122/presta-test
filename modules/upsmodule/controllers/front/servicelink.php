<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonFunction.php';
require_once dirname(__FILE__) . '/../../common/Constants.php';

class UpsModuleServiceLinkModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        if (Tools::getValue('action') == 'DoHandshake') {
            $this->handshake();
        }
        
        parent::__construct();
    }

    public function handshake()
    {
        $data = Tools::file_get_contents("php://input");
        Configuration::updateValue('SERVICE_DATA', $data);
        $data = json_decode($data, true);

        if ($data['UpsServiceLinkSecurityToken'] == Configuration::get('SECURITY_TOKEN')) {
            if ($data['Command'] == 'PushPreRegistrationToken') {
                Configuration::updateValue('PRE_KEY', $data['PreRegisteredPluginToken']);
            }
        }
    }
}
