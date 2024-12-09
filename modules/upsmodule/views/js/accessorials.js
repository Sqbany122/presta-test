/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

$(document).ready(function() {
    $("#accessory_UPS_ACSRL_ADULT_SIG_REQUIRED").click(function()
    {
        if ($("#accessory_UPS_ACSRL_SIGNATURE_REQUIRED").prop( "checked" ))
        {
            $("#accessory_UPS_ACSRL_SIGNATURE_REQUIRED").prop( "checked", false );
        }
    });

    $("#accessory_UPS_ACSRL_SIGNATURE_REQUIRED").click(function()
    {
        if ($("#accessory_UPS_ACSRL_ADULT_SIG_REQUIRED").prop( "checked" ))
        {
            $("#accessory_UPS_ACSRL_ADULT_SIG_REQUIRED").prop( "checked", false );
        }
    });
});
