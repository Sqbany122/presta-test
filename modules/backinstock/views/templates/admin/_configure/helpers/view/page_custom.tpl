<script	src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" >
<style>
    #list_graph {
        border: none !important;
        padding-top: 0px !important;
    }
    .mt-4 {
        border: none !important;
    }
</style>
<div class="panel col-lg1" id='list_graph'>
    <div class='panel col-lg1'>
    <h3 class="heading" >{l s='Graph' mod='backinstock'}</h3>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-11">
                <div class="card mt-4">
{*                    <div class="card-header"></div>*}
                    <div class="card-body">
                        <div class="chart-container pie-chart">
                            <canvas id="pie_chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            {*<div class="col-md-4" style="display: none">
                <div class="card mt-4">
                    <div class="card-header">Doughnut Chart</div>
                    <div class="card-body">
                        <div class="chart-container pie-chart">
                            <canvas id="doughnut_chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>*}
            {*<div class="col-md-4">
                <div class="card mt-4 mb-4">
                    <div class="card-header">Bar Chart</div>
                    <div class="card-body">
                        <div class="chart-container pie-chart">
                            <canvas id="bar_chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>*}
        </div>
    </div>
    {*<div id="graph_loader">
        <div id="flot-placeholder" style="width:100%;height:100%;"></div>
    </div>*}
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    </div>
    <div class='panel col-lg1'>
    <h3 class="heading" >{l s='Subscribed Customer' mod='backinstock'}</h3>
    <div class="table-responsive clearfix">
        <table id="combinations-list" class="table  configuration">
			<thead>
				<tr class="nodrag nodrop">
					<th class=" left">
						<span class="title_box ">{l s='S.No.' mod='backinstock'}</span>
					</th>
					<th class=" left">
						<span class="title_box ">{l s='Product' mod='backinstock'}</span>
					</th>
                    <th class=" left">
						<span class="title_box ">{l s='SKU' mod='backinstock'}</span>
					</th>
                    <th class=" left">
						<span class="title_box ">{l s='Current Price' mod='backinstock'}</span>
					</th>
                    <th class=" left">
						<span class="title_box ">{l s='No. of Customers' mod='backinstock'}</span>
					</th>
									</tr>
						</thead>
<tbody>
    {if empty($present)}
        <tr><td class="left">{l s='No results to display' mod='backinstock'}</td><td></td></tr>	
	{/if}
     {$i=1}
    {foreach $present as $product}
        <tr {if $i is even}class="price-alert-tab-odd"{/if}>
            <td>{$i|escape:'htmlall':'UTF-8'}</td>
            <td>{$product['name']|escape:'htmlall':'UTF-8'}<br><label style="font-size: 11px; font-weight: normal;">{$product['attributes']|escape:'htmlall':'UTF-8'}</label></td>
            <td>{$product['model']|escape:'htmlall':'UTF-8'}</td>
            <td>{$product['current_price']|escape:'htmlall':'UTF-8'}</td>
            <td>{*<a  class='popup_users'  data='{$product['product_attribute_id']|escape:'htmlall':'UTF-8'}'>*}{$product['count']|escape:'htmlall':'UTF-8'}{*</a>*}</td>
        </tr>
        {$i=$i+1}
    {/foreach}
    
</tbody>

	</table>
    </div>
</div>
</div>
    <div class="panel col-lg1" id='list_graph_stats' style="display: none">
        <div class='panel col-lg1'>
            <h3 class="heading" >{l s='Statistic Graph' mod='backinstock'}</h3>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-5">
                        <div class="card mt-4">
                            {*                    <div class="card-header"></div>*}
                            <div class="card-body">
                                <div class="chart-container pie-chart">
                                    <canvas id="pie_chart_stats"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card mt-4">
{*                            <div class="card-header">Bar Chart</div>*}
                            <div class="card-body">
                                <div class="chart-container pie-chart">
                                    <canvas id="bar_chart_stats"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
{*                <div class="row">*}
                    {*<div class="col-md-4" style="display: none">
                    <div class="card mt-4">
                    <div class="card-header">Doughnut Chart</div>
                    <div class="card-body">
                    <div class="chart-container pie-chart">
                    <canvas id="doughnut_chart"></canvas>
                    </div>
                    </div>
                    </div>
                    </div>*}
                    {*<div class="col-md-4">
                    <div class="card mt-4 mb-4">
                    <div class="card-header">Bar Chart</div>
                    <div class="card-body">
                    <div class="chart-container pie-chart">
                    <canvas id="bar_chart"></canvas>
                    </div>
                    </div>
                    </div>
                    </div>*}
{*                </div>*}
            </div>
        </div>
        <div class='panel col-lg1'>
    <h3 class="heading" >{l s='Statistics' mod='backinstock'}</h3>
    <div class="table-responsive clearfix">
        <table id="combinations-list" class="table  configuration">
			<thead>
				<tr class="nodrag nodrop">
					<th class=" left">
						<span class="title_box ">{l s='Sent Emails' mod='backinstock'}</span>
					</th>
                    {*<th class=" left">
						<span class="title_box ">{l s='Opened Emails' mod='backinstock'}</span>
					</th>*}
                    <th class=" left">
						<span class="title_box ">{l s='View Clicks' mod='backinstock'}</span>
					</th>
                    {*<th class=" left">
						<span class="title_box ">{l s='Buy Now Clicks' mod='backinstock'}</span>
					</th>*}
									</tr>
						</thead>
<tbody>
    {if empty($stats)}
        <tr><td class="left">{l s='No results to display' mod='backinstock'}</td><td></td></tr>	
	{/if}
     {$i=1}
    {foreach $stats as $stat}
        <tr {if $i is even}class="price-alert-tab-odd"{/if}>
            <td>{$stat['total_sent']|escape:'htmlall':'UTF-8'}</td>
{*            <td>{$stat['total_opened']|escape:'htmlall':'UTF-8'}</td>*}
            <td>{$stat['total_view_clicks']|escape:'htmlall':'UTF-8'}</td>
{*            <td>{$stat['total_buy_now_clicks']|escape:'htmlall':'UTF-8'}</td>*}
        </tr>
        {$i=$i+1}
    {/foreach}
    
</tbody>

	</table>
    </div>
    </div>
{*
    * DISCLAIMER
    *
    * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
    * versions in the future. If you wish to customize PrestaShop for your
    * needs please refer tohttp://www.prestashop.com for more information.
    * We offer the best and most useful modules PrestaShop and modifications for your online store.
    *
    * @category  PrestaShop Module
    * @author    knowband.com <support@knowband.com>
    * @copyright 2015 Knowband
    * @license   see file: LICENSE.txt
    *
    * Description
    *
    * Admin tpl file
    *}