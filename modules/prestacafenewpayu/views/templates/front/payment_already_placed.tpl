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
        <div class="success">
            {l s='An order has already been placed using this cart.' mod='prestacafenewpayu'}
        </div>
    </div>

    <p class="cart_navigation">
        <a href="{$link->getPageLink('index')|escape:'htmlall':'UTF-8'}" class="button_large" title="{l s='Continue shopping' mod='prestacafenewpayu'}">« {l s='Continue shopping' mod='prestacafenewpayu'}</a>
    </p>
{elseif version_compare($smarty.const._PS_VERSION_, '1.7', '<')}
    {capture name=path}{l s='PayU payment' mod='prestacafenewpayu'}{/capture}
    {assign var='current_step' value='payment'}
    <div id="carrier_area">
        <h1 class="page-heading">{l s='PayU payment' mod='prestacafenewpayu'}</h1>

        <div class="clearfix main-page-indent">
            <p class="alert alert-info">
                {l s='An order has already been placed using this cart.' mod='prestacafenewpayu'}
            </p>
        </div>

        <p class="cart_navigation clearfix">
            <a class="button-exclusive btn btn-default" href="{$link->getPageLink('index')|escape:'htmlall':'UTF-8'}">
                <i class="icon-chevron-left"></i>{l s='Continue shopping' mod='prestacafenewpayu'}
            </a>
        </p>
    </div>
{/if}