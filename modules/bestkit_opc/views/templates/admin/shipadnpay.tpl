{*
 * 2007-2013 PrestaShop
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
{*$shipadnpay|print_r*}
{*$carriers|print_r*}

{if count($carriers)}
    <div id="accordion">
        {foreach $carriers as $carrier}
            <h3>{$carrier.name|escape}</h3>
            <div>
                {foreach $shipadnpay as $payment_module}
                    {if $payment_module->id}
                        <p>
                            <input type="checkbox" name="carrier[{$carrier.id_reference|intval}][{$payment_module->id}]" {if in_array($carrier.id_reference, $payment_module->carrier)}checked="checked"{/if} id="carrier_{$carrier.id_reference|intval}_{$payment_module->id}">
                            <label for="carrier_{$carrier.id_reference|intval}_{$payment_module->id}">{$payment_module->displayName|escape}</label}
                        </p>
                    {/if}
                {/foreach}
            </div>
            <br>
        {/foreach}
    </div>
{else}
    <div class="alert alert-warning">
        {l s='You doesn\'t have any carrier' mod='bestkit_opc'}
    </div>
{/if}