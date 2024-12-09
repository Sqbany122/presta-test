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
<script type="text/javascript">
    function prestacafenewpayu_resend_payagain_email(id_order) {
        $('#resend_ajax_loader').html('<img src="../img/admin/ajax-loader.gif">');
        $.ajax({
            type: 'POST',
            url: "{$link->getAdminLink('AdminPrestaCafeNewPayu')|escape:'javascript':'UTF-8'}",
            data: $('#prestacafenewpayu_form').serialize()
        }).done(function(data) {
            $('#resend_ajax_loader').html('');
            var result = eval('('+data+')');
            {if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
                if (result.status == 'ok') {
                    $("#prestacafenewpayu_message").html('<p class="info">{l s='Email sent' mod='prestacafenewpayu'}</p>');
                } else {
                    $("#prestacafenewpayu_message").html('<p class="warn">{l s='There was an error sending email' mod='prestacafenewpayu'}</p>');
                }
            {else}
                if (result.status == 'ok') {
                    $("#prestacafenewpayu_message").html('<p class="bg-info" style="padding:10px">{l s='Email sent' mod='prestacafenewpayu'}</p>');
                } else {
                    $("#prestacafenewpayu_message").html('<p class="bg-warning" style="padding:10px">{l s='There was an error sending email' mod='prestacafenewpayu'}</p>');
                }
            {/if}
        });
    }
</script>

{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
<br />
<fieldset class="prestacafenewpayu">
    <legend><img src="../img/admin/money.gif"> {l s='PayU transactions for this order' mod='prestacafenewpayu'}</legend>
{else}
<div class="tab-pane" id="prestacafenewpayu">
    <h4 class="visible-print">{l s='PayU transactions for this order' mod='prestacafenewpayu'}</h4>
{/if}

    <div class="col-lg-12" id="prestacafenewpayu_message">
    </div>

    {if $payments && $payments|@count > 0}
        <div class="form-horizontal">
            <div class="table-responsive">
                <table class="table" id="shipping_table">
                    <thead>
                    <tr>
                        <th>
                            <span class="title_box ">{l s='Created' mod='prestacafenewpayu'}</span>
                        </th>
                        <th>
                            <span class="title_box ">{l s='Updated' mod='prestacafenewpayu'}</span>
                        </th>
                        <th>
                            <span class="title_box ">{l s='Payment ID' mod='prestacafenewpayu'}</span>
                        </th>
                        <th>
                            <span class="title_box ">{l s='Status' mod='prestacafenewpayu'}</span>
                        </th>
                        <th>
                            <span class="title_box ">{l s='Amount' mod='prestacafenewpayu'}</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$payments item='payment'}
                        <tr>
                            <td>{$payment.date_add|escape:'htmlall':'UTF-8'}</td>
                            <td>{$payment.date_upd|escape:'htmlall':'UTF-8'}</td>
                            <td>{$payment.payu_payment_id|escape:'htmlall':'UTF-8'}</td>
                            <td>
                                {if $payment.payu_order_status == 'WAITING_FOR_CONFIRMATION'}<span class="label label-warning">{l s='Action required: confirm the payment in the PayU panel' mod='prestacafenewpayu'}</span> ({$payment.payu_order_status|escape:'htmlall':'UTF-8'}){else}{$payment.payu_order_status|escape:'htmlall':'UTF-8'}{/if}
                            </td>
                            <td>
                                {Tools::ps_round($payment.payu_total_amount/100.0, 2)|escape:'htmlall':'UTF-8'}
                                {$payment.payu_currency_code|escape:'htmlall':'UTF-8'}
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    {/if}

    {if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
        <div>
    {else}
        <div class="row">
            <div class="col-lg-12">
    {/if}
            <button {if $payagain_disabled}disabled="disabled"{/if}
                    type="button" onclick="prestacafenewpayu_resend_payagain_email({$id_order|escape:'htmlall':'UTF-8'}); return false;" class="btn btn-primary">
                {l s='Send the email with the payment link to the customer' mod='prestacafenewpayu'}
            </button>
            <span id="resend_ajax_loader"></span>
    {if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
        </div>
    {else}
            </div>
        </div>
    {/if}

    <div style="display:none">
        <form method="POST" id="prestacafenewpayu_form">
            <input type="hidden" name="id_order" value="{$id_order|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="secure_key" value="{$secure_key|escape:'htmlall':'UTF-8'}"/>
        </form>
    </div>

{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
</fieldset>
{else}
</div>
{/if}
