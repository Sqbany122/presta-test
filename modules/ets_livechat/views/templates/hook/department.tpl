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
<div class="col-lg-1 col-xs-1">
    {$departments->id|intval}
</div>
<div class="col-lg-2 col-xs-2">
    {$departments->name|escape:'html':'UTF-8'}
</div>
<div class="col-lg-3 col-xs-3">
    {$departments->description|escape:'html':'UTF-8'}
</div>
<div class="col-lg-1 status col-xs-1">
    {if $departments->status}
        <a class="lc_staff_list_action field-is_featured list-action-enable action-enabled list-item-{$departments->id|intval}"  href="#" data-value="1" data-id="{$departments->id|intval}" title="{l s='Click to disabled' mod='ets_livechat'}">
            <i class="icon-check"></i>
        </a>
    {else}
        <a class="lc_staff_list_action field-enabled list-action-enable action-disabled list-item-{$departments->id|intval}" href="#" data-value="0" data-id="{$departments->id|intval}" title="{l s='Click to enable' mod='ets_livechat'}">
            <i class="icon-remove"></i>
        </a>
    {/if}
</div>
<div class="col-lg-3 col-xs-3">
    {if $departments->all_employees}
        {l s='All' mod='ets_livechat'}
    {else}
        {if $employees}
            {foreach from =$employees item='agent'}
                {$agent.firstname|escape:'html':'UTF-8'}&nbsp;{$agent.lastname|escape:'html':'UTF-8'} ({$agent.profile_name|escape:'html':'UTF-8'}) <br />
            {/foreach}
        {/if}
    {/if}
</div>
<div class="col-lg-1 col-xs-1 sort_order dragHandle">
    <div class="dragGroup">
    <span class="position">{$departments->sort_order|intval}</span>
    </div>
</div>
<div class="col-lg-1 col-xs-1">
    <span class="lg_edit edit_deparments" data-id="{$departments->id|intval}" title="{l s='Edit' mod='ets_livechat'}" >{l s='Edit' mod='ets_livechat'}</span>
    <span class="lg_delete delete_departments" data-id="{$departments->id|intval}" title="{l s='Delete' mod='ets_livechat'}" >{l s='Delete' mod='ets_livechat'}</span>
</div>