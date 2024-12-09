{if $santanderCreditProductPrice >= 100 && $currency->iso_code == 'PLN'}

<script type="text/javascript" src="{$module_dir}js/santanderCredit.js"></script> 
<br clear="all">
<div style="float: right; margin: 10px 0 0 0;">
	<a onClick="obliczRate($('#our_price_display').text(),1,{$shopId});" title="Kupuj na eRaty Santander Consumer Banku!" align="right" style="cursor: pointer;">
		<img src="{$module_dir}images/obliczRate.png" alt="Oblicz ratę!"/>
	</a> 
</div>

{/if}