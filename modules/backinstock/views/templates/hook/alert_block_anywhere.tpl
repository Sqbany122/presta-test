<style>
    {$customcss nofilter}{*Variable contains a HTML, escape not required*}
    #email_data_back .pal_small_input_row.pal_email_label {
        width: 100%;
        font-size: 13px;
    }

    .notifyButton {
        margin-top: 15px;
    }

    #email_data_back #notify_email_back {
        margin: 0;
        width: 100% !important;
        padding: 5px 0;
        /*max-width:400px*/
    }

    #email_data_back label {
        font-size: 13px;
        vertical-align: middle;
    }

    .back_body {
        display: inline-block;
        width: 100%;

    }

    .product_update_block_back {
        background: url(../../img/email_bg.jpg);
    }

    .back_left {
        width: 100%;
        float: left;
        position: relative;
        z-index: 999;
    }

    .back_right {
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
    }

    .back_left #email_data_back input {
        border-radius: 2px;
        border: 1px solid #e6e6e6;
        padding: 8px;
        background: #f9f9f9;
    }

    .back_right img {
        max-width: 100%;
    }

    .product_update_block_back {
        background: #fff;
        padding: 20px;
        border-radius: 4px;
        border: 1px solid #e8e7e7;
        position: relative;
        margin-top: 20px;

    }

    .back_heading {
        position: relative;
    }

    .back_right img {
        max-width: 60px;
        opacity: 1;
        position: absolute;
        right: 10px;
        bottom: 10px;
        box-shadow: 0 0 5px #4f73a9;
        border-radius: 50%;
    }

    .product_update_block_back:before {
        background-image: url({$module_dir}views/img/email_bg.jpg);
        background-repeat: no-repeat;
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        background-size: auto;
        background-position: center;
        opacity: 0.1;
    }

    .back_heading h5 {
        background: #3a99d7;
        padding: 10px;
        text-align: center;
        font-weight: normal;
    }

    .notifyButton .btn {
        background: #ffd630 !important;
        border: 1px solid #f5be18 !important;
        color: #000 !important;
        font-size: 14px;
    }

    #user_quantity_subscribe_back {
        padding-top: 5px !important;
    }
</style>
<script>
    {$customjs nofilter}{*Variable contains a HTML, escape not required*}
    var backinstock_privacy_accept_error = "{l s='Please accept the terms of service and privacy policy before proceeding.' mod='backinstock'}";
    var negative_quantity_error = "{l s='Please enter positive quantity' mod='backinstock'}";
</script>
{block name='alert_block'}
    {* style="display:none; background: {$background}; border:1px solid {$border};" *}
    <form id="alert_block_anywhere">
        <div class="product_update_block_back" style="display:none; background: {$background}; border:1px solid {$border};">

            <div class="back_heading" style='color:{$text}'>
                <h5 style="background:{$background_heading} !important;">
                    {l s='Notify me when this product is in Stock' mod='backinstock'}&nbsp;
                </h5>
            </div>
            <div class="back_body">
                <div class="back_left">
                    <div id="email_data_back" class="pal_popup_product_email_row_back">
                        {** Changes by gopi for cutom feilds**}
                        {if is_array($kb_available_field) && !empty($kb_available_field)}
                            <div id="bis_custom">
                                {foreach $kb_available_field as $kbfield}
                                    {if $kbfield['type'] == 'text'}
                                        <div class="form-group row">
                                            <label style="color:{$text}" for="{$kbfield['field_name']|escape:'htmlall':'UTF-8'}"
                                                class="col-md-3 form-control-label {if $kbfield['required']}required{/if}">{$kbfield['label']|escape:'htmlall':'UTF-8'}</label>
                                            <div class="col-md-12">
                                                <input type="{if $kbfield['validation'] == 'isEmail'}email{else}text{/if}"
                                                    {if $kbfield['placeholder'] != ''}placeholder="{$kbfield['placeholder']|escape:'htmlall':'UTF-8'}"
                                                    {/if} name='{$kbfield['field_name']|escape:'htmlall':'UTF-8'}'
                                                    id="{$kbfield['html_id']|escape:'htmlall':'UTF-8'}"
                                                    class="kbfield {$kbfield['html_class']|escape:'htmlall':'UTF-8'} {if $kbfield['required']}is_required{/if} {if $kbfield['validation'] != ''}validate{/if}  form-control"
                                                    {if $kbfield['validation'] != ''}
                                                    data-validate="{$kbfield['validation']|escape:'htmlall':'UTF-8'}" {/if}
                                                    {if ($kbfield['max_length'] != '') && ($kbfield['max_length'] > 0)}
                                                    maxlength="{$kbfield['max_length']|escape:'htmlall':'UTF-8'}" {/if}
                                                    {if $kbfield['min_length'] != ''}minlength="{$kbfield['min_length']|escape:'htmlall':'UTF-8'}"
                                                    {/if}
                                                    value="{if isset($kbfield['customer_value'])}{$kbfield['customer_value']|escape:'htmlall':'UTF-8'}{/if}" />
                                                {if $kbfield['error_msg'] != ''}<p class="error_message" style="display:none;">
                                                    {$kbfield['error_msg']|escape:'htmlall':'UTF-8'}</p>{/if}
                                            </div>
                                        </div>
                                    {/if}
                                    {if $kbfield['type'] == 'select'}
                                        <div class="form-group row">
                                            <label style="color:{$text}" for="{$kbfield['html_id']|escape:'htmlall':'UTF-8'}"
                                                class="col-md-3 form-control-label {if $kbfield['required']}required{/if}">{$kbfield['label']|escape:'htmlall':'UTF-8'}</label>
                                            <div class="col-md-6">
                                                <select name='{$kbfield['field_name']|escape:'htmlall':'UTF-8'}'
                                                    id='{$kbfield['html_id']|escape:'htmlall':'UTF-8'}'
                                                    class="kbfield {$kbfield['html_class']|escape:'htmlall':'UTF-8'} {if $kbfield['required']}is_required{/if} form-control">
                                                    {if $kbfield['value'] != ''}
                                                        {foreach $kbfield['value']|json_decode:1 as $field_value}
                                                            <option value="{$field_value['option_value']|escape:'htmlall':'UTF-8'}"
                                                                {if isset($kbfield['default_value'])} 
                                                                    {if $kbfield['default_value'] != ""}
                                                                        {if isset($kbfield['default_value'][0]) && isset($kbfield['default_value'][0]['option_value'])}
                                                                            {if $kbfield['default_value'][0]['option_value'] == $field_value['option_value']}
                                                                            selected {/if} 
                                                                        {/if} 
                                                                    {/if} 
                                                                {/if}>
                                                                {$field_value['option_label']|escape:'htmlall':'UTF-8'}</option>
                                                        {/foreach}
                                                    {/if}
                                                </select>
                                                {if $kbfield['error_msg'] != ''}<p class="error_message" style="display:none;">
                                                    {$kbfield['error_msg']|escape:'htmlall':'UTF-8'}</p>{/if}
                                            </div>
                                        </div>
                                    {/if}
                                    {if $kbfield['type'] == 'radio'}
                                        <div class="form-group row">
                                            <label style="color:{$text}"
                                                class="col-md-3 form-control-label {if $kbfield['required']}required{/if}">{$kbfield['label']|escape:'htmlall':'UTF-8'}</label>
                                            <div class="col-md-12">
                                                <div class="radio_kb_validate">
                                                    {if $kbfield['value'] != ''}
                                                        {foreach $kbfield['value']|json_decode:1 as $field_value}
                                                            <label for="{$kbfield['field_name']}" class="radio-inline">
                                                                <span class="custom-radio">
                                                                    <input type="radio" name="{$kbfield['field_name']|escape:'htmlall':'UTF-8'}"
                                                                        id="{$kbfield['html_id']|escape:'htmlall':'UTF-8'}"
                                                                        class="kbfield {$kbfield['html_class']|escape:'htmlall':'UTF-8'} {if $kbfield['required']}is_required{/if}"
                                                                        value="{$field_value['option_value']|escape:'htmlall':'UTF-8'}"
                                                                        {if isset($kbfield['default_value'])}
                                                                            {if $kbfield['default_value'] != ""}
                                                                                {if isset($kbfield['default_value'][0]) && isset($kbfield['default_value'][0]['option_value'])}
                                                                                    {if $kbfield['default_value'][0]['option_value'] == $field_value['option_value']}
                                                                                    checked {/if} 
                                                                                {/if} 
                                                                            {/if} 
                                                                        {/if} /><span></span>
                                                                </span>
                                                                {$field_value['option_label']|escape:'htmlall':'UTF-8'}
                                                            </label>
                                                        {/foreach}
                                                    {/if}
                                                    {if $kbfield['error_msg'] != ''}<span class="error_message"
                                                        style="display:none;">{$kbfield['error_msg']|escape:'htmlall':'UTF-8'}</span>{/if}
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                    {if $kbfield['type'] == 'checkbox'}
                                        <div class="form-group row">
                                            <label style="color:{$text}"
                                                class="col-md-3 form-control-label {if $kbfield['required']}required{/if}">{$kbfield['label']}</label>
                                            <div class="col-md-12">
                                                <div class="checkbox_kb_validate">
                                                    {if $kbfield['value'] != ''}
                                                        {foreach $kbfield['value']|json_decode:1 as $field_value}
                                                            <span class="custom-checkbox">
                                                                <input type="checkbox" name="{$kbfield['field_name']}[]"
                                                                    id="{$kbfield['html_id']}"
                                                                    class="kbfield {$kbfield['html_class']} {if $kbfield['required']}is_required{/if}"
                                                                    value="{$field_value['option_value']}" {if isset($kbfield['default_value'])}
                                                                        {if $kbfield['default_value'] != ""}
                                                                            {if isset($kbfield['default_value'][0]) && isset($kbfield['default_value'][0]['option_value'])}
                                                                                {if $kbfield['default_value'][0]['option_value'] == $field_value['option_value']}
                                                                                checked {/if} 
                                                                            {/if} 
                                                                        {/if} 
                                                                    {/if} />
                                                                <span><i class="material-icons checkbox-checked"></i></span>
                                                                <label for="{$kbfield['field_name']}">{$field_value['option_label']}</label>
                                                            </span>
                                                        {/foreach}
                                                    {/if}

                                                    {if $kbfield['error_msg'] != ''}<span class="error_message"
                                                        style="display:none;">{$kbfield['error_msg']}</span>{/if}
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                    {if $kbfield['type'] == 'textarea'}
                                        <div class="form-group row">
                                            <label style="color:{$text}" data-require="{$kbfield['required']}"
                                                class="col-md-3 form-control-label {if $kbfield['required']}required{/if}"
                                                for="{$kbfield['html_id']}">{$kbfield['label']}</label>
                                            <div class="col-md-12">
                                                <textarea {if $kbfield['placeholder'] != ''}placeholder="{$kbfield['placeholder']}"
                                                    {/if} name='{$kbfield['field_name']}' id='{$kbfield['html_id']}'
                                                    class="kbfield {$kbfield['html_class']} 
                                                                                                {if $kbfield['required']}is_required{/if} {if $kbfield['validation'] != ''}validate{/if}  form-control"
                                                    {if $kbfield['validation'] != ''} data-validate="{$kbfield['validation']}" {/if}
                                                    {if ($kbfield['max_length'] != '') && ($kbfield['max_length'] > 0)}
                                                    maxlength="{$kbfield['max_length']}" {/if}
                                                    {if $kbfield['min_length'] != ''}minlength="{$kbfield['min_length']}"
                                                        {/if}>{if isset($kbfield['customer_value'])}{$kbfield['customer_value']}{/if}</textarea>
                                                    {if $kbfield['error_msg'] != ''}<span class="error_message"
                                                        style="display:none;">{$kbfield['error_msg']}</span>{/if}
                                                </div>
                                            </div>
                                        {/if}
                                    {/foreach}
                                </div>
                            {/if}
                            {**Changes by gopi end here**}

                            {if isset($show_quantity) && $show_quantity == 1}
                                <div class="" style="width:20%;color:{$text}">{l s='Quantity:' mod='backinstock'}</div>
                                <div id="notify_quantity_back" class="" style="width:100%; ">
                                    <input type="number" id='user_quantity_subscribe_back' style="padding-top: 5px"
                                        class="form-control" name="customer_quantity_back" placeholder="{l s='5' mod='backinstock'}"
                                        value=0 />
                                </div>
                            {/if}
                            <div class="pal_small_input_row pal_email_label" style="width:20%;color:{$text}">
                                {l s='Email:' mod='backinstock'}</div>
                            <div id="notify_email_back" class="pal_small_input_row pal_email_input_back" style="width:55%;">
                                <input type="text" id='user_email_subscribe_back' class="form-control" {if $disabled == 1 }
                                    readonly {else} 
                                    {/if} name="customer_email_back"
                                    placeholder="{l s='Email' mod='backinstock'}" value='{$customer['email']}' />
                            </div>
                            <input type='hidden' name='actual_price' id='actual_price' value='{$actual_price}' />
                            <input type='hidden' name='product_id' value='{$product_id}' />
                            <input type='hidden' name='customer_id' value='{$customer['id']}' />
                            <input type='hidden' name='combination_id' id='pal_attribute_id_back' value='' />
                            <input type='hidden' name='currency_id' value='{$currency_id}' />
                            <input type='hidden' name='currency_code' value='{$currency_code}' />
                            <input type='hidden' name='shop_id' value='{$shop_id}' />
                            <input type='hidden' name='subscribe_type_back' id='subscribe_type_back' value='' />
                            <div id="email_error_back"></div>
                            {if isset($enable_gdpr_policy) && ($enable_gdpr_policy eq 1)}
                                <div id="proupdate_gdpr_data_back" class="">
                                    <div class="checkbox">
                                        <input type="checkbox" name="kb_proupdate_back_privacy_text"
                                            id="kb_proupdate_back_privacy_text" value="1">
                                        <label
                                            for="kb_proupdate_back_privacy_text">{$gdpr_policy_text|escape:'htmlall':'UTF-8'}</label>
                                        {if isset($gdpr_policy_url) && ($gdpr_policy_url neq '')}
                                            (<a href="{$gdpr_policy_url|escape:'htmlall':'UTF-8'}" target="_blank"
                                                style="cursor: pointer; color:#609;">
                                                {l s='Read the terms of service' mod='backinstock'}
                                            </a>)
                                        {/if}
                                    </div>
                                </div>
                            {/if}
                            <div class="notifyButton">
                                <button type="submit" id="save_subscribe_back" class="btn btn-primary"
                                    onclick="return save_subscribe_data_back_anywhere()"
                                    style="color:{$notify_text} !important;background:{$notify_background} !important;border-color: {$notify_border} !important;">
                                    {l s='Notify Me' mod='backinstock'}
                                </button>
                                <span id="loading_image_back" style="display: none;"><img
                                        src="{$module_dir}views/img/loading.gif" height="25" width="25" />
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="back_right">
                        <img src="{$module_dir}views/img/email.png" />
                    </div>
                </div>
                {if $kb_grb_enable eq 1}
                    <div id="kb_stock_gr" class="form-group">
                        <input type="hidden" name="kb_recaptcha_response" id="kb_recaptchaResponse">
                    </div>
                {/if}
            </div>
        </form>

        {if $kb_grb_enable eq 1}
            {if $grb_check_protocol eq 'https://'}
                <script src="https://www.google.com/recaptcha/api.js?render={$kb_site_key}"></script>
            {else}
                <script src="http://www.google.com/recaptcha/api.js?render={$kb_site_key}"></script>
            {/if}
            <script>
                var kb_site_key = "{$kb_site_key}";
                grecaptcha.ready(function() {
                    grecaptcha.execute(kb_site_key, { action: 'submit' }).then(function(token) {
                        var recaptchaResponse = document.getElementById('kb_recaptchaResponse');
                        recaptchaResponse.value = token;
                    });
                });
            </script>
        {/if}
        <script type='text/javascript'>
            var block_id;

            //var quantityAvailable = 0;
            var pal_email_require = "{l s='Please enter your Email id' mod='backinstock'}";
            var negative_quantity_error = "{l s='Please enter positive quantity' mod='backinstock'}";
            var pal_email_invalid_error = "{l s='Please enter a valid Email id' mod='backinstock'}";
            var pal_alert_create_success_msg = "{l s='You are successfully subscribed' mod='backinstock'}";
            var pal_alert_update_success_msg = "{l s='You are already subscribed' mod='backinstock'}";
            var pal_alert_update_recaptcha_msg = "{l s='Failed to verify recpatcha,please try again after sometime' mod='backinstock'}";
            var pal_alert_update_error_msg = "{l s='Error in subscription' mod='backinstock'}";
            var action_product_front_back = '{$action_product_front_back nofilter}'; //variable url content can not escape
            var empty_fname = "{l s='Please enter First name.' mod='backinstock'}";
            var maxchar_fname = "{l s='First name cannot be greater than # characters.' mod='backinstock'}";
            var minchar_fname = "{l s='First name cannot be less than # characters.' mod='backinstock'}";
            var empty_mname = "{l s='Please enter middle name.' mod='backinstock'}";
            var maxchar_mname = "{l s='Middle name cannot be greater than # characters.' mod='backinstock'}";
            var minchar_mname = "{l s='Middle name cannot be less than # characters.' mod='backinstock'}";
            var only_alphabet = "{l s='Only alphabets are allowed.' mod='backinstock'}";
            var empty_lname = "{l s='Please enter Last name.' mod='backinstock'}";
            var maxchar_lname = "{l s='Last name cannot be greater than # characters.' mod='backinstock'}";
            var minchar_lname = "{l s='Last name cannot be less than # characters.' mod='backinstock'}";
            var alphanumeric = "{l s='Field should be alphanumeric.' mod='backinstock'}";
            var empty_pass = "{l s='Please enter Password.' mod='backinstock'}";
            var maxchar_pass = "{l s='Password cannot be greater than # characters.' mod='backinstock'}";
            var minchar_pass = "{l s='Password cannot be less than # characters.' mod='backinstock'}";
            var specialchar_pass = "{l s='Password should contain atleast 1 special character.' mod='backinstock'}";
            var alphabets_pass = "{l s='Password should contain alphabets.' mod='backinstock'}";
            var capital_alphabets_pass = "{l s='Password should contain atleast 1 capital letter.' mod='backinstock'}";
            var small_alphabets_pass = "{l s='Password should contain atleast 1 small letter.' mod='backinstock'}";
            var digit_pass = "{l s='Password should contain atleast 1 digit.' mod='backinstock'}";
            var empty_field = "{l s='Field cannot be empty.' mod='backinstock'}";
            var number_field = "{l s='You can enter only numbers.' mod='backinstock'}";
            var positive_number = "{l s='Number should be greater than 0.' mod='backinstock'}";
            var maxchar_field = "{l s='Field cannot be greater than # characters.' mod='backinstock'}";
            var minchar_field = "{l s='Field cannot be less than # character(s).' mod='backinstock'}";
            var empty_email = "{l s='Please enter Email.' mod='backinstock'}";
            var validate_email = "{l s='Please enter a valid Email.' mod='backinstock'}";
            var empty_country = "{l s='Please enter country name.' mod='backinstock'}";
            var maxchar_country = "{l s='Country cannot be greater than # characters.' mod='backinstock'}";
            var minchar_country = "{l s='Country cannot be less than # characters.' mod='backinstock'}";
            var empty_city = "{l s='Please enter city name.' mod='backinstock'}";
            var maxchar_city = "{l s='City cannot be greater than # characters.' mod='backinstock'}";
            var minchar_city = "{l s='City cannot be less than # characters.' mod='backinstock'}";
            var empty_state = "{l s='Please enter state name.' mod='backinstock'}";
            var maxchar_state = "{l s='State cannot be greater than # characters.' mod='backinstock'}";
            var minchar_state = "{l s='State cannot be less than # characters.' mod='backinstock'}";
            var empty_proname = "{l s='Please enter product name.' mod='backinstock'}";
            var maxchar_proname = "{l s='Product cannot be greater than # characters.' mod='backinstock'}";
            var minchar_proname = "{l s='Product cannot be less than # characters.' mod='backinstock'}";
            var empty_catname = "{l s='Please enter category name.' mod='backinstock'}";
            var maxchar_catname = "{l s='Category cannot be greater than # characters.' mod='backinstock'}";
            var minchar_catname = "{l s='Category cannot be less than # characters.' mod='backinstock'}";
            var empty_zip = "{l s='Please enter zip code.' mod='backinstock'}";
            var maxchar_zip = "{l s='Zip cannot be greater than # characters.' mod='backinstock'}";
            var minchar_zip = "{l s='Zip cannot be less than # characters.' mod='backinstock'}";
            var empty_username = "{l s='Please enter Username.' mod='backinstock'}";
            var maxchar_username = "{l s='Username cannot be greater than # characters.' mod='backinstock'}";
            var minchar_username = "{l s='Username cannot be less than # characters.' mod='backinstock'}";
            var invalid_date = "{l s='Invalid date format.' mod='backinstock'}";
            var maxchar_sku = "{l s='SKU cannot be greater than # characters.' mod='backinstock'}";
            var minchar_sku = "{l s='SKU cannot be less than # characters.' mod='backinstock'}";
            var invalid_sku = "{l s='Invalid SKU format.' mod='backinstock'}";
            var empty_sku = "{l s='Please enter SKU.' mod='backinstock'}";
            var validate_range = "{l s='Number is not in the valid range. It should be betwen # and %%' mod='backinstock'}";
            var empty_address = "{l s='Please enter address.' mod='backinstock'}";
            var minchar_address = "{l s='Address cannot be less than # characters.' mod='backinstock'}";
            var maxchar_address = "{l s='Address cannot be greater than # characters.' mod='backinstock'}";
            var empty_company = "{l s='Please enter company name.' mod='backinstock'}";
            var minchar_company = "{l s='Company name cannot be less than # characters.' mod='backinstock'}";
            var maxchar_company = "{l s='Company name cannot be greater than # characters.' mod='backinstock'}";
            var invalid_phone = "{l s='Phone number is invalid.' mod='backinstock'}";
            var empty_phone = "{l s='Please enter phone number.' mod='backinstock'}";
            var minchar_phone = "{l s='Phone number cannot be less than # characters.' mod='backinstock'}";
            var maxchar_phone = "{l s='Phone number cannot be greater than # characters.' mod='backinstock'}";
            var empty_brand = "{l s='Please enter brand name.' mod='backinstock'}";
            var maxchar_brand = "{l s='Brand name cannot be greater than # characters.' mod='backinstock'}";
            var minchar_brand = "{l s='Brand name cannot be less than # characters.' mod='backinstock'}";
            var empty_shipment = "{l s='Please enter Shimpment.' mod='backinstock'}";
            var maxchar_shipment = "{l s='Shipment cannot be greater than # characters.' mod='backinstock'}";
            var minchar_shipment = "{l s='Shipment cannot be less than # characters.' mod='backinstock'}";
            var invalid_ip = "{l s='Invalid IP format.' mod='backinstock'}";
            var invalid_url = "{l s='Invalid URL format.' mod='backinstock'}";
            var empty_url = "{l s='Please enter URL.' mod='backinstock'}";
            var valid_amount = "{l s='Field should be numeric.' mod='backinstock'}";
            var valid_decimal = "{l s='Field can have only upto two decimal values.' mod='backinstock'}";
            var max_email = "{l s='Email cannot be greater than # characters.' mod='backinstock'}";
            var specialchar_zip = "{l s='Zip should not have special characters.' mod='backinstock'}";
            var specialchar_sku = "{l s='SKU should not have special characters.' mod='backinstock'}";
            var max_url = "{l s='URL cannot be greater than # characters.' mod='backinstock'}";
            var valid_percentage = "{l s='Percentage should be in number.' mod='backinstock'}";
            var between_percentage = "{l s='Percentage should be between 0 and 100.' mod='backinstock'}";
            var maxchar_size = "{l s='Size cannot be greater than # characters.' mod='backinstock'}";
            var specialchar_size = "{l s='Size should not have special characters.' mod='backinstock'}";
            var specialchar_upc = "{l s='UPC should not have special characters.' mod='backinstock'}";
            var maxchar_upc = "{l s='UPC cannot be greater than # characters.' mod='backinstock'}";
            var specialchar_ean = "{l s='EAN should not have special characters.' mod='backinstock'}";
            var maxchar_ean = "{l s='EAN cannot be greater than # characters.' mod='backinstock'}";
            var specialchar_bar = "{l s='Barcode should not have special characters.' mod='backinstock'}";
            var maxchar_bar = "{l s='Barcode cannot be greater than # characters.' mod='backinstock'}";
            var positive_amount = "{l s='Field should be positive.' mod='backinstock'}";
            var maxchar_color = "{l s='Color could not be greater than # characters.' mod='backinstock'}";
            var invalid_color = "{l s='Color is not valid.' mod='backinstock'}";
            var specialchar = "{l s='Special characters are not allowed.' mod='backinstock'}";
            var script = "{l s='Script tags are not allowed.' mod='backinstock'}";
            var style = "{l s='Style tags are not allowed.' mod='backinstock'}";
            var iframe = "{l s='Iframe tags are not allowed.' mod='backinstock'}";
            var not_image = "{l s='Uploaded file is not an image.' mod='backinstock'}";
            var image_size = "{l s='Uploaded file size must be less than #.' mod='backinstock'}";
            var html_tags = "{l s='Field should not contain HTML tags.' mod='backinstock'}";
            var number_pos = "{l s='You can enter only positive numbers.' mod='backinstock'}";
            var invalid_separator = "{l s='Invalid comma (#) separated values.' mod='backinstock'}";

            var price_alert_product_price = '{$product_price}';
            {if $version eq 6}
                var price_alert_product_image = '{$link->getImageLink($img_link_rewrite, $image_id , 'cart_default')}';
            {else}
                var price_alert_product_image = '{$link->getImageLink($img_link_rewrite, $image_id)}';
            {/if}
            //changes by gopi for custom feilds
            var kb_not_valid = "{l s='Field is not valid' mod='backinstock'}";
            var kb_empty_field = "{l s='Field cannot be empty.' mod='backinstock'}";
            var kb_number_field = "{l s='You can enter only numbers.' mod='backinstock'}";
            var kb_positive_number = "{l s='Number should be greater than 0.' mod='backinstock'}";
            var kb_maxchar_field = "{l s='Field cannot be greater than # characters.' mod='backinstock'}";
            var kb_minchar_field = "{l s='Field cannot be less than # character(s).' mod='backinstock'}";
            var kb_empty_email = "{l s='Please enter Email.' mod='backinstock'}";
            var kb_validate_email = "{l s='Please enter a valid Email.' mod='backinstock'}";
            var kb_max_email = "{l s='Email cannot be greater than # characters.' mod='backinstock'}";
            var kb_script = "{l s='Script tags are not allowed.' mod='backinstock'}";
            var kb_style = "{l s='Style tags are not allowed.' mod='backinstock'}";
            var kb_iframe = "{l s='Iframe tags are not allowed.' mod='backinstock'}";
            var kb_html_tags = "{l s='Field should not contain HTML tags.' mod='backinstock'}";
            var kb_not_valid = "{l s='Field is not valid' mod='backinstock'}";
            var field_not_empty = "{l s='Field cannot be empty' mod='backinstock'}";
            //changes by gopi end for custom feilds
        </script>
    {/block}
    {*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    velsof.com <support@velsof.com>
* @copyright 2014 Velocity Software Solutions Pvt Ltd
* @license   see file: LICENSE.txt
*
* Description
*
* Product Update Block Page
*}