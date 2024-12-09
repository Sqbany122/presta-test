<?php
/**
 * 2007-2013 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *         DISCLAIMER   *
 * *************************************** */
 /* Do not edit or add to this file if you wish to upgrade Prestashop to newer
 * versions in the future.
 * ****************************************************
 *
 *  @author     BEST-KIT.COM (contact@best-kit.com)
 *  @copyright  http://best-kit.com
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Bestkit_livechatchatModuleFrontController extends
    ModuleFrontController
{
	public function run()
	{
		$userKey = $this->module->initUser();
		$message = pSQL(urldecode(Tools::getValue('message', '')), true);

		if (Tools::getValue('message', '')) {
			if ((int)Tools::getValue('need_login')) {
				$captcha = urldecode(Tools::getValue('captcha', ''));
				$secret = $this->module->getConfig('recaptcha_secretkey');

				$curl = curl_init();
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
				curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, array(
			    	'secret' => $secret,
			    	'response' => $captcha,
			    ));
	
				$content = (array)Tools::jsonDecode(curl_exec($curl));
				curl_close($curl);

				if (isset($content['success']) && $content['success'] == true) {
					$_SESSION['livechat_recaptcha'] = 'success';
				} else {
					die('captcha_error');
				}

				$data = array(
					'user_key' => $userKey,
					'name' => pSQL(urldecode(Tools::getValue('name')), true),
					'email' => pSQL(Tools::getValue('email'), true),
					'date_add' => time(),
				);

				Db::getInstance()->autoExecute(_DB_PREFIX_ . 'bestkit_livechat_user', $data, 'INSERT');
			}

			if (isset($_SESSION['livechat_recaptcha']) && $_SESSION['livechat_recaptcha'] == 'success') {
				$data = array(
					'user_key' => $userKey,
					'is_admin' => 0,
					'is_new' => 1,
					'message' => $message,
					'date_add' => time(),
				);

				Db::getInstance()->autoExecute(_DB_PREFIX_ . 'bestkit_livechat_message', $data, 'INSERT');
			}
		}

		echo $this->module->getUserDialogHtml($userKey);
	}
}
