<div class="product-container product-block" itemscope itemtype="http://schema.org/Product"><div class="left-block">
<!-- @file modules\appagebuilder\views\templates\front\products\image_container -->
<div class="product-image-container image">
	<div class="leo-more-info hidden-xs" data-idproduct="{$product.id_product|intval}"></div>
	<a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
		<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
		<span class="product-additional" data-idproduct="{$product.id_product|intval}"></span>
	</a>
</div>


<div class="box-buttons">
<!-- @file modules\appagebuilder\views\templates\front\products\compare -->
{if isset($comparator_max_item) && $comparator_max_item}
	<div class="compare">
		<a class="add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}" title="{l s='Add to Compare'}">
			<i class="fa fa-bar-chart"></i> <span>{l s='Add to Compare'}</span>
		</a>
	</div>
{/if}



<!-- @file modules\appagebuilder\views\templates\front\products\wishlist -->

{hook h='displayProductListFunctionalButtons' product=$product}

</div><div class="functional-buttons clearfix">
<!-- @file modules\appagebuilder\views\templates\front\products\add_to_cart -->
<div class="cart">
	{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
		{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
			{capture}add=1&amp;id_product={$product.id_product|intval}{if isset($product.id_product_attribute) && $product.id_product_attribute}&amp;ipa={$product.id_product_attribute|intval}{/if}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
			<a class="button ajax_add_to_cart_button btn btn-outline" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product-attribute="{$product.id_product_attribute|intval}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
				<i class="fa fa-shopping-cart"></i> <span>{l s='Add to cart'}</span>
			</a>
		{else}
			<span class="button ajax_add_to_cart_button btn btn-outline disabled" title="{l s='Out of stock'}">
				<i class="fa fa-shopping-cart"></i> <span>{l s='Out of stock'}</span>
			</span>
		{/if}
	{/if}
</div>
<!-- @file modules\appagebuilder\views\templates\front\products\quick_view -->
{if isset($quick_view) && $quick_view}
	<div class="quickview">
		<a class="quick-view" href="{$product.link|escape:'html':'UTF-8'}" data-link="{$product.link|escape:'html':'UTF-8'}" title="{l s='Quick View'}">
			<i class="fa fa-eye"></i> <span>{l s='Quick View'}</span>
		</a>
	</div>
{/if}


</div></div><div class="right-block"><div class="product-meta"><div class="product-left">
<!-- @file modules\appagebuilder\views\templates\front\products\name -->
<h5 itemprop="name" class="name">
	{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
	<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
		{$product.name|truncate:60:'...'|escape:'html':'UTF-8'}
	</a>
</h5>



<!-- @file modules\appagebuilder\views\templates\front\products\price -->
{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
	<div class="content_price">
		{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
			{hook h="displayProductPriceBlock" product=$product type='before_price'}
			<span class="price product-price">
				{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
			</span>
			{if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
				{hook h="displayProductPriceBlock" product=$product type="old_price"}
				<span class="old-price product-price">
					{displayWtPrice p=$product.price_without_reduction}
				</span>
				{hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
				{if $product.specific_prices.reduction_type == 'percentage'}
					<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
				{/if}
			{/if}
			{hook h="displayProductPriceBlock" product=$product type="price"}
			{hook h="displayProductPriceBlock" product=$product type="unit_price"}
			{hook h="displayProductPriceBlock" product=$product type='after_price'}
		{/if}
	</div>
{/if}
<!-- @file modules\appagebuilder\views\templates\front\products\description -->
<p class="product-desc" itemprop="description">
	{$product.description_short|strip_tags:'UTF-8'|truncate:360:'...'}
</p>

</div>

{php}
if(strpos($_SERVER['REQUEST_URI'], 'wyprzedaz') !== false ) { echo '<div class="wyprzp">&nbsp;</div>'; }
{/php}


{if $product.id_category_default != 1331 &&
								$product.id_category_default != 1380 &&
								$product.id_category_default != 1397 &&
								$product.id_category_default != 1415 &&
								$product.id_category_default != 1398 &&
								$product.id_category_default != 1399 &&
								$product.id_category_default != 1400 &&
								$product.id_category_default != 1401 &&
								$product.id_category_default != 1402 &&
								$product.id_category_default != 1403 &&
								$product.id_category_default != 1404 &&
								$product.id_category_default != 1405 &&
								$product.id_category_default != 1406 &&
								$product.id_category_default != 1407 &&
								$product.id_category_default != 1408 &&
								$product.id_category_default != 1409 &&
								$product.id_category_default != 1410 &&
								$product.id_category_default != 1411 &&
								$product.id_category_default != 1412 &&
								$product.id_category_default != 1413 &&
								$product.id_category_default != 1414 &&
								$product.id_category_default != 1438 &&
								$product.id_category_default != 1416 &&
								$product.id_category_default != 1417 &&
								$product.id_category_default != 1418 &&
								$product.id_category_default != 1419 &&
								$product.id_category_default != 1420 &&
								$product.id_category_default != 1421 &&
								$product.id_category_default != 1422 &&
								$product.id_category_default != 1423 &&
								$product.id_category_default != 1424 &&
								$product.id_category_default != 1425 &&
								$product.id_category_default != 1426 &&
								$product.id_category_default != 1427 &&
								$product.id_category_default != 1428 &&
								$product.id_category_default != 1429 &&
								$product.id_category_default != 1430 &&
								$product.id_category_default != 1431 &&
								$product.id_category_default != 1432 &&
								$product.id_category_default != 1433 &&
								$product.id_category_default != 1434 &&
								$product.id_category_default != 1435 &&
								$product.id_category_default != 1263 &&
                                                                $product.id_category_default != 1265 &&
                                                                $product.id_category_default != 1374 &&
                                                                $product.id_category_default != 1378} 
								{php} if(strpos($_SERVER['REQUEST_URI'], 'wyprzedaz') == false ) { echo '<div class="wysylkap">&nbsp;</div>'; } {/php} 
{else if 
$product.id_category_default == 1263 ||
$product.id_category_default == 1265 ||
$product.id_category_default == 1374 ||
$product.id_category_default == 1378 }
<div style="width:100%;clear:both;text-align:right;">

<div style="display:inline-block;">
<span style="color:#000;font-size:16px;font-weight:bold;line-height:16px;">Wysyłka<span><br><span style="line-height:14px;color:green;font-size:14px;font-weight:bold;">7 - 30 dni</span>
</div>
<div style="display:inline-block;font-size:32px;margin-left:10px;">
<i class="fa fa-truck" style="float:left;"></i>
</div>

</div>
{else}
{*<div style="width:100%;clear:both;text-align:right;">

<div style="display:inline-block;">
<span style="color:#000;font-size:16px;font-weight:bold;line-height:16px;">Wysyłka<span><br><span style="line-height:14px;color:green;font-size:14px;font-weight:bold;">od 25.06</span>
</div>
<div style="display:inline-block;font-size:32px;margin-left:10px;">
<i class="fa fa-truck" style="float:left;"></i>
</div>

</div>*}
{/if}
							
<!-- @file modules\appagebuilder\views\templates\front\products\flags -->
<div class="product-box">
	{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
		<a class="sale-box" href="{$product.link|escape:'html':'UTF-8'}">
			<span class="label-sale product-label label-warning label">{l s='Sale'}</span>
		</a>
	{/if}
	{if isset($product.new) && $product.new == 1}
		<a class="new-box" href="{$product.link|escape:'html':'UTF-8'}">
			<span class="label-new product-label label-info label">{l s='New'}</span>
		</a>
	{/if}
</div></div></div></div>
