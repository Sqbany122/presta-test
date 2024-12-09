/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

$(document).ready(function() {
    
    $('button[name="submitAddkb_bis_fields"]').click(function() {
        var is_error = 0;
        var is_error1 = 0;
        var is_error2 = 0;
        var is_error3 = 0;
        var is_error4 = 0;
        var is_error5 = 0;
        var is_error6 = 0;
        var is_error7 = 0;
        var error = false;
        $('.error_message').remove();
        $('input[name^="label_"]').removeClass('error_field');
        $('input[name^="description_"]').removeClass('error_field');
        $('input[name^="placeholder_"]').removeClass('error_field');
        $('input[name^="error_msg_"]').removeClass('error_field');
        $('input[name="field_name"]').removeClass('error_field');
        $('input[name="html_class"]').removeClass('error_field');
        $('input[name="html_id"]').removeClass('error_field');
        $('input[name="min_length"]').removeClass('error_field');
        $('input[name="max_length"]').removeClass('error_field');
        $('textarea[name^="value_"]').removeClass('error_field');
        $('input[name^="default_value_"]').removeClass('error_field');
        $('input[name="file_extension"]').removeClass('error_field');
        
        $('input[name^="label_"]').each(function () {
            var label_error = velovalidation.checkMandatory($(this));
            if (label_error != true) {
                error = true;
                if (is_error < 1) {
                    $(this).parents('.col-lg-4').append('<span class="error_message">' + label_error + ' ' + check_for_all + '</span>');
                    is_error++;
                }
                $(this).addClass('error_field');
            }
        });
        
        var desc_value = '';
        $('input[name^="description_"]').each(function () {
            if ($(this).val().trim() != '') {
                desc_value = 'hasValue';
            }
        });

        $('input[name^="description_"]').each(function () {
            var desc_error = velovalidation.checkHtmlTags($(this));
            if (desc_value == 'hasValue') {
                var desc_mand = velovalidation.checkMandatory($(this));
                if (desc_mand != true) {
                    error = true;
                     if (is_error1 < 1) {
                        $(this).parents('.col-lg-4').append('<span class="error_message">' + desc_mand + ' ' + check_for_all + '</span>');
                        is_error1++;
                    }
                    $(this).addClass('error_field');
                } else if (desc_error != true) {
                    error = true;
                     if (is_error2 < 1) {
                        $(this).parents('.col-lg-4').append('<span class="error_message">' + desc_error + ' ' + check_for_all + '</span>');
                        is_error2++;
                    }
                    $(this).addClass('error_field');
                }
            }
        });
        
        var place_value = '';
        $('input[name^="placeholder_"]').each(function () {
            if ($(this).val().trim() != '') {
                place_value = 'hasValue';
            }
        });
        $('input[name^="placeholder_"]').each(function () {
            var place_error = velovalidation.checkHtmlTags($(this));
            if (place_value == 'hasValue') {
                var place_mand = velovalidation.checkMandatory($(this));
                if (place_mand != true) {
                    error = true;
                     if (is_error3 < 1) {
                        $(this).parents('.col-lg-4').append('<span class="error_message">' + place_mand + ' ' + check_for_all + '</span>');
                        is_error3++;
                    }
                    $(this).addClass('error_field');
                } else if (place_error != true) {
                    error = true;
                    if (is_error < 7) {
                        $(this).parents('.col-lg-4').append('<span class="error_message">' + place_error + ' ' + check_for_all + '</span>');
                        is_error7++;
                    }
                    $(this).addClass('error_field');
                }
            }
        });
        
        var field_name_err = velovalidation.checkMandatory($('input[name="field_name"]'));
        if (field_name_err != true) {
            error = true;
            $('input[name="field_name"]').after('<span class="error_message">' + field_name_err + '</span>');
            $('input[name="field_name"]').addClass('error_field');
        }
        
        if ($('input[name^="error_msg_"]').parents('.form-group').is(":visible")) {
            var error_val = '';
            $('input[name^="error_msg_"]').each(function () {
                if ($(this).val().trim() != '') {
                    error_val = 'hasValue';
                }
            });
            $('input[name^="error_msg_"]').each(function () {
                  if (error_val == 'hasValue') {
                    var error_msg = velovalidation.checkMandatory($(this));
                    if (error_msg != true) {
                        error = true;
                        if (is_error4 < 1) {
                            $(this).parents('.col-lg-4').append('<span class="error_message">' + error_msg + ' ' + check_for_all + '</span>');
                            is_error4++;
                        }
                        $(this).addClass('error_field');
                    }
                }
            });
        }
        var html_id_err = velovalidation.checkMandatory($('input[name="html_id"]'));
        if (html_id_err != true) {
            error = true;
            $('input[name="html_id"]').after('<span class="error_message">' + html_id_err + '</span>');
            $('input[name="html_id"]').addClass('error_field');
        }
        var html_class_err = velovalidation.checkMandatory($('input[name="html_class"]'));
        if (html_class_err != true) {
            error = true;
            $('input[name="html_class"]').after('<span class="error_message">' + html_class_err + '</span>');
            $('input[name="html_class"]').addClass('error_field');
        }
        if ($('input[name="min_length"]').is(":visible")) {
            if ($('input[name="min_length"]').val() != "") {
                var max = parseInt($('input[name="max_length"]').val().trim());
                var min = parseInt($('input[name="min_length"]').val().trim());
                var is_numberic_min = velovalidation.isNumeric($('input[name="min_length"]'));
                if (is_numberic_min != true) {
                    error = true;
                    $('input[name="min_length"]').addClass('error_field');
                    $('input[name="min_length"]').after('<span class="error_message">' + is_numberic_min + '</span>');
                }
            }
        }
        if ($('input[name="max_length"]').is(":visible")) {
            if ($('input[name="max_length"]').val() != '') {
                var max = parseInt($('input[name="max_length"]').val().trim());
                var min = parseInt($('input[name="min_length"]').val().trim());
                var is_numberic_max = velovalidation.isNumeric($('input[name="max_length"]'));
                var is_mand_m
                if (max > 50) {
                    error = true;
                    $('input[name="max_length"]').addClass('error_field');
                    $('input[name="max_length"]').after('<span class="error_message">' + kb_max_limit_exceed + '</span>');
                } else if (is_numberic_max != true) {
                    error = true;
                    $('input[name="max_length"]').addClass('error_field');
                    $('input[name="max_length"]').after('<span class="error_message">' + is_numberic_max + '</span>');
                }  else {
                    if (max >= 0) {
                        if (max <= min) {
                            error = true;
                            $('input[name="max_length"]').addClass('error_field');
                            $('input[name="max_length"]').after('<span class="error_message">' + maximum_length_excced + '</span>');
                        }
                    }
                }
            }
        }
        
        value_valu = '';
        if (($('.kb_custom_field_form input[name="type"]').val() == 'select') ||
            ($('.kb_custom_field_form input[name="type"]').val() == 'checkbox') ||
            ($('.kb_custom_field_form input[name="type"]').val() == 'radio')) {
            if ($('textarea[name^="value_"]').parents('.form-group').is(":visible")) {
                $('textarea[name^="value_"]').each(function () {
                    if ($(this).val().trim() != '') {
                        value_valu = 'hasValue';
                    }
                });
                $('textarea[name^="value_"]').each(function () {
//                if (value_valu == 'hasValue') {
                    var value_err = velovalidation.checkMandatory($(this), 1000, 0);
                    if (value_err != true) {
                        error = true;
                        if (is_error5 < 1) {
                            $(this).parents('.col-lg-5').append('<span class="error_message">' + value_err + ' ' + check_for_all + '</span>');
                            is_error5++;
                        }
                        $(this).addClass('error_field');
                    }
//                }
                });
            }
        }
    
        
        var default_value = '';
        if (($('.kb_custom_field_form input[name="type"]').val() == 'select') ||
            ($('.kb_custom_field_form input[name="type"]').val() == 'checkbox') ||
            ($('.kb_custom_field_form input[name="type"]').val() == 'radio')) {
        if ($('input[name^="default_value_"]').parents('.form-group').is(":visible")) {
            $('input[name^="default_value_"]').each(function () {
                if ($(this).val().trim() != '') {
                    default_value = 'hasValue';
                }
            });
            $('input[name^="default_value_"]').each(function () {
                if (default_value == 'hasValue') {
                    var default_err = velovalidation.checkMandatory($(this), 1000, 0);
                    if (default_err != true) {
                        error = true;
                         if (is_error6 < 1) {
                            $(this).parents('.col-lg-5').append('<span class="error_message">' + default_err + ' ' + check_for_all + '</span>');
                            is_error6++;
                        }
                        $(this).addClass('error_field');
                    }
                }
            });
        }
    }
        
       
        if ($('input[name="file_extension"]').parents('.form-group').is(":visible")) {
            var ext_mand = velovalidation.checkMandatory($('input[name="file_extension"]'));
            var ext_comma = velovalidation.checkCommaSeparateValue($('input[name="file_extension"]'));
            if (ext_mand != true) {
                error = true;
                $('input[name="file_extension"]').after('<span class="error_message">' + ext_mand + '</span>');
                $('input[name="file_extension"]').addClass('error_field');
            } else {
                if (ext_comma != true) {
                    error = true;
                    $('input[name="file_extension"]').after('<span class="error_message">' + ext_comma + '</span>');
                    $('input[name="file_extension"]').addClass('error_field');
                }
            }
        }
        
        if (error) {
            $('html, body').animate({
                scrollTop: $(".error_message").offset().top-200
            }, 1000);
            return false;
        }
        
        
         if (error) {
            return false;
        } else {
            /*Knowband button validation start*/
                $('button[name="submitAddkb_bis_fields"]').attr('disabled', 'disabled');
                $('#kbcf_add_custom_field').submit();
            /*Knowband button validation end*/
        }
    });
    
});