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
<div id="bigcart_opc">
    <div class="col-xs-12 col-sm-4">
        {if $isLogged AND !$isGuest}
        {*include file="$tpl_dir./order-address.tpl"*}
            {include file=Module::getInstanceByName('bestkit_opc')->getTemplatePath('order-address.tpl')}
        {else}
            <!-- Create account / Guest account / Login block -->
        {*include file="$tpl_dir./order-opc-new-account.tpl"*}
            {include file=Module::getInstanceByName('bestkit_opc')->getTemplatePath('order-opc-new-account.tpl')} {*"$tpl_dir../../modules/bestkit_opc/tpl/order-opc-new-account.tpl"*}
            <!-- END Create account / Guest account / Login block -->
        {/if}
    </div>
    <div class="col-xs-12 col-sm-8">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <!-- Carrier -->
                {include file=Module::getInstanceByName('bestkit_opc')->getTemplatePath('order-carrier.tpl')}
                <!-- END Carrier -->
            </div>
            <div class="col-xs-12 col-sm-6">
                <!-- Payment -->
                {include file=Module::getInstanceByName('bestkit_opc')->getTemplatePath('order-payment.tpl')}
                <!-- END Payment -->
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <!-- Shopping Cart -->
                {include file=Module::getInstanceByName('bestkit_opc')->getTemplatePath('shopping-cart.tpl')}
                <!-- End Shopping Cart -->
            </div>
        </div>
    </div>
</div>