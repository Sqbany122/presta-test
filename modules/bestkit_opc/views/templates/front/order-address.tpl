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

{assign var="back_order_page" value="order-opc.php"}

<h1 class="page-heading"><span class="heading-counter heading-counter-1">1</span>{l s='Addresses' mod='bestkit_opc'}</h1>

<div class="box box-opc">
    {if !$opc}
        {assign var='current_step' value='address'}
        {capture name=path}{l s='Addresses' mod='bestkit_opc'}{/capture}
        {assign var="back_order_page" value="order.php"}
        <h1 class="page-heading">{l s='Addresses' mod='bestkit_opc'}</h1>
        {include file="$tpl_dir./order-steps.tpl"}
        {include file="$tpl_dir./errors.tpl"}
        <form action="{$link->getPageLink($back_order_page, true)|escape:'html':'UTF-8'}" method="post">
    {else}
            {assign var="back_order_page" value="order-opc.php"}
            <div id="opc_account" class="opc-main-block">
                <div id="opc_account-overlay" class="opc-overlay" style="display: none;"></div>
    {/if}
                <div class="addresses clearfix">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="address_delivery select form-group selector1">
                                <label for="id_address_delivery">{if $cart->isVirtualCart()}{l s='Choose a billing address:' mod='bestkit_opc'}{else}{l s='Choose a delivery address:' mod='bestkit_opc'}{/if}</label>
                                <select name="id_address_delivery" id="id_address_delivery" class="address_select form-control">
                                    {foreach from=$addresses key=k item=address}
                                        <option value="{$address.id_address|intval}"{if $address.id_address == $cart->id_address_delivery} selected="selected"{/if}>
                                            {$address.alias|escape:'html':'UTF-8'}
                                        </option>
                                    {/foreach}
                                </select><span class="waitimage"></span>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div id="address_invoice_form" class="select form-group selector1"{if $cart->id_address_invoice == $cart->id_address_delivery} style="display: none;"{/if}>
                                {if $addresses|@count > 1}
                                    <label for="id_address_invoice" class="strong">{l s='Choose a billing address:' mod='bestkit_opc'}</label>
                                    <select name="id_address_invoice" id="id_address_invoice" class="address_select form-control">
                                        {section loop=$addresses step=-1 name=address}
                                            <option value="{$addresses[address].id_address|intval}"{if $addresses[address].id_address == $cart->id_address_invoice && $cart->id_address_delivery != $cart->id_address_invoice} selected="selected"{/if}>
                                                {$addresses[address].alias|escape:'html':'UTF-8'}
                                            </option>
                                        {/section}
                                    </select><span class="waitimage"></span>
                                {else}
                                    <a href="{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1&select_address=1{if $back}&mod={$back}{/if}")|escape:'html':'UTF-8'}" title="{l s='Add' mod='bestkit_opc'}" class="button button-small btn btn-default">
                            <span>
                                {l s='Add a new address' mod='bestkit_opc'}
                                <i class="icon-chevron-right right"></i>
                            </span>
                                    </a>
                                {/if}
                            </div>
                        </div>
                    </div> <!-- end row -->
                    <div>
                        <div class="col-xs-12 col-sm-12">

                            <p class="checkbox addressesAreEquals"{if $cart->isVirtualCart()} style="display:none;"{/if}>
                                <input type="checkbox" name="same" id="addressesAreEquals" value="1"{if $cart->id_address_invoice == $cart->id_address_delivery || $addresses|@count == 1} checked="checked"{/if} />
                                <label for="addressesAreEquals">{l s='Use the delivery address as the billing address.' mod='bestkit_opc'}</label>
                            </p>
                        </div>
                    </div> <!-- end row -->
                    <div class="row">
                        <div class="col-xs-12 col-sm-6"{if $cart->isVirtualCart()} style="display:none;"{/if}>
                            <ul class="address item box" id="address_delivery">
                            </ul>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <ul class="address alternate_item{if $cart->isVirtualCart()} full_width{/if} box" id="address_invoice">
                            </ul>
                        </div>
                    </div> <!-- end row -->
                    <p class="address_add submit">
                        <a href="{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{if $back}&mod={$back}{/if}")|escape:'html':'UTF-8'}" title="{l s='Add' mod='bestkit_opc'}" class="button button-small btn btn-default">
                            <span>{l s='Add a new address' mod='bestkit_opc'}<i class="icon-chevron-right right"></i></span>
                        </a>
                    </p>
                    {if !$opc}
                        <div id="ordermsg" class="form-group">
                            <label>{l s='If you would like to add a comment about your order, please write it in the field below.' mod='bestkit_opc'}</label>
                            <textarea class="form-control" cols="60" rows="6" name="message">{if isset($oldMessage)}{$oldMessage}{/if}</textarea>
                        </div>
                    {/if}
                </div> <!-- end addresses -->
                {if !$opc}
                <p class="cart_navigation clearfix">
                    <input type="hidden" class="hidden" name="step" value="2" />
                    <input type="hidden" name="back" value="{$back|escape:false}" />
                    <a href="{$link->getPageLink($back_order_page, true, NULL, "step=0{if $back}&back={$back|escape:false}{/if}")|escape:'html':'UTF-8'}" title="{l s='Previous' mod='bestkit_opc'}" class="button-exclusive btn btn-default">
                        <i class="icon-chevron-left"></i>
                        {l s='Continue Shopping' mod='bestkit_opc'}
                    </a>
                    <button type="submit" name="processAddress" class="button btn btn-default button-medium">
                        <span>{l s='Proceed to checkout' mod='bestkit_opc'}<i class="icon-chevron-right right"></i></span>
                    </button>
                </p>
        </form>
        {else}
    </div> <!--  end opc_account -->
    {/if}
    {strip}
        {if !$opc}
            {addJsDef orderProcess='order'}
            {addJsDef currencySign=$currencySign|html_entity_decode:2:"UTF-8"}
            {addJsDef currencyRate=$currencyRate|floatval}
            {addJsDef currencyFormat=$currencyFormat|intval}
            {addJsDef currencyBlank=$currencyBlank|intval}
            {addJsDefL name=txtProduct|escape:false}{l s='product' js=1 mod='bestkit_opc'}{/addJsDefL}
            {addJsDefL name=txtProducts|escape:false}{l s='products' js=1 mod='bestkit_opc'}{/addJsDefL}
            {addJsDefL name=CloseTxt|escape:false}{l s='Submit' js=1 mod='bestkit_opc'}{/addJsDefL}
        {/if}
        {capture}{if $back}&mod={$back|urlencode}{/if}{/capture}
        {capture name=addressUrl}{$link->getPageLink('address', true, NULL, 'back='|cat:$back_order_page|cat:'?step=1'|cat:$smarty.capture.default)|escape:'quotes':'UTF-8'}{/capture}
        {addJsDef addressUrl=$smarty.capture.addressUrl}
        {capture}{'&multi-shipping=1'|urlencode}{/capture}
        {addJsDef addressMultishippingUrl=$smarty.capture.addressUrl|cat:$smarty.capture.default}
        {capture name=addressUrlAdd}{$smarty.capture.addressUrl|cat:'&id_address='}{/capture}
        {addJsDef addressUrlAdd=$smarty.capture.addressUrlAdd}
        {addJsDef formatedAddressFieldsValuesList=$formatedAddressFieldsValuesList}
        {addJsDef opc=$opc|boolval}
        {capture}<h3 class="page-subheading">{l s='Your billing address' js=1 mod='bestkit_opc'}</h3>{/capture}
        {addJsDefL name=titleInvoice}{$smarty.capture.default|@addcslashes:'\''}{/addJsDefL}
        {capture}<h3 class="page-subheading">{l s='Your delivery address' js=1 mod='bestkit_opc'}</h3>{/capture}
        {addJsDefL name=titleDelivery}{$smarty.capture.default|@addcslashes:'\''}{/addJsDefL}
        {capture}<a class="button button-small btn btn-default" href="{$smarty.capture.addressUrlAdd}" title="{l s='Update' js=1 mod='bestkit_opc'}"><span>{l s='Update' js=1 mod='bestkit_opc'}<i class="icon-chevron-right right"></i></span></a>{/capture}
        {addJsDefL name=liUpdate}{$smarty.capture.default|@addcslashes:'\''}{/addJsDefL}
    {/strip}
</div>