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
{if $messages}
    <h1>{if $link_back}
        <a class="back-conversation-list" title="{l s='Back' mod='ets_livechat'}" href="{$link_back|escape:'html':'UTF-8'}">{l s='Back' mod='ets_livechat'}</a>
    {else}
        <button class="back-conversation-list" title="{l s='Back' mod='ets_livechat'}">{l s='Back' mod='ets_livechat'}</button>
    {/if}{l s='Conversation' mod='ets_livechat'} #{$messages[0].id_conversation|intval}</h1>
    
    <ul class="conversation-list-messages">
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
            </li>
        {/foreach}
    </ul>
{/if}