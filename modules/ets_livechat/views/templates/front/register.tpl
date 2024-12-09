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
* needs please, contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2019 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
<style type="text/css">
    .form-control {
      height: 35px;
      line-height: 33px;
      margin: 15px 0 20px;
      padding: 0 10px;
      width: 300px;
    }
    form {
      text-align: center;
    }
    label {
      display: block;
      margin-top: 30px;
      text-transform: uppercase;
    }
    .btn.btn-primary {
      background-color: #498af2;
      border: 1px solid #498af2;
      color: #fff;
      cursor: pointer;
      height: 36px;
      padding: 5px 15px;
    }
    .solo_register_form {
        text-align: center;
        padding-top: 5px;
    }
    #solo_register_form {
        display: inline-block;
        margin: 0 auto;
        text-align: center;
        border: 1px solid #ccc;
        padding: 30px;
    }
    .solo_register_errors {
        background-color: #ffc9cf;
        white-space: nowrap;
        border: 1px solid #f79197;
        display: inline-block;
        position: relative;
    }
    .solo_register_errors .close {
        position: absolute;
        right: 5px;
        background: transparent;
        border: none;
        margin-top: 7px;
        display: none;
    }
    .solo_register_errors ul {
        padding: 5px 20px;
        margin: 0;
        list-style: none;
        min-width: 200px;
    }

    .solo_register_form .form-control {
        border: 1px solid #ccc;
        color: #666;
    }
</style>
<div class="solo_register_form">
    <form id="solo_register_form" class="defaultForm form-horizontal" action="{$action|escape:'quotes'}" method="post" enctype="multipart/form-data">
        {if $errors}
            <div class="solo_register_errors">
                {$errors nofilter}
            </div>
        {/if}
        <section class="form-fields">
            <div class="form-group row">
                <div class="col-md-9 col-md-offset-3">
                    <h3>{l s='Register email' mod='ets_livechat'}</h3>
                </div>
            </div>
            {if isset($userProfile) && !$userProfile->firstName && !$userProfile->lastName && !$userProfile->displayName}
            <div class="form-group row">
                <label class="col-md-3 form-control-label">{l s='First name' mod='ets_livechat'}</label>
                <div class="col-md-6">
                    <input class="form-control" name="first_name" value="{if isset($smarty.post.first_name)}{$smarty.post.first_name|escape:'html':'utf-8'}{/if}" type="text">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label">{l s='Last name' mod='ets_livechat'}</label>
                <div class="col-md-6">
                    <input class="form-control" name="last_name" value="{if isset($smarty.post.last_name)}{$smarty.post.last_name|escape:'html':'utf-8'}{/if}" type="text">
                </div>
            </div>
            {/if}
            <div class="form-group row">
                <label class="col-md-3 form-control-label">{l s='Email address' mod='ets_livechat'}</label>
                <div class="col-md-6">
                    <input class="form-control" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'html':'utf-8'}{/if}" placeholder="your@email.com" type="email">
                </div>
            </div>
        </section>
        <footer class="form-footer text-xs-right">
            <input class="btn btn-primary" name="submitRegister" value="{l s='Register'  mod='ets_livechat'}" type="submit">
        </footer>
    </form>
</div>