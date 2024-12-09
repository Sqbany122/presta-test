<script type="text/javascript">
$(document).ready(function(){
		$('.more_info_block .page-product-heading li:first, .more_info_block .tab-content section:first').addClass('active');
	});
</script>
<ul class="nav nav-tabs tab-info page-product-heading">
	{if (isset($quantity_discounts) && count($quantity_discounts) > 0)}
		<li><a href="#quantityDiscount" data-toggle="tab">{l s='Volume discounts'}</a></li>
	{/if}
	{if $product->description}
		<li><a href="#tab2" data-toggle="tab">{l s='More info'}</a></li>
	{/if}
	{if isset($features) && $features}
		<li><a href="#tab3" data-toggle="tab">{l s='Data sheet'}</a></li>
	{/if}
				{if isset($HOOK_PRODUCT_TAB_CONTENT) && $HOOK_PRODUCT_TAB_CONTENT}
		{$HOOK_PRODUCT_TAB}
	{/if}
	{if isset($accessories) && $accessories}
		<li><a href="#tab4" data-toggle="tab">{l s='Accessories'}</a></li>
	{/if}			
	{if (isset($product) && $product->description) || (isset($features) && $features) || (isset($accessories) && $accessories) || (isset($HOOK_PRODUCT_TAB) && $HOOK_PRODUCT_TAB) || (isset($attachments) && $attachments) || isset($product) && $product->customizable}
		{if isset($attachments) && $attachments}
			<li><a href="#tab5" data-toggle="tab">{l s='Download'}</a></li>
		{/if}
		{if isset($product) && $product->customizable}
				<li><a href="#tab6" data-toggle="tab">{l s='Product customization'}</a></li>
		{/if}
	{/if}
	{if isset($packItems) && $packItems|@count > 0}
		<li><a href="#blockpack" data-toggle="tab">{l s='Pack'}</a></li>
	{/if}
</ul>
<div class="tab-content">
	{if (isset($quantity_discounts) && count($quantity_discounts) > 0)}
		<!-- quantity discount -->
		<section id="quantityDiscount" class="tab-pane page-product-box">
			<div id="quantityDiscount" class="tab-pane">
				<table class="std table-product-discounts table-responsive">
					<thead>
						<tr>
							<th>{l s='Quantity'}</th>
							<th>{if $display_discount_price}{l s='Price'}{else}{l s='Discount'}{/if}</th>
							<th>{l s='You Save'}</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
						<tr id="quantityDiscount_{$quantity_discount.id_product_attribute}" class="quantityDiscount_{$quantity_discount.id_product_attribute}" data-discount-type="{$quantity_discount.reduction_type}" data-discount="{$quantity_discount.real_value|floatval}" data-discount-quantity="{$quantity_discount.quantity|intval}">
							<td>
								{$quantity_discount.quantity|intval}
							</td>
							<td>
								{if $quantity_discount.price >= 0 || $quantity_discount.reduction_type == 'amount'}
									{if $display_discount_price}
										{if $quantity_discount.reduction_tax == 0 && !$quantity_discount.price}
											{convertPrice price = $productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction_with_tax)|floatval}
										{else}
											{convertPrice price=$productPriceWithoutReduction|floatval-$quantity_discount.real_value|floatval}
										{/if}
									{else}
										{convertPrice price=$quantity_discount.real_value|floatval}
									{/if}
								{else}
									{if $display_discount_price}
										{if $quantity_discount.reduction_tax == 0}
											{convertPrice price = $productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction_with_tax)|floatval}
										{else}
											{convertPrice price = $productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction)|floatval}
										{/if}
									{else}
										{$quantity_discount.real_value|floatval}%
									{/if}
								{/if}
							</td>
							<td>
								<span>{l s='Up to'}</span>
								{if $quantity_discount.price >= 0 || $quantity_discount.reduction_type == 'amount'}
									{$discountPrice=$productPriceWithoutReduction|floatval-$quantity_discount.real_value|floatval}
								{else}
									{$discountPrice=$productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction)|floatval}
								{/if}
								{$discountPrice=$discountPrice * $quantity_discount.quantity}
								{$qtyProductPrice=$productPriceWithoutReduction|floatval * $quantity_discount.quantity}
								{convertPrice price=$qtyProductPrice - $discountPrice}
							</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</section>
	{/if}

	{if isset($product) && $product->description}
		<!-- More info -->
		<section id="tab2" class="tab-pane page-product-box">
			<!-- full description -->
			<div  class="rte">{$product->description}</div>
		</section>
		<!--end  More info -->
	{/if}
	
	{if isset($features) && $features}
		<!-- Data sheet -->
		<section id="tab3" class="tab-pane page-product-box">
			<table class="table-data-sheet table-responsive">			
				{foreach from=$features item=feature}
				<tr class="{cycle values="odd,even"}">
					{if isset($feature.value)}			    
					<td>{$feature.name|escape:'html':'UTF-8'}</td>
					<td>{$feature.value|escape:'html':'UTF-8'}</td>
					{/if}
				</tr>
				{/foreach}
			</table>
		</section>
		<!--end Data sheet -->
	{/if}

	<!--HOOK_PRODUCT_TAB -->		
		{if isset($HOOK_PRODUCT_TAB_CONTENT) && $HOOK_PRODUCT_TAB_CONTENT}{$HOOK_PRODUCT_TAB_CONTENT}{/if}
	<!--end HOOK_PRODUCT_TAB -->

	<!-- description & features -->
	{if (isset($product) && $product->description) || (isset($features) && $features) || (isset($accessories) && $accessories) || (isset($HOOK_PRODUCT_TAB) && $HOOK_PRODUCT_TAB) || (isset($attachments) && $attachments) || isset($product) && $product->customizable}
			{if isset($attachments) && $attachments}
			<!--Download -->
			<section id="tab5" class="tab-pane page-product-box">
				
				{foreach from=$attachments item=attachment name=attachements}
					{if $smarty.foreach.attachements.iteration %3 == 1}<div class="row">{/if}
						<div class="col-lg-4 col-md-4 col-xs-12">
							<h4><a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")|escape:'html':'UTF-8'}">{$attachment.name|escape:'html':'UTF-8'}</a></h4>
							<p class="text-muted">{$attachment.description|escape:'html':'UTF-8'}</p>
							<a class="btn btn-default btn-block" href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")|escape:'html':'UTF-8'}">
								<i class="fa fa-download"></i>
								{l s="Download"} ({Tools::formatBytes($attachment.file_size, 2)})
							</a>
							<hr>
						</div>
					{if $smarty.foreach.attachements.iteration %3 == 0 || $smarty.foreach.attachements.last}</div>{/if}
				{/foreach}
			</section>
			<!--end Download -->
		{/if}
		{if isset($product) && $product->customizable}
			<!--Customization -->
			<section id="tab6" class="tab-pane page-product-box">
				
				<!-- Customizable products -->
				<form method="post" action="{$customizationFormTarget}" enctype="multipart/form-data" id="customizationForm" class="clearfix">
					<p class="infoCustomizable">
						{l s='After saving your customized product, remember to add it to your cart.'}
						{if $product->uploadable_files}
						<br />
						{l s='Allowed file formats are: GIF, JPG, PNG'}{/if}
					</p>
					{if $product->uploadable_files|intval}
						<div class="customizableProductsFile">
							<h5 class="product-heading-h5">{l s='Pictures'}</h5>
							<ul id="uploadable_files" class="clearfix">
								{counter start=0 assign='customizationField'}
								{foreach from=$customizationFields item='field' name='customizationFields'}
									{if $field.type == 0}
										<li class="customizationUploadLine{if $field.required} required{/if}">{assign var='key' value='pictures_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}
											{if isset($pictures.$key)}
												<div class="customizationUploadBrowse">
													<img src="{$pic_dir}{$pictures.$key}_small" alt="" />
														<a href="{$link->getProductDeletePictureLink($product, $field.id_customization_field)|escape:'html':'UTF-8'}" title="{l s='Delete'}" >
															<img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" class="customization_delete_icon" width="11" height="13" />
														</a>
												</div>
											{/if}
											<div class="customizationUploadBrowse form-group">
												<label class="customizationUploadBrowseDescription">
													{if !empty($field.name)}
														{$field.name}
													{else}
														{l s='Please select an image file from your computer'}
													{/if}
													{if $field.required}<sup>*</sup>{/if}
												</label>
												<input type="file" name="file{$field.id_customization_field}" id="img{$customizationField}" class="form-control customization_block_input {if isset($pictures.$key)}filled{/if}" />
											</div>
										</li>
										{counter}
									{/if}
								{/foreach}
							</ul>
						</div>
					{/if}
					{if $product->text_fields|intval}
						<div class="customizableProductsText">
							<h5 class="product-heading-h5">{l s='Text'}</h5>
							<ul id="text_fields">
							{counter start=0 assign='customizationField'}
							{foreach from=$customizationFields item='field' name='customizationFields'}
								{if $field.type == 1}
									<li class="customizationUploadLine{if $field.required} required{/if}">
										<label for ="textField{$customizationField}">
											{assign var='key' value='textFields_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}
											{if !empty($field.name)}
												{$field.name}
											{/if}
											{if $field.required}<sup>*</sup>{/if}
										</label>
										<textarea name="textField{$field.id_customization_field}" class="form-control customization_block_input" id="textField{$customizationField}" rows="3" cols="20">{strip}
											{if isset($textFields.$key)}
												{$textFields.$key|stripslashes}
											{/if}
										{/strip}</textarea>
									</li>
									{counter}
								{/if}
							{/foreach}
							</ul>
						</div>
					{/if}
					<p id="customizedDatas">
						<input type="hidden" name="quantityBackup" id="quantityBackup" value="" />
						<input type="hidden" name="submitCustomizedDatas" value="1" />
						<button class="button btn btn-default button button-small" name="saveCustomization">
							<span>{l s='Save'}</span>
						</button>
						<span id="ajax-loader" class="unvisible">
							<img src="{$img_ps_dir}loader.gif" alt="loader" />
						</span>
					</p>
				</form>
				<p class="clear required"><sup>*</sup> {l s='required fields'}</p>	
			</section>
			<!--end Customization -->
		{/if}
	{/if}
	{if isset($packItems) && $packItems|@count > 0}
		<section id="blockpack" class="tab-pane page-product-box">
			<h3 class="page-product-heading">{l s='Pack content'}</h3>
			{include file="$tpl_dir./product-list.tpl" products=$packItems}
		</section>
	{/if}
</div>         