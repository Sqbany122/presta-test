{extends file="helpers/form/form.tpl"}

{block name="defaultForm"}
<script>
    var kb_placeholder_text = "{l s='Select some options.' mod='backinstock'}";
    var no_results_text = "{l s='No results match' mod='backinstock'}";
    var available_product_empty = "{l s='Select atleast one product' mod='backinstock'}";
    var mod_dir='{$mod_dir|escape:'htmlall':'UTF-8'}';
    var version ={$version|escape:'htmlall':'UTF-8'};
    var action_product_update ="{$action_product_update|escape:'quotes':'UTF-8'}";
    var no_data = "{l s='No data to display' mod='backinstock'}";
    var products = "{l s='Products' mod='backinstock'}";
    var email_marketing_values = {$email_marketing_values|escape:'quotes':'UTF-8'};
    var customers = "{l s='No. of customers' mod='backinstock'}";
    var empty_field = "{l s='Field cannot be empty.' mod='backinstock'}";
    var error_length_message = "{l s='Only 10000 Characters are allowed.' mod='backinstock'}";
    var check_for_all_lang = "{l s='This field can not be empty.Please check for all languages.' mod='backinstock'}";
    var no_list_mailchimp = "{l s='No list exists for this API key.' mod='backinstock'}";
    var module_path = "{$module_path nofilter}";{*variable contains HTML content, Can not escape this*}
    var kb_numeric = "{l s='Per Product Page Field should be a positive Integer.' mod='backinstock'}";
    var kb_numeric_low_stock = "{l s='Low Stock Quantity Field should be a positive Integer.' mod='backinstock'}";
    var path = "{$path nofilter}";{*variable contains HTML content, Can not escape this*}
    var path_fold = "{$path_fold nofilter}";{*variable contains HTML content, Can not escape this*}
    var front_cron_url = "{$front_cron_url nofilter}";{*variable contains HTML content, Can not escape this*}
    var check_url_for_all_lang = "{l s='URL is not valid.Please check for all languages.' mod='backinstock'}";
        velovalidation.setErrorLanguage({
            empty_fname: "{l s='Please enter First name.' mod='backinstock'}",
            maxchar_fname: "{l s='First name cannot be greater than # characters.' mod='backinstock'}",
            minchar_fname: "{l s='First name cannot be less than # characters.' mod='backinstock'}",
            empty_mname: "{l s='Please enter middle name.' mod='backinstock'}",
            maxchar_mname: "{l s='Middle name cannot be greater than # characters.' mod='backinstock'}",
            minchar_mname: "{l s='Middle name cannot be less than # characters.' mod='backinstock'}",
            only_alphabet: "{l s='Only alphabets are allowed.' mod='backinstock'}",
            empty_lname: "{l s='Please enter Last name.' mod='backinstock'}",
            maxchar_lname: "{l s='Last name cannot be greater than # characters.' mod='backinstock'}",
            minchar_lname: "{l s='Last name cannot be less than # characters.' mod='backinstock'}",
            alphanumeric: "{l s='Field should be alphanumeric.' mod='backinstock'}",
            empty_pass: "{l s='Please enter Password.' mod='backinstock'}",
            maxchar_pass: "{l s='Password cannot be greater than # characters.' mod='backinstock'}",
            minchar_pass: "{l s='Password cannot be less than # characters.' mod='backinstock'}",
            specialchar_pass: "{l s='Password should contain atleast 1 special character.' mod='backinstock'}",
            alphabets_pass: "{l s='Password should contain alphabets.' mod='backinstock'}",
            capital_alphabets_pass: "{l s='Password should contain atleast 1 capital letter.' mod='backinstock'}",
            small_alphabets_pass: "{l s='Password should contain atleast 1 small letter.' mod='backinstock'}",
            digit_pass: "{l s='Password should contain atleast 1 digit.' mod='backinstock'}",
            empty_field: "{l s='Field cannot be empty.' mod='backinstock'}",
            number_field: "{l s='You can enter only numbers.' mod='backinstock'}",            
            positive_number: "{l s='Number should be greater than 0.' mod='backinstock'}",
            maxchar_field: "{l s='Field cannot be greater than # characters.' mod='backinstock'}",
            minchar_field: "{l s='Field cannot be less than # character(s).' mod='backinstock'}",
            empty_email: "{l s='Please enter Email.' mod='backinstock'}",
            validate_email: "{l s='Please enter a valid Email.' mod='backinstock'}",
            empty_country: "{l s='Please enter country name.' mod='backinstock'}",
            maxchar_country: "{l s='Country cannot be greater than # characters.' mod='backinstock'}",
            minchar_country: "{l s='Country cannot be less than # characters.' mod='backinstock'}",
            empty_city: "{l s='Please enter city name.' mod='backinstock'}",
            maxchar_city: "{l s='City cannot be greater than # characters.' mod='backinstock'}",
            minchar_city: "{l s='City cannot be less than # characters.' mod='backinstock'}",
            empty_state: "{l s='Please enter state name.' mod='backinstock'}",
            maxchar_state: "{l s='State cannot be greater than # characters.' mod='backinstock'}",
            minchar_state: "{l s='State cannot be less than # characters.' mod='backinstock'}",
            empty_proname: "{l s='Please enter product name.' mod='backinstock'}",
            maxchar_proname: "{l s='Product cannot be greater than # characters.' mod='backinstock'}",
            minchar_proname: "{l s='Product cannot be less than # characters.' mod='backinstock'}",
            empty_catname: "{l s='Please enter category name.' mod='backinstock'}",
            maxchar_catname: "{l s='Category cannot be greater than # characters.' mod='backinstock'}",
            minchar_catname: "{l s='Category cannot be less than # characters.' mod='backinstock'}",
            empty_zip: "{l s='Please enter zip code.' mod='backinstock'}",
            maxchar_zip: "{l s='Zip cannot be greater than # characters.' mod='backinstock'}",
            minchar_zip: "{l s='Zip cannot be less than # characters.' mod='backinstock'}",
            empty_username: "{l s='Please enter Username.' mod='backinstock'}",
            maxchar_username: "{l s='Username cannot be greater than # characters.' mod='backinstock'}",
            minchar_username: "{l s='Username cannot be less than # characters.' mod='backinstock'}",
            invalid_date: "{l s='Invalid date format.' mod='backinstock'}",
            maxchar_sku: "{l s='SKU cannot be greater than # characters.' mod='backinstock'}",
            minchar_sku: "{l s='SKU cannot be less than # characters.' mod='backinstock'}",
            invalid_sku: "{l s='Invalid SKU format.' mod='backinstock'}",
            empty_sku: "{l s='Please enter SKU.' mod='backinstock'}",
            validate_range: "{l s='Number is not in the valid range. It should be betwen # and %%' mod='backinstock'}",
            empty_address: "{l s='Please enter address.' mod='backinstock'}",
            minchar_address: "{l s='Address cannot be less than # characters.' mod='backinstock'}",
            maxchar_address: "{l s='Address cannot be greater than # characters.' mod='backinstock'}",
            empty_company: "{l s='Please enter company name.' mod='backinstock'}",
            minchar_company: "{l s='Company name cannot be less than # characters.' mod='backinstock'}",
            maxchar_company: "{l s='Company name cannot be greater than # characters.' mod='backinstock'}",
            invalid_phone: "{l s='Phone number is invalid.' mod='backinstock'}",
            empty_phone: "{l s='Please enter phone number.' mod='backinstock'}",
            minchar_phone: "{l s='Phone number cannot be less than # characters.' mod='backinstock'}",
            maxchar_phone: "{l s='Phone number cannot be greater than # characters.' mod='backinstock'}",
            empty_brand: "{l s='Please enter brand name.' mod='backinstock'}",
            maxchar_brand: "{l s='Brand name cannot be greater than # characters.' mod='backinstock'}",
            minchar_brand: "{l s='Brand name cannot be less than # characters.' mod='backinstock'}",
            empty_shipment: "{l s='Please enter Shimpment.' mod='backinstock'}",
            maxchar_shipment: "{l s='Shipment cannot be greater than # characters.' mod='backinstock'}",
            minchar_shipment: "{l s='Shipment cannot be less than # characters.' mod='backinstock'}",
            invalid_ip: "{l s='Invalid IP format.' mod='backinstock'}",
            invalid_url: "{l s='Invalid URL format.' mod='backinstock'}",
            empty_url: "{l s='Please enter URL.' mod='backinstock'}",
            valid_amount: "{l s='Field should be numeric.' mod='backinstock'}",
            valid_decimal: "{l s='Field can have only upto two decimal values.' mod='backinstock'}",
            max_email: "{l s='Email cannot be greater than # characters.' mod='backinstock'}",
            specialchar_zip: "{l s='Zip should not have special characters.' mod='backinstock'}",
            specialchar_sku: "{l s='SKU should not have special characters.' mod='backinstock'}",
            max_url: "{l s='URL cannot be greater than # characters.' mod='backinstock'}",
            valid_percentage: "{l s='Percentage should be in number.' mod='backinstock'}",
            between_percentage: "{l s='Percentage should be between 0 and 100.' mod='backinstock'}",
            maxchar_size: "{l s='Size cannot be greater than # characters.' mod='backinstock'}",
            specialchar_size: "{l s='Size should not have special characters.' mod='backinstock'}",
            specialchar_upc: "{l s='UPC should not have special characters.' mod='backinstock'}",
            maxchar_upc: "{l s='UPC cannot be greater than # characters.' mod='backinstock'}",
            specialchar_ean: "{l s='EAN should not have special characters.' mod='backinstock'}",
            maxchar_ean: "{l s='EAN cannot be greater than # characters.' mod='backinstock'}",
            specialchar_bar: "{l s='Barcode should not have special characters.' mod='backinstock'}",
            maxchar_bar: "{l s='Barcode cannot be greater than # characters.' mod='backinstock'}",
            positive_amount: "{l s='Field should be positive.' mod='backinstock'}",
            maxchar_color: "{l s='Color could not be greater than # characters.' mod='backinstock'}",
            invalid_color: "{l s='Color is not valid.' mod='backinstock'}",
            specialchar: "{l s='Special characters are not allowed.' mod='backinstock'}",
            script: "{l s='Script tags are not allowed.' mod='backinstock'}",
            style: "{l s='Style tags are not allowed.' mod='backinstock'}",
            iframe: "{l s='Iframe tags are not allowed.' mod='backinstock'}",
            not_image: "{l s='Uploaded file is not an image.' mod='backinstock'}",
            image_size: "{l s='Uploaded file size must be less than #.' mod='backinstock'}",
            html_tags: "{l s='Field should not contain HTML tags.' mod='backinstock'}",
            number_pos: "{l s='You can enter only positive numbers.' mod='backinstock'}",
            invalid_separator:"{l s='Invalid comma (#) separated values.' mod='backinstock'}",
});
</script>
{if $version eq 1.6}
        <div class='row'>
            <div class="productTabs col-lg-2 col-md-3">
                <div class="list-group">
                    {$i=1}
                    {foreach $product_tabs key=numStep item=tab}
                            <a class="list-group-item {if $tab.selected|escape:'htmlall':'UTF-8'}{if $version eq 1.5}selected{else}active{/if}{/if}" id="link-{$tab.id|escape:'htmlall':'UTF-8'}" onclick="change_tab(this,{$i|escape:'htmlall':'UTF-8'});"><img src="{$kb_image_path}admin/{$tab.data|escape:'htmlall':'UTF-8'}.png">{$tab.name|escape:'htmlall':'UTF-8'}</a>
                            {$i=$i+1}
                    {/foreach}
                </div>
            </div>
                {$form} {*Variable contains html content, escape not required*}
                {$form1} {*Variable contains html content, escape not required*}
                {$form2} {*Variable contains html content, escape not required*}
                {$form3} {*Variable contains html content, escape not required*}
                {$form4} {*Variable contains html content, escape not required*}
                {$form5} {*Variable contains html content, escape not required*}
                {$form6} {*Variable contains html content, escape not required*}
                {$form7}
                {$form8}
                {$view} {*Variable contains html content, escape not required*}
                
        </div>
        {else}
            <div>
		<div class="productTabs col-lg-2 col-md-3 tab_format" >
			<ul class="tab">
			{*todo href when nojs*}
            {$i=1}
			{foreach $product_tabs key=numStep item=tab}
				<li class="tab-row">
					<a class="tab-page {if $tab.selected|escape:'htmlall':'UTF-8'}selected{/if}" id="link-{$tab.id|escape:'htmlall':'UTF-8'}" onclick="change_tab(this,{$i|escape:'htmlall':'UTF-8'});">{$tab.name|escape:'htmlall':'UTF-8'}</a>
                    {$i=$i+1}
				</li>
			{/foreach}
			</ul>
		</div>
            {$form} {*Variable contains html content, escape not required*}
            {$form1} {*Variable contains html content, escape not required*}
            {$form2} {*Variable contains html content, escape not required*}
            
            {$form3} {*Variable contains html content, escape not required*}
            {$form4} {*Variable contains html content, escape not required*}
            {$form5} {*Variable contains html content, escape not required*}
            
            {$view} {*Variable contains html content, escape not required*}
	</div>
    
                {/if}
<style>
#content .panel-heading {
    font-family: "Open Sans",Helvetica,Arial,sans-serif !important;
}
.bootstrap .page-head h2.page-title {
    font: 15pt "Open Sans",Helvetica,Arial,sans-serif;
    margin-top: -5px;
}
.productTabs .list-group i {
    width: 22px;
    text-align: left;
}
.widget-head .heading {
    font-family: "Open Sans",Helvetica,Arial,sans-serif !important;
}
.list-group .list-group-item {
    padding-left: 45px;
}
.list-group .list-group-item.active, .list-group .list-group-item.active:hover {
    background: #dff3f8;
    color: #0092c8 !important;
    border: 0;
    border-left: 5px solid #4ac7e0;
}
.list-group img {
    position: absolute;
    left: 15px;
    top: 10px;
}
#combinations-list th {
    background: #e0ebf1;
    border-bottom: 1px solid #c5d7e4;
}
.panel .heading {
    font-family: "Open Sans",Helvetica,Arial,sans-serif !important;
}
#combinations-list tbody>tr:nth-child(even)>td {
    background-color: #f4f8fb;
}
#combinations-list tbody>tr:nth-child(odd)>td {
    background-color: #fbfcfd;
}
</style>               
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
    * @author    knowband.com <support@knowband.com>
    * @copyright 2015 Knowband
    * @license   see file: LICENSE.txt
    *
    * Description
    *
    * Admin tpl file
    *}

