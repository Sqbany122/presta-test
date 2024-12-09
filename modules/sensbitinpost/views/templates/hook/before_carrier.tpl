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
* HTTPS://SKLEP.SENSBIT.PL
*
* ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** *
*
* @author    Tomasz Dacka (kontakt@sensbit.pl)
* @copyright 2016-2017 sensbit.pl
* @license   One-site license (jednostanowiskowa, bez możliwości kopiowania i udostępniania innym)
*}


<div id='sensbitinpost-wrapper' class='row'>
	<div id='sensbitinpost' class='sensbitinpost'>
		<img style='height:auto;max-width:307px !important' class='sensbitinpost-img' src="{$sensbitinpost_options.module_dir}views/img/services/odbior-w-paczkomacie.png"/>
		{if $sensbitinpost_options.require_phone_number}
			<div class="sensbitinpost-phone-form">
				<strong>{l s='Receiver phone number' mod='sensbitinpost'}</strong>
				<input type='text' class="sensbitinpost-phone-input" value="{$sensbitinpost_options.phone_number}" minlength="9" maxlength="9" size="9" placeholder=""/>
			</div>
		{/if}
		<div class="sensbitinpost_points_list" style='display:none'>
			<strong>{l s='Choose your parcel locker' mod='sensbitinpost'}:</strong>
		</div>
		<div class="sensbitinpost-search"{if $sensbitinpost_options.hide_map} style="width:100%;padding:0;margin:0;"{/if}>
			<select class='sensbitinpost-point-select'>
				<option>
					{if !empty($sensbitinpost_options.point)}
						{$sensbitinpost_options.point_label}
					{else}
						{l s='Choose your parcel locker' mod='sensbitinpost'}
					{/if}
				</option>
			</select>
			{if !$sensbitinpost_options.hide_map}
				<a class='sensbitinpost-map-btn' onclick='sensbitinpost.openMap(".sensbitinpost-point-select", "{$sensbitinpost_options.customer_place}");
						return false;' href='#'>{l s='Choose from map' mod='sensbitinpost'}</a>
			{/if}
			<a class='sensbitinpost_show_nearby' href="" style='display:none'>{l s='Show nearby points' mod='sensbitinpost'}</a>
		</div>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function () {
		if (typeof window.jQuery === 'undefined') {
			var sensbitinpostjquery = document.createElement('script');
			sensbitinpostjquery.type = 'text/javascript';
			sensbitinpostjquery.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js';
			document.documentElement.childNodes[0].appendChild(sensbitinpostjquery);
		}
	}, false);
</script>

<script>
	{strip}
		{literal}
			var sensbitinpostloader = setInterval(function () {
				if (typeof sensbitinpost !== 'undefined' && typeof sensbitinpost.init == 'function') {
					sensbitinpost.init({/literal}{$sensbitinpost_options|json_encode nofilter}{literal});
					clearInterval(sensbitinpostloader);
				}
			}, 300);
		{/literal}
	{/strip}
</script>
