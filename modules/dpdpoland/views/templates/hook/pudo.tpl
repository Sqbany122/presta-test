{** 2014 DPD Polska Sp. z o.o.
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
 * @author JSC INVERTUS www.invertus.lt <help@invertus.lt>
 * @copyright 2014 DPD Polska Sp. z o.o.
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of DPD Polska Sp. z o.o.
 *}

<div class="pudo-map-container">
    <script id="dpd-widget" type="text/javascript">
        var id_pudo_carrier = '{$id_pudo_carrier|intval}';

        function pointSelected(pudoCode)
        {
            dpdPolandPointId = pudoCode;
            $.ajax("{$dpdpoland_ajax_uri|escape:'htmlall':'UTF-8'}", {
                data: {
                    'pudo_code': pudoCode,
                    'save_pudo_id': 1,
                    'token': "{$dpdpoland_token|escape:'htmlall':'UTF-8'}",
                    'id_cart': "{$dpdpoland_cart|intval}"
                }
            });
            togglePudoMap();
            togglePudoMap17();
            togglePudoMap14();
        }
    </script>

    <script type="text/javascript" src="//pudofinder.dpd.com.pl/source/dpd_widget.js?key=1ae3418e27627ab52bebdcc1a958fa04"></script>
    <br /><br />
</div>
