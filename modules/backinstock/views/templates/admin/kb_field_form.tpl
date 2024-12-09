<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel form-horizontal kb_custom_field_type">
            </div>
            <div class="kb_custom_field_form">
                {$kb_form_contents}{*Variable contains URL content, escape not required*}
            </div>
  
        </div>
    </div>
</div>    
<script>
    edit_field_form = {$edit_field_form|escape:'htmlall':'UTF-8'};
    var kb_numeric = "{l s='Field should be numeric.' mod='backinstock'}";
    var kb_positive = "{l s='Field should be positive.' mod='backinstock'}";
    var check_for_all = "{l s='Kindly check for all languages.' mod='backinstock'}";
    var no_select = "{l s='Please select placement' mod='backinstock'}";
    var empty_field = "{l s='Field cannot be empty' mod='backinstock'}";
    var kb_numeric = "{l s='Field should be numeric.' mod='backinstock'}";
    var kb_positive = "{l s='Field should be positive.' mod='backinstock'}";
    var maximum_length_excced = "{l s='Maximum length should be greater than minimum length.' mod='backinstock'}";
    var kb_max_limit_exceed = "{l s='Maximum length should be less than 50' mod='backinstock'}";
    velovalidation.setErrorLanguage({
        alphanumeric: "{l s='Field should be alphanumeric.' mod='backinstock'}",
        digit_pass: "{l s='Password should contain atleast 1 digit.' mod='backinstock'}",
        empty_field: "{l s='Field cannot be empty.' mod='backinstock'}",
        number_field: "{l s='You can enter only numbers.' mod='backinstock'}",            
        positive_number: "{l s='Number should be greater than 0.' mod='backinstock'}",
        maxchar_field: "{l s='Field cannot be greater than # characters.' mod='backinstock'}",
        minchar_field: "{l s='Field cannot be less than # character(s).' mod='backinstock'}",
        invalid_date: "{l s='Invalid date format.' mod='backinstock'}",
        valid_amount: "{l s='Field should be numeric.' mod='backinstock'}",
        valid_decimal: "{l s='Field can have only upto two decimal values.' mod='backinstock'}",
        maxchar_size: "{l s='Size cannot be greater than # characters.' mod='backinstock'}",
            specialchar_size: "{l s='Size should not have special characters.' mod='backinstock'}",
            maxchar_bar: "{l s='Barcode cannot be greater than # characters.' mod='backinstock'}",
            positive_amount: "{l s='Field should be positive.' mod='backinstock'}",
            maxchar_color: "{l s='Color could not be greater than # characters.' mod='backinstock'}",
            invalid_color: "{l s='Color is not valid.' mod='backinstock'}",
            specialchar: "{l s='Special characters are not allowed.' mod='backinstock'}",
            script: "{l s='Script tags are not allowed.' mod='backinstock'}",
            style: "{l s='Style tags are not allowed.' mod='backinstock'}",
            iframe: "{l s='Iframe tags are not allowed.' mod='backinstock'}",
            image_size: "{l s='Uploaded file size must be less than #.' mod='backinstock'}",
            html_tags: "{l s='Field should not contain HTML tags.' mod='backinstock'}",
            number_pos: "{l s='You can enter only positive numbers.' mod='backinstock'}",
        });
</script>
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
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Admin tpl file
*}