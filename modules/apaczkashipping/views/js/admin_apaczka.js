/**
 * @author    Innovation Software Sp.z.o.o
 * @copyright 2018 Innovation Software Sp.z.o.o
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @category  apaczkashipment
 * @package   apaczkashipment
 * @version   1.1
 */

$(document).ready(function () {
        var apaczkaMap = new ApaczkaMap({
            app_id : '1145247_5de68648cab785.67291228',
            criteria : [
                {field: 'services_receiver', operator:'eq', value:true},
                {field: 'services_sender', operator:'eq', value:true}
            ],
            onChange : function(record) {
                var parcel_locker_map_context = $("#apaczka-parcel_locker_map_context").val();
                if (parcel_locker_map_context === 'sender') {
                    ajaxSaveSenderParcelLocker(record.foreign_access_point_id);
                } else {
                    ajaxSaveReceiverParcelLocker(record.foreign_access_point_id);
                }
            }
        });
        apaczkaMap.setFilterSupplierAllowed(
            ['INPOST'],
            ['INPOST']
        );
        apaczkaMap.setSupplier('INPOST');

    var initial_service_selected = $("#serviceCode option:selected").val();
    if (initial_service_selected === 'PACZKOMAT') {
        showReceiverParcelLockerArea();
    }

    var initial_pickup_type_selected = $("#orderPickupType option:selected").val();
    if (initial_pickup_type_selected === 'BOX_MACHINE') {
        $('#apaczka-chooseParcelLockerSenderRow').show();
    }

    $(document).on('change', '#serviceCode', function () {
        if (this.value === 'PACZKOMAT') {
            showReceiverParcelLockerArea();
        } else {
            if ($('#orderPickupType option:selected').val() === 'BOX_MACHINE') {
                $("#orderPickupType option[value=SELF]").attr('selected', 'selected');
                $("#orderPickupType").trigger('change');
            }
            $('#apaczka-chooseParcelLockerReceiverRow').hide();
        }
    });

    $(document).on('mouseover', '#orderPickupType', function () {
        if ($('#serviceCode option:selected').val() !== 'PACZKOMAT') {
            $("#orderPickupType option[value=BOX_MACHINE]").attr('disabled', 'disabled');
        } else {
            $("#orderPickupType option[value=BOX_MACHINE]").removeAttr('disabled');
        }
    });

    $(document).on('change', '#orderPickupType', function () {
        if (this.value === 'BOX_MACHINE') {
            $('#apaczka-chooseParcelLockerSenderRow').show();
        } else {
            $('#apaczka-chooseParcelLockerSenderRow').hide();
        }
    });

    $(document).on('click', '#openReceiverParcelLockerModalButton', function (e) {
        e.preventDefault();
        $('#apaczka-parcel_locker_map_context').val('receiver');
        var chosenReceiverParcelLocker = $('#apaczka-chosenReceiverParcelLockerName').val();
        apaczkaMap.show({
            point: {foreign_access_point_id: chosenReceiverParcelLocker, supplier: 'INPOST'}
        });
    });

    $(document).on('click', '#openSenderParcelLockerModalButton', function (e) {
        e.preventDefault();
        $('#apaczka-parcel_locker_map_context').val('sender');
        var chosenSenderParcelLocker = $('#apaczka-chosenSenderParcelLockerName').val();
        apaczkaMap.show({
            point: {foreign_access_point_id: chosenSenderParcelLocker, supplier: 'INPOST'}
        });
    });

    $('#orderPickupType').change(function () {
        if ($(this).val() !== 'COURIER')
            $('#orderPickupDetails').hide();
        else
            $('#orderPickupDetails').show();
    }).trigger('change');
    $('#insurance').change(function () {
        if ($(this).val() !== '1')
            $('#insuranceDetails').hide();
        else
            $('#insuranceDetails').show();
    }).trigger('change');
    $('#cod').change(function () {
        if ($(this).val() !== '1')
            $('#codDetails').hide();
        else
            $('#codDetails').show();
    }).trigger('change');
});

function showReceiverParcelLockerArea() {
    $('#shipmentTypeCodeRow').hide();
    $('#noStdField').hide();
    $('#apaczka-chooseParcelLockerReceiverRow').show();
}

function ajaxSaveReceiverParcelLocker(point) {
    var responseContainer = $('.apaczka-ajaxOverlay');
    var responseContainerText = $('.apaczka-message');
    var orderId = $('#apaczka-orderId').val();
    $.ajax({
        type: 'POST',
        headers: {"cache-control": "no-cache"},
        url: apaczka_admin_shipping_controller,
        async: true,
        cache: false,
        dataType: 'json',
        data: 'action=updateParcelLockerCodeInOrder'
        + '&parcel_locker_name=' + point + '&order_id=' + orderId,
        beforeSend: function () {
            $(responseContainer).css('display', 'flex');
        },
        success: function (jsonData) {
            if (jsonData.message) {
                $(responseContainerText).text(jsonData.message);
                $(responseContainerText).removeClass('apaczkaAlertDanger');
                $(responseContainerText).addClass('apaczkaAlertSuccess');
                $(responseContainerText).css('display', 'flex');
                setTimeout(function () {
                    $(responseContainer).css('display', 'none');
                    $(responseContainerText).css('display', 'none');
                    $('#apaczka-chosenReceiverParcelLockerNameText').removeClass('parcelLockerLabelUnchecked');
                    $('#apaczka-chosenReceiverParcelLockerNameText').addClass('parcelLockerLabelChecked');
                    $('#apaczka-chosenReceiverParcelLockerNameText').text(point);
                    $('#apaczka-chosenReceiverParcelLockerName').val(point);
                }, 1000)
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $(responseContainerText).text(textStatus);
            $(responseContainerText).removeClass('apaczkaAlertSuccess');
            $(responseContainerText).addClass('apaczkaAlertDanger');
            $(responseContainerText).css('display', 'flex');
            setTimeout(function () {
                $('#apaczka-chosenReceiverParcelLockerNameText').removeClass('parcelLockerLabelChecked');
                $('#apaczka-chosenReceiverParcelLockerNameText').addClass('parcelLockerLabelUnchecked');
                $(responseContainer).css('display', 'none');
                $(responseContainerText).css('display', 'none');
            }, 1000)
        }
    })
}

function ajaxSaveSenderParcelLocker(point) {
    var responseContainer = $('.apaczka-ajaxOverlay');
    var responseContainerText = $('.apaczka-message');
    var orderId = $('#apaczka-orderId').val();
    $.ajax({
        type: 'POST',
        headers: {"cache-control": "no-cache"},
        url: apaczka_admin_shipping_controller,
        async: true,
        cache: false,
        dataType: 'json',
        data: 'action=saveSenderParcelLockerCodeToOrder'
        + '&sender_parcel_locker_name=' + point + '&order_id=' + orderId,
        beforeSend: function () {
            $(responseContainer).css('display', 'flex');
        },
        success: function (jsonData) {
            if (jsonData.message) {
                $(responseContainerText).text(jsonData.message);
                $(responseContainerText).removeClass('apaczkaAlertDanger');
                $(responseContainerText).addClass('apaczkaAlertSuccess');
                $(responseContainerText).css('display', 'flex');
                setTimeout(function () {
                    $(responseContainer).css('display', 'none');
                    $(responseContainerText).css('display', 'none');
                    $('#apaczka-chosenSenderParcelLockerNameText').text(point);
                    $('#apaczka-chosenSenderParcelLockerNameText').removeClass('parcelLockerLabelUnchecked');
                    $('#apaczka-chosenSenderParcelLockerNameText').addClass('parcelLockerLabelChecked');
                    $('#apaczka-chosenSenderParcelLockerName').val(point);
                }, 1000)
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $(responseContainerText).text(textStatus);
            $(responseContainerText).removeClass('apaczkaAlertSuccess');
            $(responseContainerText).addClass('apaczkaAlertDanger');
            $(responseContainerText).css('display', 'flex');
            setTimeout(function () {
                $('#apaczka-chosenSenderParcelLockerNameText').removeClass('parcelLockerLabelChecked');
                $('#apaczka-chosenSenderParcelLockerNameText').addClass('parcelLockerLabelUnchecked');
                $(responseContainer).css('display', 'none');
                $(responseContainerText).css('display', 'none');
            }, 1000)
        }
    })
}








