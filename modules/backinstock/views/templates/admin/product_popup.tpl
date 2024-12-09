<script type="text/javascript">
    $(document).ready(function() {
	$('#product_popup_close').bind('click',function(){
		$('#popup_head_product_count').hide();
		$('#popup_name_product').remove();
		$('#popup_product_details').remove();
		$('#dark_popup').hide();
	});
});
	</script>
<div id="popup_name_product">
	<button type="button" class="close" id="product_popup_close" >
		<span aria-hidden="true">×</span>
		<span class="sr-only">Close</span>
	</button>
	<span class="product_name_header">{$popupdata[0]['product_name']|escape:'htmlall':'UTF-8'}</span><span class="product_name_header">({$popupdata[0]['skv']|escape:'htmlall':'UTF-8'})</span>	
	</div>
	<div id="popup_product_details">
		<table>
			<tr class='product_details_popup product_header'>
				<td>
					<span>{l s='Customer Email' mod='backinstock'}</span>	
				</td>
				<td>
					<span>{l s='Subscription Date' mod='backinstock'}</span>
				</td>
				<td>
					<span>{l s='Subscription Type' mod='backinstock'}</span>
				</td>
			</tr>
			{foreach $popupdata as $product_data}
			<tr class='product_details_popup'>
				<td>{$product_data['email']|escape:'htmlall':'UTF-8'}</td>
				<td>{$product_data['date_added']|escape:'htmlall':'UTF-8'}</td>
				<td>{$product_data['subscribe_type']|escape:'htmlall':'UTF-8'}</td>
			</tr>
			{/foreach}
		</table>
	</div>
{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    velsof.com <support@velsof.com>
* @copyright 2014 Velocity Software Solutions Pvt Ltd
* @license   see file: LICENSE.txt
*
* Description
*
* Product Update Admin Panel
*}
