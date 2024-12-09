{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-2020 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}
{if $order_id == false && Tools::getValue('debug') == 1}
    <div class="alert alert-warning">
        {l s='Payment module does not include id_order or id_cart param. It is not possible to create purchase event tracking (fb pixel).' mod='fbpixel'}
    </div>
{else}
    {if Configuration::get('FBPIXEL_PURCHASE')==1}
        {if Configuration::get('FBPIXEL_DPA')==1}
        {literal}
            <script>
                $(document).ready(function () {
                    fbq('track', 'Purchase', {value: '{/literal}{if $FBPIXEL_CONCURR}{Tools::convertPriceFull($order_total_paid, $currency_from, $currency_to)}{else}{$order_total_paid}{/if}{literal}', content_type: 'product', content_ids: [{/literal}{$content_ids}{literal}], currency: '{/literal}{if $FBPIXEL_CONCURR}{$currency_to->iso_code}{else}{$order_currency_iso_code}{/if}{literal}'});
                });
            </script>
        {/literal}
        {else}
        {literal}
            <script>
                $(document).ready(function () {
                    fbq('track', 'Purchase', {value: '{/literal}{if $FBPIXEL_CONCURR}{Tools::convertPriceFull($order_total_paid, $currency_from, $currency_to)}{else}{$order_total_paid}{/if}{literal}', currency: '{/literal}{if $FBPIXEL_CONCURR}{$currency_to->iso_code}{else}{$order_currency_iso_code}{/if}{literal}'});
                });
            </script>
        {/literal}
        {/if}
    {/if}
{/if}