{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright  2007-2015 Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\front\products\flags -->
<div class="product-box">
	{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
		<a class="sale-box" href="{$product.link|escape:'html':'UTF-8'}">
			<span class="label-sale product-label label-warning label">{l s='Sale' mod='appagebuilder'}</span>
		</a>
	{/if}
	{if isset($product.new) && $product.new == 1}
		<a class="new-box" href="{$product.link|escape:'html':'UTF-8'}">
			<span class="label-new product-label label-info label">{l s='New' mod='appagebuilder'}</span>
		</a>
	{/if}
</div>