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
{$menu_top nofilter}
<div class="lc_ticket_wapper">
    <div class="lc_ticket_header">
        <div class="lc_id_ticket">
            <strong>{l s='Ticket ID' mod='ets_livechat'}: </strong>#{$ticket.id_message|intval} 
        </div>
        <div class="lc_status_ticket {$ticket.status|escape:'html':'UTF-8'}">
            <strong>{l s='Status' mod='ets_livechat'}: </strong>{if $ticket.status=='open'}{l s='Open' mod='ets_livechat'}{elseif $ticket.status=='close'}{l s='Closed' mod='ets_livechat'}{else}{l s='Canceled' mod='ets_livechat'}{/if}
        </div>
        <div class="lc_priority_ticket {Ets_livechat::displayPriority($ticket.priority)|escape:'html':'UTF-8'}" style="text-transform: capitalize;">
            <strong>{l s='Priority' mod='ets_livechat'}: </strong> {Ets_livechat::displayPriority($ticket.priority)|escape:'html':'UTF-8'}
        </div>
        <div class="lc_date_ticket">
            <strong>{l s='Date' mod='ets_livechat'}: </strong>{$ticket.date_add|escape:'html':'UTF-8'}
        </div>
        <div class="lc_rate_ticket">
            <strong>{l s='Rating' mod='ets_livechat'}:</strong>
            <div class="star_content">
                <div class="fa fa-star-o {if $ticket.rate && $ticket.rate>=1}fa-star{/if}" title="{l s='Terrible' mod='ets_livechat'}">&nbsp;</div>
                <div class="fa fa-star-o {if $ticket.rate && $ticket.rate>=2}fa-star{/if}" title="{l s='Acceptable' mod='ets_livechat'}">&nbsp;</div>
                <div class="fa fa-star-o {if $ticket.rate && $ticket.rate>=3}fa-star{/if}" title="{l s='Fairly Good' mod='ets_livechat'}">&nbsp;</div>
                <div class="fa fa-star-o {if $ticket.rate && $ticket.rate>=4}fa-star{/if}" title="{l s='Good' mod='ets_livechat'}">&nbsp;</div>
                <div class="fa fa-star-o {if $ticket.rate && $ticket.rate>=5}fa-star{/if}" title="{l s='Excellent' mod='ets_livechat'}">&nbsp;</div>
    		</div>
        </div>
        {if $departments}
            <div class="lc_transfer_ticket">
                <strong>{l s='Department' mod='ets_livechat'}:</strong> {$ticket.dertpartment_name|escape:'html':'UTF-8'} {if isset($ticket.employee_name) && $ticket.employee_name} ({l s='Assigned to' mod='ets_livechat'} &nbsp;{$ticket.employee_name|escape:'html':'UTF-8'}){/if}
            </div>
        {/if}
        <div class="lc_rate_button pull-right">
            <div class="btn-group pull-right status_{$ticket.status|escape:'html':'UTF-8'}">
                {if $ticket.status!='close'}
                    <a class="btn btn-default" href="{$link->getAdminLink('AdminLiveChatTickets')|escape:'html':'UTF-8'}&changestatus=close&viewticket&id_ticket={$ticket.id_message|intval}" title="{l s='Close' mod='ets_livechat'}">
                        <i class="icon icon-check"></i> {l s='Close' mod='ets_livechat'}
                    </a>
                {else}
                    <a class="btn btn-default" href="{$link->getAdminLink('AdminLiveChatTickets')|escape:'html':'UTF-8'}&changestatus=open&viewticket&id_ticket={$ticket.id_message|intval}" title="{l s='Reopen' mod='ets_livechat'}">
                        <i class="icon icon-reply"></i> {l s='Reopen' mod='ets_livechat'}
                    </a>
                {/if}
                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown"></button>
                <ul class="dropdown-menu">
                    {if $ticket.status=='close'}
                        <li>
                            <a class="btn btn-default" href="{$link->getAdminLink('AdminLiveChatTickets')|escape:'html':'UTF-8'}&changestatus=open&viewticket&id_ticket={$ticket.id_message|intval}" title="{l s='Reopen' mod='ets_livechat'}">
                                <i class="icon icon-reply"></i> {l s='Reopen' mod='ets_livechat'}
                            </a>
                        </li>
                    {/if}
                    {if $ticket.status!='cancel'}
                        <li>
                            <a class="btn btn-default" href="{$link->getAdminLink('AdminLiveChatTickets')|escape:'html':'UTF-8'}&changestatus=cancel&viewticket&id_ticket={$ticket.id_message|intval}" title="{l s='Cancel' mod='ets_livechat'}">
                                <i class="icon icon-remove"></i> {l s='Cancel' mod='ets_livechat'}
                            </a>
                        </li>
                    {/if}
                    <li>
                        <a class="btn btn-default change_priority" >
                            <i class="fa fa-thermometer-empty"></i> {l s='Change priority' mod='ets_livechat'}
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-default transfer_ticket">
                            <i class="fa fa-exchange"></i> {l s='Transfer ticket' mod='ets_livechat'}
                        </a>
                    </li>
                    <li>
                        <a title="{l s='Delete' mod='ets_livechat'}" onclick="return confirm('{l s='Do you want to delete this ticket?' mod='ets_livechat'}');" href="{$link->getAdminLink('AdminLiveChatTickets')|escape:'html':'UTF-8'}&deleteticket&id_ticket={$ticket.id_message|intval}">
                            <i class="icon-trash"></i> {l s='Delete' mod='ets_livechat'}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="lc_form_priority" style="display:none;">
            <form action="{$link->getAdminLink('AdminLiveChatTickets')|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data">
                <span class="lc_close" title="{l s='Close' mod='ets_livechat'}">{l s='Close' mod='ets_livechat'}</span>
                <h1>{l s='Change priority' mod='ets_livechat'}</h1>
                {if $ticket.priority}
                    {assign var='priority' value=$ticket.priority}
                {else}
                    {assign var='priority' value=$ticket.default_priority}
                {/if}
                <label for="ticket_priority">{l s='Priority' mod='ets_livechat'}</label>
                <select name="ticket_priority">
                    <option value="1" {if $priority==1} selected="selected"{/if}>{l s='Low' mod='ets_livechat'}</option>
                    <option value="2" {if $priority==2} selected="selected"{/if}>{l s='Medium' mod='ets_livechat'}</option>
                    <option value="3" {if $priority==3} selected="selected"{/if}> {l s='High' mod='ets_livechat'}</option>
                    <option value="4" {if $priority==4} selected="selected"{/if}>{l s='Urgent' mod='ets_livechat'}</option>
                </select>
                <input type="hidden" name="id_ticket" value="{$ticket.id_message|intval}" />
                <button class="btn btn-default" type="submit" name="change_priority">{l s='Change' mod='ets_livechat'}</button>
                <button style="float:right" class="btn btn-default cancel_change_priority" type="submit" name="cancel_change_priority">{l s='Cancel' mod='ets_livechat'}</button>
            </form>
        </div>
        <div class="lc_form_transfer_ticket" style="display:none;">
            <form action="{$link->getAdminLink('AdminLiveChatTickets')|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data">
                <span class="lc_close" title="{l s='Close' mod='ets_livechat'}">{l s='Close' mod='ets_livechat'}</span>
                <h3>{l s='Transfer ticket' mod='ets_livechat'}</h3>
                {if $departments}
                    <div class="form-group">
                        <label for="id_departments_ticket" class="departments">{l s='Department: ' mod='ets_livechat'}</label>
                        <select class="id_departments_ticket" id="id_departments_ticket" name="id_departments_ticket">
                            <option value="-1" >{l s='All departments' mod='ets_livechat'}</option>
                            {foreach from =$departments item='department'}
                                <option value="{$department.id_departments|intval}" {if $ticket.id_departments==$department.id_departments} selected="selected"{/if} class="{if $department.all_employees}all_employees{/if}">{$department.name|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                {/if}
                <div class="form-group">
                    <label for="id_employee_ticket" class="employee">
                        {l s='Assign to staff: ' mod='ets_livechat'}
                        <select id="id_employee_ticket" class="id_employee_ticket" name="id_employee_ticket"> 
                            <option value="-1">{l s='All staffs' mod='ets_livechat'}</option>
                            {foreach from= $employees item='employee'}
                                <option class="{if $employee.id_profile!=1}chonse_department{/if}{if $employee.departments}{foreach from= $employee.departments item='department'} department_{$department.id_departments|intval}{/foreach}{/if}" value="{$employee.id_employee|intval}" {if $ticket.id_employee== $employee.id_employee} selected="selected"{/if}> {if $employee.name}{$employee.name|escape:'html':'UTF-8'}{else}{$employee.firstname|escape:'html':'UTF-8'}&nbsp;{$employee.lastname|escape:'html':'UTF-8'}{/if}</option>
                            {/foreach}
                        </select>
                    </label>
                </div>
                <input type="hidden" name="id_ticket" value="{$ticket.id_message|intval}" />
                <button class="btn btn-default" type="submit" name="transfer_ticket">{l s='Transfer' mod='ets_livechat'}</button>
                <button class="btn btn-default cancel_transfer_ticket" type="submit" name="cancel_transfer_ticket">{l s='Cancel' mod='ets_livechat'}</button>
            </form>
        </div>
    </div>  
    <div class="lc_ticket_content">
        {if $fields}
            {assign var='is_contact_name' value=0}
            {foreach from=$fields item='field'}
                {if $field.value}
                    {if $field.type=='file'}
                        {if $field.id_download!=-1}
                            <p><strong>{$field.label|escape:'html':'UTF-8'}</strong>: <a target="_blank" href="{$field.link_download|escape:'html':'UTF-8'}">{$field.value|escape:'html':'UTF-8'}</a><span class="file_size"> ({$field.file_size|escape:'html':'UTF-8'} MB)</span></p>
                        {else}
                            <p><strong>{$field.label|escape:'html':'UTF-8'}</strong>: <b class="sent_file">{$field.value|escape:'html':'UTF-8'}</b> ({l s='File was sent to email' mod='ets_livechat'})</p>
                        {/if}
                    {else}
                        <p><strong>{$field.label|escape:'html':'UTF-8'}</strong>: <span class="lc_ticket_content_value">{if $field.is_contact_name && $ticket.id_customer && !$is_contact_name}<a href="{$link->getAdminLink('AdminCustomers')|escape:'html':'UTF-8'}&updatecustomer&id_customer={$ticket.id_customer|intval}">{/if}{$field.value|escape:'html':'UTF-8'}{if $field.is_contact_name && $ticket.id_customer && !$is_contact_name}</a> ({l s='Registered customer' mod='ets_livechat'}){/if}</span></p>
                        {if $field.is_contact_name && $ticket.id_customer}
                            {assign var='is_contact_name' value=1}
                        {/if}
                    {/if}
                {/if}
            {/foreach}
        {/if}
        {if $reply_customer}
            <div class="lc_ticket_message">
                <form action="{$link->getAdminLink('AdminLiveChatTickets')|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data">
                    <input type="hidden" value="{$ticket.id_message|intval}" name="id_ticket" />
                    <div class="lc_ticket_detail">
                        <ul class="ticket-list-messages">
                            {if $messages}
                                {foreach from=$messages item='msg'}
                                        <li class="{if $msg.id_employee}is_employee{else}is_customer{/if} lc_msg " data-id-message="{$msg.id_note|intval}">
                                            <div class="lc_sender">
                                                {if $msg.id_employee}
                                                    {if $msg.employee_avata}
                                                        <div class="avata{if $ETS_LC_AVATAR_IMAGE_TYPE=='square'} lc_avatar_square{/if}">
                                                            <img src="{$msg.employee_avata|escape:'html':'UTF-8'}" title="{$msg.employee_name|escape:'html':'utf-8'}" />
                                                            <span>{$msg.employee_name_hide|escape:'html':'utf-8'}</span>
                                                        </div>
                                                    {/if}
                                                    {if $msg.employee_name}
                                                        <span title="{$msg.employee_name|escape:'html':'utf-8'}">{$msg.employee_name|escape:'html':'utf-8'}</span>
                                                    {/if}
                                                {else}
                                                    {if $msg.customer_avata}
                                                        <div class="avata{if $ETS_LC_AVATAR_IMAGE_TYPE=='square'} lc_avatar_square{/if}">
                                                            <img src="{$msg.customer_avata|escape:'html':'UTF-8'}" title="{$msg.customer_name|escape:'html':'utf-8'}" />
                                                            <span>{$msg.customer_name_hide|escape:'html':'utf-8'}</span>
                                                        </div>
                                                    {/if}
                                                    {if $msg.customer_name}
                                                        <span title="{$msg.customer_name|escape:'html':'utf-8'}">{$msg.customer_name|escape:'html':'utf-8'}</span>
                                                    {/if}
                                                {/if}
                                            </div>
                                            <div class="lc_msg_content">{$msg.note nofilter}</div>
                                            <div class="lc_msg_time">{$msg.date_add nofilter}</div>
                                        </li>
                                {/foreach}
                            {/if}
                        </ul>
                        <div class="lc_note_message {if !$messages}nocomment{/if}">
                           <textarea id="ticket_note" class="ac_input" placeholder="{l s='Enter a message to reply' mod='ets_livechat'}" name="ticket_note" autocomplete="off"></textarea>
                           {if $form_class->save_staff_file || $form_class->send_mail_reply_customer}
                               <div class="form_upfile">
                                    <label for="ticket_file">{l s='Attachment (optional):' mod='ets_livechat'}</label>
                                    <input id="ticket_file" name="ticket_file" type="file" />
                               </div>
                           {/if}
                           <button class="lc_send_message_ticket btn btn-primary pull-right" name="lc_send_message_ticket">{l s='Send message' mod='ets_livechat'}</button>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </form>
            </div>
        {/if}
    </div>
</div>
<div class="lc_ticket_footer">
    <a class="btn btn-default" href="{$link->getAdminLink('AdminLiveChatTickets')|escape:'html':'UTF-8'}">
        <i class="fa fa-long-arrow-left"></i> {l s='Back' mod='ets_livechat'}
    </a>
</div>
