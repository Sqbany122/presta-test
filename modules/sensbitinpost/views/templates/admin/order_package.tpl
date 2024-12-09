{*<pre>
{$data|print_r}
</pre>*}
<tr id='shipment_{$data.id_shipment}' class='shipment' data-id>
	<td>
		<input type="hidden" value="{$data.id_shipment}" name="id_shipment" class='param'/>
	</td>
	<td>
		{if isset($data.tracking_number)}
			<a href="{SensbitInpostShipment::getTrackingLinkStatic($data.tracking_number)}" target="_blank">{$data.tracking_number}</a>
		{else}
			-
		{/if}
	</td>
	<td>
		/status
	</td>
	<td>
		/data
	</td>
	<td>
		/autor
	</td>
	<td>
		<button class="btn btn-xs btn-danger remove-package"><i class="icon-remove"></i></button>
	</td>
</tr>