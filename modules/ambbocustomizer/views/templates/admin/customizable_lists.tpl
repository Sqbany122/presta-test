 {*
 *    ambBOcustomizer Module : customize the prestashop back-office
 *
 *    @module    BO Customizer (AmbBoCustomizer)
 *    @file      views/templates/admin/customizable_lists.tpl
 *    @subject   Shows all lists that can be customized
 *    @author    Ambris Informatique
 *    @copyright Copyright (c) 2013-2015 Ambris Informatique SARL
 *    @license   Commercial license
 *
 *    Support by mail: support@ambris.com
 *}

{if $compat}<script type="text/javascript">var update_success_msg="{$success_msg|escape:'htmlall':'UTF-8'}"</script>{/if}

<input type="hidden" value="{$token|escape:'htmlall':'UTF-8'}" id="token" />

	 {if !$compat}
<div class="panel col-xs-12">
    <div class="panel-heading">
    {else}
    <div class="toolbarBox toolbarHead"><div class="pageTitle"><h3>
    {/if}
        {l s='Customizable lists' mod='ambbocustomizer'}
    {if !$compat}
    </div>
    {else}
    </h3>
    </div>
    </div>
    {/if}

	<table class="table" style="width:100%">
		<thead>
			<tr>
				<th class="col-xs-6">
					{l s='Menu > List' mod='ambbocustomizer'}
				</th>

				<th class="col-xs-2 text-center">
					{l s='Active' mod='ambbocustomizer'}
				</th>
				<th class="col-xs-3 text-right">
					&nbsp;
				</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$customizable_lists key=category item=lists}
				{foreach $lists as $list}
					<tr>
						<td {if $compat}style="height:2.3em"{/if}>
							<a href="{$list['show_link']|escape:'htmlall':'UTF-8'}">{l s=$category mod='ambbocustomizer'} > {$list['amb_data']->getTabName()|escape:'quotes':'UTF-8'}</a>
						</td>

						<td class="text-center">
						    <span class="switch prestashop-switch">
			                    <input type="radio" class="activate_amblist" name="{$list['amb_data']->getControllerName()|escape:'htmlall':'UTF-8'}" data-controller_name="{$list['amb_data']->getControllerName()|escape:'htmlall':'UTF-8'}" id="{$list['amb_data']->getControllerName()|escape:'htmlall':'UTF-8'}_on" value="1" {if $list['amb_data']->isActive()}checked="checked"{/if}><label for="{$list['amb_data']->getControllerName()|escape:'htmlall':'UTF-8'}_on" class="radioCheck">{l s='Yes' mod='ambbocustomizer'}</label><input type="radio" class="activate_amblist" name="{$list['amb_data']->getControllerName()|escape:'htmlall':'UTF-8'}" id="{$list['amb_data']->getControllerName()|escape:'htmlall':'UTF-8'}_off" value="0" data-controller_name="{$list['amb_data']->getControllerName()|escape:'htmlall':'UTF-8'}"  {if !$list['amb_data']->isActive()}checked="checked"{/if}><label for="{$list['amb_data']->getControllerName()|escape:'htmlall':'UTF-8'}_off" class="radioCheck">{l s='No' mod='ambbocustomizer'}</label>
			                    <a class="slide-button btn"></a>
			                </span>
						</td>
						<td class="text-right">
							<a href="{$list['edit_link']|escape:'htmlall':'UTF-8'}" class="edit btn btn-default">
								<i class="icon-pencil"></i> {l s='Manage fields' mod='ambbocustomizer'}
							</a>
						</td>
					</tr>
				{/foreach}
			{/foreach}
		</tbody>
	</table>
{if !$compat}
</div>
{/if}