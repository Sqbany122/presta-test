{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE (for the parts taken from order-detail.tpl and order-detail-no-return.tpl)
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* 2009-2017 Blue Train Lukasz Kowalczyk / PrestaCafe
*
* NOTICE OF LICENSE (for the rest of the file)
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
{block name="page_content"}
    <section id="main">
        {if isset($smarty.request.show_payu_error) && $smarty.request.show_payu_error}
            <header class="page-header">
                <h1>{l s='Your previous payment for this order failed. Please, try paying again.' mod='prestacafenewpayu'}</h1>
            </header>
        {/if}

        <section id="content">
            {if isset($smarty.request.pbl)}
                <aside id="notifications">
                    <article class="alert alert-warning" role="alert" data-alert="warning">
                        <ul>
                            <li>{l s='Please choose a payment method by clicking on one of the payment images below.' mod='prestacafenewpayu'}</li>
                        </ul>
                    </article>
                </aside>
            {/if}

            {* order-detail.tpl *}
            <div class="box">
                <strong>
                    {l s='Order Reference %s - placed on %s' mod='prestacafenewpayu' sprintf=[$order.details.reference, $order.details.order_date]}
                </strong>
                {* XXX: removed "Reorder" link from here *}
            </div>

            {* order-detail-no-return.tpl *}
            <div class="box">

                <table id="order-products" class="table table-bordered">
                    <thead class="thead-default">
                    <tr>
                        <th>{l s='Reference' mod='prestacafenewpayu'}</th>
                        <th>{l s='Product' mod='prestacafenewpayu'}</th>
                        <th>{l s='Quantity' mod='prestacafenewpayu'}</th>
                        <th>{l s='Unit price' mod='prestacafenewpayu'}</th>
                        <th class="text-xs-right">{l s='Total price' mod='prestacafenewpayu'}</th>
                    </tr>
                    </thead>

                    {foreach from=$order.products item=product}
                        <tr>
                            <td>{$product.reference|escape:'htmlall':'UTF-8'}</td>
                            <td>{$product.name|escape:'htmlall':'UTF-8'}</td>
                            <td>{$product.quantity|escape:'htmlall':'UTF-8'}</td>
                            <td>{$product.price|escape:'html':'UTF-8'}</td>
                            <td class="text-xs-right">{$product.total|escape:'html':'UTF-8'}</td>
                        </tr>
                        {if $product.customizations}
                            {foreach $product.customizations  as $customization}
                                <tr>
                                    <td colspan="2">
                                        <ul>
                                            {foreach from=$customization.fields item=field}
                                                {if $field.type == 'image'}
                                                    <li><img src="{$field.image.small.url|escape:'htmlall':'UTF-8'}" alt=""></li>
                                                {elseif $field.type == 'text'}
                                                    <li>{$field.label|escape:'htmlall':'UTF-8'} : {if (int)$field.id_module}{$field.text nofilter}{else}{$field.text|escape:'htmlall':'UTF-8'}{/if}</li>
                                                {/if}
                                            {/foreach}
                                        </ul>
                                    </td>
                                    <td>{$customization.quantity|escape:'htmlall':'UTF-8'}</td>
                                    <td colspan="2"></td>
                                </tr>
                            {/foreach}
                        {/if}
                    {/foreach}

                    <tfoot>
                    {foreach $order.subtotals as $line}
                        <tr class="text-xs-right line-{$line.type|escape:'htmlall':'UTF-8'}">
                            <td colspan="4">{$line.label|escape:'htmlall':'UTF-8'}</td>
                            <td>{$line.value|escape:'html':'UTF-8'}</td>
                        </tr>
                    {/foreach}

                    <tr class="text-xs-right line-{$order.totals.total.type|escape:'htmlall':'UTF-8'}">
                        <td colspan="4">{$order.totals.total.label|escape:'htmlall':'UTF-8'}</td>
                        <td>{$order.totals.total.value|escape:'html':'UTF-8'}</td>
                    </tr>
                    </tfoot>
                </table>

            </div>

            {if $display_payment_methods}

                <div class="box">
                    <form method="POST" action="{$link->getModuleLink('prestacafenewpayu', 'payagain', ['cart_displayed' => '1', 'secure_key_hash' => $smarty.request.secure_key_hash, 'secure_token' => $smarty.request.secure_token, 'id_cart' => $smarty.request.id_cart], true)|escape:'html':'UTF-8'}">
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

            {else}

                {assign var=account_payment_link value=$link->getModuleLink('prestacafenewpayu', 'payagain', ['cart_displayed' => '1', 'secure_key_hash' => $smarty.request.secure_key_hash, 'secure_token' => $smarty.request.secure_token, 'id_cart' => $smarty.request.id_cart], true)}
                {assign var=card_payment_link value=$link->getModuleLink('prestacafenewpayu', 'payagain', ['pbl' => 'c', 'cart_displayed' => '1', 'secure_key_hash' => $smarty.request.secure_key_hash, 'secure_token' => $smarty.request.secure_token, 'id_cart' => $smarty.request.id_cart], true)}

                <div class="box">

                    {if $show_basic_payment}
                        <a class="btn-block btn-primary" href="{$account_payment_link|escape:'html':'UTF-8'}">{l s='Pay in PayU' mod='prestacafenewpayu'}{if $surcharge} ({l s='additional surcharge:' mod='prestacafenewpayu'} {Tools::displayPrice($surcharge)}){/if}</a>
                    {/if}

                    {if $show_direct_card}
                        <a class="btn-block btn-primary" href="{$card_payment_link|escape:'html':'UTF-8'}">{l s='Pay by card in PayU' mod='prestacafenewpayu'}</a>
                    {/if}

                </div>

            {/if}
        </section>
    </section>{* id="main" *}

    <script type="text/javascript">
        $(document).ready(function() {
            PrestaCafePayu.preparePaymentMethodCells();
        });
    </script>
{/block}