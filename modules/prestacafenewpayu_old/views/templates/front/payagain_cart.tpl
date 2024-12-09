{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE (for the part taken from order-detail.tpl)
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* 2009-2017 Blue Train Lukasz Kowalczyk / PrestaCafe
*
* NOTICE OF LICENSE (for the rest of the file)
*
* Lukasz Kowalczyk is the owner of the copyright of this module.
* All rights of any kind, which are not expressly granted in this
* License, are entirely and exclusively reserved to and by Lukasz
* Kowalczyk.
*
* You may not rent, lease, transfer, modify or create derivative
* works based on this module.
*
* @author    Lukasz Kowalczyk <lkowalczyk@prestacafe.pl>
* @copyright Lukasz Kowalczyk
* @license   LICENSE.txt
*}
{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
    {assign var='current_step' value='payment'}
    {*{include file="$tpl_dir./order-steps.tpl"}*}
    {include file="$tpl_dir./errors.tpl"}

    {if $smarty.request.show_payu_error}
        <h3 class="page-heading">{l s='Your previous payment for this order failed. Please, try paying again.' mod='prestacafenewpayu'}</h3>
    {/if}

    <div class="paiement_block">

        {if $display_payment_methods}
            {if isset($smarty.request.pbl) && !$smarty.request.pbl}
                {* If the 'pbl' parameter was submitted but we are still here, it means that it was either empty or invalid. *}
                <div class="warning">
                    {l s='Please choose a payment method by clicking on one of the payment images below.' mod='prestacafenewpayu'}
                </div>
            {/if}
        {/if}

        {if $payu_error}
            <div class="warning">
                {l s='There was an error communicating with PayU servers. Please try again in a few moments.' mod='prestacafenewpayu'}
            </div>
        {/if}

        <h4> {l s='Order Reference %s - placed on' sprintf=$order->getUniqReference() mod='prestacafenewpayu'} {dateFormat date=$order->date_add full=0} </h4>

        <p></p>

        <div id="order-detail-content" class="table_block">
            <table class="std">
                <thead>
                <tr>
                    {if $return_allowed}<th class="first_item"><input type="checkbox" /></th>{/if}
                    <th class="{if $return_allowed}item{else}first_item{/if}">{l s='Reference' mod='prestacafenewpayu'}</th>
                    <th class="item">{l s='Product' mod='prestacafenewpayu'}</th>
                    <th class="item">{l s='Quantity' mod='prestacafenewpayu'}</th>
                    {if $order->hasProductReturned()}
                        <th class="item">{l s='Returned' mod='prestacafenewpayu'}</th>
                    {/if}
                    <th class="item">{l s='Unit price' mod='prestacafenewpayu'}</th>
                    <th class="last_item">{l s='Total price' mod='prestacafenewpayu'}</th>
                </tr>
                </thead>
                <tfoot>
                {if $priceDisplay && $use_tax}
                    <tr class="item">
                        <td colspan="{if $return_allowed || $order->hasProductReturned()}{if $order->hasProductReturned() && $return_allowed}7{else}6{/if}{else}5{/if}">
                            {l s='Total products (tax excl.):' mod='prestacafenewpayu'} <span class="price">{displayWtPriceWithCurrency price=$order->getTotalProductsWithoutTaxes() currency=$currency}</span>
                        </td>
                    </tr>
                {/if}
                <tr class="item">
                    <td colspan="{if $return_allowed || $order->hasProductReturned()}{if $order->hasProductReturned() && $return_allowed}7{else}6{/if}{else}5{/if}">
                        {l s='Total products' mod='prestacafenewpayu'} {if $use_tax}{l s='(tax incl.)' mod='prestacafenewpayu'}{/if}: <span class="price">{displayWtPriceWithCurrency price=$order->getTotalProductsWithTaxes() currency=$currency}</span>
                    </td>
                </tr>
                {if $order->total_discounts > 0}
                    <tr class="item">
                        <td colspan="{if $return_allowed || $order->hasProductReturned()}{if $order->hasProductReturned() && $return_allowed}7{else}6{/if}{else}5{/if}">
                            {l s='Total vouchers:' mod='prestacafenewpayu'} <span class="price-discount">{displayWtPriceWithCurrency price=$order->total_discounts currency=$currency convert=1}</span>
                        </td>
                    </tr>
                {/if}
                {if $order->total_wrapping > 0}
                    <tr class="item">
                        <td colspan="{if $return_allowed || $order->hasProductReturned()}{if $order->hasProductReturned() && $return_allowed}7{else}6{/if}{else}5{/if}">
                            {l s='Total gift-wrapping:' mod='prestacafenewpayu'} <span class="price-wrapping">{displayWtPriceWithCurrency price=$order->total_wrapping currency=$currency}</span>
                        </td>
                    </tr>
                {/if}
                <tr class="item">
                    <td colspan="{if $return_allowed || $order->hasProductReturned()}{if $order->hasProductReturned() && $return_allowed}7{else}6{/if}{else}5{/if}">
                        {l s='Total shipping' mod='prestacafenewpayu'} {if $use_tax}{l s='(tax incl.)' mod='prestacafenewpayu'}{/if}: <span class="price-shipping">{displayWtPriceWithCurrency price=$order->total_shipping currency=$currency}</span>
                    </td>
                </tr>
                <tr class="totalprice item">
                    <td colspan="{if $return_allowed || $order->hasProductReturned()}{if $order->hasProductReturned() && $return_allowed}7{else}6{/if}{else}5{/if}">
                        {l s='Total:' mod='prestacafenewpayu'} <span class="price">{displayWtPriceWithCurrency price=$order->total_paid currency=$currency}</span>
                    </td>
                </tr>
                </tfoot>
                <tbody>
                {foreach from=$products item=product name=products}
                    {if !isset($product.deleted)}
                        {assign var='productId' value=$product.product_id}
                        {assign var='productAttributeId' value=$product.product_attribute_id}
                        {if isset($product.customizedDatas)}
                            {assign var='productQuantity' value=$product.product_quantity-$product.customizationQuantityTotal}
                        {else}
                            {assign var='productQuantity' value=$product.product_quantity}
                        {/if}
                        <!-- Customized products -->
                        {if isset($product.customizedDatas)}
                            <tr class="item">
                                {if $return_allowed}<td class="order_cb"></td>{/if}
                                <td><label for="cb_{$product.id_order_detail|intval}">{if $product.product_reference}{$product.product_reference|escape:'htmlall':'UTF-8'}{else}--{/if}</label></td>
                                <td class="bold">
                                    <label for="cb_{$product.id_order_detail|intval}">{$product.product_name|escape:'htmlall':'UTF-8'}</label>
                                </td>
                                <td>{$product.customizationQuantityTotal|intval}</td>
                                {if $order->hasProductReturned()}
                                    <td>
                                        {$product['qty_returned']|escape:'htmlall':'UTF-8'}
                                    </td>
                                {/if}
                                <td>
                                    <label for="cb_{$product.id_order_detail|intval}">
                                        {if $group_use_tax}
                                            {convertPriceWithCurrency price=$product.unit_price_tax_incl currency=$currency}
                                        {else}
                                            {convertPriceWithCurrency price=$product.unit_price_tax_excl currency=$currency}
                                        {/if}
                                    </label>
                                </td>
                                <td>
                                    <label for="cb_{$product.id_order_detail|intval}">
                                        {if isset($customizedDatas.$productId.$productAttributeId)}
                                            {if $group_use_tax}
                                                {convertPriceWithCurrency price=$product.total_customization_wt currency=$currency}
                                            {else}
                                                {convertPriceWithCurrency price=$product.total_customization currency=$currency}
                                            {/if}
                                        {else}
                                            {if $group_use_tax}
                                                {convertPriceWithCurrency price=$product.total_price_tax_incl currency=$currency}
                                            {else}
                                                {convertPriceWithCurrency price=$product.total_price_tax_excl currency=$currency}
                                            {/if}
                                        {/if}
                                    </label>
                                </td>
                            </tr>
                            {foreach $product.customizedDatas  as $customizationPerAddress}
                                {foreach $customizationPerAddress as $customizationId => $customization}
                                    <tr class="alternate_item">
                                        {if $return_allowed}<td class="order_cb"><input type="checkbox" id="cb_{$product.id_order_detail|intval}" name="customization_ids[{$product.id_order_detail|intval}][]" value="{$customizationId|intval}" /></td>{/if}
                                        <td colspan="2">
                                            {foreach from=$customization.datas key='type' item='datas'}
                                                {if $type == $CUSTOMIZE_FILE}
                                                    <ul class="customizationUploaded">
                                                        {foreach from=$datas item='data'}
                                                            <li><img src="{$pic_dir|escape:'htmlall':'UTF-8'}{$data.value|escape:'htmlall':'UTF-8'}_small" alt="" class="customizationUploaded" /></li>
                                                        {/foreach}
                                                    </ul>
                                                {elseif $type == $CUSTOMIZE_TEXTFIELD}
                                                    <ul class="typedText">{counter start=0 print=false}
                                                        {foreach from=$datas item='data'}
                                                            {assign var='customizationFieldName' value="Text #"|cat:$data.id_customization_field}
                                                            <li>{$data.name|default:$customizationFieldName|escape:'htmlall':'UTF-8'} : {$data.value|escape:'htmlall':'UTF-8'}</li>
                                                        {/foreach}
                                                    </ul>
                                                {/if}
                                            {/foreach}
                                        </td>
                                        <td>
                                            {$customization.quantity|intval}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                {/foreach}
                            {/foreach}
                        {/if}
                        <!-- Classic products -->
                        {if $product.product_quantity > $product.customizationQuantityTotal}
                            <tr class="item">
                                {if $return_allowed}<td class="order_cb"><input type="checkbox" id="cb_{$product.id_order_detail|intval}" name="ids_order_detail[{$product.id_order_detail|intval}]" value="{$product.id_order_detail|intval}" /></td>{/if}
                                <td><label for="cb_{$product.id_order_detail|intval}">{if $product.product_reference}{$product.product_reference|escape:'htmlall':'UTF-8'}{else}--{/if}</label></td>
                                <td class="bold">{$product.product_name|escape:'htmlall':'UTF-8'}</td>
                                <td>{$productQuantity|intval}</td>
                                {if $order->hasProductReturned()}
                                    <td>
                                        {$product['qty_returned']|escape:'htmlall':'UTF-8'}
                                    </td>
                                {/if}
                                <td>
                                    <label for="cb_{$product.id_order_detail|intval}">
                                        {if $group_use_tax}
                                            {convertPriceWithCurrency price=$product.unit_price_tax_incl currency=$currency}
                                        {else}
                                            {convertPriceWithCurrency price=$product.unit_price_tax_excl currency=$currency}
                                        {/if}
                                    </label>
                                </td>
                                <td>
                                    <label for="cb_{$product.id_order_detail|intval}">
                                        {if $group_use_tax}
                                            {convertPriceWithCurrency price=$product.total_price_tax_incl currency=$currency}
                                        {else}
                                            {convertPriceWithCurrency price=$product.total_price_tax_excl currency=$currency}
                                        {/if}
                                    </label>
                                </td>
                            </tr>
                        {/if}
                    {/if}
                {/foreach}
                {foreach from=$discounts item=discount}
                    <tr class="item">
                        <td>{$discount.name|escape:'htmlall':'UTF-8'}</td>
                        <td>{l s='Voucher:' mod='prestacafenewpayu'} {$discount.name|escape:'htmlall':'UTF-8'}</td>
                        <td><span class="order_qte_span editable">1</span></td>
                        <td>&nbsp;</td>
                        <td>{if $discount.value != 0.00}-{/if}{convertPriceWithCurrency price=$discount.value currency=$currency}</td>
                        {if $return_allowed}
                            <td>&nbsp;</td>
                        {/if}
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>

        {if $display_payment_methods}
            <div class="paiement_block">
                <h3>{l s='Please choose a payment method by clicking on one of the payment images below.' mod='prestacafenewpayu'}</h3>

                <br/>

                <form method="POST" action="{$link->getModuleLink('prestacafenewpayu', 'payagain', ['cart_displayed' => '1', 'secure_key_hash' => $smarty.request.secure_key_hash, 'secure_token' => $smarty.request.secure_token, 'id_cart' => $smarty.request.id_cart], true)|escape:'html':'UTF-8'}">
                    <input type="hidden" name="pbl" />

                    {* All payments but the credit card which is displayed below separately *}
                    <div class="clearfix">
                        {foreach $payByLinks as $value => $pbl}
                            {if $pbl->status == 'ENABLED' && $value != 'c'}
                                <div class="payu-pbl-cell payu-pbl-cell-ps15" onclick="PrestaCafePayu.onClickPaymentMethod(this, '{$value|escape:'html':'UTF-8'}');" data-pbl="{$value|escape:'htmlall':'UTF-8'}">
                                    <img src="{$pbl->brandImageUrl|escape:'htmlall':'UTF-8'}">
                                </div>
                            {/if}
                        {/foreach}
                    </div>

                    {* Payment by card is displayed separately *}
                    {if isset($payByLinks['c']) && $payByLinks['c']->status == 'ENABLED'}
                        <hr />
                        <div class="clearfix">
                            <div class="payu-pbl-cell payu-pbl-cell-ps15" onclick="PrestaCafePayu.onClickPaymentMethod(this, 'c');" data-pbl="c">
                                <img src="{$payByLinks['c']->brandImageUrl|escape:'htmlall':'UTF-8'}">
                            </div>
                        </div>
                    {/if}

                    <br />

                    <div style="text-align: center;">
                        <input type="submit" name="submitMessage" id="submitMessage" class="exclusive_large" value="{l s='Pay in PayU' mod='prestacafenewpayu'}"/>
                    </div>
                </form>
            </div>

        {else}
            {capture name=replacement_html}{l s='Redirecting to PayU' mod='prestacafenewpayu'}&nbsp;<img src='{$img_dir|escape:'htmlall':'UTF-8'}loader.gif'>{/capture}
            {assign var=account_payment_link value=$link->getModuleLink('prestacafenewpayu', 'payagain', ['cart_displayed' => '1', 'secure_key_hash' => $smarty.request.secure_key_hash, 'secure_token' => $smarty.request.secure_token, 'id_cart' => $smarty.request.id_cart], true)}
            {assign var=card_payment_link value=$link->getModuleLink('prestacafenewpayu', 'payagain', ['pbl' => 'c', 'cart_displayed' => '1', 'secure_key_hash' => $smarty.request.secure_key_hash, 'secure_token' => $smarty.request.secure_token, 'id_cart' => $smarty.request.id_cart], true)}

            {if $show_basic_payment}
                <p class="payment_module">
                    <a href="{$account_payment_link|escape:'html':'UTF-8'}" title="{l s='Pay in PayU' mod='prestacafenewpayu'}" class="prestacafenewpayu"
                       onclick="PrestaCafePayu.paymentRedirect(event || window.event, this, '{$account_payment_link|escape:'htmlall':'UTF-8'}', '{$smarty.capture.replacement_html|escape:'javascript':'UTF-8'}')">
                        <img src="{$module_img_dir|escape:'htmlall':'UTF-8'}payment_img.png" width="86" height="49" />
                        {l s='Pay in PayU' mod='prestacafenewpayu'} <span>{l s='with quick transfer or by card' mod='prestacafenewpayu'}</span>
                    </a>
                </p>
            {/if}

            {* Direct card payment *}
            {if $show_direct_card}
                <p class="payment_module">
                    <a href="{$card_payment_link|escape:'html':'UTF-8'}" title="{l s='Pay in PayU' mod='prestacafenewpayu'}" class="prestacafenewpayu"
                       onclick="PrestaCafePayu.paymentRedirect(event || window.event, this, '{$card_payment_link|escape:'htmlall':'UTF-8'}', '{$smarty.capture.replacement_html|escape:'javascript':'UTF-8'}')">
                        <img src="{$module_img_dir|escape:'htmlall':'UTF-8'}payment_card_img.png" width="86" height="49" />
                        {l s='Pay by card' mod='prestacafenewpayu'} <span>{l s='in PayU' mod='prestacafenewpayu'}</span>
                    </a>
                </p>
            {/if}
        {/if}

    </div>
    <br /><br />

{elseif version_compare($smarty.const._PS_VERSION_, '1.7', '<')}
    {capture name=path}{l s='PayU payment' mod='prestacafenewpayu'}{/capture}
    {assign var='current_step' value='payment'}
    {include file="$tpl_dir./order-steps.tpl"}
    {include file="$tpl_dir./errors.tpl"}

    {if $smarty.request.show_payu_error}
        <h1 class="page-heading">{l s='Your previous payment for this order failed. Please, try paying again.' mod='prestacafenewpayu'}</h1>
    {/if}

    {if $display_payment_methods}
        {if isset($smarty.request.pbl) && !$smarty.request.pbl}
            {* If the 'pbl' parameter was submitted but we are still here, it means that it was either empty or invalid. *}
            <div class="alert alert-warning">
                <p>{l s='Please choose a payment method by clicking on one of the payment images below.' mod='prestacafenewpayu'}</p>
            </div>
        {/if}
    {/if}

    {if $payu_error}
        <div class="alert alert-warning">
            <p>{l s='There was an error communicating with PayU servers. Please try again in a few moments.' mod='prestacafenewpayu'}</p>
        </div>
    {/if}

    <p class="dark">
        <strong>{l s='Order Reference %s - placed on' sprintf=$order->getUniqReference() mod='prestacafenewpayu'} {dateFormat date=$order->date_add full=0}</strong>
    </p>

    {*
        Lifted from order-detail.tpl.
        Removed +/- buttons that allowed to change the quantity.
        Removed the download links (at this point they are not necessary).
     *}
    <div id="order-detail-content" class="table_block table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr>
            {if $return_allowed}<th class="first_item"><input type="checkbox" /></th>{/if}
            <th class="{if $return_allowed}item{else}first_item{/if}">{l s='Reference' mod='prestacafenewpayu'}</th>
            <th class="item">{l s='Product' mod='prestacafenewpayu'}</th>
            <th class="item">{l s='Quantity' mod='prestacafenewpayu'}</th>
            {if $order->hasProductReturned()}
                <th class="item">{l s='Returned' mod='prestacafenewpayu'}</th>
            {/if}
            <th class="item">{l s='Unit price' mod='prestacafenewpayu'}</th>
            <th class="last_item">{l s='Total price' mod='prestacafenewpayu'}</th>
        </tr>
        </thead>
        <tfoot>
        {if $priceDisplay && $use_tax}
            <tr class="item">
                <td colspan="{if $return_allowed}2{else}1{/if}">
                    <strong>{l s='Items (tax excl.)' mod='prestacafenewpayu'}</strong>
                </td>
                <td colspan="{if $order->hasProductReturned()}5{else}4{/if}">
                    <span class="price">{displayWtPriceWithCurrency price=$order->getTotalProductsWithoutTaxes() currency=$currency}</span>
                </td>
            </tr>
        {/if}
        <tr class="item">
            <td colspan="{if $return_allowed}2{else}1{/if}">
                <strong>{l s='Items' mod='prestacafenewpayu'} {if $use_tax}{l s='(tax incl.)' mod='prestacafenewpayu'}{/if} </strong>
            </td>
            <td colspan="{if $order->hasProductReturned()}5{else}4{/if}">
                <span class="price">{displayWtPriceWithCurrency price=$order->getTotalProductsWithTaxes() currency=$currency}</span>
            </td>
        </tr>
        {if $order->total_discounts > 0}
            <tr class="item">
                <td colspan="{if $return_allowed}2{else}1{/if}">
                    <strong>{l s='Total vouchers' mod='prestacafenewpayu'}</strong>
                </td>
                <td colspan="{if $order->hasProductReturned()}5{else}4{/if}">
                    <span class="price-discount">{displayWtPriceWithCurrency price=$order->total_discounts currency=$currency convert=1}</span>
                </td>
            </tr>
        {/if}
        {if $order->total_wrapping > 0}
            <tr class="item">
                <td colspan="{if $return_allowed}2{else}1{/if}">
                    <strong>{l s='Total gift wrapping cost' mod='prestacafenewpayu'}</strong>
                </td>
                <td colspan="{if $order->hasProductReturned()}5{else}4{/if}">
                    <span class="price-wrapping">{displayWtPriceWithCurrency price=$order->total_wrapping currency=$currency}</span>
                </td>
            </tr>
        {/if}
        <tr class="item">
            <td colspan="{if $return_allowed}2{else}1{/if}">
                <strong>{l s='Shipping & handling' mod='prestacafenewpayu'} {if $use_tax}{l s='(tax incl.)' mod='prestacafenewpayu'}{/if} </strong>
            </td>
            <td colspan="{if $order->hasProductReturned()}5{else}4{/if}">
                <span class="price-shipping">{displayWtPriceWithCurrency price=$order->total_shipping currency=$currency}</span>
            </td>
        </tr>
        <tr class="totalprice item">
            <td colspan="{if $return_allowed}2{else}1{/if}">
                <strong>{l s='Total' mod='prestacafenewpayu'}</strong>
            </td>
            <td colspan="{if $order->hasProductReturned()}5{else}4{/if}">
                <span class="price">{displayWtPriceWithCurrency price=$order->total_paid currency=$currency}</span>
            </td>
        </tr>
        </tfoot>
        <tbody>
        {foreach from=$products item=product name=products}
            {if !isset($product.deleted)}
                {assign var='productId' value=$product.product_id}
                {assign var='productAttributeId' value=$product.product_attribute_id}
                {if isset($product.customizedDatas)}
                    {assign var='productQuantity' value=$product.product_quantity-$product.customizationQuantityTotal}
                {else}
                    {assign var='productQuantity' value=$product.product_quantity}
                {/if}
                <!-- Customized products -->
                {if isset($product.customizedDatas)}
                    <tr class="item">
                        {if $return_allowed}<td class="order_cb"></td>{/if}
                        <td><label for="cb_{$product.id_order_detail|intval}">{if $product.product_reference}{$product.product_reference|escape:'html':'UTF-8'}{else}--{/if}</label></td>
                        <td class="bold">
                            <label for="cb_{$product.id_order_detail|intval}">{$product.product_name|escape:'html':'UTF-8'}</label>
                        </td>
                        <td>{$product.customizationQuantityTotal|intval}</td>
                        {if $order->hasProductReturned()}
                            <td>
                                {$product['qty_returned']|escape:'htmlall':'UTF-8'}
                            </td>
                        {/if}
                        <td>
                            <label class="price" for="cb_{$product.id_order_detail|intval}">
                                {if $group_use_tax}
                                    {convertPriceWithCurrency price=$product.unit_price_tax_incl currency=$currency}
                                {else}
                                    {convertPriceWithCurrency price=$product.unit_price_tax_excl currency=$currency}
                                {/if}
                            </label>
                        </td>
                        <td>
                            <label class="price" for="cb_{$product.id_order_detail|intval}">
                                {if isset($customizedDatas.$productId.$productAttributeId)}
                                    {if $group_use_tax}
                                        {convertPriceWithCurrency price=$product.total_customization_wt currency=$currency}
                                    {else}
                                        {convertPriceWithCurrency price=$product.total_customization currency=$currency}
                                    {/if}
                                {else}
                                    {if $group_use_tax}
                                        {convertPriceWithCurrency price=$product.total_price_tax_incl currency=$currency}
                                    {else}
                                        {convertPriceWithCurrency price=$product.total_price_tax_excl currency=$currency}
                                    {/if}
                                {/if}
                            </label>
                        </td>
                    </tr>
                    {foreach $product.customizedDatas  as $customizationPerAddress}
                        {foreach $customizationPerAddress as $customizationId => $customization}
                            <tr class="alternate_item">
                                {if $return_allowed}<td class="order_cb"><input type="checkbox" id="cb_{$product.id_order_detail|intval}" name="customization_ids[{$product.id_order_detail|intval}][]" value="{$customizationId|intval}" /></td>{/if}
                                <td colspan="2">
                                    {foreach from=$customization.datas key='type' item='datas'}
                                        {if $type == $CUSTOMIZE_FILE}
                                            <ul class="customizationUploaded">
                                                {foreach from=$datas item='data'}
                                                    <li><img src="{$pic_dir|escape:'htmlall':'UTF-8'}{$data.value|escape:'htmlall':'UTF-8'}_small" alt="" class="customizationUploaded" /></li>
                                                {/foreach}
                                            </ul>
                                        {elseif $type == $CUSTOMIZE_TEXTFIELD}
                                            <ul class="typedText">{counter start=0 print=false}
                                                {foreach from=$datas item='data'}
                                                    {assign var='customizationFieldName' value="Text #"|cat:$data.id_customization_field}
                                                    <li>{$data.name|default:$customizationFieldName|escape:'htmlall':'UTF-8'} : {$data.value|escape:'htmlall':'UTF-8'}</li>
                                                {/foreach}
                                            </ul>
                                        {/if}
                                    {/foreach}
                                </td>
                                <td>{$customization.quantity|intval}</td>
                                <td colspan="2"></td>
                            </tr>
                        {/foreach}
                    {/foreach}
                {/if}
                <!-- Classic products -->
                {if $product.product_quantity > $product.customizationQuantityTotal}
                    <tr class="item">
                        {if $return_allowed}<td class="order_cb"><input type="checkbox" id="cb_{$product.id_order_detail|intval}" name="ids_order_detail[{$product.id_order_detail|intval}]" value="{$product.id_order_detail|intval}" /></td>{/if}
                        <td><label for="cb_{$product.id_order_detail|intval}">{if $product.product_reference}{$product.product_reference|escape:'html':'UTF-8'}{else}--{/if}</label></td>
                        <td class="bold">{$product.product_name|escape:'html':'UTF-8'}</td>
                        <td class="return_quantity">{$productQuantity|intval}</td>
                        {if $order->hasProductReturned()}
                            <td>
                                {$product['qty_returned']|escape:'htmlall':'UTF-8'}
                            </td>
                        {/if}
                        <td class="price">
                            <label for="cb_{$product.id_order_detail|intval}">
                                {if $group_use_tax}
                                    {convertPriceWithCurrency price=$product.unit_price_tax_incl currency=$currency}
                                {else}
                                    {convertPriceWithCurrency price=$product.unit_price_tax_excl currency=$currency}
                                {/if}
                            </label>
                        </td>
                        <td class="price">
                            <label for="cb_{$product.id_order_detail|intval}">
                                {if $group_use_tax}
                                    {convertPriceWithCurrency price=$product.total_price_tax_incl currency=$currency}
                                {else}
                                    {convertPriceWithCurrency price=$product.total_price_tax_excl currency=$currency}
                                {/if}
                            </label>
                        </td>
                    </tr>
                {/if}
            {/if}
        {/foreach}
        {foreach from=$discounts item=discount}
            <tr class="item">
                <td>{$discount.name|escape:'html':'UTF-8'}</td>
                <td>{l s='Voucher' mod='prestacafenewpayu'} {$discount.name|escape:'html':'UTF-8'}</td>
                <td><span class="order_qte_span editable">1</span></td>
                <td>&nbsp;</td>
                <td>{if $discount.value != 0.00}-{/if}{convertPriceWithCurrency price=$discount.value currency=$currency}</td>
                {if $return_allowed}
                    <td>&nbsp;</td>
                {/if}
            </tr>
        {/foreach}
        </tbody>
    </table>
    </div>

    {if $display_payment_methods}
        <h3>{l s='Please choose a payment method by clicking on one of the payment images below.' mod='prestacafenewpayu'}</h3>

        <br/>

        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <form method="POST" action="{$link->getModuleLink('prestacafenewpayu', 'payagain', ['cart_displayed' => '1', 'secure_key_hash' => $smarty.request.secure_key_hash, 'secure_token' => $smarty.request.secure_token, 'id_cart' => $smarty.request.id_cart], true)|escape:'html':'UTF-8'}">
                    <input type="hidden" name="pbl" />

                    {* All payments but the credit card which is displayed below separately *}
                    <div class="row">
                        {foreach $payByLinks as $pbl}
                            {if $pbl->status == 'ENABLED' && $pbl->value != 'c'}
                                <div class="col-xs-4 col-md-2 text-center payu-pbl-cell payu-pbl-cell-ps16" onclick="PrestaCafePayu.onClickPaymentMethod(this, '{$pbl->value|escape:'html':'UTF-8'}');" data-pbl="{$pbl->value|escape:'htmlall':'UTF-8'}">
                                    <img src="{$pbl->brandImageUrl|escape:'htmlall':'UTF-8'}">
                                </div>
                            {/if}
                        {/foreach}
                    </div>

                    {* Payment by card is displayed separately *}
                    {if isset($payByLinks['c']) && $payByLinks['c']->status == 'ENABLED'}
                        <hr />
                        <div class="row">
                            <div class="col-xs-4 col-md-2 text-center payu-pbl-cell payu-pbl-cell-ps16" onclick="PrestaCafePayu.onClickPaymentMethod(this, 'c');" data-pbl="c">
                                <img src="{$payByLinks['c']->brandImageUrl|escape:'htmlall':'UTF-8'}">
                            </div>
                        </div>
                    {/if}

                    <div class="text-center submit" style="padding-top:20px">
                        <button type="submit" name="submitMessage" id="submitMessage" class="button btn btn-default button-medium"><span>{l s='Pay in PayU' mod='prestacafenewpayu'}<i class="icon-chevron-right right"></i></span></button>
                    </div>
                </form>
            </div>
            <div class="col-md-1"></div>
        </div>
    {else}
        {capture name=replacement_html}{l s='Redirecting to PayU' mod='prestacafenewpayu'}&nbsp;<img src='{$img_dir|escape:'htmlall':'UTF-8'}loader.gif'>{/capture}
        {assign var=account_payment_link value=$link->getModuleLink('prestacafenewpayu', 'payagain', ['cart_displayed' => '1', 'secure_key_hash' => $smarty.request.secure_key_hash, 'secure_token' => $smarty.request.secure_token, 'id_cart' => $smarty.request.id_cart], true)}
        {assign var=card_payment_link value=$link->getModuleLink('prestacafenewpayu', 'payagain', ['pbl' => 'c', 'cart_displayed' => '1', 'secure_key_hash' => $smarty.request.secure_key_hash, 'secure_token' => $smarty.request.secure_token, 'id_cart' => $smarty.request.id_cart], true)}

        <form action="{$link->getModuleLink('prestacafenewpayu', 'payagain', ['cart_displayed' => '1'], true)|escape:'htmlall':'UTF-8'}" method="POST">
            {* PayU payment *}
            {if $show_basic_payment}
                <div class="row">
                    <div class="col-xs-12">
                        <p class="payment_module" id="prestacafenewpayu_payment_button">
                            <a href="{$account_payment_link|escape:'html':'UTF-8'}" title="{l s='Pay in PayU' mod='prestacafenewpayu'}" class="prestacafenewpayu"
                               onclick="PrestaCafePayu.paymentRedirect(event || window.event, this, '{$account_payment_link|escape:'htmlall':'UTF-8'}', '{$smarty.capture.replacement_html|escape:'javascript':'UTF-8'}')">
                                {l s='Pay in PayU' mod='prestacafenewpayu'} <span>{l s='with quick transfer or by card' mod='prestacafenewpayu'}</span>
                            </a>
                        </p>
                    </div>
                </div>
            {/if}

            {* Direct card payment *}
            {if $show_direct_card}
                <div class="row">
                    <div class="col-xs-12">
                        <p class="payment_module" id="prestacafenewpayu_direct_card_payment_button">
                            <a href="{$card_payment_link|escape:'html':'UTF-8'}" title="{l s='Pay in PayU' mod='prestacafenewpayu'}" class="prestacafenewpayu"
                               onclick="PrestaCafePayu.paymentRedirect(event || window.event, this, '{$card_payment_link|escape:'htmlall':'UTF-8'}', '{$smarty.capture.replacement_html|escape:'javascript':'UTF-8'}')">
                                {l s='Pay by card' mod='prestacafenewpayu'} <span>{l s='in PayU' mod='prestacafenewpayu'}</span>
                            </a>
                        </p>
                    </div>
                </div>
            {/if}
        </form>
    {/if}
{else}{* PS 1.7 *}
    {* PS 1.7 is separate file because the template is parsed completely before rendering
       and some tags present in older versions of Prestashop (namely displayWtPriceWithCurrency)
       are not available in PS 1.7 which causes an error. *}
{/if}
{* The script is common for all Prestashop versions *}
<script type="text/javascript">
    $(document).ready(function() {
        PrestaCafePayu.preparePaymentMethodCells();
    });
</script>