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
{if $pre_made_messages}
    <ul class="pre_made_messages" data-id-conversation="0">                  
        <li class="header-list">
            <div class="left">{l s='Short code' mod='ets_livechat'}</div>
            <div class="right">{l s='Message' mod='ets_livechat'}</div>
        </li>
    {foreach from=$pre_made_messages item='pre_made_message'}
        <li class="made_message" data-id="{$pre_made_message.id_pre_made_message|intval}">
            <div class="left">{$pre_made_message.short_code|escape:'html':'utf-8'}</div>
            <div class="right">
                <div class="title-message">{$pre_made_message.title_message|escape:'html':'utf-8'}</div>
                <div class="content-message">{$pre_made_message.message_content nofilter}</div>
            </div>
        </li>
    {/foreach}
    </ul>
    <a href="{$link_pre_made_messages|escape:'html':'utf-8'}" target="_blank">{l s='Manage pre-made messages' mod='ets_livechat'}</a>
{/if}