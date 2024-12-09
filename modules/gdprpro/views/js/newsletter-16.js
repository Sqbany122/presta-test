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
function htmlDecode(input){
    var e = document.createElement('div');
    e.innerHTML = input;
    return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
}
$(document).one('ready', function () {
    var container = $("<div class='ps-16-newsletter-checkbox'></div>");
    $(container).append("<label>" + htmlDecode(gdprSettings.newsletterConsentText) + "<input type='checkbox' data-no-uniform='true' required class='not_uniform'></label>");
    $('button[name=submitNewsletter]').after(container);
});