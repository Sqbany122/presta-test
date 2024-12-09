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
 * @author    PrestaChamps <zoli@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */
function saveForm() {
    $('form#configuration_form').submit();
}

document.addEventListener("DOMContentLoaded", function () {
    $(".champs-button-documentation").click(function () {
        $('.documentation-dropdown').toggle();
    });
});