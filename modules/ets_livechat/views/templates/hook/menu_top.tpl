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
<ul class="lc_menu_top">
    {if $id_profile==1}
        <li {if $controller=='AdminLiveChatDashboard'}class="active"{/if}><a href="{$link->getAdminLink('AdminLiveChatDashboard')|escape:'html':'UTF-8'}"><i class="material-icons">trending_up</i>{l s='Dashboard' mod='ets_livechat'}</a></li>
    {/if}
    <li {if $controller=='AdminLiveChatTickets'}class="active"{/if}><a href="{$link->getAdminLink('AdminLiveChatTickets')|escape:'html':'UTF-8'}"><i class="icon icon-ticket"> </i>{l s='Tickets' mod='ets_livechat'}</a></li>
    <li {if $controller=='AdminModules'}class="active"{/if}><a href="{$link->getAdminLink('AdminLiveChatSettings')|escape:'html':'UTF-8'}"><i class="icon-AdminAdmin"></i>{l s='Settings' mod='ets_livechat'}</a></li>
    {if $id_profile==1}
        <li {if $controller=='AdminLiveChatHelp'}class="active"{/if}><a href="{$link->getAdminLink('AdminLiveChatHelp')|escape:'html':'UTF-8'}"><i class="icon icon-question-circle"> </i>{l s='Help' mod='ets_livechat'}</a></li>
     {/if}
</ul>