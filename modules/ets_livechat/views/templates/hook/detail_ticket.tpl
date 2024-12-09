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
<header class="page-header">
<h1> {l s='Ticket #' mod='ets_livechat'}{$ticket.id_message|intval} </h1>
</header>
<div class="lc_ticket_wapper">
    
    <div class="lc_ticket_header">
        <div class="lc_id_ticket">
            <strong><i class="fa fa-tags"></i> {l s='Ticket ID' mod='ets_livechat'}: </strong>#{$ticket.id_message|intval} 
        </div>
        <div class="lc_status_ticket {$ticket.status|escape:'html':'UTF-8'}">
            <strong><i class="fa fa-adjust"></i> {l s='Status' mod='ets_livechat'}: </strong>{if $ticket.status=='open'}{l s='Open' mod='ets_livechat'}{elseif $ticket.status=='close'}{l s='Closed' mod='ets_livechat'}{else}{l s='Canceled' mod='ets_livechat'}{/if}
        </div>
        <div class="lc_priority_ticket {Ets_livechat::displayPriority($ticket.priority)|escape:'html':'UTF-8'}" style="text-transform: capitalize;">
            <strong><i class="fa fa-thermometer-empty"></i> {l s='Priority' mod='ets_livechat'}: </strong> {Ets_livechat::displayPriority($ticket.priority)|escape:'html':'UTF-8'}
        </div>
        <div class="lc_date_ticket">
            <strong><i class="fa fa-calendar-o"></i> {l s='Date' mod='ets_livechat'}: </strong>{$ticket.date_add|escape:'html':'UTF-8'}
        </div>
        <div class="lc_rate_ticket">
            {l s='Rating' mod='ets_livechat'}:
            <div class="star_content">
    			<input class="star not_uniform" type="radio" name="criterion_ticket" value="1" title="{l s='Terrible' mod='ets_livechat'}" {if $ticket.rate &&  $ticket.rate==1}checked="checked"{/if} />
    			<input class="star not_uniform" type="radio" name="criterion_ticket" value="2" title="{l s='Acceptable' mod='ets_livechat'}" {if $ticket.rate && $ticket.rate==2}checked="checked"{/if}/>
    			<input class="star not_uniform" type="radio" name="criterion_ticket" value="3" title="{l s='Fairly Good' mod='ets_livechat'}" {if $ticket.rate && $ticket.rate==3}checked="checked"{/if}/>
    			<input class="star not_uniform" type="radio" name="criterion_ticket" value="4" title="{l s='Good' mod='ets_livechat'}" {if $ticket.rate && $ticket.rate==4}checked="checked"{/if} />
    			<input class="star not_uniform" type="radio" name="criterion_ticket" value="5" title="{l s='Excellent' mod='ets_livechat'}" {if $ticket.rate && $ticket.rate==5}checked="checked"{/if}/>
    		</div>
        </div>
    </div>  
    <div class="lc_ticket_content">
        {if $fields}
            {foreach from=$fields item='field'}
                {if $field.value}
                    {if $field.type=='file'}
                        {if $field.id_download!=-1}
                            <p><strong>{$field.label|escape:'html':'UTF-8'}</strong>: <a target="_blank" href="{$field.link_download|escape:'html':'UTF-8'}">{$field.value|escape:'html':'UTF-8'}</a><span class="file_size"> ({$field.file_size|escape:'html':'UTF-8'} MB)</span></p>
                        {else}
                            <p><strong>{$field.label|escape:'html':'UTF-8'}</strong>: <b class="sent_file">{$field.value|escape:'html':'UTF-8'}</b> ({l s='File was sent to email' mod='ets_livechat'})</p>
                        {/if}
                    {else}
                        <p><strong>{$field.label|escape:'html':'UTF-8'}</strong>: <span class="lc_ticket_content_value">{$field.value|escape:'html':'UTF-8'}</span></p>
                    {/if}
                {/if}
            {/foreach}
        {/if}
        <div class="lc_ticket_message">
            <form action="{$link->getModuleLink('ets_livechat','ticket')|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data">
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
                                                        {if $msg.employee_name}
                                                            <span title="{$msg.employee_name|escape:'html':'utf-8'}">{$msg.employee_name_hide|escape:'html':'utf-8'}</span>
                                                        {/if}
                                                    </div>
                                                {/if}
                                            {else}
                                                {if $msg.customer_avata}
                                                    <div class="avata{if $ETS_LC_AVATAR_IMAGE_TYPE=='square'} lc_avatar_square{/if}">
                                                        <img src="{$msg.customer_avata|escape:'html':'UTF-8'}" title="{$msg.customer_name|escape:'html':'utf-8'}" />
                                                        {if $msg.customer_name}
                                                            <span title="{$msg.customer_name|escape:'html':'utf-8'}">{$msg.customer_name_hide|escape:'html':'utf-8'}</span>
                                                        {/if}
                                                    </div>
                                                {/if}
                                                
                                            {/if}
                                        </div>
                                        <div class="lc_msg_content">{$msg.note nofilter}</div>
                                        <div class="lc_msg_time">{$msg.date_add nofilter}</div>
                                    </li>
                            {/foreach}
                        {/if}
                    </ul>
                    <div class="lc_note_message{if !$messages} nocomment{/if}">
                       <textarea id="ticket_note" class="ac_input" placeholder="{l s='Enter a message to reply' mod='ets_livechat'}" name="ticket_note" autocomplete="off"></textarea>
                       {if $form_class->customer_reply_upload_file && ($form_class->getEmailAdminInfo() || $form_class->save_customer_file)}
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
    </div>
</div>
<div class="lc_ticket_footer">
        <a class="btn btn-default" href="{$link->getModuleLink('ets_livechat','ticket')|escape:'html':'UTF-8'}">
        <i class="fa fa-long-arrow-left"></i> {l s='Back' mod='ets_livechat'}</a>
    </div>