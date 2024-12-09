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
<h2 class="title">{l s='Recent customer login' mod='ets_livechat'}</h2>
<p class="sub_title">{l s='You have ' mod='ets_livechat'} {$count_login_customers|intval} {l s='customer(s) who logged in by social network account' mod='ets_livechat'}</p>
<ul class="lc_list_customer">
    {if $login_customers}
        {foreach from=$login_customers item='customer'}
            <li class="item_1">
                <span class="icon-social fa fa-{$customer.social|escape:'html':'UTF-8'}"></span>
                <span class="customer_email">{$customer.email|escape:'html':'UTF-8'}</span>
                <span class="conversation_date pull-right">{$customer.date_login|escape:'html':'UTF-8'}</span>
            </li>
        {/foreach}
    {/if}
</ul>