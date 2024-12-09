/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */
function getKeyPkg(packgeID)
{
    $.ajax({
        type: 'POST',
        url: 'index.php?controller=AdminUpsPkgDimension',
        data: 
        {
            token: token,
            ajax: true,
            package_id: packgeID,
            pkg_function: 'edit',
        },
        dataType: 'json',
        success: function(resp,textStatus,jqXHR)
        {
            $("#packageHiddenID").val(packgeID);
            $("#namePkg").val(resp.name);
            $("#weight").val(resp.weight);
            $("#weightUnit").val(resp.weightUnit);
            $("#lenght").val(resp.lenght);
            $("#width").val(resp.width);
            $("#height").val(resp.height);
            $("#lenghtUnit").val(resp.lenghtUnit);
            $("#submitEdit").addClass('disabled');

            $(".errorPackage").addClass('hidden');
            document.getElementById("namePkg").style.borderColor="#ddf0f7";
            document.getElementById("weight").style.borderColor="#ddf0f7";
            document.getElementById("lenght").style.borderColor="#ddf0f7";
            document.getElementById("width").style.borderColor="#ddf0f7";
            document.getElementById("height").style.borderColor="#ddf0f7";
            $("#modalEditPkg").modal("show");
        }
    });
}

function savePackage() {

    var check1 = checkDimension(2);
    var check2 = checkWeight(2);

    $("#submitFormPackage").attr("data-id", 2);

    if (check1[0] == 0 && check2[0] == 0) {
        editSubmitPackage();
    } else {
        var error   = '';
        var warning = '';
        (check1[0] == 1) ? warning = warning + check1[1] : error = error + check1[1];
        (check2[0] == 1) ? warning = warning + check2[1] : error = error + check2[1];

        if (error == '') {
            $(".errorPackage").addClass('hidden');
            $("#ups-modal-alert-body").html(warning);
            $("#ups-modal-alert").modal('show');
        } else {
            $(".errorPackage").html(error);
            $(".errorPackage").removeClass('hidden');
        }
    }
}

function editSubmitPackage() {
    $.ajax({
        type: 'POST',
        url: 'index.php?controller=AdminUpsPkgDimension',
        data: 
        {
            token: token,
            ajax: true,
            pkg_function_save: 'saveEdit',
            package_id: $("#packageHiddenID").val(),
            height: $("#height").val(),
            namePkg: $("#namePkg").val(),
            weight: $("#weight").val(),
            weightUnit: $("#weightUnit").val(),
            lenght: $("#lenght").val(),
            width: $("#width").val(),
            lenghtUnit: $("#lenghtUnit").val(),
        },
        dataType: 'json',
        success: function(resp,textStatus,jqXHR)
        {
            if (resp.error == '')
            {
                $(".errorPackage").addClass('hidden');
                $("#modalEditPkg").modal("hide");
                location.reload();
            }
            else
            {
                if (resp.existName == 'exist')
                {
                    document.getElementById("namePkg").style.borderColor="red";
                }
                else
                {
                    validateEditPkg();
                }

                $(".errorPackage").html(resp.error);
                $(".errorPackage").removeClass('hidden');
            }
        }
    });
}

function addPackage() {
    validateAddPkg();

    var check1 = checkDimension(1);
    var check2 = checkWeight(1);

    $("#submitFormPackage").attr("data-id", 1);
    $("#error-dimension").html('');

    if (check1[0] == 0 && check2[0] == 0) {
        addSubmitPackage();
    } else {
        var error   = '';
        var warning = '';
        (check1[0] == 1) ? warning = warning + check1[1] : error = error + check1[1];
        (check2[0] == 1) ? warning = warning + check2[1] : error = error + check2[1];

        if (error == '') {
            $(".errorAddPackage").addClass('hidden');
            $("#ups-modal-alert-body").html(warning);
            $("#ups-modal-alert").modal('show');
        } else {
            $(".errorAddPackage").html(error);
            $(".errorAddPackage").removeClass('hidden');
        }
    }
}

function addSubmitPackage() {
    $.ajax({
        type: 'POST',
        url: 'index.php?controller=AdminUpsPkgDimension',
        data:
        {
            token: token,
            ajax: true,
            pkg_function_add: 'addPkg',
            addNamePkg: $("#namePackage").val(),
            add_weight: $("#weightadd").val(),
            addWeightUnit: $("#addweightUnit").val(),
            add_length: $("#lengthadd").val(),
            add_width: $("#widthadd").val(),
            add_height: $("#heightadd").val(),
            addLengthUnit: $("#addlengthUnit").val(),
        },
        dataType: 'json',
        success: function(resp,textStatus,jqXHR)
        {
            if (resp.error == '')
            {
                $(".errorAddPackage").addClass('hidden');
                location.reload();
            }
            else
            {
                $(".errorAddPackage").html(resp.error);
                $(".errorAddPackage").removeClass('hidden');
            }
        }
    })
}
$(document).ready(function() {
    $("#submitFormPackage").on("click", function() {
        var check = $(this).attr('data-id');
        if (check == "1") {
            addSubmitPackage();
        } else {
            editSubmitPackage();
        }
    })
})


function deletePackage(packagesID)
{
    $("#packageHiddenDeleteID").val(packagesID);
    $("#modalDeletePkg").modal("show");
}

function confirmDelete()
{
    $.ajax({
        type: 'POST',
        url: 'index.php?controller=AdminUpsPkgDimension',
        data:
        {
            token: token,
            ajax: true,
            delete_packageid: $("#packageHiddenDeleteID").val(),
            pkg_function_delete: 'delete',
        },
        dataType: 'json',
        success: function(resp,textStatus,jqXHR)
        {
            if (resp.error == '')
            {
                window.location.reload();
            }
        }
    });
}

function handleNextbutton()
{
    $.ajax({
        type: 'POST',
        url: 'index.php?controller=AdminUpsPkgDimension',
        data:
        {
            token: token,
            ajax: true,
            pkg_function_next: 'next',
        },
        dataType: 'json',
        success: function(resp,textStatus,jqXHR)
        {
            if (resp.error == '')
            {
                $(".errorAddPackage").addClass('hidden');
                window.location.replace('index.php?controller=AdminUpsDeliveryRates&token=' + resp.token);
            }
            else
            {
                $(".errorAddPackage").html(resp.error);
                $(".errorAddPackage").removeClass('hidden');
            }
        }
    });
}


var re = /^\d+(\.\d{1,2})?$/;

function validateAddPkg()
{
    var heightadd = document.getElementById("heightadd").value;
    var widthadd = document.getElementById("widthadd").value;
    var lengthadd = document.getElementById("lengthadd").value;
    var weight = document.getElementById("weightadd").value;
    var namePackage = document.getElementById("namePackage").value;

    if (namePackage == '' || namePackage.lenght < 1 || namePackage.lenght > 50)
    {
        document.getElementById("namePackage").style.borderColor="red";
    }
    else
    {
        document.getElementById("namePackage").style.borderColor="#ddf0f7";
    }

    if (heightadd < 0.01 || heightadd > 9999.99 || !re.exec(heightadd))
    {
        document.getElementById("heightadd").style.borderColor="red";
    }
    else
    {
        document.getElementById("heightadd").style.borderColor="#ddf0f7";
    }

    if (widthadd < 0.01 || widthadd > 9999.99 || !re.exec(widthadd))
    {
        document.getElementById("widthadd").style.borderColor="red";
    }
    else
    {
        document.getElementById("widthadd").style.borderColor="#ddf0f7";
    }

    if (lengthadd < 0.01 || lengthadd > 9999.99 || !re.exec(lengthadd))
    {
        document.getElementById("lengthadd").style.borderColor="red";
    }
    else
    {
        document.getElementById("lengthadd").style.borderColor="#ddf0f7";
    }

    if (weight < 0.01 || weight > 9999.99 || !re.exec(weight))
    {
        document.getElementById("weightadd").style.borderColor="red";
    }
    else
    {
        document.getElementById("weightadd").style.borderColor="#ddf0f7";
    }

}

function validateEditPkg()
{
    var height = document.getElementById("height").value;
    var width = document.getElementById("width").value;
    var lenght = document.getElementById("lenght").value;
    var weight = document.getElementById("weight").value;
    var namePkg = document.getElementById("namePkg").value;

    if (namePkg == '' || namePkg.lenght < 1 || namePkg.lenght > 50)
    {
        document.getElementById("namePkg").style.borderColor="red";
    }
    else
    {
        document.getElementById("namePkg").style.borderColor="#ddf0f7";
    }

    if (height < 0.01 || height > 9999.99 || !re.exec(height))
    {
        document.getElementById("height").style.borderColor="red";
    }
    else
    {
        document.getElementById("height").style.borderColor="#ddf0f7";
    }

    if (width < 0.01 || width > 9999.99 || !re.exec(width))
    {
        document.getElementById("width").style.borderColor="red";
    }
    else
    {
        document.getElementById("width").style.borderColor="#ddf0f7";
    }

    if (lenght < 0.01 || lenght > 9999.99 || !re.exec(lenght))
    {
        document.getElementById("lenght").style.borderColor="red";
    }
    else
    {
        document.getElementById("lenght").style.borderColor="#ddf0f7";
    }

    if (weight < 0.01 || weight > 9999.99 || !re.exec(weight))
    {
        document.getElementById("weight").style.borderColor="red";
    }
    else
    {
        document.getElementById("weight").style.borderColor="#ddf0f7";
    }

}

/** massage */
var errorWeightPackageMaximum   = errorWeightPackageMaximum;
var warningWeightPackageMaximum = warningWeightPackageMaximum;
var errorDimension              = errorDimension;
var warningDimensionLang        = warningDimensionLang;

function checkWeight(type) { /** 1 add 2 modify */
    if (type == 1) {
        var weightPackage = parseFloat(jQuery("#weightadd").val());
        var unitWeight    = jQuery("#addweightUnit").val();
    } else {
        var weightPackage = parseFloat(jQuery("#weight").val());
        var unitWeight    = jQuery("#weightUnit").val();
    }

    var htmlErr = '';
    var check   = 0;
    if (unitWeight == 'KGS') {
        if (weightPackage > 20 && weightPackage <= 70) {
            htmlErr = htmlErr + '<p>'+ warningWeightPackageMaximum +'</p>';
            check   = 1;
        } else if (weightPackage > 70) {
            htmlErr = htmlErr + '<p>'+ errorWeightPackageMaximum +'</p>';
            check   = 2;

            if (type == 1) {
                document.getElementById("weightadd").style.borderColor = "red";
            } else {
                document.getElementById("weight").style.borderColor = "red";
            }
        }
    } else {
		if (country == 'US') {
			if (weightPackage > 44 && weightPackage <= 150) {
				htmlErr = htmlErr + '<p>'+ warningWeightPackageMaximum2 +'</p>';
				check   = 1;
			} else if (weightPackage > 150) {
				htmlErr = htmlErr + '<p>'+ errorWeightPackageMaximum3 +'</p>';
				check   = 2;
				if (type == 1) {
					document.getElementById("weightadd").style.borderColor = "red";
				} else {
					document.getElementById("weight").style.borderColor = "red";
				}
			}
		} else {
			if (weightPackage > 154.324) {
				htmlErr = htmlErr + '<p>'+ errorWeightPackageMaximum +'</p>';
				check   = 2;

				if (type == 1) {
					document.getElementById("weightadd").style.borderColor = "red";
				} else {
					document.getElementById("weight").style.borderColor = "red";
				}
			} else if (weightPackage > 44.09 && weightPackage <= 154.324) {
				htmlErr = htmlErr + '<p>'+ warningWeightPackageMaximum +'</p>';
				check   = 1;
			}
		}
    }
    var response = [];
    response.push(check);
    response.push(htmlErr);
    return response;
}

function checkDimension(type) { /** 1 add 2 modify */
    if (type == 1) {
        var unitDimension   = jQuery("#addlengthUnit").val();
        var dimensionLength = parseFloat(jQuery("#lengthadd").val());
        var dimensionWidth  = parseFloat(jQuery("#widthadd").val());
        var dimensionHeight = parseFloat(jQuery("#heightadd").val());
    } else {
        var unitDimension   = jQuery("#lenghtUnit").val();
        var dimensionLength = parseFloat(jQuery("#lenght").val());
        var dimensionWidth  = parseFloat(jQuery("#width").val());
        var dimensionHeight = parseFloat(jQuery("#height").val());
    }
    
    if (unitDimension == 'CM') {
        var maxDimension     = 400;
        var warningDimension = 330;
    } else {
        var maxDimension     = 157.48; 
        var warningDimension = 129.92;
    }

    var check = 0;
    var htmlErr = '';
    if (dimensionLength != "" && dimensionWidth != "" && dimensionHeight != "") {
        var calculation = dimensionLength + (2 * dimensionWidth) + (2 * dimensionHeight);
        //US
		if (country == 'US') {
            if ((calculation > 165) || (dimensionLength > 108 || dimensionWidth > 108 || dimensionHeight > 108)) {
				if (calculation > 165) {
					htmlErr = htmlErr + '<p>'+ errorDimension3 +'</p>';
					check = 2;
					if (type == 1) {
						document.getElementById("heightadd").style.borderColor = "red";
						document.getElementById("lengthadd").style.borderColor = "red";
						document.getElementById("widthadd").style.borderColor  = "red";
					} else {
						document.getElementById("height").style.borderColor = "red";
						document.getElementById("lenght").style.borderColor = "red";
						document.getElementById("width").style.borderColor  = "red";
					}
				}
				if (dimensionLength > 108 || dimensionWidth > 108 || dimensionHeight > 108) {
					htmlErr = htmlErr + '<p>'+ errorDimension4 +'</p>';
					check = 2;
					if (type == 1) {
						document.getElementById("heightadd").style.borderColor = "red";
						document.getElementById("lengthadd").style.borderColor = "red";
						document.getElementById("widthadd").style.borderColor  = "red";
					} else {
						document.getElementById("height").style.borderColor = "red";
						document.getElementById("lenght").style.borderColor = "red";
						document.getElementById("width").style.borderColor  = "red";
					}
				 }
            } else {
				if (calculation > 130 && calculation <= 165) {
					htmlErr = htmlErr + '<p>'+ warningWeightPackageMaximum3 +'</p>';
					check = 1;
					if (type == 1) {
						document.getElementById("heightadd").style.borderColor = "red";
						document.getElementById("lengthadd").style.borderColor = "red";
						document.getElementById("widthadd").style.borderColor  = "red";
					} else {
						document.getElementById("height").style.borderColor = "red";
						document.getElementById("lenght").style.borderColor = "red";
						document.getElementById("width").style.borderColor  = "red";
					}
				}
				if ((dimensionLength <= 108 && dimensionLength > 38) || (dimensionWidth <= 108 && dimensionWidth > 38) || (dimensionHeight <= 108 && dimensionHeight > 38)) {
					htmlErr = htmlErr + '<p>'+ warningWeightPackageMaximum4 +'</p>';
					check = 1;
					if (type == 1) {
						document.getElementById("heightadd").style.borderColor = "red";
						document.getElementById("lengthadd").style.borderColor = "red";
						document.getElementById("widthadd").style.borderColor  = "red";
					} else {
						document.getElementById("height").style.borderColor = "red";
						document.getElementById("lenght").style.borderColor = "red";
						document.getElementById("width").style.borderColor  = "red";
					}
				} 
			}
		} else {
			if (calculation > maxDimension) {
				htmlErr = htmlErr + '<p>'+ errorDimension +'</p>';
				check = 2;
				if (type == 1) {
					document.getElementById("heightadd").style.borderColor = "red";
					document.getElementById("lengthadd").style.borderColor = "red";
					document.getElementById("widthadd").style.borderColor  = "red";
				} else {
					document.getElementById("height").style.borderColor = "red";
					document.getElementById("lenght").style.borderColor = "red";
					document.getElementById("width").style.borderColor  = "red";
				}
			}
			if (calculation > warningDimension && calculation <= maxDimension) {
				htmlErr = htmlErr + '<p>'+ warningDimensionLang +'</p>';
				check = 1;
			}
		}
    }
    var response = [];
    response.push(check);
    response.push(htmlErr);
    return response;
}

function enabledBtnSave() {
    $("#submitEdit").removeClass('disabled');
}
