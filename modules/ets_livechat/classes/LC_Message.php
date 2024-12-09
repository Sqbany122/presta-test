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
if(!class_exists('LC_Download') && file_exists(dirname(__FILE__).'/classes/LC_Download.php'))
    require_once(dirname(__FILE__).'/classes/LC_Download.php');
class LC_Message extends ObjectModel
{
    public $id_conversation;
    public $id_employee;
    public $delivered;
    public $message;
    public $id_product;
    public $type_attachment;
    public $name_attachment;
    public $datetime_added;
    public $datetime_edited;
    public static $definition = array(
		'table' => 'ets_livechat_message',
		'primary' => 'id_message',
		'fields' => array(
            'id_conversation' => array('type' => self::TYPE_INT),
            'id_employee' => array('type' => self::TYPE_INT),
            'id_product' => array('type' => self::TYPE_INT),
            'delivered' => array('type' => self::TYPE_INT), 
            'message' => array('type' => self::TYPE_HTML),
            'type_attachment' =>array('type'=>self::TYPE_STRING),  
            'name_attachment' =>array('type'=>self::TYPE_STRING),
            'datetime_added' => array('type' => self::TYPE_DATE),
            'datetime_edited' =>array('type'=>self::TYPE_DATE),       
        )
	);
    public static function getMessages($id_conversation,$latestID = 0,$limit = 0,$orderType = 'DESC',$extraID=0)
    {
        $context= Context::getContext();
        $conversation = new LC_Conversation($id_conversation);
        $ets_livechat= Module::getInstanceByName('ets_livechat');
        if(isset($context->employee->id) && $context->employee->id && !$ets_livechat->checkConversationEmployee($conversation,$context->employee->id))
            return array();
        if($latestID <= 0)
            $latestID = 0;
        else
        {
            if(Db::getInstance()->executeS('SELECT id_message FROM '._DB_PREFIX_.'ets_livechat_message where id_message>'.(int)$latestID))
            {
                $latestID--;
                if($limit)
                    $limit++;
            } 
        }
        $ets_livechat= new Ets_livechat();
        $messages=Db::getInstance()->executeS("
            SELECT s.name,m.*,CONCAT(e.firstname,' ',e.lastname) as employee_name,IF(c.id_customer,CONCAT(cu.firstname,' ',cu.lastname),c.customer_name) as customer_name,e.email as employee_email,IF(c.id_customer,cu.email,c.customer_email) as customer_email
            FROM "._DB_PREFIX_."ets_livechat_message m
            LEFT JOIN "._DB_PREFIX_."employee e ON m.id_employee=e.id_employee  
            LEFT JOIN "._DB_PREFIX_."ets_livechat_staff s ON s.id_employee=m.id_employee
            JOIN "._DB_PREFIX_."ets_livechat_conversation c ON m.id_conversation=c.id_conversation
            LEFT JOIN "._DB_PREFIX_."customer cu ON c.id_customer=cu.id_customer
            WHERE c.id_conversation=".(int)$id_conversation
            .($latestID ? " AND m.id_message > ".(int)$latestID : "")
            .($extraID? " AND m.id_message !='".(int)$extraID."'":"")
            ." ORDER BY m.id_message ".(in_array($orderType,array('ASC','DESC')) ? pSQL($orderType) : 'DESC')."
            ".($limit > 0 ? "LIMIT 0,".(int)$limit : ""));
        if($messages)
        {
            foreach($messages as $index=> &$message)
            {
                if($ets_livechat->emotions)
                {
                    foreach($ets_livechat->emotions as $key=> $emotion)
                    {
                        $img = '<span title="'.$emotion['title'].'"><img src="'.$ets_livechat->url_module.'views/img/emotions/'.$emotion['img'].'"></span>';
                        $message['message'] = str_replace(array(Tools::strtolower($key),$key),array($img,$img),$message['message']);
                    }
                }
                //$message = LC_Message::get_message_pre_made_message($message);
                if($message['datetime_edited']!=$message['datetime_added'])
                    $message['edited']=1;
                else
                    $message['edited']=0;
                if(date('Y-m-d')==date('Y-m-d',strtotime($message['datetime_added'])))
                {
                    $message['datetime_added'] =date('h:i A',strtotime($message['datetime_added']));
                }
                else
                {
                    if(date('Y')==date('Y',strtotime($message['datetime_added'])))
                    {
                        $message['datetime_added'] =date('d-m',strtotime($message['datetime_added'])).'<br/>'.date('h:i A',strtotime($message['datetime_added']));
                    }
                    else
                        $message['datetime_added'] =date('d-m-Y',strtotime($message['datetime_added'])).'<br/>'.date('h:i A',strtotime($message['datetime_added']));
                }
                if(date('Y-m-d')==date('Y-m-d',strtotime($message['datetime_edited'])))
                {
                    $message['datetime_edited'] =date('h:i A',strtotime($message['datetime_edited']));
                }
                else
                {
                    if(date('Y')==date('Y',strtotime($message['datetime_edited'])))
                    {
                        $message['datetime_edited'] =date('d-m h:i A',strtotime($message['datetime_edited']));
                    }
                    else
                        $message['datetime_edited'] =date('d-m-Y h:i A',strtotime($message['datetime_edited']));
                }
                if(Configuration::get('ETS_LC_DISPLAY_COMPANY_INFO')=='general' || $message['id_employee']==-1)
                {
                    $message['employee_name'] = Configuration::get('ETS_LC_COMPANY_NAME');
                }
                elseif($message['name'])
                    $message['employee_name'] = $message['name'];
                if(Configuration::get('ETS_LC_DISPLAY_AVATA'))
                {
                    if(!isset($messages[$index+1])||(isset($messages[$index+1])&& $messages[$index+1]['id_employee']))
                    {
                        $message['customer_avata'] = $ets_livechat->getAvatarCustomer($conversation->id_customer);
                        if(isset($message['customer_name']) && $message['customer_name'])
                            $message['customer_name']=$message['customer_name'];
                        else
                            $message['customer_name'] = 'Chat ID #'.$message['id_conversation'];
                    }
                    else
                        $message['customer_avata']='';
                    if(!isset($messages[$index+1])||(isset($messages[$index+1])&& !$messages[$index+1]['id_employee']))
                    {
                        $message['employee_avata']=$ets_livechat->getAvatarEmployee($message['id_employee']);
                    }
                    else
                        $message['employee_avata']='';
                }
                else
                {
                    $message['customer_avata']='';
                    $message['employee_avata']='';
                    if(!isset($messages[$index+1])||(isset($messages[$index+1])&& $messages[$index+1]['id_employee']))
                    {
                        if(isset($message['customer_name']) && $message['customer_name'])
                            $message['customer_name']=$message['customer_name'];
                        else
                            $message['customer_name'] = 'Chat ID #'.$message['id_conversation'];
                    }
                    else
                        $message['customer_name']='';
                    if(!isset($messages[$index+1])||(isset($messages[$index+1])&& !$messages[$index+1]['id_employee']))
                    {
                        $message['employee_name']=$message['employee_name'];
                    }
                    else
                        $message['employee_name']='';
                }
                unset($message['customer_email']);
                unset($message['employee_email']);
                unset($message['email']);
                if(version_compare(_PS_VERSION_, '1.6', '>='))
                {
                    if(Tools::strpos($message['message'],'http')!==false || Tools::strpos($message['message'],'https')!==false)
                    {
                        $pattern = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
                        $message['message'] = preg_replace($pattern, "<a href='$1' target='_blank'>$1</a>", $message['message']);
                    }
                }
                
                if($message['name_attachment'] && $attachment= Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_download WHERE id_download="'.(int)$message['type_attachment'].'" AND id_message="'.(int)$message['id_message'].'"'))
                {
                    $context=Context::getContext();
                    if(isset($context->employee) && $context->employee->id)
                        $linkdownload= $ets_livechat->getBaseLink().'/modules/ets_livechat/download.php?downloadfile='.md5(_COOKIE_KEY_.$message['type_attachment']);
                    else
                        $linkdownload = $context->link->getModuleLink('ets_livechat','download',array('downloadfile'=>md5(_COOKIE_KEY_.$message['type_attachment'])));
                    $message['message'] .= ($message['message'] ? '<br/>':'').'<span class="file_message"><a class="file_sent" href="'.$linkdownload.'" target="_blank">'.$message['name_attachment'].'</a>'.($attachment['file_size'] ? '<span class="file_size"> ('.$attachment['file_size'].' MB) </span>' :'').'</span>';    
                }
                if($message['id_product'])
                {
                    $message['message'] .=$ets_livechat->getProductHtml($message['id_product']);
                }
            }
        }
        return $messages;
    }
    public static function getOldMessages($id_conversation,$firstID = 0,$orderType = 'DESC')
    {
        $limit = (int)Configuration::get('ETS_LC_MSG_COUNT');
        $ets_livechat= new Ets_livechat();
        $conversation = new LC_Conversation($id_conversation);
        if($conversation->id_customer)
        $messages=Db::getInstance()->executeS("
            SELECT m.*,CONCAT(e.firstname,' ',e.lastname) as employee_name,IF(c.id_customer,CONCAT(cu.firstname,' ',cu.lastname),c.customer_name) as customer_name,e.email as employee_email,IF(c.id_customer,cu.email,c.customer_email) as customer_email
            FROM "._DB_PREFIX_."ets_livechat_message m
            LEFT JOIN "._DB_PREFIX_."employee e ON m.id_employee=e.id_employee 
            JOIN "._DB_PREFIX_."ets_livechat_conversation c ON m.id_conversation=c.id_conversation
            LEFT JOIN "._DB_PREFIX_."customer cu ON c.id_customer=cu.id_customer
            WHERE c.id_conversation=".(int)$id_conversation.($firstID ? " AND m.id_message <".(int)$firstID : "")."
            ORDER BY m.id_message ".(in_array($orderType,array('ASC','DESC')) ? pSQL($orderType) : 'DESC')."
            ".($limit > 0 ? "LIMIT 0,".(int)$limit : "")."
        ");
        if($messages)
        {
            foreach($messages as $index=> &$message)
            {
                if($ets_livechat->emotions)
                {
                    foreach($ets_livechat->emotions as $key=> $emotion)
                    {
                        $img = '<span title="'.$emotion['title'].'"><img src="'.$ets_livechat->url_module.'views/img/emotions/'.$emotion['img'].'"></span>';
                        $message['message'] = str_replace(array(Tools::strtolower($key),$key),array($img,$img),$message['message']);
                    }
                }
                //$message = LC_Message::get_message_pre_made_message($message);
                if($message['datetime_edited']!=$message['datetime_added'])
                    $message['edited']=1;
                else
                    $message['edited']=0;
                if(date('Y-m-d')==date('Y-m-d',strtotime($message['datetime_added'])))
                {
                    $message['datetime_added'] =date('h:i A',strtotime($message['datetime_added']));
                }
                else
                {
                    if(date('Y')==date('Y',strtotime($message['datetime_added'])))
                    {
                        $message['datetime_added'] =date('d-m',strtotime($message['datetime_added'])).'<br/>'.date('h:i A',strtotime($message['datetime_added']));
                    }
                    else
                        $message['datetime_added'] =date('d-m-Y',strtotime($message['datetime_added'])).'<br/>'.date('h:i A',strtotime($message['datetime_added']));
                }
                if(date('Y-m-d')==date('Y-m-d',strtotime($message['datetime_edited'])))
                {
                    $message['datetime_edited'] =date('h:i A',strtotime($message['datetime_edited']));
                }
                else
                {
                    if(date('Y')==date('Y',strtotime($message['datetime_edited'])))
                    {
                        $message['datetime_edited'] =date('d-m h:i A',strtotime($message['datetime_edited']));
                    }
                    else
                        $message['datetime_edited'] =date('d-m-Y h:i A',strtotime($message['datetime_edited']));
                }
                if(Configuration::get('ETS_LC_DISPLAY_COMPANY_INFO')=='general' || $message['id_employee']==-1)
                {
                    $message['employee_name'] = Configuration::get('ETS_LC_COMPANY_NAME');
                }
                if(COnfiguration::get('ETS_LC_DISPLAY_AVATA'))
                {
                    if(!isset($messages[$index+1])||(isset($messages[$index+1])&& $messages[$index+1]['id_employee']))
                    {
                        $message['customer_avata'] = $ets_livechat->getAvatarCustomer($conversation->id_customer);
                        if(isset($message['customer_name']) && $message['customer_name'])
                            $message['customer_name']=$message['customer_name'];
                        else
                            $message['customer_name'] = 'Chat ID #'.$message['id_conversation'];
                    }
                    else
                        $message['customer_avata']='';
                    if(!isset($messages[$index+1])||(isset($messages[$index+1])&& !$messages[$index+1]['id_employee']))
                    {
                        $message['employee_avata']=$ets_livechat->getAvatarEmployee($message['id_employee']);
                    }
                    else
                    {
                        $message['employee_avata']='';
                        $message['employee_name']='';
                    }
                        
                }
                else
                {
                    
                    $message['customer_avata']='';
                    $message['employee_avata']='';
                    if(!isset($messages[$index+1])||(isset($messages[$index+1])&& $messages[$index+1]['id_employee']))
                    {
                        if(isset($message['customer_name']) && $message['customer_name'])
                            $message['customer_name']=$message['customer_name'];
                        else
                            $message['customer_name'] = 'Chat ID #'.$message['id_conversation'];
                    }
                    else
                        $message['customer_name']='';
                    if(!isset($messages[$index+1])||(isset($messages[$index+1])&& !$messages[$index+1]['id_employee']))
                    {
                        $message['employee_name']=$message['employee_name'];
                    }
                    else
                        $message['employee_name']='';
                }
                if(Tools::strpos($message['message'],'http')!==false || Tools::strpos($message['message'],'https')!==false)
                {
                    $pattern = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
                    $message['message'] = preg_replace($pattern, "<a href='$1' target='_blank'>$1</a>", $message['message']);
                }
                if($message['name_attachment'] && $attachment= Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_download WHERE id_download="'.(int)$message['type_attachment'].'" AND id_message="'.(int)$message['id_message'].'"'))
                {
                    $context=Context::getContext();
                    if(isset($context->employee) && $context->employee->id)
                        $linkdownload= $ets_livechat->getBaseLink().'/modules/ets_livechat/download.php?downloadfile='.md5(_COOKIE_KEY_.$message['type_attachment']);
                    else
                        $linkdownload = $context->link->getModuleLink('ets_livechat','download',array('downloadfile'=>md5(_COOKIE_KEY_.$message['type_attachment'])));
                    $message['message'] .= ($message['message'] ? '<br/>':''). '<span class="file_message"><a class="file_sent" href="'.$linkdownload.'" target="_blank">'.$message['name_attachment'].'</a>'.($attachment['file_size'] ? '<span class="file_size"> ('.$attachment['file_size'].' MB)</span>':'').'</span>';
                }
                if($message['id_product'])
                {
                    $message['message'] .=$ets_livechat->getProductHtml($message['id_product']);
                }
                
            }
        }
        return $messages;
    }
    public static function getMessage($id_message=0,$orderby='DESC'){
        
        $context= Context::getContext();
        $ets_livechat= Module::getInstanceByName('ets_livechat');
        $declines= $ets_livechat->getDeclineConversation();
        if($id_message)
            $message = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_message="'.(int)$id_message.'"');
        else
        {
            $sql ='SELECT m.* FROM '._DB_PREFIX_.'ets_livechat_message m';
            if(isset($context->employee) && isset($context->employee->id_profile))
            {
                $sql .=' LEFT JOIN '._DB_PREFIX_.'ets_livechat_conversation c ON (c.id_conversation=m.id_conversation)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments d ON (d.id_departments=c.id_departments OR d.id_departments=c.id_departments_wait)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (de.id_departments=c.id_departments)';
            } 
            $sql .=' WHERE m.id_employee=0';
            if(isset($context->employee) && isset($context->employee->id_profile) &&  $context->employee->id_profile!=1 && Ets_livechat::checkDepartments())
                $sql .=' AND (d.all_employees=1 OR c.id_departments=0 OR de.id_employee="'.(int)$context->employee->id.'" OR c.id_departments_wait=-1)';
            if(isset($context->employee) && $context->employee->id && $context->employee->id_profile!=1 && Configuration::get('ETS_LC_STAFF_ACCEPT'))
                $sql .=' AND (c.id_employee=0 OR c.id_employee="'.(int)$context->employee->id.'" OR c.id_employee_wait="'.(int)$context->employee->id.'" OR c.id_employee_wait=-1)';
            if(isset($declines) && $declines)
                $sql .=' AND m.id_conversation NOT IN ('.implode(',',array_map('intval',$declines)).')';
            $sql .=' ORDER BY id_message '.pSQL($orderby);
            $message = Db::getInstance()->getRow($sql);
            $message['count_message_not_seen'] = LC_Conversation::getMessagesEmployeeNotSeen($message['id_conversation']);
            
        } 
        if($ets_livechat->emotions)
        {
            foreach($ets_livechat->emotions as $key=> $emotion)
            {
                $img = '<span title="'.$emotion['title'].'"><img src="'.$ets_livechat->url_module.'views/img/emotions/'.$emotion['img'].'"></span>';
                $message['message'] = str_replace(array(Tools::strtolower($key),$key),array($img,$img),$message['message']);
            }
        }
        //$message = LC_Message::get_message_pre_made_message($message);
        if(date('Y-m-d')==date('Y-m-d',strtotime($message['datetime_added'])))
        {
            $message['datetime_added'] =date('h:i A',strtotime($message['datetime_added']));
        }
        else
        {
            if(date('Y')==date('Y',strtotime($message['datetime_added'])))
            {
                $message['datetime_added'] =date('d-m h:i A',strtotime($message['datetime_added']));
            }
            else
                $message['datetime_added'] =date('d-m-Y h:i A',strtotime($message['datetime_added']));
        }
        if(isset($message['customer_name']) && $message['customer_name'])
            $message['customer_name']=$message['customer_name'];
        else
            $message['customer_name'] = 'Chat ID #'.$message['id_conversation'];
        if($message['name_attachment'] && $attachment= Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_download WHERE id_download="'.(int)$message['type_attachment'].'" AND id_message="'.(int)$message['id_message'].'"'))
        {
            $context=Context::getContext();
            if(isset($context->employee) && $context->employee->id)
                $linkdownload= $ets_livechat->getBaseLink().'/modules/ets_livechat/download.php?downloadfile='.md5(_COOKIE_KEY_.$message['type_attachment']);
            else
                $linkdownload = $context->link->getModuleLink('ets_livechat','download',array('downloadfile'=>md5(_COOKIE_KEY_.$message['type_attachment'])));
            $message['message'] .=($message['message'] ? '<br/>':'').'<span class="file_message"><a class="file_sent" href="'.$linkdownload.'" target="_blank">'.$message['name_attachment'].'</a>'.($attachment['file_size'] ? '<span class="file_size"> ('.$attachment['file_size'].' MB)</span>':'').'</span>';
        }
        return $message;
    }
    public static function getMessageByListID($ids_message)
    {
        if($ids_message)
        {
            $messages = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_message in ('.pSQL($ids_message).')');
            $ets_livechat= new Ets_livechat();
            if($messages)
            {
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
                    if(date('Y-m-d')==date('Y-m-d',strtotime($message['datetime_edited'])))
                    {
                        $message['datetime_edited'] =date('h:i A',strtotime($message['datetime_edited']));
                    }
                    else
                    {
                        if(date('Y')==date('Y',strtotime($message['datetime_edited'])))
                        {
                            $message['datetime_edited'] =date('d-m h:i A',strtotime($message['datetime_edited']));
                        }
                        else
                            $message['datetime_edited'] =date('d-m-Y h:i A',strtotime($message['datetime_edited']));
                    }
                    //$message = LC_Message::get_message_pre_made_message($message);
                }
                
            }
            return $messages;
        }
        else
            return '';
    }
    public static function get_message_pre_made_message($message)
    {
        if($message['id_employee'])
        {
            $ets_livechat_pre_made_messages = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_pre_made_message');
            if($ets_livechat_pre_made_messages)
            {
                $message_content= array();
                $short_code = array();
                foreach($ets_livechat_pre_made_messages as $ets_livechat_pre_made_message)
                {
                    if($ets_livechat_pre_made_message['short_code'])
                    {
                        $short_code[]=$ets_livechat_pre_made_message['short_code'];
                        $message_content[]=$ets_livechat_pre_made_message['message_content'];
                    }
                }
                $message['message'] = str_replace($short_code,$message_content,$message['message']);
            }
        }
        return $message;
    }
    public function delete()
    {
        $id_conversation= $this->id_conversation;
        $id_download = Db::getInstance()->getValue('SELECT id_download FROM '._DB_PREFIX_.'ets_livechat_download WHERE id_message ='.(int)$this->id);
        if($id_download)
        {
            $download= new LC_Download($id_download);
            $download->delete();
        }
        if(parent::delete())
        {
            if(!Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation='.(int)$id_conversation))
            {
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_conversation='.(int)$id_conversation);
            }
            return true;
        }
        return false;
    }
}
