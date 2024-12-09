{if $currency->iso_code == 'PLN'}

<script type="text/javascript" src="{$module_dir}js/santanderCredit.js"></script> 

<div class="block">

<h4>{$santanderCreditBlockTitle}</h4>

<div class="block_content" style="padding: 0px; text-align: center;">
	
		<a title="Zobacz jak kupić na raty" onclick="jakKupic();" style="cursor: pointer;">
			<img src="{$module_dir}images/bannerBlok.jpg" alt="Zobacz jak kupić na raty" />
		</a>
	
</div>

</div>

{/if}