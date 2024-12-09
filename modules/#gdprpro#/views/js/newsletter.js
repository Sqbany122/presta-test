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
$(document).ready(function () {
    var container = $("<span class='custom-checkbox'> <input name='gdpr_consent_chkbox' type='checkbox' value='1' required>" +
        "<span><i class='material-icons rtl-no-flip checkbox-checked'></i></span> </span>");
    $(container).append("<label>" + gdprSettings.newsletterConsentText + "</label>");
    $('input[name=submitNewsletter][type=submit]').parent().append(container);
});