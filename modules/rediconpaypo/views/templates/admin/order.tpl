{**
* redicon.pl
* @author Patryk <patryk@redicon.pl>
* @copyright redicon.pl
* @license redicon.pl
*}


    <div id="settings" class="panel card">
        <div class="panel-heading card-header">
            <i class="icon-file-text"></i> {$title_payment|escape:'htmlall':'UTF-8'}</span>
        </div>
        <div class="well form-horizontal card-body">
            <div class="row">
                {if $show_returns }
                <div class="col-md-6">
                    <div class="form-group">
                        <h3 class="modal-title">
                            {$text_return_modal|escape:'htmlall':'UTF-8'}
                        </h3>
                        <label for="paypo-return-value">{$text_return_label|escape:'html':'UTF-8'} </label>
                        <input type="number" id="paypo-return-value" value="{$max_value}" class="form-control"
                            max="{$max_value|escape:'htmlall':'UTF-8'}"
                            onkeyup="presEnter(event, '{$id_order|escape:'htmlall':'UTF-8'}')">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default" id="paypo-return-button" type="button"
                            onclick="sendReturn('{$id_order|escape:'htmlall':'UTF-8'}')">{$text_save|escape:'htmlall':'UTF-8'}</button>
                    </div>

                    {if $returns }
                    <h5>{$title_return|escape:'htmlall':'UTF-8'}</h5>
                    <ul>
                        {foreach from=$returns item=r}
                        <li>
                            <small style="display:block;">{$r.employee|escape:'htmlall':'UTF-8'}</small>
                            {$r.created_at|escape:'htmlall':'UTF-8'} -
                            {$text_before|escape:'htmlall':'UTF-8'}{$r.before_amount|escape:'htmlall':'UTF-8'}
                            {$text_after|escape:'htmlall':'UTF-8'}{$r.amount|escape:'htmlall':'UTF-8'}
                        </li>
                        {/foreach}
                    </ul>
                    {/if}
                </div>
                {/if}
                <div class="{if $show_returns }col-md-6{else}col-md-12{/if}">
                    <div class="form-group row">
                        <label class="control-label col-lg-3">
                            id_transaction
                        </label>
                        <div class="col-lg-9">
                            <input type="text" value="{$transaction.id_transaction|escape:'htmlall':'UTF-8'}"
                                class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-lg-3">
                            referenceId
                        </label>
                        <div class="col-lg-9">
                            <input type="text" value="{$transaction.referenceId|escape:'htmlall':'UTF-8'}"
                                class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-lg-3">
                            redirectUrl
                        </label>
                        <div class="col-lg-9">
                            <input type="text" value="{$transaction.redirectUrl|escape:'htmlall':'UTF-8'}"
                                class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-lg-3">
                            id_cart
                        </label>
                        <div class="col-lg-9">
                            <input type="text" value="{$transaction.id_cart|escape:'htmlall':'UTF-8'}"
                                class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-lg-3">
                            total
                        </label>
                        <div class="col-lg-9">
                            <input type="text" value="{$transaction.total|escape:'htmlall':'UTF-8'}"
                                class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-lg-3">
                            created_at
                        </label>
                        <div class="col-lg-9">
                            <input type="text" value="{$transaction.created_at|escape:'htmlall':'UTF-8'}"
                                class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-lg-3">
                            Notyfikacje:
                        </label>
                        <div class="col-lg-9">
                            <ul>
                                {foreach from=$notifications item=row}
                                <li>{$row.created_at|escape:'htmlall':'UTF-8'} -
                                    ({$row.transactionStatus|escape:'htmlall':'UTF-8'})</li>
                                {/foreach}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function presEnter(event, idOrder) {
            if (event.key === "Enter") {
                sendReturn(idOrder);
            }
        }
        function sendReturn(idOrder) {
            var element = '[id="paypo-return-button"]';
            var oldhtml = $(element).html();
            var amount = $('input[id="paypo-return-value"]').val();
            if (parseFloat(amount)) {

                var data = {};
                data.amount = amount;
                $.ajax({
                    type: "POST",
                    url: "{$return_ajax_url}",
                    data: data,
                    dataType: 'json',
                    async: true,
                    beforeSend: function () {
                        $(element).html("{l s='zapisuje...' mod='rediconpaypo'}");
                    },
                    success: function (a) {
                        // a = JSON.parse(a);
                        if (a != 'success') {
                            alert("{l s='Maksymalny zwrot to:' mod='rediconpaypo'} " + a);
                            $('input[id="paypo-return-value"]').val(parseFloat(a))
                        } else {
                            alert("{l s='Dodano zwrot, po odświeżeniu strony pojawi się na liście' mod='rediconpaypo'}");
                            $('input[id="paypo-return-value"]').val(0)
                            location.reload();
                        }
                        $(element).html(oldhtml);
                    },
                    error: function (a) {
                        alert(a.responseText)
                    }
                });
            } else {
                alert("{l s='Kwota zwrotu nie może być pusta lub równa 0' mod='rediconpaypo'}");
            }
        }
    </script>
