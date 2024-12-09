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
{*<pre>{$products|print_r}</pre>*}
<div class="panel">

    <div class="panel-heading"><i class="icon-shopping-cart"></i> {l s='Product status' mod='productstatus'}</div>

    <table class="table" cellspacing="0" cellpadding="0" id="ProductStatusTable" width="100%">
        <thead>
        <tr>
            <th>{l s='Code EAN' mod='productstatus'}</th>
            <th>{l s='Product' mod='productstatus'}</th>
            <th>{l s='Status' mod='productstatus'}</th>
            <th></th>
            <th>{l s='Supplier Delivery' mod='productstatus'}</th>
            <!--<th>{l s='Scheduled shipping' mod='productstatus'}</th>!-->
            <th>{l s='Tracking URL' mod='productstatus'}</th>
            <th>&nbsp;{l s='Comment' mod='productstatus'}</th>
        </tr>
        </thead>
        <tbody>
            {foreach $products as $key => $item}
            <tr class="product-row">
                <td>{$item.product_ean13|escape:'htmlall':'UTF-8'}</td>
                <td>{$item.product_name|escape:'htmlall':'UTF-8'}</td>
                <td class="order_status_change">
                    <div class="product_status_wrp">
                        <select class="status_selector" data-id_detail="{$item.id_order_detail|escape:'htmlall':'UTF-8'}" data-id_employee="{$cookie->__get('id_employee')|escape:'htmlall':'UTF-8'}" data-id_lang="{$cookie->__get('id_lang')|escape:'htmlall':'UTF-8'}" style="background-color:{$item.color_state|escape:'htmlall':'UTF-8'}">
                            {foreach $statuses as $state}
                            <option value="{$state.id_order_state|escape:'htmlall':'UTF-8'}" {($item.id_product_state == $state.id_order_state) ? 'selected' : ''|escape:'htmlall':'UTF-8'} data-bgcolor="{$state.color|escape:'htmlall':'UTF-8'}">{$state.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                        <div class="show_history">
                            <div class="status_history">
                                <h4>{l s='Status history' mod='productstatus'}</h4>
                                <ul>
                                    <li>Status name 1 (Employee Name)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </td>
                <td>

                </td>
                <td>
                    <input type="text" name="supplier_delivery_{$key|escape:'htmlall':'UTF-8'}" value="{$item.dates.supplier_delivery|escape:'htmlall':'UTF-8'}" class="supplier_delivery" onchange="sendAjax('supplier_delivery', '{$item.id_order_detail|escape:'htmlall':'UTF-8'}', this.value)" {if !empty($item.dates.supplier_delivery)}style="background-color: lightgreen; color:darkgreen; font-weight: bold"{/if}/>
                </td>

                <!--<td>
                    <input type="text" name="scheduled_shipping_{$key|escape:'htmlall':'UTF-8'}" value="{$item.dates.scheduled_shipping|escape:'htmlall':'UTF-8'}" class="supplier_delivery" onchange="sendAjax('scheduled_shipping', '{$item.id_order_detail|escape:'htmlall':'UTF-8'}', this.value)" {if !empty($item.dates.scheduled_shipping)}style="background-color: lightgreen; color:darkgreen; font-weight: bold"{/if}/>
                </td>!-->
                <td>
                    <input type="text" name="tracking_url_{$key|escape:'htmlall':'UTF-8'}" value="{$item.dates.tracking_url|escape:'htmlall':'UTF-8'}"  onchange="sendAjax('tracking_url', '{$item.id_order_detail|escape:'htmlall':'UTF-8'}', this.value)"/>
                </td>
                <td>
                    <input type="text" name="comment_{$key|escape:'htmlall':'UTF-8'}" value="{$item.dates.comment|escape:'htmlall':'UTF-8'}"  onchange="sendAjax('comment', '{$item.id_order_detail|escape:'htmlall':'UTF-8'}', this.value)"/>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>

</div>

<script>
    $(".supplier_delivery").datepicker({
        prevText: '',
        nextText: '',
        dateFormat: 'yy-mm-dd'
    });
</script>
