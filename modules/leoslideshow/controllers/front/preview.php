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

class LeoSlideshowPreviewModuleFrontController extends ModuleFrontController
{
    private $name_module = 'leoslideshow';
    public $theme_name;
    public $img_path;
    public $img_url;
    public $slider_data = '';

    public function __construct()
    {
        parent::__construct();
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->theme_name = Context::getContext()->shop->theme->getName();
        }else{
            $this->theme_name = Context::getContext()->shop->getTheme();
        }
        $this->img_path = _PS_ALL_THEMES_DIR_.$this->theme_name.'/img/modules/'.$this->name_module.'/';
        $this->img_url = __PS_BASE_URI__.'themes/'.$this->theme_name.'/img/modules/'.$this->name_module.'/';

        include_once($this->module->getLocalPath().$this->name_module.'.php');
    }

    /**
     * @see FrontController::initContent()
     */
    public function display()
    {
        // tpl in theme folder or module folder
        if (file_exists(_PS_THEME_DIR_.'modules/leoslideshow/views/templates/front/leoslideshow.tpl')) {
            # module validation
            $leoslideshow_tpl = _PS_THEME_DIR_.'modules/leoslideshow/views/templates/front/leoslideshow.tpl';
        } else {
            # module validation
            $leoslideshow_tpl = _PS_MODULE_DIR_.'leoslideshow/views/templates/front/leoslideshow.tpl';
        }

        //add css
        $this->addCSS(__PS_BASE_URI__.str_replace('//', '/', 'modules/leoslideshow/').'views/css/typo/typo.css', 'all');
        $this->addCSS(__PS_BASE_URI__.str_replace('//', '/', 'modules/leoslideshow/').'views/css/iView/iview.css', 'all');
        $this->addCSS(__PS_BASE_URI__.str_replace('//', '/', 'modules/leoslideshow/').'views/css/iView/skin_4_responsive/style.css', 'all');

        // add js
        $this->addJS(__PS_BASE_URI__.str_replace('//', '/', 'modules/leoslideshow/').'views/js/iView/raphael-min.js');
        $this->addJS(__PS_BASE_URI__.str_replace('//', '/', 'modules/leoslideshow/').'views/js/iView/iview.js');

        if (!is_dir(_PS_ROOT_DIR_.'/cache/'.$this->name_module)) {
            # module validation
            mkdir(_PS_ROOT_DIR_.'/cache/'.$this->name_module, 0755);
        }

        //preview group
        $id_group = Tools::getValue('id_group');
        $id_lang = $this->context->language->id;
        if ($id_group) {
            $group = LeoSlideshowGroup::getGroupByID($id_group);
            if (!Tools::getValue('id_slider') && !Tools::getValue('preview')) {
                # module validation
                $sliders = $this->getSlides($id_group, $id_lang, 1);
            }
        }

        if (!isset($group) || !$group) {
            # module validation
            return false;
        }

        $id_slider = Tools::getValue('id_slide');
        if ($id_slider && !Tools::getValue('preview')) {
            # module validation
            $sliders = $this->getSlide($id_slider, $id_lang);
        }



        if (Tools::getValue('preview')) {
            $slider_preview_data = trim(html_entity_decode((Tools::getValue('slider_preview_data'))));
            $slider_preview_data = Tools::jsonDecode($slider_preview_data);

            foreach ($slider_preview_data as $key => $val) {
                # module validation
                $sliders[0][$key] = $val;
            }
            $tmp_slider = array();
            $tmp_slider = $sliders[0]['params'];
            $sliders[0]['params'] = array();
            foreach ($tmp_slider as $key => $val) {
                # module validation
                $sliders[0]['params'][$key] = $val;
            }
            $tmp_slider = $sliders[0]['video'];
            $sliders[0]['video'] = array();
            foreach ($tmp_slider as $key => $val) {
                # module validation
                $sliders[0]['video'][$key] = $val;
            }
            $tmp_slider = $sliders[0]['layers'];
            $sliders[0]['layers'] = array();
            foreach ($tmp_slider as $key => $val) {
                foreach ($val as $k => $v) {
                    # module validation
                    $sliders[0]['layersparams'][$key][$k] = $v;
                }
            }
        }

        if (!isset($sliders) || !$sliders) {
            # module validation
            return false;
        }
        $this->name = 'leoslideshow';
        $slider_module = new LeoSlideshow();
        $group_data = $slider_module->group_data;
        $this->slider_data = $slider_module->slider_data;
        $slider_params = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($group['params']), true);
        $slider_params = array_merge($group_data, $slider_params);
        $mod_group = new LeoSlideshowGroup();
        $slider_params = $mod_group->setData($slider_params)->beforeLoad()->loadFrontEnd();
        if (isset($slider_params['fullwidth']) && (!empty($slider_params['fullwidth']) || $slider_params['fullwidth'] == 'boxed')) {
            # module validation
            $slider_params['image_cropping'] = false;
        }

        $slider_params['hide_navigator_after'] = $slider_params['show_navigator'] ? 0 : $slider_params['hide_navigator_after'];
        $slider_params['slider_class'] = trim(isset($slider_params['fullwidth']) && !empty($slider_params['fullwidth']) ? $slider_params['fullwidth'] : 'boxed');
        $slider_fullwidth = $slider_params['slider_class'] == 'boxed' ? 'off' : 'on';

        // generate back-ground
        if ($slider_params['background_image'] && $slider_params['background_url'] && file_exists($this->img_path.$slider_params['background_url'])) {
            # module validation
            $slider_params['background'] = 'background: url('.$this->img_url.$slider_params['background_url'].') no-repeat scroll left 0 '.$slider_params['background_color'].';';
        } else {
            # module validation
            $slider_params['background'] = 'background-color:'.$slider_params['background_color'];
        }

        //include library genimage
        if (!class_exists('PhpThumbFactory')) {
            # module validation
            require_once _PS_MODULE_DIR_.'leoslideshow/libs/phpthumb/ThumbLib.inc.php';
        }

        $white_main_img = __PS_BASE_URI__.'modules/'.$this->name.'/views/img/white50.png';

        //process slider
        foreach ($sliders as $key => $slider) {
            if (!Tools::getValue('preview')) {
                $slider['layers'] = array();
                $slider['params'] = array_merge($this->slider_data, Tools::jsonDecode(LeoSlideshowSlide::base64Decode($slider['params']), true));
                $slider['layersparams'] = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($slider['layersparams']), true);
                $slider['video'] = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($slider['video']), true);
            }
            $slider['data_link'] = '';
            if ($slider['params']['enable_link'] && $slider['link']) {
                $slider['data_link'] = 'data-link="'.$slider['link'].'"';
                $slider['data_target'] = 'data-target="'.LeoSlideshowSlide::renderTarget($slider['params']['target']).'"';
            } else {
                # module validation
                $slider['data_target'] = '';
            }

            $slider['data_delay'] = (int)$slider['params']['delay'];

            //videoURL
            $slider['videoURL'] = '';
            $slider['video']['active'] = '0';
            if ($slider['video']['usevideo'] == 'youtube' || $slider['video']['usevideo'] == 'vimeo') {
                $slider['video']['active'] = '1';
                $slider['video']['videoURL'] = Tools::getCurrentUrlProtocolPrefix() . 'player.vimeo.com/video/'.$slider['video']['videoid'].'/';
                if ($slider['video']['usevideo'] == 'youtube') {
                    # module validation
                    $slider['video']['videoURL'] = Tools::getCurrentUrlProtocolPrefix() . 'www.youtube.com/embed/'.$slider['video']['videoid'].'/';
                }
            }

            if ($slider['video']['videoauto'] == 1) {
                # module validation
                $slider['video']['videoauto'] = 'autoplay=1';
            } else {
                # module validation
                $slider['video']['videoauto'] = 'autoplay=0';
            }

            $slider['background_color'] = '';
            if (isset($slider_params['background_color']) && $slider_params['background_color']) {
                # module validation
                $slider['background_color'] = $slider_params['background_color'];
            }
            if (isset($slider['video']['background_color']) && $slider['video']['background_color']) {
                # module validation
                $slider['background_color'] = $slider['video']['background_color'];
            }

            LeoSlideshowSlide::getBackground($slider_params, $slider);

            if ($slider['image'] == '') {
                # module validation
                $slider['image'] = 'views/img/blank.gif';
            }

            if ($slider_params['image_cropping']) {
                //gender main_image
                if ($slider['image'] && file_exists($this->img_path.$slider['image'])) {
                    # module validation
                    $slider['main_image'] = $this->renderThumb($slider['image'], $slider_params['width'], $slider_params['height']);
                } else {
                    # module validation
                    $slider['main_image'] = $white_main_img;
                }

                if ($slider['thumbnail'] && file_exists($this->img_path.$slider['thumbnail'])) {
                    # module validation
                    //$slider['thumbnail'] = $this->renderThumb($slider['thumbnail'], $sliderParams['thumbnail_width'], $sliderParams['thumbnail_height']);
                } else if ($slider['image'] && file_exists($this->img_path.$slider['image'])) {
                    # module validation
                    //$slider['thumbnail'] = $this->renderThumb($slider['image'], $sliderParams['thumbnail_width'], $sliderParams['thumbnail_height']);
                } else {
                    # module validation
                    $slider['thumbnail'] = $white_main_img;
                }
            } else {
                $slider['main_image'] = __PS_BASE_URI__.'modules/leoslideshow'.'/views/img/blank.gif';

                if ($slider['image'] && file_exists($this->img_path.$slider['image'])) {
                    # module validation
                    $slider['main_image'] = $this->img_url.$slider['image'];
                }

                if ($slider['thumbnail'] && file_exists($this->img_path.$slider['thumbnail'])) {
                    # module validation
                    $slider['thumbnail'] = $this->img_url.$slider['thumbnail'];
                } else if ($slider['image'] && file_exists($this->img_path.$slider['image'])) {
                    # module validation
                    $slider['thumbnail'] = $slider['main_image'];
                } else {
                    # module validation
                    $slider['thumbnail'] = $white_main_img;
                }
            }

            if (isset($slider['layersparams']) && $slider['layersparams']) {
                foreach ($slider['layersparams'] as $k => &$layer_css) {
                    if ($layer_css['layer_status'] == 0) {
                        unset($slider['layersparams'][$k]);
                        continue;
                    }

                    $layer_css_val = '';
                    if (isset($layer_css['layer_font_size']) && $layer_css['layer_font_size']) {
                        # module validation
                        $layer_css_val = 'font-size:'.$layer_css['layer_font_size'];
                    }
                    if (isset($layer_css['layer_background_color']) && $layer_css['layer_background_color']) {
                        # module validation
                        $layer_css_val .= ($layer_css_val != '' ? ';' : '').'background-color:'.$layer_css['layer_background_color'];
                    }
                    if (isset($layer_css['layer_color']) && $layer_css['layer_color']) {
                        # module validation
                        $layer_css_val .= ($layer_css_val != '' ? ';' : '').'color:'.$layer_css['layer_color'];
                    }
                    $layer_css['css'] = $layer_css_val;
                    if (!isset($layer_css['layer_link'])) {
                        # module validation
                        $layer_css['layer_link'] = $slider['link'];
                    }
                    $layer_css['layer_target'] = LeoSlideshowSlide::renderTarget($layer_css['layer_target']);
                    if (isset($layer_css['layer_caption']) && $layer_css['layer_caption']) {
                        # module validation
                        $layer_css['layer_caption'] = utf8_decode($layer_css['layer_caption']);
                    }
                }
            }
            $sliders[$key] = $slider;
        }
        $slider_params['start_with_slide'] = LeoSlideshowGroup::showStartWithSlide($slider_params['start_with_slide'], $sliders);
        $sliders = LeoSlideshowSlide::showBulletCustomHTML($slider_params, $sliders);
        $slider_params['playLabel'] = LeoSlideshowHelper::getInstance()->l('Play', false, 'preview');
        $slider_params['pauseLabel'] = LeoSlideshowHelper::getInstance()->l('Pause', false, 'preview');
        $slider_params['closeLabel'] = LeoSlideshowHelper::getInstance()->l('Close', false, 'preview');
        $slider_params['rtl'] = $this->context->language->is_rtl;

        $this->context->smarty->assign(array(
            'sliderParams' => $slider_params,
            'sliders' => $sliders,
            'sliderIDRand' => rand(20, rand()),
            'sliderFullwidth' => $slider_fullwidth,
            'sliderImgUrl' => $this->img_url,
            'leoslideshow_tpl' => $leoslideshow_tpl,
        ));
        $this->setTemplate('preview.tpl');

        parent::display();
    }

    public function renderThumb($src_file, $width, $height)
    {
        $sub_folder = '/';
        if (!$src_file) {
            # module validation
            return '';
        }
        if (strpos($src_file, '/') !== false) {
            $path = @pathinfo($src_file);
            if (strpos($path['dirname'], '/') !== -1) {
                $sub_folder = $path['dirname'].'/';
                $folder_list = explode('/', $path['dirname']);
                $tmp_folder = '/';
                foreach ($folder_list as $value) {
                    if ($value) {
                        if (!is_dir(_PS_ROOT_DIR_.'/cache/'.$this->name_module.$tmp_folder.$value)) {
                            # module validation
                            mkdir(_PS_ROOT_DIR_.'/cache/'.$this->name_module.$tmp_folder.$value, 0755);
                        }

                        $tmp_folder .= $value.'/';
                    }
                }
            }
            $image_name = $path['basename'];
        } else {
            # module validation
            $image_name = $src_file;
        }

        $path = '';
        if (file_exists($this->img_path.$src_file)) {
            //return image url
            $path = __PS_BASE_URI__.'cache/'.$this->name_module.$sub_folder.$width.'_'.$height.'_'.$image_name;
            $save_path = _PS_ROOT_DIR_.'/cache/'.$this->name_module.$sub_folder.$width.'_'.$height.'_'.$image_name;
            if (!file_exists($save_path)) {
                $thumb = PhpThumbFactory::create($this->img_path.$src_file);
                $thumb->adaptiveResize($width, $height);
                $thumb->save($save_path);
            }
        }

        return $path;
    }

    /**
     * get all slider data
     */
    public function getSlides($id_group, $id_lang, $active = null)
    {
        $this->context = Context::getContext();
        if (!$id_lang) {
            # module validation
            $id_lang = $this->context->language->id;
        }

        $sql = 'SELECT lsl.`id_leoslideshow_slides` as id_slide,lsl.*,lsll.*
                    FROM '._DB_PREFIX_.'leoslideshow_slides lsl
                    LEFT JOIN '._DB_PREFIX_.'leoslideshow_slides_lang lsll ON (lsl.id_leoslideshow_slides = lsll.id_leoslideshow_slides)
                    WHERE lsl.id_group = '.(int)$id_group.'
                    AND lsll.id_lang = '.(int)$id_lang.
                        ($active ? ' AND lsl.`active` = 1' : ' ').'
                    ORDER BY lsl.position';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * get all slider data
     */
    public function getSlide($id_slider, $id_lang)
    {
        $sql = 'SELECT lsl.`id_leoslideshow_slides` as id_slide, lsl.*,lsll.*
                    FROM '._DB_PREFIX_.'leoslideshow_slides lsl
                    LEFT JOIN '._DB_PREFIX_.'leoslideshow_slides_lang lsll ON (lsl.id_leoslideshow_slides = lsll.id_leoslideshow_slides)
                    WHERE lsl.id_leoslideshow_slides= '.(int)$id_slider.' AND lsll.id_lang = '.(int)$id_lang;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
}
