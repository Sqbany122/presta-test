{if $var.type == 'select'}
	<select class="sensbitdpd_var_{$var.name} form-control param" placeholder="{$var.label}" name="{$var.name}">
		{foreach $var.options.query as $o}
			{assign value=$var.options.id var='id'}
			{assign value=$var.options.name var='name'}
			<option value="{$o[$id]}"{if $data.options[$var.name] == $o[$id]} selected="selected"{/if}>{$o[$name]}</option>
		{/foreach}
	</select>
{elseif $var.type == 'radio'}
	
	{if isset($var.grouped)}
		<div class="input-group">
			<div class="input-group-addon">
				<input class="param sensbitdpd_var_{$var.name}" placeholder="{$var.label}" type='checkbox' name="{$var.name}"{if $data.options[$var.name]} checked="checked"{/if}/>
			</div>
			{foreach $var.grouped as $v}
				{include file="./var.tpl" var=$v is_grouped=1}
			{/foreach}
		</div>
	{else}
		<select class="sensbitdpd_var_{$var.name} form-control param" placeholder="{$var.label}" name="{$var.name}">
			{foreach $var.values as $v}
				{assign value=$v.value var='id'}
				{assign value=$v.label var='name'}
				<option value="{$id}"{if $data.options[$var.name] == $id} selected="selected"{/if}>{$name}</option>
			{/foreach}
		</select>
	{/if}
	
{elseif $var.type =='text'}
	{if !isset($is_grouped) || !$is_grouped}
		<div class="input-group">
		{/if}
		{if $var.prefix}
			<div class="input-group-addon">{$var.prefix}</div>
		{/if}
		<input type="text" class="sensbitdpd_var_{$var.name} form-control param" placeholder="{$var.label}" name='{$var.name}' value="{if $data.options[$var.name]}{$data.options[$var.name]}{elseif isset($var.default)}{$data[$var.default]}{/if}">
		{if $var.suffix}
			<div class="input-group-addon">{$var.suffix}</div>
		{/if}
		{if !isset($is_grouped) || !$is_grouped}
		</div>
	{/if}
{elseif $var.type == 'switch'}
	<div class="input-group">
		{if isset($var.grouped)}
			<div class="input-group-addon">
				<input class="param sensbitdpd_var_{$var.name}" placeholder="{$var.label}" type='checkbox' name="{$var.name}"{if $data.options[$var.name]} checked="checked"{/if}/>
			</div>
			{foreach $var.grouped as $v}
				{include file="./var.tpl" var=$v is_grouped=1}
			{/foreach}
		{else}
			<div class="checkbox">
				<label>
					<input class="param sensbitdpd_var_{$var.name}" placeholder="{$var.label}" type='checkbox' name="{$var.name}"{if $data.options[$var.name]} checked="checked"{/if}/>
					{*{$var.label}*}
				</label>
			</div>
		{/if}
	</div>
{/if}