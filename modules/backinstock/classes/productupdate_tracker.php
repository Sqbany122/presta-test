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

class PriceAlertPluginTracker
{
    private $current_shop = 0;
    private $ad_id = 0;
    private $plugin_id = 0;
    private $ad_url = 'http://ocdemo.velsof.com/velsof_advertisement.php';
    private $plugin_monitor_url = 'http://ocdemo.velsof.com/store_monitor.php';

    public function __construct($shop, $ad_id, $plugin_id)
    {
        $this->current_shop = $shop;
        $this->ad_id = $ad_id;
        $this->plugin_id = $plugin_id;
    }

    /**
     * This function will get advertisement from velocity server
     */
    public function getVelsofAdd()
    {
        $data = array('adv_id' => $this-ad_id, 'plugin_id' => $this->plugin_id);
        $ch = curl_init($this->ad_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data) . '&type=front&url=' . $_SERVER['HTTP_HOST']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds
        $response = curl_exec($ch);
        curl_close($ch);
        if ($response) {
            $response_decode = (array) Tools::jsonDecode($response);
            if (!empty($response_decode)) {
                return $response_decode;
            }
        }
        return array('flag' => 0);
    }

    /**
     * To get current shop detail on which plugin is installed and send these details to velocity
     */
    public function sendShopDetails($shop_info, $plugin_status, $plugin_version)
    {
        if ($plugin_status) {
            $plugin_enabled = 'Yes';
        } else {
            $plugin_enabled = 'No';
        }

        $server_name = $_SERVER['SERVER_NAME'];
        $domain_name = $shop_info->domain;

        $admin_email = Configuration::get('PS_SHOP_EMAIL');

        $version_no = $plugin_version;

        $data_time = date('Y-m-d H:i:s');

        $data = array(
        'server_name' => $server_name,
        'domain_name' => $domain_name,
        'version_no' => $version_no,
        'plugin_enabled' => $plugin_enabled,
        'contact_email' => $admin_email,
        'data_time' => $data_time,
        'plugin_id' => $this->plugin_id);

        $ch = curl_init($this->plugin_monitor_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data) . '&type=front&url=' . $_SERVER['HTTP_HOST']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds
        curl_exec($ch);
        curl_close($ch);
    }
}
