/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

$(document).ready(function(){
    $(".filter").addClass("hidden");
})

function showOrderArchivedModal(val)
{
    $.ajax({
        type: 'POST',
        url: 'index.php?controller=AdminUpsArchivedOrders',
        data:
        {
            token: token,
            ajax: true,
            action: 'GetOrderById',
            orderID: val,
        },
        dataType : 'json',
        success : function(resp,textStatus,jqXHR)
        {
            var index;
            var products = new Array();
            var accessPointAddress = '';
            var eshopperAddress = '';

            for (index = 0; index < resp.length; index++) {
                products +=  resp[index].product_quantity + " x " + resp[index].product_name + "<br>";
            }

            //ap_address1
            if (resp[0].ap_address1)
            {
                accessPointAddress += resp[0].ap_address1;
            }

            if (resp[0].ap_address2)
            {
                accessPointAddress += ', ' + resp[0].ap_address2;
            }

            if (resp[0].ap_state)
            {
                accessPointAddress += ', ' + resp[0].ap_state;
            }

            if (resp[0].ap_city)
            {
                accessPointAddress += ', ' + resp[0].ap_city;
            }

            if (resp[0].ap_postcode)
            {
                accessPointAddress += ', ' + resp[0].ap_postcode;
            }

            // Eshoper Address
            if (resp[0].address_delivery1)
            {
                eshopperAddress += resp[0].address_delivery1;
            }

            if (resp[0].address_delivery2)
            {
                eshopperAddress += '<br>' + resp[0].address_delivery2;
            }
            //city
            if (resp[0].city)
            {
                eshopperAddress += '<br>' + resp[0].city;
            }
            //country_name
            if (resp[0].country_name)
            {
                eshopperAddress += '<br>' + resp[0].country_name;
            }

            var totalValue = resp[0].total_paid*1;
            totalValue = totalValue.toFixed(2);

            var shipping_service_full = '';
            if (resp[0].ap_name)
            {
                shipping_service_full = 'To AP (' + resp[0].shipping_service + ')';
            }
            else
            {
                shipping_service_full = 'To Address (' + resp[0].shipping_service + ')';
            }

            $("#accessorials_service").html(resp[0].accessorials_service);
            $("#id_order").html(resp[0].id_order);
            $("#customerID").html(resp[0].firstname + ' ' + resp[0].lastname);
            $("#phone").html(resp[0].phone);
            $("#email").html(resp[0].email);
            $(".product").html(products);
            $("#access_point").html(resp[0].ap_name);
            $("#address_delivery2").html(accessPointAddress);
            $("#address_delivery1").html(eshopperAddress);
            $("#shipping_service").html(shipping_service_full);
            $("#order_value").html(resp[0].currency + ' ' + totalValue);
            $("#order_date").html(resp[0].order_date);
            $("#order_time").html(resp[0].order_time);
            $("#payment_status").html(resp[0].current_state);

            $("#modalOrderArchited").modal("show");
        }
    });
}

function unArchiveOrders()
{
    var textWarning = '';

    $.ajax({
        type: 'POST',
        url: 'index.php?controller=AdminUpsArchivedOrders',
        data: {
            token: token,
            ajax: true,
            action: 'GetText',
        },
        dataType: 'json',
        success : function(resp, textStatus, jqXHR)
        {
            textWarning = resp;
            if (countRow > 1)
            {
                var arrOrderID = [];
                var numberPackageString = '';

                $("input:checkbox[class=noborder]:checked").each(function(){
                    arrOrderID.push($(this).val());
                });

                if (arrOrderID)
                {
                    numberPackageString = arrOrderID.toString();
                }

                if (numberPackageString)
                {
                    if (confirm(textWarning))
                    {
                        $.ajax({
                            type: 'POST',
                            url: 'index.php?controller=AdminUpsArchivedOrders',
                            data: {
                                token: token,
                                ajax: true,
                                action: 'UnarchiveOrders',
                                orderID: numberPackageString,
                            },
                            dataType: 'json',
                            success : function(resp, textStatus, jqXHR)
                            {
                                location.reload();
                            }
                        });
                    }
                }
            }
            else if (countRow == 1)
            {
                var id_order = '';
                if (typeof orderID != 'undefined' && orderID != '') {
                    id_order = orderID;
                } else {
                    id_order = $('table.table.order>tbody>tr> td.pointer.fixed-width-xs.text-center').html();
                }

                if (typeof id_order != 'undefined' && id_order != '') {
                    id_order = id_order.trim();
                }

                if (confirm(textWarning))
                {
                    $.ajax({
                        type: 'POST',
                        url: 'index.php?controller=AdminUpsArchivedOrders',
                        data: {
                            token: token,
                            ajax: true,
                            action: 'UnarchiveOrders',
                            orderID: id_order,
                        },
                        dataType: 'json',
                        success : function(resp, textStatus, jqXHR)
                        {
                            location.reload();
                        }
                    });
                }
            }
        }
    });
}