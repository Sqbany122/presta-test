<style>
	textarea.result {
		width:300px;
		min-height:300px;
		resize:vertical;
		margin-top:15px;
		display:none;
	}
</style>
<div class="sensbitinpost sensbitinpost-parent">
	<div class="panel">
		<div class="panel-heading">
			API
		</div>
		<div class="panel-body">
			<div class="alert alert-info">
				<p>Wraz z wersją 4.5.0 modułu wprowdziliśmy nową funkcjonalność, a mianowicie dostęp do danych modułu z zewnątrz tj. spoza panelu administracyjnego sklepu PrestaShop.</p>
				<p>Lista funkcji jest stale rozwijana i jeśli uważasz, że wprowadzenie jakieś opcji rozwiąże Twoje problemy poinformuj nas o tym!</p>
				<p>Rozważymy wprowadzenie każdej opcji! Prześlij specyfikację na adres <a href="mailto:kontakt@sensbit.pl">kontakt@sensbit.pl</a>, a wkrótce otrzymasz zwrotnie informację co będzie potrzebne do jego realizacji.</p>
			</div>
			<br/><br/>

			<h3>Składnia linku wywołującego akcję</h3>
			<pre>{$api_link}&action=nazwafunkcji&nazwaparametru=wartoścparametru&nazwakolejnegoparametry=wartosckolejnegoparametru</pre>
			<br/><br/>
			<h3>Lista akcji</h3>
			<table class="table">
				<thead>
					<tr><th>Lp.</th><th>Nazwa</th><th>Opis</th><th>Metoda</th><th>Parametry</th><th>Wartości zwracane</th><th>Testy</th></tr>
				</thead>
				<tbody>
					{foreach $api_list as $api name=api_list}
						<tr>
							<td>{$smarty.foreach.api_list.index+1}</td>
							<td><strong>{$api.name}</strong></td>
							<td>{$api.desc}</td>
							<td>{$api.method}</td>
							<td>
								<dl>
									{foreach $api.params as $param}
										<dt>{$param.id}</dt>
										<dd>{$param.desc}</dd>
									{/foreach}
								</dl>
							</td>
							<td>{$api.return}</td>
							<td>
								<form class="sensbit-apitest-form" style="width:300px;">
									<input type="hidden" class="param" name="action" value="{$api.name}"/>
									{foreach $api.params as $param}
										<label>
											{$param.id}
											<input type="text" class="param" placeholder="{$param.desc}" name="{$param.id}"/>
										</label>
									{/foreach}
									<label>
										Link do wywołania żądania:
										<input type="text" class="param" name="url" disabled="disabled" />
									</label>
									<div>
										<input type="submit" class="process btn btn-primary" style="text-transform: none" value="Wywołaj w oknie"/>
										<a target="_blank" href="#" class="process-new btn btn-success">Wywołaj w nowej karcie</a>
										<textarea class="result"></textarea>
									</div>
								</form>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	var sensbit_api_base_url = '{$api_link}';
	$(function () {
	{if SensbitInpostTools::isBootstrap()}
		$(".sensbitinpost-parent").insertBefore("#form-sensbitinpost_status table.table");
	{else}
		$(".sensbitinpost-parent").insertBefore("table[name=list_table]");
	{/if}

		$("form.sensbit-apitest-form .param").on('change', function () {
			var $form = $(this).closest('form');
			var link = sensbit_api_base_url;
			$form.find('.param').each(function (i, e) {
				var i = $(this);
				if (i.attr('name') === 'url' || !i.val().length)
				{
					return;
				}
				link += "&" + i.attr('name') + "=" + i.val();
			});
			$form.find('input[name=url]').val(link);
			$form.find('a.process-new').attr('href', link);
		});
		$("form.sensbit-apitest-form").each(function () {
			$(this).find('.param').first().trigger('change');
		});
		$("form.sensbit-apitest-form").on('submit', function (e) {
			e.preventDefault();
			var $form = $(this);
			var data = {
				debug: 1
			};

			$form.find('.param').each(function () {
				var el = $(this);
				if (el.attr('name') === 'url' || !el.val().length)
				{
					return;
				}
				data[el.attr('name')] = el.val();
			});

			delete data.url;

			$.ajax({
				url: sensbit_api_base_url,
				type: 'GET',
				data: data,
				beforeSend: function () {
					showNoticeMessage('Wywoływanie akcji ' + data.action);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					showErrorMessage(textStatus);
				},
				success: function (res) {
					$form.find('.result').val(res).fadeIn();
				}
			});
		});
	});
</script>