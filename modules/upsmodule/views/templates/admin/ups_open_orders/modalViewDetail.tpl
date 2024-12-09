{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}

<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">
            <span class="font-weight-bold" id="titleOrder">{$arrtext.txtOpenOrder|escape:'htmlall':'UTF-8'}
                <span id="orderId" class="reset-content"></span>
            </span> |
            <span id="orderDate" class="reset-content"></span>, <span id="orderTime" class="reset-content"></span>
        </h4>
    </div>
    <div class="modal-body form-horizontal" style="font-size:14px">
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtCustomer|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-7 word-break">
                <span id="firstName" class="reset-content"></span> <span id="lastName" class="reset-content"></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtArcProduct|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-7 word-break">
                <span class="product"></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtArcAddress|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-7 word-break" id="toHomeAddress" class="reset-content"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtAccPhoneNumber|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-7 word-break" id="phone" class="reset-content"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtAccEmail|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-7 word-break">
                <span id="email" class="reset-content"></span>
                <span id="hosting" class="reset-content"></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtArcShippingService|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-7 word-break">
                <span id="detailShippingService" class="reset-content"></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtOpenAccessPoint|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-7 word-break">
                <p id="accessPointName" class="reset-content"></p>
                <p id="toAPAddress" class="reset-content"></p>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtArcAccessorialService|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-7 word-break reset-content" id="accessorialService"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtArcOrderValue|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-7 word-break reset-content" id="orderValue"></div>
        </div>
        <div class="form-group row">
            <label class="col-lg-5">{$arrtext.txtOpenPaymentStatus|escape:'htmlall':'UTF-8'}</label>
            <div class="col-lg-7 word-break reset-content" id="currentState"></div>
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
    #order
    {
        text-align:center;
        font-size:16px;
    }
{/literal}
</style>
