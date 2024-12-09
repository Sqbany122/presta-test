<div class='alert alert-info'>
	<p>Poniższe ustawienia dotyczą wyświetlania się konkretnego powiązanego z modułem przewoźnika, jeśli dotyczy on dostawy z usługą Paczka w Weekend, w koszyku klienta.</p>
	<p>Możesz ograniczyć wyświetlanie się przewoźnika do konkretnych dni tygodnia oraz godzin.</p>
</div>
{if !empty($days)}
	<style>
		.sensbitinpost-days td,
		.sensbitinpost-days th{
			text-align:center;
		}
	</style>
	<button class="btn btn-success btn-xs sensbitinpost-set-default-pww-options sensbitinpost-tip" title="Ustawia automatyczną, proponowaną konfigurację tj. dostępność przewoźnika Paczka w Weekend od Czwartku 20:00 do Piątku 13:00">Ustaw domyślną konfigurację</button>
	<button class="btn btn-default btn-xs sensbitinpost-set-all-pww-options">Zaznacz wszystkie</button>
	<button class="btn btn-default btn-xs sensbitinpost-set-none-pww-options">Odznacz wszystkie</button>

	<table class="table sensbitinpost-days">
		<thead>
			<tr>
				<th>Godzina \ Dzień tygodnia</th>
					{foreach $days as $day}
					<th>
						{$day.name}
					</th>
				{/foreach}
			</tr>
		</thead>
		<tbody>
			{for $i = 0 to 23}
				<tr>
					<td>
						<label>{$i|string_format:"%02d"}:00-{$i|string_format:"%02d"}:59</label>
					</td>
					{foreach $days as $day}
						<td>
							<div class="sensbitinpost-tip" title="{$day.name} {$i|string_format:"%02d"}:00-{$i|string_format:"%02d"}:59">
								<input type="checkbox" data-day="{$day.id}" data-hour="{$i}" name="day[{$day.id}][{$i}]"{if isset($days_config) && !empty($days_config) && isset($days_config[$day.id]) && isset($days_config[$day.id][$i])} checked="checked"{/if}/>
							</div>
						</td>
					{/foreach}
				</tr>
			{/for}
		</tbody>
	</table>
	{literal}
		<script>
			$(function () {
				$('.sensbitinpost-days input').on('change', function () {
					var data = {};
					$(".sensbitinpost-days input:checked").each(function () {
						var $this = $(this);
						var day = $this.data('day');
						if (typeof data[day] === 'undefined') {
							data[day] = {};
						}
						data[$this.data('day')][$this.data('hour')] = 1;
					});
					$.ajax({
						type: 'POST',
						dataType: 'json',
						data: {
							ajax: 1,
							sensbitinpost: 1,
							action: 'set-paczka-w-weekend-restr',
							data: data
						}
						,
						beforeSend: function () {
						}
						,
						error: function (jqXHR, textStatus, errorThrown) {
							showErrorMessage(textStatus);
						}
						,
						success: function (res) {
							if (typeof res.errors !== 'undefined') {
								showErrorMessage(res.errors.join(', '));
							} else {
								showSuccessMessage('Konfiguracja Paczki w Weekend została zapisana');
							}
						}
					});
				});

				$('.sensbitinpost-set-default-pww-options').on('click', function (e) {
					e.preventDefault();
					$.ajax({
						type: 'POST',
						dataType: 'json',
						data: {
							ajax: 1,
							sensbitinpost: 1,
							action: 'set-default-pww-options'
						}
						,
						beforeSend: function () {
						}
						,
						error: function (jqXHR, textStatus, errorThrown) {
							showErrorMessage(textStatus);
						}
						,
						success: function (res) {
							if (typeof res.errors !== 'undefined') {
								showErrorMessage(res.errors.join(', '));
							} else {
								$(".sensbitinpost-days input").removeAttr('checked');
								$.each(res.default, function (k, v) {
									$.each(v, function (kk, vv) {
										var name = 'day[' + k + '][' + kk + ']';
										$("input[name='" + name + "']").attr('checked', 'checked');
									});
								});
								showSuccessMessage('Domyślna konfiguracja Paczki w Weekend została zapisana');
							}
						}
					});
				});
				$('.sensbitinpost-set-all-pww-options').on('click', function (e) {
					e.preventDefault();
					$(".sensbitinpost-days input").attr('checked', 'checked');
					$('.sensbitinpost-days input').first().trigger('change');
				});
				$('.sensbitinpost-set-none-pww-options').on('click', function (e) {
					e.preventDefault();
					$(".sensbitinpost-days input").removeAttr('checked');
					$('.sensbitinpost-days input').first().trigger('change');
				});
			});
		</script>
	{/literal}
{else}
	<div class="alert alert-danger">
		Lista dni jest pusta
	</div>
{/if}