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

<div class="message-boxbox box-opc">
    {*<h2 class="page-subheading">{l s='Leave a message' mod='opc'}</h2>*}
    <p>{l s='If you would like to add a comment about your order, please write it below.' mod='bestkit_opc'}</p>
    <div class="textarea">
        <textarea cols="120" rows="3" name="old_message" id="old_message" class="form-control">{if isset($oldMessage)}{$oldMessage}{/if}</textarea>
    </div>
</div>
