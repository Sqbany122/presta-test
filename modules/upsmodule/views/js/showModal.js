/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

var getExportUrl = window.location.href + "&exportorder";

function disableButtons()
{
    $("#page-header-desc-order-export_open_orders").parents().addClass("disabled");
    $("#page-header-desc-order-archive_orders").parents().addClass("disabled");
    $("#page-header-desc-order-export_open_orders").removeAttr("href");
}

function enableButtons()
{
    $("#page-header-desc-order-export_open_orders").parents().removeClass("disabled");
    $("#page-header-desc-order-archive_orders").parents().removeClass("disabled");
    $("#page-header-desc-order-export_open_orders").attr("href", getExportUrl);
}

function disableButtonCreateShipment()
{
    $("#page-header-desc-order-new_single_shipments").parents().addClass("disabled");
    $("#page-header-desc-order-print_label_zpl").parents().addClass("disabled");
}

function enableButtonCreateShipment()
{
    $("#page-header-desc-order-new_single_shipments").parents().removeClass("disabled");
    $("#page-header-desc-order-print_label_zpl").parents().removeClass("disabled");
}

function showButtons(arrSelected)
{
    if (arrSelected.length == 0)
    {
        disableButtons();
        disableButtonCreateShipment();
    }
    else if (arrSelected.length > 0)
    {
        enableButtons();
        enableButtonCreateShipment();
    }
}

$(document).ready(function() {
    var arrSelected = $('.noborder:checkbox:checked');

    if (arrSelected.length > 1) {
        if ($("#page-header-desc-order-new_batch_shipments").parents().hasClass("disabled"))
        {
            $("#page-header-desc-order-new_batch_shipments").parents().removeClass("disabled");
        }
    } else {
        $("#page-header-desc-order-new_batch_shipments").parents().addClass("disabled");
    }
    if (countRow > 1)
    {
        disableButtons();
        disableButtonCreateShipment();
        $("#page-header-desc-order-new_batch_shipments").parents().addClass("disabled");
    }
    else
    {
        enableButtons();
        enableButtonCreateShipment();
        if (!$("#page-header-desc-order-new_batch_shipments").parents().hasClass("disabled"))
        {
            $("#page-header-desc-order-new_batch_shipments").parents().addClass("disabled");
        }
    }
    $(".dropdown-menu").click(function(){
        var arrSelected = $('.noborder:checkbox:checked');
        if (arrSelected.length > 1) {
            console.log(arrSelected.length);
            if ($("#page-header-desc-order-new_batch_shipments").parents().hasClass("disabled"))
            {
                $("#page-header-desc-order-new_batch_shipments").parents().removeClass("disabled");
            }
        } else {
            console.log(arrSelected.length);
            $("#page-header-desc-order-new_batch_shipments").parents().addClass("disabled");
        }
    });

    $(".noborder").click(function()
    {
        var arrSelected = $('.noborder:checkbox:checked');
        if (arrSelected.length == 0)
        {
            disableButtons();
            disableButtonCreateShipment();
            $("#page-header-desc-order-new_batch_shipments").parents().addClass("disabled");
        }
        else if (arrSelected.length == 1)
        {
            enableButtons();
            enableButtonCreateShipment();
            $("#page-header-desc-order-new_batch_shipments").parents().addClass("disabled");
        }
        else
        {
            enableButtonCreateShipment();
            if ($("#page-header-desc-order-new_batch_shipments").parents().hasClass("disabled"))
            {
                $("#page-header-desc-order-new_batch_shipments").parents().removeClass("disabled");
            }
        }
    });

    $(".close").click(function() {
        location.reload();
    });
});

function formatAddress(shipTo)
{
    var element = '';

    if (typeof shipTo.addressLine1 !== "undefined" && shipTo.addressLine1) {
        element += shipTo.addressLine1;
    }

    if (typeof shipTo.addressLine2 !== "undefined" && shipTo.addressLine2) {
        element += '<br>' + shipTo.addressLine2;
    }

    if (typeof shipTo.addressLine3 !== "undefined" && shipTo.addressLine3) {
        element += '<br>' + shipTo.addressLine3;
    }

    if (typeof shipTo.city !== "undefined" && shipTo.city) {
        element += '<br>' + shipTo.city;
    }

    if (typeof shipTo.state !== "undefined" && shipTo.state) {
        element += '<br>' + shipTo.state;
    }

    if (typeof shipTo.postcode !== "undefined" && shipTo.postcode) {
        element += ', ' + shipTo.postcode;
    }

    if (typeof shipTo.country !== "undefined" && shipTo.country) {
        element += '<br>' + shipTo.country;
    }

    return element;
}

function getShipToAddress(shipTo)
{
    var element = '';
    var checked = '';

    for (var i = 0, len = shipTo.length; i < len; i++)
    {
        if (len > 1)
        {
            if (i == 0)
            {
                checked = 'checked';
            }
            else
            {
                checked = '';
            }

            element += '<input name="batchShipTo" class="form-check-input" type="radio"'
            +  checked + ' value="' + shipTo[i].orderId + '">  ';
        }

        element += '<label id="mShipToName"><strong>' + shipTo[i].name + '</strong></label>'
        + '<p class="card-text small-space" id="mShipToAddress1">';

        element += formatAddress(shipTo[i]);

        if (typeof shipTo[i].email !== "undefined" && shipTo[i].email)
        {
            element += '<br>' + shipTo[i].email;
        }

        if (typeof shipTo[i].phone !== "undefined" && shipTo[i].phone)
        {
            element += '<br>' + shipTo[i].phone;
        }
        element += '</p>';
    }

    return element;
}

function resetContent()
{
    if (!$(".alert-danger").hasClass('hidden'))
    {
        $(".alert-danger").addClass('hidden');
    }

    $("#shipmentDateArrival").html('');
    $("#shipmentFeeSingle").html('');
    $("#numberPackageSingle").val('');
}

function packageBtn()
{
    var index = Number($("#numberPackageSingle").val());
    if (index > 0)
    {
        for( var i = 1; i < (index + 2); i++)
        {
            $("#package" + i).remove();
            $("#customPackage" + i).remove();
        }
    }
    else
    {
        $("#customPackage1").remove();
    }
}

function accountComponent(accounts, accountDefault)
{
    var cmbCustomer = '';

    if (accounts)
    {
        $.each(accounts, function(accountNumber, accountName)
        {
            if (accountDefault == accountNumber)
            {
                cmbCustomer +='<option selected value="'+ accountNumber +'">'+ accountName +'</option>';
            }
            else
            {
                cmbCustomer +='<option value="'+ accountNumber +'">'+ accountName +'</option>';
            }
        });
    }
    $("#cmbCustomer").html(cmbCustomer);
}

function showCreateShipmentModal()
{
    $('.accessorial-description').parent().show();
    var arrSelected = $('.noborder:checkbox:checked');
    var arrSeelectedOrder = [];

    $("#txtScreen").val('0');

    if (countRow == 1)
    {
        arrSeelectedOrder.push(orderID);
    } else {
        if ( arrSelected.length == 1 )
        {
            arrSeelectedOrder.push(arrSelected.val());
        }
        else if ( arrSelected.length > 1 )
        {
            $.each(arrSelected, function(index, value) {
                arrSeelectedOrder.push(value.value);
            });
            // merge shipment
            $("#txtScreen").val('3');
        }
    }

    $.ajax({
        type: 'POST',
        url: 'index.php?controller=AdminUpsOpenOrders',
        data :
        {
            token: token,
            ajax : true,
            action : 'GetOrdersByIds',
            orderIds : arrSeelectedOrder,
        },
        dataType : 'json',
        success : function(resp,textStatus,jqXHR)
        {
            if (resp.errors == false)
            {
                packageBtn();
                resetContent();
                $("#editShipment").removeClass('hidden');
                $("#cancelEditShipment").addClass('hidden');

                $(".chosen-single").html(
                    '<span>' + resp.defaultPackage + '</span><div><b></b></div>'
                );
                $("#shipType").html(resp.serviceTypeName);
                $("#shippingService").html(resp.shippingService);
                $("#shipCode").html(resp.accessorials);
                $("#shipFromName").html(resp.shipFromName);
                $("#shipFrom").html(resp.shipFromAddress);
                $("#shipToAddress").html(getShipToAddress(resp.shipTo));
                $("#cmbCustomer").val(resp.accountDefault);
                $("#allOrderID").val(arrSeelectedOrder.toString());
                $("#orderID").val(arrSeelectedOrder[0]);
                $("#cmbPackageSingle option[value='custom_package']").remove();
                $('#cmbPackageSingle').append($('<option>', {
                    value: 'custom_package',
                    // text: 'Custom Package'
                    text: resp.txtCustomPackage
                }));
                $("#modalSingleShipment").modal("show");
            }
            else
            {
                alert(resp.errors);
            }
        }
    });
}

function showBatchShipmentModal()
{
    var arrSelected = $('.noborder:checkbox:checked');
    var arrSelectedOrder = [];

    if (countRow == 0) {
        return false;
    } else if (countRow == 1) {
        return false;
    } else {
        if (arrSelected.length == 1) {
            return false;
        } else if (arrSelected.length > 1) {
            $.each(arrSelected, function(index, value) {
                arrSelectedOrder.push(value.value);
            });
        }
    }

    $.ajax({
        type: 'POST',
        url: 'index.php?controller=AdminUpsOpenOrders',
        data :
        {
            token: token,
            ajax : true,
            action : 'GetOrdersByIds',
            orderIds : arrSelectedOrder,
            createBatch: true,
        },
        dataType : 'json',
        success : function(resp,textStatus,jqXHR)
        {
            resetContent();
            $(".list-orders").html('');
            $("#batchShipment").removeClass('disabled');
            if (resp.errors == false) {
                $("#batchOrderIds").val(arrSelectedOrder.toString());
                $("#upsAccount").val(resp.accountDefault);

                arrSelectedOrder.forEach(function(el) {
                    $(".list-orders").append('<li id="' + el + '">' + el + "</li>");
                });
                $("#modalBatchShipment").modal("show");
            } else {
                alert(resp.errors);
            }
        }
    });
}
