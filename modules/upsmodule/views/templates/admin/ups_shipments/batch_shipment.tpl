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
{/literal}
</style>

<!-- begin create_shipment -->
<div>
    {* <input type="hidden" id="batchOrderId" name="batchOrderId" value=""> *}
    <input type="hidden" id="firstAccount" name="firstAccount" value="{$firstAccount|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="batchOrderIds" name="batchOrderIds" value="0">
    <div class="modal-body form-horizontal" id="modal-batch">
        <div class="form-group row">
            <label class="col-lg-3">{$arrtext.txtOpenAccountNumber|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-5">
                <select class="form-control ups-form-control" id="upsAccount">
                    {html_options options=$customersOptions selected=$customerID}
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3">{$arrtext.txtToBeProcess|escape:'htmlall':'UTF-8'}:</label>
            <ol class="list-orders">
            </ol>
        </div>
    </div>
    <div class="alert alert-danger hidden"></div>
    <div class="modal-footer">
    <span><button type="button" id="batchShipment" class="btn btn-outline-info mr00" onclick="batchShipment();">{$arrtext.txtOpenCreateShipment|escape:'htmlall':'UTF-8'}</button></span>
    </div>
</div>
<!-- end create_shipment -->
