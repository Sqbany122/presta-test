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
    var current_tab_active ="{$current_tab_active|escape:'html':'utf-8'}";
    var lc_default_lang ={$lc_default_lang|intval};
    var PS_ALLOW_ACCENTED_CHARS_URL="{$PS_ALLOW_ACCENTED_CHARS_URL|intval}";
    $('#subtab-AdminLiveChatSettings').addClass('-active').addClass('active');
</script>
<script type="text/javascript" src="{$ETS_LC_MODULE_URL|escape:'html':'utf-8'}views/js/slick.js"></script>
{if $ps15}
    <script type="text/javascript" src="{$ETS_LC_MODULE_URL|escape:'html':'utf-8'}views/js/livechat.admin15.js"></script>
{else}
    <script type="text/javascript" src="{$ETS_LC_MODULE_URL|escape:'html':'utf-8'}views/js/livechat.admin.js"></script>
{/if}
<audio id="lg_ets_sound1">
  <source src="{$ETS_LC_MODULE_URL|escape:'quotes':'UTF-8'}/views/sound/sound1.mp3" type="audio/mpeg" />
</audio> 
<audio id="lg_ets_sound2">
  <source src="{$ETS_LC_MODULE_URL|escape:'quotes':'UTF-8'}/views/sound/sound2.mp3" type="audio/mpeg" />
</audio>
<audio id="lg_ets_sound3">
  <source src="{$ETS_LC_MODULE_URL|escape:'quotes':'UTF-8'}/views/sound/sound3.mp3" type="audio/mpeg" />
</audio>
<audio id="lg_ets_sound4">
  <source src="{$ETS_LC_MODULE_URL|escape:'quotes':'UTF-8'}/views/sound/sound4.mp3" type="audio/mpeg" />
</audio>
<audio id="lg_ets_sound5">
  <source src="{$ETS_LC_MODULE_URL|escape:'quotes':'UTF-8'}/views/sound/sound5.mp3" type="audio/mpeg" />
</audio>
<audio id="lg_ets_sound6">
  <source src="{$ETS_LC_MODULE_URL|escape:'quotes':'UTF-8'}/views/sound/sound6.mp3" type="audio/mpeg" />
</audio>
<audio id="lg_ets_sound7">
  <source src="{$ETS_LC_MODULE_URL|escape:'quotes':'UTF-8'}/views/sound/sound7.mp3" type="audio/mpeg" />
</audio>
<audio id="lg_ets_sound8">
  <source src="{$ETS_LC_MODULE_URL|escape:'quotes':'UTF-8'}/views/sound/sound8.mp3" type="audio/mpeg" />
</audio>
