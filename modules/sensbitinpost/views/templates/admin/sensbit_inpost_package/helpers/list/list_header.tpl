
{extends file="helpers/list/list_header.tpl"}
{block name=override_header}
	<div class="sensbitinpost sensbitinpost-parent">
		<div class="sensbitinpost-orders-filters">
			<div class="row">
				<div class="col-sm-8">
					<h4>Super filtry</h4>
					<em class="text-info"><i class="icon-question-circle"></i> Zapisujemy Twoje wybrane filtry w konfiguracji. Nie stracisz ich po odświeżeniu strony.<br/>Oprócz super filtrów możesz używać filtrów w kolumnach danych np. filtrując po nr śledzenia czy dacie wysłania.</em>
					<div class='filters-container'>
						<div class="filter-group">
							<div class="btn-group"  data-toggle="buttons">
								<div class="btn btn-primary">
									Data utworzenia przesyłki
								</div>
								<div style='width:170px;display:inline-block'>
									<div class="input-group">
										<span class="input-group-addon">Od</span>
										<input type='text' class='sensbitinpost-datetime' value="{if isset($filters.date_add)}{$filters.date_add.from}{/if}"  name='filters[date_add][from]' placeholder='Od' autocomplete="off"/>
									</div>
								</div>
								<div  style='width:170px;display:inline-block'>
									<div class="input-group">
										<span class="input-group-addon">Do</span>
										<input type='text' class='sensbitinpost-datetime' value="{if isset($filters.date_add)}{$filters.date_add.to}{/if}"  name='filters[date_add][to]' placeholder='Do' autocomplete="off"/>
									</div>
								</div>
							</div>
						</div>
						<div class="filter-group">
							<div class="btn-group"  data-toggle="buttons">
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
							<div class="btn-group"  data-toggle="buttons">
								<div class="btn btn-primary sensbitinpost-tip"  title="Jeśli na liście znajduje się jedna przesyłka, moduł spróbuje automatycznie pobrać do niej etykietę.">
									Automatycznie drukuj etykietę dla pojedyńczej przesyłki
								</div>
								{$active=isset($filters.autoprintsingle) && $filters.autoprintsingle}
								<label class="btn btn-default{if $active} active{/if}">
									<input type="radio" name="filters[autoprintsingle]" value="1"{if $active} checked='checked'{/if}/>
									Tak
								</label>
								{$active=(isset($filters.autoprintsingle) && !$filters.autoprintsingle) || (!isset($filters.autoprintsingle))}
								<label class="btn btn-default{if $active} active{/if}">
									<input type="radio" name="filters[autoprintsingle]" value="0"{if $active} checked='checked'{/if}/>
									Nie
								</label>
							</div>
						</div>
						<div class="filter-group">
							<input class="btn btn-success" type="submit" name="set_filters" value="Filtruj"/>
						</div>
					</div>
					<button class="sensbitinpost-orders-filters-open btn btn-primary btn-xs" style="margin-top:10px;">Rozwiń ⇊</button>
				</div>
				<div class="col-sm-4">
					<h4>Masowe działania</h4>
					<div class="filter-group">
						<button class="btn btn-default btn-xs sensbitinpost-bulk-select-all"><i class="icon-check-sign"></i> Zaznacz wszystkie</button>
						<button class="btn btn-default btn-xs sensbitinpost-bulk-select-none"><i class="icon-check-empty"></i> Odznacz wszystkie</button>
					</div>
					<div class="filter-group">
						<button class="btn btn-success sensbitinpost-bulk-labels">Pobierz wybrane etykiety zbiorczo</button>
					</div>
					<div class="filter-group">
						<button class="btn btn-success sensbitinpost-bulk-protocol sensbitinpost-tip" title="Generuje nowy protokół na podstawie zaznaczonych przesyłek">Generuj nowy protokół odbioru</button>
					</div>
				</div>
			</div>
		</div>	
	</div>
	<script>
		$(function () {
		{if SensbitInpostTools::isBootstrap()}
			$(".sensbitinpost-parent").insertBefore("#form-sensbitinpost_shipment table.table");
		{else}
			$(".sensbitinpost-parent").insertBefore("table[name=list_table]");
		{/if}
		});
	</script>
{/block}
