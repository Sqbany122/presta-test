<a href="#{$href|escape:'htmlall':'UTF-8'}" title="{$action|escape:'htmlall':'UTF-8'}" onclick="show_custom_details('{$href|escape:'htmlall':'UTF-8'}')" class="edit {$href|escape:'htmlall':'UTF-8'}">
    <i class="icon-{$icon|escape:'htmlall':'UTF-8'}"></i> {$action|escape:'htmlall':'UTF-8'}
</a>
<div style="display:none;" id="{$href|escape:'htmlall':'UTF-8'}">
    <h3>{$heading}</h3> {*Variable contains html content, escape not required*}
    {foreach $custom_detais as $key => $item}
        {foreach $item as $key_child => $item_child}
           <p>{$key_child}   -  {$item_child}</p> {*Variable contains html content, escape not required*}
        {/foreach}  
    {/foreach}  
</div>

{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
*}