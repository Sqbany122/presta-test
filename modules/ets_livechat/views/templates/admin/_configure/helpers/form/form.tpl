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
{extends file="helpers/form/form.tpl"}
{block name="defaultForm"}
    {$smarty.block.parent}
    {if $table=='module'}
        {hook h='displaySystemTicket'}
    {/if}
{/block}
{block name="field"}
    {$smarty.block.parent}
    {if $input.type == 'file' && $input.name=='ETS_LC_COMPANY_LOGO'  && isset($display_logo) && $display_logo}
        <label class="control-label col-lg-3 uploaded_image_label" style="font-style: italic;">{l s='Uploaded image: ' mod='ets_livechat'}</label>
        <div class="col-lg-9 uploaded_img_wrapper">
    		<a class="ybc_fancy" href="{$display_logo|escape:'html':'UTF-8'}"><img style="display: inline-block; max-width: 200px;" src="{$display_logo|escape:'html':'UTF-8'}" /></a>
            {if isset($logo_del_link) && $logo_del_link}
                <a onclick="return confirm('{l s='Do you want to delete loog?' mod='ets_livechat'}');" class="delete_url" style="display: inline-block; text-decoration: none!important;" href="{$logo_del_link|escape:'html':'UTF-8'}"><span style="color: #666"><i style="font-size: 20px;" class="process-icon-delete"></i></span></a>
            {/if}
        </div>
    {elseif $input.type == 'file' && $input.name=="ETS_LC_CUSTOMER_AVATA" && isset($display_avata) && $display_avata}
	    <label class="control-label col-lg-3 uploaded_image_label" style="font-style: italic;">{l s='Uploaded image: ' mod='ets_livechat'}</label>
        <div class="col-lg-9 uploaded_img_wrapper">
    		<a class="ybc_fancy" href="{$display_avata|escape:'html':'UTF-8'}"><img style="display: inline-block; max-width: 200px;" src="{$display_avata|escape:'html':'UTF-8'}" /></a>
            {if isset($avata_del_link) && $avata_del_link}
                <a onclick="return confirm('{l s='Do you want to delete avatar?' mod='ets_livechat'}');" class="delete_url"  style="display: inline-block; text-decoration: none!important;" href="{$avata_del_link|escape:'html':'UTF-8'}"><span style="color: #666"><i style="font-size: 20px;" class="process-icon-delete"></i></span></a>
            {/if}
        </div>
    {elseif $input.type == 'file' && $input.name=="ETS_LC_BUBBLE_IMAGE" && isset($display_bubble_imge) && $display_bubble_imge}
	    <label class="control-label col-lg-3 uploaded_image_label" style="font-style: italic;">{l s='Uploaded image: ' mod='ets_livechat'}</label>
        <div class="col-lg-9 uploaded_img_wrapper">
    		<a class="ybc_fancy" href="{$display_bubble_imge|escape:'html':'UTF-8'}"><img style="display: inline-block; max-width: 200px;" src="{$display_bubble_imge|escape:'html':'UTF-8'}" /></a>
            {*if isset($bubble_imge_del_link) && $bubble_imge_del_link}
                <a onclick="return confirm('{l s='Do you want to delete bubble image?' mod='ets_livechat'}');" class="delete_url"  style="display: inline-block; text-decoration: none!important;" href="{$bubble_imge_del_link|escape:'html':'UTF-8'}"><span style="color: #666"><i style="font-size: 20px;" class="process-icon-delete"></i></span></a>
            {/if*}
        </div>
    {/if}
{/block}
{block name="input"}
    {if $input.type == 'checkbox'}
            {if isset($input.values.query) && $input.values.query}
                {foreach $input.values.query as $value}
    				{assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]|escape:'html':'UTF-8'}
    				<div class="checkbox{if isset($input.expand) && strtolower($input.expand.default) == 'show'} hidden{/if}">
    					{strip}
    						<label for="{$id_checkbox|escape:'html':'UTF-8'}">                                
    							<input {if $value[$input.values.id]=='message'}disabled="disabled"{/if} type="checkbox" name="{$input.name|escape:'html':'UTF-8'}[]" id="{$id_checkbox|escape:'html':'UTF-8'}" {if isset($value[$input.values.id])} value="{$value[$input.values.id]|escape:'html':'UTF-8'}"{/if}{if isset($fields_value[$input.name]) && is_array($fields_value[$input.name]) && $fields_value[$input.name] && in_array($value[$input.values.id],$fields_value[$input.name]) || $value[$input.values.id]=='message'} checked="checked"{/if} />
    							{$value[$input.values.name]|escape:'html':'UTF-8'}
    						</label>
    					{/strip}
    				</div>
    			{/foreach} 
            {/if} 
    {elseif $input.type == 'switch'}
    	<span class="switch prestashop-switch fixed-width-lg">
    		{foreach $input.values as $value}
    		<input type="radio" name="{$input.name|escape:'html':'UTF-8'}"{if $value.value == 1} id="{$input.name|escape:'html':'UTF-8'}_on"{else} id="{$input.name|escape:'html':'UTF-8'}_off"{/if} value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if (isset($input.disabled) && $input.disabled) or (isset($value.disabled) && $value.disabled)} disabled="disabled"{/if}/>
    		{strip}
    		<label {if $value.value == 1} for="{$input.name|escape:'html':'UTF-8'}_on"{else} for="{$input.name|escape:'html':'UTF-8'}_off"{/if}>
    			{if $value.value == 1}
    				{l s='Yes' mod='ets_livechat'}
    			{else}
    				{l s='No' mod='ets_livechat'}
    			{/if}
    		</label>
    		{/strip}
    		{/foreach}
    		<a class="slide-button btn"></a>
    	</span>
    {else}
        {$smarty.block.parent}                    
    {/if}            
{/block}
{block name="legend"}
	<div class="panel-heading">
		{if isset($field.image) && isset($field.title)}<img src="{$field.image|escape:'html':'UTF-8'}" alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
		{if isset($field.icon)}<i class="{$field.icon|escape:'html':'UTF-8'}"></i>{/if}
		{$field.title|escape:'html':'UTF-8'}
        {if isset($addNewUrl)}
            <span class="panel-heading-action">                        
                <a class="list-toolbar-btn ybc-blog-add-new" href="{$addNewUrl|escape:'html':'UTF-8'}">
                    <span data-placement="top" data-html="true" data-original-title="{l s='Add new item ' mod='ets_livechat'}" class="label-tooltip" data-toggle="tooltip" title="">
        				<i class="process-icon-new"></i>
                    </span>
                </a>            
            </span>
        {/if}
         {if isset($post_key) && $post_key}<input type="hidden" name="post_key" value="{$post_key|escape:'html':'UTF-8'}" />{/if}
	</div>
    {if isset($configTabs) && $configTabs}
        <ul class="confi_tab_left">
        {foreach from=$configTabs item='tab' key='tabId'}
            <li class="confi_tab config_tab_{$tabId|escape:'html':'UTF-8'}" data-tab-id="{$tabId|escape:'html':'UTF-8'}">{$tab|escape:'html':'UTF-8'}</li>
        {/foreach}
        </ul>
        <ul class="confi_tab_right">
            <li class="confi_tab config_tab_livechat active" data-tab-id="status" >{l s='Live chat' mod='ets_livechat'}</li>
            <li class="confi_tab config_tab_ticket_system" data-tab-id="ticket_system" >{l s='Ticketing system' mod='ets_livechat'}</li>
            <li class="confi_tab config_tab_departments" data-tab-id="departments" >{l s='Departments' mod='ets_livechat'}</li>
            <li class="confi_tab config_tab_staffs" data-tab-id="staffs" >{l s='Staffs' mod='ets_livechat'}</li>
        </ul>
    {/if}
    
{/block}
{block name="input_row"}
    {if $input.name =='ETS_ENABLE_AUTO_REPLY'}
        <div class="ybc-form-group ybc-blog-tab-auto_reply">
            <div class="alert alert-warning">{l s='Only send auto-reply message when the staff does not need to accept or decline chat. In other words, option "Staff to accept or decline chat" at IM tab should be disabled to send the auto-reply messages' mod='ets_livechat'}</div>
        </div>
    {/if}
    {if isset($isConfigForm) && $isConfigForm}
        {if $input.name=='ETS_LC_TEXT_HEADING_ONLINE'}
            <div class="lc_status_tab_wrapper ybc-form-group ybc-blog-tab-status"> <!-- start chatbox -->
                <ul class="lc_status_tab_title">
                  <li class="statust_tab chatbox_tab active" data-tab="lc_online" data-from="online"><span>{l s='Online' mod='ets_livechat'}</span></li>
                  <li class="statust_tab chatbox_tab" data-tab="lc_busy" data-from="do_not_disturb"><span>{l s='Busy' mod='ets_livechat'}</span></li>
                  <li class="statust_tab chatbox_tab" data-tab="lc_invisible" data-from="invisible"><span>{l s='Invisible' mod='ets_livechat'}</span></li>
                  <li class="statust_tab chatbox_tab" data-tab="lc_offline" data-from="offline"><span>{l s='Offline' mod='ets_livechat'}</span></li>
                </ul>
                <input id="id_language_change" value="{$defaultFormLanguage|intval}" name="id_langague_change" type="hidden" />
            <div class="status-wrapper">
           <div class="status-left">
        {/if}
        <div class="ybc-form-group{if isset($input.tab) && $input.tab} ybc-blog-tab-{$input.tab|escape:'html':'UTF-8'}{/if} {if isset($current_tab_acitve) && $current_tab_acitve==$input.tab}active{/if}">            
            {$smarty.block.parent}
            {if isset($input.info) && $input.info}
                <div class="ybc_tc_info alert alert-warning">{$input.info|escape:'html':'UTF-8'}</div>
            {/if}
        </div>
        {if $input.name=='ETS_LIVECHAT_FACEBOOK_APP_SECRET'}
            <div class="ybc-form-group ybc-blog-tab-sosial">            
				<div class="form-group login_facebook">
			        <label class="control-label col-lg-3">{l s='Redirect URI' mod='ets_livechat'}</label>								
			        <div class="col-lg-9">
                          <span class="ets_livechat_callback_url" data-msg="{l s='Copied' mod='ets_livechat'}">{$link_callback|escape:'html':'UTF-8'}</span>				
		                  <p class="help-block">
    						{l s='Copy and paste this Redirect URI to get your social network API key pair' mod='ets_livechat'}
			              </p>
                    </div>
                </div>
			</div>
            <div class="ybc-form-group ybc-blog-tab-sosial">
                <div class="lc_line"></div>
            </div>
        {/if}
        {if $input.name=='ETS_LIVECHAT_GOOGLE_APP_SECRET'}
            <div class="ybc-form-group ybc-blog-tab-sosial">            
				<div class="form-group login_google">
			        <label class="control-label col-lg-3">{l s='Redirect URI' mod='ets_livechat'}</label>								
			        <div class="col-lg-9">
                          <span class="ets_livechat_callback_url" data-msg="{l s='Copied' mod='ets_livechat'}">{$link_callback|escape:'html':'UTF-8'}</span>				
		                  <p class="help-block">
    						{l s='Copy and paste this Redirect URI to get your social network API key pair' mod='ets_livechat'}
			              </p>
                    </div>
                </div>
			</div>
            <div class="ybc-form-group ybc-blog-tab-sosial"> 
                <div class="lc_line"></div>
            </div>
        {/if}
        {if $input.name=='ETS_LIVECHAT_TWITTER_APP_SECRET'}
            <div class="ybc-form-group ybc-blog-tab-sosial">            
				<div class="form-group login_facebook">
			        <label class="control-label col-lg-3">{l s='Callback URL' mod='ets_livechat'}</label>								
			        <div class="col-lg-9">
                          <span class="ets_livechat_callback_url" data-msg="{l s='Copied' mod='ets_livechat'}">{$link_callback|escape:'html':'UTF-8'}</span>				
		                  <p class="help-block">
    						{l s='Copy and paste this Callback URL to get your social network API key pair' mod='ets_livechat'}
			              </p>
                    </div>
                </div>
			</div>
        {/if}
        {if $input.name=='ETS_LC_TEXT_OFFLINE_THANKYOU'}
            </div>
                <div class="status-right">
                    <div class="form-group status lc_online">
                        <div class="block-chat-online">
                            {hook h='displayBlockOnline'}
                        </div>
                    </div>
                    <div class="form-group status lc_busy">
                        <div class="block-chat-busy">
                            {hook h='displayBlockBusy'}
                        </div>
                    </div>
                    <div class="form-group status lc_invisible">
                        <div class="block-chat-invisible">
                            {hook h='displayBlockInvisible'}
                        </div>
                    </div>
                    <div class="form-group status lc_offline">
                        <div class="block-chat-offline">
                            {hook h='displayBlockOffline'}
                        </div>
                    </div>
                </div>
            </div>
            </div>
        {/if} 
    {else}
        {if $input.name=='title' && isset($table) && $table=='form_ticket'}
                <ul class="lc_status_tab_title">
                  <li class="statust_tab ticket_tab active" data-tab="info" data-from="info"><span>{l s='Info' mod='ets_livechat'}</span></li>
                  <li class="statust_tab ticket_tab" data-tab="fields" data-from="fields"><span>{l s='Field list' mod='ets_livechat'}</span></li>
                  <li class="statust_tab ticket_tab" data-tab="email" data-from="email"><span>{l s='Email' mod='ets_livechat'}</span></li>
                  <li class="statust_tab ticket_tab" data-tab="general" data-from="general"><span>{l s='General settings' mod='ets_livechat'}</span></li>
                </ul>
            <div class="ticket-wrapper">
        {/if}
        {$smarty.block.parent}
        {if $input.name=='button_submit_label' && isset($table) && $table=='form_ticket'}
            <div class="form-group ticket fields change_form">
               <div class="block-fields">
                    <div class="block-field-form">
                        <span class="add_new_field_in_form field-private">{l s='Add field' mod='ets_livechat'}</span>
                    </div>
                    <div class="list-fields" id="list-fields">
                    </div>
               </div>
            </div>
            </div>
        {/if}
    {/if}
    {if $input.name=='ETS_LC_TEXT_OFFLINE_THANKYOU'}
        <div class="ybc-form-group ybc-blog-tab-staffs">
            {hook h='displayStaffs'}
        </div>
    {/if}
{/block}
{block name="field"}
    {$smarty.block.parent}
    {if isset($is_ps15) && $is_ps15}
        {if $input.name=='button_submit_label' && isset($table) && $table=='form_ticket'}
            <div class="margin-form">
               <div class="block-fields">
                    <div class="block-field-form">
                        <span class="add_new_field_in_form field-private">{l s='Add field' mod='ets_livechat'}</span>
                    </div>
                    <div class="list-fields" id="list-fields">
                    </div>
               </div>
            </div>
        {/if}
        {if $input.name=='ETS_LC_TEXT_OFFLINE_THANKYOU'}
            <div class="margin-form">
                {hook h='displayStaffs'}
            </div>
        {/if}
    {/if}
{/block}
{block name="input"}
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
    							var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}{literal}';
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
    				name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}"
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
    				<li><a href="javascript:hideOtherLanguageETS({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
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
    		name="{$input.name|escape:'html':'UTF-8'}"
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
    						<span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}_counter" class="input-group-addon">
    							<span class="text-count-down">{$input.maxchar|intval}</span>
    						</span>
    					{/if}
    					<textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}" id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_{$language.id_lang|intval}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}</textarea>
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
    							<a href="javascript:hideOtherLanguageETS({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a>
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
    		<textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name|escape:'html':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}" {if isset($input.cols)}cols="{$input.cols|escape:'html':'UTF-8'}"{/if} {if isset($input.rows)}rows="{$input.rows|escape:'html':'UTF-8'}"{/if} class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name]|escape:'html':'UTF-8'}</textarea>
    		{if isset($input.maxchar) && $input.maxchar}
    			<script type="text/javascript">
    			$(document).ready(function(){
    				countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter"));
    			});
    			</script>
    		{/if}
    	{/if}
    	{if isset($input.maxchar) && $input.maxchar}</div>{/if}
     {else}
        {$smarty.block.parent}
     {/if}
{/block}
{block name="description"}
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
				{$input.desc nofilter}
			{/if}
		</p>
	{/if}
{/block}