/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    velsof.com <support@velsof.com>
 * @copyright 2014 Velocity Software Solutions Pvt Ltd
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

function getNextSubscriptionResultPage(page_no) {
    $.ajax({
        type: "POST",
        url: ajax_subscription_page_link,
        data: 'pagination=true&page_no=' + page_no,
        dataType: 'json',
        beforeSend: function () {
            $('.kbloading').show();
        },
        complete: function ()
        {
            $('.kbloading').hide();

        },
        success: function (json) {
            if (json['status']) {
                $('#subscription_box').html('');
                $('#subscription_box').html(json['html']);
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $.gritter.add({
                title: notification_title,
                text: 'Failure',
                class_name: 'gritter-warning',
                sticky: false,
                time: '3000'
            });
        }
    });

}

function removeSubscription(id_subscriber) {
    $.ajax({
        type: "POST",
        url: ajax_subscription_page_link,
        data: 'action=remove_subscription&id_subscriber=' + id_subscriber,
        dataType: 'json',
        beforeSend: function () {
            $('.kbloading').show();
        },
        complete: function ()
        {
            $('.kbloading').hide();
        },
        success: function (json) {
            if (json['status']) {
                $.gritter.add({
                    title: notification_title,
                    text: json['msg'],
                    class_name: 'gritter-success',
                    sticky: false,
                    time: '3000'
                });
                $('#subscription_box').html('');
                $('#subscription_box').html(json['html']);
            } else {
                $.gritter.add({
                    title: notification_title,
                    text: json['msg'],
                    class_name: 'gritter-warning',
                    sticky: false,
                    time: '3000'
                });
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $.gritter.add({
                title: notification_title,
                text: 'Failure',
                class_name: 'gritter-warning',
                sticky: false,
                time: '3000'
            });
        }
    });

}