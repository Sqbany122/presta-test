{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}

<label type="text" class="upshidden" id="checkError" >{$checkError|escape:'htmlall':'UTF-8'}</label>
<script>
    var listFieldsError = {$listFieldsError|json_encode};
    var optionChoose = {if $optChoose}{$optChoose|escape:'htmlall':'UTF-8'}{else}0{/if};
</script>
<div class="row">
    <div class="col-lg-12">
    <form class="defaultForm form-horizontal" action="index.php?controller=AdminUpsAccount&token={getAdminToken tab='AdminUpsAccount'}" method="post">
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-user"></i> {$txtAccount|escape:'htmlall':'UTF-8'}
            </div>
                <div class="form-wrapper">
                    <div class="form-wrapper">
                        <label class="mb-3">{$txtLinkText|escape:'htmlall':'UTF-8'|htmlspecialchars_decode:3}</label></p>
                        <label class="mb-3">{$txtUPSPlugin|escape:'htmlall':'UTF-8'}</label>
                        <br/> 
                        <span class="mb-3">{$txtAccountNotice|escape:'htmlall':'UTF-8'}</span>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{$txtTitle|escape:'htmlall':'UTF-8'}:<sup class="star">*</sup></label>
                                    <div class="col-lg-9">
                                        <select class="form-control ups-form-control fixed-width-md" name="Title">
                                        {foreach $title as $value}
                                            <option {if $value == $Title } selected {/if} value="{$value|escape:'htmlall':'UTF-8'}">{$value|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                        </select>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{$txtFullName|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></label>
                                    <div class="col-lg-9">
                                        <input type="text" name="CustomerName" id="CustomerName" value='{$CustomerName|escape:'htmlall':'UTF-8'}' maxlength="35" class="form-control ups-form-control fixed-width-xxl" >
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{$txtCompany|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></label>
                                    <div class="col-lg-9">
                                        <input type="text" name="CompanyName" id="CompanyName" value='{$CompanyName|escape:'htmlall':'UTF-8'}' maxlength="35" class="form-control ups-form-control fixed-width-xxl" >
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{$txtEmail|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></label>
                                    <div class="col-lg-9">
                                        <input type="text" name="EmailAddress" id="EmailAddress" value="{$EmailAddress|escape:'htmlall':'UTF-8'}" maxlength="50" class="form-control ups-form-control fixed-width-xxl" >
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{$txtPhoneNumber|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></label>
                                    <div class="col-lg-9">
                                        <input type="text" name="PhoneNumber" id="PhoneNumber" value="{$PhoneNumber|escape:'htmlall':'UTF-8'}" maxlength="15" class="form-control ups-form-control fixed-width-xxl">
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label col-lg-3">
                                        <span href="" data-toggle="tooltip" class="label-tooltip" title="{$txtAccTTAddressType|escape:'htmlall':'UTF-8'}" data-placement="bottom" data-html="true">{$txtAddressType|escape:'htmlall':'UTF-8'}:<sup class="star">*</sup></span>
                                    </label>
                                    <div class="col-lg-9">
                                        <input type="text" name="AddressType" id="AddressType" value="{$AddressType|escape:'htmlall':'UTF-8'}" maxlength="50" class="form-control ups-form-control fixed-width-xxl" placeholder="{$txtAddressTypeEx|escape:'htmlall':'UTF-8'}">
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3"> 
                                        <span href="" data-toggle="tooltip" class="label-tooltip" title="{$txtAccTTAddress|escape:'htmlall':'UTF-8'}" data-placement="bottom" data-html="true">{$txtAddress|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></span> 
                                    </label>
                                    <div class="col-lg-9">
                                        <input type="text" name="AddressLine1" id="AddressLine1" value="{$AddressLine1|escape:'htmlall':'UTF-8'}" maxlength="35" class="form-control ups-form-control fixed-width-xxl" placeholder="{$txtAddressStreet|escape:'htmlall':'UTF-8'}">
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3"></label>
                                    <div class="col-lg-9 col-lg-offet-3">
                                        <input type="text" name="AddressLine2" id="AddressLine2" value="{$AddressLine2|escape:'htmlall':'UTF-8'}" maxlength="35" class="form-control ups-form-control fixed-width-xxl" placeholder="{$txtAddressApartment|escape:'htmlall':'UTF-8'}">
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3"></label>
                                    <div class="col-lg-9 col-lg-offet-3">
                                        <input type="text" name="AddressLine3" id="AddressLine3" value="{$AddressLine3|escape:'htmlall':'UTF-8'}" maxlength="35" class="form-control ups-form-control fixed-width-xxl" placeholder="{$txtAddressDepartment|escape:'htmlall':'UTF-8'}">
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3">
                                        <span href="" data-toggle="tooltip" class="label-tooltip" title="{$txtAccTTPostalCode|escape:'htmlall':'UTF-8'}" data-placement="top" data-html="true">{$txtPostalCode|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></span>
                                        </label>
                                    <div class="col-lg-9">
                                        <input type="text" name="PostalCode" id="PostalCode" value="{$PostalCode|escape:'htmlall':'UTF-8'}" maxlength="9" class="form-control ups-form-control fixed-width-sm">
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{$txtCity|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></label>
                                    <div class="col-lg-9">
                                        <input type="text" name="City" id="City" value="{$City|escape:'htmlall':'UTF-8'}" maxlength="30" class="form-control ups-form-control fixed-width-xxl">
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                {if $isUSA}
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{$txtState|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></label>
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
                                    <label class="control-label col-lg-3">{$txtCountry|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></label>
                                    <div class="col-lg-9">
                                        <select class="form-control ups-form-control fixed-width-lg" name="CountryCode" maxlength="30" disabled>
                                            {foreach $countries as $code => $name}
                                            <option {if $code == $countrySelected } selected {/if} value="{$code|escape:'htmlall':'UTF-8'}">{$name|escape:'htmlall':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="optradio" id="rate04" value="1">{$txtHaveAccountUPS|escape:'htmlall':'UTF-8'}
                                </label>
                            </div>
                        </div>
                        <div class="rate04_show upshidden">
                            <div class="help-block">{$txtLatestInvoice|escape:'htmlall':'UTF-8'}</div>
                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span href="" data-toggle="tooltip" class="label-tooltip" title="{$txtAccTTAccountName|escape:'htmlall':'UTF-8'}" data-placement="top" data-html="true">{$txtAccountName|escape:'htmlall':'UTF-8'}:<sup class="star">*</sup></span>
                                </label>
                                <div class="col-lg-9">
                                    <input type="text" id="AccountName" name="AccountName" value="{$AccountName|escape:'htmlall':'UTF-8'}" maxlength="35" class="form-control ups-form-control fixed-width-xxl">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span href="" data-toggle="tooltip" class="label-tooltip" title="{$txtTTAccountNumber|escape:'htmlall':'UTF-8'}" data-placement="top" data-html="true">{$txtAccountNumber|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></span>
                                </label>
                                <div class="col-lg-9">
                                    <input type="text" id="AccountNumber" name="AccountNumber" value="{$AccountNumber|escape:'htmlall':'UTF-8'}" maxlength="6" class="form-control ups-form-control fixed-width-sm">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span href="" data-toggle="tooltip" class="label-tooltip" title="{$txtTTInvoiceNumber|escape:'htmlall':'UTF-8'}" data-placement="top" data-html="true">{$txtInvoiceNumber|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></span>
                                </label>
                                <div class="col-lg-9">
                                    <input type="text" id="InvoiceNumber" name="InvoiceNumber" value="{$InvoiceNumber|escape:'htmlall':'UTF-8'}" maxlength="15" class="form-control ups-form-control fixed-width-xxl">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span href="" data-toggle="tooltip" class="label-tooltip" title="{$txtTInvoiceAmount|escape:'htmlall':'UTF-8'}" data-placement="top" data-html="true">{$txtInvoiceAmount|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" id="InvoiceAmount" name="InvoiceAmount" value="{$InvoiceAmount|escape:'htmlall':'UTF-8'}" maxlength="19" class="form-control ups-form-control fixed-width-xxl">
                                </div>
                                <div class="clearfix"></div>
                            </div>
							
							{if $isUSA}
							<div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span href="" data-toggle="tooltip" class="label-tooltip" title="{$txtControlID|escape:'htmlall':'UTF-8'}" data-placement="top" data-html="true">{$txtControlID|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" id="ControlID" name="ControlID" value="{$ControlID|escape:'htmlall':'UTF-8'}" maxlength="19" class="form-control fixed-width-xxl">
                                </div>
                                <div class="clearfix"></div>
                            </div>
							{/if}
							
                            <div class="form-group">
                                <label class="control-label col-lg-3" style="padding-right: 10px">{$txtCurrency|escape:'htmlall':'UTF-8'}<sup class="star">*</sup>:</label>
                                <div class="col-lg-9">
                                    <select name="Currency" class="form-control ups-form-control fixed-width-lg" id="Currency">
                                        {foreach $currency as $code => $name}
                                            <option {if $code == $Currency} selected {/if} value="{$code|escape:'htmlall':'UTF-8'}">{$name|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span href="" data-toggle="tooltip" class="label-tooltip" title="{$txtTTInvoiceDate|escape:'htmlall':'UTF-8'}" data-placement="top" data-html="true">{$txtInvoiceDate|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></span>
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
                                    <input type="radio" class="form-check-input" name="optradio" checked id="rate05" value="2">{$txtHaveAccountUPSWithout|escape:'htmlall':'UTF-8'}
                                </label>
                            </div>
                        </div>
                        <div class="rate05_show upshidden">
                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span href="" data-toggle="tooltip" class="label-tooltip" title="{$txtAccTTAccountName|escape:'htmlall':'UTF-8'}" data-placement="top" data-html="true">{$txtAccountName|escape:'htmlall':'UTF-8'}:<sup class="star">*</sup></span>
                                </label>
                                <div class="col-lg-9">
                                    <input type="text" id="AccountName1" name="AccountName1" value="{$AccountName1|escape:'htmlall':'UTF-8'}" maxlength="35" class="form-control ups-form-control fixed-width-xxl">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span href="" data-toggle="tooltip" class="label-tooltip" title="{$txtTTAccountNumber|escape:'htmlall':'UTF-8'}" data-placement="top" data-html="true">{$txtAccountNumber|escape:'htmlall':'UTF-8'}<sup class="star">*</sup></span>
                                </label>
                                <div class="col-lg-9">
                                    <input type="text" id="AccountNumber1" name="AccountNumber1" value="{$AccountNumber1|escape:'htmlall':'UTF-8'}" maxlength="6" class="form-control ups-form-control fixed-width-sm">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="optradio" checked id="rate06" value="0">{$txtHaveNoAccount|escape:'htmlall':'UTF-8'} 
									{if $isUSA}
										<a href= "https://www.ups.com/assets/resources/media/en_US/CO0_US.pdf" target="_blank">{$txtHaveNotAccountUSLink|escape:'htmlall':'UTF-8'}</a>
									{/if}
                                </label>
                            </div>
                        </div>
                        <div class="rate06_show">
                            {if $isUSA}
                            <div class="help-block">{$txtDescriptionUS|escape:'htmlall':'UTF-8'}</div>
                            {/if}
                            <div class="form-group">
                                <label class="control-label col-lg-3">{$txtVatNumber|escape:'htmlall':'UTF-8'}</label>
                                <div class="col-lg-9">
                                    <input type="text" id="vatNumber" name="vatNumber" value="{$vatNumber|escape:'htmlall':'UTF-8'}" maxlength="15" class="form-control ups-form-control fixed-width-xxl">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            {if !$isUSA}
                            <div class="form-group">
                                <label class="control-label col-lg-3">{$txtPromoCode|escape:'htmlall':'UTF-8'}</label>
                                <div class="col-lg-9">
                                    <input type="text" id="promoCode" name="promoCode" value="{$promoCode|escape:'htmlall':'UTF-8'}" maxlength="9" class="form-control ups-form-control fixed-width-lg">
                                </div>
                                <div class="clearfix"></div>
                            </div>
							{/if}
                            <div>
                                <label class="col-lg-12">{$txtPleaseNote|escape:'htmlall':'UTF-8'}</label>
                            </div>
                            <div class="form-group nomargin">
                                <label class="col-lg-12">{$txtAuthorizedUPSAccessPoint|escape:'htmlall':'UTF-8'}</label>
                            </div>
                            <div>
                                <label class="col-lg-12">
                                    {$txtInformationDangerous|escape:'htmlall':'UTF-8'|htmlspecialchars_decode:3}
                                    <br/>
                                    <a target='_blank' href="{$txtHelpLink|escape:'htmlall':'UTF-8'|htmlspecialchars_decode:3}">
                                        [{$txtHelpLink|escape:'htmlall':'UTF-8'|htmlspecialchars_decode:3}]
                                    </a>
                                </label>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <!-- Additional hidden field to store Blackbox -->
                        <input type="hidden" name="ioBlackBox" id="ioBlackBox">
                        <div class="panel-footer text-right">
                            <button type="submit" name="submitAccount" class="btn btn-default pull-right" data-toggle="modal" data-target="#account-error"><i class="process-icon-save"></i>{$txtGetStarted|escape:'htmlall':'UTF-8'}</button>
                        </div>
                    </form>
                </div>
                <!-- end wrap -->
                <!-- begin footer -->

        </div>
    </div>
</div>

<!-- end collapse -->
<style>
.nomargin {
    margin-left: 0!important;
    margin-right: 0!important;
}
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
</style>
<script>
    // Basic configuration for IOvation
    var io_bbout_element_id = 'ioBlackBox';
    var io_install_stm= false;   // do not install Active X
    var io_exclude_stm= 12;      // do not run Active X
    var io_install_flash= false;   // do not install Flash
    var io_enable_rip= true;   // enable detection of Real IP
</script>
<script src="https://ci-mpsnare.iovation.com/snare.js"></script>
