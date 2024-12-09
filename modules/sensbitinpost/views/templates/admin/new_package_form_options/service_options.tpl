<div class="row form-horizontal">
	{foreach $data.service_options as $group => $vars}
		<div class='col-xs-4'>
			<h3>{$group}</h3>
			{foreach $vars as $var}
				<div class="form-group">
					<label for="sensbitinpost_var_{$var.name}" class="col-sm-4 control-label">{if $var.type == 'switch' && !isset($var.grouped)}{$var.label}{else}{$var.label}{/if}</label>
					<div class="col-sm-{if $var.type == 'switch' && !isset($var.grouped)}8{else}4{/if}">
						{include file="./var.tpl" var=$var}
					</div>
				</div>
			{/foreach}
		</div>
	{/foreach}
</div>