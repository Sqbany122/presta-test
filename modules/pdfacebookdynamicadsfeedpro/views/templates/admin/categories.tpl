{*
* 2012-2016 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Facebook Dynamic Ads Feed Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2016 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD Facebook Dynamic Ads Feed Pro - PrestaShop 1.5.x and 1.6.x Module
* @version   1.0.1
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      27-11-2016
*}

<div class="panel form-horizontal">
<form action="{$post_url|escape:'html':'UTF-8'}" method="post">
	<h3 class="tab"> <i class="icon-info"></i> {l s='Category mapping (shop category to Google category)' mod='pdfacebookdynamicadsfeedpro'}</h3>
	<div class="form-group">
		<div class="form-group">
		<input type="hidden" name="id_pdfacebookdynamicadsfeedpro_taxonomy" id="id_pdfacebookdynamicadsfeedpro_taxonomy" value="{$id_pdfacebookdynamicadsfeedpro_taxonomy|escape:'html':'UTF-8'}">
		<input type="hidden" name="taxonomy_lang" id="taxonomy_lang" value="{$taxonomy_lang|escape:'html':'UTF-8'}">

		<div class="table-responsive-row clearfix">
			<table class="table product">
					<thead>
						<tr class="nodrag nodrop">
							<th class="">
								<span class="title_box"></span>
							</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$categories item="cat"}
							<tr class="{cycle values='odd,even'}">
								<td>
									<span style="">{l s='Shop category' mod='pdfacebookdynamicadsfeedpro'}: </span> <span style="color:#111">{$cat.path|escape:'htmlall':'UTF-8'}</span>
						
		
								</td>
							</tr>

							<tr class="{cycle values='odd,even'}">
								<td>
									<input style="width:100%" class="autocomplete_pdgmc" type="text" name="catsmappingarr[{$cat.id_category|escape:'html':'UTF-8'}]" placeholder="{l s='Start typing searched name' mod='pdfacebookdynamicadsfeedpro'}" id="shopcatid{$cat.id_category|escape:'htmlall':'UTF-8'}" value="{$cat.txt_taxonomy|escape:'html':'UTF-8'}" />
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="panel-footer">
		<button class="btn btn-default pull-right" name="submitSaveCategoriesMapping" id="submitSaveCategoriesMapping_save_btn" value="1" type="submit">
				<i class="process-icon-save"></i> {l s='Save' mod='pdfacebookdynamicadsfeedpro'}
		</button>
		<a onclick="window.history.back();" class="btn btn-default" href="index.php?controller=AdminFacebookDynamicAdsFeedProTaxonomy&amp;token={$token|escape:'htmlall':'UTF-8'}">
		<i class="process-icon-cancel"></i> {l s='Cancel' mod='pdfacebookdynamicadsfeedpro'}
		</a>
	</div>
</form>
</div>


<script type="text/javascript">
{literal}
/* function autocomplete */
$('input.autocomplete_pdgmc').each(function(index, element) {
	var query = $(element).attr("id");
	$(element).autocomplete('{/literal}{html_entity_decode($ajax_url|escape:'htmlall':'UTF-8')}{literal}&id_pdfacebookdynamicadsfeedpro_taxonomy={/literal}{$id_pdfacebookdynamicadsfeedpro_taxonomy|escape:'htmlall':'UTF-8'}{literal}&searchTaxonomyCategory=1&query='+query, {
		minChars: 3,
		autoFill: false,
		max:50,
		matchContains: true,
		mustMatch:false,
		scroll:true,
		cacheLength:0,
		formatItem: function(item) {
			return item[0];
		}
	});
});
{/literal}
</script>