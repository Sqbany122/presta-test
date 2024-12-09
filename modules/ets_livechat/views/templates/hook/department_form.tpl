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
<div class="block_form_departments_list_header">
    <h4 class="tab_title_c"><i class="fa fa-pencil-square-o"></i>{l s='Edit department' mod='ets_livechat'}</h4>
</div>
<div class="block_errors_departments" style="display:none;"> 
</div>
<div class="form-group">
    <label class="control-label col-lg-3" for="departments_status">{l s='Enable' mod='ets_livechat'}</label>
    <div class="col-lg-9">
        <span class="switch prestashop-switch fixed-width-lg">
    		<input name="departments_status" id="departments_status_on" value="1" type="radio" {if $departments.status}checked="checked"{/if} />
    		<label for="departments_status_on">{l s='Yes' mod='ets_livechat'}</label>
    		<input name="departments_status" id="departments_status_off" value="0" {if !$departments.status}checked="checked"{/if} type="radio" />
    		<label for="departments_status_off">{l s='No' mod='ets_livechat'}</label>
    		<a class="slide-button btn"></a>
    	</span>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-lg-3 required" for="departments_name">{l s='Name' mod='ets_livechat'}</label>
    <div class="col-lg-9">
        <input id="departments_name" class="" type="text" value="{$departments.name|escape:'html':'UTF-8'}" name="departments_name" />
    </div>
</div>
<div class="form-group">
    <label class="control-label col-lg-3" for="departments_description">{l s='Description' mod='ets_livechat'}</label>
    <div class="col-lg-9">
        <textarea id="departments_description" class="" name="departments_description">{$departments.description|escape:'html':'UTF-8'}</textarea>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-lg-3 required" for="departments_agents">{l s='Staffs' mod='ets_livechat'}</label>
    <div class="col-lg-9">
        <label for="departments_name_all"> <input class="departments_name_all" id="departments_name_all" type="checkbox" name="departments_name_all" value="1" {if $departments.all_employees}checked="checked"{/if} /> {l s='All' mod='ets_livechat'}</label><br />
        {foreach from =$employees item='employee'}
            <label for="departments_name_{$employee.id_employee|intval}"> <input class="departments_agents" id="departments_name_{$employee.id_employee|intval}" type="checkbox" {if $employee.id_profile!=1}name="departments_agents[]"{/if} value="{$employee.id_employee|intval}" {if $departments.all_employees || $employee.id_profile==1}checked="checked" disabled="disabled"{else}{if in_array($employee.id_employee,$departments.agents)}checked="checked"{/if}{/if} /> {$employee.firstname|escape:'html':'UTF-8'}&nbsp;{$employee.lastname|escape:'html':'UTF-8'}( {$employee.email|escape:'html':'UTF-8'} )</label><br />
        {/foreach}
    </div>
</div>
<input name="id_departments" value="{$departments.id_departments|intval}" type="hidden" id="id_departments"/>
<button id="cancel_departments" class="btn btn-default">{l s='Cancel' mod='ets_livechat'}</button>
<button id="submit_departments" class="btn btn-default">{l s='Save' mod='ets_livechat'}</button>