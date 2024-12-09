<div class='sensbitdpd'>
	{if $data.first_message}
		<div class="alert alert-warning">
			<strong>Uwaga! Zamówienie ma przypisaną wiadomość.</strong>{if $data.first_message_lines>3} <a href="#order_{$data.id_order}-first-message" class="sensbitdpd-slide-toggle">Pokaż</a>{/if}
			<div id="order_{$data.id_order}-first-message"{if $data.first_message_lines>3} style="display:none;"{/if}>
				{$data.first_message}
			</div>
		</div>
	{/if}
	<table class='table messages-container sensbitdpd-order-form' id='sensbitdpd-order-form-{$data.id_order}'>
		<thead>
			<tr>	
				<th></th>
				<th>{l s='Szablon' mod='sensbitdpd'}</th>
				<th>{l s='Usługa' mod='sensbitdpd'}</th>
				<th>{l s='Dane kontaktowe odbiorcy' mod='sensbitdpd'}</th>
				<th>{l s='Dostawa do' mod='sensbitdpd'}</th>
				<th>{l s='Pobranie' mod='sensbitdpd'}</th>
				<th>{l s='Opis przesyłki' mod='sensbitdpd'}</th>
				<th>Paczki</th>
				<th>{l s='Szczegóły' mod='sensbitdpd'}</th>
				<th></th>
			</tr>
		</thead>

		<tbody class="package-container">
			{include file="./new_package_form.tpl"}
		</tbody>
	</table>
</div>