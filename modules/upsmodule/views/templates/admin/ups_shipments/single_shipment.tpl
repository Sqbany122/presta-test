{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}

<style type="text/css">
{literal}
    .card{
        padding: 5px;
    }
    .mr00:hover {
        background-color: #00aff0;
        border-color: #00aff0;
    }
    .ml00:hover {
        background-color: #00aff0;
        border-color: #00aff0;
    }
    .deletePackage {
        color: red!important;
        font-size: 20px;
    }
    .row_pack5 {
        margin-top: 15px;
        margin-left: -25px !important;
    }
    .addressPadding {
        padding-bottom: 8px;
    }
    .labelPaddingLeft {
        padding-left: 70px;
    }
    .noteLabel {
            font-size: 11px;
            font-style: italic;
            color: red;
    }
    .small-space {
        margin-left: 15px!important;
    }
    #estimation {
        cursor: pointer;
    }
    .bootstrap label.control-label span.label-tooltip {
        font-size: 12px;
        padding: 0px;
    }
{/literal}
</style>

<script language="JavaScript" type="text/javascript">
    var txtOpenName = '{$arrtext.txtOpenName|escape:'htmlall':'UTF-8'}';
    var txtOpenAddress = '{$arrtext.txtOpenAddress|escape:'htmlall':'UTF-8'}';
    var txtOpenPostalCode = '{$arrtext.txtOpenPostalCode|escape:'htmlall':'UTF-8'}';
    var txtOpenCity = '{$arrtext.txtOpenCity|escape:'htmlall':'UTF-8'}';
    var txtOpenCountry = '{$arrtext.txtOpenCountry|escape:'htmlall':'UTF-8'}';
    var txtOpenPhone = '{$arrtext.txtOpenPhone|escape:'htmlall':'UTF-8'}';
    var txtOpenMail = '{$arrtext.txtOpenMail|escape:'htmlall':'UTF-8'}';
    var txtOpenNote = '{$arrtext.txtOpenNote|escape:'htmlall':'UTF-8'}';
    var txtOpenCancelEditing = '{$arrtext.txtOpenCancelEditing|escape:'htmlall':'UTF-8'}';
    var txtOpenState = '{$arrtext.txtOpenState|escape:'htmlall':'UTF-8'}';
    var txtE_ShippingMethod = '{$arrtext.txtE_ShippingMethod|escape:'htmlall':'UTF-8'}';
    var country_empty = '{$arrtext.country_empty|escape:'htmlall':'UTF-8'}';
    var format_phone_not_empty = '{$arrtext.format_phone_not_empty|escape:'htmlall':'UTF-8'}';
    var postal_empty_invalid = '{$arrtext.postal_empty_invalid|escape:'htmlall':'UTF-8'}';
    var city_empty = '{$arrtext.city_empty|escape:'htmlall':'UTF-8'}';
    var txtAddressRequired = '{$arrtext.txtE_AddressRequired|escape:'htmlall':'UTF-8'}';
    var email_empty = '{$arrtext.email_empty|escape:'htmlall':'UTF-8'}';
    var name_empty = '{$arrtext.name_empty|escape:'htmlall':'UTF-8'}';
    var tokenPackage = '{$tokenPackage|escape:'htmlall':'UTF-8'}';
    var tokenCustomPackage = '{$tokenBatchPackage|escape:'htmlall':'UTF-8'}';
</script>

<!-- begin create_shipment -->
<div>
    <input type="hidden" id="orderID" name="orderID" value="">
    <input type="hidden" id="firstAccount" name="firstAccount" value="{$firstAccount|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="numberPackageSingle" name="numberPackageSingle" value="">
    <input type="hidden" id="tokenPackage" name="tokenPackage" value="{$tokenPackage|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="tokenCustomPackage" name="tokenCustomPackage" value="{$tokenBatchPackage|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="clickedEstimate" name="clickedEstimate" value="">
    <input type="hidden" id="createdShipment" name="createdShipment" value="">
    <input type="hidden" id="txtOpenPhoneNumber" name="txtOpenPhoneNumber" value="{$arrtext.txtOpenPhoneNumber|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="txtOpenEmail" name="txtOpenEmail" value="{$arrtext.txtOpenEmail|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="txtScreen" name="txtScreen" value="0">
    <input type="hidden" id="allOrderID" name="allOrderID" value="0">
    <div class="modal-body form-horizontal">
        <div class="form-group row">
            <label class="col-lg-3">{$arrtext.txtOpenAccountNumber|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-5">
                <select class="form-control ups-form-control" id="cmbCustomer" onchange="changeCustomer();">
                    {html_options options=$customersOptions selected=$customerID}
                </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-12 col-lg-3" style="word-break: break-all;">
                <label>{$arrtext.txtOpenShipFrom|escape:'htmlall':'UTF-8'}</label>
                <div class="card">
                    <div class="card-body">
                        <label class="card-title" id="shipFromName"></label>
                        <p class="card-text help-block" id="shipFrom"></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-9" style="word-break: break-all;">
                <label id="shipToLabel">{$arrtext.txtOpenShipTo|escape:'htmlall':'UTF-8'}</label>
                <div class="card">
                    <div class="card-body" id="shipToAddress">

                    </div>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3">{$arrtext.txtArcShippingService|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-8" id="shippingService"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3">{if $isUSA} {$arrtext.txtShipmentsAccessorialServiceUS|escape:'quotes':'UTF-8'} {else} {$arrtext.txtShipmentsAccessorialService|escape:'htmlall':'UTF-8'} {/if}:</label>
            <div class="col-lg-8" id="shipCode"></div>
            {if $isUSA}
            <div class="form-group row">
                <div class="col-lg-8 accessorial-description"><i>{$arrtext.txtAfterAccessorialService|escape:'quotes':'UTF-8'}</i></div>
            </div>
            {/if}
        </div>
        <div class="form-group row">
            <label class="col-lg-12">{$arrtext.txtOpenPackaging|escape:'htmlall':'UTF-8'}</label>
        </div>
        <div class="card">
            <div class="card-body">

                <div class="form-group row">
                    <div class="control-label col-lg-2 label-right">{$arrtext.txtOpenPackage1|escape:'htmlall':'UTF-8'}</div>
                    <div class="col-lg-9">
                        <select class="form-control ups-form-control" id="cmbPackageSingle" onchange="addCustomPackage(this, 1);">
                            {html_options options=$packagesOptions selected=$defaultPackage}
                        </select>
                    </div>
                </div>
                <div class="form-group row link_add_pack">
                    <div>
                    <a href="javascript:void(0)" class="card-link addPackageSingle"><i class="icon-plus-circle"></i>{$arrtext.txtPkgAddPackage|escape:'htmlall':'UTF-8'}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6"><a class="card-link" id="estimation">{$arrtext.txtOpenViewEstimated|escape:'htmlall':'UTF-8'}</a></div>
            <div class="col-lg-3" id="shipmentDateArrival"></div>
            <div class="col-lg-3" id="shipmentFeeSingle"></div>
        </div>
        <div class="form-group row">
            <div id="wait"><img src="{$iconLoading|escape:'htmlall':'UTF-8'}" width="47" height="40" /><br>{$arrtext.txtOpenLoading|escape:'htmlall':'UTF-8'}</div>
        </div>
    </div>
    <div class="alert alert-danger hidden"></div>
    <div class="modal-footer">
    <span>
        <button type="button" class="btn btn-outline-info mr00" id="editShipment" onclick="showEditShipmentModal();">{$arrtext.txtOpenEdit|escape:'htmlall':'UTF-8'}</button>
        <button type="button" class="btn btn-outline-info mr00" id="cancelEditShipment" onclick="cancelEditShipment();">{$arrtext.txtOpenCancelEditing|escape:'htmlall':'UTF-8'}</button>
    </span>
    <span><button type="button" id="createShipment" class="btn btn-outline-info mr00" onclick="createSingleShipment();">{$arrtext.txtOpenCreateShipment|escape:'htmlall':'UTF-8'}</button></span>
    </div>
</div>
<!-- end create_shipment -->
