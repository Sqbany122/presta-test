/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

var i = 0;
$(document).ready(function(){
    $("#btnCompleted").on('click', function() {
        if (i > 0) {
            $('#btnCompleted').attr('disabled', true);
        }
        i++;
    })
})

