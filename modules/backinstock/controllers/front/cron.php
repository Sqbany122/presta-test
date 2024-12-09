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
 * @copyright 2020 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
 * Description
 *
 */
class BackinstockCronModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {        
        $configurations = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
        if (isset($configurations) && !empty($configurations) && $configurations['enable'] != 1) {
            echo $this->module->l('Please enable the module first.', 'cron');
            die;
        }
        
        $bck_obj = new Backinstock();
        
        if (!Tools::isSubmit('ajax')) {
            if (Tools::getValue('cron') == 'send_emails' && $configurations['enable_cron'] == 1) {
                $this->sendEmails();
            } else {
                echo $this->module->l('Please enable the cron functionality first.', 'cron');
                die;
            }
        } else {
            echo $this->module->l('You are not authorized to access this page', 'cron');
            die;
        }
        
        parent::initContent();
    }
    
    public function sendEmails()
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
        $get_data = 'select * from ' . _DB_PREFIX_ . 'product_update_product_detail a 
        where active=1 and send="0" and allowed_order = 1 and update_email = 0 and store_id='.(int) $this->context->shop->id;
        $user_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_data);
        $count = 0;
        foreach ($user_data as $user) {
            $quantity_query = 'select quantity from ' . _DB_PREFIX_
                    . 'stock_available where id_product_attribute='
                    . (int) $user['product_attribute_id'] . ' and id_product=' . (int) $user['product_id'].' and id_shop='.(int) $this->context->shop->id;
            $quantity_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($quantity_query);
//            if () {
                $id_image = Product::getCover($user['product_id']);
                $current = Product::getPriceStatic($user['product_id'], true, null, 6);
                /*
                 * Added is_array check before checking count of the array of the images of the product
                 * @author 
                 * @date 31-01-2023
                 * @commenter Prvind Panday
                 */
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
//                $lang_id = $this->context->cookie->id_lang;
                $lang_iso = $user['lang_iso'];
                if (empty($lang_iso)) {
                    $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
                } else {
                    $id_lang = Language::getIdByIso($lang_iso);
                }
                $cid = $user['product_attribute_id'];
                $id = $user['product_id'];
                $cemail = urlencode($user['email']);
                $delete_url = $link . $ch . 'email=' . $cemail . '&id=' . $id .
                        '&attribute_id=' . $cid . '&shop_id=' . $shop_id;
                $product_obj = new Product($user['product_id'], false, $id_lang, $shop_id);
                $attributes = $product_obj->getAttributeCombinationsById($user['product_attribute_id'], $id_lang);


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
                        . (int) $id_lang . ' and template_no="2"';
                $data_subject = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($getsubject);
                
                //changes by vishal for adding related products functioanlity
                $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
                if ($data['enable_related_product_final'] == 1) {
                    if ($data['related_product_method_final'] == 1) {
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
                    } else if ($data['related_product_method_final'] == 2) {
                        $kb_prod_obj = new Product((int) $user['product_id']);
                        /**
                        * Added a true parameter at 7th position to getProducts() to get only active products
                        * @date 26-01-2023
                        * @commenter Prvind Panday
                        */
                        $kb_products = Product::getProducts((int) $this->context->language->id, 0, 4, 'id_product', 'ASC', $kb_prod_obj->id_category_default, true);
                    } else if ($data['related_product_method_final'] == 3) {
                        if (!empty($data['specific_products_final'])) {
                            $kb_products = array();
                            $kb_array = array();
                            foreach ($data['specific_products_final'] as $key => $value) {
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
                        $this->context->smarty->assign('kb_heading', $heading);
                        $this->context->smarty->assign('kb_product', $kb_final_data);
                        $cart_html = $this->context->smarty->fetch(_PS_MODULE_DIR_ .'backinstock/views/templates/front/final_mail_content.tpl');
                    }
                }
                
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
                /*
                 * Modified the current_price to show correct price with tax for the customer group 
                 * @date 27-01-2023
                 * @author Prvind Panday
                 */
                // get customer session
                if (isset($user['customer_id']) && $user['customer_id'] != 0) {
                    $customer = new Customer($user['customer_id']);
                    $id_group = $customer->id_default_group;
                } else {
                    $id_group = $this->context->customer->id_default_group;
                }
                $group = new Group($id_group);
                $current_price = Tools::displayPrice(
                    $product_obj->getPriceStatic(
                        (int) $user['product_id'], 
                        $group->price_display_method ? false : true,  
                        (int) $user['product_attribute_id'],
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
                if (!empty($product_obj->specific_prices)) {
                    $current_price = Tools::displayPrice(
                        $product_obj->getPriceStatic(
                            (int) $user['product_id'], 
                            $group->price_display_method ? false : true,  
                            (int) $user['product_attribute_id'],
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
                //changes end
                $template_vars = array(
                    '{template}' => $data_subject['body'],
                    '{related_product_content}' => $kb_cart_html,
                    '{minimal_image}' => $this->context->link->getMediaLink(
                        __PS_BASE_URI__ . 'modules/backinstock/views/img/minimal6.png'
                    ),
                    '{product_description}' => $product_description,
                    '{product_link}' => $kb_product_link,
                    '{product_image}' => $img_path,
                    '{product_name}' => $product_name,
                    '{current_price}' => $current_price,
                    '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                    '{shop_url}' => _PS_BASE_URL_ . __PS_BASE_URI__,
                    'ps_root_path' => $ps_base_url
                    . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', ''),
                    '{url}' => $url
                );
                unset($product_obj);

                $subject = html_entity_decode($data_subject['subject']);
                $email = $user['email'];
                
                if (Mail::Send($id_lang, 'quantity_drop', $subject, $template_vars, $email, null, Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'), null, null, _PS_MODULE_DIR_ . 'backinstock/mails/', false, $this->context->shop->id)) {
                    $update_time = 'update ' . _DB_PREFIX_ . 'product_update_product_detail'
                            . ' set mail_send_date=now(),send="1" where id=' . (int) $user['id'];
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_time);
                    $check_sql = 'select * from ' . _DB_PREFIX_ . 'product_update_product_stats where id = 1';
                    $res_sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($check_sql);
                    if (!empty($res_sql)) {
                        $total_sent = (int)$res_sql['total_sent'] + 1;
                        $update_stats = 'update `' . _DB_PREFIX_ . 'product_update_product_stats` SET total_sent = '.(int)$total_sent.', date_updated = now()';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_stats);
                    } else {
                        $insert_stats = 'INSERT into `' . _DB_PREFIX_ . 'product_update_product_stats` SET total_sent = 1, total_opened = 0, total_buy_now_clicks = 0, total_view_clicks = 0, date_added = now(), date_updated = now()';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($insert_stats);
                    }
                    $count++;
                }
                $directory = _PS_MODULE_DIR_ . 'backinstock/mails/' . $lang_iso . '/';
                
                if (is_writable($directory)) {
                    $html_template = 'quantity_drop.html';
                    $txt_template = 'quantity_drop.txt';

                    $base_html = Tools::file_get_contents(_PS_MODULE_DIR_ . "backinstock/views/templates/admin/html_content_final.html");
                    $template_html = str_replace('[template_content]', html_entity_decode($data_subject['body']), $base_html);
                    $file = fopen($directory . $html_template, 'w+');
                    fwrite($file, $template_html);
                    fclose($file);

                    $file = fopen($directory . $txt_template, 'w+');
                    fwrite($file, $template_html);
                    fclose($file);
                }
//            }
        }
        if ($count > 0) {
            echo $this->module->l((int) $count . ' Mails sent.', 'cron');
            die;
        } else {
            echo $this->module->l('No Mails Sent', 'cron');
            die;
        }
    }
}