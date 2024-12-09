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
			{l s='Successfully' mod='leobootstrapmenu'}
		</div>
	</div>
{/if}
<div class="col-lg-12"> 
	<div class="" style="float: right">
		<div class="pull-right">
			<a href="{$live_editor_url}" class="btn btn-danger">{l s='Live Edit Tools' mod='leobootstrapmenu'}</a>
               {l s='To Make Rich Content For Megamenu' mod='leobootstrapmenu'}
		</div>
	</div>
</div>

<ul class="nav nav-tabs clearfix">
	<li class="active">
		<a href="#megamenu" data-toggle="tab">{l s='Megamenu' mod='leobootstrapmenu'}</a>
	</li>
</ul>

 
<div class="tab-content clearfix">
	<div class="tab-pane active" id="megamenu">
	
		<div class="col-md-4">
			<div class="panel panel-default">
				<h3 class="panel-title">{l s='Tree Megamenu Management' mod='leobootstrapmenu'}</h3>
				<div class="panel-content">{l s='To sort orders or update parent-child, you drap and drop expected menu, then click to Update button to Save' mod='leobootstrapmenu'}
					<hr>
					<p>
						<input type="button" value="{l s='New Menu Item' mod='leobootstrapmenu'}" id="addcategory" data-loading-text="{l s='Processing ...' mod='leobootstrapmenu'}" class="btn btn-danger" name="addcategory">
						<a href="{$admin_leotemcp_link}" class="leo-modal-action btn btn-modeal btn-success btn-info">{l s='List Widget' mod='leobootstrapmenu'}</a>
					</p>
					<hr>
					<p>
						<input type="button" value="{l s='Update' mod='leobootstrapmenu'}" id="show_cavas" data-loading-text="{l s='Processing ...' mod='leobootstrapmenu'}" class="btn btn-info" >
					</p>
						<label>{l s='Show Cavas' mod='leobootstrapmenu'}</label>
						<select name="show_cavas" class="show_cavas">
							<option value="1" {if isset($show_cavas) && $show_cavas == 1}selected{else}null{/if}>{l s='Yes' mod='leobootstrapmenu'}</option>
							<option value="0" {if isset($show_cavas) && $show_cavas == 0}selected{else}null{/if}>{l s='No' mod='leobootstrapmenu'}</option>
						</select>
					<hr>
					<p>
						<input type="button" value="{l s='Update Positions' mod='leobootstrapmenu'}" id="serialize" data-loading-text="{l s='Processing ...' mod='leobootstrapmenu'}" class="btn btn-danger" name="serialize">
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