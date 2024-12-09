{if $var.type == 'select'}
	<select class="sensbitinpost_var_{$var.name} form-control param" name="{$var.name}">
		{foreach $var.options.query as $o}
			{assign value=$var.options.id var='id'}
			{assign value=$var.options.name var='name'}
			<option value="{$o[$id]}"{if isset($data.options[$var.name]) && $data.options[$var.name] == $o[$id]} selected="selected"{/if}>{$o[$name]}</option>
		{/foreach}
	</select>
{elseif $var.type =='text'}
	<div class="input-group">
		{if $var.prefix}
			<div class="input-group-addon">{$var.prefix}</div>
		{/if}
		<input type="text" class="sensbitinpost_var_{$var.name} form-control param" name='{$var.name}' value="{if isset($data.options[$var.name]) && $data.options[$var.name]}{$data.options[$var.name]}{elseif isset($var.default)}{$data[$var.default]}{/if}">
		{if $var.suffix}
			<div class="input-group-addon">{$var.suffix}</div>
		{/if}
	</div>  
{elseif $var.type =='textarea'}
	<textarea style="height:auto;" class="sensbitinpost_var_{$var.name} form-control param" placeholder="{$var.label}" name='{$var.name}'>{if isset($data.options[$var.name]) && $data.options[$var.name]}{$data.options[$var.name]}{elseif isset($var.default)}{$data[$var.default]}{/if}</textarea>
{elseif $var.type == 'switch' || $var.type == 'radio'}
	<div class="input-group">
		{if isset($var.grouped)}
			<div class="input-group-addon">
				<input class="param sensbitinpost_var_{$var.name}" type='checkbox' name="{$var.name}"{if isset($data.options[$var.name]) && $data.options[$var.name]} checked="checked"{/if}/>
			</div>
			{include file="./var.tpl" var=$var.grouped}
		{else}
			<div class="checkbox">
				<label>
					<input class="param sensbitinpost_var_{$var.name}" type='checkbox' name="{$var.name}"{if isset($data.options[$var.name]) && $data.options[$var.name]} checked="checked"{/if}/>
					{*{$var.label}*}
				</label>
			</div>
		{/if}
	</div>
{/if}