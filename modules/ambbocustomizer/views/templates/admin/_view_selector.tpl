 {*
 *    ambBOcustomizer Module : customize the prestashop back-office
 *
 *    @module    BO Customizer (AmbBoCustomizer)
 *    @file      views/templates/admin/_view_selector.tpl
 *    @subject   Shows a view selector on list pages
 *    @author    Ambris Informatique
 *    @copyright Copyright (c) 2013-2015 Ambris Informatique SARL
 *    @license   Commercial license
 *
 *    Support by mail: support@ambris.com
 *}

<div class="{if $compat}compat{/if}" id="view_selector" >
        {if !$compat}<label for="amb_customizer_view_id" style="float:left;margin-top:7px;margin-right:7px;">{/if}{l s='Current view : ' mod='ambbocustomizer'} {if !$compat}</label>{/if}
            <select id="amb_customizer_view" data-url="{$url|escape:'quotes':'UTF-8'}" name="amb_customizer_view_id" class="fixed-width-lg view_selector_list">
                {foreach from=$view_names key=view_name item=display_name}
                   <option value="{$view_name|escape:'htmlall':'UTF-8'}" {if $current_view_name==$view_name}selected="selected"{/if}>{$display_name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
</div>