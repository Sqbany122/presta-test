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
class LC_Conversation extends ObjectModel
{
    public $id_customer;
    public $id_shop;
    public $blocked;
    public $archive;
    public $captcha_enabled;
    public $customer_writing;
    public $employee_writing;
    public $customer_name;
    public $customer_email;
    public $customer_phone; 
    public $date_message_seen_employee;
    public $date_message_seen_customer;
    public $date_message_delivered_employee;
    public $date_message_delivered_customer;
    public $date_message_writing_employee;
    public $date_message_writing_customer;
    public $date_message_last; 
    public $date_message_last_customer;  
    public $date_mail_last;
    public $latest_ip;
    public $rating;
    public $enable_sound;
    public $latest_online;
    public $datetime_added;
    public $browser_name;
    public $id_departments;
    public $id_departments_wait;
    public $id_employee;
    public $id_employee_wait;
    public $id_tranfer;
    public $date_accept;
    public $end_chat;
    public $message_deleted;
    public $message_edited;
    public $employee_message_deleted;
    public $employee_message_edited;
    public $replied;    
    public $current_url;
    public $http_referer;
    public $chatref;
    public $note;
    public $id_ticket;
    public static $definition = array(
		'table' => 'ets_livechat_conversation',
		'primary' => 'id_conversation',
		'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT),    
            'id_ticket'=> array('type' => self::TYPE_INT),
            'id_shop' => array('type' => self::TYPE_INT),      
            'blocked' => array('type' => self::TYPE_INT), 
            'archive' =>array('type' => self::TYPE_INT), 
            'captcha_enabled'=>array('type'=>self::TYPE_INT),
            'customer_writing' => array('type' => self::TYPE_INT),
            'employee_writing' => array('type' => self::TYPE_INT),  
            'customer_name' => array('type' => self::TYPE_STRING),            
            'customer_email' => array('type' => self::TYPE_STRING),
            'customer_phone' => array('type' => self::TYPE_STRING),
            'latest_ip' => array('type' => self::TYPE_STRING),
            'browser_name'=>array('type'=>self::TYPE_STRING),
            'id_departments'=>array('type'=>self::TYPE_INT),
            'id_departments_wait'=>array('type'=>self::TYPE_INT),
            'id_employee'=>array('type'=>self::TYPE_INT),
            'id_employee_wait'=>array('type'=>self::TYPE_INT),
            'id_tranfer'=>array('type'=>self::TYPE_INT),
            'date_accept' =>array('type' => self::TYPE_DATE),
            'latest_online' => array('type' => self::TYPE_DATE),
            'datetime_added' => array('type' => self::TYPE_DATE),  
            'date_message_seen_employee' => array('type' => self::TYPE_DATE), 
            'date_message_seen_customer' => array('type' => self::TYPE_DATE),
            'date_message_writing_employee' => array('type' => self::TYPE_DATE), 
            'date_message_writing_customer' => array('type' => self::TYPE_DATE), 
            'date_message_delivered_employee' => array('type' => self::TYPE_DATE), 
            'date_message_delivered_customer' => array('type' => self::TYPE_DATE),
            'date_mail_last' => array('type'=>self::TYPE_DATE),
            'rating' => array('type'=>self::TYPE_INT),
            'enable_sound'=>array('type'=>self::TYPE_INT),
            'end_chat'=>array('type'=>self::TYPE_INT),
            'message_edited'=>array('type'=>self::TYPE_STRING),
            'message_deleted'=>array('type'=>self::TYPE_STRING),	
            'employee_message_deleted'=>array('type'=>self::TYPE_STRING),
            'employee_message_edited'=>array('type'=>self::TYPE_STRING),
            'date_message_last' => array('type' => self::TYPE_DATE),   
            'date_message_last_customer'=> array('type' => self::TYPE_DATE), 
            'replied' => array('type' => self::TYPE_INT),  
            'http_referer' => array('type' => self::TYPE_STRING),
            'current_url' => array('type' => self::TYPE_STRING),  
            'chatref' => array('type' => self::TYPE_INT),  
            'note' => array('type' => self::TYPE_STRING),   
        )
	);
    public function __construct($id = null, $idLang = null)
    {
        parent::__construct($id, $idLang);
        $this->context = Context::getContext();
    }
    public static function getCustomerConversation()
    {
        $context = Context::getContext();
        if(isset($context->customer->id) && (int)$context->customer->id) 
        {
            return self::getConversationByIdCustomer((int)$context->customer->id);
        }  
        elseif(isset($context->cookie->lc_id_conversation) && (int)$context->cookie->lc_id_conversation && Db::getInstance()->getValue('SELECT id_conversation FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_conversation='.(int)$context->cookie->lc_id_conversation))
            return new LC_Conversation((int)$context->cookie->lc_id_conversation);
        return false;
    }
    public static function getConversationByIdCustomer($id_customer)
    {
        return ($id_conversation = (int)Db::getInstance()->getValue("SELECT max(id_conversation) FROM "._DB_PREFIX_."ets_livechat_conversation WHERE id_customer=".(int)$id_customer.' ORDER BY chatref DESC')) ? new LC_Conversation($id_conversation) : false;
    }
    public function getMessages($lastedID = 0, $limit=0,$orderType = 'DESC',$extraID=0)
    {
        return $this->id ? LC_Message::getMessages($this->id,$lastedID,$limit,$orderType,$extraID) : false;
    }
    public static function isUsedField($fieldName)
    {
        return ($fields = explode(',',Configuration::get('ETS_LC_CHAT_FIELDS'))) && is_array($fields) && in_array($fieldName,$fields);           
    }
    public static function isRequiredField($fieldName)
    {        
        if(!self::isUsedField($fieldName))
            return false;
        return ($fields = explode(',',Configuration::get('ETS_LC_CHAT_FIELDS_REQUIRED'))) && is_array($fields) && in_array($fieldName,$fields);           
    }
    public static function getListConversations($all=true,$archive=false,$customer_name='',$lasttime='')
    {
        $ets_livechat= new Ets_livechat();
        $declines = $ets_livechat->getDeclineConversation();
        $count_conversation = Tools::getValue('count_conversation',20);
        if($all)
        {
            $sql = 'SELECT lc.*,CONCAT(c.firstname," ",c.lastname) as fullname  FROM '._DB_PREFIX_.'ets_livechat_conversation lc
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments ld ON (ld.id_departments=lc.id_departments OR ld.id_departments=lc.id_departments_wait)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee lde ON (ld.id_departments=lde.id_departments)
                LEFT JOIN '._DB_PREFIX_.'employee e ON (e.id_employee = lde.id_employee)
                LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer=lc.id_customer)
                WHERE 1
                '.(!$ets_livechat->all_shop?'  AND lc.id_shop="'.(int)Context::getContext()->shop->id.'"':'')
                .(Context::getContext()->employee->id_profile!= 1 ? ' AND (e.id_employee="'.(int)Context::getContext()->employee->id.'" OR lc.id_departments=0 OR ld.all_employees=1 OR lc.id_departments_wait=-1)':'')
                .(Configuration::get('ETS_LC_STAFF_ACCEPT') && Context::getContext()->employee->id_profile!=1 ? ' AND (lc.id_employee=0 OR lc.id_employee="'.(int)Context::getContext()->employee->id.'" OR lc.id_employee_wait="'.(int)Context::getContext()->employee->id.'" OR lc.id_employee_wait=-1)':'')
                .(isset($declines) && $declines ? ' AND lc.id_conversation NOT IN ('.implode(',',array_map('intval',$declines)).')':'')
                .($lasttime ? ' AND date_message_last_customer < "'.pSQL($lasttime).'"': '')
                .' GROUP BY lc.id_conversation ORDER BY date_message_last_customer DESC LIMIT 0,'.(int)$count_conversation;
            if($customer_name)
            {
                $sql = 'SELECT lc.*,CONCAT(c.firstname," ",c.lastname) as fullname,m.message,m.datetime_added FROM '._DB_PREFIX_.'ets_livechat_conversation lc
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments ld ON (ld.id_departments=lc.id_departments)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee lde ON (ld.id_departments=lde.id_departments OR ld.id_departments=lc.id_departments_wait)
                LEFT JOIN '._DB_PREFIX_.'employee e ON (e.id_employee = lde.id_employee)
                LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer=lc.id_customer)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_message m ON(m.id_conversation=lc.id_conversation AND m.id_employee=0)
                WHERE '
                .(!$ets_livechat->all_shop?' lc.id_shop="'.(int)Context::getContext()->shop->id.'" AND ':'')
                .'( CONCAT(c.firstname," ",c.lastname) LIKE "%'.pSQL($customer_name).'%" OR lc.customer_name like "%'.pSQL($customer_name).'%" OR m.message like "%'.pSQL($customer_name).'%")'
                .(Context::getContext()->employee->id_profile!= 1 ? ' AND (e.id_employee="'.(int)Context::getContext()->employee->id.'" OR lc.id_departments=0 OR ld.all_employees=1 OR lc.id_departments_wait=-1)':'')
                .(Configuration::get('ETS_LC_STAFF_ACCEPT') && Context::getContext()->employee->id_profile!=1 ? ' AND (lc.id_employee=0 OR lc.id_employee="'.(int)Context::getContext()->employee->id.'" OR lc.id_employee_wait="'.(int)Context::getContext()->employee->id.'" OR lc.id_employee_wait=-1)':'') 
                .(isset($declines) && $declines ? ' AND lc.id_conversation NOT IN ('.implode(',',array_map('intval',$declines)).')':'')
                .($lasttime ? ' AND date_message_last_customer < "'.pSQL($lasttime).'"': '')
                .' GROUP BY lc.id_conversation ORDER BY date_message_last_customer DESC LIMIT 0,'.(int)$count_conversation;
                $conversations = Db::getInstance()->executeS($sql);
                if($conversations)
                {
                    foreach($conversations as &$conversation)
                    {
                        if($conversation['id_customer'] && Tools::strpos(Tools::strtolower($conversation['fullname']),Tools::strtolower($customer_name))===false && Tools::strpos(Tools::strtolower($conversation['message']),Tools::strtolower($customer_name))===false)
                        {
                            unset($conversation);
                        }
                        else
                        {
                            LC_Conversation::updateMessageStattus($conversation['id_conversation'],true,false,false,'employee');
                            if(LC_Conversation::isCustomerOnline($conversation['id_conversation']))
                                $conversation['online']=1;
                            else
                                $conversation['online']=0;
                            $conversation['wait_accept']=Ets_livechat::checkWaitAccept($conversation['id_conversation']);
                            $conversation['has_changed']=Ets_livechat::checkHasChanged($conversation['id_conversation']);
                        }
                    }
                } 
                return $conversations;
            }
        }
        else
        {
            $sql = 'SELECT lc.*,CONCAT(c.firstname," ",c.lastname) as fullname  FROM '._DB_PREFIX_.'ets_livechat_conversation lc
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments ld ON (ld.id_departments=lc.id_departments OR ld.id_departments=lc.id_departments_wait)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee lde ON (ld.id_departments=lde.id_departments)
                LEFT JOIN '._DB_PREFIX_.'employee e ON (e.id_employee = lde.id_employee)
                LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer=lc.id_customer)
                WHERE '
                .(!$ets_livechat->all_shop ? ' lc.id_shop="'.(int)Context::getContext()->shop->id.'" AND ':'')
                .' archive ='.($archive?'1':'0')
                .(Context::getContext()->employee->id_profile!= 1 ? ' AND (e.id_employee="'.(int)Context::getContext()->employee->id.'" OR lc.id_departments=0 OR ld.all_employees=1 OR lc.id_departments_wait=-1)':'')
                .(Configuration::get('ETS_LC_STAFF_ACCEPT')  && Context::getContext()->employee->id_profile!=1 ? ' AND (lc.id_employee=0 OR lc.id_employee="'.(int)Context::getContext()->employee->id.'" OR lc.id_employee_wait="'.(int)Context::getContext()->employee->id.'" OR lc.id_employee_wait=-1)':'')
                .(isset($declines) && $declines ? ' AND lc.id_conversation NOT IN ('.implode(',',array_map('intval',$declines)).')':'')
                .($lasttime ? ' AND date_message_last_customer < "'.pSQL($lasttime).'"': '')
                .' GROUP BY lc.id_conversation ORDER BY date_message_last_customer DESC LIMIT 0,'.(int)$count_conversation;
        }
        $conversations = Db::getInstance()->executeS($sql);
        if($conversations)
        {
            foreach($conversations as &$conversation)
            {
                LC_Conversation::updateMessageStattus($conversation['id_conversation'],true,false,false,'employee');
                if(LC_Conversation::isCustomerOnline($conversation['id_conversation']))
                    $conversation['online']=1;
                else
                    $conversation['online']=0;
                $conversation['wait_accept']=Ets_livechat::checkWaitAccept($conversation['id_conversation']);
                $conversation['has_changed']=Ets_livechat::checkHasChanged($conversation['id_conversation']);
            }
        } 
        return $conversations;
    }
    public static function getConversations($all=true,$archive=false,$customer_name='',$lasttime='')
    {
        $ets_livechat= new Ets_livechat();
        $declines = $ets_livechat->getDeclineConversation();
        $count_conversation = Tools::getValue('count_conversation',20);
        if($all)
        {
            $sql = 'SELECT lc.*,CONCAT(c.firstname," ",c.lastname) as fullname  FROM '._DB_PREFIX_.'ets_livechat_conversation lc
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments ld ON (ld.id_departments=lc.id_departments OR ld.id_departments=lc.id_departments_wait)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee lde ON (ld.id_departments=lde.id_departments)
                LEFT JOIN '._DB_PREFIX_.'employee e ON (e.id_employee = lde.id_employee)
                LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer=lc.id_customer)
                WHERE 1
                '.(!$ets_livechat->all_shop?'  AND lc.id_shop="'.(int)Context::getContext()->shop->id.'"':'')
                .(Context::getContext()->employee->id_profile!= 1 ? ' AND (e.id_employee="'.(int)Context::getContext()->employee->id.'" OR lc.id_departments=0 OR ld.all_employees=1 OR lc.id_departments_wait=-1)':'')
                .(Configuration::get('ETS_LC_STAFF_ACCEPT') && Context::getContext()->employee->id_profile!=1 ? ' AND (lc.id_employee=0 OR lc.id_employee="'.(int)Context::getContext()->employee->id.'" OR lc.id_employee_wait="'.(int)Context::getContext()->employee->id.'" OR lc.id_employee_wait=-1)':'')
                .(isset($declines) && $declines ? ' AND lc.id_conversation NOT IN ('.implode(',',array_map('intval',$declines)).')':'')
                .($lasttime ? ' AND date_message_last_customer < "'.pSQL($lasttime).'"': '')
                .' GROUP BY lc.id_conversation ORDER BY date_message_last_customer DESC LIMIT 0,'.(int)$count_conversation;
            if($customer_name)
            {
                $sql = 'SELECT lc.*,CONCAT(c.firstname," ",c.lastname) as fullname,m.message,m.datetime_added FROM '._DB_PREFIX_.'ets_livechat_conversation lc
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments ld ON (ld.id_departments=lc.id_departments OR ld.id_departments=lc.id_departments_wait)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee lde ON (ld.id_departments=lde.id_departments)
                LEFT JOIN '._DB_PREFIX_.'employee e ON (e.id_employee = lde.id_employee)
                LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer=lc.id_customer)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_message m ON(m.id_conversation=lc.id_conversation AND m.id_employee=0)
                WHERE '.(!$ets_livechat->all_shop?' lc.id_shop="'.(int)Context::getContext()->shop->id.'" AND ':'').'( CONCAT(c.firstname," ",c.lastname) LIKE "%'.pSQL($customer_name).'%" OR lc.customer_name like "%'.pSQL($customer_name).'%" OR m.message like "%'.pSQL($customer_name).'%")'
                .(Context::getContext()->employee->id_profile!= 1 ? ' AND (e.id_employee="'.(int)Context::getContext()->employee->id.'" OR lc.id_departments=0 OR ld.all_employees=1 OR lc.id_departments_wait=-1)':'')
                .(Configuration::get('ETS_LC_STAFF_ACCEPT') && Context::getContext()->employee->id_profile!=1 ? ' AND (lc.id_employee=0 OR lc.id_employee="'.(int)Context::getContext()->employee->id.'" OR lc.id_employee_wait="'.(int)Context::getContext()->employee->id.'" OR lc.id_employee_wait=-1)':'')
                .(isset($declines) && $declines ? ' AND lc.id_conversation NOT IN ('.implode(',',array_map('intval',$declines)).')':'')
                .($lasttime ? ' AND date_message_last_customer < "'.pSQL($lasttime).'"': '')
                .' GROUP BY lc.id_conversation ORDER BY date_message_last_customer DESC LIMIT 0,'.(int)$count_conversation;
                $conversations = Db::getInstance()->executeS($sql);
                if($conversations)
                {
                    foreach($conversations as &$conversation)
                    {
                        if($conversation['id_customer'] && Tools::strpos(Tools::strtolower($conversation['fullname']),Tools::strtolower($customer_name))===false && Tools::strpos(Tools::strtolower($conversation['message']),Tools::strtolower($customer_name))===false)
                        {
                            unset($conversation);
                        }
                        else
                        {
                            LC_Conversation::updateMessageStattus($conversation['id_conversation'],true,false,false,'employee');
                            if(LC_Conversation::isCustomerOnline($conversation['id_conversation']))
                                $conversation['online']=1;
                            else
                                $conversation['online']=0;
                            $conversation['wait_accept']=Ets_livechat::checkWaitAccept($conversation['id_conversation']);
                            $conversation['count_message_not_seen'] = LC_Conversation::getMessagesEmployeeNotSeen($conversation['id_conversation']);
                            if(Tools::strpos(Tools::strtolower($conversation['message']),Tools::strtolower($customer_name))!==false)
                            {
                                $conversation['last_message'] = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation="'.(int)$conversation['id_conversation'].'" AND id_employee=0 AND message like "%'.pSQL($customer_name).'%" ORDER BY id_message DESC');
                            }
                            else
                                $conversation['last_message'] = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation="'.(int)$conversation['id_conversation'].'" AND id_employee=0 ORDER BY id_message DESC');
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
                            if(Tools::strpos(Tools::strtolower($conversation['customer_name']),Tools::strtolower($customer_name))!==false)
                            {
                                $conversation['customer_name'] = str_replace(Tools::strtolower($customer_name),'<span class="search_text">'.$customer_name.'</span>',Tools::strtolower($conversation['customer_name']));
                            }
                            if(Tools::strpos(Tools::strtolower($conversation['fullname']),Tools::strtolower($customer_name))!==false)
                            {
                                $conversation['fullname'] = str_replace(Tools::strtolower($customer_name),'<span class="search_text">'.$customer_name.'</span>',Tools::strtolower($conversation['fullname']));
                            }
                            if(Tools::strpos(Tools::strtolower($conversation['last_message']['message']),Tools::strtolower($customer_name))!==false)
                            {
                                $conversation['last_message']['message'] = str_replace(Tools::strtolower($customer_name),'<span class="search_text">'.$customer_name.'</span>',Tools::strtolower($conversation['last_message']['message']));
                            }
                            if($ets_livechat->emotions)
                            {
                                foreach($ets_livechat->emotions as $key=> $emotion)
                                {
                                    $img = '<span title="'.$emotion['title'].'"><img src="'.$ets_livechat->url_module.'views/img/emotions/'.$emotion['img'].'"></span>';
                                    $conversation['last_message']['message'] = str_replace(array(Tools::strtolower($key),$key),array($img,$img),$conversation['last_message']['message']);
                                }
                            }
                            if($conversation['last_message'] && $attachment= Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_download WHERE id_download="'.(int)$conversation['last_message']['type_attachment'].'" AND id_message="'.(int)$conversation['last_message']['id_message'].'"'))
                            {
                                $context=Context::getContext();
                                if(isset($context->employee) && $context->employee->id)
                                    $linkdownload= $ets_livechat->getBaseLink().'/modules/ets_livechat/download.php?downloadfile='.md5(_COOKIE_KEY_.$conversation['last_message']['type_attachment']);
                                else
                                    $linkdownload = $context->link->getModuleLink('ets_livechat','download',array('downloadfile'=>md5(_COOKIE_KEY_.$conversation['last_message']['type_attachment'])));
                                $conversation['last_message']['message'] .= ($conversation['last_message']['message'] ? '<br />':''). '<span class="file_message"><a class="file_sent" href="'.$linkdownload.'" target="_blank">'.$conversation['last_message']['name_attachment'].($attachment['file_size'] ? '<span class="file_size"> ('.$attachment['file_size'].' MB)</span>':'').'</a></span>';
                            }
                        }
                    }
                } 
                return $conversations;
            }
        }
        else
        {
            $sql = 'SELECT lc.*,CONCAT(c.firstname," ",c.lastname) as fullname  FROM '._DB_PREFIX_.'ets_livechat_conversation lc
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments ld ON (ld.id_departments=lc.id_departments OR ld.id_departments=lc.id_departments_wait)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee lde ON (ld.id_departments=lde.id_departments)
                LEFT JOIN '._DB_PREFIX_.'employee e ON (e.id_employee = lde.id_employee)
                LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer=lc.id_customer)
                WHERE '
                .(!$ets_livechat->all_shop ? ' lc.id_shop="'.(int)Context::getContext()->shop->id.'" AND ':'')
                .' archive ='.($archive?'1':'0')
                .(Context::getContext()->employee->id_profile!= 1 ? ' AND (e.id_employee="'.(int)Context::getContext()->employee->id.'" OR lc.id_departments=0 OR ld.all_employees=1 OR lc.id_departments_wait=-1)':'')
                .(Configuration::get('ETS_LC_STAFF_ACCEPT')  && Context::getContext()->employee->id_profile!=1 ? ' AND (lc.id_employee=0 OR lc.id_employee="'.(int)Context::getContext()->employee->id.'" OR lc.id_employee_wait="'.(int)Context::getContext()->employee->id.'" OR lc.id_employee_wait=-1)':'')
                .(isset($declines) && $declines ? ' AND lc.id_conversation NOT IN ('.implode(',',array_map('intval',$declines)).')':'')
                .($lasttime ? ' AND date_message_last_customer < "'.pSQL($lasttime).'"': '')
                .' GROUP BY lc.id_conversation ORDER BY date_message_last_customer DESC LIMIT 0,'.(int)$count_conversation;
        }
        $conversations = Db::getInstance()->executeS($sql);
        if($conversations)
        {
            foreach($conversations as &$conversation)
            {
                
                LC_Conversation::updateMessageStattus($conversation['id_conversation'],true,false,false,'employee');
                if(LC_Conversation::isCustomerOnline($conversation['id_conversation']))
                    $conversation['online']=1;
                else
                    $conversation['online']=0;
                    $conversation['wait_accept']=Ets_livechat::checkWaitAccept($conversation['id_conversation']);
                $conversation['has_changed']=Ets_livechat::checkHasChanged($conversation['id_conversation']);
                $conversation['count_message_not_seen'] = LC_Conversation::getMessagesEmployeeNotSeen($conversation['id_conversation']);
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
                    if($ets_livechat->emotions)
                    {
                        foreach($ets_livechat->emotions as $key=> $emotion)
                        {
                            $img = '<span title="'.$emotion['title'].'"><img src="'.$ets_livechat->url_module.'views/img/emotions/'.$emotion['img'].'"></span>';
                            $conversation['last_message']['message'] = str_replace(array(Tools::strtolower($key),$key),array($img,$img),$conversation['last_message']['message']);
                        }
                    }
                    if($conversation['last_message'] && $attachment= Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_download WHERE id_download="'.(int)$conversation['last_message']['type_attachment'].'" AND id_message="'.(int)$conversation['last_message']['id_message'].'"'))
                    {
                        $context=Context::getContext();
                        if(isset($context->employee) && $context->employee->id)
                            $linkdownload= $ets_livechat->getBaseLink().'/modules/ets_livechat/download.php?downloadfile='.md5(_COOKIE_KEY_.$conversation['last_message']['type_attachment']);
                        else
                            $linkdownload = $context->link->getModuleLink('ets_livechat','download',array('downloadfile'=>md5(_COOKIE_KEY_.$conversation['last_message']['type_attachment'])));
                        $conversation['last_message']['message'] .= ($conversation['last_message']['message'] ? '<br />':'').'<span class="file_message"><a class="file_sent" href="'.$linkdownload.'" target="_blank">'.$conversation['last_message']['name_attachment'].'</a>'.($attachment['file_size'] ? '<span class="file_size"> ('.$attachment['file_size'].' MB)</span>':'').'</span>';
                    }
                }
                else
                    unset($conversation);
                
            }
        } 
        return $conversations;
    }
    public static function updateMessageStattus($id_conversation,$delevered=false,$viewed=false,$wirting=false,$type='customer')
    {
        $date= date('Y-m-d H:i:s');
        if($id_conversation && ($delevered || $viewed|| $wirting))
        {
            $conversation= new LC_Conversation($id_conversation);
            if($type=='customer')
            {
                if($viewed)
                    $conversation->date_message_seen_customer= $date;
                if($delevered)
                    $conversation->date_message_delivered_customer= $date;
                if($wirting)
                    $conversation->date_message_writing_customer = $date;
            }
            else
            {
                if($viewed)
                    $conversation->date_message_seen_employee=$date;
                if($delevered)
                    $conversation->date_message_delivered_employee=$date;
                if($wirting)
                {
                    $conversation->employee_writing=1;
                    $conversation->date_message_writing_employee = $date;
                }
                    
            }
            $conversation->update();
        }
    }
    public static function isCustomerSeen($id_conversation)
    {
        $date_lastview = Db::getInstance()->getValue('SELECT date_message_seen_customer FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_conversation ='.(int)$id_conversation);
        if($date_lastview && $date_lastview!='0000-00-00 00:00:00')
        {
            $messages = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation="'.(int)$id_conversation.'" AND id_employee!=0 AND datetime_added >"'.pSQL($date_lastview).'"');
            if(Count($messages))
                return false;
            else
                return true;
        }
        return false;
    }
    public static function isCustomerDelivered($id_conversation)
    {
        $date_delivered = Db::getInstance()->getValue('SELECT date_message_delivered_customer FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_conversation ='.(int)$id_conversation);
        if($date_delivered && $date_delivered!='0000-00-00 00:00:00')
        {
            $messages = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation="'.(int)$id_conversation.'" AND id_employee!=0 AND datetime_added >"'.pSQL($date_delivered).'"');
            if(Count($messages))
                return false;
            else
                return true;
        }
        return false;
    }
    public static function isCustomerWriting($id_conversation)
    {
        $date_writing = Db::getInstance()->getValue('SELECT date_message_writing_customer FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_conversation ='.(int)$id_conversation.' AND customer_writing=1');
        $refresh_speed=((int)Configuration::get('ETS_LC_TIME_OUT')+(int)Configuration::get('ETS_LC_TIME_OUT_BACK_END'))/1000;
        if($date_writing && $date_writing!='0000-00-00 00:00:00')
        {
            if(strtotime('NOW') - strtotime($date_writing)<=$refresh_speed)
            {
                return true;
            }
        }
        return false;
    }
    public static function isCustomerSent($id_conversation)
    {
        $mesages = Db::getInstance()->executeS('SELECT * FROm '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation="'.(int)$id_conversation.'" AND id_employee!=0');
        if(count($mesages))
            return true;
        return false;
    }
    public static function isEmployeeSeen($id_conversation)
    {
        $date_lastview = Db::getInstance()->getValue('SELECT date_message_seen_employee FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_conversation ='.(int)$id_conversation);
        if($date_lastview && $date_lastview!='0000-00-00 00:00:00')
        {
            $messages = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation="'.(int)$id_conversation.'" AND id_employee=0 AND datetime_added >"'.pSQL($date_lastview).'"');
            if(count($messages))
                return false;
            else
                return true;
        }
        return false;
    }
    public static function isEmployeeDelivered($id_conversation)
    {
        $date_delivered = Db::getInstance()->getValue('SELECT date_message_delivered_employee FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_conversation ='.(int)$id_conversation);
        if($date_delivered && $date_delivered!='0000-00-00 00:00:00')
        {
            $messages = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation="'.(int)$id_conversation.'" AND id_employee=0 AND datetime_added >"'.pSQL($date_delivered).'"');
            if(count($messages))
                return false;
            else
                return true;
        }
        return false;
    }
    public static function isEmployeeSent($id_conversation)
    {
        $mesages = Db::getInstance()->executeS('SELECT * FROm '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation="'.(int)$id_conversation.'" AND id_employee=0');
        if(count($mesages))
            return true;
        return false;
    }
    public static function isEmployeeWriting($id_conversation)
    {
        $refresh_speed=((int)Configuration::get('ETS_LC_TIME_OUT')+(int)Configuration::get('ETS_LC_TIME_OUT_BACK_END'))/1000;
        $date_writing = Db::getInstance()->getValue('SELECT date_message_writing_employee FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_conversation ='.(int)$id_conversation.' AND employee_writing=1');
        if($date_writing && $date_writing!='0000-00-00 00:00:00')
        {
            if(strtotime('now') - strtotime($date_writing)<=$refresh_speed)
            {
                return true;
            }
        }
        return false;
    }
    public function isJquestAjax()
    {
        $timeout = (int)Configuration::get('ETS_LC_ONLINE_TIMEOUT')*60;
        if((int)Configuration::get('ETS_LC_AUTO_FRONTEND_SPEED'))
            $timeout =$timeout*4;
        $timeout2= ceil($timeout/2);
        $timeout3 = ceil($timeout/3);
        $timeout4 = ceil($timeout/4);
        if(strtotime("now") < strtotime($this->date_message_last)+$timeout)
        {
            if(!(int)Configuration::get('ETS_LC_AUTO_FRONTEND_SPEED'))
                return 1;
            if(strtotime("now") < strtotime($this->date_message_last)+$timeout4)
                return 1;
            if(strtotime("now") < strtotime($this->date_message_last)+$timeout3)
                return 2;
            if(strtotime("now") < strtotime($this->date_message_last)+$timeout2)
                return 3;
            return 4;
        }
        else
            return 0;
    }
    public static function getMessagesEmployeeNotSeen($id_conversation)
    {
        $conversation = new LC_Conversation($id_conversation);
        $date_lastview =$conversation->date_message_seen_employee;
        $context= Context::getContext();
        if($date_lastview && $date_lastview!='0000-00-00 00:00:00')
        {
            $sql ='SELECT COUNT(DISTINCT m.id_message) FROM '._DB_PREFIX_.'ets_livechat_message m'; 
            if(isset($context->employee) && isset($context->employee->id_profile) && $context->employee->id_profile!=1 && Ets_livechat::checkDepartments())
            {
                $sql .=' LEFT JOIN '._DB_PREFIX_.'ets_livechat_conversation c ON (c.id_conversation=m.id_conversation)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments d ON (d.id_departments=c.id_departments)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (de.id_departments=c.id_departments)';
            } 
            $sql .=' WHERE m.id_conversation="'.(int)$id_conversation.'" AND m.id_employee=0 AND m.datetime_added >"'.pSQL($date_lastview).'"';
            if(isset($context->employee) && isset($context->employee->id_profile) &&  $context->employee->id_profile!=1 && Ets_livechat::checkDepartments())
                $sql .=' AND (d.all_employees=1 OR c.id_departments=0 OR de.id_employee="'.(int)$context->employee->id.'")';
            return Db::getInstance()->getValue($sql);
        }
        else
        {
            $sql ='SELECT COUNT(DISTINCT m.id_message) FROM '._DB_PREFIX_.'ets_livechat_message m ';
            if(isset($context->employee) && isset($context->employee->id_profile) && $context->employee->id_profile!=1 && Ets_livechat::checkDepartments())
            {
                $sql .=' LEFT JOIN '._DB_PREFIX_.'ets_livechat_conversation c ON (c.id_conversation=m.id_conversation)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments d ON (d.id_departments=c.id_departments)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (de.id_departments=c.id_departments)';
            } 
            $sql .='WHERE m.id_conversation="'.(int)$id_conversation.'" AND m.id_employee=0';
            if(isset($context->employee) && isset($context->employee->id_profile) &&  $context->employee->id_profile!=1 && Ets_livechat::checkDepartments())
                $sql .=' AND (d.all_employees=1 OR c.id_departments=0 OR de.id_employee="'.(int)$context->employee->id.'")';
            return Db::getInstance()->getValue($sql);
        }
    }
    public static function getTotalMessageNoSeen()
    {
        $context= Context::getContext();
        if(Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && Shop::getContext() == Shop::CONTEXT_ALL)
        {
            $all_shop = true;
        }
        else
            $all_shop=false; 
        $sql = 'SELECT * FROM '._DB_PREFIX_.'ets_livechat_conversation'.(!$all_shop ? ' WHERE id_shop= "'.(int)$context->shop->id.'"':'');
        $conversations = Db::getInstance()->executeS($sql);
        $total=0;
        if($conversations)
        {
            foreach($conversations as $conversation)
            {
                $total +=LC_Conversation::getMessagesEmployeeNotSeen($conversation['id_conversation']);
            }
        }
        return $total;
    }
    public static function getMessagesCustomerNotSeen($id_conversation)
    {
        $date_lastview = Db::getInstance()->getValue('SELECT date_message_seen_customer FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_conversation ='.(int)$id_conversation);
        if($date_lastview && $date_lastview!='0000-00-00 00:00:00')
        {
            $messages = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation="'.(int)$id_conversation.'" AND id_employee!=0 AND datetime_added >"'.pSQL($date_lastview).'"');
            if(count($messages))
                return count($messages);
            else
                return 0;
        }
        else
        {
            $messages = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation="'.(int)$id_conversation.'" AND id_employee!=0');
            if(count($messages))
                return count($messages);
            else
                return 0;
        }
            
    }
    public static function isCustomerOnline($id_conversation)
    {
        $timeout= (int)Configuration::get('ETS_LC_TIME_OUT_BACK_END')*3/1000+(int)Configuration::get('ETS_LC_TIME_OUT')*3/1000;
        $conversation = new LC_Conversation($id_conversation);
        if($conversation->end_chat)
            return false;
        return  strtotime('now') < strtotime($conversation->latest_online)+$timeout;
    }
    public static function sendEmail($id_conversation,$attachments=array())
    {
        if(!Configuration::get('ETS_LC_SEND_MAIL_WHEN_SEND_MG') || Ets_livechat::isAdminOnlineNoForce()||!Configuration::get('ETS_LC_MAIL_TO'))
            return false;
        $send_mail=false;
        $messages=array();
        $when_send_email =explode(',',Configuration::get('ETS_LC_SEND_MAIL'));
        if(in_array('first_message',$when_send_email))
        {
            $messages = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation="'.(int)$id_conversation.'"');
            if(count($messages)==1)
                $send_mail=true;
        }
        if(in_array('affter_a_centaint_time',$when_send_email))
        {
            $hours = (float)Configuration::get('ETS_CENTAINT_TIME_SEND_EMAIL');
            $timeout = $hours*60*60;
            $date_mail_last = Db::getInstance()->getValue('SELECT date_mail_last FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_conversation='.(int)$id_conversation);
            if((int)$timeout>0)
            {
                $messages = Db::getInstance()->executeS("SELECT * FROM "._DB_PREFIX_."ets_livechat_message WHERE id_conversation=".(int)$id_conversation." AND datetime_added >'".pSQL(date('Y-m-d H:i:s', strtotime('-'.(int)$timeout.' seconds')))."' ORDER BY id_message");
                if($messages)
                {
                    $employee_message=true; 
                    foreach($messages as $message)
                    {
                        if($message['id_employee'])
                            $employee_message= false;
                    }
                    if($employee_message)
                    {
                        if($date_mail_last==''||$date_mail_last=='0000-00-00 00:00:00')
                        {       
                            $first_datetime_added = Db::getInstance()->getValue('SELECT datetime_added FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation='.(int)$id_conversation.' order by id_message asc');
                            if(strtotime('now') > strtotime($first_datetime_added)+$timeout)
                                $send_mail= true;
                        }
                        else{
                            if(strtotime('now') > strtotime($date_mail_last)+$timeout)
                                $send_mail= true;
                        }
                    }
                }
            }
        }
        if($send_mail && $messages)
        {
            if($messages)
            {
                $ets_livechat= new Ets_livechat();
                foreach($messages as &$message)
                {
                    if($ets_livechat->emotions)
                    {
                        foreach($ets_livechat->emotions as $key=> $emotion)
                        {
                            $img = '<span title="'.$emotion['title'].'"><img src="'.$ets_livechat->url_module.'views/img/emotions/'.$emotion['img'].'"></span>';
                            $message['message'] = str_replace(array(Tools::strtolower($key),$key),array($img,$img),$message['message']);
                        }
                    }
                }
            }
            $emails= array();
            $email_to = explode(',',Configuration::get('ETS_LC_MAIL_TO'));
            if(in_array('shop',$email_to))
                $emails[]=Configuration::get('PS_SHOP_EMAIL');
            if(in_array('employee',$email_to))
            {
                $employees = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'employee e,'._DB_PREFIX_.'employee_shop es WHERE e.id_employee=es.id_employee AND es.id_shop="'.(int)Context::getContext()->shop->id.'" AND active=1');
                if($employees)
                    foreach($employees as $employee)
                    {
                        if(!in_array($employee['email'],$emails))
                            $emails[]=$employee['email'];
                    }
            }
            if(in_array('custom',$email_to))
            {
                $emails_custom = explode(',',Configuration::get('ETS_LC_CUSTOM_EMAIL'));
                if($emails_custom)
                {
                    foreach($emails_custom as $email)
                    {
                        if(!in_array($email,$emails))
                            $emails[]=$email;
                    }
                }
            }
            
            if($emails)
            {
                $ets_livechat = new Ets_livechat();
                $template_vars =array(
                    '{messages}'=>$ets_livechat->getTemplateEmail($messages),
                    '{link_admin}' =>Configuration::get('ETS_DIRECTORY_ADMIN_URL')?'<td class="footer" style="padding:0 50px 60px;text-align:center;"><span><a href="'.Tools::getShopDomainSsl(true).Context::getContext()->shop->getBaseURI().Configuration::get('ETS_DIRECTORY_ADMIN_URL').'" style="background: #1d5353 none repeat scroll 0 0;border-radius: 40px;color: #fff;display: inline-block;font-size: 20px;font-weight: 600;padding: 12px 20px;text-decoration: none;text-transform: uppercase;">Log into back office</a></span></td>':'',
                    '{customer_info}' =>$ets_livechat->displayCucstomerInfo($id_conversation), 
                    '{year}' =>date('Y'),
                );
                $conversation = new LC_Conversation($id_conversation);
                if($conversation->id_customer)
                {
                    $customer= new Customer($conversation->id_customer);
                    $customer_name = $customer->firstname.' '.$customer->lastname;
                    $from = $customer->email;
                }
                else
                {
                    $customer_name = $conversation->customer_name;
                    $from = $conversation->customer_email?$conversation->customer_email:null;
                }
                foreach($emails as $email)
                {
                    if($email && file_exists(dirname(__FILE__).'/../mails/'.Context::getContext()->language->iso_code.'/new_message.html'))
                    {
                        Mail::Send(
        					Context::getContext()->language->id,
        					'new_message',
        					sprintf(Mail::l('New Message from %s', Context::getContext()->language->id),$customer_name),
        					$template_vars,
        					$email,
        					null,
        					$from,
        					$customer_name,
        					$attachments,
        					null,
        					dirname(__FILE__).'/../mails/',
        					null,
        					Context::getContext()->shop->id
        				);

                    }
                }
            }
        }
        return false;
    }
    public static function lastMessageIsEmployee($id_conversation) 
    {
        $message = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation="'.(int)$id_conversation.'" order by id_message desc');
        if($message['id_employee'])
            return true;
        else
            return false;
    }
    public static function getLastMessageOfEmployee($id_conversation)
    {
        $message = Db::getInstance()->getRow('SELECT m.id_message,m.message,m.id_employee,m.datetime_added,m.datetime_edited,CONCAT(e.firstname," ",e.lastname) as employee_name,e.email FROM '._DB_PREFIX_.'ets_livechat_message m, '._DB_PREFIX_.'employee e WHERE m.id_employee= e.id_employee AND m.id_conversation='.(int)$id_conversation.' ORDER BY m.datetime_added desc');
        return $message;
    }
    public static function getLevelRequestAdmin()
    {
        if(!(int)Configuration::get('ETS_LC_AUTO_BACKEND_SPEED'))
            return 1;
        $timeout = 3600;
        $timeout2= ceil($timeout/2);
        $timeout3 = ceil($timeout/3);
        $timeout4 = ceil($timeout/4);
        $date_action_last = Configuration::get('ETS_LC_DATE_ACTION_LAST');
        if(strtotime("now") < strtotime($date_action_last)+$timeout4)
            return 1;
        if(strtotime("now") < strtotime($date_action_last)+$timeout3)
            return 2;
        if(strtotime("now") < strtotime($date_action_last)+$timeout2)
            return 3;
        return 4;
    }
}
