<tr id='shipment_{$shipment->id}'>
	<td>
		<div class="message"></div>
		<input type="checkbox" value="{$shipment->id}" checked="checked" class="completed-packs"/>
	</td>
	<td>
		<div class='sensbitdpd-tip' title='{$service->getName()}'>{$service->getLogo()}</div>
	</td>
	<td>
		{$shipment->tracking_number}
	</td>
	<td>
		{$status = $shipment->getStatus()}
		<a href='#' onclick="sensbitdpd.getPackStatus({$shipment->id});return false" class='sensbitdpd-tip sensbitdpd-pack-status' data-id-shipment='{$shipment->id}' data-autocheck="{$status.autocheck}" title='{l s='Kliknij aby sprawdzić aktualny status przesyłki' mod='sensbitdpd'}'>{$status.title}</a>
	</td>
	<td>
		{$shipment->date_add}
	</td>
	<td>
		{$shipment->getEmployeeName()}
	</td>
	<td>
		<button onclick="sensbitdpd.printLabels({$shipment->id});return false" class="btn btn-xs btn-success sensbitdpd-tip" title="{l s='Print label' mod='sensbitdpd'}"><i class="icon-print"></i></button>
		<button onclick="sensbitdpd.deleteShipments({$shipment->id});return false" class="btn btn-xs btn-danger sensbitdpd-tip" title="{l s='Delete shipment' mod='sensbitdpd'}"><i class="icon-remove"></i></button>
	</td>
</tr>