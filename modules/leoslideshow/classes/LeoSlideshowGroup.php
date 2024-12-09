<?php
/**
 *  Leo Theme for Prestashop 1.6.x
 *
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class LeoSlideshowGroup extends ObjectModel
{
    public $title;
    public $active;
    public $hook;
    public $id_shop;
    public $params;
    public $data = array();
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leoslideshow_groups',
        'primary' => 'id_leoslideshow_groups',
        'fields' => array(
            'title' => array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'hook' => array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 64),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'params' => array('type' => self::TYPE_HTML, 'lang' => false)
        )
    );

    public function add($autodate = true, $null_values = false)
    {
        $res = parent::add($autodate, $null_values);

        return $res;
    }

    public static function groupExists($id_group)
    {
        $req = 'SELECT gr.`id_leoslideshow_groups` as id_group 
				FROM `'._DB_PREFIX_.'leoslideshow_groups` gr 
				WHERE gr.`id_leoslideshow_groups` = '.(int)$id_group;
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);
        return ($row);
    }

    public function getGroups($active, $id_shop)
    {
        $this->context = Context::getContext();
        if (!isset($id_shop)) {
            $id_shop = $this->context->shop->id;
        }
        $req = 'SELECT * 
				FROM `'._DB_PREFIX_.'leoslideshow'.'_groups` gr 
				WHERE (`id_shop` = '.(int)$id_shop.')'.
                ($active ? ' AND gr.`active` = 1' : ' ');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);
    }

    public static function deleteAllSlider($id_group)
    {
        $sql = 'SELECT sl.`id_leoslideshow_slides` as id
				FROM `'._DB_PREFIX_.'leoslideshow_slides` sl
				WHERE sl.`id_group` = '.(int)$id_group;
        $sliders = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($sliders) {
            $where = '';
            foreach ($sliders as $sli) {
                # module validation
                $where .= $where ? ','.$sli['id'] : $sli['id'];
            }
            $sql = 'DELETE FROM `'._DB_PREFIX_.'leoslideshow_slides_lang` '
                    .'WHERE `id_leoslideshow_slides` IN ('.$where.')';
            Db::getInstance()->execute($sql);   # module validation
        }
    }

    public function delete()
    {
        $res = true;

        $sql = 'DELETE FROM `'._DB_PREFIX_.'leoslideshow_groups` '
                .'WHERE `id_leoslideshow_groups` = '.(int)$this->id;
        $res &= Db::getInstance()->execute($sql);
        $this->deleteAllSlider((int)$this->id);
        $res &= parent::delete();
        return $res;
    }

    /**
     * Get and validate StartWithSlide field.
     */
    public static function showStartWithSlide($start_with_slide = 0, $slider = array())
    {
        $result = 1;
        if (is_array($slider)) {
            $start_with_slide = (int)$start_with_slide;
            $slider_num = count($slider);
            // 1 <= $start_with_slide <= $slider_num
            if (1 <= $start_with_slide && $start_with_slide <= $slider_num) {
                $result = $start_with_slide;
            }
        }

        $result--; // index begin from 0
        return $result;
    }

    public function getDelay()
    {
        $temp_result = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($this->params), true);
        $result = $temp_result['delay'];

        return $result;
    }

    public static function getGroupOption()
    {
        $result = array();
        $mod_group = new LeoSlideshowGroup();
        $groups = $mod_group->getGroups(null, null);

        foreach ($groups as $group) {
            $temp = array();
            $temp['id'] = $group['id_leoslideshow_groups'];
            $temp['name'] = $group['title'];
            $result[] = $temp;
        }
        return $result;
    }

    /**
     * Get group to frontend
     */
    public static function getActiveGroupByHook($hook_name = '', $active = 1)
    {
        $id_shop = Context::getContext()->shop->id;
        $sql = '
				SELECT *
				FROM '._DB_PREFIX_.'leoslideshow_groups gr
				WHERE gr.id_shop = '.(int)$id_shop.'
				AND gr.hook = "'.$hook_name.'"'.
                ($active ? ' AND gr.`active` = 1' : ' ').'
				ORDER BY gr.id_leoslideshow_groups';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    /**
     * Get group to preview
     */
    public static function getGroupByID($id_group)
    {
        $sql = '
			SELECT *
			FROM '._DB_PREFIX_.'leoslideshow_groups gr
			WHERE gr.id_leoslideshow_groups = '.(int)$id_group;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public function setData($data = array())
    {
        if (is_array($data)) {
            $this->data = $data;
        }
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function beforeLoad()
    {
        $data = $this->data;

        #Timer
        if ($data['timer_show'] == LeoSlideshowConfig::TIMER_HIDE_AUTO) {
            $data['autoAdvance'] = 1;
        } elseif ($data['timer_show'] == LeoSlideshowConfig::TIMER_HIDE_STOP)
            $data['autoAdvance'] = 0;
        elseif ($data['timer_show'] == LeoSlideshowConfig::TIMER_SHOW_AUTO)
            $data['autoAdvance'] = 1;
        elseif ($data['timer_show'] == LeoSlideshowConfig::TIMER_SHOW_STOP)
            $data['autoAdvance'] = 0;

        # Navigator
        if ($data['navigator_type'] == LeoSlideshowConfig::IVIEW_NAV_THUMBNAIL) {
            $data['controlNavThumbs'] = 1;
        } elseif ($data['navigator_type'] == LeoSlideshowConfig::IVIEW_NAV_BULLET)
            $data['controlNavThumbs'] = 0;

        # Multi language
        $data['playLabel'] = LeoSlideshowHelper::getInstance()->l('Play', false, 'leoslideshowgroup');
        $data['pauseLabel'] = LeoSlideshowHelper::getInstance()->l('Pause', false, 'leoslideshowgroup');
        $data['closeLabel'] = LeoSlideshowHelper::getInstance()->l('Close', false, 'leoslideshowgroup');
        $data['nextLabel'] = LeoSlideshowHelper::getInstance()->l('Next', false, 'leoslideshowgroup');
        $data['previousLabel'] = LeoSlideshowHelper::getInstance()->l('Previous', false, 'leoslideshowgroup');
        $this->data = $data;
        return $this;
    }

    public function loadFrontEnd()
    {
        return $this->getData();
    }
    
    public function count(){
        $sql = 'SELECT id_leoslideshow_groups FROM '._DB_PREFIX_.'leoslideshow_groups';
        $groups = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);        
        $number_groups = count($groups);
        return $number_groups;
    }
}
