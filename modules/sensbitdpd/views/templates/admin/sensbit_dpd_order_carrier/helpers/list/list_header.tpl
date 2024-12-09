
{extends file="helpers/list/list_header.tpl"}
{block name=override_header}
	<div class="sensbitdpd sensbitdpd-parent">
		{if isset($order_completed) && $order_completed}
			<style>
				.table.sensbitdpd_shipment, .bulk-actions {
					display:none !important;
				}
			</style>
			<div class='alert alert-success'>
				Kurier został zamówiony
			</div>
			<div class='row'>
				{foreach $order_data as $type => $info}
					{if !empty($info.id_shipments)}
						<div class='col-sm-6'>
							<ul class='list-group'>
								<li class='list-group-item'>
									<h4>{if $type == 'DOMESTIC'}Przesyłki krajowe{else}Przesyłki międzynarodowe{/if} - zlecenie nr <strong>{if isset($info.dispatch_order)}{$info.dispatch_order}{/if}</strong></h4>
								</li>
								<li class='list-group-item'>
									<strong>Standardowe przesyłki</strong> <span class="badge">{$info.parcelsCount}</span><br/>
									łączna waga: {$info.parcelsWeight} kg<br/>
									{if isset($info.parcelPacks)}
										{foreach $info.parcelPacks as $p}
											{$id_shipments[]=(int)$p.id_shipment}
											<a href="#" onclick='sensbitdpd.printLabels({$p.id_shipment});return false;'>{$p.tracking_number}</a>
										{/foreach}
									{/if}
								</li>
								<li class='list-group-item'>
									<strong>Przesyłki paletowe</strong> <span class="badge">{$info.palletsCount}</span><br/>
									łączna waga: {$info.palletsWeight} kg
									{if isset($info.palletPacks)}
										{foreach $info.palletPacks as $p}
											{$id_shipments[]=(int)$p.id_shipment}
											<a href="#" onclick='sensbitdpd.printLabels({$p.id_shipment});return false;'>{$p.tracking_number}</a>
										{/foreach}
									{/if}
								</li>
								<li class='list-group-item'>
									<strong>Przesyłki z dokumentami</strong> <span class="badge">{$info.doxCount}</span><br/>
									{if isset($info.doxPacks)}
										{foreach $info.doxPacks as $p}
											{$id_shipments[]=(int)$p.id_shipment}
											<a href="#" onclick='sensbitdpd.printLabels({$p.id_shipment});return false;'>{$p.tracking_number}</a>
										{/foreach}
									{/if}
								</li>
								<li class='list-group-item'>
									<a href="#" class="btn btn-primary btn-block btn-xs" onclick='sensbitdpd.printLabels({$info.id_shipments|json_encode});
										return false;'>Pobierz etykiety</a>
									<a href="#" class="btn btn-success btn-block" style='text-transform: uppercase' onclick='sensbitdpd.printProtocol({$info.id_shipments|json_encode});
										return false;'>Generuj protokół odbioru</a>
								</li>
							</ul>
						</div>
					{/if}
				{/foreach}
			</div>
		{else}
			{if $packages}
				<div class='sensbitdpd-orders-filters' style='margin-bottom:20px;'>
					<div class="row">
						<div class="col-sm-6">
							<h4>Parametry zlecenia</h4>
							<div class="filter-group">
								<div class="btn-group">
									<div class="btn btn-primary">
										Dane zlecającego
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitdpd-tip' value="{if isset($order_carrier.pickupCustomer)}{$order_carrier.pickupCustomer.customerFullName}{/if}"  name='pickupCustomer[customerFullName]' placeholder='Nazwa'/>
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitdpd-tip' value="{if isset($order_carrier.pickupCustomer)}{$order_carrier.pickupCustomer.customerName}{/if}"  name='pickupCustomer[customerName]' placeholder='Imię i nazwisko'/>
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitdpd-tip' value="{if isset($order_carrier.pickupCustomer)}{$order_carrier.pickupCustomer.customerPhone}{/if}"  name='pickupCustomer[customerPhone]' placeholder='Nr telefonu'/>
									</div>
								</div>
							</div>
							<div class="filter-group">
								<div class="btn-group">
									<div class="btn btn-primary">
										Dane płatnika
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitdpd-tip' value="{if isset($order_carrier.pickupPayer)}{$order_carrier.pickupPayer.payerNumber}{/if}"  name='pickupPayer[payerNumber]' placeholder='Numer płatnika'/>
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitdpd-tip' value="{if isset($order_carrier.pickupPayer)}{$order_carrier.pickupPayer.payerCostCenter}{/if}"  name='pickupPayer[payerCostCenter]' placeholder='Centrum kosztowe'/>
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitdpd-tip' value="{if isset($order_carrier.pickupPayer)}{$order_carrier.pickupPayer.payerName}{/if}"  name='pickupPayer[payerName]' placeholder='Nazwa'/>
									</div>
								</div>
							</div>
							<div class="filter-group">
								<div class="btn-group">
									<div class="btn btn-primary">
										Data i czas odbioru
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitdpd-tip sensbitdpd-datepicker' value="{if isset($order_carrier.pickupDate)}{$order_carrier.pickupDate}{/if}"  name='pickupDate' placeholder='Data odbioru yyyy-mm-dd'/>
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitdpd-tip sensbitdpd-timepicker' value="{if isset($order_carrier.pickupTimeFrom)}{$order_carrier.pickupTimeFrom}{/if}"  name='pickupTimeFrom' placeholder='Dolny zakres godzinowy odbioru'/>
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitdpd-tip sensbitdpd-timepicker' value="{if isset($order_carrier.pickupTimeTo)}{$order_carrier.pickupTimeTo}{/if}"  name='pickupTimeTo' placeholder='Górny zakres godzinowy odbioru'/>
									</div>
								</div>
							</div>
							<div class="filter-group">
								<div class="btn-group"  data-toggle="buttons">
									<div class="btn btn-primary">
										Listy przewozowe gotowe?
									</div>
									{if isset($order_carrier.waybillsReady) && $order_carrier.waybillsReady}
										{$active=true}
									{else}
										{$active=false}
									{/if}
									<label class="btn btn-default{if $active} active{/if}">
										<input type='checkbox' value="1"{if $active} checked="checked"{/if}  name='waybillsReady' placeholder='Czy przygotowane listy przewozowe?'/>
										TAK
									</label>
									<label class="btn btn-default{if !$active} active{/if}">
										<input type='checkbox' value="0"{if !$active} checked="checked"{/if}  name='waybillsReady' placeholder='Czy przygotowane listy przewozowe?'/>
										NIE
									</label>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<h4>Adres odbioru dla kuriera</h4>
							<div class="filter-group">
								{if isset($pickupSenderPoints) && !empty($pickupSenderPoints)}
									<select id="sensbitdpd_pickup_sender_point" name="sensbitdpd_pickup_sender_point">
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
										<input type='text' class='sensbitdpd-tip' value="{if isset($order_carrier.pickupSender)}{$order_carrier.pickupSender.senderName}{/if}"  name='pickupSender[senderName]' placeholder='Nazwa'/>
									</div>
								</div>
							</div>
							<div class="filter-group">
								<div class="btn-group">
									<div class="btn btn-primary">
										Imię i nazwisko
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitdpd-tip' value="{if isset($order_carrier.pickupSender)}{$order_carrier.pickupSender.senderFullName}{/if}"  name='pickupSender[senderFullName]' placeholder='Imię i nazwisko'/>
									</div>
								</div>
							</div>
							<div class="filter-group">
								<div class="btn-group">
									<div class="btn btn-primary">
										Adres
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitdpd-tip' value="{if isset($order_carrier.pickupSender)}{$order_carrier.pickupSender.senderAddress}{/if}"  name='pickupSender[senderAddress]' placeholder='Adres'/>
									</div>
								</div>
							</div>
							<div class="filter-group">
								<div class="btn-group">
									<div class="btn btn-primary">
										Miasto
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitdpd-tip' value="{if isset($order_carrier.pickupSender)}{$order_carrier.pickupSender.senderCity}{/if}"  name='pickupSender[senderCity]' placeholder='Miasto'/>
									</div>
								</div>
							</div>
							<div class="filter-group">
								<div class="btn-group">
									<div class="btn btn-primary">
										Kod pocztowy
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitdpd-tip' value="{if isset($order_carrier.pickupSender)}{$order_carrier.pickupSender.senderPostalCode}{/if}"  name='pickupSender[senderPostalCode]' placeholder='Kod pocztowy'/>
									</div>
								</div>
							</div>
							<div class="filter-group">
								<div class="btn-group">
									<div class="btn btn-primary">
										Telefon
									</div>
									<div style='width:170px;display:inline-block'>
										<input type='text' class='sensbitdpd-tip' value="{if isset($order_carrier.pickupSender)}{$order_carrier.pickupSender.senderPhone}{/if}"  name='pickupSender[senderPhone]' placeholder='Telefon'/>
									</div>
								</div>
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
								$(".sensbitdpd-datepicker").datepicker({
									minDate: 0,
									prevText: '',
									nextText: '',
									dateFormat: 'yy-mm-dd'
								});
							}

							if ($.fn.datetimepicker) {
								$(".sensbitdpd-timepicker").datetimepicker({
									dateFormat: '',
									timeFormat: 'hh:mm tt',
									timeOnly: true
								});
							}

							$(".sensbitdpd_shipment input[type=checkbox]").prop('checked', true);
						});
					</script>
				{/literal}
			{else}
				<style>
					.table.sensbitdpd_shipment, .bulk-actions {
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
		{if SensbitDpdTools::isBootstrap()}
			$(".sensbitdpd-parent").insertBefore("#form-sensbitdpd_shipment table.table");
		{else}
			$(".sensbitdpd-parent").insertBefore("table[name=list_table]");
		{/if}
		});
	</script>
{/block}
