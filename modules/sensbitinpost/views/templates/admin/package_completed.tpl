<tr id='shipment_{$shipment->id}'>
	<td>
		<div class="message"></div>
		<input type="checkbox" value="{$shipment->id}" checked="checked" class="completed-packs"/>
	</td>
	<td>
		<div class='sensbitinpost-tip' title='{$service->getName()}'>{$service->getLogo()}</div>
	</td>
	<td>
		<a href="{SensbitInpostShipment::getTrackingLinkStatic($shipment->tracking_number)}" target="_blank">{$shipment->tracking_number}</a>
	</td>
	<td>
		{$status = $shipment->getStatus()}
		<a href='#' onclick="sensbitinpost.getPackStatus({$shipment->id});return false" class='sensbitinpost-tip sensbitinpost-status-checker sensbitinpost-pack-status' data-id-shipment='{$shipment->id}' data-autocheck="{$status.autocheck}" title='{l s='Kliknij aby sprawdzić aktualny status przesyłki' mod='sensbitinpost'}'>{$status.title}</a>
	</td>
	<td>
		{$shipment->date_created}
	</td>
	<td>
		{$shipment->getEmployeeName()}
	</td>
	<td>
		<button onclick="sensbitinpost.printLabels({$shipment->id});return false" class="btn btn-xs btn-success sensbitinpost-tip" title="{l s='Print label' mod='sensbitinpost'}"><i class="icon-print"></i></button>
			{if $shipment->isReturnable()}
			<button onclick="sensbitinpost.printReturnLabels({$shipment->id});return false" class="btn btn-xs btn-default sensbitinpost-tip" title="{l s='Print return label' mod='sensbitinpost'}"><i class="icon-print"></i> <i class="icon-undo"></i></button>
			{/if}

		{if Configuration::get(SensbitInpost::CFG_SIMPLE_PRINTNODE_ENABLED)}
			<button onclick="sensbitinpost.printNode({$shipment->id});return false" class="btn btn-xs btn-warning sensbitinpost-tip" title="{l s='Print label on PrintNode' mod='sensbitinpost'}"><i class="icon-print"></i> <i class="icon-cloud"></i></button>
			{/if}
			{if true || $status == 'created' || $status == 'offers_prepared'}
			<button onclick="sensbitinpost.deleteShipments({$shipment->id});return false" class="btn btn-xs btn-danger sensbitinpost-tip" title="{l s='Delete shipment' mod='sensbitinpost'}"><i class="icon-remove"></i></button>
			{/if}

	</td>
</tr>