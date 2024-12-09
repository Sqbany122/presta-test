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
<div class="lc_chatbox{if $display_bubble_imge} lc_use_bubble_image{/if}{if !$config.ETS_LC_ALLOW_CLOSE} lc_chatbox_no_close{/if}{if $config.ETS_LC_DISPLAY_COMPANY_INFO=='staff'} no_company_name{/if}{if !$config.ETS_LC_DISPLAY_COMPANY_INFO} no_display_compay_info{/if}{if $isRTL} lc_chatbox_rtl {/if}{if $chatBoxStatus} lc_saved_chatbox_status{/if} {if $chatBoxStatus=='open'}lc_chatbox_open{else}lc_chatbox_closed{/if} {if $isAdminOnline}{if $isAdminOnline=='online'}lc_admin_online{/if}{if $isAdminOnline=='do_not_disturb'}lc_admin_do_not_disturb{/if}{if $isAdminOnline=='invisible'}lc_admin_invisible{/if}{else}lc_admin_offline{/if} {if $config.ETS_LC_HIDE_ON_MOBILE}lc_hide_on_mobile{/if} lc_{$config.ETS_CLOSE_CHAT_BOX_TYPE|escape:'html':'utf-8'} {if ($messages &&  count($messages)<$config.ETS_LC_MSG_COUNT)|| !$messages}loaded{/if} {if !$config.ETS_LC_DISPLAY_AVATA} no_display_avata {/if} {if !$config.ETS_LC_DISPLAY_TIME}no_display_datetime{/if} {if $conversation && $conversation->end_chat} end_chat{/if} {if $isAdminBusy} is_admin_busy{/if}{if $count_message_not_seen} has_new_message{/if}" style="width: {if $config.ETS_LC_BOX_WIDTH}{$config.ETS_LC_BOX_WIDTH|intval}{else}250{/if}px; {if $config.ETS_CLOSE_CHAT_BOX_TYPE=='bubble_alert' && $chatBoxStatus!='open' && $lc_chatbox_top!==false} bottom:auto; top:{$lc_chatbox_top|floatval}px; left:{$lc_chatbox_left|floatval}px; button:auto{/if}" data-id-conversation="{$id_conversation|intval}" {if $config.ETS_CLOSE_CHAT_BOX_TYPE=='bubble_alert' && $lc_chatbox_top!==false} data-top="{$lc_chatbox_top|floatval}" data-left="{$lc_chatbox_left|floatval}" {/if}>
    <audio id="lg_ets_sound">
      <source src="{$livechatDir|escape:'quotes':'UTF-8'}/views/sound/{$config.ETS_SOUND_WHEN_NEW_MESSAGE|escape:'html':'utf-8'}.mp3" type="audio/mpeg" />
    </audio>
    <form id="lc_form_livechat" method="post" action="{$ajaxUrl|escape:'html':'utf-8'}" method="post" enctype="multipart/form-data" novalidate="">
        {if $display_bubble_imge}
        <img src="{$display_bubble_imge|escape:'html':'UTF-8'}" class="lc_heading lc_bubble_image"/>
        {/if}
        <div title="{if $chatBoxStatus!='open'}{l s='Show this chat' mod='ets_livechat'}{/if}" class="lc_heading lc_heading_online" style="background-color: {if $config.ETS_LC_HEADING_COLOR_ONLINE}{$config.ETS_LC_HEADING_COLOR_ONLINE|escape:'html':'utf-8'}{else}#8FB30C{/if};">
            <span class="lc_move_chat_window" title="{l s='Click and hold your mouse to move chat window' mod='ets_livechat'}">&nbsp;</span>
            <div class="lc_online_heading">{if !$config.ETS_LC_DISPLAY_COMPANY_INFO}<span class="title_class" title="{if $isAdminOnline}{if $isAdminOnline=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $isAdminOnline=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $isAdminOnline=='invisible'}{l s='Offline' mod='ets_livechat'}{/if}{else}{l s='Offline' mod='ets_livechat'}{/if}" >{if $isAdminOnline}{if $isAdminOnline=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $isAdminOnline=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $isAdminOnline=='invisible'}{l s='Offline' mod='ets_livechat'}{/if}{else}{l s='Offline' mod='ets_livechat'}{/if}</span>{/if}{$config.ETS_LC_TEXT_HEADING_ONLINE|escape:'html':'utf-8'}</div>
            <span class="lc_heading_count_message_not_seen {if $count_message_not_seen}show{/if}">{$count_message_not_seen|intval}</span>
            {if $config.ETS_LC_ALLOW_CLOSE}<span class="lc_close" title="{l s='Close' mod='ets_livechat'}">{l s='Close' mod='ets_livechat'}</span>{/if}
            {if $config.ETS_LC_ALLOW_MAXIMIZE}<span class="lc_maximize" title="{l s='Maximize' mod='ets_livechat'}">{l s='Maximize' mod='ets_livechat'}</span>{/if}
            <span class="lc_minimize" title="{l s='Hide chat window' mod='ets_livechat'}">{l s='Hide this chat' mod='ets_livechat'}</span>
            {if $config.ETS_LC_USE_SOUND_FONTEND}<span class="lc_sound{if !$messages} lc_hide{/if} {if ($conversation &&  $conversation->enable_sound==1)||!$conversation}enable{else}disable{/if}" title="{if ($conversation &&  $conversation->enable_sound==1)||!$conversation}{l s='Disable sound' mod='ets_livechat'}{else}{l s='Enable sound' mod='ets_livechat'}{/if}">{l s='Sound' mod='ets_livechat'}</span>{/if}
            {if !$config.ETS_LC_DISPLAY_COMPANY_INFO}<span class="lc_customer_info_toggle {if !$messages} lc_hide{/if}"  title="{l s='Customer info' mod='ets_livechat'}">{l s='Customer info' mod='ets_livechat'}</span>{/if}
        </div>
        <div title="{if $chatBoxStatus!='open'}{l s='Show this chat' mod='ets_livechat'}{/if}" class="lc_heading lc_heading_offline" style="background-color: {if $config.ETS_LC_HEADING_COLOR_OFFLINE}{$config.ETS_LC_HEADING_COLOR_OFFLINE|escape:'html':'utf-8'}{else}#292929{/if};">
            <span class="lc_move_chat_window" title="{l s='Click and hold your mouse to move chat window' mod='ets_livechat'}">&nbsp;</span>
            <div class="lc_offline_heading">{if !$config.ETS_LC_DISPLAY_COMPANY_INFO}<span class="title_class" title="{if $isAdminOnline}{if $isAdminOnline=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $isAdminOnline=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $isAdminOnline=='invisible'}{l s='Offline' mod='ets_livechat'}{/if}{else}{l s='Offline' mod='ets_livechat'}{/if}" >{if $isAdminOnline}{if $isAdminOnline=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $isAdminOnline=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $isAdminOnline=='invisible'}{l s='Offline' mod='ets_livechat'}{/if}{else}{l s='Offline' mod='ets_livechat'}{/if}</span>{/if}{$config.ETS_LC_TEXT_HEADING_OFFLINE|escape:'html':'utf-8'}</div>
            <span class="lc_heading_count_message_not_seen {if $count_message_not_seen}show{/if}">{$count_message_not_seen|intval}</span>
            {if $config.ETS_LC_ALLOW_CLOSE}<span class="lc_close" title="{l s='Close' mod='ets_livechat'}">{l s='Close' mod='ets_livechat'}</span>{/if}
            {if $config.ETS_LC_ALLOW_MAXIMIZE}<span class="lc_maximize" title="{l s='Maximize' mod='ets_livechat'}">{l s='Maximize' mod='ets_livechat'}</span>{/if}
            <span class="lc_minimize" title="{l s='Hide chat window' mod='ets_livechat'}"></span>
            {if $config.ETS_LC_USE_SOUND_FONTEND}<span class="lc_sound{if !$messages} lc_hide{/if} {if ($conversation &&  $conversation->enable_sound==1)||!$conversation}enable{else}disable{/if}" title="{if ($conversation &&  $conversation->enable_sound==1)||!$conversation}{l s='Disable sound' mod='ets_livechat'}{else}{l s='Enable sound' mod='ets_livechat'}{/if}">{l s='Sound' mod='ets_livechat'}</span>{/if}
            {if !$config.ETS_LC_DISPLAY_COMPANY_INFO}<span class="lc_customer_info_toggle {if !$messages} lc_hide{/if}"  title="{l s='Customer info' mod='ets_livechat'}">{l s='Customer info' mod='ets_livechat'}</span>{/if}
        </div>
        <div title="{if $chatBoxStatus!='open'}{l s='Show this chat' mod='ets_livechat'}{/if}" class="lc_heading lc_heading_do_not_disturb" style="background-color: {if $config.ETS_LC_HEADING_COLOR_BUSY}{$config.ETS_LC_HEADING_COLOR_BUSY|escape:'html':'utf-8'}{else}#ff0000{/if};">
            <span class="lc_move_chat_window" title="{l s='Click and hold your mouse to move chat window' mod='ets_livechat'}">&nbsp;</span>
            <div class="lc_do_not_disturb_heading">{if !$config.ETS_LC_DISPLAY_COMPANY_INFO}<span class="title_class" title="{if $isAdminOnline}{if $isAdminOnline=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $isAdminOnline=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $isAdminOnline=='invisible'}{l s='Offline' mod='ets_livechat'}{/if}{else}{l s='Offline' mod='ets_livechat'}{/if}" >{if $isAdminOnline}{if $isAdminOnline=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $isAdminOnline=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $isAdminOnline=='invisible'}{l s='Offline' mod='ets_livechat'}{/if}{else}{l s='Offline' mod='ets_livechat'}{/if}</span>{/if}{$config.ETS_LC_TEXT_HEADING_BUSY|escape:'html':'utf-8'}</div>
            <span class="lc_heading_count_message_not_seen {if $count_message_not_seen}show{/if}">{$count_message_not_seen|intval}</span>
            {if $config.ETS_LC_ALLOW_CLOSE}<span class="lc_close">{l s='Close' mod='ets_livechat'}</span>{/if}
            {if $config.ETS_LC_ALLOW_MAXIMIZE}<span class="lc_maximize" title="{l s='Maximize' mod='ets_livechat'}">{l s='Maximize' mod='ets_livechat'}</span>{/if}
            <span class="lc_minimize" title="{l s='Hide chat window' mod='ets_livechat'}">{l s='Hide this chat' mod='ets_livechat'}</span>
            {if $config.ETS_LC_USE_SOUND_FONTEND}<span class="lc_sound{if !$messages} lc_hide{/if} {if ($conversation &&  $conversation->enable_sound==1)||!$conversation}enable{else}disable{/if}" title="{if ($conversation &&  $conversation->enable_sound==1)||!$conversation}{l s='Disable sound' mod='ets_livechat'}{else}{l s='Enable sound' mod='ets_livechat'}{/if}">{l s='Sound' mod='ets_livechat'}</span>{/if}
            {if !$config.ETS_LC_DISPLAY_COMPANY_INFO}<span class="lc_customer_info_toggle {if !$messages} lc_hide{/if}"  title="{l s='Customer info' mod='ets_livechat'}">{l s='Customer info' mod='ets_livechat'}</span>{/if}
        </div>
        <div class="lc_heading lc_heading_invisible" style="background-color: {if $config.ETS_LC_HEADING_COLOR_INVISIBLE}{$config.ETS_LC_HEADING_COLOR_INVISIBLE|escape:'html':'utf-8'}{else}#f8ff19{/if};">
            <span class="lc_move_chat_window" title="{l s='Click and hold your mouse to move chat window' mod='ets_livechat'}">&nbsp;</span>
            <div class="lc_invisible_heading">{if !$config.ETS_LC_DISPLAY_COMPANY_INFO}<span class="title_class" title="{if $isAdminOnline}{if $isAdminOnline=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $isAdminOnline=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $isAdminOnline=='invisible'}{l s='Offline' mod='ets_livechat'}{/if}{else}{l s='Offline' mod='ets_livechat'}{/if}" >{if $isAdminOnline}{if $isAdminOnline=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $isAdminOnline=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $isAdminOnline=='invisible'}{l s='Offline' mod='ets_livechat'}{/if}{else}{l s='Offline' mod='ets_livechat'}{/if}</span>{/if}{$config.ETS_LC_TEXT_HEADING_INVISIBLE|escape:'html':'utf-8'}</div>
            <span class="lc_heading_count_message_not_seen {if $count_message_not_seen}show{/if}">{$count_message_not_seen|intval}</span>
            {if $config.ETS_LC_ALLOW_CLOSE}<span class="lc_close">{l s='Close' mod='ets_livechat'}</span>{/if}
            {if $config.ETS_LC_ALLOW_MAXIMIZE}<span class="lc_maximize" title="{l s='Maximize' mod='ets_livechat'}">{l s='Maximize' mod='ets_livechat'}</span>{/if}
            <span class="lc_minimize" title="{if $chatBoxStatus=='open'}{l s='Hide chat window' mod='ets_livechat'}{else}{l s='Show chat window' mod='ets_livechat'}{/if}">{if $chatBoxStatus=='open'}{l s='Hide this chat' mod='ets_livechat'}{else}{l s='Show this chat' mod='ets_livechat'}{/if}</span>
            {if $config.ETS_LC_USE_SOUND_FONTEND}<span class="lc_sound{if !$messages} lc_hide{/if} {if ($conversation &&  $conversation->enable_sound==1)||!$conversation}enable{else}disable{/if}" title="{if ($conversation &&  $conversation->enable_sound==1)||!$conversation}{l s='Disable sound' mod='ets_livechat'}{else}{l s='Enable sound' mod='ets_livechat'}{/if}">{l s='Sound' mod='ets_livechat'}</span>{/if}
            {if !$config.ETS_LC_DISPLAY_COMPANY_INFO}<span class="lc_customer_info_toggle {if !$messages} lc_hide{/if}"  title="{l s='Customer info' mod='ets_livechat'}">{l s='Customer info' mod='ets_livechat'}</span>{/if}
        </div>
        {if $config.ETS_LC_DISPLAY_COMPANY_INFO}
            <div class="lc_company_info">
                {if $config.ETS_LC_DISPLAY_COMPANY_INFO =='general' || !$lastMessageOfEmployee}
                    <div class="lc_company_logo {if $config.ETS_LC_AVATAR_IMAGE_TYPE=='square'} lc_avatar_square{/if}" >
                        <img title="{$config.ETS_LC_COMPANY_NAME|escape:'html':'UTF-8'}" src="{$livechatDir|escape:'quotes':'UTF-8'}views/img/config/{if $config.ETS_LC_COMPANY_LOGO}{$config.ETS_LC_COMPANY_LOGO|escape:'quotes':'UTF-8'}{else}adminavatar.jpg{/if}" />
                    </div>
                    {if $config.ETS_LC_COMPANY_NAME}
                        <div class="company-name">{$config.ETS_LC_COMPANY_NAME|escape:'html':'UTF-8'}<span class="title_class" title="{if $isAdminOnline}{if $isAdminOnline=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $isAdminOnline=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $isAdminOnline=='invisible'}{l s='Offline' mod='ets_livechat'}{/if}{else}{l s='Offline' mod='ets_livechat'}{/if}" >{if $isAdminOnline}{if $isAdminOnline=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $isAdminOnline=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $isAdminOnline=='invisible'}{l s='Offline' mod='ets_livechat'}{/if}{else}{l s='Offline' mod='ets_livechat'}{/if}</span></div>
                    {/if}
                {else}
                    <div class="lc_company_logo {if $config.ETS_LC_AVATAR_IMAGE_TYPE=='square'} lc_avatar_square{/if}">
                        <img title="{$employee_info.name|escape:'html':'UTF-8'}" src="{$employee_info.logo|escape:'html':'UTF-8'}" />
                    </div>
                    <div class="company-name">{$employee_info.name|escape:'html':'UTF-8'}<span class="title_class" title="{if $isAdminOnline}{if $isAdminOnline=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $isAdminOnline=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $isAdminOnline=='invisible'}{l s='Offline' mod='ets_livechat'}{/if}{else}{l s='Offline' mod='ets_livechat'}{/if}">{if $isAdminOnline}{if $isAdminOnline=='online'}{l s='Online' mod='ets_livechat'}{/if}{if $isAdminOnline=='do_not_disturb'}{l s='Busy' mod='ets_livechat'}{/if}{if $isAdminOnline=='invisible'}{l s='Offline' mod='ets_livechat'}{/if}{else}{l s='Offline' mod='ets_livechat'}{/if}</span></div>
                {/if}
                {if $config.ETS_LC_SUB_TITLE}
                    <div class="sub_title">{$config.ETS_LC_SUB_TITLE|escape:'html':'UTF-8'}</div>
                {/if}
            </div>
        {/if}
        <div class="lc_introduction {if $messages}lc_hide{/if}">
            <div class="lc_online_text">{$config.ETS_LC_TEXT_ONLINE|escape:'html':'utf-8'}</div>
            <div class="lc_do_not_disturb_text">{$config.ETS_LC_TEXT_DO_NOT_DISTURB|escape:'html':'utf-8'}</div>
            <div class="lc_invisible_text">{$config.ETS_LC_TEXT_INVISIBLE|escape:'html':'utf-8'}</div>   
            <div class="lc_offline_text">{$config.ETS_LC_TEXT_OFFLINE|escape:'html':'utf-8'}</div>
            <div class="lc_end_chat_rate">{l s='This chat has ended. Are you satisfied with our support?' mod='ets_livechat'}</div>
            <div class="lc_end_chat">{l s='Your last chat has ended. Do you want to send another message?' mod='ets_livechat'}</div>
        </div>
        <div class="lc_error_start_chat_c" style="display:none;">
            {if $config.ETS_LC_DISPLAY_REQUIRED_FIELDS}
                <div class="lc_warning_error">
                    {if $config.ETS_LC_ADDITIONAL_NOTIFICATION}
                        {$config.ETS_LC_ADDITIONAL_NOTIFICATION nofilter}
                    {else}
                        {l s='Sorry for this inconvenience but please enter some additional information to start chatting' mod='ets_livechat'}
                    {/if}
                </div>
            {/if}
        </div>
        <div class="lc_customer_info cass {if ( $customer && ($customer.phone || !LC_Conversation::isRequiredField('phone')) && !$end_chat )|| $config.ETS_LC_DISPLAY_REQUIRED_FIELDS}closed{/if}">
            <div class="lc_customer_info_form">
                {if LC_Conversation::isUsedField('name')}
                    <div class="lc_customer_name">
                        <input {if !LC_Conversation::isRequiredField('name')}placeholder="{l s='Your name' mod='ets_livechat'}"{else}placeholder="{l s='Your name *' mod='ets_livechat'}"{/if} id="lc_customer_name" class="form-control" {if $isCustomerLoggedIn || !$config.ETS_LC_UPDATE_CONTACT_INFO && isset($customer.name)}disabled="disabled"{/if} type="text" name="name" value="{if isset($customer.name)}{$customer.name|escape:'html':'utf-8'}{/if}" />
                    </div>
                {/if}
                {if LC_Conversation::isUsedField('email') || !$isAdminOnline}
                <div class="lc_customer_email">
                    <input {if !LC_Conversation::isRequiredField('email') && $isAdminOnline}placeholder="{l s='Email' mod='ets_livechat'}"{else}placeholder="{l s='Email *' mod='ets_livechat'}"{/if} id="lc_customer_email" class="form-control" {if $isCustomerLoggedIn || !$config.ETS_LC_UPDATE_CONTACT_INFO && isset($customer.email)}disabled="disabled"{/if} type="text" name="email" value="{if isset($customer.email)}{$customer.email|escape:'html':'utf-8'}{/if}" />
                </div>
                {/if}
                {if LC_Conversation::isUsedField('phone')}
                    <div class="lc_customer_phone">
                        <input id="lc_customer_phone" {if !LC_Conversation::isRequiredField('phone')}placeholder="{l s='Phone number' mod='ets_livechat'}"{else}placeholder="{l s='Phone number *' mod='ets_livechat'}"{/if} class="form-control" {if $isCustomerLoggedIn && isset($customer.phoneRegistered) && $customer.phoneRegistered || !$config.ETS_LC_UPDATE_CONTACT_INFO && ($isCustomerLoggedIn && !$customer.phoneRegistered && isset($customer.phone) && $customer.phone || !$isCustomerLoggedIn && isset($customer.phone))}disabled="disabled"{/if} type="text" name="phone" value="{if isset($customer.phone)}{$customer.phone|escape:'html':'utf-8'}{/if}" />
                    </div>
                {/if}
                {if !$isCustomerLoggedIn && !$conversation}
                    {if $config.ETS_LIVECHAT_ENABLE_FACEBOOK || $config.ETS_LIVECHAT_ENABLE_GOOGLE || $config.ETS_LIVECHAT_ENABLE_TWITTER}
                        <div class="lc_social_form">
                            <label>{l s='Or sign in with' mod='ets_livechat'}</label>
                            <ul class="lc_social">
                                {if $config.ETS_LIVECHAT_ENABLE_FACEBOOK}
                                    <li class="lc_social_item facebook active" data-auth="Facebook" title="{l s='Sign in with facebook' mod='ets_livechat'}">
                                        <span class="lc_social_btn medium rounded custom">
                                            <i class="icon icon-facebook fa fa-facebook"></i>
                                        </span>
                                    </li>
                                {/if}
                                {if $config.ETS_LIVECHAT_ENABLE_GOOGLE}
                                    <li class="lc_social_item google active" data-auth="Google" title="{l s='Sign in with google' mod='ets_livechat'}">
                                        <span class="lc_social_btn medium rounded custom">
                                            <i class="icon icon-google fa fa-google"></i>
                                        </span>
                                    </li>
                                {/if}
                                {if $config.ETS_LIVECHAT_ENABLE_TWITTER}
                                    <li class="lc_social_item twitter active" data-auth="Twitter" title="{l s='Sign in with twitter' mod='ets_livechat'}">
                                        <span class="lc_social_btn medium rounded custom">
                                            <i class="icon icon-twitter fa fa-twitter"></i>
                                        </span>
                                    </li>
                                {/if}
                            </ul>
                        </div>
                    {/if}
                {/if}
                {if $departments}
                    <div>
                        <select name="id_departments" id="id_departments" {if !$change_department} disabled="disabled"{/if}>
                            <option value="0">{l s='Select Department' mod='ets_livechat'}{if LC_Conversation::isRequiredField('departments')}&nbsp;*{/if}</option>
                            {foreach from=$departments item='department'}
                                <option value="{$department.id_departments|intval}"{if isset($conversation->id_departments) && $conversation->id_departments==$department.id_departments} selected="selected"{/if}>{$department.name|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                {/if}
                {if $config.ETS_LC_UPDATE_CONTACT_INFO}<div class="lc_update_info btn btn-primary {if !$customer || ($customer && !$customer.phone && LC_Conversation::isRequiredField('phone')) || ($conversation && !$change_department) || $end_chat}lc_hide{/if}" style="background:{$config.LC_BACKGROUD_COLOR_BUTTON|escape:'html':'utf-8'}">{l s='Update' mod='ets_livechat'}</div>{/if}
            </div>
            {if $config.ETS_LC_DISPLAY_COMPANY_INFO}<span class="lc_customer_info_toggle {if !$conversation} lc_hide{/if}"  title="{l s='Customer info' mod='ets_livechat'}">{l s='Customer info' mod='ets_livechat'}</span>{/if}
        </div>
        <div class="lc_messages {if !$messages}lc_hide{/if}" >
            <div>
            <div class="lc_messages_table">
                <div class="lc_messages_table-cell">
                    <div class="lc_messages_table-content">
                        <div>
                        <div class="massage_more_loading" style="display:none;">
                            {l s='Loading...' mod='ets_livechat'}
                        </div>
                        <div class="more_load">{l s='Load more' mod='ets_livechat'}</div>
                        <ul class="lc_msg_board">
                            {if $messages && !$end_chat}
                                {foreach from=$messages item='msg'}
                                    <li class="{if $msg.id_employee}is_employee {if $msg.employee_name}has_name_emplode{/if} {else}is_customer {if $msg.customer_name}has_name_customer{/if}{/if} lc_msg " data-id-message="{$msg.id_message|intval}">
                                        <div class="lc_sender">
                                            {if $msg.id_employee}
                                                {if $msg.employee_avata}
                                                    <div class="avata{if $config.ETS_LC_AVATAR_IMAGE_TYPE=='square'} lc_avatar_square{/if}">
                                                        <img src="{$msg.employee_avata|escape:'quotes':'UTF-8'}" title="{$msg.employee_name|escape:'html':'utf-8'}" />
                                                    </div>
                                                {/if}
                                                {if $msg.employee_name}
                                                    <span title="{$msg.employee_name|escape:'html':'utf-8'}">{$msg.employee_name|escape:'html':'utf-8'}</span>
                                                {/if}
                                            {else}
                                                {if $msg.customer_avata}
                                                    <div class="avata{if $config.ETS_LC_AVATAR_IMAGE_TYPE=='square'} lc_avatar_square{/if}">
                                                        <img src="{$msg.customer_avata|escape:'quotes':'UTF-8'}" title="{$msg.customer_name|escape:'html':'utf-8'}" />
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
                                        {if !$msg.id_employee && ($config.ETS_LC_ENABLE_EDIT_MESSAGE||$config.ETS_LC_ENABLE_DELETE_MESSAGE)}
                                            <div class="lc_action_message">
                                                {if $config.ETS_LC_ENABLE_DELETE_MESSAGE}
                                                    <span title="{l s='Delete' mod='ets_livechat'}" class="customer_delete_message" data-id-message="{$msg.id_message|intval}">{l s='Delete' mod='ets_livechat'}</span>
                                                {/if}
                                                {if $config.ETS_LC_ENABLE_EDIT_MESSAGE}
                                                    <span title="{l s='Edit' mod='ets_livechat'}" class="customer_edit_message" data-id-message="{$msg.id_message|intval}">{l s='Edit' mod='ets_livechat'}</span>
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
                        <ul class="message_status" {if $lastMessageIsEmployee || !$isRequestAjax || $end_chat ||$isEmployeeWriting} style="display:none;"{/if}>
                            {if $isEmployeeSeen}
                                <li {if !$lastMessageIsEmployee && $isRequestAjax && !$end_chat && !$isEmployeeWriting} class="show"{/if}><span class="seen_employee show">{l s='Seen' mod='ets_livechat'}</span></li>
                            {else}
                                {if $isEmployeeDelivered}
                                    <li class="show"><span class="delivered_employee show">{l s='Delivered' mod='ets_livechat'}</span></li>
                                {else}
                                    {if $isEmployeeSent}
                                        <li class="show"><span class="sent_employee show">{l s='Sent' mod='ets_livechat'}</span></li>
                                    {/if}
                                {/if}
                            {/if}
                            <li class="seen_employee"><span >{l s='Seen' mod='ets_livechat'}</span></li>
                            <li class="delivered_employee"><span>{l s='Delivered' mod='ets_livechat'}</span></li>
                            <li class="sent_employee"><span>{l s='Sent' mod='ets_livechat'}</span></li>
                        </ul>
                        <ul class="lc_message_end_chat"{if $isRequestAjax}style="display:none;"{/if} >
                            <li class="customer_end">
                                {l s='Chat paused. Send another message if you want to restart chat session' mod='ets_livechat'}
                            </li>
                        </ul>
                        <span class="writing_employee {if $isEmployeeWriting && $isRequestAjax && !$end_chat }show{/if}">{$employee_name|escape:'html':'utf-8'} {l s='is writing' mod='ets_livechat'}&nbsp;<span></span><span></span><span></span></span>
                    </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        <div class="lc_error" style="display:none;">
            
        </div>
        {if $config.ETS_LC_TIME_WAIT}
            <div class="blok_wait_support">
                {l s='A staff is connecting to you. Please wait ...!' mod='ets_livechat'} <br />
                {l s='Estimated time:' mod='ets_livechat'}
                <div id="clock_wait"></div>
            </div>
        {/if}
        <div class="block_admin_busy">
            {if $isAdminBusy}
                {l s='Sorry. All staffs are busy at the moment. Please come back later or ' mod='ets_livechat'}
                <button class="lc_customer_end_chat">{l s='click here to end chat.' mod='ets_livechat'}</button>
            {/if}
        </div>
        <div class="lc_text_area {if !$config.ETS_LC_ENABLE_EMOTION_ICON || !$emotions}no_display_emotion{/if} {if $config.ETS_DISPLAY_SEND_BUTTON || !$messages}show_send_box {if !$messages}start_chating{/if} {/if} {if isset($captcha) && $captcha}show_capcha{/if}">
            <input id="message_delivered" value="1" name="message_delivered" type="hidden"/>
            <input id="message_seen" value="0" name="message_seen" type="hidden"/>
            <input id="message_writing" value="0" name="message_writing" type="hidden"/>
            <input type="hidden" id="lc_conversation_end_chat" name="lc_conversation_end_chat" {if $conversation && $conversation->end_chat}value="1"{else}value="0"{/if} />
            <input type="hidden" name="id_message" value=""/>
            <textarea name="lc_message_old" style="display:none;" id="lc_message_old"></textarea>
            <div class="lc_captcha {if isset($captcha) && $captcha}active{/if}">
                {if $captcha}<img src="{$captcha|escape:'html':'utf-8'}" class="lc_captcha_img" />{/if}
                <span data-captcha-img="{$captchaUrl|escape:'html':'utf-8'}" class="lc_captcha_refesh" title="{l s='Refesh' mod='ets_livechat'}">{l s='Refesh' mod='ets_livechat'}</span>
                <input type="text" name="captcha" class="lc_captcha_input form-control" placeholder="{l s='Security code'  mod='ets_livechat'}" />
            </div>
            {if $config.ETS_LC_SEND_FILE && $upload_file}
                <div class="form_upfile">
                    <input type="file" name="message_file"/>
                </div>
            {/if}
            <textarea placeholder="{l s='Type a message' mod='ets_livechat'}" class="form-control" name="message"></textarea>
            {if $config.ETS_LC_ENABLE_EMOTION_ICON && $emotions}
                <div class="lc_emotion{if isset($captcha) && $captcha} ena_captcha{/if}">
                    <ul>
                        {foreach from=$emotions key='name' item='icon'}
                            <li data-emotion="{$name|escape:'html':'utf-8'}" title="{$icon.title|escape:'html':'utf-8'}"><img alt="{$icon.title|escape:'html':'utf-8'}" src="{$livechatDir|escape:'html':'utf-8'}views/img/emotions/{$icon.img|escape:'html':'utf-8'}"/></li>
                        {/foreach}
                    </ul>
                </div>
            {/if}
            {if $product_current}
                <div class="lc_product_send">
                    <label for="send_product_id">
                    <input type="checkbox" name="send_product_id" id="send_product_id" value="{$product_current.id_product|intval}"{if $config.ETS_LC_PRODUCT_LINK_REQUIRE} checked="checked" disabled="disabled"{/if} />
                    <span class="icon_checkbox"></span>{l s='Also send current product information' mod='ets_livechat'}</label>
                    <div class="lc_product_info">
                        <a class="lc_product_name" href="{$product_current.link|escape:'html':'UTF-8'}" style="color:{if $config.ETS_LC_PRODUCT_NAME_COLOR}{$config.ETS_LC_PRODUCT_NAME_COLOR|escape:'html':'UTF-8'}{else}#2fb5d2{/if}" >{$product_current.name|escape:'html':'UTF-8'}</a>
                        <span class="lc_product_price" style="color:{if $config.ETS_LC_PRODUCT_PRICE_COLOR}{$config.ETS_LC_PRODUCT_PRICE_COLOR|escape:'html':'UTF-8'}{else}#f7b265{/if}">{$product_current.price|escape:'html':'UTF-8'}</span>
                    </div>
                </div>
            {/if}
            <div class="lc_send_box" {if $config.LC_BACKGROUD_COLOR_BUTTON} style="background:{$config.LC_BACKGROUD_COLOR_BUTTON|escape:'html':'utf-8'}"{/if}>
                {if !$messages}
                    <div class="lc_loading_send">&nbsp;{l s='Starting Chat' mod='ets_livechat'}</div>
                {/if}
                <input class="btn btn-primary lc_send" name="lc_send" type="submit" value="{if $isAdminOnline && $isAdminOnline!='invisible' }{if $messages}{$config.ETS_LC_TEXT_SEND|escape:'html':'utf-8'}{else}{$config.ETS_LC_TEXT_SEND_START_CHAT|escape:'html':'utf-8'}{/if}{else}{if $messages}{$config.ETS_LC_TEXT_SEND|escape:'html':'utf-8'}{else}{$config.ETS_LC_TEXT_SEND_OffLINE|escape:'html':'utf-8'}{/if}{/if}" />
            </div>
        </div>
        <div class="lc_error_start_chat" style="display:none;">
        </div>
        {if $config.ETS_LC_TEXT_OFFLINE_THANKYOU}
            <div class="lc_thankyou">
                <div>{$config.ETS_LC_TEXT_OFFLINE_THANKYOU|escape:'html':'utf-8'}</div>
                <div><span class="btn btn-primary lc_send_another_msg" style="background:{$config.LC_BACKGROUD_COLOR_BUTTON|escape:'html':'utf-8'}">{l s='Send another message'  mod='ets_livechat'}</span></div>
            </div>
        {/if}
        <div class="criterion_contact{if !$messages } contact_start_chat{/if}{if (!$messages||!$config.ETS_LC_DISPLAY_RATING) && !$config.ETS_LC_DISPLAY_SEND_US_AN_EMAIL} lc_hide{/if} {if !$config.ETS_LC_DISPLAY_RATING||!$messages} no_rate_customer{/if}">
            {if $config.ETS_LC_DISPLAY_RATING}
                <div class="criterions_livechat{if !$messages} lc_hide{/if}" >
                    <label>{l s='Rating' mod='ets_livechat'}:</label>
            		<div class="star_content">
            			<input class="star not_uniform" type="radio" name="criterion_livechat" value="1" data-toggle="tooltip" title="{l s='Terrible' mod='ets_livechat'}" {if $conversation &&  $conversation->rating==1}checked="checked"{/if} />
            			<input class="star not_uniform" type="radio" name="criterion_livechat" value="2" title="{l s='Acceptable' mod='ets_livechat'}" {if $conversation && $conversation->rating==2}checked="checked"{/if}/>
            			<input class="star not_uniform" type="radio" name="criterion_livechat" value="3" title="{l s='Fairly Good' mod='ets_livechat'}" {if $conversation && $conversation->rating==3}checked="checked"{/if}/>
            			<input class="star not_uniform" type="radio" name="criterion_livechat" value="4" title="{l s='Good' mod='ets_livechat'}" {if $conversation && $conversation->rating==4}checked="checked"{/if} />
            			<input class="star not_uniform" type="radio" name="criterion_livechat" value="5" title="{l s='Excellent' mod='ets_livechat'}" {if $conversation && $conversation->rating==5}checked="checked"{/if}/>
            		</div>
                    <div class="no-rate">{l s='No thanks' mod='ets_livechat'}</div>
            		<div class="clearfix"></div>
                </div>
            {/if}
            {if $config.ETS_LC_DISPLAY_SEND_US_AN_EMAIL && $contact_link}
                <a href="{$contact_link|escape:'quotes':'UTF-8'}" class="contact" title="{if $config.ETS_LC_LINK_SUPPORT_TITLE}{$config.ETS_LC_LINK_SUPPORT_TITLE|escape:'html':'UTF-8'}{else}{l s='Send us an email' mod='ets_livechat'}{/if}">{if $config.ETS_LC_LINK_SUPPORT_TITLE}{$config.ETS_LC_LINK_SUPPORT_TITLE|escape:'html':'UTF-8'}{else}{l s='Send us an email' mod='ets_livechat'}{/if}</a>
            {/if}
        </div>
    </form>
</div>
{if $config.LC_BACKGROUD_HOVER_BUTTON}
    {literal}
    <style>
        .lc_update_info:hover,.lc_send_box:hover,.lc_send_another_msg:hover{
    {/literal}
            background :{$config.LC_BACKGROUD_HOVER_BUTTON|escape:'html':'UTF-8'}!important;
     {literal}
        }
     {/literal}
    </style>
{/if}