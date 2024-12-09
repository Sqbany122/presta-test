/**
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
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2019 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */
$(document).ready(function()
{
    var ctx_conversation = $('#chartConversation');
    var ctx_ticket= $('#chartticket');
    var chart_conversation= lc_creatDashboardChart(ctx_conversation,conversation_datasets,chart_labels);
    var chart_ticket= lc_creatDashboardChart(ctx_ticket,ticket_datasets,chart_labels);
    $(document).on('click','.submitLineChart',function(){
        if(!$(this).hasClass('active'))
        {
            $this=$(this);
            $('.submitLineChart').removeClass('active');
            $(this).addClass('active');
            $('body').addClass('lc_loading');
            $.ajax({
                url: '',
                type: 'post',
                dataType: 'json',
                data: {
                    actionSubmitChart: $this.attr('data-submit'),
                    ajax : 1,
                },
                success: function(json)
                { 
                    lc_updateDashboardChart(chart_conversation,json.label_datas,json.data_conversations);
                    lc_updateDashboardChart(chart_ticket,json.label_datas,json.data_tickets);
                    $('.lc_chart_recently_customer').html(json.recently_customer);
                    $('body').removeClass('lc_loading');
                }
            });
        }
        return false;
    });
});
function lc_creatDashboardChart(ctx,datasets,labels)
{
    var aR = null; //store already returned tick
    var conversationLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: datasets,
            labels: labels,
            
        },
        options: {
          scales: {
             yAxes: [{
                ticks: {
                   min: 0,
                   callback: function(value) {if (value % 1 === 0) {return value;}},
                }
             }]
          },
          legend: {
                display: true,
          },
          tooltips: {
                mode: 'point'
          },
       }
    });
    return conversationLineChart;
}
function lc_updateDashboardChart(chart,label_datas,datas)
{
    chart.data.labels=[];
    if(label_datas)
    {
        $(label_datas).each(function(){
            chart.data.labels.push(this);
        });
    }
    var i=0;
    chart.data.datasets.forEach((dataset) => {
        dataset.data=[];
        if(datas[i])
        {
            $(datas[i]).each(function(){
                dataset.data.push(this);
            });
        }
        i++;
    });
    chart.update();
}