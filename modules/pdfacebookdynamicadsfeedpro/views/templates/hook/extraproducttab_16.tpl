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
<div id="ModulepdfacebookdynamicadsfeedproProductTab" class="panel product-tab">
<h3>{l s='Product options' mod='pdfacebookdynamicadsfeedpro'}</h3>
		
	<div class="form-group">
		<input type="hidden" name="submitted_conf[]" value="Modulepdfacebookdynamicadsfeedpro" />

		<label class="control-label col-lg-2">
			{l s='In Facebook feed' mod='pdfacebookdynamicadsfeedpro'}
		</label>
		<div class="col-lg-9">
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="in_facebook_feed" id="in_facebook_feed_on" value="1" {if $product->in_facebook_feed}checked="checked" {/if} />
				<label for="in_facebook_feed_on" class="radioCheck">
					{l s='Yes' mod='pdfacebookdynamicadsfeedpro'}
				</label>
				<input type="radio" name="in_facebook_feed" id="in_facebook_feed_off" value="0" {if !$product->in_facebook_feed}checked="checked"{/if} />
				<label for="in_facebook_feed_off" class="radioCheck">
					{l s='No' mod='pdfacebookdynamicadsfeedpro'}
				</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Include this product in Facebook Dynamic Ads feed' mod='pdfacebookdynamicadsfeedpro'}
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" for="product_name_facebook_feed">
			{l s='Alternate product name'  mod='pdfacebookdynamicadsfeedpro'}
		</label>
		<div class="col-lg-5">

			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='product_name_facebook_feed'
				maxchar=128
				input_value=$product->product_name_facebook_feed}
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Alternate product name if provided normal product name will be replaced with this one' mod='pdfacebookdynamicadsfeedpro'}
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" for="custom_label_0">
			{l s='Custom label 0'  mod='pdfacebookdynamicadsfeedpro'}
		</label>
		<div class="col-lg-5">

			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='custom_label_0'
				maxchar=100
				input_value=$product->custom_label_0}
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Can contain additional information about the item' mod='pdfacebookdynamicadsfeedpro'}
			</div>
		</div>
	</div>


	<div class="form-group">
		<label class="control-label col-lg-3" for="custom_label_1">
			{l s='Custom label 1'  mod='pdfacebookdynamicadsfeedpro'}
		</label>
		<div class="col-lg-5">

			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='custom_label_1'
				maxchar=100
				input_value=$product->custom_label_1}
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Can contain additional information about the item' mod='pdfacebookdynamicadsfeedpro'}
			</div>
		</div>
	</div>


	<div class="form-group">
		<label class="control-label col-lg-3" for="custom_label_2">
			{l s='Custom label 2'  mod='pdfacebookdynamicadsfeedpro'}
		</label>
		<div class="col-lg-5">

			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='custom_label_2'
				maxchar=100
				input_value=$product->custom_label_2}
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Can contain additional information about the item' mod='pdfacebookdynamicadsfeedpro'}
			</div>
		</div>
	</div>



		<div class="form-group">
		<label class="control-label col-lg-3" for="custom_label_3">
			{l s='Custom label 3'  mod='pdfacebookdynamicadsfeedpro'}
		</label>
		<div class="col-lg-5">

			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='custom_label_3'
				maxchar=100
				input_value=$product->custom_label_3}
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Can contain additional information about the item' mod='pdfacebookdynamicadsfeedpro'}
			</div>
		</div>
	</div>


		<div class="form-group">
		<label class="control-label col-lg-3" for="custom_label_4">
			{l s='Custom label 4'  mod='pdfacebookdynamicadsfeedpro'}
		</label>
		<div class="col-lg-5">

			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='custom_label_4'
				maxchar=100
				input_value=$product->custom_label_4}
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Can contain additional information about the item' mod='pdfacebookdynamicadsfeedpro'}
			</div>
		</div>
	</div>



	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'  mod='pdfacebookdynamicadsfeedpro'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='pdfacebookdynamicadsfeedpro'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay' mod='pdfacebookdynamicadsfeedpro'}</button>
	</div>

</div>

<script type="text/javascript">
	if (tabs_manager.allow_hide_other_languages) {
		hideOtherLanguage({$default_form_language|escape:'htmlall':'UTF-8'});
	}
</script>

<!-- PD Facebook Dynamic Ads Feed Pro -->