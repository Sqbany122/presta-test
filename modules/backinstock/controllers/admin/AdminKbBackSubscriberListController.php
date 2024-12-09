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
 *
*/

include_once(_PS_MODULE_DIR_.'backinstock/classes/KbBisCustomFields.php');

class AdminKbBackSubscriberListController extends ModuleAdminControllerCore
{

    public function __construct()
    {
        parent::__construct();
        $this->custom_smarty = new Smarty();
        $this->custom_smarty->setTemplateDir(_PS_MODULE_DIR_ . 'backinstock/views/templates/admin/');
        $this->custom_smarty->caching = false;
        $this->bootstrap = true;
        $this->display = 'list';
        $this->identifier = 'id';
        $this->module = Module::getInstanceByName('backinstock');
        $this->module->lang = false;
        $this->shop = false;
        $this->table = 'product_update_product_detail';


        $this->toolbar_title = $this->module->l('Subscriber Listing', 'adminkbbacksubscriberlistcontroller');

        $this->fields_list = array(
            'id' => array(
                'title' => $this->module->l('Id', 'adminkbbacksubscriberlistcontroller'),
                'align' => 'text-center',
                'order_key' => 'a.id',
                'filter_key' => 'a!id',
            ),
            'email' => array(
                'title' => $this->module->l('Email', 'adminkbbacksubscriberlistcontroller'),
                'havingFilter' => true,
                'filter_key' => 'a!email',
                'callback' => 'getCustomerEmail',
            ),
            'id_image' => array(
                'title' => $this->module->l('Image', 'adminkbbacksubscriberlistcontroller'),
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'callback' => 'showCoverImage'
            ),
            'product_name' => array(
                'title' => $this->module->l('Product Name', 'adminkbbacksubscriberlistcontroller'),
                'type' => 'text',
                'search' => true,
                'havingFilter' => true,
                'orderby' => false,
                'callback' => 'getProductName',
            ),
            'req_quan' => array(
                'title' => $this->module->l('Quantity', 'adminkbbacksubscriberlistcontroller'),
                'type' => 'text',
                'search' => true,
                'havingFilter' => true,
                'orderby' => false,
            ),
            'send' => array(
                'title' => $this->module->l('Back in stock Mail Sent', 'adminkbbacksubscriberlistcontroller'),
                'list' => array(
                    0 => $this->module->l('Pending'),
                    1 => $this->module->l('Sent'),
                ),
                'type' => 'select',
                'align' => 'text-center',
                'havingFilter' => true,
                'filter_key' => 'a!send',
                'callback' => 'showMailSentStatus',
            ),
            'order' => array(
                'type' => 'select',
                'title' => $this->module->l('Order Placed', 'adminkbbacksubscriberlistcontroller'),
                'align' => 'text-center',
                'list' => array(
                    0 => $this->module->l('No'),
                ),
                'havingFilter' => true,
                'filter_key' => 'a!order',
                'callback' => 'showOrderLink',
            ),
            'low_stock_mail' => array(
                'title' => $this->module->l('Low Stock Alert Mail Sent', 'adminkbbacksubscriberlistcontroller'),
                'list' => array(
                    0 => $this->module->l('Pending', 'adminkbbacksubscriberlistcontroller'),
                    1 => $this->module->l('Sent', 'adminkbbacksubscriberlistcontroller'),
                ),
                'type' => 'select',
                'havingFilter' => true,
                'align' => 'text-center',
                'filter_key' => 'a!low_stock_mail',
                'callback' => 'showLowStockMailSentStatus',
            ),
            'date_added' => array(
                'title' => $this->module->l('Date Added', 'adminkbbacksubscriberlistcontroller'),
                'type' => 'datetime',
                'havingFilter' => true,
                'filter_key' => 'a!date_added',
            ),
        );

        $this->_select .= 'a.product_id as id_image,a.*';
        $this->_where .= ' AND a.store_id ='.(int) $this->context->shop->id;
        $this->_group_by =  'a.id';
        $this->_orderBy =  'a.id';
        $this->_orderWay =  'desc';
        $this->addRowAction('delete');
        $this->addRowAction('viewcustom');
        $this->list_no_link = true;
    }
    
    public function showMailSentStatus($id_row, $tr)
    {
        unset($id_row);
        $mail_status = array(
            0 => $this->module->l('Pending', 'adminkbbacksubscriberlistcontroller'),
            1 => $this->module->l('Sent', 'adminkbbacksubscriberlistcontroller'),
        );
        return $mail_status[$tr['send']];
    }
    public function displayViewCustomLink($token = null, $id = 0, $name = null)
    {
        unset($name);
        unset($token);
        $tpl = $this->custom_smarty->createTemplate('list_action.tpl');
        $check_query = 'select * from ' . _DB_PREFIX_ . 'kb_bis_custom_field_mapping 
                where id_prouct_customer =' . (int)$id ;
        $check_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executes($check_query);
        $custom_detais = array();
        foreach ($check_data as $key => $val) {
            $field_val = $val['value'];
            $kbfield = new KbBisCustomFields($val['id_field'], Context::getContext()->language->id);
            if ($kbfield->active) {
                if (($kbfield->type == 'select') || ($kbfield->type == 'radio')) {
                    $option = json_decode($kbfield->value, true);
                    $value_opt = '';
                    foreach ($option as $opt) {
                        $store_value =  $val['value'];
                        $store_value =  str_replace('"', '', $store_value);
                        if ($opt['option_value'] == $store_value) {
                            $value_opt = $opt['option_label'];
                        }
                    }
                    $field_val = $value_opt;
                    $custom_detais[$key][$kbfield->label] = $field_val;
                } else {
                    $custom_detais[$key][$kbfield->label] = $field_val;
                }
            }
        }
        $tpl->assign(array(
            'href' => 'custom-details-' . $id,
            'action' => $this->module->l('View Details', 'adminkbbacksubscriberlistcontroller'),
            'icon' => 'search-plus',
            'heading' => $this->module->l('Additional Details', 'adminkbbacksubscriberlistcontroller'),
            'custom_detais' => !empty($custom_detais) ? ($custom_detais) : $this->module->l('No Data Found.', 'adminkbbacksubscriberlistcontroller')
        ));
        return $tpl->fetch();
    }
    /*
     * Default function, used here to include JS/CSS files for the module.
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addJS($this->getKbModuleDir() . 'views/js/admin/kbcustomfield_admin.js');
    }
    protected function getKbModuleDir()
    {
        return _PS_MODULE_DIR_.'backinstock/';
    }
    public function getProductName($id_row, $tr)
    {
        $product_name = '';
        $id_product = 0;
        if ($id_row != '') {
            $product_name = $id_row;
            $id_product = $tr['product_id'];
            $product_obj = new Product($tr['product_id']);
            if ((int) $tr['product_attribute_id'] > 0) {
                $attributes = $product_obj->getAttributesResume($this->context->language->id);
                if (is_array($attributes) && !empty($attributes)) {
                    foreach ($attributes as $attr_key => $attribute_data) {
                        if ($attribute_data['id_product_attribute'] == $tr['product_attribute_id']) {
                            $product_name .= ': '.$attribute_data['attribute_designation'];
                            break;
                        }
                    }
                }
            }
        }
        $admin_product_url = $this->context->link->getAdminLink('AdminProducts').'&updateproduct&id_product='.$id_product;
        $this->context->smarty->assign('url', $admin_product_url);
        $this->context->smarty->assign('name', $product_name);
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ .'backinstock/views/templates/admin/link_template.tpl');
    }
    
    public function getCustomerEmail($id_row, $tr)
    {
        $customer_email = $id_row;
        $id_customer = 0;
        if ($id_row != '') {
            $id_customer = $tr['customer_id'];
        }
        if ($id_customer) {
            $admin_customer_url = $this->context->link->getAdminlink('AdminCustomers') . '&id_customer=' . $id_customer. '&updatecustomer';
            $this->context->smarty->assign('url', $admin_customer_url);
            $this->context->smarty->assign('name', $customer_email);
            return $this->context->smarty->fetch(_PS_MODULE_DIR_ .'backinstock/views/templates/admin/link_template.tpl');
        } else {
            return $customer_email;
        }
    }
    
    public function showLowStockMailSentStatus($id_row, $tr)
    {
        unset($id_row);
        $mail_status = array(
            0 => $this->module->l('Pending', 'adminkbbacksubscriberlistcontroller'),
            1 => $this->module->l('Sent', 'adminkbbacksubscriberlistcontroller'),
        );
        return $mail_status[$tr['low_stock_mail']];
    }
    public function showOrderLink($id_row, $tr)
    {
        if ($id_row) {
            $order_obj = new Order($id_row);
            $order_url = $this->context->link->getAdminLink('AdminOrders') . '&id_order=' . $id_row.'&vieworder';
            $this->context->smarty->assign('url', $order_url);
            $this->context->smarty->assign('name', $order_obj->getUniqReference());
            return $this->context->smarty->fetch(_PS_MODULE_DIR_ .'backinstock/views/templates/admin/link_template.tpl');
        } else {
            return $this->module->l('No Order Placed', 'adminkbbacksubscriberlistcontroller');
        }
    }
    
    public function initContent()
    {
        if (isset($this->context->cookie->kb_redirect_success)) {
            $this->confirmations[] = $this->context->cookie->kb_redirect_success;
            unset($this->context->cookie->kb_redirect_success);
        }
        if (isset($this->context->cookie->kb_redirect_warning)) {
            $this->warnings[] = $this->context->cookie->kb_redirect_warning;
            unset($this->context->cookie->kb_redirect_warning);
        }
        parent::initContent();
    }
    
    /**
     * Get edit link
     */
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
    
    public function processDelete()
    {
        if (Tools::getIsset('deleteproduct_update_product_detail') && Tools::getIsset('id')) {
            $id = Tools::getValue('id');
            $sql = 'DELETE FROM `'._DB_PREFIX_.'product_update_product_detail` WHERE' .
                ' id = "'.pSQL($id).'" ';
            if (Db::getInstance()->execute($sql)) {
                $this->confirmations[] = $this->module->l('Subscriber deleted successfully.', 'adminkbbacksubscriberlistcontroller');
            }
        }
    }
    public function showCoverImage($id_row, $row_data)
    {
        if (!empty($row_data['product_id'])) {
            $product = new ProductCore($row_data['product_id']);
            $coverImage = $product->getCover($row_data['product_id']);

            if (!empty($coverImage)) {
                $path_to_image = _PS_IMG_DIR_ . 'p/' . Image::getImgFolderStatic($coverImage['id_image']) . (int) $coverImage['id_image'] . '.' . $this->imageType;
                return ImageManagerCore::thumbnail($path_to_image, 'product_mini_' . $row_data['product_id'] . '_' . $this->context->shop->id . '.' . $this->imageType, 45, $this->imageType);
            }
        }
    }
    
    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['csv_export'] = array(
            'href' => self::$currentIndex . '&export' . $this->table . '&token=' . $this->token,
            'desc' => $this->module->l('Export Subscribers', 'adminkbbacksubscriberlistcontroller'),
            'icon' => 'process-icon-export'
        );
        $this->page_header_toolbar_btn['manual_trigger'] = array(
            'href' => self::$currentIndex . '&manualStockTrigger' . $this->table . '&token=' . $this->token,
            'desc' => $this->module->l('Manual Stock Trigger', 'adminkbbacksubscriberlistcontroller'),
            'icon' => 'process-icon-refresh'
        );
        parent::initPageHeaderToolbar();
    }
    
    public function postProcess()
    {
        if (Tools::isSubmit('exportproduct_update_product_detail')) {
            return $this->processExportKbCSV();
        }
        if (Tools::isSubmit('manualStockTriggerproduct_update_product_detail')) {
            return $this->processManualStockTrigger();
        }
        
        parent::postProcess();
    }
    
    public function processManualStockTrigger()
    {
        /**
         * To delete the theme mails folder if exists
         * @date 06-03-2023
         * @author Kanishka Kannoujia
         * @commenter Prvind Panday
         */
        $backinstock = new BackInStock();
        if (file_exists(_PS_THEME_DIR_ . 'modules/backinstock/mails')) {
            $backinstock->deleteDir(_PS_THEME_DIR_ . 'modules/backinstock/mails');
        }
        $send_mails_count = 0;
        $get_data = 'select * from ' . _DB_PREFIX_ . 'product_update_product_detail a 
        where active=1 and send="0"';
        $user_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_data);
        foreach ($user_data as $user) {
            $quantity_query = 'select quantity from ' . _DB_PREFIX_
                    . 'stock_available where id_product_attribute='
                    . (int) $user['product_attribute_id'] . ' and id_product=' . (int) $user['product_id'];
            $quantity_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($quantity_query);
            if ($quantity_data[0]['quantity'] > 0) {
                $id_image = Product::getCover($user['product_id']);
                $current = Product::getPriceStatic($user['product_id'], true, null, 6);
                if (is_array($id_image) && count($id_image) > 0) {
                    $image = new Image($id_image['id_image']);
                    $img_path = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . '.jpg';
                }

                $link = $this->context->link->getModuleLink('backinstock', 'delete');

                $url = $this->context->link->getProductLink($user['product_id']);

                $dot_found = 0;
                $needle = '.php';
                $dot_found = strpos($link, $needle);
                if ($dot_found !== false) {
                    $ch = '&';
                } else {
                    $ch = '?';
                }

                $shop_id = Context::getContext()->shop->id;
                $lang_id = $this->context->cookie->id_lang;
                $cid = $user['product_attribute_id'];
                $id = $user['product_id'];
                $cemail = urlencode($user['email']);
                $delete_url = $link . $ch . 'email=' . $cemail . '&id=' . $id .
                        '&attribute_id=' . $cid . '&shop_id=' . $shop_id;
                $product_obj = new Product($user['product_id'], false, $lang_id, $shop_id);
                $attributes = $product_obj->getAttributeCombinationsById($user['product_attribute_id'], $this->context->cookie->id_lang);


                $product_name = $product_obj->name;
                $product_description = $product_obj->description_short;
                if (count($attributes) > 0) {
                    $attr = '';
                    foreach ($attributes as $attribute) {
                        $attr .= $attribute['group_name'] . ': ' . $attribute['attribute_name'] . ', ';
                    }
                    $attr = Tools::substr($attr, 0, -2);
                } else {
                    $attr = '';
                }

                if ((bool) Configuration::get('PS_SSL_ENABLED')) {
                    $ps_base_url = _PS_BASE_URL_SSL_;
                } else {
                    $ps_base_url = _PS_BASE_URL_;
                }
                $getsubject = 'select subject,body from ' . _DB_PREFIX_ . 'product_update_email_templates where id_lang='
                        . (int) $this->context->language->id . ' and template_no="2"';
                $data_subject = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($getsubject);
                //changes by vishal for adding related products functioanlity
                $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
                if ($data['enable_related_product_low_stock'] == 1) {
                    if ($data['related_product_method_low_stock'] == 1) {
                        $results = ProductSale::getBestSalesLight((int) $this->context->language->id, 0, 4);
                        if (!empty($results)) {
                            $kb_products = array();
                            foreach ($results as $key => $value) {
                                $kb_array = array();
                                $kb_product_obj = new Product($value['id_product']);
                                $kb_array['id_product'] = $value['id_product'];
                                $kb_array['description_short'] = $kb_product_obj->description_short[$this->context->language->id];
                                $kb_array['name'] = $kb_product_obj->name[$this->context->language->id];
                                $kb_array['link_rewrite'] = $kb_product_obj->link_rewrite[$this->context->language->id];
                                $kb_products[] = $kb_array;
                            }
                        } else {
                            $kb_products = array();
                        }
                    } else if ($data['related_product_method_low_stock'] == 2) {
                        $kb_prod_obj = new Product($user['product_id']);
                        /**
                        * Added a true parameter at 7th position to getProducts() to get only active products
                        * @date 26-01-2023
                        * @commenter Prvind Panday
                        */
                        $kb_products = Product::getProducts((int) $this->context->language->id, 0, 4, 'id_product', 'ASC', $kb_prod_obj->id_category_default, true);
                    } else if ($data['related_product_method_low_stock'] == 3) {
                        if (!empty($data['specific_products_low_stock'])) {
                            $kb_products = array();
                            $kb_array = array();
                            foreach ($data['specific_products_low_stock'] as $key => $value) {
                                $kb_product_obj = new Product($value);
                                /*
                                 * Added a condition to filter out the products which are not active and not in stock
                                 * @author Prvind Panday
                                 * @date 26-01-2023
                                 * @commenter Prvind Panday
                                 */
                                if ($kb_product_obj->active == 0 || $kb_product_obj->quantity == 0) {
                                    continue;
                                }
                                $kb_array['id_product'] = $value;
                                $kb_array['description_short'] = $kb_product_obj->description_short[$this->context->language->id];
                                $kb_array['name'] = $kb_product_obj->name[$this->context->language->id];
                                $kb_array['link_rewrite'] = $kb_product_obj->link_rewrite[$this->context->language->id];
                                $kb_products[] = $kb_array;
                            }
                        } else {
                            $kb_products = array();
                        }
                    }
                    if (!empty($kb_products)) {
                        $link = new Link();
                        $cart_html = "";
                        if (isset($data['initial_related_title'][$this->context->language->id]) && !empty($data['initial_related_title'][$this->context->language->id])) {
                            $heading = $data['final_related_title'][$this->context->language->id];
                        } else {
                            $heading = "RELATED PRODUCTS";
                        }
                        $kb_final_data = array();
                        foreach ($kb_products as $products) {
                            $kb_temp = array();
                            $kb_product_obj = new Product($products['id_product']);
                            $kb_id_image = $kb_product_obj->getImages((int) $this->context->language->id);
                            if (empty($kb_id_image)) {
                                continue;
                            }
                            if (!isset($products['attributes'])) {
                                $products['attributes'] = ' ';
                            }
                            if (!isset($products['name'])) {
                                $products['name'] = ' ';
                            }
                            if (!isset($products['description_short'])) {
                                $products['description_short'] = ' ';
                            }
                            if ((bool) Configuration::get('PS_SSL_ENABLED')) {
                                $kb_img_path = 'https://' . $link->getImageLink($products['link_rewrite'], $kb_id_image[0]['id_image']);
                            } else {
                                $kb_img_path = 'http://' . $link->getImageLink($products['link_rewrite'], $kb_id_image[0]['id_image']);
                            }
                            $id_lang = $this->context->language->id;
                            $kb_temp['name'] = $products['name'];
                            $kb_temp['image'] = $kb_img_path;
                            if (isset($data['enable_utm']) && $data['enable_utm'] == 1) {
                                $utm_paramters = 'utm_source=' . $data['product_update_utm_source'] . '&utm_medium=' . $data['product_update_utm_medium'] . '&utm_campaign=' . $data['product_update_utm_campaign'];
                                $kb_product_link_new = $this->context->link->getProductLink($kb_product_obj, null, null, null, $id_lang, $shop_id);
                                if (strpos($kb_product_link_new, '?') !== false) {
                                    $kb_product_link_new .= '&' . $utm_paramters;
                                } else {
                                    $kb_product_link_new .= '?' . $utm_paramters;
                                }
                            } else {
                                $kb_product_link_new = $this->context->link->getProductLink($kb_product_obj, null, null, null, $id_lang, $shop_id);
                            }
                            $kb_price = Tools::displayPrice(Product::getPriceStatic($products['id_product']));
                            $kb_temp['kb_product_link_new'] = $kb_product_link_new;
                            $kb_temp['price'] = $kb_price;
                            $kb_final_data[] = $kb_temp;
                        }
                    }
                }
                $this->context->smarty->assign('kb_heading', $heading);
                $this->context->smarty->assign('kb_product', $kb_final_data);
                $cart_html = $this->context->smarty->fetch(_PS_MODULE_DIR_ .'backinstock/views/templates/front/final_mail_content.tpl');
                if (!empty($kb_products)) {
                    $kb_cart_html = $cart_html;
                } else {
                    $kb_cart_html = "";
                }
                $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
                $id_lang = $this->context->language->id;
                if (isset($data['enable_utm']) && $data['enable_utm'] == 1) {
                    $utm_paramters = 'utm_source=' . $data['product_update_utm_source'] . '&utm_medium=' . $data['product_update_utm_medium'] . '&utm_campaign=' . $data['product_update_utm_campaign'];
                    $kb_product_link = $this->context->link->getProductLink($product_obj, null, null, null, $id_lang, $shop_id);
                    if (strpos($kb_product_link, '?') !== false) {
                        $kb_product_link .= '&' . $utm_paramters;
                    } else {
                        $kb_product_link .= '?' . $utm_paramters;
                    }
                } else {
                    $kb_product_link = $this->context->link->getProductLink($product_obj, null, null, null, $id_lang, $shop_id);
                }
                if (strpos($kb_product_link, '?') !== false) {
                    $kb_product_link .= '&via=email' ;
                } else {
                    $kb_product_link .= '?via=email';
                }
                //changes end
                /*
                 * Modified the current_price to show correct price with tax for the customer group 
                 * @date 27-01-2023
                 * @author Prvind Panday
                 */
                // get customer session
                $kb_product_obj = new product($alert_data['product_id']);
                $id_group = $this->context->customer->id_default_group;
                $group = new group($id_group);
                $current_price = Tools::displayPrice(
                    $kb_product_obj->getPriceStatic(
                        $alert_data['product_id'], 
                        $group->price_display_method ? false : true,  
                        $alert_data['combination_id'],
                        6, 
                        null, 
                        false, 
                        true
                    )
                );
                /*
                 * Checked whether the product has specific price or not, if yes then it will show the specific price with tax or without tax for the customer group
                 * @date 30-01-2023
                 * @commenter Prvind Panday
                 */
                if (!empty($kb_product_obj->specific_prices)) {
                    $current_price = Tools::displayPrice(
                        $kb_product_obj->getPriceStatic(
                            $alert_data['product_id'], 
                            $group->price_display_method ? false : true,  
                            $alert_data['combination_id'],
                            6, 
                            null, 
                            false, 
                            false
                        )
                    );
                }
                /*
                 * Template variables created for the email content, the variables are replaced in the email content
                 * @date 30-01-2023
                 * @commenter Prvind Panday
                 */
                /*
                 * Checked the image path is not empty and if empty then set the default image path else blank
                 * @author Prvind Panday
                 * @date 31-01-2023
                 * @commenter Prvind Panday
                 */
                $template_vars = array(
                    '{template}' => $data_subject['body'],
                    '{related_product_content}' => $kb_cart_html,
                    '{minimal_image}' => $this->context->link->getMediaLink(
                        __PS_BASE_URI__ . 'modules/backinstock/views/img/minimal6.png'
                    ),
                    '{product_description}' => $product_description,
                    '{product_link}' => $kb_product_link,
                    '{product_image}' => isset($img_path) ? $img_path : '',
                    '{product_name}' => $product_name,
                    '{current_price}' => $current_price,
                    '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                    '{shop_url}' => _PS_BASE_URL_ . __PS_BASE_URI__,
                    'ps_root_path' => $ps_base_url
                    . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', ''),
                    '{url}' => $url
                );
                unset($product_obj);
                $subject = $data_subject['subject'];
                $email = $user['email'];
                $lang_iso = $user['lang_iso'];
                $id_lang = Language::getIdByIso($lang_iso);
                if (empty($lang_iso)) {
                    $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
                } else {
                    $id_lang = Language::getIdByIso($lang_iso);
                }
                if (Mail::Send($id_lang, 'quantity_drop', $subject, $template_vars, $email, null, Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'), null, null, dirname(__FILE__) . '/mails/', false, $this->context->shop->id)) {
                    $update_time = 'update ' . _DB_PREFIX_ . 'product_update_product_detail'
                            . ' set mail_send_date=now(),send="1" where id=' . (int) $user['id'];
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_time);
                    $send_mails_count++;
                    $check_sql = 'select * from ' . _DB_PREFIX_ . 'product_update_product_stats where id = 1';
                    $res_sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($check_sql);
                    if (!empty($res_sql)) {
                        $total_sent = (int)$res_sql['total_sent'] + 1;
                        $update_stats = 'update `' . _DB_PREFIX_ . 'product_update_product_stats` SET total_sent = '.(int)$total_sent.' date_updated = now()';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_stats);
                    } else {
                        $insert_stats = 'INSERT into `' . _DB_PREFIX_ . 'product_update_product_stats` SET total_sent = 1, total_opened = 0, total_buy_now_clicks = 0, total_view_clicks = 0, date_added = now(), date_updated = now()';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($insert_stats);
                    }
                }
            }
        }
        if ((int) $send_mails_count > 0) {
            $this->context->cookie->__set(
                'kb_redirect_success',
                $send_mails_count . $this->module->l(' Back In stock Mails send to Subscribers.', 'adminkbbacksubscriberlistcontroller')
            );
        } else {
            $this->context->cookie->__set(
                'kb_redirect_warning',
                $this->module->l('No manual update in product quantity of any of the subscribed product.', 'adminkbbacksubscriberlistcontroller')
            );
        }
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminKbBackSubscriberList', true)
        );
    }
    
    public function processExportKbCSV()
    {
        $data_type = Tools::getValue('data_type', '');
        $sql_connection_query = 'Select * from ' . _DB_PREFIX_ . 'product_update_product_detail';
        $download_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_connection_query);
        //start by dharmanshu fro cuto field showing 21-08-2021
        $sql_query_custom_field = 'Select * from ' . _DB_PREFIX_ . 'kb_bis_fields_lang fl
            LEFT JOIN ' . _DB_PREFIX_ . 'kb_bis_fields fm ON fl.id_field =  fm.id_field

            where fl.id_lang = '.Context::getContext()->language->id.' 
            AND
            fm.active = 1 
            order by fl.id_field ASC';
        $custom_field_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_query_custom_field);
         
        $custm_field_array = [];
        foreach ($custom_field_data as $key => $value) {
                $custm_field_array[$value['id_field']] = $value['label'];
             //array_push($custm_field_array,$value['label']);
        }
        if (!empty($custm_field_array)) {
            ksort($custm_field_array);
        }
        
        
        if (count($download_data) > 0) {
            $header_array = array(
                $this->module->l('id', 'adminkbbacksubscriberlistcontroller'),
                $this->module->l('Email', 'adminkbbacksubscriberlistcontroller'),
                $this->module->l('Product Name', 'adminkbbacksubscriberlistcontroller'),
                $this->module->l('Reference', 'adminkbbacksubscriberlistcontroller'),
                $this->module->l('Combination', 'adminkbbacksubscriberlistcontroller'),
                $this->module->l('Date', 'adminkbbacksubscriberlistcontroller'),
                $this->module->l('Back In Stock Email Status', 'adminkbbacksubscriberlistcontroller'),
                $this->module->l('Low Stock Alert Email Status', 'adminkbbacksubscriberlistcontroller'),
            );
              $header_array = array_merge($header_array, $custm_field_array);
            //end by dharmanshu fro cuto field showing 21-08-2021
            $export_data = array();
            $mail_status = array(
                0 => $this->module->l('Pending', 'adminkbbacksubscriberlistcontroller'),
                1 => $this->module->l('Sent', 'adminkbbacksubscriberlistcontroller'),
            );
            foreach ($download_data as $data_key => $data) {
                $detail = array();
                //start by dharmanshu for ading teh custom field data and product details 21-08-2021
                $check_query = 'select * from ' . _DB_PREFIX_ . 'kb_bis_custom_field_mapping 
                where id_prouct_customer =' . (int)$data['id'].' order by id_field ASC';
                $check_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executes($check_query);
                   
                $custom_detais = array();
                foreach ($check_data as $key => $val) {
                    $field_val = $val['value'];
                    $kbfield = new KbBisCustomFields($val['id_field'], Context::getContext()->language->id);
                  
                    if ($kbfield->active) {
                        if (($kbfield->type == 'select') || ($kbfield->type == 'radio')) {
                            $option = json_decode($kbfield->value, true);
                            $value_opt = '';
                            foreach ($option as $opt) {
                                $store_value =  $val['value'];
                                $store_value =  str_replace('"', '', $store_value);
                                if ($opt['option_value'] == $store_value) {
                                    $value_opt = $opt['option_label'];
                                }
                            }
                            $field_val = $value_opt;
                            $custom_detais [$val['id_field']] = $field_val;
                        } else {
                            $custom_detais [$val['id_field']] = $field_val;
                        }
                    }
                }
                //start if not having all custom field data then add emty data for other custom fields
                if (!empty($custom_detais)) {
                    ksort($custom_detais);
                    foreach ($custm_field_array as $key => $value) {
                        if (!array_key_exists($key, $custom_detais)) {
                            $custom_detais[$key] = '';
                        }
                    }
                    ksort($custom_detais);
                }
                //end
                $product_data = new Product($data['product_id']);
                
               
                
               // $this->product = new Product($product['id_product']);
                $product_obj = new Product($data['product_id']);
                $attribute_product_data = '';
                $attributes = $product_obj->getAttributesResume($this->context->language->id);
                if (is_array($attributes) && !empty($attributes)) {
                    foreach ($attributes as $attr_key => $attribute_data) {
                        if ($attribute_data['id_product_attribute'] == $data['product_attribute_id'] && isset($attribute_data['attribute_designation'])) {
                            $attribute_product_data .= ''.$attribute_data['attribute_designation'];
                            break;
                        }
                    }
                }
                
                $detail = array(
                    $data['id'],
                    $data['email'],
                    $data['product_name'],
                    $product_data->reference,
                    $attribute_product_data,
                    $data['date_added'],
                    $mail_status[$data['send']],
                    $mail_status[$data['low_stock_mail']]
                );
                $detail = array_merge($detail, $custom_detais);
                //end by dharmanshu for ading teh custom field data and product details 21-08-2021
                $export_data[] = $detail;
            }
            $this->kbCsvExport($header_array, $export_data);
            return true;
        }
    }
    
    public function kbCsvExport($header_array, $download_data)
    {
        $filename = "kb_subscribers.csv";
        $file = fopen('php://output', 'w');
        header("Content-Transfer-Encoding: Binary");
        header('Content-Type: application/excel');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        ob_clean();
        fputcsv($file, $header_array, ',');
        if (count($download_data)) {
            foreach ($download_data as $p_data) {
                fputcsv($file, $p_data, ',');
            }
        }
        fclose($file);
        die();
    }
    
    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }
}
