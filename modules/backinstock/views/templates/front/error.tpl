{capture name=path}{l s='Product Update Error' mod='backinstock'}{/capture}

<div class="alert alert-danger">
    
    <p>{l s='There is 1 error.' mod='backinstock'}</p>
    <ol>
        <li>{l s='Failed to unsubscribe.' mod='backinstock'}</li>
    </ol>
    <p><a href={$acc_link|escape:'htmlall':'UTF-8'} title="Back" style="color: white;">« {l s='Go to Home Page' mod='backinstock'}</a></p>
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
* @author    velsof.com <support@velsof.com>
* @copyright 2014 Velocity Software Solutions Pvt Ltd
* @license   see file: LICENSE.txt
*
* Description
*
* Price Alert Error Page
*}
