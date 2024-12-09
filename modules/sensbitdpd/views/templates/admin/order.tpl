<div style="clear:both"></div>
{$data=$sensbitdpd}

{capture name='content'}
	<form class='packages-ready-form messages-container'{if empty($data.shipments)} style="display:none"{/if}>
		<h4>{l s='Packages created for order' mod='sensbitdpd'} {$data.order->reference}</h4>
		<table class='table'>
			<thead>
				<tr>
					<th></th>
					<th>{l s='Service' mod='sensbitdpd'}</th>
					<th>{l s='Tracking number' mod='sensbitdpd'}</th>
					<th>{l s='Status przesyłki' mod='sensbitdpd'}</th>
					<th>{l s='Create date' mod='sensbitdpd'}</th>
					<th>{l s='Created by' mod='sensbitdpd'}</th>
					<th></th>
				</tr>
			</thead>
			<tbody class='packages-ready-container'>
				{foreach from=$data.shipments item=shipment}
					{$shipment->getCompletedRowHtml()}
				{/foreach}
			</tbody>
		</table>
		<div class="packages-completed-actions">
			<button class="btn btn-default print-labels">{l s='Print labels' mod='sensbitdpd'}</button>
			<button class="btn btn-default delete-shipments">{l s='Cancel shipments' mod='sensbitdpd'}</button>

		</div>
	</form>


	{if empty($data.templates) && empty($data.global_templates)}
		<div class='alert alert-warning'>Nie posiadasz skonfigurowanych szablonów przesyłek.</div>
	{else}
		{if !empty($data.templates)}
			<h4>Szablony powiązane z tym zamówieniem</h4>
			{foreach $data.templates as $template}
				<a href='' class='button btn btn-default btn-xs sensbitdpd-service' data-id='{$template.id_template}'>{$template.name} {SensbitDpdService::getServiceLogo($template.service)|unescape}</a>
			{/foreach}
		{else}
			<div class='alert alert-warning'>To zamówienie nie posiada żadnych przypisanych szablonów.</div>
		{/if}
		{if !empty($data.global_templates)}
			<h4{if !empty($data.templates)} style="margin-top:15px"{/if}>Wszystkie pozostałe szablony niepowiązane z tym zamówieniem. <a href="#" class="btn btn-xs btn-success switch_global_templates s">Pokaż</a><a href="#" class="btn btn-xs btn-warning switch_global_templates h">Ukryj</a></h4>
			<div class="global_templates">
				{foreach $data.global_templates as $template}
					<a href='' class='button btn btn-default btn-xs sensbitdpd-service' data-id='{$template.id_template}'>{$template.name} {SensbitDpdService::getServiceLogo($template.service)|unescape}</a>
				{/foreach}
			</div>
		{/if}

		<form class='packages-form messages-container' style="display:none">
			{*<h3>{l s='Create new package for order ' mod='sensbitdpd'} {$data.order->reference}</h3>*}
			<table class='table'>
				<thead>
					<tr>	
						<th></th>
						<th>{l s='Szablon' mod='sensbitdpd'}</th>
						<th>{l s='Usługa' mod='sensbitdpd'}</th>
						<th>{l s='Dane kontaktowe odbiorcy' mod='sensbitdpd'}</th>
						<th>{l s='Dostawa do' mod='sensbitdpd'}</th>
						<th>{l s='Pobranie' mod='sensbitdpd'}</th>
						<th>{l s='Opis przesyłki' mod='sensbitdpd'}</th>
						<th>Paczki</th>
						<th>{l s='Szczegóły' mod='sensbitdpd'}</th>
						<th></th>
					</tr>
				</thead>

				<tbody class="package-container">

				</tbody>

			</table>
			<div style="margin-top:20px">
				<button class="btn btn-warning prepare-packs">{l s='Create' mod='sensbitdpd'}</button>
			</div>
		</form>

		<script>
			{if !SensbitDpdTools::isBootstrap()}
			$('.sensbitdpd').eq(0).insertAfter($('.sensbitdpd').eq(0).parent());
			{/if}
			sensbitdpd.setOptions({
				id_order: {$data.order->id|intval},
				ajax_url_packages: '{$link->getAdminLink('AdminSensbitDpdPackage')}',
				google_key: '{$data.google_key}'
			});
		</script>
	{/if}
{/capture}

<div class="sensbitdpd{if $data.hide_global_templates} hide_global_templates{/if}{if empty($data.templates) && $data.hide_panel_if_no_templates} hide_no_templates{/if}">
	{if $data.bootstrap}
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-truck"></i> Wysyłka z DPD <a href="{$data.module_link}"><i class="icon-cogs"></i></a> <a href="#" class="switch_no_templates s">Pokaż</a><a href="#" class="switch_no_templates h">Ukryj</a>
			</div>
			<div class="panel-body panel_container">
				{if $data.customer_id_point}
					<div class="alert alert-info">
						Wybrany punkt odbioru przez klienta: <strong>{$data.customer_id_point}</strong>{if isset($data.customer_id_point_data.address)}  <em>{$data.customer_id_point_data.address}</em>{else} <strong style="color:#c00">Uwaga. Nie można pobrać danych tego punktu odbioru. Może już nie istnieć!</strong>{/if}
					</div>
				{elseif !$data.hide_no_point}
					<div class="alert alert-{if $data.default_id_point}warning{else}danger{/if}">
						{if $data.default_id_point}
							Klient nie wybrał punktu odbioru w tym zamówieniu ale znamy jego ostatnio wybrany punkt: <strong>{$data.default_id_point}</strong>{if isset($data.default_id_point_data.address)} <em>{$data.default_id_point_data.address}</em>{else} <strong style="color:#c00">Uwaga. Nie można pobrać danych tego punktu odbioru. Może już nie istnieć!</strong>{/if}
						{else}
							Klient nie wybrał punktu odbioru w tym zamówieniu.
						{/if}
					</div>
				{/if}
				{$smarty.capture.content}
			</div>
		</div>
	{else}
		<fieldset class='panel'>
			<legend><img src="../img/admin/delivery.gif"> Wysyłka z DPD<a href="#" class="btn btn-xs btn-success switch_no_templates s">Pokaż</a><a href="#" class="btn btn-xs btn-warning switch_no_templates h">Ukryj</a></legend>
			<div class="panel_container">
				{$smarty.capture.content}
			</div>
		</fieldset>
	{/if}
</div>

