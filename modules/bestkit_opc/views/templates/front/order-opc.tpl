{*
 * 2007-2014 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *         DISCLAIMER   *
 * *************************************** */
/* Do not edit or add to this file if you wish to upgrade Prestashop to newer
* versions in the future.
* ****************************************************
*
*  @author     BEST-KIT.COM (contact@best-kit.com)
*  @copyright  http://best-kit.com
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $PS_CATALOG_MODE}
    {capture name=path}{l s='Your shopping cart' mod='bestkit_opc'}{/capture}
    {include file="$tpl_dir./breadcrumb.tpl"}
    <h1 class="page-heading"> id="cart_title">{l s='Your shopping cart' mod='bestkit_opc'}</h1>
    <p class="alert alert-warning">{l s='This store has not accepted your new order.' mod='bestkit_opc'}</p>
{else}
	{addJsDef imgDir=$img_dir}
	{addJsDef authenticationUrl=$link->getPageLink("authentication", true)|escape:'quotes':'UTF-8'}
	{addJsDef orderOpcUrl=$link->getPageLink("order-opc", true)|escape:'quotes':'UTF-8'}
	{addJsDef historyUrl=$link->getPageLink("history", true)|escape:'quotes':'UTF-8'}
	{addJsDef guestTrackingUrl=$link->getPageLink("guest-tracking", true)|escape:'quotes':'UTF-8'}
	{addJsDef addressUrl=$link->getPageLink("address", true, NULL)|escape:'quotes':'UTF-8'}
	{addJsDef orderProcess='order-opc'}
	{addJsDef guestCheckoutEnabled=$PS_GUEST_CHECKOUT_ENABLED|intval}
	{addJsDef currencySign=$currencySign|html_entity_decode:2:"UTF-8"}
	{addJsDef currencyRate=$currencyRate|floatval}
	{addJsDef currencyFormat=$currencyFormat|intval}
	{addJsDef currencyBlank=$currencyBlank|intval}
	{addJsDef displayPrice=$priceDisplay}
	{addJsDef taxEnabled=$use_taxes}
	{addJsDef conditionEnabled=$conditions|intval}
	{addJsDef vat_management=$vat_management|intval}
	{addJsDef errorCarrier=$errorCarrier|@addcslashes:'\''}
	{addJsDef errorTOS=$errorTOS|@addcslashes:'\''}
	{addJsDef checkedCarrier=$checked|intval}
	{addJsDef addresses=array()}
	{addJsDef isVirtualCart=$isVirtualCart|intval}
	{addJsDef isPaymentStep=$isPaymentStep|intval}
	{addJsDefL name=txtWithTax|escape:false}{l s='(tax incl.)' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDefL name=txtWithoutTax|escape:false}{l s='(tax excl.)' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDefL name=txtHasBeenSelected|escape:false}{l s='has been selected' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDefL name=txtNoCarrierIsSelected|escape:false}{l s='No carrier has been selected' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDefL name=txtNoCarrierIsNeeded|escape:false}{l s='No carrier is needed for this order' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDefL name=txtConditionsIsNotNeeded|escape:false}{l s='You do not need to accept the Terms of Service for this order.' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDefL name=txtTOSIsAccepted|escape:false}{l s='The service terms have been accepted' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDefL name=txtTOSIsNotAccepted|escape:false}{l s='The service terms have not been accepted' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDefL name=txtThereis|escape:false}{l s='There is' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDefL name=txtErrors|escape:false}{l s='Error(s)' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDefL name=txtDeliveryAddress|escape:false}{l s='Delivery address' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDefL name=txtInvoiceAddress|escape:false}{l s='Invoice address' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDefL name=txtModifyMyAddress|escape:false}{l s='Modify my address' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDefL name=txtInstantCheckout|escape:false}{l s='Instant checkout' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDefL name=txtSelectAnAddressFirst|escape:false}{l s='Please start by selecting an address.' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDefL name=txtFree|escape:false}{l s='Free' js=1 mod='bestkit_opc'}{/addJsDefL}
	{addJsDef opc=$opc|boolval}

	{if !$opcModuleObj->getConfig('show_breadcrumbs')}
	{literal}
	<style>
		.breadcrumb {display: none;}
	</style>
	{/literal}
	{/if}
	
    {if $productNumber}
        <div class="row" id="opc_wrapper">
            {include file=$style_tpl}
        </div>
    {else}
        {capture name=path}{l s='Your shopping cart' mod='bestkit_opc'}{/capture}
        <h1 class="page-heading">{l s='Your shopping cart' mod='bestkit_opc'}</h1>
        <p class="alert alert-warning">{l s='Your shopping cart is empty.' mod='bestkit_opc'}</p>
    {/if}
{/if}
