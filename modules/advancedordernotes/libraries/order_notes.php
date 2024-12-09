<?php
/**
*    2007-2017 PrestaShop
*
*    NOTICE OF LICENSE
*
*    This source file is subject to the Academic Free License (AFL 3.0)
*    that is bundled with this package in the file LICENSE.txt.
*    It is also available through the world-wide-web at this URL:
*    http://opensource.org/licenses/afl-3.0.php
*    If you did not receive a copy of the license and are unable to
*    obtain it through the world-wide-web, please send an email
*    to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*    @author    PrestaShop SA <contact@prestashop.com>
*    @copyright 2007-2017 PrestaShop SA
*    @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*/

Class OrderNotesCore 
{


	static function get_latest_notes($page = 1)
	{
		if($page <= 0)
			$page = 1;
		
		$offset = (int)($page - 1)*50 ;
		$row_count = 50;


		$sql = 'SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes` ORDER BY id DESC limit '.(int)$offset.', '.(int)$row_count;
		$notes_raw = Db::getInstance()->executeS($sql);

		$notes = array();
		foreach( $notes_raw as $n )
		{
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
		}

	
		return $notes;
	}


	static function get_pagination_count()
	{
		$sql = 'SELECT count(*) FROM `'._DB_PREFIX_.'adv_ordernotes`';
		$q = Db::getInstance()->getRow($sql);

		$page_count = ceil((int)$q['count(*)'] / 50);

		return $page_count;
	}

}