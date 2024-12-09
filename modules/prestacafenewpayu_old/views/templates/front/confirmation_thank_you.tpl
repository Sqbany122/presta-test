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
            {l s='Your order on %s is complete.' sprintf=$shop_name mod='prestacafenewpayu'}
            <br /><br />
            <strong>{l s='Your order will be sent as soon as we receive payment.' mod='prestacafenewpayu'}</strong>
            <br /><br />
            {l s='If you have questions, comments or concerns, please contact our' mod='prestacafenewpayu'} <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='expert customer support team' mod='prestacafenewpayu'}</a>
        </div>
    </div>

    <br/> <br/>
{elseif version_compare($smarty.const._PS_VERSION_, '1.7', '<')}
    {capture name=path}{l s='PayU payment' mod='prestacafenewpayu'}{/capture}
    {assign var='current_step' value='payment'}
    <div id="carrier_area">
        <p class="alert alert-success">{l s='Your order on %s is complete.' sprintf=$shop_name mod='prestacafenewpayu'}</p>
        <div class="box">
            <p><strong>{l s='Your order will be sent as soon as we receive payment.' mod='prestacafenewpayu'}</strong></p>
            <p>{l s='If you have questions, comments or concerns, please contact our' mod='prestacafenewpayu'} <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='expert customer support team' mod='prestacafenewpayu'}</a></p>
        </div>
    </div>
{/if}