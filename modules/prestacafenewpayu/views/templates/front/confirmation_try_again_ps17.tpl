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
            <h1>{l s='Your payment for this order failed. Please, try paying again.' mod='prestacafenewpayu'}</h1>
        </header>

        <section id="content">
            <div class="box">
                <aside id="notifications">
                    <article class="alert alert-warning" role="alert" data-alert="warning">
                        <ul>
                            <li>{l s='There was a problem with your PayU payment. Click on the button below to try again.' mod='prestacafenewpayu'}</li>
                        </ul>
                    </article>
                </aside>

                <a class="btn-primary"
                   href="{$link->getModuleLink('prestacafenewpayu', 'payagain', ['id_cart' => $smarty.request.id_cart, 'secure_key_hash' => $smarty.request.secure_key_hash, 'secure_token' => $smarty.request.secure_token, 'show_payu_error' => '1'], true)|escape:'html':'UTF-8'}">
                    {l s='Pay in PayU' mod='prestacafenewpayu'}
                </a>
            </div>
        </section>
    </section>
{/block}