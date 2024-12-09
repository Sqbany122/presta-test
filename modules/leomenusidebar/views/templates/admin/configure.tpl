{*
 *  Leo Theme for Prestashop 1.6.x
 *
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
*}
{$html}
{if $successful == 1}
	<div class="bootstrap">
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Successfully' mod='leomenusidebar'}
		</div>
	</div>
{/if}
<div class="col-lg-12"> 
	<div class="" style="float: right">
		<div class="pull-right">
			<a href="{$live_editor_url}" class="btn btn-danger">{l s='Live Edit Tools' mod='leomenusidebar'}</a>
               {l s='To Make Rich Content For Megamenu' mod='leomenusidebar'}
		</div>
	</div>
</div>

<ul class="nav nav-tabs clearfix">
	<li class="active">
		<a href="#megamenu" data-toggle="tab">{l s='Megamenu' mod='leomenusidebar'}</a>
	</li>
</ul>

 
<div class="tab-content clearfix">
	<div class="tab-pane active" id="megamenu">
	
		<div class="col-md-4">
			<div class="panel panel-default">
				<h3 class="panel-title">{l s='Tree Megamenu Management' mod='leomenusidebar'}</h3>
				<div class="panel-content">{l s='To sort orders or update parent-child, you drap and drop expected menu, then click to Update button to Save' mod='leomenusidebar'}
					<hr>
					<p>
						<input type="button" value="{l s='New Menu Item' mod='leomenusidebar'}" id="addcategory" data-loading-text="{l s='Processing ...' mod='leomenusidebar'}" class="btn btn-danger" name="addcategory">
						<a href="{$admin_leotemcp_link}" class="leo-modal-action btn btn-modeal btn-success btn-info">{l s='List Widget' mod='leomenusidebar'}</a>
					</p>
					<hr>
					<p>
						<input type="button" value="{l s='Update Type Sub' mod='leomenusidebar'}" id="typesub" data-loading-text="{l s='Processing ...' mod='leomenusidebar'}" class="btn btn-info" >
					</p>
						<label>{l s='Type Sub' mod='leomenusidebar'}</label>
						<select name="typesub" class="type_sub">							
							<option value="auto" {if isset($typesub) && $typesub == 'auto'}selected="selected"{else}null{/if}>{l s='Auto' mod='leomenusidebar'}</option>
							<option value="right" {if isset($typesub) && $typesub == 'right'}selected="selected"{else}null{/if}>{l s='Right' mod='leomenusidebar'}</option>
							<option value="left" {if isset($typesub) && $typesub == 'left'}selected="selected"{else}null{/if}>{l s='Left' mod='leomenusidebar'}</option>
						</select>
					<hr>
					<p>
						<input type="button" value="{l s='Update Positions' mod='leomenusidebar'}" id="serialize" data-loading-text="{l s='Processing ...' mod='leomenusidebar'}" class="btn btn-danger" name="serialize">
					</p>
					<hr>
					{$tree}
				</div>
			</div>
		</div>
		<div class="col-md-8">
			{$helper_form}
		</div>
		<script type="text/javascript">
			var addnew ="{$addnew}"; 
			var action="{$action}";
			$("#content").PavMegaMenuList({
				action:action,
				addnew:addnew
			});
		</script>
	</div>
</div>
<script>
	$('#myTab a[href="#profile"]').tab('show');
</script>