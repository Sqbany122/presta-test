{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}
<div id="view_dir" style="display:none" value="{$view_dir|escape:'htmlall':'UTF-8'}">{$view_dir|escape:'htmlall':'UTF-8'}</div>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
<link rel="stylesheet" href="{$view_dir|escape:'htmlall':'UTF-8'}/css/upseshoper.css">
<link rel="stylesheet" href="{$view_dir|escape:'htmlall':'UTF-8'}/css/upseshoper16.css">

<script type="text/javascript">
    if (typeof jQuery === 'undefined') {
        document.write(unescape("%3Cscript src='js/jquery.js' type='text/javascript'%3E%3C/script%3E"));
    }
</script>
<script type='text/javascript' src='https://www.bing.com/api/maps/mapcontrol?callback=GetMap&key={$bingMapKey|escape:'htmlall':'UTF-8'}' async defer></script>
<script type="text/javascript">

$(window).load(function(){
    shippingFeeValue();
});

function shippingFeeValue()
{
     $.ajax({
        type: 'POST',
        url: '{$link->getPageLink('order&ajax=1&action=selectDeliveryOption')|escape:'htmlall':'UTF-8'}',
        success: function(resp, textStatus, jqXHR)
        {
            if (resp !== "") {
                $("#js-checkout-summary").replaceWith(resp.preview);
            }
        }
    })
}

</script>

<script type="text/javascript" src="{$view_dir|escape:'htmlall':'UTF-8'}/js/loadmap.js"></script>
<script language="JavaScript" type="text/javascript">
    var idAddress = "{$idAddress|escape:'htmlall':'UTF-8'}";
    var carrierName = "{$carrierName|escape:'htmlall':'UTF-8'}";
    var idCarrier = "{$id_carrier|escape:'htmlall':'UTF-8'}";
    var txtEShippingMethod = "{$arrtext.txtE_ShippingMethod|escape:'htmlall':'UTF-8'}";
    var addressRequired = "{$arrtext.txtE_AddressRequired|escape:'htmlall':'UTF-8'}";
    var cheapestTime = "{$cheapestTime|escape:'htmlall':'UTF-8'}";
    var cheapestPrice = "{$cheapestPrice|escape:'htmlall':'UTF-8'}";
    var hiddenUPSShipping = "{$hiddenUPSShipping|escape:'htmlall':'UTF-8'}";
    var myAddress = "{$myAddress|escape:'htmlall':'UTF-8'}";
    var countryCode = "{$customerIso|escape:'htmlall':'UTF-8'}";
    var shippingServiceType = "{$shippingServiceType|escape:'htmlall':'UTF-8'}";
    var deliveryBy = "{$deliveryBy|escape:'htmlall':'UTF-8'}";
    var urlChangeShippingService = '{$link->getPageLink('eshoper&fc=module&module=upsmodule&ajax=1&action=ChangeShippingService')|escape:'javascript'}';
    var urlSelectAccessPoint = '{$link->getPageLink('eshoper&fc=module&module=upsmodule&ajax=1&action=SelectAccessPoint')|escape:'javascript'}';
    var urlSearchAccessPoint = '{$link->getPageLink('eshoper&fc=module&module=upsmodule&ajax=1&action=SearchAccessPoint')|escape:'javascript'}';
</script>
{* <!-- begin collapse --> *}
<div id="accordion">
    <input type="hidden" id="selectedAPAddress" name="selectedAPAddress" value="">
    <input type="hidden" id="selectedShippingService" name="selectedShippingService" value="{$chooseShippingService|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="selectedShippingFee" name="selectedShippingFee" value="{$cookieShippingFee|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="selectAddressinFor" name="selectAddressinFor" value="">

    <div class="panel col-lg-12 card show_01">
        <div class="card-body form-wrapper">
            {if ($list_ship_service_ap) && ($showHiddenAccesPointBlock)}
            <div class="form-group mt-2">
                <div class="row">
                    <img class="img-logo" src="{$view_dir|escape:'htmlall':'UTF-8'}/img/ups_logo_small.png">
                    <span class="form-check-label mb-0">
                        {$arrtext.txtAccessPointBrand|escape:'htmlall':'UTF-8'}
                    </span>
                </div>
                <div class="card-body">
                    {$arrtext.txtAccessPointBrand2|escape:'htmlall':'UTF-8'}
                </div>
            </div>
            <div class="rate04_show card-body pb-0">
                <div class="card-body">
                    <div class="form-group row mb-0">
                        {foreach $list_ship_service_ap as $key => $value}
                            <input type="hidden" id="AP_{$value.id_service|escape:'htmlall':'UTF-8'}" name="AP_{$value.id_service|escape:'htmlall':'UTF-8'}" value="{$value.shippingFeeValue|escape:'htmlall':'UTF-8'}">
                            <input type="hidden" id="AP_TinT_{$value.id_service|escape:'htmlall':'UTF-8'}" name="AP_TinT_{$value.id_service|escape:'htmlall':'UTF-8'}" value="{$value.totalTransitDays|escape:'htmlall':'UTF-8'}">
                            <input type="hidden" id="AP_Price_{$value.id_service|escape:'htmlall':'UTF-8'}" name="AP_Price_{$value.id_service|escape:'htmlall':'UTF-8'}" value="{$value.priceDisplay|escape:'htmlall':'UTF-8'}">
                            <div class="col-lg-12 ">
                                <div class="row label-px-2 form-label-py-5">
                                    <div class="col-lg-1 small-plane">
                                        <input type="radio" class="form-check-input check-service-ap" name="optradio" id={$value.id_service|escape:'htmlall':'UTF-8'} value="{$value.id_service|escape:'htmlall':'UTF-8'}" {if $value.id_service == $chooseShippingService}checked="checked"{/if}>
                                    </div>
                                    <div class="col-lg-5">
                                        <span class="form-check-label">{$value.name|escape:'htmlall':'UTF-8'}</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span class="form-check-label">{$value.detailFee|escape:'htmlall':'UTF-8'}</span>
                                    </div>
                                </div>
                                <div class="row label-px-2 form-label-py-10 right-cust">
                                    {$value.detailTime|escape:'htmlall':'UTF-8'}
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
                <div class="form-group row frm_e_shop">
                    <div class="col-lg-12 col-md-12">
                        <label class="mb-3 mt-3">{$arrtext.txtSearchAP|escape:'htmlall':'UTF-8'}</label></p>
                        <span><a style="color:red;">(*)</a>{$arrtext.txtE_ShippingMethod|escape:'htmlall':'UTF-8'}</span>
                        <div class="row mb-3">
                            <div class="col-lg-12 col-md-12">
                                <span class="near">{$arrtext.txtNear|escape:'htmlall':'UTF-8'}:</span><span class="myadress"><a href="javascript:void(0);" onclick="useMyAddress();">{$arrtext.txtUseAddress|escape:'htmlall':'UTF-8'}</a></span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="alert alert-danger" style="display: none"></div>
                            <div class="col-lg-3 col-md-12 searchAddressInput">
                                <input type="text" class="searchAddress" name="searchAddress" id="searchAddress" placeholder="{$arrtext.txtAddressLine|escape:'htmlall':'UTF-8'}">
                            </div>
                            <div class="col-lg-3 col-md-12 searchAddressButton">
                                <button type="button" name="searchAddress" onclick="Search();" class="btn btn-outline-dark my-0 ml-0 btn-block">{$arrtext.txtSearch|escape:'htmlall':'UTF-8'}</button>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-lg-12">
                            <input type="text" class="searchCountry" name="countryName" id="countryName" value="{$countryName|escape:'htmlall':'UTF-8'}" readonly>
                            </div>
                        </div>
                        <div id="myMap" class="iMap"></div>
                        <label id="resultSearch">{$arrtext.txtResults|escape:'htmlall':'UTF-8'}</label>
                        <div class="card" id="showSearch">
                            <div class="card-body pb00">
                                {block name='accesspoint'}
                                    {include file='modules/upsmodule/views/templates/front/_partials/content_accesspoint.tpl' Infor=[]}
                                {/block}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/if}

            {if ($list_ship_service_add)}
            <div class="form-group">
                <div class="row">
                    <img class="img-logo" src="{$view_dir|escape:'htmlall':'UTF-8'}/img/ups_logo_small.png">
                    <span class="form-check-label mb-0">
                        {$arrtext.txtE_DeliverAddress|escape:'htmlall':'UTF-8'}
                    </span>
                </div>
                <div class="card-body">
                    {$arrtext.txtE_DeliverAddressPkg|escape:'htmlall':'UTF-8'}
                </div>
            </div>
            <div class="rate05_show card-body pb-0 ">
                <div class="card-body">
                    <div class="form-group row mb-0">
                        {foreach $list_ship_service_add as $key => $value}
                            <input type="hidden" id="ADD_{$value.id_service|escape:'htmlall':'UTF-8'}" name="ADD_{$value.id_service|escape:'htmlall':'UTF-8'}" value="{$value.shippingFeeValue|escape:'htmlall':'UTF-8'}">
                            <input type="hidden" id="ADD_TinT_{$value.id_service|escape:'htmlall':'UTF-8'}" name="ADD_TinT_{$value.id_service|escape:'htmlall':'UTF-8'}" value="{$value.totalTransitDays|escape:'htmlall':'UTF-8'}">
                            <input type="hidden" id="ADD_Price_{$value.id_service|escape:'htmlall':'UTF-8'}" name="ADD_Price_{$value.id_service|escape:'htmlall':'UTF-8'}" value="{$value.priceDisplay|escape:'htmlall':'UTF-8'}">
                            <div class="col-lg-12">

                                <div class="row label-px-2 form-label-py-5">
                                    <div class="col-lg-1 small-plane">
                                        <input type="radio" class="form-check-input check-service-add" name="optradio" id={$value.id_service|escape:'htmlall':'UTF-8'} value="{$value.id_service|escape:'htmlall':'UTF-8'}" {if $value.id_service == $chooseShippingService}checked="checked"{/if}>
                                    </div>
                                    <div class="col-lg-5">
                                        <span class="form-check-label">{$value.name|escape:'htmlall':'UTF-8'}</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span class="form-check-label">{$value.detailFee|escape:'htmlall':'UTF-8'}</span>
                                    </div>
                                </div>
                                <div class="row label-px-2 form-label-py-10 right-cust">
                                    {$value.detailTime|escape:'htmlall':'UTF-8'}
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
            {/if}
        </div>
    </div>
</div>
{* <!-- end collapse --> *}
<script type="text/javascript" src="{$view_dir|escape:'htmlall':'UTF-8'}/js/upseshoper.16.js"></script>
