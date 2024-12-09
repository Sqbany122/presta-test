{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
<link rel="stylesheet" href="{$view_dir|escape:'htmlall':'UTF-8'}/css/upseshoper.css">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
    if (typeof jQuery === 'undefined') {
        document.write(unescape("%3Cscript src='js/jquery.js' type='text/javascript'%3E%3C/script%3E"));
    }
</script>
<script type='text/javascript' src="https://www.bing.com/api/maps/mapcontrol?callback=GetMap&key={$bingMapKey|escape:'htmlall':'UTF-8'}" async defer></script>
<script type="text/javascript">
var idCarrier = "{$id_carrier|escape:'htmlall':'UTF-8'}";
$(window).load(function(){
    shippingFeeValue();
});

function shippingFeeValue()
{
     $.ajax({
        type: 'POST',
        url: '{url entity='order' params=['ajax' => 1, 'action' => 'selectDeliveryOption']}',
        success: function(resp, textStatus, jqXHR)
        {
            $("#js-checkout-summary").replaceWith(resp.preview);
            $('#cart-subtotal-shipping').css('display', 'block');
        }
    })
}

</script>
<div id="hidden_view_dir"><input type="hidden" id="view_dir" name="view_dir" value="{$view_dir|escape:'htmlall':'UTF-8'}"></div>

<script type="text/javascript" src="{$view_dir|escape:'htmlall':'UTF-8'}/js/loadmap.js"></script>
<script language="JavaScript" type="text/javascript">
    var txtEShippingMethod = "{$arrtext.txtE_ShippingMethod|escape:'htmlall':'UTF-8'}";
    var addressRequired = "{$arrtext.txtE_AddressRequired|escape:'htmlall':'UTF-8'}";
    var cheapestTime = "{$cheapestTime|escape:'htmlall':'UTF-8'}";
    var cheapestPrice = "{$cheapestPrice|escape:'htmlall':'UTF-8'}";
    var hiddenUPSShipping = "{$hiddenUPSShipping|escape:'htmlall':'UTF-8'}";
    var myAddress = "{$myAddress|escape:'htmlall':'UTF-8'}";
    var countryCode = "{$customerIso|escape:'htmlall':'UTF-8'}";
    var shippingServiceType = "{$shippingServiceType|escape:'htmlall':'UTF-8'}";
    var urlChangeShippingService = '{url entity='eshoper' params=['fc' => 'module', 'module' => upsmodule, 'ajax' => 1, 'action' => 'ChangeShippingService']}';
    var urlSelectAccessPoint = '{url entity='eshoper' params=['fc' => 'module', 'module' => upsmodule, 'ajax' => 1, 'action' => 'SelectAccessPoint']}';
    var urlSearchAccessPoint = '{url entity='eshoper' params=['fc' => 'module', 'module' => upsmodule, 'ajax' => 1, 'action' => 'SearchAccessPoint']}';
</script>
{* <!-- begin collapse --> *}
<div id="accordion">
    <input type="hidden" id="selectedAPAddress" name="selectedAPAddress" value="">
    <input type="hidden" id="selectedShippingService" name="selectedShippingService" value="{$chooseShippingService|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="selectedShippingFee" name="selectedShippingFee" value="{$cookieShippingFee|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="selectAddressinFor" name="selectAddressinFor" value="">

    <div class="panel col-lg-12 card show_01">
        <div class="card-body form-wrapper">
        {if $pluginCountry == 'US'}
            {if ($list_ship_service_add)}
                {include file='modules/upsmodule/views/templates/front/ad_temp.tpl' view_dir=$view_dir arrtext=$arrtext list_ship_service_add=$list_ship_service_add chooseShippingService=$chooseShippingService}
            {/if}

            {if ($list_ship_service_ap) && ($showHiddenAccesPointBlock)}
                {include file='modules/upsmodule/views/templates/front/ap_temp.tpl' view_dir=$view_dir arrtext=$arrtext list_ship_service_ap=$list_ship_service_ap countryName=$countryName chooseShippingService=$chooseShippingService}
            {/if}
        {else}
            {if ($list_ship_service_ap) && ($showHiddenAccesPointBlock)}
                {include file='modules/upsmodule/views/templates/front/ap_temp.tpl' view_dir=$view_dir arrtext=$arrtext list_ship_service_ap=$list_ship_service_ap countryName=$countryName chooseShippingService=$chooseShippingService}
            {/if}

            {if ($list_ship_service_add)}
                {include file='modules/upsmodule/views/templates/front/ad_temp.tpl' view_dir=$view_dir arrtext=$arrtext list_ship_service_add=$list_ship_service_add chooseShippingService=$chooseShippingService}
            {/if}
        {/if}
        </div>
    </div>
</div>
{* <!-- end collapse --> *}
<script type="text/javascript" src="{$view_dir|escape:'htmlall':'UTF-8'}/js/upseshoper.js"></script>
