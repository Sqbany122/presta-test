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
{extends file='page.tpl'}
{block name='page_content'}
    <section id="main">
        <header class="page-header">
            <h1>{l s='Thank you' mod='prestacafenewpayu'}</h1>
        </header>

        <section id="content">
            {* order-detail.tpl *}
            <div class="box">
                {l s='Your order on %s is complete.' sprintf=[$shop_name] mod='prestacafenewpayu'}
                {l s='Your order will be sent as soon as we receive payment.' mod='prestacafenewpayu'}
                <br />
                {l s='If you have questions, comments or concerns, please contact our' mod='prestacafenewpayu'} <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='expert customer support team' mod='prestacafenewpayu'}</a>
            </div>
        </section>
    </section>
{/block}