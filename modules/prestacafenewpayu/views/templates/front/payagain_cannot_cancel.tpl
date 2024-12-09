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
        <div class="warning">
            {if isset($order)}
                {l s='The PayU payment started for order %s cannot be canceled now. Cannot pay again.' sprintf=$order->reference mod='prestacafenewpayu'}
            {else}
                {l s='The PayU payment cannot be canceled now. Cannot pay again.' mod='prestacafenewpayu'}
            {/if}
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
            <p class="alert alert-warning">
                {if isset($order)}
                    {l s='The PayU payment started for order %s cannot be canceled now. Cannot pay again.' sprintf=$order->reference mod='prestacafenewpayu'}
                {else}
                    {l s='The PayU payment cannot be canceled now. Cannot pay again.' mod='prestacafenewpayu'}
                {/if}
            </p>
        </div>

        <p class="cart_navigation clearfix">
            <a class="button-exclusive btn btn-default" href="{$link->getPageLink('index')|escape:'htmlall':'UTF-8'}">
                <i class="icon-chevron-left"></i>{l s='Continue shopping' mod='prestacafenewpayu'}
            </a>
        </p>
    </div>
{else}
    <section id="main">
        <section id="content">
            <div class="box">
                <aside id="notifications">
                    <article class="alert alert-warning" role="alert" data-alert="warning">
                        <ul>
                            {if isset($order)}
                                <li>{l s='The PayU payment started for order %s cannot be canceled now. Cannot pay again.' sprintf=$order->reference mod='prestacafenewpayu'}</li>
                            {else}
                                <li>{l s='The PayU payment cannot be canceled now. Cannot pay again.' mod='prestacafenewpayu'}</li>
                            {/if}
                        </ul>
                    </article>
                </aside>
            </div>
        </section>

        <footer class="page-footer">
            <a class="account-link" href="{$link->getPageLink('index')|escape:'html':'UTF-8'}">
                {l s='Continue shopping' mod='prestacafenewpayu'}
            </a>
        </footer>
    </section>
{/if}