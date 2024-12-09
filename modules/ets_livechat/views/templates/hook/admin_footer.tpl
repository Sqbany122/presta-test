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
<script type="text/javascript">
{foreach from=$assigns key='vairableName' item='val'}
    var {$vairableName|escape:'html':'UTF-8'} = {if $vairableName=='ETS_LC_URL_AJAX' || $vairableName=='ETS_LC_TEXT_SEND_OffLINE' || $vairableName=='ETS_LC_TEXT_SEND'|| $vairableName=='ETS_LC_TEXT_BUTTON_EDIT'||$vairableName=='ETS_LC_AVATAR_IMAGE_TYPE'}'{$val|escape:'quotes':'UTF-8'}'{else}{$val|intval}{/if};
{/foreach}
var ETS_LC_MODULE_URL_AJAX ='{$ETS_LC_MODULE_URL_AJAX|escape:'quotes':'UTF-8'}';
var ETS_LC_MODULE_URL_ADMIM ='{$ETS_LC_MODULE_URL_ADMIM|escape:'quotes':'UTF-8'}';
var ets_ajax_message_url = '{$ets_ajax_message_url|escape:'quotes':'UTF-8'}';
var ETS_CONVERSATION_DISPLAY_ADMIN ={$ETS_CONVERSATION_DISPLAY_ADMIN|intval};
var ETS_CONVERSATION_LIST_TYPE ='{$ETS_CONVERSATION_LIST_TYPE|escape:'html':'UTF-8'}';
var ETS_CLOSE_CHAT_BOX_BACKEND_TYPE ='lc_{$ETS_CLOSE_CHAT_BOX_BACKEND_TYPE|escape:'html':'utf-8'}';
var confirm_delete ="{l s='Do you want to delete?' mod='ets_livechat'}";
var confirm_delete_deparment = "{l s='Do you want to delete this department?' mod='ets_livechat'}";
var confirm_delete_message = "{l s='Do you want to delete this message?' mod='ets_livechat'}";
var confirm_delete_attachment = "{l s='Do you want to delete attachments?' mod='ets_livechat'}"
var confirm_delete_chatbox = "{l s='Do you want to delete this chat?' mod='ets_livechat'}";
var confirm_delete_avata = "{l s='Do you want to delete customer avatar?' mod='ets_livechat'}";
var confirm_delete_avata_staff = "{l s='Do you want to delete staff avatar?' mod='ets_livechat'}";
var confirm_delete_logo = "{l s=' Do you want to delete shop logo?' mod='ets_livechat'}";
var confirm_clear ="{l s='Do you want to clear message?' mod='ets_livechat'}";
var confirm_end_chat="{l s='Do you want to end this chat?' mod='ets_livechat'}";
var confirm_change_department ="{l s='Do you want to change department?' mod='ets_livechat'}";
var confirm_delete_field= "{l s='Do you want to delete this field?' mod='ets_livechat'}";
var confirm_delete_ticket= "{l s='Do you want to delete this ticket?' mod='ets_livechat'}";
var confirm_delete_form = "{l s='Do you want to delete this form?' mod='ets_livechat'}";
var clear_conversation ="{l s='Conversations have been cleared successfully' mod='ets_livechat'}";
var delete_attachments ="{l s='Attachments have been deleted successfully' mod='ets_livechat'}";
var lc_invalid_file = "{l s='File upload is invalid' mod='ets_livechat'}";
var succesfull_noted= "{l s='Note saved' mod='ets_livechat'}";
var id_text= "{l s='ID' mod='ets_livechat'}";
var form_title_text = "{l s='Form title' mod='ets_livechat'}";
var description_text = "{l s='Description' mod='ets_livechat'}";
var action_text = "{l s='Action' mod='ets_livechat'}";
var add_auto_message_text= "{l s='Add auto message' mod='ets_livechat'}";
var edit_auto_message_text= "{l s='Edit auto message' mod='ets_livechat'}";
var add_pre_made_message_text= "{l s='Add Pre-made message' mod='ets_livechat'}";
var edit_pre_made_message_text= "{l s='Edit Pre-made message' mod='ets_livechat'}";
var add_department_text = "{l s='Add department' mod='ets_livechat'}";
var edit_staff_text = "{l s='Edit staff' mod='ets_livechat'}";
var list_staff_text = "{l s='Staffs' mod='ets_livechat'}";
var livechat_config_text ="{l s='Live Chat configuration' mod='ets_livechat'}";
var department_config_text ="{l s='Department configuration' mod='ets_livechat'}";
var staff_config_text ="{l s='Staff configuration' mod='ets_livechat'}";
var user_information_text ="{l s='User Information' mod='ets_livechat'}";
{if $converation_opened}
var converation_opened ={$converation_opened|escape:'quotes':'utf-8'};
{else}
var converation_opened='';
{/if}
var delete_text= "{l s='Delete' mod='ets_livechat'}";
var edit_text= "{l s='Edit' mod='ets_livechat'}";
var edited_text ="{l s='Edited' mod='ets_livechat'}";
var edited_at_text ="{l s='Edited at' mod='ets_livechat'}";
var editing_text ="{l s='Editing' mod='ets_livechat'}";
var hide_chat_text ="{l s='Hide this chat' mod='ets_livechat'}";
var show_chat_text= "{l s='Show this chat' mod='ets_livechat'}";
var maximize_text ="{l s='Maximize' mod='ets_livechat'}";
var minimize_text ="{l s='Minimize' mod='ets_livechat'}";
var online_text ="{l s='Online' mod='ets_livechat'}";
var busy_text ="{l s='Busy' mod='ets_livechat'}";
var invisible_text ="{l s='Invisible' mod='ets_livechat'}";
var offline_text ="{l s='Offline' mod='ets_livechat'}";
var successful_update="{l s='Configuration saved.' mod='ets_livechat'}";
var decline_text = "{l s='You declined this chat' mod='ets_livechat'}";
var is_lc_RTL ={$isRTL|intval};
var level_request =1;
var everything_text = "{l s='Everything' mod='ets_livechat'}";
var week_1_text = "{l s='1 week old' mod='ets_livechat'}";
var month_1_text ="{l s='1 month old' mod='ets_livechat'}";
var month_6_text ="{l s='6 months old' mod='ets_livechat'}";
var year_1_text = "{l s='1 year old' mod='ets_livechat'}";
var file_text = "{l s='file' mod='ets_livechat'}";
var files_text = "{l s='files' mod='ets_livechat'}";
var chatbox_changed = "{l s='Chat has been transferred. You no longer have permission to access this chat' mod='ets_livechat'}";
var invalid_file_max_size ="{l s='Attachment size exceeds the allowable limit.' mod='ets_livechat'}";
var uploading ="{l s='Uploading...' mod='ets_livechat'}";
var confirm_decline_submit ="{l s='Do you want to decline this chat?' mod='ets_livechat'}";
var create_ticket  = "{l s='was created from this chat' mod='ets_livechat'}";
var ticket_text = "{l s='Ticket' mod='ets_livechat'}";
var open_text = "{l s='Open' mod='ets_livechat'}";
var close_text = "{l s='Close' mod='ets_livechat'}";
var ets_made_messages = {$made_messages|json_encode};
</script>
{if $admin_controller}
    <script type="text/javascript" src="{$ETS_LC_MODULE_URL|escape:'html':'utf-8'}views/js/ticket.js"></script>
{/if}
{if $enable_livechat}
    <script type="text/javascript" src="{$ETS_LC_MODULE_URL|escape:'html':'utf-8'}views/js/slick.js"></script>
    <script type="text/javascript" src="{$ETS_LC_MODULE_URL|escape:'html':'utf-8'}views/js/jquery.rating.pack.js"></script>
    <script type="text/javascript" src="{$ETS_LC_MODULE_URL|escape:'html':'utf-8'}views/js/admin.js"></script>
{/if}
<script type="text/javascript">
    var link_customer_search = "{$link_customer_search|escape:'html':'UTF-8'}";
</script>
{if $id_profile!=1}
    <style>
        #subtab-AdminLiveChatHelp{
            display:none;
        }
        #subtab-AdminLiveChatDashboard{
            display:none;
        }
    </style>
{/if}
<script type="text/javascript">
$(document).ready(function(){
    $.ajax({
        url: ETS_LC_MODULE_URL_AJAX,
        type: 'post',
        dataType: 'json',
        data: {
            getTicketNoReaded: 1,
        },
        success: function(json)
        { 
            if(json.count_ticket > 0)
            {
                $('#subtab-AdminLiveChatTickets span').append('<span class="count_ticket"></span>');
                $('#subtab-AdminLiveChatTickets .count_ticket').html(json.count_ticket);
            } 
        }
    });
});
</script>