{if isset($subscribers) && count($subscribers) > 0}
    <section id="main_subscription" class="page-content card card-block">
        <h3 class="page-heading bottom-indent">{l s='Your Out of Stock Product Subscriptions - ' mod='backinstock'}</h3>
        </br>
        <div id="kb_subscription_list">
            <table class="table table-striped table-bordered table-labeled" id="order-list">
                <thead>
                    <tr>
                        <th class="first_item">{l s='S.No' mod='backinstock'}</th>
                        <th class="item">{l s='Product' mod='backinstock'}</th>
                        <th class="item">{l s='Product Name' mod='backinstock'}</th>
                        <th class="item">{l s='Quantity Requested' mod='backinstock'}</th>
                        <th class="item">{l s='Date' mod='backinstock'}</th>
                        <th class="item">{l s='Status' mod='backinstock'}</th>
                        {if isset($remove_subscription_button) && $remove_subscription_button == 1}
                            <th class="last_item">{l s='Action' mod='backinstock'}</th>
                        {/if}
                    </tr>
                </thead>
                <tbody>
                    {assign var=indexRow value=0}
                    {foreach $subscribers as $subscribers_key => $subscribers_data}
                        {assign var=indexRow value=$indexRow+1}
                        <tr>
                            <td>{$indexRow|escape:'htmlall':'UTF-8'}</td>
                            <td><a href="{$subscribers_data['product_link']}">
                                    {* variable contains url content, can not escape *}
                                    <img src='{$subscribers_data['image_link']}' width='60' />
                                    {* variable contains url content, can not escape *}
                                </a>
                            </td>
                            <td><a
                                    href="{$subscribers_data['product_link']}">{$subscribers_data['product_name']|escape:'htmlall':'UTF-8'}</a>
                            </td> {* variable contains url content, can not escape *}
                            <td>{$subscribers_data['req_quan']}</td>
                            <td>{$subscribers_data['date_added']|escape:'htmlall':'UTF-8'}</td>

                            <td>
                                {if isset($subscribers_data['quantity']) && $subscribers_data['quantity'] > 0}
                                    {l s='In Stock' mod='backinstock'}
                                {else}
                                    {l s='Out Of Stock' mod='backinstock'}
                                {/if}
                            </td>

                            {if isset($remove_subscription_button) && $remove_subscription_button == 1}
                                <td>
                                    <a href="javascript:void(0)" title="{l s='Click to remove subscription' mod='backinstock'}"
                                        onclick="removeSubscription({$subscribers_data['id_subscription']|escape:'htmlall':'UTF-8'})">
                                        <span class="fa fa-trash"></span>
                                        {l s='Remove' mod='backinstock'}

                                    </a>
                                </td>
                            {/if}
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </section>
    <section id="footer_subscription" class="page-content card card-block">
        <div class="sv-p-paging">
            {if $total_pages == 1}
                <div class="product-count" style="text-align:left;">{l s='Showing' mod='backinstock'}
                    {$start|escape:'htmlall':'UTF-8'} - {$end|escape:'htmlall':'UTF-8'} {l s='of' mod='backinstock'}
                    {$total_subscription|escape:'htmlall':'UTF-8'} {l s='Items' mod='backinstock'}</div>
            {/if}
            {if $total_pages > 1}
                <div class="bottom-pagination-content clearfix">
                    <div class="product-count" style="text-align:left;">{l s='Showing' mod='backinstock'}
                        {$start|escape:'htmlall':'UTF-8'} - {$end|escape:'htmlall':'UTF-8'} {l s='of' mod='backinstock'}
                        {$total_subscription|escape:'htmlall':'UTF-8'} {l s='Items' mod='backinstock'}</div>

                    <!-- Pagination -->
                    <ul class="pagination" style="float:right;">
                        {* for left most page *}
                        {if $total_pages > 1}

                            {if $kbpage neq 1}
                                <li>
                                    <a href='javascript:void(0)' onclick="getNextSubscriptionResultPage(1)">
                                        <span>
                                            <<< /span>
                                    </a>
                                </li>
                            {else}
                                <li class="active current">
                                    <span><span>
                                            <<< /span>
                                        </span>
                                </li>
                            {/if}
                        {/if}
                        {* for 1 page previous to current page *}
                        {if $total_pages > 1}
                            {if $kbpage-1 > 0}

                                <li>
                                    {assign var=pageNo value=$kbpage-1}
                                    <a href='javascript:void(0)'
                                        onclick="getNextSubscriptionResultPage({$pageNo|escape:'htmlall':'UTF-8'})">
                                        <span>
                                            << /span>
                                    </a>
                                <li>
                                {else}

                                    {if $kbpage neq 1}
                                    <li>
                                        <a href='javascript:void(0)'
                                            onclick="getNextSubscriptionResultPage({$kbpage|escape:'htmlall':'UTF-8'})">
                                            <span>
                                                << /span>
                                        </a>
                                    </li>
                                {else}
                                    <li class="active current">
                                        <span><span>
                                                << /span>
                                            </span>
                                    </li>
                                {/if}
                            {/if}
                        {/if}
                        {* if at last page then also show last-2 page *}
                        {if $total_pages > 1}
                            {if $kbpage-2 >= 1 && $kbpage eq $total_pages}

                                <li>
                                    {assign var=pageNo value=$kbpage-2}
                                    <a href='javascript:void(0)'
                                        onclick="getNextSubscriptionResultPage({$pageNo|escape:'htmlall':'UTF-8'})">
                                        <span>{$pageNo|escape:'htmlall':'UTF-8'}</span>
                                    </a>
                                </li>
                            {/if}
                        {/if}
                        {* for middle of pages *}
                        {for $count=1 to $total_pages}

                            {assign var=params value=[

                                        'page' => $count
                                        ]}
                            {if $count eq $kbpage+1 || $count eq $kbpage-1 || $kbpage eq $count }
                                {if $kbpage ne $count}
                                    <li>
                                        <a href='javascript:void(0)'
                                            onclick="getNextSubscriptionResultPage({$count|escape:'htmlall':'UTF-8'})">
                                            <span>{$count|escape:'htmlall':'UTF-8'}</span>
                                        </a>
                                    </li>
                                {else}
                                    <li class="active current">
                                        <span><span>{$count|escape:'htmlall':'UTF-8'}</span></span>
                                    </li>
                                {/if}
                            {/if}
                        {/for}
                        {* if  current page is 1 then also show 3rd page if exist *}
                        {if $total_pages > 1}
                            {if $kbpage+2 le $total_pages && $kbpage eq 1}

                                <li>
                                    {assign var=pageNo value=$kbpage+2}
                                    <a href='javascript:void(0)'
                                        onclick="getNextSubscriptionResultPage({$pageNo|escape:'htmlall':'UTF-8'})">
                                        <span>{$pageNo|escape:'htmlall':'UTF-8'}</span>
                                    </a>
                                </li>
                            {/if}
                        {/if}
                        {* for next page *}
                        {if $total_pages > 1}
                            {if $kbpage+1 < $total_pages}

                                <li>
                                    {assign var=pageNo value=$kbpage+1}
                                    <a href='javascript:void(0)'
                                        onclick="getNextSubscriptionResultPage({$pageNo|escape:'htmlall':'UTF-8'})">
                                        <span>></span>
                                    </a>
                                </li>
                            {else}

                                {if $kbpage neq $total_pages}
                                    <li>
                                        <a href='javascript:void(0)'
                                            onclick="getNextSubscriptionResultPage({$total_pages|escape:'htmlall':'UTF-8'})">
                                            <span>></span>
                                        </a>
                                    </li>
                                {else}
                                    <li class="active current">
                                        <span><span>></span></span>
                                    </li>
                                {/if}
                            {/if}

                            {* for last page i.e >> *}
                        {/if}
                        {if $total_pages > 1}

                            {if $kbpage neq $total_pages}
                                <li>
                                    <a href='javascript:void(0)'
                                        onclick="getNextSubscriptionResultPage({$total_pages|escape:'htmlall':'UTF-8'})">
                                        <span>>></span>
                                    </a>
                                </li>
                            {else}
                                <li class="active current">
                                    <span><span>>></span></span>
                                </li>
                            {/if}
                        {/if}
                    </ul>

                    <!-- /Pagination -->
                </div>
            {/if}

        </div>
    </section>
{else}
    <section id="main_subscription" class="page-content card card-block">
        <h3 class="page-heading bottom-indent">{l s='No Subscriptions.' mod='backinstock'}</h3>
    </section>
{/if}

{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    velsof.com <support@velsof.com>
* @copyright 2014 Velocity Software Solutions Pvt Ltd
* @license   see file: LICENSE.txt
*
* Description
*
* Product Update Block Page
*}