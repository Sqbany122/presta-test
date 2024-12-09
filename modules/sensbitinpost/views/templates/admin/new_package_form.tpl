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
		<input type="hidden" value="{$data.is_locker|intval}" name="is_locker" class='param'/>
		<input type="hidden" value="{$data.is_allegro|intval}" name="is_allegro" class='param'/>
		<input type="hidden" value="{$data.is_courier|intval}" name="is_courier" class='param'/>
		<input type="hidden" value="0" name="id_shipment" class='param'/>
		{$data.template}
	</td>
	<td>
		{if $data.is_allegro}
			<div class="row">
				<div class="col-sm-6">
					<div class='tip' title='{$data.service_name}'>{$data.service_logo}</div>
				</div>
				<div class="col-sm-6">
					<div class="input-group">
						<input type='text' class='param' value="{$data.options.allegro_user_id}"  name='allegro_user_id' placeholder='{l s='Allegro user id' mod='sensbitinpost'}'/>
						<input type='text' class='param' value="{$data.allegro_transaction_id}"  name='allegro_transaction_id' placeholder='{l s='Allegro transaction id' mod='sensbitinpost'}'/>
					</div>
				</div>
			</div>
		{else}
			<div class='tip' title='{$data.service_name}'>{$data.service_logo}</div>
		{/if}
	</td>
	<td>
		{if $data.is_courier}
			<div class="subpackages">
				<div class="subpackage">
					<div class="number">
						<input type='text' value="1" disabled='disabled'/>

						<button class="btn btn-xs btn-block btn-warning copy"><i class="icon-copy"></i></button>
						<button class="btn btn-xs btn-block btn-danger remove"><i class="icon-remove"></i></button>

					</div>
					<div class="data" style="max-width:255px">
						<div>

							<div>
								<div class="input-group" style='width:100%'>
									<input data-placement="top" type='text' class='param' value="{if $data.options.weight}{$data.options.weight}{else}{$data.total_weight}{/if}"  name='weight' placeholder='Waga przesyłki'/>
									<span class="input-group-addon">kg.</span>
								</div>
							</div>
							<div>
								<div class="input-group">
									<input data-placement="bottom" type='text' style='min-width:30px;' class='param' value="{if $data.options.length}{$data.options.length}{else}{$data.total_depth}{/if}"  name='length' placeholder='Długośc przesyłki'/>
									<span class="input-group-btn" style="width:0"></span>
									<input data-placement="bottom" type='text' style='min-width:30px;' class='param' value="{if $data.options.width}{$data.options.width}{else}{$data.total_width}{/if}"  name='width' placeholder='Szerokość przesyłki'/>
									<span class="input-group-btn" style="width:0"></span>
									<input data-placement="bottom" type='text' style='min-width:30px;' class='param' value="{if $data.options.height}{$data.options.height}{else}{$data.total_height}{/if}"  name='height' placeholder='Wysokość przesyłki'/>
									<span class="input-group-addon">cm.</span>
								</div>
							</div>
							<div>
								<label>
									<input data-placement="bottom" class='param' placeholder="Zaznacz jeśli dana paczka jest niestandardowa." name="is_non_standard" type="checkbox"{if isset($data.options.is_non_standard) && $data.options.is_non_standard} checked='checked'{/if}/>
									Przesyłka niestandardowa
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		{elseif $sizes}
			<select name="size" class='param' title="{l s='Size' mod='sensbitinpost'}">
				{foreach $sizes as $size}
					<option value="{$size.id}"{if isset($data.options.template) && $data.options.template == $size.id} selected='selected'{/if}>{$size.label}</option>
				{/foreach}
			</select>
		{else}
			-
		{/if}
	</td>
	<td>
		{if $sending_methods}
			<select class="param sending-method" name="sending_method" placeholder="{l s='Sending method' mod='sensbitinpost'}">
				{foreach $sending_methods as $sending_method}
					<option value="{$sending_method.id}"{if isset($data.options.sending_method) && $data.options.sending_method == $sending_method.id} selected='selected'{/if}>{$sending_method.label}</option>
				{/foreach}
			</select>
		{/if}
		<div class="input-group dropoff-point-container" style='display:none'>
			<input type='text' name='dropoff_point' value="{if isset($data.options.dropoff_point)}{$data.options.dropoff_point}{/if}" class='param package_{$uniq}_dropoff_point' placeholder="{l s='Dropoff point' mod='sensbitinpost'}"/>
			<span class="input-group-addon"><button title="{l s='Select from map' mod='sensbitinpost'}" class="btn btn-xs btn-warning tip" onclick="sensbitinpost.openMap('.package_{$uniq}_dropoff_point');return false;">{l s='Map' mod='sensbitinpost'}</button></span>
		</div>
	</td>
	<td>
		<input data-placement="top" class='param form-control' name="email" type="email" value="{$data.email}" placeholder="{l s='Email' mod='sensbitinpost'}" />
		<input data-placement="bottom" class='param form-control' name="phone" type="text" value="{if $data.is_locker}{$data.customer_phone}{else}{if empty($data.phone_mobile)}{$data.phone}{else}{$data.phone_mobile}{/if}{/if}" placeholder="{l s='Phone' mod='sensbitinpost'}"/>
	</td>
	<td>
		{if $data.is_locker}
			<div class="input-group">
				<input type='text' name='target_point' value="{$data.parcel_locker_name}" class='param package_{$uniq}_target_point' placeholder="{l s='Target point' mod='sensbitinpost'}"/>
				<span class="input-group-addon"><button title="{l s='Select from map' mod='sensbitinpost'}" class="btn btn-xs btn-warning tip" onclick="sensbitinpost.openMap('.package_{$uniq}_target_point');return false;">{l s='Map' mod='sensbitinpost'}</button></span>
			</div>

		{else}
			<div class='address'>
				<div style="display:inline">
					{if $data.company}
						{$data.company} <br/>
					{/if}
					{$data.firstname} {$data.lastname}
					<br/>
					{$data.address1} {$data.address2}<br/>
					{$data.postcode} {$data.city}
				</div>
				<button class="btn btn-default btn-xs edit-address tip" title="{l s='Edit address' mod='sensbitinpost'}"><i class="icon-edit"></i></button>
			</div>
			<div class='address-edit' style="display:none">
				<input type='text' class='param' value="{$data.company}" name='company' placeholder='{l s='Company' mod='sensbitinpost'}'/>
				<div class='row'>
					<div class='col-sm-6'>
						<input data-placement="left" type='text' class='param' value="{$data.firstname}" name='firstname' placeholder='{l s='Firstname' mod='sensbitinpost'}'/>
					</div>
					<div class='col-sm-6'>
						<input data-placement="right" type='text' class='param' value="{$data.lastname}"  name='lastname' placeholder='{l s='Lastname' mod='sensbitinpost'}'/>
					</div>
				</div>
				<div class='row'>
					<div class='col-sm-6'>
						<input data-placement="left" type='text' class='param' value="{$data.address1}"  name='street' placeholder='{l s='Street' mod='sensbitinpost'}'/>
					</div>
					<div class='col-sm-6'>
						<input data-placement="right" type='text' class='param' value="{$data.address2}"  name='building_number' placeholder='{l s='Building number' mod='sensbitinpost'}'/>
					</div>
				</div>
				<div class='row'>
					<div class='col-sm-6'>
						<input data-placement="left" type='text' class='param' value="{$data.postcode}"  name='postcode' placeholder='{l s='Postcode' mod='sensbitinpost'}'/>
					</div>
					<div class='col-sm-6'>
						<input data-placement="right" type='text' class='param' value="{$data.city}"  name='city' placeholder='{l s='City' mod='sensbitinpost'}'/>
					</div>
				</div>
				<button class="btn btn-success btn-xs save-address tip" title="{l s='Save address' mod='sensbitinpost'}"><i class="icon-check"></i></button>

			</div>
		{/if}
	</td>
	<td>
		{if isset($data.options.is_cod)}
			<div class="input-group">
				<span class="input-group-addon"><input class='param' placeholder="{l s='Is COD' mod='sensbitinpost'}" name="is_cod" type="checkbox"{if $data.options.is_cod} checked='checked'{/if}/></span>
				<input class='param' placeholder="{l s='COD value' mod='sensbitinpost'}" name="cod_value" type="text" value="{$data.total_paid_tax_incl|round:2}"/>
			</div>
		{else}
			-
		{/if}
	</td>
	<td>
		{if $insurances}
			{if $data.is_locker}
				<select class="param" name="insurance" placeholder="{l s='Insurance' mod='sensbitinpost'}">
					{foreach $insurances as $insurance}
						<option value="{$insurance.id}"{if isset($data.options.insurance) && $data.options.insurance == $insurance.id} selected='selected'{/if}>{$insurance.label}</option>
					{/foreach}
				</select>
			{else}
				<div class="input-group">
					<span class="input-group-addon"><input class='param' placeholder="{l s='Is insurance' mod='sensbitinpost'}" name="is_insurance" type="checkbox"{if isset($data.options.insurance) && $data.options.insurance != '0'} checked='checked'{/if}/></span>
					<input class='param' placeholder="{l s='Insurance value' mod='sensbitinpost'}" name="insurance" type="text" value="{if isset($data.options.insurance) && ($data.options.insurance == 'auto' || $data.options.insurance == '0')}{$data.total_paid_tax_incl|round:2}{else}{$data.options.insurance}{/if}"/>
				</div>
			{/if}
		{/if}
	</td>
	<td>
		<input class='param' name="reference" type="text" value="{$data.custom_reference}" placeholder="{l s='Reference' mod='sensbitinpost'}"/>
	</td>
	<td>
		<button class="btn btn-xs btn-info show-options"{if empty($data.service_options)} disabled="disabled"{/if}><i class="icon-list"></i></button>
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