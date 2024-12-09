/**
 * @author    Innovation Software Sp.z.o.o
 * @copyright 2018 Innovation Software Sp.z.o.o
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @category  apaczkashipment
 * @package   apaczkashipment
 * @version   1.1
 */

$(document).ready(function () {
    var initial_carrier_id_value = $("input.delivery_option_radio:checked").val();
    var apaczkaMap = new ApaczkaMap({
        app_id: '1145247_5de68648cab785.67291228',
        criteria: [
            {field: 'services_receiver', operator: 'eq', value: true}
        ],
        onChange: function (record) {
            if (record) {
                sendChosenParcelLockerInfo(record.foreign_access_point_id)
            }
        }
    });
    apaczkaMap.setFilterSupplierAllowed(
        ['INPOST'],
        ['INPOST']
    );
    apaczkaMap.setSupplier('INPOST');
    if (initial_carrier_id_value === parcel_locker_carrier_id + ',') {
        $('#apaczka_showParcelsMap').show();
    } else {
        $('#apaczka_showParcelsMap').hide();
    }

    $(document).on('change', 'input.delivery_option_radio', function () {
        var checked_value = $("input.delivery_option_radio:checked").val();
        if (checked_value === parcel_locker_carrier_id + ',') {
            $('#apaczka_showParcelsMap').show();
        } else {
            $('#apaczka_showParcelsMap').hide();
        }
    });

    $(document).on('click', 'button[name=processCarrier]', function(e) {
        var chosenCarrier = $("input.delivery_option_radio:checked").val();
        if(chosenCarrier === parcel_locker_carrier_id + ','
        && $('#apaczka_parcelLockerCode').val().length === 0) {
            e.preventDefault();
            alert('Prosimy wybrać paczkomat!')
        }
    });

    $(document).on('click', '#apaczka_showParcelsMap', function () {
        apaczkaMap.show({})
    });
});

function sendChosenParcelLockerInfo(parcel_locker_data) {
    var responseContainer = $('#apaczka-ajaxOverlay');
    var responseLoader = $('#apaczka-ajaxLoader');
    var responseContainerText = $('#apaczka-message');
    $.ajax({
        type: 'POST',
        headers: {"cache-control": "no-cache"},
        url: apaczka_shipping_controller,
        async: true,
        cache: false,
        dataType: 'json',
        data: 'action=saveParcelLockerCodeToOrder'
        + '&parcel_locker_name=' + parcel_locker_data,
        beforeSend: function () {
            $(responseContainer).css('display', 'flex');
            $(responseLoader).css('display', 'flex');
        },
        success: function (jsonData) {
            $(responseLoader).css('display', 'none');
            if (jsonData.message) {
                $(responseContainerText).text(jsonData.message);
                $('#apaczka_showParcelsMap').text('Wybrany paczkomat: ' + parcel_locker_data);
                $('#apaczka_parcelLockerCode').val(parcel_locker_data);
                $(responseContainerText).addClass('apaczkaAlertSuccess');
                $(responseContainerText).css('display', 'flex');
                setTimeout(function () {
                    $(responseContainer).css('display', 'none');
                    $(responseContainerText).css('display', 'none');
                }, 1000)
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $(responseLoader).css('display', 'none');
            $(responseContainerText).text(textStatus);
            $(responseContainerText).addClass('apaczkaAlertDanger');
            $(responseContainerText).css('display', 'flex');
            setTimeout(function () {
                $(responseContainer).css('display', 'none');
                $(responseContainerText).css('display', 'none');
            }, 1000)
        }
    })

}

