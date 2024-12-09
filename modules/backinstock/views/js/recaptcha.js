/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
*/


$(document).ready(function(){
    $('#configuration_form_submit_btn').on('click', function(){
        $('.error_message').remove();
        $('*').removeClass('error_field');
        var error= false;
        if ($("input[name='KB_BACKINSTOCK_RECAPTCHA_ENABLE']:checked").val() == 1) {
            var kb_key = velovalidation.checkMandatory($("#KB_BACKINSTOCK_RECAPTCHA_SITE_KEY"));
            if(kb_key != true) {
                error = true;
                $('input[name="KB_BACKINSTOCK_RECAPTCHA_SITE_KEY"]').addClass('error_field');
                $('input[name="KB_BACKINSTOCK_RECAPTCHA_SITE_KEY"]').after("<span class='error_message'>" + empty_feild + "</span>");
            }
            var kb_secret = velovalidation.checkMandatory($("#KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY"));
            if(kb_secret != true) {
                error = true;
                $('input[name="KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY"]').addClass('error_field');
                $('input[name="KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY"]').after('<span class="error_message">' + empty_feild + '</span>');
            }
        }

        if(error == true) {
            event.preventDefault();
        } else {
            $("#configuration_form").submit();
        }
    });
})