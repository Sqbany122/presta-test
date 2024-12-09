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
<div class="block_form_staff_list">
    <div class="block_form_staffs_list_header">
        <h4 class="tab_title_c">{l s='Staffs' mod='ets_livechat'}</h4>
    </div>
    <div id="form_staff" style="display:none;">
    
    </div>
    <div id="lc_staff_list" class="lc_staff_list">
    <div id="staff_0" class="lc_staff_list form-group">
        <div class="col-lg-2">{l s='Name' mod='ets_livechat'}</div>
        <div class="col-lg-2 nick_name">{l s='Nick name' mod='ets_livechat'}</div>
         <div class="col-lg-1 signature">{l s='Signature' mod='ets_livechat'}</div>
        <div class="col-lg-1 status">{l s='Status' mod='ets_livechat'}</div>
        <div class="col-lg-3 email">{l s='Email' mod='ets_livechat'}</div>
        <div class="col-lg-2 avata_staff">{l s='Avatar' mod='ets_livechat'}</div>
        <div class="col-lg-1">{l s='Action' mod='ets_livechat'}</div>
    </div>
    {if $employees}
        {foreach from=$employees item='employee'}
            <div id="staff_{$employee.id_employee|intval}" class="lc_staff_list form-group">
                <div class="col-lg-2 name" >{$employee.firstname|escape:'html':'UTF-8'}&nbsp;{$employee.lastname|escape:'html':'UTF-8'}</div>
                <div class="col-lg-2 nick_name">{$employee.name|escape:'html':'UTF-8'}</div>
                <div class="col-lg-1 signature">{$employee.signature|escape:'html':'UTF-8'}</div>
                <div class="col-lg-1 status col-xs-1">
                        {if $employee.status}
                            <a class="lc_staff_list_action field-is_featured list-action-enable action-enabled list-item-{$employee.id_employee|intval}"  href="#" data-value="1" data-id="{$employee.id_employee|intval}" title="{l s='Click to disabled' mod='ets_livechat'}">
                                <i class="icon-check"></i>
                            </a>
                        {else}
                            <a class="lc_staff_list_action field-enabled list-action-enable action-disabled list-item-{$employee.id_employee|intval}" href="#" data-value="0" data-id="{$employee.id_employee|intval}" title="{l s='Click to Enable' mod='ets_livechat'}">
                                <i class="icon-remove"></i>
                            </a>
                        {/if}
                </div>
                <div class="col-lg-3 email">{$employee.email|escape:'html':'utf-8'}</div>
                <div class="col-lg-2 avata_staff">
                    {if $employee.avata}<img src="{$employee.avata|escape:'html':'UTF-8'}" />{/if}
                </div>
                <div class="col-lg-1">
                    <span class="lg_edit edit_staff" data-id="{$employee.id_employee|intval}" title="{l s='Edit' mod='ets_livechat'}" >{l s='Edit' mod='ets_livechat'}</span>
                </div>
            </div>
        {/foreach}
    {/if}
</div>
</div>