/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @category  PrestaShop Module
 * @author    velsof.com <support@velsof.com>
 * @copyright 2014 Velocity Software Solutions Pvt Ltd
 * @license   see file: LICENSE.txt
 */

$(document).ajaxComplete(function() {
    $(".mColorPickerInput").each(function () {
        $(this).parent().parent().parent().removeClass("col-lg-2");
        $(this).parent().parent().parent().addClass("col-lg-3");
    });
},5000);
var times;
var selected_lang_initial;
var selected_lang_final;
var times;
var times_stats;
$(document).ready(function () {
    
    $('#availability_form').addClass('col-lg-10 col-md-9');
    $('#statistics_form').addClass('col-lg-10 col-md-9');
    $('#cron_details').parent().removeClass('col-lg-8 col-lg-offset-3');
    $('.cron_url').html(front_cron_url+'cron=send_emails');
    $('.cron_url_via_ssh').html("5 * * * * curl -O /dev/null "+front_cron_url+'cron=send_emails');
    $(".mColorPickerInput").each(function () {
        $(this).parent().parent().parent().removeClass("col-lg-2");
        $(this).parent().parent().parent().addClass("col-lg-3");
    });
    $('#delete_product_row').on('click', function () {
        
    });
    
    if (typeof kb_placeholder_text != 'undefined') {
        $("#multiple-select-specific_products_initial").chosen({
            placeholder_text: kb_placeholder_text,
            no_results_text: no_results_text,
        });
        $("#multiple-select-specific_products_final").chosen({
            placeholder_text: kb_placeholder_text,
            no_results_text: no_results_text,
        });
        $("#multiple-select-specific_products_low_stock").chosen({
            placeholder_text: kb_placeholder_text,
            no_results_text: no_results_text,
        });
    }
    
    $('.product_update_block_back').appendTo('#add-to-cart-or-refresh');
    $('[name="back_stock_email[SendinBlue_list]"]').css('float', 'left');
    $('<img style="width:40px;height:40px;display:none" src="' + path + 'views/img/show_loader.gif" id="show_loader_list"/>').appendTo($('[name="back_stock_email[SendinBlue_list]"]').parent());

    $('[name="back_stock_email[mailchimp_list]"]').css('float', 'left');
    $('<img style="width:40px;height:40px;display:none" src="' + path + 'views/img/show_loader.gif" id="show_loader_list_mailchimp"/>').appendTo($('[name="back_stock_email[mailchimp_list]"]').parent());

    $('[name="back_stock_email[klaviyo_list]"]').css('float', 'left');
    $('<img style="width:40px;height:40px;display:none" src="' + path + 'views/img/show_loader.gif" id="show_loader_list_klaviyo"/>').appendTo($('[name="back_stock_email[klaviyo_list]"]').parent());

    if ($('[name="product_update[enable_gdpr_policy]"]:checked').val() == '1') {
        $("[name='product_update_gdpr_policy_url_1']").parents('.form-group ').show();
        $("[name='product_update_gdpr_policy_text_1']").parents('.form-group ').show();
    } else {
        $("[name='product_update_gdpr_policy_url_1']").parents('.form-group ').hide();
        $("[name='product_update_gdpr_policy_text_1']").parents('.form-group ').hide();
    }
    $('[name="product_update[enable_gdpr_policy]"]').on('change', function () {// alert('hi');
        if ($('[name="product_update[enable_gdpr_policy]"]:checked').val() == '1') {
            $("[name='product_update_gdpr_policy_url_1']").parents('.form-group ').show();
            $("[name='product_update_gdpr_policy_text_1']").parents('.form-group ').show();
        } else {
            $("[name='product_update_gdpr_policy_url_1']").parents('.form-group ').hide();
            $("[name='product_update_gdpr_policy_text_1']").parents('.form-group ').hide();
        }
    });
    if ($('[name="product_update[enable_subscription_list]"]:checked').val() == '1') {
        $("[name='product_update[subscription_per_page]']").parents('.form-group ').show();
        $("[name='product_update[enable_remove_subscription]']").parents('.form-group ').show();
    } else {
        $("[name='product_update[subscription_per_page]']").parents('.form-group ').hide();
        $("[name='product_update[enable_remove_subscription]']").parents('.form-group ').hide();
    }

    if ($('[name="product_update[enable_low_stock_alert]"]:checked').val() == '1') {
        $("[name='product_update[low_stock_alert_quantity]']").parents('.form-group ').show();
    } else {
        $("[name='product_update[low_stock_alert_quantity]']").parents('.form-group ').hide();
    }
    $('[name="product_update[enable_subscription_list]"]').on('change', function () {// alert('hi');
        if ($('[name="product_update[enable_subscription_list]"]:checked').val() == '1') {
            $("[name='product_update[subscription_per_page]']").parents('.form-group ').show();
            $("[name='product_update[enable_remove_subscription]']").parents('.form-group ').show();
        } else {
            $("[name='product_update[subscription_per_page]']").parents('.form-group ').hide();
            $("[name='product_update[enable_remove_subscription]']").parents('.form-group ').hide();
        }
    });
    $('[name="product_update[enable_low_stock_alert]"]').on('change', function () {// alert('hi');
        if ($('[name="product_update[enable_low_stock_alert]"]:checked').val() == '1') {
            $("[name='product_update[low_stock_alert_quantity]']").parents('.form-group ').show();
        } else {
            $("[name='product_update[low_stock_alert_quantity]']").parents('.form-group ').hide();
        }
    });
    
    //changes by vishal for adding related product functionality
    if ($('[name="product_update[enable_utm]"]:checked').val() == '1') {
        $("[name='product_update[product_update_utm_source]']").parents('.form-group ').show();
        $("[name='product_update[product_update_utm_medium]']").parents('.form-group ').show();
        $("[name='product_update[product_update_utm_campaign]']").parents('.form-group ').show();
    } else {
        $("[name='product_update[product_update_utm_source]']").parents('.form-group ').hide();
        $("[name='product_update[product_update_utm_medium]']").parents('.form-group ').hide();
        $("[name='product_update[product_update_utm_campaign]']").parents('.form-group ').hide();
    }
    $('[name="product_update[enable_utm]"]').on('change', function () {// alert('hi');
        if ($('[name="product_update[enable_utm]"]:checked').val() == '1') {
            $("[name='product_update[product_update_utm_source]']").parents('.form-group ').show();
            $("[name='product_update[product_update_utm_medium]']").parents('.form-group ').show();
            $("[name='product_update[product_update_utm_campaign]']").parents('.form-group ').show();
        } else {
            $("[name='product_update[product_update_utm_source]']").parents('.form-group ').hide();
            $("[name='product_update[product_update_utm_medium]']").parents('.form-group ').hide();
            $("[name='product_update[product_update_utm_campaign]']").parents('.form-group ').hide();
        }
    });    
    $('[name="product_update[enable_related_product_initial]"]').on('change', function () {// alert('hi');
        if ($('[name="product_update[enable_related_product_initial]"]:checked').val() == '1') {
            $("[name='product_update[related_product_method_initial]']").parents('.form-group ').show();
            $("[name='product_update[related_product_method_initial]']").parents('.form-group ').prev().show();
            if ($('[name="product_update[related_product_method_initial]"]').val() == '3') {
                $("[name='product_update[specific_products_initial][]']").parents('.form-group ').show();
            }
        } else {
            $("[name='product_update[related_product_method_initial]']").parents('.form-group ').hide();
            $("[name='product_update[specific_products_initial][]']").parents('.form-group ').hide();
            $("[name='product_update[related_product_method_initial]']").parents('.form-group ').prev().hide();
        }
    });
    if ($('[name="product_update[enable_related_product_initial]"]:checked').val() == '1') {
            $("[name='product_update[related_product_method_initial]']").parents('.form-group ').show();
            $("[name='product_update[related_product_method_initial]']").parents('.form-group ').prev().show();
            if ($('[name="product_update[related_product_method_initial]"]').val() == '3') {
                $("[name='product_update[specific_products_initial][]']").parents('.form-group ').show();
            }
        } else {
            $("[name='product_update[related_product_method_initial]']").parents('.form-group ').hide();
            $("[name='product_update[related_product_method_initial]']").parents('.form-group ').prev().hide();
            $("[name='product_update[specific_products_initial][]']").parents('.form-group ').hide();
        }
    $('[name="product_update[enable_related_product_final]"]').on('change', function () {// alert('hi');
        if ($('[name="product_update[enable_related_product_final]"]:checked').val() == '1') {
            $("[name='product_update[related_product_method_final]']").parents('.form-group ').show();
            $("[name='product_update[related_product_method_final]']").parents('.form-group ').prev().show();
            if ($('[name="product_update[related_product_method_final]"]').val() == '3') {
                $("[name='product_update[specific_products_final][]']").parents('.form-group ').show();
            }
        } else {
            $("[name='product_update[related_product_method_final]']").parents('.form-group ').hide();
            $("[name='product_update[related_product_method_final]']").parents('.form-group ').prev().hide();
            $("[name='product_update[specific_products_final][]']").parents('.form-group ').hide();
        }
    });
    if ($('[name="product_update[enable_related_product_final]"]:checked').val() == '1') {
            $("[name='product_update[related_product_method_final]']").parents('.form-group ').show();
            $("[name='product_update[related_product_method_final]']").parents('.form-group ').prev().show();
            if ($('[name="product_update[related_product_method_final]"]').val() == '3') {
                $("[name='product_update[specific_products_final][]']").parents('.form-group ').show();
            }
        } else {
            $("[name='product_update[related_product_method_final]']").parents('.form-group ').prev().hide();
            $("[name='product_update[related_product_method_final]']").parents('.form-group ').hide();
            $("[name='product_update[specific_products_final][]']").parents('.form-group ').hide();
        }
    $('[name="product_update[enable_related_product_low_stock]"]').on('change', function () {// alert('hi');
        if ($('[name="product_update[enable_related_product_low_stock]"]:checked').val() == '1') {
            $("[name='product_update[related_product_method_low_stock]']").parents('.form-group ').show();
            $("[name='product_update[related_product_method_low_stock]']").parents('.form-group ').prev().show();
            if ($('[name="product_update[related_product_method_low_stock]"]').val() == '3') {
                $("[name='product_update[specific_products_low_stock][]']").parents('.form-group ').show();
            }
        } else {
            $("[name='product_update[related_product_method_low_stock]']").parents('.form-group ').prev().hide();
            $("[name='product_update[related_product_method_low_stock]']").parents('.form-group ').hide();
            $("[name='product_update[specific_products_low_stock][]']").parents('.form-group ').hide();
        }
    });
    if ($('[name="product_update[enable_related_product_low_stock]"]:checked').val() == '1') {
            $("[name='product_update[related_product_method_low_stock]']").parents('.form-group ').show();
            $("[name='product_update[related_product_method_low_stock]']").parents('.form-group ').prev().show();
            if ($('[name="product_update[related_product_method_low_stock]"]').val() == '3') {
                $("[name='product_update[specific_products_low_stock][]']").parents('.form-group ').show();
            }
        } else {
            $("[name='product_update[related_product_method_low_stock]']").parents('.form-group ').prev().hide();
            $("[name='product_update[related_product_method_low_stock]']").parents('.form-group ').hide();
            $("[name='product_update[specific_products_low_stock][]']").parents('.form-group ').hide();
        }
    $('[name="product_update[related_product_method_initial]"]').on('change', function () {// alert('hi');
        if ($('[name="product_update[related_product_method_initial]"]').val() == '3') {
            $("[name='product_update[specific_products_initial][]']").parents('.form-group ').show();
        } else {
            $("[name='product_update[specific_products_initial][]']").parents('.form-group ').hide();
        }
    });
    if ($('[name="product_update[related_product_method_initial]"]').val() == '3') {
            $("[name='product_update[specific_products_initial][]']").parents('.form-group ').show();
        } else {
            $("[name='product_update[specific_products_initial][]']").parents('.form-group ').hide();
        }
    $('[name="product_update[related_product_method_final]"]').on('change', function () {// alert('hi');
        if ($('[name="product_update[related_product_method_final]"]').val() == '3') {
            $("[name='product_update[specific_products_final][]']").parents('.form-group ').show();
        } else {
            $("[name='product_update[specific_products_final][]']").parents('.form-group ').hide();
        }
    });
    if ($('[name="product_update[related_product_method_final]"]').val() == '3') {
            $("[name='product_update[specific_products_final][]']").parents('.form-group ').show();
        } else {
            $("[name='product_update[specific_products_final][]']").parents('.form-group ').hide();
        }
    $('[name="product_update[related_product_method_low_stock]"]').on('change', function () {// alert('hi');
        if ($('[name="product_update[related_product_method_low_stock]"]').val() == '3') {
            $("[name='product_update[specific_products_low_stock][]']").parents('.form-group ').show();
        } else {
            $("[name='product_update[specific_products_low_stock][]']").parents('.form-group ').hide();
        }
    });
    if ($('[name="product_update[related_product_method_low_stock]"]').val() == '3') {
            $("[name='product_update[specific_products_low_stock][]']").parents('.form-group ').show();
        } else {
            $("[name='product_update[specific_products_low_stock][]']").parents('.form-group ').hide();
        }
    //changes end
    
    if ($('[id^="back_stock_email[mailchimp_status]_on"]').is(':checked') === true) {//alert('hi');
        $("[name='back_stock_email[mailchimp_api]']").parents('.form-group').show();
        $("[name='back_stock_email[mailchimp_list]']").parents('.form-group').show();
    } else {//
        $("[name='back_stock_email[mailchimp_api]']").parents('.form-group').hide();
        $("[name='back_stock_email[mailchimp_list]']").parents('.form-group').hide();
    }

    if ($('[id^="back_stock_email[klaviyo_status]_on"]').is(':checked') === true) {//alert('hi');
        $("[name='back_stock_email[klaviyo_api]']").parents('.form-group').show();
        $("[name='back_stock_email[klaviyo_list]']").parents('.form-group').show();
    } else {//
        $("[name='back_stock_email[klaviyo_api]']").parents('.form-group').hide();
        $("[name='back_stock_email[klaviyo_list]']").parents('.form-group').hide();
    }

    if ($('[id^="back_stock_email[SendinBlue_status]_on"]').is(':checked') === true) {//alert('hi');
        $("[name='back_stock_email[SendinBlue_api]']").parents('.form-group').show();
        $("[name='back_stock_email[SendinBlue_list]']").parents('.form-group').show();
    } else {//
        $("[name='back_stock_email[SendinBlue_api]']").parents('.form-group').hide();
        $("[name='back_stock_email[SendinBlue_list]']").parents('.form-group').hide();
    }

    $('[name="back_stock_email[SendinBlue_status]"]').on('change', function () {// alert('hi');
        if ($(this).val() == '1') {
            $("[name='back_stock_email[SendinBlue_api]']").parents('.form-group').show();
            $("[name='back_stock_email[SendinBlue_list]']").parents('.form-group').show();
        } else {//
            $("[name='back_stock_email[SendinBlue_api]']").parents('.form-group').hide();
            $("[name='back_stock_email[SendinBlue_list]']").parents('.form-group').hide();
        }
    });

    $('[name="back_stock_email[klaviyo_status]"]').click(function () {// alert('hi');
        if ($(this).val() == '1') {//alert('hi');
            $("[name='back_stock_email[klaviyo_api]']").parents('.form-group').show();
            $("[name='back_stock_email[klaviyo_list]']").parents('.form-group').show();
        } else {//
            $("[name='back_stock_email[klaviyo_api]']").parents('.form-group').hide();
            $("[name='back_stock_email[klaviyo_list]']").parents('.form-group').hide();
        }
    });
    $('[name="back_stock_email[mailchimp_status]"]').click(function () {// alert('hi');
        if ($(this).val() == '1') {
            $("[name='back_stock_email[mailchimp_api]']").parents('.form-group').show();
            $("[name='back_stock_email[mailchimp_list]']").parents('.form-group').show();
        } else {//
            $("[name='back_stock_email[mailchimp_api]']").parents('.form-group').hide();
            $("[name='back_stock_email[mailchimp_list]']").parents('.form-group').hide();
        }
    });
    $('[name="back_stock_email[back_stock_email[]"]').on('change', function () {// alert('hi');
        if ($(this).val() == '1') {
            $("[name='back_stock_email[SendinBlue_api]']").parents('.form-group').show();
            $("[name='back_stock_email[SendinBlue_list]']").parents('.form-group').show();
        } else {//
            $("[name='back_stock_email[SendinBlue_api]']").parents('.form-group').hide();
            $("[name='back_stock_email[SendinBlue_list]']").parents('.form-group').hide();
        }
    });
    if (email_marketing_values['mailchimp_status'] == 1) {
        $('.spin_error').remove();
        var mailchimphtml = '';
        if (email_marketing_values['mailchimp_api'] != '') {
            $.ajax({
                url: module_path,
                type: 'post',
                data: 'ajax=true&method=mailchimpgetlist&api_key=' + email_marketing_values['mailchimp_api'],
                dataType: 'json',
                beforeSend: function () {
                    $('#show_loader_list_mailchimp').show();
                },
                success: function (json) {
                    if (json['error'] !== undefined) {
                        $("[name='back_stock_email[mailchimp_list]']").html('<option value="no_list">' + json['error'][0]['label'] + '</option>');
                        $("[name='back_stock_email[mailchimp_list]']").css('border', '1px solid #ff0000');
                    }
                    else {
                        mailchimphtml += '<select name="back_stock_email[mailchimp_list]"';
                        mailchimphtml += 'id="back_stock_email[mailchimp_list]">';
                        for (i in json['success'])
                        {
                            if (email_marketing_values['mailchimp_list'] == json['success'][i]['value']) {
                                mailchimphtml += '<option value="' + json['success'][i]['value'] + '" selected>' + json['success'][i]['label'] + '</option>';
                            }
                            else {
                                mailchimphtml += '<option value="' + json['success'][i]['value'] + '">' + json['success'][i]['label'] + '</option>';
                            }
                        }
                        mailchimphtml += '</select>';
                        $("[name='back_stock_email[mailchimp_list]']").html(mailchimphtml);
                        $("[name='back_stock_email[mailchimp_list]']").css('border', '');
                    }
                },
                complete: function () {
                    $('#show_loader_list_mailchimp').hide();
                },
            });
        }
    }

    if (email_marketing_values['SendinBlue_status'] == 1) {
        $('.spin_error').remove();
        var lists_html = '';
        if (email_marketing_values['SendinBlue_api'] != '') {
            $.ajax({
                url: module_path,
                type: 'post',
                data: 'ajax=true&method=getSendinblueList&api_key=' + email_marketing_values['SendinBlue_api'],
                dataType: 'json',
                beforeSend: function () {
                    $('#show_loader_list').show();
                },
                success: function (json) {
                    if (json == '') {
                        $("[name='back_stock_email[SendinBlue_list]']").html('<option value="no_list">' + no_list_found + '</option>');
                        $("[name='back_stock_email[SendinBlue_list]']").css('border', '1px solid #ff0000');
                    }
                    else {
                        $.each(json, function (key, value) {
                            $.each(value, function (key, list) {
                                if (email_marketing_values['SendinBlue_list'] == list.id) {
                                    lists_html += "<option selected='' value='" + (list.id) + "'>" + (list.name) + "</option>";
                                } else {
                                    lists_html += "<option value='" + (list.id) + "'>" + (list.name) + "</option>";
                                }
                            });

                        });
                        $("[name='back_stock_email[SendinBlue_list]']").html(lists_html);
                        $("[name='back_stock_email[SendinBlue_list]']").css('border', '');
                    }
                },
                complete: function () {
                    $('#show_loader_list').hide();
                },
            });
        }
    }

    if (email_marketing_values['klaviyo_status'] == 1) {
        $('.spin_error').remove();
        var api_key = email_marketing_values['klaviyo_api'];
        var listid = email_marketing_values['klaviyo_list'];
        var klaviyohtml = '';
        if (api_key != '') {
            $.ajax({
                url: module_path,
                type: 'post',
                data: 'ajax=true&method=klaviyogetlist&api_key=' + api_key,
                dataType: 'json',
                beforeSend: function () {
                    $('#show_loader_list_klaviyo').show();
                },
                success: function (json) {
                    if (json['error'] !== undefined) {
                        $("[name='back_stock_email[klaviyo_list]']").html('<option value="no_list">' + json['error'][0]['label'] + '</option>');
                        $("[name='back_stock_email[klaviyo_list]']").css('border', '1px solid #ff0000');
                    }
                    else {
                        klaviyohtml += '<select name="back_stock_email[klaviyo_list]"';

                        klaviyohtml += 'id="klaviyo_selectlist">';

                        for (i in json['success'])
                        {
                            if (listid == json['success'][i]['value'])
                                klaviyohtml += '<option value="' + json['success'][i]['value'] + '" selected>' + json['success'][i]['label'] + '</option>';
                            else
                                klaviyohtml += '<option value="' + json['success'][i]['value'] + '">' + json['success'][i]['label'] + '</option>';
                        }
                        klaviyohtml += '</select>';
                        $("[name='back_stock_email[klaviyo_list]']").html(klaviyohtml);
                        $("[name='back_stock_email[klaviyo_list]']").css('border', '');
                    }
                },
                complete: function () {
                    $('#show_loader_list_klaviyo').hide();
                },
            });
        }
    }

    $('#general_form_submit_btn').click(function () {
//        event.prevent
        if (validation_admin() == false) {
            return false;
        }
        /*Knowband button validation start*/
        $('#general_form_submit_btn').attr('disabled', 'disabled');
        $('#general_form').submit();
        //$('button[name="submitPDPConfiguration"]').submit();
        /*Knowband button validation end*/
    });
    //start by dharmanshu 19-08-2021 for recaptcha
    $('#recaptcha_form_submit_btn_6').click(function () {
        var error =  false;
        $('.error_message').remove();
        $('input[name="KB_BACKINSTOCK_RECAPTCHA_SITE_KEY"]').removeClass('error_field')
        $('input[name="KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY"]').removeClass('error_field')
         var site_key_racptcha = velovalidation.checkMandatory($('input[name="KB_BACKINSTOCK_RECAPTCHA_SITE_KEY"]'));
        if (site_key_racptcha != true)
        {
            error = true;
            $('input[name="KB_BACKINSTOCK_RECAPTCHA_SITE_KEY"]').addClass('error_field');
            $('input[name="KB_BACKINSTOCK_RECAPTCHA_SITE_KEY"]').parent().after('<span class="error_message" style="position: relative;left: 233px;bottom: 5px;">' + empty_field + '</span>');
        }
         var secret_key_racptcha = velovalidation.checkMandatory($('input[name="KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY"]'));
        if (secret_key_racptcha != true)
        {
            error = true;
            $('input[name="KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY"]').addClass('error_field');
            $('input[name="KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY"]').parent().after('<span class="error_message" style="position: relative;left: 233px;bottom: 5px;">' + empty_field + '</span>');
        }
        
        if(error){
         return false;
        }else{
               $('#recaptcha_form_submit_btn_6').attr('disabled', 'disabled');
               $('form[id="recaptcha_form"]').submit();
        }        
    });
    //end by dharmanshu 19-08-2021 for recaptcha
    $('.form_email_marketing').click(function () {
//        event.prevent
        if (validation_email_marketing_admin() == false) {
            return false;
        }
        /*Knowband button validation start*/
        $('.form_email_marketing').attr('disabled', 'disabled');
        $('#email_marketing_form').submit();
        //$('button[name="submitPDPConfiguration"]').submit();
        /*Knowband button validation end*/
    });
    // Changes by prvind for saving the availability settings form
    $('#availability_form_submit_btn_7').click(function () {
        $('#availability_form').submit();
    });
    // Changes Over
    $('.form_initial').click(function () {
//        event.prevent
        if (validation_email_initial_settings() == false) {
            return false;
        }
        /*Knowband button validation start*/
        $('.form_initial').attr('disabled', 'disabled');

        saveEmailProductTemplate(action_product_update);
        //$('#initial_form').submit();
        /*Knowband button validation end*/
    });
    $('.form_low_stock').click(function () {
//        event.prevent
        if (validation_email_low_stock_settings() == false) {
            return false;
        }
        /*Knowband button validation start*/
        $('.form_low_stock').attr('disabled', 'disabled');
        $('#low_stock_alert_form').submit();
        /*Knowband button validation end*/
    });
    $('.form_final').click(function () {
//        event.prevent
        if (validation_email_final_settings() == false) {
            return false;
        }
        /*Knowband button validation start*/
        $('.form_final').attr('disabled', 'disabled');
        //$('#final_form').submit();
        saveEmailProductTemplateFinal(action_product_update);
        /*Knowband button validation end*/
    });
    
    // Changes by prvind panday for availability settings auto complete
        
        $('input[name="availability_form[product_name]"]').autocomplete(path_fold + 'ajax_products_list.php', {
            delay: 10,
            minChars: 1,
            autoFill: true,
            max: 20,
            matchContains: true,
            mustMatch: true,
            scroll: false,
            cacheLength: 0,
            // param multipleSeparator:'||' ajouté à cause de bug dans lib autocomplete
            multipleSeparator: '||',
            formatItem: function (item) {
                return item[1] + ' - ' + item[0];
            },
            extraParams: {
                excludeIds: function () {
                    var selected_pro = $('input[name="availability_form[excluded_products_hidden]"]').val();
                    return selected_pro.replace(/\-/g, ',');
                },
                excludeVirtuals: 0,
                exclude_packs: 0
            }
        }).result(function (event, item, formatted) {
            addProductToExclude(item);
            event.stopPropagation();
        });
        
    function addProductToExclude(data) {
        if (data == null)
            return false;

        var productId = data[1];
        var productName = data[0];
        var $divAccessories = $('#kb_excluded_product_holder');
        var delButtonClass = 'delExcludedProduct';

        var current_excluded_pro = $('input[name="availability_form[excluded_products_hidden]"]').val();
        if (current_excluded_pro != '') {
            var prod_arr_exclude = current_excluded_pro.split(",");
            if ($.inArray(productId, prod_arr_exclude) != '-1') {
                return false;
            }
        }

        $divAccessories.html($divAccessories.html() + '<div class="form-control-static"><button type="button" id="delete_product_row" class="' + delButtonClass + ' btn btn-default" name="' + productId + '"><i class="icon-remove text-danger"></i></button>&nbsp;' + productName + '</div>');

        $('input[name="availability_form[product_name]"]').val('');

        if (current_excluded_pro != '') {
            $('input[name="availability_form[excluded_products_hidden]"]').val(current_excluded_pro + ',' + productId);
        } else {
            $('input[name="availability_form[excluded_products_hidden]"]').val(productId);
        }
    }
    
    function deleteSelectedProduct(productId, current) {
        $('input[name="availability_form[excluded_products_hidden]"]').val(removeIdFromCommaString($('input[name="availability_form[excluded_products_hidden]"]').val(), productId, ','));
        $('input[name="availability_form[product_name]"]').val('');
        $(current).parent().remove();
    }
    
    function removeIdFromCommaString(list, value, separator) {
        separator = separator || ",";
        var values = list.split(separator);
        for (var i = 0; i < values.length; i++) {
            if (values[i] == value) {
                values.splice(i, 1);
                return values.join(separator);
            }
        }
        return list;
    }
        
    // Changes over


    $('#analysis_form .panel').append($('#list_graph'));
    $('#statistics_form .panel').append($('#list_graph_stats'));
    if (version == 1.5) {
//        $('#velsof_template_content').addClass('rte autoload_rte col-lg-9');
        $('#analysis_form fieldset').append($('#list_graph'));
        $('#statistics_form fieldset').append($('#list_graph_stats'));
        $("[name='velocity_email_template[template_id]']").closest('.margin-form').hide();
        $("[name='velocity_email_template[template_id_final]']").closest('.margin-form').hide();
        $("[name='velocity_email_template[subject]']").closest('.margin-form').hide();
        $("[name='velocity_email_template[content]']").closest('.margin-form').hide();
        $("[name='velocity_email_template[subject]']").closest('.margin-form').prev('label').hide();
        $("[name='velocity_email_template[content]']").closest('.margin-form').prev('label').hide();
        $("[name='velocity_email_template[subject_final]']").closest('.margin-form').hide();
        $("[name='velocity_email_template[content_drop]']").closest('.margin-form').hide();
        $("[name='velocity_email_template[subject_final]']").closest('.margin-form').prev('label').hide();
        $("[name='velocity_email_template[content_drop]']").closest('.margin-form').prev('label').hide();
    }
    $("[name='velocity_email_template[template_id]']").closest('.form-group').hide();
    $("[name='velocity_email_template[template_id_final]']").closest('.form-group').hide();

    $("[name='velocity_low_stock_alert_setting[template_id]']").closest('.form-group').hide();

    $("[name='velocity_email_template[subject]']").closest('.form-group').hide();
    $("[name='velocity_email_template[content]']").closest('.form-group').hide();

    $("[name='velocity_low_stock_alert_setting[subject]']").closest('.form-group').hide();
    $("[name='velocity_low_stock_alert_setting[content]']").closest('.form-group').hide();

    $("[name='velocity_email_template[subject_final]']").closest('.form-group').hide();
    $("[name='velocity_email_template[content_drop]']").closest('.form-group').hide();
    $('.form_initial').hide();
    $('.form_final').hide();
    $('#general_form').addClass('col-lg-10 col-md-9');
    $('#initial_form').addClass('col-lg-10 col-md-9');
    $('#final_form').addClass('col-lg-10 col-md-9');
    $('#analysis_form').addClass('col-lg-10 col-md-9');
    $('#low_stock_alert_form').addClass('col-lg-10 col-md-9');
    $('#subscriber_list').addClass('col-lg-10 col-md-9');
    $('#email_marketing_form').addClass('col-lg-10 col-md-9');
    $('#general_form').show();
    //start by dharmanshu for the recaptcha form
    $('#recaptcha_form').hide();
    $('#recaptcha_form').addClass('col-lg-10 col-md-9')
    //end by dharmanshu for the recaptcha form
    $('#availability_form').hide();
    $('#statistics_form').hide();
//    $('<div class="panel_spin_wheel">MailChimp</div>').insertBefore($("[name='back_stock_email[mailchimp_status]']").parents('.form-group'));
//    $('<div class="panel_spin_wheel">Klaviyo</div>').insertBefore($("[name='back_stock_email[klaviyo_status]']").parents('.form-group'));
//    $('<div class="panel_spin_wheel">SendinBlue</div>').insertBefore($("[name='back_stock_email[back_stock_email[]']").parents('.form-group'));

    $('#initial_form').hide();
    $('#final_form').hide();
    $('#email_marketing_form').hide();
    $('#low_stock_alert_form').hide();
//    $('#subscriber_list').hide();
    $('#analysis_form').hide();
    times = 0;
    times_stats = 0;
    $('#slide1_controls').on('click', function () {
        $('.velsof-adv-panel').removeAttr('style');
        $('#slide1_controls').hide();
        $('#slide2_controls').show();
        setTimeout("$('.buttonsidebar').css('-webkit-transform','rotate(0deg)')", 900);
        setTimeout("$('.buttonsidebar').css('ms-transform','rotate(0deg)')", 900);
        setTimeout("$('.buttonsidebar').css('-moz-transform','rotate(0deg)')", 900);
        setTimeout("$('.buttonsidebar').css('transform','rotate(0deg)')", 900);
    });

    $('#slide2_controls').on('click', function () {
        $('.velsof-adv-panel').attr('style', 'right:-10px;');
        $('#slide1_controls').show();
        $('#slide2_controls').hide();
        setTimeout("$('.buttonsidebar').css('-webkit-transform','rotate(180deg)')", 900);
        setTimeout("$('.buttonsidebar').css('ms-transform','rotate(180deg)')", 900);
        setTimeout("$('.buttonsidebar').css('-moz-transform','rotate(180deg)')", 900);
        setTimeout("$('.buttonsidebar').css('transform','rotate(180deg)')", 900);
    });
    if ($('.color-input').length) {

        $('.color-input').colpick({
            layout: 'hex',
            submit: 0,
            colorScheme: 'light',
            onBeforeShow: function (hsb, hex, rgb, el, bySetColor) {
                $(this).colpickSetColor($(this).attr('value'));
            },
            onChange: function (hsb, hex, rgb, el, bySetColor) {
                $(el).css('background-color', '#' + hex);
                $(el).attr('value', '#' + hex);
                if ($(el).attr('name') == 'kb_sfl_config[buy_color]') {
                }


                if (!bySetColor) {
                    //$(el).val(hex);
                }
            }
        }).keyup(function () {
            $(this).colpickSetColor(this.value);
        });
    }
    $('.initial_lang').bind('change', function () {
        if (version == 1.5) {
            $("[name='velocity_email_template[subject]']").closest('.margin-form').hide();
            $("[name='velocity_email_template[content]']").closest('.margin-form').hide();
            $("[name='velocity_email_template[subject]']").closest('.margin-form').prev('label').hide();
            $("[name='velocity_email_template[content]']").closest('.margin-form').prev('label').hide();
        }
        $("[name='velocity_email_template[subject]']").closest('.form-group').hide();
        $("[name='velocity_email_template[content]']").closest('.form-group').hide();
        $('.form_initial').hide();
        var selected_lang = $(this).val();
        selected_lang_initial = $(this).val();
        selected_temp = 1;
        if (selected_lang != 0)
        {
            $.ajax({
                type: "POST",
                url: action_product_update,
                data: 'ajax_rend=true&selected_lang=' + selected_lang + '&template_action=true&fetch_template=true+&selected_temp=' + selected_temp,
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (json) {
                    $("[name='velocity_email_template[subject]']").closest('.form-group').show();
                    $("[name='velocity_email_template[content]']").closest('.form-group').show();
                    $('.form_initial').show();
                    if (version == 1.5) {
                        $("[name='velocity_email_template[subject]']").closest('.margin-form').show();
                        $("[name='velocity_email_template[content]']").closest('.margin-form').show();
                        $("[name='velocity_email_template[subject]']").closest('.margin-form').prev('label').show();
                        $("[name='velocity_email_template[content]']").closest('.margin-form').prev('label').show();
//                    $('#velsof_template_content').val(json['body']);
//tinyMCE.EditorManager.editors = [];
//tinyMCE.EditorManager.execCommand('mceRemoveControl',true, "velsof_template_content");
//tinyMCE.EditorManager.execCommand('mceRemoveEditor',true, "velsof_template_content");


                        //tinyMCE.editors=[]; // remove any existing references
                        //$(".autoload_rte").val(json['body']);
                        //tinySetup({
                        //	editor_selector :"autoload_rte"
                        //});
                        $("#velsof_template_content").remove();
                        $(".mceEditor").remove();
                        $("#velsof_hidden_id").prev().prev().find("sup").before('<textarea name="velocity_email_template[content]" id="velsof_template_content" cols="9" rows="5" class="rte autoload_rte1 col-lg-9" aria-hidden="true"></textarea>');
                        $(".autoload_rte1").val(json['body']);

                        tinySetup({
                            editor_selector: "autoload_rte1"
                        });

                        // console.log(tinyMCE.get('velsof_template_content').setContent(json['body']));
                        //  tinyMCE.get('velsof_template_content').setContent(json['body']);
                    } else {
                        tinyMCE.get('velsof_template_content').setContent(json['body']);
                    }
                    $('#velsof_template_subject').val(json['subject']);
                    $('#hidden_template_id').val(json['id_template']);
                },
//            error:function(a,b,c){
//                alert(a.status);
//            }
            });
        }
    });

    $('.low_stock_lang').bind('change', function () {
        $("[name='velocity_low_stock_alert_setting[subject]']").closest('.form-group').hide();
        $("[name='velocity_low_stock_alert_setting[content]']").closest('.form-group').hide();
        $('.form_low_stock').hide();
        var selected_lang = $(this).val();
        selected_temp = 3;
        if (selected_lang != 0)
        {
            $.ajax({
                type: "POST",
                url: action_product_update,
                data: 'ajax_rend=true&selected_lang=' + selected_lang + '&template_action=true&fetch_template=true+&selected_temp=' + selected_temp,
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (json) {
                    $("[name='velocity_low_stock_alert_setting[subject]']").closest('.form-group').show();
                    $("[name='velocity_low_stock_alert_setting[content]']").closest('.form-group').show();
                    $('.form_low_stock').show();
                    console.log(tinyMCE.get('velocity_low_stock_alert_setting_template_content'));
                    tinyMCE.get('velocity_low_stock_alert_setting_template_content').setContent(json['body']);
                    $('#velocity_low_stock_alert_setting_subject').val(json['subject']);
                    $('#hidden_velocity_low_stock_alert_setting_template_id').val(json['id_template']);
                },
            });
        }
    });

    $('.final_lang').bind('change', function () {
        if (version == 1.5) {
            $("[name='velocity_email_template[subject_final]']").closest('.margin-form').hide();
            $("[name='velocity_email_template[content_drop]']").closest('.margin-form').hide();
            $("[name='velocity_email_template[subject_final]']").closest('.margin-form').prev('label').hide();
            $("[name='velocity_email_template[content_drop]']").closest('.margin-form').prev('label').hide();
        }
        $('.form_final').hide();
        $("[name='velocity_email_template[subject_final]']").closest('.form-group').hide();
        $("[name='velocity_email_template[content_drop]']").closest('.form-group').hide();
        var selected_lang = $(this).val();
        selected_lang_final = $(this).val();
        selected_temp = 2;
        if (selected_lang != 0)
        {
            $.ajax({
                type: "POST",
                url: action_product_update,
                data: 'ajax_rend=true&selected_lang=' + selected_lang + '&template_action=true&fetch_template=true+&selected_temp=' + selected_temp,
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (json) {
                    $("[name='velocity_email_template[subject_final]']").closest('.form-group').show();
                    $("[name='velocity_email_template[content_drop]']").closest('.form-group').show();
                    if (version == 1.5) {
                        $("[name='velocity_email_template[subject_final]']").closest('.margin-form').show();
                        $("[name='velocity_email_template[content_drop]']").closest('.margin-form').show();
                        $("[name='velocity_email_template[subject_final]']").closest('.margin-form').prev('label').show();
                        $("[name='velocity_email_template[content_drop]']").closest('.margin-form').prev('label').show();
                        $("#velsof_template_content_final").remove();
                        $(".mceEditor").remove();
                        $("#velsof_hidden_id_final").prev().prev().find("sup").before('<textarea name="velocity_email_template[content_drop]" id="velsof_template_content_final" cols="9" rows="5" class="rte autoload_rte1 col-lg-9" aria-hidden="true"></textarea>');
                        $(".autoload_rte1").val(json['body']);

                        tinySetup({
                            editor_selector: "autoload_rte1"
                        });
                    } else {
                        tinyMCE.get('velsof_template_content_final').setContent(json['body']);
                    }
                    $('.form_final').show();
                    $('#velsof_template_subject_final').val(json['subject']);
                    $('#hidden_template_id_final').val(json['id_template']);

                },
//            error:function(a,b,c){
//                alert(a.status);
//            }
            });
        }
    });

//        $('.form_initial').bind('click',function(){
//
////            return false;
//        saveEmailProductTemplate(action_product_update);
//
//        });
//        $('.form_final').bind('click',function(){
//        saveEmailProductTemplateFinal(action_product_update);
//        });
    $('.form_low_stock').bind('click', function () {
        saveEmailProductTemplateLowStock(action_product_update);
    });
    $('.popup_users').bind('click', function () {
        var attribute = $(this).attr('data');
        $.ajax({
            type: "POST",
            url: action_product_update,
            data: 'pop_up=true&attribute=' + attribute,
            beforeSend: function () {

            },
            success: function (x) {
                $('#popup_head_product_count').append(x);
                $('#popup_head_product_count').show();
                $('#dark_popup').show();
            }
        });
    });

    //SendinBLUE
    $("[name^='back_stock_email[SendinBlue_api]']").on('blur', function () {
        $('.error_message').remove();
        var sendinblue_api_key = $(this).val().trim();
        if (sendinblue_api_key != '') {
            $.ajax({
                url: module_path,
                type: 'post',
                data: 'ajax=true&method=getSendinblueList&api_key=' + sendinblue_api_key,
                dataType: 'json',
                beforeSend: function () {
                    $('#show_loader_list').show();
                },
                success: function (response) {
                    if (response != '') {
                        var lists_html = '';
                        $.each(response, function (key, value) {
                            $.each(value, function (key, list) {
                                lists_html += "<option value='" + (list.id) + "'>" + (list.name) + "</option>";
                            });

                        });
                        $("[name='back_stock_email[SendinBlue_list]']").html(lists_html);
                        $("[name='back_stock_email[SendinBlue_list]']").css('border', '');
                    } else {
                        $("[name='back_stock_email[SendinBlue_list]']").html('<option value="no_list">' + no_list_found + '</option>');
                        $("[name='back_stock_email[SendinBlue_list]']").css('border', '1px solid #ff0000');
                    }
                },
                complete: function () {
                    $('#show_loader_list').hide();
                }
            });
        }
    });




    $("[name^='back_stock_email[mailchimp_api]']").on('blur', function () {
        $('.error_message').remove();
        var mailchimp_api_key = $(this).val().trim();
        var clickmailchimphtml = '';
        if (mailchimp_api_key != '') {
            $.ajax({
                url: module_path,
                type: 'post',
                data: 'ajax=true&method=mailchimpgetlist&api_key=' + mailchimp_api_key,
                dataType: 'json',
                beforeSend: function () {
                    $('#show_loader_list_mailchimp').show();
                },
                success: function (json) {
                    if (json['error'] !== undefined) {
                        $("[name='back_stock_email[mailchimp_list]']").html('<option value="no_list">' + json['error'][0]['label'] + '</option>');
                        $("[name='back_stock_email[mailchimp_list]']").css('border', '1px solid #ff0000');
                    } else {
                        clickmailchimphtml += '<select name="back_stock_email[mailchimp_list]"';

                        clickmailchimphtml += 'id="back_stock_email[mailchimp_list]">';

                        for (i in json['success'])
                        {
                            clickmailchimphtml += '<option value="' + json['success'][i]['value'] + '">' + json['success'][i]['label'] + '</option>';
                        }
                        clickmailchimphtml += '</select>';
                        $("[name='back_stock_email[mailchimp_list]']").html(clickmailchimphtml);
                        $("[name='back_stock_email[mailchimp_list]']").css('border', '');
                    }
                },
                complete: function () {
                    $('#show_loader_list_mailchimp').hide();
                },
            });
        }
    });

    $("[name^='back_stock_email[klaviyo_api]']").on('blur', function () {
        $('.error_message').remove();
        var klaviyo_api_key = $(this).val().trim();
        var clickklaviyohtml = '';
        $.ajax({
            url: module_path,
            type: 'post',
            data: 'ajax=true&method=klaviyogetlist&api_key=' + klaviyo_api_key,
            dataType: 'json',
            beforeSend: function () {
                $('#show_loader_list_klaviyo').show();
            },
            success: function (json_data) {
                if (json_data['error'] !== undefined) {
                    $("[name='back_stock_email[klaviyo_list]']").html('<option value="no_list">' + json_data['error'][0]['label'] + '</option>');
                    $("[name='back_stock_email[klaviyo_list]']").css('border', '1px solid #ff0000');
                }
                else {
                    clickklaviyohtml += '<select name="back_stock_email[klaviyo_list]"';

                    clickklaviyohtml += 'id="klaviyo_selectlist">';

                    for (i in json_data['success'])
                    {
                        clickklaviyohtml += '<option value="' + json_data['success'][i]['value'] + '">' + json_data['success'][i]['label'] + '</option>';
                    }
                    clickklaviyohtml += '</select>';
                    $("[name='back_stock_email[klaviyo_list]']").html(clickklaviyohtml);
                    $("[name='back_stock_email[klaviyo_list]']").css('border', '');

                }
            },
            complete: function () {
                $('#show_loader_list_klaviyo').hide();
            },
        });
    });
});
function saveEmailProductTemplate(url)
{
    var selected_lang = selected_lang_initial;
    var selected_temp = 1;
    var text_email_body;
    var subject;
    var content;

    subject = $('#velsof_template_subject').val();

    content = tinyMCE.get('velsof_template_content').getContent();
    text_email_body = tinyMCE.get('velsof_template_content').getBody().textContent;


    var id_lang = selected_lang_initial;
    var template_id = $('#hidden_template_id').val();
    //tinyMCE.triggerSave();
    // if (content!='' && subject!=''){
    $.ajax({
        type: "POST",
        url: url,
        data: {ajax_rend: true, selected_temp: selected_temp, content: content, subject: subject, id_lang: id_lang, template_id: template_id, template_action: true, selected_lang: selected_lang, save_template: true, text_content: text_email_body},
        dataType: 'json',
        beforeSend: function () {
            $('#email_template_action').show();
        },
        success: function (json) {
            $('.form_initial').attr('disabled', false);
            $('#email_template_action').hide();
            $('#scratch_coupon_container .scratchcoupon_template_msg').remove();
            if (json['error'] != undefined)
            {
                var html = '<div class="bootstrap pricealert_template_msg"><div class="alert alert-danger">';
                html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                html += json['error'];
                html += '</div></div>';
                $('#content').children('div').eq(1).after(html);
                setTimeout(function () {
                    $('#velsof_supercheckout_container .pricealert_template_msg').remove();
                }, 5000);
            }
            else
            {
                var html = '<div class="bootstrap pricealert_template_msg"><div class="alert alert-success">';
                html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                html += json['msg'];
                html += '</div></div>';
                $('#content').children('div').eq(1).after(html);
                setTimeout(function () {
                    $('#velsof_supercheckout_container .pricealert_template_msg').remove();
                }, 5000);
            }
        }
    });
    // }
}
function saveEmailProductTemplateFinal(url)
{
    var selected_lang = selected_lang_final;
    var selected_temp = 2;
    var text_email_body;
    var subject;
    var content;

    subject = $('#velsof_template_subject_final').val();
    content = tinyMCE.get('velsof_template_content_final').getContent();
    text_email_body = tinyMCE.get('velsof_template_content_final').getBody().textContent;
    var id_lang = selected_lang_final;
    var template_id = $('#hidden_template_id_final').val();
//	tinyMCE.triggerSave();
//    if (content!='' && subject!=''){
    $.ajax({
        type: "POST",
        url: url,
        data: {ajax_rend: true, selected_temp: selected_temp, content: content, subject: subject, id_lang: id_lang, template_id: template_id, template_action: true, selected_lang: selected_lang, save_template: true, text_content: text_email_body},
        dataType: 'json',
        beforeSend: function () {
        },
        success: function (json) {
            $('.form_final').attr('disabled', false);
            $('#email_template_action').hide();
            $('#scratch_coupon_container .scratchcoupon_template_msg').remove();
            if (json['error'] != undefined)
            {
                var html = '<div class="bootstrap pricealert_template_msg"><div class="alert alert-danger">';
                html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                html += json['error'];
                html += '</div></div>';
                $('#content').children('div').eq(1).after(html);
                setTimeout(function () {
                    $('#velsof_supercheckout_container .pricealert_template_msg').remove();
                }, 5000);
            }
            else
            {
                var html = '<div class="bootstrap pricealert_template_msg"><div class="alert alert-success">';
                html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                html += json['msg'];
                html += '</div></div>';
                $('#content').children('div').eq(1).after(html);
                setTimeout(function () {
                    $('#velsof_supercheckout_container .pricealert_template_msg').remove();
                }, 5000);
            }
        }
    });
//    }
}

function saveEmailProductTemplateLowStock(url)
{
    var selected_lang = $('.v').val();
    var selected_temp = 3;
    var text_email_body;
    var subject;
    var content;

    subject = $('#velocity_low_stock_alert_setting_subject').val();
//    content = tinyMCE.activeEditor.getContent();
//    text_email_body = tinyMCE.activeEditor.getBody().textContent;
    content = tinyMCE.get('velocity_low_stock_alert_setting_template_content').getContent('');
    var text_email_body = $(content).text();

    var id_lang = $('.low_stock_lang').val();
    var template_id = $('#hidden_velocity_low_stock_alert_setting_template_id').val();
    tinyMCE.triggerSave();
    if (content != '' && subject != '') {
        $.ajax({
            type: "POST",
            url: url,
            data: {ajax_rend: true, selected_temp: selected_temp, content: content, subject: subject, id_lang: id_lang, template_id: template_id, template_action: true, selected_lang: selected_lang, save_template: true, text_content: text_email_body},
            dataType: 'json',
            beforeSend: function () {
            },
            success: function (json) {
                $('#email_template_action').hide();
                $('#scratch_coupon_container .scratchcoupon_template_msg').remove();
                if (json['error'] != undefined)
                {
                    var html = '<div class="bootstrap pricealert_template_msg"><div class="alert alert-danger">';
                    html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                    html += json['error'];
                    html += '</div></div>';
                    $('#content').children('div').eq(1).after(html);
                    setTimeout(function () {
                        $('#velsof_supercheckout_container .pricealert_template_msg').remove();
                    }, 5000);
                }
                else
                {
                    var html = '<div class="bootstrap pricealert_template_msg"><div class="alert alert-success">';
                    html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                    html += json['msg'];
                    html += '</div></div>';
                    $('#content').children('div').eq(1).after(html);
                    setTimeout(function () {
                        $('#velsof_supercheckout_container .pricealert_template_msg').remove();
                    }, 5000);
                }
            }
        });
    }
}

$('#filter_data').live('click', function (e) {

    var date_from = document.getElementById("date_from_cus").value;
    var date_to = document.getElementById("date_to_cus").value;

    var from = new Date(date_from);
    var to = new Date(date_to);
    var status = "true";

    var pro_value = $("#c_products").multipleSelect("getSelects");

    if (pro_value == 0 || pro_value == '')
    {

        document.getElementById("date-error-cust").innerHTML = pal_product_select_require;
        status = "false";
        return false;
    }
    else
    {
        document.getElementById("date-error-cust").innerHTML = "";


    }

    if (document.getElementById("date_from_cus").value == "")
    {
        document.getElementById("date_from_cus").placeholder = pal_date_required;
        document.getElementById("date_from_cus").style.background = "rgb(255,209,209)";
        status = "false";
    }
    else
    {
        document.getElementById("date_from_cus").setAttribute = ('placeholder', "");
        document.getElementById("date_from_cus").style.background = "";

    }

    if (document.getElementById("date_to_cus").value == "")
    {
        document.getElementById("date_to_cus").placeholder = pal_date_required;
        document.getElementById("date_to_cus").style.background = "rgb(255,209,209)";
        status = "false";
    }
    else if (document.getElementById("date_from_cus").value != "")
    {
        document.getElementById("date_to_cus").setAttribute = ('placeholder', "");
        document.getElementById("date_to_cus").style.background = "";

    }

    if (from > to)
    {
        document.getElementById("date-error-cust").innerHTML = pal_date_range_error;
        status = "false";
    }
    else if (from <= to && from != "")
    {
        document.getElementById("date-error-cust").innerHTML = "";

    }

    if (status == "true") {
        $.ajax({
            type: "POST",
            url: mod_dir + 'get_customer.php',
            data: "from=" + date_from + "&to=" + date_to + "&product=" + pro_value,
            beforeSend: function () {
                $('#c_loader').show();
            },
            success: function (response) {
                $('#c_loader').hide();
                $('#customer_data').html(response);
            }
        });
    }
});


$('#search_data').live('click', function (e) {
    var date_from = document.getElementById("count-from-date").value;
    var date_to = document.getElementById("count-to-date").value;

    var from = new Date(date_from);
    var to = new Date(date_to);
    var status = "true";


    var pro_value = $("#p_categories").multipleSelect("getSelects");

    if (document.getElementById("count-from-date").value == "")
    {
        document.getElementById("count-from-date").placeholder = pal_date_required;
        document.getElementById("count-from-date").style.background = "rgb(255,209,209)";
        status = "false";
    }
    else
    {
        document.getElementById("count-from-date").setAttribute = ('placeholder', "");
        document.getElementById("count-from-date").style.background = "";

    }

    if (document.getElementById("count-to-date").value == "")
    {
        document.getElementById("count-to-date").placeholder = pal_date_required;
        document.getElementById("count-to-date").style.background = "rgb(255,209,209)";
        status = "false";
    }
    else if (document.getElementById("count-from-date").value != "")
    {
        document.getElementById("count-to-date").setAttribute = ('placeholder', "");
        document.getElementById("count-to-date").style.background = "";

    }

    if (from > to)
    {
        document.getElementById("date-error-pro").innerHTML = pal_date_range_error;
        status = "false";
    }
    else if (from <= to && from != "")
    {
        document.getElementById("date-error-pro").innerHTML = "";

    }

    if (status == "true") {
        var skv = $('#Search_skv').val();
        var categories;
        if ($('#p_categories').val() == null)
        {
            categories = "nothing";
        }
        else
        {
            categories = $('#p_categories').val();
        }

        $.ajax({
            type: "POST",
            url: mod_dir + 'get_alert.php',
            data: "from=" + $("#count-from-date").val() + "&to=" + $("#count-to-date").val() + "&category=" + categories + "&skv=" + $('#Search_skv').val(),
            beforeSend: function () {
                $('#loader').show();
            },
            success: function (response) {
                $('#loader').hide();
//              $('#graph_loader').hide();
                $('#product_data').html(response);
                $('#graph_loader').show();

            }
        });
    }
    else
    {
        return false;
    }

});
function getDefaultGraph() {
    if (times == 0) {
        getDate(1);
    }
}
function getDate(type)
{
    switch (type) {
        case 1 :
            var todate = moment().format("MM/DD/YYYY");
            var fromdate = moment(todate).startOf('week').format("MM/DD/YYYY");
            $("#calender-txt").html(this_week_txt);
            break;
        case 2 :
            var todate = moment().format("MM/DD/YYYY");
            var fromdate = moment(todate).startOf('month').format("MM/DD/YYYY");
            $("#calender-txt").html(this_month_txt);
            break;
        case 3 :
            var todate = moment().format("MM/DD/YYYY");
            var fromdate = moment(todate).startOf('year').format("MM/DD/YYYY");
            $("#calender-txt").html(this_year_txt);
            break;
        case 4 :
            var todate = moment().subtract(1, 'weeks').endOf('Week').format("MM/DD/YYYY");
            var fromdate = moment().subtract(1, 'weeks').startOf('Week').format("MM/DD/YYYY");
            $("#calender-txt").html(last_week_txt);
            break;
        case 5 :
            var todate = moment().subtract(1, 'months').endOf('month').format("MM/DD/YYYY");
            var fromdate = moment().subtract(1, 'months').startOf('month').format("MM/DD/YYYY");
            $("#calender-txt").html(last_month_txt);
            break;
        case 6 :
            var todate = moment().subtract(1, 'years').endOf('year').format("MM/DD/YYYY");
            var fromdate = moment().subtract(1, 'years').startOf('year').format("MM/DD/YYYY");
            $("#calender-txt").html(last_year_txt);
            break;
    }
//    $('.bgcolor').removeClass('tab-active');
//    $('.bgcolor-total').addClass('tab-active');
    $("#count-to-date").val(todate);
    $("#count-from-date").val(fromdate);
    var fromdate = $("#count-from-date").val();
    var todate = $("#count-to-date").val();
    getGraph();
}
$('#filter_graph').live('click', function (e) {
    var date_from = document.getElementById("count-from-date").value;
    var date_to = document.getElementById("count-to-date").value;
    var from = new Date(date_from);
    var to = new Date(date_to);
    var status = "true";
    var currentDate = new Date()
    var day = currentDate.getDate()
    var month = currentDate.getMonth() + 1
    var year = currentDate.getFullYear()
    var date_curent = (month + "/" + day + "/" + year);
    if (document.getElementById("count-from-date").value == "")
    {
        document.getElementById("count-from-date").placeholder = pal_date_required;
        document.getElementById("count-from-date").style.background = "rgb(255,209,209)";
        status = "false";
    }
    else
    {
        document.getElementById("count-from-date").setAttribute = ('placeholder', "");
        document.getElementById("count-from-date").style.background = "";

    }

    if (document.getElementById("count-to-date").value == "")
    {
        document.getElementById("count-to-date").placeholder = pal_date_required;
        document.getElementById("count-to-date").style.background = "rgb(255,209,209)";
        status = "false";
    }
    else if (document.getElementById("count-from-date").value != "")
    {
        document.getElementById("count-to-date").setAttribute = ('placeholder', "");
        document.getElementById("count-to-date").style.background = "";

    }

    if (from > to)
    {
        document.getElementById("date-error-pro").innerHTML = pal_date_range_error;
        status = "false";
    }
    else if (from <= to && from != "")
    {
        document.getElementById("date-error-pro").innerHTML = "";

    }
    if (document.getElementById("count-from-date").value > date_curent)
    {
        $('#date-error-pro').html(pal_date_future);
        status = "false";
    }
    if (status == "true") {
        $.ajax({
            type: "POST",
            url: mod_dir + 'get_alert.php',
            data: "from=" + $("#count-from-date").val() + "&to=" + $("#count-to-date").val(),
            beforeSend: function () {
                $('#loader').show();
            },
            success: function (response) {
                $('#loader').hide();
//                $('#graph_loader').hide();
//                $('#product_data').html(response);
                $('#graph_loader').show();
                getGraph();
            }
        });
    }
    else
    {
        return false;
    }
});

function getGraph()
{
    $.ajax({
        type: "POST",
        url: action_product_update,
        data: "graph=true&from=" + $("#count-from-date").val() + "&to=" + $("#count-to-date").val(),
        dataType: 'json',
        success: function (json) {
            times = 1;
            $('.no_data').remove();
            if (json['combination'].length == 0) {
                $('#graph_loader').css('height', '80px');
                $('#flot-placeholder').html("");
                $('#graph_loader').prepend('<div class="no_data">' + no_data + '</div>');
            }
            else {
                $('#graph_loader').css('height', '300px');
                $('.no_data').remove();
//                drawProductChart(json);
                makechart(json);
            }
        }
    });
}

function drawProductChart(json)
{
    var dataset = [], ticks = [];
    var temp = [], comb = [];
    var i;


    var bar_property = {order: i + 1, lineWidth: 0};
    for (i = 0; i < json['combination'].length; i++)
    {
        temp.push([i, parseInt(json['count'][i])]);
        comb.push([json['combination'][i]['label']]);
        var combination = {label: comb, data: temp, bars: bar_property};
        dataset.push(combination);
        var temp = [], comb = [];

    }
    for (d = 0; d < json['name'].length; d++) {
        ticks.push([d, json['name'][d]['name']]);
    }
    var options = {
        series: {
            grow: {active: true}
        },
        bars: {
            show: true,
            barWidth: 0.2,
            fill: 1,
            align: "center"
        },
        xaxis: {
            axisLabel: products,
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 16,
//                           axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 10,
            ticks: ticks,
            autoscaleMargin: 0.01,
            tickColor: "#fff",
        },
        yaxis: {
            axisLabel: customers,
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 15,
            axisLabelFontFamily: 'sans-serif',
            axisLabelPadding: 3,
            tickFormatter: function (v, axis) {
                return Math.round(v * 100) / 100;
            }
        },
        legend: {
            noColumns: 0,
            labelBoxBorderColor: null,
            position: "ne"
        },
        grid: {
            hoverable: true,
            borderWidth: 1,
            borderColor: '#EEEEEE',
            mouseActiveRadius: 10,
            backgroundColor: "#ffffff",
            axisMargin: 20
        }
    };

    $.plot($("#flot-placeholder"), dataset, options);

    var previousPoint = null, previousLabel = null;

    $("#flot-placeholder").on("plothover", function (event, pos, item) {
        if (item) {
            if ((previousLabel != item.series.label) || (previousPoint != item.dataIndex)) {
                previousPoint = item.dataIndex;
                previousLabel = item.series.label;
                $("#tooltip").remove();

                var x = Math.floor(item.datapoint[0]);
                if (x < 0) {
                    x = 0;
                }
                var y = item.datapoint[1];

                var color = item.series.color;

                showTooltip(item.pageX,
                    item.pageY,
                    color,
                    "<strong>" + item.series.label + "</strong><br>" + item.series.xaxis.ticks[x].label + " : <strong>" + y + "</strong>");
            }
        } else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });

    function showTooltip(x, y, color, contents) {
        $('<div id="tooltip">' + contents + '</div>').css({
            position: 'absolute',
            display: 'none',
            top: y - 40,
            left: x - 70,
            border: '1px solid ' + color,
            padding: '3px',
            'font-size': '11px',
            'border-radius': '5px',
            'background-color': '#fff',
            'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
            opacity: 0.9
        }).appendTo("body").fadeIn(200);
    }
}

function getStatsGraph()
{
    $.ajax({
        type: "POST",
        url: action_product_update,
        data: "statsgraph=true",
        dataType: 'json',
        success: function (json) {
            times_stats = 1;
            $('.no_data').remove();
            if (Object.keys(json).length == 0) {
                $('#graph_loader_stats').css('height', '80px');
                $('#flot-placeholder_stats').html("");
                $('#graph_loader_stats').prepend('<div class="no_data">' + no_data + '</div>');
            }
            else {
                $('#graph_loader_stats').css('height', '300px');
                $('.no_data').remove();
//                drawProductChart(json);
                makestatschart(json);
            }
        }
    });
}

function makechart(json)
{
    var language = [];
    var total = [];
    var color = [];
    
    $.each( json['name'], function( i, l ){
        language.push(l['name']);
    });
    $.each( json['count'], function( i, l ){
        total.push(l);
    });
    
    $.each( json['name'], function( i, l ){
        var col = generateRandomColor();
        color.push(col);
    });
    

    var chart_data = {
        labels: language,
        datasets: [
            {
                label: 'Vote',
                backgroundColor: color,
                color: '#fff',
                data: total
            }
        ]
    };

    var options = {
        responsive: true,
        scales: {
            yAxes: [{
                    ticks: {
                        min: 0
                    }
                }]
        }
    };

    var group_chart1 = $('#pie_chart');

    var graph1 = new Chart(group_chart1, {
        type: "pie",
        data: chart_data
    });

//    var group_chart2 = $('#doughnut_chart');
//
//    var graph2 = new Chart(group_chart2, {
//        type: "doughnut",
//        data: chart_data
//    });
//
//    var group_chart3 = $('#bar_chart');
//
//    var graph3 = new Chart(group_chart3, {
//        type: 'bar',
//        data: chart_data,
//        options: options
//    });
}

function makestatschart(json)
{
    var stats_language = [];
    var stats_total = [];
    var stats_color = [];
    
    $.each( json['data'], function( i, l ){
        if( i == 'total_sent') {
            stats_language.push('Sent Emails');
            stats_total.push(l);
            var stats_col = generateRandomColor();
            stats_color.push(stats_col);
        }
//        if( i == 'total_opened') {
//            stats_language.push('Opened Emails');
//            stats_total.push(l);
//            var stats_col = generateRandomColor();
//            stats_color.push(stats_col);
//        }
        if( i == 'total_view_clicks') {
            stats_language.push('View Clicks');
            stats_total.push(l);
            var stats_col = generateRandomColor();
            stats_color.push(stats_col);
        }
//        if( i == 'total_buy_now_clicks') {
//            stats_language.push('Buy Now Clicks');
//            stats_total.push(l);
//            var stats_col = generateRandomColor();
//            stats_color.push(stats_col);
//        }
    });
    

    var stats_chart_data = {
        labels: stats_language,
        datasets: [
            {
                label: stats_language,
                backgroundColor: stats_color,
                color: '#fff',
                data: stats_total
            }
        ]
    };

    var stats_options = {
        responsive: true,
        scales: {
            yAxes: [{
                    ticks: {
                        min: 0
                    }
                }]
        }
    };

    var stats_group_chart1 = $('#pie_chart_stats');

    var stats_graph1 = new Chart(stats_group_chart1, {
        type: "pie",
        data: stats_chart_data
    });

//    var group_chart2 = $('#doughnut_chart');
//
//    var graph2 = new Chart(group_chart2, {
//        type: "doughnut",
//        data: chart_data
//    });
//
    var stats_group_chart3 = $('#bar_chart_stats');

    var stats_graph3 = new Chart(stats_group_chart3, {
        type: 'bar',
        data: stats_chart_data,
        options: stats_options
    });
}

function generateRandomColor() {
    var col = Math.floor(Math.random()*16777215).toString(16);
    var color = "#"+col; 
    return color;
}

function add_mcefile() {



    $(document).ready(function () {


        _tinyMCE = tinySetup({
            editor_selector: "velsof_email_content",
            theme_advanced_buttons1: "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
            theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",
            theme_advanced_buttons4: "styleprops,|,cite,abbr,acronym,del,ins,attribs,pagebreak",
            setup: function (ed) {


                ed.onKeyUp.add(function (ed, e) {
                    tinyMCE.triggerSave();
                    textarea = $('#' + ed.id);
                    max = textarea.parent('div').find('span.counter').attr('max');
                    if (max != 'none')
                    {
                        textarea_value = textarea.val();
                        count = stripHTML(textarea_value).length;
                        rest = max - count;
                        if (rest < 0)
                            textarea.parent('div').find('span.counter').html('<span style="color:red;">Maximum ' + max + ' characters : ' + rest + '</span>');
                        else
                            textarea.parent('div').find('span.counter').html(' ');
                    }
                });
            }
        });


    });
}
function change_tab(a, b) {
    if (version == 1.5) {
        $('.tab-page').removeClass('active');
        $('.tab-page').removeClass('selected');
    }
    $('.list-group-item').removeClass('active');
    $(a).addClass(' active');
    if (b == 1) {
        $('#general_form').show();
        $('#initial_form').hide();
        $('#final_form').hide();
        $('#analysis_form').hide();
        $('#email_marketing_form').hide();
        $('#low_stock_alert_form').hide();
        $('#recaptcha_form').hide(); //by dharmanshu 
        $('#availability_form').hide();
        $('#statistics_form').hide();
    } else if (b == 2) {
        $('#general_form').hide();
        $('#initial_form').show();
        $('#final_form').hide();
        $('#analysis_form').hide();
        $('#email_marketing_form').hide();
        $('#low_stock_alert_form').hide();
        $('#recaptcha_form').hide(); //by dharmanshu
        $('#availability_form').hide();
        $('#statistics_form').hide();
//        $('#subscriber_list').hide();


    } else if (b == 3) {
        $('#general_form').hide();
        $('#initial_form').hide();
        $('#final_form').show();
        $('#analysis_form').hide();
        $('#list_viewers').show();
        $('#email_marketing_form').hide();
        $('#low_stock_alert_form').hide();
        $('#availability_form').hide();
        $('#statistics_form').hide();
        $('#recaptcha_form').hide(); //by dharmanshu
//        $('#subscriber_list').hide();

    } else if (b == 4) {
        $('#general_form').hide();
        $('#initial_form').hide();
        $('#final_form').hide();
        $('#analysis_form').hide();
        $('#list_viewers').hide();
        $('#email_marketing_form').hide();
        $('#low_stock_alert_form').show();
        $('#availability_form').hide();
        $('#statistics_form').hide();
        $('#recaptcha_form').hide(); //by dharmanshu
//        $('#subscriber_list').hide();

    } else if (b == 5) {
        $('#general_form').hide();
        $('#initial_form').hide();
        $('#final_form').hide();
        $('#analysis_form').hide();
        $('#list_viewers').hide();
        $('#email_marketing_form').show();
        $('#subscriber_list').hide();
        $('#low_stock_alert_form').hide();
        $('#recaptcha_form').hide(); //by dharmanshu
        $('#availability_form').hide();
        $('#statistics_form').hide();

    } else if (b == 7) {
        $('#general_form').hide();
        $('#initial_form').hide();
        $('#final_form').hide();
        $('#analysis_form').hide();
        $('#list_viewers').hide();
        $('#low_stock_alert_form').hide();
        $('#availability_form').hide();
        $('#statistics_form').hide();
        $('#recaptcha_form').show(); //by dharmanshu
//        $('#subscriber_list').show();
    } else if (b == 8) {
        $('#general_form').hide();
        $('#initial_form').hide();
        $('#final_form').hide();
        $('#analysis_form').hide();
        $('#list_viewers').hide();
        $('#low_stock_alert_form').hide();
        $('#recaptcha_form').hide();
        $('#availability_form').show();
        $('#statistics_form').hide();
        function deleteSelectedProduct(productId, current) {
            $('input[name="availability_form[excluded_products_hidden]"]').val(removeIdFromCommaString($('input[name="availability_form[excluded_products_hidden]"]').val(), productId, ','));
            $('input[name="availability_form[product_name]"]').val('');
            $(current).parent().remove();
        }

        function removeIdFromCommaString(list, value, separator) {
            separator = separator || ",";
            var values = list.split(separator);
            for (var i = 0; i < values.length; i++) {
                if (values[i] == value) {
                    values.splice(i, 1);
                    return values.join(separator);
                }
            }
            return list;
        }
    } else if (b == 9) {
        $('#general_form').hide();
        $('#initial_form').hide();
        $('#final_form').hide();
        $('#analysis_form').hide();
        $('#list_viewers').hide();
        $('#low_stock_alert_form').hide();
        $('#recaptcha_form').hide();
        $('#availability_form').hide();
        $('#statistics_form .panel').append($('#list_graph_stats'));
        $('#list_graph_stats').css('display','block');
        $('#list_graph').css('display','none');
        $('#statistics_form').show();
        if (times_stats == 0) {
            getStatsGraph();
        }
    } else {
        $('#subscriber_list').hide();
        $('#general_form').hide();
        $('#initial_form').hide();
        $('#final_form').hide();
        $('#analysis_form .panel').append($('#list_graph'));
        $('#list_graph').css('display','block');
        $('#list_graph_stats').css('display','none');
        $('#analysis_form').show();
        $('#list_viewers').show();
        $('#email_marketing_form').hide();
        $('#low_stock_alert_form').hide();
        $('#availability_form').hide();
        $('#statistics_form').hide();
        $('#recaptcha_form').hide(); //by dharmanshu
        if (times == 0) {
            getGraph();
        }


    }
}

var error_display = 0;
var error_display1 = 0;
var error_display2 = 0;
var error_display3 = 0;
var error_display4 = 0;
var error_display9 = 0;
var error_display10 = 0;
function validation_admin() {
    var error = false;
    $('.error_message').remove();
    var product_update_privacy_text_err = false;
    var product_update_privacy_URL_err = false;
    var product_update_privacy_check_URL_err = false;
    if ($('[id^="product_update[enable_gdpr_policy]_on"]').is(':checked') === true) {
        $("input[name^=product_update_gdpr_policy_text_]").each(function () {
            var product_update_privacy_text_mand = velovalidation.checkMandatory($(this));
            if (product_update_privacy_text_mand != true) {
                error = true;
                product_update_privacy_text_err = true;
            }
        });
        if (product_update_privacy_text_err) {
            $("input[name^=product_update_gdpr_policy_text_]").addClass('error_field');
            $("input[name^=product_update_gdpr_policy_text_]").after('<span class="error_message">' + check_for_all_lang + '</span>');
        }
        $("input[name^=product_update_gdpr_policy_url]").each(function () {
            var product_update_gdpr_policy_url_mand = velovalidation.checkMandatory($(this));
            if (product_update_gdpr_policy_url_mand != true) {
                error = true;
                product_update_privacy_URL_err = true;
            }
        });
        if (product_update_privacy_URL_err) {
            $("input[name^=product_update_gdpr_policy_url_]").addClass('error_field');
            $("input[name^=product_update_gdpr_policy_url_]").after('<span class="error_message">' + check_for_all_lang + '</span>');
        }
        if (!product_update_privacy_URL_err) {
            $("input[name^=product_update_gdpr_policy_url]").each(function () {
                var product_update_gdpr_policy_check_url = velovalidation.checkUrl($(this));
                if (product_update_gdpr_policy_check_url != true) {
                    error = true;
                    product_update_privacy_check_URL_err = true;
                }
            });
            if (product_update_privacy_check_URL_err) {
                $("input[name^=product_update_gdpr_policy_url_]").addClass('error_field');
                $("input[name^=product_update_gdpr_policy_url_]").after('<span class="error_message">' + check_url_for_all_lang + '</span>');
            }
        }
    }



    // validation for subscription page
    if ($('[id^="product_update[enable_subscription_list]_on"]').is(':checked') === true) {
        var subscription_per_page = velovalidation.checkMandatory($('input[name="product_update[subscription_per_page]"]'));
        if (subscription_per_page != true)
        {
            error = true;
            $('input[name="product_update[subscription_per_page]"]').addClass('error_field');
            $('input[name="product_update[subscription_per_page]"]').parent().after('<span class="error_message">' + empty_field + '</span>');
        } else {
            var subscription_per_page = velovalidation.isNumeric($('input[name="product_update[subscription_per_page]"]'), true);
            if (subscription_per_page != true)
            {
                error = true;
                $('input[name="product_update[subscription_per_page]"]').addClass('error_field');
                $('input[name="product_update[subscription_per_page]"]').parent().after('<span class="error_message">' + kb_numeric + '</span>');
            } else if ($('input[name="product_update[subscription_per_page]"]').val() == 0) {
                error = true;
                $('input[name="product_update[subscription_per_page]"]').parent().addClass('error_field');
                $('input[name="product_update[subscription_per_page]"]').after('<span class="error_message">' + kb_numeric + '</span>');
            }
        }
    }
    if ($('[id^="product_update[enable_low_stock_alert]_on"]').is(':checked') === true) {
        var low_stock_alert_quantity = velovalidation.checkMandatory($('input[name="product_update[low_stock_alert_quantity]"]'));
        if (low_stock_alert_quantity != true)
        {
            error = true;
            $('input[name="product_update[low_stock_alert_quantity]"]').addClass('error_field');
            $('input[name="product_update[low_stock_alert_quantity]"]').parent().after('<span class="error_message">' + empty_field + '</span>');
        } else {
            var low_stock_alert_quantity = velovalidation.isNumeric($('input[name="product_update[low_stock_alert_quantity]"]'), true);
            if (low_stock_alert_quantity != true)
            {
                error = true;
                $('input[name="product_update[low_stock_alert_quantity]"]').addClass('error_field');
                $('input[name="product_update[low_stock_alert_quantity]"]').parent().after('<span class="error_message">' + kb_numeric_low_stock + '</span>');
            } else if ($('input[name="product_update[low_stock_alert_quantity]"]').val() == 0) {
                error = true;
                $('input[name="product_update[low_stock_alert_quantity]"]').parent().addClass('error_field');
                $('input[name="product_update[low_stock_alert_quantity]"]').after('<span class="error_message">' + kb_numeric_low_stock + '</span>');
            }
        }
    }
    
    //changes by vishal for adding related product functionality
    if ($('[name="product_update[enable_utm]"]:checked').val() == '1') {
        var utm_source = velovalidation.checkMandatory($('input[name="product_update[product_update_utm_source]"]'));
        if (utm_source != true) {
            error = true;
            $('input[name="product_update[product_update_utm_source]"]').addClass('error_field');
            $('input[name="product_update[product_update_utm_source]"]').after('<span class="error_message">' + empty_field + '</span>');
        }
        var utm_medium = velovalidation.checkMandatory($('input[name="product_update[product_update_utm_medium]"]'));
        if (utm_medium != true) {
            error = true;
            $('input[name="product_update[product_update_utm_medium]"]').addClass('error_field');
            $('input[name="product_update[product_update_utm_medium]"]').after('<span class="error_message">' + empty_field + '</span>');
        }
        var utm_campaign = velovalidation.checkMandatory($('input[name="product_update[product_update_utm_campaign]"]'));
        if (utm_campaign != true) {
            error = true;
            $('input[name="product_update[product_update_utm_campaign]"]').addClass('error_field');
            $('input[name="product_update[product_update_utm_campaign]"]').after('<span class="error_message">' + empty_field + '</span>');
        }
    }
    
    if ($('select[name="product_update[specific_products_final][]"]').val() == null) {
        if ($('[name="product_update[related_product_method_final]"]').val() == '3') {
            error = true;
            $('#multiple-select-specific_products_final').addClass('error_field');
            $('select[name="product_update[specific_products_final][]"]').closest('.col-lg-9').append('<p class="error_message">' + available_product_empty + '</p>');
        }
    }
    if ($('select[name="product_update[specific_products_initial][]"]').val() == null) {
        if ($('[name="product_update[related_product_method_initial]"]').val() == '3') {
            error = true;
            $('#multiple-select-specific_products_initial').addClass('error_field');
            $('select[name="product_update[specific_products_initial][]"]').closest('.col-lg-9').append('<p class="error_message">' + available_product_empty + '</p>');
        }
    }
    if ($('select[name="product_update[specific_products_low_stock][]"]').val() == null) {
        if ($('[name="product_update[related_product_method_low_stock]"]').val() == '3') {
            error = true;
            $('#multiple-select-specific_products_low_stock').addClass('error_field');
            $('select[name="product_update[specific_products_low_stock][]"]').closest('.col-lg-9').append('<p class="error_message">' + available_product_empty + '</p>');
        }
    }
    //changes end
    // changes over
    /*Knowband validation start*/
    var validate_background_color_mandatory = velovalidation.checkMandatory($('input[name="product_update[background]"]'));
    if (validate_background_color_mandatory != true) {
        error = true;
        $("input[name='product_update[background]']").closest('.form-group').find('.error_message').show();
        if (error_display < 1 && $("input[name='product_update[background]']").closest('.form-group').find('.error_message').length <= 0) {
            $('<p class="error_message"></p>').appendTo($("input[name='product_update[background]']").closest('.form-group'));
            error_display++;
        }
        $('input[name="product_update[background]"]').closest('.input-group').addClass('error_field');
        $('input[name="product_update[background]"]').closest('.form-group').find('.error_message').html(validate_background_color_mandatory);

    } else {
        var validate_background_color = velovalidation.isColor($('input[name="product_update[background]"]'));
        var validate_background_color_tags = velovalidation.checkHtmlTags($('input[name="product_update[background]"]'));
        if (validate_background_color_tags != true) {
            error = true;
            $("input[name='product_update[background]']").closest('.form-group').find('.error_message').show();
            if (error_display1 < 1 && $("input[name='product_update[background]']").closest('.form-group').find('.error_message').length <= 0) {
                $('<p class="error_message"></p>').appendTo($("input[name='product_update[background]']").closest('.form-group'));
                error_display1++;
            }
            $('input[name="product_update[background]"]').closest('.input-group').addClass('error_field');
            $('input[name="product_update[background]"]').closest('.form-group').find('.error_message').html(validate_background_color_tags);
        }
        else if (validate_background_color != true) {
            error = true;
            $("input[name='product_update[background]']").closest('.form-group').find('.error_message').show();
            if (error_display1 < 1 && $("input[name='product_update[background]']").closest('.form-group').find('.error_message').length <= 0) {
                $('<p class="error_message"></p>').appendTo($("input[name='product_update[background]']").closest('.form-group'));
                error_display1++;
            }
            $('input[name="product_update[background]"]').closest('.input-group').addClass('error_field');
            $('input[name="product_update[background]"]').closest('.form-group').find('.error_message').html(validate_background_color);
        } else {
            $('input[name="product_update[background]"]').closest('.input-group').removeClass('error_field');
            $('input[name="product_update[background]"]').closest('.form-group').find('.error_message').hide();
        }
    }
    /*Knowband validation end*/

    /*Knowband validation start*/
    var validate_css = velovalidation.checkTags($('textarea[name="kb_backinstock_css'));
    if (validate_css != true) {
        error = true;
        $("textarea[name='kb_backinstock_css']").closest('div').find('.error_message').show();
        if (error_display9 < 1 && $("textarea[name='kb_backinstock_css']").closest('div').find('.error_message').length <= 0) {
            $('textarea[name="kb_backinstock_css"]').after($('<p class="error_message"></p>'));
            error_display9++;
        }
        $('textarea[name="kb_backinstock_css"]').addClass('error_field');
        $('textarea[name="kb_backinstock_css"]').closest('div').find('.error_message').html(validate_css);

    } else if ($('textarea[name="kb_backinstock_css').val().trim.length > 10000) {
        error = true;
        $("textarea[name='kb_backinstock_css']").closest('div').find('.error_message').show();
        if (error_display9 < 1 && $("textarea[name='kb_backinstock_css']").closest('div').find('.error_message').length <= 0) {
            $('textarea[name="kb_backinstock_css"]').after($('<p class="error_message"></p>'));
            error_display9++;
        }
        $('textarea[name="kb_backinstock_css"]').addClass('error_field');
        $('textarea[name="kb_backinstock_css"]').closest('div').find('.error_message').html(error_length_message);
    } else {
        $('textarea[name="kb_backinstock_css"]').removeClass('error_field');
        $('textarea[name="kb_backinstock_css"]').closest('div').find('.error_message').hide();
    }
    /*Knowband validation end*/
    /*Knowband validation start*/
    var validate_js = velovalidation.checkTags($('textarea[name="kb_backinstock_js'));
    if (validate_js != true) {
        error = true;
        $("textarea[name='kb_backinstock_js']").closest('div').find('.error_message').show();
        if (error_display10 < 1 && $("textarea[name='kb_backinstock_js']").closest('div').find('.error_message').length <= 0) {
            $('textarea[name="kb_backinstock_js"]').after($('<p class="error_message"></p>'));
            error_display10++;
        }
        $('textarea[name="kb_backinstock_js"]').addClass('error_field');
        $('textarea[name="kb_backinstock_js"]').closest('div').find('.error_message').html(validate_css);

    } else if ($('textarea[name="kb_backinstock_js').val().trim.length > 10000) {
        error = true;
        $("textarea[name='kb_backinstock_js']").closest('div').find('.error_message').show();
        if (error_display10 < 1 && $("textarea[name='kb_backinstock_js']").closest('div').find('.error_message').length <= 0) {
            $('textarea[name="kb_backinstock_js"]').after($('<p class="error_message"></p>'));
            error_display10++;
        }
        $('textarea[name="kb_backinstock_js"]').addClass('error_field');
        $('textarea[name="kb_backinstock_js"]').closest('div').find('.error_message').html(error_length_message);
    } else {
        $('textarea[name="kb_backinstock_js"]').removeClass('error_field');
        $('textarea[name="kb_backinstock_js"]').closest('div').find('.error_message').hide();
    }
    /*Knowband validation end*/

    /*Knowband validation start*/
    var validate_border_color_mandatory = velovalidation.checkMandatory($('input[name="product_update[border]"]'));
    if (validate_border_color_mandatory != true) {
        error = true;
        $("input[name='product_update[border]']").closest('.form-group').find('.error_message').show();
        if (error_display2 < 1 && $("input[name='product_update[border]']").closest('.form-group').find('.error_message').length <= 0) {
            $('<p class="error_message"></p>').appendTo($("input[name='product_update[border]']").closest('.form-group'));
            error_display2++;
        }
        $('input[name="product_update[border]"]').closest('.input-group').addClass('error_field');
        $('input[name="product_update[border]"]').closest('.form-group').find('.error_message').html(validate_border_color_mandatory);

    } else {
        var validate_border_color = velovalidation.isColor($('input[name="product_update[border]"]'));
        var validate_border_color_tags = velovalidation.checkHtmlTags($('input[name="product_update[border]"]'));
        if (validate_border_color_tags != true) {
            error = true;
            $("input[name='product_update[border]']").closest('.form-group').find('.error_message').show();
            if (error_display3 < 1 && $("input[name='product_update[border]']").closest('.form-group').find('.error_message').length <= 0) {
                $('<p class="error_message"></p>').appendTo($("input[name='product_update[border]']").closest('.form-group'));
                error_display3++;
            }
            $('input[name="product_update[border]"]').closest('.input-group').addClass('error_field');
            $('input[name="product_update[border]"]').closest('.form-group').find('.error_message').html(validate_border_color_tags);
        }
        else if (validate_border_color != true) {
            error = true;
            $("input[name='product_update[border]']").closest('.form-group').find('.error_message').show();
            if (error_display3 < 1 && $("input[name='product_update[border]']").closest('.form-group').find('.error_message').length <= 0) {
                $('<p class="error_message"></p>').appendTo($("input[name='product_update[border]']").closest('.form-group'));
                error_display3++;
            }
            $('input[name="product_update[border]"]').closest('.input-group').addClass('error_field');
            $('input[name="product_update[border]"]').closest('.form-group').find('.error_message').html(validate_border_color);
        } else {
            $('input[name="product_update[border]"]').closest('.input-group').removeClass('error_field');
            $('input[name="product_update[border]"]').closest('.form-group').find('.error_message').hide();
        }
    }
    /*Knowband validation end*/

    /*Knowband validation start*/
    var validate_text_color_mandatory = velovalidation.checkMandatory($('input[name="product_update[text]"]'));
    if (validate_text_color_mandatory != true) {
        error = true;
        $("input[name='product_update[text]']").closest('.form-group').find('.error_message').show();
        if (error_display4 < 1 && $("input[name='product_update[text]']").closest('.form-group').find('.error_message').length <= 0) {
            $('<p class="error_message"></p>').appendTo($("input[name='product_update[text]']").closest('.form-group'));
            error_display4++;
        }
        $('input[name="product_update[text]"]').closest('.input-group').addClass('error_field');
        $('input[name="product_update[text]"]').closest('.form-group').find('.error_message').html(validate_text_color_mandatory);

    } else {
        var validate_text_color = velovalidation.isColor($('input[name="product_update[text]"]'));
        var validate_text_color_tags = velovalidation.checkHtmlTags($('input[name="product_update[text]"]'));
        if (validate_text_color_tags != true) {
            error = true;
            $("input[name='product_update[text]']").closest('.form-group').find('.error_message').show();
            if (error_display4 < 1 && $("input[name='product_update[text]']").closest('.form-group').find('.error_message').length <= 0) {
                $('<p class="error_message"></p>').appendTo($("input[name='product_update[text]']").closest('.form-group'));
                error_display4++;
            }
            $('input[name="product_update[text]"]').closest('.input-group').addClass('error_field');
            $('input[name="product_update[text]"]').closest('.form-group').find('.error_message').html(validate_text_color_tags);
        }
        else if (validate_text_color != true) {
            error = true;
            $("input[name='product_update[text]']").closest('.form-group').find('.error_message').show();
            if (error_display4 < 1 && $("input[name='product_update[text]']").closest('.form-group').find('.error_message').length <= 0) {
                $('<p class="error_message"></p>').appendTo($("input[name='product_update[text]']").closest('.form-group'));
                error_display4++;
            }
            $('input[name="product_update[text]"]').closest('.input-group').addClass('error_field');
            $('input[name="product_update[text]"]').closest('.form-group').find('.error_message').html(validate_text_color);
        } else {
            $('input[name="product_update[text]"]').closest('.input-group').removeClass('error_field');
            $('input[name="product_update[text]"]').closest('.form-group').find('.error_message').hide();
        }
    }
    /*Knowband validation end*/


    if (error == true) {
        return false;
    } else {
        return true;
    }
}

var error_display8 = 0;
var error_display9 = 0;
function validation_email_final_settings() {
    var error = false;
    /*Knowband validation start*/
    var validate_subject_mandatory = velovalidation.checkMandatory($('input[name="velocity_email_template[subject_final]"]'));
    if (validate_subject_mandatory != true) {
        error = true;
        $("input[name='velocity_email_template[subject_final]']").closest('div').find('.error_message').show();
        if (error_display8 < 1 && $("input[name='velocity_email_template[subject]']").closest('div').find('.error_message').length <= 0) {
            $('input[name="velocity_email_template[subject_final]"]').after($('<p class="error_message"></p>'));
            error_display8++;
        }
        $('input[name="velocity_email_template[subject_final]"]').addClass('error_field');
        $('input[name="velocity_email_template[subject_final]"]').closest('div').find('.error_message').html(validate_subject_mandatory);

    } else {
//        var validate_username = velovalidation.checkUsername($('input[name="velocity_email_template[subject]"]'));
        var validate_subject_tags = velovalidation.checkHtmlTags($('input[name="velocity_email_template[subject_final]"]'));
        if (validate_subject_tags != true) {
            error = true;
            $("input[name='velocity_email_template[subject_final]']").closest('div').find('.error_message').show();
            if (error_display8 < 1 && $("input[name='velocity_email_template[subject_final]']").closest('div').find('.error_message').length <= 0) {
                $('input[name="velocity_email_template[subject_final]"]').after($('<p class="error_message"></p>'));
                error_display8++;
            }
            $('input[name="velocity_email_template[subject_final]"]').addClass('error_field');
            $('input[name="velocity_email_template[subject_final]"]').closest('div').find('.error_message').html(validate_subject_tags);
        }
        else {
            $('input[name="velocity_email_template[subject_final]"]').removeClass('error_field');
            $('input[name="velocity_email_template[subject_final]"]').closest('div').find('.error_message').hide();
        }
    }
    /*Knowband validation end*/
//    console.log(tinyMCE.get('velsof_template_content_final').getContent());
    /*Knowband validation start*/
    var validate_template_mandatory = CheckMandonly(tinyMCE.get('velsof_template_content_final').getContent());
    if (validate_template_mandatory != true) {
        error = true;
        $("textarea[name='velocity_email_template[content_drop]']").closest('div').find('.error_message').show();
        if (error_display9 < 1 && $("textarea[name='velocity_email_template[content_drop]']").closest('div').find('.error_message').length <= 0) {
            $("textarea[name='velocity_email_template[content_drop]']").after($('<p class="error_message"></p>'));
            error_display9++;
        }
        $('#mce_74').addClass('error_field');
        $('textarea[name="velocity_email_template[content_drop]"]').closest('div').find('.error_message').html(validate_template_mandatory);

    }
    else {
        $('#mce_74').removeClass('error_field');
        $('textarea[name="velocity_email_template[content_drop]"]').closest('div').find('.error_message').hide();
    }
    /*Knowband validation end*/



    if (error == true) {
        return false;
    } else {
        return true;
    }
}
var error_display6 = 0;
var error_display7 = 0;
function validation_email_initial_settings() {
    var error = false;
    /*Knowband validation start*/
    var validate_subject_mandatory = velovalidation.checkMandatory($('input[name="velocity_email_template[subject]"]'));
    if (validate_subject_mandatory != true) {
        error = true;
        $("input[name='velocity_email_template[subject]']").closest('div').find('.error_message').show();
        if (error_display6 < 1 && $("input[name='velocity_email_template[subject]']").closest('div').find('.error_message').length <= 0) {
            $('input[name="velocity_email_template[subject]"]').after($('<p class="error_message"></p>'));
            error_display6++;
        }
        $('input[name="velocity_email_template[subject]"]').addClass('error_field');
        $('input[name="velocity_email_template[subject]"]').closest('div').find('.error_message').html(validate_subject_mandatory);

    } else {
//        var validate_username = velovalidation.checkUsername($('input[name="velocity_email_template[subject]"]'));
        var validate_subject_tags = velovalidation.checkHtmlTags($('input[name="velocity_email_template[subject]"]'));
        if (validate_subject_tags != true) {
            error = true;
            $("input[name='velocity_email_template[subject]']").closest('div').find('.error_message').show();
            if (error_display6 < 1 && $("input[name='velocity_email_template[subject]']").closest('div').find('.error_message').length <= 0) {
                $('input[name="velocity_email_template[subject]"]').after($('<p class="error_message"></p>'));
                error_display6++;
            }
            $('input[name="velocity_email_template[subject]"]').addClass('error_field');
            $('input[name="velocity_email_template[subject]"]').closest('div').find('.error_message').html(validate_subject_tags);
        }
        else {
            $('input[name="velocity_email_template[subject]"]').removeClass('error_field');
            $('input[name="velocity_email_template[subject]"]').closest('div').find('.error_message').hide();
        }
    }

    /*Knowband validation end*/

    /*Knowband validation start*/
    var validate_template_mandatory = CheckMandonly(tinyMCE.get('velsof_template_content').getContent());
    if (validate_template_mandatory != true) {
        error = true;
        $("textarea[name='velocity_email_template[content]']").closest('div').find('.error_message').show();
        if (error_display7 < 1 && $("textarea[name='velocity_email_template[content]']").closest('div').find('.error_message').length <= 0) {
            $("textarea[name='velocity_email_template[content]']").after($('<p class="error_message"></p>'));
            error_display7++;
        }
        $('#mce_34').addClass('error_field');
        $('textarea[name="velocity_email_template[content]"]').closest('div').find('.error_message').html(validate_template_mandatory);

    }
    else {
        $('#mce_34').removeClass('error_field');
        $('textarea[name="velocity_email_template[content]"]').closest('div').find('.error_message').hide();
    }
    /*Knowband validation end*/



    if (error == true) {
        return false;
    } else {
        return true;
    }
}

function validation_email_low_stock_settings() {
    var error = false;
    /*Knowband validation start*/
    var validate_subject_mandatory = velovalidation.checkMandatory($('input[name="velocity_low_stock_alert_setting[subject]"]'));
    if (validate_subject_mandatory != true) {
        error = true;
        $("input[name='velocity_low_stock_alert_setting[subject]']").closest('div').find('.error_message').show();
        if (error_display6 < 1 && $("input[name='velocity_low_stock_alert_setting[subject]']").closest('div').find('.error_message').length <= 0) {
            $('input[name="velocity_low_stock_alert_setting[subject]"]').after($('<p class="error_message"></p>'));
            error_display6++;
        }
        $('input[name="velocity_low_stock_alert_setting[subject]"]').addClass('error_field');
        $('input[name="velocity_low_stock_alert_setting[subject]"]').closest('div').find('.error_message').html(validate_subject_mandatory);

    } else {
//        var validate_username = velovalidation.checkUsername($('input[name="velocity_low_stock_alert_setting[subject]"]'));
        var validate_subject_tags = velovalidation.checkHtmlTags($('input[name="velocity_low_stock_alert_setting[subject]"]'));
        if (validate_subject_tags != true) {
            error = true;
            $("input[name='velocity_low_stock_alert_setting[subject]']").closest('div').find('.error_message').show();
            if (error_display6 < 1 && $("input[name='velocity_low_stock_alert_setting[subject]']").closest('div').find('.error_message').length <= 0) {
                $('input[name="velocity_low_stock_alert_setting[subject]"]').after($('<p class="error_message"></p>'));
                error_display6++;
            }
            $('input[name="velocity_low_stock_alert_setting[subject]"]').addClass('error_field');
            $('input[name="velocity_low_stock_alert_setting[subject]"]').closest('div').find('.error_message').html(validate_subject_tags);
        }
        else {
            $('input[name="velocity_low_stock_alert_setting[subject]"]').removeClass('error_field');
            $('input[name="velocity_low_stock_alert_setting[subject]"]').closest('div').find('.error_message').hide();
        }
    }

    /*Knowband validation end*/

    /*Knowband validation start*/
    var validate_template_mandatory = CheckMandonly(tinyMCE.get('velocity_low_stock_alert_setting_template_content').getContent());
    if (validate_template_mandatory != true) {
        error = true;
        $("textarea[name='velocity_low_stock_alert_setting[content]']").closest('div').find('.error_message').show();
        if (error_display7 < 1 && $("textarea[name='velocity_low_stock_alert_setting[content]']").closest('div').find('.error_message').length <= 0) {
            $("textarea[name='velocity_low_stock_alert_setting[content]']").after($('<p class="error_message"></p>'));
            error_display7++;
        }
//        $('#mce_34').addClass('error_field');
        $('textarea[name="velocity_low_stock_alert_setting[content]"]').closest('div').find('.error_message').html(validate_template_mandatory);

    }
    else {
//        $('#mce_34').removeClass('error_field');
        $('textarea[name="velocity_low_stock_alert_setting[content]"]').closest('div').find('.error_message').hide();
    }
    /*Knowband validation end*/



    if (error == true) {
        return false;
    } else {
        return true;
    }
}

function validation_email_marketing_admin() {

    var error = false;
    $('.error_message').remove();
    /*Knowband validation end*/
    var email_marketing_tab = 0;
    if ($('[id^="back_stock_email[mailchimp_status]_on"]').is(':checked') === true) {
        //  alert('test');
        var mailchimp_api_mand = velovalidation.checkMandatory($("input[name='back_stock_email[mailchimp_api]']"));
        var list_val = $("[name='back_stock_email[mailchimp_list]']").val();
        if (mailchimp_api_mand !== true) {
            error = true;

            $("input[name='back_stock_email[mailchimp_api]']").addClass('error_field');
            $("input[name='back_stock_email[mailchimp_api]']").after($('<p class="mailchimp_api_mand error_message"></p>'));
            $('.mailchimp_api_mand').html(mailchimp_api_mand);
            email_marketing_tab = 1;
        } else if (list_val == 'no_list') {
            error = true;

            $("input[name='back_stock_email[mailchimp_api]']").addClass('error_field');
            $("input[name='back_stock_email[mailchimp_api]']").after($('<p class="mailchimp_api_mand error_message"></p>'));
            $('.mailchimp_api_mand').html(no_list_mailchimp);
            email_marketing_tab = 1;
        }
    }
    if ($('[id^="back_stock_email[klaviyo_status]_on"]').is(':checked') === true) {
        //  alert('test');
        var klaviyo_api_mand = velovalidation.checkMandatory($("input[name='back_stock_email[klaviyo_api]']"));
        var list_val_ka = $("[name='back_stock_email[klaviyo_list]']").val();
        if (klaviyo_api_mand !== true) {
            error = true;

            $("input[name='back_stock_email[klaviyo_api]']").addClass('error_field');
            $("input[name='back_stock_email[klaviyo_api]']").after($('<p class="klaviyo_api_mand error_message"></p>'));
            $('.klaviyo_api_mand').html(klaviyo_api_mand);
            email_marketing_tab = 1;
        } else if (list_val_ka == 'no_list') {
            error = true;

            $("input[name='back_stock_email[klaviyo_api]']").addClass('error_field');
            $("input[name='back_stock_email[klaviyo_api]']").after($('<p class="klaviyo_api_mand error_message"></p>'));
            $('.klaviyo_api_mand').html(no_list_mailchimp);
            email_marketing_tab = 1;
        }
    }
    if ($('[id^="back_stock_email[SendinBlue_status]_on"]').is(':checked') === true) {
        //  alert('test');
        var getresponse_api_mand = velovalidation.checkMandatory($("input[name='back_stock_email[SendinBlue_api]']"));
        var list_val = $("[name='back_stock_email[SendinBlue_list]']").val();
        if (getresponse_api_mand !== true) {
            error = true;

            $("input[name='back_stock_email[SendinBlue_api]']").addClass('error_field');
            $("input[name='back_stock_email[SendinBlue_api]']").after($('<p class="SendinBlue_api error_message"></p>'));
            $('.SendinBlue_api').html(getresponse_api_mand);
            email_marketing_tab = 1;
        } else if (list_val == 'no_list') {
            error = true;

            $("input[name='back_stock_email[SendinBlue_api]']").addClass('error_field');
            $("input[name='back_stock_email[SendinBlue_api]']").after($('<p class="SendinBlue_api error_message"></p>'));
            $('.SendinBlue_api').html(no_list_mailchimp);
            email_marketing_tab = 1;
        }
    }
    if (error == true) {
        return false;
    } else {
        return true;
    }

}

function CheckMandonly(val) {
    var val = val.trim();
    var return_val = true;
    if (val == '') {
        return_val = empty_field;
    }
    return return_val;

}