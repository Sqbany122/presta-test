{*
 * 2009-2017 Blue Train Lukasz Kowalczyk / PrestaCafe
 *
 * NOTICE OF LICENSE
 *
 * Lukasz Kowalczyk is the owner of the copyright of this module.
 * All rights of any kind, which are not expressly granted in this
 * License, are entirely and exclusively reserved to and by Lukasz
 * Kowalczyk.
 *
 * You may not rent, lease, transfer, modify or create derivative
 * works based on this module.
 *
 * @author    Lukasz Kowalczyk <lkowalczyk@prestacafe.pl>
 * @copyright Lukasz Kowalczyk
 * @license   LICENSE.txt
*}
{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
    {assign var='current_step' value='payment'}
    {include file="$tpl_dir./order-steps.tpl"}
    {include file="$tpl_dir./errors.tpl"}

    <div class="paiement_block">
        {if isset($smarty.request.pbl)}
            <div class="warning">
                {l s='Please choose a payment method by clicking on one of the payment images below.' mod='prestacafenewpayu'}
            </div>
        {/if}

        <h3>{l s='Please choose a payment method by clicking on one of the payment images below.' mod='prestacafenewpayu'}</h3>

        <br/>

        <form method="POST">
            <input type="hidden" name="pbl" />

            {* All payments but the credit card which is displayed below separately *}
            <div class="clearfix">
                {foreach $payByLinks as $value => $pbl}
                    {if $pbl->status == 'ENABLED' && $value != 'c'}
                        <div class="payu-pbl-cell payu-pbl-cell-ps15" onclick="PrestaCafePayu.onClickPaymentMethod(this, '{$value|escape:'html':'UTF-8'}');" data-pbl="{$value|escape:'htmlall':'UTF-8'}">
                            <img src="{$pbl->brandImageUrl|escape:'htmlall':'UTF-8'}">
                        </div>
                    {/if}
                {/foreach}
            </div>

            {* Payment by card is displayed separately *}
            {if isset($payByLinks['c']) && $payByLinks['c']->status == 'ENABLED'}
                <hr />
                <div class="clearfix">
                    <div class="payu-pbl-cell payu-pbl-cell-ps15" onclick="PrestaCafePayu.onClickPaymentMethod(this, 'c');" data-pbl="c">
                        <img src="{$payByLinks['c']->brandImageUrl|escape:'htmlall':'UTF-8'}">
                    </div>
                </div>
            {/if}

            <br />

            <div style="text-align: center;">
                <input type="submit" name="submitMessage" id="submitMessage" class="exclusive_large" value="{l s='Pay in PayU' mod='prestacafenewpayu'}"/>
            </div>
        </form>

        <br />

        <p class="cart_navigation">
            <a href="{$link->getPageLink('order', true, null, "step=3")|escape:'htmlall':'UTF-8'}" class="button_large" title="{l s='Other payment methods' mod='prestacafenewpayu'}">« {l s='Other payment methods' mod='prestacafenewpayu'}</a>
        </p>
    </div>

    <br/> <br/>
{elseif version_compare($smarty.const._PS_VERSION_, '1.7', '<')}
    {capture name=path}{l s='PayU payment:' mod='prestacafenewpayu'}{/capture}
    {assign var='current_step' value='payment'}

    {*<h1 class="page-heading">{l s='PayU payment:' mod='prestacafenewpayu'}</h1>*}
    {include file="$tpl_dir./order-steps.tpl"}
    {include file="$tpl_dir./errors.tpl"}

    {if isset($smarty.request.pbl)}
        <div class="alert alert-warning">
            <p>{l s='Please choose a payment method by clicking on one of the payment images below.' mod='prestacafenewpayu'}</p>
        </div>
    {/if}

    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <h3>{l s='Please choose a payment method by clicking on one of the payment images below.' mod='prestacafenewpayu'}</h3>

            <br/>

            <form method="POST">
                <input type="hidden" name="pbl" />

                {* All payments but the credit card which is displayed below separately *}
                <div class="row">
                    {foreach $payByLinks as $pbl}
                        {if $pbl->status == 'ENABLED' && $pbl->value != 'c'}
                            <div class="col-xs-4 col-md-2 text-center payu-pbl-cell payu-pbl-cell-ps16" onclick="PrestaCafePayu.onClickPaymentMethod(this, '{$pbl->value|escape:'html':'UTF-8'}');" data-pbl="{$pbl->value|escape:'htmlall':'UTF-8'}">
                                <img src="{$pbl->brandImageUrl|escape:'htmlall':'UTF-8'}">
                            </div>
                        {/if}
                    {/foreach}
                </div>

                {* Payment by card is displayed separately *}
                {if isset($payByLinks['c']) && $payByLinks['c']->status == 'ENABLED'}
                    <hr />
                    <div class="row">
                        <div class="col-xs-4 col-md-2 text-center payu-pbl-cell payu-pbl-cell-ps16" onclick="PrestaCafePayu.onClickPaymentMethod(this, 'c');" data-pbl="c">
                            <img src="{$payByLinks['c']->brandImageUrl|escape:'htmlall':'UTF-8'}">
                        </div>
                    </div>
                {/if}

                <div class="text-center submit" style="padding-top:20px">
                    <button type="submit" name="submitMessage" id="submitMessage" class="button btn btn-default button-medium"><span>{l s='Pay in PayU' mod='prestacafenewpayu'}<i class="icon-chevron-right right"></i></span></button>
                </div>
            </form>

        </div>
        <div class="col-md-1"></div>
    </div>

    <br />

    <p class="cart_navigation clearfix">
        <a class="button-exclusive btn btn-default" href="{$link->getPageLink('order', true, null, "step=3")|escape:'htmlall':'UTF-8'}">
            <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='prestacafenewpayu'}
        </a>
    </p>
{/if}
{* The script is common for all Prestashop versions *}
<script type="text/javascript">
    $(document).ready(function() {
        PrestaCafePayu.preparePaymentMethodCells();
    });
</script>
