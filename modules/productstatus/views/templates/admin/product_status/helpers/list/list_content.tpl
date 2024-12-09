{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
 *  @author Andreika
 *  @copyright  Andreika
 *  @version  2.8.5
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<tbody>
{if count($list)}
{foreach $list AS $index => $tr}
	{**<pre>{$tr|print_r}</pre>**}
	{$total = $tr.total_price + $total}
	{$weight = ($tr.product_weight * $tr.product_quantity) + $weight}
	{$count = $count + $tr.product_quantity}
    <tr
	{if $position_identifier}id="tr_{$id_category|escape:'htmlall':'UTF-8'}_{$tr.$identifier|escape:'htmlall':'UTF-8'}_{if isset($tr.position['position'])}{$tr.position['position']|escape:'htmlall':'UTF-8'}{else}0{/if}"{/if}
	class="{if $index is odd}alt_row{/if} {if $row_hover}row_hover{/if}"
	{if isset($tr.color) && $color_on_bg}style="background-color: {$tr.color|escape:'htmlall':'UTF-8'}"{/if}
	>
        {if (!$ps16) OR ($bulk_actions && $has_bulk_actions)}
		<td class="center">
            {if $bulk_actions && $has_bulk_actions}
				{if isset($list_skip_actions.delete)}
					{if !in_array($tr.$identifier, $list_skip_actions.delete)}
						<input type="checkbox" name="{$table|escape:'htmlall':'UTF-8'}Box[]" value="{$tr.$identifier|escape:'htmlall':'UTF-8'}" class="noborder" />
					{/if}
				{else}
					<input type="checkbox" name="{$table|escape:'htmlall':'UTF-8'}Box[]" value="{$tr.$identifier|escape:'htmlall':'UTF-8'}" class="noborder" />
				{/if}
            {/if}
		</td>
        {/if}
		{foreach $fields_display AS $key => $params}
			{block name="open_td"}
				<td
					{if isset($params.position)}
						id="td_{if !empty($id_category)}{$id_category|escape:'htmlall':'UTF-8'}{else}0{/if}_{$tr.$identifier|escape:'htmlall':'UTF-8'}"
					{/if}
					class="{if !$no_link}pointer{/if}
					{if isset($params.position) && $order_by == 'position'  && $order_way != 'DESC'} dragHandle{/if}
					{if isset($params.class)} {$params.class|escape:'htmlall':'UTF-8'}{/if}
					{if isset($params.align)} {$params.align|escape:'htmlall':'UTF-8'}{/if}"
					{if (!isset($params.position) && !$no_link && !isset($params.remove_onclick))}
						onclick="document.location = '{$current_index|escape:'htmlall':'UTF-8'}&{$identifier|escape:'htmlall':'UTF-8'}={$tr.$identifier|escape:'htmlall':'UTF-8'}{if $view}&view{else}&update{/if}{$table|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}'">
					{else}
					>
				{/if}
			{/block}
			{block name="td_content"}
				{if isset($params.prefix)}{$params.prefix|escape:'quotes':'UTF-8'}{/if}
				{if isset($params.color) && isset($tr[$params.color])}
					<span class="color_field" style="background-color:{$tr[$params.color]|escape:'htmlall':'UTF-8'};color:{if Tools::getBrightness($tr[$params.color]) < 128}white{else}#383838{/if}">
				{/if}
				{if isset($tr.$key)}
					{if isset($params.active)}
						{$tr.$key|escape:'htmlall':'UTF-8'}
					{elseif isset($params.activeVisu)}
						<img src="../img/admin/{if $tr.$key}enabled.gif{else}disabled.gif{/if}"
						alt="{if $tr.$key}{l s='Enabled' mod='productstatus'}{else}{l s='Disabled' mod='productstatus'}{/if}" title="{if $tr.$key}{l s='Enabled' mod='productstatus'}{else}{l s='Disabled' mod='productstatus'}{/if}" />
					{elseif isset($params.position)}
						{if $order_by == 'position' && $order_way != 'DESC'}
							<a href="{$tr.$key.position_url_down|escape:'htmlall':'UTF-8'}" {if !($tr.$key.position != $positions[count($positions) - 1])}style="display: none;"{/if}>
								<img src="../img/admin/{if $order_way == 'ASC'}down{else}up{/if}.gif" alt="{l s='Down' mod='productstatus'}" title="{l s='Down' mod='productstatus'}" />
							</a>

							<a href="{$tr.$key.position_url_up|escape:'htmlall':'UTF-8'}" {if !($tr.$key.position != $positions.0)}style="display: none;"{/if}>
								<img src="../img/admin/{if $order_way == 'ASC'}up{else}down{/if}.gif" alt="{l s='Up' mod='productstatus'}" title="{l s='Up' mod='productstatus'}" />
							</a>
						{else}
							{$tr.$key.position + 1|escape:'htmlall':'UTF-8'}
						{/if}
					{elseif isset($params.image)}
						{$tr.$key|escape:'htmlall':'UTF-8'}
					{elseif isset($params.icon)}
						{if is_array($tr[$key])}
							<img src="../img/admin/{$tr[$key]['src']|escape:'htmlall':'UTF-8'}" alt="{$tr[$key]['alt']|escape:'htmlall':'UTF-8'}" title="{$tr[$key]['alt']|escape:'htmlall'}" />
						{/if}
					{elseif isset($params.price)}
						{displayPrice price=$tr.$key}
					{elseif isset($params.float)}
						{$tr.$key|escape:'htmlall':'UTF-8'}
                    {elseif $key == 'status_name'}
                        {*{$tr.$key|escape:'htmlall':'UTF-8'}*}

                        {*<pre>
                        {$tr.state_color|print_r}
                        </pre>*}

                        <select class="status_selector" data-id_detail="{$tr.id_order_detail|escape:'htmlall':'UTF-8'}" style="background-color:{$tr.state_color|escape:'htmlall'};">
                        {foreach $statuses as $item}
                            <option value="{$item.id_order_state|escape:'htmlall':'UTF-8'}" {if $item.id_order_state == $tr.id_product_state}selected="selected"{/if} data-bgcolor="{$item.color|escape:'htmlall':'UTF-8'}">{$item.name|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                        </select>
					{elseif isset($params.type) && $params.type == 'date'}
						{if isset($params.type2) && $params.type2 == 'editable' && isset($tr.id)}
                            <input type="text" name="{$key|escape:'htmlall':'UTF-8'}_{$tr.id|escape:'htmlall':'UTF-8'}" value="{$tr.$key|escape:'htmlall':'UTF-8'}" class="{$key|escape:'htmlall':'UTF-8'} datepicker" onchange="sendAjax('{$key|escape:'htmlall':'UTF-8'}', '{$tr.id|escape:'htmlall':'UTF-8'}', this.value)"
                                   {if !empty($tr.$key)}style="background-color: lightgreen; color:darkgreen; font-weight: bold"{/if}/>
                            <input type="hidden" name="id_order_detail" value="{$tr.id|escape:'htmlall':'UTF-8'}" class="{$key|escape:'htmlall':'UTF-8'}" />
						{else}
                            {$tr.$key|escape:'htmlall':'UTF-8'}
						{/if}

					{elseif isset($params.type) && $params.type == 'editabletext'}
						{if isset($params.type2) && $params.type2 == 'editable' && isset($tr.id)}
                            <input type="text" name="{$key|escape:'htmlall':'UTF-8'}_{$tr.id|escape:'htmlall':'UTF-8'}" value="{$tr.$key|escape:'htmlall':'UTF-8'}" class="{$key|escape:'htmlall':'UTF-8'}" onchange="sendAjax('{$key|escape:'htmlall':'UTF-8'}', '{$tr.id|escape:'htmlall':'UTF-8'}', this.value)"/>
                            <input type="hidden" name="id_order_detail" value="{$tr.id|escape:'htmlall':'UTF-8'}" class="{$key|escape:'htmlall':'UTF-8'}" />
                        {else}
                            {$tr.$key|escape:'htmlall':'UTF-8'}
                        {/if}

					{elseif isset($params.type) && $params.type == 'datetime'}
						{$tr.$key|escape:'htmlall':'UTF-8'}
					{elseif isset($params.type) && $params.type == 'decimal'}
						{$tr.$key|string_format:"%.2f"|escape:'htmlall':'UTF-8'}
					{elseif isset($params.type) && $params.type == 'percent'}
						{$tr.$key|escape:'htmlall':'UTF-8'} {l s='%' mod='productstatus'}
					{* If type is 'editable', an input is created *}
					{elseif isset($params.type) && $params.type == 'editable' && isset($tr.id)}
						<input type="text" name="{$key|escape:'htmlall':'UTF-8'}_{$tr.id|escape:'htmlall':'UTF-8'}" value="{$tr.$key|escape:'htmlall':'UTF-8'}" class="{$key|escape:'htmlall':'UTF-8'}" />
					{elseif isset($params.callback)}
						{$tr.$key|escape:'htmlall':'UTF-8'}
					{elseif $key == 'color'}
						<div style="float: left; width: 18px; height: 12px; border: 1px solid #996633; background-color: {$tr.$key|escape:'htmlall':'UTF-8'}; margin-right: 4px;"></div>
					{elseif isset($params.maxlength) && Tools::strlen($tr.$key) > $params.maxlength}
						<span title="{$tr.$key|escape:'htmlall':'UTF-8'}">{$tr.$key|truncate:$params.maxlength:'...'|escape:'htmlall':'UTF-8'}</span>
					{else}
						{$tr.$key|escape:'htmlall':'UTF-8'}
					{/if}
				{else}
					{block name="default_field"}--{/block}
				{/if}
				{if isset($params.suffix)}{$params.suffix|escape:'quotes':'UTF-8'}{/if}
				{if isset($params.color) && isset($tr.color)}
					</span>
				{/if}
			{/block}
			{block name="close_td"}
				</td>
			{/block}
		{/foreach}

	{if $shop_link_type}
		<td class="center" title="{$tr.shop_name|escape:'htmlall':'UTF-8'}">
			{if isset($tr.shop_short_name)}
				{$tr.shop_short_name|escape:'htmlall':'UTF-8'}
			{else}
				{$tr.shop_name|escape:'htmlall':'UTF-8'}
			{/if}</td>
	{/if}
	{if $has_actions}
		<td class="center" style="white-space: nowrap;">
			{foreach $actions AS $action}
				{if isset($tr.$action)}
					{$tr.$action|escape:'htmlall':'UTF-8'}
				{/if}
			{/foreach}
		</td>
	{/if}
	</tr>
{/foreach}
	<tr><td colspan="3">{l s='Total maount(in page)' mod='productstatus'}: <strong>{displayPrice price=$total|escape:'htmlall':'UTF-8'}</strong></td></tr>
	<tr><td colspan="3">{l s='Total weight(in page)' mod='productstatus'}: <strong>{$weight|escape:'htmlall':'UTF-8'} {l s='kg' mod='productstatus'}</strong></td></tr>
	<tr><td colspan="3">{l s='Total items(in page)' mod='productstatus'}: <strong>{$count|escape:'htmlall':'UTF-8'} {l s='pcs' mod='productstatus'}</strong></td></tr>
{else}
	<tr><td class="center" colspan="{count($fields_display) + 2|escape:'htmlall':'UTF-8'}">{l s='No items found' mod='productstatus'}</td></tr>
{/if}

</tbody>
