<div class='alert alert-info'>
	<p>Poniżej możesz podejrzeć powiązania przewoźników z modułami.</p>
	<p>Powiązanie przewoźnika z modułem oznacza iż ten moduł przejmuje kontrolę nad wyświetlaniem się tego przewoźnika w koszyku Twojego sklepu.</p>
	<p>Jeśli powiążesz przewoźnika z tym modułem, i będzie on dotyczył usługi Inpost Paczka w Weekend, moduł ograniczy wyświetlanie się tego przewoźnika w sklepie do określonych przez Ciebie dni tygodnia oraz godzin.</p>
	<p>Jeśli przewoźnik powiązany jest z innym modułem zalecamy usunięcie powiązania gdyż może on powodować problemy z wyświetlaniem się przewoźnika w koszyku.</p>
</div>
{if !empty($carriers)}
	<table class="table">
		<thead>
			<tr>
				<th>ID</th>
				<th>Nazwa</th>
				<th>Aktywny</th>
				<th>Paczka w Weekend?</th>
				<th>Powiązany moduł</th>
				<th>Akcje</th>
			</tr>
		</thead>
		<tbody>
			{foreach $carriers as $carrier}
				<tr>
					<td>{$carrier.id_carrier}</td>
					<td>{$carrier.name}</td>
					<td style="text-align: center;">
						{if $carrier.active}
							<img src="../img/admin/enabled.gif" alt="Tak" title="Tak"/>
						{else}
							<img src="../img/admin/disabled.gif" alt="Nie" title="Nie"/>
						{/if}
					</td>
					<td style="text-align: center;">
						{if $carrier.is_paczka_w_weekend}
							<img src="../img/admin/enabled.gif" alt="Tak" title="Tak"/>
						{else}
							<div class="sensbitinpost-tip" title="Jeśli chcesz powiązać przewoźnika z usługa Paczka w Weekend stwórz odpowiedni szablon przesyłki paczkomatowej z zaznaczoną opcją dodatkową Paczka w Weekend w opcjach usługi">
								<img src="../img/admin/disabled.gif" alt="Nie" title="Nie"/>
							</div>
						{/if}
					</td>
					<td>
						{if $carrier.shipping_external}
							{if isset($carrier.module_instance) && $carrier.module_instance !== false}
								{$carrier.module_instance->displayName}

								{if $carrier.module_instance->active}
									<span style='color:#0c0'>(Moduł jest włączony.)</span>
								{else}
									<span style='color:#c00'>(Moduł jest wyłączony.)</span>
								{/if}
							{else}
								{$carrier.external_module_name} <span style='color:#c00'>(Moduł nie został znaleziony w sklepie!)</span>
							{/if}
						{else}

						{/if}
					</td>
					<td>
						<button{if !$carrier.shipping_external} style='display:none'{/if} class="btn btn-danger btn-xs sensbitinpost-unset-module-from-carrier" data-id_carrier='{$carrier.id_carrier}'><i class="icon-remove"></i> Usuń powiązany moduł</button>
						<button{if $carrier.external_module_name === 'sensbitinpost'} style='display:none'{/if} class="btn btn-success btn-xs sensbitinpost-set-module-for-carrier" data-id_carrier='{$carrier.id_carrier}'><i class="icon-plus"></i> Powiąż moduł z tym przewoźnikiem</button>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	{literal}
		<script>
			$(function () {
				$(".sensbitinpost-unset-module-from-carrier").on('click', function (e) {
					e.preventDefault();
					var el = $(this);
					var module_cont = el.closest('td').prev('td');
					if (confirm('Usunięcie modułu z przewoźnika spowoduje bezpowrotną utratę danych o powiązanym module w przewoźniku! Czy na pewno chcesz kontynuuować?')) {
						$.ajax({
							type: 'POST',
							dataType: 'json',
							data: {
								ajax: 1,
								sensbitinpost: 1,
								action: 'unset-module-from-carrier',
								id_carrier: el.data('id_carrier')
							},
							beforeSend: function () {
								showNoticeMessage('Usuwanie modułu z przewoźnika');
							},
							error: function (jqXHR, textStatus, errorThrown) {
								showErrorMessage(textStatus);
							},
							success: function (res) {
								if (typeof res.errors !== 'undefined') {
									showErrorMessage(res.errors.join(', '));
								} else {
									module_cont.html('');
									el.hide();
									el.siblings('.sensbitinpost-set-module-for-carrier').show();
									showSuccessMessage('Usunięcie modułu z przewoźnika wykonane zostało pomyślnie');
								}
							}
						});
					}
				});
				$(".sensbitinpost-set-module-for-carrier").on('click', function (e) {
					e.preventDefault();
					var el = $(this);
					var module_cont = el.closest('td').prev('td');
					if (confirm('Ustawienie modułu w przewoźniku spowoduje przejęcie przez moduł kontroli nad wyświetlaniem się danego przewoźnika w koszyku sklepu. Czy na pewno chcesz kontynuuować?')) {
						$.ajax({
							type: 'POST',
							dataType: 'json',
							data: {
								ajax: 1,
								sensbitinpost: 1,
								action: 'set-module-for-carrier',
								id_carrier: el.data('id_carrier')
							},
							beforeSend: function () {
								showNoticeMessage('Ustawianie modułu w przewoźniku');
							},
							error: function (jqXHR, textStatus, errorThrown) {
								showErrorMessage(textStatus);
							},
							success: function (res) {
								if (typeof res.errors !== 'undefined') {
									showErrorMessage(res.errors.join(', '));
								} else {
									module_cont.html(res.module);
									el.hide();
									el.siblings('.sensbitinpost-unset-module-from-carrier').show();
									showSuccessMessage('Ustawienie modułu w przewoźniku wykonane zostało pomyślnie');
								}
							}
						});
					}
				});
			});
		</script>
	{/literal}
{else}
	<div class="alert alert-danger">
		Lista przewoźników jest pusta
	</div>
{/if}