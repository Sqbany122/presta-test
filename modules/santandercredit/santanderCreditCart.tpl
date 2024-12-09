{if $totalOrder>=100 && $currency->iso_code == 'PLN'}
	
	<script type="text/javascript" src="{$module_dir}js/santanderCredit.js"></script> 	
	
	<div style="text-align: right; margin: 10px 0;">
		<img src="{$module_dir}images/obliczRate.png" alt="Oblicz ratę!" onclick="obliczRate({$totalOrder},1,{$shopId});" style="cursor: pointer; "/>
	</div>

{/if}