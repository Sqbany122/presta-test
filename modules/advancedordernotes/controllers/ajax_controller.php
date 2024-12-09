<?php
/**
* 2007-2017 PrestaShop
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
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @version   Release: $Revision: 14011 $
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require(dirname(__FILE__).'/../../../config/config.inc.php');
require(dirname(__FILE__).'/../../../init.php');
require(dirname(__FILE__).'/../advancedordernotes.php');


$adv_path = str_replace('advancedordernotes/controllers', 'advancedordernotes/', dirname(__FILE__));
define("advanced_on_path", $adv_path);

class AjaxController
{

	private $function_pre = 'ajax_';

	public function __construct()
	{
		if (Tools::getIsset('action'))
		{

			if( Tools::getValue('token') != Configuration::get('aon_token'))
				die('bad token');


			$function_name = $this->function_pre.Tools::getValue('action');
			if (method_exists('AjaxController', $function_name))
				$this->$function_name();
		}
	}


	public function only_employees()
	{
		$cookie = new Cookie('psAdmin');

		if (!$cookie->id_employee)
		{
			die();
		}

	}

	public function ajax_add_order_note()
	{
		
		// CHECK IF IS ADMIN 
		$this->only_employees();

		$note = pSQL(Tools::getValue('note'));
		$id_order = (int)(Tools::getValue('hidden_id_order'));
		$date = date('Y-m-d H:i:s');
		$id_employee = (int)(Tools::getValue('hidden_id_employee'));
		$note_status = Tools::getValue('note_status');

		$ajaxorder = new AdvancedOrderNotes();
		echo $ajaxorder->ajax_add_order_note($id_order, $note, $date, $id_employee, $note_status );
		die();


	}


	public function ajax_get_order_act_info()
	{
		$this->only_employees();

		$id_order = (int)Tools::getValue('order_id');

		

		$context = Context::getContext();
		$sql = 'SELECT * FROM  `'._DB_PREFIX_.'adv_ordernotes` WHERE id_order = '.(int)$id_order. ' ORDER BY date ASC';
		$r = Db::getInstance()->executeS($sql);

		$to_send = array();
		$i = 0;
		foreach($r as $res)
		{

			$employee = new Employee($res['id_employee']);

			$os =  Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes_statuses` WHERE name= "'.pSQL($res['note_status']).'" ');


			if($os['id'])
			{
				$to_send[$i]['note_color'] = $os['color'];
				$to_send[$i]['note_background'] = $os['background'];
				$to_send[$i]['note_status'] = $res['note_status'];
			}
			else
			{
				$to_send[$i]['note_color'] ='';
				$to_send[$i]['note_background'] = '';
				$to_send[$i]['note_status'] = '';
			}




			$to_send[$i]['employee_name'] = $employee->lastname;
			$to_send[$i]['message'] = $res['note'];
			$to_send[$i]['date_add'] = $res['date'];
			$i++;

		}


		$context->smarty->assign(array(
				'aon_notes' => $to_send

		));


		$c = new AdvancedOrderNotes;
		$tpl =  $c->display(advanced_on_path, 'views/templates/hook/order_info_packs.tpl');
			
		die($tpl);
	




	}

	public function ajax_check_if_order_has_note()
	{

	
		$this->only_employees();
		$order_ids = Tools::getValue('order_ids');
		$answer = array();

		foreach($order_ids as $id_order)
		{

			$sql = 'SELECT count(id) FROM  `'._DB_PREFIX_.'adv_ordernotes` WHERE id_order = '.(int)$id_order;
			$r = Db::getInstance()->getRow($sql);


			if($r['count(id)'] > 0)
				$answer[$id_order] = $r['count(id)'];

		}

		die(

			Tools::jsonEncode(
					$answer
				)	

			);


	}


	public function ajax_search_orders()
	{

		// CHECK IF IS ADMIN 
		
		$this->only_employees();

		$id_order = Tools::getValue('id_order');
		$customer_name = Tools::getValue('customer_name');
		$email = Tools::getValue('email');
		$phone = Tools::getValue('phone');

		$sql = 'SELECT * FROM  `'._DB_PREFIX_.'address`   psa 
		INNER JOIN  `'._DB_PREFIX_.'orders` pso 
		ON 
		pso.id_address_delivery = psa.id_address
		INNER  JOIN  `'._DB_PREFIX_.'customer` psc
		ON 
		pso.id_customer = psc.id_customer  

		INNER  JOIN  `'._DB_PREFIX_.'adv_ordernotes` pon
		ON 
		pon.id_order = pso.id_order  


		WHERE 1=1 ';

		if(!empty($phone))
			$sql .= ' 
			AND
				( psa.phone LIKE "%'.pSQL($phone).'%" 
					OR 
				psa.phone_mobile LIKE "%'.pSQL($phone).'%" ) ';

		if(!empty($customer_name))
			$sql .= ' 
			AND
				( psc.lastname LIKE "%'.pSQL($customer_name).'%" 
					OR 
				psc.firstname LIKE "%'.pSQL($customer_name).'%" ) ';


		if(!empty($email))
			$sql .= ' 
			AND
				( psc.email = "'.pSQL($email).'"  ) ';

		if(!empty($id_order))
			$sql .= ' 
			AND
				( pso.id_order = "'.pSQL($id_order).'"  ) ';

		
		$addresses = Db::getInstance()->executeS($sql);
		
		$notes = array();

		foreach($addresses as $n):

			$info = array();
			$order_info = new Order($n['id_order']);
			$customer_info = new Customer($order_info->id_customer);
			$address = new Address($order_info->id_address_delivery);
			$employee = new Employee($n['id_employee']);

			$info['id_order'] = $n['id_order'];
			$info['id_customer'] = $order_info->id_customer;
			$info['full_name'] = $customer_info->lastname . ' ' .$customer_info->firstname;
			$info['email'] = $customer_info->email;

			if(!empty($address->phone))
				$info['phone_number'] = $address->phone;
			else
				$info['phone_number'] = $address->phone_mobile;	
						
			$info['country'] = $address->country;

			if(!empty($address->address1))
				$info['address'] = $address->address1;
			else
				$info['address'] = $address->address2;	
				
			$info['note'] = $n['note'];
			$info['date'] = $n['date'];
			$info['city'] = $address->city;	
			$info['employee'] = Tools::substr($employee->firstname,0 , 1).'. '.$employee->lastname;

			$notes[] = $info;

		endforeach;


		die(
			Tools::jsonEncode($notes)
			);

	}

}