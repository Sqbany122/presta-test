<div class="panel">
<p>{l s='Product indexed today:' mod='gm_omniprice'} {if $indexedToday}{l s='Yes' mod='gm_omniprice'}{else}{l s='No' mod='gm_omniprice'}{/if}</p>
<p>{l s='For simplicity, this table shows price change history for default currency, country, customer group and product attribute.' mod='gm_omniprice'}</p>
<table class="table table-hover" id="orderProducts">
    <thead>
        <tr>
            <th class="fixed-width-md"><span class="title_box">{l s='Date' mod='gm_omniprice'}</span></th>
            <th class="fixed-width-md"><span class="title_box">{l s='Price' mod='gm_omniprice'}</span></th>
            <th class="fixed-width-md"><span class="title_box">{l s='Promotion' mod='gm_omniprice'}</span></th>
        </tr>
    </thead>
    {foreach from=$historyData item=historyItem}
        <tr>
            <td>{$historyItem.date}</td>
            <td>{$historyItem.price_tin}</td>
            <td>{if $historyItem.is_specific_price == '1'}{l s='Yes' mod='gm_omniprice'}{/if}{if $historyItem.is_specific_price == '0'}{l s='No' mod='gm_omniprice'}{/if}</td>
        </tr>
    {/foreach}
</table>
</div>