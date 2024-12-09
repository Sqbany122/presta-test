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
class Ets_livechatHistoryModuleFrontController extends ModuleFrontController
{
    public function __construct()
	{
		parent::__construct();
		$this->context = Context::getContext();
	}
	public function init()
	{
		parent::init();
        //Sorry, you do not have permission');
	}
	public function initContent()
	{
	    parent::initContent();
        $this->module->setMeta();
        if (!$this->context->customer->isLogged())   
            Tools::redirect('index.php?controller=authentication');
        $conversation = LC_Conversation::getCustomerConversation();
        $this->context->smarty->assign(
            array(
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false, 
                'path' => $this->module->getBreadCrumb(),
            )
        );
        if(Tools::isSubmit('viewchat') && $id_conversation=Tools::getValue('id'))
        {
            $conversation_class= new LC_Conversation($id_conversation);
            if($conversation->chatref!=$conversation->chatref)
                die($this->module->l('You do not access permistion','history'));
            else
            {
                $this->context->smarty->assign(
                    array(
                        'conversation_messages' => $this->module->_displayConversationDetail($conversation_class),
                    )
                );
                if($this->module->is17)
                    $this->setTemplate('module:ets_livechat/views/templates/front/conversation_detail.tpl');      
                else         
                    $this->setTemplate('conversation_detail16.tpl');
            }
        }
        else
        {
            $this->context->smarty->assign(
                array(
                    'list_conversations' => $this->module->_displayHistoryChatCustomer($conversation ? $conversation->chatref :0),
                )
            );
            if($this->module->is17)
                $this->setTemplate('module:ets_livechat/views/templates/front/history.tpl');      
            else         
                $this->setTemplate('history16.tpl');
        }
          
    }
}