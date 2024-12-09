{** 2014 DPD Polska Sp. z o.o.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * prestashop@dpd.com.pl so we can send you a copy immediately.
 *
 * @author JSC INVERTUS www.invertus.lt <help@invertus.lt>
 * @copyright 2014 DPD Polska Sp. z o.o.
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of DPD Polska Sp. z o.o.
 *}

<script>
	var dpdpoland_16 = true;
</script>

<form id="configuration_form" class="form-horizontal" action="{$saveAction|escape:'htmlall':'UTF-8'}&menu=configuration" method="post" enctype="multipart/form-data">
    <div id="credentials" class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i>
            {l s='DPD credentials' mod='dpdpoland'}
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3 required" for="login_input">
                {l s='Login:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="login_input" type="text" name="{DpdPolandConfiguration::LOGIN|escape:'htmlall':'UTF-8'}" value="{DpdPoland::getInputValue(DpdPolandConfiguration::LOGIN, $settings->login)|escape:'htmlall':'UTF-8'}" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3 required" for="password">
                {l s='Password:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="password" type="password" name="{DpdPolandConfiguration::PASSWORD|escape:'htmlall':'UTF-8'}" value="{DpdPoland::getInputValue(DpdPolandConfiguration::PASSWORD, $settings->password)|escape:'htmlall':'UTF-8'}" />
            </div>
        </div>

        <hr />

        <div class="form-group">
            <div class="bootstrap">
                <div id="error_message" class="alert alert-danger hidden-element"></div>
                <div id="success_message" class="alert alert-success hidden-element"></div>
            </div>

            <div class="col-lg-3">

            </div>
            <div class="col-lg-9">
                <div class="col-lg-5">
                    <label class="control-label col-lg-5" for="client_number">
                        {l s='DPD client number:' mod='dpdpoland'}
                    </label>
                    <div class="col-lg-7">
                        <input id="client_number" type="text" name="" value="" />
                    </div>
                </div>
                <div class="col-lg-5">
                    <label class="control-label col-lg-5" for="client_name">
                        {l s='Client name:' mod='dpdpoland'}
                    </label>
                    <div class="col-lg-7">
                        <input id="client_name" type="text" name="" value="" />
                    </div>
                </div>
                <div class="col-lg-2">
                    <a id="addClientNumber" class="btn btn-link confirm_leave">
						<i class="icon-plus-sign"></i> {l s='Add' mod='dpdpoland'}
					</a>
                </div>
            </div>

            <label class="control-label col-lg-3 required" for="password">
                {l s='Default client number:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <div id="client_numbers_table_container">
                    {include file=$smarty.const._DPDPOLAND_TPL_DIR_|cat:'admin/payer_numbers_table.tpl'}
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <button class="btn btn-default pull-right" name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
                <i class="process-icon-save"></i>
                {l s='Save' mod='dpdpoland'}
            </button>
        </div>
    </div>

    <div id="shipping" class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i>
            {l s='Active shiping services' mod='dpdpoland'}
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="dpd_standard">
                {l s='DPD domestic shipment - Standard:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="dpd_standard" type="checkbox" name="{DpdPolandConfiguration::CARRIER_STANDARD|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::CARRIER_STANDARD, $settings->carrier_standard)}checked="checked"{/if} value="1" />
            </div>
            <div class="col-lg-9 col-lg-offset-3">
                <p class="help-block">
                    {l s='DPD domestic shipment - Standard:' mod='dpdpoland'}
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3" for="dpd_standard_cod">
                {l s='DPD domestic shipment - Standard with COD:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="dpd_standard_cod" {if !DPDpoland::CODMethodIsAvailable()}onclick="alert(no_COD_methods_message); return false;"{/if} type="checkbox" name="{DpdPolandConfiguration::CARRIER_STANDARD_COD}" {if DpdPoland::getInputValue(DpdPolandConfiguration::CARRIER_STANDARD_COD, $settings->carrier_standard_cod) && DPDpoland::CODMethodIsAvailable()}checked="checked"{/if} value="1" />
            </div>
            <div class="col-lg-9 col-lg-offset-3">
                <p class="help-block">
                    {l s='DPD domestic shipment - Standard with COD' mod='dpdpoland'}
                </p>
            </div>
            {if DPDpoland::CODMethodIsAvailable()}
                <div class="payment_modules_container col-lg-offset-3">
                    <table>
                        {section name=iii loop=$payment_modules}
                            <tr>
                                <td align="right">
                                    <label>{$payment_modules[iii].displayName|escape:'htmlall':'UTF-8'}</label>
                                </td>
                                <td>
                                    &nbsp;<input type="checkbox" name="{DpdPolandConfiguration::COD_MODULE_PREFIX|escape:'htmlall':'UTF-8'}{$payment_modules[iii].name|escape:'htmlall':'UTF-8'}" value="1" {if Configuration::get(DpdPolandConfiguration::COD_MODULE_PREFIX|escape:'htmlall':'UTF-8'|cat:$payment_modules[iii].name|escape:'htmlall':'UTF-8')}checked="checked"{/if} />
                                </td>
                            </tr>
                        {/section}
                    </table>
                </div>
            {/if}
        </div>

        <p class="alert alert-info">
            {l s='RULE: DPD Polska Sp. z o.o. allows payment on the delivery ONLY by cash. In your payment modules you have available this types of payment, please mark those payment methods that support this rule.' mod='dpdpoland'}
        </p>

        <div class="form-group">
            <label class="control-label col-lg-3" for="dpd_classic">
                {l s='DPD international shipment (DPD Classic):' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="dpd_classic" type="checkbox" name="{DpdPolandConfiguration::CARRIER_CLASSIC|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::CARRIER_CLASSIC, $settings->carrier_classic)}checked="checked"{/if} value="1" />
            </div>
            <div class="col-lg-9 col-lg-offset-3">
                <p class="help-block">
                    {l s='DPD international shipment (DPD Classic)' mod='dpdpoland'}
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="dpd_pudo">
                {l s='DPD Poland Reception Point Pickup:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="dpd_pudo" type="checkbox" name="{DpdPolandConfiguration::CARRIER_PUDO|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::CARRIER_PUDO, $settings->carrier_pudo)}checked="checked"{/if} value="1" />
            </div>
            <div class="col-lg-9 col-lg-offset-3">
                <p class="help-block">
                    {l s='DPD Poland Reception Point Pickup' mod='dpdpoland'}
                </p>
            </div>
        </div>

        <p class="alert alert-info">
            {l s='Please note that after module installation carriers are not created.' mod='dpdpoland'}
        </p>

        <div class="panel-footer">
            <button class="btn btn-default pull-right" name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
                <i class="process-icon-save"></i>
                {l s='Save' mod='dpdpoland'}
            </button>
        </div>
    </div>

	<div id="active_zones" class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i>
            {l s='Active zones' mod='dpdpoland'}
        </div>

		<div id="zones_table_container" class="form-group">
			<table class="table" id="zones_table">
				<tbody>
					<tr>
						<td>

						</td>
						<td class="border_bottom text-center">
							<label>{l s='DPD domestic shipment - Standard' mod='dpdpoland'}</label>
						</td>
						<td class="border_bottom text-center">
							<label>{l s='DPD domestic shipment - Standard with COD' mod='dpdpoland'}</label>
						</td>
						<td class="border_bottom text-center">
							<label>{l s='DPD international shipment (DPD Classic)' mod='dpdpoland'}</label>
						</td>
                        <td class="border_bottom text-center">
                            <label>{l s='DPD Poland Reception Point Pickup' mod='dpdpoland'}</label>
                        </td>
					</tr>
					{section name=ii loop=$zones}
						<tr class="fees_all">
							<td class="border_top border_bottom border_bold">
								{$zones[ii].name|escape:'htmlall':'UTF-8'}
							</td>
							<td>
								<input type="checkbox"{if in_array($zones[ii].id_zone, $carrier_zones['standard'])} checked="checked"{/if} name="standard_{$zones[ii].id_zone|intval}" class="form-control domestic_zone" value="1" />
							</td>
							<td>
								<input type="checkbox"{if in_array($zones[ii].id_zone, $carrier_zones['standard_cod'])} checked="checked"{/if} name="standard_cod_{$zones[ii].id_zone|intval}" class="form-control domestic_cod_zone" value="1" />
							</td>
							<td>
								<input type="checkbox"{if in_array($zones[ii].id_zone, $carrier_zones['classic'])} checked="checked"{/if} name="classic_{$zones[ii].id_zone|intval}" class="form-control classic_zone" value="1">
							</td>
                            <td>
                                <input type="checkbox"{if in_array($zones[ii].id_zone, $carrier_zones['pudo'])} checked="checked"{/if} name="pudo_{$zones[ii].id_zone|intval}" class="form-control pudo_zone" value="1">
                            </td>
						</tr>
					{/section}
				</tbody>
			</table>
		</div>

		<p class="alert alert-info">
            {l s='Please define price ranges for each carrier in carrier configuration page or import CSV file with price ranges.' mod='dpdpoland'}
        </p>

		<div class="panel-footer">
            <button class="btn btn-default pull-right" name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
                <i class="process-icon-save"></i>
                {l s='Save' mod='dpdpoland'}
            </button>
        </div>
	</div>

    <div id="price_calculation" class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i>
            {l s='Price calculation' mod='dpdpoland'}
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="price_calculation_csv">
                {l s='Shipping price calculation method:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <p class="radio">
                    <label for="price_calculation_csv">
                        <input id="price_calculation_csv" type="radio" name="{DpdPolandConfiguration::PRICE_CALCULATION_TYPE|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::PRICE_CALCULATION_TYPE, $settings->price_calculation_type) == DpdPolandConfiguration::PRICE_CALCULATION_CSV}checked="checked"{/if} value="{DpdPolandConfiguration::PRICE_CALCULATION_CSV}" />
                        {l s='CSV rules' mod='dpdpoland'}
                    </label>
                </p>
                <p class="radio">
                    <label for="price_calculation_prestashop">
                        <input id="price_calculation_prestashop" type="radio" name="{DpdPolandConfiguration::PRICE_CALCULATION_TYPE|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::PRICE_CALCULATION_TYPE, $settings->price_calculation_type) == DpdPolandConfiguration::PRICE_CALCULATION_PRESTASHOP}checked="checked"{/if} value="{DpdPolandConfiguration::PRICE_CALCULATION_PRESTASHOP}" />
                        {l s='PrestaShop shipping locations rules' mod='dpdpoland'}
                    </label>
                </p>
            </div>
        </div>

        <div class="panel-footer">
            <button class="btn btn-default pull-right" name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
                <i class="process-icon-save"></i>
                {l s='Save' mod='dpdpoland'}
            </button>
        </div>
    </div>

    <div id="weight_measurement" class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i>
            {l s='Weight measurement units conversation' mod='dpdpoland'}
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">
                {l s='System default weight units:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                {Configuration::get('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'}
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3">
                {l s='DPD weight units:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                {$smarty.const._DPDPOLAND_DEFAULT_WEIGHT_UNIT_|escape:'htmlall':'UTF-8'}
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3" for="weight_conversion_input">
                {l s='Conversation rate:' mod='dpdpoland'}
            </label>
            <div class="col-lg-7">
                <input id="weight_conversion_input" type="text" name="{DpdPolandConfiguration::WEIGHT_CONVERSATION_RATE|escape:'htmlall':'UTF-8'}" value="{DpdPoland::getInputValue(DpdPolandConfiguration::WEIGHT_CONVERSATION_RATE, $settings->weight_conversation_rate)|escape:'htmlall':'UTF-8'}" />
            </div>
            <div class="col-lg-2">
                1 {Configuration::get('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'} = <span id="dpd_weight_unit">{DpdPoland::getInputValue(DpdPolandConfiguration::WEIGHT_CONVERSATION_RATE, $settings->weight_conversation_rate|escape:'htmlall':'UTF-8')}</span> {$smarty.const._DPDPOLAND_DEFAULT_WEIGHT_UNIT_|escape:'htmlall':'UTF-8'}
            </div>
            <div class="col-lg-9 col-lg-offset-3">
                <p class="help-block">
                    {l s='Conversation rate from system to DPD weight units. If your system uses the same units as DPD please fill 1.' mod='dpdpoland'}
                </p>
            </div>
        </div>

        <div class="panel-footer">
            <button class="btn btn-default pull-right" name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
                <i class="process-icon-save"></i>
                {l s='Save' mod='dpdpoland'}
            </button>
        </div>
    </div>

    <div id="dimension_measurement" class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i>
            {l s='Dimension measurement units conversation' mod='dpdpoland'}
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">
                {l s='System default dimension units:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                {Configuration::get('PS_DIMENSION_UNIT')|escape:'htmlall':'UTF-8'}
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3">
                {l s='DPD dimension units:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                {$smarty.const._DPDPOLAND_DEFAULT_DIMENSION_UNIT_|escape:'htmlall':'UTF-8'}
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3" for="weight_conversion_input">
                {l s='Conversation rate:' mod='dpdpoland'}
            </label>
            <div class="col-lg-7">
                <input id="weight_conversion_input" type="text" name="{DpdPolandConfiguration::DIMENSION_CONVERSATION_RATE|escape:'htmlall':'UTF-8'}" value="{DpdPoland::getInputValue(DpdPolandConfiguration::DIMENSION_CONVERSATION_RATE, $settings->dimension_conversation_rate)|escape:'htmlall':'UTF-8'}" />
            </div>
            <div class="col-lg-2">
                1 {Configuration::get('PS_DIMENSION_UNIT')|escape:'htmlall':'UTF-8'} = <span id="dpd_weight_unit">{DpdPoland::getInputValue(DpdPolandConfiguration::DIMENSION_CONVERSATION_RATE, $settings->dimension_conversation_rate)|escape:'htmlall':'UTF-8'}</span> {$smarty.const._DPDPOLAND_DEFAULT_DIMENSION_UNIT_|escape:'htmlall':'UTF-8'}
            </div>
            <div class="col-lg-9 col-lg-offset-3">
                <p class="help-block">
                    {l s='Conversation rate from system to DPD dimension units. If your system uses the same units as DPD please fill 1.' mod='dpdpoland'}
                </p>
            </div>
        </div>

        <div class="panel-footer">
            <button class="btn btn-default pull-right" name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
                <i class="process-icon-save"></i>
                {l s='Save' mod='dpdpoland'}
            </button>
        </div>
    </div>

    <div id="customer_data" class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i>
            {l s='General WS parameters' mod='dpdpoland'}
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3 required">
                {l s='Customer company name:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input type="text" name="{DpdPolandConfiguration::CUSTOMER_COMPANY|escape:'htmlall':'UTF-8'}" value="{DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_COMPANY, $settings->customer_company)|escape:'htmlall':'UTF-8'}" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3 required">
                {l s='Customer name and surname:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input type="text" name="{DpdPolandConfiguration::CUSTOMER_NAME|escape:'htmlall':'UTF-8'}" value="{DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_NAME, $settings->customer_name)|escape:'htmlall':'UTF-8'}" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3 required">
                {l s='Customer tel. No.:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input type="text" name="{DpdPolandConfiguration::CUSTOMER_PHONE|escape:'htmlall':'UTF-8'}" value="{DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_PHONE, $settings->customer_phone)|escape:'htmlall':'UTF-8'}" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3 required">
                {l s='Customer FID:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input type="text" name="{DpdPolandConfiguration::CUSTOMER_FID|escape:'htmlall':'UTF-8'}" value="{DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_FID, $settings->customer_fid)|escape:'htmlall':'UTF-8'}" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3 required">
                {l s='Master FID:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input type="text" name="{DpdPolandConfiguration::MASTER_FID|escape:'htmlall':'UTF-8'}" value="{DpdPoland::getInputValue(DpdPolandConfiguration::MASTER_FID, $settings->master_fid)|escape:'htmlall':'UTF-8'}" />
            </div>
        </div>

        <div class="panel-footer">
            <button class="btn btn-default pull-right" name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
                <i class="process-icon-save"></i>
                {l s='Save' mod='dpdpoland'}
            </button>
        </div>
    </div>

    <div id="ws_url" class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i>
            {l s='Web Services URL' mod='dpdpoland'}
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3 required">
                {l s='Web Services URL:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="ws_url_input" type="text" name="{DpdPolandConfiguration::WS_URL|escape:'htmlall':'UTF-8'}" value="{DpdPoland::getInputValue(DpdPolandConfiguration::WS_URL, $settings->ws_url)|escape:'htmlall':'UTF-8'}" size="{if isset($ps14)}94{else}150{/if}"/>
            </div>
            <div class="col-lg-9 col-lg-offset-3">
                <p class="help-block">
                    {l s='Standard URL: https://dpdservices.dpd.com.pl/DPDPackageObjServicesService/DPDPackageObjServices?wsdl' mod='dpdpoland'}
                </p>
            </div>
        </div>

        <div class="panel-footer">
            <button class="btn btn-default pull-right" name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
                <i class="process-icon-save"></i>
                {l s='Save' mod='dpdpoland'}
            </button>
        </div>
    </div>

    <div id="additional_settings" class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i>
            {l s='Additional settings' mod='dpdpoland'}
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="declared_value">
                {l s='Declared value:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="declared_value" type="checkbox" name="{DpdPolandConfiguration::DECLARED_VALUE|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::DECLARED_VALUE, $settings->declared_value)}checked="checked"{/if} value="1" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="cud">
                {l s='Return parcel:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="cud" type="checkbox" name="{DpdPolandConfiguration::CUD|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::CUD, $settings->cud)}checked="checked"{/if} value="1" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="rod">
                {l s='Return documents:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="rod" type="checkbox" name="{DpdPolandConfiguration::ROD|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::ROD, $settings->rod)}checked="checked"{/if} value="1" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="additional_ref1_none">
                {l s='Ref1' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <p class="radio">
                    <label for="additional_ref1_none">
                        <input id="additional_ref1_none" type="radio" name="{DpdPolandConfiguration::ADDITIONAL_REF1|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_REF1, $settings->ref1) == DpdPolandConfiguration::ADDITIONAL_TYPE_NONE}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_NONE}" />
                        {l s='None' mod='dpdpoland'}
                    </label>
                </p>
                <p class="radio">
                    <label for="additional_ref1_dynamic">
                        <input id="additional_ref1_dynamic" type="radio" name="{DpdPolandConfiguration::ADDITIONAL_REF1|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_REF1, $settings->ref1) == DpdPolandConfiguration::ADDITIONAL_TYPE_DYNAMIC}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_DYNAMIC}" />
                        <span class="col-lg-3">
                            {l s='Dynamic' mod='dpdpoland'}
                        </span>
                        <span class="col-lg-9 hidable">
                            <select name="{DpdPolandConfiguration::REF1_DYNAMIC|escape:'htmlall':'UTF-8'}">
                                <option value="{DpdPolandConfiguration::DYNAMIC_ORDER_ID|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::REF1_DYNAMIC, $settings->ref1_dynamic) == DpdPolandConfiguration::DYNAMIC_ORDER_ID} selected="selected"{/if}>{l s='Order ID' mod='dpdpoland'}</option>
                                <option value="{DpdPolandConfiguration::DYNAMIC_ORDER_REFERENCE|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::REF1_DYNAMIC, $settings->ref1_dynamic) == DpdPolandConfiguration::DYNAMIC_ORDER_REFERENCE} selected="selected"{/if}>{l s='Order reference' mod='dpdpoland'}</option>
                                <option value="{DpdPolandConfiguration::DYNAMIC_INVOICE_ID|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::REF1_DYNAMIC, $settings->ref1_dynamic) == DpdPolandConfiguration::DYNAMIC_INVOICE_ID} selected="selected"{/if}>{l s='Invoice ID' mod='dpdpoland'}</option>
                                <option value="{DpdPolandConfiguration::DYNAMIC_SHIPPING_ADDRESS|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::REF1_DYNAMIC, $settings->ref1_dynamic) == DpdPolandConfiguration::DYNAMIC_SHIPPING_ADDRESS} selected="selected"{/if}>{l s='Other field in shipping address' mod='dpdpoland'}</option>
                            </select>
                        </span>
                    </label>
                </p>
                <p class="radio">
                    <label for="additional_ref1_static">
                        <input id="additional_ref1_static" type="radio" name="{DpdPolandConfiguration::ADDITIONAL_REF1|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_REF1, $settings->ref1) == DpdPolandConfiguration::ADDITIONAL_TYPE_STATIC}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_STATIC}" />
                        <span class="col-lg-3">
                            {l s='Static' mod='dpdpoland'}
                        </span>
                        <span class="col-lg-9 hidable">
                            <input type="text" name="{DpdPolandConfiguration::REF1_STATIC|escape:'htmlall':'UTF-8'}" value="{DpdPoland::getInputValue(DpdPolandConfiguration::REF1_STATIC, $settings->ref1_static)|escape:'htmlall':'UTF-8'}" />
                        </span>
                    </label>
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="additional_ref2_none">
                {l s='Ref2' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <p class="radio">
                    <label for="additional_ref2_none">
                        <input id="additional_ref2_none" type="radio" name="{DpdPolandConfiguration::ADDITIONAL_REF2|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_REF2, $settings->ref2) == DpdPolandConfiguration::ADDITIONAL_TYPE_NONE}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_NONE}" />
                        {l s='None' mod='dpdpoland'}
                    </label>
                </p>
                <p class="radio">
                    <label for="additional_ref2_dynamic">
                        <input id="additional_ref2_dynamic" type="radio" name="{DpdPolandConfiguration::ADDITIONAL_REF2|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_REF2, $settings->ref2) == DpdPolandConfiguration::ADDITIONAL_TYPE_DYNAMIC}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_DYNAMIC}" />
                        <span class="col-lg-3">
                            {l s='Dynamic' mod='dpdpoland'}
                        </span>
                        <span class="col-lg-9 hidable">
                            <select name="{DpdPolandConfiguration::REF2_DYNAMIC|escape:'htmlall':'UTF-8'}">
                                <option value="{DpdPolandConfiguration::DYNAMIC_ORDER_ID|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::REF2_DYNAMIC, $settings->ref2_dynamic) == DpdPolandConfiguration::DYNAMIC_ORDER_ID} selected="selected"{/if}>{l s='Order ID' mod='dpdpoland'}</option>
                                <option value="{DpdPolandConfiguration::DYNAMIC_ORDER_REFERENCE|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::REF2_DYNAMIC, $settings->ref2_dynamic) == DpdPolandConfiguration::DYNAMIC_ORDER_REFERENCE} selected="selected"{/if}>{l s='Order reference' mod='dpdpoland'}</option>
                                <option value="{DpdPolandConfiguration::DYNAMIC_INVOICE_ID|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::REF2_DYNAMIC, $settings->ref2_dynamic) == DpdPolandConfiguration::DYNAMIC_INVOICE_ID} selected="selected"{/if}>{l s='Invoice ID' mod='dpdpoland'}</option>
                                <option value="{DpdPolandConfiguration::DYNAMIC_SHIPPING_ADDRESS|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::REF2_DYNAMIC, $settings->ref2_dynamic) == DpdPolandConfiguration::DYNAMIC_SHIPPING_ADDRESS} selected="selected"{/if}>{l s='Other field in shipping address' mod='dpdpoland'}</option>
                            </select>
                        </span>
                    </label>
                </p>
                <p class="radio">
                    <label for="additional_ref2_static">
                        <input id="additional_ref2_static" type="radio" name="{DpdPolandConfiguration::ADDITIONAL_REF2|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_REF2, $settings->ref2) == DpdPolandConfiguration::ADDITIONAL_TYPE_STATIC}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_STATIC}" />
                        <span class="col-lg-3">
                            {l s='Static' mod='dpdpoland'}
                        </span>
                        <span class="col-lg-9 hidable">
                            <input type="text" name="{DpdPolandConfiguration::REF2_STATIC|escape:'htmlall':'UTF-8'}" value="{DpdPoland::getInputValue(DpdPolandConfiguration::REF2_STATIC, $settings->ref2_static)|escape:'htmlall':'UTF-8'}" />
                        </span>
                    </label>
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="additional_customer_data_1_none">
                {l s='CustomerData1' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <p class="radio">
                    <label for="additional_customer_data_1_none">
                        <input id="additional_customer_data_1_none" type="radio" name="{DpdPolandConfiguration::ADDITIONAL_CUSTOMER_DATA_1|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_CUSTOMER_DATA_1, $settings->customer_data_1) == DpdPolandConfiguration::ADDITIONAL_TYPE_NONE}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_NONE}" />
                        {l s='None' mod='dpdpoland'}
                    </label>
                </p>
                <p class="radio">
                    <label for="additional_customer_data_1_dynamic">
                        <input id="additional_customer_data_1_dynamic" type="radio" name="{DpdPolandConfiguration::ADDITIONAL_CUSTOMER_DATA_1|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_CUSTOMER_DATA_1, $settings->customer_data_1) == DpdPolandConfiguration::ADDITIONAL_TYPE_DYNAMIC}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_DYNAMIC}" />
                        <span class="col-lg-3">
                            {l s='Dynamic' mod='dpdpoland'}
                        </span>
                        <span class="col-lg-9 hidable">
                            <select name="{DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC|escape:'htmlall':'UTF-8'}">
                                <option value="{DpdPolandConfiguration::DYNAMIC_ORDER_ID|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC, $settings->customer_data_dynamic) == DpdPolandConfiguration::DYNAMIC_ORDER_ID} selected="selected"{/if}>{l s='Order ID' mod='dpdpoland'}</option>
                                <option value="{DpdPolandConfiguration::DYNAMIC_ORDER_REFERENCE|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC, $settings->customer_data_dynamic) == DpdPolandConfiguration::DYNAMIC_ORDER_REFERENCE} selected="selected"{/if}>{l s='Order reference' mod='dpdpoland'}</option>
                                <option value="{DpdPolandConfiguration::DYNAMIC_INVOICE_ID|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC, $settings->customer_data_dynamic) == DpdPolandConfiguration::DYNAMIC_INVOICE_ID} selected="selected"{/if}>{l s='Invoice ID' mod='dpdpoland'}</option>
                                <option value="{DpdPolandConfiguration::DYNAMIC_SHIPPING_ADDRESS|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC, $settings->customer_data_dynamic) == DpdPolandConfiguration::DYNAMIC_SHIPPING_ADDRESS} selected="selected"{/if}>{l s='Other field in shipping address' mod='dpdpoland'}</option>
                            </select>
                        </span>
                    </label>
                </p>
                <p class="radio">
                    <label for="additional_customer_data_1_static">
                        <input id="additional_customer_data_1_static" type="radio" name="{DpdPolandConfiguration::ADDITIONAL_CUSTOMER_DATA_1|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_CUSTOMER_DATA_1, $settings->customer_data_1) == DpdPolandConfiguration::ADDITIONAL_TYPE_STATIC}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_STATIC}" />
                        <span class="col-lg-3">
                            {l s='Static' mod='dpdpoland'}
                        </span>
                        <span class="col-lg-9 hidable">
                            <input type="text" name="{DpdPolandConfiguration::CUSTOMER_DATA_STATIC|escape:'htmlall':'UTF-8'}" value="{DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_STATIC, $settings->customer_data_static)|escape:'htmlall':'UTF-8'}" />
                        </span>
                    </label>
                </p>
            </div>
        </div>

        <div class="panel-footer">
            <button class="btn btn-default pull-right" name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
                <i class="process-icon-save"></i>
                {l s='Save' mod='dpdpoland'}
            </button>
        </div>
    </div>
</form>