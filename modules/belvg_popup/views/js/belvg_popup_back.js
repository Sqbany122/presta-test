/**
 * 2007-2018 Belvg
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author Belvg
 *  @copyright  2007-2018 Belvg
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Belvg
 */

(function ($) {
    $(function () {
        cartRuleInitilialization();
        delayOrScrollCountInitialization();


        $('#BELVG_POPUP_DISPLAY_TYPE').change(function ()
        {
            cartRuleInitilialization();
        })

        $('#BELVG_POPUP_TRIGGER_MECHANISM').change(function ()
        {
            delayOrScrollCountInitialization();
        });

        function cartRuleInitilialization()
        {
            if ($('#BELVG_POPUP_DISPLAY_TYPE').val() != 'coupon') {
                $('#BELVG_POPUP_CART_RULE').parents('.form-group').hide()
            } else {
                $('#BELVG_POPUP_CART_RULE').parents('.form-group').show()
            }
        }

        function delayOrScrollCountInitialization()
        {
            if ($('#BELVG_POPUP_TRIGGER_MECHANISM').val() === 'delay') {
                $('#BELVG_POPUP_SCROLL_COUNT').parents('.form-group').hide()
                $('#BELVG_POPUP_DELAY').parents('.form-group').show()
            } else {
                $('#BELVG_POPUP_DELAY').parents('.form-group').hide()
                $('#BELVG_POPUP_SCROLL_COUNT').parents('.form-group').show()
            }
        }
    });
})(jQuery);
