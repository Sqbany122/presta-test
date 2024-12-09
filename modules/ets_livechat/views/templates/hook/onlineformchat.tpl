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
<div class="translatable-field lang-{$language->id|intval}" {if $language->id != $defaultFormLanguage}style="display:none"{/if} >
    <div class="chatbox_demo_backend lc_chatbox lc_admin_online{if $isRTL} lc_chatbox_rtl{/if}{if $config.ETS_LC_HIDE_ON_MOBILE} lc_hide_on_mobile{/if} lc_{$config.ETS_CLOSE_CHAT_BOX_TYPE|escape:'html':'utf-8'}{if !$config.ETS_LC_DISPLAY_AVATA} no_display_avata {/if} {if !$config.ETS_LC_DISPLAY_TIME}no_display_datetime{/if}{if !$config.ETS_LC_DISPLAY_COMPANY_INFO} no_display_compay_info{/if}">
        <div class="lc_heading lc_heading_online" style="background-color: {if $config.ETS_LC_HEADING_COLOR_ONLINE}{$config.ETS_LC_HEADING_COLOR_ONLINE|escape:'html':'utf-8'}{else}#8FB30C{/if};">
            <div class="lc_online_heading">{$config.ETS_LC_TEXT_HEADING_ONLINE|escape:'html':'utf-8'}</div>
            <span class="lc_close{if !$config.ETS_LC_ALLOW_CLOSE} lc_hide{/if}" title="{l s='Close' mod='ets_livechat'}">{l s='Close' mod='ets_livechat'}</span>
            <span class="lc_maximize{if !$config.ETS_LC_ALLOW_MAXIMIZE} lc_hide{/if}" title="{l s='Maximize' mod='ets_livechat'}">{l s='Maximize' mod='ets_livechat'}</span>
            <span class="lc_minimize" title="{l s='Hide this chat' mod='ets_livechat'}">{l s='Hide this chat' mod='ets_livechat'}</span>
            <span class="lc_sound enable{if !$config.ETS_LC_USE_SOUND_FONTEND} lc_hide{/if}" title="{l s='Disable sound' mod='ets_livechat'}">{l s='Sound' mod='ets_livechat'}</span>
        </div>
        <div class="lc_company_info{if $config.ETS_LC_DISPLAY_COMPANY_INFO=='general'} display_company_info{/if}{if !$config.ETS_LC_DISPLAY_COMPANY_INFO} lc_hide{/if}">
            <div class="company_info">
                <div class="lc_company_logo {if $config.ETS_LC_AVATAR_IMAGE_TYPE=='square'} lc_avatar_square{/if}" >
                    <img title="{$config.ETS_LC_COMPANY_NAME|escape:'html':'UTF-8'}" src="{$livechatDir|escape:'quotes':'UTF-8'}/views/img/config/{$config.ETS_LC_COMPANY_LOGO|escape:'quotes':'UTF-8'}" />
                </div>
                <div class="company company-name">{$config.ETS_LC_COMPANY_NAME|escape:'html':'UTF-8'}</div><span class="title_class" title="{l s='Online' mod='ets_livechat'}" >{l s='Online' mod='ets_livechat'}</span>
            </div>
            <div class="employee_info company_info">
                <div class="lc_company_logo {if $config.ETS_LC_AVATAR_IMAGE_TYPE=='square'} lc_avatar_square{/if}">
                    <img title="{$employee_info.name|escape:'html':'UTF-8'}" src="{$employee_info.logo|escape:'quotes':'UTF-8'}" />
                </div>
                <div class="employee-name company-name">{$employee_info.name|escape:'html':'UTF-8'}</div><span class="title_class" title="{l s='Online' mod='ets_livechat'}">{l s='Online' mod='ets_livechat'}</span>
            </div>
            {if $config.ETS_LC_SUB_TITLE}
                <div class="sub_title">{$config.ETS_LC_SUB_TITLE|escape:'html':'UTF-8'}</div>
            {/if}
        </div>
        <div class="lc_introduction">
            <div class="lc_online_text">{$config.ETS_LC_TEXT_ONLINE|escape:'html':'utf-8'}</div>
        </div>
        <div class="lc_customer_info {if !$config.ETS_LC_DISPLAY_REQUIRED_FIELDS}closed{/if}">
            <div class="lc_customer_info_form">
                {if LC_Conversation::isUsedField('name')}
                    <div>
                        <input {if !LC_Conversation::isRequiredField('name')}placeholder="{l s='Your name' mod='ets_livechat'}"{else}placeholder="{l s='Your name *' mod='ets_livechat'}"{/if} class="form-control" type="text" name="name" value="" />
                    </div>
                {/if}
                {if LC_Conversation::isUsedField('email') || !$isAdminOnline}
                <div>
                    
                    <input {if !LC_Conversation::isRequiredField('email') && $isAdminOnline}placeholder="{l s='Email' mod='ets_livechat'}"{else}placeholder="{l s='Email *' mod='ets_livechat'}"{/if} class="form-control" type="text" name="email" value="" />
                </div>
                {/if}
                {if LC_Conversation::isUsedField('phone')}
                    <div>
                        <input {if !LC_Conversation::isRequiredField('phone')}placeholder="{l s='Phone number' mod='ets_livechat'}"{else}placeholder="{l s='Phone number *' mod='ets_livechat'}"{/if} class="form-control" type="text" name="phone" value="" />
                    </div>
                {/if}
                {if $departments}
                    <div>
                        <select class="form-control">
                            <option value="0">{l s='Select Department' mod='ets_livechat'}{if LC_Conversation::isRequiredField('departments')}&nbsp;*{/if}</option>
                            {foreach from=$departments item='department'}
                                <option value="{$department.id_departments|intval}">{$department.name|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                {/if}
            </div>
        </div>
        <div class="lc_text_area_demo {if !$config.ETS_LC_ENABLE_EMOTION_ICON || !$emotions}no_display_emotion{/if} {if $config.ETS_DISPLAY_SEND_BUTTON}show_send_box{/if} start_chating {if isset($needCaptcha) && $needCaptcha}show_capcha{/if}">
            <div class="lc_captcha {if isset($needCaptcha) && $needCaptcha}active{/if}">
                <img src="{$captcha|escape:'html':'utf-8'}" class="lc_captcha_img" />
                <input type="text" name="captcha" class="lc_captcha_input form-control" placeholder="{l s='Security code'  mod='ets_livechat'}" />
            </div>
            <textarea placeholder="{l s='Type a message' mod='ets_livechat'}" class="form-control" name=""></textarea>
            {if $config.ETS_LC_ENABLE_EMOTION_ICON && $emotions}
                <div class="lc_emotion{if isset($captcha) && $captcha} ena_captcha{/if}">
                    <ul>
                        {foreach from=$emotions key='name' item='icon'}
                            <li data-emotion="{$name|escape:'html':'utf-8'}" title="{$icon.title|escape:'html':'utf-8'}"><img alt="{$icon.title|escape:'html':'utf-8'}" src="{$livechatDir|escape:'html':'utf-8'}views/img/emotions/{$icon.img|escape:'html':'utf-8'}"/></li>
                        {/foreach}
                    </ul>
                </div>
            {/if}
            <div class="lc_send_box">
                <input class="btn btn-primary lc_send_start"{if $config.LC_BACKGROUD_COLOR_BUTTON} style="background:{$config.LC_BACKGROUD_COLOR_BUTTON|escape:'html':'utf-8'}"{/if} type="button" value="{$config.ETS_LC_TEXT_SEND_START_CHAT|escape:'html':'utf-8'}" />
            </div>
        </div>
    </div>
</div>
{if $config.LC_BACKGROUD_HOVER_BUTTON}
    {literal}
    <style>
    .lc_send_start:hover{
    {/literal}
            background :{$config.LC_BACKGROUD_HOVER_BUTTON|escape:'html':'UTF-8'}!important;
     {literal}
        }
     {/literal}
    </style>
{/if}