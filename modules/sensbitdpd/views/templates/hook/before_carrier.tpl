{**
* 2016-2017 Sensbit
*
* MODUŁ ZOSTAŁ UDOSTĘPNIONY NA PODSTAWIE LICENCJI NA JEDNO STANOWISKO/DOMENĘ
* NIE MASZ PRAWA DO JEGO KOPIOWANIA, EDYTOWANIA I SPRZEDAWANIA
* W PRZYPADKU PYTAŃ LUB BŁĘDÓW SKONTAKTUJ SIĘ Z AUTOREM
*
* ENGLISH:
* MODULE IS LICENCED FOR ONE-SITE / DOMAIM
* YOU ARE NOT ALLOWED TO COPY, EDIT OR SALE
* IN CASE OF ANY QUESTIONS CONTACT AUTHOR
*
* ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** *
*
* PL: ODWIEDŹ NASZ SKLEP PO WIĘCEJ PROFESJONALNYCH MODUŁÓW PRESTASHOP
* EN: VISIT OUR ONLINE SHOP FOR MORE PROFESSIONAL PRESTASHOP MODULES
* HTTPS://sensbit.pl
*
* ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** *
*
* @author    Tomasz Dacka (kontakt@sensbit.pl)
* @copyright 2016-2017 sensbit.pl
* @license   One-site license (jednostanowiskowa, bez możliwości kopiowania i udostępniania innym)
*}


<div id='sensbitdpd-wrapper' class='row'>
	<div id='sensbitdpd' class='sensbitdpd'>
		<img style='height:auto;' class='sensbitdpd-img' src="{$sensbitdpd_options.module_dir}views/img/services/odbior-w-punkcie.png"/>
		<div class="sensbitdpd-search"{if $sensbitdpd_options.hide_map} style="width:100%;padding:0;margin:0;"{/if}>
			<select class='sensbitdpd-point-select'>
				<option>
					{if !empty($sensbitdpd_options.point)}
						{$sensbitdpd_options.point_label}
					{else}
						{l s='Choose your point' mod='sensbitdpd'}
					{/if}
				</option>
			</select>
			{if !$sensbitdpd_options.hide_map}
				<a class='sensbitdpd-map-btn' onclick='sensbitdpd.openMap(".sensbitdpd-point-select", "{$sensbitdpd_options.customer_place}");
					return false;' href='#'>{l s='Choose from map' mod='sensbitdpd'}</a>
			{/if}
		</div>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		if (typeof window.jQuery === 'undefined') {
			var sensbitdpdjquery = document.createElement('script');
			sensbitdpdjquery.type = 'text/javascript';
			sensbitdpdjquery.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js';
			document.documentElement.childNodes[0].appendChild(sensbitdpdjquery);
		}
	}, false);
</script>

<script>
	{strip}
		{literal}
			var sensbitdpdloader = setInterval(function () {
				if (typeof sensbitdpd !== 'undefined' && typeof sensbitdpd.init === 'function') {
					sensbitdpd.init({/literal}{$sensbitdpd_options|json_encode nofilter}{literal});
					clearInterval(sensbitdpdloader);
				}
			}, 300);
		{/literal}
	{/strip}
</script>
