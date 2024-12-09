/*
* 2007-2017 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

jQuery(document).ready(function(){

	var order_ids = new Array();
	
	if(jQuery('#form-order table input[type=checkbox]').length > 0)
	{
		jQuery('#form-order table input[type=checkbox]').each(function(){

			var value = jQuery(this).val();
			jQuery(this).parent().parent().addClass('order_'+value);
			order_ids.push(value);
		});
	}
	else
	{

		jQuery('table.order tbody tr').each(function(){

			var el = jQuery(this);
	
			var value = parseFloat(jQuery(el).find('td:eq(1)').text());

			el.addClass('order_'+value);
			order_ids.push(value);
		});


	}


	jQuery('body').on('mouseenter','.is_aon_order', function(){

		var value = jQuery(this).attr('rel');
		var offset = jQuery(this).offset();
		var top = offset.top;
		var left = offset.left;

		jQuery('.act_baloon').remove();

		var distance_to_bottom = jQuery(document).height() - (jQuery(window).height() + jQuery('body').scrollTop());


		var response_z = $.ajax({ type: "POST",   
							url: '../modules/advancedordernotes/ajax_controller.php',  
							cache: false,
							data: { action: 'get_order_act_info',  token: aon_token, order_id: value  }, 
							async: true,
							 success: function(data) 
							 {
							 		if(data.length > 100)
							 		{	
							 			jQuery('body').append('<div class="act_baloon" style="top:'+top+'px; left:'+(left+25)+'px; ">'+data+'</div>');

							 			if(jQuery('.act_baloon').outerHeight() > distance_to_bottom)
							 			{
							 				jQuery('.act_baloon').attr('style','top:'+(top - jQuery('.act_baloon').outerHeight() -40 )+'px; left:'+(left+25)+'px;');
							 			}
							 		}
								return;
							}
	}).responseText;
		var html = '';
		jQuery('body').append(html);

	}).on('mouseout', '.is_aon_order',function(){

		jQuery('.act_baloon').remove();

	});


	var response_z = $.ajax({ type: "POST",   
							url:  '../modules/advancedordernotes/ajax_controller.php',  
							cache: false,
							data: { action: 'check_if_order_has_note',  token: aon_token, order_ids: order_ids  }, 
							async: true,
							 success: function(data) {

							 		if(data.length > 3)
							 		{	
							 			  obj = JSON.parse(data);
							 			   for (var prop in obj) {
										     
										        if(!obj.hasOwnProperty(prop)) continue;
			      							    if(jQuery('.order_'+prop).length > 0)
										        {	
										        	if(obj[prop] > 0)
										        		jQuery('.order_'+prop+' td:eq(7)').append('<span style="margin-left:20px; position:relative;" class="is_aon_order" rel="'+prop+'"><span style="position: absolute; border-radius: 50%; color: #fff; background-color: #FF5450;  height: 14px; width: 14px; top:-5px; font-size: 8px; right: -5px; text-align: center; line-height: 14px;display: block;">'+obj[prop]+'</span><img style="height:20px; width:20px;" src="'+aon_image+'"/></span>');
										        }
										    
										    }

							 		}
								return;
							}
	}).responseText;

});