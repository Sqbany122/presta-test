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
{/literal}
</style>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title2" id="shipment">
            <div class="modal-header-block">
                <img class="modal-header-logo" src="{$view_dir|escape:'htmlall':'UTF-8'}/img/ups_logo_small.png">
                <span class="modal-header-item" id="titleShipment">{$arrtext.txtShipments|escape:'htmlall':'UTF-8'}
                    <span id="id_ups_shipment">
                    </span>
                </span>
                <span id="shipment_date"></span>, <span id="shipment_time"></span>
                <span class="modal-header-item" id="status"></span>
            </div>
        </h4>
    </div>
    <div class="modal-body form-horizontal" style="font-size:14px">
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtShipmentsOrderID|escape:'htmlall':'UTF-8'}:</label>
            <div class="col-lg-7" id="id_order" style="word-break:break-all;"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtShipmentsTracking|escape:'htmlall':'UTF-8'}:</label>
            <div class="col-lg-7" id="tracking_number"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtCustomer|escape:'htmlall':'UTF-8'}:</label>
            <div class="col-lg-7" style="word-break:break-all;">
                <span id="customer_name"></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtArcProduct|escape:'htmlall':'UTF-8'}:</label>
            <div class="col-lg-7" style="word-break:break-all;">
                <span id="product_details"></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtArcAddress|escape:'htmlall':'UTF-8'}:</label>
            <div class="col-lg-7" style="word-break:break-all;">
                <span id="shipping_address1"></span><br>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtAccPhoneNumber|escape:'htmlall':'UTF-8'}:</label>
            <div class="col-lg-7" id="phone" style="word-break:break-all;"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtAccEmail|escape:'htmlall':'UTF-8'}:</label>
            <div class="col-lg-7" style="word-break:break-all;">
                <span id="email"></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtArcShippingService|escape:'htmlall':'UTF-8'}:</label>
            <div class="col-lg-7" style="word-break:break-all;">
                <span id="shipping_service"></span><br>
                <span id="shipping_addressAP"></span><br>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtShipmentsPackageDetails|escape:'htmlall':'UTF-8'}:</label>
            <div class="col-lg-7" id="package_detail" style="word-break:break-all;"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtShipmentsAccessorialService|escape:'htmlall':'UTF-8'}:</label>
            <div class="col-lg-7" style="word-break:break-all;">
                <span id="accessorials_service"></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtArcOrderValue|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-7" id="order_value" style="word-break:break-all;"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtShipmentsShippingFee|escape:'htmlall':'UTF-8'}:</label>
            <div class="col-lg-7" id="shipping_fee" style="word-break:break-all;"></div>
        </div>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-outline-info mr00" data-dismiss="modal">{$arrtext.txtArcOk|escape:'htmlall':'UTF-8'}</button>
    </div>
</div>
