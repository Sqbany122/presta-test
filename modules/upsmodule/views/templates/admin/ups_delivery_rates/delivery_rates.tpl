{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
<style>
    {literal}
        .adminupsdeliveryrates
        {
            background-color:#eff1f2!important;
            font-size: 12.4px!important;
        }
    {/literal}
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="defaultForm form-horizontal">
            <div class="panel">
                <div class="panel-heading">
                    {$arrtext.txtDelivery|escape:'htmlall':'UTF-8'}
                </div>
                <div id="collapseSix" class="collapse show" aria-labelledby="headingSix" data-parent="#accordion">
                    <div class="form-wrapper">
                        <form class="form-horizontal" action="index.php?controller=AdminUpsDeliveryRates&token={getAdminToken tab='AdminUpsDeliveryRates'}" method="post">
                            <div class="form-group row">
                                <label class="control-label col-lg-2">{$arrtext.txtDeliveryCurrency|escape:'htmlall':'UTF-8'}</label>
                                <div class="col-lg-3">
                                    <input class="col-lg-3" type="text" id="currency" readonly name="currency" value="{$selectedCurrency|escape:'htmlall':'UTF-8'}">
                                </div>
                            </div>
							{if $us == 1}
							<label class="col-lg-12 text-left">{$textHeader0|escape:'htmlall':'UTF-8'}</label>
							<div class="form-group row">
								<div class="row">
									<label class="control-label text-left">{$textHeader1|escape:'htmlall':'UTF-8'} <a href="{$textHeader1Link|escape:'htmlall':'UTF-8'}"  target="_blank">{$textHeader1Link|escape:'htmlall':'UTF-8'}</a></label> 
									<label class="control-label text-left">{$textHeader2|escape:'htmlall':'UTF-8'}</label>
								</div>
                            </div>
							{/if}
                            {if $listServiceAp|@count gt 0}
                                <label class="col-lg-12">{$arrtext.txtDeliveryUPSAp|escape:'htmlall':'UTF-8'}</label>
                                {foreach $listServiceAp as $index => $serivce}
                                    <div class="form-group row">
                                        <label class="control-label col-lg-2 optionFlatReal">{$serivce.name|escape:'htmlall':'UTF-8'}<br/></label>
                                        <div class="col-lg-3 optionFlatReal">
                                            <select class="form-control ups-form-control w260 ap-service-type optionCurrency" name="{$serivce.key|escape:'htmlall':'UTF-8'}_Type" id="ap_serviceType_{$index|escape:'htmlall':'UTF-8'}">
                                                <option value="FLAT_RATE" {if $serivce.type == 'FLAT_RATE'}selected="selected"{/if}>{$arrtext.txtDeliveryFlatRates|escape:'htmlall':'UTF-8'}</option>
                                                <option value="REAL_TIME" {if $serivce.type == 'REAL_TIME'}selected="selected"{/if}>{$arrtext.txtDeliveryRealTime|escape:'htmlall':'UTF-8'}</option>
                                            </select>
                                        </div>
                                        {if is_array($serivce.val)}
                                            <div class="col-lg-6 ship_ups" id="Ap_listRates_{$index|escape:'htmlall':'UTF-8'}">
                                                <input class="col-lg-3" type="hidden" id="Ap_countRates_{$index|escape:'htmlall':'UTF-8'}" name="{$serivce.key|escape:'htmlall':'UTF-8'}" value="{$serivce.val|count}">
                                                {foreach $serivce.val as $indexRates => $rates}
                                                    <div class="form-group row">
                                                        {if $indexRates == 0}
                                                            <a href="#" class="addrow1 minimunOrder"><i class="icon-plus-sign txt_info"></i></a>
                                                            <div class="col-lg-5">
                                                                <div class="col-lg-12 minimunOrderSize" align="center">{$arrtext.txtDeliveryValueThresholds|escape:'htmlall':'UTF-8'}</div>
                                                                <input class="col-lg-12" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_MinValue_{$indexRates|escape:'htmlall':'UTF-8'}" value={$rates.MinValue|escape:'htmlall':'UTF-8'} maxlength="15">
                                                            </div>
                                                        {else}
                                                            <a href="#" class="addrow1"><i class="icon-plus-sign txt_info"></i></a>
                                                            <div class="col-lg-5">
                                                                <input class="col-lg-12" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_MinValue_{$indexRates|escape:'htmlall':'UTF-8'}" value={$rates.MinValue|escape:'htmlall':'UTF-8'} maxlength="15">
                                                            </div>
                                                        {/if}
                                                        {if $indexRates == 0}
                                                            <div class="col-lg-offset-1">
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="col-lg-12 minimunOrderSize" align="center">{$arrtext.txtDeliveryRates|escape:'htmlall':'UTF-8'}</div>
                                                                <input class="col-lg-10" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_DeliRate_{$indexRates|escape:'htmlall':'UTF-8'}" value={$rates.DeliRate|escape:'htmlall':'UTF-8'} maxlength="15">
                                                                {if $indexRates > 0}
                                                                    <a href="#" class="subrow1 ic_sub optionFlatReal"><i class="icon-minus-sign text-danger"></i></a>
                                                                {/if}
                                                            </div>
                                                        {else}
                                                            <div class="col-lg-offset-1">
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <input class="col-lg-10" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_DeliRate_{$indexRates|escape:'htmlall':'UTF-8'}" value={$rates.DeliRate|escape:'htmlall':'UTF-8'} maxlength="15">
                                                            </div>
                                                            {if $indexRates > 0}
                                                                <a href="#" class="subrow1 ic_sub"><i class="icon-minus-sign text-danger"></i></a>
                                                            {/if}
                                                        {/if}
                                                    </div>
                                                {/foreach}
                                            </div>
                                            <div class="ship_ups col-lg-6 d-none optionFlatReal" id="Ap_percent_{$index|escape:'htmlall':'UTF-8'}">
                                                <label class="control-label mr07 deliveryRatesLeft">{$arrtext.txtDeliveryRatesIs|escape:'htmlall':'UTF-8'}</label>
                                                <input class="form-control ups-form-controll w260 mr07 deliveryRatesInput" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_Percent" value="100">
                                                <label class="control-label deliveryRatesPercent">{$arrtext.txtDeliveryShippingRates|escape:'htmlall':'UTF-8'}</label>
                                            </div>
                                        {else}
                                            <div class="col-lg-6 ship_ups d-none" id="Ap_listRates_{$index|escape:'htmlall':'UTF-8'}">
                                                <input class="col-lg-3" type="hidden" id="Ap_countRates_{$index|escape:'htmlall':'UTF-8'}" name="{$serivce.key|escape:'htmlall':'UTF-8'}" value="1">
                                                <div class="form-group row">
                                                    <a href="#" class="addrow1 minimunOrder"><i class="icon-plus-sign txt_info"></i></a>
                                                    <div class="col-lg-5">
                                                        <div class="col-lg-12 minimunOrderSize" align="center">{$arrtext.txtDeliveryValueThresholds|escape:'htmlall':'UTF-8'}</div>
                                                        <input class="col-lg-12" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_MinValue_0" value="0" maxlength="15">
                                                    </div>
                                                    <div class="col-lg-offset-1">
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="col-lg-12 minimunOrderSize" align="center">{$arrtext.txtDeliveryRates|escape:'htmlall':'UTF-8'}</div>
                                                        <input class="col-lg-10" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_DeliRate_0" value="0" maxlength="15">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ship_ups col-lg-6 optionFlatReal" id="Ap_percent_{$index|escape:'htmlall':'UTF-8'}">
                                                <label class="control-label mr07 deliveryRatesLeft">{$arrtext.txtDeliveryRatesIs|escape:'htmlall':'UTF-8'}</label>
                                                <input class="form-control ups-form-controll w260 mr07 deliveryRatesInput" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_Percent" value="{$serivce.val|escape:'htmlall':'UTF-8'}">
                                                <label class="control-label deliveryRatesPercent">{$arrtext.txtDeliveryShippingRates|escape:'htmlall':'UTF-8'}</label>
                                            </div>
                                        {/if}
                                    </div>
                                    {if is_array($serivce.val)}
                                        <div class="form-group row d-none" id="Ap_infoRealTime_{$index|escape:'htmlall':'UTF-8'}">
                                            <div class="col-lg-2 col-md-2 col-sm-3"></div>
                                            <div class="col-lg-8 col-md-8 col-sm-7 help-block mb-0">{$arrtext.txtDeliveryYourShopper|escape:'htmlall':'UTF-8'} </p> {$arrtext.txtNote|escape:'htmlall':'UTF-8'|htmlspecialchars_decode:3}</div>
                                        </div>
                                    {else}
                                        <div class="form-group row" id="Ap_infoRealTime_{$index|escape:'htmlall':'UTF-8'}">
                                            <div class="col-lg-2 col-md-2 col-sm-3"></div>
                                            <div class="col-lg-8 col-md-8 col-sm-7 help-block mb-0">{$arrtext.txtDeliveryYourShopper|escape:'htmlall':'UTF-8'} </p> {$arrtext.txtNote|escape:'htmlall':'UTF-8'|htmlspecialchars_decode:3}</div>
                                        </div>
                                    {/if}
                                {/foreach}
                            {/if}
                            {if $listServiceAdd|@count gt 0}
                                <label>{$arrtext.txtDeliveryUPSAdd|escape:'htmlall':'UTF-8'}</label>
                                {foreach $listServiceAdd as $index => $serivce}
                                    <div class="form-group row">
                                        <label class="control-label col-lg-2 optionFlatReal">{$serivce.name|escape:'htmlall':'UTF-8'}<br/></label>
                                        <div class="col-lg-3 optionFlatReal">
                                            <select class="form-control ups-form-control w260 add-service-type optionCurrency" name="{$serivce.key|escape:'htmlall':'UTF-8'}_Type" id="add_serviceType_{$index|escape:'htmlall':'UTF-8'}">
                                                <option value="FLAT_RATE" {if $serivce.type == 'FLAT_RATE'}selected="selected"{/if}>{$arrtext.txtDeliveryFlatRates|escape:'htmlall':'UTF-8'}</option>
                                                <option value="REAL_TIME" {if $serivce.type == 'REAL_TIME'}selected="selected"{/if}>{$arrtext.txtDeliveryRealTime|escape:'htmlall':'UTF-8'}</option>
                                            </select>
                                        </div>
                                        {if is_array($serivce.val)}
                                            <div class="col-lg-6 ship_ups" id="Add_listRates_{$index|escape:'htmlall':'UTF-8'}">
                                                <input class="col-lg-3" type="hidden" id="Add_countRates_{$index|escape:'htmlall':'UTF-8'}" name="{$serivce.key|escape:'htmlall':'UTF-8'}" value="{$serivce.val|count}">
                                                {foreach $serivce.val as $indexRates => $rates}
                                                    <div class="form-group row">
                                                        {if $indexRates == 0}
                                                            <a href="#" class="addrowAdd minimunOrder"><i class="icon-plus-sign txt_info"></i></a>
                                                            <div class="col-lg-5">
                                                                <div class="col-lg-12 minimunOrderSize" align="center">{$arrtext.txtDeliveryValueThresholds|escape:'htmlall':'UTF-8'}</div>
                                                                <input class="col-lg-12" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_MinValue_{$indexRates|escape:'htmlall':'UTF-8'}" value={$rates.MinValue|escape:'htmlall':'UTF-8'} maxlength="15">
                                                            </div>
                                                        {else}
                                                            <a href="#" class="addrowAdd"><i class="icon-plus-sign txt_info"></i></a>
                                                            <div class="col-lg-5">
                                                                <input class="col-lg-12" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_MinValue_{$indexRates|escape:'htmlall':'UTF-8'}" value={$rates.MinValue|escape:'htmlall':'UTF-8'} maxlength="15">
                                                            </div>
                                                        {/if}
                                                        {if $indexRates == 0}
                                                            <div class="col-lg-offset-1">
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="col-lg-12 minimunOrderSize" align="center">{$arrtext.txtDeliveryRates|escape:'htmlall':'UTF-8'}</div>
                                                                <input class="col-lg-10" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_DeliRate_{$indexRates|escape:'htmlall':'UTF-8'}" value={$rates.DeliRate|escape:'htmlall':'UTF-8'} maxlength="15">
                                                            </div>
                                                            {if $indexRates > 0}
                                                                <a href="#" class="subrow1 ic_sub optionFlatReal"><i class="icon-minus-sign text-danger"></i></a>
                                                            {/if}
                                                        {else}
                                                            <div class="col-lg-offset-1">
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <input class="col-lg-10" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_DeliRate_{$indexRates|escape:'htmlall':'UTF-8'}" value={$rates.DeliRate|escape:'htmlall':'UTF-8'} maxlength="15">
                                                            </div>
                                                            {if $indexRates > 0}
                                                                <a href="#" class="subrow1 ic_sub"><i class="icon-minus-sign text-danger"></i></a>
                                                            {/if}
                                                        {/if}
                                                    </div>
                                                {/foreach}
                                            </div>
                                            <div class="ship_ups col-lg-6 d-none optionFlatReal" id="Add_percent_{$index|escape:'htmlall':'UTF-8'}">
                                                <label class="control-label mr07 deliveryRatesLeft">{$arrtext.txtDeliveryRatesIs|escape:'htmlall':'UTF-8'}</label>
                                                <input class="form-control ups-form-controll w260 mr07 deliveryRatesInput" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_Percent" value="100">
                                                <label class="control-label deliveryRatesPercent">{$arrtext.txtDeliveryShippingRates|escape:'htmlall':'UTF-8'}</label>
                                            </div>
                                        {else}
                                            <div class="col-lg-6 ship_ups d-none" id="Add_listRates_{$index|escape:'htmlall':'UTF-8'}">
                                                <input class="col-lg-3" type="hidden" id="Add_countRates_{$index|escape:'htmlall':'UTF-8'}" name="{$serivce.key|escape:'htmlall':'UTF-8'}" value="1">
                                                <div class="form-group row">
                                                    <a href="#" class="addrowAdd minimunOrder"><i class="icon-plus-sign txt_info"></i></a>
                                                    <div class="col-lg-5">
                                                        <div class="col-lg-12 minimunOrderSize" align="center">{$arrtext.txtDeliveryValueThresholds|escape:'htmlall':'UTF-8'}</div>
                                                        <input class="col-lg-12" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_MinValue_0" value="0" maxlength="15">
                                                    </div>
                                                    <div class="col-lg-offset-1">
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="col-lg-12 minimunOrderSize" align="center">{$arrtext.txtDeliveryRates|escape:'htmlall':'UTF-8'}</div>
                                                        <input class="col-lg-10" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_DeliRate_0" value="0" maxlength="15">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ship_ups col-lg-6 optionFlatReal" id="Add_percent_{$index|escape:'htmlall':'UTF-8'}">
                                                <label class="control-label mr07 deliveryRatesLeft">{$arrtext.txtDeliveryRatesIs|escape:'htmlall':'UTF-8'}</label>
                                                <input class="form-control ups-form-controll w260 mr07 deliveryRatesInput" type="text" size="5" name="{$serivce.key|escape:'htmlall':'UTF-8'}_Percent" value="{$serivce.val|escape:'htmlall':'UTF-8'}">
                                                <label class="control-label deliveryRatesPercent">{$arrtext.txtDeliveryShippingRates|escape:'htmlall':'UTF-8'}</label>
                                            </div>

                                        {/if}
                                    </div>
                                    {if is_array($serivce.val)}
                                        <div class="form-group row d-none" id="Add_infoRealTime_{$index|escape:'htmlall':'UTF-8'}">
                                            <div class="col-lg-2 col-md-2 col-sm-3"></div>
                                            <div class="col-lg-8 col-md-8 col-sm-7 help-block mb-0">{$arrtext.txtDeliveryYourShopper|escape:'htmlall':'UTF-8'} </p> {$arrtext.txtNote|escape:'htmlall':'UTF-8'|htmlspecialchars_decode:3}</div>
                                        </div>
                                    {else}
                                        <div class="form-group row" id="Add_infoRealTime_{$index|escape:'htmlall':'UTF-8'}">
                                            <div class="col-lg-2 col-md-2 col-sm-3"></div>
                                            <div class="col-lg-8 col-md-8 col-sm-7 help-block mb-0">{$arrtext.txtDeliveryYourShopper|escape:'htmlall':'UTF-8'} </p> {$arrtext.txtNote|escape:'htmlall':'UTF-8'|htmlspecialchars_decode:3}</div>
                                        </div>
                                    {/if}
                                {/foreach}
                            {/if}
                            <div class="panel-footer text-right">
                                <button type="submit" name="submitNext" class="btn btn-default pull-right">
                                    <i class="process-icon-next"></i>
                                    {$arrtext.txtNext|escape:'htmlall':'UTF-8'}
                                </button>
                                <button type="submit" name="submitDeliveryRates" class="btn btn-default pull-right">
                                    <i class="process-icon-save"></i>
                                    {$arrtext.txtPkgSave|escape:'htmlall':'UTF-8'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end collapse -->
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();

    $(document).on("click", ".addrow1", function (e) {
        e.preventDefault();
        var itemId = e.target.offsetParent.id;
        var indexService = itemId.substr(13);
        var increaseRatesService = $('#Ap_countRates_' + indexService).val();
        var nameService = $('#Ap_countRates_' + indexService).attr("name");
        var nameInputMinValue = nameService + '_MinValue_' + increaseRatesService;
        var nameInputDeliRates = nameService + '_DeliRate_' + increaseRatesService;
        $('#Ap_countRates_' + indexService).val(parseInt(increaseRatesService) + 1);

        $('#' + itemId).append(
            '<div class="form-group row">'+
                '<a href="#" class="addrow1"><i class="icon-plus-sign txt_info"></i></a>' +
                '<div class="col-lg-5"><input class="col-lg-12" type="text" size="5" value="0" name="' + nameInputMinValue + '" maxlength="15"></div>' +
                '<div class="col-lg-offset-1"></div>' +
                '<div class="col-lg-4"><input name="' + nameInputDeliRates + '" class="col-lg-10" value="0" type="text" size="5" maxlength="15"></div>' +
                '<a href="#" class="subrow1 ic_sub deliveryRatesLeft"><i class="icon-minus-sign text-danger"></i></a>' +
            '</div>'
            );
    });

    $(document).on("click", ".addrowAdd", function (e) {
        e.preventDefault();
        var itemId = e.target.offsetParent.id;
        var indexService = itemId.substr(14);
        var increaseRatesService = $('#Add_countRates_' + indexService).val();
        var nameService = $('#Add_countRates_' + indexService).attr("name");
        var nameInputMinValue = nameService + '_MinValue_' + increaseRatesService;
        var nameInputDeliRates = nameService + '_DeliRate_' + increaseRatesService;
        $('#Add_countRates_' + indexService).val(parseInt(increaseRatesService) + 1);

        $('#' + itemId).append(
            '<div class="form-group row">'+
                '<a href="#" class="addrowAdd"><i class="icon-plus-sign txt_info"></i></a>' +
                '<div class="col-lg-5"><input class="col-lg-12" type="text" value="0" size="5" name="' + nameInputMinValue + '" maxlength="15"></div>' +
                '<div class="col-lg-offset-1"></div>' +
                '<div class="col-lg-4"><input name="' + nameInputDeliRates + '" class="col-lg-10" value="0" type="text" size="5" maxlength="15"></div>' +
                '<a href="#" class="subrow1 ic_sub deliveryRatesLeft"><i class="icon-minus-sign text-danger"></i></a>' +
            '</div>'
            );
    });

    $(document).on("click", ".subrow1", function (e) {
        e.preventDefault();
        $(this).parent().remove();
    });

    $(".ap-service-type").on('change', function() {
        var selectId = $(this).attr("id");
        var indexService = selectId.substr(15);

        if ($(this).val() == 'FLAT_RATE')
        {
            $("#Ap_listRates_" + indexService).removeClass("d-none");
            $("#Ap_percent_" + indexService).addClass("d-none");
            $("#Ap_infoRealTime_" + indexService).addClass("d-none");
        }
        else
        {
            $("#Ap_listRates_" + indexService).addClass("d-none");
            $("#Ap_percent_" + indexService).removeClass("d-none");
            $("#Ap_infoRealTime_" + indexService).removeClass("d-none");
        }
    });

    $(".add-service-type").on('change', function() {
        var selectId = $(this).attr("id");
        var indexService = selectId.substr(16);

        if ($(this).val() == 'FLAT_RATE')
        {
            $("#Add_listRates_" + indexService).removeClass("d-none");
            $("#Add_percent_" + indexService).addClass("d-none");
            $("#Add_infoRealTime_" + indexService).addClass("d-none");
        }
        else
        {
            $("#Add_listRates_" + indexService).addClass("d-none");
            $("#Add_percent_" + indexService).removeClass("d-none");
            $("#Add_infoRealTime_" + indexService).removeClass("d-none");
        }
    });

    $('[data-toggle="tooltip"]').tooltip();
});
</script>
