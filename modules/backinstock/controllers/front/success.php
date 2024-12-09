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
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
*/

include_once(_PS_MODULE_DIR_ . 'backinstock/libraries/drewm/mailchimp-api/src/MailChimp.php');
include_once(_PS_MODULE_DIR_ . 'backinstock/libraries/sendinBlue/Mailin.php');
include_once(_PS_MODULE_DIR_ . 'backinstock/classes/KbBisCustomFieldMapping.php');
include_once(_PS_MODULE_DIR_ . 'backinstock/classes/KbBisCustomFields.php');

class BackInStockSuccessModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $account_link = $this->context->link->getPageLink('index', 'true');
        $this->context->smarty->assign('acc_link', $account_link);
        $this->context->smarty->assign('pr_success_text', $this->module->l('Product Update Success'));
        $this->context->smarty->assign('pr_success_msg', $this->module->l('Successfully deleted the product.'));
        $this->context->smarty->assign('pr_go_to_home', $this->module->l('Go to Home Page'));
        $this->setTemplate('success.tpl');
    }

    public function postProcess()
    {
        
        $render = Tools::getValue('render');
        if ($render == 'add') {
            //changes by gopi for v3 verification start here
            $recaptcha = Configuration::get('KB_BACKINSTOCK_RECAPTCHA_ENABLE');
            if ($recaptcha) {
                if (!empty(Tools::getValue('kb_recaptcha_response'))) {
                    $this->v3RecaptchaVerification(Tools::getValue('kb_recaptcha_response'));
                } else {
                    echo json_encode(array('status' => 2));
                    die;
                }
            }
            //changes by gopi end here
            $alert_data = array();
            /**
             * Tools::displayPrice() is used to display the price in the current currency format which was not done before for current_price in $alert_data array.
             * @date 28-03-2023
             * @author Prvind Panday
             */
            $alert_data['current_price'] = Tools::displayPrice(Product::getPriceStatic(Tools::getValue('product_id'), true, null, 6));
            $alert_data['product_id'] = Tools::getValue('product_id');
            $alert_data['customer_id'] = Tools::getValue('customer_id');
            if (Tools::getValue('customer_id') == 0) {
                $customer_id = Customer::customerExists(Tools::getValue('customer_email_back'), true);
                if ($customer_id) {
                    $alert_data['customer_id'] = $customer_id;
                }
            }
            $alert_data['combination_id'] = Tools::getValue('combination_id');
            $quantity = Db::getInstance()->getRow(
                'SELECT quantity FROM '._DB_PREFIX_.'stock_available'
                . ' WHERE id_product='. (int) Tools::getValue('product_id') .' '
                . 'AND id_product_attribute='. (int) Tools::getValue('combination_id') .' '
                . 'AND id_shop='.(int) $this->context->shop->id
            );
            $alert_data['stock_quantity'] = $quantity['quantity'];
            $alert_data['currency_code'] = $this->context->currency->iso_code;
            $alert_data['currency_id'] = $this->context->currency->id;
            $alert_data['shop_id'] = $this->context->shop->id;
            $alert_data['customer_email_back'] = Tools::getValue('customer_email_back');
            $alert_data['req_quan'] = Tools::getValue('customer_quantity_back');
            $alert_data['current_format'] = Tools::displayPrice(Product::getPriceStatic(Tools::getValue('product_id'), true, null, 6));
            $alert_data['subscribe_type_back'] = Tools::getValue('subscribe_type_back');
            
            /*
             * prepared data is passed to the function addSubscribers to add the subscriber in our database
             * @date 30-01-2023
             * @commenter Prvind Panday
             */
            if (Tools::getIsset('product_id') && Tools::getIsset('customer_email_back')) {
                echo $this->addSubscribers($alert_data);
                die;
            } else {
                echo json_encode(array('status' => 3));
                die;
            }
        }
    }
    
    public static function getIdProductAttributesByIdAttributes($id_product, $id_attributes)
    {
        if (!is_array($id_attributes)) {
            return 0;
        }

        return Db::getInstance()->getValue(
            'SELECT pac.`id_product_attribute`
            FROM `'._DB_PREFIX_.'product_attribute_combination` pac
            INNER JOIN `'._DB_PREFIX_.'product_attribute` pa 
            ON pa.id_product_attribute = pac.id_product_attribute
            WHERE id_product = '.(int)$id_product.' AND id_attribute IN ('
            .implode(',', array_map('intval', $id_attributes)).')
            GROUP BY id_product_attribute
            HAVING COUNT(id_product) = '. (int) count($id_attributes)
        );
    }
    //changes by gopi :function to ceck v3
    public function v3RecaptchaVerification($values)
    {
        // Build POST request:
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_secret = Configuration::get('KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY');
        $recaptcha_response = $values;
        $recaptcha_result = Tools::file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
        $recaptcha_result = json_decode($recaptcha_result);
        // Take action based on the score returned:
        if (isset($recaptcha_result->score) && $recaptcha_result->score >= 0.5) {
        } else {
            echo json_encode(array('status' => 2));
            die;
        }
    }
    //changes end here by gopi
    public function addSubscribers($alert_data)
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
        $id_image = Product::getCover($alert_data['product_id']);
        if (count($id_image) > 0) {
            $image = new Image($id_image['id_image']);
            $img_path = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . '.jpg';
        }

        $shop_id = Context::getContext()->shop->id;
        $lang_id = $this->context->cookie->id_lang;
        $link = $this->context->link->getModuleLink('backinstock', 'delete');
        $dot_found = 0;
        $needle = '.php';
        $dot_found = strpos($link, $needle);
        if ($dot_found !== false) {
            $ch = '&';
        } else {
            $ch = '?';
        }
        $cid = $alert_data['combination_id'];
        $id = $alert_data['product_id'];
        //$cemail = $alert_data['customer_email_back'];
        $cemail = urlencode($alert_data['customer_email_back']);
        $delete_url = $link . $ch . 'email=' . $cemail . '&id=' . $id .
                '&attribute_id=' . $cid . '&shop_id=' . $shop_id;

        $product_obj = new Product($alert_data['product_id'], false, $lang_id, $shop_id);
        $attributes = $product_obj->getAttributeCombinationsById(
            $alert_data['combination_id'],
            $this->context->cookie->id_lang
        );

        $product_name = $product_obj->name;
        if (count($attributes) > 0) {
            $alert_data['attributes'] = '';
            foreach ($attributes as $attribute) {
                $alert_data['attributes'] .= $attribute['group_name'] . ': ' . $attribute['attribute_name'] . ', ';
            }
            $alert_data['attributes'] = Tools::substr($alert_data['attributes'], 0, -2);
        } else {
            $alert_data['attributes'] = '';
        }

        unset($product_obj);

        $check_query = 'select count(*) as if_exist from ' . _DB_PREFIX_ . 'product_update_product_detail 
            where email="' . pSQL($alert_data['customer_email_back']) . '" and send="0" and product_id=' . (int) $alert_data['product_id'] . ' AND
                        product_attribute_id = ' . (int) $alert_data['combination_id'] .
                ' and store_id=' . (int) $shop_id;
        $check_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($check_query);
        if ($check_data['if_exist'] == 1) {
            return json_encode(array('status' => 0));
        } else {
            $shop_id = Context::getContext()->shop->id;
            $lang_id = $this->context->cookie->id_lang;
            $product_obj = new Product($alert_data['product_id'], false, $lang_id, $shop_id);
            $getsubject = 'select id_category from ' . _DB_PREFIX_ .
                    'category_product where id_product=' . (int) $alert_data['product_id'];
            $data_subject = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($getsubject);
            $p = 0;
            $temp_data = array();
            foreach ($data_subject as $data) {
                $temp_data[$p] = ($data['id_category']);
                $p++;
            }
            $alert_data['category_id'] = implode(',', $temp_data);
            $alert_data['sku'] = $product_obj->reference;

            $lang_iso = Language::getIsoById(Context::getContext()->language->id);

            $insert_query = 'insert into ' . _DB_PREFIX_ . 'product_update_product_detail 
				values ("","' . pSQL($alert_data['customer_email_back']) . '"
				,' . (int) $alert_data['customer_id'] . ',
				' . (int) $alert_data['product_id'] . ',
				"' . pSQL($product_name) . '",
				"' . pSQL($alert_data['category_id']) . '",
				"' . pSQL($alert_data['sku']) . '",
				' . (int) $alert_data['combination_id'] . ',
				' . (float) $alert_data['current_price'] . ',
				"' . pSQL($alert_data['subscribe_type_back']) . '",
				"' . pSQL($alert_data['currency_code']) . '",
				' . (int) $alert_data['shop_id'] . ',0,0,' . (int) $alert_data['req_quan'] . ',"' . pSQL($lang_iso) . '","0","0",1,"","0",now(),now(),now())';
            if ((bool) Configuration::get('PS_SSL_ENABLED')) {
                $ps_base_url = _PS_BASE_URL_SSL_;
            } else {
                $ps_base_url = _PS_BASE_URL_;
            }
            $productupdate = new BackInStock();
            $getsubject = 'select subject,body from ' . _DB_PREFIX_ . 'product_update_email_templates where id_lang='
                    . (int) $this->context->language->id . ' and template_no="1"';
            $data_subject = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($getsubject);
            //changes by vishal for adding related products functioanlity
            $heading = '';
            $kb_final_data = array();
            $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
            if (isset($data['enable_related_product_initial']) && $data['enable_related_product_initial'] == 1) {
                if ($data['related_product_method_initial'] == 1) {
                    /**
                     * Changed the no of products 4 to 10 to get more products
                     * @date 28-03-2023
                     * @commenter Prvind Panday
                     * @author Prvind Panday
                     */
                    $results = ProductSale::getBestSalesLight((int) $this->context->language->id, 0, 10);
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
                } else if ($data['related_product_method_initial'] == 2) {
                    $kb_prod_obj = new Product(Tools::getValue('product_id'));
                    /**
                     * Added a true parameter at 7th position to getProducts() to get only active products
                     * @date 26-01-2023
                     * @commenter Prvind Panday
                     */
                    $kb_products = Product::getProducts((int) $this->context->language->id, 0, 4, 'id_product', 'ASC', $kb_prod_obj->id_category_default, true);
                } else if ($data['related_product_method_initial'] == 3) {
                    if (!empty($data['specific_products_initial'])) {
                        $kb_products = array();
                        $kb_array = array();
                        foreach ($data['specific_products_initial'] as $key => $value) {
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
                    $heading = "RELATED PRODUCTS";
                    if (isset($data['initial_related_title'][$this->context->language->id]) && !empty($data['initial_related_title'][$this->context->language->id])) {
                        $heading = $data['initial_related_title'][$this->context->language->id];
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
            $cart_html = $this->context->smarty->fetch(_PS_MODULE_DIR_ .'backinstock/views/templates/front/initial_mail_content.tpl');
            if (!empty($kb_products)) {
                $kb_cart_html = $cart_html;
            } else {
                $kb_cart_html = "";
            }
            $id_lang = $this->context->language->id;
            if (isset($data['enable_utm']) && $data['enable_utm'] == 1) {
                $utm_paramters = 'utm_source=' . $data['product_update_utm_source'] . '&utm_medium=' . $data['product_update_utm_medium'] . '&utm_campaign=' . $data['product_update_utm_campaign'];
                $kb_product_link = $this->context->link->getProductLink($product_obj, null, null, null, $id_lang, $shop_id);
                if (strpos($kb_product_link, '?') !== false) {
                    $kb_product_link .= '&' . $utm_paramters;
                } else {
                    $kb_product_link .= '?' . $utm_paramters;
                }
                if (strpos($delete_url, '?') !== false) {
                    $delete_url .= '&' . $utm_paramters;
                } else {
                    $delete_url .= '?' . $utm_paramters;
                }
            } else {
                $kb_product_link = $this->context->link->getProductLink($product_obj, null, null, null, $id_lang, $shop_id);
            }
            if (strpos($kb_product_link, '?') !== false) {
                $kb_product_link .= '&via=email';
            } else {
                $kb_product_link .= '?via=email';
            }
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
            unset($kb_product_obj);
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
                '{best_seller_product}' => $productupdate->getBestSeller($alert_data['product_id']),
                '{thankyou_banner}' => $this->context->link->getMediaLink(
                    __PS_BASE_URI__ . 'modules/backinstock/views/img/thank-you-banners.jpg'
                ),
                '{product_link}' => $kb_product_link,
                '{product_image}' => isset($img_path) ? $img_path : '',
                '{product_name}' => $product_name,
                '{delete_link}' => $delete_url,
                '{current_price}' => $current_price,
                '{attributes}' => $alert_data['attributes'],
            );
            $subject = html_entity_decode($data_subject['subject']);
            $id_lang = $this->context->language->id;
            $email = $alert_data['customer_email_back'];
            $template = $alert_data['subscribe_type_back'];
            
            if (Mail::Send($id_lang,$template,$subject,$template_vars,$email,null,Configuration::get('PS_SHOP_EMAIL'),Configuration::get('PS_SHOP_NAME'),null,null,_PS_MODULE_DIR_ . 'backinstock/mails/',false,$this->context->shop->id)) {
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
            } 
            $directory = _PS_MODULE_DIR_ . 'backinstock/mails/' . $this->context->language->iso_code . '/';
            if (is_writable($directory)) {
                $html_template = $alert_data['subscribe_type_back'] . '.html';
                $txt_template = $alert_data['subscribe_type_back'] . '.txt';

                $base_html = Tools::file_get_contents(_PS_MODULE_DIR_ . "backinstock/views/templates/admin/html_content_initial.html");
                $template_html = str_replace('[template_content]', html_entity_decode($data_subject['body']), $base_html);
                $file = fopen($directory . $html_template, 'w+');
                fwrite($file, $template_html);
                fclose($file);

                $file = fopen($directory . $txt_template, 'w+');
                fwrite($file, $template_html);
                fclose($file);
            }
            if (Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($insert_query)) {
                //changes by gopifor adding custom feild data
                $id_product_customer = DB::getInstance()->Insert_ID();
                $availableFields = $this->getBisCustomFeild();
                foreach ($availableFields as $available) {
                    if (Tools::getIsset($available['field_name'])) {
                        if (($available['type']== 'select') || ($available['type'] == 'checkbox') || ($available['type'] == 'radio')) {
                            $field_value = Tools::jsonEncode(Tools::getValue($available['field_name']));
                        } elseif (($available['type'] == 'text' ||
                            $available['type'] == 'textarea')) {
                            $field_value = Tools::getValue($available['field_name']);
                        }
                        if ($field_value != '') {
                            $bis_map_obj = new KbBisCustomFieldMapping();
                            $bis_map_obj->id_prouct_customer = $id_product_customer;
                            $bis_map_obj->id_field = $available['id_field'];
                            $bis_map_obj->value = $field_value;
                            $bis_map_obj->save();
                        }
                    }
                }
                //changes end by gopi
                /*
                 * @author - Rishabh Jain
                 * To add the email to the enabled marketing list
                 */
                $sendPromotionalEmail = true;
                if ($sendPromotionalEmail) {
                    $db_settings = array();
                    if (Configuration::get('VELOCITY_BACK_STOCK_EMAIL_MARKETING')) {
                        $db_settings = Tools::unSerialize(Configuration::get('VELOCITY_BACK_STOCK_EMAIL_MARKETING'));
                        $firstname = null;
                        $lastname = null;
                        if ((int) $alert_data['customer_id'] > 0) {
                            $customer_obj = new Customer($alert_data['customer_id']);
                            $firstname = $customer_obj->firstname;
                            $lastname = $customer_obj->lastname;
                        }
                        if ($db_settings['mailchimp_status'] == 1) {
                            if ($firstname == null && $lastname == null) {
                                $this->mailchimpSubscribeEmailList($email);
                            } else if ($firstname == null) {
                                $this->mailchimpSubscribeEmailList($email, $firstname, null);
                            } else if ($lastname == null) {
                                $this->mailchimpSubscribeEmailList($email, null, $lastname);
                            } else {
                                $this->mailchimpSubscribeEmailList($email, $firstname, $lastname);
                            }
                        }
                        if ($db_settings['klaviyo_status'] == 1) {
                            if ($firstname == null && $lastname == null) {
                                $this->klaviyoSubscribeEmailList($email);
                            } else if ($firstname == null) {
                                $this->klaviyoSubscribeEmailList($email, $firstname, null);
                            } else if ($lastname == null) {
                                $this->klaviyoSubscribeEmailList($email, null, $lastname);
                            } else {
                                $this->klaviyoSubscribeEmailList($email, $firstname, $lastname);
                            }
                        }
                        if ($db_settings['SendinBlue_status'] == 1) {
                            if ($firstname == null && $lastname == null) {
                                $this->sendInBlueSubscribeEmailList($email);
                            } else if ($firstname == null) {
                                $this->sendInBlueSubscribeEmailList($email, $firstname, null);
                            } else if ($lastname == null) {
                                $this->sendInBlueSubscribeEmailList($email, null, $lastname);
                            } else {
                                $this->sendInBlueSubscribeEmailList($email, $firstname, $lastname);
                            }
                        }
                    }
                }
                /*
                 * Changes Over
                 */
                return json_encode(array('status' => 1));
            } else {
                return json_encode(array('status' => 2));
            }
//            return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($insert_query);
        }
    }
    //changes by gopi
    public function getBisCustomFeild()
    {
        return KbBisCustomFields::getAvailableBisCustomFields();
    }
    /*
    * Function to subscribe customer email to Mailchimp
    *
    * @param    string email   Email of customer
    * @param    string first_name   First name of customer
    * @param    string last_name   Last name of customer
    * @return   boolean Return  True if email is subscribed successfully otherwise returns error message
    */
    public function mailchimpSubscribeEmailList($email, $first_name = null, $last_name = null)
    {
        try {
            $db_settings = Tools::unserialize(Configuration::get('VELOCITY_BACK_STOCK_EMAIL_MARKETING'));
            $api_key = $db_settings['mailchimp_api'];
            $list_id = $db_settings['mailchimp_list'];
            $Mailchimp = new Mailchimp($api_key);
            $result = $Mailchimp->post("lists/$list_id/members", array(
                'email_address' => trim($email),
                'status' => 'subscribed',
            ));

            $subscriber_hash = $Mailchimp->subscriberHash(trim($email));
            $Mailchimp->patch("lists/$list_id/members/$subscriber_hash", array('merge_fields' => array('FNAME' => $first_name, 'LNAME' => $last_name)));
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * Function to subscribe customer email to SendinBlue List
     *
     * @param    string email   Email of customer
     * @param    string first_name   First name of customer
     * @param    string last_name   Last name of customer
     * @return   boolean Return  True if email is subscribed successfully otherwise returns error message
     * @date 26-01-2023
     * @commenter Prvind Panday
     * @comment Method Used From Spin & Win Module
     */
    public function sendInBlueSubscribeEmailList($email, $first_name = null, $last_name = null)
    {
        $db_settings = Tools::unserialize(Configuration::get('SPIN_WHEEL'));
        $apikey = $db_settings['SendinBlue_api'];
        $listid = $db_settings['SendinBlue_list'];
        $mailin = new KbSpinMailin('https://api.sendinblue.com/v2.0', $apikey);

        $data_arr = array(
            "email" => $email,
            "listid" => array($listid),
            "attributes" => array("NAME" => $first_name, "SURNAME" => $last_name)
        );

        $mailin->create_update_user($data_arr); //calling function to add user
        $listid = (int) $listid;
        /*changes made by vibhaas */
        $array_input = array(
            'listIds' => [$listid],
            'updateEnabled' => false,
            'email' => $email,
            'attributes' => array(
                'FIRSTNAME' => $first_name,
                'LASTNAME' => $last_name,
            ),
        );
        $array_input = json_encode($array_input);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendinblue.com/v3/contacts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $array_input,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "api-key: " . $apikey . "",
                "cache-control: no-cache",
                "content-type: application/json"
            ),
        )
        );

        $response = curl_exec($curl);
        curl_close($curl);
    }

    /*
    * Function to subscribe customer email to Klaviyo
    *
    * @param    string email   Email of customer
    * @param    string first_name   First name of customer
    * @param    string last_name   Last name of customer
    */
    public function klaviyoSubscribeEmailList($email, $first_name = null, $last_name = null)
    {
        $db_settings = Tools::unserialize(Configuration::get('VELOCITY_BACK_STOCK_EMAIL_MARKETING'));
        $api_key = $db_settings['klaviyo_api'];
        $list_id = $db_settings['klaviyo_list'];
        $properties = array();
        if ($first_name) {
            $properties['$first_name'] = $first_name;
        }
        if ($last_name) {
            $properties['$last_name'] = $last_name;
        }
        $properties_val = count($properties) ? urlencode(json_encode($properties)) : '{}';
        $fields = array(
            'api_key=' . $api_key,
            'email=' . urlencode($email),
            'confirm_optin=false',
            'properties=' . $properties_val,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://a.klaviyo.com/api/v1/list/' . $list_id . '/members');
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);
    }
}
