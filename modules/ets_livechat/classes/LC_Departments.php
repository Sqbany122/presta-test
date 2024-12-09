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
class LC_Departments extends ObjectModel
{
    public $name;
    public $description;
    public $status;
    public $sort_order;
    public static $definition = array(
		'table' => 'ets_livechat_departments',
		'primary' => 'id_departments',
		'fields' => array(
			'name' => array('type' => self::TYPE_STRING),
            'description' => array('type' => self::TYPE_STRING),
            'status' => array('type' => self::TYPE_INT),   
            'sort_order' => array('type' => self::TYPE_INT),  
            'all_employees' => array('type' => self::TYPE_INT), 
        )
	);
    public function __construct($id = null, $idLang = null)
    {
        parent::__construct($id, $idLang);
        $this->context = Context::getContext();
    }
    public function delete()
    {
        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_livechat_departments_employee WHERE id_departments='.(int)$this->id);
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_conversation set id_departments=0 WHERE id_departments='.(int)$this->id);
        if(parent::delete())
        {
            $departments = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_departments ORDER BY sort_order ASC');
            if($departments)
            {
                $i=1;
                foreach($departments as $department)
                {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_departments SET sort_order="'.(int)$i.'" WHERE id_departments='.(int)$department['id_departments']);
                    $i++;
                }
            }
        }
        return true;
    }
}