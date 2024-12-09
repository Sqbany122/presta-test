/**
 * 2009-2017 Blue Train Lukasz Kowalczyk / PrestaCafe
 *
 * NOTICE OF LICENSE
 *
 * Lukasz Kowalczyk is the owner of the copyright of this module.
 * All rights of any kind, which are not expressly granted in this
 * License, are entirely and exclusively reserved to and by Lukasz
 * Kowalczyk.
 *
 * You may not rent, lease, transfer, modify or create derivative
 * works based on this module.
 *
 * @author    Lukasz Kowalczyk <lkowalczyk@prestacafe.pl>
 * @copyright Lukasz Kowalczyk
 * @license   LICENSE.txt
 */

var PrestaCafePayu = {};

(function() {
    var self = this;

    /**
     * Make all cells (class=payu-pbl-cell) the height of the tallest
     * cell and highlight the selected payment method, if any, based
     * on the value of input[name=pbl].
     */
    self.preparePaymentMethodCells = function() {
        var $cells = $('.payu-pbl-cell');
        var heights = $cells.map(function() {
            return $(this).height();
        });
        var maxHeight = Math.max.apply(null, heights)+'px';
        $cells.css('height', maxHeight);
        // line-height is necessary for the vertical middle alignment to work
        // (see the ".payu-pbl-cell img" css rule)
        $cells.css('line-height', maxHeight);
        // Restore selection if the back button was clicked
        var currentPblValue = $('input[type="hidden"][name="pbl"]').val();
        $cells.each(function() {
            if ($(this).data('pbl') == currentPblValue) {
                $(this).addClass('payu-pbl-cell-selected');
            }
        });
    };

    /**
     * Called upon clicking on a payment method image. Highlights the image
     * and updates input[name=pbl].
     * @param element the clicked div.payu-pbl-cell
     * @param value pbl value
     */
    self.onClickPaymentMethod = function(element, value) {
        $('.payu-pbl-cell').removeClass('payu-pbl-cell-selected');
        $(element).addClass('payu-pbl-cell-selected');
        $('input[type="hidden"][name="pbl"]').val(value);
    };

    /**
     * No-reenter safeguard for the paymentRedirect function.
     * @type {boolean}
     */
    var paymentLinkDisabled = false;

    self.paymentRedirect = function(event, element, href) {
        event.preventDefault();
        if (paymentLinkDisabled) {
            return;
        }
        paymentLinkDisabled = true;
        window.location.replace(href);
    };
}).apply(PrestaCafePayu);
