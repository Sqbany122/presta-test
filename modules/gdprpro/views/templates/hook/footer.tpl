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
        {include file="{$v_tpl_path}v1.tpl"}
    {else}
        {include file="{$v_tpl_path}v2.tpl"}
    {/if}
</div>
<a class="show-gdpr-modal" href="#"
   style="background-color: {$footerLinkBgColor}; color: {$footerLinkTextColor} !important; border-color: {$footerLinkBorderColor}">
    {$footerLinkText}
</a>
<div id="gdpr-consent"></div>
<style>
    {if $popupTemplate == 'v1'}
		.gdprModal .gdprModal__placeholder {
			{if isset($popupBgColor) && $popupBgColor}
				background-color: {$popupBgColor} !important;
			{/if}
			{if isset($popupPosition) && $popupPosition == 'top'}
				top: 0px !important;
				bottom: auto !important;
			{/if}
			{if isset($popupPosition) && $popupPosition == 'bottom'} 
				top: unset !important;
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

		.gdprModal.gdprModal--visible:after{
			content: '';
		    position: absolute;
		    width: inherit;
		    height: inherit;
		    {if isset($popupOverlayBgColor) && $popupOverlayBgColor}
		    	background: {$popupOverlayBgColor};
		    {/if}
		    {if isset($popupOverlayBgOpacity) && $popupOverlayBgOpacity}
		    	opacity: {$popupOverlayBgOpacity};
		    {else}
		    	opacity: 0;
		    {/if}
		}
		
    {else}
		.gdprModal .gdprModal__placeholder {
			background-color: transparent !important;
			{if isset($popupPosition) && $popupPosition == 'top'} 
				top: 0px !important;
				bottom: auto !important;
			{/if}
			{if isset($popupPosition) && $popupPosition == 'bottom'} 
				top: unset !important;
				bottom: 0px !important;
			{/if}
		}

		.gdprModal.gdprModal--visible:after{
			content: '';
		    position: absolute;
		    width: inherit;
		    height: inherit;
		    {if isset($popupOverlayBgColor) && $popupOverlayBgColor}
		    	background: {$popupOverlayBgColor};
		    {/if}
		    {if isset($popupOverlayBgOpacity) && $popupOverlayBgOpacity}
		    	opacity: {$popupOverlayBgOpacity};
		    {else}
		    	opacity: 0;
		    {/if}
		}
		{if isset($popupOverlayBgColor) && $popupOverlayBgColor && isset($popupOverlayBgOpacity) && $popupOverlayBgOpacity}
		.gdprModal.gdprModal--visible{
			height: inherit !important;
		}
		{/if}
		

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

    {if isset($popupPosition) && $popupPosition == 'middle'}
		.gdprModal__placeholder {
			top: 50% !important;
			bottom: initial !important;
			left: 50% !important;
			width: 780px !important;
			-webkit-transform: scale(1) translate(-50%, -50%) !important;
			transform: scale(1) translate(-50%, -50%) !important;
			
		    max-height: 90vh;
			overflow: auto;			
		}
    {/if}
</style>