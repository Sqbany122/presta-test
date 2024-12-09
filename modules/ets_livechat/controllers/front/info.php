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
class Ets_livechatInfoModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $display_column_right = false;
    public $_errros= array();
    public $_sussecfull;
    public function __construct()
	{
		parent::__construct();
        $this->display_column_right=false;
        $this->display_column_left =false;
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
        if (!$this->context->customer->isLogged())   
            Tools::redirect('index.php?controller=authentication');
        $this->module->setMeta();
        if(Tools::isSubmit('deleteavatar'))
        {
            $customer_avata= Db::getInstance()->getValue('SELECT avata FROM '._DB_PREFIX_.'ets_livechat_customer_info WHERE id_customer='.(int)$this->context->customer->id);
            if($customer_avata)
            {
                @unlink(dirname(__FILE__).'/../../views/img/config/'.$customer_avata);
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_customer_info SET avata="" WHERE id_customer='.(int)$this->context->customer->id);
                $this->_sussecfull = $this->module->l('Deleted avatar successfully','info');
            }
        }
        if(Tools::isSubmit('submitCustomerInfo'))
        {
            if(isset($_FILES['customer_avata']['tmp_name']) && isset($_FILES['customer_avata']['name']) && $_FILES['customer_avata']['name'])
            {
                $type = Tools::strtolower(Tools::substr(strrchr($_FILES['customer_avata']['name'], '.'), 1));
                $imageName = $_FILES['customer_avata']['name'];
                $fileName = dirname(__FILE__).'/../../views/img/config/'.$imageName;   
                if(file_exists($fileName)) 
                {
                    $time=md5(time());
                    for($i=0;$i<6;$i++)
                    {
                        $index =rand(0,Tools::strlen($time)-1);
                        $imageName =$time[$index].$imageName;
                    }
                    $fileName = dirname(__FILE__).'/../../views/img/config/'.$imageName;
                }              
                if(file_exists($fileName))
                {
                    $this->_errros[] = $this->l('Avata already exists. Try to rename the file then reupload');
                }
                else
                {
        			$imagesize = @getimagesize($_FILES['customer_avata']['tmp_name']);
                    if (!$this->_errros && isset($_FILES['customer_avata']) &&				
        				!empty($_FILES['customer_avata']['tmp_name']) &&
        				!empty($imagesize) &&
        				in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
        			)
        			{
        				$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');    				
        				if ($error = ImageManager::validateUpload($_FILES['customer_avata']))
        					$this->_errros[] = $error;
        				elseif (!$temp_name || !move_uploaded_file($_FILES['customer_avata']['tmp_name'], $temp_name))
        					$this->_errros[] = $this->l('Can not upload the file');
        				elseif (!ImageManager::resize($temp_name, $fileName, 120, 120, $type))
        					$this->_errros[] = $this->displayError($this->l('An error occurred during the image upload process.'));
        				if (isset($temp_name))
        					@unlink($temp_name);
                    }
                }
                if(Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_customer_info WHERE id_customer='.(int)$this->context->customer->id))
                {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_customer_info SET avata="'.pSQL($imageName).'" WHERE id_customer='.(int)$this->context->customer->id);
                }
                else
                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_livechat_customer_info(id_customer,avata) values("'.(int)$this->context->customer->id.'","'.pSQl($imageName).'")');
                $this->_sussecfull = $this->module->l('Updated successfully','info');
            }
        }
        $this->context->smarty->assign(
            array(
                'errors_html'=>$this->_errros ? $this->module->displayError($this->_errros) : false,
                'form_html_post'=>$this->module->renderFormCustomerInformation(),
                'sucsecfull_html' => $this->_sussecfull ? $this->module->displaySuccessMessage($this->_sussecfull):'',
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false, 
                'path' => $this->module->getBreadCrumb(),
            )
        );
        if($this->module->is17)
            $this->setTemplate('module:ets_livechat/views/templates/front/info.tpl');      
        else         
            $this->setTemplate('info16.tpl');  
    }
}
