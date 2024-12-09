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
<div class="lc_ticket_system_form">
    <ul class="confi_tab_right">
            <li class="confi_tab config_tab_livechat active" data-tab-id="status" >{l s='Live chat' mod='ets_livechat'}</li>
            <li class="confi_tab config_tab_ticket_system" data-tab-id="ticket_system" >{l s='Ticketing system' mod='ets_livechat'}</li>
            <li class="confi_tab config_tab_departments" data-tab-id="departments" >{l s='Departments' mod='ets_livechat'}</li>
            <li class="confi_tab config_tab_staffs" data-tab-id="staffs" >{l s='Staffs' mod='ets_livechat'}</li>
    </ul>
    <div class="panel-heading">
        <i class="icon-AdminAdmin"></i>
        {l s='Ticketing system configuration' mod='ets_livechat'}
    </div>
    <div class="lc_block_form_new_ticket">
    </div>
    <div class="lc_system_ticket">
        <div class="block_form_departments_list">
        <div class="block_form_departments_list_header">
            <h4 class="tab_title_c">{l s='Ticket forms' mod='ets_livechat'}</h4>
            <span class="add_new_ticket_form"><span class="tooltip_c">{l s='Add new form' mod='ets_livechat'}</span></span>
        </div>
        
        <div class="lc-list-ticket-form">    
            <div id="ticket_form_0" class="ticket_form form-group" {if !$forms}style="display:none;"{/if}>
                <div class="col-lg-1 col-xs-1">{l s='ID' mod='ets_livechat'}</div>
                <div class="col-lg-2 col-xs-2">{l s='Form' mod='ets_livechat'}</div>
                <div class="col-lg-3 col-xs-3">{l s='Form URL' mod='ets_livechat'}</div>
                <div class="col-lg-3 col-xs-3">{l s='Description' mod='ets_livechat'}</div>
                <div class="col-lg-1 col-xs-1">{l s='Sort order' mod='ets_livechat'}</div>
                <div class="col-lg-1 col-xs-1">{l s='Status' mod='ets_livechat'}</div>
                <div class="col-lg-1 col-xs-1">{l s='Action' mod='ets_livechat'}</div>
            </div>  
            <div id="lc-list-ticket-form">    
                {if $forms}
                    {foreach from=$forms item='form'}
                        <div id="ticket_form_{$form.id_form|intval}" class="ticket_form form-group">
                            <div class="col-lg-1 col-xs-1">{$form.id_form|intval}</div>
                            <div class="col-lg-2 col-xs-2">{$form.title|escape:'html':'UTF-8'}</div>
                            <div class="col-lg-3 col-xs-3"><a target="_blank" href="{$form.link|escape:'html':'UTF-8'}">{$form.link|escape:'html':'UTF-8'}</a></div>
                            <div class="col-lg-3 col-xs-3">{$form.description|escape:'html':'utf-8'}</div>
                            <div class="col-lg-1 col-xs-1 sort_order dragHandle">
                                <div class="dragGroup">
                                    <span class="position">{$form.sort_order|intval}</span>
                                </div>
                            </div>
                            <div class="col-lg-1 col-xs-1">
                                {if $form.id_form!=1}
                                    {if $form.active}
                                        <a class="lc_form_list_action field-is_featured list-action-enable action-enabled list-item-{$form.id_form|intval}"  href="#" data-value="1" data-id="{$form.id_form|intval}" title="{l s='Click to disabled' mod='ets_livechat'}">
                                            <i class="icon-check"></i>
                                        </a>
                                    {else}
                                        <a class="lc_form_list_action field-enabled list-action-enable action-disabled list-item-{$form.id_form|intval}" href="#" data-value="0" data-id="{$form.id_form|intval}" title="{l s='Click to Enable' mod='ets_livechat'}">
                                            <i class="icon-remove"></i>
                                        </a>
                                    {/if}
                                {else}
                                    <b class="list-action-enable action-enabled">
                                        <i class="icon-check"></i>
                                    </b>
                                    
                                {/if}
                            </div>
                            <div class="col-lg-1 col-xs-1">
                                <span class="lg_edit edit_form" data-id="{$form.id_form|intval}" title="{l s='Edit' mod='ets_livechat'}" >{l s='Edit' mod='ets_livechat'}</span>
                                {if $form.id_form!=1}
                                    <span class="lg_delete delete_form" data-id="{$form.id_form|intval}" title="{l s='Delete' mod='ets_livechat'}" >{l s='Delete' mod='ets_livechat'}</span>
                                {/if}
                            </div>
                        </div>
                    {/foreach}
                {/if}
            </div>  
            <div class="lc-no-form lc_no_recode" {if $forms}style="display:none;"{/if}>
                {l s='No ticket form available' mod='ets_livechat'}
            </div>
        </div>
        </div>
    </div>
</div>


    
