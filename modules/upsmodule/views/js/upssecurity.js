/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

$(document).ready(function() {
    document.getElementsByClassName("form-group")[2].setAttribute("id", "xFrameOption");
    document.getElementsByClassName("form-group")[3].setAttribute("id", "frameKillerScript");
    document.getElementsByClassName("form-group")[4].setAttribute("id", "contentSecurity");

    if ($('input[name=UPS_SEC_CLICKJACKING]:checked').val() == 1) {
        $('#xFrameOption').show();
        $('#frameKillerScript').show();
        $('#contentSecurity').show();
    } else {
        $('#xFrameOption').hide();
        $('#frameKillerScript').hide();
        $('#contentSecurity').hide();
    }

    $('input[name=UPS_SEC_CLICKJACKING]').on('click', function() {
        if ($(this).val() == 1) {
            $('#xFrameOption').slideDown();
            $('#frameKillerScript').slideDown();
            $('#contentSecurity').slideDown();
        } else {
            $('#xFrameOption').slideUp();
            $('#frameKillerScript').slideUp();
            $('#contentSecurity').slideUp();
        }
    });
});
