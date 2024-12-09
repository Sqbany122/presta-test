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
require_once(dirname(__FILE__).'/classes/src/autoload.php');
if(!class_exists('LC_Conversation') && file_exists(dirname(__FILE__).'/classes/LC_Conversation.php'))
    require_once(dirname(__FILE__).'/classes/LC_Conversation.php');
if(!class_exists('LC_Message') && file_exists(dirname(__FILE__).'/classes/LC_Message.php'))
    require_once(dirname(__FILE__).'/classes/LC_Message.php');
if(!class_exists('LC_Download') && file_exists(dirname(__FILE__).'/classes/LC_Download.php'))
    require_once(dirname(__FILE__).'/classes/LC_Download.php');
if(!class_exists('LC_Departments') && file_exists(dirname(__FILE__).'/classes/LC_Departments.php'))
    require_once(dirname(__FILE__).'/classes/LC_Departments.php');
if(!class_exists('LC_Ticket_form') && file_exists(dirname(__FILE__).'/classes/LC_Ticket_form.php'))
    require_once(dirname(__FILE__).'/classes/LC_Ticket_form.php');
if(!class_exists('LC_Ticket_field') && file_exists(dirname(__FILE__).'/classes/LC_Ticket_field.php'))
    require_once(dirname(__FILE__).'/classes/LC_Ticket_field.php');
if(!class_exists('LC_Ticket') && file_exists(dirname(__FILE__).'/classes/LC_Ticket.php'))
    require_once(dirname(__FILE__).'/classes/LC_Ticket.php');
if(!class_exists('LC_Note') && file_exists(dirname(__FILE__).'/classes/LC_Note.php'))
    require_once(dirname(__FILE__).'/classes/LC_Note.php');
if(!class_exists('LC_paggination_class') && file_exists(dirname(__FILE__).'/classes/LC_paggination_class.php'))
    require_once(dirname(__FILE__).'/classes/LC_paggination_class.php');
class Ets_livechat extends Module
{
    private $errorMessage;
    public $lc_configs;
    public $baseAdminPath;
    private $_html;
    public $emotions = array();
    public $url_module;
    public $all_shop=false;
    public $errors = array();
    public $shops = array();
    public $is17=false;
    public $is16=false;
    public $file_types=array();
    public function __construct()
	{
		$this->name = 'ets_livechat';
		$this->tab = 'front_office_features';
		$this->version = '2.0.6';
		$this->author = 'ETS-Soft';
		$this->need_instance = 0;
		$this->secure_key = Tools::encrypt($this->name);        
		$this->bootstrap = true;
        $this->module_key = 'aa56a4e99fd9c76076df96cce0242bac';
        $this->is_configurable=1;
		parent::__construct();
        $this->file_types=array("doc","docx",'pdf','zip','rar','rar5','xlsx','xls','txt','png','jpg','jpeg','gif','ppt','pptx','csv');
        $this->url_module = $this->_path;
        $this->displayName = $this->l('Live Chat And Ticketing System');
		$this->description = $this->l('Give customers support via live chat and support tickets. No monthly fee, life-time use, can chat with 1000+ customers at the same time.');
		$this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => _PS_VERSION_);
        //Emotion icons
        if (version_compare(_PS_VERSION_, '1.7', '>='))
			$this->is17 = true;
		elseif(version_compare(_PS_VERSION_, '1.6', '>='))
            $this->is16 = true;
        if(Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && Shop::getContext() == Shop::CONTEXT_ALL)
        {
            $this->all_shop = true;
            $this->shops = Shop::getShops(false);
        }  
        
        //Configuration::updateValue('ETS_LC_BUBBLE_IMAGE','chatbubble.png');
        $this->emotions = array(
            ':(('=>array(
                'img' => 'crying.gif',
                'title' => $this->l('Crying'),
            ),
            ':))'=>array(
                'img' => 'laughing.gif',
                'title' => $this->l('Laughing'),
            ),
            ':(|)'=>array(
                'img' => 'monkey.gif',
                'title' => $this->l('Monkey'),
            ),
            ':(' => array(
                'img' => 'sad.gif',
                'title' => $this->l('Sad'),                
            ),
            '|:D/'=>array(
                'img' => 'dancing.gif',
                'title' => $this->l('Dancing'),
            ),
            '>:D>' => array(
                'img' => 'big hug.gif',
                'title' => $this->l('Big hug'),                
            ),
            ':D' => array(
                'img' => 'big grin.gif',
                'title' => $this->l('Big grin'),                
            ),
            ';;)' => array(
                'img' => 'batting eyelashes.gif',
                'title' => $this->l('batting eyelashes'),                
            ),
            ';))'=>array(
                'img' => 'hee hee.gif',
                'title' => $this->l('Hee hee'),
            ),
            ';)' => array(
                'img' => 'winking.gif',
                'title' => $this->l('Winking'),                
            ),
            ':-/' => array(
                'img' => 'confused.gif',
                'title' => $this->l('Confused'),                
            ),
            ':x'=>array(
                'img' => 'love struck.gif',
                'title' => $this->l('Love struck'),
            ),
            ':">'=>array(
                'img' => 'blushing.gif',
                'title' => $this->l('Blushing'),
            ),
            '>:P'=>array(
                'img' => 'phbbbbt.gif',
                'title' => $this->l('Phbbbbt'),
            ),
            ':P'=>array(
                'img' => 'tongue.gif',
                'title' => $this->l('Tongue'),
            ),
            ':-*'=>array(
                'img' => 'kiss.gif',
                'title' => $this->l('Kiss'),
            ),
            '=(('=>array(
                'img' => 'broken heart.gif',
                'title' => $this->l('Broken heart'),
            ),
            '3:-O'=>array(
                'img' => 'cow.gif',
                'title' => $this->l('Cow'),
            ),
            ':-O'=>array(
                'img' => 'surprise.gif',
                'title' => $this->l('Surprise'),
            ),
            'X('=>array(
                'img' => 'angry.gif',
                'title' => $this->l('Angry'),
            ),
            '~:>'=>array(
                'img' => 'chicken.gif',
                'title' => $this->l('Chicken'),
            ),
            ':>'=>array(
                'img' => 'smug.gif',
                'title' => $this->l('Smug'),
            ),
            'B-)'=>array(
                'img' => 'cool.gif',
                'title' => $this->l('Cool'),
            ),
            '#:-S'=>array(
                'img' => 'whew.gif',
                'title' => $this->l('Whew!'),
            ),
            ':-SS'=>array(
                'img' => 'nailbiting.gif',
                'title' => $this->l('Nailbiting'),
            ),
            ':-S'=>array(
                'img' => 'worried.gif',
                'title' => $this->l('Worried'),
            ),
            '>:)'=>array(
                'img' => 'devil.gif',
                'title' => $this->l('Devil'),
            ),
            '(:|'=>array(
                'img' => 'yawn.gif',
                'title' => $this->l('Yawn'),
            ),
            ':|'=>array(
                'img' => 'straight face.gif',
                'title' => $this->l('straight face'),
            ),
            '/:)'=>array(
                'img' => 'raised eyebrow.gif',
                'title' => $this->l('Raised eyebrow'),
            ),
            '=))'=>array(
                'img' => 'rolling on the floor.gif',
                'title' => $this->l('rolling on the floor'),
            ),
            'O:)'=>array(
                'img' => 'angel.gif',
                'title' => $this->l('Angel'),
            ),
            ':-B'=>array(
                'img' => 'nerd.gif',
                'title' => $this->l('Nerd'),
            ),
            '=;'=>array(
                'img' => 'talk to the hand.gif',
                'title' => $this->l('Nerd'),
            ),
            ':-??'=>array(
                'img' => 'i do not know.gif',
                'title' => $this->l('I don\'t know'),
            ),
            '%-('=>array(
                'img' => 'not listening.gif',
                'title' => $this->l('Not listening'),
            ),
            ':@)'=>array(
                'img' => 'pig.gif',
                'title' => $this->l('Pig'),
            ),
            
            '@};-'=>array(
                'img' => 'rose.gif',
                'title' => $this->l('Rose'),
            ),
            '%%-'=>array(
                'img' => 'good luck.gif',
                'title' => $this->l('Good luck'),
            ),
            '~O)'=>array(
                'img' => 'coffee.gif',
                'title' => $this->l('Coffee'),
            ),
            '*-:)'=>array(
                'img' => 'idea.gif',
                'title' => $this->l('Idea'),
            ),
            '8-X'=>array(
                'img' => 'skull.gif',
                'title' => $this->l('Skull'),
            ),
            '=:)'=>array(
                'img' => 'bug.gif',
                'title' => $this->l('Bug'),
            ),
            '>-)'=>array(
                'img' => 'alien.gif',
                'title' => $this->l('Alien'),
            ),
            ':-L'=>array(
                'img' => 'frustrated.gif',
                'title' => $this->l('Frustrated'),
            ),
            '[-O>'=>array(
                'img' => 'praying.gif',
                'title' => $this->l('Praying'),
            ),
            ':-c'=>array(
                'img' => 'call me.gif',
                'title' => $this->l('Call me'),
            ),
            ':)]'=>array(
                'img' => 'on the phone.gif',
                'title' => $this->l('On the phone'),
            ),
            '~X('=>array(
                'img' => 'at wits.gif',
                'title' => $this->l('At wits\' end'),
            ),
            ':-h'=>array(
                'img' => 'wave.gif',
                'title' => $this->l('Wave'),
            ),
            ':-t'=>array(
                'img' => 'time out.gif',
                'title' => $this->l('Time out'),
            ),
            '8->'=>array(
                'img' => 'daydreaming.gif',
                'title' => $this->l('Daydreaming'),
            ),
            'I-|'=>array(
                'img' => 'sleepy.gif',
                'title' => $this->l('Sleepy'),
            ),
            '8-|'=>array(
                'img' => 'rolling eyes.gif',
                'title' => $this->l('Rolling eyes'),
            ),
            'L-)'=>array(
                'img' => 'loser.gif',
                'title' => $this->l('loser'),
            ),
            ':-&'=>array(
                'img' => 'sick.gif',
                'title' => $this->l('Sick'),
            ),
            ':-$'=>array(
                'img' => 'do not tell anyone.gif',
                'title' => $this->l('Don\'t tell anyone'),
            ),
            '[-('=>array(
                'img' => 'not talking.gif',
                'title' => $this->l('Not talking'),
            ),
            ':O)'=>array(
                'img' => 'clown.gif',
                'title' => $this->l('Clown'),
            ),
            '8-}'=>array(
                'img' => 'silly.gif',
                'title' => $this->l('Silly'),
            ),
            '>:-P'=>array(
                'img' => 'party.gif',
                'title' => $this->l('Party'),
            ),
            '=P~'=>array(
                'img' => 'drooling.gif',
                'title' => $this->l('Drooling'),
            ),
            ':-?'=>array(
                'img' => 'thinking.gif',
                'title' => $this->l('thinking'),
            ),
            '#-o'=>array(
                'img' => 'doh.gif',
                'title' => $this->l('D\'oh'),
            ),
            '=D>'=>array(
                'img' => 'applause.gif',
                'title' => $this->l('Applause'),
            ),
            
            '@-)'=>array(
                'img' => 'hypnotized.gif',
                'title' => $this->l('Hypnotized'),
            ),
            ':^o'=>array(
                'img' => 'liar.gif',
                'title' => $this->l('Liar'),
            ),
            ':-w'=>array(
                'img' => 'waiting.gif',
                'title' => $this->l('Waiting'),
            ),
            ':->'=>array(
                'img' => 'sigh.gif',
                'title' => $this->l('Sigh'),
            ),
            
            '<):)'=>array(
                'img' => 'cowboy.gif',
                'title' => $this->l('Cowboy'),
            ),
            '$-)'=>array(
                'img' => 'money eyes.gif',
                'title' => $this->l('Money eyes'),
            ),
            ':-"'=>array(
                'img' => 'whistling.gif',
                'title' => $this->l('Whistling'),
            ),
            'b-('=>array(
                'img' => 'feeling beat up.gif',
                'title' => $this->l('Feeling beat up'),
            ),
            ':)>-'=>array(
                'img' => 'peace sign.gif',
                'title' => $this->l('peace sign'),
            ),
            '[-X'=>array(
                'img' => 'shame on you.gif',
                'title' => $this->l('Shame on you'),
            ),
            '>:/'=>array(
                'img' => 'bring it on.gif',
                'title' => $this->l('Bring it on'),
            ),
            ':-@'=>array(
                'img' => 'chatterbox.gif',
                'title' => $this->l('chatterbox'),
            ),
            '^:)^'=>array(
                'img' => 'not worthy.gif',
                'title' => $this->l('Not worthy'),
            ),
            ':)' => array(
                'img' => 'happy.gif',
                'title' => $this->l('Happy'),                
            ),
            ':-j'=>array(
                'img' => 'oh go on.gif',
                'title' => $this->l('Oh go on'),
            ),
            ':-j'=>array(
                'img' => 'oh go on.gif',
                'title' => $this->l('Oh go on'),
            ),
        );
        $this->lc_configTabs = array(
            'status' => $this->l('Statuses'),
            'chat_box' => $this->l('Chat box'),
            'im' => $this->l('IM'),
            'privacy'=>$this->l('Privacy'), 
            'fields'=> $this->l('Fields'), 
            'email'=>$this->l('Email'),
            'security'=>'Security' ,
            'timing'=>$this->l('Timing'), 
            'display' =>$this->l('Display'),
            'sound'=>$this->l('Sound'),
            'auto_reply'=> $this->l('Auto reply'),
            'pre_made_message' => $this->l('Pre-made messages'),
            'sosial' => $this->l('Social login'),
            //'staffs' => $this->l('staffs'),
            //'departments' => $this->l('Departments'),
            'back_list_ip' => $this->l('IP black list'),
            'clearer' => $this->l('Clean-up'),
            
            //'help' => $this->l('Help'),
            //'ticket_system' => $this->l('Ticket system'),
        );     
    }
    /**
	 * @see Module::uninstall()
	 */
	public function uninstall()
	{
        return parent::uninstall() && $this->_uninstallDb() && $this->_uninstallTabs() ;
    }
    /**
	 * @see Module::install()
	 */
    public function install()
	{
	   if(Module::isInstalled('ets_livechat_free'))
            return false;
	    return parent::install()        
        && $this->registerHook('displayHeader')
        && $this->registerHook('displayBackOfficeHeader')
        && $this->registerHook('actionAuthentication')
        && $this->registerHook('actionCustomerLogoutAfter')
        && $this->registerHook('displayBackOfficeFooter')
        && $this->registerHook('DisplayBlockOnline')
        && $this->registerHook('DisplayBlockBusy')
        && $this->registerHook('DisplayBlockInvisible')
        && $this->registerHook('DisplayBlockOffline')
        && $this->registerHook('displayStaffs')
        && $this->registerHook('displaySystemTicket')
        && $this->registerHook('customerAccount')
        && $this->registerHook('displayMyAccountBlock')
        && $this->registerHook('moduleRoutes')
        && $this->registerHook('displayLeftColumn')
        && $this->registerHook('displayFooter')
        && $this->registerHook('displayRightColumn')
        && $this->registerHook('displayNav') 
        && $this->registerHook('displayNav1') 
        && $this->registerHook('customBlockSupport')      
        && $this->_installDb() && $this->createTemplateMail() && $this->_installTabs();        
    }   
    public function _installDb()
    {
        $languages = Language::getLanguages(false);
        chmod(dirname(__FILE__).'/ets_livechat_search_customer.php',0644);
        chmod(dirname(__FILE__).'/ets_livechat_ajax.php',0644);
        chmod(dirname(__FILE__).'/download.php',0644);
        chmod(dirname(__FILE__).'/../ets_livechat',0755);
        $res = Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_auto_msg` (
              `id_auto_msg` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
              `message_order` int(11) NOT NULL,
              `auto_content` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute("CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ets_livechat_conversation` (
              `id_conversation` int(10) AUTO_INCREMENT PRIMARY KEY ,
              `id_customer` int(11) NOT NULL DEFAULT '0',
              `id_ticket` int(11) NOT NULL DEFAULT '0',
              `id_shop` INT(11) NOT NULL,
              `blocked` tinyint(4) NOT NULL DEFAULT '0',
              `archive` int(1) NOT NULL,
              `customer_writing` tinyint(1) NOT NULL DEFAULT '0',
              `employee_writing` tinyint(1) NOT NULL DEFAULT '0',
              `date_message_seen_customer` datetime DEFAULT NULL,
              `date_message_seen_employee` datetime DEFAULT NULL,
              `captcha_enabled` tinyint(1) NOT NULL DEFAULT '0',
              `customer_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
              `customer_email` varchar(255) NOT NULL,
              `customer_phone` varchar(255) NOT NULL,
              `latest_online` datetime DEFAULT NULL,
              `latest_ip` varchar(255) DEFAULT NULL,
              `browser_name` varchar(222) NOT NULL,
              `id_departments` int(11) NOT NULL,
              `id_departments_wait` int(11) NOT NULL,
              `id_employee` int(11) NOT NULL,
              `id_employee_wait` int(11) NOT NULL,
              `id_tranfer` int(11) NOT NULL,
              `date_accept` datetime DEFAULT NULL,
              `datetime_added` datetime DEFAULT NULL,
              `date_message_writing_employee` datetime NOT NULL,
              `date_message_writing_customer` datetime NOT NULL,
              `date_message_delivered_employee` datetime NOT NULL,
              `date_message_delivered_customer` datetime NOT NULL,
              `date_message_last` datetime NOT NULL,
              `date_message_last_customer` datetime NOT NULL,
              `date_mail_last` datetime NOT NULL,
              `rating` int(1) NOT NULL,
              `end_chat` INT(11),
              `message_deleted` text,
              `message_edited` text,
              `employee_message_deleted` text,
              `employee_message_edited` text,
              `replied` INT(1), 
              `current_url` VARCHAR(1000) NOT NULL,
              `http_referer` VARCHAR(1000) NOT NULL,
              `enable_sound` int(1) NOT NULL DEFAULT '1',
              `note` text,
              `chatref` INT(11)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8");
        $res &=Db::getInstance()->execute("CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ets_livechat_ip_address` (
            `ip_address` varchar(222) NOT NULL,
            `latitude` varchar(222) NOT NULL,
            `longitude` varchar(222) NOT NULL
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8");  
        $res &=Db::getInstance()->execute("CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ets_livechat_message` (
          `id_message` int(11) AUTO_INCREMENT PRIMARY KEY ,
          `id_conversation` int(11) unsigned NOT NULL,
          `id_employee` int(10) NOT NULL,
          `id_product` int(10) NOT NULL,
          `message` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
          `type_attachment` VARCHAR(222) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
          `name_attachment` VARCHAR(222) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
          `delivered` tinyint(1) NOT NULL DEFAULT '0',
          `datetime_added` datetime DEFAULT NULL,
          `datetime_edited` datetime DEFAULT NULL
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8");
        $res &=Db::getInstance()->execute("CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ets_livechat_download` (
          `id_download` int(11) AUTO_INCREMENT PRIMARY KEY ,
          `id_message` int(11) unsigned NOT NULL,
          `id_ticket` int(11),
          `id_field` int(11), 
          `id_note` int(11),
          `id_conversation` int(11) unsigned NOT NULL,
          `file_type` VARCHAR(222) NOT NULL,
          `file_size` FLOAT(11,2) NOT NULL,
          `filename` VARCHAR(222) CHARACTER SET utf8 COLLATE utf8_bin
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8");
        $res &=Db::getInstance()->execute("CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ets_livechat_pre_made_message` (
          `id_pre_made_message` int(11) AUTO_INCREMENT PRIMARY KEY ,
          `title_message` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
          `message_content` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
          `short_code` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
          `position` int(11) NOT NULL
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8");   
        $res &=Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_departments` ( 
            `id_departments` INT(11) NOT NULL AUTO_INCREMENT , 
            `name` VARCHAR(222) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL , 
            `description` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, 
            `all_employees` INT(11) NOT NULL,
            `sort_order` INT(11),
            `status` INT(1) NOT NULL , PRIMARY KEY (`id_departments`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_departments_employee` ( 
            `id_departments` INT(11) NOT NULL , 
            `id_employee` INT(11) NOT NULL ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_employee_online`( 
            `id_employee` INT(11) NOT NULL ,
            `id_shop` INT(11) NOT NULL ,
            `date_online` DATETIME NOT NULL ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance() ->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_url` (
        `id_conversation` INT(11),
        `url` VARCHAR(1000) NOT NULL , 
        `date_add` DATETIME NOT NULL ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_staff` ( 
        `id_employee` INT(11) NOT NULL , 
        `name` VARCHAR(222) NOT NULL , 
        `avata` VARCHAR(222) NOT NULL , 
        `signature` VARCHAR(222) NOT NULL , 
        `status` INT(1) NOT NULL ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_staff_decline` ( 
        `id_employee` INT(11) NOT NULL , 
        `id_conversation` INT(11) NOT NULL ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_employee_status` ( 
            `id_employee` INT(11) NOT NULL ,
            `id_shop` INT(11) NOT NULL ,
            `status` VARCHAR (222) NOT NULL ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_ticket_form` ( 
            `id_form` INT(11) NOT NULL AUTO_INCREMENT ,
            `active` INT(1),
            `id_shop` INT(11),
            `mail_new_ticket` VARCHAR(222),
            `custom_mail` VARCHAR(222),
            `send_mail_to_customer` INT(1),
            `send_mail_reply_customer` INT(1),
            `send_mail_reply_admin` INT(1),
            `customer_reply_upload_file` INT(1),
            `allow_user_submit` INT(1),
            `save_customer_file` INT(1),
            `save_staff_file` INT(1),
            `require_select_department` INT(1),
            `departments` VARCHAR(222),
            `allow_captcha` INT(11),
            `customer_no_captcha` INT(1),
            `deleted` INT(11), 
            `sort_order` INT(11),
            `default_priority` INT(2),
            PRIMARY KEY (`id_form`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_ticket_form_field` ( 
            `id_field` INT(11) NOT NULL AUTO_INCREMENT ,
            `id_form` INT(11), 
            `type` VARCHAR(222),
            `is_contact_mail` INT(1),
            `is_contact_name` INT (1),  
            `is_subject` INT(1),
            `is_customer_phone_number` INT(1),
            `required` INT (1),
            `deleted` INT (1),
            `position` INT (11),     
            PRIMARY KEY (`id_field`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');  
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_ticket_form_lang` ( 
            `id_form` INT(11),
            `id_lang` INT (11),
            `title` VARCHAR(222) NOT NULL , 
            `button_submit_label` VARCHAR(222) NOT NULL , 
            `description` TEXT NOT NULL , 
            `friendly_url` VARCHAR(222) NOT NULL , 
            `meta_title` VARCHAR(222) NOT NULL , 
            `meta_description` TEXT NOT NULL , 
            `meta_keywords` TEXT NOT NULL
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8'); 
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_ticket_form_field_lang` ( 
            `id_field` INT(11),
            `id_lang` INT (11),
            `label` VARCHAR (222),
            `placeholder` TEXT NOT NULL,
            `description` TEXT NOT NULL,
            `options` VARCHAR(222) NOT NULL) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS`'._DB_PREFIX_.'ets_livechat_customer_info` ( 
            `id_customer` INT(11) NOT NULL , 
            `avata` VARCHAR(222) NOT NULL ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_ticket_form_message` ( 
        `id_message` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_form` INT(11) NOT NULL , 
        `id_shop` INT(11) NOT NULL ,
        `id_departments` INT(11),
        `id_customer` INT(11) NOT NULL , 
        `status` VARCHAR(22),
        `priority` INT(2),
        `rate` INT(11),
        `readed` INT(1),
        `replied` INT(1),
        `id_employee` INT(11),
        `customer_readed` INT(1),
        `subject` text,
        `date_customer_update` DATETIME NOT NULL ,
        `date_admin_update` DATETIME NOT NULL,
        `date_add` DATETIME NOT NULL , PRIMARY KEY (`id_message`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_ticket_form_message_field` ( 
        `id_message` INT(11) NOT NULL , 
        `id_field` INT(11) NOT NULL , 
        `id_download` INT(11),
        `value` TEXT NOT NULL ) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_ticket_form_message_note` ( 
        `id_note` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_message` INT(11) NOT NULL , 
        `id_employee` INT(11) NOT NULL,
        `id_download` INT(11),
        `note` text,
        `readed` INT(1),
        `file_name` text,
        `date_add` DATETIME NOT NULL , PRIMARY KEY (`id_note`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_social_login` ( 
        `id_social_login` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_customer` INT(11) NOT NULL , 
        `social` VARCHAR(22) NOT NULL,
        `date_login` DATETIME NOT NULL , PRIMARY KEY (`id_social_login`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS`'._DB_PREFIX_.'ets_livechat_social_customer`( 
        `identifier` VARCHAR(222) NOT NULL , 
        `email` VARCHAR(222) NOT NULL ) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        Tools::copy(dirname(__FILE__).'/views/img/temp/customeravata.jpg',dirname(__FILE__).'/views/img/config/customeravata.jpg');
        Tools::copy(dirname(__FILE__).'/views/img/temp/chatbubble.png',dirname(__FILE__).'/views/img/config/chatbubble.png');
        Tools::copy(dirname(__FILE__).'/views/img/temp/adminavatar.jpg',dirname(__FILE__).'/views/img/config/adminavatar.jpg');
        $this->setConfig();
        if($this->lc_configs)
        {
            foreach($this->lc_configs as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values,true);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '',true);
            }
        } 
        Configuration::updateValue('ETS_CONVERSATION_DISPLAY_ADMIN',1);
        $this->createFormDefault();
        $this->updateLastAction();
        $shops = Db::getInstance()->executeS("SELECT * FROM "._DB_PREFIX_."shop");
        $employees = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'employee');
        if($shops)
        {
            foreach($shops as $shop)
            {
                if($employees)
                {
                    foreach($employees as $employee)
                    {
                        Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_livechat_employee_status(id_employee,id_shop,status) VALUES("'.(int)$employee['id_employee'].'","'.(int)$shop['id_shop'].'","online")');
                    }
                }
            }
        }
        return $res;
    } 
    public function _installTabs()
    {
        $languages = Language::getLanguages(false);
        $tab = new Tab();
        $tab->class_name = 'AdminLiveChat';
        $tab->module = 'ets_livechat';
        $tab->id_parent = 0;            
        foreach($languages as $lang){
            $tab->name[$lang['id_lang']] = $this->l('Live Chat and Support');
        }
        $tab->save();
        $tabId = Tab::getIdFromClassName('AdminLiveChat');
        if($tabId)
        {
            $subTabs = array(
                array(
                    'class_name' =>'AdminLiveChatDashboard',
                    'tab_name' => $this->l('Dashboard'),
                    'icon'=>'icon icon-dashboard',
                ),
                array(
                    'class_name' => 'AdminLiveChatTickets',
                    'tab_name' => $this->l('Tickets'),
                    'icon'=>'icon icon-ticket',
                ),
                array(
                    'class_name' => 'AdminLiveChatSettings',
                    'tab_name' => $this->l('Settings'),
                    'icon'=>'icon-AdminAdmin',
                ),
                array(
                    'class_name' => 'AdminLiveChatHelp',
                    'tab_name' => $this->l('Help'),
                    'icon'=>'icon icon-question-circle',
                ),
            );
            foreach($subTabs as $tabArg)
            {
                $tab = new Tab();
                $tab->class_name = $tabArg['class_name'];
                $tab->module = 'ets_livechat';
                $tab->id_parent = $tabId; 
                $tab->icon=$tabArg['icon'];           
                foreach($languages as $lang){
                        $tab->name[$lang['id_lang']] = $tabArg['tab_name'];
                }
                $tab->save();
            }                
        }            
        return true;
    } 
    private function _uninstallTabs()
    {
        $tabs = array('AdminLiveChatDashboard','AdminLiveChatTickets','AdminLiveChatSettings','AdminLiveChatHelp');
        if($tabs)
        foreach($tabs as $classname)
        {
            if($tabId = Tab::getIdFromClassName($classname))
            {
                $tab = new Tab($tabId);
                if($tab)
                    $tab->delete();
            }                
        }
        if($tabId = Tab::getIdFromClassName('AdminLiveChat'))
        {
            $tab = new Tab($tabId);
            if($tab)
                $tab->delete();
        }
        return true;
    }  
    private function _uninstallDb()
    {
        $this->setConfig();
        if($this->lc_configs)
        {
            foreach($this->lc_configs as $key => $config)
            {
                Configuration::deleteByName($key);                
            }
            unset($config);
        }   
        Configuration::deleteByName('ETS_CONVERSATION_DISPLAY_ADMIN');
        Configuration::deleteByName('ETS_LC_DATE_ACTION_LAST');   
        foreach (glob(dirname(__FILE__).'/views/img/config/*.*') as $filename) {
            if($filename!=dirname(__FILE__).'/views/img/config/index.php')
                @unlink($filename);
        }
        foreach (glob(dirname(__FILE__).'/downloads/*.*') as $filename) {
            if($filename!=dirname(__FILE__).'/downloads/index.php')
                @unlink($filename);
        }
        $res = Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_auto_msg');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_conversation');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_ip_address');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_message');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_pre_made_message');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_download');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_url');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_departments');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_departments_employee');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_staff');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_employee_online');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_employee_status');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_staff_decline');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_ticket_form');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_ticket_form_lang');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_ticket_form_field');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_ticket_form_field_lang');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_customer_info');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_ticket_form_message');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_ticket_form_message_field');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_ticket_form_message_note');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_social_login');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ets_livechat_social_customer');
        return  $res;
    } 
    public function createTemplateMail(){
        $languages= Language::getLanguages(false);
        foreach($languages as $language)
        {
            if (!file_exists(dirname(__FILE__).'/mails/'.$language['iso_code'])) {
                mkdir(dirname(__FILE__).'/mails/'.$language['iso_code'], 0755, true);
                if($language['is_rtl'])
                {
                    Tools::copy(dirname(__FILE__).'/mails/he/admin_new_ticket_admin.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/admin_new_ticket_admin.txt');
                    Tools::copy(dirname(__FILE__).'/mails/he/admin_new_ticket_admin.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/admin_new_ticket_admin.html');
                    Tools::copy(dirname(__FILE__).'/mails/he/admin_new_ticket_customer.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/admin_new_ticket_customer.txt');
                    Tools::copy(dirname(__FILE__).'/mails/he/admin_new_ticket_customer.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/admin_new_ticket_customer.html');
                    Tools::copy(dirname(__FILE__).'/mails/he/chat_ticket_admin.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/chat_ticket_admin.txt');
                    Tools::copy(dirname(__FILE__).'/mails/he/chat_ticket_admin.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/chat_ticket_admin.html');
                    Tools::copy(dirname(__FILE__).'/mails/he/chat_ticket_customer.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/chat_ticket_customer.txt');
                    Tools::copy(dirname(__FILE__).'/mails/he/chat_ticket_customer.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/chat_ticket_customer.html');
                    Tools::copy(dirname(__FILE__).'/mails/he/send_message.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/send_message.txt');
                    Tools::copy(dirname(__FILE__).'/mails/he/send_message.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/send_message.html');
                    Tools::copy(dirname(__FILE__).'/mails/he/new_message.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/new_message.txt');
                    Tools::copy(dirname(__FILE__).'/mails/he/new_message.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/new_message.html');
                    Tools::copy(dirname(__FILE__).'/mails/he/reply_ticket_to_customer.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/reply_ticket_to_customer.txt');
                    Tools::copy(dirname(__FILE__).'/mails/he/reply_ticket_to_customer.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/reply_ticket_to_customer.html');
                    Tools::copy(dirname(__FILE__).'/mails/he/reply_ticket_to_admin.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/reply_ticket_to_admin.txt');
                    Tools::copy(dirname(__FILE__).'/mails/he/reply_ticket_to_admin.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/reply_ticket_to_admin.html');
                    Tools::copy(dirname(__FILE__).'/mails/he/new_ticket_customer.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/new_ticket_customer.txt');
                    Tools::copy(dirname(__FILE__).'/mails/he/new_ticket_customer.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/new_ticket_customer.html');
                    Tools::copy(dirname(__FILE__).'/mails/he/new_ticket_admin.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/new_ticket_admin.txt');
                    Tools::copy(dirname(__FILE__).'/mails/he/new_ticket_admin.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/new_ticket_admin.html');
                    Tools::copy(dirname(__FILE__).'/mails/he/livechat_message.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/livechat_message.txt');
                    Tools::copy(dirname(__FILE__).'/mails/he/livechat_message.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/livechat_message.html');
                    Tools::copy(dirname(__FILE__).'/mails/he/index.php',dirname(__FILE__).'/mails/'.$language['iso_code'].'/index.php');
                }
                else
                {
                    Tools::copy(dirname(__FILE__).'/mails/en/admin_new_ticket_admin.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/admin_new_ticket_admin.txt');
                    Tools::copy(dirname(__FILE__).'/mails/en/admin_new_ticket_admin.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/admin_new_ticket_admin.html');
                    Tools::copy(dirname(__FILE__).'/mails/en/admin_new_ticket_customer.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/admin_new_ticket_customer.txt');
                    Tools::copy(dirname(__FILE__).'/mails/en/admin_new_ticket_customer.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/admin_new_ticket_customer.html');
                    Tools::copy(dirname(__FILE__).'/mails/en/chat_ticket_admin.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/chat_ticket_admin.txt');
                    Tools::copy(dirname(__FILE__).'/mails/en/chat_ticket_admin.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/chat_ticket_admin.html');
                    Tools::copy(dirname(__FILE__).'/mails/en/chat_ticket_customer.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/chat_ticket_customer.txt');
                    Tools::copy(dirname(__FILE__).'/mails/en/chat_ticket_customer.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/chat_ticket_customer.html');
                    Tools::copy(dirname(__FILE__).'/mails/en/send_message.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/send_message.txt');
                    Tools::copy(dirname(__FILE__).'/mails/en/send_message.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/send_message.html');
                    Tools::copy(dirname(__FILE__).'/mails/en/new_message.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/new_message.txt');
                    Tools::copy(dirname(__FILE__).'/mails/en/new_message.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/new_message.html');
                    Tools::copy(dirname(__FILE__).'/mails/en/reply_ticket_to_customer.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/reply_ticket_to_customer.txt');
                    Tools::copy(dirname(__FILE__).'/mails/en/reply_ticket_to_customer.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/reply_ticket_to_customer.html');
                    Tools::copy(dirname(__FILE__).'/mails/en/reply_ticket_to_admin.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/reply_ticket_to_admin.txt');
                    Tools::copy(dirname(__FILE__).'/mails/en/reply_ticket_to_admin.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/reply_ticket_to_admin.html');
                    Tools::copy(dirname(__FILE__).'/mails/en/new_ticket_customer.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/new_ticket_customer.txt');
                    Tools::copy(dirname(__FILE__).'/mails/en/new_ticket_customer.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/new_ticket_customer.html');
                    Tools::copy(dirname(__FILE__).'/mails/en/new_ticket_admin.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/new_ticket_admin.txt');
                    Tools::copy(dirname(__FILE__).'/mails/en/new_ticket_admin.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/new_ticket_admin.html');
                    Tools::copy(dirname(__FILE__).'/mails/en/livechat_message.txt',dirname(__FILE__).'/mails/'.$language['iso_code'].'/livechat_message.txt');
                    Tools::copy(dirname(__FILE__).'/mails/en/livechat_message.html',dirname(__FILE__).'/mails/'.$language['iso_code'].'/livechat_message.html');
                    Tools::copy(dirname(__FILE__).'/mails/en/index.php',dirname(__FILE__).'/mails/'.$language['iso_code'].'/index.php');
                }
                    
            }
        }
        return true;
    }   
    public function getContent()
	{	   
	   $this->baseAdminPath = $this->context->link->getAdminLink('AdminModules').'&tabsetting=1&configure='.$this->name;
       if(!Tools::getValue('tabsetting'))
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminLiveChatDashboard'));
       //die('xxx');
       if($this->context->employee->id_profile!=1)
       {
            $this->context->smarty->assign(
                array(
                    'form_html' =>$this->getFormStaff($this->context->employee->id),
                    'action' => $this->context->link->getAdminLink('AdminModules').'&tabsetting=1&configure=ets_livechat',
                )
            );
            return $this->displayAdminJs().$this->displayMenuTop().$this->display(__FILE__,'my_info.tpl');

        }
       if(Tools::isSubmit('saveFormTicket'))
       {
            $this->saveObjForm();
       }
	   if(Tools::isSubmit('get_form_ticket_form'))
       {
            $this->_displayFormTicket();
       }
       $this->context->controller->addJqueryUI('ui.sortable');
       if(Tools::getValue('action')=='updatePreMadeMessageOrdering')
       {
            if(Tools::getValue('pre_made_message'))
            {
                foreach(Tools::getValue('pre_made_message') as $key=>$value)
                {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_pre_made_message SET position="'.(int)$key.'" WHERE id_pre_made_message="'.(int)$value.'"');
                }
                die(Tools::jsonEncode(
                    array(
                        'ok'=>true,
                    )
                ));
            }
       }
	   $this->_postConfig();       
       //Display errors if have
       if($this->errorMessage)
            $this->_html .= $this->errorMessage;
       //Add js
       $this->_html .= $this->displayAdminJs(); 
       $this->_html .= $this->displayMenuTop();     
       //Render views       
       $this->renderConfig(); 
       if(!Module::isEnabled($this->name))
            return $this->display(__FILE__,'disabled.tpl');
       if($this->all_shop)
            return $this->display(__FILE__,'allshop.tpl').$this->_html;
       else
            return $this->_html;
    }
    public function displayAdminJs()
    {
        $this->context->controller->addJqueryPlugin('tagify');
        $this->smarty->assign(array(
            'ETS_LC_MODULE_URL' => $this->_path,
            'current_tab_active' => Tools::getValue('current_tab_acitve',Tools::getValue('ETS_TAB_CURENT_ACTIVE','status')),
            'lc_default_lang' => Configuration::get('PS_LANG_DEFAULT'),
            'PS_BASE_URI' => __PS_BASE_URI__,
            'ps15' => version_compare(_PS_VERSION_, '1.6', '<'),
            'PS_ALLOW_ACCENTED_CHARS_URL' => Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
        ));
        return $this->display(__FILE__,'admin-js.tpl');
    } 
    public function renderConfig()
    {
        $configs = $this->setConfig();
        $fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Live Chat configuration'),
					'icon' => 'icon-AdminAdmin'
				),
				'input' => array(),
                'submit' => array(
					'title' => $this->l('Save'),
				)
            ),
		);
        if($configs)
        {
            foreach($configs as $key => $config)
            {
                $confFields = array(
                    'name' => isset($config['multiple']) && $config['multiple'] ? $key.'[]' : $key,
                    'type' => $config['type'],
                    'label' => $config['label'],
                    'desc' => isset($config['desc']) ? $config['desc'] : false,
                    'required' => isset($config['required']) && $config['required'] && $config['type']!='switch' ? true : false,
                    'autoload_rte' => isset($config['autoload_rte']) && $config['autoload_rte'] ? true : false,
                    'options' => isset($config['options']) && $config['options'] ? $config['options'] : array(),
                    'suffix' => isset($config['suffix']) && $config['suffix'] ? $config['suffix']  : false,
                    'form_group_class'=>isset($config['form_group_class'])?$config['form_group_class']:'',
                    'values' => $config['type'] == 'switch' ? array(
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
						) : (isset($config['values']) ? $config['values'] : false),
                    'lang' => isset($config['lang']) ? $config['lang'] : false,
                    'multiple' => isset($config['multiple']) && $config['multiple'],
                    'tab'=>isset($config['tab'])?$config['tab']:'',
                );
                if(!$confFields['suffix'])
                    unset($confFields['suffix']);
               // if($config['type'] == 'file')
//                {
//                    if($imageName = Configuration::get($key))
//                    {
//                        $confFields['display_img'] = $this->_path.'views/img/config/'.$imageName;
//                        $confFields['image'] ='<img src ="'.$confFields['display_img'].'" />';
//                        if(!isset($config['required']) || (isset($config['required']) && !$config['required']))
//                            $confFields['delete_url'] = $this->baseAdminPath.'&delimage=yes&image='.$key; 
//                    }
//                }
                $fields_form['form']['input'][] = $confFields;
            }
        }     
        $helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'saveConfig';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&tabsetting=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=config';
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));        
        $fields = array();        
        $languages = Language::getLanguages(false);
        $helper->override_folder = '/';
        if(Tools::isSubmit('saveConfig'))
        {            
            if($configs)
            {                
                foreach($configs as $key => $config)
                {
                    if(isset($config['lang']) && $config['lang'])
                        {                        
                            foreach($languages as $l)
                            {
                                $fields[$key][$l['id_lang']] = str_replace(array('%5B','%5D'),array('[',']'),Tools::getValue($key.'_'.$l['id_lang'],isset($config['default']) ? $config['default'] : ''));
                            }
                        }
                        elseif($config['type']=='checkbox')
                        {
                            $fields[$key] = Tools::getValue($key,array());
                        }
                        elseif($config['type']=='select' && isset($config['multiple']) && $config['multiple'])
                        {
                            $fields[$key.'[]'] = Tools::getValue($key,array());   
                        }                        
                        else
                            $fields[$key] = Tools::getValue($key,isset($config['default']) ? $config['default'] : '');
                }
            }
        }
        else
        {
            if($configs)
            {
                    foreach($configs as $key => $config)
                    {
                        if(isset($config['lang']) && $config['lang'])
                        {                    
                            foreach($languages as $l)
                            {
                                $fields[$key][$l['id_lang']] = str_replace(array('%5B','%5D'),array('[',']'),Configuration::get($key,$l['id_lang']));
                            }
                        }
                        elseif($config['type']=='checkbox')
                        {//echo Configuration::get($key); die;
                            $fields[$key] = Configuration::get($key) ? explode(',',Configuration::get($key)) : array();
                        }
                        elseif($config['type']=='select' && isset($config['multiple']) && $config['multiple'])
                        {
                            $fields[$key.'[]'] = Configuration::get($key)!='' ? explode(',',Configuration::get($key)) : array();   
                        } 
                        else
                            $fields[$key] = Configuration::get($key);                   
                    }
            }
        }   
        $display_bubble_imge = Configuration::get('ETS_LC_BUBBLE_IMAGE') ?  $this->_path.'views/img/config/'.Configuration::get('ETS_LC_BUBBLE_IMAGE') :'';     
        $helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $fields,
			'languages' => $this->context->controller->getLanguages(),
            'isConfigForm' => true,
            'display_logo' => Configuration::get('ETS_LC_COMPANY_LOGO') ?  $this->_path.'views/img/config/'.Configuration::get('ETS_LC_COMPANY_LOGO') :'',
            'logo_del_link'=> $this->baseAdminPath.'&delimage=yes&image=ETS_LC_COMPANY_LOGO',
            'display_bubble_imge' => $display_bubble_imge,
            'bubble_imge_del_link'=> $this->baseAdminPath.'&delimage=yes&image=ETS_LC_BUBBLE_IMAGE', 
            'display_avata'=>Configuration::get('ETS_LC_CUSTOMER_AVATA') ?  $this->_path.'views/img/config/'.Configuration::get('ETS_LC_CUSTOMER_AVATA') :'',
            'avata_del_link' =>$this->baseAdminPath.'&delimage=yes&image=ETS_LC_CUSTOMER_AVATA', 
            'configTabs' => $this->lc_configTabs,
			'id_language' => $this->context->language->id,  
            'enable_livechat'=>$this->checkEnableLivechat(),
            'is_ps15' => version_compare(_PS_VERSION_, '1.6', '<'),
            'link_callback'  => $this->context->link->getModuleLink($this->name,'callback'),                 
        );        
        $this->_html .= $helper->generateForm(array($fields_form));		
     }     
     private function _postConfig()
     {
        $errors = array();
        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $configs = $this->setConfig();
        //Delete image
        if(Tools::isSubmit('delimage'))
        {
            $image = Tools::getValue('image');
            if(isset($configs[$image]) && !isset($configs[$image]['required']) || (isset($configs[$image]['required']) &!$configs[$image]['required']))
            {
                if($this->all_shop && $this->shops)
                {
                    foreach($this->shops as $shop)
                    {
                        $imageName = Configuration::get($image,null,$shop['id_shop_group'],$shop['id_shop']);
                        $imagePath = dirname(__FILE__).'/views/img/config/'.$imageName;
                        if($imageName && file_exists($imagePath))
                        {
                            if($imageName!='customeravata.jpg' && $imageName!='adminavatar.jpg')
                                @unlink($imagePath);
                            Configuration::updateValue($image,'',false,$shop['id_shop_group'],$shop['id_shop']);
                        }
                    }
                }
                $imageName = Configuration::get($image);
                $imagePath = dirname(__FILE__).'/views/img/config/'.$imageName;
                if($imageName && file_exists($imagePath))
                {
                    if($imageName!='customeravata.jpg' && $imageName!='adminavatar.jpg')
                        @unlink($imagePath);
                    Configuration::updateValue($image,'');
                }
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&tabsetting=1&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&current_tab_acitve=chat_box');
            }
            else
                $errors[] = $configs[$image]['label'].$this->l(' is required');
        }
        if(Tools::isSubmit('saveConfig') && !Tools::isSubmit('submitFilterChart'))
        {            
            if($configs)
            {
                foreach($configs as $key => $config)
                {
                    if(isset($config['lang']) && $config['lang'])
                    {
                        if(isset($config['required']) && $config['required'] && $config['type']!='switch' && trim(Tools::getValue($key.'_'.$id_lang_default) == ''))
                        {
                            $errors[] = $config['label'].' '.$this->l('is required');
                        }                        
                    }
                    else
                    {
                        if(isset($config['type']) && $config['type']=='file' && $_FILES[$key]["name"])
                        {
                            $imageFileType = Tools::strtolower(pathinfo( basename($_FILES[$key]["name"]),PATHINFO_EXTENSION));
                            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                            && $imageFileType != "gif" ) {
                                $errors[]= $config['label']." is invalid.";
                            }
                        }
                        if(isset($config['required']) && $config['required']===true && isset($config['type']) && $config['type']=='file')
                        {
                            if(Configuration::get($key)=='' && !isset($_FILES[$key]['size']))
                                $errors[] = $config['label'].' '.$this->l('is required');
                            elseif(isset($_FILES[$key]['size']))
                            {
                                $fileSize = round((int)$_FILES[$key]['size'] / (1024 * 1024));
                                if($fileSize > 100)
                                    $errors[] = $config['label'].$this->l(' can not be larger than 100Mb');
                            }   
                        }                        
                        else
                        {                            
                            if(isset($config['required']) && $config['required']===true && $config['type']!='switch' && trim(Tools::getValue($key) == ''))
                            {
                                $errors[] = $config['label'].' '.$this->l('is required');
                            }                            
                            elseif(!is_array(Tools::getValue($key)) && isset($config['validate']) && method_exists('Validate',$config['validate']))
                            {
                                $validate = $config['validate'];
                                if(trim(Tools::getValue($key)) && !Validate::$validate(trim(Tools::getValue($key))))
                                    $errors[] = $config['label'].' '.$this->l('is invalid');
                                unset($validate);
                            }
                            elseif(Tools::isSubmit($key) && !is_array(Tools::getValue($key)) && !Validate::isCleanHtml(trim(Tools::getValue($key))))
                            {
                                $errors[] = $config['label'].' '.$this->l('is invalid');
                            } 
                        }                          
                    }                    
                }
            }        
            if(!Validate::isUnsignedInt(Tools::getValue('ETS_LC_MSG_COUNT')))
                $errors[]= $this->l('Message count is invalid');
            if(Tools::getValue('ETS_LC_TIME_OUT')!='' && (!Validate::isUnsignedInt(Tools::getValue('ETS_LC_TIME_OUT'))|| Tools::getValue('ETS_LC_TIME_OUT')=='0' || Tools::getValue('ETS_LC_TIME_OUT')<1000))
                $errors[]= $this->l('Refresh speed front end, min 1000');
            if(Tools::getValue('ETS_LC_TIME_OUT_BACK_END')!='' && (!Validate::isUnsignedInt(Tools::getValue('ETS_LC_TIME_OUT_BACK_END'))|| Tools::getValue('ETS_LC_TIME_OUT_BACK_END')=='0' || Tools::getValue('ETS_LC_TIME_OUT_BACK_END')<1000))
                $errors[]= $this->l('Refresh speed back end, min 1000');
            if(Tools::getValue('ETS_LC_BOX_WIDTH')!='' && (!Validate::isUnsignedInt(Tools::getValue('ETS_LC_BOX_WIDTH'))|| Tools::getValue('ETS_LC_BOX_WIDTH')=='0' || Tools::getValue('ETS_LC_BOX_WIDTH')<300 ))
                $errors[]= $this->l('Chat box width is invalid, min 300');
            if(Tools::getValue('ETS_LC_MSG_LENGTH')!='' && (!Validate::isUnsignedInt(Tools::getValue('ETS_LC_MSG_LENGTH'))|| Tools::getValue('ETS_LC_MSG_LENGTH')=='0' || Tools::getValue('ETS_LC_MSG_LENGTH')>1000 ||Tools::getValue('ETS_LC_MSG_LENGTH')<10 ))
                $errors[]= $this->l('Message length is invalid, min 10, max 1000');
            if(Tools::getValue('ETS_LC_MSG_COUNT')!='' && (!Validate::isUnsignedInt(Tools::getValue('ETS_LC_MSG_COUNT'))|| Tools::getValue('ETS_LC_MSG_COUNT')=='0' ||Tools::getValue('ETS_LC_MSG_COUNT')<3 ))
                $errors[]= $this->l('Message count is invalid, min 3');    
            if(Tools::getValue('ETS_LIVECHAT_ENABLE_FACEBOOK'))
            {
                if(!Tools::getValue('ETS_LIVECHAT_FACEBOOK_APP_ID'))
                    $errors[]= $this->l('Facebook application ID is required');
                if(!Tools::getValue('ETS_LIVECHAT_FACEBOOK_APP_SECRET'))
                    $errors[]= $this->l('Facebook application secret is required');
            }
            if(Tools::getValue('ETS_LIVECHAT_ENABLE_GOOGLE'))
            {
                if(!Tools::getValue('ETS_LIVECHAT_GOOGLE_APP_ID'))
                    $errors[]= $this->l('Google application ID is required');
                if(!Tools::getValue('ETS_LIVECHAT_GOOGLE_APP_SECRET'))
                    $errors[]= $this->l('Google application secret is required');
            }
            if(Tools::getValue('ETS_LIVECHAT_ENABLE_TWITTER'))
            {
                if(!Tools::getValue('ETS_LIVECHAT_TWITTER_APP_ID'))
                    $errors[]= $this->l('Twitter application ID is required');
                if(!Tools::getValue('ETS_LIVECHAT_TWITTER_APP_SECRET'))
                    $errors[]= $this->l('Twitter application secret is required');
            }
            if(in_array('custom',Tools::getValue('ETS_LC_MAIL_TO')))
            {
                if(Tools::getValue('ETS_LC_CUSTOM_EMAIL'))
                {
                    $emails = explode(',',Tools::getValue('ETS_LC_CUSTOM_EMAIL'));
                    foreach($emails as $email)
                    {
                        if(!Validate::isEmail($email))
                        {
                            $errors[]= $this->l('Custom emails is invalid');
                        }
                    }
                }
                else
                    $errors[]= $this->l('Custom emails is invalid');
            }
            //Custom validation
            if(!$errors)
            {
                if($configs)
                {
                    foreach($configs as $key => $config)
                    {
                        if(isset($config['lang']) && $config['lang'])
                        {
                            $valules = array();
                            foreach($languages as $lang)
                            {
                                if($config['type']=='switch')                                                           
                                    $valules[$lang['id_lang']] = (int)trim(Tools::getValue($key.'_'.$lang['id_lang'])) ? 1 : 0;                                
                                else
                                    $valules[$lang['id_lang']] = trim(Tools::getValue($key.'_'.$lang['id_lang'])) ? trim(Tools::getValue($key.'_'.$lang['id_lang'])) : trim(Tools::getValue($key.'_'.$id_lang_default));
                            }
                            if($this->all_shop && $this->shops)
                            {
                                foreach($this->shops as $shop)
                                {
                                    Configuration::updateValue($key,$valules,true,$shop['id_shop_group'],$shop['id_shop']);
                                }
                            }
                            Configuration::updateValue($key,$valules,true);                           
                        }
                        else
                        {
                            if($config['type']=='switch')
                            {   
                                if($this->all_shop && $this->shops)
                                {
                                    foreach($this->shops as $shop)
                                    {
                                        Configuration::updateValue($key,(int)trim(Tools::getValue($key)) ? 1 : 0,true,$shop['id_shop_group'],$shop['id_shop']);
                                    }
                                }
                                Configuration::updateValue($key,(int)trim(Tools::getValue($key)) ? 1 : 0,true);
                            }
                            elseif($config['type']=='checkbox')
                            {    
                                if($this->all_shop && $this->shops)
                                {
                                    Configuration::updateValue($key,Tools::getValue($key) && is_array(Tools::getValue($key)) ? implode(',',Tools::getValue($key)) : '',true,$shop['id_shop_group'],$shop['id_shop']);
                                }
                                Configuration::updateValue($key,Tools::getValue($key) && is_array(Tools::getValue($key)) ? implode(',',Tools::getValue($key)) : '',true);                                
                            }
                            elseif($config['type']=='file')
                            {
                                //Upload file
                                if(isset($_FILES[$key]['tmp_name']) && isset($_FILES[$key]['name']) && $_FILES[$key]['name'])
                                {
                                    //$salt = sha1(microtime());
                                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'], '.'), 1));
                                    $imageName = $_FILES[$key]['name'];
                                    $fileName = dirname(__FILE__).'/views/img/config/'.$imageName;   
                                    if(file_exists($fileName))
                                    {
                                        $time=md5(time());
                                        for($i=0;$i<6;$i++)
                                        {
                                            $index =rand(0,Tools::strlen($time)-1);
                                            $imageName =$time[$index].$imageName;
                                        }
                                        $fileName = dirname(__FILE__).'/views/img/config/'.$imageName;
                                    }              
                                    if(file_exists($fileName))
                                    {
                                        $errors[] = $config['label'].$this->l(' already exists. Try to rename the file then reupload');
                                    }
                                    else
                                    {
                                         
                            			$imagesize = @getimagesize($_FILES[$key]['tmp_name']);
                                        
                                        if (!$errors && isset($_FILES[$key]) &&				
                            				!empty($_FILES[$key]['tmp_name']) &&
                            				!empty($imagesize) &&
                            				in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                            			)
                            			{
                            			    
                            				$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');    				
                            				if ($error = ImageManager::validateUpload($_FILES[$key]))
                            					$errors[] = $error;
                            				elseif (!$temp_name || !move_uploaded_file($_FILES[$key]['tmp_name'], $temp_name))
                            					$errors[] = $this->l('Can not upload the file');
                            				elseif (!ImageManager::resize($temp_name, $fileName, null, null, $type))
                            					$errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
                            				if (isset($temp_name))
                            					@unlink($temp_name);
                                            if(!$errors)
                                            {
                                                if($this->all_shop && $this->shops)
                                                {
                                                    foreach($this->shops as $shop)
                                                    {
                                                        if(Configuration::get($key,null,$shop['id_shop_group'],$shop['id_shop'])!='')
                                                        {
                                                            $oldImage = dirname(__FILE__).'/views/img/config/'.Configuration::get($key,null,$shop['id_shop_group'],$shop['id_shop']);
                                                            if(Configuration::get($key,null,$shop['id_shop_group'],$shop['id_shop'])!='customeravata.jpg' && Configuration::get($key,null,$shop['id_shop_group'],$shop['id_shop'])!='adminavatar.jpg')
                                                            {
                                                                if(file_exists($oldImage))
                                                                    @unlink($oldImage);
                                                            }
                                                            
                                                        }
                                                        Tools::copy(dirname(__FILE__).'/views/img/config/'.$imageName,dirname(__FILE__).'/views/img/config/'.$shop['id_shop'].$imageName);                                                
                                                        Configuration::updateValue($key, $shop['id_shop'].$imageName,true,$shop['id_shop_group'],$shop['id_shop']);
                                                    }
                                                }
                                                if(Configuration::get($key)!='')
                                                {
                                                    $oldImage = dirname(__FILE__).'/views/img/config/'.Configuration::get($key);
                                                    if(file_exists($oldImage)&&Configuration::get($key)!='customeravata.jpg' && Configuration::get($key)!='adminavatar.jpg')
                                                        @unlink($oldImage);
                                                }                                                
                                                Configuration::updateValue($key, $imageName,true);                                                                                               
                                            }
                                        }
                                    }
                                }
                                //End upload file
                            }
                            elseif($config['type']=='select' && isset($config['multiple']) && $config['multiple'])
                            {
                                if($this->all_shop && $this->shops)
                                {
                                    foreach($this->shops as $shop)
                                    {
                                        Configuration::updateValue($key,implode(',',Tools::getValue($key)),true,$shop['id_shop_group'],$shop['id_shop']);
                                    }
                                }
                                Configuration::updateValue($key,implode(',',Tools::getValue($key)));                               
                            }
                            else
                            {
                                if($this->all_shop && $this->shops)
                                {
                                    foreach($this->shops as $shop)
                                    {
                                        Configuration::updateValue($key,trim(Tools::getValue($key)),true,$shop['id_shop_group'],$shop['id_shop']);  
                                    }
                                }
                                Configuration::updateValue($key,trim(Tools::getValue($key)),true);  
                            }
                                 
                        }                        
                    }
                }                
            }
            if (count($errors))
            {
                if(Tools::isSubmit('run_ajax'))
                {
                    die(Tools::jsonEncode(
                        array(
                            'error'=>true,
                            'errors'=>$this->displayError($errors),
                        )
                    ));
                }
                $this->errorMessage = $this->displayError($errors);  
            }
            else
            {
               if(Tools::isSubmit('run_ajax'))
               {
                    die(Tools::jsonEncode(
                        array(
                            'error'=>false,
                        )
                    ));
               }
               Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&tabsetting=1&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&current_tab_acitve='.Tools::getValue('ETS_TAB_CURENT_ACTIVE'));
            }
                           
        }
     }
     public function getConfigs($js = false,$id_lang=false)
     {
        if(!$id_lang)
            $id_lang= $this->context->language->id;
        $configs = array();
        $this->setConfig();
        foreach($this->lc_configs as $key => $val){
            if($js && (!isset($val['js']) || isset($val['js']) && !$val['js']))
                continue;
            $configs[$key] = isset($val['lang']) && $val['lang'] ? Tools::getValue($key.'_'.$id_lang,Configuration::get($key,$id_lang)) : Tools::getValue($key,Configuration::get($key));
        }
        return $configs;
     }     
     public function strToIds($str)
     {
        $ids = array();
        if($temp = explode(',',$str)){
            foreach($temp as $id)
                if(!in_array((int)$id, $ids))
                    $ids[] = (int)$id;
        }
        return $ids;
     }
     public function formatTime($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);    
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;    
        $string = array(
            'y' => $this->l('year'),
            'm' => $this->l('month'),
            'w' => $this->l('week'),
            'd' => $this->l('day'),
            'h' => $this->l('hour'),
            'i' => $this->l('minute'),
            's' => $this->l('second'),
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? $this->l('s') : '');
            } else {
                unset($string[$k]);
            }
        }    
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . $this->l(' ago') : $this->l('just now');
    }
    public function checkAccess()
    {
        if(!($pages = explode(',',Tools::strtolower(Configuration::get('ETS_LC_MISC')))))
            return false; 
        if(!($groups = explode(',',Tools::strtolower(Configuration::get('ETS_LC_CUSTOMER_GROUP')))))
            return false;    
        $black_list = explode(PHP_EOL, Configuration::get('ETS_BLACK_LIST_IP'));
        $my_ip =Tools::getRemoteAddr();
        if(in_array($my_ip,$black_list))
            return false;
        $id_customer = ($this->context->customer->id) ? (int)($this->context->customer->id) : 0;
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int)$id_customer);
        }
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        }  
        if((in_array('all',$pages) || in_array(Tools::strtolower($this->context->controller->php_self),$pages) || !in_array(Tools::strtolower($this->context->controller->php_self),array('index','category','product','cms')) && in_array('other',$pages))&& (in_array('all',$groups)|| in_array($id_group,$groups)))
            return true;
        return false;
    }    
    //Views
    public function hookDisplayHeader()
    {   
        if(!$this->checkAccess() && Tools::getValue('module')!=$this->name)
            return;
        //die($this->context->link->getPageLink('cart').'xx');
        if($this->checkAccess())
        {
            if(version_compare(_PS_VERSION_, '1.6', '<'))
            {
                $this->context->controller->addJS($this->_path.'views/js/livechat15.js');
            }
            else
            {
                $this->context->controller->addJS($this->_path.'views/js/livechat.js');
            }
        }
        $assigns = $this->getConfigs(true);
        $assigns['ETS_LC_URL_AJAX'] = $this->context->link->getModuleLink($this->name,'ajax',array('token'=>md5($this->id)));
        $assigns['ETS_LC_URL_OAUTH'] = $this->context->link->getModuleLink($this->name,'oauth');
        $conversation = LC_Conversation::getCustomerConversation();
        $this->smarty->assign( 
            array(
                'assigns'=>$assigns,
                'isRequestAjax' =>$conversation?$conversation->isJquestAjax():0,
            )
        );  
        if($conversation && Tools::getValue('module')!=$this->name)
        {
            $current_url = $this->getLinkCurrentByUrl();
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_conversation set current_url="'.pSQL($current_url).'",http_referer="'.pSQL($_SERVER['HTTP_USER_AGENT']).'" WHERE id_conversation='.(int)$conversation->id);
        }
        $this->context->controller->addJS($this->_path.'views/js/jquery.rating.pack.js');
        $this->context->controller->addCSS($this->_path.'views/css/font-awesome.css','all');
        if(version_compare(_PS_VERSION_, '1.6', '<'))
        {
            
            $this->context->controller->addCSS($this->_path.'views/css/livechat15.css','all');
        }
        if(version_compare(_PS_VERSION_, '1.7', '<'))
            $this->context->controller->addJqueryUI('ui.draggable');
        $this->context->controller->addCSS($this->_path.'views/css/livechat.css','all');
        $this->context->controller->addCSS($this->_path.'views/css/my_account.css','all');
        if(version_compare(_PS_VERSION_, '1.7', '<'))
            $this->context->controller->addCSS($this->_path.'views/css/my_account16.css','all');
        if(Tools::getValue('module')==$this->name)
        {
            $this->context->controller->addJqueryUI('ui.datepicker');
            $this->context->controller->addJS($this->_path.'views/js/my_account.js');
        }
        return $this->display(__FILE__, 'header.tpl');
     }
     public function hookDisplayBackOfficeHeader()
     {
        $this->context->controller->addCSS($this->_path.'views/css/font-awesome.css','all');
        if((Tools::isSubmit('configure') && Tools::strtolower(Tools::getValue('configure'))=='ets_livechat') || in_array(Tools::getValue('controller'),array('AdminLiveChatDashboard','AdminLiveChatTickets','AdminLiveChatHistory','AdminLiveChatSettings','AdminLiveChatHelp')))
        {
            $this->context->controller->addCSS($this->_path.'views/css/slick.css','all');
            $this->context->controller->addCSS($this->_path.'views/css/livechat.admin.css','all');
        }
        $this->context->controller->addCSS($this->_path.'views/css/livechat.admin.footer.css','all');  
        if((Tools::isSubmit('configure') && Tools::strtolower(Tools::getValue('configure'))=='ets_livechat') || in_array(Tools::getValue('controller'),array('AdminLiveChatDashboard','AdminLiveChatTickets','AdminLiveChatHistory','AdminLiveChatSettings','AdminLiveChatHelp')))
        {
            if(version_compare(_PS_VERSION_, '1.6', '<'))
                $this->context->controller->addCSS($this->_path.'views/css/livechat.admin15.css','all');
        }
        if(version_compare(_PS_VERSION_, '1.6', '<'))
                $this->context->controller->addCSS($this->_path.'views/css/livechat.admin15.footer.css','all');
        if(version_compare(_PS_VERSION_, '1.7', '<'))
                $this->context->controller->addCSS($this->_path.'views/css/livechat.admin16.footer.css','all');        
        $this->context->controller->addJquery();
        $this->context->controller->addJqueryUI(array('ui.draggable'));
        $this->context->controller->addJqueryPlugin('autocomplete');
        return $this->display(__FILE__,'admin_header.tpl');
     }   
     public function getDepartments()
     {
        if(!LC_Conversation::isUsedField('departments'))
            return false;
        else
        {
            $departments = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_departments WHERE status=1 ORDER BY sort_order ASC');
            return $departments;
        }
     }  
     public function checkChangeDepartment($conversation)
     {
        if(!$this->getDepartments())
            return false;
        if(!$conversation || !$conversation->id || $conversation->end_chat)
            return true;
        else
            return false;
     }
     public function checkAutoEndChat($conversation)
     {
        if(Configuration::get('ETS_LC_ENDCHAT_AUTO')>0 && !$conversation->end_chat)
        {
            $timeend= Configuration::get('ETS_LC_ENDCHAT_AUTO')*60;
            if(strtotime('now') > (strtotime($conversation->date_message_last_customer)+$timeend))
            {
                $conversation->end_chat=-1;
                $conversation->save();
                return true;
            } 
        }
        return false;
     }  
     public function displayChatBoxCustomer($refresh=false)
     {        
        $conversation = LC_Conversation::getCustomerConversation();
        $message_writing = (int)Tools::getValue('message_writing');
        $message_seen = (int)Tools::getValue('message_seen');
        $message_delivered = (int)Tools::getValue('message_delivered');
        if($conversation)
        {
            if($message_delivered)
                $conversation->date_message_delivered_customer= date('Y-m-d H:i:s');
            if($message_seen)
            {
                $conversation->date_message_seen_customer = date('Y-m-d H:i:s');
            }    
            if($message_writing)
            {
                $conversation->date_message_writing_customer=date('Y-m-d H:i:s');
                $conversation->customer_writing=1;
            }
                
            $conversation->latest_online =date('Y-m-d H:i:s');
            $conversation->update();
        }
        $isEmployeeSeen = $conversation? LC_Conversation::isEmployeeSeen($conversation->id):0;
        $isEmployeeDelivered = $conversation? LC_Conversation::isEmployeeDelivered($conversation->id):0;
        $isEmployeeWriting = $conversation? LC_Conversation::isEmployeeWriting($conversation->id):0;
        $isEmployeeSent=$conversation? LC_Conversation::isEmployeeSent($conversation->id):0;
        $isAdminOnline = self::isAdminOnline();
        $lastMessageOfEmployee = LC_Conversation::getLastMessageOfEmployee($conversation ? $conversation->id : 0);
        $company =$this->_getCompanyInfo($conversation ? $conversation->id_employee : 0,Configuration::get('ETS_LC_DISPLAY_COMPANY_INFO'));
        $isRequestAjax=0;
        //$end_chat='';
        $isRequestAjax =$conversation ? $conversation->isJquestAjax():0;
//        if($conversation &&  $conversation->end_chat)
//        {
//            if( $conversation &&  $conversation->end_chat >=1)
//            {
//                if(Configuration::get('ETS_LC_USE_COMPANY_NAME') && Configuration::get('ETS_LC_COMPANY_NAME'))
//                {
//                    $end_chat = Configuration::get('ETS_LC_COMPANY_NAME');
//                }
//                else
//                    $end_chat= Db::getInstance()->getValue("SELECT CONCAT(e.firstname,' ',e.lastname) as employee_name FROM "._DB_PREFIX_."employee e WHERE id_employee='".(int)$conversation->end_chat."'");
//            }
//            elseif($conversation &&  $conversation->end_chat ==-1)
//                $end_chat=-1;
//        }
        $this->context->cookie->lc_siteloaded =1;
        $this->context->cookie->write();
        if($conversation && $conversation->employee_message_edited)
        {
            $employee_message_edited = LC_Message::getMessageByListID($conversation->employee_message_edited);
        }
        else
            $employee_message_edited='';
        $employee_name ='';
        if(Configuration::get('ETS_LC_DISPLAY_COMPANY_INFO')=='general' && Configuration::get('ETS_LC_COMPANY_NAME'))
        {
            $employee_name = Configuration::get('ETS_LC_COMPANY_NAME');
        }
        else
            $employee_name= $lastMessageOfEmployee ? $lastMessageOfEmployee['employee_name']:Configuration::get('ETS_LC_COMPANY_NAME');  
        $departments = $this->getDepartments();
        $change_department= $this->checkChangeDepartment($conversation);
        if($refresh && $conversation && $conversation->id)
        {
            $assign=array(    
                'isAdminBusy' => Ets_livechat::isAdminBusy(),            
                'wait_support' => $conversation && $this->checkWaitSupport($conversation->id) ? Ets_livechat::getTimeWait():false,
                'end_chat'=> $conversation->end_chat, //$end_chat ? $end_chat.' '.$this->l('ends chat. Send another message if you would like to restart chat'):'',
                'end_chat_admin' => $conversation->end_chat>=1 ? true: false,
                'isRequestAjax'=> $isRequestAjax,
                'employee_accept' => $conversation->id_employee && ($employee= new Employee($conversation->id_employee)) ? (Configuration::get('ETS_LC_DISPLAY_COMPANY_INFO')=='general' ? Configuration::get('ETS_LC_COMPANY_NAME') : $employee->firstname.' '.$employee->lastname). $this->l(' accepted chat') :false,
                'isCustomerLoggedIn' => isset($this->context->customer->id) && (int)$this->context->customer->id,
                'isAdminOnline' => $isAdminOnline,
                'departments'=>$departments,
                'change_department'=>$change_department,
                'isEmployeeSeen'=>$isEmployeeSeen&& in_array('seen',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                'isEmployeeDelivered'=>$isEmployeeDelivered && in_array('delevered',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                'isEmployeeWriting'=>$isEmployeeWriting && in_array('writing',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                'isEmployeeSent'=> $isEmployeeSent && in_array('sent',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                'customer' => isset($this->context->customer->id) && $this->context->customer->id ? array(
                    'name' => trim(Tools::ucfirst($this->context->customer->firstname).' '.Tools::ucfirst($this->context->customer->lastname)),
                ) : ($conversation ? 
                    array(
                        'name' => $conversation->customer_name,
                    ) : false
                ),
                'upload_file'=>$this->checkFileNumberUpload($conversation->id),
                'employee_name'=>$employee_name,
                'refresh' =>$refresh,
                'company'=>$company,
                'id_conversation' => $conversation ? $conversation->id : 0,
                'playsound_enable' => $conversation? $conversation->enable_sound:1,
                'lastMessageIsEmployee' => LC_Conversation::lastMessageIsEmployee($conversation ? $conversation->id : 0),
                'count_message_not_seen' =>LC_Conversation::getMessagesCustomerNotSeen($conversation?$conversation->id:0),
                'messages' => $conversation && !$conversation->end_chat && ($messages = $conversation->getMessages((int)Tools::getValue('latestID'),(int)Configuration::get('ETS_LC_MSG_COUNT'))) ? array_reverse($messages) : false,
                'captcha' => Ets_livechat::needCaptcha() ? $this->context->link->getModuleLink($this->name,'captcha',array('rand' => Tools::substr(sha1(mt_rand()), 17, 6))) : false,
                'captchaUrl' => Ets_livechat::needCaptcha() ?$this->context->link->getModuleLink($this->name,'captcha',array('init' => 'ok')):'',
                'employee_message_deleted' => $conversation?$conversation->employee_message_deleted:'',
                'employee_message_edited'=>$employee_message_edited,
                'conversation_rate' => $conversation->rating,
                'message_edited' => Tools::getValue('id_message')?LC_Message::getMessage(Tools::getValue('id_message')):'',
            );
            if($conversation)
            {
                $conversation->employee_message_deleted='';
                $conversation->employee_message_edited='';
                $conversation->update();
            } 
            die(Tools::jsonEncode($assign));
        }
        $assign=array(                
            'isRTL' => isset($this->context->language->is_rtl) && $this->context->language->is_rtl,
            'conversation' => $conversation,  
            'wait_support' => $conversation && $this->checkWaitSupport($conversation->id) ? Ets_livechat::getTimeWait():false,          
            'isAdminBusy' => Ets_livechat::isAdminBusy(),
            'config' => $this->getConfigs(),
            'end_chat'=> $conversation && $conversation->end_chat, //$end_chat? $end_chat.' '.$this->l('ends chat. Send another message if you would like to restart chat'):'',
            'end_chat_admin' => $conversation && $conversation->end_chat==1 ? true: false,
            'isRequestAjax'=> $isRequestAjax,
            'isCustomerLoggedIn' => isset($this->context->customer->id) && (int)$this->context->customer->id,
            'isAdminOnline' => $isAdminOnline,
            'departments'=>$departments,
            'change_department'=>$change_department,
            'isEmployeeSeen'=>$isEmployeeSeen&& in_array('seen',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
            'isEmployeeDelivered'=>$isEmployeeDelivered && in_array('delevered',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
            'isEmployeeWriting'=>$isEmployeeWriting && in_array('writing',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
            'isEmployeeSent'=> $isEmployeeSent && in_array('sent',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
            'customer' => isset($this->context->customer->id) && $this->context->customer->id ? array(
                'name' => trim(Tools::ucfirst($this->context->customer->firstname).' '.Tools::ucfirst($this->context->customer->lastname)),
                'email' => $this->context->customer->email,
                'phone' => ($addresses = $this->context->customer->getAddresses($this->context->language->id)) ? ($addresses[0]['phone'] ? $addresses[0]['phone'] : ($addresses[0]['phone_mobile'] ? $addresses[0]['phone_mobile'] : ($conversation && $conversation->customer_phone ? $conversation->customer_phone : ''))) : ($conversation && $conversation->customer_phone ? $conversation->customer_phone : ''),
                'phoneRegistered' => $addresses && ($addresses[0]['phone'] || $addresses[0]['phone_mobile']),
            ) : ($conversation ? 
                array(
                    'name' => $conversation->customer_name,
                    'email' => $conversation->customer_email,
                    'phone' => $conversation->customer_phone,
                    'phoneRegistered' => $conversation->customer_phone,
                ) : false
            ),
            'employee_name'=>$employee_name,
            'employee_info' => $this->_getCompanyInfo($lastMessageOfEmployee['id_employee'] ? $lastMessageOfEmployee['id_employee'] : ($conversation ? $conversation->id_employee :0),'staff'),
            'refresh' =>$refresh,
            'company'=>$company,
            'upload_file'=>$this->checkFileNumberUpload($conversation ? $conversation->id:0),
            'lc_chatbox_top'=> isset($this->context->cookie->lc_chatbox_top) && $this->context->cookie->lc_chatbox_top!=='' ? $this->context->cookie->lc_chatbox_top : false,
            'lc_chatbox_left' => isset($this->context->cookie->lc_chatbox_left) ? $this->context->cookie->lc_chatbox_left : false,
            'id_conversation' => $conversation ? $conversation->id : 0,
            'has_conversation'=>1,
            'playsound_enable' => $conversation? $conversation->enable_sound:1,
            'lastMessageIsEmployee' => LC_Conversation::lastMessageIsEmployee($conversation ? $conversation->id : 0),
            'lastMessageOfEmployee' =>$lastMessageOfEmployee,
            'count_message_not_seen' =>LC_Conversation::getMessagesCustomerNotSeen($conversation?$conversation->id:0),
            'ajaxUrl' => $this->context->link->getModuleLink($this->name,'ajax'),
            'chatBoxStatus' => isset($this->context->cookie->ets_lc_chatbox_status) && $this->context->cookie->ets_lc_chatbox_status ? $this->context->cookie->ets_lc_chatbox_status : '',
            'messages' => $conversation && !$conversation->end_chat && ($messages = $conversation->getMessages((int)Tools::getValue('latestID'),(int)Configuration::get('ETS_LC_MSG_COUNT'))) ? array_reverse($messages) : false,
            'captcha' => Ets_livechat::needCaptcha() ? $this->context->link->getModuleLink($this->name,'captcha',array('rand' => Tools::substr(sha1(mt_rand()), 17, 6))) : false,
            'captchaUrl' => $this->context->link->getModuleLink($this->name,'captcha',array('init' => 'ok')),
            'emotions' => $this->emotions, 
            'employee_message_deleted' => $conversation?$conversation->employee_message_deleted:'',
            'employee_message_edited'=>$employee_message_edited,
            'message_edited' => Tools::getValue('id_message')?LC_Message::getMessage(Tools::getValue('id_message')):'',
            'livechatDir' => $this->_path,
            'contact_link' => $this->getLinkContact(),
            'product_current' => $this->getProductCurrent($conversation),
            'display_bubble_imge' =>  Configuration::get('ETS_CLOSE_CHAT_BOX_TYPE')=='image' && Configuration::get('ETS_LC_BUBBLE_IMAGE') ?  $this->_path.'views/img/config/'.Configuration::get('ETS_LC_BUBBLE_IMAGE') :'',
        );            
        $this->smarty->assign($assign);   
        return $this->display(__FILE__, 'chatbox-customer.tpl');                
    } 
    public function getLinkContact()
    {
        
        if(Configuration::get('ETS_LC_LINK_SUPPORT_TYPE')=='contact-form')
            return $this->context->link->getPageLink('contact');
        elseif(Configuration::get('ETS_LC_LINK_SUPPORT_TYPE')=='ticket-form' && $id_form=Configuration::get('ETS_LC_LINK_SUPPORT_FORM'))
            return $this->getFormLink($id_form);
        elseif(Configuration::get('ETS_LC_LINK_SUPPORT_TYPE')=='custom-link' && Configuration::get('ETS_LC_SUPPORT_LINK',$this->context->language->id))
            return Configuration::get('ETS_LC_SUPPORT_LINK',$this->context->language->id);
        return '';
        
    }
    public function checkDepartmentsExitsEmployee($id_departments,$id_employee=0)
    {
        if(!$id_employee)
        {
            $id_employee= $this->context->employee->id;
            $employee= $this->context->employee;
        }
        else
            $employee= new Employee($id_employee);
        if($employee->id_profile==1)
            return true;
        $sql = 'SELECT d.id_departments FROM '._DB_PREFIX_.'ets_livechat_departments d
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (d.id_departments=de.id_departments)
        WHERE (d.all_employees=1 OR de.id_employee="'.(int)$id_employee.'") AND d.id_departments="'.(int)$id_departments.'"';
        return Db::getInstance()->getRow($sql);
    }
    public function checkWaitingAcceptance($conversation)
    {
        if((($conversation->id_employee_wait && Configuration::get('ETS_LC_STAFF_ACCEPT') && $conversation->id_employee==$this->context->employee->id) || ($conversation->id_departments_wait && !$this->checkDepartmentsExitsEmployee($conversation->id_departments_wait) ) ) && !$conversation->end_chat)
        {
            if($conversation->id_employee_wait!=-1)
            {
                $employee = new Employee($conversation->id_employee_wait);
                    return $employee->firstname.' '.$employee->lastname;
            }
            else
            {
                if($conversation->id_departments_wait!=-1)
                {
                    $department =new LC_Departments($conversation->id_departments_wait);
                        return $department->name;
                }
                else
                    return $this->l('All department');
            }
            
        }
    }
    public function displayChatBoxEmployee($id_conversation,$refresh=false,$list=false)
    {
        if(!$this->checkExistConversation($id_conversation))
            return '';
        if($this->all_shop&& $this->shops)
        {
            foreach($this->shops as $shop)
            {
                Ets_livechat::updateAdminOnline($shop['id_shop']);
            }
        }
        Ets_livechat::updateAdminOnline();
        $conversation = new LC_Conversation($id_conversation);
        $isCustomerOnline = LC_Conversation::isCustomerOnline($id_conversation);
        if($conversation->id_customer)
        {
            $customer= new Customer($conversation->id_customer);
            $customer_name = $customer->firstname.' '.$customer->lastname;
            $customer_phone = ($addresses = $customer->getAddresses($this->context->language->id)) ? ($addresses[0]['phone'] ? $addresses[0]['phone'] : ($addresses[0]['phone_mobile'] ? $addresses[0]['phone_mobile'] : ($conversation && $conversation->customer_phone ? $conversation->customer_phone : ''))) : ($conversation && $conversation->customer_phone ? $conversation->customer_phone : '');
            $customer_email = $customer->email;
        }
        else
        {
            $customer_name = $conversation->customer_name;
            $customer_phone= $conversation->customer_phone;
            $customer_email = $conversation->customer_email;
        }
        $customer_avata = $this->getAvatarCustomer($conversation->id_customer);
        $isCustomerSeen = $conversation? LC_Conversation::isCustomerSeen($conversation->id):0;
        $isCustomerDelivered = $conversation? LC_Conversation::isCustomerDelivered($conversation->id):0;
        $isCustomerWriting = $conversation? LC_Conversation::isCustomerWriting($conversation->id):0;
        $isCustomerSent = $conversation? LC_Conversation::isCustomerSent($conversation->id):0;
        $isRequestAjax=0;
        $end_chat='';
        if($conversation->end_chat)
        {
            if($conversation->end_chat==$this->context->employee->id)
                $end_chat = $this->l('You has ended this chat');
            elseif($conversation->end_chat >0)
            {
                $employee= new Employee($conversation->end_chat);
                    $end_chat =$employee->firstname.' '.$employee->lastname.' '.$this->l('has ended this chat');
            }
            else
                $end_chat = $this->l('Chat has ended.').' '.($customer_name ? $customer_name:'Chat ID #'.$conversation->id).' '. $this->l('has left chat');
        }elseif($this->checkAutoEndChat($conversation))
        {
            $end_chat = $this->l('Chat has ended.').' '.($customer_name ? $customer_name:'Chat ID #'.$conversation->id).' '. $this->l('has left chat');
        }
        else
        {
            $isRequestAjax =$conversation? $conversation->isJquestAjax():0;
        }
        if($conversation->message_edited)
        {
            $message_edited = LC_Message::getMessageByListID($conversation->message_edited);
        }
        else
            $message_edited='';
        if($conversation->id_ticket)
            $conversation->ticket= new LC_Ticket($conversation->id_ticket);
        $conversation_hided = Tools::jsonDecode($this->context->cookie->converation_hided,true);
        $employees=  Db::getInstance()->executeS(
        'SELECT e.*,d.id_departments,s.name,s.avata,IFNULL(s.status,1) as status FROM '._DB_PREFIX_.'employee e 
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (de.id_employee=e.id_employee)
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments d ON (d.id_departments = de.id_departments)
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_staff s ON (s.id_employee=e.id_employee) 
        WHERE e.active=1 GROUP BY e.id_employee');
        if($employees)
        {
            foreach($employees as &$employee)
            {
                $employe_departments= Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_departments_employee WHERE id_employee='.(int)$employee['id_employee']);
                $employee['departments']=$employe_departments;
            }
        }
        if(!$refresh)
        {
            $assign = array(  
                'error' => $this->errors?$this->displayError($this->errors):false,              
                'isRTL' => isset($this->context->language->is_rtl) && $this->context->language->is_rtl,
                'conversation' => $conversation,            
                'config' => $this->getConfigs(),
                'end_chat'=> $end_chat,
                'history_chat' => Configuration::get('ETS_LIVECHAT_ADMIN_OLD') ? $this->_displayHistoryChatCustomer($conversation->chatref):'',
                'customer_avata'=>$customer_avata,
                'waiting_acceptance' => $this->checkWaitingAcceptance($conversation),
                'has_changed'=> Ets_livechat::checkHasChanged($conversation),
                'wait_accept' => Ets_livechat::checkWaitAccept($conversation),
                'accept_employee' => new Employee($conversation->id_employee),
                'employees'=>$employees,
                'departments' => $this->getDepartments(),
                'ETS_LIVECHAT_ADMIN_DE' => Configuration::get('ETS_LIVECHAT_ADMIN_DE') || $this->context->employee->id_profile==1,
                'isRequestAjax'=> $isRequestAjax,
                'isCustomerOnline' => $isCustomerOnline,
                'pre_made_messages'=> Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_pre_made_message'),
                'isCustomerSeen' => $isCustomerSeen && in_array('seen',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                'isCustomerDelivered' =>$isCustomerDelivered && in_array('delevered',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                'isCustomerWriting' => $isCustomerWriting && in_array('writing',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                'isCustomerSent' => $isCustomerSent && in_array('sent',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                'lastMessageIsEmployee' => LC_Conversation::lastMessageIsEmployee($conversation ? $conversation->id : 0),
                'customer_name' => $customer_name,
                'date_accept' => $conversation && $conversation->id ?  $this->convertDate($conversation->date_accept) :false,
                'chatbox_closed' => $conversation && $conversation_hided ? in_array($conversation->id ,$conversation_hided): 0,
                'customer_email'=>$customer_email,
                'customer_phone' =>$customer_phone,
                'id_conversation' => $conversation ? $conversation->id : 0,
                'customer_rated' => $conversation? $conversation->rating:0,
                'count_message_not_seen' => (int)LC_Conversation::getMessagesEmployeeNotSeen($conversation->id),
                'refresh' =>$refresh,
                'link_ticket'=> $this->getAdminLink('AdminLiveChatTickets'),
                'link_customer' =>$conversation->id_customer ? $this->getAdminLink('AdminCustomers').'&updatecustomer&id_customer='.$conversation->id_customer:'',
                'ajaxUrl' => $this->_path.'ets_livechat_ajax.php?token='.Tools::getAdminTokenLite('AdminModules'),
                'chatBoxStatus' => isset($this->context->cookie->ets_lc_chatbox_status) && $this->context->cookie->ets_lc_chatbox_status ? $this->context->cookie->ets_lc_chatbox_status : false,
                'messages' => $conversation && ($messages = $conversation->getMessages(0,(int)Configuration::get('ETS_LC_MSG_COUNT'))) ? array_reverse($messages) : false,
                'message_deleted' => $conversation?$conversation->message_deleted:'',
                'message_edited'=>$message_edited,
                'employee_message_edited' => Tools::getValue('id_message')? LC_Message::getMessage(Tools::getValue('id_message')):'',
                'emotions' => $this->emotions, 
                'livechatDir' => $this->_path,
                'form_ticket' => $this->renderHtmlForm(1,$conversation->id), 
            );
            $this->smarty->assign($assign);    
            return $this->display(__FILE__, 'chatbox-employee.tpl');
        }
        else
        {
            $assign = array(  
                'error' => $this->errors?$this->displayError($this->errors):false,                        
                'end_chat'=> $end_chat,
                'waiting_acceptance' => $this->checkWaitingAcceptance($conversation),
                'has_changed'=> Ets_livechat::checkHasChanged($conversation),
                'wait_accept' =>Ets_livechat::checkWaitAccept($conversation),
                'isRequestAjax'=> $isRequestAjax,
                'isCustomerOnline' => $isCustomerOnline,
                'customer_avata'=>$customer_avata,
                'pre_made_messages'=> Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_pre_made_message'),
                'isCustomerSeen' => $isCustomerSeen && in_array('seen',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                'isCustomerDelivered' =>$isCustomerDelivered && in_array('delevered',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                'isCustomerWriting' => $isCustomerWriting && in_array('writing',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                'isCustomerSent' => $isCustomerSent && in_array('sent',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                'lastMessageIsEmployee' => LC_Conversation::lastMessageIsEmployee($conversation ? $conversation->id : 0),
                'customer_name' => $customer_name,
                'chatbox_closed' => $conversation && $conversation_hided ? in_array($conversation->id ,$conversation_hided): 0,
                
                'customer_email'=>$customer_email,
                'customer_phone' =>$customer_phone,
                'id_conversation' => $conversation ? $conversation->id : 0,
                'customer_rated' => $conversation? $conversation->rating:0,
                'count_message_not_seen' => (int)LC_Conversation::getMessagesEmployeeNotSeen($conversation->id),
                'refresh' =>$refresh,
                'chatBoxStatus' => isset($this->context->cookie->ets_lc_chatbox_status) && $this->context->cookie->ets_lc_chatbox_status ? $this->context->cookie->ets_lc_chatbox_status : false,
                'messages' => $conversation && ($messages = $conversation->getMessages(0,(int)Configuration::get('ETS_LC_MSG_COUNT'))) ? array_reverse($messages) : false,
                'message_deleted' => $conversation?$conversation->message_deleted:'',
                'message_edited'=>$message_edited,
                'employee_message_edited' => Tools::getValue('id_message')?LC_Message::getMessage(Tools::getValue('id_message')):'',
            );
            if($conversation)
            {
                $conversation->message_deleted='';
                $conversation->message_edited='';
                $conversation->update();
            }
            if(!$list)
            {
                die(Tools::jsonEncode($assign));
            }
            else
                return $assign;
        }
        
    }
    public function hookActionAuthentication($params)
    {
        if(isset($params['customer']) && isset($params['customer']->id) && ($id_customer = $params['customer']->id) && (int)$this->context->cookie->lc_id_conversation && ($conversation = new LC_Conversation((int)$this->context->cookie->lc_id_conversation)) && !$conversation->id_customer && $conversation->id)
        {
            if($oldConversation = LC_Conversation::getConversationByIdCustomer($id_customer))
            {
                if($oldConversation->chatref)
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_conversation SET chatref="'.(int)$oldConversation->chatref.'" WHERE chatref='.(int)$conversation->chatref);
                else
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_conversation SET chatref="'.(int)$conversation->chatref.'" WHERE id_customer='.(int)$id_customer);
            }
            $conversation->id_customer = $id_customer;
            $conversation->update();         
        }
    }
    public function hookActionCustomerLogoutAfter()
    {
        $this->context->cookie->lc_id_conversation = 0;
        $this->context->cookie->write();  
    }
    //Static functions
    public static function needCaptcha()
    {
        $conversation = LC_Conversation::getCustomerConversation();
        if($conversation && $conversation->captcha_enabled)
            return true;
        $latest_ip = Tools::getRemoteAddr();
        $count_conversation = count(Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE latest_ip="'.pSQL($latest_ip).'" AND datetime_added>="'.pSQL(date('Y-m-d H:i:s',strtotime('-1 MINUTE'))).'" AND id_conversation ='.(int)Context::getContext()->cookie->lc_id_conversation));
        if($count_conversation>=10)
            return true;
        if(!Configuration::get('ETS_LC_CAPTCHA'))
            return false;
        $isAdminOnline = self::isAdminOnline();
        $conversation = LC_Conversation::getCustomerConversation();
        $generalCheck = self::checkCaptcha('always') || (self::checkCaptcha('first') && !$conversation) || (!$isAdminOnline && self::checkCaptcha('fromsecond') && $conversation) || !self::isCustomerLoggedIn() && self::checkCaptcha('notlog') || !self::isCustomerLoggedIn() && self::checkCaptcha('secondnotlogin') && $conversation;
        if(!$generalCheck && $conversation && $conversation->id && self::checkCaptcha('auto'))
        {
            $messages = Db::getInstance()->executeS("SELECT id_message,id_employee FROM "._DB_PREFIX_."ets_livechat_message WHERE id_conversation=".(int)$conversation->id." AND datetime_added >'".pSQL(date('Y-m-d H:i:s', strtotime('-1 minute')))."' ORDER BY id_message DESC limit 0,10");
            if(count($messages) < 10)
                return false;
            if($messages)
            {
                foreach($messages as $message)
                {
                    if($message['id_employee'])
                        return false;
                }
                return true;
            }
                
        }
        return $generalCheck;
    }
    public static function checkCaptcha($type)
    {
        if(!$type || !($str = Configuration::get('ETS_LC_CAPTCHA')) || $str && !($types = explode(',',$str)) || $types && !is_array($types))
            return false;
        return in_array($type, $types);
    }
    public static function validCaptcha($captcha = false)
    {
        if(!self::needCaptcha())
            return true;
        $context = Context::getContext();
        if(!$captcha)
            $captcha = Tools::strtolower(trim(Tools::getValue('captcha')));
        if($context->cookie->ets_lc_captcha_code && $captcha != Tools::strtolower($context->cookie->ets_lc_captcha_code))
            return false;
        return true;
    }
    public static function isCustomerLoggedIn()
    {
        $context = Context::getContext();
        return isset($context->customer->id) && (int)$context->customer->id;
    }
    public static function getTimeWait($id_conversation=0)
    {
        if(!$id_conversation)
        {
            $conversation = LC_Conversation::getCustomerConversation();
            if($conversation)
                $id_conversation=$conversation->id;
        }
        $timeFirstMessage = Db::getInstance()->getValue('SELECT MIN(datetime_added) FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation='.(int)$id_conversation);
        $timeWait = Configuration::get('ETS_LC_TIME_WAIT') ? Configuration::get('ETS_LC_TIME_WAIT')*60  : 0;
        if(!$timeWait)
            return true;
        if(strtotime('now') < strtotime($timeFirstMessage)+$timeWait)
            return strtotime($timeFirstMessage)+$timeWait- strtotime('now');
    }
    public static function isAdminBusy()
    {
        $conversation = LC_Conversation::getCustomerConversation();
        if($conversation && !Ets_livechat::getTimeWait() && !$conversation->end_chat)
        {
            if($conversation->id_employee)
                return false;
            else
                return !Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation='.(int)$conversation->id.' AND id_employee!=0');
        }
        elseif($conversation && Ets_livechat::getTimeWait() && !$conversation->end_chat)
        {
            if(Configuration::get('ETS_LC_FORCE_ONLINE'))
                return false;
            $sql = 'SELECT e.id_employee FROM '._DB_PREFIX_.'employee e';
            if($conversation->id_departments)
            {
                $sql .=' LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (de.id_employee=e.id_employee)';
                $sql .=' LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments d ON (d.all_employees=1 OR d.id_departments = de.id_departments)';
            }
            $sql .= ' WHERE e.active=1 AND e.id_employee NOT IN (SELECT id_employee FROM '._DB_PREFIX_.'ets_livechat_staff_decline WHERE id_conversation="'.(int)$conversation->id.'")';
            if($conversation->id_departments)
            {
                $sql .=' AND (e.id_profile=1 OR d.id_departments="'.(int)$conversation->id_departments.'")';
            }
            $employees = Db::getInstance()->executeS($sql);
            if($employees)
            {
                foreach($employees as $employee)
                {
                    $date_online = Db::getInstance()->getValue('SELECT date_online FROM '._DB_PREFIX_.'ets_livechat_employee_online WHERE id_shop="'.(int)Context::getContext()->shop->id.'"'.' AND id_employee="'.(int)$employee['id_employee'].'"'.' ORDER BY date_online DESC');
                    $timeout= (int)Configuration::get('ETS_LC_TIME_OUT_BACK_END')*3/1000+(int)Configuration::get('ETS_LC_TIME_OUT')*3/1000;
                    
                    if($date_online && (strtotime(date('Y-m-d H:i:s')) < strtotime($date_online)+$timeout))
                        return false;
                }
            }
            return !Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation='.(int)$conversation->id.' AND id_employee > 0');
            
        }
        return false;
        
    }
    public static function isAdminOnlineNoForce()
    {
        $timeout= (int)Configuration::get('ETS_LC_TIME_OUT_BACK_END')*3/1000+(int)Configuration::get('ETS_LC_TIME_OUT')*3/1000;
        $conversation = LC_Conversation::getCustomerConversation();
        $last_online= Ets_livechat::getDateLastAdminOnline();
        $statusEmployee = ($conversation && $conversation->id_employee && !$conversation->end_chat && $status=self::getStatusEmployee($conversation->id_employee) ) ? $status : '';
        if($last_online && (strtotime(date('Y-m-d H:i:s')) < strtotime($last_online)+$timeout) )
            if($statusEmployee=='offline')
                return 0;
            else
                return $statusEmployee;
        else
            return 0;
    }
    public static function isAdminOnline()
    {
        if(Configuration::get('ETS_LC_FORCE_ONLINE'))
            return 'online';
        $conversation = LC_Conversation::getCustomerConversation();
        $timeout= (int)Configuration::get('ETS_LC_TIME_OUT_BACK_END')*5/1000+(int)Configuration::get('ETS_LC_TIME_OUT')*5/1000;
        $currenttime = strtotime(date('Y-m-d H:i:s'));
        $datetime = date('Y-m-d H:i:s', $currenttime-$timeout);
        $employees = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_employee_online WHERE date_online >="'.pSQL($datetime).'"'.($conversation && $conversation->id_employee ? ' AND id_employee='.(int)$conversation->id_employee :''));
        if($employees)
        {
            $employee_status = array(
                'online' => false,
                'do_not_disturb' => false,
                'invisible' => false,
            );
            foreach($employees as $employee)
            {
                $status = Db::getInstance()->getValue('SELECT status FROM '._DB_PREFIX_.'ets_livechat_employee_status WHERE id_employee ='.(int)$employee['id_employee'].' AND id_shop='.(int)Context::getContext()->shop->id);
                if(isset($employee_status[$status]))
                    $employee_status[$status] = true;
            }
            foreach($employee_status as $k => $v)
                if($v)
                    return $k;
            return 0;
        }
        else
            return 0;
    }
    public static function getDateLastAdminOnline()
    {
        $conversation = LC_Conversation::getCustomerConversation();
        //if($conversation)
//        {
//            $sql = 'SELECT date_online FROM '._DB_PREFIX_.'ets_livechat_employee_online eo
//                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de on (eo.id_employee = de.id_employee)
//                LEFT JOIN '._DB_PREFIX_.'employee e ON (e.id_employee=eo.id_employee)
//                WHERE 1 '.($conversation->id_departments && $conversation->id_departments_wait!=-1 ? '( AND de.id_departments= "'.(int)$conversation->id_departments.'" OR e.id_profile=1)':'').' AND eo.id_shop="'.(int)Context::getContext()->shop->id.'" ORDER BY date_online DESC';
//        }
//        else
            $sql = 'SELECT date_online FROM '._DB_PREFIX_.'ets_livechat_employee_online WHERE id_shop="'.(int)Context::getContext()->shop->id.'"'.($conversation && $conversation->id_employee && !$conversation->end_chat ? ' AND id_employee="'.(int)$conversation->id_employee.'"':'').' ORDER BY date_online DESC';
        return Db::getInstance()->getValue($sql);
    }
    public function hookDisplayBackOfficeFooter()
    {
        if($this->context->cookie->converation_opened)
        {
            $declines=$this->getDeclineConversation();
            $conversation_opened = Tools::jsonDecode($this->context->cookie->converation_opened,true);
            if($conversation_opened)
            {
                foreach($conversation_opened as $key=>$id_conversation)
                {
                    $conversation = new LC_Conversation($id_conversation);
                    if(!$this->checkConversationEmployee($conversation,$this->context->employee->id) || in_array($id_conversation,$declines))
                        unset($conversation_opened[$key]);
                }
                if($conversation_opened)
                    $this->context->cookie->converation_opened = Tools::jsonEncode($conversation_opened);
                else
                    $this->context->cookie->converation_opened='';          
                $this->context->cookie->write();
            }
        }
        $made_messages = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_pre_made_message');
        $this->smarty->assign(array(
            'ETS_LC_MODULE_URL' => $this->_path,
            'ETS_CONVERSATION_DISPLAY_ADMIN' =>(int)Configuration::get('ETS_CONVERSATION_DISPLAY_ADMIN'),
            'ETS_CONVERSATION_LIST_TYPE' =>Configuration::get('ETS_CONVERSATION_LIST_TYPE'),
            'ETS_CLOSE_CHAT_BOX_BACKEND_TYPE' => Configuration::get('ETS_CLOSE_CHAT_BOX_BACKEND_TYPE'),
            'assigns'=>$this->getConfigs(true),
            'level_request' => LC_Conversation::getLevelRequestAdmin(),
            'converation_opened'=>$this->context->cookie->converation_opened,
            'ets_ajax_message_url'=>$this->_path.'ets_livechat_ajax.php?token='.Tools::getAdminTokenLite('AdminModules').'&getMessage=1', 
            'isRTL' => isset($this->context->language->is_rtl) && $this->context->language->is_rtl,
            'enable_livechat'=>$this->checkEnableLivechat(),
            'admin_controller' =>in_array(Tools::getValue('controller'),array('AdminLiveChatDashboard','AdminLiveChatTickets','AdminLiveChatHistory','AdminLiveChatSettings','AdminLiveChatHelp')),
            'controller_current' => Tools::getValue('controller'),
            'ETS_LC_MODULE_URL_AJAX' =>$this->_path.'ets_livechat_ajax.php?token='.Tools::getAdminTokenLite('AdminModules'), //$this->context->link->getAdminLink('AdminModules',true).'&configure='.$this->name,
            'id_profile' => $this->context->employee->id_profile,
            'made_messages' => $made_messages,
            'link_customer_search' => $this->getBaseLink().'/modules/'.$this->name.'/ets_livechat_search_customer.php?token='.md5($this->id),
            'ETS_LC_MODULE_URL_ADMIM' => $this->context->link->getAdminLink('AdminModules').'&tabsetting=1&configure=ets_livechat',
        ));
        return $this->display(__FILE__,'admin_footer.tpl');
    }
    public function displayListCustomerChat()
    {
        if($this->all_shop && $this->shops)
        {
            foreach($this->shops as $shop)
            {
                 Ets_livechat::updateAdminOnline($shop['id_shop']);
            }
        }
        Ets_livechat::updateAdminOnline();
        if(!Tools::getValue('auto'))
        {
            $this->updateLastAction();
        }
        $conversations =LC_Conversation::getConversations(Tools::getValue('customer_all'),Tools::getValue('customer_archive'),Tools::getValue('customer_search'));
        $status= Ets_livechat::getStatusEmployee($this->context->employee->id) ? Ets_livechat::getStatusEmployee($this->context->employee->id) : 'online';
        $this->context->smarty->assign(
            array(
                'isRTL' => isset($this->context->language->is_rtl) && $this->context->language->is_rtl,
                'config'=>$this->getConfigs(),
                'employee_info' => $this->_getCompanyInfo($this->context->employee->id),
                'totalMessageNoSeen'=>LC_Conversation::getTotalMessageNoSeen(),
                'conversations' => $conversations,
                'lc_chatbox_top'=> isset($this->context->cookie->lc_chatbox_top) && $this->context->cookie->lc_chatbox_top!=='' ? $this->context->cookie->lc_chatbox_top :false,
                'lc_chatbox_left'=> isset($this->context->cookie->lc_chatbox_left) ? $this->context->cookie->lc_chatbox_left :false,
                'loaded'=> Count($conversations) < Tools::getValue('count_conversation'),
                'id_profile' => $this->context->employee->id_profile,
                'status_employee' => Configuration::get('ETS_LC_FORCE_ONLINE')? 'foce_online' : $status,
                'ETS_CONVERSATION_DISPLAY_ADMIN' =>(int)Configuration::get('ETS_CONVERSATION_DISPLAY_ADMIN'),
                'livechatDir'=> $this->_path,
                'modulUrl' => 'index.php?controller=AdminModules&token='.Tools::getAdminTokenLite('AdminModules').'&tabsetting=1&configure='.$this->name,
                'refresh'=>(int)Tools::getValue('refresh'),
            )
        );
        return $this->display(__FILE__,'list_customer_chat.tpl');
    }
    public function getTemplateEmail($messages)
    {
        $this->context->smarty->assign(
            array(
                'messages'=>$messages,
            )
        );
        return $this->display(__FILE__,'email_messages.tpl');
    }
    public function renderExtraForm()
    {
        $sql ='SELECT * FROM '._DB_PREFIX_.'ets_livechat_auto_msg';
        $auto_replies = Db::getInstance()->executeS($sql);
        $pre_made_messages = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_pre_made_message order by position');
        $employees= Db::getInstance()->executeS(
        'SELECT e.*,IFNULL(s.status,1) as status,pl.name as profile_name FROM '._DB_PREFIX_.'employee e
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_staff s ON (e.id_employee=s.id_employee) 
        LEFT JOIN '._DB_PREFIX_.'profile_lang pl ON (e.id_profile = pl.id_profile AND pl.id_lang="'.(int)$this->context->language->id.'")
        WHERE e.active=1
        HAVING status=1');
        $departments = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_departments ORDER BY sort_order ASC');
        if($departments)
        {
            foreach($departments as &$department)
            {
                $department['agents'] = Db::getInstance()->executeS(
                'SELECT e.*,pl.name as profile_name FROM '._DB_PREFIX_.'employee e
                LEFT JOIN '._DB_PREFIX_.'profile_lang pl ON (e.id_profile= pl.id_profile AND pl.id_lang ="'.(int)$this->context->language->id.'")
                INNER JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (e.id_employee= de.id_employee)
                WHERE de.id_departments="'.(int)$department['id_departments'].' AND e.id_profile!=1"
                ');
            }
        }
        $message_week= $this->getAttachmentsMessage(true,' AND datetime_added <"'.pSQL(date('Y-m-d',strtotime('-1 WEEK'))).'"');
        $note_week= $this->getAttachmentsNote(true,' AND date_add <"'.pSQL(date('Y-m-d',strtotime('-1 WEEK'))).'"');
        $attachment_week= $this->getAttachmentsTickets(true,' AND t.date_add <"'.pSQL(date('Y-m-d',strtotime('-1 WEEK'))).'"');
        $messages_1_month_ago= $this->getAttachmentsMessage(true,' AND datetime_added <"'.pSQL(date('Y-m-d',strtotime('-1 MONTH'))).'"');
        $note_1_month_ago= $this->getAttachmentsNote(true,' AND date_add <"'.pSQL(date('Y-m-d',strtotime('-1 MONTH'))).'"');
        $attachment_1_month_ago = $this->getAttachmentsTickets(true,' AND t.date_add <"'.pSQL(date('Y-m-d',strtotime('-1 MONTH'))).'"');
        $messages_6_month_ago = $this->getAttachmentsMessage(true,' AND datetime_added <"'.pSQL(date('Y-m-d',strtotime('-6 MONTH'))).'"');
        $notes_6_month_ago = $this->getAttachmentsNote(true,' AND date_add <"'.pSQL(date('Y-m-d',strtotime('-6 MONTH'))).'"');
        $attachments_6_month_ago = $this->getAttachmentsTickets(true,' AND t.date_add <"'.pSQL(date('Y-m-d',strtotime('-6 MONTH'))).'"');
        $messages_year_ago = $this->getAttachmentsMessage(true,' AND datetime_added <"'.pSQL(date('Y-m-d',strtotime('-1 YEAR'))).'"');
        $notes_year_ago = $this->getAttachmentsNote(true,' AND date_add <"'.pSQL(date('Y-m-d',strtotime('-1 YEAR'))).'"');
        $attachments_year_ago = $this->getAttachmentsTickets(true,' AND t.date_add <"'.pSQL(date('Y-m-d',strtotime('-1 YEAR'))).'"');
        $messages_everything = $this->getAttachmentsMessage(true);
        $notes_everything = $this->getAttachmentsNote(true);
        $attachments_everything = $this->getAttachmentsTickets(true);
        $this->context->smarty->assign(
            array(
                'auto_replies'=> $auto_replies,
                'pre_made_messages'=>$pre_made_messages,
                'version'=>'v'.$this->version,
                'employees' => $employees,
                'attachments_1_week' => $message_week['count']+$note_week['count']+$attachment_week['count'],
                'attachments_1_week_size' => $message_week['size']+$note_week['size']+$attachment_week['size'],
                'attachments_1_month_ago' => $messages_1_month_ago['count'] + $note_1_month_ago['count']+$attachment_1_month_ago['count'],
                'attachments_1_month_ago_size' => $messages_1_month_ago['size'] + $note_1_month_ago['size']+$attachment_1_month_ago['size'],
                'attachments_6_month_ago' => $messages_6_month_ago['count']+$notes_6_month_ago['count']+$attachments_6_month_ago['count'],
                'attachments_6_month_ago_size' => $messages_6_month_ago['size']+$notes_6_month_ago['size']+$attachments_6_month_ago['size'],
                'attachments_year_ago' =>$messages_year_ago['count']+$notes_year_ago['count']+$attachments_year_ago['count'] ,
                'attachments_year_ago_size' =>$messages_year_ago['size']+$notes_year_ago['size']+$attachments_year_ago['size'] ,
                'attachments_everything' =>  $messages_everything['count']+$notes_everything['count']+$attachments_everything['count'],
                'attachments_everything_size' =>  $messages_everything['size']+$notes_everything['size']+$attachments_everything['size'],
                'departments' => $departments
            )
        );
        if(version_compare(_PS_VERSION_, '1.6', '<'))
            return $this->display(__FILE__,'extra_form_15.tpl');
        else
            return $this->display(__FILE__,'extra_form.tpl');
    }
    public function _postAutoReply()
    {
        $message_order= Tools::getValue('message_order');
        $auto_content = trim(Tools::getValue('auto_content'));
        $id_auto_msg=(int)Tools::getValue('id_auto_msg');
        if(trim($auto_content)=='' || Tools::strlen(trim($auto_content))>(int)Configuration::get('ETS_LC_MSG_LENGTH'))
        {
            $this->errors[]=$this->l('Auto content is invalid');
        }
        if(!$id_auto_msg)
        {
            if(!(int)$message_order|| !Validate::isUnsignedInt($message_order))
                $this->errors[]= $this->l('Message order is invalid');
            if(Db::getInstance()->getValue('SELECT id_auto_msg FROM '._DB_PREFIX_.'ets_livechat_auto_msg WHERE message_order ="'.(int)$message_order.'"'))
            {
                $this->errors[] = $this->l('Message order existed');
            }
        }
        else
        {
            if(Db::getInstance()->getValue('SELECT id_auto_msg FROM '._DB_PREFIX_.'ets_livechat_auto_msg WHERE message_order ="'.(int)$message_order.'" AND id_auto_msg!='.(int)$id_auto_msg))
            {
                $this->errors[] = $this->l('Message order existed');
            }
        }
        if(!$this->errors)
        {
            if($id_auto_msg)
            {
                $sql = "UPDATE "._DB_PREFIX_."ets_livechat_auto_msg SET auto_content ='".pSQL($auto_content)."',message_order ='".(int)$message_order."' WHERE id_auto_msg=".(int)$id_auto_msg;
                Db::getInstance()->execute($sql);
                $success = $this->l('Updated auto message successfully');
            }
            else
            {
                $sql= "INSERT INTO "._DB_PREFIX_."ets_livechat_auto_msg VALUES('','".(int)$message_order."','".pSQL($auto_content)."')";
                Db::getInstance()->execute($sql);
                $id_auto_msg = Db::getInstance()->Insert_ID();
                $success = $this->l('Added auto message successfully');
            }
            die(
                Tools::jsonEncode(
                    array(
                        'error'=>false,
                        'id_auto_msg'=>$id_auto_msg,
                        'message_order'=>(int)$message_order,
                        'auto_content' =>trim($auto_content),
                        'success'=>$success
                    )
                )
            );    
        }
        else
        {
            die(
                Tools::jsonEncode(
                    array(
                        'error'=>$this->errors? $this->displayError($this->errors):false,
                    )
                )
            );
        }
    }
    public function _getFromDepartments($id_departments)
    {
        $departments= Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_departments WHERE id_departments='.(int)$id_departments);
        if($departments)
        {
            $agents= Db::getInstance()->executeS('
            SELECT * FROM '._DB_PREFIX_.'employee e 
            INNER JOIN '._DB_PREFIX_.'ets_livechat_departments_employee d ON (d.id_employee = e.id_employee)
            WHERE d.id_departments = "'.(int)$id_departments.'"');
            $array_agents= array();
            if($agents)
            {
                foreach($agents as $agent)
                {
                    $array_agents[]=$agent['id_employee'];
                }
            }
            $departments['agents'] =$array_agents;
        }
        $employees= Db::getInstance()->executeS(
        'SELECT e.*,IFNULL(s.status,1) as status FROM '._DB_PREFIX_.'employee e
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_staff s ON (e.id_employee=s.id_employee) 
        WHERE e.active=1
        HAVING status=1');
        $this->context->smarty->assign(
            array(
                'departments' => $departments,
                'employees' =>$employees,
            )
        );
        die(
            Tools::jsonEncode(
                array(
                    'departments_from' => $this->display(__FILE__,'department_form.tpl'),
                )
            )
        );
    }
    public function _postDepartments()
    {
        $departments_agents= explode(',',trim(Tools::getValue('departments_agents'),','));
        if((!$departments_agents || !trim(Tools::getValue('departments_agents'),',')) && !Tools::getValue('departments_name_all'))
            $this->errors[]= $this->l('Staffs is required');
        if($id_departments = Tools::getValue('id_departments'))
        {
            $departments= new LC_Departments($id_departments);
        }
        else
        {
            $departments = new LC_Departments();
            $max_position = Db::getInstance()->getValue('SELECT max(sort_order) FROM '._DB_PREFIX_.'ets_livechat_departments');
            $departments->sort_order = $max_position+1;
        }
            
        $departments->all_employees = Tools::getValue('departments_name_all');
        $departments->status=Tools::getValue('departments_status');
        if(trim(Tools::getValue('departments_name')))
            $departments->name = Tools::getValue('departments_name');
        else
            $this->errors[]= $this->l('Department name is required');
        $departments->description = Tools::getValue('departments_description');
        if($this->errors)
        {
            die(
                Tools::jsonEncode(
                    array(
                        'error'=>$this->errors? $this->displayError($this->errors):false,
                    )
                )
            );
        }
        else
        {
            $success='';
            if(!$departments->id)
            {
                if(!$departments->add())
                {
                    die(
                        Tools::jsonEncode(
                            array(
                                'error'=> $this->displayError($this->l('Add errors')),
                            )
                        )
                    );
                }
                else
                    $success = $this->l('Added department successfully');
            }
            elseif(!$departments->update())
            die(
                    Tools::jsonEncode(
                        array(
                            'error'=> $this->displayError($this->l('Update errors')),
                        )
                    )
            );
            else
                $success = $this->l('Updated department successfully');
            Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_livechat_departments_employee WHERE id_departments='.(int)$departments->id);
            foreach($departments_agents as $departments_agent)
            {
                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_livechat_departments_employee (id_departments,id_employee) VALUES ( "'.(int)$departments->id.'","'.(int)$departments_agent.'")');
            }
            $this->context->smarty->assign(
                array(
                    'departments' => $departments,
                    'employees' => Db::getInstance()->executeS('SELECT e.*,pl.name as profile_name FROM '._DB_PREFIX_.'employee e LEFT JOIN '._DB_PREFIX_.'profile_lang pl ON (e.id_profile=pl.id_profile AND pl.id_lang ="'.(int)$this->context->language->id.'") WHERE e.id_employee IN ('.implode(',',array_map('intval',$departments_agents)).')'),
                )
            );
            die(
                Tools::jsonEncode(
                    array(
                        'error'=>false,
                        'id_departments' => $departments->id,
                        'success' => $success,
                        'department'=>$this->display(__FILE__,'department.tpl'),
                    )
                )
            );
        }
    }
    public function _postPreMadeMessage(){
        $short_code_message = trim(Tools::getValue('short_code_message'));
        $message_content= trim(Tools::getValue('message_content'));
        $id_pre_made_message= (int)Tools::getValue('id_pre_made_message');
        if(Tools::strlen($short_code_message)<=0 || Tools::strlen($short_code_message)>200)
        {
            $this->errors[]=$this->l('Short code is invalid.');
        }
        if(Tools::strlen($message_content)<=0 || Tools::strlen($message_content)>(int)Configuration::get('ETS_LC_MSG_LENGTH'))
        {
            $this->errors[]=$this->l('Message content is invalid.');
        }
        if(!$this->errors)
        {
            if($id_pre_made_message)
            {
                $sql = 'UPDATE '._DB_PREFIX_.'ets_livechat_pre_made_message SET short_code="'.pSQL($short_code_message).'",message_content="'.pSQL($message_content).'" WHERE id_pre_made_message='.(int)$id_pre_made_message;
                Db::getInstance()->execute($sql);
                $success = $this->l('Updated pre-made message successfully');
            }
            else
            {
                $sql= "INSERT INTO "._DB_PREFIX_."ets_livechat_pre_made_message (short_code,message_content) VALUES('".pSQL($short_code_message)."','".pSQL($message_content)."')";
                Db::getInstance()->execute($sql);
                $id_pre_made_message = Db::getInstance()->Insert_ID();
                $success = $this->l('Added pre-made message successfully');
            }
            die(
                Tools::jsonEncode(
                    array(
                        'error'=>false,
                        'pre_made_message'=>Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_pre_made_message where id_pre_made_message ='.(int)$id_pre_made_message),
                        'success'=>$success,
                    )
                )
            );    
        }
        else
        {
            die(
                Tools::jsonEncode(
                    array(
                        'error'=>$this->errors? $this->displayError($this->errors):false,
                    )
                )
            );
        }
    }
    static public function getBrowserInfo($browser_name)
    {
        $class='';
        switch ($browser_name) {
            case 'Firefox':
                $class='firefox';
                break;
            case 'Chrome':
                $class='chrome';
                break;
            case 'Opera':
                $class='opera';
                break;
            case 'Safari':
                $class='safari';
                break;
            case 'Internet explorer':
                $class='internet_explorer';
                break;
            default:
                $class='';
        } 
        return $class;
    }
    public function displayListPreMadeMessages()
    {
        $pre_made_messages = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_pre_made_message ORDER BY position');
        $this->context->smarty->assign(
            array(
                'pre_made_messages'=>$pre_made_messages,
                'link_pre_made_messages' =>'index.php?controller=AdminModules&token='.Tools::getAdminTokenLite('AdminModules').'&tabsetting=1&configure='.$this->name.'&current_tab_acitve=pre_made_message',
            )
        );
        return $this->display(__FILE__,'pre_made_messages.tpl');
    }
    public function checkExistConversation($id_conversation)
    {
        if(Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_conversation='.(int)$id_conversation.(!$this->all_shop ? ' AND id_shop="'.(int)$this->context->shop->id.'"':'')))
            return true;
        else
            return false;
    }
    public function checkNewMessage()
    {
        if((int)Tools::getValue('customer_search') || (int)Tools::getValue('customer_archive'))
            return 0;
        if((int)Tools::getValue('customer_all'))
        {
            $sql ='SELECT m.* FROM '._DB_PREFIX_.'ets_livechat_message m';
            if($this->context->employee->id_profile!=1 && Ets_livechat::checkDepartments())
            {
                $sql .=' LEFT JOIN '._DB_PREFIX_.'ets_livechat_conversation c ON (c.id_conversation=m.id_conversation)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments d ON (d.id_departments=c.id_departments)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (de.id_departments=c.id_departments)';
            };
            $sql .=' WHERE m.id_message > "'.(int)Tools::getValue('lastID_message').'" AND m.id_employee=0';
            if($this->context->employee->id_profile!=1 && Ets_livechat::checkDepartments())
                $sql .=' AND (d.all_employees=1 OR c.id_departments=0 OR de.id_employee="'.(int)$this->context->employee->id.'")';
            $sql .=' GROUP BY m.id_conversation'; 
        }    
        else
        {
            $sql ='SELECT m.* FROM '._DB_PREFIX_.'ets_livechat_message m
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_conversation c ON (c.id_conversation=m.id_conversation)';
            if($this->context->employee->id_profile!=1 && Ets_livechat::checkDepartments())
            {
                $sql .=' LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments d ON (d.id_departments=c.id_departments)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (de.id_departments=c.id_departments)';
            };
            $sql .=' WHERE m.id_conversation=c.id_conversation AND  c.archive =0 AND m.id_message > "'.(int)Tools::getValue('lastID_message').'" AND m.id_employee=0';
            if($this->context->employee->id_profile!=1 && Ets_livechat::checkDepartments())
                $sql .=' AND ( d.all_employees=1 OR c.id_departments=0 OR de.id_employee="'.(int)$this->context->employee->id.'")';
            $sql .=' GROUP BY m.id_conversation';
        }
        if($messages = Db::getInstance()->executeS($sql))
        {
            if(count($messages)==1 && $messages[0]['id_conversation']==Tools::getValue('lastID_Conversation'))
                return 1;
            return 2;
        }
        return 0;
    }
    public function hookDisplayBlockOnline($params)
    {
        $html ='';
        $languages = Language::getLanguages(false);
        if($languages)
        {
            foreach($languages as $language)
            {
                $this->assignConfig($language['id_lang']);
                $html .=$this->display(__FILE__,'onlineformchat.tpl');
            }
        }
        return $html;
    }
    public function hookDisplayBlockBusy($params)
    {
        if(version_compare(_PS_VERSION_, '1.6', '<'))
            return '';
        $html ='';
        $languages = Language::getLanguages(false); 
        if($languages)
        {
            foreach($languages as $language)
            {
                $this->assignConfig($language['id_lang']);
                $html .=$this->display(__FILE__,'busyformchat.tpl');
            }
        }
        return $html;
    }
    public function hookDisplayBlockInvisible($params)
    {
        if(version_compare(_PS_VERSION_, '1.6', '<'))
            return '';
        $html ='';
        $languages = Language::getLanguages(false);
        if($languages)
        {
            foreach($languages as $language)
            {
                $this->assignConfig($language['id_lang']);
                $html .=$this->display(__FILE__,'invisiblefromchat.tpl');
            }
        }
        return $html;
    }
    public function hookDisplayBlockOffline($params)
    {
        if(version_compare(_PS_VERSION_, '1.6', '<'))
            return '';
        $html ='';
        $languages = Language::getLanguages(false);
        if($languages)
        {
            foreach($languages as $language)
            {
                $this->assignConfig($language['id_lang']);
                $html .=$this->display(__FILE__,'offlineformchat.tpl');
            }
        }
        return $html;
    }
    public function assignConfig($id_lang)
    {
        $langauge= new Language($id_lang);
        $this->context->smarty->assign(
            array(
                'config'=>$this->getConfigs(false,$id_lang),
                'isRTL' => isset($langauge->is_rtl) && $langauge->is_rtl,
                'language' => $langauge,
                'defaultFormLanguage'=>Configuration::get('PS_LANG_DEFAULT'),
                'employee' => $this->context->employee,
                'employee_info' => $this->_getCompanyInfo($this->context->employee->id,'staff'),
                'needCaptcha'=>Ets_livechat::needCaptcha(),
                'captcha' =>$this->context->link->getModuleLink($this->name,'captcha',array('rand' => Tools::substr(sha1(mt_rand()), 17, 6))),
                'livechatDir'=> $this->_path,
                'isAdminOnline' =>'online',
                'departments' => $this->getDepartments(),
                'emotions'=>$this->emotions,
            )
        );
    }
    public function displayError($errors)
    {
        $this->context->smarty->assign(
            array(
                'errors'=>$errors
            )
        );
        return $this->display(__FILE__,'error.tpl');
    }
    public function displayCucstomerInfo($id_conversation)
    {
        $conversation = new LC_Conversation($id_conversation);
        if($conversation->id_customer)
        {
            $customer= new Customer($conversation->id_customer);
            $this->context->smarty->assign(
                array(
                    'name' => trim(Tools::ucfirst($customer->firstname).' '.Tools::ucfirst($customer->lastname)),
                    'email' => $customer->email,
                    'phone' => ($addresses = $customer->getAddresses($this->context->language->id)) ? ($addresses[0]['phone'] ? $addresses[0]['phone'] : ($addresses[0]['phone_mobile'] ? $addresses[0]['phone_mobile'] : ($conversation && $conversation->customer_phone ? $conversation->customer_phone : ''))) : ($conversation && $conversation->customer_phone ? $conversation->customer_phone : ''),
                )
            );
        }
        else{
            $this->context->smarty->assign(
                array(
                    'name' => $conversation->customer_name,
                    'email' => $conversation->customer_email,
                    'phone' => $conversation->customer_phone,
                )
            );
        }
        return $this->display(__FILE__,'customer_info.tpl');
    }
    public static function updateAdminOnline($id_shop=0)
    {
        if(!$id_shop)
            $id_shop= Context::getContext()->shop->id;
        if(!Module::isEnabled('ets_livechat'))
            return false;
        if(Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_employee_online WHERE id_employee='.(int)Context::getContext()->employee->id.' AND id_shop='.(int)$id_shop))
        {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_employee_online SET date_online = "'.pSQL(date('Y-m-d H:i:s')).'" WHERE id_employee='.(int)Context::getContext()->employee->id.' AND id_shop='.(int)$id_shop);
        }
        else
            Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_livechat_employee_online(id_employee,id_shop,date_online) VALUES("'.(int)Context::getContext()->employee->id.'","'.(int)$id_shop.'","'.pSQL(date('Y-m-d H:i:s')).'")');
    }
    public function getLinkCurrentByUrl()
    {
        if($_SERVER['SERVER_PORT']!="80")
        {
            $url =$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
        }
        else
            $url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
            $url ='https://'.$url;
        }
        else
            $url ='http://'.$url;
        if (strpos($url, '#') !== FALSE) {
            $url = Tools::substr($url, 0, strpos($url, '#'));
        }
        return $url;
    }
    public function getLoginConfigs()
    {
        return array(
            'callback' => $this->context->link->getModuleLink($this->name, 'callback', array(), true),
            'providers' => array(
                'Google'=>array(
                    'enabled' => Configuration::get('ETS_LIVECHAT_ENABLE_GOOGLE') ? true :false,
                    'keys' => array(
                        'id' => Configuration::get('ETS_LIVECHAT_GOOGLE_APP_ID'),
                        'secret' => Configuration::get('ETS_LIVECHAT_GOOGLE_APP_SECRET'),
                        'key' => '',
                    ),
                ),
                'Facebook'=>array(
                    'enabled' => Configuration::get('ETS_LIVECHAT_ENABLE_FACEBOOK') ? true : false,
                    'keys' => array(
                        'id' => Configuration::get('ETS_LIVECHAT_FACEBOOK_APP_ID'),
                        'secret' => Configuration::get('ETS_LIVECHAT_FACEBOOK_APP_SECRET'),
                        'key' => '',
                    ),
                ),
                'Twitter'=>array(
                    'enabled' => Configuration::get('ETS_LIVECHAT_ENABLE_TWITTER') ? true : false,
                    'keys' => array(
                        'id' => Configuration::get('ETS_LIVECHAT_TWITTER_APP_ID'),
                        'secret' => Configuration::get('ETS_LIVECHAT_TWITTER_APP_SECRET'),
                        'key' => '',
                    ),
                ), 
            )
        );
    }
    public function closePopup()
	{
		return $this->display(__FILE__, 'frontJs.tpl');
	}
    public function updateContext(Customer $customer)
	{
	    if ($this->is17)
	        return false;
        $this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
        $this->context->cookie->id_customer = (int)($customer->id);
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->logged = 1;
        $customer->logged = 1;
        $this->context->cookie->is_guest = $customer->isGuest();
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->email = $customer->email;
        // Add customer to the context
        $this->context->customer = $customer;
        if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id)) {
            $this->context->cart = new Cart($id_cart);
        } else {
            $this->context->cart->id_carrier = 0;
            $this->context->cart->setDeliveryOption(null);
            $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)($customer->id));
            $this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)($customer->id));
        }
        $this->context->cart->id_customer = (int)$customer->id;
        $this->context->cart->secure_key = $customer->secure_key;
        $this->context->cart->save();
        $this->context->cookie->id_cart = (int)$this->context->cart->id;
        $this->context->cookie->write();
        $this->context->cart->autosetProductAddress();
        Hook::exec('actionAuthentication', array('customer' => $this->context->customer));
        // Login information have changed, so we check if the cart rules still apply
        CartRule::autoRemoveFromCart($this->context);
        CartRule::autoAddToCart($this->context);
	}
    public function createUser($profile, $provider)
	{
		if (!$profile) {
			die(Tools::jsonEncode(array('errors' => $this->l('Connect API error! Please check your account again.'))));
		}
		elseif ($provider)
		{
			$profile = $this->prepareDataToSave($profile);
			$customer = new Customer();
			$customer->id_shop = (int)$this->context->shop->id;
			$customer->lastname = $profile->lastName;
			$customer->firstname = $profile->firstName;
			$customer->email = $profile->email;
			$passwdGen = Tools::passwdGen(8);
			$customer->passwd = md5(_COOKIE_KEY_.$passwdGen);
			if ($customer->save())
			{
                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_livechat_social_customer (identifier,email) values("'.pSQL($profile->identifier).'","'.pSQL($customer->email).'")');
                $customer->updateGroup(array((int)Configuration::get('PS_CUSTOMER_GROUP')));
				if ($this->is17)
                {
                    $this->context->updateCustomer($customer);
                    Hook::exec('actionAuthentication', array('customer' => $this->context->customer));
                    CartRule::autoRemoveFromCart($this->context);
                    CartRule::autoAddToCart($this->context);
                }
                else
				    $this->updateContext($customer);
                $this->trackingLogin($customer,$provider);
                
			}
			else
                die(Tools::jsonEncode(array('errors' => $this->l('Create account error. Please check your account profile.'))));
		}
	}
    public function trackingLogin($customer, $network)
    {
        if((int)Db::getInstance()->getValue('SELECT id_social_login FROM '._DB_PREFIX_.'ets_livechat_social_login WHERE id_customer='.(int)$customer->id))
        {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_social_login SET social="'.pSQL($network).'",date_login="'.pSQL(date('Y-m-d H:i:s')).'" WHERE id_customer='.(int)$customer->id);
        }
        else
            Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_livechat_social_login(id_customer,social,date_login) VALUES("'.(int)$customer->id.'","'.pSQL($network).'","'.pSQL(date('Y-m-d H:i:s')).'")');
    }
    public function prepareDataToSave($profile)
	{
		if ($profile->firstName && $profile->lastName && Validate::isName($profile->firstName) && Validate::isName($profile->lastName)){
			return $profile;
		} elseif ($profile->firstName){
			$profile->lastName = $profile->firstName;
		} elseif ($profile->lastName){
			$profile->firstName = $profile->lastName;
		} elseif ($profile->displayName) {
			$profile->displayName = str_replace('+', '', $profile->displayName);
			$parts = explode(' ', trim($profile->displayName));
			$nameParts = array();
			foreach($parts as $part) {
				if (trim($part) == '') continue;
				$nameParts[] = $part;
			}
			if (count($nameParts) == 1) {
				$profile->firstName = $profile->lastName = $nameParts[0];
			} elseif (count($nameParts) > 1) {
				$profile->firstName = $nameParts[0];
				unset($nameParts[0]);
				$profile->lastName = implode(' ', $nameParts);
			}
		}
        if (!$profile->firstName || !\Validate::isName($profile->firstName))
            $profile->firstName = 'Unknown';
        if (!$profile->lastName || !\Validate::isName($profile->lastName))
            $profile->lastName = 'Unknown';

		return $profile;
	}
    public function checkConversationEmployee($conversation,$id_employee){
        if(!is_object($conversation))
            $conversation = new LC_Conversation($conversation);
        $employee= new Employee($id_employee);
        $sql= 'SELECT * FROM '._DB_PREFIX_.'ets_livechat_conversation lc
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments d ON (lc.id_departments=d.id_departments OR lc.id_departments_wait=d.id_departments)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (d.id_departments= de.id_departments)
            WHERE lc.id_conversation="'.(int)$conversation->id.'" '
            .($employee->id_profile!=1 && Ets_livechat::checkDepartments() ? ' AND (de.id_employee="'.(int)$id_employee.'" OR d.all_employees =1 OR lc.id_departments=0 OR lc.id_departments_wait=-1)':'')
            .(Configuration::get('ETS_LC_STAFF_ACCEPT') && $employee->id_profile!=1 ? ' AND (lc.id_employee=0 OR lc.id_employee="'.(int)$employee->id.'" OR lc.id_employee_wait="'.(int)$employee->id.'" OR lc.id_employee_wait=-1)':'');
        return Db::getInstance()->getRow($sql);
    }
    public function hookDisplayStaffs()
    {
        $employees = Db::getInstance()->executeS(
        'SELECT e.*,s.name,s.avata,IFNULL(s.status,1) as status,s.signature FROM '._DB_PREFIX_.'employee e
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_staff s ON (e.id_employee= s.id_employee)
        WHERE e.active=1
        '
        );
        if($employees)
        {
            foreach($employees as &$employee)
            {
                if($employee['avata'])
                    $employee['avata'] = $this->_path.'views/img/config/'.$employee['avata'];
                else
                    $employee['avata'] = $this->_path.'views/img/config/adminavatar.jpg';
            }
        }
        $this->context->smarty->assign(
            array(
                'employees'=>$employees,
            )
        );
        return $this->display(__FILE__,'staffs.tpl');
    }
    public function getFormStaff($id_employee)
    {
        $employee = Db::getInstance()->getRow(
            'SELECT * FROM '._DB_PREFIX_.'employee WHERE id_employee='.(int)$id_employee
        );
        $staff = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_staff WHERE id_employee='.(int)$id_employee);
        if($staff)
        {
            $employee['name']=$staff['name'];
            $employee['avata']=$staff['avata'] ? $this->_path.'views/img/config/'.$staff['avata'] :'';
            $employee['status']=$staff['status'];
            $employee['signature']=$staff['signature'];
        }
        else
        {
            $employee['name']='';
            $employee['avata']='';
            $employee['status']=1;
            $employee['signature']='';
        }
        $this->context->smarty->assign(
            array(
                'employee'=>$employee,
                'id_profile' => $this->context->employee->id_profile,
            )
        );
        return $this->display(__FILE__,'staff.tpl');
    }
    public function _postStaff($id_employee)
    {
        $errors = array();
        if(isset($_FILES['avata_staff']['tmp_name']) && isset($_FILES['avata_staff']['name']) && $_FILES['avata_staff']['name'])
        {
            $type = Tools::strtolower(Tools::substr(strrchr($_FILES['avata_staff']['name'], '.'), 1));
            $imageName = $_FILES['avata_staff']['name'];
            $fileName = dirname(__FILE__).'/views/img/config/'.$imageName;   
            if(file_exists($fileName))
            {
                $time=md5(time());
                for($i=0;$i<6;$i++)
                {
                    $index =rand(0,Tools::strlen($time)-1);
                    $imageName =$time[$index].$imageName;
                }
                $fileName = dirname(__FILE__).'/views/img/config/'.$imageName;
            }              
            if(file_exists($fileName))
            {
                $errors[] = $this->l('Avatar already existed. Try to rename the file then reupload');
            }
            else
            {
    			$imagesize = @getimagesize($_FILES['avata_staff']['tmp_name']);
                if (!$errors && isset($_FILES['avata_staff']) &&				
    				!empty($_FILES['avata_staff']['tmp_name']) &&
    				!empty($imagesize) &&
    				in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
    			)
    			{
    				$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');    				
    				if ($error = ImageManager::validateUpload($_FILES['avata_staff']))
    					$errors[] = $error;
    				elseif (!$temp_name || !move_uploaded_file($_FILES['avata_staff']['tmp_name'], $temp_name))
    					$errors[] = $this->l('Can not upload the file');
    				elseif (!ImageManager::resize($temp_name, $fileName, 120, 120, $type))
    					$errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
    				if (isset($temp_name))
    					@unlink($temp_name);
                }
            }
        }
        else
            $imageName='';
        if($errors)
        {
            die(
                Tools::jsonEncode(
                    array(
                        'error' => $this->displayError($errors),
                    )
                )
            );
        }
        else
        {
            
            if($staff = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_staff WHERE id_employee='.(int)$id_employee))
            {
                $oldAvata = $staff['avata'];
                $update = Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_staff SET name="'.pSQL(Tools::getValue('nick_name')).'"'.($this->context->employee->id_profile==1 ? ',status="'.(int)Tools::getValue('staff_status').'"':'').', signature="'.pSQL(Tools::getValue('signature')).'"'.($imageName ? ' , avata="'.pSQL($imageName).'"':'').' WHERE id_employee='.(int)$id_employee);
                if($update && $oldAvata && $imageName)
                {
                    @unlink(dirname(__FILE__).'/views/img/config/'.$oldAvata);
                }
            }
            else
            {
                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_livechat_staff (id_employee,name,avata,status,signature) values("'.(int)$id_employee.'","'.pSQL(Tools::getValue('nick_name')).'","'.pSQL($imageName).'","'.($this->context->employee->id_profile==1 ? (int)Tools::getValue('staff_status'): 1).'","'.pSQL(Tools::getValue('signature')).'")');
            }
            die(
                Tools::jsonEncode(
                    array(
                        'success' => $this->l('Updated successfully'),
                        'image' => $imageName ? $this->_path.'views/img/config/'.$imageName:'',
                        'nick_name'=>Tools::getValue('nick_name'),
                        'status'=>Tools::getValue('staff_status'),
                        'signature'=> Tools::getValue('signature'),
                        'id_employee'=>$id_employee,
                    )
                )
            );
        }
        
    }
    public function _getCompanyInfo($id_employee,$info='')
    {
        if(!$info)
            $info=Configuration::get('ETS_LC_DISPLAY_COMPANY_INFO');
        if($info=='general' || $id_employee==0)
        {
            $name= Configuration::get('ETS_LC_COMPANY_NAME');
            $logo = $this->_path.'views/img/config/'.Configuration::get('ETS_LC_COMPANY_LOGO');
        }
        else
        {
            $employee = Db::getInstance()->getRow(
                'SELECT * FROM '._DB_PREFIX_.'employee WHERE id_employee='.(int)$id_employee
            );
            $staff = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_staff WHERE id_employee='.(int)$id_employee);
            if($staff)
            {
                if($staff['name'])
                    $name = $staff['name'];
                else
                   $name= $employee['firstname'].' '.$employee['lastname']; 
                if($staff['avata'])
                    $logo=$this->_path.'views/img/config/'.$staff['avata'];
                else
                    $logo = $this->_path.'views/img/config/'.Configuration::get('ETS_LC_COMPANY_LOGO');
            }
            else
            {
                $name= $employee['firstname'].' '.$employee['lastname'];
                $logo =$this->_path.'views/img/config/'.Configuration::get('ETS_LC_COMPANY_LOGO');
            }
        }
        return array(
                    'name'=>$name,
                    'logo' => $logo,
                );
    }
    public static function checkDepartments()
    {
        if(!LC_Conversation::isUsedField('departments'))
            return false;
        else
        {
            $departments = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_departments WHERE status=1');
            if($departments)
                return true;
        }
        return false;
    }
    public function _accpectConversation($id_conversation,$ajax=true)
    {
        $conversation = new LC_Conversation($id_conversation);
        $error='';
        if(($conversation->id_employee==0 || ($conversation->id_departments_wait==0 && $conversation->id_employee)) && $conversation->id_departments)
            if($this->context->employee->id_profile!=1 && Ets_livechat::checkDepartments() && $conversation->id_departments && !$this->checkDepartmentsExitsEmployee($conversation->id_departments))
                $error = $this->l('You do not have access permission');
        if($conversation->id_employee && $conversation->id_departments_wait >0)
            if($this->context->employee->id_profile!=1 && !$this->checkDepartmentsExitsEmployee($conversation->id_departments_wait))
                $error = $this->l('You do not have access permission');
        if($conversation->id_employee && $conversation->id_employee_wait >0 && $conversation->id_employee_wait!=$this->context->employee->id)
            $error = $this->l('You do not have access permission');
        if($conversation->id_employee && !$conversation->id_employee_wait && !$conversation->id_departments_wait)
            $error = $this->l('There was an employee who accepted the conversation');
        if(Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_staff_decline WHERE id_employee='.(int)$this->context->employee->id.' AND id_conversation='.(int)$conversation->id))
            $error = $this->l('You declined');
        if(!$error)
        {
            $conversation->id_employee=$this->context->employee->id;
            $conversation->id_employee_wait=0;
            if($conversation->id_departments_wait)
            {
                $conversation->id_departments = $conversation->id_departments_wait ==-1 ? 0 : $conversation->id_departments_wait;
                $conversation->id_departments_wait=0;
            }
            $conversation->date_accept = date('Y-m-d H:i:s');
            $conversation->update();
            if($ajax)
            {
               die(
                    Tools::jsonEncode(
                        array(
                            'error'=>false,
                        )
                    )
                ); 
            }
        }
        if($ajax)
        {
            die(
                Tools::jsonEncode(
                    array(
                        'error'=>$error,
                    )
                )
            );
        }
        return false;
        
    }
    public function _declineConversation($id_conversation)
    {
        $conversation = new LC_Conversation($id_conversation);
        if($conversation->id_employee==$this->context->employee->id)
        {
            die(Tools::jsonEncode(
                array(
                    'error' => $this->l('You do not have access permission'),
                )
            ));
        }
        if(!Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_staff_decline WHERE id_employee="'.(int)$this->context->employee->id.'" AND id_conversation='.(int)$id_conversation))
        {
            Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_livechat_staff_decline(id_employee,id_conversation) VALUES("'.(int)$this->context->employee->id.'","'.(int)$id_conversation.'")');
        }
        die(
            Tools::jsonEncode(
                array(
                    'error' => false,
                    'id_profile'=>$this->context->employee->id_profile,                    
                )
            )
        );
    }
    public function getDeclineConversation()
    {
        if($this->checkVesionModule())
        {
            if(isset($this->context->employee) && $this->context->employee->id && $this->context->employee->id_profile!=1)
            {
                $array = array();
                $declines = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_staff_decline WHERE id_employee="'.(int)$this->context->employee->id.'"');
                if($declines)
                {
                    foreach($declines as $decline)
                        $array[]=$decline['id_conversation'];
                    
                }
                return $array;
            }
        }
        return array();
    }
    public function duplicateConversation(&$conversation)
    {
        if(!Tools::getValue('lc_conversation_end_chat'))
        {
            die(
                Tools::jsonEncode(
                    array(
                        'lc_conversation_end_chat'=>1,
                    )
                )
            );
        }
        $conversation->end_chat=0;
        $conversation->id_employee=0;
        $conversation->id_employee_wait=0;
        $conversation->id_departments_wait=0;
        $conversation->id=0;
        $conversation->replied=0;
        $conversation->blocked=0;
        $conversation->rating=0;
        $conversation->id_ticket=0;
        $conversation->captcha_enabled=0;
        if(!$conversation->add())
            return false;
        else
        {
            $this->context->cookie->lc_id_conversation = $conversation->id;
            $this->context->cookie->write();
            return true;
        }
    }
    public function _cancelAcceptance($id_conversation)
    {
        $conversation = new LC_Conversation($id_conversation);
        if(($conversation->id_departments_wait || $conversation->id_employee_wait) && $conversation->id_employee==$this->context->employee->id)
        {
            $conversation->id_employee_wait=0;
            $conversation->id_departments_wait=0;
            $conversation->update();
            die(Tools::jsonEncode(
                array(
                    'error'=>false,
                )
            ));
        }
        else
        {
            die(Tools::jsonEncode(
                array(
                    'error' => $this->l('You do not have access permission'),
                )
            ));
        }
    }
    public static function checkSupperAdminDecline($conversation)
    {
        if(Context::getContext()->employee->id_profile==1)
        {
            if(Db::getInstance()->getValue('SELECT * FROM '._DB_PREFIX_.'ets_livechat_staff_decline WHERE id_conversation="'.(int)$conversation->id.'" AND  id_employee='.(int)Context::getContext()->employee->id))
                return true;
            else
                return false;
        }
        return false;
    }
    public static function checkWaitAccept($conversation)
    {
        if(!is_object($conversation))
            $conversation = new LC_Conversation($conversation);
        if(Configuration::get('ETS_LC_ENDCHAT_AUTO')>0 && !$conversation->end_chat)
        {
            $timeend= Configuration::get('ETS_LC_ENDCHAT_AUTO')*60;
            if(strtotime('now') > (strtotime($conversation->date_message_last_customer)+$timeend))
            {
                $conversation->end_chat=-1;
                $conversation->save();
                return 0;
            } 
        }
        if(!$conversation->end_chat && !$conversation->id_employee && Configuration::get('ETS_LC_STAFF_ACCEPT'))
        {
            if(self::checkSupperAdminDecline($conversation))
                return 0;
            else
                return 1;
        }
        
        return 0;
    }
    public static function checkHasChanged($conversation)
    {
        if(!is_object($conversation))
            $conversation= new LC_Conversation($conversation);
        if(Configuration::get('ETS_LC_ENDCHAT_AUTO')>0 && !$conversation->end_chat)
        {
            $timeend= Configuration::get('ETS_LC_ENDCHAT_AUTO')*60;
            if(strtotime('now') > (strtotime($conversation->date_message_last_customer)+$timeend))
            {
                $conversation->end_chat=-1;
                $conversation->save();
                return false;
            } 
        }
        $context =Context::getContext();
        if(!$conversation->end_chat && ($conversation->id_employee_wait==$context->employee->id OR $conversation->id_employee_wait==-1 OR ($context->employee->id_profile==1 && $conversation->id_employee_wait)) && $conversation->id_employee && $conversation->id_employee!=$context->employee->id && Configuration::get('ETS_LC_STAFF_ACCEPT'))
        {
            $employee= new Employee($conversation->id_tranfer);
            return $employee->firstname.' '.$employee->lastname;
        }
        $ets_livechat= new Ets_livechat();
        if($conversation->end_chat && $conversation->id_departments_wait >0 && $conversation->id_departments && !$ets_livechat->checkDepartmentsExitsEmployee($conversation->id_departments) && $ets_livechat->checkDepartmentsExitsEmployee($conversation->id_departments_wait))
        {
            $department= new LC_Departments($conversation->id_departments);
                return $department->name;
        }
        return 0;
    }
    public function checkWaitSupport($conversation)
    {
        if(!Configuration::get('ETS_LC_STAFF_ACCEPT'))
            return false;
        if(!is_object($conversation))
            $conversation = new LC_Conversation($conversation);
        if($conversation->id_employee)
            return false;
        else
            return !Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation='.(int)$conversation->id.' AND id_employee !=0');
    }
    public static function getStatusEmployee($id_employee)
    {
        $status= Db::getInstance()->getValue('SELECT status FROM '._DB_PREFIX_.'ets_livechat_employee_status WHERE id_employee='.(int)$id_employee.' AND id_shop='.(int)Context::getContext()->shop->id);
        return $status ? $status : false;
    }
    public function hookCustomerAccount($params)
    {
        if(!$this->context->customer->logged)
            return '';
        $count_support = Db::getInstance()->getValue('SELECT COUNT(DISTINCT m.id_message) FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message m
        INNER JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_message_note n ON (m.id_message = n.id_message)
        WHERE m.id_customer = "'.(int)$this->context->customer->id.'" AND n.readed=0 AND n.id_employee!=0');
        $this->smarty->assign(
            array(
                'ETS_LC_CUSTOMER_OLD'=> Configuration::get('ETS_LC_CUSTOMER_OLD'),
                'count_support' => $count_support
            )
        );
        
        if($this->is17)
    	   return $this->display(__FILE__, 'my-account.tpl');
        else
            return $this->display(__FILE__, 'my-account16.tpl');
    }
    public function hookDisplayMyAccountBlock($params)
    {
    	return $this->hookCustomerAccount($params);
    }
    public function getBreadCrumb()
    {
        $nodes = array();
        $nodes[] = array(
            'title' => $this->l('Home'),
            'url' => $this->context->link->getPageLink('index', true),
        );
        if(Tools::getValue('controller')=='info')
        {
            $nodes[] = array(
                'title' => $this->l('My account'),
                'url' => $this->context->link->getPageLink('my-account'),
            );
            $nodes[] = array(
                'title' => $this->l('Chat info'),
                'url' => $this->context->link->getModuleLink($this->name,'info')
            );
        }
        if(Tools::getValue('controller')=='ticket')
        {
            $nodes[] = array(
                'title' => $this->l('My account'),
                'url' => $this->context->link->getPageLink('my-account'),
            );
            $nodes[] = array(
                'title' => $this->l('Support tickets'),
                'url' => $this->context->link->getModuleLink($this->name,'ticket')
            );
            if(Tools::getValue('viewticket') && $id_ticket=Tools::getValue('id_ticket'))
            {
                $nodes[] = array(
                    'title' => $this->l('Ticket #').$id_ticket,
                    'url' => $this->context->link->getModuleLink($this->name,'ticket',array('viewticket'=>1,'id_ticket'=>$id_ticket))
                );
            }
        }
        if(Tools::getValue('controller')=='history')
        {
            $nodes[] = array(
                'title' => $this->l('My account'),
                'url' => $this->context->link->getPageLink('my-account'),
            );
            $nodes[] = array(
                'title' => $this->l('Chat history'),
                'url' => $this->context->link->getModuleLink($this->name,'history'),
            );
            if(Tools::isSubmit('viewchat') && $id_conversation=Tools::getValue('id'))
            {
                $nodes[] = array(
                    'title' => $this->l('Conversation #').Tools::getValue('id'),
                    'url' => $this->context->link->getModuleLink($this->name,'history',array('viewchat'=>1,'id'=>$id_conversation)),
                );
            }
        }
        if(Tools::getValue('controller')=='form' && $id_form=Tools::getValue('id_form'))
        {
            $form = new LC_Ticket_form($id_form,$this->context->language->id);
            $nodes[] = array(
                'title' => $this->l('My account'),
                'url' => $this->context->link->getPageLink('my-account'),
            );
            $nodes[] = array(
                'title' => $this->l('Support tickets'),
                'url' => $this->context->link->getModuleLink($this->name,'ticket')
            );
            $nodes[] = array(
                'title' => $form->title ? $form->title : $this->l('Form #').$id_form,
                'url' => $this->getFormLink($id_form),
            );
        }
        if($this->is17)
            return array('links' => $nodes,'count' => count($nodes));
        return $this->displayBreadcrumb($nodes);
    }
    public function displayBreadcrumb($nodes)
    {
        $this->smarty->assign(array('nodes' => $nodes));
        return $this->display(__FILE__, 'nodes.tpl');
    }
    public function displaySuccessMessage($msg, $title = false, $link = false)
    {
         $this->smarty->assign(array(
            'msg' => $msg,
            'title' => $title,
            'link' => $link
         ));
         if($msg)
            return $this->displayConfirmation($this->display(__FILE__, 'success_message.tpl'));
    }
    public function renderFormCustomerInformation(){
        $customer_avata= Db::getInstance()->getValue('SELECT avata FROM '._DB_PREFIX_.'ets_livechat_customer_info WHERE id_customer='.(int)$this->context->customer->id);
        $this->smarty->assign(
            array(
                'customer' => $this->context->customer,
                'link' => $this->context->link,
                'link_delete_image' => $this->context->link->getModuleLink($this->name,'info',array('deleteavatar'=>1)),
                'customer_avata' => $customer_avata && file_exists(dirname(__FILE__).'/views/img/config/'.$customer_avata) ? $this->_path.'views/img/config/'.$customer_avata:'',
                'avata_default' => $this->_path.'views/img/config/customeravata.jpg',
            )
        );
        return $this->display(__FILE__,'info.tpl');
    }
    public function _displayHistoryChatCustomer($chatref){
        if($chatref)
        {
            $sql = 'SELECT count(DISTINCT lc.id_conversation) FROM '._DB_PREFIX_.'ets_livechat_conversation lc
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments ld ON (ld.id_departments=lc.id_departments OR ld.id_departments=lc.id_departments_wait)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee lde ON (ld.id_departments=lde.id_departments)
            LEFT JOIN '._DB_PREFIX_.'employee e ON (e.id_employee = lde.id_employee)
            LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer=lc.id_customer)
            WHERE 1 AND lc.id_shop="'.(int)Context::getContext()->shop->id.'"
            AND lc.chatref='.(int)$chatref;
            $totalRecords = Db::getInstance()->getValue($sql);
            $page = (int)Tools::getValue('page') && (int)Tools::getValue('page') > 0 ? (int)Tools::getValue('page') : 1;
            require_once(dirname(__FILE__).'/classes/LC_paggination_class.php');
            $paggination = new LC_paggination_class();            
            $paggination->total = $totalRecords;
            $paggination->url = isset($this->context->customer) && $this->context->customer->id ? $this->context->link->getModuleLink($this->name,'history',array('page'=>'_page_')) :'gethistory&page=_page_';
            $paggination->limit =  20;
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $start = $paggination->limit * ($page - 1);
            if($start < 0)
                $start = 0;
            $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
            $paggination->style_links = $this->l('links');
            $paggination->style_results = $this->l('results');
            $sql = 'SELECT lc.*,CONCAT(c.firstname," ",c.lastname) as fullname  FROM '._DB_PREFIX_.'ets_livechat_conversation lc
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments ld ON (ld.id_departments=lc.id_departments OR ld.id_departments=lc.id_departments_wait)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee lde ON (ld.id_departments=lde.id_departments)
            LEFT JOIN '._DB_PREFIX_.'employee e ON (e.id_employee = lde.id_employee)
            LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer=lc.id_customer)
            WHERE 1 AND lc.id_shop="'.(int)Context::getContext()->shop->id.'"
            AND lc.chatref="'.(int)$chatref.'" 
            GROUP BY lc.id_conversation ORDER BY date_message_last_customer DESC LIMIT '.(int)$start.','.(int)$paggination->limit.'';
            $conversations = Db::getInstance()->executeS($sql);
        }
        else
            $conversations= array();
        if($conversations)
        {
            foreach($conversations as &$conversation)
            {
                if(isset($this->context->customer) && $this->context->customer->id)
                    $conversation['link_view'] = $this->context->link->getModuleLink($this->name,'history',array('viewchat'=>1,'id'=>$conversation['id_conversation']));
                else
                    $conversation['link_view'] = $this->context->link->getAdminLink('AdminModules').'&tabsetting=1&configure=ets_livechat&viewchat&id='.(int)$conversation['id_conversation'];
                $conversation['last_message'] = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation="'.(int)$conversation['id_conversation'].'" AND id_employee=0 ORDER BY id_message DESC');
                if($conversation['last_message'])
                {
                    if(date('Y-m-d')==date('Y-m-d',strtotime($conversation['last_message']['datetime_added'])))
                    {
                        $conversation['last_message']['datetime_added'] =date('h:i A',strtotime($conversation['last_message']['datetime_added']));
                    }
                    else
                    {
                       if(date('Y')==date('Y',strtotime($conversation['last_message']['datetime_added'])))
                       {
                            $conversation['last_message']['datetime_added'] =date('d-m h:i A',strtotime($conversation['last_message']['datetime_added']));
                       }
                       else
                            $conversation['last_message']['datetime_added'] =date('d-m-Y h:i A',strtotime($conversation['last_message']['datetime_added']));
                    }
                    if($this->emotions)
                    {
                        foreach($this->emotions as $key=> $emotion)
                        {
                            $img = '<span title="'.$emotion['title'].'"><img src="'.$this->_path.'views/img/emotions/'.$emotion['img'].'"></span>';
                            $conversation['last_message']['message'] = str_replace(array(Tools::strtolower($key),$key),array($img,$img),$conversation['last_message']['message']);
                        }
                    }
                }
                else
                    unset($conversation);
                
            }
        }
        $this->smarty->assign(
            array(
                'conversations'=>$conversations,
                'paggination' => isset($paggination) ?  $paggination->render():'',
            )
        );
        return $this->display(__FILE__,'history.tpl');
    }
    public function _displayConversationDetail($conversation)
    {
        if(!is_object($conversation))
            $conversation= new LC_Conversation($conversation);
        $this->smarty->assign(
            array(
                'messages' => ($messages=$conversation->getMessages(0,0,'ASC',0)) ? array_reverse($messages) :false,
                'config' => $this->getConfigs(),
                'link_back' => isset($this->context->customer) && $this->context->customer->id ? $this->context->link->getModuleLink($this->name,'history') :'',
            )
        );
        return $this->display(__FILE__,'conversation_detail.tpl');
    }
    public function _getMoreCustomer()
    {
        $lastID_Conversation = Tools::getValue('lastID_Conversation');
        $conversation = new LC_Conversation($lastID_Conversation);
        $conversations =LC_Conversation::getConversations(Tools::getValue('customer_all'),Tools::getValue('customer_archive'),Tools::getValue('customer_search'),$conversation->date_message_last_customer);
        $this->context->smarty->assign(
            array(
                'conversations' =>$conversations,
                'refresh'=>1,
            )
        );
        die(
            Tools::jsonEncode(
                array(
                    'list_more_customer' => $this->display(__FILE__,'list_customer_chat.tpl'),
                    'loaded' => Count($conversations)<Tools::getValue('count_conversation'),
                )
            )
        );
    }
    public function hookDisplaySystemTicket()
    {
        $forms = $this->getFormTicket();
        $this->smarty->assign(
            array(
                'forms' => $forms,
            )
        );
       return $this->display(__FILE__,'system_ticket.tpl');
    }
    public function getFormTicket($active=false,$filter='')
    {
       $sql = 'SELECT * FROM '._DB_PREFIX_.'ets_livechat_ticket_form f
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_lang fl ON (f.id_form=fl.id_form)
       WHERE f.id_shop="'.(int)$this->context->shop->id.'" '.($active ? ' AND active=1':'').($filter ? $filter :'').' GROUP BY f.id_form ORDER BY f.sort_order asc';
       $forms = Db::getInstance()->executeS($sql); 
       if($forms)
       {
         foreach($forms as &$form)
         {
            $form['link'] = $this->getFormLink($form['id_form']);
         }
       }
       return $forms;
    }
    public function _displayFormTicket()
    {
        die(
            Tools::jsonEncode(
                array(
                    'form_html' =>  $this->_renderFormticket(),
                    'fields_list' => ($id_form = Tools::getValue('id_form'))? $this->_displayListFields($id_form):'',
                )
            )
        );
    }
    public function _renderFormticket()
    {
        //Form
        $fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Ticket forms'),				
				),
				'input' => $this->setConfigForm(),
                'submit' => array(
					'title' => $this->l('Save'),
				),
                'buttons'=>array(
                    array(
                        'type'=>'button',
                        'id'=>'form_ticket_form_cancel_btn',
                        'title'=> $this->l('Cancel'),
                        'icon'=> 'process-icon-cancel',
                    )
                ),
                'form'=>array(
                    'id_form' => 'form_new_ticket_form',
                )
            ),
		);
        $helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = 'form_ticket';
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);
        foreach($languages as &$l)
        {
            if($l['id_lang']==$lang->id)
                $l['is_default']=true;
            else
                $l['is_default']=false;
        }
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'saveFormTicket';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&tabsetting=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = $this->context->employee->id ? Tools::getAdminTokenLite('AdminModules'): false;
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $this->getFieldsFormTicketValues(),
			'languages' => $languages,
			'id_language' => $this->context->language->id,
			'image_baseurl' => $this->_path.'views/img/',
            'is_ps15' => version_compare(_PS_VERSION_, '1.6', '<'),
            'link' => $this->context->link,
		);
		$helper->override_folder = '/';
        return $helper->generateForm(array($fields_form));
    }
    public function _displayListFields($id_form)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'ets_livechat_ticket_form_field f
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_field_lang fl ON (f.id_field = fl.id_field AND fl.id_lang="'.(int)$this->context->language->id.'")
                WHERE f.id_form='.(int)$id_form.' AND f.deleted=0 ORDER BY f.position ASC';
        $fields = Db::getInstance()->executeS($sql);
        if($fields)
        {
            foreach($fields as $key=> &$field)
            {
                $field['html_form'] = $this->GetFormField($field['id_field'],$key+1);
            }
        }
        $this->context->smarty->assign(
            array(
                'fields' => $fields,
            )
        );
        return $this->display(__FILE__,'list_fields.tpl');
    }
    public function getFieldsFormTicketValues()
    {
        $values= array();
        $languages = Language::getLanguages(false);
        if($id_form= Tools::getValue('id_form'))
        {
            $form = new LC_Ticket_form($id_form);
            $values['id_form'] = $form->id;
        }
        else
        {
             $form = new LC_Ticket_form();
             $values['id_form']='';
        }
        $fields = $this->setConfigForm();
        foreach($fields as $filed)
        {
            $key = $filed['name'];
            if($key!='id_form')
            {
                if(isset($filed['lang']) && $filed['lang'])
                {
                    foreach($languages as $language)
                    {
                        $values[$key][$language['id_lang']] = $form->id ? $form->{$key}[$language['id_lang']]:(isset($filed['default']) ? $filed['default'] : '');
                    }
                }    
                else
                {
                    $value= $form->id ? $form->{$key} : (isset($filed['default']) ? $filed['default'] : '');
                    $values[$key] = $filed['type']!='checkbox' ? $value : explode(',',$value) ;
                }
            }  
        }
        return $values;
    }
    public function saveObjForm()
    {
        $languages = Language::getLanguages(false);
        $success = '';
        if($id_form=Tools::getValue('id_form'))
        {
            $object = new LC_Ticket_form($id_form);
        }
        else
        {
             $object = new LC_Ticket_form();
        }
        $fields = $this->setConfigForm();
        $id_language_default = Configuration::get('PS_LANG_DEFAULT');
        if(!(int)Tools::getValue('number_field'))
        {
            $this->errors[] =$this->l('Field is required');
        }
        else
        {
            $default_labels = Tools::getValue('ets_fields_label_'.$id_language_default);
            if($default_labels)
            {
                foreach($default_labels as $default_label)
                {
                    if(!$default_label)
                    {
                        $this->errors[] = $this->l('Label field is required');
                        break;
                    }    
                }
            }
            foreach($languages as $language)
            {
                $language_labels = Tools::getValue('ets_fields_label_'.$language['id_lang']);
                if($language_labels)
                {
                    foreach($language_labels as $language_label)
                    {
                        if($language_label && !Validate::isCleanHtml($language_label))
                            $this->errors[] = $this->l('Label field is invalid');
                    }
                }
            }
            
        }
        foreach($fields as  $field)
        {
            $key= $field['name'];
            if($object->id==1 && $key=='active')
                continue;
            if(!isset($field['lang']) || (isset($field['lang']) && !$field['lang']))
            {
                if(isset($field['required']) && $field['required'] && !Tools::getValue($key))
                {
                    $this->errors[] = $field['label'].' '.$this->l('is required');
                }
                elseif(isset($field['validate']) && method_exists('Validate',$field['validate']))
                {
                    $validate = $field['validate'];
                    if(!Validate::$validate(trim(Tools::getValue($key))))
                        $this->errors[] = $field['label'].' '.$this->l('is invalid');
                    else
                        $object ->{$key} = $field['type']!='checkbox' ?  Tools::getValue($key) : implode(',',Tools::getValue($key));
                    unset($validate);
                }
                else
                    $object ->{$key} = $field['type']!='checkbox' ?  Tools::getValue($key) : implode(',',Tools::getValue($key));
            }
            elseif(isset($field['lang']) && $field['lang'])
            {
                if(isset($field['required']) && $field['required'] && !Tools::getValue($key.'_'.$id_language_default))
                {
                    $this->errors[] = $field['label'].' '.$this->l('is required');
                }
                else
                {
                    $values=  array();
                    foreach($languages as $language)
                    {
                        if(isset($field['validate']) && method_exists('Validate',$field['validate']))
                        {
                            $validate = $field['validate'];
                            if(!Validate::$validate(trim(Tools::getValue($key.'_'.$language['id_lang']))))
                                $this->errors[] = $field['label'].' in language '.$language['iso_code'].' '.$this->l('is invalid');
                            else
                                $values[$language['id_lang']] = Tools::getValue($key.'_'.$language['id_lang']) ? Tools::getValue($key.'_'.$language['id_lang']) : Tools::getValue($key.'_'.$id_language_default);
                            unset($validate);
                        }
                        else
                            $values[$language['id_lang']] =Tools::getValue($key.'_'.$language['id_lang']) ? Tools::getValue($key.'_'.$language['id_lang']) : Tools::getValue($key.'_'.$id_language_default);
                    }
                    $object->{$key} = $values;
                }
            }
        }
        if(!$this->errors)
        {
            if(!$object->id)
            {
                $object->id_shop = $this->context->shop->id;
                if(!$object->add())
                {
                    $this->errors[]= $this->l('Add error');
                }
                $success = $this->l('Form added successfully');
            }
            else
            {
                if(!$object->update())
                    $this->errors[] = $this->l('Update error');
                else
                    $success = $this->l('Form updated successfully');
            }
        }
        if($this->errors)
        {
            if(Tools::isSubmit('run_ajax'))
                die(
                    Tools::jsonEncode(
                        array(
                            'error' => $this->displayError($this->errors),
                        )
                    )
                );
            else
                return false;
        }
        else
        {
            if(Tools::getValue('number_field'))
            {
                $ets_fields_id_field = Tools::getValue('ets_fields_id_field');
                if($ets_fields_id_field)
                {
                    $ets_fields_type= Tools::getValue('ets_fields_type');
                    $ets_fields_is_contact_mail = Tools::getValue('ets_fields_is_contact_mail');
                    $ets_fields_is_contact_name = Tools::getValue('ets_fields_is_contact_name');
                    $ets_fields_position = Tools::getValue('ets_fields_position');
                    $ets_fields_is_subject = Tools::getValue('ets_fields_is_subject');
                    $ets_fields_is_customer_phone_number = Tools::getValue('ets_fields_is_customer_phone_number');
                    $ets_fields_required = Tools::getValue('ets_fields_required');
                    foreach($ets_fields_id_field as $index=> $id_field)
                    {
                        if($id_field)
                            $field_class= new LC_Ticket_field($id_field);
                        else
                            $field_class = new LC_Ticket_field();
                        if($object->id==1 && !$field_class->id)
                            continue;
                        if($object->id!=1)
                        {
                            $field_class->type = isset($ets_fields_type[$index]) ? $ets_fields_type[$index] :'text';
                            $field_class->is_contact_mail = isset($ets_fields_is_contact_mail[$index]) ? $ets_fields_is_contact_mail[$index]:0;
                            $field_class->is_contact_name = isset($ets_fields_is_contact_name[$index]) ? $ets_fields_is_contact_name[$index]:0;
                            $field_class->id_form = $object->id;
                            $field_class->position = isset($ets_fields_position[$index]) ? $ets_fields_position[$index] :1;
                            $field_class->is_subject = isset($ets_fields_is_subject[$index]) ? $ets_fields_is_subject[$index] :0;
                            $field_class->required = isset($ets_fields_required[$index]) ? $ets_fields_required[$index] : '';
                            $field_class->is_customer_phone_number = isset($ets_fields_is_customer_phone_number[$index]) ? $ets_fields_is_customer_phone_number[$index] : '';
                        }
                        foreach($languages as $language)
                        {
                            $ets_fields_options= Tools::getValue('ets_fields_options_'.$language['id_lang']);
                            $ets_fields_options_default = Tools::getValue('ets_fields_options_'.$id_language_default);
                            $ets_fields_label = Tools::getValue('ets_fields_label_'.$language['id_lang']);
                            $ets_fields_label_default = Tools::getValue('ets_fields_label_'.$id_language_default);
                            $ets_fields_placeholder= Tools::getValue('ets_fields_placeholder_'.$language['id_lang']);
                            $ets_fields_placeholder_default =  Tools::getValue('ets_fields_placeholder_'.$id_language_default);
                            $ets_fields_description = Tools::getValue('ets_fields_description_'.$language['id_lang']);
                            $ets_fields_description_default=  Tools::getValue('ets_fields_description_'.$id_language_default);
                            $field_class->label[$language['id_lang']] = isset($ets_fields_label[$index]) && $ets_fields_label[$index]  ? $ets_fields_label[$index]: isset($ets_fields_label_default[$index]) ? $ets_fields_label_default[$index]:'';
                            $field_class->options[$language['id_lang']] = isset($ets_fields_options[$index]) && $ets_fields_options[$index] ? $ets_fields_options[$index]: isset($ets_fields_options_default[$index]) ? $ets_fields_options_default[$index]:'';
                            $field_class->placeholder[$language['id_lang']] = isset($ets_fields_placeholder[$index]) && $ets_fields_placeholder[$index] ? $ets_fields_placeholder[$index] : isset($ets_fields_placeholder_default[$index]) ? $ets_fields_placeholder_default[$index]:'';
                            $field_class->description[$language['id_lang']] = isset($ets_fields_description[$index]) && $ets_fields_description[$index] ? $ets_fields_description[$index]: isset($ets_fields_description_default[$index]) ? $ets_fields_description_default[$index]:'';
                        }
                        if($field_class->id)
                            $field_class->update();
                        else
                            $field_class->add();
                    }
                }
            }
            if($success)
            {
                if(Tools::isSubmit('run_ajax'))
                {
                    die(
                        Tools::jsonEncode(
                            array(
                                'error'=>false,
                                'success'=> $success,
                                'id_form' => $object->id,
                                'link_form' => $this->getFormLink($object->id),
                                'form_value' => array(
                                    'id_form' => $object->id,
                                    'title' => $object->title[$this->context->language->id],
                                    'description' => $object->description[$this->context->language->id],
                                    'active' => $object->active,
                                    'sort_order' => $object->sort_order,
                                    'link' => $this->getFormLink($object->id),
                                ),
                                'active_title' => $object->active ? $this->l('Click to disabled'): $this->l('Click to enabled'),
                                'fields_list' => $this->_displayListFields($object->id),                            )
                        )
                    );
                }
            }
        }
    }
    public function createFormDefault()
    {
        $languages = Language::getLanguages(false);
        $form = new LC_Ticket_form();
        $fields = $this->setConfigForm();
        foreach($fields as $filed)
        {
            $key = $filed['name'];
            if($key!='id_form')
            {
                if(isset($filed['lang']) && $filed['lang'])
                {
                    $value=array();
                    foreach($languages as $language)
                    {
                        $value[$language['id_lang']] = $key =='title' ? $this->l('Ticket from chat') :  ($key=='friendly_url' ? Tools::link_rewrite('Ticket from chat') : (isset($filed['default']) ? $filed['default'] : ''));
                    }
                    $form->{$key} = $value;
                }    
                else
                {
                    $form->{$key} = isset($filed['default']) ? $filed['default'] : '';
                }
            }  
        }
        $form->id_shop=$this->context->shop->id;
        $form->add();
        $field_class = new LC_Ticket_field();
        $field_class->type='text';
        $field_class->is_subject=1;
        $field_class->required=1;
        $field_class->id_form=$form->id;
        $field_class->position=1;
        foreach($languages as $language)
        {
            $field_class->label[$language['id_lang']] = $this->l('Subject');
        }
        $field_class->add();
        $field_class2 = new LC_Ticket_field();
        $field_class2->type='text_editor';
        $field_class2->id_form=$form->id;
        $field_class2->position=2;
        foreach($languages as $language)
        {
            $field_class2->label[$language['id_lang']] = $this->l('Description');
        }
        $field_class2->add();
        $field_class3 = new LC_Ticket_field();
        $field_class3->type='email';
        $field_class3->id_form=$form->id;
        $field_class3->position=4;
        $field_class3->is_contact_mail=1;
        foreach($languages as $language)
        {
            $field_class3->label[$language['id_lang']] = $this->l('Email');
        }
        $field_class3->add();
        $field_class4 = new LC_Ticket_field();
        $field_class4->type='text';
        $field_class4->id_form= $form->id;
        $field_class4->position=3;
        $field_class4->is_contact_name=1;
        foreach($languages as $language)
        {
            $field_class4->label[$language['id_lang']] = $this->l('Name');
        }
        $field_class4->add();
        $form = new LC_Ticket_form();
        $fields = $this->setConfigForm();
        foreach($fields as $filed)
        {
            $key = $filed['name'];
            if($key!='id_form')
            {
                if(isset($filed['lang']) && $filed['lang'])
                {
                    $value=array();
                    foreach($languages as $language)
                    {
                        if($key=='description')
                            $value[$language['id_lang']] = $this->l('Form for technical support submit');
                        else
                            $value[$language['id_lang']] = $key =='title' ? $this->l('Technical support') :  ($key=='friendly_url' ? Tools::link_rewrite('Technical support') : (isset($filed['default']) ? $filed['default'] : ''));
                    }
                    $form->{$key} = $value;
                }    
                else
                {
                    $form->{$key} = isset($filed['default']) ? $filed['default'] : '';
                }
            }  
        }
        $form->id_shop=$this->context->shop->id;
        $form->add();
        $field_class = new LC_Ticket_field();
        $field_class->type='text';
        $field_class->is_contact_name=1;
        $field_class->required=1;
        $field_class->id_form=$form->id;
        $field_class->position=1;
        foreach($languages as $language)
        {
            $field_class->label[$language['id_lang']] = $this->l('Name');
        }
        $field_class->add();
        $field_class2 = new LC_Ticket_field();
        $field_class2->type='email';
        $field_class2->is_contact_mail=1;
        $field_class2->required=1;
        $field_class2->id_form=$form->id;
        $field_class2->position=2;
        foreach($languages as $language)
        {
            $field_class2->label[$language['id_lang']] = $this->l('Email');
        }
        $field_class2->add();
        $field_class3 = new LC_Ticket_field();
        $field_class3->type='text';
        $field_class3->is_subject=1;
        $field_class3->required=1;
        $field_class3->position=3;
        $field_class3->id_form=$form->id;
        foreach($languages as $language)
        {
            $field_class3->label[$language['id_lang']] = $this->l('Subject');
        }
        $field_class3->add();
        $field_class4 = new LC_Ticket_field();
        $field_class4->type='phone_number';
        $field_class4->required=0;
        $field_class4->is_customer_phone_number=1;
        $field_class4->id_form=$form->id;
        $field_class4->position=4;
        foreach($languages as $language)
        {
            $field_class4->label[$language['id_lang']] = $this->l('Phone');
        }
        $field_class4->add();
        $field_class5 = new LC_Ticket_field();
        $field_class5->type='file';
        $field_class5->required=0;
        $field_class5->id_form=$form->id;
        $field_class5->position=6;
        foreach($languages as $language)
        {
            $field_class5->label[$language['id_lang']] = $this->l('File');
        }
        $field_class5->add();
        $field_class6 = new LC_Ticket_field();
        $field_class6->type='text_editor';
        $field_class6->required=1;
        $field_class6->id_form=$form->id;
        $field_class6->position=5;
        foreach($languages as $language)
        {
            $field_class6->label[$language['id_lang']] = $this->l('Message');
            $field_class6->placeholder[$language['id_lang']]= $this->l('Can we help you?');
        }
        $field_class6->add();
    }
    public function GetFormField($id_field=0,$position=1)
    {
        $this->context->smarty->assign(
            array(
                'languages' => Language::getLanguages(false),
                'fields' => $this->setConfigField(),
                'fields_value' => $this->getFieldsFormTicketFieldValues($id_field),
                'id_field' => $id_field,
                'field_class'=> new LC_Ticket_field($id_field,$this->context->language->id),
                'position' => $position,
                'defaultFormLanguage' => Configuration::get('PS_LANG_DEFAULT'),
            )
        );
        return $this->display(__FILE__,'form_field.tpl');
    }
    public function getFieldsFormTicketFieldValues($id_field)
    {
        $values= array();
        $languages = Language::getLanguages(false);
        if($id_field)
        {
            $filed_class = new LC_Ticket_field($id_field);
            $values['id_field'] = $filed_class->id;
        }
        else
        {
             $filed_class = new LC_Ticket_field();
             $values['id_field']='';
        }
        $defin = LC_Ticket_field::$definition;
        $fields = $defin['fields'];
        foreach($fields as $key => $filed)
        {
            if(isset($filed['lang']) && $filed['lang'])
            {
                foreach($languages as $language)
                {
                    $values[$key][$language['id_lang']] = $filed_class->id ? $filed_class->{$key}[$language['id_lang']]:(isset($filed['default']) ? $filed['default'] : '');
                }
            }    
            else
                $values[$key] = $filed_class->id ? $filed_class->{$key} : (isset($filed['default']) ? $filed['default'] : '');
        }
        return $values;
    }
    public function _checkEixtForm($id_form)
    {
        return Db::getInstance()->getValue('SELECT COUNT(id_form) FROM '._DB_PREFIX_.'ets_livechat_ticket_form WHERE id_shop='.(int)$this->context->shop->id.' AND active=1 AND deleted=0 AND id_form='.(int)$id_form);
    }
    public function renderHtmlForm($id_form,$id_conversation=0,$admin=false)
    {
        $fields= Db::getInstance()->executeS('
        SELECT * FROM '._DB_PREFIX_.'ets_livechat_ticket_form_field f
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_field_lang fl ON (f.id_field=fl.id_field)
        WHERE f.deleted=0 AND f.id_form="'.(int)$id_form.'" AND fl.id_lang="'.(int)$this->context->language->id.'" GROUP BY f.id_field ORDER BY f.position ASC');
        $is_customer_email=false;
        $is_customer_name= false;
        $is_customer_phone_number=false;
        $form = new LC_Ticket_form($id_form,$this->context->language->id);
        $search_customer=false;
        $post_fields = Tools::getValue('fields');
        $post_fields['search_customer'] = Tools::getValue('search_customer_ticket');
        $post_fields['id_customer_ticket'] = Tools::getValue('id_customer_ticket');
        if($fields)
        {
            foreach($fields as &$field)
            {
                $field['options'] =$field['options'] ? explode("\n",$field['options']) :array();
                if(isset($this->context->customer) && $this->context->customer->logged)
                {
                    if($field['is_contact_mail'] && !$is_customer_email)
                    {
                        $field['value']= $this->context->customer->email;
                        $is_customer_email=true;
                    }
                    if($field['is_contact_name'] && !$is_customer_name)
                    {
                        $field['value']=$this->context->customer->firstname.' '.$this->context->customer->lastname;
                        $is_customer_name=true;
                    }
                    if($field['is_customer_phone_number'] && !$is_customer_phone_number)
                    {
                       $addresses = $this->context->customer->getAddresses($this->context->language->id);
                       if($addresses)
                       {
                            $field['value']= $addresses[0]['phone'] ? $addresses[0]['phone'] : $addresses[0]['phone_mobile'];
                            $is_customer_phone_number=true;
                       } 
                    }
                }
                elseif($id_conversation)
                {
                    $conversation = new LC_Conversation($id_conversation);
                    if($conversation->id_customer)
                    {
                        $customer= new Customer($conversation->id_customer);
                        $customer_email = $customer->email;
                        $customer_name = $customer->lastname.' '.$customer->lastname;
                    }
                    else
                    {
                        $customer_email = $conversation->customer_email;
                        $customer_name = $conversation->customer_name;
                    }
                    if($field['is_contact_mail'] && !$is_customer_email)
                    {
                        $field['value']= $customer_email;
                        $is_customer_email=true;
                    }
                    if($field['is_contact_name'] && !$is_customer_name)
                    {
                        $field['value']=$customer_name;
                        $is_customer_name=true;
                    }
                }
                elseif($admin)
                {
                    if(!$form->allow_user_submit)
                    {
                        if($field['is_contact_mail'] && !$is_customer_email)
                        {
                            $field['readonly']= true;
                            $is_customer_email=true;
                        }
                        if($field['is_contact_name'] && !$is_customer_name)
                        {
                            $field['readonly']= true;
                            $is_customer_name=true;
                        }
                        if($field['is_customer_phone_number'] && !$is_customer_phone_number)
                        {
                            $field['readonly']= true;
                            $is_customer_phone_number=true;
                        }
                        $search_customer=true;  
                        
                    }
                    elseif(isset($post_fields['id_customer_ticket']) && $id_customer= $post_fields['id_customer_ticket'])
                    {
                        $customer= new Customer($id_customer);
                        if($field['is_contact_mail'] && !$is_customer_email && $customer->email)
                        {
                            $field['readonly']= true;
                            $is_customer_email=true;
                        }
                        if($field['is_contact_name'] && !$is_customer_name && $customer->firstname)
                        {
                            $field['readonly']= true;
                            $is_customer_name=true;
                        }
                        if($field['is_customer_phone_number'] && !$is_customer_phone_number)
                        {
                           $addresses = $customer->getAddresses($this->context->language->id);
                           if($addresses)
                           {
                                $field['readonly']= true;
                                $is_customer_phone_number=true;
                           } 
                        }
                    }
                    
                    if($field['is_contact_mail'] || $field['is_contact_name'] || $field['is_customer_phone_number'])
                    {
                         $search_customer=true; 
                    }  
                    
                }
            }  
        }
        if($form->allow_captcha && (!$this->context->customer->logged || !$form->customer_no_captcha))
        {
            $captchaUrl = $this->context->link->getModuleLink('ets_livechat','captcha',array('rand'=>time(),'id_form'=>$form->id));
            $captcha = $this->context->link->getModuleLink('ets_livechat','captcha',array('init'=>'ok','id_form'=>$form->id));
        }
        if($admin)
        {
            $sql = 'SELECT * FROM '._DB_PREFIX_.'ets_livechat_ticket_form f
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_lang fl ON (f.id_form =fl.id_form)
            WHERE f.id_shop= "'.(int)$this->context->shop->id.'" AND fl.id_lang = "'.(int)$this->context->language->id.'" AND active=1 AND f.id_form!=1 AND f.id_form!="'.(int)$id_form.'" AND deleted=0 ORDER BY f.sort_order ASC';
            $forms= Db::getInstance()->executeS($sql);
            if($forms)
            {
                foreach($forms as &$item)
                {
                    $item['link']= $this->context->link->getAdminLink('AdminLiveChatTickets').'&addticket&id_form='.$item['id_form'];
                }
            }
        }
        $employees=  Db::getInstance()->executeS(
        'SELECT e.*,d.id_departments,s.name,s.avata,IFNULL(s.status,1) as status FROM '._DB_PREFIX_.'employee e 
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (de.id_employee=e.id_employee)
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments d ON (d.id_departments = de.id_departments)
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_staff s ON (s.id_employee=e.id_employee) 
        WHERE e.active=1 GROUP BY e.id_employee');
        if($employees)
        {
            foreach($employees as &$employee)
            {
                $employe_departments= Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_departments_employee WHERE id_employee='.(int)$employee['id_employee']);
                $employee['departments']=$employe_departments;
            }
        }
        $this->context->smarty->assign(
            array(
                'fields'=>$fields,
                'post_fields' =>$post_fields,
                'id_form' => $id_form,
                'form' => $form,
                'admin'=>$admin,
                'forms' => $admin && $forms ? $forms : false,
                'new_ticket_link' => $admin && Count($forms)==1 ? $forms[0]['link']:false,
                'search_customer'=>$search_customer,
                'backend' => $id_conversation ? true : false,
                'conversation' => $id_conversation ? new LC_Conversation($id_conversation) :false,
                'captchaUrl' => isset($captchaUrl) ? $captchaUrl :false,
                'captcha' => isset($captcha) ? $captcha :false,
                'departments'=>$form->getDepartments(),
                'employees'=>$employees,
                'logged' => isset($this->context->customer) && $this->context->customer->logged,
                'link_action' => $this->context->link->getModuleLink($this->name,'form',array('id_form'=>$id_form))
            )  
        );
        return $this->display(__FILE__,'form_html.tpl');
    }
    public function displayMessageTicket($id_message,$admin=false)
    {
        $fields = Db::getInstance()->executeS('
        SELECT mf.value,mf.id_download,fl.label,f.type FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message_field mf
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_field f ON (f.id_field=mf.id_field)
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_field_lang fl ON (mf.id_field=fl.id_field)
        WHERE mf.id_message='.(int)$id_message.' AND fl.id_lang='.(int)$this->context->language->id);
        if($fields)
        {
            foreach($fields as &$field)
            {
                if($field['id_download'] >0)
                {
                    $download= new LC_Download($field['id_download']);
                    $field['file_size'] =$download->file_size;
                }
                if((isset($this->context->employee) && $this->context->employee->id) || $admin)
                    $field['link_download']= $this->getBaseLink().'/modules/ets_livechat/download.php?downloadfile='.md5(_COOKIE_KEY_.$field['id_download']);
                else
                    $field['link_download'] = $this->context->link->getModuleLink($this->name,'download',array('downloadfile'=>md5(_COOKIE_KEY_.$field['id_download'])));
            }
        }
        $this->context->smarty->assign(
            array(
                'fields' => $fields,
                'link_basic' => $this->getBaseLink(),
            )
        );
        return $this->display(__FILE__,'message_field.tpl');
    }
    public function getSubjectMessageTicket($id_message)
    {
        $fields = Db::getInstance()->executeS('
        SELECT mf.value,fl.label,f.* FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message_field mf
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_field f ON (f.id_field=mf.id_field)
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_field_lang fl ON (mf.id_field=fl.id_field)
        WHERE mf.id_message='.(int)$id_message.' AND fl.id_lang='.(int)$this->context->language->id);
        if($fields)
        {
            foreach($fields as $field)
            {
                if($field['is_subject'] && $field['value'])
                    return $field['value'];
            }
        }
        return '';
    }
    public function getBaseLink()
    {
        $link = (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$this->context->shop->domain.$this->context->shop->getBaseURI();
        return trim($link,'/');
    }
    public function displayMenuTop()
    {
        $this->context->smarty->assign(
            array(
                'link'=> $this->context->link,
                'controller' => Tools::getValue('controller'),
                'id_profile' => $this->context->employee->id_profile,
            )
        );
        return $this->display(__FILE__,'menu_top.tpl');
    }
    public function getMessagesTicket($ticket,$orderBy=false,$limit=false)
    {
        if(!is_array($ticket))
            $ticket = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message WHERE id_message='.(int)$ticket);
        $messages = Db::getInstance()->executeS(
        'SELECT * FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message_note
        WHERE id_message='.(int)$ticket['id_message'].' ORDER BY date_add '.($orderBy ? pSQL($orderBy):'ASC').($limit ? ' LIMIT 0,'.(int)$limit:''));
        if($ticket['id_customer'])
            $customer_info = Db::getInstance()->getRow('
            SELECT c.*, ci.avata FROM '._DB_PREFIX_.'customer c 
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_customer_info ci ON (c.id_customer =ci.id_customer)
            WHERE c.id_customer='.(int)$ticket['id_customer']);
        if($messages)
        {
            foreach($messages as $index=> &$message)
            {
                //if(date('Y-m-d')==date('Y-m-d',strtotime($message['date_add'])))
//                {
//                    $message['date_add'] =date('h:i A',strtotime($message['date_add']));
//                }
//                else
//                {
//                    if(date('Y')==date('Y',strtotime($message['date_add'])))
//                    {
//                        $message['date_add'] =date('d-m',strtotime($message['date_add'])).'<br/>'.date('h:i A',strtotime($message['date_add']));
//                    }
//                    else
//                        $message['date_add'] =date('d-m-Y',strtotime($message['date_add'])).'<br/>'.date('h:i A',strtotime($message['date_add']));
//                }
                if($message['id_employee'])
                {
                    $employee= Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'employee e
                    LEFT JOIN '._DB_PREFIX_.'ets_livechat_staff s ON (e.id_employee=s.id_employee)
                    WHERE e.id_employee="'.(int)$message['id_employee'].'"
                    ');
                    if(Configuration::get('ETS_LC_DISPLAY_COMPANY_INFO')=='general')
                    {
                        $message['employee_name'] = Configuration::get('ETS_LC_COMPANY_NAME');
                        $message['employee_name_hide'] = $this->getNameHide(Configuration::get('ETS_LC_COMPANY_NAME'));
                    }
                    else
                    {
                        $message['employee_name'] = $employee['name'] ? $employee['name']: $employee['firstname'].' '.$employee['lastname'];
                        $message['employee_name_hide'] = $this->getNameHide($employee['name'] ? $employee['name'] : $employee['firstname'].' '.$employee['lastname']);
                    }    
                }
                else
                {
                    if(isset($customer_info))
                    {
                        $message['customer_name']=$customer_info['firstname'].' '.$customer_info['lastname']; 
                        $message['customer_name_hide']=$this->getNameHide($customer_info['firstname'].' '.$customer_info['lastname']); 
                    }
                    else
                    {
                        $message['customer_name']= 'Ticket ID #'.$ticket['id_message'];
                        $message['customer_name_hide']= 'Ticket ID #'.$ticket['id_message'];
                    }
                }
                if(Configuration::get('ETS_LC_DISPLAY_AVATA'))
                {
                    if($orderBy=='DESC')
                        $index_next= $index+1;
                    else
                        $index_next=$index-1;
                    if(!isset($messages[$index_next])||(isset($messages[$index_next])&& $messages[$index_next]['id_employee']) && !$messages[$index]['id_employee'])
                    {
                        if(isset($customer_info) && $customer_info['avata'])
                            $message['customer_avata'] = $this->_path.'/views/img/config/'.$customer_info['avata'];
                        elseif(Configuration::get('ETS_LC_CUSTOMER_AVATA'))
                            $message['customer_avata']=$this->_path.'views/img/config/'.Configuration::get('ETS_LC_CUSTOMER_AVATA');
                        else
                            $message['customer_avata']=$this->_path.'views/img/config/customeravata.jpg';
                    }
                    else
                        $message['customer_avata']='';
                    if(!isset($messages[$index_next])||(isset($messages[$index_next])&& !$messages[$index_next]['id_employee']) && $messages[$index]['id_employee'])
                    {
                        if(Configuration::get('ETS_LC_DISPLAY_COMPANY_INFO')=='general')
                        {
                            if(Configuration::get('ETS_LC_COMPANY_LOGO'))
                                $message['employee_avata']=$this->_path.'views/img/config/'.Configuration::get('ETS_LC_COMPANY_LOGO');
                            else
                                $message['employee_avata']=$this->_path.'views/img/config/adminavatar.jpg';
                        }
                        else
                            $message['employee_avata'] = ($avata=Db::getInstance()->getValue('SELECT avata FROM '._DB_PREFIX_.'ets_livechat_staff WHERE id_employee='.(int)$message['id_employee'])) ? $this->_path.'views/img/config/'.$avata : $this->_path.'views/img/config/adminavatar.jpg';
                    }
                    else
                        $message['employee_avata']='';
                }
                else
                {
                    $message['customer_avata']='';
                    $message['employee_avata']='';
                }
                if($message['file_name'] && $message['id_download'])
                {
                    if($message['id_download']!=-1 && $attachment= Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_download WHERE id_download="'.(int)$message['id_download'].'"'))
                    {
                        if(isset($this->context->employee) && $this->context->employee->id)
                            $link_download= $this->getBaseLink().'/modules/ets_livechat/download.php?downloadfile='.md5(_COOKIE_KEY_.$message['id_download']);
                        else
                            $link_download = $this->context->link->getModuleLink($this->name,'download',array('downloadfile'=>md5(_COOKIE_KEY_.$message['id_download'])));
                        $message['note'] .= ($message['note'] ? '<br />':'').'<span class="message_file"><a href="'.$link_download.'">'.$message['file_name'].'</a>'.($attachment['file_size'] ? '<span class="file_size"> ('.$attachment['file_size'].' MB)</span>':'').'</span>';
                    }
                    else
                        $message['note'] .= ($message['note'] ? '<br />':'').'<b>'.$message['file_name'].' ('.$this->l('File was sent to mail').')</b>';
                        
                }
                if( $message['id_employee'] && $employee['signature'])
                {
                    $message['note'] .=  '<br /> ----- <br /><span class="employee_signature">'.$employee['signature'].'</span>';
                }
            }
        }
        return $messages;
    }
    public function getListTickets()
    {
        $filter ='';
        $post_value=array();
        if(Tools::getValue('id_ticket'))
        {
            $filter .=' AND fm.id_message='.(int)Tools::getValue('id_ticket');
            $post_value['id_ticket']=(int)Tools::getValue('id_ticket');
        }
        if(Tools::getValue('form_title'))
        {
            $filter .=' AND fl.title LIKE "%'.pSQL(Tools::getValue('form_title')).'%"';
            $post_value['form_title'] = Tools::getValue('form_title');
        }
        if(Tools::getValue('priority'))
        {
            $filter .= ' AND fm.priority="'.(int)Tools::getValue('priority').'"';
            $post_value['priority'] = (int)Tools::getValue('priority');
        }
        if(Tools::getValue('status'))
        {
            $filter .= ' AND fm.status="'.pSQL(Tools::getValue('status')).'"';
            $post_value['status'] = Tools::getValue('status');
        }
        if(Tools::getValue('date_add_from'))
        {
            $filter .=' AND fm.date_admin_update >= "'.pSQL(Tools::getValue('date_add_from')).' 00:00:00"';
            $post_value['date_add_from'] = Tools::getValue('date_add_from');
        }
        if(Tools::getValue('date_add_to'))
        {
            $filter .=' AND fm.date_admin_update <= "'.pSQL(Tools::getValue('date_add_to')).' 00:00:00"';
            $post_value['date_add_to'] = Tools::getValue('date_add_to');
        }
        if(Tools::getValue('subject'))
        {
            $filter .=' AND fm.subject LIKE "'.pSQL(Tools::getValue('subject')).'%"';
            $post_value['subject'] = Tools::getValue('subject');
        }
        $sort =Tools::getValue('sort','date_admin_update');
        $sort_type = Tools::getValue('sort_type','desc');
        $page = (int)Tools::getValue('page') && (int)Tools::getValue('page') > 0 ? (int)Tools::getValue('page') : 1;
        $totalRecords = (int)$this->rederListTicket(true,$filter);
        $paggination = new LC_paggination_class();            
        $paggination->total = $totalRecords;
        
        $paggination->url = $this->context->link->getModuleLink($this->name,'ticket',array_merge(array('page'=>'_page_'),$post_value));
        $paggination->limit =  20;
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');
        $tickets= $this->rederListTicket(false,$filter,$sort,$sort_type,$paggination->limit,$start);
        if($tickets)
        {
            foreach($tickets as &$ticket)
            {
                if(Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message_note WHERE id_message='.(int)$ticket['id_message'].' AND id_employee!=0 AND readed=0'))
                    $ticket['no_readed'] = 1;
                else
                    $ticket['no_readed']=0;    
            }
        }
        $sql = 'SELECT * FROM '._DB_PREFIX_.'ets_livechat_ticket_form f
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_lang fl ON (f.id_form =fl.id_form)
        WHERE f.id_shop= "'.(int)$this->context->shop->id.'" AND fl.id_lang = "'.(int)$this->context->language->id.'" AND active=1 AND f.id_form!=1 AND deleted=0 '.(!$this->context->customer->logged ? ' AND allow_user_submit=1':'').' ORDER BY f.sort_order ASC';
        $forms= Db::getInstance()->executeS($sql);
        if($forms)
        {
            foreach($forms as &$form)
            {
                $form['link']= $this->getFormLink($form['id_form']);
            }
        }
        $this->context->smarty->assign(
            array(
                'tickets' => $tickets,
                'post_value' => $post_value,
                'link'=> $this->context->link,
                'sort'=>$sort,
                'sort_type' => $sort_type,
                'new_ticket_link' => count($forms)==1 ? $forms[0]['link'] :false,
                'forms'=>$forms,
                'pagination_text' => $paggination->render(),
                'totalRecords' => $totalRecords,
            )
        );
        return $this->display(__FILE__,'list_ticket.tpl');
    }
    public function rederListTicket($count=false,$filter=false,$sort=false,$sort_type=false,$limit=false,$start=0)
    {
        $sql= 'SELECT fm.*,fl.title,c.firstname,c.lastname,c.email,f.default_priority FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message fm
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form f ON (fm.id_form=f.id_form)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_lang fl ON (f.id_form=fl.id_form AND fl.id_lang="'.(int)$this->context->language->id.'")
            LEFT JOIN '._DB_PREFIX_.'customer c ON (fm.id_customer=c.id_customer)
            WHERE fm.id_customer="'.(int)$this->context->customer->id.'"'.($filter ? $filter :'').'
            GROUP BY fm.id_message'
            .($sort ? ' ORDER BY '.pSQL($sort):'').' '.($sort_type && $sort ? pSQL($sort_type) :'')
            .($limit ? ' LIMIT '.(int)$start.','.(int)$limit :'');
        $tickets= Db::getInstance()->executeS($sql);
        if($count)
            return Count($tickets);
        if($tickets)
        {
            foreach($tickets as &$ticket)
            {
                //$ticket['subject'] = $this->getSubjectMessageTicket($ticket['id_message']);
                $ticket['link_view'] = $this->context->link->getModuleLink($this->name,'ticket',array('viewticket'=>1,'id_ticket'=>$ticket['id_message']));
            }
        }
        return $tickets;
    }
    public function displayDetailTicket($id_ticket)
    {
        if($ticket= $this->checkAccesTicketFrontEnd($id_ticket))
        {
            $messages = $this->getMessagesTicket($ticket);
            if($ticket['id_form'])
            {
                $form_class= new LC_Ticket_form($ticket['id_form']);
            }
            else
                Tools::redirectLink($this->context->link->getModuleLink($this->name,'ticket'));
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_ticket_form_message_note SET readed=1 WHERE id_message='.(int)$id_ticket.' AND id_employee!=0');    
            $fields = Db::getInstance()->executeS('
            SELECT mf.value,fl.label,f.type,mf.id_download FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message_field mf
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_field f ON (f.id_field=mf.id_field)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_field_lang fl ON (mf.id_field=fl.id_field)
            WHERE mf.id_message='.(int)$id_ticket.' AND fl.id_lang='.(int)$this->context->language->id);
            if($fields)
            {
                foreach($fields as &$field)
                {
                    if($field['id_download'] >0)
                    {
                        $download = new LC_Download($field['id_download']);
                        $field['file_size'] = $download->file_size;
                    }
                    $field['link_download'] = $this->context->link->getModuleLink($this->name,'download',array('downloadfile'=>md5(_COOKIE_KEY_.$field['id_download'])));
                }
            }
            $this->context->smarty->assign(
                 array(
                    'ticket' => $ticket,
                    'messages' => $messages,
                    'fields' => $fields,
                    'form_class' => $form_class,
                    'ETS_LC_AVATAR_IMAGE_TYPE'=> Configuration::get('ETS_LC_AVATAR_IMAGE_TYPE'),
                    'link_basic' => $this->getBaseLink(),
                 ) 
            );
            return $this->display(__FILE__,'detail_ticket.tpl');
        }
        else
            Tools::redirectLink($this->context->link->getModuleLink($this->name,'ticket'));
    }
    public function checkAccesTicketFrontEnd($id_ticket)
    {
        $sql= 'SELECT fm.*,fl.title,c.firstname,c.lastname,c.email,f.default_priority,f.id_form FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message fm
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form f ON (fm.id_form=f.id_form)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_lang fl ON (f.id_form=fl.id_form AND fl.id_lang="'.(int)$this->context->language->id.'")
            LEFT JOIN '._DB_PREFIX_.'customer c ON (fm.id_customer=c.id_customer)
            WHERE fm.id_message="'.(int)$id_ticket.'" AND fm.id_customer="'.(int)$this->context->customer->id.'"
            GROUP BY fm.id_message'; 
        return Db::getInstance()->getRow($sql);
    }
    public function checkAccesTicket($id_ticket)
    {
         if($this->context->employee->id_profile==1)
            $sql= 'SELECT fm.*,fl.title,c.firstname,c.lastname,c.email,f.default_priority FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message fm
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form f ON (fm.id_form=f.id_form)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_lang fl ON (f.id_form=fl.id_form AND fl.id_lang="'.(int)$this->context->language->id.'")
            LEFT JOIN '._DB_PREFIX_.'customer c ON (fm.id_customer=c.id_customer)
            WHERE fm.id_message="'.(int)$id_ticket.'"'.(!$this->module->all_shop ? ' AND fm.id_shop="'.(int)$this->context->shop->id.'"':'').'
            GROUP BY fm.id_message'; 
         else
            $sql = 'SELECT fm.*,fl.title,c.firstname,c.lastname,c.email,f.default_priority FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message fm
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form f ON (fm.id_form=f.id_form)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_lang fl ON (f.id_form=fl.id_form AND fl.id_lang="'.(int)$this->context->language->id.'")
            LEFT JOIN '._DB_PREFIX_.'customer c ON (fm.id_customer=c.id_customer)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments d ON (fm.id_departments = d.id_departments)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (d.id_departments=de.id_departments)
            WHERE fm.id_message="'.(int)$id_ticket.'"'.(!$this->module->all_shop ? ' AND fm.id_shop="'.(int)$this->context->shop->id.'"':'').' AND (fm.id_departments<=0 OR de.id_employee="'.(int)$this->context->employee->id.'" OR d.all_employees=1) AND (fm.id_employee<=0 OR fm.id_employee="'.(int)$this->context->employee->id.'") GROUP BY fm.id_message';
         $ticket = Db::getInstance()->getRow($sql);
         if($ticket['id_departments']>0)
         {
                $department  = new LC_Departments($ticket['id_departments']);
                $ticket['dertpartment_name'] = $department->name;
         }
         else
            $ticket['dertpartment_name'] = $this->l('All departments');
         if($ticket['id_employee']>0)
         {
            $employee = new Employee($ticket['id_employee']);
            $ticket['employee_name'] = $employee->firstname.' '.$employee->lastname;
         }
         return $ticket;
    }
    public function checkEnableLivechat()
    {
        if((int)Configuration::get('ETS_DISPLAY_DASHBOARD_ONLY') && Tools::getValue('controller')!='AdminDashboard')
            return true;
        else
        {
            if($this->checkVesionModule())
            {
                if(Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_staff WHERE id_employee="'.(int)$this->context->employee->id.'" AND status=0') && $this->context->employee->id_profile!=1)
                    return false;
            }
        }
        return true;
    }
    public function setMeta()
    {
        if(Tools::getValue('controller')=='history')
        {
            $metas =array(
                'title' =>Tools::isSubmit('viewchat') && Tools::getValue('id') ? $this->l('Conversation #').Tools::getValue('id') : $this->l('Chat history'),
                'meta_title' =>Tools::isSubmit('viewchat') && Tools::getValue('id') ? $this->l('Conversation #').Tools::getValue('id') : $this->l('Chat history'),
                'description' => '',
                'keywords' => '',
                'robots' => 'index',
            );
        }
        if(Tools::getValue('controller')=='info')
        {
            $metas =array(
                'title' =>$this->l('Chat info'),
                'meta_title' =>$this->l('Chat info'),
                'description' => '',
                'keywords' => '',
                'robots' => 'index',
            );
        }
        if(Tools::getValue('controller')=='ticket')
        {
            $metas =array(
                'title' => Tools::isSubmit('viewticket') && Tools::getValue('id_ticket') ? $this->l('Ticket  #').Tools::getValue('id_ticket'): $this->l('Support tickets'),
                'meta_title' => Tools::isSubmit('viewticket') && Tools::getValue('id_ticket') ? $this->l('Ticket  #').Tools::getValue('id_ticket'): $this->l('Support tickets'),
                'description' => '',
                'keywords' => '',
                'robots' => 'index',
            );
        }
        if($this->is17)
        {
            $body_classes = array(
                'lang-'.$this->context->language->iso_code => true,
                'lang-rtl' => (bool) $this->context->language->is_rtl,
                'country-'.$this->context->country->iso_code => true,                                   
            );
            $page = array(
                'title' => '',
                'canonical' => '',
                'meta' => $metas,
                'page_name' => 'lc_form_page',
                'body_classes' => $body_classes,
                'admin_notifications' => array(),
            ); 
            $this->context->smarty->assign(array('page' => $page)); 
        }    
        else
        {
            $this->context->smarty->assign($metas);
        }
    }
    public function getProductCurrent($conversation)
    {
        if(Tools::getValue('product_page_product_id') && Configuration::get('ETS_LC_SEND_PRODUCT_LINK') && (!$conversation || $conversation->end_chat))
        {
            return $this->getProductInfo(Tools::getValue('product_page_product_id'));
        }
        return false;
    }
    public function getProductInfo($id_product)
    {
        $id_customer = (isset($this->context->customer->id) && $this->context->customer->id) ? (int)($this->context->customer->id) : 0;
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int)$id_customer);
        }
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        }
        $group= new Group($id_group);
        if($group->price_display_method)
            $tax=false;
        else
            $tax=true;
        $product= new Product($id_product,true,$this->context->shop->id); 
        if(!Validate::isLoadedObject($product))
            return false;
        $id_product_attribute = $product->getDefaultIdProductAttribute();
        $pinfo = array();    
        $pinfo['name'] = $product->name;
        $price = $product->getPrice($tax,null);
        $oldPrice = $product->getPriceWithoutReduct(!$tax,false);
        $discount = $oldPrice - $price;
        $pinfo['price'] = Tools::displayPrice($price);       
        $pinfo['old_price'] = Tools::displayPrice($oldPrice); 
        $pinfo['discount_percent'] = (($oldPrice - $price) >0 ?  round(($oldPrice - $price) / $oldPrice * 100):0);
        $pinfo['discount_amount'] = Tools::displayPrice($discount);
        $pinfo['id_product'] =$product->id;
        $images = $product->getImages((int)$this->context->cookie->id_lang);
        $link = $this->context->link;
        if(isset($images[0]))
    	    $id_image = Configuration::get('PS_LEGACY_IMAGES') ? ($product->id.'-'.$images[0]['id_image']) : $images[0]['id_image'];
    	else
            $id_image = $this->context->language->iso_code.'-default';			
        $pinfo['img_url'] =  $link->getImageLink($product->link_rewrite, $id_image, $this->is17 ? ImageType::getFormattedName('home') : ImageType::getFormatedName('home'));
        $pinfo['link'] = $link->getProductLink($product,null,null,null,null,null,$id_product_attribute);
        return $pinfo;
    }
    public function getProductHtml($id_product)
    {
        if($product=$this->getProductInfo($id_product))
        {
            $this->context->smarty->assign(
                array(
                    'product' => $product,
                )
            );
            return $this->display(__FILE__,'product.tpl');
        }
    }
    public function _submitSendMail($id_conversation)
    {
        $conversation = new LC_Conversation($id_conversation);
        $errors= array();
        if(!Tools::getValue('title_mail'))
        {
            $errors[]= $this->l('Title is required');
        }
        elseif(Tools::getValue('title_mail') && !Validate::isCleanHtml(Tools::getValue('title_mail')))
            $errors[]= $this->l('Title is invalid');
        if(!Tools::getValue('content_mail'))
            $errors[] = $this->l('Message is required');
        elseif(Tools::getValue('content_mail') && !Validate::isCleanHtml(Tools::getValue('content_mail')))
        {
            $errors[]= $this->l('Message is invalid');
        }
        if($conversation->id_customer)
        {
            $customer= new Customer($conversation->id_customer);
            $email= $customer->email;
            $name= $customer->firstname.' '.$customer->lastname;
        }
        else
        {
            $email = $conversation->customer_email;
            $name= $conversation->customer_name;
        }
        if(!$email || !Validate::isEmail($email))
            $errors[]= $this->l('Customer email is invalid');
        if($errors)
        {
            die(
                Tools::jsonEncode(
                    array(
                        'error'=> $this->displayError($errors),
                    )
                )
            );
        }else
        {
            $template_vars=array(
                '{content_mail}'=> Tools::getValue('content_mail'),
            );
            if(Mail::Send(
				Context::getContext()->language->id,
				'livechat_message',
				Tools::getValue('title_mail'),
				$template_vars,
				$email,
				$name,
				null,
				null,
				null,
				null,
				dirname(__FILE__).'/mails/',
				null,
				Context::getContext()->shop->id
			)){
                die(Tools::jsonEncode(
                    array(
                        'error'=>false,
                        'success' => $this->l('Email sent successfully'),
                    )
                ));
            }
            else
            {
                die(
                    Tools::jsonEncode(
                        array(
                            'error' => $this->displayError($this->l('Email sent error')),
                        )
                    )
                );
            }
        }
        
    }
    public function getCountMessage($filter)
    {
        $sql ='SELECT COUNT(DISTINCT m.id_message) FROM '._DB_PREFIX_.'ets_livechat_message m
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_conversation c on (m.id_conversation= c.id_conversation)
            WHERE 1 '.(!$this->all_shop ? ' AND c.id_shop="'.(int)$this->context->shop->id.'"':'').($filter ? $filter :'');
        //die($sql);
        return Db::getInstance()->getValue($sql);
    }
    public function getCountConversation($filter=false)
    {
        $sql = 'SELECT count(c.id_conversation) FROM '._DB_PREFIX_.'ets_livechat_conversation c WHERE 1'.(!$this->all_shop ? ' AND id_shop='.(int)$this->context->shop->id:'').($filter ? $filter: '');
        return Db::getInstance()->getValue($sql);
    }
    public function getAvatarCustomer($id_customer)
    {
        $customer_avatar = Db::getInstance()->getValue('SELECT avata FROM '._DB_PREFIX_.'ets_livechat_customer_info WHERE id_customer='.(int)$id_customer);
        if($id_customer && $customer_avatar)
            return  $this->_path.'/views/img/config/'.$customer_avatar;
        elseif(Configuration::get('ETS_LC_CUSTOMER_AVATA'))
            return  $this->_path.'views/img/config/'.Configuration::get('ETS_LC_CUSTOMER_AVATA');
        else
            return $this->_path.'views/img/config/customeravata.jpg';
    }
    public function getAvatarEmployee($id_employee)
    {
        if(Configuration::get('ETS_LC_COMPANY_LOGO'))
            $shop_logo= $this->_path.'views/img/config/'.Configuration::get('ETS_LC_COMPANY_LOGO');
        else
           $shop_logo=$this->_path.'views/img/config/adminavatar.jpg';
        if(Configuration::get('ETS_LC_DISPLAY_COMPANY_INFO')=='general' || $id_employee==-1)
        {
            return $shop_logo;
        }
        else
            return ($avata=Db::getInstance()->getValue('SELECT avata FROM '._DB_PREFIX_.'ets_livechat_staff WHERE id_employee='.(int)$id_employee)) ? $this->_path.'views/img/config/'.$avata : $shop_logo;
    }
    public function getEmailCustomer($ticket)
    {
        if(!is_array($ticket))
        {
            $sql= 'SELECT fm.*,fl.title,c.firstname,c.lastname,c.email,f.default_priority FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message fm
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form f ON (fm.id_form=f.id_form)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_lang fl ON (f.id_form=fl.id_form AND fl.id_lang="'.(int)$this->context->language->id.'")
            LEFT JOIN '._DB_PREFIX_.'customer c ON (fm.id_customer=c.id_customer)
            WHERE fm.id_message="'.(int)$ticket.'"
            GROUP BY fm.id_message'; 
            $ticket = Db::getInstance()->getRow($sql);
        }
        $form= new LC_Ticket_form($ticket['id_form']);
        if($ticket['email'])
        {
            return array(
                'email'=> $ticket['email'],
                'name'=> $ticket['firstname'].' '.$ticket['lastname'],
            );
        }
        if($form->send_mail_to_customer)
        {
                $is_contact_email=false;
                $is_contact_name=false;
                $fields = Db::getInstance()->executeS(
                'SELECT ff.*,fmf.value FROM '._DB_PREFIX_.'ets_livechat_ticket_form_field ff
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_message_field fmf ON (ff.id_field = fmf.id_field)
                WHERE fmf.id_message="'.(int)$ticket['id_message'].'" GROUP BY ff.id_field');
                if($fields)
                {
                    foreach($fields as $field)
                    {
                        if($field['value'] && $field['is_contact_mail'] && !$is_contact_email && $field['type']=='email')
                        {
                            $customer_email= $field['value'];
                            $is_contact_email=true;
                        }
                        if($field['value'] && $field['is_contact_name'] && !$is_contact_name && $field['type']=='text')
                        {
                            $customer_name= $field['value'];
                            $is_contact_name=true;
                        }
                    }
                }
                if($is_contact_email)
                {
                    return array(
                        'email'=> isset($customer_email) ? $customer_email : '',
                        'name'=> isset($customer_name) ? $customer_name : '',
                    );
                }
        }
        return false;
    }
    public function hookModuleRoutes($params) {
        //die('xyz');
        $routers = array(
            'livechatform' => array(
                'controller' => 'form',
                'rule' => 'support_ticket/{id_form}-{url_alias}',
                'keywords' => array(
                    'id_form' =>    array('regexp' => '[0-9]+', 'param' => 'id_form'),
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-]+','param' => 'url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name,
                ),
            ),    
        );
        return $routers;                
    }        
    public function getFormLink($id_form)
    {
        if(Configuration::get('PS_REWRITING_SETTINGS') && $this->checkVesionModule())
        {
            $form = new LC_Ticket_form($id_form,$this->context->language->id);
            if($form->friendly_url)
            {
                return $this->getBaseLink().'/support_ticket/'.$form->id.'-'.$form->friendly_url;
                            
            }                        
        }                      
        return $this->context->link->getModuleLink($this->name,'form',array('id_form'=>$id_form));
    }
    public function convertDate($date)
    {
        if(date('Y-m-d')==date('Y-m-d',strtotime($date)))
        {
            $date =date('h:i A',strtotime($date));
        }
        else
        {
           if(date('Y')==date('Y',strtotime($date)))
           {
                $date =date('d-m h:i A',strtotime($date));
           }
           else
                $date =date('d-m-Y h:i A',strtotime($date));
        }
        return $date;
    }
    public function displayRecentlyCustomer($count_login_customers,$login_customers)
    {
        $this->context->smarty->assign(
            array(
                'login_customers'=>$login_customers,
                'count_login_customers' => $count_login_customers,
            )
        );
        return $this->display(__FILE__,'recently_customers.tpl');
    }
    public function checkVesionModule()
    {
        $version = Db::getInstance()->getValue('SELECT version FROM '._DB_PREFIX_.'module WHERE name ="'.pSQL($this->name).'"');
        if($version && version_compare($version, '2.0', '>='))
        {
           return  true;
        }
    }
    public function getAttachmentsMessage($count=false,$filter=false)
    {
        $sql = 'SELECT id_message,message,name_attachment,type_attachment FROM '._DB_PREFIX_.'ets_livechat_message
        WHERE name_attachment!="" '.($filter ? $filter :'').(!$this->all_shop ? ' AND id_conversation IN (SELECT id_conversation FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_shop ="'.(int)Context::getContext()->shop->id.'")':'');
        $messages = Db::getInstance()->executeS($sql);
        if($count)
        {
            $total=0;
            if($messages)
            {
                foreach($messages as $message)
                {
                    $download= new LC_Download($message['type_attachment']);
                    $total += $download->file_size;
                }
            }
            return array(
                'count'=>count($messages),
                'size'=>$total    
            );
        }
        else
        {
            if($messages)
            {
                foreach($messages as $message)
                {
                     $message_class = new LC_Message($message['id_message']);
                     
                    if(!$message['message'])
                    {
                       $message_class->delete();
                    }
                    else
                    {
                        $download= new LC_Download($message['type_attachment']);
                        $download->delete();
                        $message_class->name_attachment='';
                        $message_class->type_attachment=0;
                        $message_class->update();
                    }
                }
            }
            return true;
        }
    }
    public function getAttachmentsNote($count=false,$filter=false)
    {
        $sql = 'SELECT id_note,id_download,id_note,note FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message_note
        WHERE id_download!=0 '.($filter ? $filter :'').(!$this->all_shop ? ' AND id_message IN (SELECT id_message FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message WHERE id_shop="'.(int)$this->context->shop->id.'")':'');
        $notes= Db::getInstance()->executeS($sql);
        if($count)
        {
            $total=0;
            if($notes)
            {
                foreach($notes as $note)
                {
                    $download= new LC_Download($note['id_download']);
                    $total += $download->file_size;
                }
            }
            return array(
                'count'=>count($notes),
                'size'=>$total    
            );
        }
        else
        {
            if($notes)
            {
                foreach($notes as $note)
                {
                    $note_class = new LC_Note($note['id_note']);
                    if(!$note['note'])
                    {
                        $note_class->delete();
                    }
                    else
                    {
                        $download= new LC_Download($note['id_download']);
                        $download->delete();
                        $note_class->id_download=0;
                        $note_class->update();
                    }
                }
            }
            return true;
        }
    }
    public function getAttachmentsTickets($count=false,$filter=false)
    {
        $sql = 'SELECT f.id_message, f.id_field,f.id_download,f.value FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message_field f
        INNER JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_message t ON (f.id_message= t.id_message)
        WHERE f.id_download!=0 '.($filter ? $filter :'').(!$this->all_shop ? ' AND t.id_shop="'.(int)$this->context->shop->id.'"':'');
        $attachments = Db::getInstance()->executeS($sql);
        if($count)
        {
            $total=0;
            if($attachments)
            {
                foreach($attachments as $attachment)
                {
                    $download= new LC_Download($attachment['id_download']);
                    $total += $download->file_size;
                }
            }
            return array(
                'count'=>count($attachments),
                'size'=>$total    
            );
        }
        else
        {
            if($attachments)
            {
                foreach($attachments as $attachment)
                {
                    $download = new LC_Download($attachment['id_download']);
                    $download->delete();
                    Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message_field WHERE id_download="'.(int)$download->id.'"');
                }
                return true;
            }
        }
    }
    public static function displayPriority($priority)
    {
        if($priority=='1')
            return 'low';
        if($priority==2)
            return 'medium';
        if($priority==3)
            return 'high';
        else
            return 'urgent';
    }
    public function getNameHide($name)
    {
        $name=trim($name);
        for($i=0; $i < Tools::strlen($name)-1; $i++)
        {
            if($name[$i]!=' ' && $name[$i+1]==' ')
              return $name[0].'.'.Tools::substr($name,$i+2);
        }
        return $name;
    }
    public function displayBlockSupport($position)
    {
       $forms = Db::getInstance()->executeS('
       SELECT * FROM '._DB_PREFIX_.'ets_livechat_ticket_form f
       LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_lang fl ON (f.id_form=fl.id_form AND fl.id_lang ="'.(int)$this->context->language->id.'")
       WHERE f.active=1 AND f.deleted=0 AND f.id_form!=1 AND f.id_shop="'.(int)$this->context->shop->id.'" ORDER BY f.sort_order ASC');
       if($forms)
       {
            foreach($forms as &$form)
            {
                $form['link'] = $this->getFormLink($form['id_form']);
                
            }
            $this->context->smarty->assign(
                array(
                    'forms'=>$forms,
                    'position'=>$position,
                    'ps17' => $this->is17 ,
                )
            );
            return $this->display(__FILE__,'form_block.tpl');
       } 
       return '';
    }
    public function hookDisplayLeftColumn()
    {
        if(in_array('left',explode(',',Configuration::get('ETS_LV_SUPPORT_TICKET'))))
        {
            return $this->displayBlockSupport('left');
        }
        return '';
    }
    public function hookDisplayFooter()
    {
        if(in_array('footer',explode(',',Configuration::get('ETS_LV_SUPPORT_TICKET'))))
        {
            return $this->displayBlockSupport('footer');
        }
        return '';
    }
    public function hookDisplayRightColumn()
    {
        if(in_array('right',explode(',',Configuration::get('ETS_LV_SUPPORT_TICKET'))))
        {
            return $this->displayBlockSupport('right');
        }
        return '';
    }
    public function hookDisplayNav()
    {
        if(in_array('top_nav',explode(',',Configuration::get('ETS_LV_SUPPORT_TICKET'))))
        {
            return $this->displayBlockSupport('top_nav');
        }
        return '';
    }
    public function hookDisplayNav1()
    {
        if(in_array('top_nav',explode(',',Configuration::get('ETS_LV_SUPPORT_TICKET'))))
        {
            return $this->displayBlockSupport('top_nav');
        }
        return '';
    }
    public function hookCustomBlockSupport()
    {
        if(in_array('custom_hook',explode(',',Configuration::get('ETS_LV_SUPPORT_TICKET'))))
        {
            return $this->displayBlockSupport('custom_hook');
        }
        return '';
    }
    public function updateLastAction()
    {
        
        if($this->all_shop && $this->shops)
        {
            foreach($this->shops as $shop)
            {
                Configuration::updateValue('ETS_LC_DATE_ACTION_LAST',date('Y-m-d H:i:s'),true,$shop['id_shop_group'],$shop['id_shop']);
                Ets_livechat::updateAdminOnline($shop['id_shop']);
            }
        }
        Configuration::updateValue('ETS_LC_DATE_ACTION_LAST',date('Y-m-d H:i:s'));
        Ets_livechat::updateAdminOnline();
    }
    public function checkFileNumberUpload($id_conversation=0)
    {
        if($max_number = (int)Configuration::get('ETS_LC_NUMBER_FILE_MS'))
        {
            $total_file=0;
            if($id_conversation)
            {
                $sql = 'SELECT count(id_message) FROM '._DB_PREFIX_.'ets_livechat_message
                WHERE name_attachment!="" AND id_conversation ="'.(int)$id_conversation.'" AND id_employee=0';
                $total_file= Db::getInstance()->getValue($sql);
            } 
            if($total_file < $max_number)
                return true;
            else
                return false;
        }
        return true;
    }
    public function getAdminLink($controller)
    {
        return $this->getBaseLink().'/'.Configuration::get('ETS_DIRECTORY_ADMIN_URL').'/index.php?controller='.$controller.'&token='.Tools::getAdminTokenLite($controller);
    }
    public function updateDefaultConfig()
    {
        $languages = Language::getLanguages(false);
        $this->setConfig();
        if($this->lc_configs)
        {
            foreach($this->lc_configs as $key => $config)
            {
                if(Configuration::get($key)===false)
                {
                    if(isset($config['lang']) && $config['lang'])
                    {
                        $values = array();
                        foreach($languages as $lang)
                        {
                            $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                        }
                        Configuration::updateValue($key, $values,true);
                    }
                    else
                        Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '',true);
                }
                
            }
        }
        if(!file_exists(dirname(__FILE__).'/views/img/config/customeravata.jpg'))
            Tools::copy(dirname(__FILE__).'/views/img/temp/customeravata.jpg',dirname(__FILE__).'/views/img/config/customeravata.jpg');
        if(dirname(__FILE__).'/views/img/config/chatbubble.png')
            Tools::copy(dirname(__FILE__).'/views/img/temp/chatbubble.png',dirname(__FILE__).'/views/img/config/chatbubble.png');
        if(dirname(__FILE__).'/views/img/config/adminavatar.jpg')
            Tools::copy(dirname(__FILE__).'/views/img/temp/adminavatar.jpg',dirname(__FILE__).'/views/img/config/adminavatar.jpg');
    }
    public function setConfig()
    {
        
        if($this->lc_configs)
            return $this->lc_configs;
        if($this->checkVesionModule())
        {
            $ticket_forms = Db::getInstance()->executeS('SELECT f.id_form,fl.title 
            FROM '._DB_PREFIX_.'ets_livechat_ticket_form f
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_lang fl ON (f.id_form=fl.id_form)
            WHERE fl.id_lang="'.(int)$this->context->language->id.'" AND f.id_shop="'.(int)$this->context->shop->id.'"
            ');
        }
        else
            $ticket_forms=array();
        $admin_dir = basename(getcwd());
        $sql = 'SELECT * FROM '._DB_PREFIX_.'group g,'._DB_PREFIX_.'group_lang gl, '._DB_PREFIX_.'group_shop gs
            WHERE g.id_group = gl.id_group AND g.id_group = gs.id_group AND gl.id_lang="'.(int)$this->context->language->id.'" AND gs.id_shop ="'.(int)$this->context->shop->id.'"';
        $groups= Db::getInstance()->executeS($sql);
        $customerGroups=array(
            array(
                'id_option' => 'all',
                'name'=> $this->l('All'),
            )
        );
        if($groups)
        {
            foreach($groups as $group)
            {
                $customerGroups[]=array(
                    'id_option'=> $group['id_group'],
                    'name'=>$group['name'],
                );
            }
        }
        $this->lc_configs = array(
            'ETS_LC_TEXT_HEADING_ONLINE' => array(
                'label' => $this->l('Chat box heading text'),
                'type' => 'text',    
                'default' => $this->l('Chat with us'), 
                'tab'=>'status', 
                'required' => true,    
                'form_group_class'=>'status lc_online change_form',
                'lang' => true,  
            ),
            'ETS_LC_HEADING_COLOR_ONLINE' => array(
                'label' => $this->l('Heading background color'),
                'tab'=>'status',
                'type' => 'color', 
                'default' => '#76a600', 
                'validate' => 'isColor',
                'form_group_class'=>'status lc_online change_form',  
                'required' => true,  
            ), 
            'ETS_LC_TEXT_ONLINE' => array(
                'label' => $this->l('Welcome message'),
                'type' => 'textarea',    
                'tab'=>'status',
                'default' => $this->l('Hi there we\'re online! Can we help you?'),  
                'required' => true,  
                'form_group_class'=>'status lc_online change_form',  
                'lang' => true,  
            ),  
            'ETS_LC_TEXT_HEADING_BUSY' => array(
                'label' => $this->l('Chat box heading'),
                'type' => 'text',    
                'default' => $this->l('I\'m busy'),  
                'tab'=>'status',
                'form_group_class'=>'status lc_busy change_form',
                'required' => true,    
                'lang' => true,  
            ), 
            'ETS_LC_HEADING_COLOR_BUSY'=>array(
                'label' => $this->l('Heading background color'),
                'type'=>'color',
                'default'=>'#920013',
                'tab'=>'status',
                'form_group_class'=>'status lc_busy change_form',
                'validate'=>'iscolor',
                'required'=>true,
            ),
            'ETS_LC_TEXT_DO_NOT_DISTURB' => array(
                'label' => $this->l('Welcome message'),
                'type' => 'textarea',
                'tab'=>'status',    
                'form_group_class'=>'status lc_busy change_form',
                'default' => $this->l('Hello. I\'m busy at the moment. Please leave me a chat message, I\'ll get back to you later'),  
                'required' => true,    
                'lang' => true,  
            ), 
            'ETS_LC_TEXT_HEADING_INVISIBLE' => array(
                'label' => $this->l('Chat box heading text'),
                'type' => 'text',    
                'default' => $this->l('Chat with us'),  
                'tab'=>'status',
                'form_group_class'=>'status lc_invisible change_form',
                'required' => true,    
                'lang' => true,  
            ),  
            'ETS_LC_HEADING_COLOR_INVISIBLE'=>array(
                'label' => $this->l('Heading background color'),
                'type'=>'color',
                'default'=>'#505050',
                'tab'=>'status',
                'form_group_class'=>'status lc_invisible change_form',
                'validate'=>'iscolor',
                'required'=>true,
            ),
            'ETS_LC_TEXT_INVISIBLE' => array(
                'label' => $this->l('Welcome message'),
                'type' => 'textarea',    
                'tab'=>'status',
                'form_group_class'=>'status lc_invisible change_form',
                'default' => $this->l('Hi there I\'m not online at the moment, however you can leave me a message. I\'ll call you back later'),  
                'required' => true,    
                'lang' => true,  
            ), 
            'ETS_LC_TEXT_HEADING_OFFLINE' => array(
                'label' => $this->l('Chat box heading text'),
                'type' => 'text',    
                'default' => $this->l('Chat with us'),  
                'tab'=>'status',
                'form_group_class'=>'status lc_offline change_form',
                'required' => true,    
                'lang' => true,  
            ),
            'ETS_LC_HEADING_COLOR_OFFLINE' => array(
                'label' => $this->l('Heading background color'),
                'type' => 'color', 
                'default' => '#505050', 
                'tab'=>'status',
                'form_group_class'=>'status lc_offline change_form',
                'validate' => 'isColor',  
                'required' => true,  
            ), 
            'ETS_LC_TEXT_OFFLINE' => array(
                'label' => $this->l('Welcome message'),
                'type' => 'textarea',
                'tab'=>'status',    
                'form_group_class'=>'status lc_offline change_form',
                'default' => $this->l('Hi there I\'m not online at the moment, however you can leave me a message. I\'ll call you back later'),  
                'required' => true,    
                'lang' => true,  
            ),
            'ETS_LC_TEXT_OFFLINE_THANKYOU' => array(
                'label' => $this->l('Thank you messsage'),
                'type' => 'textarea',  
                'tab'=>'status',  
                'default' => $this->l('We have received your message. We will get back to you soon. Thank you!'),                 
                'lang' => true,  
                'form_group_class'=>'status lc_offline change_form',
                'desc' => $this->l('Leave this field blank allows customer to send continuous offline messages (like when they send online messages) without seeing a "thank you" message')
            ),
            'ETS_LC_DISPLAY_COMPANY_INFO'=>array(
                'label'=>$this->l('Supporter info'),
                'type'=>'radio',
                'tab'=>'chat_box',
                'default'=>'staff',
                'js'=>1,
                'values'=>array(
                    array(
                        'id'=>'ETS_LC_DISPLAY_COMPANY_INFO_staff',
                        'value'=>'staff',
                        'label'=> $this->l('Staffs information')
                    ),
                    array(
                        'id'=>'ETS_LC_DISPLAY_COMPANY_INFO_general',
                        'value'=>'general',
                        'label'=> $this->l('General information')
                    )
                ), 
            ), 
            'ETS_LC_COMPANY_LOGO'=>array(
                'type'=>'file',
                'tab'=>'chat_box',
                'label'=>$this->l('Shop logo'),
                'form_group_class'=>'company_info',
                'default'=>'adminavatar.jpg',
                'desc'=>$this->l('Available image type: jpg, png, gif, jpeg')
            ),
            'ETS_LC_COMPANY_NAME'=>array(
                'type'=>'text',
                'tab'=>'chat_box',
                'label'=>$this->l('Shop name'),
                'required'=>true, 
                'default' =>'ETS-Soft',
                'form_group_class'=>'company_info change_form',
            ),
            'ETS_LC_SUB_TITLE'=>array(
                'type'=>'text',
                'tab'=>'chat_box',
                'label'=>$this->l('Mood'),
                //'form_group_class'=>'change_form company_info',
                'default' =>$this->l('Ask whatever you want!'),
                'lang'=>true,
            ),
            'ETS_LC_DISPLAY_AVATA'=>array(
                'label'=>$this->l('Display avatar in chat box'),
                'type'=>'switch',
                'tab'=>'chat_box',
                'default'=>1,
                'validate'=>'isUnsignedInt',
                'required'=>true,  
            ),
            'ETS_LC_CUSTOMER_AVATA'=>array(
                'label'=>$this->l('Default customer avatar'),
                'type'=>'file',
                'tab'=>'chat_box',
                'form_group_class'=>'customer_avata',
                'default'=>'customeravata.jpg',
                'desc'=>$this->l('Available image type: jpg, png, gif, jpeg')
            ),
            'ETS_LC_AVATAR_IMAGE_TYPE' => array(
                'label' => $this->l('Avatar image type on frontend'),
                'type' => 'select',
                'options' => array(
        			 'query' => array(
                        array(
                            'id_option'=>'rounded',
                            'name'=>$this->l('Rounded')
                        ),
                        array(
                            'id_option'=>'square',
                            'name'=>$this->l('Square')
                        )
                     ),                             
                     'id' => 'id_option',
        			 'name' => 'name'  
                ),    
                'default' => 'rounded',
                'tab'=>'chat_box', 
                'js'=>1,    
                'form_group_class'=>'image_type'                          
            ),
            'ETS_LC_BOX_WIDTH' => array(
                'label' => $this->l('Frontend chat box width'),
                'type' => 'text',
                'tab'=>'chat_box',    
                'default' => 340,  
                'required' => true,
                'suffix' => $this->l('px'),                  
            ),
            'ETS_CLOSE_CHAT_BOX_TYPE'=>array(
                'type'=>'select',
                'label'=>$this->l('Frontend collapsed chatbox type'),
                'options' => array(
        			 'query' => array( 
                            array(
                                'id_option' => 'bubble_alert', 
                                'name' => $this->l('Bubble circle'),                                
                            ),
                            array(
                                'id_option' => 'bottom_alert_bar', 
                                'name' => $this->l('Bottom bar'),                                
                            ),
                            array(
                                'id_option' => 'image', 
                                'name' => $this->l('Custom image'),                                
                            ),
                        ),                             
                     'id' => 'id_option',
        			 'name' => 'name'  
                ),    
                'default' => 'bottom_alert_bar',
                'tab'=>'chat_box',  
            ),
            'ETS_LC_BUBBLE_IMAGE'=>array(
                'label'=>$this->l('Chat bubble image'),
                'type'=>'file',
                'tab'=>'chat_box',
                'default'=>'chatbubble.png',
                'form_group_class' => 'lc_bubble_image',
                'desc'=>$this->l('Available image type: jpg, png, gif, jpeg')
            ), 
            'ETS_CLOSE_CHAT_BOX_BACKEND_TYPE'=>array(
                'type'=>'select',
                'label'=>$this->l('Backend collapsed chatbox type'),
                'options' => array(
        			 'query' => array( 
                            array(
                                'id_option' => 'bubble_alert', 
                                'name' => $this->l('Floating circle'),                                
                            ),
                            array(
                                'id_option' => 'small_bubble', 
                                'name' => $this->l('Small bubble on top'),                                
                            ),
                            array(
                                'id_option' => 'bottom_alert_bar', 
                                'name' => $this->l('Bottom alert bar'),                                
                            ),
                        ),                             
                     'id' => 'id_option',
        			 'name' => 'name'  
                ),    
                'default' => 'bubble_alert',
                'tab'=>'chat_box',  
            ), 
            'ETS_LC_TEXT_SEND' => array(
                'label' => $this->l('Button label when chatting'),
                'type' => 'text',    
                'default' => $this->l('Send'),  
                'tab'=>'chat_box',
                'js'=>1,
                'required' => true,   
                'lang' => true,  
            ),
            'ETS_LC_TEXT_BUTTON_EDIT' => array(
                'label' => $this->l('Button label when edit'),
                'type' => 'text',    
                'default' => $this->l('Edit'),  
                'tab'=>'chat_box',
                'js'=>1,
                'required' => true,    
                'lang' => true,  
            ),
            'ETS_LC_TEXT_SEND_OffLINE' => array(
                'label' => $this->l('Button label when offline'),
                'type' => 'text',    
                'default' => $this->l('Send offline message'),  
                'tab'=>'chat_box',
                'js'=>1,
                'required' => true,
                'form_group_class'=>'change_form',     
                'lang' => true,  
            ), 
            'ETS_LC_TEXT_SEND_START_CHAT' => array(
                'label' => $this->l('Button label to start chatting when online'),
                'type' => 'text',    
                'default' => $this->l('Start chatting!'),  
                'tab'=>'chat_box',
                'required' => true,  
                'js'=>1,
                'form_group_class'=>'change_form',   
                'lang' => true,  
            ),
            'ETS_DISPLAY_SEND_BUTTON'=>array(
                'label'=>$this->l('Display "Send" button'),
                'type'=>'switch',
                'default'=>1,
                'js'=>1,
                'tab'=>'chat_box'
            ),  
            'LC_BACKGROUD_COLOR_BUTTON'=>array(
                'label'=>$this->l('Button background color'),
                'type'=>'color',
                'default'=>'#00aff0',
                'tab'=>'chat_box'
            ),
            'LC_BACKGROUD_HOVER_BUTTON'=>array(
                'label'=>$this->l('Button background color when hover'),
                'type'=>'color',
                'default'=>'#00dcfa',
                'tab'=>'chat_box'
            ),
            'ETS_LC_DISPLAY_REQUIRED_FIELDS' => array(
                'label' => $this->l('Enable 2 steps to start chat'),
                'type' => 'switch', 
                'tab'=>'im',
                'default' => 0, 
                'validate' => 'isUnsignedInt',  
                'required' => true,
                'desc' => $this->l('When customers start chatting, they\'re only required to enter a single message to start the chat'),
                'js' => 1, 
                                      
            ),
            'ETS_LC_ADDITIONAL_NOTIFICATION'=>array(
                'label'=> $this->l('Additional notification'),
                'type'=>'textarea',
                'tab'=>'im', 
                'default'=>$this->l('Sorry for this inconvenience but please enter some additional information to start chatting'),
                'lang'=>true,
                'desc'=>$this->l('After the first message, customers will see this notification and enter their information to continue chatting as normal'),
                'form_group_class'=>'display_required_fields'
            ),
            'ETS_LC_HIDE_ON_MOBILE' => array(
                'label' => $this->l('Hide chatbox on mobile devices'),
                'type' => 'switch', 
                'tab'=>'chat_box',
                'default' => 0, 
                'validate' => 'isUnsignedInt',  
                'required' => true,          
            ),
            'ETS_LC_ENABLE_EMOTION_ICON' => array(
                'label' => $this->l('Enable emotion icons'),
                'type' => 'switch', 
                'tab'=>'im',
                'default' => 1, 
                'validate' => 'isUnsignedInt',  
                'required' => true,         
            ), 
            'ETS_LC_DISPLAY_RATING' =>array(
                'label'=> $this->l('Allow customer to rate a conversation'),
                'type'=>'switch',
                'default'=>1,
                'tab'=>'im',
                'js'=>1,
            ),
            'ETS_LC_DISPLAY_SEND_US_AN_EMAIL' =>array(
                'label'=> $this->l('Display a support link at chatbox bottom'),
                'type'=>'switch',
                'default'=>1,
                'tab'=>'chat_box',
            ),  
            'ETS_LC_LINK_SUPPORT_TITLE' => array(
                'label' => $this->l('Support link title'),
                'type' => 'text',
                'tab'=>'chat_box', 
                'lang'=>1,
                'default' => $this->l('Send us an email'),
                'form_group_class'=>'link_support'                          
            ), 
            'ETS_LC_LINK_SUPPORT_TYPE' => array(
                'label' => $this->l('Link type'),
                'type' => 'select',
                'options' => array(
        			 'query' => array(
                        array(
                            'id_option'=>'contact-form',
                            'name'=>$this->l('Contact form')
                        ),
                        array(
                            'id_option'=>'ticket-form',
                            'name'=>$this->l('Ticket form')
                        ),
                        array(
                            'id_option'=>'custom-link',
                            'name'=>$this->l('Custom link')
                        )
                     ),                             
                     'id' => 'id_option',
        			 'name' => 'name'  
                ),    
                'default' => 'contact-form',
                'tab'=>'chat_box', 
                'form_group_class'=>'link_support'                          
            ), 
            'ETS_LC_LINK_SUPPORT_FORM' => array(
                'label' => $this->l('Ticket form'),
                'type' => 'select',
                'options' => array(
        			 'query' => $ticket_forms,                             
                     'id' => 'id_form',
        			 'name' => 'title'  
                ),    
                'default' => 'contact-form',
                'tab'=>'chat_box', 
                'form_group_class'=>'link_support ticket'                          
            ),
            'ETS_LC_SUPPORT_LINK' => array(
                'label' => $this->l('Custom link'),
                'type' => 'text',
                'tab'=>'chat_box', 
                'lang'=>1,
                'form_group_class'=>'link_support custom'                          
            ),          
            'ETS_LC_DISPLAY_TIME' => array(
                'label' => $this->l('Display message time'),
                'type' => 'switch', 
                'tab'=>'im',
                'default' => 1,
                'required' => true,
                'js' => 1,
                'validate' => 'isUnsignedInt',
            ), 
            'ETS_LC_ENABLE_EDIT_MESSAGE'=>array(
                'label' => $this->l('Enable edit message'),
                'type' => 'switch', 
                'tab'=>'im',
                'default' => 1,
                'required' => true,
                'js' => 1,
                'validate' => 'isUnsignedInt',
            ),
            'ETS_LC_ENABLE_DELETE_MESSAGE'=>array(
                'label' => $this->l('Enable delete message'),
                'type' => 'switch', 
                'tab'=>'im',
                'default' => 1,
                'required' => true,
                'js' => 1,
                'validate' => 'isUnsignedInt',
            ),
            'ETS_LC_MSG_COUNT' => array(
                'label' => $this->l('Message count'),
                'type' => 'text',     
                'tab'=>'im',
                'default' => 10, 
                'required' => true, 
                'validate' => 'isUnsignedInt', 
                'desc' => $this->l('The number of message displayed per Ajax load'),   
                'js' => 1,                         
            ), 
            'ETS_LC_MSG_LENGTH'=>array(
                'label'=> $this->l('Message length'),
                'type'=>'text',
                'tab'=>'im',
                'default'=>500,
                'required' => true, 
                'validate'=> 'isUnsignedInt',
                'desc'=> $this->l('Maximum message length counted by character'),
            ),
            'ETS_LC_ENTER_TO_SEND' => array(
                'label' => $this->l('Press "Enter" key to send message'),
                'type' => 'switch', 
                'tab'=>'im',
                'default' => 1, 
                'validate' => 'isUnsignedInt',  
                'required' => true,
                'js' => 1,          
            ), 
            'ETS_LC_SEND_MESSAGE_TO_MAIL' =>array(
                'label'=> $this->l('Allow admin to send message to customer via email'),
                'type'=>'switch',
                'default'=>1,
                'tab'=>'im',
            ),
            'ETS_LC_CUSTOMER_OLD' =>array(
                'label'=> $this->l('Allow customer to see past messages'),
                'type'=>'switch',
                'default'=>1,
                'tab'=>'im',
            ),
            'ETS_LC_STAFF_ACCEPT' =>array(
                'label'=> $this->l('Staff to accept or decline chat'),
                'type'=>'switch',
                'default'=>1,
                'js'=>1,
                'tab'=>'im',
                'desc'=> $this->l('Staffs need to manually accept or decline customer chat session'),
            ),
            'ETS_LC_SEND_FILE' =>array(
                'label'=> $this->l('Allow customer to upload file'),
                'type'=>'switch',
                'default'=>0,
                'js'=>1,
                'tab'=>'im',
            ),
            'ETS_LC_MAX_FILE_MS'=>array(
                'label'=> $this->l('Max upload file size'),
                'type'=>'text',
                'default'=>Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
                'js'=>1,
                'tab'=>'im',
                'validate' => 'isUnsignedInt',
                'suffix' => 'MB',
                'desc'=> $this->l('Limited to both live chat and ticketing system. Leave this field blank to ignore this limitation'),
            ),
            'ETS_LC_NUMBER_FILE_MS'=>array(
                'label'=> $this->l('Maximum number of files that customer can upload per conversation'),
                'type'=>'text',
                'default'=>100,
                'js'=>1,
                'validate' => 'isUnsignedInt',
                'tab'=>'im',
                'desc'=> $this->l('Leave this field blank to ignore this limitation'),
            ),
            'ETS_LC_UPDATE_CONTACT_INFO' => array(
                'label' => $this->l('Allow customer to update their contact'),
                'type' => 'switch', 
                'default' => 1, 
                'tab'=>'privacy',
                'validate' => 'isUnsignedInt',  
                'required' => true,      
                'js' => 1,
                'desc' => $this->l('Allow customer update their name, phone, email when the chat has been started'),           
            ), 
            'ETS_LC_DISPLAY_MESSAGE_STATUSES'=>array(
                'label'=> $this->l('Display message statuses'),
                'type' => 'checkbox',   
                'tab'=>'privacy',
                'values' => array(
                     'query' => array(
                        array(
                            'id' => 'sent', 
                            'label' => $this->l('Sent'),                                
                        ),
                        array(
                            'id' => 'delevered', 
                            'label' => $this->l('Delivered'),                                
                        ),
                        array(
                            'id' => 'seen', 
                            'label' => $this->l('Seen'),                                
                        ),
                        array(
                            'id' => 'writing', 
                            'label' => $this->l('Writting'),                                
                        )
                     ), 
                     'id' => 'id',
    	             'name' => 'label',                                                               
                ), 
                'default'=>'sent,delevered,seen,writing'
            ),  
            'ETS_LC_ALLOW_CLOSE' => array(
                'label' => $this->l('Allow customer to close chat box'),
                'type' => 'switch', 
                'tab'=>'privacy',
                'default' => 0, 
                'validate' => 'isUnsignedInt',  
                'required' => true,         
            ),
            'ETS_LC_ALLOW_MAXIMIZE' => array(
                'label' => $this->l('Allow customer to maximize/minimize chatbox'),
                'type' => 'switch', 
                'tab'=>'privacy',
                'default' => 1, 
                'validate' => 'isUnsignedInt',  
                'required' => true,         
            ),
            
            'ETS_LC_CHAT_FIELDS' => array(
                'label' => $this->l('Chat box fields'),
                'type' => 'checkbox', 
                'tab'=>'fields',
                'default' => 'name,email,phone,text,departments',               
                'values' => array(
                     'query' => array(
                        array(
                            'id' => 'name',
                            'label' => $this->l('Name'),
                        ),
                        array(
                            'id' => 'email',
                            'label' => $this->l('Email'),
                        ),
                        array(
                            'id' => 'phone',
                            'label' => $this->l('Phone'),
                        ),
                        array(
                            'id'=>'departments',
                            'label' => $this->l('Departments'),
                        ),
                        array(
                            'id' => 'message',
                            'label' => $this->l('Message'),
                        ),
                     ), 
                     'id' => 'id',
		             'name' => 'label',                                                               
                ),  
                'desc' => $this->l('Email is always required when offline. Message is required field. Name, email and phone are auto filled in if customer is logged in'),          
            ),   
            'ETS_LC_CHAT_FIELDS_REQUIRED' => array(
                'label' => $this->l('Required fields'),
                'type' => 'checkbox', 
                'tab'=>'fields',
                'default' => 'name,email,phone,text,departments',               
                'values' => array(
                     'query' => array(
                        array(
                            'id' => 'name',
                            'label' => $this->l('Name'),
                        ),
                        array(
                            'id' => 'email',
                            'label' => $this->l('Email'),
                        ),
                        array(
                            'id' => 'phone',
                            'label' => $this->l('Phone'),
                        ),
                        array(
                            'id'=>'departments',
                            'label' => $this->l('Departments'),
                        ),
                        array(
                            'id' => 'message',
                            'label' => $this->l('Message'),
                        ),
                     ), 
                     'id' => 'id',
		             'name' => 'label',                                                               
                ),  
                'desc' => $this->l('Fields that don\'t accept empty value'),          
            ),
            'ETS_LC_SEND_PRODUCT_LINK'=>array(
                'label'=> $this->l('Send product link'),
                'type'=>'switch',
                'tab'=>'fields',
                'default'=>1,
                'desc' => $this->l('Allow customers to send product link when customers start chatting at the product detail page')
            ), 
            'ETS_LC_PRODUCT_LINK_REQUIRE'=>array(
                'label'=> $this->l('Require product link'),
                'type'=>'switch',
                'tab'=>'fields',
                'default'=>0,
                'desc' => $this->l('Product link will always be sent when start chatting'),
                'form_group_class' => 'send_product_link',
            ),
            'ETS_LC_PRODUCT_NAME_COLOR'=>array(
                'label'=> $this->l('Product name color'),
                'type'=>'color',
                'tab'=>'fields',
                'default'=>'#2fb5d2',
                'form_group_class' => 'send_product_link',
            ),
            'ETS_LC_PRODUCT_PRICE_COLOR'=>array(
                'label'=> $this->l('Product price color'),
                'type'=>'color',
                'tab'=>'fields',
                'default'=>'#f39d72',
                'form_group_class' => 'send_product_link',
            ),
            'ETS_LC_SEND_MAIL_WHEN_SEND_MG'=>array(
                'label'=> $this->l('Send email to admin when offline'),
                'type'=>'switch',
                'tab'=>'email',
                'default'=>1,
            ), 
            'ETS_LC_MAIL_TO' => array(
                'label' => $this->l('Mail to'),
                'type' => 'checkbox', 
                'tab'=>'email',
                'default' => 'shop,employee',               
                'values' => array(
                     'query' => array(
                        array(
                            'id' => 'shop',
                            'label' => $this->l('Shop email'),
                        ),
                        array(
                            'id' => 'employee',
                            'label' => $this->l('All employees'),
                        ),
                        array(
                            'id' => 'custom',
                            'label' => $this->l('Custom emails'),
                        ),
                     ), 
                     'id' => 'id',
		             'name' => 'label',                                                               
                ), 
                'form_group_class'=>'lc_send_mail'
            ),
            'ETS_LC_CUSTOM_EMAIL' => array(
                'label' => $this->l('Custom emails'),
                'type' => 'text',   
                'tab'=>'email', 
                'form_group_class'=>'customer_emails lc_send_mail',
                'desc' => $this->l('Email addresses separated by a comma'),
            ),
            'ETS_LC_SEND_MAIL'=>array(
                'label'=>'Mail when',
                'type' => 'checkbox', 
                'tab'=>'email',
                'default' => 'first_message',               
                'values' => array(
                     'query' => array(
                        array(
                            'id' => 'first_message',
                            'label' => $this->l('Send notification email to admin when customer send the first message'),
                        ),
                        array(
                            'id' => 'affter_a_centaint_time',
                            'label' => $this->l('Send notification email to admin if customer send a message after a certain time since admin is offline'),
                        ),
                     ), 
                     'id' => 'id',
		             'name' => 'label',                                                               
                ), 
                'form_group_class' =>'lc_send_mail',
            ),
            'ETS_CENTAINT_TIME_SEND_EMAIL'=>array(
                'type' => 'text',
                'label'=> $this->l('Time'), 
                'default' => 1, 
                'validate' => 'isUnsignedFloat',   
                'js' => 1,   
                'suffix' => $this->l('Hours'),
                'tab'=>'email',
                'form_group_class'=>'time_send_email lc_send_mail'
            ),
            'ETS_DIRECTORY_ADMIN_URL'=>array(
                'type' => 'text',
                'label'=> $this->l('Admin directory'), 
                'tab'=>'email',
                'desc' => Tools::getShopDomainSsl(true).Context::getContext()->shop->getBaseURI().'[admin-directory]',
                'form_group_class'=>'lc_send_mail',
                'default' =>$admin_dir,
            ),
            'ETS_LC_CAPTCHA' => array(
                'label' => $this->l('Require CAPTCHA when'),
                'type' => 'checkbox',        
                'tab'=>'security',  
                'form_group_class'=>'captcha',
                'default' =>'auto',   
                'values' => array(
                     'query' => array(
                        array(
                            'id' => 'auto', 
                            'label' => $this->l('Auto enable Captcha when detect spams'),                                
                        ),
                        array(
                            'id' => 'first', 
                            'label' => $this->l('When customer send the first message'),                                
                        ),
                        array(
                            'id' => 'fromsecond', 
                            'label' => $this->l('From the second message when no employee is online'),                                
                        ),
                        array(
                            'id' => 'secondnotlogin', 
                            'label' => $this->l('From the second message when customer is not logged in'),                                
                        ),
                        array(
                            'id' => 'notlog', 
                            'label' => $this->l('Always if customer is not logged in'),                                
                        ), 
                        array(
                            'id' => 'always', 
                            'label' => $this->l('Always (everytime customer send a message)'),                                
                        ),
                     ), 
                     'id' => 'id',
		             'name' => 'label',                                                               
                ),  
                'desc' => $this->l('Avoid spam messages, avoid server overload'),          
            ),
            'ETS_LC_CAPTCHA_TYPE' => array(
                'label' => $this->l('Captcha image type'),
                'type' => 'select',
                'tab'=>'security',
                'options' => array(
        			 'query' => array( 
                            array(
                                'id_option' => 'colorful', 
                                'name' => $this->l('Colorful'),                                
                            ),
                            array(
                                'id_option' => 'basic', 
                                'name' => $this->l('Basic'),                                
                            ),
                            array(
                                'id_option' => 'complex', 
                                'name' => $this->l('Complex'),                                
                            ), 
                        ),                             
                     'id' => 'id_option',
        			 'name' => 'name'  
                ),    
                'default' => 'colorful',                                            
            ),   
            'ETS_LC_AUTO_OPEN' => array(
                'label' => $this->l('Auto open chat box'),
                'type' => 'switch', 
                'tab'=>'timing',
                'default' => 0, 
                'validate' => 'isUnsignedInt',  
                'required' => true,      
                'js' => 1,     
            ),  
            'ETS_LC_AUTO_OPEN_CHATBOX_DELAY' => array(
                'label' => $this->l('Delay time to open chat box'),
                'type' => 'text', 
                'tab'=>'timing',              
                'validate' => 'isUnsignedInt',
                'desc' => $this->l('Delay time to automatcially open chat box. Leave blank to open chat box immediately when customer lands on website'),   
                'js' => 1,
                'suffix' => $this->l('second(s)'),
                'default'=>10,     
                'form_group_class'=>'lc_auto_open'        
            ),
            'ETS_LC_AUTO_OPEN_ONLINE_ONLY' => array(
                'label' => $this->l('Only auto open chat box when administrator is online'),
                'type' => 'switch', 
                'tab'=>'timing',
                'default' => 1, 
                'validate' => 'isUnsignedInt',  
                'required' => true,      
                'js' => 1,    
                'form_group_class'=>'lc_auto_open' ,        
            ),
            'ETS_LC_TIME_OUT' => array(
                'label' => $this->l('Refresh speed of frontend'),
                'type' => 'text', 
                'tab'=>'timing',
                'default' => 3000, 
                'validate' => 'isUnsignedInt',  
                'required' => true,      
                'js' => 1,   
                'suffix' => $this->l('ms'),
                'desc' => $this->l('3000 ms is recommended. Increase this value can reduce your server load but it will slow down the communication speed'),        
            ),
            'ETS_LC_AUTO_FRONTEND_SPEED' => array(
                'label' => $this->l('Auto optimize frontend refresh speed'),
                'type' => 'switch', 
                'tab'=>'timing',
                'default' => 1, 
                'validate' => 'isUnsignedInt',  
                'required' => true,      
                'js' => 1,           
            ), 
            'ETS_LC_TIME_OUT_BACK_END' => array(
                'label' => $this->l('Refresh speed of backend'),
                'type' => 'text', 
                'tab'=>'timing',
                'default' => 3000, 
                'validate' => 'isUnsignedInt',  
                'required' => true,      
                'js' => 1,   
                'suffix' => $this->l('ms'),
                'desc' => $this->l('3000 ms is recommended. Increase this value can reduce your server load but it will slow down the communication speed'),        
            ), 
            'ETS_LC_AUTO_BACKEND_SPEED' => array(
                'label' => $this->l('Auto optimize backend refresh speed'),
                'type' => 'switch', 
                'tab'=>'timing',
                'default' => 1, 
                'validate' => 'isUnsignedInt',  
                'required' => true,      
                'js' => 1,           
            ),
            'ETS_LC_ONLINE_TIMEOUT' => array(
                'label' => $this->l('Automatically pause customer chat if they\'re not active in'),
                'type' => 'text', 
                'tab'=>'timing',
                'default' => 10, 
                'validate' => 'isUnsignedFloat',  
                'required' => true,      
                'js' => 1,   
                'suffix' => $this->l('minute(s)'),       
            ),  
            'ETS_LC_ENDCHAT_AUTO' =>array(
                'label'=> $this->l('End chat automatically if there is no new messages in'),
                'type'=>'text',
                'default'=>60,
                'tab'=>'timing',
                'js' => 1, 
                'validate' => 'isUnsignedInt',
                'suffix'=> $this->l('minute(s)'),
                'desc'=>$this->l('You can leave this field blank'),
            ),
            'ETS_LC_TIME_WAIT' =>array(
                'label'=> $this->l('Estimated waiting time'),
                'type'=>'text',
                'default'=>5,
                'tab'=>'timing',
                'js' => 1, 
                'validate' => 'isUnsignedInt',
                'suffix'=> $this->l('minute(s)'),
                'desc'=>$this->l('You can leave this field blank'),
            ),          
            'ETS_LC_MISC' => array(
                'label' => $this->l('Display chatbox on those pages only'),
                'type' => 'select',
                'options' => array(
        			 'query' => array( 
                            array(
                                'id_option' => 'all', 
                                'name' => $this->l('All'),                                
                            ),
                            array(
                                'id_option' => 'index', 
                                'name' => $this->l('Home'),                                
                            ),
                            array(
                                'id_option' => 'category', 
                                'name' => $this->l('Category'),                                
                            ), 
                            array(
                                'id_option' => 'product', 
                                'name' => $this->l('Product'),                                
                            ),
                            array(
                                'id_option' => 'cms', 
                                'name' => $this->l('CMS'),                                
                            ),
                            array(
                                'id_option' => 'other', 
                                'name' => $this->l('Other pages'),                                
                            ),
                        ),                             
                     'id' => 'id_option',
        			 'name' => 'name'  
                ),    
                'default' => 'all',
                'multiple' => true, 
                'tab'=>'display',
                'form_group_class'=>'page_display_chatbox'                               
            ),
            'ETS_LC_CUSTOMER_GROUP' => array(
                'label' => $this->l('Customer group'),
                'type' => 'select',
                'options' => array(
        			 'query' => $customerGroups,                             
                     'id' => 'id_option',
        			 'name' => 'name'  
                ),    
                'default' => 'all',
                'multiple' => true, 
                'tab'=>'display',
                'desc'=> $this->l('Select customer group who can use live chat feature')                               
            ),
            'ETS_CONVERSATION_LIST_TYPE'=>array(
                'type'=>'select',
                'label'=>$this->l('Conversation list type'),
                'options' => array(
        			 'query' => array( 
                            array(
                                'id_option' => 'floating', 
                                'name' => $this->l('Fixed'),                                
                            ),
                            array(
                                'id_option' => 'fixed', 
                                'name' => $this->l('Floating'),                                
                            ),
                        ),                             
                     'id' => 'id_option',
        			 'name' => 'name'  
                ),    
                'default' => 'floating',
                'tab'=>'display',  
            ),
            'ETS_DISPLAY_DASHBOARD_ONLY'=>array(
                'label'=> $this->l('Display chat on backend dashboard only'),
                'type'=>'switch',
                'default'=>0,
                'tab'=>'display',
            ),
            'ETS_LV_SUPPORT_TICKET'=>array(
                'label'=> $this->l('Display support links block on'),
                'type' => 'checkbox',   
                'tab'=>'display',
                'values' => array(
                     'query' => array(
                        array(
                            'id'=>'left',
                            'label' => $this->l('Left sidebar'),
                        ),
                        array(
                            'id'=>'right',
                            'label' => $this->l('Right sidebar'),
                        ),
                        array(
                            'id'=>'footer',
                            'label' => $this->l('Footer'),
                        ),
                        array(
                            'id'=>'top_nav',
                            'label' => $this->l('Top navigation'),
                        ),
                        array(
                            'id'=>'custom_hook',
                            'label' => $this->l('Custom hook'),
                        )
                     ), 
                     'id' => 'id',
    	             'name' => 'label',                                                               
                ), 
                'default'=>'footer',
                'desc' => $this->l('To use "custom hook", put').'<span class="lighhight_hook">{hook h="customBlockSupport"}</span>'. $this->l('on tpl file where you want to display support links block.'),
            ), 
            'ETS_BLACK_LIST_IP'=>array(
                'type'=>'textarea',
                'label'=>$this->l('IP black list'),
                'tab'=>'back_list_ip',
                'desc'=>$this->l('This is the list of IP addresses you want to block their requests to chat. Please enter each IP in a line'),
            ),
            'ETS_SOUND_WHEN_NEW_MESSAGE'=>array(
                'type'=>'select',
                'label' => $this->l('Notification sound type'),
                'options'=>array(
                    'query'=>array(
                        array(
                            'id_option'=>'sound1',
                            'name' => $this->l('Sound 1'),
                        ),
                        array(
                            'id_option'=>'sound2',
                            'name' => $this->l('Sound 2'),
                        ),
                        array(
                            'id_option'=>'sound3',
                            'name' => $this->l('Sound 3'),
                        ),
                        array(
                            'id_option'=>'sound4',
                            'name' => $this->l('Sound 4'),
                        ),
                        array(
                            'id_option'=>'sound5',
                            'name' => $this->l('Sound 5'),
                        ),
                        array(
                            'id_option'=>'sound6',
                            'name' => $this->l('Sound 6'),
                        ),
                        array(
                            'id_option'=>'sound7',
                            'name' => $this->l('Sound 7'),
                        ),
                        array(
                            'id_option'=>'sound8',
                            'name' => $this->l('Sound 8'),
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name' 
                ),
                'default' => 'sound1',
                'tab'=>'sound',
            ),
            'ETS_LC_USE_SOUND_BACKEND'=>array(
                'label'=> $this->l('Enable notification sound on backend'),
                'type'=>'switch',
                'default'=>1,
                'tab'=>'sound',
                'js'=>1,
            ),
            'ETS_LC_USE_SOUND_FONTEND'=>array(
                'label'=> $this->l('Enable notification sound on frontend'),
                'type'=>'switch',
                'default'=>1,
                'js'=>1,
                'tab'=>'sound',
            ),
            'ETS_TAB_CURENT_ACTIVE'=>array(
                'label'=>'',
                'type'=>'hidden',
                'tab'=>'sound',
            ),
            'ETS_ENABLE_PRE_MADE_MESSAGE'=>array(
                'label'=> $this->l('Enable pre-made message'),
                'type'=>'switch',
                'default'=>0,
                'js'=>1,
                'tab'=>'pre_made_message',
            ), 
            'ETS_LIVECHAT_ADMIN_DE'=>array(
                'label'=> $this->l('Allow staffs to transfer their conversation to another department'),
                'type'=>'switch',
                'default'=>1,
                'js'=>1,
                'tab'=>'departments',
            ),
            'ETS_LIVECHAT_ADMIN_OLD'=>array(
                'label'=> $this->l('Allow staffs to see past messages from customer'),
                'type'=>'switch',
                'default'=>1,
                'js'=>1,
                'tab'=>'departments',
            ),
            'ETS_ENABLE_AUTO_REPLY'=>array(
                'label'=> $this->l('Enable auto reply'),
                'type'=>'switch',
                'default'=>0,
                'js'=>1,
                'tab'=>'auto_reply',
            ),
            'ETS_FORCE_ONLINE_AUTO_REPLY' =>array(
                'label'=> $this->l('Only send auto message when "Force online" is enabled'),
                'type'=>'switch',
                'default'=>1,
                'tab'=>'auto_reply',
                'form_group_class'=>'form_auto_reply_stop',
            ),
            'ETS_STOP_AUTO_REPLY' =>array(
                'label'=> $this->l('Stop auto replying if admin has manually replied to a customer message'),
                'type'=>'switch',
                'default'=>1,
                'tab'=>'auto_reply',
                'form_group_class'=>'form_auto_reply_stop',
            ),
            'ETS_LIVECHAT_ENABLE_FACEBOOK'=>array(
                'label'=> $this->l('Login with Facebook'),
                'type'=>'switch',
                'default'=>0,
                'tab'=>'sosial',
            ), 
            'ETS_LIVECHAT_FACEBOOK_APP_ID'=>array(
                'label'=> $this->l('Facebook Application ID'),
                'type'=>'text',
                'tab'=>'sosial',
                'form_group_class'=>'login_facebook',
                'required' => 1,
                'desc' => '<a href="https://developers.facebook.com/apps" target="_blank">'.$this->l('Where do I get this info?').'</a>',
            ),
            'ETS_LIVECHAT_FACEBOOK_APP_SECRET'=>array(
                'label'=> $this->l('Facebook Application Secret'),
                'type'=>'text',
                'tab'=>'sosial',
                'form_group_class'=>'login_facebook',
                'required' => 1,
                'desc' => '<a href="https://developers.facebook.com/apps" target="_blank">'.$this->l('Where do I get this info?').'</a>',
            ), 
            'ETS_LIVECHAT_ENABLE_GOOGLE'=>array(
                'label'=> $this->l('Login with Google'),
                'type'=>'switch',
                'default'=>0,
                'tab'=>'sosial',
            ), 
            'ETS_LIVECHAT_GOOGLE_APP_ID'=>array(
                'label'=> $this->l('Google Application ID'),
                'type'=>'text',
                'tab'=>'sosial',
                'form_group_class'=>'login_google',
                'required' => 1,
                'desc' => '<a href="https://console.developers.google.com" target="_blank">'.$this->l('Where do I get this info?').'</a>',
            ),
            'ETS_LIVECHAT_GOOGLE_APP_SECRET'=>array(
                'label'=> $this->l('Google Application Secret'),
                'type'=>'text',
                'tab'=>'sosial',
                'form_group_class'=>'login_google',
                'required' => 1,
                'desc' => '<a href="https://console.developers.google.com" target="_blank">'.$this->l('Where do I get this info?').'</a>',
            ),   
            'ETS_LIVECHAT_ENABLE_TWITTER'=>array(
                'label'=> $this->l('Login with Twitter'),
                'type'=>'switch',
                'default'=>0,
                'tab'=>'sosial',
            ), 
            'ETS_LIVECHAT_TWITTER_APP_ID'=>array(
                'label'=> $this->l('Twitter Application ID'),
                'type'=>'text',
                'tab'=>'sosial',
                'form_group_class'=>'login_twitter',
                'required' => 1,
                'desc' => '<a href="https://developer.twitter.com/en/apps/" target="_blank">'.$this->l('Where do I get this info?').'</a>',
            ),
            'ETS_LIVECHAT_TWITTER_APP_SECRET'=>array(
                'label'=> $this->l('Twitter Application Secret'),
                'type'=>'text',
                'tab'=>'sosial',
                'form_group_class'=>'login_twitter',
                'required' => 1,
                'desc' => '<a href="https://developer.twitter.com/en/apps/" target="_blank">'.$this->l('Where do I get this info?').'</a>',
            ),  
        );
        return $this->lc_configs;
    }
    public function setConfigForm()
    {
        $deparments =array(
            array(
                'id' => 'all', 
                'label' => $this->l('All'),                                
            ),
        );
        if($this->checkVesionModule())
        {
            $list_departments= Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_departments WHERE status=1');
            if($list_departments)
            {
                foreach ($list_departments as $value)
                {
                    $deparments[]=array(
                        'id'=>$value['id_departments'],
                        'label'=> $value['name'],
                    );
                }
            }
        }
        $filed_forms=array(					
    		array(
    			'type' => 'text',
    			'label' => $this->l('Form title'),
    			'name' => 'title',
    			'lang' => true,    
                'required' => true, 
    		    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}', 
                'form_group_class' => 'ticket info change_form',                             
    		),
            array(
               'type' => 'text',
    			'label' => $this->l('Friendly URL'),
    			'name' => 'friendly_url',
    			'lang' => true,   
                'form_group_class' => 'ticket info change_form', 
                'desc' => Tools::getValue('id_form') ? '<a href="'.$this->getFormLink(Tools::getValue('id_form')).'" class="link_form_support">'.$this->getFormLink(Tools::getValue('id_form')).'</a>':'<a href="#" class="link_form_support"> </a>',
            ),
            array(
                'type' => 'textarea',
    			'label' => $this->l('Description'),
    			'name' => 'description',
    			'lang' => true,    
                'form_group_class' => 'ticket info change_form',
            ),
            array(
                'type' => 'text',
    			'label' => $this->l('Meta title'),
    			'name' => 'meta_title',
    			'lang' => true,    
                'form_group_class' => 'ticket info change_form',
            ),
            array(
    			'type' => 'textarea',
    			'label' => $this->l('Meta description'),
    			'name' => 'meta_description',
                'lang' => true,
                'cols' => 20,
                'form_group_class' => 'ticket info change_form',	
    		),
            array(
    			'type' => 'tags',
    			'label' => $this->l('Meta keywords'),
    			'name' => 'meta_keywords',
                'lang' => true,		
                'form_group_class' => 'ticket info change_form',			
    		),
            array(
    			'type' => 'switch',
    			'label' => $this->l('Active'),
    			'name' => 'active',
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
                'default'=>1,
                'form_group_class' => 'ticket info change_form field-private',		
    		),
            array(
                'type'=>'checkbox',
                'label'=> $this->l('Who to send email notification when a new ticket arrived?'),
                'name'=> 'mail_new_ticket',
                'values' => array(
                     'query' => array(
                        array(
                            'id' => 'supper_admins', 
                            'label' => $this->l('Super admins'),                                
                        ),
                        array(
                            'id' => 'all_employees', 
                            'label' => $this->l('All employees'),                                
                        ),
                        array(
                            'id' => 'department', 
                            'label' => $this->l('All employees in the associated department'),                                
                        ),
                        array(
                            'id' => 'custom_emails', 
                            'label' => $this->l('Custom emails'),                                
                        )
                     ), 
                     'id' => 'id',
    	             'name' => 'label',                                                               
                ), 
                'default'=>'supper_admins',
                'form_group_class' => 'ticket email change_form',
            ),
            array(
    			'type' => 'text',
    			'label' => $this->l('Custom emails'),
    			'name' => 'custom_mail',   
                'form_group_class' => 'ticket email change_form custom_email',   
                'desc' => $this->l('Enter email separated by a comma (,)'),            						
    		),
            array(
    			'type' => 'switch',
    			'label' => $this->l('Send a confirmation email to customer when ticket is submitted?'),
    			'name' => 'send_mail_to_customer',
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
                'default' =>1,
                'form_group_class' => 'ticket email change_form',		
    		),
            array(
    			'type' => 'switch',
    			'label' => $this->l('Send email to customer when admin reply to their ticket?'),
    			'name' => 'send_mail_reply_customer',
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
                'form_group_class' => 'ticket email change_form',
                'default' =>1,		
    		),
            array(
    			'type' => 'switch',
    			'label' => $this->l('Send email to admin when customer reply to a ticket?'),
    			'name' => 'send_mail_reply_admin',
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
                'default' =>1,
                'form_group_class' => 'ticket email change_form',		
    		),
            array(
    			'type' => 'switch',
    			'label' => $this->l('Allow unregistered users to submit ticket?'),
    			'name' => 'allow_user_submit',
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
                'default'=>1,
                'form_group_class' => 'ticket general change_form',		
    		),
            array(
    			'type' => 'switch',
    			'label' => $this->l('Save customer\'s upload file?'),
    			'name' => 'save_customer_file',
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
                'default'=>1,
                'form_group_class' => 'ticket general change_form',	
                'desc' => $this->l('Enable this to save customer\'s upload file on the server. Otherwise the upload file will only be sent to admin via email.'),	
    		),
            array(
    			'type' => 'switch',
    			'label' => $this->l('Save staff\'s upload file?'),
    			'name' => 'save_staff_file',
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
                'default'=>1,
                'form_group_class' => 'ticket general change_form',	
                'desc' => $this->l('Enable this to save staff\'s upload file on the server. Otherwise the upload file will only be sent to customer via email.'),	
    		),
            array(
                'type' => 'switch',
    			'label' => $this->l('Allow customer to attach file when reply to a ticket'),
    			'name' => 'customer_reply_upload_file',
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
                'default'=>1,
                'form_group_class' => 'ticket general change_form',
            ),
            array(
    			'type' => 'switch',
    			'label' => $this->l('Require customer to select a department before submitting a ticket?'),
    			'name' => 'require_select_department',
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
                'default' =>1,
                'form_group_class' => 'ticket general change_form',	
    		),
            array(
                'type'=>'checkbox',
                'label'=> $this->l('Associated departments'),
                'name'=> 'departments',
                'values' => array(
                     'query' => $deparments, 
                     'id' => 'id',
    	             'name' => 'label',                                                               
                ), 
                'default'=>'all',
                'form_group_class' => 'ticket general change_form departments',
                'desc'=>$this->l('Select the departments who can solve the tickets generated from this form together'),
            ),
            array(
    			'type' => 'switch',
    			'label' => $this->l('Enable CAPTCHA protection?'),
    			'name' => 'allow_captcha',
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
                'form_group_class' => 'ticket general change_form',	
    		),
            array(
    			'type' => 'switch',
    			'label' => $this->l('Do not require registered user to enter captcha code'),
    			'name' => 'customer_no_captcha',
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
                'default'=>1,
                'form_group_class' => 'ticket general change_form customer_no_captcha',	
    		),
            array(
    			'type' => 'select',
    			'label' => $this->l('Default priority'),
    			'name' => 'default_priority',
    			'options' => array(
                    'query' => array( 
                            array(
                                'id_option' =>'1', 
                                'name' => $this->l('Low')
                            ),        
                            array(
                                'id_option' => '2', 
                                'name' => $this->l('Medium')
                            ),
                            array(
                                'id_option' => '3', 
                                'name' => $this->l('High')
                            ),
                            array(
                                'id_option' => '4', 
                                'name' => $this->l('Urgent')
                            ),
                        ),                             
                     'id' => 'id_option',
        			 'name' => 'name'  
                ),	
                'default'=>'2',
                'form_group_class' => 'ticket general change_form',				
    		),
            array(
                'type' => 'text',
                'label'=> $this->l('Submit button label'),
                'name'=>'button_submit_label',
                'default'=>$this->l('Submit'),
                'lang'=>1,
                'form_group_class' => 'ticket general change_form',	
            ),
            array(
                'type'=>'hidden',
                'name' => 'id_form',
            )
        );
        return $filed_forms;
    }
    public function setConfigField()
    {
        $filed_filed=array(
            array(
                'type'=>'text',
                'name'=>'label',
                'label' => $this->l('Label'),
                'lang'=> true,
                'class'=> 'lc_field_label',
                'required'=>true,
            ),
            array(
                'type' =>'select',
                'name' => 'type',
                'label' => $this->l('Type'),
                'class' =>'lc_field_type',
                'options' => array(
                    'query' => array( 
                            array(
                                'id_option' =>'text', 
                                'name' => $this->l('Text')
                            ),        
                            array(
                                'id_option' => 'text_editor', 
                                'name' => $this->l('Text editor')
                            ),
                            array(
                                'id_option' => 'select', 
                                'name' => $this->l('Select')
                            ),
                            array(
                                'id_option' => 'radio', 
                                'name' => $this->l('Radio')
                            ),
                            array(
                                'id_option' => 'email', 
                                'name' => $this->l('Email')
                            ),
                            array(
                                'id_option' => 'phone_number', 
                                'name' => $this->l('Phone number')
                            ),
                            array(
                                'id_option' => 'file', 
                                'name' => $this->l('File')
                            ),
                        ),                             
                     'id' => 'id_option',
        			 'name' => 'name'  
                ),
                'form_group_class'=>'field_type field-private',		
            ),
            array(
                'type'=>'textarea',
                'name'=>'options',
                'label' => $this->l('Options'),
                'lang'=> true,
                'form_group_class' => 'field_contact_option field-private',
                'desc'=> $this->l('Enter each option value on a line'),
            ),
            array(
                'type'=>'switch',
                'label' => $this->l('Is contact email'),
                'name' => 'is_contact_mail',
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
                'form_group_class'=>'field_contact_email field-private'
            ),
            array(
                'type'=>'switch',
                'label' => $this->l('Is contact name'),
                'name' => 'is_contact_name',
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
                'form_group_class'=>'field_contact_name field-private'
            ),
            array(
                'type'=>'switch',
                'label' => $this->l('Is subject'),
                'name' => 'is_subject',
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
                'form_group_class'=>'field_contact_subject'
            ),
            array(
                'type'=>'switch',
                'label' => $this->l('Is customer phone number'),
                'name' => 'is_customer_phone_number',
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
                'form_group_class'=>'field_contact_phone'
            ),
            array(
                'type'=> 'text',
                'lang'=> true,
                'name'=>'placeholder',
                'label'=> $this->l('Placeholder'),
                'form_group_class'=>'field_contact_placeholder'
            ),
            array(
                'type'=> 'textarea',
                'label'=> $this->l('Description'),
                'name' => 'description',
                'lang'=> true,
                
            ),
            array(
                'type'=>'switch',
                'label' => $this->l('Required'),
                'name' => 'required',
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
                'type'=>'hidden',
                'name' => 'id_field',
            )
        );
        return $filed_filed;
    }
}