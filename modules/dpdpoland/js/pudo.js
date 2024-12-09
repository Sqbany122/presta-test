/**
 * 2014 DPD Polska Sp. z o.o.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * prestashop@dpd.com.pl so we can send you a copy immediately.
 *
 *  @author    JSC INVERTUS www.invertus.lt <help@invertus.lt>
 *  @copyright 2014 DPD Polska Sp. z o.o.
 *  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of DPD Polska Sp. z o.o.
 */

var animation_speed = 'fast';
var dpdPolandPointId = 0;
$(document).ready(function(){
    togglePudoMap();
    togglePudoMap17();
    togglePudoMap14();

    $(document).on('click', '.delivery_option_radio', togglePudoMap);
    $(document).on('click', 'input[name^="delivery_option"]', togglePudoMap17);
    $(document).on('click', 'input[name="id_carrier"]', togglePudoMap14);
});

function toggleCheckoutButton(idSelectedCarrier)
{
    if (idSelectedCarrier == id_pudo_carrier && dpdPolandPointId == 0) {
        $('button[name="processCarrier"]').attr('disabled');
    }

    if (idSelectedCarrier != id_pudo_carrier || dpdPolandPointId != 0) {
        $('button[name="processCarrier"]').removeAttrs('disabled');
    }

}
function togglePudoMap()
{
    var id_selected_carrier = $('.delivery_option_radio:checked').val();

    if (typeof id_selected_carrier == 'undefined') {
        return;
    }

    id_selected_carrier = id_selected_carrier.replace(',', '');

    if (typeof id_selected_carrier == 'undefined' || id_selected_carrier == 0) {
        return;
    }

    if (id_selected_carrier == id_pudo_carrier) {
        $('.pudo-map-container').slideDown(animation_speed);
    } else {
        $('.pudo-map-container').slideUp(animation_speed);
    }
    if (id_selected_carrier == id_pudo_carrier && dpdPolandPointId == 0) {
        $('button[name="processCarrier"]').attr('disabled', 'disabled');
    }

    if (id_selected_carrier != id_pudo_carrier || dpdPolandPointId != 0) {
        $('button[name="processCarrier"]').removeAttr('disabled');
    }
}

function togglePudoMap17()
{
    var id_selected_carrier_ps17 = $('input[name^="delivery_option"]:checked').val();

    if (typeof id_selected_carrier_ps17 == 'undefined') {
        return;
    }

    id_selected_carrier_ps17 = id_selected_carrier_ps17.replace(',', '');

    if (typeof id_selected_carrier_ps17 == 'undefined' || id_selected_carrier_ps17 == 0) {
        return;
    }


    if (id_selected_carrier_ps17 == id_pudo_carrier) {
        $('.pudo-map-container').slideDown(animation_speed);
    } else {
        $('.pudo-map-container').slideUp(animation_speed);
    }
    if (id_selected_carrier_ps17 == id_pudo_carrier && dpdPolandPointId == 0) {
        $('button[name="confirmDeliveryOption"]').attr('disabled','disabled');
    }
    console.log(dpdPolandPointId);
    console.log(id_selected_carrier_ps17);
    console.log(id_pudo_carrier);
    if (id_selected_carrier_ps17 != id_pudo_carrier || dpdPolandPointId != 0) {
        $('button[name="confirmDeliveryOption"]').removeAttr('disabled');
    }
}

function togglePudoMap14() {
    var id_selected_carrier_ps14 = $('input[name="id_carrier"]:checked').val();

    if (typeof id_selected_carrier_ps14 == 'undefined') {
        return;
    }

    id_selected_carrier_ps14 = id_selected_carrier_ps14.replace(',', '');

    if (typeof id_selected_carrier_ps14 == 'undefined' || id_selected_carrier_ps14 == 0) {
        return;
    }

    if (id_selected_carrier_ps14 == id_pudo_carrier) {
        $('.pudo-map-container').slideDown(animation_speed);
    } else {
        $('.pudo-map-container').slideUp(animation_speed);
    }
}
/**
 * 2014 DPD Polska Sp. z o.o.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * prestashop@dpd.com.pl so we can send you a copy immediately.
 *
 *  @author    JSC INVERTUS www.invertus.lt <help@invertus.lt>
 *  @copyright 2014 DPD Polska Sp. z o.o.
 *  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of DPD Polska Sp. z o.o.
 */

var animation_speed = 'fast';
var dpdPolandPointId = 0;
$(document).ready(function(){
    togglePudoMap();
    togglePudoMap17();
    togglePudoMap14();

    $(document).on('click', '.delivery_option_radio', togglePudoMap);
    $(document).on('click', 'input[name^="delivery_option"]', togglePudoMap17);
    $(document).on('click', 'input[name="id_carrier"]', togglePudoMap14);
});

function toggleCheckoutButton(idSelectedCarrier)
{
    if (idSelectedCarrier == id_pudo_carrier && dpdPolandPointId == 0) {
        $('button[name="processCarrier"]').attr('disabled');
    }

    if (idSelectedCarrier != id_pudo_carrier || dpdPolandPointId != 0) {
        $('button[name="processCarrier"]').removeAttrs('disabled');
    }

}
function togglePudoMap()
{
    var id_selected_carrier = $('.delivery_option_radio:checked').val();

    if (typeof id_selected_carrier == 'undefined') {
        return;
    }

    id_selected_carrier = id_selected_carrier.replace(',', '');

    if (typeof id_selected_carrier == 'undefined' || id_selected_carrier == 0) {
        return;
    }

    if (id_selected_carrier == id_pudo_carrier) {
        $('.pudo-map-container').slideDown(animation_speed);
    } else {
        $('.pudo-map-container').slideUp(animation_speed);
    }
    if (id_selected_carrier == id_pudo_carrier && dpdPolandPointId == 0) {
        $('button[name="processCarrier"]').attr('disabled', 'disabled');
    }

    if (id_selected_carrier != id_pudo_carrier || dpdPolandPointId != 0) {
        $('button[name="processCarrier"]').removeAttr('disabled');
    }
}

function togglePudoMap17()
{
    var id_selected_carrier_ps17 = $('input[name^="delivery_option"]:checked').val();

    if (typeof id_selected_carrier_ps17 == 'undefined') {
        return;
    }

    id_selected_carrier_ps17 = id_selected_carrier_ps17.replace(',', '');

    if (typeof id_selected_carrier_ps17 == 'undefined' || id_selected_carrier_ps17 == 0) {
        return;
    }


    if (id_selected_carrier_ps17 == id_pudo_carrier) {
        $('.pudo-map-container').slideDown(animation_speed);
    } else {
        $('.pudo-map-container').slideUp(animation_speed);
    }
    if (id_selected_carrier_ps17 == id_pudo_carrier && dpdPolandPointId == 0) {
        $('button[name="confirmDeliveryOption"]').attr('disabled','disabled');
    }
    if (id_selected_carrier_ps17 != id_pudo_carrier || dpdPolandPointId != 0) {
        $('button[name="confirmDeliveryOption"]').removeAttr('disabled');
    }
}

function togglePudoMap14() {
    var id_selected_carrier_ps14 = $('input[name="id_carrier"]:checked').val();

    if (typeof id_selected_carrier_ps14 == 'undefined') {
        return;
    }

    id_selected_carrier_ps14 = id_selected_carrier_ps14.replace(',', '');

    if (typeof id_selected_carrier_ps14 == 'undefined' || id_selected_carrier_ps14 == 0) {
        return;
    }

    if (id_selected_carrier_ps14 == id_pudo_carrier) {
        $('.pudo-map-container').slideDown(animation_speed);
    } else {
        $('.pudo-map-container').slideUp(animation_speed);
    }
}
