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

class AdminBestkitLivechatController extends ModuleAdminController
{
	protected function checkIsAdminLogged()
	{
		if ($this->controller_name != 'AdminLogin' && (!isset($this->context->employee) || !$this->context->employee->isLoggedBack())) {
			if (isset($this->context->employee)) {
				$this->context->employee->logout();
			}

			Tools::redirectAdmin($this->context->link->getAdminLink('AdminLogin') . ((!Tools::getIsset('logout') && $this->controller_name != 'AdminNotFound' && Tools::getValue('controller')) ? '&redirect=' . $this->controller_name : ''));
		}
	}

	public function init()
	{
		$this->checkIsAdminLogged();

		$action = Tools::getValue('liveChatAction') . 'Action';
		if (method_exists($this, $action)) {
			$this->module = Module::getInstanceByName('bestkit_livechat');
			$this->module->getConfig('last_admin_activity', time());
			$this->$action();
		}

		die();
	}

	protected function updatingAction()
	{
		$activeUser = Tools::getValue('activeUser');
		$data = array(
			'chats' => $this->module->getUsers(),
			'total_new_messages' => $this->module->getTotalNewMessages(),
			'last_message_time' => $this->module->getLastMessageTime(),
			'active_chat' => $this->module->getAdminDialogHtml($activeUser),
		);

		echo Tools::jsonEncode($data);
	}

	protected function getMessagesAction()
	{
		$user_key = Tools::getValue('user_key');
		if ($user_key) {
			echo $this->module->getAdminDialogHtml(Tools::getValue('user_key'));
		}
	}

	protected function sendMessageAction()
	{
		$user_key = Tools::getValue('user_key');
		$message = pSQL(urldecode(Tools::getValue('message')), true);
		if ($user_key && $message) {
			$data = array(
				'user_key' => $user_key,
				'is_admin' => 1,
				'is_new' => 0,
				'message' => $message,
				'date_add' => time(),
			);

			Db::getInstance()->Execute('
				UPDATE `' . _DB_PREFIX_ . 'bestkit_livechat_message` SET `is_new` = 0 WHERE `user_key` = "' . $user_key . '"
			');

			Db::getInstance()->autoExecute(_DB_PREFIX_ . 'bestkit_livechat_message', $data, 'INSERT');

			echo $this->module->getAdminDialogHtml(Tools::getValue('user_key'));
		}
	}

	protected function deleteChatAction()
	{
		$user_key = Tools::getValue('user_key');
		if ($user_key) {
			Db::getInstance()->Execute('
				DELETE FROM `' . _DB_PREFIX_ . 'bestkit_livechat_user` WHERE `user_key` = "' . $user_key . '";
				DELETE FROM `' . _DB_PREFIX_ . 'bestkit_livechat_message` WHERE `user_key` = "' . $user_key . '";
			');
		}
	}
}
