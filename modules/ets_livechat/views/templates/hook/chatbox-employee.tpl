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
<div class="lc_chatbox_employe{if $chatbox_closed} lc_chatbox_closed{/if} chatbox_employe_{$conversation->id|intval}{if count($messages)<$config.ETS_LC_MSG_COUNT} loaded{/if}{if !$config.ETS_LC_DISPLAY_AVATA} no_display_avata{/if}{if !$config.ETS_LC_DISPLAY_TIME} no_display_datetime{/if}{if $end_chat} end_chat{/if}{if $waiting_acceptance} waiting_acceptance{/if}{if $has_changed} has_changed{/if}{if $wait_accept} wait_accept{/if}">
    <div class="list_star_fly">
        <div class="star_item star_1 star_size_big">
            <div class="list_star_item">
                <i class="icon-star" aria-hidden="true"></i>
                <i class="icon-star star_fly_delay04" aria-hidden="true"></i>
                <i class="icon-star star_fly_delay015" aria-hidden="true"></i>
                
                <i class="icon-star star_fly_delay055" aria-hidden="true"></i>
            </div>
        </div>
        <div class="star_item star_2 star_size_big star_opacity_5">
            <div class="list_star_item">
                <i class="icon-star star_fly_delay04" aria-hidden="true"></i>
                <i class="icon-star" aria-hidden="true"></i>
                
                <i class="icon-star star_fly_delay04" aria-hidden="true"></i>
                <i class="icon-star" aria-hidden="true"></i>
            </div>
        </div>
        <div class="star_item star_3 star_size_2">
            <div class="list_star_item">
                <i class="icon-star star_fly_delay025" aria-hidden="true"></i>
                <i class="icon-star" aria-hidden="true"></i>
                <i class="icon-star star_fly_delay08" aria-hidden="true"></i>
                
                <i class="icon-star" aria-hidden="true"></i>
            </div>
        </div>
        <div class="star_item star_4 star_size_3 star_opacity_2">
            <div class="list_star_item">
                <i class="icon-star star_fly_delay025" aria-hidden="true"></i>
                <i class="icon-star" aria-hidden="true"></i>
                <i class="icon-star star_fly_delay04" aria-hidden="true"></i>
                <i class="icon-star" aria-hidden="true"></i>
                
            </div>
        </div>
        <div class="star_item star_5 star_size_1 star_opacity_8">
            <div class="list_star_item">
                
                <i class="icon-star star_fly_delay04" aria-hidden="true"></i>
                <i class="icon-star" aria-hidden="true"></i>
                <i class="icon-star star_fly_delay07" aria-hidden="true"></i>
                <i class="icon-star star_fly_delay08" aria-hidden="true"></i>
            </div>
        </div>
    </div>
    <form  method="post" action="{$ajaxUrl|escape:'html':'utf-8'}" class="lc_chatbox_form {if $isCustomerOnline}customer_online {else}customer_offline{/if}" enctype="multipart/form-data">
        <div class="content_mesage">
            <div class="lc_heading lc_heading_online">
                <div class="lc_online_heading" title="{l s='Online' mod='ets_livechat'}"><span title="{l s='Online' mod='ets_livechat'}"></span>{if $customer_name}{$customer_name|escape:'html':'UTF-8'}{else}{l s='Chat ID #' mod='ets_livechat'}{$conversation->id|intval}{/if}</div>
                <span class="lc_heading_count_message_not_seen {if $count_message_not_seen}show{/if}">{$count_message_not_seen|intval}</span>
                <span class="lc_close"  title="{l s='Close' mod='ets_livechat'}">{l s='Close' mod='ets_livechat'}</span>
                {if $config.ETS_LC_ALLOW_MAXIMIZE}<span  class="lc_maximize" title="{l s='Maximize' mod='ets_livechat'}">{l s='Maximize' mod='ets_livechat'}</span>{/if}
                <span  class="lc_minimize" title="{if $chatbox_closed}{l s='Show this chat' mod='ets_livechat'}{else}{l s='Hide this chat' mod='ets_livechat'}{/if}">{if $chatbox_closed}{l s='Show this chat' mod='ets_livechat'}{else}{l s='Hide this chat' mod='ets_livechat'}{/if}</span>
                <span class="lc_setting_customer"  title="{l s='Setting' mod='ets_livechat'}">{l s='Setting' mod='ets_livechat'}</span>
                <span {if $end_chat}style="display:none;"{/if} class="cl_end_chat"  title="{l s='End chat' mod='ets_livechat'}">{l s='End chat' mod='ets_livechat'}</span>
                {if $customer_name||$customer_phone||$customer_email}<span class="lc_togger_customer_info"  title="{l s='Customer info' mod='ets_livechat'}">{l s='Customer info' mod='ets_livechat'}</span>{/if}
            </div>
            <div class="lc_heading lc_heading_offline">
                <div class="lc_offline_heading" title="{l s='Offline' mod='ets_livechat'}"><span title="{l s='Offline' mod='ets_livechat'}"></span>{if $customer_name}{$customer_name|escape:'html':'UTF-8'}{else}{l s='Chat ID #' mod='ets_livechat'}{$conversation->id|intval}{/if}</div>
                <span class="lc_heading_count_message_not_seen {if $count_message_not_seen}show{/if}">{$count_message_not_seen|intval}</span>
                <span class="lc_close"  title="{l s='Close' mod='ets_livechat'}">{l s='Close' mod='ets_livechat'}</span>
                {if $config.ETS_LC_ALLOW_MAXIMIZE}<span class="lc_maximize"  title="{l s='Maximize' mod='ets_livechat'}">{l s='Maximize' mod='ets_livechat'}</span>{/if}
                <span class="lc_minimize"  title="{if $chatbox_closed}{l s='Show this chat' mod='ets_livechat'}{else}{l s='Hide this chat' mod='ets_livechat'}{/if}">{if $chatbox_closed}{l s='Show this chat' mod='ets_livechat'}{else}{l s='Hide this chat' mod='ets_livechat'}{/if}</span>
                <span class="lc_setting_customer"  title="{l s='Setting' mod='ets_livechat'}">{l s='Setting' mod='ets_livechat'}</span>
                <span {if $end_chat}style="display:none;"{/if} class="cl_end_chat"  title="{l s='End chat' mod='ets_livechat'}">{l s='End chat' mod='ets_livechat'}</span>
                {if $customer_name||$customer_phone||$customer_email}<span class="lc_togger_customer_info"  title="{l s='Customer info' mod='ets_livechat'}">{l s='Customer info' mod='ets_livechat'}</span>{/if}
            </div>
            <div class="lg_status_group {if $conversation->blocked==1 || $conversation->captcha_enabled==1}show{/if}">
                <span class="changed_satatusblock {if $conversation->blocked==1}enabled{else}disabled{/if}"  title="{if $conversation->blocked==1}{l s='Blocked' mod='ets_livechat'}{else}{l s='Block' mod='ets_livechat'}{/if}" data-status="{$conversation->blocked|intval}" >{if $conversation->blocked==1}{l s='Blocked' mod='ets_livechat'}{else}{l s='Block' mod='ets_livechat'}{/if}</span>
                <span class="changed_satatuscaptcha {if $conversation->captcha_enabled==1}enabled{else}disabled{/if}"  title="{l s='Require customer to fill in captcha code' mod='ets_livechat'}" data-status="{$conversation->captcha_enabled|intval}" >{if $conversation->captcha_enabled==1}{l s='Captcha' mod='ets_livechat'}{else}{l s='Captcha' mod='ets_livechat'}{/if}</span>
                {*if $config.ETS_LC_USE_GOOGLE_MAP*}
                    <span class="livechat_map" title="{l s='Location' mod='ets_livechat'}">
                        <span class="view_map" title="{l s='Location' mod='ets_livechat'}">
                        <a target="_blank" href="https://www.infobyip.com/ip-{$conversation->latest_ip|escape:'html':'utf-8'}.html" title="{l s='Location' mod='ets_livechat'}">{l s='Location' mod='ets_livechat'}</a></span>
                    </span>
                {*/if*}
                <span class="delete_conversation"  title="{l s='Delete' mod='ets_livechat'}">{l s='Delete' mod='ets_livechat'}</span>
                {if (isset($departments) && $departments && $ETS_LIVECHAT_ADMIN_DE) || (isset($employees) && $employees && $config.ETS_LC_STAFF_ACCEPT)}
                    <div class="lg_group_departments">
                        {if isset($departments) && $departments && $ETS_LIVECHAT_ADMIN_DE}
                            <label for="id_departments_{$conversation->id|intval}" class="departments">
                                {l s='Department: ' mod='ets_livechat'}
                                <select class="id_departments" id="id_departments_{$conversation->id|intval}" data-id="{$conversation->id|intval}">
                                    <option value="-1" {if $conversation->id_departments_wait}{if $conversation->id_departments_wait==-1}selected="selected"{/if}{else}{if $conversation->id_departments==-1}selected="selected"{/if}{/if} >{l s='All departments' mod='ets_livechat'}</option>
                                    {foreach from =$departments item='department'}
                                        <option value="{$department.id_departments|intval}" {if $conversation->id_departments_wait}{if $conversation->id_departments_wait==$department.id_departments} selected="selected"{/if}{else}{if $department.id_departments==$conversation->id_departments}selected="selected"{/if}{/if} class="{if $department.all_employees}all_employees{/if}">{$department.name|escape:'html':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </label>
                        {/if}
                        {if isset($employees) && $employees && $config.ETS_LC_STAFF_ACCEPT}
                            <label for="id_employee_{$conversation->id|intval}" class="employee">
                                {l s='Employee: ' mod='ets_livechat'}
                                <select id="id_employee_{$conversation->id|intval}" class="id_employee">
                                    <option value="-1" {if $conversation->id_employee_wait}{if $conversation->id_employee_wait==-1} selected="selected" {/if} {else}{if $conversation->id_employee==-1} selected="selected"{/if}{/if}>{l s='All employees' mod='ets_livechat'}</option>
                                    {foreach from= $employees item='employee'}
                                        <option class="{if $employee.id_profile!=1}chonse_department{/if}{if $employee.departments}{foreach from= $employee.departments item='department'} department_{$department.id_departments|intval}{/foreach}{/if}" value="{$employee.id_employee|intval}" {if $conversation->id_employee_wait}{if $employee.id_employee==$conversation->id_employee_wait} selected="selected"{/if}{else}{if $conversation->id_employee==$employee.id_employee} selected="selected"{/if}{/if}>{if $employee.name}{$employee.name|escape:'html':'UTF-8'}{else}{$employee.firstname|escape:'html':'UTF-8'}&nbsp;{$employee.lastname|escape:'html':'UTF-8'}{/if}</option>
                                    {/foreach}
                                </select>
                            </label>
                        {/if}
                        <span class="change_department">{l s='Transfer chat' mod='ets_livechat'}</span>
                    </div>
                {/if}
            </div>
            <div class="lc_customer_info">
                <div class="info_show_not_expand">
                    {if $customer_name}
                        <span><b>{l s='Name' mod='ets_livechat'}: </b>{$customer_name|escape:'html':'utf-8'}</span>
                    {/if}
                    {if $customer_phone}
                        <span><b>{l s='Phone number' mod='ets_livechat'}: </b><a href="tel:{$customer_phone|escape:'html':'utf-8'}">{$customer_phone|escape:'html':'utf-8'}</a></span>
                    {/if}
                    {if $customer_email}
                        <span><b>{l s='Email' mod='ets_livechat'}: </b><a href="mailto:{$customer_email|escape:'html':'utf-8'}" target="_top">{$customer_email|escape:'html':'utf-8'}</a></span>
                    {/if}
                    {if $conversation->id_customer}
                        <a class="view_customer" target="_blank" href="{$link_customer|escape:'html':'utf-8'}" title="{l s='View customer details' mod='ets_livechat'}" >{l s='View customer details' mod='ets_livechat'}</a>
                    {/if}
                </div>
                <div class="info_show_expand">
                    <h4 class="title_expand"><i class="fa fa-info-circle"></i> {l s='User Information' mod='ets_livechat'}</h4>
                    <a class="btn btn-primary customer_info_expand view_location" href="https://www.infobyip.com/ip-{$conversation->latest_ip|escape:'html':'utf-8'}.html">
                        {l s='View location' mod='ets_livechat'}
                    </a>
                    <div class="expand-admin" title="{l s='Close' mod='ets_livechat'}"><span title="Expand">{l s='Close' mod='ets_livechat'}</span></div>
                    <div class="info_show_avatar">
                        <div class="info_show_aimg">
                            <img src="{$customer_avata|escape:'html':'UTF-8'}" title="{$customer_name|escape:'html':'UTF-8'}" />
                        </div>
                        {if $customer_name}
                            <span class="name">
                                {if $conversation->id_customer}<a href="{$link_customer|escape:'html':'utf-8'}">{/if}
                                        {$customer_name|escape:'html':'utf-8'}
                                {if $conversation->id_customer} <span class="name_rgt">({l s='Registered customer' mod='ets_livechat'})</span></a>{/if}
                            </span>
                        {/if}
                        {if $customer_email}
                            <span class="mail"><i class="fa fa-envelope-o" aria-hidden="true"></i><a href="mailto:{$customer_email|escape:'html':'utf-8'}" target="_top">{$customer_email|escape:'html':'utf-8'}</a></span>
                        {/if}
                        {if $customer_phone}
                            <span class="phone"><i class="fa fa-phone" aria-hidden="true"></i><a href="tel:{$customer_phone|escape:'html':'utf-8'}">{$customer_phone|escape:'html':'utf-8'}</a></span>
                        {/if}
                        {if $customer_email}
                            <span class="lc_send_mail_button" title="{l s='Send email' mod='ets_livechat'}">{l s='Send email' mod='ets_livechat'}</span>
                        {/if}
                    </div>
                    {if $conversation->id_employee}
                        <div class="lc_staff_accepted">
                            {$accept_employee->firstname|escape:'html':'UTF-8'}&nbsp;{$accept_employee->lastname|escape:'html':'UTF-8'}{l s=' accepted chat at: ' mod='ets_livechat'}
                            {$date_accept|escape:'html':'UTF-8'}
                        </div>
                    {/if}
                    <div class="info_show_note">
                        <label>{l s='Note:' mod='ets_livechat'}</label>
                        <textarea placeholder="{l s='Add a note for this customer' mod='ets_livechat'}" class="conversation_note" data-id="{$conversation->id|intval}">{if $conversation->note}{$conversation->note|escape:'html':'UTF-8'}{/if}</textarea>
                    </div>
                    <div class="info_show_address">
                        <div class="browser_name customer_info_expand {Ets_livechat::getBrowserInfo($conversation->browser_name)|escape:'html':'UTF-8'}">
                            <strong>{l s='Web browser:' mod='ets_livechat'}&nbsp;</strong>
                            {if $conversation->browser_name}{$conversation->browser_name|escape:'html':'utf-8'}
                            {else}
                            {l s='Unknown' mod='ets_livechat'}
                            {/if}
                        </div>
                        {if $conversation->http_referer}
                            <div class="user_agent customer_info_expand">
                                <strong>{l s='User Agent' mod='ets_livechat'}:</strong> 
                                {$conversation->http_referer|escape:'html':'UTF-8'}
                            </div>
                        {/if}
                        {if $conversation->current_url}
                            <div class="online_path customer_info_expand">
                                <strong>{l s='Online path' mod='ets_livechat'}:</strong> 
                                <a href="{$conversation->current_url|escape:'html':'UTF-8'}" target="_blank" title="{$conversation->current_url|escape:'html':'UTF-8'}">{$conversation->current_url|escape:'html':'UTF-8'}</a>
                            </div>
                        {/if}
                        <div class="ip_address">
                            <strong>{l s='IP address:' mod='ets_livechat'}</strong> <a title="{l s='View loaction' mod='ets_livechat'}" href="https://www.infobyip.com/ip-{$conversation->latest_ip|escape:'html':'UTF-8'}.html">{$conversation->latest_ip|escape:'html':'UTF-8'}</a>
                        </div>
                        <div class="change_to_ticket">
                            {if $conversation->id_ticket}
                                <span class="ticket-conversation">
                                    {l s='Ticket' mod='ets_livechat'} <a href="{$link_ticket|escape:'html':'UTF-8'}&viewticket&id_ticket={$conversation->id_ticket|intval}">#{$conversation->ticket->subject|escape:'html':'UTF-8'}</a> {l s=' was created from this chat' mod='ets_livechat'}
                                </span>
                            {else}
                                <span class="button btn btn_change_to_ticket">{l s='Create ticket from chat' mod='ets_livechat'}</span>
                            {/if}
                        </div>
                    </div>
                    <div class="made_message_rate_box {if !$config.ETS_LC_DISPLAY_RATING}no_display_rating{/if} {if !$pre_made_messages || !$config.ETS_ENABLE_PRE_MADE_MESSAGE}no_display_pre_made_message{/if} {if !$conversation->rating}no_display_rate{/if}">
                        {if $config.ETS_LC_DISPLAY_RATING}
                            <div class="criterions_livechat {if !$conversation->rating}lc_hide{/if}" >
                                <label>{l s='Rating:' mod='ets_livechat'}</label>
                        		<div class="star_content">
                                    <div class="star {if $conversation && $conversation->rating>=1}star_on{/if}"><span class="lc_tooltip">{l s='Terrible' mod='ets_livechat'}</span></div>
                                    <div class="star {if $conversation && $conversation->rating>=2}star_on{/if}"><span class="lc_tooltip">{l s='Acceptable' mod='ets_livechat'}</span></div>
                                    <div class="star {if $conversation && $conversation->rating>=3}star_on{/if}"><span class="lc_tooltip">{l s='Fairly Good' mod='ets_livechat'}</span></div>
                                    <div class="star {if $conversation && $conversation->rating>=4}star_on{/if}"><span class="lc_tooltip">{l s='Good' mod='ets_livechat'}</span></div>
                                    <div class="star {if $conversation && $conversation->rating>=5}star_on{/if}"><span class="lc_tooltip">{l s='Excellent' mod='ets_livechat'}</span></div>
                        		</div>
                                {if $conversation && $conversation->rating==1}
                                    <span class="star_rate"> ({l s='Terrible' mod='ets_livechat'})</span>
                                {else if $conversation && $conversation->rating==2}
                                    <span class="star_rate"> ({l s='Acceptable' mod='ets_livechat'})</span>
                                {else if $conversation && $conversation->rating==3}
                                    <span class="star_rate"> ({l s='Fairly Good' mod='ets_livechat'})</span>
                                {else if $conversation && $conversation->rating==4}
                                    <span class="star_rate"> ({l s='Good' mod='ets_livechat'})</span>
                                {else if $conversation && $conversation->rating==5}
                                    <span class="star_rate"> ({l s='Excellent' mod='ets_livechat'})</span>
                                {/if}
                                <input name="customer_rated" value="{$conversation->rating|intval}"/>
                        		<div class="clearfix"></div>
                            </div>
                        {/if}
                    </div>
                    {if $history_chat}
                        <div class="conversation-history">
                            {$history_chat nofilter}
                        </div>
                    {/if}
                </div>
            </div>
            <div class="lc_messages" >
                <div class="massage_more_loading" style="display:none;">
                    {l s='Loading...' mod='ets_livechat'}
                </div>
                <div class="more_load">{l s='Load more' mod='ets_livechat'}</div>
                <ul class="lc_msg_board">
                    {if $messages}
                        {foreach from=$messages item='msg'}
                            <li class="{if $msg.id_employee}is_employee {if $msg.employee_name}has_name_emplode{/if} {else}is_customer {if $msg.customer_name}has_name_customer{/if}{/if} lc_msg" data-id-message="{$msg.id_message|intval}">
                                <div class="lc_sender">
                                    {if $msg.id_employee}
                                        {if $msg.employee_avata}
                                            <div class="avata{if $config.ETS_LC_AVATAR_IMAGE_TYPE=='square'} lc_avatar_square{/if}">
                                                <img src="{$msg.employee_avata|escape:'html':'UTF-8'}" title="{$msg.employee_name|escape:'html':'utf-8'}" />
                                            </div>
                                        {/if}
                                        {if $msg.employee_name}
                                            <span title="{$msg.employee_name|escape:'html':'utf-8'}">{$msg.employee_name|escape:'html':'utf-8'}</span>
                                        {/if}
                                    {else}
                                        {if $msg.customer_avata}
                                            <div class="avata{if $config.ETS_LC_AVATAR_IMAGE_TYPE=='square'} lc_avatar_square{/if}">
                                                <img src="{$msg.customer_avata|escape:'html':'UTF-8'}" title="{$msg.customer_name|escape:'html':'utf-8'}" />
                                            </div>
                                        {/if}
                                        {if $msg.customer_name}
                                            <span title="{$msg.customer_name|escape:'html':'utf-8'}">{$msg.customer_name|escape:'html':'utf-8'}</span>
                                        {/if}
                                    {/if}
                                </div>
                                {if $config.ETS_LC_DISPLAY_TIME}
                                    <div class="lc_msg_time">{$msg.datetime_added nofilter}</div>
                                {/if}
                                <div class="lc_msg_content">{$msg.message nofilter}</div>
                                {if $msg.id_employee && ($config.ETS_LC_ENABLE_EDIT_MESSAGE||$config.ETS_LC_ENABLE_DELETE_MESSAGE)}
                                    <div class="lc_action_message">
                                        {if $config.ETS_LC_ENABLE_DELETE_MESSAGE}
                                            <span title="{l s='Delete' mod='ets_livechat'}" class="employee_delete_message" data-id-message="{$msg.id_message|intval}">{l s='Delete' mod='ets_livechat'}</span>
                                        {/if}
                                        {if $config.ETS_LC_ENABLE_EDIT_MESSAGE}
                                            <span title="{l s='Edit' mod='ets_livechat'}" class="employee_edit_message" data-id-message="{$msg.id_message|intval}">{l s='Edit' mod='ets_livechat'}</span>
                                        {/if}
                                    </div>
                                {/if}
                                {if $msg.edited}
                                    <div class="lc_msg_edited">{if $config.ETS_LC_DISPLAY_TIME}{l s='Edited at:' mod='ets_livechat'} {$msg.datetime_edited|escape:'html':'utf-8'}{else}{l s='Edited' mod='ets_livechat'}{/if}</div>
                                {/if}
                            </li>
                        {/foreach}
                    {/if}
                </ul>
                <div class="lc_error lc_hide">
                </div>
                <ul class="message_status" {if !$lastMessageIsEmployee || !$isRequestAjax || $end_chat || $isCustomerWriting}style="display:none;"{/if}>
                    {if $isCustomerSeen}
                        <li {if $lastMessageIsEmployee && $isRequestAjax && !$end_chat && !$isCustomerWriting}class="show"{/if}><span class="seen_customer show">{l s='Seen' mod='ets_livechat'}</span></li>
                    {else}
                        {if $isCustomerDelivered}
                            <li class="show"><span class="delivered_customer show">{l s='Delivered' mod='ets_livechat'}</span></li>
                        {else}
                            {if $isCustomerSent}
                                <li class="show"><span class="sent_customer show">{l s='Sent' mod='ets_livechat'}</span></li>
                            {/if}
                        {/if}
                    {/if}
                    <li class="seen_customer"><span >{l s='Seen' mod='ets_livechat'}</span></li>
                    <li class="delivered_customer"><span>{l s='Delivered' mod='ets_livechat'}</span></li>
                    <li class="sent_customer"><span>{l s='Sent' mod='ets_livechat'}</span></li>
                </ul>
                <ul class="lc_message_end_chat" {if $isRequestAjax && !$end_chat}style="display:none;"{/if} >
                    <li class="employee_end" {*if !$end_chat}style="display:none;"{/if*} style="display:none;" >
                        {$end_chat|escape:'html':'UTF-8'}
                    </li>
                    <li class="customer_end" {if $end_chat || $isRequestAjax}style="display:none;"{/if}>
                        {l s='Chat paused.' mod='ets_livechat'}
                    </li>
                </ul>
                {if Ets_livechat::checkSupperAdminDecline($conversation)}
                    <div class="conversation-decline">{l s='You declined this chat' mod='ets_livechat'}</div>
                {/if}
                <span class="writing_customer {if $isCustomerWriting && !$end_chat && $isRequestAjax}show{/if}">{$customer_name|escape:'html':'utf-8'} {l s='is writing' mod='ets_livechat'}&nbsp;<span></span><span></span><span></span></span>
            </div>
        </div>
        <div class="block_waiting_acceptance">
            {l s='You have transferred this chat to' mod='ets_livechat'}&nbsp;<span class="staff_name">{$waiting_acceptance|escape:'html':'UTF-8'}</span>
            <a href="#" class="cancel_acceptance" data-id="{$conversation->id|intval}" >{l s='Click here to cancel the transfer' mod='ets_livechat'}</a>
        </div>
        {if $config.ETS_LC_STAFF_ACCEPT && !$end_chat}
            <div class="accept_conversation">
                {if $has_changed}
                    <p class="alert alert-warning">{$has_changed|escape:'html':'UTF-8'}{l s=' has tranferred this chat to you' mod='ets_livechat'}</p>
                {/if}
                <button class="btn btn-primary accept_submit" data-id="{$conversation->id|intval}">{l s='Accept' mod='ets_livechat'}</button>
                <button class="btn btn-primary decline_submit" data-id="{$conversation->id|intval}">{l s='Decline' mod='ets_livechat'}</button>
            </div>
        {/if}
        <div class="lc_text_area {if !$config.ETS_LC_ENABLE_EMOTION_ICON || !$emotions}no_display_emotion{/if} {if $config.ETS_DISPLAY_SEND_BUTTON}show_send_box{/if}">
            <input class="message_delivered" id="message_delivered" value="1" name="message_delivered" type="hidden"/>
            <input class="message_seen" id="message_seen" value="0" name="message_seen" type="hidden"/>
            <input class="message_writing" id="message_writing" value="0" name="message_writing" type="hidden"/>
            <input type="hidden" name="id_conversation" value="{$conversation->id|intval}" />
            <input type="hidden" name="id_message" value=""/>
            <input name="send_message" value="1" type="hidden"/>
            <input name="id_message" value="" type="hidden" />
            <textarea name="lc_message_old" id="lc_message_old" style="display:none;"></textarea>
            <div class="form_upfile">
                <input type="file" name="message_file"  />
            </div>
            <textarea id="type_message_{$conversation->id|intval}" class="" placeholder="{l s='Type a message' mod='ets_livechat'}" class="form-control" name="message"></textarea>
            <div class="lc_send_box"{if $config.LC_BACKGROUD_COLOR_BUTTON} style="background:{$config.LC_BACKGROUD_COLOR_BUTTON|escape:'html':'utf-8'}"{/if} >
                <input class="btn btn-primary lc_send" name="lc_send" type="submit" value="{$config.ETS_LC_TEXT_SEND|escape:'html':'utf-8'}" />
            </div>
            {if $config.ETS_LC_ENABLE_EMOTION_ICON && $emotions}
                <div class="lc_emotion">
                    <ul>
                        {foreach from=$emotions key='name' item='icon'}
                            <li data-emotion="{$name|escape:'html':'utf-8'}" title="{$icon.title|escape:'html':'utf-8'}"><img alt="{$icon.title|escape:'html':'utf-8'}" src="{$livechatDir|escape:'html':'utf-8'}views/img/emotions/{$icon.img|escape:'html':'utf-8'}"/></li>
                        {/foreach}
                    </ul>
                </div> 
            {/if}
            {if $customer_email && $config.ETS_LC_SEND_MESSAGE_TO_MAIL}
                <div class="box_send_mail">
                    <input type="checkbox" name="send_message_to_mail" value="1" id="send_message_to_mail_{$conversation->id|intval}" />
                    <label for="send_message_to_mail_{$conversation->id|intval}">{l s='Send this message to customer email' mod='ets_livechat'}</label>
                </div>
            {/if}
            <div class="made_message_rate_box {if !$config.ETS_LC_DISPLAY_RATING}no_display_rating{/if} {if !$pre_made_messages || !$config.ETS_ENABLE_PRE_MADE_MESSAGE}no_display_pre_made_message{/if} {if !$conversation->rating}no_display_rate{/if}">
                {if $config.ETS_LC_DISPLAY_RATING}
                    <div class="criterions_livechat {if !$conversation->rating}lc_hide{/if}" >
                        <label>{l s='Rating:' mod='ets_livechat'}</label>
                		<div class="star_content">
                            <div class="star {if $conversation && $conversation->rating>=1}star_on{/if}"><span class="lc_tooltip">{l s='Terrible' mod='ets_livechat'}</span></div>
                            <div class="star {if $conversation && $conversation->rating>=2}star_on{/if}"><span class="lc_tooltip">{l s='Acceptable' mod='ets_livechat'}</span></div>
                            <div class="star {if $conversation && $conversation->rating>=3}star_on{/if}"><span class="lc_tooltip">{l s='Fairly Good' mod='ets_livechat'}</span></div>
                            <div class="star {if $conversation && $conversation->rating>=4}star_on{/if}"><span class="lc_tooltip">{l s='Good' mod='ets_livechat'}</span></div>
                            <div class="star {if $conversation && $conversation->rating>=5}star_on{/if}"><span class="lc_tooltip">{l s='Excellent' mod='ets_livechat'}</span></div>
                		</div>
                        <input name="customer_rated" value="{$conversation->rating|intval}"/>
                		<div class="clearfix"></div>
                    </div>
                {/if}
                {*if $pre_made_messages && $config.ETS_ENABLE_PRE_MADE_MESSAGE}
                    <script type="text/javascript">
                        $('#type_message_{$conversation->id|intval}').autocomplete(ets_ajax_message_url,{literal}{
                    		minChars: 1,
                    		autoFill: true,
                    		max:20,
                    		matchContains: true,
                    		mustMatch:false,
                    		scroll:false,
                    		cacheLength:0,
                    		formatItem: function(item) {
                    			return item[0];
                    		}
                    	});
                        var ybcAddPost{/literal}{$conversation->id|intval}{literal} = function(event,data,formatted)
                        {
                            if(data[0])
                        	   {/literal}$('#type_message_{$conversation->id|intval}{literal}').val(data[0]);
                            else
                                return true;   
                        }
                        {/literal}
                    </script>
                {/if*}
            </div>
        </div>
        {if !$conversation->id_ticket}
            <div class="lc_form_change_to_ticket_popup bootstrap" style="display:none;">
                 <div class="pop_table">
                        <div class="pop_table_cell">
                            <div class="lc_form_change_to_ticket">
                                <span class="lc_close close_ticket" title="{l s='Close' mod='ets_livechat'}">{l s='Close' mod='ets_livechat'}</span>
                                <h3>{l s='Create ticket from chat' mod='ets_livechat'}</h3>
                                {$form_ticket nofilter}
                            </div>
                        </div>
                 </div>
            </div>
        {/if}
    </form>
    {if $customer_email}
        <div class="lc_send_mail_form_wapper">
            <div class="lc_send_mail_form_block">
                <div class="lc_send_mail_form_content">
                    <span class="lc_close_form_mail">{l s='Close' mod='ets_livechat'}</span>
                    <form id="form_mail" class="lc_send_mail_form" action="" method="post" class="defaultForm" enctype="multipart/form-data">
                        <input id="mail_conversation_id" value="{$conversation->id|intval}" name="mail_conversation_id" type="hidden" />
                        <div  class="panel">
                            <div class="panel-heading">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                {l s='Send email to' mod='ets_livechat'}&nbsp;{$customer_name|escape:'html':'UTF-8'}
                                ({$customer_email|escape:'html':'utf-8'})
                            </div>
                            <div class="form-wrapper">
                                <div class="form-group ">
                                    <label class="control-label col-lg-3 required"> {l s='Title' mod='ets_livechat'} </label>
                                    <div class="col-lg-9">
                                        <input type="text" name="title_mail" id="title_mail" value="" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3 required"> {l s='Message' mod='ets_livechat'} </label>
                                    <div class="col-lg-9">
                                        <textarea name="content_mail" id="content_mail"></textarea>
                                    </div>
                                </div>
                                <input name='submitSendMail' value="1" type="hidden"  />
                            </div>
                            <div class="panel-footer">
                                <button class="module_form_mail_cancel_btn" class="btn btn-default pull-left" type="button" value="1" name="cancelSendMail">
                                    <i class="process-icon-cancel"></i>
                                    {l s='Cancel' mod='ets_livechat'}
                                </button>
                                <button class="module_form_mail_submit_btn" class="btn btn-default pull-right" type="submit" value="1" name="submitSendMail">
                                    <i class="process-icon-save"></i>
                                    {l s='Send' mod='ets_livechat'}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    {/if}
    {if $config.LC_BACKGROUD_HOVER_BUTTON}
        {literal}
        <style>
        .lc_send_box:hover{
        {/literal}
                background :{$config.LC_BACKGROUD_HOVER_BUTTON|escape:'html':'UTF-8'}!important;
         {literal}
            }
         {/literal}
        </style>
    {/if}
</div>
