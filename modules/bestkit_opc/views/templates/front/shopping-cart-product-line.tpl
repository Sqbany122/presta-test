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

<tr id="product_{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
    <td>
        <div class="image-product-opc">
            <a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}">
                <img class="image-product-img-opc" src="{$link->getImageLink($product.link_rewrite, $product.id_image, {$image_type})}" alt="{$product.name|escape:'htmlall':'UTF-8'}" {if isset($smallSize)}width="{$smallSize.width}" height="{$smallSize.height}" {/if} />
            </a>
            {if !isset($noDeleteButton) || !$noDeleteButton}
                {if (!isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed) > 0 && empty($product.gift)}
                    <a rel="nofollow" class="cart_quantity_delete delete-product-opc" id="{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")}">
                        <img src="{$img_dir|escape:false}icon/delete.gif" alt="{l s='Delete' mod='bestkit_opc'}" class="icon" width="12" height="12" />
                    </a>
                {/if}
            {/if}
        </div>
    </td>
    <td>
        <h5 class="title-product-opc">
            <a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}</a>
        </h5>
        {if isset($product.attributes) && $product.attributes}
            <div class="attr-product-opc">
                <a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}">{$product.attributes|escape:'htmlall':'UTF-8'}</a>
            </div>
        {/if}
    </td>
    <td style="display:none">
        {if $product.reference}
            {$product.reference|escape:'htmlall':'UTF-8'}
        {else}
            --
        {/if}
    </td>
{**}
    <td {if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0} style="text-align: center;"{/if}>
        <div class="cart_quantity_wrap">
            {if isset($cannotModify) AND $cannotModify == 1}
                <span>
                    {if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
                        {$customizedDatas.$productId.$productAttributeId|@count}
                    {else}
                        {($product.cart_quantity-$quantityDisplayed)|escape:false}
                    {/if}
                </span>
            {else}
                {if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}
                    <span id="cart_quantity_custom_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval}" >{$product.customizationQuantityTotal}</span>
                {/if}
                {if !isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0}
                    <div id="cart_quantity_button" class="cart_quantity_button">
                        <a rel="nofollow" class="cart_quantity_up" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")}" title="{l s='Add' mod='bestkit_opc'}">
                            <span>+</span>
                        </a>
                        <input size="2" type="text" autocomplete="off" class="cart_quantity_input form-control" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}"  name="quantity_{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}" />
                        {if $product.minimal_quantity < ($product.cart_quantity-$quantityDisplayed) OR $product.minimal_quantity <= 1}
                            <a rel="nofollow" class="cart_quantity_down" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;op=down&amp;token={$token_cart}")}" title="{l s='Subtract' mod='bestkit_opc'}">
                                <span>-</span>
                            </a>
                        {else}
                            <a class="cart_quantity_down" style="opacity: 0.3;" href="#" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}" title="{l s='You must purchase a minimum of %d of this product.' sprintf=$product.minimal_quantity mod='bestkit_opc'}">
                                <span>-</span>
                            </a>
                        {/if}
                    </div>
                    <input type="hidden" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}_hidden" />
                {/if}
            {/if}
        </div>
    </td>

    <td style="display: none">
        <span class="price-opc" id="product_price_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
            {if !empty($product.gift)}
                <span class="gift-icon">{l s='Gift!' mod='bestkit_opc'}</span>
            {else}
                {if isset($product.is_discounted) && $product.is_discounted}
                    <span style="text-decoration:line-through;">{convertPrice price=$product.price_without_specific_price}</span><br />
                {/if}
                {if !$priceDisplay}
                    {convertPrice price=$product.price_wt}
                {else}
                    {convertPrice price=$product.price}
                {/if}
            {/if}
        </span>
    </td>
{**}
    <td>
        <span class="price-opc" id="total_product_price_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
            {if !empty($product.gift)}
                <span class="gift-icon">{l s='Gift!' mod='bestkit_opc'}</span>
            {else}
                {if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
                    {if !$priceDisplay}{displayPrice price=$product.total_customization_wt}{else}{displayPrice price=$product.total_customization}{/if}
                {else}
                    {if !$priceDisplay}{displayPrice price=$product.total_wt}{else}{displayPrice price=$product.total}{/if}
                {/if}
            {/if}
        </span>
    </td>
    {if !isset($noDeleteButton) || !$noDeleteButton}
        <td style="display: none">
            {if (!isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed) > 0 && empty($product.gift)}
                <div>
                    <a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")}">{l s='Delete' mod='bestkit_opc'}</a>
                </div>
            {/if}
        </td>
    {/if}
{**}
</tr>
