{*
* 2013-2016 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Facebook Dynamic Ads Feed Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2013-2016 Patryk Marek PrestaDev.pl
* @version   Release: 2.1.2
*}

<!-- PD Facebook Dynamic Ads Feed Pro -->
<fieldset id="ModulepdfacebookdynamicadsfeedproProductTab">
<h4>{l s='Product options' mod='pdfacebookdynamicadsfeedpro'}</h4>
<div class="separation"></div>

		<input type="hidden" name="submitted_conf[]" value="ModulePdPriceComparePro" />
		
		<label>
			{l s='In Facebook feed' mod='pdfacebookdynamicadsfeedpro'}
		</label>
		<div class="margin-form">
				<input type="radio" name="in_facebook_feed" id="in_facebook_feed_on" value="1" {if $product->in_facebook_feed}checked="checked" {/if} />
				<label for="in_facebook_feed_on" class="t">
					{l s='Yes' mod='pdfacebookdynamicadsfeedpro'}
				</label>
				<input type="radio" name="in_facebook_feed" id="in_facebook_feed_off" value="0" {if !$product->in_facebook_feed}checked="checked"{/if} />
				<label for="in_facebook_feed_off" class="t">
					{l s='No' mod='pdfacebookdynamicadsfeedpro'}
				</label>
				<p class="preference_description">
					{l s='Include this product in Facebook Dynamic Ads feed' mod='pdfacebookdynamicadsfeedpro'}
				</p>
		</div>
		<div class="clear"></div>


		<label for="product_name_facebook_feed">
			{l s='Alternate product name' mod='pdfacebookdynamicadsfeedpro'}
		</label>
		<div class="margin-form">
			{include file="controllers/products/input_text_lang.tpl"
                languages=$languages
                input_name='product_name_facebook_feed'
                maxchar=128
                input_value=$product->product_name_facebook_feed}
		
			<p class="preference_description">
				{l s='Alternate product name if provided normal product name will be replaced with this one' mod='pdfacebookdynamicadsfeedpro'}
			</p>
		</div>
		<div class="clear"></div>


		<label for="custom_label_0">
			{l s='Custom label 0' mod='pdfacebookdynamicadsfeedpro'}
		</label>
		<div class="margin-form">
			{include file="controllers/products/input_text_lang.tpl"
                languages=$languages
                input_name='custom_label_0'
                maxchar=100
                input_value=$product->custom_label_0}
		
			<p class="preference_description">
				{l s='Can contain additional information about the item' mod='pdfacebookdynamicadsfeedpro'}
			</p>
		</div>
		<div class="clear"></div>

		<label for="custom_label_1">
			{l s='Custom label 1' mod='pdfacebookdynamicadsfeedpro'}
		</label>
		<div class="margin-form">
			{include file="controllers/products/input_text_lang.tpl"
                languages=$languages
                input_name='custom_label_1'
                maxchar=100
                input_value=$product->custom_label_1}
		
			<p class="preference_description">
				{l s='Can contain additional information about the item' mod='pdfacebookdynamicadsfeedpro'}
			</p>
		</div>
		<div class="clear"></div>


		<label for="custom_label_2">
			{l s='Custom label 2' mod='pdfacebookdynamicadsfeedpro'}
		</label>
		<div class="margin-form">
			{include file="controllers/products/input_text_lang.tpl"
                languages=$languages
                input_name='custom_label_2'
                maxchar=100
                input_value=$product->custom_label_2}
		
			<p class="preference_description">
				{l s='Can contain additional information about the item' mod='pdfacebookdynamicadsfeedpro'}
			</p>
		</div>
		<div class="clear"></div>


		<label for="custom_label_3">
			{l s='Custom label 3' mod='pdfacebookdynamicadsfeedpro'}
		</label>
		<div class="margin-form">
			{include file="controllers/products/input_text_lang.tpl"
                languages=$languages
                input_name='custom_label_3'
                maxchar=100
                input_value=$product->custom_label_3}
		
			<p class="preference_description">
				{l s='Can contain additional information about the item' mod='pdfacebookdynamicadsfeedpro'}
			</p>
		</div>
		<div class="clear"></div>


		<label for="custom_label_4">
			{l s='Custom label 4' mod='pdfacebookdynamicadsfeedpro'}
		</label>
		<div class="margin-form">
			{include file="controllers/products/input_text_lang.tpl"
                languages=$languages
                input_name='custom_label_4'
                maxchar=100
                input_value=$product->custom_label_4}
		
			<p class="preference_description">
				{l s='Can contain additional information about the item' mod='pdfacebookdynamicadsfeedpro'}
			</p>
		</div>
		<div class="clear"></div>

</fieldset>
<!-- PD Facebook Dynamic Ads Feed Pro -->