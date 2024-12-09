{**
* 2013 - 2018 HiPresta
*
* MODULE Facebook Connect
*
* @author    HiPresta <suren.mikaelyan@gmail.com>
* @copyright HiPresta 2018
* @license   Addons PrestaShop license limitation
* @link      http://www.hipresta.com
*
* NOTICE OF LICENSE
*
* Don't use this module on several shops. The license provided by PrestaShop Addons
* for all its modules is valid only once for a single shop.
*}

{if $show_module}
	{if $psv >= 1.6}
		<div class="col-lg-12">
			<div class="panel clearfix">
				<div class="panel-heading"> <i class="icon-cogs"></i> {l s='Check our modules' mod='hifacebookconnect'}</div>
	{else}
		<fieldset id="fieldset_0" class="module_advertising">
			<legend>{l s='Check our modules' mod='hifacebookconnect'}</legend>
	{/if}
		{foreach from=$modules key=k item=module}
			<div class="module_info">
				<a class="addons-style-module-link" href="https://hipresta.com/module/hiprestashopapi/prestashopapi?secure_key=6db77b878f95ee7cb56d970e4f52f095&redirect=1&module_key={$k}&dashboard=1" target="_blank">
					<div class="media addons-style-module panel">
						<div class="media-body addons-style-media-body">
							<h4 class="media-heading addons-style-media-heading">{$module->display_name}</h4>
						</div>
						<div class="addons-style-theme-preview center-block">
							<img class="addons-style-img_preview-theme" src="{$module->image_link}" style="max-width: 100%">
							<p class="btn btn-default">
								{if $psv >= 1.6}
									<i class="icon-shopping-cart"></i>
								{else}
									<img src="../img/t/AdminParentOrders.gif" alt="">
								{/if}
								{$module->price}
							</p>
						</div>
					</div>
				</a>
			</div>
		{/foreach}
	{if $psv >=1.6}
			</div>
		</div>
	{else}
		</fieldset>
		<script type="text/javascript">
			$(document).ready(function() {
				var content = $('fieldset.module_advertising').clone();
				$('fieldset.module_advertising').remove();
				if ($('#column_left').find('#adminpresentation').length != 0) {
					$('#column_left').find('#adminpresentation').next().prepend(content);
				} else {
					$('#column_left').find('#blockNewVersionCheck').next().prepend(content);
				}
			});
		</script>
	{/if}
{/if}
<div class="clearfix"></div>
