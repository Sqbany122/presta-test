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
<div class="lc-managament-information">
    <div class="panel lc-panel">
        <div class="panel-heading">
        {l s='Chat information' mod='ets_livechat'}
        </div>
    </div>
    <form class="defaultForm form-horizontal" novalidate="" enctype="multipart/form-data" method="post" action="">
        <section class="form-fields">
            <div class="form-group row ">
                <label class="col-md-3 form-control-label" for="customer_name">{l s='Name' mod='ets_livechat'}</label>
                <div class="col-md-9">
                    <input id="customer_name" class="form-control" readonly="true" type="text" value="{$customer->firstname|escape:'html':'UTF-8'} {$customer->lastname|escape:'html':'UTF-8'}" name="customer_name" />
                    <p class="help-block"> <a href="{$link->getPageLink('identity')|escape:'html':'UTF-8'}" title="{l s='Update my name' mod='ets_livechat'}">{l s='Update my name' mod='ets_livechat'}</a> </p>
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-md-3 form-control-label" for="customer_avata">{l s='Avatar' mod='ets_livechat'}</label>
                <div class="col-md-9">
                    <div class="upload_form_custom">
            			<span class="input-group-addon"><i class="fa fa-file"></i></span>
            			<span class="input-group-btn">
            				<i class="fa fa-folder-open"></i>{l s='Add file' mod='ets_livechat'}
    				    </span>
                        <input class="form-control" type="file" value="" name="customer_avata" id="customer_avata" />
            		</div>
                    {if $customer_avata}
                        <div class="customer_avata">
                            <img style="max-width: 200px;" src="{$customer_avata|escape:'html':'UTF-8'}" title="{$customer->firstname|escape:'html':'UTF-8'} {$customer->lastname|escape:'html':'UTF-8'}" alt="{$customer->firstname|escape:'html':'UTF-8'} {$customer->lastname|escape:'html':'UTF-8'}" />
                            <a title="{l s='Delete' mod='ets_livechat'}" onclick="return confirm('{l s='Do you want to delete avatar image?' mod='ets_livechat'}');" class="delete_url" href="{$link_delete_image|escape:'html':'UTF-8'}" style="display: inline-block; text-decoration: none!important;">
                                <span style="color: #666">
                                    <i class="fa fa-trash" style="font-size: 20px;"></i>{l s='Delete' mod='ets_livechat'}
                                </span>
                            </a>
                        </div>
                    {else}
                        <div class="customer_avata">
                            <img style="max-width: 200px;" src="{$avata_default|escape:'html':'UTF-8'}" title="{l s='Default avatar' mod='ets_livechat'}" />
                        </div>
                    {/if}
                    <p class="help-block">{l s='Available image type: jpg, png, gif, jpeg' mod='ets_livechat'}</p>
                </div>
            </div>
        </section>
        <button class="btn btn-primary float-xs-right" type="submit" name="submitCustomerInfo">{l s='Save' mod='ets_livechat'}</button>
    </form>
</div>