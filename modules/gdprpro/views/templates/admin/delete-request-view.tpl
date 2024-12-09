{*
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 *}
<div class="container">
    <h2>
        {l s='Data delete request for' mod='gdprpro'}
        {$customer->firstname}
        {$customer->lastname}
    </h2>
    <div class="panel">
        <div class="panel-body">
            <p>
                <b>{l s='Fulfilled at' mod='gdprpro'}:</b>
                {if $request->status == 0}
                    <span class="label label-info">{l s='Unfulfilled' mod='gdprpro'}</span>
                {else}
                    <span class="label label-success">{l s='Fulfilled' mod='gdprpro'}</span>
                {/if}
            </p>
            <p>
                <b>{l s='Fulfilled at' mod='gdprpro'}:</b>
                {$request->fulfilled_at}
            </p>
            <p>
                <b>{l s='Requested at' mod='gdprpro'}:</b>
                {$request->created_at}
            </p>
            <hr>

            <div class="clearfix"></div>
            {include file="./customer-data.tpl"}
        </div>
        <div class="panel-footer">
            {if $request->status == 0}
                <button href="" class="btn btn-default pull-right" data-toggle="modal" data-target="#myModal">
                    <i class="process-icon-ok "></i>
                    {l s='Anonimize' mod='gdprpro'}
                </button>
            {else}
                <div class="alert alert-info">
                    {l s='This request has been already fulfilled' mod='gdprpro'}
                </div>
            {/if}

        </div>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    {l s='Confirm data anonymization for' mod='gdprpro'}
                    {$customer->firstname}
                    {$customer->lastname}
                </h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    {l s='Please note that this process cannot be undone. The data below will be completely anonymized' mod='gdprpro'}
                </div>
                {*<p class="text-center">*}
                    {*<img src="https://media0.giphy.com/media/K64409MbT84rm/giphy.gif" style="width: 100%">*}
                {*</p>*}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Cancel' mod='gdprpro'}</button>
                {if $numberOfInvoices > 0}
                    <a href="{$downloadInvoicesLink}" target="_blank"
                       class="btn btn-primary">{l s='Download invoices' mod='gdprpro'}</a>
                {/if}
                <a href="{$deleteLink}" class="btn btn-danger">{l s='Anonymize' mod='gdprpro'}</a>
            </div>
        </div>
    </div>
</div>
