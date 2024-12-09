{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}

<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title txt_info">
            <span class="font-weight-bold" id="titleOrder">{$arrtext.txtArcOrder|escape:'htmlall':'UTF-8'}
                <span id="id_order"></span>
            </span> |
            <span id="order_date"></span>, <span id="order_time"></span>
        </h4>
    </div>
    <div class="modal-body form-horizontal" style="font-size:14px">
        <div class="form-group row">
            <label class="col-lg-4">{$arrtext.txtCustomer|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-8 word-break" id="customerID"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4">{$arrtext.txtArcProduct|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-8 word-break">
                <span class="product"></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4">{$arrtext.txtArcAddress|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-8 word-break" id="address_delivery1"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4">{$arrtext.txtAccPhoneNumber|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-8 word-break" id="phone"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4">{$arrtext.txtAccEmail|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-8 word-break">
                <span id="email"></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4">{$arrtext.txtArcShippingService|escape:'htmlall':'UTF-8'}:</label>
            <div class="col-lg-8 word-break" id="shipping_service"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4">{$arrtext.txtOpenAccessPoint|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-8" style="word-break:break-all;">
                <p id="access_point"></p>
                <p id="address_delivery2"></p>
                <p id="city"></p>
                <p id="country_name"></p>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4">{$arrtext.txtArcAccessorialService|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-8 word-break" id="accessorials_service"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4">{$arrtext.txtArcOrderValue|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-8 word-break" id="order_value"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4">{$arrtext.txtArcPaymentStatus|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-8 word-break" id="payment_status"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-info mr00" data-dismiss="modal">{$arrtext.txtArcOk|escape:'htmlall':'UTF-8'}</button>
    </div>
</div>

<style type="text/css">
{literal}
    .word-break
    {
        word-break: break-all;
    }
    .mr00:hover
    {
        background-color: #00aff0;
        border-color: #00aff0;
    }
    .ml00:hover
    {
        background-color: #00aff0;
        border-color: #00aff0;
    }
{/literal}
</style>
