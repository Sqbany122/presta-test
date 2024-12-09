/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

$(document).ready(function(){
    document.title = 'Account • EU Plugin';

    listFieldsError.forEach(function(element){
        document.getElementById(element).style.borderColor="red";
    });

    $(window).load(function () {
        var dateNow = $("#datepickerValue").val();
        if (dateNow)
        {
            $("#datepicker").datepicker({dateFormat: 'mm/dd/yy'}).datepicker("setDate", dateNow);
        }
        else
        {
            $("#datepicker").datepicker({dateFormat: 'mm/dd/yy'}).datepicker("setDate", new Date());
        }

        if ($("#checkError").text() == 1)
        {
            document.getElementById("formAccount").style.display = "block";
            document.getElementById("submitAccountSuccess").style.display = "block";

            if ($("#checkRadio").text() == 1)
            {
                $("#rate07").prop('checked', true);
                $(".rate07_show").removeClass("upshidden");
                $(".rate08_show").addClass("upshidden");
                $('#rate07').val(1);
            }
            else
            {
                // checkValidate("AccountNumber1",/[A-Za-z0-9]{6}/);
            }

            if ($("#checkRemove").text() == 1)
            {
                document.getElementById("AccountNumber").style.borderColor="red";
                document.getElementById("AccountNumber1").style.borderColor="red";
            }
        }
    });

    $('#rate07').change(function(){
        if ($(this).is(':checked')){
            $(".rate07_show").removeClass("upshidden");
            $(".rate08_show").addClass("upshidden");
            var today = moment().format('YYYY-MM-DD');
            $('#InvoiceDate').val(today);
            $('#rate07').val(1);
        }
    });

    $('#rate08').change(function(){
        if ($(this).is(':checked')){
            $(".rate08_show").removeClass("upshidden");
            $(".rate07_show").addClass("upshidden");
            $('#rate08').val(2);
        }
    });
});

function addAccountNumber(){
    document.getElementById("formAccount").style.display = "block";
    document.getElementById("submitAccountSuccess").style.display = "block";
}

function removeAccount(accountNumber)
{
    var link = "index.php?controller=AdminUpsAccountSuccess&deleteAccount=1"
                + "&accountNumber=" + accountNumber
                + "&token=" + token;

    $.ajax({
        type: 'POST',
        url: 'index.php?controller=AdminUpsAccountSuccess',
        data: {
            token: token,
            ajax: true,
            action: 'ValidateAccountRemove',
            accountNumber: accountNumber,
        },
        dataType : 'json',
        success : function(resp, textStatus, jqXHR)
        {
            if (resp)
            {
                if (confirm('When this account number is removed here, its associated shipping service will be associated to the default account number.') == true)
                {
                    window.open(link, "_self");
                }
            }
            else
            {
                window.open(link, "_self");
            }
        }
    });
}
