
    {if isset($is_subscription_page) && $is_subscription_page == 1}
    <li class="lnk_wishlist">
        <a title="{l s='Click to visit out of stock subscription list.' mod='backinstock'}" href="{$subscription_list_link|escape:'htmlall':'UTF-8'}">
            <i class="icon-bell"></i> {* Variable contains HTML/CSS/JSON, escape not required *}
            <span>  {l s='Out Of Stock Subscriptions' mod='backinstock'}</span>
            
        </a>
    </li>
    {/if}
    
    
{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    velsof.com <support@velsof.com>
* @copyright 2014 Velocity Software Solutions Pvt Ltd
* @license   see file: LICENSE.txt
*
* Description
*
* Product Update Block Page
*}
