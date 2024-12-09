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
<!-- MODULE ets_livechat -->
<li class="col-lg-4 col-md-6 col-sm-6 col-xs-12" >
<a id="chat-info-link" href="{$link->getModuleLink('ets_livechat','info')|escape:'html':'UTF-8'}" title="{l s='Chat info' mod='ets_livechat'}">
    <span class="link-item">
        <span class="ss_icon_group">
            <i class="fa fa-file-text-o"></i>
        </span>
        {l s='Chat info' mod='ets_livechat'}
    </span>
</a>
</li> 
<li class="col-lg-4 col-md-6 col-sm-6 col-xs-12" >
<a id="chat-tickets-link" href="{$link->getModuleLink('ets_livechat','ticket')|escape:'html':'UTF-8'}" title="{l s='Support tickets' mod='ets_livechat'}">
    <span class="link-item">
        <span class="ss_icon_group">
            <i class="fa fa-ticket"></i>
        </span>
        {l s='Support tickets' mod='ets_livechat'}{if $count_support} ({$count_support|intval}){/if}
    </span>
</a>
</li>
{if $ETS_LC_CUSTOMER_OLD}
<li class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
<a id="chat-history-link" href="{$link->getModuleLink('ets_livechat','history')|escape:'html':'UTF-8'}" title="{l s='Chat history' mod='ets_livechat'}">
    <span class="link-item">
        <span class="ss_icon_group">
            <i class="fa fa-history"></i>
        </span>
        {l s='Chat history' mod='ets_livechat'}
    </span>
</a>
</li>
{/if}
<!-- END : MODULE ets_livechat -->