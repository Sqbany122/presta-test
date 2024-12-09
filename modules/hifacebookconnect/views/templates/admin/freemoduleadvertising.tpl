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


{if $psv >= 1.6}
	<div class="col-lg-12">
		<div class="panel clearfix">
			<div class="panel-heading"> <i class="icon-cogs"></i> {l s='Check our free modules' mod='hifacebookconnect'}</div>
{else}
	<fieldset id="fieldset_0" class="module_advertising">
		<legend>{l s='Check our free modules' mod='hifacebookconnect'}</legend>
{/if}
		{if $show_module}
			{foreach from=$modules key=k item=module}
				<div class="module_info col-lg-6 col-md-6 col-sm-6">
					<a class="addons-style-module-link" href="https://hipresta.com/module/hiprestashopapi/prestashopapi?secure_key=6db77b878f95ee7cb56d970e4f52f095&redirect=1&module_key={$k}"" target="_blank">
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
		{/if}
{if $psv >=1.6}
		</div>
	</div>
{else}
	</fieldset>
{/if}
<div class="clearfix"></div>
