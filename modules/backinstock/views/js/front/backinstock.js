/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2015 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */
var validation_fields = {
        'isGenericName': /^[^<>={}]*$/,
        'isAddress': /^[^!<>?=+@{}_$%]*$/,
        'isPhoneNumber': /^[+0-9. ()-]*$/,
        'isInt': /^[0-9]*$/,
        'isIntExcludeZero': /^[1-9]*$/,
        'isPrice': /^[0-9]*(?:\.\d{1,6})?$/,
        'isPriceExcludeZero': /^[1-9]*(?:\.\d{1,6})?$/,
        'isDate': /^([0-9]{4})-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/,
        'isUrl': /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi,
        'isEmail': /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
};

function validate_isName(s)
{
	var reg = /^[^0-9!<>,;?=+()@#"�{}_$%:]+$/;
	return reg.test(s);
}

function validate_isGenericName(s)
{
	var reg = /^[^<>={}]+$/;
	return reg.test(s);
}

function validate_isAddress(s)
{
	var reg = /^[^!<>?=+@{}_$%]+$/;
	return reg.test(s);
}

function validate_isPostCode(s, pattern, iso_code)
{
	if (typeof iso_code === 'undefined' || iso_code == '')
		iso_code = '[A-Z]{2}';
	if (typeof(pattern) == 'undefined' || pattern.length == 0)
		pattern = '[a-zA-Z 0-9-]+';
	else
	{
		var replacements = {
			' ': '(?:\ |)',
			'-': '(?:-|)',
			'N': '[0-9]',
			'L': '[a-zA-Z]',
			'C': iso_code
		};

		for (var new_value in replacements)
			pattern = pattern.split(new_value).join(replacements[new_value]);
	}
	var reg = new RegExp('^' + pattern + '$');
	return reg.test(s);
}

function validate_isMessage(s)
{
	var reg = /^[^<>{}]+$/;
	return reg.test(s);
}

function validate_isPhoneNumber(s)
{
	var reg = /^[+0-9. ()-]+$/;
	return reg.test(s);
}

function validate_isDniLite(s)
{
	var reg = /^[0-9a-z-.]{1,16}$/i;
	return reg.test(s);
}

function validate_isCityName(s)
{
	var reg = /^[^!<>;?=+@#"�{}_$%]+$/;
	return reg.test(s);
}

function validate_isEmail(s)
{
	var reg = /^[a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z\p{L}0-9]+$/i;
	return reg.test(s);
}

function validate_isPasswd(s)
{
	return (s.length >= 5 && s.length < 255);
}

function kbValidateField(element)
{
    if (element.attr('data-validate') == 'isName') {
        return validate_isName(element.val());
    } else if (element.attr('data-validate') == 'isGenericName') {
          return validate_isGenericName(element.val());
    } else if (element.attr('data-validate') == 'isAddress') {
          return validate_isAddress(element.val());
    } else if (element.attr('data-validate') == 'isPostCode') {
          return validate_isPostCode(element.val());
    } else if (element.attr('data-validate') == 'isCityName') {
          return validate_isCityName(element.val());
    } else if (element.attr('data-validate') == 'isMessage') {
          return validate_isMessage(element.val());
    } else if (element.attr('data-validate') == 'isPhoneNumber') {
          return validate_isPhoneNumber(element.val());
    } else if (element.attr('data-validate') == 'isDniLite') {
          return validate_isDniLite(element.val());
    } else if (element.attr('data-validate') == 'isEmail') {
         return validate_isEmail(element.val());
    } else if (element.attr('data-validate') == 'isPasswd') {
         return validate_isPasswd(element.val());
    }
}
function save_subscribe_data_back()
{
    if ($('#kb_proupdate_back_privacy_text').length) {
        if (!document.getElementById("kb_proupdate_back_privacy_text").checked) {
            alert(backinstock_privacy_accept_error);
            return false;
        }
    }
    //changes by gopi for custom feilds validation
    error_kb = false;
    $('.error_message1').remove();
    $('.error_message').hide();
    if($('#user_quantity_subscribe_back').val()<0){
        error_kb = true;
        $(this).addClass('error_field');
        $('#user_quantity_subscribe_back').after('<p class="error_message1">' + negative_quantity_error + '</p>');
    }
    $('input[type=email]').each(function () {
        $(this).removeClass('error_field');
        if ($(this).hasClass('kbfield')) {
            if ($(this).hasClass('is_required')) {
                var min = $(this).attr('minlength');
                var max = $(this).attr('maxlength');
                var input_mand = velovalidation.checkMandatory($(this), max, min);
                if (input_mand != true) {
                    error_kb = true;
                    $(this).addClass('error_field');
                    if ($(this).closest('.form-group').find('.error_message').length) {
                        $(this).closest('.form-group').find('.error_message').show();
                    } else {
                        $(this).after('<p class="error_message1">' + input_mand + '</p>');

                    }
                } else {
                    if ($(this).attr('data-validate')) {
                        if ($(this).val().trim() != '') {
                            if (!kbValidateField($(this))) {
                                error_kb = true;
                                $(this).addClass('error_field');
                                if ($(this).closest('.form-group').find('.error_message').length) {
                                    $(this).closest('.form-group').find('.error_message').show();
                                } else {
                                    $(this).after('<p class="error_message1">' + kb_not_valid + '</p>');
                                }
                            } else {
                                $(this).closest('.form-group').find('.error_message').hide();
                            }
                        }
                    } else {
                        $(this).closest('.form-group').find('.error_message').hide();
                    }
                }
            } else {
                if ($(this).attr('data-validate')) {
                    if ($(this).val().trim() != '') {
                        if (!kbValidateField($(this))) {
                            error_kb = true;
                            $(this).addClass('error_field');
                            if ($(this).closest('.form-group').find('.error_message').length) {
                                $(this).closest('.form-group').find('.error_message').show();
                            } else {
//                                if (is_error2 < 1) {
                                $(this).after('<p class="error_message1">' + kb_not_valid + '</p>');
//                                    is_error2++;
//                                }
                            }
                        } else {
                            $(this).closest('.form-group').find('.error_message').hide();
                        }
                    }
                } else {
                    $(this).closest('.form-group').find('.error_message').hide();
                }
            }
        }
    });


    $('input[type=text]').each(function () {
        $(this).removeClass('error_field');
        if ($(this).hasClass('kbfield')) {
            if ($(this).hasClass('is_required')) {
                var min = $(this).attr('minlength');
                var max = $(this).attr('maxlength');
                var input_mand = velovalidation.checkMandatory($(this), max, min);
                if (input_mand != true) {
                    error_kb = true;
                    $(this).addClass('error_field');
                    if ($(this).closest('.form-group').find('.error_message').length) {
                        $(this).closest('.form-group').find('.error_message').show();
                    } else {
                        $(this).after('<p class="error_message1">' + input_mand + '</p>');

                    }
                } else {
                    if ($(this).attr('data-validate')) {
                        if ($(this).val().trim() != '') {
                            if (!kbValidateField($(this))) {
                                error_kb = true;
                                $(this).addClass('error_field');
                                if ($(this).closest('.form-group').find('.error_message').length) {
                                    $(this).closest('.form-group').find('.error_message').show();
                                } else {
                                    $(this).after('<p class="error_message1">' + kb_not_valid + '</p>');
                                }
                            } else {
                                $(this).closest('.form-group').find('.error_message').hide();
                            }
                        }
                    } else if (!$(this).hasClass('kbfielddatetime') && $(this).hasClass('hasDatepicker')) {
                        var date_valid = velovalidation.checkDateddmmyy($(this));
                        if (date_valid != true) {
                            $(this).addClass('error_field');
                            if ($(this).closest('.form-group').find('.error_message').length) {
                                $(this).closest('.form-group').find('.error_message').show();
                            } else {
                                $(this).after('<p class="error_message1">' + date_valid + '</p>');
                            }
                        } else {
                            $(this).closest('.form-group').find('.error_message').hide();
                        }
                    } else {
                        $(this).closest('.form-group').find('.error_message').hide();
                    }
                }
            } else {
                //start by dharmanshu 20-08-2021 for validation issue
                var data_value = $(this).val();
                if ($(this).attr('data-validate') && data_value.trim()!='' ) {
                    if ($(this).val().trim() != '') {
                        if (!kbValidateField($(this))) {
                            error_kb = true;
                            $(this).addClass('error_field');
                            if ($(this).closest('.form-group').find('.error_message').length) {
                                $(this).closest('.form-group').find('.error_message').show();
                            } else {
//                                if (is_error2 < 1) {
                                $(this).after('<p class="error_message1">' + kb_not_valid + '</p>');
//                                    is_error2++;
//                                }
                            }
                        } else {
                            $(this).closest('.form-group').find('.error_message').hide();
                        }
                    }
                } else {
                    $(this).closest('.form-group').find('.error_message').hide();
                }
                //end by dharmanshu 20-08-2021
            }
        }
    });

    $('textarea').each(function () {
        $(this).removeClass('error_field');
        if ($(this).hasClass('kbfield')) {
            if ($(this).hasClass('is_required')) {
                var min = $(this).attr('minlength');
                var max = $(this).attr('maxlength');
                var input_mand = velovalidation.checkMandatory($(this), max, min);
                if (input_mand != true) {
                    error_kb = true;
                    $(this).addClass('error_field');
                    if ($(this).closest('.form-group').find('.error_message').length) {
                        $(this).closest('.form-group').find('.error_message').show();
                    } else {
                        $(this).after('<p class="error_message1">' + input_mand + '</p>');

                    }
                } else {
                    if ($(this).attr('data-validate')) {
                        if (!kbValidateField($(this))) {
                            error_kb = true;
                            $(this).addClass('error_field');
                            if ($(this).closest('.form-group').find('.error_message').length) {
                                $(this).closest('.form-group').find('.error_message').show();
                            } else {
                                $(this).after('<p class="error_message1">' + kb_not_valid + '</p>');
                            }
                        } else {
                            $(this).closest('.form-group').find('.error_message').hide();
                        }
                    } else {
                        $(this).closest('.form-group').find('.error_message').hide();
                    }
                }
            } else {
                //start by dharmanshu 20-08-2021 for validation issue
                var data_value = $(this).val();
                if ($(this).attr('data-validate') && data_value.trim()!='' ) {
                    if (!kbValidateField($(this))) {
                        error_kb = true;
                        $(this).addClass('error_field');
                        if ($(this).closest('.form-group').find('.error_message').length) {
                            $(this).closest('.form-group').find('.error_message').show();
                        } else {
//                                if (is_error2 < 1) {
                            $(this).after('<p class="error_message1">' + kb_not_valid + '</p>');
//                                    is_error2++;
//                                }
                        }
                    } else {
                        $(this).closest('.form-group').find('.error_message').hide();
                    }
                } else {
                    $(this).closest('.form-group').find('.error_message').hide();
                }
                //end by dharmanshu 20-08-2021
            }
        }
    });

    $('select').closest('.col-md-6').each(function () {
        $(this).find('select').removeClass('error_field');
        if ($(this).find('select').hasClass('kbfield')) {
            if ($(this).find('select').hasClass('is_required')) {
                if ($(this).find('select').val() == "null") {
                    error_kb = true;
                    $(this).find('select').addClass('error_field');
                    $(this).find('select').after('<span class="error_message1">' + field_not_empty + '</span>');
                }
            }
        }
    });

    $('input[type="radio"]').closest('.col-md-12').each(function () {
        $(this).removeClass('error_field');
        $(this).find('.radio_kb_validate').removeAttr('style');
        if ($(this).find('input:radio').hasClass('kbfield')) {
            if ($(this).find('input:radio').hasClass('is_required')) {
                if ($(this).find('input:radio:checked').length == 0) {
                    error_kb = true;
                    $(this).find('.radio_kb_validate').attr('style', 'border: 1px solid red;margin-bottom: 10px;padding: 5px;');
                    $(this).find('.radio_kb_validate').parent().parent().after('<span class="error_message1">' + field_not_empty + '</span>');
                }
            }
        }
    });
//    var validate_email
    var email=document.getElementById("user_email_subscribe_back").value;
    var combi_id = $('#idCombination').val();
    $('#pal_attribute_id_back').val(combi_id);

    /*Knowband validation start*/
    validate_email = velovalidation.checkEmail($('#user_email_subscribe_back'));
    validate_email_mand = velovalidation.checkMandatory($('#user_email_subscribe_back'));
    if (validate_email_mand != true) {
        $('#email_error_back').show();
        $('#email_error_back').html(validate_email_mand);
        $('#user_email_subscribe_back').css('border','1px solid #f3515c');
        error_occurred = true;
    }
    if ((validate_email != true)) {
         $('#email_error_back').show();
        $('#email_error_back').html(validate_email);
        $('#user_email_subscribe_back').css('border','1px solid #f3515c');
        error_occurred = true;
    }
    if(validate_email_mand == true && validate_email == true) {
         //by dharmanshu for validation issue fix 21-08-2021
         $('#email_error_back').hide();
        //$('#email_error_back').html('');
        $('#user_email_subscribe_back').css('border','1px solid #d6d4d4');
    }
    if (error_kb) {
        error_occurred = true;
        return false;
    }
    if(validate_email_mand == true && validate_email == true) {
         //end by dharmanshu for validation issue fix 21-08-2021
         $('#email_error_back').hide();
        //$('#email_error_back').html('');
        $('#user_email_subscribe_back').css('border','1px solid #d6d4d4');
    /*Knowband validation end*/

    /*Knowband button validation start*/
        var btn = $('.save_subscribe_back');
        btn.attr('disabled',true);
        /*Knowband button validation end*/
        $.ajax({
            type: "POST",
            url: action_product_front_back,
            data: $("#subscribe_form_back").serialize() +'&'+ $("#buy_block").serialize(),
            beforeSend: function() {
               $('#loading_image_back').show();
               document.getElementById("email_error_back").innerHTML='';
               document.getElementById("email_error_back").style.display='none';
            },
            success: function(res) {
                $(block_id).addClass('pal_success_row_back');
                let resp = $.parseJSON(res);
                if (resp['status'] == 1)
                {
                    document.getElementById("email_error_back").innerHTML=pal_alert_create_success_msg;
                    document.getElementById("email_error_back").style.display='block';
                    document.getElementById("email_error_back").style.color='green';
                    //$(block_id).html(pal_alert_create_success_msg);
                    //$('#product_update_block_back #pal_title_row_stock_back').css('background','green');
		            $('#loading_image_back').css("display","none");
                }
                else if (resp['status'] == 0)
                {
                    document.getElementById("email_error_back").innerHTML=pal_alert_update_success_msg;
                    //document.getElementById("user_email_subscribe").style.border='2px solid #f3515c';
                    document.getElementById("email_error_back").style.display='block';
                    document.getElementById("email_error_back").style.color='red';
                    //$(block_id).html(pal_alert_update_success_msg);
                    //$('#product_update_block_back .product_update_block_back').css('background','red');
                    $('#loading_image_back').css("display", "none");
                }
                else if (resp['status'] == 2)//chaanges by gopi 
                {
                    document.getElementById("email_error_back").innerHTML = pal_alert_update_recaptcha_msg;
                    document.getElementById("email_error_back").style.display = 'block';
                    document.getElementById("email_error_back").style.color = 'red';
                    $('#loading_image_back').css("display", "none"); 
                }  else if (resp['status'] == 3)
                {
                    /*
                     * Added a new condition for error message in case of data is not saved in database
                     * @author Prvind Panday
                     * @date 26-01-2023
                     * @commenter Prvind Panday
                     */
                    document.getElementById("email_error_back").innerHTML = pal_alert_update_error_msg;
                    document.getElementById("email_error_back").style.display = 'block';
                    document.getElementById("email_error_back").style.color = 'red';
                    $('#loading_image_back').css("display", "none"); 
                }
                $('#product_update_popup_back').slideUp("slow");
                $('#arrow_update_back').hide();
            },
            complete: function () {
                $('input[name="kb_proupdate_back_privacy_text"]').trigger('click');
                btn.removeAttr('disabled');
            }
        });
    }
    return false;
}
var quantityAvailable;

function setErrorLanguage() {
    velovalidation.setErrorLanguage({
        empty_fname: empty_fname,
        minchar_fname: minchar_fname,
        empty_mname: empty_mname,
        maxchar_mname: maxchar_mname,
        maxchar_fname: maxchar_fname,
        minchar_mname: minchar_mname,
        only_alphabet: only_alphabet,
        empty_lname: empty_lname,
        maxchar_lname: maxchar_lname,
        minchar_lname: minchar_lname,
        alphanumeric: alphanumeric,
        empty_pass: empty_pass,
        maxchar_pass: maxchar_pass,
        minchar_pass: minchar_pass,
        specialchar_pass: specialchar_pass,
        alphabets_pass: alphabets_pass,
        capital_alphabets_pass: capital_alphabets_pass,
        small_alphabets_pass: small_alphabets_pass,
        digit_pass: digit_pass,
        empty_field: empty_field,
        number_field: number_field,
        positive_number: positive_number,
        maxchar_field: maxchar_field,
        minchar_field: minchar_field,
        empty_email: empty_email,
        validate_email: validate_email,
        empty_country: empty_country,
        maxchar_country: maxchar_country,
        minchar_country: minchar_country,
        empty_city: empty_city,
        maxchar_city: maxchar_city,
        minchar_city: minchar_city,
        empty_state: empty_state,
        maxchar_state: maxchar_state,
        minchar_state: minchar_state,
        empty_proname: empty_proname,
        maxchar_proname: maxchar_proname,
        minchar_proname: minchar_proname,
        empty_catname: empty_catname,
        maxchar_catname: maxchar_catname,
        minchar_catname: minchar_catname,
        empty_zip: empty_zip,
        maxchar_zip: maxchar_zip,
        minchar_zip: minchar_zip,
        empty_username: empty_username,
        maxchar_username: maxchar_username,
        minchar_username: minchar_username,
        invalid_date: invalid_date,
        maxchar_sku: maxchar_sku,
        minchar_sku: minchar_sku,
        invalid_sku: invalid_sku,
        empty_sku: empty_sku,
        validate_range: validate_range,
        empty_address: empty_address,
        minchar_address: minchar_address,
        maxchar_address: maxchar_address,
        empty_company: empty_company,
        minchar_company: minchar_company,
        maxchar_company: maxchar_company,
        invalid_phone: invalid_phone,
        empty_phone: empty_phone,
        minchar_phone: minchar_phone,
        maxchar_phone: maxchar_phone,
        empty_brand: empty_brand,
        maxchar_brand: maxchar_brand,
        minchar_brand: minchar_brand,
        empty_shipment: empty_shipment,
        maxchar_shipment: maxchar_shipment,
        minchar_shipment: minchar_shipment,
        invalid_ip: invalid_ip,
        invalid_url: invalid_url,
        empty_url: empty_url,
        valid_amount: valid_amount,
        valid_decimal: valid_decimal,
        max_email: max_email,
        specialchar_zip: specialchar_zip,
        specialchar_sku: specialchar_sku,
        max_url: max_url,
        valid_percentage: valid_percentage,
        between_percentage: between_percentage,
        maxchar_size: maxchar_size,
        specialchar_size: specialchar_size,
        specialchar_upc: specialchar_upc,
        maxchar_upc: maxchar_upc,
        specialchar_ean: specialchar_ean,
        maxchar_ean: maxchar_ean,
        specialchar_bar: specialchar_bar,
        maxchar_bar: maxchar_bar,
        positive_amount: positive_amount,
        maxchar_color: maxchar_color,
        invalid_color: invalid_color,
        specialchar: specialchar,
        script: script,
        style: style,
        iframe: iframe,
        not_image: not_image,
        image_size: image_size,
        html_tags: html_tags,
        number_pos: number_pos,
        invalid_separator: invalid_separator

});
}

function showSection(show_box) {
    if (show_box == 1)
    {
        $('.product_update_block_back').show();
        $('#pal_title_row_price').hide();
        $('#subscribe_type_back').val("quantity");
        block_id = ".product_update_block_back";

    }
    else
    {
        $('.product_update_block_back').hide();
        $('#pal_title_row_price').show();
        $('#subscribe_type_back').val("price");
        block_id = "#pal_title_row_price";
    }
    $(document).on('change', '.attribute_select', function (e) {
        if (quantityAvailable == 0)
        {
            $('.product_update_block_back').show();
            $('#pal_title_row_price').hide();
            $('#subscribe_type_back').val("quantity");
            block_id = ".product_update_block_back";
        }
        else
        {
            $('.product_update_block_back').hide();
            $('#pal_title_row_price').show();
            $('#subscribe_type_back').val("price");
            block_id = "#pal_title_row_price";
        }

    });
    $(document).on('click', '.color_pick', function (e) {
        findCombination();
        if (quantityAvailable == 0)
        {
            $('.product_update_block_back').show();
            $('#pal_title_row_price').hide();
            $('#subscribe_type_back').val("quantity");
            block_id = ".product_update_block_back";
        }
        else
        {
            $('.product_update_block_back').hide();
            $('#pal_title_row_price').show();
            $('#subscribe_type_back').val("price");
            block_id = "#pal_title_row_price";
        }

    });
    $(document).on('click', '.attribute_radio', function (e) {
        if (quantityAvailable == 0)
        {
            $('.product_update_block_back').show();
            $('#pal_title_row_price').hide();
            $('#subscribe_type_back').val("quantity");
            block_id = ".product_update_block_back";
        }
        else
        {
            $('.product_update_block_back').hide();
            $('#pal_title_row_price').show();
            $('#subscribe_type_back').val("price");
            block_id = "#pal_title_row_price";
        }

    });
    $('#user_email_subscribe_back').keypress(function (e) {
        if (e.which == 13) {
            e.preventDefault();
            //do something
        }
    });
}
$("#show_Update_popup").on('click', function (e) {
    var combi_id = $('#idCombination').val();
    $('#pal_attribute_id_back').val(combi_id);
    if ($('#product_update_popup_back').is(':visible'))
    {
        $('#product_update_popup_back').slideUp("fast");
        $('#arrow_update_back').hide();
        e.stopPropagation();
    }
    else
    {
        $('#arrow_update_back').show();
        $('#product_update_popup_back').slideDown("slow");
        e.stopPropagation();
    }
});

$("#product_update_popup_back").click(function (e) {
    e.stopPropagation();
    return false;
});

$(document).click(function () {
    $('#product_update_popup_back').slideUp('fast');
    $('#arrow_update_back').hide();
});

function disableBuyNowButton()
{
    if ($('.add-to-cart').is(":disabled")) {
        var quantityAvailable = 0;
        showSection(quantityAvailable);
    } else {
        var quantityAvailable = 1;
        showSection(quantityAvailable);
    }
}
//start by prvind for fixing errorlanguage not defined issue 
$(document).ready(function(){
    setTimeout(function(){
      disableBuyNowButton();  
    },1000)
    if(typeof empty_fname != 'undefined') {
        setTimeout(function(){
            setErrorLanguage();  
          },3000);
    }
});
$(document).ajaxComplete(function(){
    setTimeout(function(){
      disableBuyNowButton();  
    },1000);
    if(typeof empty_fname != 'undefined') {
        setTimeout(function(){
            setErrorLanguage();  
          },3000);
    }
})
//changes end