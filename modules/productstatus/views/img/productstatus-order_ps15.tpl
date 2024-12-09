
<br/>
<fieldset>
    <legend><img alt="Товары" src="../img/admin/cart.gif"> {l s='Product status' mod='productstatus'}</legend>

    <table class="table" cellspacing="0" cellpadding="0" id="ProductStatusTable" width="100%">
        <thead>
        <tr>
            <th>{l s='Code' mod='productstatus'}</th>
            <th>{l s='Product' mod='productstatus'}</th>
            <th>{l s='Status' mod='productstatus'}</th>
            <th>{l s='Supplier Delivery' mod='productstatus'}</th>
        </tr>
        </thead>
        <tbody>
            {foreach $products as $item}
            <tr class="product-row">
                <td>{$item.product_id |escape:'htmlall':'UTF-8'}</td>
                <td>{$item.product_name |escape:'htmlall':'UTF-8'}</td>
                <td class="order_status_change">
                    <div class="product_status_wrp">
                        <select class="status_selector" data-id_detail="{$item.id_order_detail |escape:'htmlall':'UTF-8'}" data-id_employee="{$cookie->__get('id_employee') |escape:'htmlall':'UTF-8'}" data-id_lang="{$cookie->__get('id_lang') |escape:'htmlall':'UTF-8'}" style="background-color:{$item.color_state |escape:'htmlall':'UTF-8'}">
                            {foreach $statuses as $state}
                            <option value="{$state.id_order_state |escape:'htmlall':'UTF-8'}" {($item.id_product_state == $state.id_order_state) ? 'selected' : '' |escape:'htmlall':'UTF-8'} data-bgcolor="{$state.color |escape:'htmlall':'UTF-8'}">{$state.name |escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                        <div class="show_history">
                            <div class="status_history">
                                <h3>{l s='Status history' mod='productstatus'}</h3>
                                <ul>
                                    <li>Status name 1 (Employee Name)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <input type="date" name="supplier_delivery_{$key|escape:'htmlall':'UTF-8'}" value="{$item.dates.supplier_delivery|escape:'htmlall':'UTF-8'}" class="" onchange="sendAjax('supplier_delivery', '{$item.id_order_detail|escape:'htmlall':'UTF-8'}', this.value)"/>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>

</fieldset>
