/*
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7040 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function updateCarrierList(json)
{
    var html = json.carrier_block;
    
    // @todo  check with theme 1.4
    //if ($('#HOOK_EXTRACARRIER').length == 0 && json.HOOK_EXTRACARRIER !== null && json.HOOK_EXTRACARRIER != undefined)
    //	html += json.HOOK_EXTRACARRIER;
    
    $('#carrier_area').replaceWith(html);
    bindInputs();
    /* update hooks for carrier module */
    $('#HOOK_BEFORECARRIER').html(json.HOOK_BEFORECARRIER);
}

function updatePaymentMethods(json)
{
    $('#HOOK_TOP_PAYMENT').html(json.HOOK_TOP_PAYMENT);
    $('#opc_payment_methods-content #HOOK_PAYMENT').html(json.HOOK_PAYMENT);
    $("#HOOK_PAYMENT .col-xs-12").each(function( index ) {
        $(this).removeClass('col-md-6');
    });
    $('.warning').addClass('alert alert-warning').removeClass('warning');
}

function updateAddressSelection()
{
	//console.log('updateAddressSelection');
    var idAddress_delivery = ($('input#opc_id_address_delivery').length == 1 ? $('input#opc_id_address_delivery').val() : $('#id_address_delivery').val());
    var idAddress_invoice = ($('input#opc_id_address_invoice').length == 1 ? $('input#opc_id_address_invoice').val() : ($('input[type=checkbox]#addressesAreEquals:checked').length == 1 ? idAddress_delivery : ($('#id_address_invoice').length == 1 ? $('select#id_address_invoice').val() : idAddress_delivery)));
    
    $('#opc_account-overlay').fadeIn('slow');
    $('#opc_delivery_methods-overlay').fadeIn('slow');
    $('#opc_payment_methods-overlay').fadeIn('slow');
    
    $.ajax({
        type: 'POST',
        url: orderOpcUrl,
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=updateAddressesSelected&id_address_delivery=' + idAddress_delivery + '&id_address_invoice=' + idAddress_invoice + '&token=' + static_token,
        success: function(jsonData)
        {
            if (jsonData.hasError)
            {
                var errors = '';
                for(error in jsonData.errors)
                    //IE6 bug fix
                    if(error != 'indexOf')
                        errors += jsonData.errors[error] + "\n";
                alert(errors);
            }
            else
            {
                // Update all product keys with the new address id

                // Update global var deliveryAddress
                deliveryAddress = idAddress_delivery;

                $('#cart_summary .address_'+deliveryAddress).each(function() {
                    $(this)
                        .removeClass('address_'+deliveryAddress)
                        .addClass('address_'+idAddress_delivery);
                    
                    if ($(this).find('.cart_unit span').length > 0 && $(this).find('.cart_unit span').attr('id').length > 0)
                        $(this).find('.cart_unit span').attr('id', $(this).find('.cart_unit span').attr('id').replace(/_\d+$/, '_'+idAddress_delivery));

                    if ($(this).find('.cart_total span').length > 0 && $(this).find('.cart_total span').attr('id').length > 0)
                        $(this).find('.cart_total span').attr('id', $(this).find('.cart_total span').attr('id').replace(/_\d+$/, '_'+idAddress_delivery));

                    if ($(this).find('.cart_quantity_input').length > 0 && $(this).find('.cart_quantity_input').attr('name').length > 0)
                    {
                        name = $(this).find('.cart_quantity_input').attr('name')+'_hidden';
                        
                        $(this).find('.cart_quantity_input').attr('name', $(this).find('.cart_quantity_input').attr('name').replace(/_\d+$/, '_'+idAddress_delivery));
                        
                        if ($(this).find('[name='+name+']')> 0)
                            $(this).find('[name='+name+']').attr('name', name.replace(/_\d+_hidden$/, '_'+idAddress_delivery+'_hidden'));
                    }

                    if ($(this).find('.cart_quantity_delete').length > 0 && $(this).find('.cart_quantity_delete').attr('id').length > 0) {
                        $(this).find('.cart_quantity_delete')
                            .attr('id', $(this).find('.cart_quantity_delete').attr('id').replace(/_\d+$/, '_'+idAddress_delivery))
                            .attr('href', $(this).find('.cart_quantity_delete').attr('href').replace(/id_address_delivery=\d+&/, 'id_address_delivery='+idAddress_delivery+'&'))
                    }
                });

                updateCarrierList(jsonData.carrier_data);
                updatePaymentMethods(jsonData);
                updateCartSummary(jsonData.summary);
                updateHookShoppingCart(jsonData.HOOK_SHOPPING_CART);
                updateHookShoppingCartExtra(jsonData.HOOK_SHOPPING_CART_EXTRA);
                if ($('#gift-price').length == 1)
                    $('#gift-price').html(jsonData.gift_price);
                $('#opc_account-overlay').fadeOut('slow');
                $('#opc_delivery_methods-overlay').fadeOut('slow');
                $('#opc_payment_methods-overlay').fadeOut('slow');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            if (textStatus != 'abort')
                alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
            $('#opc_account-overlay').fadeOut('slow');
            $('#opc_delivery_methods-overlay').fadeOut('slow');
            $('#opc_payment_methods-overlay').fadeOut('slow');
        }
    });
}

function getCarrierListAndUpdate()
{
    $('#opc_delivery_methods-overlay').fadeIn('slow');
    $.ajax({
        type: 'POST',
        url: orderOpcUrl,
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getCarrierList&token=' + static_token,
        success: function(jsonData)
        {
                if (jsonData.hasError)
                {
                    var errors = '';
                    for(error in jsonData.errors)
                        //IE6 bug fix
                        if(error != 'indexOf')
                            errors += jsonData.errors[error] + "\n";
                    alert(errors);
                }
                else
                    updateCarrierList(jsonData);
                $('#opc_delivery_methods-overlay').fadeOut('slow');
            }
    });
}

function updateCarrierSelectionAndGift()
{
    var recyclablePackage = 0;
    var gift = 0;
    var giftMessage = '';
    
    var delivery_option_radio = $('.delivery_option_radio');
    var delivery_option_params = '&';
    $.each(delivery_option_radio, function(i) {
        if ($(this).prop('checked'))
            delivery_option_params += $(delivery_option_radio[i]).attr('name') + '=' + $(delivery_option_radio[i]).val() + '&';
    });
    if (delivery_option_params == '&')
        delivery_option_params = '&delivery_option=&'

    if ($('input#recyclable:checked').length)
        recyclablePackage = 1;
    if ($('input#gift:checked').length)
    {
        gift = 1;
        giftMessage = encodeURIComponent($('textarea#gift_message').val());
    }

    $('#opc_payment_methods-overlay').fadeIn('slow');
    $('#opc_delivery_methods-overlay').fadeIn('slow');
    $.ajax({
        type: 'POST',
        url: orderOpcUrl,
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=updateCarrierAndGetPayments' + delivery_option_params + 'recyclable=' + recyclablePackage + '&gift=' + gift + '&gift_message=' + giftMessage + '&token=' + static_token ,
        success: function(jsonData)
        {
            if (jsonData.hasError)
            {
                var errors = '';
                for(error in jsonData.errors)
                    //IE6 bug fix
                    if(error != 'indexOf')
                        errors += jsonData.errors[error] + "\n";
                alert(errors);
            }
            else
            {
                updateCartSummary(jsonData.summary);
                updatePaymentMethods(jsonData);
                updateHookShoppingCart(jsonData.summary.HOOK_SHOPPING_CART);
                updateHookShoppingCartExtra(jsonData.summary.HOOK_SHOPPING_CART_EXTRA);
                updateCarrierList(jsonData.carrier_data);
                $('#opc_payment_methods-overlay').fadeOut('slow');
                $('#opc_delivery_methods-overlay').fadeOut('slow');
                refreshDeliveryOptions();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            if (textStatus != 'abort')
                alert("TECHNICAL ERROR: unable to save carrier \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
            $('#opc_payment_methods-overlay').fadeOut('slow');
            $('#opc_delivery_methods-overlay').fadeOut('slow');
        }
    });
}

function confirmFreeOrder()
{
    if ($('#opc_new_account-overlay').length != 0)
        $('#opc_new_account-overlay').fadeIn('slow');
    else
        $('#opc_account-overlay').fadeIn('slow');
    $('#opc_delivery_methods-overlay').fadeIn('slow');
    $('#opc_payment_methods-overlay').fadeIn('slow');
    $.ajax({
        type: 'POST',
        url: orderOpcUrl,
        async: true,
        cache: false,
        dataType : "html",
        data: 'ajax=true&method=makeFreeOrder&token=' + static_token ,
        success: function(html)
        {
            var array_split = html.split(':');
            if (array_split[0] === 'freeorder')
            {
                if (isGuest)
                    document.location.href = guestTrackingUrl+'?id_order='+encodeURIComponent(array_split[1])+'&email='+encodeURIComponent(array_split[2]);
                else
                    document.location.href = historyUrl;
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            if (textStatus != 'abort')
                alert("TECHNICAL ERROR: unable to confirm the order \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
        }
    });
}

function saveAddress(type)
{
    if (type != 'delivery' && type != 'invoice')
        return false;
    
    var params = 'firstname='+encodeURIComponent($('#firstname'+(type == 'invoice' ? '_invoice' : '')).val())+'&lastname='+encodeURIComponent($('#lastname'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'company='+encodeURIComponent($('#company'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'vat_number='+encodeURIComponent($('#vat_number'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'dni='+encodeURIComponent($('#dni'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'address1='+encodeURIComponent($('#address1'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'address2='+encodeURIComponent($('#address2'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'postcode='+encodeURIComponent($('#postcode'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'city='+encodeURIComponent($('#city'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'id_country='+encodeURIComponent($('#id_country').val())+'&';
    if ($('#id_state'+(type == 'invoice' ? '_invoice' : '')).val())
    {
    params += 'id_state='+encodeURIComponent($('#id_state'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    }
    params += 'other='+encodeURIComponent($('#other'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'phone='+encodeURIComponent($('#phone'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'phone_mobile='+encodeURIComponent($('#phone_mobile'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'alias='+encodeURIComponent($('#alias'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    // Clean the last &
    params = params.substr(0, params.length-1);

    var result = false;
    
    $.ajax({
        type: 'POST',
        url: addressUrl,
        async: false,
        cache: false,
        dataType : "json",
        data: 'ajax=true&submitAddress=true&type='+type+'&'+params+'&token=' + static_token,
        success: function(jsonData)
        {
            if (jsonData.hasError)
            {
                var tmp = '';
                var i = 0;
                for(error in jsonData.errors)
                    //IE6 bug fix
                    if(error != 'indexOf')
                    {
                        i = i+1;
                        tmp += '<li>'+jsonData.errors[error]+'</li>';
                    }
                tmp += '</ol>';
                var errors = '<b>'+txtThereis+' '+i+' '+txtErrors+':</b><ol>'+tmp;
                $('#opc_account_errors').html(errors).slideDown('slow');
                $.scrollTo('#opc_account_errors', 800);
                $('#opc_new_account-overlay').fadeOut('slow');
                $('#opc_delivery_methods-overlay').fadeOut('slow');
                $('#opc_payment_methods-overlay').fadeOut('slow');
                result = false;
            }
            else
            {
                // update addresses id
                $('input#opc_id_address_delivery').val(jsonData.id_address_delivery);
                $('input#opc_id_address_invoice').val(jsonData.id_address_invoice);
                result = true;
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            if (textStatus != 'abort')
                alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
            $('#opc_new_account-overlay').fadeOut('slow');
            $('#opc_delivery_methods-overlay').fadeOut('slow');
            $('#opc_payment_methods-overlay').fadeOut('slow');
        }
        });

    return result;
}

function updateNewAccountToAddressBlock()
{
    $('#opc_account-overlay, #opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');;
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: orderOpcUrl + '?rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getAddressBlockAndCarriersAndPayments&token=' + static_token ,
        success: function(json)
        {
            if (json.hasError)
            {
                var errors = '';
                for(var error in json.errors)
                    //IE6 bug fix
                    if(error !== 'indexOf')
                        errors += $('<div />').html(json.errors[error]).text() + "\n";
                alert(errors);
            }
            else
            {
                isLogged = 1;
                if (json.no_address == 1)
                    document.location.href = addressUrl;

                $('#opc_new_account').fadeOut('fast', function() {
                    if (typeof json.formatedAddressFieldsValuesList !== 'undefined' && json.formatedAddressFieldsValuesList )
                        formatedAddressFieldsValuesList = json.formatedAddressFieldsValuesList;
                    if (typeof json.order_opc_adress !== 'undefined' && json.order_opc_adress)
                        $('#opc_new_account').html(json.order_opc_adress);
                    // update block user info
                    if (json.block_user_info !== '' && $('#header_user').length == 1)
                    {
                        var elt = $(json.block_user_info).find('#header_user_info').html();
                        $('#header_user_info').fadeOut('nortmal', function() {
                            $(this).html(elt).fadeIn();
                        });
                    }
                    $(this).fadeIn('fast', function() {
                        //After login, the products are automatically associated to an address
                        $.each(json.summary.products, function() {
                            updateAddressId(this.id_product, this.id_product_attribute, '0', this.id_address_delivery);
                        });
                        updateAddressesDisplay(true);
                        updateCarrierList(json.carrier_data);
                        updateCarrierSelectionAndGift();
                        updatePaymentMethods(json);
                        if ($('#gift-price').length == 1)
                            $('#gift-price').html(json.gift_price);
                        $('#opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');
                    });
                });
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            if (textStatus !== 'abort')
                alert("TECHNICAL ERROR: unable to send login informations \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
            $('#opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');
        }
    });
}

$(function() {
    // GUEST CHECKOUT / NEW ACCOUNT MANAGEMENT
    if ((!isLogged) || (isGuest))
    {
        if (guestCheckoutEnabled && !isLogged)
        {
            $('#opc_account_choice').show();
            $('#opc_account_form').slideUp();
            $('#opc_invoice_address').slideUp();
            
            $('#opc_createAccount').click(function() {
                $('.is_customer_param').show();
                $('#opc_account_form').slideDown('slow');
                $('#is_new_customer').val('1');
                $('#opc_account_choice').slideUp();
                $('#opc_invoice_address').slideUp();
                $('#passwd, .form-group.password').show(); // 07/04
                updateState();
                updateNeedIDNumber();
                updateZipCode();
												
if (typeof idSelectedState !== 'undefined' && idSelectedState) {
	$('.id_state option[value=' + idSelectedState + ']').prop('selected', true);
	
	$('.id_state option[value=' + idSelectedState + ']').attr('selected', 'selected');
	bindUniform();
}
if (typeof idSelectedStateInvoice !== 'undefined' && idSelectedStateInvoice) {
	$('.id_state_invoice option[value=' + idSelectedStateInvoice + ']').prop('selected', true);
	
	$('.id_state_invoice option[value=' + idSelectedStateInvoice + ']').attr('selected', 'selected');
	bindUniform();
}
				bindUniform();
            });
            $('#opc_guestCheckout').click(function() {
                $('.is_customer_param').show();
                $('#opc_account_form').slideDown('slow');
                $('#is_new_customer').val('0');
                $('#opc_account_choice').slideUp();
                $('#opc_invoice_address').slideUp();
                $('#new_account_title').html(txtInstantCheckout);
                $('#passwd, .form-group.password').hide(); // 07/04
                updateState();
                updateNeedIDNumber();
                updateZipCode();
if (typeof idSelectedState !== 'undefined' && idSelectedState) {
	$('.id_state option[value=' + idSelectedState + ']').prop('selected', true);
	
	$('.id_state option[value=' + idSelectedState + ']').attr('selected', 'selected');
	bindUniform();
}
if (typeof idSelectedStateInvoice !== 'undefined' && idSelectedStateInvoice) {
	$('.id_state_invoice option[value=' + idSelectedStateInvoice + ']').prop('selected', true);
	
	$('.id_state_invoice option[value=' + idSelectedStateInvoice + ']').attr('selected', 'selected');
	bindUniform();
}

                bindUniform();
            });
            $('#opc_back_to_create_or_guest').click(function() {
                $('.is_customer_param').slideUp();
                $('#opc_account_form').slideUp();
                $('#is_new_customer').val('0');
                $('#opc_account_choice').show('slow');
                $('#opc_invoice_address').show('slow');
            });
        }
        else if (isGuest)
        {
            //$('.is_customer_param').hide();
            $('#opc_account_form').show('slow');
            $('#is_new_customer').val('0');
            $('#opc_account_choice').slideUp();
            $('#opc_back_to_create_or_guest').slideUp();
            $('#opc_invoice_address').slideUp();
            $('#new_account_title').html(txtInstantCheckout);
            updateState();
            updateNeedIDNumber();
            updateZipCode();
        }
        else
        {
            $('#opc_account_choice').slideUp();
            $('#opc_back_to_create_or_guest').slideUp();
            $('#is_new_customer').val('1');
            $('.is_customer_param').show();
            $('#opc_account_form').show();
            $('#opc_invoice_address').slideUp();
            updateState();
            updateNeedIDNumber();
            updateZipCode();
        }
        
        // LOGIN FORM
        $('#login_form_content').slideUp();
        $('#openLoginFormBlock').click(function() {
            $('#openNewAccountBlock').show();
            $(this).hide();
            $('#login_form_content').slideDown('slow');
            $('#new_account_form_content').slideUp('slow');
            return false;
        });
$('#login_email, #login_passwd').keypress(function(event){
    if (event.which == 13) {
        $('#SubmitLogin').trigger('click')
    } 
})
        // LOGIN FORM SENDING
        $('#SubmitLogin').click(function() {
            $.ajax({
                type: 'POST',
                url: authenticationUrl,
                async: false,
                cache: false,
                dataType : "json",
                data: 'SubmitLogin=true&ajax=true&email='+encodeURIComponent($('#login_email').val())+'&passwd='+encodeURIComponent($('#login_passwd').val())+'&token=' + static_token ,
                success: function(jsonData)
                {
                    if (jsonData.hasError)
                    {
                        var errors = '<b>'+txtThereis+' '+jsonData.errors.length+' '+txtErrors+':</b><ol>';
                        for(error in jsonData.errors)
                            //IE6 bug fix
                            if(error != 'indexOf')
                                errors += '<li>'+jsonData.errors[error]+'</li>';
                        errors += '</ol>';
                        $('#opc_login_errors').html(errors).slideDown('slow');
                    }
                    else
                    {
                        // update token
                        static_token = jsonData.token;
                        updateNewAccountToAddressBlock();
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    if (textStatus != 'abort')
                        alert("TECHNICAL ERROR: unable to send login informations \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
                }
            });
            return false;
        });
        
        // INVOICE ADDRESS
        $('#invoice_address').click(function() {
            if ($('#invoice_address:checked').length > 0)
            {
                $('#opc_invoice_address').slideDown('slow');
                if ($('#company_invoice').val() == '')
                    $('#vat_number_block_invoice').hide();
                updateState('invoice');
                updateNeedIDNumber('invoice');
                updateZipCode('invoice');
            }
            else
                $('#opc_invoice_address').slideUp('slow');
        });
        
        // VALIDATION / CREATION AJAX
        $('#submitAccount').click(function() {
            $('#opc_new_account-overlay').fadeIn('slow');
            $('#opc_delivery_methods-overlay').fadeIn('slow');
            $('#opc_payment_methods-overlay').fadeIn('slow');
            
            // RESET ERROR(S) MESSAGE(S)
            $('#opc_account_errors').html('').slideUp('slow');
            
            if ($('input#opc_id_customer').val() == 0)
            {
                var callingFile = authenticationUrl;
                var params = 'submitAccount=true&';
            }
            else
            {
                var callingFile = orderOpcUrl;
                var params = 'method=editCustomer&';
            }

            $('#opc_account_form input:visible, #opc_account_form input[type=hidden]').each(function() {
                if ($(this).is('input[type=checkbox]'))
                {
                    if ($(this).is(':checked'))
                        params += encodeURIComponent($(this).attr('name'))+'=1&';
                }
                else if ($(this).is('input[type=radio]'))
                {
                    if ($(this).is(':checked'))
                        params += encodeURIComponent($(this).attr('name'))+'='+encodeURIComponent($(this).val())+'&';
                }
                else
                    params += encodeURIComponent($(this).attr('name'))+'='+encodeURIComponent($(this).val())+'&';
            });
            $('#opc_account_form select:visible').each(function() {
                params += encodeURIComponent($(this).attr('name'))+'='+encodeURIComponent($(this).val())+'&';
            });
            params += 'customer_lastname='+encodeURIComponent($('#customer_lastname').val())+'&';
            params += 'customer_firstname='+encodeURIComponent($('#customer_firstname').val())+'&';
            params += 'alias='+encodeURIComponent($('#alias').val())+'&';
            params += 'other='+encodeURIComponent($('#other').val())+'&';
            params += 'is_new_customer='+encodeURIComponent($('#is_new_customer').val())+'&';
            // Clean the last &
            params = params.substr(0, params.length-1);
            
            $.ajax({
                type: 'POST',
                url: callingFile,
                async: false,
                cache: false,
                dataType : "json",
                data: 'ajax=true&'+params+'&token=' + static_token ,
                success: function(jsonData)
                {
                    if (jsonData.hasError)
                    {
                        var tmp = '';
                        var i = 0;
                        for(error in jsonData.errors)
                            //IE6 bug fix
                            if(error != 'indexOf')
                            {
                                i = i+1;
                                tmp += '<li>'+jsonData.errors[error]+'</li>';
                            }
                        tmp += '</ol>';
                        var errors = '<b>'+txtThereis+' '+i+' '+txtErrors+':</b><ol>'+tmp;
                        $('#opc_account_errors').html(errors).slideDown('slow');
                        $.scrollTo('#opc_account_errors', 800);
                    }

                    isGuest = ($('#is_new_customer').val() == 1 ? 0 : 1);
                    
                    if (jsonData.id_customer != undefined && jsonData.id_customer != 0 && jsonData.isSaved)
                    {
                        // update token
                        static_token = jsonData.token;
                        
                        // update addresses id
                        $('input#opc_id_address_delivery').val(jsonData.id_address_delivery);
                        $('input#opc_id_address_invoice').val(jsonData.id_address_invoice);
                        
                        // It's not a new customer
                        if ($('input#opc_id_customer').val() != '0')
                        {
                            if (!saveAddress('delivery'))
                                return false;
                        }
                        
                        // update id_customer
                        $('input#opc_id_customer').val(jsonData.id_customer);
                        
                        if ($('#invoice_address:checked').length != 0)
                        {
                            if (!saveAddress('invoice'))
                                return false;
                        }
                        
                        // update id_customer
                        $('input#opc_id_customer').val(jsonData.id_customer);
                        
                        // force to refresh carrier list
                        if (isGuest)
                        {
                            isLogged = 1;
                            $('#opc_account_saved').fadeIn('slow');
                            $('#submitAccount').hide();
                            updateAddressSelection();
                        }
                        else
                            updateNewAccountToAddressBlock();
                    }
                    $('#opc_new_account-overlay').fadeOut('slow');
                    $('#opc_delivery_methods-overlay').fadeOut('slow');
                    $('#opc_payment_methods-overlay').fadeOut('slow');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    if (textStatus != 'abort')
                        alert("TECHNICAL ERROR: unable to save account \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
                    $('#opc_new_account-overlay').fadeOut('slow');
                    $('#opc_delivery_methods-overlay').fadeOut('slow');
                    $('#opc_payment_methods-overlay').fadeOut('slow');
                }
            });
            return false;
        });
    }
    
    bindInputs();
    
    $('#opc_account_form input,select,textarea').change(function() {
        if ($(this).is(':visible'))
        {
            $('#opc_account_saved').fadeOut('slow');
            $('#submitAccount').show();
        }
    });
    
});

function bindInputs()
{
	// CUSTOM [FROM module version 1.6.3] [begin]
	$('.cart_discount_delete').live('click', function(){
		$('#cart_summary').css('opacity', '0.3');
		$.ajax({
			type: 'POST',
			url: orderOpcUrl,
			async: true,
			cache: false,
			dataType : 'html',
			data: 'ajax=true&deleteDiscount=' + $(this).attr('attr-id_discount') + '&token=' + static_token,
			success: function(data) {
				//cartReload
				//$('#cart_summary').replaceWith($(data).find('#cart_summary'));
				$('#shopping_cart_ajax').replaceWith($(data));
				$('.cart_quantity_input').typeWatch({ highlight: true, wait: 600, captureLength: 0, callback: function(val) { updateQty(val, true, this.el) } });
			}
		});
		return false;
	})
	
	$('button[name=submitAddDiscount]').die('click').live('click', function(){
		$('#cart_summary').css('opacity', '0.3');
		$.ajax({
			type: 'POST',
			url: orderOpcUrl,
			async: true,
			cache: false,
			dataType : 'html',
			data: 'ajax=true&submitAddDiscount=1&discount_name=' + $('input[name=discount_name]').val() + '&token=' + static_token,
			success: function(data) {
				//cartReload
				$('#shopping_cart_ajax').replaceWith($(data));
				$('.cart_quantity_input').typeWatch({ highlight: true, wait: 600, captureLength: 0, callback: function(val) { updateQty(val, true, this.el) } });
			}
		});
		return false;
	})
	// CUSTOM [FROM module version 1.6.3] [end]

    // Order message update
    $('#old_message').blur(function() {
        $('#opc_delivery_methods-overlay').fadeIn('slow');
        $.ajax({
            type: 'POST',
            url: orderOpcUrl,
            async: false,
            cache: false,
            dataType : "json",
            data: 'ajax=true&method=updateMessage&message=' + encodeURIComponent($('#old_message').val()) + '&token=' + static_token ,
            success: function(jsonData)
            {
                if (jsonData.hasError)
                {
                    var errors = '';
                    for(error in jsonData.errors)
                        //IE6 bug fix
                        if(error != 'indexOf')
                            errors += jsonData.errors[error] + "\n";
                    alert(errors);
                }
            else
                $('#opc_delivery_methods-overlay').fadeOut('slow');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                if (textStatus != 'abort')
                    alert("TECHNICAL ERROR: unable to save message \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
                $('#opc_delivery_methods-overlay').fadeOut('slow');
            }
        });
    });
    
    // Recyclable checkbox
    $('input#recyclable').click(function() {
        updateCarrierSelectionAndGift();
    });
    
    // Gift checkbox update
    $('input#gift').click(function() {
        if ($('input#gift').is(':checked'))
            $('#gift_msg').slideDown();
        else
            $('#gift_msg').slideUp();
        updateCarrierSelectionAndGift();
    });
    
    if ($('input#gift').is(':checked'))
        $('#gift_msg').slideDown();
    else
        $('#gift_msg').slideUp();

    // Gift message update
    $('textarea#gift_message').change(function() {
        updateCarrierSelectionAndGift();
    });
    
    // Term Of Service (TOS)
    $('#cgv').unbind('click').click(function() {
        updatePaymentMethodsDisplay();
    });
}

function updatePaymentMethodsDisplay()
{
	var checked = 0;
	if ($('#cgv:checked').length != 0) {
		checked = 1;
		$('#uniform-cgv span').addClass('checked');
	} else {
		checked = 0;
		$('#uniform-cgv span').removeClass('checked');
	}

	$('#opc_payment_methods-overlay').fadeIn('slow');
	$.ajax({
		type: 'POST',
		url: orderOpcUrl,
		async: true,
		cache: false,
		dataType : "json",
		data: 'ajax=true&method=updateTOSStatusAndGetPayments&checked=' + checked + '&token=' + static_token,
		success: function(json)
		{
			updateCarrierSelectionAndGift();
			$('#HOOK_TOP_PAYMENT').html(json.HOOK_TOP_PAYMENT);
			$('#opc_payment_methods-content #HOOK_PAYMENT').html(json.HOOK_PAYMENT);
			$("#HOOK_PAYMENT .col-xs-12").each(function( index ) {
				$(this).removeClass('col-md-6');
			});
			$('.warning').addClass('alert alert-warning').removeClass('warning');
			$('#opc_payment_methods-overlay').fadeOut('slow');
		}
	});
}

function multishippingMode(it)
{
    if ($(it).prop('checked'))
    {
        $('#address_delivery, .address_delivery').hide();
        $('#link_multishipping_block').show();
        $('.addressesAreEquals').hide();
        $('#address_invoice_form').show();
        
        $('#link_multishipping_form').click(function() {return false;});
        $('.address_add a').attr('href', addressMultishippingUrl);
        
        $('#link_multishipping_form').fancybox({
            'transitionIn': 'elastic',
            'transitionOut': 'elastic',
            'type': 'ajax',
            'onClosed': function()
            {
                // Reload the cart
                $.ajax({
                    url: orderOpcUrl,
                    data: 'ajax=true&method=cartReload',
                    dataType : 'html',
                    cache: false,
                    success: function(data) {
                        $('#cart_summary').replaceWith($(data).find('#cart_summary'));
                        $('.cart_quantity_input').typeWatch({ highlight: true, wait: 600, captureLength: 0, callback: function(val) { updateQty(val, true, this.el) } });
                    }
                });
                updateCarrierSelectionAndGift();
            },
            'onStart': function()
            {
                // Removing all ids on the cart to avoid conflic with the new one on the fancybox
                // This action could "break" the cart design, if css rules use ids of the cart
                $.each($('#cart_summary *'), function(it, el) {
                    $(el).attr('id', '');
                });
            },
            'onComplete': function()
            {
                $('#fancybox-content .cart_quantity_input').typeWatch({ highlight: true, wait: 600, captureLength: 0, callback: function(val) { updateQty(val, false, this.el)} });
                cleanSelectAddressDelivery();
                $('#fancybox-content').append($('<div class="multishipping_close_container"><a id="multishipping-close" class="button_large" href="#">' + CloseTxt + '</a></div>'));
                $('#multishipping-close').click(function() {
                    var newTotalQty = 0;
                    $('#fancybox-content .cart_quantity_input').each(function(){
                        newTotalQty += parseInt($(this).val());
                    });
                    if (newTotalQty != totalQty) {
                        if(!confirm(QtyChanged)) {
                            return false;
                        }
                    }
                    $.fancybox.close();
                    return false;
                });
                totalQty = 0;
                $('#fancybox-content .cart_quantity_input').each(function(){
                    totalQty += parseInt($(this).val());
                });
            }
        });
    }
    else
    {
        $('#address_delivery, .address_delivery').show();
        $('#link_multishipping_block').hide();
        $('.addressesAreEquals').show();
        if ($('.addressesAreEquals').find('input:checked').length) {
            $('#address_invoice_form').hide();
        } else {
            $('#address_invoice_form').show();
        }
        $('.address_add a').attr('href', addressUrl);
        
        // Disable multi address shipping
        $.ajax({
            url: orderOpcUrl,
            async: true,
            cache: false,
            data: 'ajax=true&method=noMultiAddressDelivery'
        });
        
        // Reload the cart
        $.ajax({
            url: orderOpcUrl,
            async: true,
            cache: false,
            data: 'ajax=true&method=cartReload',
            dataType : 'html',
            success: function(data) {
                $('#cart_summary').replaceWith($(data).find('#cart_summary'));
            }
        });
    }
}

$(document).ready(function() {
    // If the multishipping mode is off assure us the checkbox "I want to specify a delivery address for each products I order." is unchecked.
    $('#multishipping_mode_checkbox').attr('checked', false);
    // If the multishipping mode is on, check the box "I want to specify a delivery address for each products I order.".
    if (typeof(multishipping_mode) != 'undefined' && multishipping_mode) {
        $('#multishipping_mode_checkbox').click();
        $('.addressesAreEquals').hide();
        $('.addressesAreEquals').find('input').attr('checked', false);
    }
    
    if (typeof(open_multishipping_fancybox) != 'undefined' && open_multishipping_fancybox)
        $('#link_multishipping_form').click();

    $("#HOOK_PAYMENT .col-xs-12").each(function( index ) {
        $(this).removeClass('col-md-6');
    });
    $('.warning').addClass('alert alert-warning').removeClass('warning');
});
