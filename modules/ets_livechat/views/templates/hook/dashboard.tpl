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
{$menu_top nofilter}
<script type="text/javascript" src="{$ETS_LC_MODULE_URL|escape:'html':'utf-8'}views/js/chart.min.js"></script>
<div class="lc_dashboard_wapper">
    <div class="row">
        <div class="col-lg-12 lc_dashboard_content">
            <div class="col-lg-3 lc_dashboard_conversation">
                <span>{l s='Conversations' mod='ets_livechat'}</span>
                <div class="lc_count">
                    <i class="fa fa-list"></i>
                    <span class="pull-right">{$countConversation|intval}</span>
                </div>
                <div class="lc_total">
                    {l s='This month' mod='ets_livechat'}
                    <span class="pull-right">{$countConversationInMonth|intval}</span>
                </div>
            </div>
            <div class="col-lg-3 lc_dashboard_received_ticket">
                <span>{l s='Messages' mod='ets_livechat'}</span>
                <div class="lc_count">
                    <i class="fa fa-comments-o"></i>
                    <span class="pull-right">{$countMessages|intval}</span>
                </div>
                <div class="lc_total">
                    {l s='This month' mod='ets_livechat'}
                    <span class="pull-right">{$countMessagesInMonth|intval}</span>
                </div>
            </div>
            <div class="col-lg-3 lc_dashboard_open_ticket">
                <span>{l s='Open tickets' mod='ets_livechat'}</span>
                <div class="lc_count">
                    <i class="fa fa-cubes" aria-hidden="true"></i>
                    <span class="pull-right">{$countOpenTicket|intval}</span>
                </div>
                <div class="lc_total">
                    {l s='This month' mod='ets_livechat'}
                    <span class="pull-right">{$countOpenTicketInMonth|intval}</span>
                </div>
            </div>
            <div class="col-lg-3 lc_dashboard_solved_ticket">
                <span>{l s='Solved tickets' mod='ets_livechat'}</span>
                <div class="lc_count">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                    <span class="pull-right">{$countSolvedTicket|intval}</span>
                </div>
                <div class="lc_total">
                    {l s='This month' mod='ets_livechat'}
                    <span class="pull-right">{$countSolvedTicketInMouth|intval}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="lc_dashboard_chart">
            <div class="lv_admin_filter">
                <form id="calendar_form" class="form-inline" action="{$action|escape:'html':'UTF-8'}" method="post" name="calendar_form">
                    <h4 class="panel-heading">{l s='Statistics' mod='ets_livechat'}</h4>
                    <div class="btn-group">
    					<button type="button" data-submit="submitDateWeek" name="submitDateWeek" class="btn btn-default submitLineChart active">
    						{l s='Week' mod='ets_livechat'}
    					</button>
    					<button type="button" data-submit="submitDateMonth" name="submitDateMonth" class="btn btn-default submitLineChart">
    						{l s='Month' mod='ets_livechat'}
    					</button>
    					<button type="button" data-submit="submitDateYear" name="submitDateYear" class="btn btn-default submitLineChart">
    						{l s='Year' mod='ets_livechat'}
    					</button>
                        <button type="button" data-submit="submitDateAll" name="submitDateAll" class="btn btn-default submitLineChart">
    						{l s='All' mod='ets_livechat'}
    					</button>
    				</div>
                </form>
            </div>
            <div class="col-lg-3 lc_chart_conversation lc_chart_recently_customer">
                {$recently_customer nofilter}
            </div>
            <div class="col-lg-4 lc_chart_conversation">
                <h4>{l s='Live chat' mod='ets_livechat'}</h4>
                <canvas  id="chartConversation" style="height: 300px; width: 100%;"></canvas >
            </div>
            <div class="col-lg-4 lc_chart_ticket">
                <h4>{l s='Tickets' mod='ets_livechat'}</h4>
                <canvas id="chartticket" style="height: 300px; width: 100%;"></canvas>
            </div>
        </div>
        <div class="lc_ticket_recently">
            <div class="col-sm-4 col-xs-6 col-lg-4">
                <div class="lc_ticket_recently_block">
                    <h4>{l s='Active staffs' mod='ets_livechat'}</h4>
                    {if $active_staffs && ($recentlyConversations || $recentlyTickets)}
                        <div class="lc_ticket_recently_list">
                            {foreach from=$active_staffs item='staff'}
                                <div class="lc_chart_staffs">
                                    <div class="lc_chart_staffs_avata">
                                        <img src="{$staff.avatar|escape:'html':'UTF-8'}" alt="{$staff.firstname|escape:'html':'UTF-8'} {$staff.lastname|escape:'html':'UTF-8'}"/>
                                        <span class="name">{$staff.firstname|escape:'html':'UTF-8'} {$staff.lastname|escape:'html':'UTF-8'}</span>
                                        <div class="lc_rate_staff">
                                            <div class="star_content">
                                                <div class="fa fa-star-o {if $staff.avg_rate && $staff.avg_rate>=1}fa-star{/if} {if $staff.avg_rate >0 && $staff.avg_rate <1}fa-star-{$staff.du|intval}{/if}" title="{l s='Terrible' mod='ets_livechat'}">&nbsp;</div>
                                                <div class="fa fa-star-o {if $staff.avg_rate && $staff.avg_rate>=2}fa-star{/if} {if $staff.avg_rate >1 && $staff.avg_rate <2}fa-star-{$staff.du|intval}{/if}" title="{l s='Acceptable' mod='ets_livechat'}">&nbsp;</div>
                                                <div class="fa fa-star-o {if $staff.avg_rate && $staff.avg_rate>=3}fa-star{/if} {if $staff.avg_rate >2 && $staff.avg_rate <3}fa-star-{$staff.du|intval}{/if}" title="{l s='Fairly Good' mod='ets_livechat'}">&nbsp;</div>
                                                <div class="fa fa-star-o {if $staff.avg_rate && $staff.avg_rate>=4}fa-star{/if} {if $staff.avg_rate >3 && $staff.avg_rate <4}fa-star-{$staff.du|intval}{/if}" title="{l s='Good' mod='ets_livechat'}">&nbsp;</div>
                                                <div class="fa fa-star-o {if $staff.avg_rate && $staff.avg_rate>=5}fa-star{/if} {if $staff.avg_rate >4 && $staff.avg_rate <5}fa-star-{$staff.du|intval}{/if}" title="{l s='Excellent' mod='ets_livechat'}">&nbsp;</div>
                                    		</div>
                                            ({$staff.avg_rate|floatval})
                                        </div>   
                                    </div>
                                    <div class="lc_chart_staff_infor">
                                        <p>{l s='Replied' mod='ets_livechat'} {$staff.total_conversation|intval} {if $staff.total_conversation >1}{l s='conversations' mod='ets_livechat'}{else}{l s='conversation' mod='ets_livechat'}{/if}.</p>
                                        <p>{l s='Solved' mod='ets_livechat'} {$staff.total_ticket|intval} {if $staff.total_ticket > 1}{l s='tickets' mod='ets_livechat'}{else}{l s='ticket' mod='ets_livechat'}{/if}</p>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    {else}
                        <div class="no-record">{l s='No active staffs' mod='ets_livechat'}</div>
                    {/if}
                </div>
            </div>
            <div class="col-sm-4 col-xs-12 col-lg-4">
                <div class="lc_chart_recen_conversations">
                    <div class="lc_chart_staffs_actions">
                        <h4>{l s='Recent conversations' mod='ets_livechat'}</h4>
                    </div>
                    <ul class="lc_list_customer">
                        {if $recentlyConversations}
                            {foreach from =$recentlyConversations item='conversation'}
                                <li class="item">
                                    <span class="img">
                                        <img src="{$conversation.avatar|escape:'html':'UTF-8'}" alt="{if $conversation.fullname}{$conversation.fullname}{else}{$conversation.customer_name}{/if}" />
                                    </span>
                                    <span class="name{if !$ETS_DISPLAY_DASHBOARD_ONLY} recen-conversation-item{/if}" {if !$ETS_DISPLAY_DASHBOARD_ONLY}data-id="{$conversation.id_conversation|intval}"{/if} title="{l s='View conversation' mod='ets_livechat'}" >
                                        {if $conversation.fullname}{$conversation.fullname|escape:'html':'UTF-8'}{else}{$conversation.customer_name|escape:'html':'UTF-8'}{/if}
                                        <span class="last_message">{$conversation.last_message.message nofilter}</span>
                                    </span>
                                    <div class="item-content">
                                        <span class="date_last_message pull-right">{$conversation.last_message.datetime_added|escape:'html':'UTF-8'}</span>
                                        <span class="browser_name {Ets_livechat::getBrowserInfo($conversation.browser_name)|escape:'html':'UTF-8'}"></span>
                                    </div>
                                </li>
                            {/foreach}
                        {else}
                            <div class="no-record">{l s='No conversations available' mod='ets_livechat'}</div>
                        {/if}
                    </ul>
                    {if !$ETS_DISPLAY_DASHBOARD_ONLY && $recentlyConversations}
                        <a href="#" class="view-all-conversations view-all" title="{l s='View all conversations' mod='ets_livechat'}">
                            {l s='View all conversations' mod='ets_livechat'}
                        </a>
                    {/if}
                </div>
            </div>
            <div class="col-sm-4 col-xs-12 col-lg-4">
                <div class="lc_chart_recen_tickets">
                    <div class="lc_chart_staffs_actions">
                        <h4>{l s='Recent tickets' mod='ets_livechat'}</h4>
                    </div>
                    <ul class="lc_list_customer">
                        {if $recentlyTickets}
                            {foreach from =$recentlyTickets item='ticket'}
                                <li class="item">
                                    <span class="img"><img src="{$ticket.avatar|escape:'html':'UTF-8'}" alt="{if $ticket.customer.name}{$ticket.customer.name}{/if}" /></span>
                                    {if $ticket.customer.name}
                                        <span class="name">
                                            <a class="link-view-ticket" href="{$link->getAdminLink('AdminLiveChatTickets')|escape:'html':'UTF-8'}&viewticket&id_ticket={$ticket.id_message|intval}" title="{l s='View ticket' mod='ets_livechat'}">
                                                {$ticket.customer.name|escape:'html':'UTF-8'}
                                            </a>
                                            <span class="last_message">{$ticket.subject|escape:'html':'UTF-8'}</span>
                                        </span>
                                    {else}
                                        <span class="last_message"><a class="link-view-ticket" href="{$link->getAdminLink('AdminLiveChatTickets')|escape:'html':'UTF-8'}&viewticket&id_ticket={$ticket.id_message|intval}" title="{l s='View ticket' mod='ets_livechat'}">{$ticket.subject|escape:'html':'UTF-8'}</a></span>
                                    {/if}
                                    <div class="item-content">
                                        <span class="date_last_message pull-right">{$ticket.date_add|escape:'html':'UTF-8'}</span>
                                        <span title="{if $ticket.status=='close'}{l s='Closed' mod='ets_livechat'}{/if} {if $ticket.status=='open'}{l s='Open' mod='ets_livechat'}{/if}{if $ticket.status=='cancel'}{l s='Canceled' mod='ets_livechat'}{/if}" class="status {$ticket.status|escape:'html':'UTF-8'}"></span>
                                    </div>
                                </li>
                            {/foreach}
                        {else}
                            <div class="no-record">{l s='No tickets available' mod='ets_livechat'}</div>
                        {/if}
                    </ul>
                    {if $recentlyTickets}
                        <a href="{$link->getAdminLink('AdminLiveChatTickets')|escape:'html':'UTF-8'}" class="view-all-tickets view-all" title="{l s='View all tickets' mod='ets_livechat'}">
                            {l s='View all tickets' mod='ets_livechat'}
                        </a>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var conversation_datasets ={$conversation_datasets|json_encode};
var ticket_datasets = {$ticket_datasets|json_encode};
var chart_labels=[{foreach from=$chart_labels item='data'}'{$data|escape:'html':'UTF-8'}',{/foreach}];
</script>
<script type="text/javascript" src="{$ETS_LC_MODULE_URL|escape:'html':'utf-8'}views/js/dashboard.js"></script>