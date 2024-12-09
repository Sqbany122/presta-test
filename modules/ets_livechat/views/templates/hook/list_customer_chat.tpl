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
{if !$refresh}
<audio id="lg_ets_sound" src="{$livechatDir|escape:'quotes':'UTF-8'}/views/sound/{$config.ETS_SOUND_WHEN_NEW_MESSAGE|escape:'html':'utf-8'}.mp3" type="audio/mpeg"></audio>
<script type="text/javascript">
{if $status_employee=='offline'}
    $('body').addClass('lc_chatbox_backend_offline');
{/if}
</script>
<div class="lc_list_customer_chat {if $isRTL}lc_chatbox_rtl {/if} list_customer_active {if $status_employee=='do_not_disturb'}do_not_disturb{/if}{if $status_employee=='invisible'}invisible{/if}{if $status_employee=='offline'}offline{/if}{if $status_employee=='online' || $status_employee=='foce_online'}online{/if} lc_{$config.ETS_CONVERSATION_LIST_TYPE|escape:'html':'UTF-8'} {if !$ETS_CONVERSATION_DISPLAY_ADMIN}lc_left_hide{/if} lc_{$config.ETS_CLOSE_CHAT_BOX_BACKEND_TYPE|escape:'html':'utf-8'}{if $id_profile!=1} lc_hide_admin_setting{/if}" >
    <div class="lc_admin_info">
        <div{if !$ETS_CONVERSATION_DISPLAY_ADMIN} title="{l s='Open' mod='ets_livechat'}"{/if} class="lc_heading lc_heading_online" style="background-color: {if $config.ETS_LC_HEADING_COLOR_ONLINE}{$config.ETS_LC_HEADING_COLOR_ONLINE|escape:'html':'utf-8'}{else}#8FB30C{/if};">{l s='Online chat' mod='ets_livechat'}<div title="{if !$ETS_CONVERSATION_DISPLAY_ADMIN}{l s='Open' mod='ets_livechat'}{else}{l s='Close' mod='ets_livechat'}{/if}" class="toogle-hide-left" {if $lc_chatbox_top!==false} data-top="{$lc_chatbox_top|floatval}" data-left="{$lc_chatbox_left|floatval}"{/if} style="{if $lc_chatbox_top!==false}top:{$lc_chatbox_top|floatval}px;left:{$lc_chatbox_left|floatval}px;{/if} background-color: {if $config.ETS_LC_HEADING_COLOR_ONLINE}{$config.ETS_LC_HEADING_COLOR_ONLINE|escape:'html':'utf-8'}{else}#8FB30C{/if};">{if $config.ETS_CLOSE_CHAT_BOX_BACKEND_TYPE=='bubble_alert'}<span class="lc_move_chat_window" title="{l s='Click and hold your mouse to move chat window' mod='ets_livechat'}">&nbsp;</span>{/if}<span class="total_message">{$totalMessageNoSeen|intval}</span><span class="title_class" title="{if $status_employee=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $status_employee=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $status_employee=='invisible'}{l s='Invisible' mod='ets_livechat'}{/if}{if $status_employee=='offline'}{l s='Offline' mod='ets_livechat'}{/if}">{if $status_employee=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $status_employee=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $status_employee=='invisible'}{l s='Invisible' mod='ets_livechat'}{/if}{if $status_employee=='offline'}{l s='Offline' mod='ets_livechat'}{/if}</span></div></div>
        <div{if !$ETS_CONVERSATION_DISPLAY_ADMIN} title="{l s='Open' mod='ets_livechat'}"{/if} class="lc_heading lc_heading_offline" style="background-color: {if $config.ETS_LC_HEADING_COLOR_OFFLINE}{$config.ETS_LC_HEADING_COLOR_OFFLINE|escape:'html':'utf-8'}{else}#292929{/if};">{l s='Online chat' mod='ets_livechat'}<div title="{if !$ETS_CONVERSATION_DISPLAY_ADMIN}{l s='Open' mod='ets_livechat'}{else}{l s='Close' mod='ets_livechat'}{/if}" class="toogle-hide-left" {if $lc_chatbox_top!==false} data-top="{$lc_chatbox_top|floatval}" data-left="{$lc_chatbox_left|floatval}"{/if} style="{if $lc_chatbox_top!==false}top:{$lc_chatbox_top|floatval}px;left:{$lc_chatbox_left|floatval}px;{/if} background-color: {if $config.ETS_LC_HEADING_COLOR_OFFLINE}{$config.ETS_LC_HEADING_COLOR_OFFLINE|escape:'html':'utf-8'}{else}#292929{/if};">{if $config.ETS_CLOSE_CHAT_BOX_BACKEND_TYPE=='bubble_alert'}<span class="lc_move_chat_window" title="{l s='Click and hold your mouse to move chat window' mod='ets_livechat'}">&nbsp;</span>{/if}<span class="total_message">{$totalMessageNoSeen|intval}</span><span class="title_class" title="{if $status_employee=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $status_employee=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $status_employee=='invisible'}{l s='Invisible' mod='ets_livechat'}{/if}{if $status_employee=='offline'}{l s='Offline' mod='ets_livechat'}{/if}">{if $status_employee=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $status_employee=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $status_employee=='invisible'}{l s='Invisible' mod='ets_livechat'}{/if}{if $status_employee=='offline'}{l s='Offline' mod='ets_livechat'}{/if}</span></div></div>
        <div{if !$ETS_CONVERSATION_DISPLAY_ADMIN} title="{l s='Open' mod='ets_livechat'}"{/if} class="lc_heading lc_heading_do_not_disturb" style="background-color: {if $config.ETS_LC_HEADING_COLOR_BUSY}{$config.ETS_LC_HEADING_COLOR_BUSY|escape:'html':'utf-8'}{else}#ff0000{/if};">{l s='Online chat' mod='ets_livechat'}<div title="{if !$ETS_CONVERSATION_DISPLAY_ADMIN}{l s='Open' mod='ets_livechat'}{else}{l s='Close' mod='ets_livechat'}{/if}" class="toogle-hide-left" {if $lc_chatbox_top!==false} data-top="{$lc_chatbox_top|floatval}" data-left="{$lc_chatbox_left|floatval}"{/if} style="{if $lc_chatbox_top!==false}top:{$lc_chatbox_top|floatval}px;left:{$lc_chatbox_left|floatval}px;{/if} background-color: {if $config.ETS_LC_HEADING_COLOR_BUSY}{$config.ETS_LC_HEADING_COLOR_BUSY|escape:'html':'utf-8'}{else}#ff0000{/if};">{if $config.ETS_CLOSE_CHAT_BOX_BACKEND_TYPE=='bubble_alert'}<span class="lc_move_chat_window" title="{l s='Click and hold your mouse to move chat window' mod='ets_livechat'}">&nbsp;</span>{/if}<span class="total_message">{$totalMessageNoSeen|intval}</span><span class="title_class" title="{if $status_employee=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $status_employee=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $status_employee=='invisible'}{l s='Invisible' mod='ets_livechat'}{/if}{if $status_employee=='offline'}{l s='Offline' mod='ets_livechat'}{/if}">{if $status_employee=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $status_employee=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $status_employee=='invisible'}{l s='Invisible' mod='ets_livechat'}{/if}{if $status_employee=='offline'}{l s='Offline' mod='ets_livechat'}{/if}</span></div></div>
        <div{if !$ETS_CONVERSATION_DISPLAY_ADMIN} title="{l s='Open' mod='ets_livechat'}"{/if} class="lc_heading lc_heading_invisible" style="background-color: {if $config.ETS_LC_HEADING_COLOR_INVISIBLE}{$config.ETS_LC_HEADING_COLOR_INVISIBLE|escape:'html':'utf-8'}{else}#f8ff19{/if};">{l s='Online chat' mod='ets_livechat'}<div title="{if !$ETS_CONVERSATION_DISPLAY_ADMIN}{l s='Open' mod='ets_livechat'}{else}{l s='Close' mod='ets_livechat'}{/if}" class="toogle-hide-left" {if $lc_chatbox_top!==false} data-top="{$lc_chatbox_top|floatval}" data-left="{$lc_chatbox_left|floatval}"{/if} style="{if $lc_chatbox_top!==false}top:{$lc_chatbox_top|floatval}px;left:{$lc_chatbox_left|floatval}px;{/if} background-color: {if $config.ETS_LC_HEADING_COLOR_INVISIBLE}{$config.ETS_LC_HEADING_COLOR_INVISIBLE|escape:'html':'utf-8'}{else}#f8ff19{/if};"><span class="lc_move_chat_window" title="{l s='Click and hold your mouse to move chat window' mod='ets_livechat'}">&nbsp;</span><span class="total_message">{$totalMessageNoSeen|intval}</span><span class="title_class" title="{if $status_employee=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $status_employee=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $status_employee=='invisible'}{l s='Invisible' mod='ets_livechat'}{/if}{if $status_employee=='offline'}{l s='Offline' mod='ets_livechat'}{/if}">{if $status_employee=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $status_employee=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $status_employee=='invisible'}{l s='Invisible' mod='ets_livechat'}{/if}{if $status_employee=='offline'}{l s='Offline' mod='ets_livechat'}{/if}</span></div></div>
        <div class="lc_company_logo {if $config.ETS_LC_AVATAR_IMAGE_TYPE=='square'} lc_avatar_square{/if}">
            <img src="{$employee_info.logo|escape:'html':'UTF-8'}" />
        </div>
        <div class="company-name"><span class="name">{$employee_info.name|escape:'html':'UTF-8'}</span><span class="title_class" title="{if $status_employee=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $status_employee=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $status_employee=='invisible'}{l s='Invisible' mod='ets_livechat'}{/if}{if $status_employee=='offline'}{l s='Offline' mod='ets_livechat'}{/if}">{if $status_employee=='online' || $status_employee=='foce_online'}{l s='Online' mod='ets_livechat'}{/if}{if $status_employee=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $status_employee=='invisible'}{l s='Invisible' mod='ets_livechat'}{/if}{if $status_employee=='offline'}{l s='Offline' mod='ets_livechat'}{/if}</span></div>
        <div class="list_status_employee">
            <select name="ets_lc_status_employee {$status_employee|escape:'html':'UTF-8'}" id="ets_lc_status_employee" {if Configuration::get('ETS_LC_FORCE_ONLINE') && $id_profile!=1} disabled="disabled" {/if} >
                <option value="online" {if $status_employee=='online'}selected="selected"{/if}>{l s='Online' mod='ets_livechat'}</option>
                <option value="do_not_disturb" {if $status_employee=='do_not_disturb'}selected="selected"{/if}>{l s='Busy' mod='ets_livechat'}</option>
                <option value="invisible" {if $status_employee=='invisible'}selected="selected"{/if}>{l s='Invisible' mod='ets_livechat'}</option>
                <option value="offline" {if $status_employee=='offline'}selected="selected"{/if}>{l s='Offline' mod='ets_livechat'}</option>
                <option value="foce_online" {if $status_employee=='foce_online'}selected="selected"{/if} {if $id_profile!=1}disabled="disabled"{/if}>{l s='Force online' mod='ets_livechat'}</option>
            </select>
        </div>
        {if $config.ETS_LC_USE_SOUND_BACKEND}<div class="lc_sound enable" title="{l s='Disable sound' mod='ets_livechat'}">{l s='Sound' mod='ets_livechat'}</div>{/if}
        {if $id_profile==1}
            <div class="setting-admin"><a href="{$modulUrl|escape:'quotes':'UTF-8'}" title="{l s='Setting' mod='ets_livechat'}">{l s='Setting' mod='ets_livechat'}</a></div>
        {/if}
        <div class="expand-admin" title="{l s='Expand' mod='ets_livechat'}"><span title="{l s='Expand' mod='ets_livechat'}">{l s='Expand' mod='ets_livechat'}</span></div>
    </div>
    <div class="tab_content_customer{if $loaded} loaded{/if}">
    <ul class="list_customer">
{/if}
    {if $conversations}
        {foreach from = $conversations item='conversation'}
            <li class="{if $conversation.online}online{/if}{if isset($conversation.wait_accept) &&  $conversation.wait_accept} wait_accept{/if}{if isset($conversation.has_changed) &&  $conversation.has_changed} has_changed{/if}">
                <div class="conversation-item conversation_item_{$conversation.id_conversation|intval}" data-id="{$conversation.id_conversation|intval}" data-id-message="{$conversation.last_message.id_message|intval}">
                    {if $conversation.online}<i class="icon-online"></i>{else}<i class="icon-offline"></i>{/if} {if $conversation.fullname}{$conversation.fullname|escape:'quotes':'UTF-8'}{else}{if $conversation.customer_name}{$conversation.customer_name|escape:'quotes':'UTF-8'}{else}{l s='Chat id #' mod='ets_livechat'}{$conversation.id_conversation|intval}{/if}{/if} {if $conversation.count_message_not_seen}<span class="count_message_not_seen">{$conversation.count_message_not_seen|intval}</span>{/if}
                    <div class="lc_msg_time">{$conversation.last_message.datetime_added|escape:'html':'utf-8'}</div>
                    <div class="message_content">
                        {$conversation.last_message.message nofilter}
                    </div>
                </div>
                {if $conversation.archive==0}
                    <span class="archive_customer" data-id="{$conversation.id_conversation|intval}" title="{l s='Archive this conversation' mod='ets_livechat'}">{l s='Archive this conversation' mod='ets_livechat'}</span>
                {else}
                    <span class="active_customer" data-id="{$conversation.id_conversation|intval}" title="{l s='Move this conversation to active list' mod='ets_livechat'}">{l s='Move this conversation to active list' mod='ets_livechat'}</span>
                {/if}
                <span class="lc_text_wait_accept">{l s='Transferring' mod='ets_livechat'}</span>
            </li>
        {/foreach}
    {/if}   
{if !$refresh}
    </ul>
    <div class="massage_more_loading" style="display:none;">
            {l s='Loading...' mod='ets_livechat'}
        </div>
    <div class="more_load">{l s='Load more' mod='ets_livechat'}</div>
    </div>
    <div class="tab_customer">
        <div class="customer_active" data-content="list_customer_active">{l s='Active' mod='ets_livechat'}</div>
        <div class="customer_archive" data-content="list_customer_archive">{l s='Archived' mod='ets_livechat'}</div>
        <div class="customer_all" data-content="list_customer_all">{l s='All' mod='ets_livechat'}</div>
    </div>
    <div class="search_customer_chat_box">
        <input id="input_search_customer_chat" class="input_search_customer_chat" placeholder="{l s='Search ...' mod='ets_livechat'}"/>
    </div>
</div>
{/if}