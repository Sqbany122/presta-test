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

{if !empty($display_header)}
	{include file=Module::getInstanceByName('bestkit_opc')->getTemplatePathTheme('header.tpl') HOOK_HEADER=$HOOK_HEADER} {*'./header.tpl'*}
{/if}
{if !empty($template)}
	{$template|escape:false}
{/if}
{if !empty($display_footer)}
	{include file=Module::getInstanceByName('bestkit_opc')->getTemplatePathTheme('footer.tpl')} {*'./footer.tpl'*}
{/if}
{if !empty($live_edit)}
	{$live_edit|escape:false}
{/if}