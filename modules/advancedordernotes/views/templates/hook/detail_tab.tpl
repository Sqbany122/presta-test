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
<br/>
<div class="">
<fieldset>
<div class="panel">

    <div class="panel-heading">
        <img style="height:20px; width:20px;" src="../modules/advancedordernotes/AdvancedOrderNotes.gif" alt="{l s='Order notes' mod='advancedordernotes'}"> {l s='Order notes' mod='advancedordernotes'}
    </div>

    <input type="hidden" id="hidden_id_order" value="{$id_order|escape:'htmlall':'UTF-8'}" />
    <input type="hidden" id="hidden_id_employee" value="{$id_employee|escape:'htmlall':'UTF-8'}" />
    <input type="hidden" id="aon_token" value="{$aon_token|escape:'htmlall':'UTF-8'}" />

    <table width="100%" cellspacing="0" cellpadding="0" class="table">
        <colgroup>
            <col width="5%">
                <col width="">
                    <col width="20%">
                        <col width="20%">

        </colgroup>
        <thead>
            <tr>
                <th>{l s='#' mod='advancedordernotes'}</th>
                <th>{l s='Order note' mod='advancedordernotes'}</th>
                <th>{l s='Status' mod='advancedordernotes'}</th>
                <th>{l s='Employee' mod='advancedordernotes'}</th>
                <th>{l s='Date' mod='advancedordernotes'}</th>
            </tr>
        </thead>

        <tbody id="order_notes">
            {if $size != 0} {assign var=number value=1} {foreach $notes as $note} {if $number == $count_size}
            <tr style="background-color:#73b900;">
                <td style="color:#fff; background-color:#73b900;">{$note.nr|escape:'htmlall':'UTF-8'}</td>
                <td style="color:#fff; background-color:#73b900;">{$note.note|escape:'htmlall':'UTF-8'}</td>
                <td style="color:#fff; background-color:#73b900;">{if $note.note_status}<span style="padding:0px 10px; border-radius:5px;  background-color:{$note.note_background|escape:'htmlall':'UTF-8'}; color:{$note.note_color|escape:'htmlall':'UTF-8'}">{$note.note_status|escape:'htmlall':'UTF-8'}</span>{else}-{/if}</td>
                <td style="color:#fff; background-color:#73b900;">{$note.employee|escape:'htmlall':'UTF-8'}</td>
                <td style="color:#fff; background-color:#73b900;">{$note.date|escape:'htmlall':'UTF-8'}</td>
            </tr>
            {else}
            <tr>
                <td>{$note.nr|escape:'htmlall':'UTF-8'}</td>
                <td>{$note.note|escape:'htmlall':'UTF-8'}</td>
                <td>{if $note.note_status}<span style="padding:0px 10px; border-radius:5px;  background-color:{$note.note_background|escape:'htmlall':'UTF-8'}; color:{$note.note_color|escape:'htmlall':'UTF-8'}">{$note.note_status|escape:'htmlall':'UTF-8'}</span>{else}-{/if}</td>
                <td>{$note.employee|escape:'htmlall':'UTF-8'}</td>
                <td>{$note.date|escape:'htmlall':'UTF-8'}</td>
            </tr>
            {/if} {$number = $number +1} {/foreach} {else}
            <tr class="noorders">
                <td colspan="4">{l s='There are no notes for this order' mod='advancedordernotes'}</td>
            </tr>
            {/if}
        </tbody>
    </table>

    <br/>
    <div>
        <textarea class="col-lg-12 col-md-12"  id="ordernotes_comment" placeholder="{l s='Enter your note here...' mod='advancedordernotes'}"></textarea>
        {if $note_status == 1}
       
                <select id="note_statuses" class="col-lg-2 col-md-2" style="float:left;margin-top:15px;">
                    <option value="">{l s='No status' mod='advancedordernotes'}</option>
                    {foreach from=$note_statuses item=s}
                        <option value="{$s.name|escape:'htmlall':'UTF-8'}">{$s.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
        {else}
        <input type="hidden" name="" id="note_statuses" value=""  />            
        {/if}
        <input class="button btn btn-primary pull-right" type="submit" name="addNote" id="addOrderNote" value="{l s='Add note' mod='advancedordernotes'}" style="float:right; margin-top:15px; " />
        <div style="clear:both"></div>
    </div>

    <div class="clear" style="margin-bottom: 10px;"></div>
</div>
</fieldset>
<br/>
</div>