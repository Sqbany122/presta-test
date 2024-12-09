<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2018 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 */

class BackInStockSubscriptionModuleFrontController extends ModuleFrontController
{
    public $controller_name = 'subscription';

    public function __construct()
    {
        $this->context = Context::getContext();
        $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
        if ($data['enable'] == 0) {
            Tools::redirect(
                $this->context->link->getPageLink(
                    'index',
                    (bool)Configuration::get('PS_SSL_ENABLED')
                )
            );
        }

        if (!$this->context->customer->logged) {
            Tools::redirect(
                $this->context->link->getPageLink(
                    'my-account',
                    (bool)Configuration::get('PS_SSL_ENABLED')
                )
            );
        } else {
            if (isset($data['enable_subscription_list']) && $data['enable_subscription_list'] == 0) {
                Tools::redirect(
                    $this->context->link->getPageLink(
                        'index',
                        (bool)Configuration::get('PS_SSL_ENABLED')
                    )
                );
            }
        }
        parent::__construct();
        
        $this->module = Module::getInstanceByName('backinstock');
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJs($this->module->getModuleDirUrl() . 'backinstock/views/js/front/subscription_list.js');
        $this->addCSS($this->module->getModuleDirUrl() . 'backinstock/views/css/front/subscription_list.css');
        
        // glitter css and js file
        $this->addCSS(_PS_MODULE_DIR_ . 'backinstock/views/css/front/notifications/jquery.notyfy.css');
        $this->addCSS(_PS_MODULE_DIR_ . 'backinstock/views/css/front/notifications/default.css');
        $this->addCSS(_PS_MODULE_DIR_ . 'backinstock/views/css/front/notifications/jquery.gritter.css');
        $this->addJS(_PS_MODULE_DIR_  . 'backinstock/views/js/front/notifications/jquery.gritter.min.js');
        $this->addJS(_PS_MODULE_DIR_  . 'backinstock/views/js/front/notifications/jquery.notyfy.js');
        $this->addJS(_PS_MODULE_DIR_  . 'backinstock/views/js/front/backinstock_notifications.js');
    }

    public function initContent()
    {
        $response = array();
        $response['status'] = false;
        $searched_produts = array();
        if ((int) Tools::getValue('ajax') == 1) {
            $ajax_subscription_page_link = $this->context->link->getModuleLink(
                $this->module->name,
                'subscription',
                array(
                    'ajax' => true,
                ),
                true
            );
            $this->context->smarty->assign('ajax_subscription_page_link', $ajax_subscription_page_link);
            /*
             * @author - Rishabh jain
             * DOC - below condition is to remove the subscriber if the subscriber has clicked on the delet button
             * or to refresh the subscription list as per the page number
             */
            if (Tools::getValue('action', '') == 'remove_subscription') {
                $id_subscriber = Tools::getValue('id_subscriber', 0);
                if ((int) $id_subscriber > 0) {
//change by gopi for fixing delete subsription when guest user is converted into customer
//                    $sql = 'DELETE FROM `'._DB_PREFIX_.'product_update_product_detail` WHERE' .
//                        ' id = "'.pSQL($id_subscriber).'" and customer_id = "'.(int) $this->context->customer->id .'"';
                    $sql = 'DELETE FROM `'._DB_PREFIX_.'product_update_product_detail` WHERE' .
                        ' id = "'.pSQL($id_subscriber).'"';
                    if (Db::getInstance()->execute($sql)) {
                        $this->getSubscribers();
//                    if (1) {
                        $response['status'] = true;
                        $response['msg'] = $this->module->l('The Selected Product is unsubscribed sucessfully', 'subscription');
                        $response['html'] = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/ajax_subscription_list.tpl');
                        echo Tools::jsonEncode($response);
                        die;
                    } else {
                        $this->getSubscribers();
                        $response['status'] = false;
                        $response['msg'] = $this->module->l('The Selected Product could not be unsubscribed.', 'subscription');
                        $response['html'] = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/ajax_subscription_list.tpl');
                        echo Tools::jsonEncode($response);
                        die;
                    }
                }
            } else {
                $this->getSubscribers();
                $response['status'] = true;
                $response['html'] = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/ajax_subscription_list.tpl');
                echo Tools::jsonEncode($response);
                die;
            }
        } else {
            $this->getSubscribers();
            $ajax_subscription_page_link = $this->context->link->getModuleLink(
                $this->module->name,
                'subscription',
                array(
                    'ajax' => true,
                ),
                true
            );
            $this->context->smarty->assign('ajax_subscription_page_link', $ajax_subscription_page_link);
            $this->getSubscribers();
        }
        $this->setTemplate('subscriptions.tpl');
        parent::initContent();
    }
    
    /*
     * @author - Rishabh Jain
     * DOC - 30/01/20
     * Function to get the subscriber list on the basis of logged in customer
     */
    
    private function getSubscribers()
    {
        $query = "SELECT id,product_id,product_attribute_id,req_quan,date_added FROM " . _DB_PREFIX_ . "product_update_product_detail"
            . " where customer_id = ".(int) $this->context->customer->id.""
            . " ORDER BY id DESC";
        $result = Db::getInstance()->executeS($query);
        $is_enabled_remove_subscription_button = 0;
        if (count($result) > 0) {
            $formvalue = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
            if (isset($formvalue['subscription_per_page'])) {
                $limit = $formvalue['subscription_per_page'];
            } else {
                $limit = 10;
            }
            if (isset($formvalue['enable_remove_subscription']) && $formvalue['enable_remove_subscription'] == 1) {
                $is_enabled_remove_subscription_button = 1;
            }
            $remove_subscription_button = 0;
            if (isset($formvalue['enable_remove_subscription'])) {
                $remove_subscription_button = $formvalue['enable_remove_subscription'];
            }
            
            
            $final_subscription_array = array();
            foreach ($result as $key => $result_data) {
                $product_obj = new Product($result_data['product_id']);
                if ((int)$product_obj->active == 1) {
                    $quantity_query = 'select quantity'
                        . ' from ' . _DB_PREFIX_. 'stock_available'
                        . ' where id_product_attribute='. (int) $result_data['product_attribute_id']
                        . ' and id_product=' . (int) $result_data['product_id'];
                    $quantity_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($quantity_query);
                    $final_subscription_array[$key]['quantity'] = $quantity_data;
                    $final_subscription_array[$key]['product_link'] = $this->context->link->getProductLink($product_obj);
                    $final_subscription_array[$key]['product_name'] = $product_obj->name[$this->context->language->id];
                    $lang_obj = new Language($this->context->language->id);
                    $final_subscription_array[$key]['date_added'] = date($lang_obj->date_format_full, strtotime($result_data['date_added']));
                    $final_subscription_array[$key]['id_subscription'] = $result_data['id'];
                    $final_subscription_array[$key]['req_quan'] = $result_data['req_quan'];
                    $image = Image::getCover($product_obj->id);
                    $link = new Link;
                    if ($this->checkSecureUrl()) {
                        $image_link = $image ? 'https://'.$link->getImageLink($product_obj->link_rewrite[$this->context->language->id], $image['id_image'], ImageType::getFormatedName('home')) : false;
                    } else {
                        $image_link = $image ? 'http://'.$link->getImageLink($product_obj->link_rewrite[$this->context->language->id], $image['id_image'], ImageType::getFormatedName('home')) : false;
                    }
                    if ($image_link == '') {
                        $image_link = $this->getImgDirUrl() . _THEME_PROD_DIR_ . Language::getIsoById((int) $this->context->language->id) . '.jpg';
                    }
                    /*
                    * @author - Rishabh Jain
                    * DOC - 30/01/20
                    * Below code is to add the attributes names in the prodcut name
                     * and to fetch the image as per the attribute
                    */
                    if ((int) $result_data['product_attribute_id'] > 0) {
                        $attributes = $product_obj->getAttributesResume($this->context->language->id);
                        foreach ($attributes as $attr_key => $attribute_data) {
                            if ($attribute_data['id_product_attribute'] == $result_data['product_attribute_id']) {
                                $final_subscription_array[$key]['product_name'] .= ': '.$attribute_data['attribute_designation'];
                                $combination_images = $product_obj->_getAttributeImageAssociations($attribute_data['id_product_attribute']);
                                $product_attr_data = array();
                                foreach ($combination_images as $image_id) {
                                    $image = new Image($image_id);
                                    if (isset($image_id) && $image_id != '') {
                                        $img_path = $this->getImgDirUrl() . _THEME_PROD_DIR_ . $image->getExistingImgPath() . '.jpg';
                                    } else {
                                        $img_path = $this->getImgDirUrl() . _THEME_PROD_DIR_ . Language::getIsoById((int) $this->context->language->id) . '.jpg';
                                    }
                                    $product_attr_data['image_ids'][] = $image_id;
                                    $product_attr_data['comb_images'][$image_id]['caption'] = $image->legend[$this->context->language->id];
                                    $product_attr_data['comb_images'][$image_id]['path'] = $img_path;
                                }
                                if (isset($combination_images) && !empty($combination_images)) {
                                    $product_attr_data['default_comb_img'] = $product_attr_data['comb_images'][$combination_images[0]]['path'];
                                    $image_link = $product_attr_data['comb_images'][$combination_images[0]]['path'];
                                }
                            }
                        }
                    }
                    // changes over
                    $final_subscription_array[$key]['image_link'] = $image_link;
                }
            }
            $total_records = count($final_subscription_array);
            $current_page = 1;
            if ((int)Tools::getValue('page_no', 0)) {
                $current_page = (int)Tools::getValue('page_no', 0);
            }
            $start = 0;
            if ($current_page > 1) {
                $start = $limit*($current_page - 1);
            }
            $end_text = $start + $limit;
            if ($end_text > $total_records) {
                $end_text = $total_records;
            }
            $this->context->smarty->assign('total_subscription', count($final_subscription_array));
            $final_subscription_array = array_slice($final_subscription_array, $start, $limit);
            $total_pages = ceil($total_records / $limit);
            $this->context->smarty->assign('subscribers', $final_subscription_array);
//            $this->context->smarty->assign('remove_subscription_button', $remove_subscription_button);
            $this->context->smarty->assign('remove_subscription_button', $is_enabled_remove_subscription_button);
            $this->context->smarty->assign('total_pages', $total_pages);
            $this->context->smarty->assign('kbpage', $current_page);
            $this->context->smarty->assign('start', $start + 1);
            $this->context->smarty->assign('end', $end_text);
        }
    }
    private function getImgDirUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_;
        } else {
            $module_dir = _PS_BASE_URL_;
        }
        return $module_dir;
    }
    private function checkSecureUrl()
    {
        $custom_ssl_var = 0;

        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }
        } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $custom_ssl_var = 1;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            return true;
        } else {
            return false;
        }
    }
}
