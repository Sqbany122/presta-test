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
<div class="block_errors_staff" style="display:none;"> 
</div>
<div class="form-group">
    <label class="control-label col-lg-3 " for="nick_name">{l s='Nick name' mod='ets_livechat'}</label>
    <div class="col-lg-9">
        <input id="nick_name" class="" type="text" value="{$employee.name|escape:'html':'UTF-8'}" name="nick_name" />
        <p style="color:grey"><em>If you do not enter a specific nick name, employee name will become nick name</em></p>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-lg-3" for="email_staff">{l s='Email' mod='ets_livechat'}</label>
    <div class="col-lg-9">
        <input id="email_staff" value="{$employee.email|escape:'html':'UTF-8'}" type="text" disabled="disabled" />
    </div>
</div>
<div class="form-group">
    <label class="control-label col-lg-3" for="avata_staff">{l s='Avatar' mod='ets_livechat'}</label>
    <div class="col-lg-9">
        <input type="file" name="avata_staff"  />
        <p class="help-block"> {l s='Available image types: jpg, png, gif, jpeg' mod='ets_livechat'}</p>
    </div>
    {if $employee.avata}
        <label class="control-label col-lg-3 uploaded_image_label " style="font-style: italic;">{l s='Uploaded image:' mod='ets_livechat'} </label>
        <div class="col-lg-9 uploaded_img_wrapper">
    		<a class="ybc_fancy" href="{$employee.avata|escape:'html':'UTF-8'}"><img style="display: inline-block; max-width: 200px;" src="{$employee.avata|escape:'html':'UTF-8'}" /></a>
            <a class="delete_avata_staff" style="display: inline-block; text-decoration: none!important;" href="#" data-id="{$employee.id_employee|intval}"><span style="color: #666"><i style="font-size: 20px;" class="process-icon-delete"></i></span></a>
        </div>
    {/if}
</div>
<div class="form-group">
    <label class="control-label col-lg-3" for="staff_signature">{l s='Signature' mod='ets_livechat'}</label>
    <div class="col-lg-9">
        <input id="staff_signature" name="signature" type="text" value="{$employee.signature|escape:'html':'UTF-8'}"/>
        <p class="help-block" > {l s='Signature is appended to ticket reply message' mod='ets_livechat'}</p>
    </div>
</div>
{if $id_profile==1}
    <div class="form-group">
        <label class="control-label col-lg-3" for="staff_status">{l s='Status' mod='ets_livechat'}</label>
        <div class="col-lg-9">
            <span class="switch prestashop-switch fixed-width-lg">
        		<input name="staff_status" id="staff_status_on" value="1" type="radio" {if $employee.status}checked="checked"{/if} />
        		<label for="staff_status_on">{l s='Enabled' mod='ets_livechat'}</label>
        		<input name="staff_status" id="staff_status_off" value="0" {if !$employee.status}checked="checked"{/if} type="radio" />
        		<label for="staff_status_off">{l s='Disabled' mod='ets_livechat'}</label>
        		<a class="slide-button btn"></a>
        	</span>
        </div>
    </div>
{/if}
<div class="form-group staffs_button">
    <label class="control-label col-lg-3" for="staff_button"></label>
    <div class="col-lg-9">
        <input name="id_employee" value="{$employee.id_employee|escape:'html':'UTF-8'}" type="hidden" id="id_employee"/>
        {if $id_profile==1}
            <button id="cancel_staff" class="btn btn-default"><i class="fa fa-times"></i> {l s='Cancel' mod='ets_livechat'}</button>
        {/if}
        <button id="submit_save_staff" class="btn btn-default"><i class="fa fa-floppy-o"></i> {l s='Save' mod='ets_livechat'}</button>
    </div>
</div>