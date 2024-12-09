<?php

define('LOG_WRITER', 1);

if ($_SERVER['HTTP_HOST'] != 'bc2ups-uat.fsoft.com.vn') {
    // PRODUCTION
    define('DEVELOPMENT', 0);
    define('UAT', 0);
} else { // TEST
    define('DEVELOPMENT', 0);
    define('UAT', 1);
}

$mapping = array(
    'Ups\Db'           => __DIR__ . '/Ups/Db.php',
    'Ups\Sdk'          => __DIR__ . '/Sdk.php',
    'Sdk\Client'       => __DIR__ . '/Client.php',
    'Sdk\Handle'       => __DIR__ . '/Handle.php',
    'Sdk\mycurl'       => __DIR__ . '/mycurl.php',
    'Sdk\Result'       => __DIR__ . '/Result.php',
    'Ups\Api\Rate'     => __DIR__ . '/Ups/Api/Rate.php',
    'Ups\Api\TinT'     => __DIR__ . '/Ups/Api/TinT.php',
    'Sdk\I18N\I18N'    => __DIR__ . '/I18N/I18N.php',
    'Ups\Api\Locator'  => __DIR__ . '/Ups/Api/Locator.php',
    'Ups\Api\Tracking' => __DIR__ . '/Ups/Api/Tracking.php',
    'Ups\Api\Shipping' => __DIR__ . '/Ups/Api/Shipping.php',
    'Ups\Api\Registration'      => __DIR__ . '/Ups/Api/Registration.php',
    'Ups\Api\PromoRequest'      => __DIR__ . '/Ups/Api/PromoRequest.php',
    'Ups\Api\CommonHandle'      => __DIR__ . '/Ups/Api/CommonHandle.php',
    'Ups\Api\PromoAgreement'    => __DIR__ . '/Ups/Api/PromoAgreement.php',
    'Ups\Api\LabelRecovery'     => __DIR__ . '/Ups/Api/LabelRecovery.php',
    'Sdk\I18N\MessageSource'    => __DIR__ . '/I18N/MessageSource.php',
    'Ups\Api\VoidShipment'      => __DIR__ . '/Ups/Api/VoidShipment.php',
    'Helper\ConvertToAscii'     => __DIR__ . '/Helper/ConvertToAscii.php',
    'Sdk\I18N\PhpMessageSource' => __DIR__ . '/I18N/PhpMessageSource.php',
    'Ups\Api\OpenAccountInterface' => __DIR__ . '/Ups/Api/OpenAccountInterface.php',
    'Ups\Api\ServiceOptionsTrait'       => __DIR__ . '/Ups/Api/ServiceOptionsTrait.php',
    'Ups\Api\InternationalTrait'        => __DIR__ . '/Ups/Api/InternationalTrait.php',
    'PluginManager\CommonHandle'        => __DIR__ . '/PluginManager/CommonHandle.php',
    'PluginManager\ToolApi\License'     => __DIR__ . '/PluginManager/ToolApi/License.php',
    'PluginManager\ToolApi\MyUpsID'     => __DIR__ . '/PluginManager/ToolApi/MyUpsID.php',
    'PluginManager\ToolApi\Handshake'   => __DIR__ . '/PluginManager/ToolApi/Handshake.php',
    'PluginManager\ToolApi\ToolHandle'  => __DIR__ . '/PluginManager/ToolApi/ToolHandle.php',
    'PluginManager\ToolApi\OpenAccount' => __DIR__ . '/PluginManager/ToolApi/OpenAccount.php',
    'PluginManager\ToolApi\Registration' => __DIR__ . '/PluginManager/ToolApi/Registration.php',
    'PluginManager\CollectionApi\Shipment' => __DIR__ . '/PluginManager/CollectionApi/Shipment.php',
    'PluginManager\ToolApi\BingMapsService' => __DIR__ . '/PluginManager/ToolApi/BingMapsService.php',
    'PluginManager\CollectionApi\MerchantInfo' => __DIR__ . '/PluginManager/CollectionApi/MerchantInfo.php',
    'PluginManager\CollectionApi\MerchantStatus' => __DIR__ . '/PluginManager/CollectionApi/MerchantStatus.php',
    'PluginManager\CollectionApi\ShipmentStatus'   => __DIR__ . '/PluginManager/CollectionApi/ShipmentStatus.php',
    'PluginManager\ToolApi\RegisteredPluginToken'  => __DIR__ . '/PluginManager/ToolApi/RegisteredPluginToken.php',
    'PluginManager\CollectionApi\CollectionHandle' => __DIR__ . '/PluginManager/CollectionApi/CollectionHandle.php',
    'PluginManager\CollectionApi\ShippingService' => __DIR__ . '/PluginManager/CollectionApi/ShippingService.php',
    'PluginManager\CollectionApi\DeliveryRatesInfo' => __DIR__ . '/PluginManager/CollectionApi/DeliveryRatesInfo.php',
    'PluginManager\CollectionApi\DefaultPackageInfo' => __DIR__ . '/PluginManager/CollectionApi/DefaultPackageInfo.php',
    'PluginManager\CollectionApi\UpgradePluginVersion' => __DIR__ . '/PluginManager/CollectionApi/UpgradePluginVersion.php',
    'PluginManager\CollectionApi\CreateLogger' => __DIR__ . '/PluginManager/CollectionApi/CreateLogger.php',
    'Bean\Validator' => __DIR__ . '/beans/Validator.php',
    'Bean\ShippingService' => __DIR__ . '/beans/ShippingService.php',
    'Bean\Accessorial' => __DIR__ . '/beans/Accessorial.php',
    'Bean\UPSCountry' => __DIR__ . '/beans/Country.php',
);

spl_autoload_register(function ($class) use ($mapping) {
    if (isset($mapping[$class])) {
        require_once $mapping[$class];
    }
}, true);

defined('UPS_SHIPMENT') or define ('UPS_SHIPMENT', _DB_PREFIX_ . 'ups_shipment');
defined('UPS_OPENORDER') or define( 'UPS_OPENORDER', _DB_PREFIX_ . 'ups_openorder');
defined('KEY_COL') or define('KEY_COL', 'key_name');
defined('VAL_COL') or define('VAL_COL', 'ups_value');
defined('STATUS_COL') or define('STATUS_COL', 'status');
defined('PS_COD_MODULE') or define('PS_COD_MODULE', 'ps_cashondelivery');
defined('TERM_COND_KEY') or define('TERM_COND_KEY', 'Term');
defined('COUNTRY_KEY') or define('COUNTRY_KEY', 'Country');
defined('LANGUAGE_KEY') or define('LANGUAGE_KEY', 'Language');
defined('LICENSE_KEY') or define('LICENSE_KEY', 'LicenseKey');
defined('ACCOUNT_NUM_KEY') or define('ACCOUNT_NUM_KEY', 'AccountNumber');
defined('MERCHANT_INFO') or define('MERCHANT_INFO', 'MerchantInfo');

defined('PATH_ASSETS_FOLDER') or define('PATH_ASSETS_FOLDER', _PS_MODULE_DIR_ . 'upsmodule/assets/');

defined('UPS_URI_CIE') or define('UPS_URI_CIE', 'https://wwwcie.ups.com/rest/');
defined('UPS_URI_UAT') or define('UPS_URI_UAT', 'https://onlinetools.ups.com/rest/');
defined('UPS_URI_PRO') or define('UPS_URI_PRO', 'https://onlinetools.ups.com/rest/');

defined('TOOL_API_URI_DEV') or define('TOOL_API_URI_DEV', 'https://fa-ecptools-dev.azurewebsites.net/api/');
defined('TOOL_API_URI_UAT') or define('TOOL_API_URI_UAT', 'https://fa-ecptools-uat.azurewebsites.net/api/');
defined('TOOL_API_URI_PRO') or define('TOOL_API_URI_PRO', 'https://fa-ecptools-prd.azurewebsites.net/api/');

defined('COLLECTION_API_URI_DEV') or define('COLLECTION_API_URI_DEV', 'https://fa-ecpanalytics-dev.azurewebsites.net/api/');
defined('COLLECTION_API_URI_UAT') or define('COLLECTION_API_URI_UAT', 'https://fa-ecpanalytics-uat.azurewebsites.net/api/');
defined('COLLECTION_API_URI_PRO') or define('COLLECTION_API_URI_PRO', 'https://fa-ecpanalytics-prd.azurewebsites.net/api/');

if (DEVELOPMENT) {
    define('UPS_URI', UPS_URI_UAT);
    define('TOOL_API_URI', TOOL_API_URI_DEV);
    define('COLLECTION_API_URI', COLLECTION_API_URI_DEV);
} elseif (UAT) {
    define('UPS_URI', UPS_URI_UAT);
    define('TOOL_API_URI', TOOL_API_URI_UAT);
    define('COLLECTION_API_URI', COLLECTION_API_URI_UAT);
} else {
    define('UPS_URI', UPS_URI_PRO);
    define('TOOL_API_URI', TOOL_API_URI_PRO);
    define('COLLECTION_API_URI', COLLECTION_API_URI_PRO);
}
