<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/Constants.php';

class CommonFunction
{
    public static function isCodModuleEnable()
    {
        $installedCod = Module::isInstalled(Constants::PS_COD_MODULE) ? 1 : 0;
        $enableCod    = Module::isEnabled(Constants::PS_COD_MODULE) ? 1 : 0;
        $enableCodBtn = ($installedCod && $enableCod) ? 1 : 0;

        return $enableCodBtn;
    }

    public static function createZipArchive($files = array(), $destination = '')
    {
        $validFiles = array();

        if (is_array($files)) {
            foreach ($files as $file) {
                if (file_exists($file['srcPath'])) {
                    $validFiles[] = array(
                        'srcPath' => $file['srcPath'],
                        'desPath' => $file['desPath'],
                    );
                }
            }
        }

        if (count($validFiles)) {
            $zip = new ZipArchive();
            if ($zip->open($destination, ZipArchive::CREATE) == true) {
                foreach ($validFiles as $file) {
                    $zip->addFile($file['srcPath'], $file['desPath']);
                }
                $zip->close();

                return file_exists($destination);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function removeFiles($files)
    {
        if (!empty($files)) {
            foreach ($files as $file) {
                unlink($file['path']);
            }
        }
    }

    public static function checkScreenConfig()
    {
        $statusScreens = unserialize(Configuration::get('UPS_CONFIG_SCREEN_STATUS'));

        if (!Configuration::get('UPS_CONFIG_DONE')) {
            foreach ($statusScreens as $screen) {
                if (!$screen['status']) {
                    return $screen['class_name'];
                }
            }

            Configuration::updateValue('UPS_CONFIG_DONE', 1);
        }

        return 'DONE';
    }

    public static function setDoneConfigScreen($className)
    {
        $statusScreens       = unserialize(Configuration::get('UPS_CONFIG_SCREEN_STATUS'));
        $statusScreensUpdate = array();
        $statusConfigDone    = true;

        foreach ($statusScreens as $screen) {
            if ($screen['class_name'] == $className) {
                $screen['status'] = 1;
            }

            if ($screen['status'] == 0) {
                $statusConfigDone = false;
            }

            $statusScreensUpdate[] = $screen;
        }

        Configuration::updateValue('UPS_CONFIG_SCREEN_STATUS', serialize($statusScreensUpdate));
        if ($statusConfigDone) {
            Configuration::updateValue('UPS_CONFIG_DONE', 1);
        }
    }

    public static function getCurrencyMerchant()
    {
        switch (Configuration::get('UPS_COUNTRY_SELECTED')) {
            case 'PL':
                return 'PLN';

            case 'GB':
                return 'GBP';

            case 'US':
                return 'USD';

            default:
                return 'EUR';
        }
    }

    public static function getIdsCarrierByReference($idReference)
    {
        $sql = new DbQuery();

        $sql->select('id_carrier');
        $sql->from('carrier');
        $sql->where('id_reference = ' . (int) $idReference);

        $idCarriers = Db::getInstance()->executeS($sql);

        if (empty($idCarriers)) {
            return array();
        } else {
            return array_column($idCarriers, 'id_carrier');
        }
    }

    public static function formatDisplayTime($inputString)
    {
        $strTime = '';

        if (Tools::strlen($inputString) > 4) {
            $strTime = '0000';
        } elseif (Tools::strlen($inputString) < 4) {
            $strTime = str_pad($inputString, 4, '0', STR_PAD_LEFT);
        } else {
            $strTime = $inputString;
        }

        $date = strtotime($strTime);
        return date(Constants::FORMAT_TIME_ESHOPER, $date);
    }

    public static function sqlUpdateOrderStatus($status, $orderIds)
    {
        $orderIds = implode(",", array_map('intval', $orderIds));
        return Db::getInstance()->update(
            'orders',
            array('current_state' => (int) $status),
            "`id_order` IN (" . $orderIds . ")"
        );
    }

    public static function sqlUpdateTrackingNumber($trackingNumber, $orderIds)
    {
        $orderIds = implode(",", array_map('intval', $orderIds));
        return Db::getInstance()->update(
            'order_carrier',
            array('tracking_number' => pSQL($trackingNumber)),
            "`id_order` IN (" . $orderIds . ")"
        );
    }
}
