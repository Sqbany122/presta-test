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
            <h1>{l s='Please choose a payment method by clicking on one of the payment images below.' mod='prestacafenewpayu'}</h1>
        </header>

        <section id="content" class="page-content">
            <div class="box">
                {if isset($smarty.request.pbl)}
                    <aside id="notifications">
                        <div class="container">
                            <article class="alert alert-warning" role="alert" data-alert="warning">
                                <ul>
                                    <li>{l s='Please choose a payment method by clicking on one of the payment images below.' mod='prestacafenewpayu'}</li>
                                </ul>
                            </article>
                        </div>
                    </aside>
                {/if}

                <form method="POST" action="{$link->getModuleLink('prestacafenewpayu', 'payment', [], true)|escape:'html':'UTF-8'}">
                    <section>
                        <input type="hidden" name="pbl" />

                        {* All payments but the credit card which is displayed below separately *}
                        <div class="row">
                            {foreach $payByLinks as $pbl}
                                {if $pbl->status == 'ENABLED' && $pbl->value != 'c'}
                                    <div class="col-xs-4 col-md-2 text-center payu-pbl-cell payu-pbl-cell-ps17" onclick="PrestaCafePayu.onClickPaymentMethod(this, '{$pbl->value|escape:'html':'UTF-8'}');" data-pbl="{$pbl->value|escape:'htmlall':'UTF-8'}">
                                        <img src="{$pbl->brandImageUrl|escape:'html':'UTF-8'}">
                                    </div>
                                {/if}
                            {/foreach}
                        </div>

                        {* Payment by card is displayed separately *}
                        {if isset($payByLinks['c']) && $payByLinks['c']->status == 'ENABLED'}
                            <hr />
                            <div class="row">
                                <div class="col-xs-4 col-md-2 text-center payu-pbl-cell payu-pbl-cell-ps17" onclick="PrestaCafePayu.onClickPaymentMethod(this, 'c');" data-pbl="c">
                                    <img src="{$payByLinks['c']->brandImageUrl|escape:'html':'UTF-8'}">
                                </div>
                            </div>
                        {/if}

                    </section>

                    <footer class="form-footer clearfix">
                        <button class="btn btn-primary form-control-submit pull-xs-right" data-link-action="save-customer" type="submit">
                            {l s='Pay in PayU' mod='prestacafenewpayu'}
                        </button>
                    </footer>
                </form>
            </div>
        </section>

        <footer class="page-footer">
            <a class="account-link" href="{$link->getPageLink('order', true, null, "step=3")|escape:'html':'UTF-8'}">
                <i class="material-icons">chevron_left</i>
                <span>{l s='Other payment methods' mod='prestacafenewpayu'}</span>
            </a>
        </footer>

    </section>
{* The script is common for all Prestashop versions *}
<script type="text/javascript">
    $(document).ready(function() {
        PrestaCafePayu.preparePaymentMethodCells();
    });
</script>
{/block}