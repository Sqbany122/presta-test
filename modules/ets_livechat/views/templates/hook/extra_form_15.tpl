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
<div class="clear"></div>
<div class="margin-form">
<span class="add_auto_message form_auto_reply">{l s='Add auto message' mod='ets_livechat'}</span>
    <div id="block_form_auto_reply" style="display:none;" class="form_auto_reply">
        <div class="block_errors_auto_reply" style="display:none;"> 
        </div>
        <div class="form-group form-message-order">
            <label class="control-label col-lg-3" for="message_order">{l s='Message order' mod='ets_livechat'}</label>
            <div class="col-lg-9">
                <input id="message_order" class="" type="text" value="" name="message_order" />
                <p class="help-block">{l s='Auto reply to customer width this message order' mod='ets_livechat'}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3" for="auto_content">{l s='Auto content' mod='ets_livechat'}</label>
            <div class="col-lg-9">
                <textarea id="auto_content" class="" name="auto_content">
                </textarea>
            </div>
        </div>
        <input name="id_auto_msg" value="" type="hidden" id="id_auto_msg"/>
        <button id="cancel_auto_reply" class="btn btn-default">{l s='Cancel' mod='ets_livechat'}</button>
        <button id="submit_auto_reply" class="btn btn-default">{l s='Save' mod='ets_livechat'}</button>
    </div>
    <div id="auto_reply_list" class="form_auto_reply">
        <div id="auto_reply_0" class="auto_reply form-group">
            <div class="col-lg-3">{l s='Message order' mod='ets_livechat'}</div>
            <div class="col-lg-6">{l s='Auto content' mod='ets_livechat'}</div>
            <div class="col-lg-2">{l s='Action' mod='ets_livechat'}</div>
        </div>
        {if $auto_replies}
            {foreach from=$auto_replies item='auto_reply'}
                <div id="auto_reply_{$auto_reply.id_auto_msg|intval}" class="auto_reply form-group">
                    <div class="col-lg-3">{$auto_reply.message_order|intval}</div>
                    <div class="col-lg-6">{$auto_reply.auto_content|escape:'html':'utf-8'}</div>
                    <div class="col-lg-2">
                        <span class="lg_edit edit_auto_reply" data-id="{$auto_reply.id_auto_msg|intval}" title="{l s='Edit' mod='ets_livechat'}" >{l s='Edit' mod='ets_livechat'}</span>
                        <span class="lg_delete delete_auto_reply" data-id="{$auto_reply.id_auto_msg|intval}" title="{l s='Delete' mod='ets_livechat'}" >{l s='Delete' mod='ets_livechat'}</span>
                    </div>
                </div>
            {/foreach}
        {/if}
    </div>
</div>
<div class="clear"></div>
<div class="margin-form">
    <span class="add_pre_made_message form_pre_message">{l s='Add pre-made message' mod='ets_livechat'}</span>
    <div id="block_form_pre_made_message" style="display:none;" class="form_pre_message">
        <div class="block_errors_pre_made_message" style="display:none;"> 
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3" for="short_code_message">{l s='Short code' mod='ets_livechat'}</label>
            <div class="col-lg-9">
                <input id="short_code_message" class="" type="text" value="" name="short_code_message" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3" for="message_content">{l s='Message content' mod='ets_livechat'}</label>
            <div class="col-lg-9">
                <textarea id="message_content" class="" name="message_content">
                </textarea>
            </div>
        </div>
        <input name="id_pre_made_message" value="" type="hidden" id="id_pre_made_message"/>
        <button id="cancel_pre_made_message" class="btn btn-default">{l s='Cancel' mod='ets_livechat'}</button>
        <button id="submit_pre_made_message" class="btn btn-default">{l s='Save' mod='ets_livechat'}</button>
    </div>
    <div id="pre_made_message_list" class="form_pre_message">
        <div id="pre_made_message_0" class="pre_made_message form-group">
            <div class="col-lg-2">{l s='Short code' mod='ets_livechat'}</div>
            <div class="col-lg-3">{l s='Title' mod='ets_livechat'}</div>
            <div class="col-lg-4">{l s='Message content' mod='ets_livechat'}</div>
            <div class="col-lg-2">{l s='Action' mod='ets_livechat'}</div>
        </div>
        {if $pre_made_messages}
            {foreach from=$pre_made_messages item='pre_made_message'}
                <div id="pre_made_message_{$pre_made_message.id_pre_made_message|intval}" class="pre_made_message form-group">
                    <div class="col-lg-2">{$pre_made_message.short_code|escape:'html':'utf-8'}</div>
                    <div class="col-lg-3">{$pre_made_message.title_message|escape:'html':'utf-8'}</div>
                    <div class="col-lg-4">{$pre_made_message.message_content|escape:'html':'utf-8'}</div>
                    <div class="col-lg-2">
                        <span class="lg_edit edit_pre_made_message" data-id="{$pre_made_message.id_pre_made_message|intval}" title="{l s='Edit' mod='ets_livechat'}" >{l s='Edit' mod='ets_livechat'}</span>
                        <span class="lg_delete delete_pre_made_message" data-id="{$pre_made_message.id_pre_made_message|intval}" title="{l s='Delete' mod='ets_livechat'}" >{l s='Delete' mod='ets_livechat'}</span>
                    </div>
                </div>
            {/foreach}
        {/if}
    </div>
</div>
<div class="clear"></div>
<div class="margin-form">
     <div id="block_form_clear_message">
        <div class="block_sessucfull" style="display:none;"> 
            <div class="bootstrap">
        		<div class="alert alert-success">
        			<button data-dismiss="alert" class="close" type="button">×</button>
        			{l s='Conversations have been cleared successfully' mod='ets_livechat'}
        		</div>
        	</div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3" for="title_message">{l s='Clear all conversations' mod='ets_livechat'}</label>
            <div class="col-lg-9">
                <select id="ETS_CLEAR_MESSAGE" class="fixed-width-xl" name="ETS_CLEAR_MESSAGE">
                    <option value="everything">{l s='Everything' mod='ets_livechat'}</option>
                    <option value="1_week">{l s='1 week old' mod='ets_livechat'}</option>
                    <option value="1_month_ago">{l s='1 month old' mod='ets_livechat'}</option>
                    <option value="6_month_ago">{l s='6 months old' mod='ets_livechat'}</option>
                    <option value="1_year_ago">{l s='1 year old' mod='ets_livechat'}</option>
                </select>
            </div>
        </div>
        {if $attachments_everything}
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Delete past attachments' mod='ets_livechat'}: </label>
                <div class="col-lg-3"><span class="count_attachment">
                    <select id="ETS_CLEAR_ATTACHMENT" class="fixed-width-xl" name="ETS_CLEAR_ATTACHMENT">
                        <option value="everything">{l s='Everything' mod='ets_livechat'} ({$attachments_everything|intval} {if $attachments_everything > 1}{l s='files' mod='ets_livechat'}{else}{l s='file' mod='ets_livechat'}{/if}{if $attachments_everything_size < 1024} - {$attachments_everything_size|floatval} MB {else}{$attachments_everything_size/1024|floatval} GB{/if}) </option>
                        <option value="1_week">{l s='1 week old' mod='ets_livechat'} ({$attachments_1_week|intval} {if $attachments_1_week>1}{l s='files' mod='ets_livechat'}{else}{l s='file' mod='ets_livechat'}{/if}{if $attachments_1_week_size < 1024} -{$attachments_1_week_size|floatval} MB {else}{$attachments_1_week_size/1024|floatval} GB{/if} )</option>
                        <option value="1_month_ago">{l s='1 month old' mod='ets_livechat'} ({$attachments_1_month_ago|intval} {if $attachments_1_month_ago>1}{l s='files' mod='ets_livechat'}{else}{l s='file' mod='ets_livechat'}{/if}{if $attachments_1_month_ago_size < 1024} - {$attachments_1_month_ago_size|floatval} MB {else}{$attachments_1_month_ago_size/1024|floatval} GB{/if})</option>
                        <option value="6_month_ago">{l s='6 months old' mod='ets_livechat'} ({$attachments_6_month_ago|intval} {if $attachments_6_month_ago>1}{l s='files' mod='ets_livechat'}{else}{l s='file' mod='ets_livechat'}{/if}{if $attachments_6_month_ago_size < 1024} - {$attachments_6_month_ago_size|floatval} MB {else}{$attachments_6_month_ago_size/1024|floatval} GB{/if})</option>
                        <option value="1_year_ago">{l s='1 year old' mod='ets_livechat'} ({$attachments_year_ago|intval} {if $attachments_year_ago>1}{l s='files' mod='ets_livechat'}{else}{l s='file' mod='ets_livechat'}{/if}{if $attachments_year_ago_size < 1024} -{$attachments_year_ago_size|floatval} MB {else}{$attachments_year_ago_size/1024|floatval} GB{/if})</option>
                    </select>
                    <p class="help-block" style="width: 250%;">*{l s='Note: This will clear attachments of both live chat and ticketing system' mod='ets_livechat'}</p>
                </div>
                <div class="col-lg-2">
                    <button id="submit_clear_attachments" class="btn btn-default">{l s='Delete attachments' mod='ets_livechat'}</button>
                </div>
            </div>
        {/if}
        <button id="submit_clear_message" class="btn btn-default">{l s='Clear' mod='ets_livechat'}</button>
    </div>
</div>
<div class="clear"></div>
<div class="margin-form">
    <span class="add_departments form_departments">{l s='Add department' mod='ets_livechat'}</span>
    <div id="block_form_departments" style="display:none;" class="form_departments">
        <div class="block_errors_departments" style="display:none;"> 
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3" for="departments_status">{l s='Department status' mod='ets_livechat'}</label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
		    		<input name="departments_status" id="departments_status_on" value="1" type="radio" />
            		<label for="departments_status_on">{l s='Yes' mod='ets_livechat'}</label>
		    		<input name="departments_status" id="departments_status_off" value="0" checked="checked" type="radio" />
            		<label for="departments_status_off">{l s='No' mod='ets_livechat'}</label>
		    		<a class="slide-button btn"></a>
            	</span>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3 required" for="departments_name">{l s='Name' mod='ets_livechat'}</label>
            <div class="col-lg-9">
                <input id="departments_name" class="" type="text" value="" name="departments_name" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3" for="departments_description">{l s='Description' mod='ets_livechat'}</label>
            <div class="col-lg-9">
                <textarea id="departments_description" class="" name="departments_description">
                </textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3 required" for="departments_agents">{l s='Employees Agents' mod='ets_livechat'}</label>
            <div class="col-lg-9">
                {foreach from =$employees item='employee'}
                    <label for="departments_name_{$employee.id_employee|intval}"> <input class="departments_agents" id="departments_name_{$employee.id_employee|intval}" type="checkbox" name="departments_agents[]" value="{$employee.id_employee|intval}"/> {$employee.firstname|escape:'html':'UTF-8'}&nbsp;{$employee.lastname|escape:'html':'UTF-8'}( {$employee.email|escape:'html':'UTF-8'} )</label><br />
                {/foreach}
            </div>
        </div>
        <input name="id_departments" value="" type="hidden" id="id_departments"/>
        <button id="cancel_departments" class="btn btn-default">{l s='Cancel' mod='ets_livechat'}</button>
        <button id="submit_departments" class="btn btn-default">{l s='Save' mod='ets_livechat'}</button>
    </div>
    <div id="departments_list" class="form_departments">
        <div id="departments_0" class="departments form-group">
            <div class="col-lg-1 col-xs-1">{l s='ID' mod='ets_livechat'}</div>
            <div class="col-lg-4 col-xs-4">{l s='Name' mod='ets_livechat'}</div>
            <div class="col-lg-1 col-xs-1">{l s='Status' mod='ets_livechat'}</div>
            <div class="col-lg-5 col-xs-5">{l s='Agents' mod='ets_livechat'}</div>
            <div class="col-lg-1 col-xs-1">{l s='Action' mod='ets_livechat'}</div>
        </div>
        {if $departments}
            {foreach from =$departments item='department'}
                <div class="form-group" id="departments_{$department.id_departments|intval}">
                    <div class="col-lg-1 col-xs-1">
                        {$department.id_departments|intval}
                    </div>
                    <div class="col-lg-4 col-xs-4">
                        {$department.name|escape:'html':'UTF-8'}
                    </div>
                    <div class="col-lg-1 status col-xs-1">
                        {if $department.status}
                            <a class="lc_department_list_action field-is_featured list-action-enable action-enabled list-item-{$department.id_departments|intval}"  href="#" data-value="1" data-id="{$department.id_departments|intval}" title="{l s='Click to disabled' mod='ets_livechat'}">
                                <i class="icon-check"></i>
                            </a>
                        {else}
                            <a class="lc_department_list_action field-enabled list-action-enable action-disabled list-item-{$department.id_departments|intval}" href="#" data-value="0" data-id="{$department.id_departments|intval}" title="{l s='Click to Enable' mod='ets_livechat'}">
                                <i class="icon-remove"></i>
                            </a>
                        {/if}
                    </div>
                    <div class="col-lg-5 col-xs-5">
                        {if $department.agents}
                            {foreach from =$department.agents item='agent'}
                                {$agent.firstname|escape:'html':'UTF-8'}&nbsp;{$agent.lastname|escape:'html':'UTF-8'}( {$agent.email|escape:'html':'UTF-8'} ) <br />
                            {/foreach}
                        {/if}
                    </div>
                    <div class="col-lg-1 col-xs-1">
                        <span class="lg_edit edit_deparments" data-id="{$department.id_departments|intval}" title="{l s='Edit' mod='ets_livechat'}" >{l s='Edit' mod='ets_livechat'}</span>
                        <span class="lg_delete delete_departments" data-id="{$department.id_departments|intval}" title="{l s='Delete' mod='ets_livechat'}" >{l s='Delete' mod='ets_livechat'}</span>
                    </div>
                </div>
            {/foreach}
        {/if}
    </div>
</div>