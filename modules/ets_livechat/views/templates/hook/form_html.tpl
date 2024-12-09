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
{if !$backend}
<form action="" method="post" id="form_{$id_form|intval}" enctype="multipart/form-data" class="defaultForm form-horizontal">
    <input type="hidden" name="submit_send_ticket" value="{$id_form|intval}" />
    <input type="hidden" name="id_form" value="{$id_form|intval}"/>
    {if isset($errors) && $errors && !$admin}
        {$errors nofilter}
    {/if}
    {if isset($success) && $success}
        <div class="col-xs-12 alert alert-success">
            <button class="close" type="button" data-dismiss="alert">×</button>
            <ul>
                <li>{$success|escape:'html':'UTF-8'}</li>
            </ul>
        </div>
    {/if}
    <div class="panel">
        <div class="panel-heading">
        {if $admin}{l s='Add ticket to' mod='ets_livechat'}&nbsp;"{/if}{$form->title|escape:'html':'UTF-8'}{if $admin}"{/if}
            {if $admin}
                <span class="panel-heading-action">
                    {if $forms}
                        {if $new_ticket_link}
                            <a class="submit_new_ticket btn btn-default btn-primary" href="{$new_ticket_link|escape:'html':'UTF-8'}" title="{l s='Submit new ticket' mod='ets_livechat'}">{l s='Submit new ticket' mod='ets_livechat'}</a>
                        {else}
                            <span class="submit_new_ticket_bt btn btn-default btn-primary" title="{l s='Submit new ticket' mod='ets_livechat'}">{l s='Submit new ticket' mod='ets_livechat'}</span>
                        {/if}
                    {/if}
                </span>
            {/if}
        </div>
        {if $form->description}
            <div class="desc">{$form->description nofilter}</div>
        {/if}
        {if !$new_ticket_link && $forms}
            <div class="lc_form_submit_new_ticket lc_popup" style="display:none;">
                <div class="pop_table">
                    <div class="pop_table_cell">
                        <div class="lc_form_submit_new_ticket_content">
                        <h4 class="lc_form_submit_title">
                            {l s='Select ticket form' mod='ets_livechat'}
                            <span class="lc_form_submit_close ets_close" title="{l s='Close' mod='ets_livechat'}"></span>
                        </h4>
                        <div class="form-group">
                            <select name="form_ticket" id="form_ticket">
                                    <option value="--">{l s='--Select a support form--' mod='ets_livechat'}</option>
                                {foreach from=$forms item='form'}
                                    <option value="{$form.link|escape:'html':'UTF-8'}">{$form.title|escape:'html':'UTF-8'}</option>
                                {/foreach}
                            </select>
                            <button name="new_ticket_bt" type="button" id="new_ticket_bt" class="default btn btn-default btn-primary">{l s='Continue' mod='ets_livechat'}</button>
                        </div>
                    </div>
                    </div>
                </div>
                
            </div>
        {/if}
        {if $logged || $form->allow_user_submit || $admin}
            <section class="form-fields">
                {if isset($search_customer) && $search_customer}
                    <div class="form-group row">
                        <label for="search_customer_ticket" class="form-control-label col-xs-12 col-sm-12{if !$form->allow_user_submit} required{/if}">{l s='Customer associated' mod='ets_livechat'}</label>
                        <div class="col-xs-12 col-sm-12">
                            <div class="input-group">
                                {if isset($post_fields.search_customer) && $post_fields.search_customer}
                                    <div class="customer_name_search">{$post_fields.search_customer|escape:'html':'UTF-8'}<span class="delete_customer_search"><i class="icon-trash"></i></span></div>
                                {/if}
                                <input class="form-control {if !$form->allow_user_submit} readonly{/if}" id="search_customer_ticket" type="text" name="search_customer_ticket"  placeholder="{l s='Search customer by ID, name, email or phone number' mod='ets_livechat'}" {if isset($post_fields.search_customer)} value="{$post_fields.search_customer|escape:'html':'UTF-8'}"{/if} />
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                            </div>
                            <input type="hidden" name="id_customer_ticket" id="id_customer_ticket"{if isset($post_fields.id_customer_ticket)} value="{$post_fields.id_customer_ticket|escape:'html':'UTF-8'}"{/if} />                                                    
                        </div>
                    </div>               
                {/if}
                {if $departments}
                    <div class="form-group row">
                        <label for="id_departments" class="form-control-label col-xs-12 col-sm-12 required">{l s='Department' mod='ets_livechat'}</label>
                        <div class="col-xs-12 col-sm-12">
                            <select  id="id_departments" class="form-control" name="id_departments">
                                <option value="">{l s='--Select a department--' mod='ets_livechat'}</option>
                                {foreach from=$departments item='department'}
                                    <option value="{$department.id_departments|intval}" {if (!isset($success) || !$success) && isset($smarty.post.id_departments) && $smarty.post.id_departments==$department.id_departments} selected="selected"{/if}>{$department.name|escape:'html':'UTF-8'}</option>
                                {/foreach}
                            </select>
                            <span class="select_arrow"></span>
                        </div>
                    </div>
                {/if}                        
             {if $fields}
                    {foreach from=$fields item='input'}
                        <div class="form-group row">
                            {if $input.label}
                                <label for="fields_{$input.id_field|intval}" class="form-control-label col-xs-12 col-sm-12{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
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
                            <div class="col-xs-12 col-sm-12">
                                {if $input.type == 'text' || $input.type == 'email' || $input.type=="phone_number" }
                                    <input class="form-control{if $input.is_contact_mail} is_contact_mail{/if}{if $input.is_contact_name} is_contact_name{/if}{if $input.is_customer_phone_number} is_customer_phone_number{/if}" id="fields_{$input.id_field|intval}" type="text" name="fields[{$input.id_field|intval}]"{if $input.placeholder} placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if} value="{if isset($input.value) && $input.value}{$input.value|escape:'html':'UTF-8'}{else}{if isset($post_fields[$input.id_field]) && (!isset($success) || !$success )}{$post_fields[$input.id_field]|escape:'html':'UTF-8'}{/if}{/if}" {if (isset($input.value) &&  $input.value) || isset($input['readonly'])}readonly="true"{/if}/>
                                {elseif $input.type=='text_editor'}
                                    <textarea class="form-control" id="fields_{$input.id_field|intval}" name="fields[{$input.id_field|intval}]"{if $input.placeholder} placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if}>{if isset($post_fields[$input.id_field]) && (!isset($success) || !$success )}{$post_fields[$input.id_field]|escape:'html':'UTF-8'}{/if}</textarea>
                                {elseif $input.type=='select'}
                                    <div class="lc_form_select">
                                        <select class="form-control" id="fields_{$input.id_field|intval}" name="fields[{$input.id_field|intval}]">
                                            {if $input.options}
                                                {foreach from=$input.options item='option'}
                                                    <option value="{$option|escape:'html':'UTF-8'}" {if isset($post_fields[$input.id_field]) && (!isset($success) || !$success ) && $post_fields[$input.id_field]==$option}selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                                                {/foreach}
                                            {/if}
                                        </select>
                                        <span class="select_arrow"></span>
                                    </div>
                                {elseif $input.type=='radio'}
                                    {if $input.options}
                                        {foreach from=$input.options key='key' item='option'}
                                            <label class="set_lvc_radio" for="fields{$input.id_field|intval}_{$key|intval}">
                                            {$option|escape:'html':'UTF-8'}
                                            <input type="radio" id="fields{$input.id_field|intval}_{$key|intval}" name="fields[{$input.id_field|intval}]" value="{$option|escape:'html':'UTF-8'}" {if isset($post_fields[$input.id_field]) && (!isset($success) || !$success ) && $post_fields[$input.id_field]==$option}checked="checked"{/if}/>
                                            </label>
                                        {/foreach}
                                    {/if}
                                {elseif $input.type=='file'}
                                    <div class="lc_upload_file">
                                        <input class="form-control" name="fields[{$input.id_field|intval}]" id="fields_{$input.id_field|intval}" type="file" />
                            		</div>
                                {/if}
                                {if $input.description}
                                    <p class="help-block">{$input.description|escape:'html':'UTF-8'} </p>
                                {/if}
                            </div>
                        </div>
                    {/foreach}
                {/if}
                {if $captchaUrl && !$admin}
                    <div class="form-group row">
                        <label class="form-control-label col-xs-12 col-sm-12" for="field_captcha">
                            {l s='Captcha code' mod='ets_livechat'}
                        </label>
                        <div class="col-xs-12 col-sm-12">
                            <div class="lc_ticket_captcha">
                                <img class="lc_ticket_captcha_img" src="{$captchaUrl|escape:'html':'UTF-8'}" />
                                <span data-captcha-img="{$captcha|escape:'html':'utf-8'}" class="lc_ticket_captcha_refesh" title="{l s='Refresh' mod='ets_livechat'}">{l s='Refresh' mod='ets_livechat'}</span>
                                <input id="field_captcha" class="form-control" name="field_captcha" value="" type="text" />
                            </div>
                        </div>
                    </div>
                {/if}
            </section>
            <div class="form-group row">
                <footer class="form-footer text-sm-right col-xs-12 col-sm-12">
                    <button class="btn btn-primary submit_send_ticket" type="submit" value="1" name="submit_send_ticket_{$id_form|intval}">{if $form->button_submit_label}{$form->button_submit_label|escape:'html':'UTF-8'}{else}{l s='Submit' mod='ets_livechat'}{/if}</button>
                    {if $admin}
                        <a style="float:right" class="btn btn-default" href="{$link->getAdminLink('AdminLiveChatTickets')|escape:'html':'UTF-8'}"><i class="fa fa-long-arrow-left"></i>{l s='Back' mod='ets_livechat'}</a>
                    {/if}
                </footer>
            </div>
        {else}
            <div class="alert info alert-warning">
                {l s='Please login to submit ticket' mod='ets_livechat'}
            </div>
        {/if}
        <div class="clearfix"></div>
    </div>
</form>
{else}
    {if $fields}
        {foreach from=$fields item='input'}
            <div class="form-group row">
                {if $input.label}
                    <label for="fields_{$input.id_field|intval}" class="form-control-label col-xs-12 col-sm-12{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
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
                <div class="col-xs-12 col-sm-12">
                    {if $input.type == 'text' || $input.type == 'email' || $input.type=="phone_number" }
                        <input class="form-control" id="fields_{$input.id_field|intval}" type="text" name="fields[{$input.id_field|intval}]"{if $input.placeholder} placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if} value="{if isset($input.value) && $input.value}{$input.value|escape:'html':'UTF-8'}{else}{if isset($post_fields[$input.id_field]) && (!isset($success) || !$success )}{$post_fields[$input.id_field]|escape:'html':'UTF-8'}{/if}{/if}" {if isset($input.value) &&  $input.value}readonly="true"{/if}/>
                    {elseif $input.type=='text_editor'}
                        <textarea class="form-control" id="fields_{$input.id_field|intval}" name="fields[{$input.id_field|intval}]"{if $input.placeholder} placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if}>{if isset($post_fields[$input.id_field]) && (!isset($success) || !$success )}{$post_fields[$input.id_field]|escape:'html':'UTF-8'}{/if}</textarea>
                    {elseif $input.type=='select'}
                        <div class="lc_form_select">
                            <select class="form-control" id="fields_{$input.id_field|intval}" name="fields[{$input.id_field|intval}]">
                                {if $input.options}
                                    {foreach from=$input.options item='option'}
                                        <option value="{$option|escape:'html':'UTF-8'}" {if isset($post_fields[$input.id_field]) && (!isset($success) || !$success ) && $post_fields[$input.id_field]==$option}selected="selected"{/if}>{$option|escape:'html':'UTF-8'}</option>
                                    {/foreach}
                                {/if}
                            </select>
                            <span class="select_arrow"></span>
                        </div>
                    {elseif $input.type=='radio'}
                        {if $input.options}
                            {foreach from=$input.options key='key' item='option'}
                                <label class="set_lvc_radio" for="fields{$input.id_field|intval}_{$key|intval}">
                                {$option|escape:'html':'UTF-8'}
                                <input type="radio" id="fields{$input.id_field|intval}_{$key|intval}" name="fields[{$input.id_field|intval}]" value="{$option|escape:'html':'UTF-8'}" {if isset($post_fields[$input.id_field]) && (!isset($success) || !$success ) && $post_fields[$input.id_field]==$option}checked="checked"{/if}/>
                                </label>
                            {/foreach}
                        {/if}
                    {elseif $input.type=='file'}
                        <div class="lc_upload_file">
                            <input class="form-control" name="fields[{$input.id_field|intval}]" id="fields_{$input.id_field|intval}" type="file" />
                		</div>
                    {/if}
                    {if $input.description}
                        <p class="help-block">{$input.description|escape:'html':'UTF-8'} </p>
                    {/if}
                </div>
            </div>
        {/foreach}
    {/if}
    {if $departments}
        <div class="form-group row">
            <label for="ticket_id_departments_{$conversation->id|escape:'html':'UTF-8'}" class="form-control-label col-xs-12 col-sm-12">{l s='Department' mod='ets_livechat'}</label>
            <div class="col-xs-12 col-sm-12">
                <select id="ticket_id_departments_{$conversation->id|escape:'html':'UTF-8'}" class="form-control" name="ticket_id_departments" class="ticket_id_departments" data-id="{$conversation->id|intval}">
                    <option value="">{l s='--Select a department--' mod='ets_livechat'}</option>
                    {foreach from=$departments item='department'}
                        <option value="{$department.id_departments|intval}" {if $conversation->id_departments==$department.id_departments} selected="selected"{/if} class="{if $department.all_employees}all_employees{/if}">{$department.name|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
                <span class="select_arrow"></span>
            </div>
        </div>
    {/if}
    <div class="form-group row">
        <label for="ticket_id_employee_{$conversation->id|escape:'html':'UTF-8'}" class="form-control-label col-xs-12 col-sm-12">{l s='Staff' mod='ets_livechat'}</label>
        <div class="col-xs-12 col-sm-12">
            <select id="ticket_id_employee_{$conversation->id|escape:'html':'UTF-8'}" class="form-control" name="ticket_id_employee" class="ticket_id_employee">
                <option value="">{l s='--All employees--' mod='ets_livechat'}</option>
                {foreach from= $employees item='employee'}
                    <option class="{if $employee.id_profile!=1}chonse_department{/if}{if $employee.departments}{foreach from= $employee.departments item='department'} department_{$department.id_departments|intval}{/foreach}{/if}" value="{$employee.id_employee|intval}" {if $conversation->id_employee==$employee.id_employee} selected="selected"{/if}>{if $employee.name}{$employee.name|escape:'html':'UTF-8'}{else}{$employee.firstname|escape:'html':'UTF-8'}&nbsp;{$employee.lastname|escape:'html':'UTF-8'}{/if}</option>
                {/foreach}
            </select>
            <span class="select_arrow"></span>
        </div>
    </div>
    <div class="form-group row">
        <label for="ticket_status" class="form-control-label col-xs-12 col-sm-12">
            {l s='Status' mod='ets_livechat'}
        </label>
        <div class="col-xs-12 col-sm-12">
            <div class="lc_form_select">
                <select class="form-control" id="ticket_status" name="ticket_status">
                    <option value="open">{l s='Open' mod='ets_livechat'}</option>
                    <option value="close">{l s='Closed' mod='ets_livechat'}</option>
                    <option value="cancel">{l s='Canceled' mod='ets_livechat'}</option>
                </select>
                <span class="select_arrow"></span>
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label for="ticket_priority" class="form-control-label col-xs-12 col-sm-12">
            {l s='Priority' mod='ets_livechat'}
        </label>
        <div class="col-xs-12 col-sm-12">
            <div class="lc_form_select">
                <select class="form-control" id="ticket_priority" name="ticket_priority">
                    <option value="1">{l s='Low' mod='ets_livechat'}</option>
                    <option value="2">{l s='Medium' mod='ets_livechat'}</option>
                    <option value="3">{l s='High' mod='ets_livechat'}</option>
                    <option value="4">{l s='Urgent' mod='ets_livechat'}</option>
                </select>
                <span class="select_arrow"></span>
            </div>
        </div>
    </div>
    {if $conversation && $conversation->id_customer}
        <div class="form-group row">
            <label for="display_customer_on" class="form-control-label col-xs-12 col-sm-12">{l s='Display to customer' mod='ets_livechat'}</label>
            <div class="col-xs-12 col-sm-12">
                <span class="switch prestashop-switch fixed-width-lg">
          		    <input name="display_customer" id="display_customer_on" value="1" checked="checked" type="radio" />
              		<label for="display_customer_on">{l s='Yes' mod='ets_livechat'}</label>
                    <input name="display_customer" id="display_customer_off" value="0" type="radio" />
              		<label for="display_customer_off">{l s='No' mod='ets_livechat'}</label>
          		    <a class="slide-button btn"></a>
            	</span>
            </div>
        </div>
    {/if}
    <div class="form-group row">
        <footer class="form-footer text-sm-right col-xs-12 col-sm-12">
            <button class="btn btn-primary submit_change_to_ticket" type="button" value="1" name="submit_change_to_ticket">{l s='Create ticket' mod='ets_livechat'}</button>
            <button class="btn btn-primary cancel_change_to_ticket" type="button" value="1" name="cancel_change_to_ticket">{l s='Cancel' mod='ets_livechat'}</button>
        </footer>
    </div>
{/if}