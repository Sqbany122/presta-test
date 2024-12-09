{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}
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
                <div class="radio col-lg-12 radio-py-10">
                    <input type="radio" class="form-check-input check-service-add" name="optradio" id={$value.id_service|escape:'htmlall':'UTF-8'} value="{$value.id_service|escape:'htmlall':'UTF-8'}"
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
</div>
