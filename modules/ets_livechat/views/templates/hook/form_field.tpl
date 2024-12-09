{*
* 2007-2019 ETS-Soft
*
* NOTICE OF LICENSE
*
* This file is not open source! Each license that you purchased is only available for 1 wesite only.
* If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
* You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
* 
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2019 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
<div id="ticket_form_field_{if $id_field}{$id_field|intval}{else}0{/if}" class="form-group ticket-form-field{if $id_field} field_saved{/if}">
<div class="position_field"><span class="position">{$position|intval}</span></div>
{if !$id_field || $field_class->id_form!=1}
<div class="delete-form-field lc_delete" data-id="{if $id_field}{$id_field|intval}{/if}">{l s='Delete' mod='ets_livechat'}</div>
{/if}
{if $id_field}
    <div class="field_lable">{$field_class->label|escape:'html':'UTF-8'} - {$field_class->type}</div>
{else}
    <div class="field_lable">{l s ='New field' mod='ets_livechat'}</div>
{/if}
<div class="field-toggle{if !$id_field} show_filed{/if}">an/hien</div>
<div class="filed-body" {if $id_field}style="display:none;"{/if}>
    <input type="hidden" value="{$position|intval}" name="ets_fields_position[{$position|intval}]" class="ets_fields_position" />
    {if $fields}
        <div class="form-wrapper">
        {foreach $fields as $input}
            <div class="form-group{if isset($input.form_group_class)} {$input.form_group_class|escape:'html':'UTF-8'}{/if}{if $input.type == 'hidden'} hide{/if}" >
                {if $input.type == 'hidden'}
					<input type="hidden" name="ets_fields_{$input.name|escape:'html':'UTF-8'}[{$position|intval}]" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
				{else}
                    {if isset($input.label)}
						<label class="control-label col-lg-3{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
							{if isset($input.hint)}
    							<span class="lc_tooltip">
                                    {if is_array($input.hint)}
    									{foreach $input.hint as $hint}
    										{if is_array($hint)}
    											{$hint.text|escape:'html':'UTF-8'}
    										{else}
    											{$hint|escape:'html':'UTF-8'}
    										{/if}
    									{/foreach}
    								{else}
    									{$input.hint|escape:'html':'UTF-8'}
    								{/if}
							{/if}
							{$input.label|escape:'html':'UTF-8'}
                            {if isset($input.hint)}
							     </span>
							{/if}
						</label>
					{/if}
                    <div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}9{/if}{if !isset($input.label)} col-lg-offset-3{/if}">
                        {if $input.type == 'text' || $input.type == 'tags'}
                            {if isset($input.lang) AND $input.lang}
    							{if $languages|count > 1}
    							<div class="form-group">
    							{/if}
    							{foreach $languages as $language}
    								{assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
    								{if $languages|count > 1}
    								<div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
    									<div class="col-lg-9">
    								{/if}
    										{if $input.type == 'tags'}
    											{literal}
    												<script type="text/javascript">
    													$().ready(function () {
    														var input_id = '{/literal}{if isset($input.id)}{$input.id|intval}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}{literal}';
    														$('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' js=1}{literal}'});
    														$({/literal}'#{$table|escape:'html':'UTF-8'}{literal}_form').submit( function() {
    															$(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
    														});
    													});
    												</script>
    											{/literal}
    										{/if}
    										{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
    										<div class="input-group{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}">
    										{/if}
    										{if isset($input.maxchar) && $input.maxchar}
    										<span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}_counter" class="input-group-addon">
    											<span class="text-count-down">{$input.maxchar|intval}</span>
    										</span>
    										{/if}
    										{if isset($input.prefix)}
    											<span class="input-group-addon">
    											  {$input.prefix|escape:'html':'UTF-8'}
    											</span>
    											{/if}
    										<input type="text"
    											id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}"
    											name="ets_fields_{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}[{$position|intval}]"
    											class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
    											value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
    											onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
    											{if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
    											{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
    											{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
    											{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
    											{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
    											{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
    											{if isset($input.required) && $input.required} required="required" {/if}
    											{if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if} />
    											{if isset($input.suffix)}
    											<span class="input-group-addon">
    											  {$input.suffix|escape:'html':'UTF-8'}
    											</span>
    											{/if}
    										{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
    										</div>
    										{/if}
    								{if $languages|count > 1}
    									</div>
    									<div class="col-lg-2">
    										<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
    											{$language.iso_code|escape:'html':'UTF-8'}
    											<i class="icon-caret-down"></i>
    										</button>
    										<ul class="dropdown-menu">
    											{foreach from=$languages item=language}
    											<li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
    											{/foreach}
    										</ul>
    									</div>
    								</div>
    								{/if}
    							{/foreach}
    							{if isset($input.maxchar) && $input.maxchar}
    							<script type="text/javascript">
    							$(document).ready(function(){
    							{foreach from=$languages item=language}
    								countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}_counter"));
    							{/foreach}
    							});
    							</script>
    							{/if}
    							{if $languages|count > 1}
    							</div>
    							{/if}
    							{else}
    								{if $input.type == 'tags'}
    									{literal}
    									<script type="text/javascript">
    										$().ready(function () {
    											var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}{literal}';
    											$('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag'}{literal}'});
    											$({/literal}'#{$table|escape:'html':'UTF-8'}{literal}_form').submit( function() {
    												$(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
    											});
    										});
    									</script>
    									{/literal}
    								{/if}
    								{assign var='value_text' value=$fields_value[$input.name]}
    								{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
    								<div class="input-group{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}">
    								{/if}
    								{if isset($input.maxchar) && $input.maxchar}
    								<span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$input.maxchar|intval}</span></span>
    								{/if}
    								{if isset($input.prefix)}
    								<span class="input-group-addon">
    								  {$input.prefix|escape:'html':'UTF-8'}
    								</span>
    								{/if}
    								<input type="text"
    									name="ets_fields_{$input.name|escape:'html':'UTF-8'}[{$position|intval}]"
    									id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
    									value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
    									class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
    									{if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
    									{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
    									{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
    									{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
    									{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
    									{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
    									{if isset($input.required) && $input.required } required="required" {/if}
    									{if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if}
    									/>
    								{if isset($input.suffix)}
    								<span class="input-group-addon">
    								  {$input.suffix|escape:'html':'UTF-8'}
    								</span>
    								{/if}
    
    								{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
    								</div>
    								{/if}
    								{if isset($input.maxchar) && $input.maxchar}
    								<script type="text/javascript">
    								$(document).ready(function(){
    									countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter"));
    								});
    								</script>
    								{/if}
    							{/if}
                                {elseif $input.type == 'select'}
    								{if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
    									{$input.empty_message|escape:'html':'UTF-8'}
    									{$input.required = false}
    									{$input.desc = null}
    								{else}
    									<select name="ets_fields_{$input.name|escape:'html':'utf-8'}[{$position|intval}]"
    											class="{if isset($input.class)}{$input.class|escape:'html':'utf-8'}{/if} fixed-width-xl"
    											id="{if isset($input.id)}{$input.id|escape:'html':'utf-8'}{else}{$input.name|escape:'html':'utf-8'}{/if}"
    											{if isset($input.multiple) && $input.multiple} multiple="multiple"{/if}
    											{if isset($input.size)} size="{$input.size|escape:'html':'utf-8'}"{/if}
    											{if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'utf-8'}"{/if}
    											{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}>
    										{if isset($input.options.default)}
    											<option value="{$input.options.default.value|escape:'html':'utf-8'}">{$input.options.default.label|escape:'html':'utf-8'}</option>
    										{/if}
    										{if isset($input.options.optiongroup)}
    											{foreach $input.options.optiongroup.query AS $optiongroup}
    												<optgroup label="{$optiongroup[$input.options.optiongroup.label]|escape:'html':'UTF-8'}">
    													{foreach $optiongroup[$input.options.options.query] as $option}
    														<option value="{$option[$input.options.options.id]|escape:'html':'UTF-8'}"
    															{if isset($input.multiple)}
    																{foreach $fields_value[$input.name] as $field_value}
    																	{if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
    																{/foreach}
    															{else}
    																{if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
    															{/if}
    														>{$option[$input.options.options.name]|escape:'html':'UTF-8'}</option>
    													{/foreach}
    												</optgroup>
    											{/foreach}
    										{else}
    											{foreach $input.options.query AS $option}
    												{if is_object($option)}
    													<option value="{$option->$input.options.id|escape:'html':'UTF-8'}"
    														{if isset($input.multiple)}
    															{foreach $fields_value[$input.name] as $field_value}
    																{if $field_value == $option->$input.options.id}
    																	selected="selected"
    																{/if}
    															{/foreach}
    														{else}
    															{if $fields_value[$input.name] == $option->$input.options.id}
    																selected="selected"
    															{/if}
    														{/if}
    													>{$option->$input.options.name|escape:'html':'UTF-8'}</option>
    												{elseif $option == "-"}
    													<option value="">-</option>
    												{else}
    													<option value="{$option[$input.options.id]|escape:'html':'UTF-8'}"
    														{if isset($input.multiple)}
    															{foreach $fields_value[$input.name] as $field_value}
    																{if $field_value == $option[$input.options.id]}
    																	selected="selected"
    																{/if}
    															{/foreach}
    														{else}
    															{if $fields_value[$input.name] == $option[$input.options.id]}
    																selected="selected"
    															{/if}
    														{/if}
    													>{$option[$input.options.name]|escape:'html':'UTF-8'}</option>
    
    												{/if}
    											{/foreach}
    										{/if}
    									</select>
								{/if}
                            {elseif $input.type == 'switch'}
									<span class="switch prestashop-switch fixed-width-lg">
										{foreach $input.values as $value}
										<input type="radio" name="ets_fields_{$input.name|escape:'html':'UTF-8'}[{$position|intval}]"{if $value.value == 1} id="{$input.name|escape:'html':'UTF-8'}_{$position|intval}_on"{else} id="{$input.name|escape:'html':'UTF-8'}_{$position|intval}_off"{/if} value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if (isset($input.disabled) && $input.disabled) or (isset($value.disabled) && $value.disabled)} disabled="disabled"{/if}/>
										{strip}
										<label {if $value.value == 1} for="{$input.name|escape:'html':'UTF-8'}_{$position|intval}_on"{else} for="{$input.name|escape:'html':'UTF-8'}_{$position|intval}_off"{/if}>
											{if $value.value == 1}
												{l s='Yes' d='Admin.Global'}
											{else}
												{l s='No' d='Admin.Global'}
											{/if}
										</label>
										{/strip}
										{/foreach}
										<a class="slide-button btn"></a>
									</span>
				            {elseif $input.type == 'textarea'}
									{if isset($input.maxchar) && $input.maxchar}<div class="input-group">{/if}
									{assign var=use_textarea_autosize value=true}
									{if isset($input.lang) AND $input.lang}
										{foreach $languages as $language}
											{if $languages|count > 1}
											<div class="form-group translatable-field lang-{$language.id_lang|intval}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
												<div class="col-lg-9">
											{/if}
													{if isset($input.maxchar) && $input.maxchar}
														<span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}_counter" class="input-group-addon">
															<span class="text-count-down">{$input.maxchar|intval}</span>
														</span>
													{/if}
													<textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="ets_fields_{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}[{$position|intval}]" id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_{$language.id_lang|intval}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}</textarea>
											{if $languages|count > 1}
												</div>
												<div class="col-lg-2">
													<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
														{$language.iso_code|escape:'html':'UTF-8'}
														<span class="caret"></span>
													</button>
													<ul class="dropdown-menu">
														{foreach from=$languages item=language}
														<li>
															<a href="javascript:hideOtherLanguage({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a>
														</li>
														{/foreach}
													</ul>
												</div>
											</div>
											{/if}
										{/foreach}
										{if isset($input.maxchar) && $input.maxchar}
											<script type="text/javascript">
											$(document).ready(function(){
											{foreach from=$languages item=language}
												countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}_counter"));
											{/foreach}
											});
											</script>
										{/if}
									{else}
										{if isset($input.maxchar) && $input.maxchar}
											<span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}_counter" class="input-group-addon">
												<span class="text-count-down">{$input.maxchar|intval}</span>
											</span>
										{/if}
										<textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="ets_fields_{$input.name|escape:'html':'UTF-8'}[{$position|intval}]" id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}" {if isset($input.cols)}cols="{$input.cols|escape:'html':'UTF-8'}"{/if} {if isset($input.rows)}rows="{$input.rows|escape:'html':'UTF-8'}"{/if} class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name]|escape:'html':'UTF-8'}</textarea>
										{if isset($input.maxchar) && $input.maxchar}
											<script type="text/javascript">
											$(document).ready(function(){
												countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter"));
											});
											</script>
										{/if}
									{/if}
									{if isset($input.maxchar) && $input.maxchar}</div>{/if}
                        {/if} 
                {/if}{*end intput*}
                {if isset($input.desc) && !empty($input.desc)}
					<p class="help-block">
						{if is_array($input.desc)}
							{foreach $input.desc as $p}
								{if is_array($p)}
									<span id="{$p.id|escape:'html':'UTF-8'}">{$p.text|escape:'html':'UTF-8'}</span><br />
								{else}
									{$p|escape:'html':'UTF-8'}<br />
								{/if}
							{/foreach}
						{else}
							{$input.desc|escape:'html':'UTF-8'}
						{/if}
					</p>
				{/if}
            </div>
         </div>
        {/foreach}
        </div>
    {/if}
{if $id_field}
    </div>
{/if}
</div>