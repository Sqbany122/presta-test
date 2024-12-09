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

if (!defined('_PS_VERSION_'))
	exit;

class AdvancedOrderNotes extends Module
{
	public $require_email;
	public $product_button;
	public $fake_order;
	public $show_on_leave;
	public $under_shopping;
	public $_html;
	public $cart_navigation;



	public function __construct()
	{
		$this->name = 'advancedordernotes';
		$this->tab = 'front_office_features';
		$this->version = '1.1.4';
		$this->author = 'graphicvision';
		$this->need_instance = 1;

		$this->module_key = 'd74ef1fb220d228552656e9eff0cf24e';
		parent::__construct();
		$this->bootstrap = 1;

		$this->displayName = $this->l('Advanced Order Notes');
		$this->description = $this->l('Allows your employees to add notes about orders and clients.');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall the Advanced Order Notes?');
	
	}

	public function install()
	{
		
		if (!parent::install() ||  !$this->registerHook('Header') || !$this->registerHook('displayBackOfficeHeader') || !$this->registerHook('displayAdminOrder') || !$this->installDb())
			return false;
			
		$this->createAdminTabs();
		return true;
	}


	private function installModuleTab($title, $class_sfx = '', $parent = '')
    {
        $class = 'Admin'.Tools::ucfirst($this->name).Tools::ucfirst($class_sfx);

 		Configuration::updateValue('aon_comments_show', 'order');
 		Configuration::updateValue('aon_note_status', 1);

        @copy(_PS_MODULE_DIR_.$this->name.'/logo.png', _PS_IMG_DIR_.'t/'.$class.'.png');
        
        if ($parent == '')
        {
            # validate module
            $position = Tab::getCurrentTabId();
        } 
        else 
        {
            # validate module
            $position = Tab::getIdFromClassName($parent);
        }

        $tab1 = new Tab();
        $tab1->class_name = $class;
        $tab1->module = $this->name;
        $tab1->id_parent = (int)$position;
        $langs = Language::getLanguages(false);

        foreach ($langs as $l) 
        {
            # validate module
            $tab1->name[$l['id_lang']] = $title;
        }

        $tab1->add(true, false);
    }


    private function uninstallModuleTab($class_sfx = '')
    {
        $tab_class = 'Admin'.Tools::ucfirst($this->name).Tools::ucfirst($class_sfx);

        $id_tab = Tab::getIdFromClassName($tab_class);
        if ($id_tab != 0) {
            $tab = new Tab($id_tab);
            $tab->delete();
            return true;
        }
        return false;
    }




	private function createAdminTabs()
	{	
		@copy(dirname(__FILE__).'/logo.png', _PS_ROOT_DIR_.'/img/t/AdminAdvancedOrderNotes.gif');
			


            
        $class = 'Admin'.Tools::ucfirst($this->name).'Management';
        $tab1 = new Tab();
        $tab1->class_name = $class;
        $tab1->module = $this->name;
        $tab1->id_parent = 0;
        $langs = Language::getLanguages(false);
        foreach ($langs as $l) 
        {
            $tab1->name[$l['id_lang']] = $this->l('Order Notes');
        }
        
        $tab1->add(true, false);

        $this->installModuleTab('Dashboard', 'Dashboard', 'Admin'.Tools::ucfirst($this->name).'Management');




	    $comments_show = Configuration::get('aon_token');

		if(!$comments_show)
			Configuration::updateValue('aon_token', md5(_PS_BASE_URL_.rand(10000)));





	}

	public function uninstall()
	{
		$this->uninstallTab();
		parent::uninstall();

		return true;
	}





	private function uninstallTab()
	{
		$tab_id = Tab::getIdFromClassName('AdvancedOrderNotes');
		if ($tab_id)
		{
			$tab = new Tab($tab_id);
			$tab->delete();
		}
		
		$tab_id = Tab::getIdFromClassName('AdminAdvancedOrderNotes');
		if ($tab_id)
		{
			$tab = new Tab($tab_id);
			$tab->delete();
		}
		

		$tab_id = Tab::getIdFromClassName('AdminAdvancedordernotesManagement');
		if ($tab_id)
		{
			$tab = new Tab($tab_id);
			$tab->delete();
		}
		
		$tab_id = Tab::getIdFromClassName('AdminAdvancedOrderNotesDashboard');
		if ($tab_id)
		{
			$tab = new Tab($tab_id);
			$tab->delete();
		}
		

		$sql = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'adv_ordernotes`';
		Db::getInstance()->execute($sql);


		$this->uninstallModuleTab( 'Dashboard' );

		@unlink(_PS_ROOT_DIR_.'/img/t/AdminAdvancedOrderNotes.gif');

		return true;
	}


	public function installDb()
	{

		 $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'adv_ordernotes` (
					`id` int(99) NOT NULL AUTO_INCREMENT,
					`id_order` int(99) NOT NULL,
					`id_employee` int(11) NOT NULL,
					`note` text NOT NULL,
					`note_status` varchar(255) NOT NULL,
					`date` datetime NOT NULL,
					`status` int(1) NOT NULL,
					PRIMARY KEY (`id`),
  					KEY `id` (`id`)
					) ENGINE= '._MYSQL_ENGINE_.' CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ';

		 Db::getInstance()->execute($sql);
		 $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'adv_ordernotes_statuses` (
					`id` int(99) NOT NULL AUTO_INCREMENT,
					`name` varchar(255) NOT NULL,
					`background` varchar(255) NOT NULL,
					`color` varchar(255) NOT NULL,
					PRIMARY KEY (`id`),
  					KEY `id` (`id`)
					) ENGINE= '._MYSQL_ENGINE_.' CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ';


		Db::getInstance()->execute($sql);


		$c = Db::getInstance()->getRow('SELECT count(*) FROM `'._DB_PREFIX_.'adv_ordernotes_statuses`');
		
		if($c['count(*)'] == 0)
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'adv_ordernotes_statuses` ( id, name, background, color ) VALUES ( NULL, "DONE", "#dff0d8", "#3c763d") , ( NULL, "INFO", "#31708f", "#d9edf7"),  ( NULL, "CANCELED", "#a94442", "#f2dede")');


		$c = Db::getInstance()->executeS('SHOW FIELDS FROM  `'._DB_PREFIX_.'adv_ordernotes`');

		$need_collumn = 1;
		$need_collumn2 = 1;

		foreach($c as $q)
		{

			if($q['Field'] == 'note_status')
				$need_collumn = 0;

			if($q['Field'] == 'id_employee')
				$need_collumn2 = 0;

		}

		if($need_collumn == 1)
	   		Db::getInstance()->execute(	'ALTER TABLE `'._DB_PREFIX_.'adv_ordernotes` ADD COLUMN note_status varchar(255) NOT NULL');


		if($need_collumn2 == 1)
	   		Db::getInstance()->execute(	'ALTER TABLE `'._DB_PREFIX_.'adv_ordernotes` ADD COLUMN id_employee int(11) NOT NULL');


		return true;
	}



	public function hookdisplayAdminOrder()
	{

		$id_order = (int) Tools::getValue('id_order');
		$comments_show = Configuration::get('aon_comments_show');
		$note_status = Configuration::get('aon_note_status');



		if ($comments_show == 'phone'):

			$order_info = new Order($id_order);
			$id_address_invoice = $order_info->id_address_delivery;
			$address_invoice = new Address($id_address_invoice);
			$ai_phone = $address_invoice->phone;
			$ai_phone_mobile = $address_invoice->phone_mobile;



			if( !empty($ai_phone) && !empty($ai_phone_mobile) )
			{
				$sql = 'SELECT * FROM `'._DB_PREFIX_.'address` WHERE phone LIKE "%'.pSQL($ai_phone).'%" OR phone_mobile LIKE "%'.pSQL($ai_phone_mobile).'%"';
			}
			elseif(!empty($ai_phone) && empty($ai_phone_mobile) )
			{
				$sql = 'SELECT * FROM `'._DB_PREFIX_.'address` WHERE phone LIKE "%'.pSQL($ai_phone).'%"';
			}
			elseif(empty($ai_phone) && !empty($ai_phone_mobile) )
			{
				$sql = 'SELECT * FROM `'._DB_PREFIX_.'address` WHERE  phone_mobile LIKE "%'.pSQL($ai_phone_mobile).'%"';
			}

			$get_all_address = Db::getInstance()->executeS($sql);
			$ids = array();
			foreach($get_all_address as $gad){
				$sql = 'SELECT * FROM `'._DB_PREFIX_.'orders` WHERE id_address_delivery = '.(int)$gad['id_address'].' OR id_address_invoice ='.(int)$gad['id_address'];
				$s =  Db::getInstance()->ExecuteS($sql);
				
				foreach($s as $x)
					$ids[] = $x['id_order'];

			}

			$ids = implode(',', $ids);
			$sql = 'SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes` WHERE id_order IN ('.pSQL($ids).')';
			$notes = Db::getInstance()->executeS($sql);
		elseif($comments_show == 'order'):
			$sql = 'SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes` WHERE id_order = '.(int)$id_order;
			$notes = Db::getInstance()->executeS($sql);
		elseif($comments_show == 'email'):
			$order_info = new Order($id_order);
			$customer = new Customer($order_info->id_customer);
			$sql = 'SELECT * FROM `'._DB_PREFIX_.'customer` pc INNER JOIN `'._DB_PREFIX_.'orders` po ON pc.id_customer = po.id_customer INNER JOIN `'._DB_PREFIX_.'adv_ordernotes` pon ON pon.id_order = po.id_order  WHERE pc.email = "'.pSQL($customer->email).'"';
			$notes = Db::getInstance()->executeS($sql);
		endif;


		$size = 0;
		$count_size = 0;

		if( isset($notes[0]))
			$size = 1;

		$count_size = count($notes);
		
		$context = Context::getContext();
		$note_filter = array();
		$id_employee = $context->cookie->id_employee;


		$employees = Employee::getEmployees();
		

		$i = 1;
		


		if(!empty($notes))
		{
			foreach($notes as $note)
			{

				$employee_name = '';

				foreach ($employees as $emp)
				{

					if(isset($emp['id_employee']) && isset($note['id_employee']))
					{
						if($emp['id_employee'] == $note['id_employee'] )
							$employee_name = $emp['firstname'].' '.$emp['lastname'];					
					}


				}

				$os =  Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes_statuses` WHERE name= "'.pSQL($note['note_status']).'" ');

				$note_filter[$i]['note_color'] ='';
				$note_filter[$i]['note_background'] = '';

				if($os['id'])
				{
					$note_filter[$i]['note_color'] = $os['color'];
					$note_filter[$i]['note_background'] = $os['background'];
				}

				$note_filter[$i]['nr'] = $i;
				$note_filter[$i]['note'] = $note['note'];
				$note_filter[$i]['note_status'] = $note['note_status'];

				$note_filter[$i]['employee'] = $employee_name;
				$note_filter[$i]['date'] = $note['date'];
				$i++;
			}			
		}



		$this->context->smarty->assign(array(
		
		    'id_order' => $id_order,
		    'notes' => $note_filter,
		    'size' => $size,
		    'count_size' => $count_size,
		    'note_status' => $note_status,
		    'note_statuses' => self::getAllNoteStatuses(),
		    'id_employee' => $id_employee,
		    'aon_token' => Configuration::get('aon_token')
		));


		return $this->display(__FILE__, '/views/templates/hook/detail_tab.tpl');
	}


	public function hookdisplayBackOfficeHeader()
	{

		$token = Configuration::get('aon_token');

		$this->context->smarty->assign(array(
			    '_path' => $this->_path,
			    'aon_token' => $token
		));
		$html  = '';

		if(   Tools::getValue('controller') == 'Adminclientordernotes' ||  Tools::getValue('controller')  == 'AdminOrders' )
		{
			if(Tools::getIsset('id_order'))
				$html .=  $this->display(__FILE__, '/views/templates/admin/defines.tpl');
			else
				$html .=  $this->display(__FILE__, '/views/templates/admin/defines_backoffice.tpl');

		}

		return $html;
	}


	public function ajax_add_order_note($id_order, $note, $date, $id_employee, $note_status = '')
	{


		$sql = 'SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes` WHERE id_order = '.(int)$id_order;
		$notes = Db::getInstance()->executeS($sql);

		$sql = 'INSERT INTO `'._DB_PREFIX_.'adv_ordernotes` (id, id_order, id_employee, note, date, status, note_status ) VALUES (NULL, '.(int)$id_order .', '.(int)$id_employee.', "'.pSQL($note).'", "'.pSQL($date).'", 1, "'.pSQL($note_status).'")';
		Db::getInstance()->execute($sql);
		
		$employees = Employee::getEmployees();
		$employee_name = '';

		foreach ($employees as $emp){

			if($emp['id_employee'] == $id_employee )
				$employee_name = $emp['firstname'].' '.$emp['lastname'];

		}

		$os =  Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes_statuses` WHERE name= "'.pSQL($note_status).'" ');


		$context = Context::getContext();
		if($os['id'])
		{
			$context->smarty->assign(array(
				    'note_color' => $os['color'],
				    'note_status' => $note_status,
				    'note_background' =>  $os['background']
				));

		}
		else
		{
			$context->smarty->assign(array(
					'note_status' => '',
				    'note_color' => '',
				    'note_background' =>  ''
				));
		}




		$context->smarty->assign(array(
			    'notes_count' => (count($notes)+1),
			    'note' => $note,
			    'employee_name' => $employee_name,
			    'date' => $date
			));


		return $this->display(__FILE__, '/views/templates/admin/note_row.tpl');


	}


	public function displayForm()
	{

	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	    	
	       if (version_compare(_PS_VERSION_, '1.6', '>=')) 
	        {
	            $radio_switch = 'switch';
	        } else 
	        {
	            $radio_switch = 'radio';
	        }


	    $fields_form = array();
	    $fields_form[0]['form'] = array(

	        'legend' => array(
	            'title' => $this->l('Settings'),
	        ),

	        'input' => array(


            	array(
					  'type' => 'select',
					  'label' => $this->l('Group notes by'),
					  'name' => 'product_button',
					  'required' => false,
					  'desc' => $this->l('Phone = Show same ( universal notes ) at every order with that phone number |||  Order = Show only notes related to that order;'),
					  'lang' => false,
					  'options' => array(
						        'query' => array(
									array('key' => 'phone', 'name' => 'Phone'),
									array('key' => 'email', 'name' => 'Email'),
									array('key' => 'order', 'name' => 'Order')
						        ),
						        'id' => 'key',
						        'name' => 'name',
						    )
					),


   						 array(
	                        'type' => $radio_switch,
	                        'class' => 't', 
	                        'is_bool' => true, 
	                        'lang' => false,
	                        'label' => $this->l('Note status'),
	                        'name' => 'note_status',
	                        'desc' => $this->l('Enable the option to select a status when adding a order note.'),
	                        'values' => array(
	                            array(
	                                'id' => 'active_on',
	                                'value' => 1,
	                                'label' => $this->l('Yes')
	                            ),
	                            array(
	                                'id' => 'active_off',
	                                'value' => 0,
	                                'label' => $this->l('No')
	                            )
	                        ),
	                    ),
			          


			  		 array(
					            'type'  => 'textarea',
					            'name'  => 'note_statuses',
					            'label' => $this->l('Note statuses'),
					            'desc' => $this->l('One note status per line, the characteristics are NAME,TEXT_COLOR,BG_COLOR'),
					    ),


	        ),
	        'submit' => array(
	            'title' => $this->l('Save'),
	            'class' => 'btn btn-default pull-right'
	        )
	    );
	     



	    $helper = new HelperForm();
	     
	    // Module, token and currentIndex
	    $helper->module = $this;
	    $helper->name_controller = $this->name;
	    $helper->token = Tools::getAdminTokenLite('AdminModules');
	    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
	     
	    // Language
	    $helper->default_form_language = $default_lang;
	    $helper->allow_employee_form_lang = $default_lang;
	     
	    // Title and toolbar

	    $helper->title = $this->displayName;
	    $helper->show_toolbar = true;
	    $helper->toolbar_scroll = true;
	    $helper->submit_action = 'submit'.$this->name;

	    $helper->toolbar_btn = array(
	        'save' =>
	        array(
	            'desc' => $this->l('Save'),
	            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
	            '&token='.Tools::getAdminTokenLite('AdminModules'),
	        ),
	        'back' => array(
	            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
	            'desc' => $this->l('Back to list')
	        )
	    );
	     
	    // Load current value
	    $helper->fields_value['product_button'] = Configuration::get('aon_comments_show');
	    $helper->fields_value['note_status'] = Configuration::get('aon_note_status');



	    $statuses = self::getAllNoteStatuses(true);
	    $textarea = '';
	    foreach($statuses as $st)
	    {
	    	$textarea .= $st['name'].','.$st['color'].','.$st['background'].PHP_EOL;
	    }

	    $helper->fields_value['note_statuses'] = rtrim($textarea);

	    return $helper->generateForm($fields_form);
	}


	public static function getAllNoteStatuses($no_id = false)
	{
		if($no_id == true)
			return Db::getInstance()->executeS('SELECT name,background,color FROM  `'._DB_PREFIX_.'adv_ordernotes_statuses`');
		else
			return Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'adv_ordernotes_statuses`');	
	}

	public function getContent()
	{

	    $output = null;
	    $comments_show = Configuration::get('aon_token');


		if(!$comments_show)
			Configuration::updateValue('aon_token', md5(_PS_BASE_URL_.rand(10000)));


	 
	    if (Tools::isSubmit('submit'.$this->name))
	    {
			Configuration::updateValue('aon_comments_show', Tools::getValue('product_button'));
			Configuration::updateValue('aon_note_status', Tools::getValue('note_status'));	

			$note_statuses = Tools::getValue('note_statuses');
	
			$expl = array_filter(explode(PHP_EOL, $note_statuses));
			Db::getInstance()->execute('TRUNCATE   `'._DB_PREFIX_.'adv_ordernotes_statuses`');

			foreach($expl as $e)
			{
				$n = array_filter(explode(',', $e));
				Db::getInstance()->execute('INSERT INTO   `'._DB_PREFIX_.'adv_ordernotes_statuses` ( id, name, background, color ) VALUES ( NULL, "'.rtrim(ltrim($n[0])).'","'.rtrim(ltrim($n[1])).'","'.rtrim(ltrim($n[2])).'")');
		
			}

			Configuration::updateValue('aon_note_statuses', Tools::getValue('note_statuses'));	

	        $output .= $this->displayConfirmation($this->l('Settings updated'));
	    }

	    return $output.$this->displayForm();

	}


}