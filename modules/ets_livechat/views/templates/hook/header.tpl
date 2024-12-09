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
{if isset($assigns) && $assigns}
<script type="text/javascript">
    {foreach from=$assigns key='vairableName' item='val'}
        var {$vairableName|escape:'html':'UTF-8'} = {if $vairableName=='ETS_LC_URL_AJAX' || $vairableName=='ETS_LC_URL_OAUTH' || $vairableName=='ETS_LC_TEXT_SEND_OffLINE' || $vairableName=='ETS_LC_TEXT_SEND' || $vairableName=='ETS_LC_TEXT_BUTTON_EDIT' || $vairableName=='ETS_LC_AVATAR_IMAGE_TYPE' || $vairableName=='ETS_LC_TEXT_SEND_START_CHAT'}'{$val nofilter}'{else}{$val|intval}{/if};
    {/foreach}
    var isRequestAjax ='{$isRequestAjax|intval}';
</script>
{/if}
<script type="text/javascript">
    var delete_text= "{l s='Delete' mod='ets_livechat'}";
    var edit_text= "{l s='Edit' mod='ets_livechat'}";
    var edited_text ="{l s='Edited' mod='ets_livechat'}";
    var edited_at_text ="{l s='Edited at:' mod='ets_livechat'}";
    var editing_text ="{l s='Editing' mod='ets_livechat'}";
    var online_text ="{l s='Online' mod='ets_livechat'}";
    var busy_text ="{l s='Busy' mod='ets_livechat'}";
    var invisible_text ="{l s='Offline' mod='ets_livechat'}";
    var offline_text ="{l s='Offline' mod='ets_livechat'}";
    var disable_sound_text ="{l s='Disable sound' mod='ets_livechat'}";
    var enable_sound_text ="{l s='Enable sound' mod='ets_livechat'}";
    var maximize_text ="{l s='Maximize' mod='ets_livechat'}";
    var minimize_text ="{l s='Minimize' mod='ets_livechat'}";
    var text_admin_busy="{l s='Sorry. All staffs are busy at the moment. Please come back later or' mod='ets_livechat'}";
    var text_customer_end_chat ="{l s='Click here to end chat' mod='ets_livechat'}";
    var ets_livechat_invalid_file ="{l s='File upload is invalid' mod='ets_livechat'}";
    var invalid_file_max_size ="{l s='Attachment size exceeds the allowable limit.' mod='ets_livechat'}";
    var uploading ="{l s='Uploading...' mod='ets_livechat'}";
    var show_text = "{l s='Show chat window' mod='ets_livechat'}";
</script>