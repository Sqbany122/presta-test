{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}

<label type="text" class="upshidden" id="checkError" >{$checkError|escape:'htmlall':'UTF-8'}</label>
<label type="text" class="upshidden" id="checkRadio" >{$checkRadio|escape:'htmlall':'UTF-8'}</label>
<label type="text" class="upshidden" id="checkRemove" >{$checkRemove|escape:'htmlall':'UTF-8'}</label>
<script>
    var listFieldsError = {$listFieldsError|json_encode};
</script>
<div class="row">
    <div class="col-lg-12">
        <form class="defaultForm form-horizontal" action="index.php?controller=AdminUpsAccountSuccess&token={getAdminToken tab='AdminUpsAccountSuccess'}" method="post">
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-user"></i>{$texts.txtAccount|escape:'htmlall':'UTF-8'}
            </div>
            <div class="form-wrapper">
                <label class="mb-3">{$texts.txtLinkText|escape:'htmlall':'UTF-8'|htmlspecialchars_decode:3}</label></p>
                <label class="mb-3">{$texts.title|escape:'htmlall':'UTF-8'}</label>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group row ">
                            <span class="col-lg-3 control-label pt-0">{$texts.txtFullName|escape:'htmlall':'UTF-8'}:</span>
                            <label class="col-lg-9">{$primaryInfo.CustomerName|escape:'htmlall':'UTF-8'}</label>
                        </div>
                        <div class="form-group row">
                            <span class="col-lg-3 control-label pt-0">{$texts.txtCompany|escape:'htmlall':'UTF-8'}:</span>
                            <label class="col-lg-9">{$primaryInfo.CompanyName|escape:'htmlall':'UTF-8'}</label>
                        </div>
                        <div class="form-group row">
                            <span class="col-lg-3 control-label pt-0">{$texts.txtEmail|escape:'htmlall':'UTF-8'}:</span>
                            <span class="col-lg-9">{$primaryInfo.EmailAddress|escape:'htmlall':'UTF-8'}</span>
                        </div>
                        <div class="form-group row">
                            <span class="col-lg-3 control-label pt-0">{$texts.txtPhoneNumber|escape:'htmlall':'UTF-8'}:</span>
                            <span class="col-lg-9">{$primaryInfo.PhoneNumber|escape:'htmlall':'UTF-8'}</span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group row">
                            <span class="col-lg-3 control-label pt-0">{$texts.txtAddress|escape:'htmlall':'UTF-8'}:</span>
                            <span class="col-lg-9">{$primaryInfo.AddressLine1|escape:'htmlall':'UTF-8'}</span>
                        </div>
                        <div class="form-group row">
                            <span class="col-lg-3 control-label pt-0"></span>
                            <span class="col-lg-9">{$primaryInfo.AddressLine2|escape:'htmlall':'UTF-8'}</span>
                        </div><div class="form-group row">
                            <span class="col-lg-3 control-label pt-0"></span>
                            <span class="col-lg-9">{$primaryInfo.AddressLine3|escape:'htmlall':'UTF-8'}</span>
                        </div>
                        <div class="form-group row">
                            <span class="col-lg-3 control-label pt-0">{$texts.txtPostalCode|escape:'htmlall':'UTF-8'}:</span>
                            <span class="col-lg-9">{$primaryInfo.PostalCode|escape:'htmlall':'UTF-8'}</span>
                        </div>
                        <div class="form-group row">
                            <span class="col-lg-3 control-label pt-0">{$texts.txtCity|escape:'htmlall':'UTF-8'}:</span>
                            <span class="col-lg-9">{$primaryInfo.City|escape:'htmlall':'UTF-8'}</span>
                        </div>
                        {if $isUSA}
                        <div class="form-group row">
                            <span class="col-lg-3 control-label pt-0">{$texts.txtState|escape:'htmlall':'UTF-8'}:</span>
                            <span class="col-lg-9">{$primaryInfo.ProvinceCode|escape:'htmlall':'UTF-8'}</span>
                        </div>
                        {/if}
                        <div class="form-group row">
                            <span class="col-lg-3 control-label pt-0">{$texts.txtCountry|escape:'htmlall':'UTF-8'}:</span>
                            <span class="col-lg-9">{$primaryInfo.Country|escape:'htmlall':'UTF-8'}</span>
                        </div>
                    </div>
                </div>
                <label class="mb-3">{$texts.txtAccPaymentAccount|escape:'htmlall':'UTF-8'}</label>
                <div class="row">
                    {foreach $listAccount as $account}
                        <div class="col-sm-6 col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <label class="card-title">{$account.AddressType|escape:'htmlall':'UTF-8'}</label>
                                    <p class="card-text help-block">{$texts.txtAccountNumber|escape:'htmlall':'UTF-8'}: {$account.AccountNumber|escape:'htmlall':'UTF-8'}</p>
                                    <p class="card-text help-block">{$texts.txtPostalCode|escape:'htmlall':'UTF-8'}: {$account.PostalCode|escape:'htmlall':'UTF-8'}</p>
                                    <p class="card-text help-block">{$texts.txtCountry|escape:'htmlall':'UTF-8'}: {$account.Country|escape:'htmlall':'UTF-8'}</p>
                                    {if !$account.default}
                                    <a onclick="removeAccount('{$account.AccountNumber|escape:'htmlall':'UTF-8'}');"
                                        class="card-link pointer">{$texts.txtAccRemove|escape:'htmlall':'UTF-8'}</a>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
                <a href="javascript:void(0)" onclick="addAccountNumber();" ><i class="icon-plus"></i> {$texts.txtAccAddAnotherAccount|escape:'htmlall':'UTF-8'}</a>
                <div class="temp" id="formAccount" style="display: none">
                    <div class="help-block">{$texts.txtAccEnterAddress|escape:'htmlall':'UTF-8'}</div>
                    <span class="help-block">{$texts.txtAccountNotice|escape:'htmlall':'UTF-8'}</span> 
                    <div class="form-group">
                        <label class="control-label col-lg-3">
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" data-original-title="{$texts.txtAccTTAddressType|escape:'htmlall':'UTF-8'}" data-placement="top">{$texts.txtAddressType|escape:'htmlall':'UTF-8'}:<sup class ="star">*</sup></span>
                        </label>
                        <div class="col-lg-9">
                            <input type="text" name="AddressType" id="AddressType" value="{$AddressType|escape:'htmlall':'UTF-8'}" maxlength="50" class="form-control ups-form-control fixed-width-xxl" placeholder="{$texts.txtAddressTypeEx|escape:'htmlall':'UTF-8'}">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" data-original-title="{$texts.txtAccTTAccountName|escape:'htmlall':'UTF-8'}" data-placement="top">{$texts.txtAccountName|escape:'htmlall':'UTF-8'}:<sup class="star">*</sup></span>
                        </label>
                        <div class="col-lg-9">
                            <input type="text" name="BusinessName" id="BusinessName" value="{$BusinessName|escape:'htmlall':'UTF-8'}" maxlength="35" class="form-control ups-form-control fixed-width-xxl">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3"> 
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" data-original-title="{$texts.txtAccTTAddress|escape:'htmlall':'UTF-8'}" data-placement="top">{$texts.txtAddress|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></span> 
                        </label>
                        <div class="col-lg-9">
                            <input type="text" name="AddressLine1" id="AddressLine1" value="{$AddressLine1|escape:'htmlall':'UTF-8'}" maxlength="35" class="form-control ups-form-control fixed-width-xxl" placeholder="{$texts.txtAddressStreet|escape:'htmlall':'UTF-8'}">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3"></label>
                        <div class="col-lg-9 col-lg-offet-3">
                            <input type="text" name="AddressLine2" id="AddressLine2" value="{$AddressLine2|escape:'htmlall':'UTF-8'}" maxlength="35" class="form-control ups-form-control fixed-width-xxl" placeholder="{$texts.txtAddressApartment|escape:'htmlall':'UTF-8'}">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3"></label>
                        <div class="col-lg-9 col-lg-offet-3">
                            <input type="text" name="AddressLine3" id="AddressLine3" value="{$AddressLine3|escape:'htmlall':'UTF-8'}" maxlength="35" class="form-control ups-form-control fixed-width-xxl" placeholder="{$texts.txtAddressDepartment|escape:'htmlall':'UTF-8'}">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" data-original-title="{$texts.txtAccTTPostalCode|escape:'htmlall':'UTF-8'}" data-placement="top">{$texts.txtPostalCode|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></span>
                        </label>
                        <div class="col-lg-9">
                            <input type="text" name="PostalCode" id="PostalCode" value="{$PostalCode|escape:'htmlall':'UTF-8'}" maxlength="9" class="form-control ups-form-control fixed-width-lg">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{$texts.txtCity|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></label>
                        <div class="col-lg-9">
                            <input type="text" name="City" id="City" value="{$City|escape:'htmlall':'UTF-8'}" maxlength="30" class="form-control ups-form-control fixed-width-xxl">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    {if $isUSA}
                    <div class="form-group">
                        <label class="control-label col-lg-3">{$texts.txtState|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></label>
                        <div class="col-lg-9">
                            <select class="form-control ups-form-control fixed-width-lg" name="ProvinceCode" id="ProvinceCode" maxlength="30">
                                {foreach $states as $code => $name}
                                <option value="{$code|escape:'htmlall':'UTF-8'}" {if $code == $selectedStateCode} selected {/if}>{$name|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    {/if}
                    <div class="form-group">
                        <label class="control-label col-lg-3">{$texts.txtCountry|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></label>
                        <div class="col-lg-9">
                            <select class="form-control ups-form-control fixed-width-lg" name="CountryCode" maxlength="30" disabled>
                                {foreach $countries as $code => $name}
                                <option {if $code == $countrySelected } selected {/if} value="{$code|escape:'htmlall':'UTF-8'}">{$name|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{$texts.txtPhoneNumber|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></label>
                        <div class="col-lg-9">
                            <input type="text" name="PhoneNumber" id="PhoneNumber" value="{$PhoneNumber|escape:'htmlall':'UTF-8'}" maxlength="15" class="form-control ups-form-control fixed-width-xxl" placeholder="+48 87654321">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="optradio" id="rate07">{$texts.txtHaveAccountUPS|escape:'htmlall':'UTF-8'}
                            </label>
                        </div>
                    </div>
                    <div class="rate07_show upshidden">
                        <div class="help-block">{$texts.txtLatestInvoice|escape:'htmlall':'UTF-8'}</div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">
                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" data-original-title="{$texts.txtAccTTAccountNumber|escape:'htmlall':'UTF-8'}" data-placement="top">{$texts.txtAccountNumber|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></span>
                            </label>
                            <div class="col-lg-9">
                                <input type="text" id="AccountNumber" name="AccountNumber" value="{$AccountNumber|escape:'htmlall':'UTF-8'}" maxlength="6" class="form-control ups-form-control fixed-width-lg">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">
                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" data-original-title="{$texts.txtAccTTInvoiceNumber|escape:'htmlall':'UTF-8'}" data-placement="top">{$texts.txtInvoiceNumber|escape:'htmlall':'UTF-8'}:<sup class="star">*</sup></span>
                            </label>
                            <div class="col-lg-9">
                                <input type="text" id="InvoiceNumber" name="InvoiceNumber" value="{$InvoiceNumber|escape:'htmlall':'UTF-8'}" maxlength="15" class="form-control ups-form-control fixed-width-xxl">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">
                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" data-original-title="{$texts.txtAccTTInvoiceAmount|escape:'htmlall':'UTF-8'}" data-placement="top">{$texts.txtInvoiceAmount|escape:'htmlall':'UTF-8'}:<sup class="star">*</sup></span>
                            </label>
                            <div class="col-lg-9">
                                <input type="text" id="InvoiceAmount" name="InvoiceAmount" value="{$InvoiceAmount|escape:'htmlall':'UTF-8'}" maxlength="19" class="form-control ups-form-control fixed-width-xxl">
                            </div>
                            <div class="clearfix"></div>
                        </div>
						
						{if $isUSA}
							<div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span href="" data-toggle="tooltip" class="label-tooltip" title="{$texts.txtControlID|escape:'htmlall':'UTF-8'}" data-placement="top" data-html="true">{$texts.txtControlID|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" id="ControlID" name="ControlID" value="{$ControlID|escape:'htmlall':'UTF-8'}" maxlength="19" class="form-control fixed-width-xxl">
                                </div>
                                <div class="clearfix"></div>
                            </div>
						{/if}
						
                        <div class="form-group">
                            <label class="control-label col-lg-3" style="padding-right: 10px">{$texts.txtCurrency|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></label>
                            <div class="col-lg-9">
                                <select name="Currency" class="form-control ups-form-control fixed-width-lg" id="sel1">
                                    {foreach $currency as $code => $name}
                                        <option {if $code == $Currency} selected {/if} value="{$code|escape:'htmlall':'UTF-8'}">{$name|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">
                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" data-original-title="{$texts.txtAccTTInvoiceDate|escape:'htmlall':'UTF-8'}" data-placement="top">{$texts.txtInvoiceDate|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></span>
                            </label>
                            <div class="col-lg-9">
                                <input type="text" id="datepicker" name="InvoiceDate" class="form-control ups-form-control fixed-width-xxl" readonly="readonly">
                                <input type="hidden" id="datepickerValue" name="datepickerValue" value="{$InvoiceDate|escape:'htmlall':'UTF-8'}" class="form-control ups-form-control fixed-width-xxl">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="optradio" checked id="rate08" value="2">{$texts.txtHaveAccountUPSWithout|escape:'htmlall':'UTF-8'}
                            </label>
                        </div>
                    </div>
                    <div class="rate08_show">
                        <div class="form-group">
                            <label class="control-label col-lg-3">
                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" data-original-title="{$texts.txtAccTTAccountNumber|escape:'htmlall':'UTF-8'}" data-placement="top">{$texts.txtAccountNumber|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></span>
                            </label>
                            <div class="col-lg-9">
                                <input type="text" id="AccountNumber1" name="AccountNumber1" value="{$AccountNumber1|escape:'htmlall':'UTF-8'}" maxlength="6" class="form-control ups-form-control fixed-width-lg">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="ioBlackBox" id="ioBlackBox">
            </div>
            <div class="panel-footer">
                <button type="submit" name="submitAccountSuccess" id="submitAccountSuccess" class="btn btn-default pull-left" style="display: none"><i class="process-icon-save"></i> {$texts.txtAccVerify|escape:'htmlall':'UTF-8'}</button>
                <button type="submit" name="nextAccountSuccess" class="btn btn-default pull-right"><i class="process-icon-save"></i> {$texts.txtNext|escape:'htmlall':'UTF-8'}</button>
            </div>
        </div>
    </form>
    </div>
</div>
<style>
.star {
    color: red;
    font-size: 14px;
}
.upshidden {
  display: none !important;
}
.form-check-inline .form-check-input {
    margin-right: 5px !important;
}
.bootstrap .form-horizontal .control-label {
    padding-top: 0;
}
</style>
<script>
    // Basic configuration for IOvation
    var io_bbout_element_id = 'ioBlackBox';
    var io_install_stm = false;   // do not install Active X
    var io_exclude_stm = 12;      // do not run Active X
    var io_install_flash = false;   // do not install Flash
    var io_enable_rip = true;   // enable detection of Real IP
</script>

<script src="https://ci-mpsnare.iovation.com/snare.js"></script>
