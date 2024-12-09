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
 
if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd());
}
include(_PS_ADMIN_DIR_.'/../../config/config.inc.php');
include(dirname(__FILE__).'/ajax.init.php');
$context = Context::getContext();
$ets_livechat = Module::getInstanceByName('ets_livechat');
if($context->employee->id)
{
    if(Tools::isSubmit('downloadfile') && Tools::getValue('downloadfile'))
    {
        $file=Db::getInstance()->getRow('SELECT filename,id_message,id_ticket,id_field,id_note FROM '._DB_PREFIX_.'ets_livechat_download WHERE md5(concat("'._COOKIE_KEY_.'",id_download))="'.pSQL(Tools::getValue('downloadfile')).'"');
        $filename= $file['filename'];
        
        if($file['id_message'])
        {
            $message= new LC_Message($file['id_message']);
            $name_attachment = $message->name_attachment;
            if(!$ets_livechat->checkConversationEmployee($message->id_conversation,$context->employee->id))
                die('You do not have permission to download this file.');
        }
        elseif($file['id_ticket'] && $file['id_field'])
        {
            $name_attachment = Db::getInstance()->getValue('SELECT value FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message_field WHERE id_message='.(int)$file['id_ticket'].' AND id_field='.(int)$file['id_field']);
            $ticket = new LC_Ticket($file['id_ticket'] );
            if(!$ets_livechat->checkAccesTicket($ticket->id))
                die('You do not have permission to download this file.');
        }
        elseif($file['id_note'])
        {
            $note= new LC_Note($file['id_note']);
            if(!$ets_livechat->checkAccesTicket($note->id_message))
                die('You do not have permission to download this file.');
            $name_attachment = $note->file_name;
        }
        
        if($filename && file_exists(dirname(__FILE__).'/downloads/'.$filename))
        {
            $ext = Tools::strtolower(Tools::substr(strrchr($filename, '.'), 1));
            switch ($ext) {
    			case "pdf": $ctype="application/pdf"; break;
    			case "exe": $ctype="application/octet-stream"; break;
    			case "zip": $ctype="application/zip"; break;
    			case "doc": $ctype="application/msword"; break;
    			case "docx": $ctype="application/msword"; break;
    			case "xls": $ctype="application/vnd.ms-excel"; break;
    			case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
    			case "gif": $ctype="image/gif"; break;
    			case "png": $ctype="image/png"; break;
    			case "jpeg":
    			case "jpg": $ctype="image/jpg"; break;
    			default: $ctype="application/force-download";
    		}
            header("Pragma: public"); // required
    		header("Expires: 0");
    		header("X-Robots-Tag: noindex, nofollow", true);
    		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    		header("Cache-Control: private",false); // required for certain browsers
    		header("Content-Type: $ctype");
    		header("Content-Disposition: attachment; filename=\"".$name_attachment."\";" );
    		header("Content-Transfer-Encoding: Binary");
    		$file_url =dirname(__FILE__).'/downloads/'.$filename;
    		if ($fsize = @filesize($file_url)) {
    			header( "Content-Length: ".$fsize);
    		}
    		ob_clean();
    		flush();
    		readfile($file_url);
            exit();
        }
        else
            die('File Not Found');
    }
}
else
    die('You has been logged out');
