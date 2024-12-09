{*
* 2016 Sensbit
*
* MODUŁ ZOSTAŁ UDOSTĘPNIONY NA PODSTAWIE LICENCJI NA JEDNO STANOWISKO/DOMENĘ
* NIE MASZ PRAWA DO JEGO KOPIOWANIA, EDYTOWANIA I SPRZEDAWANIA
* W PRZYPADKU PYTAŃ LUB BŁĘDÓW SKONTAKTUJ SIĘ Z AUTOREM
*
* ENGLISH:
* MODULE IS LICENCED FOR ONE-SITE / DOMAIM
* YOU ARE NOT ALLOWED TO COPY, EDIT OR SALE
* IN CASE OF ANY QUESTIONS CONTACT AUTHOR
*
* ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** *
*
* EN: ODWIEDŹ NASZ SKLEP PO WIĘCEJ PROFESJONALNYCH MODUŁÓW PRESTASHOP
* PL: VISIT OUR ONLINE SHOP FOR MORE PROFESSIONAL PRESTASHOP MODULES
* HTTPS://sensbit.pl
*
* ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** *
*
* @author    Tomasz Dacka (kontakt@sensbit.pl)
* @copyright 2016 sensbit.pl
* @license   One-site license (jednostanowiskowa, bez możliwości kopiowania i udostępniania innym)
*}

{extends file="helpers/form/form.tpl"}
{block name="defaultForm"}
	<style>
		.sensbitdpd-admin-tabs{
			margin-bottom:20px;
		}
		.sensbitdpd-admin-tabs:after {
			content:"";
			display:table;
			clear:both;
		}
		.sensbitdpd-admin-tabs .admin-tab{
			float:left;
			display:block;
		}
	</style>
	<div class="sensbitdpd sensbitdpd-admin-tabs">
		{foreach $admin_tabs as $tab}
			{if $tab.parent && strpos($tab.class, 'Settings') === false}
				<div class="admin-tab" style="width:{100/(count($admin_tabs)-2)}%">
					<a class="btn btn-default btn-block" href="{SensbitDpdTools::getLinkToAdmin($tab.class)}">{$tab.name[Context::getContext()->language->iso_code]}</a>
				</div>
			{/if}
		{/foreach}
	</div>
    <div {if !$is_bootstrap}style="float:left; width:200px;"{else}style="width:200px;position:absolute;"{/if}>
		<div style="text-align:center">
			<a href='https://sensbit.pl/pl/?utm_source=module_sensbitdpd&utm_medium=logosensbit&utm_content=sensbitdpd&utm_campaign=Odwiedziny%20z%20modu%C5%82%C3%B3w'>
				<img style="margin:20px auto" src='//sensbit.pl/logo_157x44.png' class='img-responsive'/>
			</a>
			<a href='https://sensbit.pl/moduly-i-modyfikacje-prestashop/integracje-z-przewoznikami-prestashop/42-integracja-z-dpd-dla-prestashop-15-16-i-17'>
				<img style="margin:20px auto" src='https://sensbit.pl/157-medium_default/integracja-z-dpd-dla-prestashop-15-16-i-17.jpg' class='img-responsive'/>
			</a>
			<strong>{$module->displayName}</strong>
			<br/>v. {$module->version}
			<br/>PHP v. {phpversion()}
			<br/>PrestaShop v. {$smarty.const._PS_VERSION_}
		</div>
        <ul class="sensbitdpd-tabs {if $is_bootstrap}bootstrap{/if}">
            {foreach $fields as $key => $field name=fields}
                <li>
                    <a href="#fieldset_{$smarty.foreach.fields.index|escape:'htmlall':'UTF-8'}" class="{if $smarty.foreach.fields.first}active{/if}">{$field.form.legend.title|escape:'htmlall':'UTF-8'}</a>
                </li>
            {/foreach}
        </ul>
		<a target="_blank" href="https://sensbit.pl/pl/moje-moduly?utm_source=module_{$module->name}&utm_medium=version&utm_content={$module->name}&utm_campaign=Odwiedziny%20z%20modu%C5%82%C3%B3w"><img src="https://sensbit.pl/version?m={$module->name}&v={$module->version}&r={time()}" class="img-responsive"/></a>
    </div>
    <div style="margin-left:210px;min-height:850px">
        {$smarty.block.parent}
    </div>
{/block}
{block name="script"}
    {$smarty.block.parent}
	{literal}
		$(document).ready(function () {
		$(".sensbitdpd-tabs a").on('click', function (e) {
		e.preventDefault();
		data = $(this).attr('href').split('_');
		if ($(this).parents('.sensbitdpd-tabs').hasClass('bootstrap'))
		{
		target = '.panel';
		removeBR = false;
		} else {
		target = 'fieldset';
		removeBR = true;
		}
		if (removeBR === true)
		$(".sensbitdpd_config_form " + target).prevAll("br").remove();
		$(".sensbitdpd_config_form " + target).hide();
		$(".sensbitdpd_config_form " + target).eq(data[1]).fadeIn();
		$(".sensbitdpd-tabs a").removeClass('active');
		$(this).addClass('active');
		});
		$(".sensbitdpd-tabs a").first().trigger('click');
		});
	{/literal}
{/block}
{block name="field"}
    {if $input.type == 'buttons'}
		<div class="margin-form">
			{foreach $input.list AS $button}
				{if isset($button.link)}
					<a href="{$button.link|escape:'htmlall':'UTF-8'}" alt="{$button.title|escape:'htmlall':'UTF-8'}" target="_blank" class="{if isset($button.class)}{$button.class|escape:'htmlall':'UTF-8'}{/if}">{$button.title|escape:'htmlall':'UTF-8'}</a>
				{elseif (isset($button.show) && $button.show == true) || !isset($button.show)}
					<input type="submit"
						   id="{if isset($button.id)}{$button.id|escape:'htmlall':'UTF-8'}{else}{/if}"
						   value="{$button.title|escape:'htmlall':'UTF-8'}"
						   name="{if isset($button.name)}{$button.name|escape:'htmlall':'UTF-8'}{else}{$submit_action|escape:'htmlall':'UTF-8'}{/if}{if isset($button.stay) && $button.stay}AndStay{/if}"
						   class="{if isset($button.class)}{$button.class|escape:'htmlall':'UTF-8'}{/if}"
						   onclick='{if isset($button.onclick)}{$button.onclick|escape:'htmlall':'UTF-8'}{/if}'/> 
				{/if}
			{/foreach}
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}