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

<fieldset class="col-lg-7">
<div class="panel">
    <div class="panel-heading">
        <img style="height:20px; width:20px;" src="../modules/advancedordernotes/logo.png" alt="{l s='Advanced Order Notes' mod='advancedordernotes'}"> {l s='Advanced Order Notes' mod='advancedordernotes'}
    </div>
    <hr/>
    {if $aon_notes}
    <table width="100%" cellspacing="0" cellpadding="0" class="table" >
        <colgroup>
                <col width="60%">
                <col width="10%">
                <col width="10%">
                <col width="20%">

        </colgroup>
        <thead>
            <tr>
                <th style="text-align:left">{l s='Note' mod='advancedordernotes'}</th>
                <th style="text-align:left">{l s='status' mod='advancedordernotes'}</th>
                <th style="text-align:left">{l s='Employee' mod='advancedordernotes'}</th>
                <th style="text-align:left">{l s='Date' mod='advancedordernotes'}</th>
            </tr>
        </thead>
	        <tbody id="act_body">
	        	{assign var="counter" value=1}
	        	{foreach from=$aon_notes item=rp}

	           	<tr {if $counter == $aon_notes|@count} class="last_note" {/if}>
	           		<td style="text-align:left">{$rp.message|escape:'htmlall':'UTF-8'}</td>
                    <td style="text-align:left">{if $rp.note_status}<span style="padding:0px 10px; border-radius:5px;  background-color:{$rp.note_background|escape:'htmlall':'UTF-8'}; color:{$rp.note_color|escape:'htmlall':'UTF-8'}">{$rp.note_status|escape:'htmlall':'UTF-8'}</span>{else}-{/if}</td>
	           		<td style="text-align:left">{$rp.employee_name|escape:'htmlall':'UTF-8'}</td>
	           		<td style="text-align:left">{$rp.date_add|escape:'htmlall':'UTF-8'}</td>
	           	</tr>
	           	{assign var="counter" value=$counter+1}
	           	 {/foreach}
	        </tbody>
       
    </table>
    {/if}
    <div class="clear" style="margin-bottom: 10px;"></div>
</div>
</fieldset>
