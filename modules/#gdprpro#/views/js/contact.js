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
document.addEventListener("DOMContentLoaded", function (event) {
    var content = $("#gdpr-contact-consent").html();
    $("#gdpr-contact-consent").html("");
    if ($('button[name=submitMessage]').length > 0) {
        // $(submitButton).insertBefore(content);
        $('button[name=submitMessage]:first').before(content);
    } else {
        console.error("[GDPRPRO] Cant find the submit button, please make sure that contact forms submit button has the `submitMessage` name");
    }
});