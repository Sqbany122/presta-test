/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

$(document).ready(function() {
	//var shipping = $('#cart-subtotal-shipping').find('span.value').html();
    // case hidden UPS Shipping
    if (Boolean(hiddenUPSShipping))
    {
        //$("#delivery_option_" + idCarrier).prop("checked", false);
        $("label[for='delivery_option_" + idCarrier + "']").parent().css( "display", "none" );
        $("label[for='delivery_option_" + idCarrier + "']").parent().next().css( "display", "none" );
        //$("label[for='delivery_option_" + idCarrier + "']").parent().remove();
    }

    document.querySelector("#js-delivery").addEventListener("submit", function(e) {
        var selectShippingMethod = $("#delivery_option_" + idCarrier).prop("checked");
        var selectedShippingServiceSession = $("#selectedShippingService").val();
        var selectedAddress = $("#selectedAPAddress").val();
        if (selectedShippingServiceSession.indexOf('_AP_') != -1 && selectShippingMethod && (!selectedAddress || selectedAddress == ""))
        {
            e.preventDefault();
            $(".alert-danger").html(txtEShippingMethod);
            $(".alert-danger").css('display', 'block');
            $('html, body').animate({
                scrollTop: $(".frm_e_shop").offset().top
            }, 200);
            return  false;
        }
        if (Boolean(hiddenUPSShipping) && selectShippingMethod) {
            e.preventDefault();
            $(".delivery-options-list-error").remove();
            $(".delivery-options-list").prepend('<div class="delivery-options-list-error"><p class="alert alert-danger">Unfortunately, there are no carriers available for your delivery address.</p></div>');
            return  false;
        }
    });

    $("label[for='delivery_option_" + idCarrier + "'] .carrier-delay").html(cheapestTime);
    $("label[for='delivery_option_" + idCarrier + "'] .carrier-price").html(cheapestPrice);

    if (shippingServiceType == 1)
    {
        if ($( ".frm_e_shop" ).hasClass( "d-none" ))
        {
            $( ".frm_e_shop" ).removeClass( "d-none" );
        }
    }
    else
    {
        if (!$( ".frm_e_shop" ).hasClass( "d-none" ))
        {
            $( ".frm_e_shop" ).addClass( "d-none" );
        }
    }

    if (!$( "#resultSearch" ).hasClass( "d-none" ))
    {
        $( "#resultSearch" ).addClass( "d-none" );
    }

    if (!$( "#showSearch" ).hasClass( "d-none" ))
    {
        $( "#showSearch" ).addClass( "d-none" );
    }

    $('input[type=radio][name=optradio]').change(function() {
        var shipping_value = 0;
        if (this.value.indexOf('ADD') != -1)
        {
            shipping_value = $("#ADD_" + this.value).val();
            shipping_time = $("#ADD_TinT_" + this.value).val();
            shipping_value_format = $("#ADD_Price_" + this.value).val();
        }
        else
        {
            shipping_value = $("#AP_" + this.value).val();
            shipping_time = $("#AP_TinT_" + this.value).val();
            shipping_value_format = $("#AP_Price_" + this.value).val();
            $(".btn_select").css("background-color", "lightgrey");
            $(".alert-danger").css('display', 'none');
        }
        // ajax to save session selectedShippingService
        $.ajax({
            type: 'POST',
            url: urlChangeShippingService,
            data: {
                ajax: true,
                selectedShippingService: this.value,
                selectedShippingFee: shipping_value
            },
            success: function(resp, textStatus, jqXHR)
            {
                $("#selectedShippingService").val(resp.selectedShippingService);
                $("#selectedShippingFee").val(resp.selectedShippingFee);
                // function shippingFeeValue from extra-content_shipping.tpl
                shippingFeeValue();
            }
        });

        $("label[for='delivery_option_" + idCarrier + "'] .carrier-delay").html(shipping_time);
        $("label[for='delivery_option_" + idCarrier + "'] .carrier-price").html(shipping_value_format);
    });

    $.each($('.check-service-add'), function (index, value) {
        $(value).change(function(){
            if ($(this).is(':checked')){
                $( ".frm_e_shop" ).addClass( "d-none" );
            }
        });
    });

    $.each($('.check-service-ap'), function (index, value) {
        $(value).change(function(){
            if ($(this).is(':checked'))
            {
                $( ".frm_e_shop" ).removeClass( "d-none" );
            }
        });
    });
});

function Search() {
    // GetMap();
    var searchAddress = $("#searchAddress").val();
    var selectedService = $(".check-service-ap:checked").val();
    //selectAddress
    $("#selectAddress").html('');
    if (searchAddress)
    {
        $(".alert-danger").css('display', 'none');
        $.ajax({
            type: 'POST',
            url: urlSearchAccessPoint,
            data: {
                ajax: true,
                fullAddress: searchAddress,
                country: countryCode,
                selectedService: selectedService
            },
            success : function(resp, textStatus, jqXHR)
            {
                $("#information-accesspoint").replaceWith(resp.preview);
                if (resp.Description == 'Success')
                {
                    if ($( "#resultSearch" ).hasClass( "d-none" ))
                    {
                        $( "#resultSearch" ).removeClass( "d-none" );
                    }

                    if ($( "#showSearch" ).hasClass( "d-none" ))
                    {
                        $( "#showSearch" ).removeClass( "d-none" );
                    }

                    $("#selectAddressinFor").val(resp.selectAddress);
                    map.entities.clear();
                    var arrayLocatorInfo = resp.arrGeoCode;
                    if (arrayLocatorInfo)
                    {
                        arrayLocatorInfo.forEach(function (value, index) {
                            var streetaddress= value.substr(0, value.indexOf(','))*1;
                            streetaddress = streetaddress.toFixed(6);
                            indexMap.push({
                                value : streetaddress,
                                index: index
                            });
                            //Create the geocode request.
                            var geocodeRequest = {
                                where: value,
                                callback: getBoundary,
                                errorCallback: function (e) {}
                            };
                            //Make the geocode request.
                            searchManager.geocode(geocodeRequest);
                        });
                    }
                }
                else
                {
                    $(".alert-danger").html(resp.Description);
                    $(".alert-danger").css('display', 'block');
                    GetMap();
                }
            }
        });
    }
    else
    {
        $(".alert-danger").html(addressRequired);
        $(".alert-danger").css('display', 'block');
        $("#searchAddress").css("border", ".1875rem solid #2fb5d2");
        $("#searchAddress").focus();
        GetMap();
    }
}

function selectAddressButton(addressInfo, locatorLength)
{
    var arrSelectAddress = JSON.parse($("#selectAddressinFor").val());
    // ajax to save session
    $.ajax({
        type: 'POST',
        url: urlSelectAccessPoint,
        data: {
            ajax: true,
            acessPointAddress: JSON.stringify(arrSelectAddress[addressInfo-1])
        },
        success: function(resp, textStatus, jqXHR)
        {
            $("#selectedAPAddress").val(resp.selectedAddress);
        }
    });
    for (var i = 1; i < (locatorLength + 1); i++)
    {
        if (i == addressInfo)
        {
            $("#btn_select_" + i).css('background-color', 'paleturquoise');
        }
        else
        {
            $("#btn_select_" + i).css('background-color', 'lightgrey');
        }
    }
}

function useMyAddress() {
    str_esc = myAddress.replace( "&amp;","&")
        .replace( "&lt;","<")
        .replace( "&gt;",">")
        .replace( "&quot;", "\"")
        .replace("&#039;", "'");
    $("#searchAddress").val(str_esc);
}
