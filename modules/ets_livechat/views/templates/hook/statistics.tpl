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
* needs please, contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2019 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
<script type="text/javascript" src="{$js_dir_path|escape:'quotes':'UTF-8'}d3.v3.min.js"></script>
<script type="text/javascript" src="{$js_dir_path|escape:'quotes':'UTF-8'}nv.d3.min.js"></script>
<div class="panel statics_form">
    <div class="panel-heading">
		<i class="icon icon-line-chart fa fa-line-chart"></i> {l s='Statistics' mod='ets_livechat'}
    </div>
    <div class="form-wrapper">
        <div class="form-group-wapper">
            <div class="lv_admin_statistic form-group form_group_contact chart">
                <div class="lv_admin_chart">
                    <div class="line_chart">
                        <svg style="width:100%; height: 500px;"></svg>
                    </div>
                </div>
                <div class="lv_admin_filter">
                    <form id="lv_admin_filter_chart" class="defaultForm form-horizontal" action="{$action|escape:'quotes'}" enctype="multipart/form-data" method="POST">
                        <div class="lv_admin_filter_chart_settings">
                                <div class="lv_admin_filter_date">
                                    <label>{l s='Month' mod='ets_livechat'}</label>
                                    <select id="months" name="months" class="form-control">
                                        <option value="" {if !$lv_month} selected="selected"{/if}>{l s='All' mod='ets_livechat'}</option>
                                        {if $months}
                                            {foreach from=$months key=k item=month}
                                                <option value="{$k|intval}"{if $lv_month == $k} selected="selected"{/if}>{l s=$month mod='ets_livechat'}</option>
                                            {/foreach}
                                        {/if}
                                    </select>
                                </div>
                                <div class="lv_admin_filter_date">
                                    <label>{l s='Year' mod='ets_livechat'}</label>
                                    <select id="years" name="years" class="form-control">
                                        <option value="" {if !$lv_year} selected="selected"{/if}>{l s='All' mod='ets_livechat'}</option>
                                        {if $years}
                                            {foreach from=$years item=year}
                                                <option value="{$year|intval}" {if $lv_year == $year} selected="selected"{/if}>{$year|intval}</option>
                                            {/foreach}
                                        {/if}
                                    </select>
                                </div>
                                <div class="lv_admin_filter_button">
                                    <button name="submitFilterChart" class="btn btn-default" type="submit">{l s='Filter' mod='ets_livechat'}</button>
                                    {if $show_reset}
                                        <a href="{$action|escape:'quotes'}&current_tab_acitve=statistics" class="btn btn-default">{l s='Reset' mod='ets_livechat'}</a>
                                    {/if}
                                </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var ets_lv_x_days = '{l s='Day' mod='ets_livechat'}';
    var ets_lv_x_months = '{l s='Month' mod='ets_livechat'}';
    var ets_lv_x_years = '{l s='Year' mod='ets_livechat'}';
    var ets_lv_y_label = '{l s='Count' mod='ets_livechat'}';
    var ets_lv_line_chart = {$lineChart|json_encode}
</script>