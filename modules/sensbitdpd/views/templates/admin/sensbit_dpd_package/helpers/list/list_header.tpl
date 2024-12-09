
{extends file="helpers/list/list_header.tpl"}
{block name=override_header}
	<div class="sensbitdpd sensbitdpd-parent">
		<div class="sensbitdpd-orders-filters">
			<div class="row">
				<div class="col-sm-8">
					<h4>Super filtry</h4>
					<em class="text-info"><i class="icon-question-circle"></i> Zapisujemy Twoje wybrane filtry w konfiguracji. Nie stracisz ich po odświeżeniu strony.<br/>Oprócz super filtrów możesz używać filtrów w kolumnach danych np. filtrując po nr śledzenia czy numerze protokołu.</em>
					<div class='filters-container'>
						<div class="filter-group">
							<div class="btn-group"  data-toggle="buttons">
								<div class="btn btn-primary">
									Data utworzenia przesyłki
								</div>
								<div style='width:170px;display:inline-block'>
									<div class="input-group">
										<span class="input-group-addon">Od</span>
										<input type='text' class='sensbitdpd-datetime' value="{if isset($filters.date_add)}{$filters.date_add.from}{/if}"  name='filters[date_add][from]' placeholder='Od' autocomplete="off"/>
									</div>
								</div>
								<div  style='width:170px;display:inline-block'>
									<div class="input-group">
										<span class="input-group-addon">Do</span>
										<input type='text' class='sensbitdpd-datetime' value="{if isset($filters.date_add)}{$filters.date_add.to}{/if}"  name='filters[date_add][to]' placeholder='Do' autocomplete="off"/>
									</div>
								</div>
							</div>
						</div>
						<div class="filter-group">
							<div class="btn-group" data-toggle="buttons">
								<div class="btn btn-primary">
									Wyświetlane dane
								</div>
								{foreach $fields_to_filter as $name => $vars}
									{if !in_array($name, array('actions'))}
										{$active=isset($filters.fields) && in_array($name,$filters.fields)}
										<label class="btn btn-default{if $active} active{/if}">
											<input type="checkbox" name="filters[fields][]" value="{$name}"{if $active} checked='checked'{/if}/>
											{$vars.title}
										</label>
									{/if}
								{/foreach}
							</div>
						</div>
						<div class="filter-group">
							<input class="btn btn-success" type="submit" name="set_filters" value="Filtruj"/>
						</div>
					</div>
					<button class="sensbitdpd-orders-filters-open btn btn-primary btn-xs" style="margin-top:10px;">Rozwiń ⇊</button>
				</div>
				<div class="col-sm-4">
					<h4>Masowe działania</h4>

					<div class="filter-group">
						<button class="btn btn-primary sensbitdpd-tip sensbitdpd-bulk-labels" title="Na podstawie wybranych przesyłek generuje zbiorczy plik z etykietami.">
							Pobierz wybrane etykiety zbiorczo
						</button>
					</div>
					<div class="filter-group">
						<button class="btn btn-primary sensbitdpd-tip sensbitdpd-bulk-protocol" title="Na podstawie wybranych przesyłek generuje nowy protokół odbioru dla kuriera.">
							Generuj nowy protokół dla wybranych przesyłek
						</button>
					</div>
					<div class="filter-group">
						<button class="btn btn-primary sensbitdpd-tip sensbitdpd-bulk-change-order-state-by-packages_open-panel" title="Na podstawie wybranych przesyłek zmienia status powiązanych zamówień na wybrany.">
							Zmień status powiązanych zamówień
						</button>
						<div class="row sensbitdpd-bulk-change-order-state-by-packages_panel" style="margin-top:10px;display:none;">
							<div class="col-sm-8">
								<select class="sensbitdpd-bulk-change-order-state-by-packages_id-order-state">
									{foreach $statuses as $status}
										{$active=isset($filters.status) && in_array($status.id_order_state, $filters.status)}
										<option value="{$status.id_order_state}">{$status.name}</option>
									{/foreach}
								</select>
							</div>
							<div class="col-sm-4">
								<button class="btn btn-warning sensbitdpd-tip sensbitdpd-bulk-change-order-state-by-packages_do" title="Na podstawie wybranych przesyłek zmienia status powiązanych zamówień na wybrany.">
									Zmień
								</button>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(function () {
		{if SensbitDpdTools::isBootstrap()}
			$(".sensbitdpd-parent").insertBefore("#form-sensbitdpd_shipment table.table");
		{else}
			$(".sensbitdpd-parent").insertBefore("table[name=list_table]");
		{/if}
		});
	</script>
{/block}
