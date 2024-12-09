
{extends file="helpers/list/list_header.tpl"}
{block name=override_header}
	<div class="sensbitinpost sensbitinpost-parent">
		{if isset($order_completed) && $order_completed}
			<style>
				.table.sensbitinpost_shipment, .bulk-actions {
					display:none !important;
				}
			</style>
			<div class='alert alert-success'>
				{if count($infos) > 1}Zlecenia odbioru zostały utworzone.{else}Zlecenie odbioru zostało utworzone.{/if}
			</div>
			<div class='row'>
				{foreach $infos as $info}
					<div class='col-sm-6'>
						<ul class='list-group'>
							<li class='list-group-item'>
								<h4>Zlecenie nr <strong>{if isset($info.dispatch_order)}{$info.dispatch_order}{/if}</strong></h4>
							</li>
							<li class='list-group-item'>
								<strong>Łącznie przesyłek</strong> <span class="badge">{$info.shipments|count}</span><br/>
								{foreach $info.shipments_res as $s}
									<a href="#" onclick='sensbitinpost.printLabels({$s->id});return false;'>{$s->tracking_number}</a>
								{/foreach}
							</li>
							<li class='list-group-item'>
								<a href="#" class="btn btn-primary btn-block btn-xs" onclick='sensbitinpost.printLabels({$info.shipments|json_encode});
										return false;'>Pobierz etykiety</a>
								<a href="#" class="btn btn-success btn-block" style='text-transform: uppercase' onclick='sensbitinpost.printProtocol({$info.shipments|json_encode});
										return false;'>Pobierz protokół nadań przesyłek</a>
							</li>
						</ul>
					</div>
				{/foreach}
			</div>
		{else}
			{if $packages}
				<div class='sensbitinpost-orders-filters' style='margin-bottom:20px;'>
					<div class="row">
						<div class="col-sm-6">
							<h4>Adres odbioru dla kuriera</h4>
							<div class="alert alert-info">Skorzystaj z poniższych opcji gdy chcesz zamówić kuriera i utworzyć zlecenie odbioru na podstawie którego wygenerujesz protokół.</div>

							<div class="filter-group">
								{if isset($pickupSenderPoints) && !empty($pickupSenderPoints)}
									<select id="sensbitinpost_pickup_sender_point" name="sensbitinpost_pickup_sender_point">
										<option disabled>Wybierz z predefiniowanych...</option>
										{foreach $pickupSenderPoints as $p}
											<option{if $p.default} selected="selected"{/if} data-json='{$p|json_encode nofilter}' value="{$p.id_pickup_sender|intval}">{$p.label}</option>
										{/foreach}
									</select>
								{else}
									<div class="alert alert-danger">
										Nie zdefiniowano punktów odbioru dla kuriera. Użyto danych nadawcy. <a href="">Kliknij by ustalić punkty odbioru.</a>
									</div>
								{/if}
							</div>
							<div class="filter-group">
								<div class="btn-group">
									<div class="btn btn-primary">
										Nazwa
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitinpost-tip' value="{if isset($order_carrier.address.name)}{$order_carrier.address.name}{/if}"  name='pickup_sender_name' placeholder='Nazwa'/>
									</div>
								</div>
							</div>
							<div class="filter-group">
								<div class="btn-group">
									<div class="btn btn-primary">
										Ulica
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitinpost-tip' value="{if isset($order_carrier.address.street)}{$order_carrier.address.street}{/if}"  name='address[street]' placeholder='Ulica'/>
									</div>
								</div>
							</div>
							<div class="filter-group">
								<div class="btn-group">
									<div class="btn btn-primary">
										Nr budynku/lokalu
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitinpost-tip' value="{if isset($order_carrier.address.building_number)}{$order_carrier.address.building_number}{/if}"  name='address[building_number]' placeholder='Nr budynku/lokalu'/>
									</div>
								</div>
							</div>

							<div class="filter-group">
								<div class="btn-group">
									<div class="btn btn-primary">
										Kod pocztowy
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitinpost-tip' value="{if isset($order_carrier.address.post_code)}{$order_carrier.address.post_code}{/if}"  name='address[post_code]' placeholder='Kod pocztowy'/>
									</div>
								</div>
							</div>
							<div class="filter-group">
								<div class="btn-group">
									<div class="btn btn-primary">
										Miasto
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitinpost-tip' value="{if isset($order_carrier.address.city)}{$order_carrier.address.city}{/if}"  name='address[city]' placeholder='Miasto'/>
									</div>
								</div>
							</div>
							<div class="filter-group">
								<div class="btn-group">
									<div class="btn btn-primary">
										Kod kraju
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitinpost-tip' value="{if isset($order_carrier.address.country_code)}{$order_carrier.address.country_code}{/if}"  name='address[country_code]' placeholder='Kod kraju np. PL'/>
									</div>
								</div>
							</div>
							<div class="filter-group">
								<div class="btn-group">
									<div class="btn btn-primary">
										Komentarz do zlecenia odbioru
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitinpost-tip' value="{if isset($order_carrier.comment)}{$order_carrier.comment}{/if}"  name='comment' placeholder='Komentarz'/>
									</div>
								</div>
							</div>
							<div class="filter-group">
								<div class="btn-group"  data-toggle="buttons">
									<div class="btn btn-primary">
										Dla każdego typu przesyłki utwórz osobne zlecenie odbioru kuriera
									</div>
									{if isset($order_carrier.split) && $order_carrier.split}
										{$active=true}
									{else}
										{$active=false}
									{/if}
									<label class="btn btn-default{if $active} active{/if}">
										<input type='radio' value="1"{if $active} checked="checked"{/if}  name='split' placeholder='Wiele zleceń na podstawie różnych typów przesyłek'/>
										TAK
									</label>
									<label class="btn btn-default{if !$active} active{/if}">
										<input type='radio' value="0"{if !$active} checked="checked"{/if}  name='split' placeholder='Wiele zleceń na podstawie różnych typów przesyłek'/>
										NIE
									</label>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<h4>Protokoły odbioru bez zamawiania kuriera</h4>
							<div class="alert alert-info">Skorzystaj z poniższych opcji gdy chcesz wydrukować protokół ale nie potrzebujesz zamawiać kuriera bo ten przyjeżdza do Ciebie codziennie.</div>
							<div class="filter-group">
								<button class="btn btn-success sensbitinpost-order-carrier-print-protocol sensbitinpost-tip" title="Dla wybranych przesyłek pobiera protokół odbioru dla kuriera">Wydrukuj protokół</button><br/>
							</div>
							<div class="filter-group">
								<button class="btn btn-warning sensbitinpost-order-carrier-print-protocol-send sensbitinpost-tip" title="Dla wybranych przesyłek pobiera protokół odbioru dla kuriera <strong> i ustawia je jako wysłane by zniknęły z listy przesyłek do wysłania</strong>.">Wydrukuj protokół i ustaw przesyłki jako wysłane</button>
							</div>
						</div>

					</div>

					<div class="filter-group">
						<input class="btn btn-success" type="submit" name="order_carrier" value="Zamów kuriera"/>
					</div>


				</div>
				{literal}
					<script>
						$(document).ready(function () {
							if ($.fn.datepicker) {
								$(".sensbitinpost-datepicker").datepicker({
									minDate: 0,
									prevText: '',
									nextText: '',
									dateFormat: 'yy-mm-dd'
								});
							}

							if ($.fn.datetimepicker) {
								$(".sensbitinpost-timepicker").datetimepicker({
									dateFormat: '',
									timeFormat: 'hh:mm tt',
									timeOnly: true
								});
							}

							$(".sensbitinpost_shipment input[type=checkbox]").prop('checked', true);
						});
					</script>
				{/literal}
			{else}
				<style>
					.table.sensbitinpost_shipment, .bulk-actions {
						display:none !important;
					}
				</style>
				<div class="alert alert-danger">
					Nie ma przesyłek dla których można by zamówić kuriera. Dodaj nową przesyłkę i sprawdź ponownie.
				</div>
			{/if}
		{/if}
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
