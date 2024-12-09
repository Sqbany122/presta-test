<script type="text/javascript">
$(document).ready(function() {
$('.popup_users').bind('click',function(){
	var attribute=$(this).attr('data');
	$.ajax({
			type: "POST",
			url: mod_dir + 'show_popup.php',
			data:'attribute='+attribute,
			beforeSend: function() {
				
			},		
			success: function(x) {
				$('#popup_head_product_count').append(x);
				$('#popup_head_product_count').show();
				$('#dark_popup').show();
				
			}
});
});
});

</script>
<div class="widget-body">
    {if $product_data eq 0}
        {l s='No data to display' mod='backinstock'}        
    {else}
        <table class="price-alert-tab">
        <thead>
        <tr>
        <th>{l s='Sr No.' mod='backinstock'}</th>
        <th style='width: 23%;'>{l s='Product' mod='backinstock'}</th>
        <th>{l s='Model' mod='backinstock'}</th>
        <th>{l s='Current Price' mod='backinstock'}</th>
        <th>{l s='No. of Customers' mod='backinstock'}</th>
        </tr>
        </thead>
        <tbody>
            {$serial = 1}
        {for $i=0 to count($product_data)-1}
            {if $product_data[$i]['user_count'] neq 0}
            <tr {if $serial is even}class="price-alert-tab-odd"{/if}>
                <td>{$serial|escape:'htmlall':'UTF-8'}</td>
                <td>{$product_data[$i]['name']|escape:'htmlall':'UTF-8'}<br><label style="font-size: 11px; font-weight: normal;">{$product_data[$i]['attributes']|escape:'htmlall':'UTF-8'}</label></td>
                <td>{$product_data[$i]['model']|escape:'htmlall':'UTF-8'}</td>
                <td>{$product_data[$i]['current_price']|escape:'htmlall':'UTF-8'}</td>
                <td>
			<a  class='popup_users'  data='{$product_data[$i]['product_attribute_id']|escape:'htmlall':'UTF-8'}'>{$product_data[$i]['user_count']|escape:'htmlall':'UTF-8'}</a></td>
            </tr>
            {$serial = $serial+1}
            {/if}
        {/for}
        </tbody>
        </table>   
    {/if}
</div>
<div id="popup_head_product_count" style="display:none;">
	
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
* Product Update Filter Products Page
*}


