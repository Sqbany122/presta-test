{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}
<div class="form-group mt-2">
    <div class="row">
        <img class="img-logo" src="{$view_dir|escape:'htmlall':'UTF-8'}/img/ups_logo_small.png">
        <span class="form-check-label mb-0">
        {if $isUSA}
            {$arrtext.txtAccessPointBrandUS|escape:'htmlall':'UTF-8'}
        {else}
            {$arrtext.txtAccessPointBrand|escape:'htmlall':'UTF-8'}
        {/if}
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
                <div class="radio col-lg-12 radio-py-10">
                    <input type="radio" class="form-check-input check-service-ap" name="optradio" id={$value.id_service|escape:'htmlall':'UTF-8'} value="{$value.id_service|escape:'htmlall':'UTF-8'}"
                    {if $value.id_service == $chooseShippingService}checked="checked"{/if}>
                    <div class="row label-px-1">
                        <div class="col-lg-9">
                            <span class="form-check-label">{$value.name|escape:'htmlall':'UTF-8'}</span>
                        </div>
                        <div class="col-lg-3 col-right">
                            <span class="form-check-label">{$value.detailFee|escape:'htmlall':'UTF-8'}</span>
                        </div>
                    </div>
                    <div class="row label-px-2">
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
