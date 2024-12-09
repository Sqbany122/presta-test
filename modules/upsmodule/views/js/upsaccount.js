/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

$(document).ready(function(){
    $(window).load(function () {

        listFieldsError.forEach(function(element){
            document.getElementById(element).style.borderColor="red";
        });

        var dateNow = $("#datepickerValue").val();
        if (dateNow)
        {
            $( "#datepicker" ).datepicker({dateFormat: 'mm/dd/yy'}).datepicker("setDate", dateNow);
        }
        else
        {
            $( "#datepicker" ).datepicker({dateFormat: 'mm/dd/yy'}).datepicker("setDate", new Date());
        }

        if ($("#checkError").text() == 1)
        {
            if (optionChoose == 1)
            {
                $("#rate04").prop('checked', true);
                $(".rate04_show").removeClass("upshidden");
                $(".rate05_show").addClass("upshidden");
                $(".rate06_show").addClass("upshidden");
            }

            if (optionChoose == 2)
            {
                $("#rate05").prop('checked', true);
                $(".rate06_show").addClass("upshidden");
                $(".rate04_show").addClass("upshidden");
                $(".rate05_show").removeClass("upshidden");
            }

            if (optionChoose == 0)
            {
                $("#rate06").prop('checked', true);
                $(".rate06_show").removeClass("upshidden");
                $(".rate05_show").addClass("upshidden");
                $(".rate04_show").addClass("upshidden");
            }
        }
    });

    $('#rate04').change(function(){
        if ($(this).is(':checked')){
            $(".rate04_show").removeClass("upshidden");
            $(".rate05_show").addClass("upshidden");
            $(".rate06_show").addClass("upshidden");
            var today = moment().format('YYYY-MM-DD');
            $('#InvoiceDate').val(today);
        }
    });
    $('#rate05').change(function(){
        if ($(this).is(':checked')){
            $(".rate05_show").removeClass("upshidden");
            $(".rate04_show").addClass("upshidden");
            $(".rate06_show").addClass("upshidden");
        }
    });
    $('#rate06').change(function(){
        if ($(this).is(':checked')){
            $(".rate06_show").removeClass("upshidden");
            $(".rate05_show").addClass("upshidden");
            $(".rate04_show").addClass("upshidden");
        }
    });
});
