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

<div id="opc_payments">
    {if !$opc}
        <script type="text/javascript">
            var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
            var currencyRate = '{$currencyRate|floatval}';
            var currencyFormat = '{$currencyFormat|intval}';
            var currencyBlank = '{$currencyBlank|intval}';
            var txtProduct = "{l s='product' mod='bestkit_opc'}";
            var txtProducts = "{l s='products' mod='bestkit_opc'}";
        </script>
        {capture name=path}{l s='Your payment method' mod='bestkit_opc'}{/capture}
        {include file="$tpl_dir./breadcrumb.tpl"}
    {/if}

    {if !$opc}
        <h1 class="page-heading">{l s='Choose your payment method' mod='bestkit_opc'}</h1>
    {else}
        <h1 class="page-heading"><span class="heading-counter heading-counter-4">4</span>{l s='Payment method' mod='bestkit_opc'}</h1>
    {/if}

    {if $conditions AND $cms_id}
        <p class="checkbox">
            <label for="cgv">
                <input type="checkbox" name="cgv" id="cgv" value="1" {if $checkedTOS}checked="checked"{/if} />
                {l s='I agree to the Terms of Service and will adhere to them unconditionally.' mod='bestkit_opc'}
            </label>
            <a href="{$link_conditions|escape:false}" class="iframe">{l s='(Read Terms of Service)' mod='bestkit_opc'}</a>
        </p>
        <script type="text/javascript">$('a.iframe').fancybox();</script>
    {/if}

    {if !$opc}
        {assign var='current_step' value='payment'}
        {include file="$tpl_dir./order-steps.tpl"}
        {include file="$tpl_dir./errors.tpl"}
    {else}
        <div id="opc_payment_methods">
            <div id="opc_payment_methods-overlay" class="overlay-opc" style="display: none;"></div>
    {/if}

    <div id="HOOK_TOP_PAYMENT">{$HOOK_TOP_PAYMENT|escape:false}</div>

    {if $HOOK_PAYMENT}
        {if !$opc}
            <h3 class="page-subheading">{l s='Please select your preferred payment method to pay the amount of' mod='bestkit_opc'}&nbsp;<span class="price-opc">{convertPrice price=$total_price}</span> {if $taxes_enabled}{l s='(tax incl.)' mod='bestkit_opc'}{/if}</h3>
        {/if}
        {if $opc}
            <div id="opc_payment_methods-content">
        {/if}
            <div id="HOOK_PAYMENT">{$HOOK_PAYMENT|escape:false}</div>
        {if $opc}
            </div>
        {/if}
    {else}
        <p class="alert alert-warning">{l s='No payment modules have been installed.' mod='bestkit_opc'}</p>
    {/if}

    {if !$opc}
        <button type="button" class="btn btn-default button button-small exclusive" onclick="window.location='{$link->getPageLink('order.php', true)|escape:false}?step=2';" title="{l s='Previous' mod='bestkit_opc'}">
            <span>
                <i class="icon-chevron-sign-left left"></i>
                {l s='Previous' mod='bestkit_opc'}
            </span>
        </button>
    {else}
			{if $opcModuleObj->getConfig('continue_shopping')}
            <button type="button" class="btn btn-default button button-small exclusive" onclick="window.location='{if (isset($smarty.server.HTTP_REFERER) && strstr($smarty.server.HTTP_REFERER, $link->getPageLink('order.php'))) || !isset($smarty.server.HTTP_REFERER)}{$link->getPageLink('index.php')}{else}{$smarty.server.HTTP_REFERER|escape:'htmlall':'UTF-8'|secureReferrer}{/if}';" title="{l s='Continue shopping' mod='bestkit_opc'}">
                <span>
                    <i class="icon-chevron-sign-left left"></i>
                    {l s='Continue shopping' mod='bestkit_opc'}
                </span>
            </button>
			{/if}
        </div>
    {/if}
</div>
