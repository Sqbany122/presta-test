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
        <section id="content">
            <div class="box">
                <aside id="notifications">
                    <article class="alert alert-info" role="alert" data-alert="info">
                        <ul>
                            <li>{l s='An order has already been placed using this cart.' mod='prestacafenewpayu'}</li>
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
{/block}