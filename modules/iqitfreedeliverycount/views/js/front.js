/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/
$(document).ready(function(){

	$iqitFreeDelivery = $('.iqitfreedeliverycount-detach');
	$('#header .cart-prices').append($iqitFreeDelivery.clone());
	$('#layer_cart .layer_cart_row').last().append($iqitFreeDelivery);
	$('.iqitfreedeliverycount-detach').removeClass('hidden-detach');
	$('#header .ajax_block_cart_total').first().on('DOMSubtreeModified propertychange', function() {

		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseDir + 'modules/iqitfreedeliverycount/iqitfreedeliverycount-ajax.php' + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			data: 'recalculate=' + iqitfdc_from,
			success: function(jsonData)
			{	
				if (jsonData == 0)
				$('.iqitfreedeliverycount-detach').addClass('hidden');
				else{
					$('.iqitfreedeliverycount-detach').removeClass('hidden');
					$('.ifdc-remaining-price').text(jsonData); 
				}
				
			}
		});
	});
});