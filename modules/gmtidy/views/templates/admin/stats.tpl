<div class="panel">
    <div class="panel-heading">
        {l s='Statistics' mod='gmtidy'}
    </div>
    <div class="row">
        <div class="col-md-4">
            <ul>
                <li>{l s='Abandoned carts' mod='gmtidy'}: <b>{$abandonedCarts}</b></li>
                <li>{l s='Connection stats' mod='gmtidy'}: <b>{$connectionsStats}</b></li>
                <li>{l s='Search stats' mod='gmtidy'}: <b>{$searchStats}</b></li>
                <li>{l s='Email logs' mod='gmtidy'}: <b>{$emailLogs}</b></li>
                <li>{l s='Logs' mod='gmtidy'}: <b>{$logs}</b></li>
            </ul>
        </div>
        <div class="col-md-4">
            <ul>
                <li>{l s='Guests with no addresses' mod='gmtidy'}: <b>{$guestsWithoutAddresses}</b></li>
                <li>{l s='Customers with no addresses' mod='gmtidy'}: <b>{$customersWithoutAddresses}</b></li>
                <li>{l s='Guests with no orders' mod='gmtidy'}: <b>{$guestsWithoutOrders}</b></li>
                <li>{l s='Customers with no orders' mod='gmtidy'}: <b>{$customersWithoutOrders}</b></li>
                <li>{l s='Customers threads' mod='gmtidy'}: <b>{$customerThreads}</b></li>
            </ul>
        </div>
        <div class="col-md-4">
            <ul>
                <li>{l s='Expired specific prices' mod='gmtidy'}: <b>{$expiredSpecificPrices}</b></li>
                <li>{l s='Expired vouchers' mod='gmtidy'}: <b>{$expiredVouchers}</b></li>
                <li>{l s='Unused feature values' mod='gmtidy'}: <b>{$unusedFeatureValues}</b></li>
                <li>{l s='Empty features' mod='gmtidy'}: <b>{$emptyFeatures}</b></li>
                <li>{l s='Products with no tax group' mod='gmtidy'}: <b>{$productsWithNoTaxGroup}</b></li>
            </ul>
        </div>
    </div>
</div>