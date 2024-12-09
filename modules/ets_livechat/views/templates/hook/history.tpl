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
<h3>{l s='Chat History' mod='ets_livechat'}</h3>
{if $conversations}
    <table class="list-conversation-table" border="1">
        <tr>
            <td>{l s='ID' mod='ets_livechat'}</td>
            <td>{l s='Last messages' mod='ets_livechat'}</td>
            {*<td>{l s='Date' mod='ets_livechat'}</td>*}
            <td>{l s='Action' mod='ets_livechat'}</td>
        </tr>
        {foreach from=$conversations item='conversation'}
            <tr>
                <td>{$conversation.id_conversation|intval}</td>
                <td>{$conversation.last_message.message nofilter}<br /><span class="lvc_history_date">{$conversation.last_message.datetime_added|escape:'html':'utf-8'}</span></td>
                {*<td><div class="lc_msg_time">{$conversation.last_message.datetime_added|escape:'html':'utf-8'}</div></td>*}
                <td><a class="view-conversation" data-id="{$conversation.id_conversation|intval}" href="{$conversation.link_view|escape:'html':'UTF-8'}" title="{l s='View' mod='ets_livechat'}"><i class="fa fa-eye"></i>{l s='View' mod='ets_livechat'}</a></td>
            </tr>
        {/foreach}
    </table>
    {$paggination nofilter}
{else}
    {l s='No conversation available' mod='ets_livechat'}
{/if}