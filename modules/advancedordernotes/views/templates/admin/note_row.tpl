{*
* 2007-2017 PrestaShop
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
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<tr>
	<td>{$notes_count|escape:'htmlall':'UTF-8'}</td>
	<td>{$note|escape:'htmlall':'UTF-8'}</td>
	<td>{if $note_status}<span style="padding:0px 10px; border-radius:5px;  background-color:{$note_background|escape:'htmlall':'UTF-8'}; color:{$note_color|escape:'htmlall':'UTF-8'}">{$note_status|escape:'htmlall':'UTF-8'}</span>{else}-{/if}</td>
	<td>{$employee_name|escape:'htmlall':'UTF-8'}</td>
	<td>{$date|escape:'htmlall':'UTF-8'}</td>
</tr>