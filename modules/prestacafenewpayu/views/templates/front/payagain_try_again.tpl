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

        {if $smarty.request.show_payu_error}
            <div class="warning">
                {l s='There was an error communicating with PayU. Click on the button below to try again.' mod='prestacafenewpayu'}
            </div>
        {/if}

        <form method="POST">
            <input type="hidden" name="id_cart" value="{$smarty.request.id_cart|escape:'htmlall':'UTF-8'}">
            <input type="hidden" name="secure_key_hash" value="{$smarty.request.secure_key_hash|escape:'htmlall':'UTF-8'}">
            <input type="hidden" name="secure_token" value="{$smarty.request.secure_token|escape:'htmlall':'UTF-8'}">

            <input type="submit" value="{l s='Try again' mod='prestacafenewpayu'}" class="exclusive">
        </form>
    </div>

    <br/> <br/>
{elseif version_compare($smarty.const._PS_VERSION_, '1.7', '<')}
    {capture name=path}{l s='PayU payment' mod='prestacafenewpayu'}{/capture}
    {assign var='current_step' value='payment'}
    <div id="carrier_area">
        <h1 class="page-heading">{l s='PayU payment' mod='prestacafenewpayu'}</h1>

        {if $smarty.request.show_payu_error}
            <div class="clearfix main-page-indent">
                <p class="alert alert-warning">
                    {l s='There was an error communicating with PayU. Click on the button below to try again.' mod='prestacafenewpayu'}
                </p>
            </div>
        {/if}

        <form method="POST">
            <input type="hidden" name="id_cart" value="{$smarty.request.id_cart|escape:'htmlall':'UTF-8'}">
            <input type="hidden" name="secure_key_hash" value="{$smarty.request.secure_key_hash|escape:'htmlall':'UTF-8'}">
            <input type="hidden" name="secure_token" value="{$smarty.request.secure_token|escape:'htmlall':'UTF-8'}">

            <div class="submit">
                <button type="submit" name="submitMessage" id="submitMessage" class="button btn btn-default button-medium"><span>{l s='Try again' mod='prestacafenewpayu'}<i class="icon-chevron-right right"></i></span></button>
            </div>
        </form>
    </div>
{/if}