/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

$(document).ready(function(){
    $("#configuration_form_submit_btn").addClass("disabled");

    $("#checkbox_agreed_checkbox1").click(function()
    {
        var arrSelected = $('#checkbox_agreed_checkbox1:checked');
        var flagCheckAPI = $('#UPS_MODULE_FLAG_CHECK_ERROR').val();
        if (arrSelected.length == 0 && flagCheckAPI == 'error')
        {
            $("#configuration_form_submit_btn").addClass("disabled");
        }
        else if (arrSelected.length == 1 && flagCheckAPI != 'error')
        {
            $("#configuration_form_submit_btn").removeClass("disabled");
        }
    })

});

function printTerm()
{
    $.ajax({
        type: 'POST',
        url: 'index.php?controller=AdminUpsTermCondition',
        data: {
            token: token,
            ajax: true,
            action: 'PrintTerm',
        },
        dataType: 'json',
        success: function(resp,textStatus,jqXHR) {
            var popupContent = '';
            popupContent += '<h2>' + resp.title_Term + '</h2>';
            popupContent += '<div>' + resp.content_Term + '</div>';
            var printWindow = window.open('', '', 'height=800,width=1000,scrollbars=yes,resizable');
            printWindow.document.write('<html><head><title>"UPS Terms and Conditions"</title>');
            printWindow.document.write('</head><body style="white-space: pre-wrap;">');
            printWindow.document.write(popupContent );
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    });
}

function showPopupTerm()
{
    $("#modalShowTerm").modal("show");
}
