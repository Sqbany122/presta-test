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
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Admin tpl file
*}

<div class="col-lg-12 col-md-12" id="cron_details" style="display: none">
    <table class="topbuttons" style="width: 360px; border-spacing: 10px; border-collapse: separate; margin-top: -12px;">
        <tr>
            <td>
                <a style="text-decoration: none;" href="cron=update_carts&type=manual" target="_blank">
                    <span class="btn btn-block btn-success action-btn" style="text-shadow: none;padding:7px;">{l s='Update Abandoned Cart List' mod='backinstock'}</span>
                </a>
            </td>
            <td>
                <a style="text-decoration: none;" onclick='if (!confirm("{l s='Do you want to Run Cron Manually?' mod='backinstock'}"))
                            return false;' href="cron=send_mails&secure_key=&type=manual" target="_blank">
                    <span class="btn btn-block btn-success action-btn" style="text-shadow: none;padding: 7px;">{l s='Run Send Mail Cron Manually' mod='backinstock'}</span>
                </a>
            </td>
            <td>
                <a style="text-decoration: none;" onclick='if (!confirm("{l s='Do you want to Run Cron Manually?' mod='backinstock'}"))
                            return false;' href="cron=send_push_notifications&secure_key=&type=manual" target="_blank">
                    <span class="btn btn-block btn-success action-btn" style="text-shadow: none;padding: 7px;">{l s='Send Notifications Manually' mod='backinstock'}</span>
                </a>
            </td>
        </tr>
    </table>
</div>
                
<div id="cron_instructions">
    {* Start - Code Added by RS on 06-Sept-2017 for adding the warning to configure the CRON jobs before enabling the module *}
    <div class="alert alert-warning">
        {l s='Please make sure you configure the CRON jobs as specified below before enabling the module on your store.' mod='backinstock'}
        {l s='It is strongly recommended that the CRON jobs run once an hour.' mod='backinstock'}
    </div>
    {* End - Code Added by RS on 06-Sept-2017 for adding the warning to configure the CRON jobs before enabling the module *}
    <div class="widget" id="cron_instructions" data-toggle="collapse-widget" style="margin: 15px 8px 0px 0px;">
        <div class="widget-head" >
            <h3 class="heading" style='margin: 0px; height: 0px;'>{l s='Cron Instructions' mod='backinstock'}</h3>
        </div>
        <div class="widget-body" style="padding-top: 30px;">
            <p style="color:#A7A7A7; font-size: 13px; font-weight:normal;">
                {l s='Add the cron to your store via control panel/putty to send email to customer automatically according to your serial reminder settings. Find below the Instruction to add the cron.' mod='backinstock'}<br /><br />
                <b>{l s='URLs to Add to Cron via Control Panel' mod='backinstock'}</b><br>
                <p class="cron_url">
                    
                </p>
                <b style="color: #A7A7A7; font-size: 13px;">{l s='Cron setup via SSH' mod='backinstock'}</b><br />
                <p class="cron_url_via_ssh">
                    
                </p>
            </p>
        </div>
    </div>
</div>