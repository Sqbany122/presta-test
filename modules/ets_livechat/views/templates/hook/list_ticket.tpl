{*
* 2007-2019 ETS-Soft
*
* NOTICE OF LICENSE
*
* This file is not open source! Each license that you purchased is only available for 1 wesite only.
* If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
* You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
* 
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2019 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
<div class="lc_tickets">
    <div class="panel lc-panel">
        <div class="panel-heading">
            {l s='Support tickets' mod='ets_livechat'}
            <span class="badge">{$totalRecords|intval}</span>
            <span class="panel-heading-action">
                {if $forms}
                    {if $new_ticket_link}
                        <a class="submit_new_ticket btn btn-default btn-primary" href="{$new_ticket_link|escape:'html':'UTF-8'}" title="{l s='Submit new ticket' mod='ets_livechat'}">{l s='Submit new ticket' mod='ets_livechat'}</a>
                    {else}
                        <span class="submit_new_ticket_bt btn btn-default btn-primary" title="{l s='Submit new ticket' mod='ets_livechat'}">{l s='Submit new ticket' mod='ets_livechat'}</span>
                    {/if}
                {/if}
            </span>
        </div>
        {if !$new_ticket_link && $forms}
            <div class="lc_form_submit_new_ticket" style="display:none;">
                <div class="pop_table">
                    <div class="pop_table_cell">
                        <div class="lc_form_submit_new_ticket_content">
                            <h4 class="lc_form_submit_title">
                                {l s='Which kind of support do you need?' mod='ets_livechat'}
                                <span class="lc_form_submit_close lc_close" title="{l s='Close' mod='ets_livechat'}"></span>
                            </h4>
                            <div class="form-group">
                                <select name="form_ticket" id="form_ticket">
                                        <option value="--">{l s='--Select a support form--' mod='ets_livechat'}</option>
                                    {foreach from=$forms item='form'}
                                        <option value="{$form.link|escape:'html':'UTF-8'}">{$form.title|escape:'html':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                                <button name="new_ticket_bt" type="button" id="new_ticket_bt" class="default btn btn-default btn-primary">{l s='Continue' mod='ets_livechat'}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
        <div class="table-responsive clearfix">
            <form method="post" action="{$link->getModuleLink('ets_livechat','ticket')|escape:'html':'UTF-8'}">
                <table class="table configuration">
                    <thead>
                        <tr class="nodrag nodrop">
                            <th class="id_ticket">
                                <span class="title_box">
                                    {l s='Id' mod='ets_livechat'}
                                    <a {if isset($sort) && isset($sort_type) && $sort=='id_message' && $sort_type=='desc'} class="active"{/if} href="{$link->getModuleLink('ets_livechat','ticket',['sort'=>'id_message','sort_type'=>'desc'])|escape:'html':'UTF-8'}">
                                        <i class="icon-caret-down"></i>
                                    </a> 
                                    <a {if isset($sort) && isset($sort_type) && $sort=='id_message' && $sort_type=='asc'} class="active"{/if} href="{$link->getModuleLink('ets_livechat','ticket',['sort'=>'id_message','sort_type'=>'asc'])|escape:'html':'UTF-8'}">
                                        <i class="icon-caret-up"></i>
                                    </a>
                                </span> 
                            </th>
                            <th class="content_ticket">
                                <span class="title_box">{l s='Subject' mod='ets_livechat'}</span>
                            </th>
                            <th class="date_ticket">
                                <span class="title_box">{l s='Date' mod='ets_livechat'}
                                    <a {if isset($sort) && isset($sort_type) && $sort=='date_admin_update' && $sort_type=='desc'} class="active"{/if} href="{$link->getModuleLink('ets_livechat','ticket',['sort'=>'date_admin_update','sort_type'=>'desc'])|escape:'html':'UTF-8'}">
                                        <i class="icon-caret-down"></i>
                                    </a> 
                                    <a {if isset($sort) && isset($sort_type) && $sort=='date_admin_update' && $sort_type=='asc'} class="active"{/if} href="{$link->getModuleLink('ets_livechat','ticket',['sort'=>'date_admin_update','sort_type'=>'asc'])|escape:'html':'UTF-8'}">
                                        <i class="icon-caret-up"></i>
                                    </a>
                                </span>
                            </th>
                            <th class="priority">
                                <span class="title_box">{l s='Priority' mod='ets_livechat'}
                                    <a {if isset($sort) && isset($sort_type) && $sort=='priority' && $sort_type=='desc'} class="active"{/if} href="{$link->getModuleLink('ets_livechat','ticket',['sort'=>'priority','sort_type'=>'desc'])|escape:'html':'UTF-8'}">
                                        <i class="icon-caret-down"></i>
                                    </a> 
                                    <a {if isset($sort) && isset($sort_type) && $sort=='priority' && $sort_type=='asc'} class="active"{/if} href="{$link->getModuleLink('ets_livechat','ticket',['sort'=>'priority','sort_type'=>'asc'])|escape:'html':'UTF-8'}">
                                        <i class="icon-caret-up"></i>
                                    </a>
                                </span>
                                
                            </th>
                            <th class="status">
                                <span class="title_box">{l s='Status' mod='ets_livechat'}</span>
                            </th>
                            <th class="action_ticket">
                                <span class="title_box">{l s='Action' mod='ets_livechat'}</span>
                            </th>
                        </tr>
                        <tr class="nodrag nodrop filter row_hover">
                            <th class="id_ticket">
                                <input type="text" name="id_ticket" value="{if isset($post_value.id_ticket)}{$post_value.id_ticket|intval}{/if}" />
                            </th>
                            <th class="content_ticket">
                                <input type="text" name="subject" value="{if isset($post_value.subject)}{$post_value.subject|escape:'html':'UTF-8'}{/if}" />
                            </th>
                            <th class="date_ticket">
                                <div class="date_range row">
									<div class="input-group fixed-width-md center">
										<input class="filter datepicker date-input form-control " name="date_add_from" placeholder="From" type="text" value="{if isset($post_value.date_add_from)}{$post_value.date_add_from|escape:'html':'UTF-8'}{/if}" />
										<span class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									<div class="input-group fixed-width-md center">
										<input class="filter datepicker date-input form-control" name="date_add_to" placeholder="To" type="text" value="{if isset($post_value.date_add_to)}{$post_value.date_add_to|escape:'html':'UTF-8'}{/if}" />
										<input id="ticket_date_add_1" name="ticket_date_add_to" value="" type="hidden" />
										<span class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
								</div>
                            </th>
                            <th class="priority">
                                <select name="priority">
                                    <option value="">---</option>
                                    <option value="1" {if isset($post_value.priority) && $post_value.priority==1}selected="selected"{/if}>{l s='Low' mod='ets_livechat'}</option>
                                    <option value="2" {if isset($post_value.priority) && $post_value.priority==2}selected="selected"{/if}>{l s='Medium' mod='ets_livechat'}</option>
                                    <option value="3" {if isset($post_value.priority) && $post_value.priority==3}selected="selected"{/if}>{l s='High' mod='ets_livechat'}</option>
                                    <option value="4" {if isset($post_value.priority) && $post_value.priority ==4}selected="selected"{/if}>{l s='Urgent' mod='ets_livechat'}</option>
                                </select>
                            </th>
                            <th class="status">
                                <select name="status">
                                    <option value="">---</option>
                                    <option value="open" {if isset($post_value.status) && $post_value.status=='open'}selected="selected"{/if}>{l s='Open' mod='ets_livechat'}</option>
                                    <option value="cancel" {if isset($post_value.status) && $post_value.status=='cancel'}selected="selected"{/if}>{l s='Canceled' mod='ets_livechat'}</option>
                                    <option value="close" {if isset($post_value.status) && $post_value.status=='close'}selected="selected"{/if}>{l s='Closed' mod='ets_livechat'}</option>
                                </select>
                            </th>
                            <th class="actions">
                                <span class="pull-right">
                                    <button id="submitFilterTicket" {if !($tickets) }disabled="disabled"{/if} class="btn btn-default btn-second" type="submit" name="submitFilterTicket">
                                        <i class="icon-search"></i>{l s='Search' mod='ets_livechat'}
                                    </button>
                                    {if $post_value}    
                                        <br />
                                        <a class="btn btn-warning" href="{$link->getModuleLink('ets_livechat','ticket')|escape:'html':'UTF-8'}">
                                            <i class="icon-eraser"></i>{l s='Reset' mod='ets_livechat'}
                                        </a>
                                    {/if}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <body>
                        {if $tickets}
                            {foreach from=$tickets item='ticket'}
                                <tr id="tr-ticket-{$ticket.id_message|intval}" class="{if $ticket.no_readed}no-reaed{/if}">
                                    <td class="id_ticket">
                                        {$ticket.id_message|intval}
                                    </td>
                                    <td class="content_ticket">
                                        {$ticket.subject|escape:'html':'UTF-8'}
                                    </td>
                                    <td class="date_ticket">
                                        {$ticket.date_admin_update|escape:'html':'UTF-8'}
                                    </td>
                                    <td class="priority">
                                       {Ets_livechat::displayPriority($ticket.priority)|escape:'html':'UTF-8'}
                                    </td>
                                    <td class="status">
                                        <span class="lc_ticket_status {$ticket.status|escape:'html':'UTF-8'}">
                                            {if $ticket.status=='open'}
                                                {l s='Open' mod='ets_livechat'}
                                            {elseif $ticket.status=='close'}
                                                {l s='Closed' mod='ets_livechat'}
                                            {else}
                                                {l s='Canceled' mod='ets_livechat'}
                                            {/if}
                                        </span>
                                    </td>
                                    <td class="action_ticket">
                                        <a class="view_ticket_content view btn btn-default btn-second" href="{$ticket.link_view|escape:'html':'UTF-8'}" title="{l s='View' mod='ets_livechat'}">
                                            <i class="fa fa-eye"></i> {l s='View' mod='ets_livechat'}
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        {/if}
                    </body>
                </table>
                {$pagination_text nofilter}
            </form>
        </div>
    </div>
</div>