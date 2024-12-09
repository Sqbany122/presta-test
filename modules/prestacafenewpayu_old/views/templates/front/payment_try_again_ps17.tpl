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
                {if $smarty.request.show_payu_error}
                    <aside id="notifications">
                        <article class="alert alert-warning" role="alert" data-alert="warning">
                            <ul>
                                <li>{l s='There was an error communicating with PayU servers. Click on the button below to try again.' mod='prestacafenewpayu'}</li>
                            </ul>
                        </article>
                    </aside>
                {/if}

                <form method="POST" action="{$link->getModuleLink('prestacafenewpayu', 'payment', [], true)|escape:'html':'UTF-8'}">
                    {if isset($smarty.request.pbl)}
                        <input type="hidden" name="pbl" value="{$smarty.request.pbl|escape:'htmlall':'UTF-8'}" />
                    {/if}
                    <div class="submit">
                        <button type="submit" name="submitMessage" id="submitMessage" class="btn-primary">
                            <span>{l s='Try again' mod='prestacafenewpayu'}<i class="icon-chevron-right right"></i></span>
                        </button>
                    </div>
                </form>

            </div>
        </section>

        <footer class="page-footer">
            <a class="account-link" href="{$link->getPageLink('index')|escape:'html':'UTF-8'}">
                {l s='Continue shopping' mod='prestacafenewpayu'}
            </a>
        </footer>
    </section>
{/block}