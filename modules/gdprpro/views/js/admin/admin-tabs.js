/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <zoli@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */
 
// store the hash (DON'T put this code inside the $() function, it has to be executed 
// right away before the browser can start scrolling!
var wlHash = window.location.hash.replace('#','');

// delete hash so the page won't scroll to it
window.location.hash = "";

$(document).ready(function() {	
	$(document).on('click', "#pch-tabs-container .list-group-item.t-pane", function(e) {				
		e.preventDefault();
		
		$("#pch-tabs-container .list-group-item").removeClass('active');
		$("#pch-tabs-container .tab-content-container .t-content").removeClass('active');
		
		$(this).addClass('active');
		$(this).closest(".panel-collapse").prev("a.has-sub").addClass('active');
		$(this).closest(".panel-collapse:not(.in)").prev("a.has-sub").click();
		var target = $(this).data('target');
        $("#content-tab-" + target).addClass('active');
	});
	
	if (wlHash != null && wlHash != '') {		
		$("#pch-tabs-container .list-group-item.t-pane[data-target='"+wlHash+"']").trigger('click');		
	}
});

document.addEventListener("DOMContentLoaded", function () {
    $(".champs-button-documentation").click(function() {
        $('.documentation-dropdown').toggle();
    });
	$(document).click(function(event) {
		if (!$(event.target).closest(".documentation-dropdown-languages,.champs-button-documentation").length) {
			$('.documentation-dropdown').hide();
		}
	});
});