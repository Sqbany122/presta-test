<?php
/**
 *   ambBOcustomizer Module : customize the prestashop back-office
 *
 *    @author    Ambris Informatique
 *    @copyright Copyright (c) 2013-2015 Ambris Informatique SARL
 *    @license   Commercial license
 *    @module    BO Customizer (AmbBoCustomizer)
 *    @file      classes/AmbBackCaller.php
 *    @subject   Contains all specific callbacks
 *
 *            Support by mail: support@ambris.com
 */

class AmbBackCaller
{

    public static $counter = 0;
    public static $separator = '::::';

    public static function adminLink($value, $line)
    {
        //Expects :
        // _admin_controller
        // _params
        // _value
        $key = self::extractFieldName($value);
        $val = self::extractFieldValue($value);
        $admin_controller = isset($line[$key . '_controller']) ? $line[$key . '_controller'] : "";
        $val = isset($val) ? $val : "";
        $params = isset($line[$key . '_params']) ? $line[$key . '_params'] : "";
        $translator = isset($line[$key . '_translator']) ? $line[$key . '_translator'] : false;

        //Value HAS to be set in the select at the same value as the field identifier
        return '<a href="' . Context::getContext()->link->getAdminLink($admin_controller) . '&'
        . str_replace(
            'SELF',
            self::extractFieldValue($value),
            $params
        ) . '">' . ($translator ? Translate::getAdminTranslation($val, $translator) : $val
        )
            . '</a>';
    }

    public static function scoring($value, $line)
    {

        $star = '<span class="icon-star"></span>';
        $nostar = '<span class="icon-star-empty"></span>';
        return ($value > 0 ? str_repeat($star, $value) : '') . ($value < 5 ? str_repeat($nostar, 5 - $value) : '');
    }

    public static function simpleLink($value, $line)
    {
        //Expects :
        // _value
        // _url
        $key = self::extractFieldName($value);
        $val = self::extractFieldValue($value);

        $value = isset($val) ? $val : "";
        $url = isset($line[$key . '_url']) ? $line[$key . '_url'] : "";

        //Value HAS to be set in the select at the same value as the field identifier
        return '<a href="' . $url . '">' . $value . '</a>';
    }

    public static function multipleLinks($value, $line)
    {
        //Expects :
        // _value
        // _url
        $key = self::extractFieldName($value);
        $val = self::extractFieldValue($value);

        $values = isset($val) ? explode(',', $val) : "";
        $urls = isset($line[$key . '_url']) ? explode(',', $line[$key . '_url']) : "";
        $target = isset($line[$key . '_target']) ? explode(',', $line[$key . '_target']) : "";

        $return = '';

        for ($i = 0, $nb = count($values); $i < $nb; $i++) {
            if ($urls[$i] != 'nolink') {
                $return .= '<a href="' . str_replace('@', $values[$i], $urls[$i]) . '" ' . (isset($target[$i]) && $target[$i] != "" ? '"target="' . $target[$i] . '"' : "") . '>' . $values[$i] . '</a>';
            } else {
                $return .= $values[$i];
            }

            $return .= '<br />';
        }

        return $return;
    }

    public static function multipleAdminLinks($value, $line)
    {
        //Expects :
        // _value
        // _url
        // _admin_controller
        // _params
        $key = self::extractFieldName($value);
        $val = self::extractFieldValue($value);

        $values = isset($val) ? explode(',', $val) : "";
        $raw_ids = isset($line[$key . '_raw_ids']) ? explode(',', $line[$key . '_raw_ids']) : "";
        $ids = isset($line[$key . '_ids']) ? explode(',', $line[$key . '_ids']) : "";
        $admin_controller = isset($line[$key . '_controller']) ? $line[$key . '_controller'] : "";
        $params = isset($line[$key . '_params']) ? $line[$key . '_params'] : "";
        $callback = isset($line[$key . '_callback']) ? $line[$key . '_callback'] : false;

        $return = '';

        if ($callback) {
            if (is_array($raw_ids)) {
                $done = array();
                for ($i = 0, $nb = count($raw_ids); $i < $nb; $i++) {
                    if (!in_array($raw_ids[$i], $done)) {
                        $done[] = $raw_ids[$i];
                        $return .= '<a href="' . Context::getContext()->link->getAdminLink($admin_controller) . '&'
                        . $params . '&' . $ids[$i] . '">' . self::$callback($raw_ids[$i]) . '</a>';
                        $return .= '<br />';
                    }
                }
            }
        } else {
            for ($i = 0, $nb = count($values); $i < $nb; $i++) {
                $return .= '<a href="' . Context::getContext()->link->getAdminLink($admin_controller) . '&'
                    . $params . '&' . $ids[$i] . '">' . $values[$i] . '</a>';
                $return .= '<br />';
            }
        }

        return $return;
    }

    public static function multipleAdminLinksWithCallback($value, $line)
    {
        //Expects :
        // _value
        // _url
        // _admin_controller
        // _params
        $key = self::extractFieldName($value);
        $val = self::extractFieldValue($value);

        $values = isset($val) ? explode(',', $val) : "";
        //$callback =
        $ids = isset($line[$key . '_ids']) ? explode(',', $line[$key . '_ids']) : "";
        $admin_controller = isset($line[$key . '_controller']) ? $line[$key . '_controller'] : "";
        $params = isset($line[$key . '_params']) ? $line[$key . '_params'] : "";

        $return = '';

        for ($i = 0, $nb = count($values); $i < $nb; $i++) {
            $return .= '<a href="' . Context::getContext()->link->getAdminLink($admin_controller) . '&'
                . $params . '&' . $ids[$i] . '">' . $values[$i] . '</a>';
            $return .= '<br />';
        }

        return $return;
    }

    public static function tooltip($value, $line)
    {
        $key = self::extractFieldName($value);
        $val = self::extractFieldValue($value);

        $token = Tools::getAdminToken(
            'AdminAmbBoCustomizerAjax'
            . (int) (Tab::getIdFromClassName('AdminAmbBoCustomizerAjax'))
            . (int) (Context::getContext()->employee->id)
        );
        $method = isset($line[$key . '_method']) ? $line[$key . '_method'] : 'FetchData';
        $id = isset($line[$key . '_id']) ? $line[$key . '_id'] : '0';
        $value = isset($val)
        ? '<span class="badge">' . $val . '</span>'
        : '<i class="icon-search-plus"></i>';

        self::$counter++;
        return '<div class="text-center">
                    <span class="amb_bo_customizer_tooltip" role="button" tabindex="' . self::$counter
            . '" data-id="' . $id . '" data-token="' . $token . '" data-method="' . $method . '">' . $value . '
                    </span>
                </div>';
    }

    public static function preloadedTooltip($value, $line)
    {
        $key = self::extractFieldName($value);
        $val = self::extractFieldValue($value);
        $value = isset($val) ? $val : "";
        $display_value = isset($line[$key . '_display_value']) ? (bool) $line[$key . '_display_value'] : false;

        if (Tools::strlen($value) > 0) {
            self::$counter++;
            return '<a type="button" class="btn btn-link" data-toggle="popover"
                        title="" data-content="' . htmlentities($value, ENT_QUOTES) . '">'
                . ($display_value ? $value : '<i class="icon-search-plus"></i>') . '</button>';
        } else {
            return false;
        }
    }

    public static function extractFieldName($value)
    {
        $exploded = explode(self::$separator, $value);
        return $exploded[0];
    }

    public static function extractFieldValue($value)
    {
        $exploded = explode(self::$separator, $value);
        if (isset($exploded[1])) {
            return $exploded[1];
        } else {
            return false;
        }
    }

    public static function displayAddress($value, $line)
    {

        $key = self::extractFieldName($value);

        $id = isset($line[$key . '_address_id']) ? $line[$key . '_address_id'] : "";

        $address = new Address($id);
        return AddressFormat::generateAddress($address, array('avoid' => array('phone', 'phone_mobile')), "<br />");
    }

    public static function displayPhones($value, $line)
    {

        $key = self::extractFieldName($value);

        $id = isset($line[$key . '_address_id']) ? $line[$key . '_address_id'] : "";

        $address = new Address($id);
        return AddressFormat::generateAddress($address, array('avoid' => array('firstname', 'lastname', 'address1', 'address2', 'Country:name', 'postcode', 'city', 'company', 'state', 'vat_number', 'State:name')), "<br />");
    }

    //Specific fields callbacks

    public static function displayProductType($value, $line)
    {
        $key = self::extractFieldName($value);
        $val = self::extractFieldValue($value);

        $id_product = isset($val) ? $val : "";

        $product = new Product($id_product);
        $product_type = $product->getType();

        if ($product_type == Product::PTYPE_SIMPLE) {
            return Translate::getAdminTranslation('Standard product', 'AdminProducts');
        } elseif ($product_type == Product::PTYPE_PACK) {
            return Translate::getAdminTranslation('Pack', 'AdminProducts');
        } elseif ($product_type == Product::PTYPE_VIRTUAL) {
            return Translate::getAdminTranslation('Virtual Product', 'AdminProducts');
        } else {
            return false;
        }
    }

    public static function displayProductVisibility($value, $line)
    {
        $key = self::extractFieldName($value);
        $val = self::extractFieldValue($value);
        $id_product = isset($val) ? $val : "";
        $product = new Product($id_product);

        if ($product->visibility == 'both') {
            return Translate::getAdminTranslation('Everywhere', 'AdminProducts');
        } elseif ($product->visibility == 'catalog') {
            return Translate::getAdminTranslation('Catalog only', 'AdminProducts');
        } elseif ($product->visibility == 'search') {
            return Translate::getAdminTranslation('Search only', 'AdminProducts');
        } elseif ($product->visibility == 'none') {
            return Translate::getAdminTranslation('Nowhere', 'AdminProducts');
        } else {
            return false;
        }
    }

    public static function displayProductState($value, $line)
    {
        $key = self::extractFieldName($value);
        $val = self::extractFieldValue($value);
        $id_product = isset($val) ? $val : "";
        $product = new Product($id_product);

        if ($product->condition == 'new') {
            return Translate::getAdminTranslation('New', 'AdminProducts');
        }
        if ($product->condition == 'used') {
            return Translate::getAdminTranslation('Used', 'AdminProducts');
        }
        if ($product->condition == 'refurbished') {
            return Translate::getAdminTranslation('Refurbished', 'AdminProducts');
        } else {
            return false;
        }
    }

    public static function displayCategoryImage($value, $line)
    {
        $key = self::extractFieldName($value);
        $id_category = isset($line[$key . '_id']) ? $line[$key . '_id'] : "";

        $image = _PS_CAT_IMG_DIR_ . $id_category . '.jpg';
        $image_url = ImageManager::thumbnail(
            $image,
            Context::getContext()->controller->table
            . '_' . (int) $id_category . '.'
            . Context::getContext()->controller->imageType,
            350,
            Context::getContext()->controller->imageType,
            true,
            true
        );

        return $image_url;
    }

    public static function printOrderSlipPdf($value, $line)
    {
        $line;
        return $value;
    }

    public static function append($value, $line)
    {
        $key = self::extractFieldName($value);
        $val = self::extractFieldValue($value);
        $value = $val;
        $to_append = $line[$key . '_to_append'];

        return $value . $to_append;
    }

    public static function displayInvoiceNumber($id)
    {
        $invoice = new OrderInvoice($id);
        if (Validate::isLoadedObject($invoice)) {
            return $invoice->getInvoiceNumberFormatted(Context::getContext()->language->id);
        } else {
            return "";
        }
    }
    public static function displayOrderSlipNumber($id)
    {
        if ((int) $id > 0) {
            return sprintf('%06d', $id);
        } else {
            return "";
        }
    }
    public static function displayDeliveryNumber($id)
    {
        $order = new Order($id);
        if (Validate::isLoadedObject($order) && $order->delivery_number > 0) {
            return Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id, null, $order->id_shop) . sprintf('%06d', $order->delivery_number);
        } else {
            return "";
        }
    }
}
