{**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2015 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *}
{if count($best_seller_1) > 0}

<table align="center" border="0" bgcolor="#E1E1E1" class="mlContentTable" cellspacing="0" cellpadding="0" style="background: #E1E1E1; min-width: 640px; width: 640px;" width="640" id="ml-block-55422691">
    <tbody>
        <tr>
            <td>
                <table width="640" class="mlContentTable" bgcolor="#E1E1E1" cellspacing="0" cellpadding="0" border="0" align="center" style="background: #E1E1E1; width: 640px;"> 
                    <tbody>
                        <tr>
                            <td align="left" class="mlContentContainer" style="padding: 5px 50px 0px 50px; font-family: Helvetica; font-size: 14px; color: #000000; line-height: 23px;">
                                <p style="margin: 0px 0px 10px 0px;        line-height: 23px;text-align: center;">
                                    <strong>
                                        {l s='Check out these top picks' mod='backinstock'}
                                    </strong>
                                </p>
                            </td> 
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table> 


<table align="center" border="0" bgcolor="#FFFFFF" class="mlContentTable" cellspacing="0" cellpadding="0" style="background: #FFFFFF; min-width: 640px; width: 640px;" width="640" id="ml-block-55422685">
    <tbody>
        <tr>
            <td>
                <table width="640" class="mlContentTable" bgcolor="#FFFFFF" cellspacing="0" cellpadding="0" border="0" align="center" style="background: #FFFFFF; width: 640px;">
                    <tbody>
                        <tr>
                            <td class="mlContentContainer" style="padding: 0px 50px 0px 50px;"> 
                                <table border="0" cellpadding="0" cellspacing="0" width="100%"> 	
                                    <tbody>
                                        <tr>
                                            <td valign="top"> 
                                                {foreach $best_seller_1 as $seller}
                                                <table class="mlContentBlock" width="255" cellspacing="0" cellpadding="0" border="0" align="left" style="width: 255px;">    
                                                    <tbody>
                                                        <tr>
                                                            <td width="100%" height="15"></td>
                                                        </tr> 
                                                        <tr>
                                                            <td class="mlContentImage" style="font-family: Helvetica; font-size: 14px; color: #404040;"> 
                                                                <a href="{$seller['link']}" title="{$seller['name']}"> 
                                                                    <img border="0" src="{$link->getImageLink($seller['link_rewrite'], $seller['id_image'], 'home_default')}" width="255" height="245" alt="{$seller['name']}" align="2" style="display: block;"> 
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="15"></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="left" class="image-caption-content" style="font-family: Helvetica; font-size: 14px; color: #404040; line-height: 23px;">    
                                                                <p style="margin: 0px 0px 10px 0px;line-height: 23px;text-align: center;">
                                                                    <strong>
                                                                        
                                                                        {$currency_sign} {$seller['price_without_reduction']}<br>{*Variable contains html content, escape not required*}
                                                                    </strong>
                                                                        <a href="{$seller['link']}" title="{$seller['name']}"> {$seller['name']}</a>{*Variable contains html content, escape not required*}
                                                                </p>   
                                                            </td>     
                                                        </tr>                       
                                                        <tr>                 
                                                            <td class="image-caption-bottom-gap" width="100%" height="5"></td>   
                                                        </tr>           
                                                    </tbody>
                                                </table> 
                                                    {/foreach}    
                                            </td> 	
                                        </tr> 	   
                                    </tbody>
                                </table>  
                            </td>     
                        </tr> 
                    </tbody>
                </table> 
            </td>   
        </tr>
    </tbody>
</table> 



<table align="center" border="0" bgcolor="#FFFFFF" class="mlContentTable" cellspacing="0" cellpadding="0" style="background: #FFFFFF; min-width: 640px; width: 640px;" width="640" id="ml-block-55422689">
    <tbody><tr>
            <td>
                <table width="640" class="mlContentTable" bgcolor="#FFFFFF" cellspacing="0" cellpadding="0" border="0" align="center" style="background: #FFFFFF; width: 640px;">
                    <tbody><tr>
                            <td style="padding: 15px 0px 0px 0px;">
                                <table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-top: 1px solid #d8d8d8;">
                                    <tbody><tr>
                                            <td width="100%" height="15px"></td>
                                        </tr>
                                    </tbody></table>
                            </td>
                        </tr>
                    </tbody></table>
            </td>
        </tr>
    </tbody>
</table>
{/if}




{if count($best_seller_2) > 0}
<table align="center" border="0" bgcolor="#FFFFFF" class="mlContentTable" cellspacing="0" cellpadding="0" style="background: #FFFFFF; min-width: 640px; width: 640px;" width="640" id="ml-block-55422685">
    <tbody>
        <tr>
            <td>
                <table width="640" class="mlContentTable" bgcolor="#FFFFFF" cellspacing="0" cellpadding="0" border="0" align="center" style="background: #FFFFFF; width: 640px;">
                    <tbody>
                        <tr>
                            <td class="mlContentContainer" style="padding: 0px 50px 0px 50px;"> 
                                <table border="0" cellpadding="0" cellspacing="0" width="100%"> 	
                                    <tbody>
                                        <tr>
                                            <td valign="top"> 
                                                {foreach $best_seller_2 as $seller}
                                                <table class="mlContentBlock" width="255" cellspacing="0" cellpadding="0" border="0" align="left" style="width: 255px;">    
                                                    <tbody>
                                                        <tr>
                                                            <td width="100%" height="15"></td>
                                                        </tr> 
                                                        <tr>
                                                            <td class="mlContentImage" style="font-family: Helvetica; font-size: 14px; color: #404040;">
                                                                <a href="{$seller['link']}" title="{$seller['name']}"> {*Variable contains html content, escape not required*}
                                                                    <img border="0" src="{$link->getImageLink($seller['link_rewrite'], $seller['id_image'], 'home_default')}" width="255" height="245" alt="" align="2" style="display: block;">{*Variable contains html content, escape not required*}   
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="15"></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="left" class="image-caption-content" style="font-family: Helvetica; font-size: 14px; color: #404040; line-height: 23px;">    
                                                                <p style="margin: 0px 0px 10px 0px;line-height: 23px;text-align: center;">
                                                                    <strong>
                                                                        
                                                                        {$currency_sign} {$seller['price_without_reduction']}<br>{*Variable contains html content, escape not required*}
                                                                    </strong>
                                                                   <a href="{$seller['link']}" title="{$seller['name']}"> {$seller['name']}</a>{*Variable contains html content, escape not required*}
                                                                </p>   
                                                            </td>     
                                                        </tr>                       
                                                        <tr>                 
                                                            <td class="image-caption-bottom-gap" width="100%" height="5"></td>   
                                                        </tr>           
                                                    </tbody>
                                                </table> 
                                                    {/foreach}    
                                            </td> 	
                                        </tr> 	   
                                    </tbody>
                                </table>  
                            </td>     
                        </tr> 
                    </tbody>
                </table> 
            </td>   
        </tr>
    </tbody>
</table> 
                                    
<table align="center" border="0" bgcolor="#FFFFFF" class="mlContentTable" cellspacing="0" cellpadding="0" style="background: #FFFFFF; min-width: 640px; width: 640px;" width="640" id="ml-block-55422705">
    <tbody><tr>
            <td>
                <table width="640" class="mlContentTable" bgcolor="#FFFFFF" cellspacing="0" cellpadding="0" border="0" align="center" style="background: #FFFFFF; width: 640px;">
                    <tbody><tr>
                            <td style="padding: 15px 0px 0px 0px;">
                                <table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-top: 1px solid #d8d8d8;">
                                    <tbody><tr>
                                            <td width="100%" height="15px"></td>
                                        </tr>
                                    </tbody></table>
                            </td>
                        </tr>
                    </tbody></table>
            </td>
        </tr>
    </tbody>
</table>
{/if}