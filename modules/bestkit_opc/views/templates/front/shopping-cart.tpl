{*
 * 2007-2013 PrestaShop
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
<div id="shopping_cart_ajax">
<script type="text/javascript">
    var freeShippingTranslationOPC = '{l s='Free!' mod='bestkit_opc' js=1}';
</script>

{capture name=path}{l s='Your shopping cart' mod='bestkit_opc'}{/capture}
{*include file="$tpl_dir./breadcrumb.tpl"*}

<h1 class="page-heading"><span class="heading-counter heading-counter-3">3</span>{l s='Review your order' mod='bestkit_opc'}</h1>

{assign var='current_step' value='summary'}
{include file="$tpl_dir./order-steps.tpl"}
{include file="$tpl_dir./errors.tpl"}

{if isset($empty)}
    <p class="alert alert-warning">{l s='Your shopping cart is empty.' mod='bestkit_opc'}</p>
{elseif $PS_CATALOG_MODE}
    <p class="alert alert-warning">{l s='This store has not accepted your new order.' mod='bestkit_opc'}</p>
{else}
    <script type="text/javascript">
        var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
        var currencyRate = '{$currencyRate|floatval}';
        var currencyFormat = '{$currencyFormat|intval}';
        var currencyBlank = '{$currencyBlank|intval}';
        var txtProduct = "{l s='product' mod='bestkit_opc'}";
        var txtProducts = "{l s='products' mod='bestkit_opc'}";
    </script>
    <p style="display:none" id="emptyCartWarning" class="alert alert-warning">{l s='Your shopping cart is empty.' mod='bestkit_opc'}</p>
    {if isset($lastProductAdded) AND $lastProductAdded}
        {foreach from=$products item=product}
            {if $product.id_product == $lastProductAdded.id_product AND (!$product.id_product_attribute OR ($product.id_product_attribute == $lastProductAdded.id_product_attribute))}
                <div class="cart_last_product">
                    <div class="cart_last_product_header">
                        <div class="left">{l s='Last added product' mod='bestkit_opc'}</div>
                    </div>
                    <a  class="cart_last_product_img" href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category)|escape:'htmlall':'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small')}" alt="{$product.name|escape:'htmlall':'UTF-8'}"/></a>
                    <div class="cart_last_product_content">
                        <h5><a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category)|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}</a></h5>
                        {if isset($product.attributes) && $product.attributes}<a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category)|escape:'htmlall':'UTF-8'}">{$product.attributes|escape:'htmlall':'UTF-8'}</a>{/if}
                    </div>
                    <br class="clear" />
                </div>
            {/if}
        {/foreach}
    {/if}
    {*<p class="info-title">{l s='Your shopping cart contains:' mod='bestkit_opc'} <span id="summary_products_quantity">{$productNumber} {if $productNumber == 1}{l s='product' mod='bestkit_opc'}{else}{l s='products' mod='bestkit_opc'}{/if}</span></p>*}
    <div id="order-detail-content">
        <table id="cart_summary" class="table-opc">
            <colgroup>
                <col width="1" />
                <col />
                <col width="1" />
                <col width="1" />
            </colgroup>
            <thead>
                <tr>
                    <th>{l s='Product' mod='bestkit_opc'}</th>
                    <th>{l s='Description' mod='bestkit_opc'}</th>
                    {*
                    <th>{l s='Ref.' mod='bestkit_opc'}</th>
                    <th>{l s='Unit price' mod='bestkit_opc'}</th>
                    *}
                    <th>{l s='Qty' mod='bestkit_opc'}</th>
                    <th>{l s='Total' mod='bestkit_opc'}</th>
                    {*<th></th>*}
                </tr>
            </thead>
                <tfoot>
			{if $opcModuleObj->getConfig('show_total_products')}
                {if $use_taxes}
                    {if $priceDisplay}
                        <tr>
                            <td colspan="3">
                                {if $display_tax_label}
                                    {l s='Total products (tax excl.):' mod='bestkit_opc'}
                                {else}
                                    {l s='Total products:' mod='bestkit_opc'}
                                {/if}
                            </td>
                            <td id="total_product" class="price-opc">{displayPrice price=$total_products}</td>
                        </tr>
                    {else}
                        <tr>
                            <td colspan="3">
                                {if $display_tax_label}
                                    {l s='Total products (tax incl.):' mod='bestkit_opc'}
                                {else}
                                    {l s='Total products:' mod='bestkit_opc'}
                                {/if}
                            </td>
                            <td id="total_product" class="price-opc">{displayPrice price=$total_products_wt}</td>
                        </tr>
                    {/if}
                {else}
                    <tr>
                        <td colspan="3">{l s='Total products:' mod='bestkit_opc'}</td>
                        <td id="total_product" class="price-opc">{displayPrice price=$total_products}</td>
                    </tr>
                {/if}
			{/if}
			
			{if $opcModuleObj->getConfig('show_total_discount')}
                <tr {if $total_discounts == 0}style="display:none"{/if}>
                    <td colspan="3">
                        {if $use_taxes && $display_tax_label}
                            {l s='Total vouchers (tax excl.):' mod='bestkit_opc'}
                        {else}
                            {l s='Total vouchers:' mod='bestkit_opc'}
                        {/if}
                    </td>
                    <td id="total_discount" class="price-opc">
                        {if $use_taxes && !$priceDisplay}
                            {assign var='total_discounts_negative' value=$total_discounts * -1}
                        {else}
                            {assign var='total_discounts_negative' value=$total_discounts_tax_exc * -1}
                        {/if}
                        {displayPrice price=$total_discounts_negative}
                    </td>
                </tr>
			{/if}
			
			{if $opcModuleObj->getConfig('show_total_wrapping')}
                <tr {if $total_wrapping == 0}style="display: none;"{/if}>
                    <td colspan="3">
                        {if $use_taxes}
                            {if $display_tax_label}
                                {l s='Total gift-wrapping (tax incl.):' mod='bestkit_opc'}
                            {else}
                                {l s='Total gift-wrapping:' mod='bestkit_opc'}
                            {/if}
                        {else}
                            {l s='Total gift-wrapping:' mod='bestkit_opc'}
                        {/if}
                    </td>
                    <td id="total_wrapping" class="price-opc">
                        {if $use_taxes}
                            {if $priceDisplay}
                                {displayPrice price=$total_wrapping_tax_exc}
                            {else}
                                {displayPrice price=$total_wrapping}
                            {/if}
                        {else}
                            {displayPrice price=$total_wrapping_tax_exc}
                        {/if}
                    </td>
                </tr>
			{/if}
			
			{if $opcModuleObj->getConfig('show_total_shipping')}
                {if $total_shipping_tax_exc <= 0 && !isset($virtualCart)}
                    <tr>
                        <td colspan="3">{l s='Shipping:' mod='bestkit_opc'}</td>
                        <td id="total_shipping" class="price-opc free-price-opc">{l s='Free!' mod='bestkit_opc'}</td>
                    </tr>
                {else}
                    {if $use_taxes}
                        {if $priceDisplay}
                            <tr {if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
                                <td colspan="3">
                                    {if $display_tax_label}
                                        {l s='Total shipping (tax excl.):' mod='bestkit_opc'}
                                    {else}
                                        {l s='Total shipping:' mod='bestkit_opc'}
                                    {/if}
                                </td>
                                <td id="total_shipping" class="price-opc">{displayPrice price=$total_shipping_tax_exc}</td>
                            </tr>
                        {else}
                            <tr {if $total_shipping <= 0} style="display:none;"{/if}>
                                <td colspan="3">
                                    {if $display_tax_label}
                                        {l s='Total shipping (tax incl.):' mod='bestkit_opc'}
                                    {else}
                                        {l s='Total shipping:' mod='bestkit_opc'}
                                    {/if}
                                </td>
                                <td id="total_shipping" class="price-opc">{displayPrice price=$total_shipping}</td>
                            </tr>
                        {/if}
                    {else}
                        <tr {if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
                            <td colspan="3">
                                {l s='Total shipping:' mod='bestkit_opc'}
                            </td>
                            <td id="total_shipping" class="price-opc">{displayPrice price=$total_shipping_tax_exc}</td>
                        </tr>
                    {/if}
                {/if}
			{/if}
			
			{if $opcModuleObj->getConfig('show_total_tax_excl')}
                <tr>
                    <td colspan="3">
                        {l s='Total (tax excl.):' mod='bestkit_opc'}
                    </td>
                    <td id="total_price_without_tax" class="price-opc">{displayPrice price=$total_price_without_tax}</td>
                </tr>
			{/if}
				
			{if $opcModuleObj->getConfig('show_total_tax')}
                <tr>
                    <td colspan="3">
                        {l s='Total tax:' mod='bestkit_opc'}
                    </td>
                    <td id="total_tax" class="price-opc">{displayPrice price=$total_tax}</td>
                </tr>
			{/if}
			
			{if $opcModuleObj->getConfig('show_total')}
                <tr>
                    {if $use_taxes}
                        <td colspan="3">{l s='Total:' mod='bestkit_opc'}</td>
                        <td>
                            <span id="total_price" class="total-price-opc">{displayPrice price=$total_price}</span>
                        </td>
                    {else}
                        <td colspan="3">{l s='Total:' mod='bestkit_opc'}</td>
                        <td>
                            <span id="total_price" class="total-price-opc">{displayPrice price=$total_price_without_tax}</span>
                        </td>
                    {/if}
                </tr>
			{/if}
			
			{if $voucherAllowed}
                <tr>
					<td colspan="4" id="cart_voucher">
						{if isset($errors_discount) && $errors_discount}
							<div class="alert alert-error">
								<ul>
									{foreach $errors_discount as $k=>$error}
										<li>{$error|escape:'htmlall':'UTF-8'}</li>
									{/foreach}
								</ul>
							</div>
						{/if}
						<form action="{if $opc}{$link->getPageLink('order-opc.php', true)}{else}{$link->getPageLink('order.php', true)}{/if}" method="post" id="voucher">
							<fieldset>
								<h4><label for="discount_name">{l s='Vouchers' mod='bestkit_opc'}</label></h4>
								<p>
									<input type="text" class="discount_name form-control" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" />
								</p>
								<p class="submit">
									<input type="hidden" name="submitDiscount" />
									<button class="btn btn-default button button-small exclusive" name="submitAddDiscount" id="submitAddDiscount"><span>{l s='OK' mod='bestkit_opc'}</span></button>
								
									{*<input type="submit" name="submitAddDiscount" value="{l s='OK' mod='bestkit_opc'}" class="button" />*}
								</p>
							{if $displayVouchers}
								<h4 class="title_offers">{l s='Take advantage of our offers:' mod='bestkit_opc'}</h4>
								<div id="display_cart_vouchers">
								{foreach $displayVouchers as $voucher}
									<span onclick="$('#discount_name').val('{$voucher.name|escape:false}');return false;" class="voucher_name">{$voucher.name|escape:false}</span> - {$voucher.description|escape:false} <br />
								{/foreach}
								</div>
							{/if}
							</fieldset>
						</form>
					</td>
                </tr>
			{/if}
			
            </tfoot>
            <tbody>
                {foreach $products as $product}
                    {assign var='productId' value=$product.id_product}
                    {assign var='productAttributeId' value=$product.id_product_attribute}
                    {assign var='quantityDisplayed' value=0}
                    {assign var='odd' value=$product@iteration%2}
                    {assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId) || count($gift_products)}
                    {* Display the product line *}
                    {include file="./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}
                    {* Then the customized datas ones*}
                    {if isset($customizedDatas.$productId.$productAttributeId)}
                        {foreach $customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] as $id_customization=>$customization}
                            <tr id="product_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}">
                                <td></td>
                                <td colspan="2">
                                    {foreach $customization.datas as $type => $custom_data}
                                        {if $type == $CUSTOMIZE_FILE}
                                            <div class="customizationUploaded">
                                                <ul class="customizationUploaded">
                                                    {foreach $custom_data as $picture}
                                                        <li><img src="{$pic_dir|escape:false}{$picture.value|escape:false}_small" alt="" class="customizationUploaded" /></li>
                                                    {/foreach}
                                                </ul>
                                            </div>
                                        {elseif $type == $CUSTOMIZE_TEXTFIELD}
                                            <ul class="typedText">
                                                {foreach $custom_data as $textField}
                                                    <li>
                                                        {if $textField.name}
                                                            {$textField.name|escape:false}
                                                        {else}
                                                            {l s='Text #' mod='bestkit_opc'}{($textField@index+1)|escape:false}
                                                        {/if}
                                                        {l s=':' mod='bestkit_opc'}
                                                        {$textField.value|escape:false}
                                                    </li>
                                                {/foreach}
                                            </ul>
                                        {/if}
                                    {/foreach}
                                </td>
                                <td class="cart_quantity">
                                    {if isset($cannotModify) AND $cannotModify == 1}
                                        <span style="float:left">{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}</span>
                                    {else}
                                        <div id="cart_quantity_button" class="cart_quantity_button" style="float:left">
                                            <a rel="nofollow" class="cart_quantity_up" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery}&amp;id_customization={$id_customization}&amp;token={$token_cart}")}" title="{l s='Add' mod='bestkit_opc'}"><img src="{$img_dir}icon/quantity_up.gif" alt="{l s='Add' mod='bestkit_opc'}" width="14" height="9" /></a><br />
                                            {if $product.minimal_quantity < ($customization.quantity -$quantityDisplayed) OR $product.minimal_quantity <= 1}
                                            <a rel="nofollow" class="cart_quantity_down" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery}&amp;id_customization={$id_customization}&amp;op=down&amp;token={$token_cart}")}" title="{l s='Subtract' mod='bestkit_opc'}">
                                                <img src="{$img_dir|escape:false}icon/quantity_down.gif" alt="{l s='Subtract' mod='bestkit_opc'}" width="14" height="9" />
                                            </a>
                                            {else}
                                            <a class="cart_quantity_down" style="opacity: 0.3;" id="cart_quantity_down_{$product.id_product|escape:false}_{$product.id_product_attribute|escape:false}_{$id_customization|escape:false}" href="#" title="{l s='Subtract' mod='bestkit_opc'}">
                                                <img src="{$img_dir|escape:false}icon/quantity_down.gif" alt="{l s='Subtract' mod='bestkit_opc'}" width="14" height="9" />
                                            </a>
                                            {/if}
                                        </div>
                                        <input type="hidden" value="{$customization.quantity|escape:false}" name="quantity_{$product.id_product|escape:false}_{$product.id_product_attribute|escape:false}_{$id_customization|escape:false}_hidden"/>
                                        <input size="2" type="text" value="{$customization.quantity|escape:false}" class="cart_quantity_input" name="quantity_{$product.id_product|escape:false}_{$product.id_product_attribute|escape:false}_{$id_customization|escape:false}_{$product.id_address_delivery|intval}"/>
                                    {/if}
                                </td>
                                <td class="cart_delete">
                                    {if isset($cannotModify) AND $cannotModify == 1}
                                    {else}
                                        <a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;id_address_delivery={$product.id_address_delivery}&amp;token={$token_cart}")}">{l s='Delete' mod='bestkit_opc'}</a>
                                    {/if}
                                </td>
                            </tr>
                            {assign var='quantityDisplayed' value=$quantityDisplayed+$customization.quantity}
                        {/foreach}
                        {* If it exists also some uncustomized products *}
                        {if $product.quantity-$quantityDisplayed > 0}{include file="./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}{/if}
                    {/if}
                {/foreach}
                {assign var='last_was_odd' value=$product@iteration%2}
                {foreach $gift_products as $product}
                    {assign var='productId' value=$product.id_product}
                    {assign var='productAttributeId' value=$product.id_product_attribute}
                    {assign var='quantityDisplayed' value=0}
                    {assign var='odd' value=($product@iteration+$last_was_odd)%2}
                    {assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId)}
                    {assign var='cannotModify' value=1}
                    {* Display the gift product line *}
                    {include file="./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}
                {/foreach}
            </tbody>
            {if sizeof($discounts)}
                <tbody>
                    {foreach $discounts as $discount}
                        <tr id="cart_discount_{$discount.id_discount|escape:false}">
                            <td>{$discount.name|escape:false}</td>
                            <td>
                                <span class="price-opc">
                                    {if !$priceDisplay}
                                        {displayPrice price=$discount.value_real*-1}
                                    {else}
                                        {displayPrice price=$discount.value_tax_exc*-1}
                                    {/if}
                                </span>
                            </td>
                            <td>
                                {* 1 *}
                                {if strlen($discount.code)}
                                    <a href="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}?deleteDiscount={$discount.id_discount}" class="cart_discount_delete" attr-id_discount="{$discount.id_discount|intval}" title="{l s='Delete' mod='bestkit_opc'}">{l s='Delete' mod='bestkit_opc'}</a>
                                {/if}
                                {*
                                    <a href="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}?deleteDiscount={$discount.id_discount}" class="price_discount_delete" title="{l s='Delete' mod='bestkit_opc'}">
                                        <img src="{$img_dir|escape:false}icon/delete.gif" alt="{l s='Delete' mod='bestkit_opc'}" class="icon" width="11" height="13" />
                                    </a>
                                *}
                            </td>
                            <td>
                                <span class="price-opc">
                                    {if !$priceDisplay}
                                        {displayPrice price=$discount.value_real*-1}
                                    {else}
                                        {displayPrice price=$discount.value_tax_exc*-1}
                                    {/if}
                                </span>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            {/if}
        </table>
    </div>

    {if $show_option_allow_separate_package}
        <p class="checkbox">
            <label for="allow_seperated_package">
                <input type="checkbox" name="allow_seperated_package" id="allow_seperated_package" {if $cart->allow_seperated_package}checked="checked"{/if} />
                {l s='Send the available products first' mod='bestkit_opc'}
            </label>
        </p>
    {/if}
    {if !$opc}
        {if Configuration::get('PS_ALLOW_MULTISHIPPING')}
            <p class="checkbox">
                <label for="enable-multishipping">
                    <input type="checkbox" {if $multi_shipping}checked="checked"{/if} id="enable-multishipping" />
                    {l s='I want to specify a delivery address for each individual product.' mod='bestkit_opc'}
                </label>
            </p>
        {/if}
    {/if}

    <div id="HOOK_SHOPPING_CART">{$HOOK_SHOPPING_CART|escape:false}</div>

    {* Define the style if it doesn't exist in the PrestaShop version*}
    {* Will be deleted for 1.5 version and more *}
    {if !isset($addresses_style)}
        {$addresses_style.company = 'address_company'}
        {$addresses_style.vat_number = 'address_company'}
        {$addresses_style.firstname = 'address_name'}
        {$addresses_style.lastname = 'address_name'}
        {$addresses_style.address1 = 'address_address1'}
        {$addresses_style.address2 = 'address_address2'}
        {$addresses_style.city = 'address_city'}
        {$addresses_style.country = 'address_country'}
        {$addresses_style.phone = 'address_phone'}
        {$addresses_style.phone_mobile = 'address_phone_mobile'}
        {$addresses_style.alias = 'address_title'}
    {/if}

    {if ((!empty($delivery_option) AND !isset($virtualCart)) OR $delivery->id OR $invoice->id) AND !$opc}
        <div class="order_delivery clearfix">
            {if !isset($formattedAddresses)}
                {if $delivery->id}
                    <ul id="delivery_address" class="address item">
                        <li class="address_title">{l s='Delivery address' mod='bestkit_opc'}</li>
                        {if $delivery->company}<li class="address_company">{$delivery->company|escape:'htmlall':'UTF-8'}</li>{/if}
                        <li class="address_name">{$delivery->firstname|escape:'htmlall':'UTF-8'} {$delivery->lastname|escape:'htmlall':'UTF-8'}</li>
                        <li class="address_address1">{$delivery->address1|escape:'htmlall':'UTF-8'}</li>
                        {if $delivery->address2}<li class="address_address2">{$delivery->address2|escape:'htmlall':'UTF-8'}</li>{/if}
                        <li class="address_city">{$delivery->postcode|escape:'htmlall':'UTF-8'} {$delivery->city|escape:'htmlall':'UTF-8'}</li>
                        <li class="address_country">{$delivery->country|escape:'htmlall':'UTF-8'} {if $delivery_state}({$delivery_state|escape:'htmlall':'UTF-8'}){/if}</li>
                    </ul>
                {/if}
                {if $invoice->id}
                    <ul id="invoice_address" class="address alternate_item">
                        <li class="address_title">{l s='Invoice address' mod='bestkit_opc'}</li>
                        {if $invoice->company}<li class="address_company">{$invoice->company|escape:'htmlall':'UTF-8'}</li>{/if}
                        <li class="address_name">{$invoice->firstname|escape:'htmlall':'UTF-8'} {$invoice->lastname|escape:'htmlall':'UTF-8'}</li>
                        <li class="address_address1">{$invoice->address1|escape:'htmlall':'UTF-8'}</li>
                        {if $invoice->address2}<li class="address_address2">{$invoice->address2|escape:'htmlall':'UTF-8'}</li>{/if}
                        <li class="address_city">{$invoice->postcode|escape:'htmlall':'UTF-8'} {$invoice->city|escape:'htmlall':'UTF-8'}</li>
                        <li class="address_country">{$invoice->country|escape:'htmlall':'UTF-8'} {if $invoice_state}({$invoice_state|escape:'htmlall':'UTF-8'}){/if}</li>
                    </ul>
                {/if}
            {else}
                {foreach $formattedAddresses as $address}
                    <ul class="address {if $address@last}last_item{elseif $address@first}first_item{/if} {if $address@index % 2}alternate_item{else}item{/if}">
                        <li class="address_title">{$address.object.alias|escape:false}</li>
                        {foreach $address.ordered as $pattern}
                            {assign var=addressKey value=" "|explode:$pattern}
                            <li>
                            {foreach $addressKey as $key}
                                <span class="{if isset($addresses_style[$key])}{$addresses_style[$key]}{/if}">
                                    {if isset($address.formated[$key])}
                                        {$address.formated[$key]|escape:'htmlall':'UTF-8'}
                                    {/if}
                                </span>
                            {/foreach}
                            </li>
                        {/foreach}
                    </ul>
                {/foreach}
            {/if}
        </div>
    {/if}
    {if !empty($HOOK_SHOPPING_CART_EXTRA)}
        <div class="cart_navigation_extra">
            <div id="HOOK_SHOPPING_CART_EXTRA">{$HOOK_SHOPPING_CART_EXTRA|escape:false}</div>
        </div>
    {/if}
{/if}
</div>