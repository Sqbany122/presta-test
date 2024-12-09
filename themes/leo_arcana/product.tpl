{*
* 2007-2015 PrestaShop
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
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $errors|@count == 0}
	{if !isset($priceDisplayPrecision)}
		{assign var='priceDisplayPrecision' value=2}
	{/if}
	{if !$priceDisplay || $priceDisplay == 2}
		{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, 6)}
		{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
	{elseif $priceDisplay == 1}
		{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, 6)}
		{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
	{/if}
<div itemscope itemtype="https://schema.org/Product">
	<meta itemprop="url" content="{$link->getProductLink($product)}">
	<div class="primary_block row">
		{if !$content_only}
			<div class="container">
				<div class="top-hr"></div>
			</div>
		{/if}
		{if isset($adminActionDisplay) && $adminActionDisplay}
			<div id="admin-action" class="container">
				<p class="alert alert-info">{l s='This product is not visible to your customers.'}
					<input type="hidden" id="admin-action-product-id" value="{$product->id}" />
					<a id="publish_button" class="btn btn-outline button button-small" href="#">
						<span>{l s='Publish'}</span>
					</a>
					<a id="lnk_view" class="btn btn-outline button button-small" href="#">
						<span>{l s='Back'}</span>
					</a>
				</p>
				<p id="admin-action-result"></p>
			</div>
		{/if}
		{if isset($confirmation) && $confirmation}
			<p class="confirmation">
				{$confirmation}
			</p>
		{/if}
		<!-- left infos-->  
		<div class="pb-left-column col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<!-- product img-->   
			{if isset($images) && count($images) > 0}
				<!-- thumbnails -->
				<div id="views_block" class="clearfix {if isset($images) && count($images) < 2}hidden{/if}">
					{if isset($images) && count($images) > 2}
						<span class="view_scroll_spacer">
							<a id="view_scroll_left" class="" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">
								{l s='Previous'}
							</a>
						</span>
					{/if}
					<div id="thumbs_list">
						<ul id="thumbs_list_frame">
						{if isset($images)}
							{foreach from=$images item=image name=thumbnails}
								{assign var=imageIds value="`$product->id`-`$image.id_image`"}
								{if !empty($image.legend)}
									{assign var=imageTitle value=$image.legend|escape:'html':'UTF-8'}
								{else}
									{assign var=imageTitle value=$product->name|escape:'html':'UTF-8'}
								{/if}
								<li id="thumbnail_{$image.id_image}"{if $smarty.foreach.thumbnails.last} class="last"{/if}>
									<a{if $jqZoomEnabled && $have_image && !$content_only} href="javascript:void(0);" rel="{literal}{{/literal}gallery: 'gal1', smallimage: '{$link->getImageLink($product->link_rewrite, $imageIds, 'large_default')|escape:'html':'UTF-8'}',largeimage: '{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}'{literal}}{/literal}"{else} href="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}"	data-fancybox-group="other-views" class="fancybox{if $image.id_image == $cover.id_image} shown{/if}"{/if} title="{$imageTitle}">
										<img class="img-responsive" id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'small_default')|escape:'html':'UTF-8'}" alt="{$imageTitle}" title="{$imageTitle}" itemprop="image" />
									</a>
								</li>
							{/foreach}
						{/if}
						</ul>
					</div> <!-- end thumbs_list -->
					{if isset($images) && count($images) > 2}
						<a id="view_scroll_right" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">
							{l s='Next'}
						</a>
					{/if}
				</div> <!-- end views-block -->
				<!-- end thumbnails -->
			{/if}
							<div class="p-label">
					{if $product->new}					
						<span class="label-new label label-info">{l s='New'}</span>					
					{/if}
					{if $product->on_sale}					
						<span class="label-sale label label-warning">{l s='Sale!'}</span>					
					{elseif $product->specificPrice && $product->specificPrice.reduction && $productPriceWithoutReduction > $productPrice}
						<span class="label-discount label label-danger">{l s='Reduced price!'}</span>
					{/if}
				</div>
			<div id="image-block" class="clearfix">

				{if $have_image}
					<span id="view_full_size">
						{if $jqZoomEnabled && $have_image && !$content_only}
							<a class="jqzoom" title="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}" rel="gal1" href="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'thickbox_default')|escape:'html':'UTF-8'}">
								<img class="img-responsive" itemprop="image" src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')|escape:'html':'UTF-8'}" title="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}" alt="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}"/>
							</a>
						{if !$content_only}
							<span class="span_link">
								{l s='View larger'}
							</span>
						{/if}
						{else}
							<img id="bigpic" class="img-responsive" itemprop="image" src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')|escape:'html':'UTF-8'}" title="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}" alt="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}"/>
							{if !$content_only}
								<span class="span_link no-print status-enable"></span>
							{/if}
						{/if}
					</span>
				{else}
					<span id="view_full_size">
						<img itemprop="image" src="{$img_prod_dir}{$lang_iso}-default-large_default.jpg" id="bigpic" alt="" title="{$product->name|escape:'html':'UTF-8'}"/>
						{if !$content_only}
							<span class="span_link">
								{l s='View larger'}
							</span>
						{/if}
					</span>
				{/if}
			</div> <!-- end image-block -->
			{if isset($images) && count($images) > 1}
				<p class="resetimg clear no-print">
					<span id="wrapResetImages" style="display: none;">
						<a href="{$link->getProductLink($product)|escape:'html':'UTF-8'}" data-id="resetImages">
							<i class="fa fa-repeat"></i>
							{l s='Display all pictures'}
						</a>
					</span>
				</p>
			{/if}
		</div>
		<!-- end left infos-->
		<!-- center infos -->
		<div class="pb-center-column col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<h1 itemprop="name">{$product->name|escape:'html':'UTF-8'}</h1>
			{if $product->online_only}
				<p class="online_only">{l s='Online only'}</p>
			{/if}

		</div>
		<!-- end center infos-->
		<!-- pb-right-column-->
		<div class="pb-right-column col-xs-12 col-sm-12 col-md-6 col-lg-6">		
	
			{if ($product->show_price && !isset($restricted_country_mode)) || isset($groups) || $product->reference || (isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS)}
				<!-- add to cart form-->
				<form id="buy_block"{if $PS_CATALOG_MODE && !isset($groups) && $product->quantity > 0} class="hidden"{/if} action="{$link->getPageLink('cart')|escape:'html':'UTF-8'}" method="post">
					<!-- hidden datas -->
					<p class="hidden">
						<input type="hidden" name="token" value="{$static_token}" />
						<input type="hidden" name="id_product" value="{$product->id|intval}" id="product_page_product_id" />
						<input type="hidden" name="add" value="1" />
						<input type="hidden" name="id_product_attribute" id="idCombination" value="" />
					</p>
					<div class="box-info-product">
						<div class="content_prices clearfix">
							{if $product->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
								<!-- prices -->
								<div class="price">
									<p class="our_price_display" itemprop="offers" itemscope itemtype="https://schema.org/Offer">{strip}
										{if $product->quantity > 0}<link itemprop="availability" href="https://schema.org/InStock"/>{/if}
										{if $priceDisplay >= 0 && $priceDisplay <= 2}
											<span id="our_price_display" itemprop="price" content="{$productPrice}">{convertPrice price=$productPrice|floatval}</span>
											{*
												{if $tax_enabled  && ((isset($display_tax_label) && $display_tax_label == 1) || !isset($display_tax_label))}
													{if $priceDisplay == 1} {l s='tax excl.'}{else} {l s='tax incl.'}{/if}
												{/if}
											*}
											<meta itemprop="priceCurrency" content="{$currency->iso_code}" />
											{hook h="displayProductPriceBlock" product=$product type="price"}
										{/if}
									{/strip}</p>
									<p id="reduction_percent" {if $productPriceWithoutReduction <= 0 || !$product->specificPrice || $product->specificPrice.reduction_type != 'percentage'} style="display:none;"{/if}>{strip}
										<span id="reduction_percent_display">
											{if $product->specificPrice && $product->specificPrice.reduction_type == 'percentage'}-{$product->specificPrice.reduction*100}%{/if}
										</span>
									{/strip}</p>
									<p id="reduction_amount" {if $productPriceWithoutReduction <= 0 || !$product->specificPrice || $product->specificPrice.reduction_type != 'amount' || $product->specificPrice.reduction|floatval ==0} style="display:none"{/if}>{strip}
										<span id="reduction_amount_display">
										{if $product->specificPrice && $product->specificPrice.reduction_type == 'amount' && $product->specificPrice.reduction|floatval !=0}
											-{convertPrice price=$productPriceWithoutReduction|floatval-$productPrice|floatval}
										{/if}
										</span>
									{/strip}</p>
									<p id="old_price"{if (!$product->specificPrice || !$product->specificPrice.reduction)} class="hidden"{/if}>{strip}
										{if $priceDisplay >= 0 && $priceDisplay <= 2}
											{hook h="displayProductPriceBlock" product=$product type="old_price"}
											<span id="old_price_display">{if $productPriceWithoutReduction > $productPrice}{convertPrice price=$productPriceWithoutReduction|floatval}{/if}
											{*{if $productPriceWithoutReduction > $productPrice && $tax_enabled && $display_tax_label == 1} {if $priceDisplay == 1}{l s='tax excl.'}{else}{l s='tax incl.'}{/if}{/if}*}</span>
										{/if}
									{/strip}</p>
									{if $priceDisplay == 2}
										<br />
										<span id="pretaxe_price">{strip}
											<span id="pretaxe_price_display">{convertPrice price=$product->getPrice(false, $smarty.const.NULL)}</span> {l s='tax excl.'}
										{/strip}</span>
									{/if}
								</div> <!-- end prices -->
								{if $packItems|@count && $productPrice < $product->getNoPackPrice()}
									<p class="pack_price">{l s='Instead of'} <span style="text-decoration: line-through;">{convertPrice price=$product->getNoPackPrice()}</span></p>
								{/if}
								{if $product->ecotax != 0}
									<p class="price-ecotax">{l s='Including'} <span id="ecotax_price_display">{if $priceDisplay == 2}{$ecotax_tax_exc|convertAndFormatPrice}{else}{$ecotax_tax_inc|convertAndFormatPrice}{/if}</span> {l s='for ecotax'}
										{if $product->specificPrice && $product->specificPrice.reduction}
										<br />{l s='(not impacted by the discount)'}
										{/if}
									</p>
								{/if}
								{*if !empty($product->unity) && $product->unit_price_ratio > 0.000000}
									{math equation="pprice / punit_price" pprice=$productPrice  punit_price=$product->unit_price_ratio assign=unit_price}
									<p class="unit-price"><span id="unit_price_display">{convertPrice price=$unit_price}</span> {l s='per'} {$product->unity|escape:'html':'UTF-8'}</p>
									{hook h="displayProductPriceBlock" product=$product type="unit_price"}
								{/if*}
							{/if} {*close if for show price*}
							{hook h="displayProductPriceBlock" product=$product type="weight" hook_origin='product_sheet'}
							{hook h="displayProductPriceBlock" product=$product type="after_price"}
							<div class="clear"></div>
							<!-- quantity wanted -->
							{if !$PS_CATALOG_MODE}
								<p id="quantity_wanted_p"{if (!$allow_oosp && $product->quantity <= 0) || !$product->available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>
									<label for="quantity_wanted">{l s='Quantity'}</label>
									<input type="text" min="1" name="qty" id="quantity_wanted" class="text form-control" value="{if isset($quantityBackup)}{$quantityBackup|intval}{else}{if $product->minimal_quantity > 1}{$product->minimal_quantity}{else}1{/if}{/if}" />
								<span class="pull-left">
									
									<a href="#" data-field-qty="qty" class="btn status-enable button-plus btn-sm product_quantity_up">
										<span><i class="fa fa-angle-up"></i></span>
									</a>
									
									<a href="#" data-field-qty="qty" class="btn status-enable button-minus btn-sm product_quantity_down">
										<span><i class="fa fa-angle-down"></i></span>
									</a>
									
								</span>
									<span class="clearfix"></span>
								</p>
							{/if}
							<!-- minimal quantity wanted -->
							<p id="minimal_quantity_wanted_p"{if $product->minimal_quantity <= 1 || !$product->available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>
								{l s='The minimum purchase order quantity for the product is'} <b id="minimal_quantity_label">{$product->minimal_quantity}</b>
							</p>
						</div> <!-- end content_prices -->

						<div class="product_attributes clearfix">
							{if isset($groups)}
								<!-- attributes -->
								<div id="attributes">
									<div class="clearfix"></div>
									{foreach from=$groups key=id_attribute_group item=group}
										{if $group.attributes|@count}
											<fieldset class="attribute_fieldset">
												<label class="attribute_label" {if $group.group_type != 'color' && $group.group_type != 'radio'}for="group_{$id_attribute_group|intval}"{/if}>{$group.name|escape:'html':'UTF-8'}&nbsp;</label>
												{assign var="groupName" value="group_$id_attribute_group"}
												<div class="attribute_list">
													{if ($group.group_type == 'select')}
														<select name="{$groupName}" id="group_{$id_attribute_group|intval}" class="form-control attribute_select no-print">
															{foreach from=$group.attributes key=id_attribute item=group_attribute}
																<option value="{$id_attribute|intval}"{if (isset($smarty.get.$groupName) && $smarty.get.$groupName|intval == $id_attribute) || $group.default == $id_attribute} selected="selected"{/if} title="{$group_attribute|escape:'html':'UTF-8'}">{$group_attribute|escape:'html':'UTF-8'}</option>
															{/foreach}
														</select>
													{elseif ($group.group_type == 'color')}
														<ul id="color_to_pick_list" class="clearfix">
															{assign var="default_colorpicker" value=""}
															{foreach from=$group.attributes key=id_attribute item=group_attribute}
																{assign var='img_color_exists' value=file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
																<li{if $group.default == $id_attribute} class="selected"{/if}>
																	<a href="{$link->getProductLink($product)|escape:'html':'UTF-8'}" id="color_{$id_attribute|intval}" name="{$colors.$id_attribute.name|escape:'html':'UTF-8'}" class="color_pick{if ($group.default == $id_attribute)} selected{/if}"{if !$img_color_exists && isset($colors.$id_attribute.value) && $colors.$id_attribute.value} style="background:{$colors.$id_attribute.value|escape:'html':'UTF-8'};"{/if} title="{$colors.$id_attribute.name|escape:'html':'UTF-8'}">
																		{if $img_color_exists}
																			<img src="{$img_col_dir}{$id_attribute|intval}.jpg" alt="{$colors.$id_attribute.name|escape:'html':'UTF-8'}" title="{$colors.$id_attribute.name|escape:'html':'UTF-8'}" width="20" height="20" />
																		{/if}
																	</a>
																</li>
																{if ($group.default == $id_attribute)}
																	{$default_colorpicker = $id_attribute}
																{/if}
															{/foreach}
														</ul>
														<input type="hidden" class="color_pick_hidden" name="{$groupName|escape:'html':'UTF-8'}" value="{$default_colorpicker|intval}" />
													{elseif ($group.group_type == 'radio')}
														<ul>
															{foreach from=$group.attributes key=id_attribute item=group_attribute}
																<li>
																	<input type="radio" class="attribute_radio" name="{$groupName|escape:'html':'UTF-8'}" value="{$id_attribute}" {if ($group.default == $id_attribute)} checked="checked"{/if} />
																	<span>{$group_attribute|escape:'html':'UTF-8'}</span>
																</li>
															{/foreach}
														</ul>
													{/if}
												</div> <!-- end attribute_list -->
											</fieldset>
										{/if}
									{/foreach}
								</div> <!-- end attributes -->
							{/if}
						</div> <!-- end product_attributes -->


			{if ($display_qties == 1 && !$PS_CATALOG_MODE && $PS_STOCK_MANAGEMENT && $product->available_for_order)}
				<!-- number of item in stock -->
				<p id="pQuantityAvailable"{if $product->quantity <= 0} style="display: none;"{/if}>
					<span id="quantityAvailable">{$product->quantity|intval}</span>
					<span {if $product->quantity > 1} style="display: none;"{/if} id="quantityAvailableTxt">{l s='Item'}</span>
					<span {if $product->quantity == 1} style="display: none;"{/if} id="quantityAvailableTxtMultiple">{l s='Items'}</span>
				</p>
			{/if}
			<!-- availability or doesntExist -->
			<p id="availability_statut"{if !$PS_STOCK_MANAGEMENT || ($product->quantity <= 0 && !$product->available_later && $allow_oosp) || ($product->quantity > 0 && !$product->available_now) || !$product->available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>
				{*<span id="availability_label">{l s='Availability:'}</span>*}
				<span id="availability_value" class="label{if $product->quantity <= 0 && !$allow_oosp} label-danger{elseif $product->quantity <= 0} label-warning{else} label-success{/if}">{if $product->quantity <= 0}{if $PS_STOCK_MANAGEMENT && $allow_oosp}{$product->available_later}{else}{l s='This product is no longer in stock'}{/if}{elseif $PS_STOCK_MANAGEMENT}{$product->available_now}{/if}</span>
			</p>
			{if $PS_STOCK_MANAGEMENT}
				{if !$product->is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
				<p class="warning_inline" id="last_quantities"{if ($product->quantity > $last_qties || $product->quantity <= 0) || $allow_oosp || !$product->available_for_order || $PS_CATALOG_MODE} style="display: none"{/if} >{l s='Warning: Last items in stock!'}</p>
			{/if}
			<p id="availability_date"{if ($product->quantity > 0) || !$product->available_for_order || $PS_CATALOG_MODE || !isset($product->available_date) || $product->available_date < $smarty.now|date_format:'%Y-%m-%d'} style="display: none;"{/if}>
				<span id="availability_date_label">{l s='Availability date:'}</span>
				<span id="availability_date_value">{if Validate::isDate($product->available_date)}{dateFormat date=$product->available_date full=false}{/if}</span>
			</p>
			
			<!-- Out of stock hook -->
			<div id="oosHook"{if $product->quantity > 0} style="display: none;"{/if}>
				{$HOOK_PRODUCT_OOS}
			</div>
			
						<div class="box-cart-bottom">
							<div{if (!$allow_oosp && $product->quantity <= 0) || !$product->available_for_order || (isset($restricted_country_mode) && $restricted_country_mode) || $PS_CATALOG_MODE} class="unvisible"{/if}>
								<p id="add_to_cart" class="buttons_bottom_block no-print" style="display:inline-block">
									<button type="submit" name="Submit" class="exclusive btn btn-outline">
										<span>{if $content_only && (isset($product->customization_required) && $product->customization_required)}{l s='Customize'}{else}{l s='Add to cart'}{/if}</span>
									</button>
								</p>

{hook h="displayCustomAlertBlockAnywhere"} 

{*foreach from=Product::getProductCategoriesFull(Tools::getValue('id_product')) item=cat}
	{if $cat.id_category == 1380 OR $cat.id_category == 1397 OR $cat.id_category == 1415 OR $cat.id_category == 1398 OR $cat.id_category == 1399 OR $cat.id_category == 1400 OR $cat.id_category == 1401 OR $cat.id_category == 1402 OR $cat.id_category == 1403 OR $cat.id_category == 1404 OR $cat.id_category == 1405 OR $cat.id_category == 1406 OR $cat.id_category == 1407 OR $cat.id_category == 1408 OR $cat.id_category == 1409 OR $cat.id_category == 1410 OR $cat.id_category == 1411 OR $cat.id_category == 1412 OR $cat.id_category == 1413 OR $cat.id_category == 1414 OR $cat.id_category == 1438 OR $cat.id_category == 1416 OR $cat.id_category == 1417 OR $cat.id_category == 1418 OR $cat.id_category == 1419 OR $cat.id_category == 1420 OR $cat.id_category == 1421 OR $cat.id_category == 1422 OR $cat.id_category == 1423 OR $cat.id_category == 1424 OR $cat.id_category == 1425 OR $cat.id_category == 1426 OR $cat.id_category == 1427 OR $cat.id_category == 1428 OR $cat.id_category == 1429 OR $cat.id_category == 1430 OR $cat.id_category == 1431 OR $cat.id_category == 1432 OR $cat.id_category == 1433 OR $cat.id_category == 1434 OR $cat.id_category == 1435 OR $cat.id_category == 1436 OR $cat.id_category == 1437}
	<a class="infop" href="/info/wysylka-pasz.html" target="_blank"><div style="font-size:11px;text-align:center;width:100%;padding:5px;background:#e36767;color:#fff;font-weight:bold;">ZOBACZ TERMINY WYSYŁKI PASZ I SUPLEMENTÓW W OKRESIE ŚWIĄTECZNO-NOWOROCZNYM</div></a>{break}{/if}
{/foreach*}

{foreach from=Product::getProductCategoriesFull(Tools::getValue('id_product')) item=cat}
	{if $cat.id_category == 1263 OR $cat.id_category == 1265 OR $cat.id_category == 1374 OR $cat.id_category == 1378}
<div style="display:block;width:100%;margin-bottom:15px;"><i class="fa fa-truck" style="float:left;font-size:32px;margin-right:10px;"></i><span style="color:#000;font-size:21px;font-weight:bold;line-height:28px;">Wysyłka <span style="line-height:14px;color:green;font-size:21px;font-weight:bold;">7 - 30 dni</span></span></div>
{break}
	{/if}
{/foreach}

{*if $product->id_category_default != 1331 &&
								$product->id_category_default != 1380 &&
								$product->id_category_default != 1397 &&
								$product->id_category_default != 1415 &&
								$product->id_category_default != 1398 &&
								$product->id_category_default != 1399 &&
								$product->id_category_default != 1400 &&
								$product->id_category_default != 1401 &&
								$product->id_category_default != 1402 &&
								$product->id_category_default != 1403 &&
								$product->id_category_default != 1404 &&
								$product->id_category_default != 1405 &&
								$product->id_category_default != 1406 &&
								$product->id_category_default != 1407 &&
								$product->id_category_default != 1408 &&
								$product->id_category_default != 1409 &&
								$product->id_category_default != 1410 &&
								$product->id_category_default != 1411 &&
								$product->id_category_default != 1412 &&
								$product->id_category_default != 1413 &&
								$product->id_category_default != 1414 &&
								$product->id_category_default != 1438 &&
								$product->id_category_default != 1416 &&
								$product->id_category_default != 1417 &&
								$product->id_category_default != 1418 &&
								$product->id_category_default != 1419 &&
								$product->id_category_default != 1420 &&
								$product->id_category_default != 1421 &&
								$product->id_category_default != 1422 &&
								$product->id_category_default != 1423 &&
								$product->id_category_default != 1424 &&
								$product->id_category_default != 1425 &&
								$product->id_category_default != 1426 &&
								$product->id_category_default != 1427 &&
								$product->id_category_default != 1428 &&
								$product->id_category_default != 1429 &&
								$product->id_category_default != 1430 &&
								$product->id_category_default != 1431 &&
								$product->id_category_default != 1432 &&
								$product->id_category_default != 1433 &&
								$product->id_category_default != 1434 &&
								$product->id_category_default != 1435 &&
								$product->id_category_default != 1263 &&
								$product->id_category_default != 1265 &&
								$product->id_category_default != 1374 &&
								$product->id_category_default != 1378} 
								<!--<img src="/themes/24ha.png" alt="Wysyłka produktu w ciągu 48 godzin!" style="margin-top:10px;float:right;">!-->
								{else}
								
								{/if} 
								
{foreach from=Product::getProductCategoriesFull(Tools::getValue('id_product')) item=cat}
    {if $cat.name == 'Wyprzedaż'}<img src="/themes/wyprz.png" alt="Wyprzedaż produktu">{/if}
{/foreach*}



<div class="block content">
    <div class="service_box">
        <div class="icon-box"><i class="icomoon icon-hours"></i></div>
        <div class="service-item"><span class="title">Wysyłka w 48h</span><span class="content">Twoje zamówienie skompletujemy i wyślemy w ciągu 48 godzin</span></div>
    </div>
    <div class="service_box">
        <div class="icon-box"><i class="icomoon icon-shipped"></i></div>
        <div class="service-item"><span class="title">Darmowa dostawa</span><span class="content">Przy płatności z góry, dla zamówień powyżej 300zł, wysyłka gratis (NIE DOTYCZY PASZ)</span></div>
    </div>
    <div class="service_box">
        <div class="icon-box"><i class="icomoon icon-wallet"></i></div>
        <div class="service-item"><span class="title">Bezpieczne płatności</span><span class="content">Zapłać przez internet, szybko, łatwo i bezpiecznie</span></div>
    </div>
    <div class="service_box">
        <div class="icon-box"><i class="icomoon icon-winner"></i></div>
        <div class="service-item"><span class="title">Gwarancja jakości</span><span class="content">Oferujemy oryginalne produkty sprawdzonych dostawców.</span></div>
    </div>
</div>

<div id="paypoinfo"></div>
<!-- POPUP -->
<section class="paypo"><dialog class="paypo__dialog"><svg class="paypo__close" enable-background="new 0 0 17 17" version="1.1" viewBox="0 0 17 17" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"><g enable-background="new"><path d="m16.23 13.84-13.05-13.05c-0.64-0.64-1.69-0.64-2.33 0s-0.64 1.69 0 2.33l13.05 13.05c0.64 0.64 1.69 0.64 2.33 0 0.65-0.64 0.65-1.69 0-2.33z" clip-rule="evenodd" fill="#C1C1C1" fill-rule="evenodd" /></g><g enable-background="new"><path d="m16.24 0.79c-0.64-0.64-1.69-0.64-2.33 0l-13.05 13.05c-0.64 0.64-0.64 1.69 0 2.33s1.69 0.64 2.33 0l13.05-13.05c0.64-0.64 0.64-1.69 0-2.33z" clip-rule="evenodd" fill="#C1C1C1" fill-rule="evenodd" /></g></svg><iframe class="paypo__container" src="https://popup.paypo.pl" title="PayPo"></iframe></dialog></section>

<!-- SKRYPT -->
{literal}
<script>window.addEventListener('DOMContentLoaded',function(){document.querySelector('#paypoinfo').insertAdjacentHTML('afterend','<a href="#" class="paypo__open""><svg width="100%" height="auto" viewBox="0 0 1000 99" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_8649_6173)"><rect width="1000" height="99" fill="white"/><path d="M52.9238 65.122V56.787H44.5368V65.122H52.9238Z" fill="#A60585"/><path d="M52.8867 53.8821V45.4785H44.4997V53.8821H52.8867Z" fill="#36B587"/><path d="M41.7041 65.0871V56.6835H33.3171V65.0871H41.7041Z" fill="#FAD15C"/><path d="M77.9264 44.9519C77.9264 50.7448 73.2744 55.4074 67.2749 55.4074H62.7095V64.9903H55.7539V34.5021H67.2749C73.2744 34.5021 77.9264 39.1633 77.9264 44.9519ZM70.9707 44.9519C70.9707 42.7292 69.4052 41.0303 67.2749 41.0303H62.7095V48.8736H67.2749C69.4052 48.8736 70.9707 47.1789 70.9707 44.9519Z" fill="black"/><path d="M102.187 42.6202V64.9807H95.4926V62.8798C94.0193 64.5787 91.8331 65.6082 88.8417 65.6082C82.9959 65.6082 78.1748 60.4652 78.1748 53.8011C78.1748 47.1371 82.9959 41.9955 88.8417 41.9955C91.8331 41.9955 94.0137 43.0235 95.4926 44.7239V42.623L102.187 42.6202ZM95.4926 53.7997C95.4926 50.4467 93.2561 48.3444 90.1808 48.3444C87.1056 48.3444 84.869 50.4453 84.869 53.7997C84.869 57.1542 87.1056 59.2621 90.1808 59.2621C93.2561 59.2621 95.4926 57.1542 95.4926 53.7997Z" fill="black"/><path d="M127.618 42.6199L120.042 64.1597C117.56 71.2215 113.612 74.0199 107.41 73.709V67.4903C110.512 67.4903 111.975 66.5099 112.951 63.8053L104.135 42.6199H111.448L116.369 56.2057L120.536 42.6199H127.618Z" fill="black"/><path d="M152.404 44.9519C152.404 50.7448 147.751 55.4074 141.751 55.4074H137.186V64.9903H130.23V34.5021H141.751C147.747 34.5021 152.404 39.1633 152.404 44.9519ZM145.447 44.9519C145.447 42.7292 143.882 41.0303 141.751 41.0303H137.186V48.8736H141.751C143.882 48.8736 145.447 47.1789 145.447 44.9519Z" fill="black"/><path d="M153.274 54.1049C153.274 47.6146 158.404 42.6046 164.795 42.6046C171.186 42.6046 176.318 47.6146 176.318 54.1049C176.318 60.5953 171.186 65.6067 164.795 65.6067C158.404 65.6067 153.274 60.5967 153.274 54.1049ZM169.795 54.1049C169.795 51.0124 167.622 48.9647 164.795 48.9647C161.969 48.9647 159.795 51.0124 159.795 54.1049C159.795 57.1975 161.969 59.2465 164.795 59.2465C167.622 59.2465 169.795 57.1989 169.795 54.1049Z" fill="black"/><path d="M259.414 66L249.82 51.999L259.024 38.7H251.926L243.58 50.556V38.7H237.34V66H243.58V53.364L252.316 66H259.414ZM272.891 46.5V57.147C272.891 59.916 271.409 61.164 269.264 61.164C267.392 61.164 265.871 60.033 265.871 57.615V46.5H260.021V58.473C260.021 63.738 263.375 66.546 267.314 66.546C269.888 66.546 271.838 65.61 272.891 64.167V66H278.741V46.5H272.891ZM294.189 45.954C291.576 45.954 289.665 46.851 288.378 48.333V46.5H282.528V73.8H288.378V64.167C289.665 65.649 291.576 66.546 294.189 66.546C299.298 66.546 303.51 62.061 303.51 56.25C303.51 50.439 299.298 45.954 294.189 45.954ZM293.019 61.008C290.328 61.008 288.378 59.175 288.378 56.25C288.378 53.325 290.328 51.492 293.019 51.492C295.71 51.492 297.66 53.325 297.66 56.25C297.66 59.175 295.71 61.008 293.019 61.008ZM326.998 52.116V46.5H322.981V41.04L317.131 42.795V46.5H314.011V52.116H317.131V59.019C317.131 64.479 319.354 66.78 326.998 66V60.696C324.424 60.852 322.981 60.696 322.981 59.019V52.116H326.998ZM334.653 58.59H348.498C348.654 57.849 348.732 57.069 348.732 56.25C348.732 50.361 344.52 45.954 338.826 45.954C332.664 45.954 328.452 50.439 328.452 56.25C328.452 62.061 332.586 66.546 339.255 66.546C342.96 66.546 345.846 65.181 347.757 62.529L343.077 59.838C342.297 60.696 340.932 61.32 339.333 61.32C337.188 61.32 335.394 60.618 334.653 58.59ZM334.536 54.222C335.082 52.233 336.564 51.141 338.787 51.141C340.542 51.141 342.297 51.96 342.921 54.222H334.536ZM357.397 49.971V46.5H351.547V66H357.397V57.186C357.397 53.325 360.829 52.35 363.247 52.74V46.11C360.79 46.11 358.177 47.358 357.397 49.971ZM379.064 46.5V48.333C377.777 46.851 375.866 45.954 373.253 45.954C368.144 45.954 363.932 50.439 363.932 56.25C363.932 62.061 368.144 66.546 373.253 66.546C375.866 66.546 377.777 65.649 379.064 64.167V66H384.914V46.5H379.064ZM374.423 61.008C371.732 61.008 369.782 59.175 369.782 56.25C369.782 53.325 371.732 51.492 374.423 51.492C377.114 51.492 379.064 53.325 379.064 56.25C379.064 59.175 377.114 61.008 374.423 61.008ZM395.957 60.54L403.328 50.4V46.5H388.508V51.96H395.489L388.118 62.1V66H403.718V60.54H395.957ZM414.12 59.955H407.88L406.32 71.46H411L414.12 59.955Z" fill="black"/><path d="M434.02 60.54L441.391 50.4V46.5H426.571V51.96H433.552L426.181 62.1V66H441.781V60.54H434.02ZM457.749 46.5V48.333C456.462 46.851 454.551 45.954 451.938 45.954C446.829 45.954 442.617 50.439 442.617 56.25C442.617 62.061 446.829 66.546 451.938 66.546C454.551 66.546 456.462 65.649 457.749 64.167V66H463.599V46.5H457.749ZM453.108 61.008C450.417 61.008 448.467 59.175 448.467 56.25C448.467 53.325 450.417 51.492 453.108 51.492C455.799 51.492 457.749 53.325 457.749 56.25C457.749 59.175 455.799 61.008 453.108 61.008ZM479.049 45.954C476.436 45.954 474.525 46.851 473.238 48.333V46.5H467.388V73.8H473.238V64.167C474.525 65.649 476.436 66.546 479.049 66.546C484.158 66.546 488.37 62.061 488.37 56.25C488.37 50.439 484.158 45.954 479.049 45.954ZM477.879 61.008C475.188 61.008 473.238 59.175 473.238 56.25C473.238 53.325 475.188 51.492 477.879 51.492C480.57 51.492 482.52 53.325 482.52 56.25C482.52 59.175 480.57 61.008 477.879 61.008ZM499.179 46.5L497.229 47.826V37.53H491.379V51.843L489.429 53.169V58.746L491.379 57.42V66H497.229V53.403L499.179 52.077V46.5ZM515.35 46.5V48.333C514.063 46.851 512.152 45.954 509.539 45.954C504.43 45.954 500.218 50.439 500.218 56.25C500.218 62.061 504.43 66.546 509.539 66.546C512.152 66.546 514.063 65.649 515.35 64.167V66H521.2V46.5H515.35ZM510.709 61.008C508.018 61.008 506.068 59.175 506.068 56.25C506.068 53.325 508.018 51.492 510.709 51.492C513.4 51.492 515.35 53.325 515.35 56.25C515.35 59.175 513.4 61.008 510.709 61.008ZM530.917 44.16L534.037 38.7H541.057L536.533 44.16H530.917ZM534.349 66.546C538.171 66.546 541.486 64.557 543.163 61.515L538.054 58.59C537.43 59.955 535.987 60.774 534.271 60.774C531.736 60.774 529.864 58.941 529.864 56.25C529.864 53.559 531.736 51.726 534.271 51.726C535.987 51.726 537.391 52.545 538.054 53.91L543.163 50.946C541.486 47.943 538.132 45.954 534.349 45.954C528.421 45.954 524.014 50.439 524.014 56.25C524.014 62.061 528.421 66.546 534.349 66.546ZM561.706 60.54L569.077 50.4V46.5H554.257V51.96H561.238L553.867 62.1V66H569.467V60.54H561.706ZM585.436 46.5V48.333C584.149 46.851 582.238 45.954 579.625 45.954C574.516 45.954 570.304 50.439 570.304 56.25C570.304 62.061 574.516 66.546 579.625 66.546C582.238 66.546 584.149 65.649 585.436 64.167V66H591.286V46.5H585.436ZM580.795 61.008C578.104 61.008 576.154 59.175 576.154 56.25C576.154 53.325 578.104 51.492 580.795 51.492C583.486 51.492 585.436 53.325 585.436 56.25C585.436 59.175 583.486 61.008 580.795 61.008ZM616.646 49.776L621.365 43.77V38.7H603.815V44.55H613.799L608.846 50.829L611.225 54.378H612.512C615.086 54.378 616.217 55.743 616.217 57.42C616.217 59.097 615.086 60.462 612.512 60.462C610.133 60.462 608.963 59.37 608.339 57.576L602.957 60.696C604.634 64.713 608.378 66.546 612.512 66.546C617.777 66.546 622.457 63.426 622.457 57.42C622.457 53.637 620 50.946 616.646 49.776ZM635.401 66.546C642.46 66.546 646.516 60.774 646.516 52.35C646.516 43.926 642.46 38.154 635.401 38.154C628.342 38.154 624.286 43.926 624.286 52.35C624.286 60.774 628.342 66.546 635.401 66.546ZM635.401 60.462C632.125 60.462 630.526 57.615 630.526 52.35C630.526 47.085 632.125 44.238 635.401 44.238C638.677 44.238 640.276 47.085 640.276 52.35C640.276 57.615 638.677 60.462 635.401 60.462ZM672.728 38.7V48.333C671.441 46.851 669.53 45.954 666.917 45.954C661.808 45.954 657.596 50.439 657.596 56.25C657.596 62.061 661.808 66.546 666.917 66.546C669.53 66.546 671.441 65.649 672.728 64.167V66H678.578V38.7H672.728ZM668.087 61.008C665.396 61.008 663.446 59.175 663.446 56.25C663.446 53.325 665.396 51.492 668.087 51.492C670.778 51.492 672.728 53.325 672.728 56.25C672.728 59.175 670.778 61.008 668.087 61.008ZM693.794 45.954C691.22 45.954 689.27 46.89 688.217 48.333V46.5H682.367V66H688.217V55.353C688.217 52.584 689.699 51.336 691.844 51.336C693.716 51.336 695.237 52.467 695.237 54.885V66H701.087V54.027C701.087 48.762 697.733 45.954 693.794 45.954ZM707.527 44.784C709.438 44.784 711.037 43.185 711.037 41.274C711.037 39.363 709.438 37.764 707.527 37.764C705.616 37.764 704.017 39.363 704.017 41.274C704.017 43.185 705.616 44.784 707.527 44.784ZM704.602 66H710.452V46.5H704.602V66Z" fill="#36B587"/><line x1="211" y1="29" x2="211" y2="77" stroke="#36B587" stroke-width="2"/><g filter="url(#filter0_dddddd_8649_6173)"><rect x="762" y="31" width="205" height="42" rx="21" fill="#2CD091"/><path d="M832.34 59.3821C829.4 59.3821 827.52 57.9821 826.74 55.9221L829.5 54.3221C830 55.4821 830.86 56.2621 832.44 56.2621C833.96 56.2621 834.34 55.6621 834.34 55.1221C834.34 54.2621 833.54 53.9221 831.44 53.3421C829.36 52.7621 827.32 51.7621 827.32 49.082C827.32 46.3821 829.6 44.822 832.02 44.822C834.32 44.822 836.12 45.922 837.14 47.982L834.44 49.5621C833.96 48.5621 833.3 47.9421 832.02 47.9421C831.02 47.9421 830.52 48.442 830.52 49.0021C830.52 49.6421 830.86 50.0821 833.04 50.7421C835.16 51.3821 837.54 52.1221 837.54 55.0821C837.54 57.7821 835.38 59.3821 832.34 59.3821ZM845.019 48.8221C847.639 48.8221 849.799 51.1221 849.799 54.1021C849.799 57.0821 847.639 59.3821 845.019 59.3821C843.679 59.3821 842.699 58.9221 842.039 58.1621V63.1021H839.039V49.1021H842.039V50.0421C842.699 49.2821 843.679 48.8221 845.019 48.8221ZM844.419 56.5421C845.799 56.5421 846.799 55.6021 846.799 54.1021C846.799 52.6021 845.799 51.6621 844.419 51.6621C843.039 51.6621 842.039 52.6021 842.039 54.1021C842.039 55.6021 843.039 56.5421 844.419 56.5421ZM854.5 50.8821C854.9 49.5421 856.24 48.902 857.5 48.902V52.3021C856.26 52.1021 854.5 52.6021 854.5 54.5821V59.1021H851.5V49.1021H854.5V50.8821ZM865.87 49.1021H868.87V59.1021H865.87V58.1621C865.21 58.9221 864.23 59.3821 862.89 59.3821C860.27 59.3821 858.11 57.0821 858.11 54.1021C858.11 51.1221 860.27 48.8221 862.89 48.8221C864.23 48.8221 865.21 49.2821 865.87 50.0421V49.1021ZM863.49 56.5421C864.87 56.5421 865.87 55.6021 865.87 54.1021C865.87 52.6021 864.87 51.6621 863.49 51.6621C862.11 51.6621 861.11 52.6021 861.11 54.1021C861.11 55.6021 862.11 56.5421 863.49 56.5421ZM882.071 49.1021H885.271L882.071 59.1021H879.271L877.671 53.7621L876.071 59.1021H873.271L870.071 49.1021H873.271L874.711 54.4621L876.271 49.1021H879.071L880.631 54.4621L882.071 49.1021ZM893.331 45.102H896.331V59.1021H893.331V58.1621C892.671 58.9221 891.691 59.3821 890.351 59.3821C887.731 59.3821 885.571 57.0821 885.571 54.1021C885.571 51.1221 887.731 48.8221 890.351 48.8221C891.691 48.8221 892.671 49.2821 893.331 50.0421V45.102ZM890.951 56.5421C892.331 56.5421 893.331 55.6021 893.331 54.1021C893.331 52.6021 892.331 51.6621 890.951 51.6621C889.571 51.6621 888.571 52.6021 888.571 54.1021C888.571 55.6021 889.571 56.5421 890.951 56.5421ZM905.632 45.102L903.312 47.9021H900.432L902.032 45.102H905.632ZM902.252 56.3021H906.232V59.1021H898.232V57.1021L902.012 51.9021H898.432V49.1021H906.032V51.1021L902.252 56.3021Z" fill="black"/></g></g><defs><filter id="filter0_dddddd_8649_6173" x="712" y="3" width="305" height="142" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset/><feGaussianBlur stdDeviation="0.5"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_8649_6173"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1"/><feGaussianBlur stdDeviation="1.5"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.06 0"/><feBlend mode="normal" in2="effect1_dropShadow_8649_6173" result="effect2_dropShadow_8649_6173"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="2"/><feGaussianBlur stdDeviation="3"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect2_dropShadow_8649_6173" result="effect3_dropShadow_8649_6173"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="8"/><feGaussianBlur stdDeviation="5"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect3_dropShadow_8649_6173" result="effect4_dropShadow_8649_6173"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="12"/><feGaussianBlur stdDeviation="15"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.02 0"/><feBlend mode="normal" in2="effect4_dropShadow_8649_6173" result="effect5_dropShadow_8649_6173"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="22"/><feGaussianBlur stdDeviation="25"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.02 0"/><feBlend mode="normal" in2="effect5_dropShadow_8649_6173" result="effect6_dropShadow_8649_6173"/><feBlend mode="normal" in="SourceGraphic" in2="effect6_dropShadow_8649_6173" result="shape"/></filter><clipPath id="clip0_8649_6173"><rect width="1000" height="99" fill="white"/></clipPath></defs></svg></a>');document.querySelector('.paypo__open').addEventListener('click',function(){document.body.style.overflow='hidden',document.querySelector('.paypo__dialog').setAttribute('open',!0)}),document.querySelector('.paypo__dialog').addEventListener('click',function(){document.body.style.overflow='auto',document.querySelector('.paypo__dialog').removeAttribute('open')})});</script>
<!-- STYLE -->
<style>.paypo,.paypo :not(svg,svg*){all:initial;display:block}.paypo dialog{position:static;display:none;width:auto;height:auto;padding:0;border:none;margin:0;background:0 0;color:inherit}.paypo dialog[open]{display:block}.paypo iframe{display:block;border:none}.paypo__open{display:block;width:100%;cursor:pointer}.paypo .paypo__dialog{position:fixed;top:0;left:0;z-index:1000000;width:100%;height:100%;background-color:rgba(0,0,0,.5)}.paypo .paypo__close{position:absolute;top:18px;right:20px;display:block;width:20px;height:20px;cursor:pointer}@media (min-width:600px){.paypo .paypo__close{right:calc(50% - 288px)}}.paypo .paypo__container{width:616px;max-width:100%;height:100%;margin-right:auto;margin-left:auto}</style>
{/literal}
							</div>
						</div> <!-- end box-cart-bottom -->
						<div class="group-btn">
							{if isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS}{$HOOK_PRODUCT_ACTIONS}{/if}
						</div>
						

			{*if isset($HOOK_EXTRA_RIGHT) && $HOOK_EXTRA_RIGHT}{$HOOK_EXTRA_RIGHT}{/if*}
			{*if !$content_only}
				<!-- usefull links-->
					<ul id="usefull_link_block" class="clearfix no-print list-inline">
					{if $HOOK_EXTRA_LEFT}{$HOOK_EXTRA_LEFT}{/if}
					<li class="print">
						<a href="javascript:print();">
							{l s='Print'}
						</a>
					</li>
				</ul>
			{/if*}
					</div>
				</form>
			{/if}
			
			{if $product->description_short || $packItems|@count > 0}
				<div id="short_description_block">

					{if $packItems|@count > 0}
						<div class="short_description_pack">
						<h3>{l s='Pack content'}</h3>
							{foreach from=$packItems item=packItem}

							<div class="pack_content">
								{$packItem.pack_quantity} x <a href="{$link->getProductLink($packItem.id_product, $packItem.link_rewrite, $packItem.category)|escape:'html':'UTF-8'}">{$packItem.name|escape:'html':'UTF-8'}</a>
								<p>{$packItem.description_short}</p>
							</div>
							{/foreach}
						</div>
					{/if}
				</div> <!-- end short_description_block -->
			{/if}
			
		</div>
		<!-- end right infos-->
		
</div>
</div>	

 
<div class="clearfix"></div>
<div class="more-info-product col-md-12" style="background:#f2f2f2;margin:15px 0px 30px 0px;padding-bottom:20px;">
	<div id="description">
		<h4 class="title-info-product">{l s='Opis produktu' d='Shop.Theme.Catalog'}</h4>

       		<div class="product-description">{$product->description}</div>

	</div>
	
	
			    {*if isset($product_manufacturer->id)}
		<h4 class="title-info-product">{l s='Informacje' d='Shop.Theme.Catalog'}</h4>
			      	<div class="product-manufacturer">
				        <a style="display:inline-block;vertical-align:middle" href="{$link->getManufacturerLink($product_manufacturer->id)}"><img class="img-responsive" src="/img/m/{$product_manufacturer->id}.jpg" alt="{$product_manufacturer->name}" style="height:60px;" /></a>
			      	</div>
			    {/if*}
	
				<div style="display:block;padding:15px 0px;">
				{if isset($product_manufacturer->id)}Producent: <a href="{$link->getManufacturerLink($product_manufacturer->id)}"><b>{$product_manufacturer->name}</b></a><br>{/if}
				
				Indeks produktu: <span style="color:#000;font-weight:bold;">{$product->reference}</span>
			</div>





	</div>
</div>




	{if !$content_only}
	
		{*if isset($USE_PTABS) && $USE_PTABS*}
			{*<div class="clearfix more_info_block">{include file="$tpl_dir./sub/product_info/tab.tpl"}</div>*}
		{*else*}
			<div class="clearfix more_info_default">{include file="$tpl_dir./sub/product_info/default.tpl"}</div>
		{*/if*}
	
		{if isset($HOOK_PRODUCT_FOOTER) && $HOOK_PRODUCT_FOOTER}{$HOOK_PRODUCT_FOOTER}{/if}
	{/if}

{strip}
{if isset($smarty.get.ad) && $smarty.get.ad}
	{addJsDefL name=ad}{$base_dir|cat:$smarty.get.ad|escape:'html':'UTF-8'}{/addJsDefL}
{/if}
{if isset($smarty.get.adtoken) && $smarty.get.adtoken}
	{addJsDefL name=adtoken}{$smarty.get.adtoken|escape:'html':'UTF-8'}{/addJsDefL}
{/if}
{addJsDef allowBuyWhenOutOfStock=$allow_oosp|boolval}
{addJsDef availableNowValue=$product->available_now|escape:'quotes':'UTF-8'}
{addJsDef availableLaterValue=$product->available_later|escape:'quotes':'UTF-8'}
{addJsDef attribute_anchor_separator=$attribute_anchor_separator|escape:'quotes':'UTF-8'}
{addJsDef attributesCombinations=$attributesCombinations}
{addJsDef currentDate=$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}
{if isset($combinations) && $combinations}
	{addJsDef combinations=$combinations}
	{addJsDef combinationsFromController=$combinations}
	{addJsDef displayDiscountPrice=$display_discount_price}
	{addJsDefL name='upToTxt'}{l s='Up to' js=1}{/addJsDefL}
{/if}
{if isset($combinationImages) && $combinationImages}
	{addJsDef combinationImages=$combinationImages}
{/if}
{addJsDef customizationId=$id_customization}
{addJsDef customizationFields=$customizationFields}
{addJsDef default_eco_tax=$product->ecotax|floatval}
{addJsDef displayPrice=$priceDisplay|intval}
{addJsDef ecotaxTax_rate=$ecotaxTax_rate|floatval}
{if isset($cover.id_image_only)}
	{addJsDef idDefaultImage=$cover.id_image_only|intval}
{else}
	{addJsDef idDefaultImage=0}
{/if}
{addJsDef img_ps_dir=$img_ps_dir}
{addJsDef img_prod_dir=$img_prod_dir}
{addJsDef id_product=$product->id|intval}
{addJsDef jqZoomEnabled=$jqZoomEnabled|boolval}
{addJsDef maxQuantityToAllowDisplayOfLastQuantityMessage=$last_qties|intval}
{addJsDef minimalQuantity=$product->minimal_quantity|intval}
{addJsDef noTaxForThisProduct=$no_tax|boolval}
{if isset($customer_group_without_tax)}
	{addJsDef customerGroupWithoutTax=$customer_group_without_tax|boolval}
{else}
	{addJsDef customerGroupWithoutTax=false}
{/if}
{if isset($group_reduction)}
	{addJsDef groupReduction=$group_reduction|floatval}
{else}
	{addJsDef groupReduction=false}
{/if}
{addJsDef oosHookJsCodeFunctions=Array()}
{addJsDef productHasAttributes=isset($groups)|boolval}
{addJsDef productPriceTaxExcluded=($product->getPriceWithoutReduct(true)|default:'null' - $product->ecotax)|floatval}
{addJsDef productPriceTaxIncluded=($product->getPriceWithoutReduct(false)|default:'null' - $product->ecotax * (1 + $ecotaxTax_rate / 100))|floatval}
{addJsDef productBasePriceTaxExcluded=($product->getPrice(false, null, 6, null, false, false) - $product->ecotax)|floatval}
{addJsDef productBasePriceTaxExcl=($product->getPrice(false, null, 6, null, false, false)|floatval)}
{addJsDef productBasePriceTaxIncl=($product->getPrice(true, null, 6, null, false, false)|floatval)}
{addJsDef productReference=$product->reference|escape:'html':'UTF-8'}
{addJsDef productAvailableForOrder=$product->available_for_order|boolval}
{addJsDef productPriceWithoutReduction=$productPriceWithoutReduction|floatval}
{addJsDef productPrice=$productPrice|floatval}
{addJsDef productUnitPriceRatio=$product->unit_price_ratio|floatval}
{addJsDef productShowPrice=(!$PS_CATALOG_MODE && $product->show_price)|boolval}
{addJsDef PS_CATALOG_MODE=$PS_CATALOG_MODE}
{if $product->specificPrice && $product->specificPrice|@count}
	{addJsDef product_specific_price=$product->specificPrice}
{else}
	{addJsDef product_specific_price=array()}
{/if}
{if $display_qties == 1 && $product->quantity}
	{addJsDef quantityAvailable=$product->quantity}
{else}
	{addJsDef quantityAvailable=0}
{/if}
{addJsDef quantitiesDisplayAllowed=$display_qties|boolval}
{if $product->specificPrice && $product->specificPrice.reduction && $product->specificPrice.reduction_type == 'percentage'}
	{addJsDef reduction_percent=$product->specificPrice.reduction*100|floatval}
{else}
	{addJsDef reduction_percent=0}
{/if}
{if $product->specificPrice && $product->specificPrice.reduction && $product->specificPrice.reduction_type == 'amount'}
	{addJsDef reduction_price=$product->specificPrice.reduction|floatval}
{else}
	{addJsDef reduction_price=0}
{/if}
{if $product->specificPrice && $product->specificPrice.price}
	{addJsDef specific_price=$product->specificPrice.price|floatval}
{else}
	{addJsDef specific_price=0}
{/if}
{addJsDef specific_currency=($product->specificPrice && $product->specificPrice.id_currency)|boolval} {* TODO: remove if always false *}
{addJsDef stock_management=$PS_STOCK_MANAGEMENT|intval}
{addJsDef taxRate=$tax_rate|floatval}
{addJsDefL name=doesntExist}{l s='This combination does not exist for this product. Please select another combination.' js=1}{/addJsDefL}
{addJsDefL name=doesntExistNoMore}{l s='This product is no longer in stock' js=1}{/addJsDefL}
{addJsDefL name=doesntExistNoMoreBut}{l s='with those attributes but is available with others.' js=1}{/addJsDefL}
{addJsDefL name=fieldRequired}{l s='Please fill in all the required fields before saving your customization.' js=1}{/addJsDefL}
{addJsDefL name=uploading_in_progress}{l s='Uploading in progress, please be patient.' js=1}{/addJsDefL}
{addJsDefL name='product_fileDefaultHtml'}{l s='No file selected' js=1}{/addJsDefL}
{addJsDefL name='product_fileButtonHtml'}{l s='Choose File' js=1}{/addJsDefL}
{/strip}
{else}

	{include file="$tpl_dir./404.tpl"}

{/if}
<style>{literal}#MagicToolboxSelectors6505 > div:nth-child(1){display:none!important;}{/literal}</style>
