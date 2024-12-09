{*
 * 2007-2014 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *         DISCLAIMER   *
 * *************************************** */
/* Do not edit or add to this file if you wish to upgrade Prestashop to newer
* versions in the future.
* ****************************************************
*
*  @author     BEST-KIT.COM (contact@best-kit.com)
*  @copyright  http://best-kit.com
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="carrier_area">
    {if !$opc}
        {literal}
            <script type="text/javascript">
                var orderProcess = 'order';
                var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
                var currencyRate = '{$currencyRate|floatval}';
                var currencyFormat = '{$currencyFormat|intval}';
                var currencyBlank = '{$currencyBlank|intval}';
                var txtProduct = "{l s='product' mod='bestkit_opc'}";
                var txtProducts = "{l s='products' mod='bestkit_opc'}";
                var orderUrl = '{$link->getPageLink("order", true)|escape:false}';
                var msg = "{l s='You must agree to the terms of service before continuing.' js=1 mod='bestkit_opc'}";

                function acceptCGV() {
                    if ($('#cgv').length && !$('input#cgv:checked').length) {
                        alert(msg);
                        return false;
                    }
                    else {
                        return true;
                    }
                }
            </script>
        {/literal}
    {else}
        <script type="text/javascript">
            var txtFree = "{l s='Free!' mod='bestkit_opc'}";
        </script>
    {/if}

    {if isset($virtual_cart) && !$virtual_cart && $giftAllowed && $cart->gift == 1}
        {literal}
            <script type="text/javascript">
                $('document').ready( function(){
                    if ($('input#gift').is(':checked')){
                        $('#gift_msg').slideDown();
                    }
                });
            </script>
        {/literal}
    {/if}

    {if !$opc}
    {capture name=path}{l s='Shipping' mod='bestkit_opc'}{/capture}
    {include file="$tpl_dir./breadcrumb.tpl"}
    {/if}

    {if !$opc}
        <h1 class="page-heading">{l s='Shipping' mod='bestkit_opc'}</h1>
    {else}
        <h1 class="page-heading"><span class="heading-counter heading-counter-2">2</span>{l s='Delivery methods' mod='bestkit_opc'}</h1>
    {/if}

    {if !$opc}
        {assign var='current_step' value='shipping'}
        {include file="$tpl_dir./order-steps.tpl"}

        {include file="$tpl_dir./errors.tpl"}
        <form id="form" action="{$link->getPageLink('order', true, NULL, "multi-shipping={$multi_shipping}")|escape:false}" method="post" onsubmit="return acceptCGV();">
    {else}
        <div id="opc_delivery_methods" class="opc-main-block">
            <div id="opc_delivery_methods-overlay" class="overlay-opc" style="display: none;"></div>
    {/if}

    <div class="box box-opc">

    {if isset($virtual_cart) && $virtual_cart}
        <input id="input_virtual_carrier" class="hidden" type="hidden" name="id_carrier" value="0" />
    {else}
        {if isset($isVirtualCart) && $isVirtualCart}
            <div class="item">
                <p class="alert alert-warning">{l s='No carrier needed for this order' mod='bestkit_opc'}</p>
            </div>
        {/if}

        <div class="item">
            {if isset($delivery_option_list)}
                {foreach $delivery_option_list as $id_address => $option_list}
                    {*<h2 class="page-subheading">
                        {if isset($address_collection[$id_address])}
                            {l s='Choose a shipping option for this address:' mod='bestkit_opc'} {$address_collection[$id_address]->alias}
                        {else}
                            {l s='Choose a shipping option' mod='bestkit_opc'}
                        {/if}
                    </h2>*}
                    <div class="shipping-delivery-opc">
                    {foreach $option_list as $key => $option}
                        <label for="delivery_option_{$id_address|escape:false}_{$option@index|escape:false}" class="shipping-delivery-item-opc">
                            <table class="resume table-opc">
                                <colgroup>
                                    <col width="1" />
                                    <col width="1" />
                                    <col />
                                    <col width="1" />
                                </colgroup>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="radio-inline">
                                                <input class="delivery_option_radio" type="radio" name="delivery_option[{$id_address}]" onchange="{if $opc}updateCarrierSelectionAndGift();{else}updateExtraCarrier('{$key}', {$id_address});{/if}" id="delivery_option_{$id_address}_{$option@index}" value="{$key}" {if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}checked="checked"{/if} />
                                            </div>
                                        </td>
                                        <td>
                                            {foreach $option.carrier_list as $carrier}
                                                {if $carrier.logo}
                                                    <img src="{$carrier.logo|escape:false}" alt="{$carrier.instance->name|escape:false}" class="shipping-logo-opc" />
                                                {elseif !$option.unique_carrier}
                                                    {$carrier.instance->name|escape:false}
                                                    {if !$carrier@last} - {/if}
                                                {/if}
                                            {/foreach}
                                        </td>
                                        <td>
                                            {if $option.unique_carrier}
                                                {foreach $option.carrier_list as $carrier}
                                                    <h4 class="shipping-title-opc">{$carrier.instance->name|escape:false}</h4>
                                                {/foreach}
                                                {if isset($carrier.instance->delay[$cookie->id_lang])}
                                                    <div class="shipping-desc-opc">{$carrier.instance->delay[$cookie->id_lang]|escape:false}</div>
                                                {/if}
                                            {/if}
                                            {if count($option_list) > 1}
                                                {if $option.is_best_grade}
                                                    {if $option.is_best_price}
                                                        <div class="delivery_option_best delivery_option_icon">{l s='The best price and speed' mod='bestkit_opc'}</div>
                                                    {else}
                                                        <div class="delivery_option_fast delivery_option_icon">{l s='The fastest' mod='bestkit_opc'}</div>
                                                    {/if}
                                                {else}
                                                    {if $option.is_best_price}
                                                        <div class="delivery_option_best_price delivery_option_icon">{l s='The best price' mod='bestkit_opc'}</div>
                                                    {/if}
                                                {/if}
                                            {/if}
                                        </td>
                                        <td>
                                            {if $option.total_price_with_tax && !$free_shipping}
                                                <div class="shipping-price-opc">
                                                    {if $use_taxes == 1}
                                                        {convertPrice price=$option.total_price_with_tax}
                                                        <br />
                                                        {l s='(tax incl.)' mod='bestkit_opc'}
                                                    {else}
                                                        {convertPrice price=$option.total_price_without_tax}
                                                        <br />
                                                        {l s='(tax excl.)' mod='bestkit_opc'}
                                                    {/if}
                                                </div>
                                            {else}
                                                <span class="price-opc free-price-opc">{l s='Free!' mod='bestkit_opc'}</span>
                                            {/if}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table-opc {if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}selected{/if} {if $option.unique_carrier}hidden{/if}">
                                {foreach $option.carrier_list as $carrier}
                                    <tr>
                                        {if !$option.unique_carrier}
                                            <td>
                                                <input type="hidden" value="{$carrier.instance->id|escape:false}" name="id_carrier" />
                                                {if $carrier.logo}
                                                    <img src="{$carrier.logo|escape:false}" alt="{$carrier.instance->name|escape:false}" class="shipping-logo-opc" />
                                                {/if}
                                            </td>
                                            <td>
                                                {$carrier.instance->name|escape:false}
                                            </td>
                                        {/if}
                                        <td {if $option.unique_carrier}colspan="2"{/if}>
                                            <input type="hidden" value="{$carrier.instance->id|escape:false}" name="id_carrier" />
                                            {if isset($carrier.instance->delay[$cookie->id_lang])}
                                                <small>
                                                    {$carrier.instance->delay[$cookie->id_lang]|escape:false}
                                                    <br />
                                                    {if count($carrier.product_list) <= 1}
                                                        ({l s='product concerned:' mod='bestkit_opc'}
                                                    {else}
                                                        ({l s='products concerned:' mod='bestkit_opc'}
                                                    {/if}
                                                    {* This foreach is on one line, to avoid tabulation in the title attribute of the acronym *}
                                                    {foreach $carrier.product_list as $product}{if $product@index == 4}<acronym title="{/if}{if $product@index >= 4}{$product.name}{if !$product@last}, {else}">...</acronym>){/if}{else}{$product.name}{if !$product@last}, {else}){/if}{/if}{/foreach}
                                                </small>
                                            {/if}
                                        </td>
                                    </tr>
                                {/foreach}
                            </table>
                        </label>
                    {/foreach}
                    </div>
                    <div class="hook_extracarrier" id="HOOK_EXTRACARRIER_{$id_address}">{if isset($HOOK_EXTRACARRIER_ADDR) &&  isset($HOOK_EXTRACARRIER_ADDR.$id_address)}{$HOOK_EXTRACARRIER_ADDR.$id_address}{/if}</div>
                    {foreachelse}
                    <p class="alert alert-warning" id="noCarrierWarning">
                        {foreach $cart->getDeliveryAddressesWithoutCarriers(true) as $address}
                            {if empty($address->alias)}
                                {l s='No carriers available.' mod='bestkit_opc'}
                            {else}
                                {l s='No carriers available for the address "%s".' sprintf=$address->alias mod='bestkit_opc'}
                            {/if}
                            {if !$address@last}
                                <br />
                            {/if}
                        {/foreach}
                    </p>
                {/foreach}
            {/if}
        </div>

        <div class="item">
            {*<h2 class="page-subheading">{l s='Choose your delivery method' mod='bestkit_opc'}</h2>*}
            <div id="HOOK_BEFORECARRIER">{if isset($carriers) && isset($HOOK_BEFORECARRIER)}{$HOOK_BEFORECARRIER}{/if}</div>
            {if $recyclablePackAllowed}
                <div class="checkbox">
                    <label for="recyclable">
                        <div class="checker hover {if $recyclable == 1}active{/if} focus" id="uniform-recyclable">
                                <span>
                                    <input type="checkbox" name="recyclable" id="recyclable" value="1" {if $recyclable == 1}checked="checked"{/if} />
                                </span>
                        </div>
                        {l s='I agree to receive my order in recycled packaging' mod='bestkit_opc'}
                    </label>
                </div>
            {/if}
        </div>
                <div style="display: none;" id="extra_carrier"></div>
                {if $giftAllowed}
                    <div class="item">
                        <h2 class="page-subheading">{l s='Gift' mod='bestkit_opc'}</h2>
                        <div class="checkbox">
                            <label for="gift">
                                <div class="checker {if $cart->gift == 1}active{/if} focus" id="uniform-gift">
                                    <span>
                                        <input type="checkbox" name="gift" id="gift" value="1" {if $cart->gift == 1}checked="checked"{/if} />
                                    </span>
                                </div>
                                {l s='I would like my order to be gift-wrapped' mod='bestkit_opc'}
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                {if $gift_wrapping_price > 0}
                                    ({l s='Additional cost of' mod='bestkit_opc'}
                                    <span class="price-opc" id="gift-price">
                                        {if $priceDisplay == 1}
                                            {convertPrice price=$total_wrapping_tax_exc_cost}
                                        {else}
                                            {convertPrice price=$total_wrapping_cost}
                                        {/if}
                                    </span>
                                    {if $use_taxes}
                                        {if $priceDisplay == 1}
                                            {l s='(tax excl.)' mod='bestkit_opc'}
                                        {else}
                                            {l s='(tax incl.)' mod='bestkit_opc'}
                                        {/if}
                                    {/if})
                                {/if}
                            </label>
                        </div>
                        <div id="gift_msg" class="textarea" style="display:none;">
                            <label for="gift_message">{l s='If you wish, you can add a note to the gift:' mod='bestkit_opc'}</label>
                            <textarea rows="5" cols="35" id="gift_message" name="gift_message" class="form-control">{$cart->gift_message|escape:'htmlall':'UTF-8'}</textarea>
                        </div>
                    </div>
                {/if}
            {/if}

        {*
            {if $conditions AND $cms_id}
                <h3 class="condition_title">{l s='Terms of service' mod='bestkit_opc'}</h3>
                <div class="checkbox">
                    <label for="cgv">
                        <input type="checkbox" name="cgv" id="cgv" value="1" {if $checkedTOS}checked="checked"{/if} />
                        {l s='I agree to the Terms of Service and will adhere to them unconditionally' mod='bestkit_opc'}
                        <a href="{$link_conditions|escape:false}" class="iframe">{l s='(Read Terms of Service)' mod='bestkit_opc'}</a>
                    </label>
                </div>
                <script type="text/javascript">$('a.iframe').fancybox();</script>
            {/if}
        *}

        {include file=Module::getInstanceByName('bestkit_opc')->getTemplatePath('order-message.tpl')}
    </div>

    {if !$opc}
            <p class="submit">
                <input type="hidden" name="step" value="3" />
                <input type="hidden" name="back" value="{$back|escape:false}" />
                {if !$is_guest}
                    {if $back}
                        <a href="{$link->getPageLink('order', true, NULL, "step=1&back={$back}&multi-shipping={$multi_shipping}")|escape:false}" title="{l s='Previous' mod='bestkit_opc'}" class="button">&laquo; {l s='Previous' mod='bestkit_opc'}</a>
                    {else}
                        <a href="{$link->getPageLink('order', true, NULL, "step=1&multi-shipping={$multi_shipping}")|escape:false}" title="{l s='Previous' mod='bestkit_opc'}" class="button">&laquo; {l s='Previous' mod='bestkit_opc'}</a>
                    {/if}
                {else}
                        <a href="{$link->getPageLink('order', true, NULL, "multi-shipping={$multi_shipping|escape:false}")}" title="{l s='Previous' mod='bestkit_opc'}" class="button">&laquo; {l s='Previous' mod='bestkit_opc'}</a>
                {/if}
                {if isset($virtual_cart) && $virtual_cart || (isset($delivery_option_list) && !empty($delivery_option_list))}
                    <input type="submit" name="processCarrier" value="{l s='Next' mod='bestkit_opc'} &raquo;" class="exclusive" />
                {/if}
            </p>
        </form>
    {else}
        </div>
    {/if}
</div>
