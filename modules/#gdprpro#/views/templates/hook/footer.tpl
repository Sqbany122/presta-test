{*
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 *}
<div id="gdpr-modal-container" style="display: none;">
    {$langId = Context::getContext()->language->id}
    {if $popupTemplate == 'v1'}
        {include file="./template/v1.tpl"}
    {else}
        {include file="./template/v2.tpl"}
    {/if}
</div>
<div id="gdpr-consent"></div>
<style>
    {if $popupTemplate == 'v1'}
    .gdprModal .gdprModal__placeholder {
    {if isset($popupBgColor) && $popupBgColor} background-color: {$popupBgColor} !important;
    {/if} {if isset($popupPosition) && $popupPosition == 'top'} top: 0px !important;
        bottom: auto !important;
    {/if} {if isset($popupPosition) && $popupPosition == 'bottom'} top: unset !important;
        bottom: 0px !important;
    {/if}
    }

    {if isset($popupTextColor) && $popupTextColor}
    .gdpr-consent-tabs .div_control_your_privacy p,
    .gdpr-consent-tabs .div_control_your_privacy .div_text,
    .gdpr-consent-tabs .h3_popuptitle,
    .gdpr-consent-tabs .div_accept_moreinfo .span_moreinfo {
        color: {$popupTextColor} !important;
    }

    {/if}
    {else}
    .gdprModal .gdprModal__placeholder {
        background-color: transparent !important;
    {if isset($popupPosition) && $popupPosition == 'top'} top: 0px !important;
        bottom: auto !important;
    {/if}{if isset($popupPosition) && $popupPosition == 'bottom'} top: unset !important;
        bottom: 0px !important;
    {/if}
    }

    {if isset($popupBgColor) && $popupBgColor}
    .gdpr-consent-tabs {
        background-color: {$popupBgColor} !important;
    }

    {/if}
    {if isset($popupTextColor) && $popupTextColor}
    .gdpr-consent-tabs .div_control_your_privacy p,
    .gdpr-consent-tabs .div_control_your_privacy .div_text,
    .gdpr-consent-tabs .h3_popuptitle,
    .gdpr-consent-tabs .div_accept_moreinfo .span_moreinfo {
        color: {$popupTextColor} !important;
    }

    {/if}
    {/if}
</style>