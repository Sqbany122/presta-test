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


<div class="panel product-tab">
	<div class="panel-heading tab">
		<i class="icon-link"></i> {l s='Product options' mod='pdfacebookdynamicadsfeedpro'}
	</div>
	<div class="clearfix container-fluid">
	<input type="hidden" name="submitted_conf[]" value="Modulepdfacebookdynamicadsfeedpro" />
	
	<br>
	<div class="form-group col-lg-12">
		<label class="form-control-label">
			{l s='In Facebook feed' mod='pdfacebookdynamicadsfeedpro'}
			<span class="help-box" data-toggle="popover" data-content="{l s='Include this product in Facebook Dynamic Ads feed' mod='pdfacebookdynamicadsfeedpro'}"></span>
		</label>

		<div class="row">
			<div class="radio_block">
				<div class="input-group col-lg-12">
					<div class="radio">
						<label class="">
							<input  type="radio" name="in_facebook_feed" id="in_facebook_feed_on" value="1" {if $product->in_facebook_feed}checked="checked" {/if} />
							{l s='Yes' mod='pdfacebookdynamicadsfeedpro'}
						</label>
					</div>
					<div class="radio">
						<label class="">
							<input  type="radio" name="in_facebook_feed" id="in_facebook_feed_off" value="0" {if !$product->in_facebook_feed}checked="checked"{/if} />
							{l s='No' mod='pdfacebookdynamicadsfeedpro'}
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>



	<div class="form-group col-md-4">
			<label class="form-control-label" for="product_name_facebook_feed">
				{l s='Alternate product name' mod='pdfacebookdynamicadsfeedpro'}
				<span class="help-box" data-toggle="popover" data-content="{l s='Alternate product name if provided normal product name will be replaced with this one' mod='pdfacebookdynamicadsfeedpro'}"></span>
			</label>
			<div class="row">	
				<div class="input-group col-lg-12">
					
				{include file="$path_tpl/input_text_lang.tpl"
					languages=$languages
					input_name='product_name_facebook_feed'
					maxchar=128
					input_value=$product->product_name_facebook_feed}
				</div>
			</div>
	</div>




	<div class="form-group col-md-4">
			<label class="form-control-label" for="custom_label_0">
				{l s='Custom label 0' mod='pdfacebookdynamicadsfeedpro'}
				<span class="help-box" data-toggle="popover" data-content="{l s='Alternate product name if provided normal product name will be replaced with this one' mod='pdfacebookdynamicadsfeedpro'}"></span>
			</label>
			<div class="row">	
				<div class="input-group col-lg-12">
					
				{include file="$path_tpl/input_text_lang.tpl"
					languages=$languages
					input_name='custom_label_0'
					maxchar=128
					input_value=$product->custom_label_0}
				</div>
			</div>
	</div>




	<div class="form-group col-md-4">
			<label class="form-control-label" for="custom_label_1">
				{l s='Custom label 1' mod='pdfacebookdynamicadsfeedpro'}
				<span class="help-box" data-toggle="popover" data-content="{l s='Can contain additional information about the item' mod='pdfacebookdynamicadsfeedpro'}"></span>
			</label>
			<div class="row">	
				<div class="input-group col-lg-12">
				{include file="$path_tpl/input_text_lang.tpl"
					languages=$languages
					input_name='custom_label_1'
					maxchar=128
					input_value=$product->custom_label_1}
				</div>
			</div>
	</div>



	<div class="form-group col-md-4">
			<label class="form-control-label" for="custom_label_2">
				{l s='Custom label 2' mod='pdfacebookdynamicadsfeedpro'}
				<span class="help-box" data-toggle="popover" data-content="{l s='Can contain additional information about the item' mod='pdfacebookdynamicadsfeedpro'}"></span>
			</label>
			<div class="row">	
				<div class="input-group col-lg-12">
				{include file="$path_tpl/input_text_lang.tpl"
					languages=$languages
					input_name='custom_label_2'
					maxchar=128
					input_value=$product->custom_label_2}
				</div>
			</div>
	</div>


	<div class="form-group col-md-4">
			<label class="form-control-label" for="custom_label_3">
				{l s='Custom label 3' mod='pdfacebookdynamicadsfeedpro'}
				<span class="help-box" data-toggle="popover" data-content="{l s='Can contain additional information about the item' mod='pdfacebookdynamicadsfeedpro'}"></span>
			</label>
			<div class="row">	
				<div class="input-group col-lg-12">
				{include file="$path_tpl/input_text_lang.tpl"
					languages=$languages
					input_name='custom_label_3'
					maxchar=128
					input_value=$product->custom_label_3}
				</div>
			</div>
	</div>


	<div class="form-group col-md-4">
			<label class="form-control-label" for="custom_label_4">
				{l s='Custom label 4' mod='pdfacebookdynamicadsfeedpro'}
				<span class="help-box" data-toggle="popover" data-content="{l s='Can contain additional information about the item' mod='pdfacebookdynamicadsfeedpro'}"></span>
			</label>
			<div class="row">	
				<div class="input-group col-lg-12">
				{include file="$path_tpl/input_text_lang.tpl"
					languages=$languages
					input_name='custom_label_4'
					maxchar=128
					input_value=$product->custom_label_4}
				</div>
			</div>
	</div>



</div>

<!-- PD Facebook Dynamic Ads Feed Pro -->