{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright  2007-2015 Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
 <!-- @file modules\appagebuilder\views\templates\hook\ApLink -->
 
{if $formAtts.is_diplay == 1 && $link}
	<a href="{$link}" target="{$formAtts.target_type}">{if $formAtts.title != ''}{$formAtts.title}{else}{$link_title}{/if}</a>
{/if}