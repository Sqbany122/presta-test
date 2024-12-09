/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

$(document).ready(function() {
    $(window).load(function () {
        document.getElementsByClassName("form-group")[2].setAttribute("id", "defaultOptior");
        document.getElementsByClassName("form-group")[3].setAttribute("id", "selectShippingToApDelivery");
        document.getElementsByClassName("form-group")[4].setAttribute("id", "displaySetting");
        document.getElementsByClassName("form-group")[5].setAttribute("id", "ChooseAultSigntures");
        document.getElementsByClassName("form-group")[6].setAttribute("id", "numOfAccess");
        document.getElementsByClassName("form-group")[7].setAttribute("id", "ApsInRange");
        document.getElementsByClassName("form-group")[8].setAttribute("id", "ChooseAccToApDelivery");
        document.getElementsByClassName("form-group")[11].setAttribute("id", "selectShippingToAddDelivery");
        document.getElementsByClassName("form-group")[12].setAttribute("id", "ChooseAccToAddDelivery");
        if (document.getElementById("UPS_SP_SERV_AP_DELIVERY_on").checked == true)
        { 
            document.getElementById("displaySetting").hidden = "";
            document.getElementById("numOfAccess").hidden = "";
            document.getElementById("defaultOptior").hidden = "";
            document.getElementById("selectShippingToApDelivery").hidden = "";
            document.getElementById("ApsInRange").hidden = "";
            document.getElementById("ChooseAccToApDelivery").hidden = "";
			document.getElementById("ChooseAultSigntures").hidden = "";
        }

        else{ 
            document.getElementById("defaultOptior").hidden = "hidden";
            document.getElementById("selectShippingToApDelivery").hidden = "hidden";
            document.getElementById("displaySetting").hidden = "hidden";
            document.getElementById("numOfAccess").hidden = "hidden";
            document.getElementById("ApsInRange").hidden = "hidden";
            document.getElementById("ChooseAccToApDelivery").hidden = "hidden";
            document.getElementById("UPS_SP_SERV_AP_CHOOSE_ACC").hidden = "hidden";
        }

        if (document.getElementById("UPS_SP_SERV_ADDRESS_DELIVERY_on").checked == true)
        {
            document.getElementById("selectShippingToAddDelivery").hidden = "";
            document.getElementById("ChooseAccToAddDelivery").hidden = "";
        }

        else{
            document.getElementById("selectShippingToAddDelivery").hidden = "hidden";
            document.getElementById("ChooseAccToAddDelivery").hidden = "hidden";
        }
    });

    $("#UPS_SP_SERV_AP_DELIVERY_off").click(function () { 
        document.getElementById("defaultOptior").hidden = "hidden";
        document.getElementById("selectShippingToApDelivery").hidden = "hidden";
        document.getElementById("displaySetting").hidden = "hidden";
        document.getElementById("numOfAccess").hidden = "hidden";
        document.getElementById("ApsInRange").hidden = "hidden";
        document.getElementById("ChooseAccToApDelivery").hidden = "hidden";
        document.getElementById("UPS_SP_SERV_AP_CHOOSE_ACC").hidden = "hidden";
        document.getElementById("ChooseAultSigntures").hidden = "hidden";
        document.getElementById("UPS_SP_SERV_AP_DELIVERY_on") = "UPS_SP_SERV_AP_DELIVERY_off";
        return false;
    });
    $("#UPS_SP_SERV_AP_DELIVERY_on").click(function () {
        document.getElementById("displaySetting").hidden = "";
        document.getElementById("numOfAccess").hidden = "";
        document.getElementById("defaultOptior").hidden = "";
        document.getElementById("selectShippingToApDelivery").hidden = "";
        document.getElementById("ApsInRange").hidden = "";
        document.getElementById("ChooseAccToApDelivery").hidden = "";
		document.getElementById("ChooseAultSigntures").hidden = "";
        document.getElementById("UPS_SP_SERV_AP_DELIVERY_off")= "UPS_SP_SERV_AP_DELIVERY_on";
        return false;
    });
    $("#UPS_SP_SERV_ADDRESS_DELIVERY_off").click(function () {
        document.getElementById("selectShippingToAddDelivery").hidden = "hidden";
        document.getElementById("ChooseAccToAddDelivery").hidden = "hidden";
        document.getElementById("UPS_SP_SERV_ADDRESS_DELIVERY_on") = "UPS_SP_SERV_ADDRESS_DELIVERY_off";
        return false;
    });
    $("#UPS_SP_SERV_ADDRESS_DELIVERY_on").click(function () {
        document.getElementById("selectShippingToAddDelivery").hidden = "";
        document.getElementById("ChooseAccToAddDelivery").hidden = "";
        document.getElementById("UPS_SP_SERV_ADDRESS_DELIVERY_off") = "UPS_SP_SERV_ADDRESS_DELIVERY_on";
        return false;
    });

});
