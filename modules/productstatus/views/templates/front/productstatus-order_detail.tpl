{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
 *  @author Andreika
 *  @copyright  Andreika
 *  @version  2.8.5
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<br/>
<h3><img alt="Товары" src="../img/admin/cart.gif"> {l s='Product status' mod='productstatus'}</h3>
<div class="table_block">
    <table class="std" cellspacing="0" cellpadding="0" id="ProductStatusTable" width="100%">
        <thead>
        <tr>
            <th>{l s='Code' mod='productstatus'}</th>
            <th>{l s='Reference' mod='productstatus'}</th>
            <th>{l s='Product' mod='productstatus'}</th>
            <th>{l s='Status' mod='productstatus'}</th>
            <th>{l s='Supplier Delivery' mod='productstatus'}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $products as $item}
            <tr class="product-row">
                <td>{$item.product_id|escape:'htmlall':'UTF-8'}</td>
                <td>{if empty($item.reference)} -- {else}{$item.reference|escape:'htmlall':'UTF-8'}{/if}</td>
                <td>{$item.product_name|escape:'htmlall':'UTF-8'}</td>
                <td class="order_status_change">
                    <div class="product_status_wrp">
                        <span>{$item.name_state|escape:'htmlall':'UTF-8'}</span>
                    </div>
                </td>
                <td>
                    {if $item.dates.supplier_delivery == '0000-00-00'}
                        {l s='no data' mod='productstatus'}
                    {else}
                        {$item.dates.supplier_delivery|escape:'htmlall':'UTF-8'}
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>

</div>