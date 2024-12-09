<div style="clear:both"></div>
{$data=$sensbitinpost}

{capture name='content'}
	<form class='packages-ready-form messages-container'{if empty($data.shipments)} style="display:none"{/if}>
		<h4>{l s='Packages created for order' mod='sensbitinpost'} {$data.order->reference}</h4>
		<table class='table'>
			<thead>
				<tr>
					<th></th>
					<th>{l s='Service' mod='sensbitinpost'}</th>
					<th>{l s='Tracking number' mod='sensbitinpost'}</th>
					<th>{l s='Status przesyłki' mod='sensbitinpost'}</th>
					<th>{l s='Create date' mod='sensbitinpost'}</th>
					<th>{l s='Created by' mod='sensbitinpost'}</th>
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
			<button class="btn btn-default print-labels">{l s='Print labels' mod='sensbitinpost'}</button>
			<button class="btn btn-default delete-shipments">{l s='Cancel shipments' mod='sensbitinpost'}</button>

		</div>
	</form>


	{if empty($data.templates) && empty($data.global_templates)}
		<div class='alert alert-warning'>Nie posiadasz skonfigurowanych szablonów przesyłek.</div>
	{else}
		{if !empty($data.templates)}
			<div class="sensbitinpost-connected-templates">
				<h4>Szablony powiązane z tym zamówieniem</h4>
				{foreach $data.templates as $template}
					<a href='' class='button btn btn-default btn-xs sensbitinpost-service' data-id='{$template.id_template}'>{$template.name} {SensbitInpostService::getServiceLogo($template.service)|unescape}</a>
				{/foreach}
			</div>
		{else}
			<div class='alert alert-warning'>To zamówienie nie posiada żadnych przypisanych szablonów.</div>
		{/if}
		{if !empty($data.global_templates)}
			<h4{if !empty($data.templates)} style="margin-top:15px"{/if}>Wszystkie pozostałe szablony niepowiązane z tym zamówieniem. <a href="#" class="btn btn-xs btn-success switch_global_templates s">Pokaż</a><a href="#" class="btn btn-xs btn-warning switch_global_templates h">Ukryj</a></h4>
			<div class="global_templates">
				<div class="alert alert-info">{l s='Under each template you can see information why the template is not related to this order' mod='sensbitinpost'}</div>
				{foreach $data.global_templates as $template}
					<a href='' class='button btn btn-default btn-xs sensbitinpost-service' data-id='{$template.id_template}'>{$template.name} {SensbitInpostService::getServiceLogo($template.service)|unescape}
						<div class='sensbitinpost-valid-info'>
							{*	{if !$template.valid_carrier || !$template.valid_weight || !$template.valid_price || !$template.valid_module}
							{l s='Invalid' mod='sensbitinpost'}
							{/if}*}
							{if !$template.valid_carrier}<span>{l s='Other carrier' mod='sensbitinpost'}</span>{/if}
							{if !$template.valid_weight}<span>{l s='Wrong weight range' mod='sensbitinpost'}</span>{/if}
							{if !$template.valid_price}<span>{l s='Wrong price range' mod='sensbitinpost'}</span>{/if}
							{if !$template.valid_module}<span>{l s='Other payment module' mod='sensbitinpost'}</span>{/if}
						</div>
					</a>
				{/foreach}
			</div>
		{/if}
		<form class='packages-form messages-container' style="display:none">
			{*<h3>{l s='Create new package for order ' mod='sensbitinpost'} {$data.order->reference}</h3>*}
			<table class='table'>
				<thead>
					<tr>	
						<th></th>
						<th>{l s='Template' mod='sensbitinpost'}</th>
						<th>{l s='Service' mod='sensbitinpost'}</th>
						<th>{l s='Size' mod='sensbitinpost'}</th>
						<th>{l s='Sending method' mod='sensbitinpost'}</th>
						<th>{l s='Receiver contact data' mod='sensbitinpost'}</th>
						<th>{l s='Destination' mod='sensbitinpost'}</th>
						<th>{l s='Cash on delivery' mod='sensbitinpost'}</th>
						<th>{l s='Insurance' mod='sensbitinpost'}</th>
						<th>{l s='Reference' mod='sensbitinpost'}</th>
						<th>{l s='Szczegóły' mod='sensbitinpost'}</th>
						<th></th>
					</tr>
				</thead>

				<tbody class="package-container">

				</tbody>

			</table>
			<div style="margin-top:20px">
				<button class="btn btn-warning prepare-packs">{l s='Create' mod='sensbitinpost'}</button>
			</div>
		</form>

		<script>
			{if !SensbitInpostTools::isBootstrap()}
			$('.sensbitinpost').eq(0).insertAfter($('.sensbitinpost').eq(0).parent());
			{/if}
			sensbitinpost.setOptions({
				id_order: {$data.order->id|intval},
				ajax_url_packages: '{$link->getAdminLink('AdminSensbitInpostPackage')}',
				google_key: '{$data.google_key}'
			});
		</script>
	{/if}
{/capture}

<div class="sensbitinpost{if $data.hide_global_templates} hide_global_templates{/if}{if empty($data.templates) && $data.hide_panel_if_no_templates} hide_no_templates{/if}">
	{if $data.bootstrap}
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-truck"></i> Wysyłka z Inpost.pl <a href="{$data.module_link}"><i class="icon-cogs"></i></a> <a href="#" class="switch_no_templates s">Pokaż</a><a href="#" class="switch_no_templates h">Ukryj</a>
			</div>
			<div class="panel-body panel_container">
				{if $data.customer_parcel_locker}
					{if $data.hide_no_point}
						<div class="alert alert-warning"><strong>Uwaga!</strong> Klient wybrał paczkomat w zamówieniu ale następnie zmienił przewoźnika!</div>
					{/if}
					<div class="alert alert-info">
						Wybrany paczkomat przez klienta: <strong>{$data.customer_parcel_locker}</strong>{if isset($data.customer_parcel_locker_data.address)}  <em>{$data.customer_parcel_locker_data.address}</em>{else} <strong style="color:#c00">Uwaga. Nie można pobrać danych tego paczkomatu. Może już nie istnieć!</strong>{/if}
					</div>
				{elseif !$data.hide_no_point}
					<div class="alert alert-{if $data.default_parcel_locker}warning{else}danger{/if}">
						{if $data.default_parcel_locker}
							Klient nie wybrał paczkomatu w tym zamówieniu ale znamy jego ostatnio wybrany paczkomat: <strong>{$data.default_parcel_locker}</strong>{if isset($data.default_parcel_locker_data.address)} <em>{$data.default_parcel_locker_data.address}</em>{else} <strong style="color:#c00">Uwaga. Nie można pobrać danych tego paczkomatu. Może już nie istnieć!</strong>{/if}
						{else}
							Klient nie wybrał paczkomatu w tym zamówieniu.
						{/if}
					</div>
				{/if}
				{$smarty.capture.content}
			</div>
		</div>
	{else}
		<fieldset class="panel">
			<legend><img src="../img/admin/delivery.gif"> Wysyłka z Inpost.pl <a href="{$data.module_link}"><i class="icon-cogs"></i></a> <a href="#" class="btn btn-xs btn-success switch_no_templates s">Pokaż</a><a href="#" class="btn btn-xs btn-warning switch_no_templates h">Ukryj</a></legend>
			<div class="panel_container">
				{if $data.customer_parcel_locker}
					{if $data.hide_no_point}
						<div class="alert alert-warning"><strong>Uwaga!</strong> Klient wybrał paczkomat w zamówieniu ale następnie zmienił przewoźnika!</div>
					{/if}
					<div class="alert alert-info">
						Wybrany paczkomat przez klienta: <strong>{$data.customer_parcel_locker}</strong>{if isset($data.customer_parcel_locker_data.address)}  <em>{$data.customer_parcel_locker_data.address}</em>{else} <strong style="color:#c00">Uwaga. Nie można pobrać danych tego paczkomatu. Może już nie istnieć!</strong>{/if}
					</div>
				{elseif !$data.hide_no_point}
					<div class="alert alert-{if $data.default_parcel_locker}warning{else}danger{/if}">
						{if $data.default_parcel_locker}
							Klient nie wybrał paczkomatu w tym zamówieniu ale znamy jego ostatnio wybrany paczkomat: <strong>{$data.default_parcel_locker}</strong>{if isset($data.default_parcel_locker_data.address)} <em>{$data.default_parcel_locker_data.address}</em>{else} <strong style="color:#c00">Uwaga. Nie można pobrać danych tego paczkomatu. Może już nie istnieć!</strong>{/if}
						{else}
							Klient nie wybrał paczkomatu w tym zamówieniu.
						{/if}
					</div>
				{/if}
				{$smarty.capture.content}
			</div>
		</fieldset>
	{/if}
</div>

