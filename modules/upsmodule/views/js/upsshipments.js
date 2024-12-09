/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

$(document).ready(function(){
    $(".dropdown-menu li").last().remove("li");
    $(".dropdown-menu .divider").remove("li");
    $(".filter").addClass("hidden");
    var getExportUrl = window.location.href + "&exportups_shipment";

    // ======= Check the screen has checkbox or not =======

    if (countRow > 1)
    {
        // $(".btn-group").hide();
        $("#page-header-desc-ups_shipment-export_shipments").parents().addClass("disabled");
        $("#page-header-desc-ups_shipment-export_shipments").removeAttr("href");
        $("#page-header-desc-ups_shipment-cancel_shipments").parents().addClass("disabled");
        $("#page-header-desc-ups_shipment-new_single_shipments").parents().addClass("disabled");
        $("#page-header-desc-ups_shipment-print_label_zpl").parents().addClass("disabled");

        // Event click Checkbox and Export Shipment
        $(".noborder").click(function()
        {
            var arrSelected = $('.noborder:checkbox:checked');
            if (arrSelected.length == 0)
            {
                $("#page-header-desc-ups_shipment-export_shipments").parents().addClass("disabled");
                $("#page-header-desc-ups_shipment-export_shipments").removeAttr("href");
                $("#page-header-desc-ups_shipment-cancel_shipments").parents().addClass("disabled");
                $("#page-header-desc-ups_shipment-new_single_shipments").parents().addClass("disabled");
                $("#page-header-desc-ups_shipment-print_label_zpl").parents().addClass("disabled");
            }
            else if (arrSelected.length > 0)
            {
                $("#page-header-desc-ups_shipment-export_shipments").parents().removeClass("disabled");
                $("#page-header-desc-ups_shipment-export_shipments").attr("href", getExportUrl);
                $("#page-header-desc-ups_shipment-cancel_shipments").parents().removeClass("disabled");
                $("#page-header-desc-ups_shipment-new_single_shipments").parents().removeClass("disabled");
                $("#page-header-desc-ups_shipment-print_label_zpl").parents().removeClass("disabled");
            }

            // Export Shipment
            var arrShippingID = [];
            var numberPackageString = '';

            $("input:checkbox[class=noborder]:checked").each(function(){
                arrShippingID.push($(this).val());
            });

            if (arrShippingID)
                numberPackageString = arrShippingID.toString();

            $.ajax({
                type: 'POST',
                url: 'index.php?controller=AdminUpsShipments',
                data: {
                    token: token,
                    ajax: true,
                    action: 'ExportShipment',
                    shipmentID: numberPackageString,
                },
                dataType: 'json',
                success: function(resp,textStatus,jqXHR)
                {

                }
            });
        });

        // Event click Select All and Export Shipment
        $(".dropdown-menu li:first-child").on("click", function(){
            var arrSelected = $('.noborder:checkbox:checked');
            if (arrSelected.length == 0)
            {
                $("#page-header-desc-ups_shipment-export_shipments").parents().addClass("disabled");
                $("#page-header-desc-ups_shipment-export_shipments").removeAttr("href");
                $("#page-header-desc-ups_shipment-cancel_shipments").parents().addClass("disabled");
                $("#page-header-desc-ups_shipment-new_single_shipments").parents().addClass("disabled");
                $("#page-header-desc-ups_shipment-print_label_zpl").parents().addClass("disabled");
            }
            else if (arrSelected.length > 0)
            {
                $("#page-header-desc-ups_shipment-export_shipments").parents().removeClass("disabled");
                $("#page-header-desc-ups_shipment-export_shipments").attr("href", getExportUrl);
                $("#page-header-desc-ups_shipment-cancel_shipments").parents().removeClass("disabled");
                $("#page-header-desc-ups_shipment-new_single_shipments").parents().removeClass("disabled");
                $("#page-header-desc-ups_shipment-print_label_zpl").parents().removeClass("disabled");
            }

            // Export Shipment
            var arrShippingID = [];
            var numberPackageString = '';

            $("input:checkbox[class=noborder]:checked").each(function(){
                arrShippingID.push($(this).val());
            });

            if (arrShippingID)
                numberPackageString = arrShippingID.toString();

            $.ajax({
                type: 'POST',
                url: 'index.php?controller=AdminUpsShipments',
                data: {
                    token: token,
                    ajax: true,
                    action: 'ExportShipment',
                    shipmentID: numberPackageString,
                },
                dataType: 'json',
                success: function(resp,textStatus,jqXHR)
                {

                }
            });
        })

        // Event click Unselect All
        $(".dropdown-menu li:nth-child(2)").on("click", function(){
            var arrSelected = $('.noborder:checkbox:checked');
            if (arrSelected.length == 0)
            {
                $("#page-header-desc-ups_shipment-export_shipments").parents().addClass("disabled");
                $("#page-header-desc-ups_shipment-export_shipments").removeAttr("href");
                $("#page-header-desc-ups_shipment-cancel_shipments").parents().addClass("disabled");
                $("#page-header-desc-ups_shipment-new_single_shipments").parents().addClass("disabled");
                $("#page-header-desc-ups_shipment-print_label_zpl").parents().addClass("disabled");
            }
            else if (arrSelected.length > 0)
            {
                $("#page-header-desc-ups_shipment-export_shipments").parents().removeClass("disabled");
                $("#page-header-desc-ups_shipment-export_shipments").attr("href", getExportUrl);
                $("#page-header-desc-ups_shipment-cancel_shipments").parents().removeClass("disabled");
                $("#page-header-desc-ups_shipment-new_single_shipments").parents().removeClass("disabled");
                $("#page-header-desc-ups_shipment-print_label_zpl").parents().removeClass("disabled");
            }
        });
    }
    else if (countRow == 0)
    {
        $("#page-header-desc-ups_shipment-export_shipments").parents().addClass("disabled");
        $("#page-header-desc-ups_shipment-export_shipments").removeAttr("href");
        $("#page-header-desc-ups_shipment-cancel_shipments").parents().addClass("disabled");
        $("#page-header-desc-ups_shipment-cancel_shipments").removeAttr("onclick");
        $("#page-header-desc-ups_shipment-new_single_shipments").parents().addClass("disabled");
        $("#page-header-desc-ups_shipment-print_label_zpl").parents().addClass("disabled");
    }
    else if (countRow == 1)
    {
        $("#page-header-desc-ups_shipment-export_shipments").attr("href", getExportUrl);

        // Export Shipment
        var id_ups_shipment = shipmentID;

        $.ajax({
            type: 'POST',
            url: 'index.php?controller=AdminUpsShipments',
            data: {
                token: token,
                ajax: true,
                action: 'ExportShipment',
                shipmentID: id_ups_shipment,
            },
            dataType: 'json',
            success: function(resp,textStatus,jqXHR)
            {

            }
        });
    }
});

function showViewShipmentModal(val)
{
    if (val)
    {
        $.ajax({
            type: 'POST',
            url: 'index.php?controller=AdminUpsShipments',
            data: {
                token: token,
                ajax: true,
                action: 'GetOrderByTrackingNumber',
                combinationKeys: val,
            },
            dataType : 'json',
            success : function(resp,textStatus,jqXHR)
            {
                if (resp)
                {
                    var shippingAddress = resp.shipping_address1;
                    var addressCustomer = resp.customerAddressLine1;
                    if (resp.shipping_address2)
                    {
                        shippingAddress += '<br />' + resp.shipping_address2;
                    }

                    if (resp.city && shippingAddress)
                    {
                        shippingAddress += '<br />' + resp.city;
                    }

                    if (resp.apStateName && shippingAddress)
                    {
                        shippingAddress += '<br />' + resp.apStateName;
                    }

                    if (resp.postalcode && shippingAddress)
                    {
                        shippingAddress += '<br />' + resp.postalcode;
                    }
                    if (resp.countrytoAD && shippingAddress)
                    {
                        shippingAddress += '<br />' + resp.countrytoAD;
                    }
                    // infoCustomer
                    if (resp.customerAddressLine2 && addressCustomer)
                    {
                        addressCustomer += '<br />' + resp.customerAddressLine2;
                    }
                    if (resp.cityCustomer && addressCustomer)
                    {
                        addressCustomer += '<br />' + resp.cityCustomer;
                    }
                    if (resp.stateName && addressCustomer)
                    {
                        addressCustomer += '<br />' + resp.stateName;
                    }
                    if (resp.postcodeCustomer && addressCustomer)
                    {
                        addressCustomer += '<br />' + resp.postcodeCustomer;
                    }
                    if (resp.country && addressCustomer)
                    {
                        addressCustomer += '<br />' + resp.country;
                    }

                    if (resp.flagToAP)
                    {
                        shippingAP = 'Access Point: ' + shippingAddress;
                        $("#shipping_addressAP").html(shippingAP);

                        $("#shipping_address1").html(addressCustomer);
                    }
                    else
                    {
                        $("#shipping_address1").html(shippingAddress);
                        $("#shipping_addressAP").html('');
                    }

                    $("#id_ups_shipment").html(resp.id_ups_shipment);
                    $("#shipment_date").html(resp.date);
                    $("#shipment_time").html(resp.time);
                    $("#status").html(resp.status);
                    $("#tracking_number").html(resp.tracking_number);
                    $("#id_order").html(resp.id_order);
                    $("#customer_name").html(resp.customer_name);
                    $("#product_details").html(resp.product_details);
                    $("#phone").html(resp.phone);
                    $("#email").html(resp.email);
                    $("#shipping_service").html(resp.shipping_service);
                    $("#package_detail").html(resp.package_detail);
                    $("#accessorials_service").html(resp.accessorials_service);
                    $("#order_value").html(resp.currency + ' ' + resp.order_value);
                    $("#shipping_fee").html(resp.currencyMerchant + ' ' + resp.shipping_fee);

                    $("#modalViewDetail").modal("show");
                }
            }
        });
    }
}

function cancelShipment()
{
    var numberPackageString = '';

    if (countRow > 1)
    {
        var arrShippingID = [];

        $("input:checkbox[class=noborder]:checked").each(function(){
            arrShippingID.push($(this).val());
        });

        if (arrShippingID)
        {
            numberPackageString = arrShippingID.toString();
        }
    }
    else if (countRow == 1)
    {
        // Have to change the name param shipmentID to tracking id. UPS
        numberPackageString = shipmentID;
    }

    if (numberPackageString && confirm(txtCancelConfirm))
    {
        $.ajax({
            type: 'POST',
            url: 'index.php?controller=AdminUpsShipments',
            data: {
                token: token,
                ajax: true,
                action: 'CancelShipment',
                trackingId: numberPackageString,
            },
            dataType: 'json',
            success : function(resp,textStatus,jqXHR)
            {
                if (resp.canceled) {
                    alert(txtSuccess);
                } else {
                    alert(txtUnsuccess);
                }
                location.reload();
            }
        });
    }
}

function printLabel(labelFormat)
{
    var trackingIds = [];
    var numberPackageString = '';

    $("input:checkbox[class=noborder]:checked").each(function(){
        trackingIds.push($(this).val());
    });

    if (countRow == 1)
    {
        trackingIds.push(shipmentID);
    }

    if (trackingIds != '')
    {
        numberPackageString = trackingIds.toString();

        $.ajax({
            type: 'POST',
            url: 'index.php?controller=AdminUpsShipments',
            data: {
                token: token,
                ajax: true,
                action: 'PrintLabel',
                trackingId: numberPackageString,
                labelFormat: labelFormat,
            },
            dataType : 'json',
            async: false,
            success : function(resp, textStatus, jqXHR)
            {
                if (resp.link === '')
                {
                    alert(resp.error);
                }
                else
                {
                    window.open(resp.link, "_blank");
                }
            }
        });
    }
}

function showPopup()
{
    $("#modalShowNotice").modal("show");
}
