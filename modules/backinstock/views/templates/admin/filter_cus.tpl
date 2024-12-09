<div class="widget-body">
    {if $product_data eq 0}
        {l s='No data to display' mod='backinstock'}        
    {else}
        <table class="price-alert-tab">
        <thead>
        <tr>
        <th>{l s='Sr No.' mod='backinstock'}</th>
        <th>{l s='Email' mod='backinstock'}</th>
        <th style='width: 23%;'>{l s='Product' mod='backinstock'}</th>
        <th>{l s='Model' mod='backinstock'}</th>
        <th>{l s='Current Price' mod='backinstock'}</th>
        <th>{l s='Purchased' mod='backinstock'}</th>
        </tr>
        </thead>
        <tbody>
            {$serial = 1}
        {for $i=0 to count($product_data)-1}            
            <tr {if $serial is even}class="price-alert-tab-odd"{/if}>
                <td>{$serial|escape:'htmlall':'UTF-8'}</td>
                <td>{$product_data[$i]['email']|escape:'htmlall':'UTF-8'}</td>
                <td>{$product_data[$i]['name']|escape:'htmlall':'UTF-8'}<br><label style="font-size: 11px; font-weight: normal;">{$product_data[$i]['attributes']|escape:'htmlall':'UTF-8'}</label></td>
                <td>{$product_data[$i]['model']|escape:'htmlall':'UTF-8'}</td>
                <td>{$product_data[$i]['current_price']|escape:'htmlall':'UTF-8'}</td>
                <td>{$product_data[$i]['status']|escape:'htmlall':'UTF-8'}</td>
            </tr>
            {$serial = $serial+1}
        {/for}
        </tbody>
        </table>   
    {/if}
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
* Price Alert Filter Customers Page
*}


