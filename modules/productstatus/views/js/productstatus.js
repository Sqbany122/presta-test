/**
 * Created with PhpStorm.
 * User: Quantum, aka Eduard Faizullin
 * Date: 27.12.13
 * Time: 10:48
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author Andreika
 *  @copyright  Andreika
 *  @version  2.8.5
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

function sendAjax(field, id, value)
{
    $.ajax({
        url: '/modules/productstatus/ajax.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action:       'setDate',
            id_order_detail:     id,
            date:    value,
            field: field
        },

        success: function(data){
        }
    })
}


jQuery(function(){

    $( ".datepicker" ).datepicker({
        dateFormat: "yy-mm-dd"
    });

    $('select.status_selector').change(function(){
        var activeObj = this;
        var userData = $('#user_data');


        //console.log ( $(activeObj).val() );

        $.ajax({
            url: '/modules/productstatus/ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action:       'setStatus',
                id_state:     $(activeObj).val(),
                id_detail:    $(activeObj).data('id_detail'),
                id_employee:  userData.data('id_employee'),
                id_lang:      userData.data('lang_id')
            },

            success: function(data){

                $(activeObj).css('background-color', ($('option:selected',activeObj).data('bgcolor') != '' ? $('option:selected',activeObj).data('bgcolor') : 'transparent'));
                var textColor = fixColors( ($('option', activeObj).data('bgcolor') != '' ? $('option', activeObj).data('bgcolor') : '#ffffff'));
                jQuery(activeObj).css('color', textColor);
            }
        })
    });

    $('.status_selector').each(function(){
        var textColor = fixColors( $(this) );
        $(this).css('color', textColor);
    });

    var hoverDelay;

    $('td.order_status_change .show_history').hover(function(e){
        var activeObj = this;
        hoverDelay = setTimeout(function(){
            getHistoryList(activeObj);
            $('.status_history', activeObj).slideDown();
        }, 500);
    }, function(e){
        clearTimeout(hoverDelay);
        $('td.order_status_change .status_history').slideUp();
    });


    function getHistoryList(el){
        $('td.order_status_change .status_history ul').html('');

        $.ajax({
            url: '/modules/productstatus/ajax.php',
            type: 'GET',
            dataType: 'json',
            data: {
                action: 'getHistory',
                id_order_detail: $('select', $(el).closest('td')).data('id_detail'),
                id_lang: $('select', $(el).closest('td')).data('id_lang')
            },
            success: function(data){
                if(!data.result) {
                    $('td.order_status_change .status_history ul').append('<li class="empty"> No data received </li>');
                    return;
                };

                $.each(data.result, function(key,value){
                    var field_added = value.added;
                    field_name = ' - ' + value.name;
                    field_employee = value.employee ? ' &nbsp;<span>(' + value.employee + ')</span>' : '';

                    $('td.order_status_change .status_history ul').append('<li>' + field_added + field_name + field_employee + '</li>');

                });
            },
            error: function(data){
                $('td.order_status_change .status_history ul').append('<li class="empty">' + data.statusText + '</li>');
            }
        });
    }

});

/*

$(function() {

    // Event in AdminProductStatus tab, List view
    $('td.status_change select').change(function(){

        var activeObj = this;
        var userData = $('#user_data')

        //console.log ( $(activeObj).val() );

        $.ajax({
            url: '/modules/productstatus/ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action:       'setStatus',
                id_state:     $(activeObj).val(),
                id_detail:    $(activeObj).data('id_detail'),
                id_employee:  userData.data('id_employee'),
                id_lang:      userData.data('lang_id'),
            },

            success: function(data){

                $(activeObj).closest('tr').css('background-color', (data.result.color != '' ? data.result.color : 'transparent'));

                var textColor = fixColors( (data.result.color != '' ? data.result.color : '#ffffff'));
                $(activeObj).closest('tr').find('td').css('color', textColor);

            }

        })
    });


    // Event in AdminOrders tab, One order view
    $('td.order_status_change select').change(function(){

        var activeObj = this;

        $.ajax({
            url: '/modules/productstatus/ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action:       'setStatus',
                id_state:     $(activeObj).val(),
                id_detail:    $(activeObj).data('id_detail'),
                id_employee:  $(activeObj).data('id_employee'),
                id_lang:      $(activeObj).data('id_lang'),
            },

            success: function(data){

                $(activeObj).closest('tr').css('background-color', (data.result.color != '' ? data.result.color : '#ffffff'));

                var textColor = fixColors( (data.result.color != '' ? data.result.color : '#ffffff'));
                $(activeObj).closest('.product-row').find('td').css('color', textColor);

            }

        })
    });

    var hoverDelay;

    $('td.order_status_change .show_history').hover(function(e){
        var activeObj = this;
        hoverDelay = setTimeout(function(){
            getHistoryList(activeObj);
            $('.status_history', activeObj).slideDown();
        }, 500);
    }, function(e){
        clearTimeout(hoverDelay);
        $('td.order_status_change .status_history').slideUp();
    });


    function getHistoryList(el){
        $('td.order_status_change .status_history ul').html('');

        $.ajax({
            url: '/modules/productstatus/ajax.php',
            type: 'GET',
            dataType: 'json',
            data: {
                action: 'getHistory',
                id_order_detail: $('select', $(el).closest('tr')).data('id_detail')
            },
            success: function(data){
                if(!data.result) {
                    $('td.order_status_change .status_history ul').append('<li class="empty"> No data received </li>');
                    return;
                };

                $.each(data.result, function(key,value){
                    var field_added = value.added;
                        field_name = ' - ' + value.name;
                        field_employee = value.employee ? ' &nbsp;<span>(' + value.employee + ')</span>' : '';

                    $('td.order_status_change .status_history ul').append('<li>' + field_added + field_name + field_employee + '</li>');

                });
            },
            error: function(data){
                console.log(data);
            }
        });
    }


    // Onload action in AdminProductStatus tab, List view
    $('.AdminProductStatusTable tbody tr').each(function(){
        var textColor = fixColors( $(this) );
        $(this).find('td').css('color', textColor);
    })

    // Onload action in AdminOrders tab, One order view
    $('#ProductStatusTable .product-row').each(function(){
        var textColor = fixColors( $(this) );
        $(this).find('td').css('color', textColor);
    })

});

*/

