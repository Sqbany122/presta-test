{if $product->specificPrice}
<span class="gm_omniprice" style="color:{$gm_omniprice_color}; background-color: {$gm_omniprice_background};">
    {l s='Lowest price within %d days before promotion:' sprintf=[$gm_omniprice_days] mod='gm_omniprice'}
    <span class="gm_omniprice_lowest" style="color:{$gm_omniprice_price_color};">{$gm_omniprice_lowest}</span>
</span>
{/if}