{*<pre>
{$data|print_r}
</pre>*}

<tr id='package_{$uniq}' class='package' data-id='{$uniq}'>
	<td>
		<div class="message">Trwa przygotowywanie przesyłki.</div>
		<input type="checkbox" value="{$uniq}" name="checked" checked="checked"/>
	</td>
	<td>
		<input type="hidden" value="{$data.id_order}" name="id_order" class='param'/>
		<input type="hidden" value="{$uniq}" name="uniq" class='param'/>
		<input type="hidden" value="{$data.service}" name="service" class='param'/>
		<input type="hidden" value="{$data.is_point|intval}" name="is_point" class='param'/>
		<input type="hidden" value="0" name="id_shipment" class='param'/>
		{$data.template}
	</td>
	<td>
		<div class='tip' title='{$data.service_name}'>{$data.service_logo}</div>
	</td>
	<td>
		<input class='param form-control' name="email" type="email" value="{$data.email}" placeholder="{l s='Email' mod='sensbitdpd'}" />
		<input class='param form-control' name="phone" type="text" value="{if empty($data.phone_mobile)}{$data.phone}{else}{$data.phone_mobile}{/if}" placeholder="{l s='Phone' mod='sensbitdpd'}"/>
	</td>
	<td>
		{if $data.is_point}
			<input style='width: 100%' type='text' name='id_point' value="{$data.id_point}" class='param package_{$uniq}_id_point' placeholder="{l s='ID punktu odbioru' mod='sensbitdpd'}"/>
			<input type="text" class='place_name' value="{if isset($data.place_label)}{$data.place_label}{/if}" disabled="disabled"/>
		{/if}
		<div class='address' {if $data.is_point}style="display:none"{/if}>
			<div style="display:inline">
				{if $data.company}
					{$data.company} <br/>
				{/if}
				{$data.firstname} {$data.lastname}
				<br/>
				{$data.address1} {$data.address2}<br/>
				{$data.postcode} {$data.city}<br/>
				{$data.country_iso_code}
			</div>
			<button class="btn btn-default btn-xs edit-address tip" title="{l s='Edit address' mod='sensbitdpd'}"><i class="icon-edit"></i></button>
		</div>
		<div class='address-edit' style="display:none; max-width:230px;">
			<input type='text' class='param' value="{$data.company}" name='company' placeholder='{l s='Company' mod='sensbitdpd'}'/>
			<div class='row'>
				<div class='col-sm-6'>
					<input type='text' class='param' value="{$data.firstname}" name='firstname' placeholder='{l s='Firstname' mod='sensbitdpd'}'/>
				</div>
				<div class='col-sm-6'>
					<input type='text' class='param' value="{$data.lastname}"  name='lastname' placeholder='{l s='Lastname' mod='sensbitdpd'}'/>
				</div>
			</div>
			<div class='row'>
				<div class='col-sm-6'>
					<input type='text' class='param' value="{$data.address1}"  name='street' placeholder='{l s='Street' mod='sensbitdpd'}'/>
				</div>
				<div class='col-sm-6'>
					<input type='text' class='param' value="{$data.address2}"  name='building_number' placeholder='{l s='Building number' mod='sensbitdpd'}'/>
				</div>
			</div>
			<div class='row'>
				<div class='col-sm-4'>
					<input type='text' class='param' value="{$data.postcode}"  name='postcode' placeholder='{l s='Postcode' mod='sensbitdpd'}'/>
				</div>
				<div class='col-sm-4'>
					<input type='text' class='param' value="{$data.city}"  name='city' placeholder='{l s='City' mod='sensbitdpd'}'/>
				</div>
				<div class='col-sm-4'>
					<input type='text' class='param' value="{$data.country_iso_code}"  name='country_iso_code' placeholder='{l s='Country code' mod='sensbitdpd'}'/>
				</div>
			</div>

			<button class="btn btn-success btn-xs save-address tip" title="{l s='Save address' mod='sensbitdpd'}"><i class="icon-check"></i></button>

		</div>

	</td>
	<td>
		{if isset($data.options.cod)}
			<div class="input-group">
				<span class="input-group-addon"><input class='param' placeholder="{l s='Is COD' mod='sensbitdpd'}" name="cod" type="checkbox"{if $data.options.cod} checked='checked'{/if}/></span>
				<input class='param' style="width:70px" placeholder="{l s='COD value' mod='sensbitdpd'}" name="cod_value" type="text" value="{if empty($data.options.cod_amount)}{$data.total_paid_tax_incl|round:2}{else}{$data.options.cod_amount}{/if}"/>
				<input class='param' style="width:40px;text-align: center" placeholder="{l s='COD Currency' mod='sensbitdpd'}" name="cod_currency" type="text" value="{if empty($data.options.cod_currency)}{$data.currency}{else}{$data.options.cod_currency}{/if}"/>
			</div>
		{else}
			-
		{/if}
	</td>
	<td>
		<input class='param' name="reference" type="text" value="{$data.custom_reference}" placeholder="{l s='Reference' mod='sensbitdpd'}"/>
	</td>
	<td>
		<div class="subpackages">
			<div class="subpackage">
				<div class="number">
					<input type='text' value="1" disabled='disabled'/>
					<button class="btn btn-xs btn-block btn-warning copy"><i class="icon-copy"></i></button>
					<button class="btn btn-xs btn-block btn-danger remove"><i class="icon-remove"></i></button>
				</div>
				<div class="data">
					<div>
						<input type='text' class='package-param' value="{if isset($data.options.package_content) && $data.options.package_content}{$data.options.package_content}{else}{/if}" name='package_content' placeholder='Zawartość paczki'/>
					</div>
					<div>
						<div class="input-group" style='width:100%'>
							<input type='text' class='package-param' value="{if $data.options.weight}{$data.options.weight}{else}{$data.total_weight}{/if}"  name='weight' placeholder='Waga paczki'/>
							<span class="input-group-addon">kg.</span>
						</div>
					</div>
					<div>
						<div class="input-group">
							<input type='text' style='min-width:30px;' class='package-param' value="{if $data.options.length}{$data.options.length}{else}{$data.total_depth}{/if}"  name='length' placeholder='Długość paczki'/>
							<span class="input-group-btn" style="width:0"></span>
							<input type='text' style='min-width:30px;' class='package-param' value="{if $data.options.width}{$data.options.width}{else}{$data.total_width}{/if}"  name='width' placeholder='Szerokość paczki'/>
							<span class="input-group-btn" style="width:0"></span>
							<input type='text' style='min-width:30px;' class='package-param' value="{if $data.options.height}{$data.options.height}{else}{$data.total_height}{/if}"  name='height' placeholder='Wysokość paczki'/>
							<span class="input-group-addon">cm.</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</td>
	<td>
		<button class="btn btn-xs btn-info show-options"><i class="icon-list"></i></button>
	</td>
	<td>
		<button class="btn btn-xs btn-danger remove-package"><i class="icon-remove"></i></button>
	</td>
</tr>

<tr id='package_options_{$uniq}' class="package-options">
	<td colspan="9">
		{assign value=$data.service var='service'}
		{include file="./new_package_form_options/service_options.tpl"}
	</td>
</tr> 
