/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

function getCarrierElement() {
    if ($("input[id='delivery_option_" + idCarrier + "']").length === 1) {
        return $("input[id='delivery_option_" + idCarrier + "']");
    } else if ($("input[data-key='"+idCarrier+",']").length === 1) {
        return $("input[data-key='"+idCarrier+",']");
    }
}

$(document).ready(function() {
    var carrierEl = getCarrierElement();
    idAddress;
    carrierEl.prop("checked", true);

    var checkUPSShipping = 'no';

    // case hidden UPS Shipping
    if (Boolean(hiddenUPSShipping)) {
        carrierEl.parentsUntil( ".delivery_option" ).addClass('d-none');
    }
    document.querySelector("#form").addEventListener("submit", function(e) {
        var selectedUPSShipping = carrierEl.prop("checked");
        if (Boolean(hiddenUPSShipping) && selectedUPSShipping) {
            e.preventDefault();
            $(".delivery-options-list-error").remove();
            $(".delivery_options_address").prepend('<div class="delivery-options-list-error"><p class="alert alert-danger">Unfortunately, there are no carriers available for your delivery address.</p></div>');
            return false;
        }
        var idSelected = $('input[type=radio][name="delivery_option\['+ idAddress +'\]"]:checked').val();
        var tmp_idCarrier = idCarrier + ',';
        if (idSelected == tmp_idCarrier) {
            var checkCarrierConfirm = carrierConfirm();
            if (checkCarrierConfirm) {
                e.preventDefault();
                return false;
            }
        }
    });

    if (!carrierName) {
        var carrierName = "UPS SHIPPING"
    }

    if ($("label[for='delivery_option_" + idCarrier + "']").length === 1) {
        $("label[for='delivery_option_" + idCarrier + "'] .carrier-delay").html(cheapestTime);
        $("label[for='delivery_option_" + idCarrier + "'] .carrier-price").html(cheapestPrice);
    } else {
        var shiping_time_layout = "<strong>" + carrierName + "</strong><br>" +deliveryBy+ ":&nbsp;" + cheapestTime + "<br>";
        carrierEl.closest("tr").children("td:nth-of-type(3)").html(shiping_time_layout);
        carrierEl.closest("tr").children(".delivery_option_price").html(cheapestPrice + " (tax incl.)");
    }

    if (shippingServiceType == 1) {
        if ($( ".frm_e_shop" ).hasClass( "d-none" )) {
            $( ".frm_e_shop" ).removeClass( "d-none" );
        }
    } else {
        if (!$( ".frm_e_shop" ).hasClass( "d-none" )) {
            $( ".frm_e_shop" ).addClass( "d-none" );
        }
    }

    if (!$( "#resultSearch" ).hasClass( "d-none" )) {
        $( "#resultSearch" ).addClass( "d-none" );
    }

    if (!$( "#showSearch" ).hasClass( "d-none" )) {
        $( "#showSearch" ).addClass( "d-none" );
    }

    $('input[type=radio][name="delivery_option\['+ idAddress +'\]"]').change(function() {
        var idSelected = $('input[type=radio][name="delivery_option\['+ idAddress +'\]"]:checked').val();
        var tmp_idCarrier = idCarrier + ',';
        if (idSelected != tmp_idCarrier) {
            $("#HOOK_EXTRACARRIER_5").css("display", "none");
        } else if (idSelected == tmp_idCarrier) {
            $("#HOOK_EXTRACARRIER_5").css("display", "block");
        }
    });

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
            $(".alert-danger").hide("slow");
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

        if ($("label[for='delivery_option_" + idCarrier + "']").length === 1) {
            $("label[for='delivery_option_" + idCarrier + "'] .carrier-delay").html(shipping_time);
            $("label[for='delivery_option_" + idCarrier + "'] .carrier-price").html(shipping_value_format);
        } else {
            var shiping_time_layout = "<strong>" + carrierName + "</strong><br>" + deliveryBy + ":&nbsp;" + shipping_time + "<br>";
            carrierEl.closest("tr").children("td:nth-of-type(3)").html(shiping_time_layout);
            carrierEl.closest("tr").children(".delivery_option_price").html(shipping_value_format + " (tax incl.)");
        }

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

function carrierConfirm() {
    var selectedShippingServiceSession = $("#selectedShippingService").val();
    var selectedAddress = $("#selectedAPAddress").val();
    if (selectedShippingServiceSession.indexOf('_AP_') != -1 && (!selectedAddress || selectedAddress == ""))
    {
        $(".alert-danger").html(txtEShippingMethod);
        $(".alert-danger").css('display', 'block');
        $('html, body').animate({
            scrollTop: $(".frm_e_shop").offset().top
        }, 200);
        return  false;
    }
    return true;
}

function Search() {
    // GetMap();
    var searchAddress = $("#searchAddress").val();
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
