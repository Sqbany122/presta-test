<div class='sensbitinpost'>
	{if $data.first_message}
		<div class="alert alert-warning">
			<strong>Uwaga! Zamówienie ma przypisaną wiadomość.</strong>{if $data.first_message_lines>3} <a href="#order_{$data.id_order}-first-message" class="sensbitinpost-slide-toggle">Pokaż</a>{/if}
			<div id="order_{$data.id_order}-first-message"{if $data.first_message_lines>3} style="display:none;"{/if}>
				{$data.first_message}
			</div>
		</div>
	{/if}
	<table class='table messages-container sensbitinpost-order-form' id='sensbitinpost-order-form-{$data.id_order}'>
		<thead>
			<tr>	
				<th></th>
				<th>{l s='Szablon' mod='sensbitinpost'}</th>
				<th>{l s='Usługa' mod='sensbitinpost'}</th>
				<th>{l s='Rozmiar' mod='sensbitinpost'}</th>
				<th>{l s='Nadanie' mod='sensbitinpost'}</th>
				<th>{l s='Dane kontaktowe odbiorcy' mod='sensbitinpost'}</th>
				<th>{l s='Dostawa do' mod='sensbitinpost'}</th>
				<th>{l s='Pobranie' mod='sensbitinpost'}</th>
				<th>{l s='Ubezpieczenie' mod='sensbitinpost'}</th>
				<th>{l s='Opis przesyłki' mod='sensbitinpost'}</th>
				<th>{l s='Szczegóły' mod='sensbitinpost'}</th>
				<th></th>
			</tr>
		</thead>

		<tbody class="package-container">
			{include file="./new_package_form.tpl"}
		</tbody>
	</table>
</div>