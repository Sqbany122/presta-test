<table align="center" border="0" bgcolor="#E1E1E1" class="mlContentTable" cellspacing="0" cellpadding="0"
    style="min-width: 640px; width: 640px;" width="640" id="ml-block-55422691">
    <tbody>
        <tr>
            <td>
                <table width="640" class="mlContentTable" bgcolor="white" cellspacing="0" cellpadding="0" border="0"
                    align="center" style="width: 640px;">
                    <tbody>
                        <tr>
                            <td align="left" class="mlContentContainer"
                                style="padding: 5px 50px 0px 50px; font-family: Helvetica; font-size: 14px; color: #000000; line-height: 23px;">

                                <hr>
                                <div
                                    style="color: #E91E63;display: block;font-size: 20px;text-align: center;margin: 0px auto;padding: 5px;line-height: 30px">
                                    <strong>{$kb_heading|escape:'quotes':'UTF-8'}</strong>
                                    <hr>
                                    <ul style="list-style-type: none;padding:0px;overflow:auto;">
                                        {foreach $kb_product as $key => $value}
                                            <li style='width:44%;float:left;margin-bottom:2%;margin-right:2%;'>
                                                <div style='text-align:center;border:1px solid gray;'><a
                                                        href="{$value['kb_product_link_new']}"><img style='width:100%'
                                                            src="{$value['image']}"></a></div>
                                                {* variable contains url content, can not escape *}
                                                <div style='text-align: center;margin: 5% auto;font-size: 16px;
                        font-weight: bolder;max-height:20px;text-overflow: ellipsis;
                        white-space:nowrap;overflow:hidden;line-height: initial;'><span>{$value['name']|escape:'quotes':'UTF-8'}</span></div>
                                                <div style='text-align: center;margin: 5% auto;font-size: 16px;
                        font-weight: bolder;max-height:20px;text-overflow: ellipsis;
                        white-space:nowrap;overflow:hidden;line-height: initial;'><span>{$value['price']|escape:'quotes':'UTF-8'}</span>
                                                </div>
                                            {/foreach}
                                    </ul>
                                    <hr>
                                    <div style="float:right;{*background: #F44336;*}
                        color: #fff;display: block;font-size: 150%;
                        text-align: center;margin: 0px auto;padding: 1%;line-height: 30px"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
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