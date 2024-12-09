/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */
function htmlDecode(input) {
    var e = document.createElement('div');
    e.innerHTML = input;
    return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
}

$(document).ready(function () {
    var container = $("<div class='ps-17-newsletter-checkbox'><input name='gdpr_consent_chkbox' type='checkbox' value='1' required></div>");
    $(container).append("<label>" + htmlDecode(gdprSettings.newsletterConsentText) + "</label>");
    $('input[name=submitNewsletter][type=submit]').parent().append(container);
});