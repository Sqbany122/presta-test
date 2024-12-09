{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}

<div id='information-accesspoint' name='information-accesspoint'>
    {block name='apchild'}
        {foreach from=$Infor item=elementInfor}
            {block name='apchild'}
                <div class="form-group row mb-5">
                    <div class="col-lg-8 col-md-12 card-body pb00" id="addressAccessPoint">
                        <strong>
                            {$elementInfor.indexArrayListInforAP|escape:'htmlall':'UTF-8'}. {$elementInfor.name|escape:'htmlall':'UTF-8'}
                        </strong>
                        <br />
                        {$elementInfor.address|escape:'htmlall':'UTF-8'}
                        <br />
                        {$elementInfor.txtE_ShoppingAPOperating|escape:'htmlall':'UTF-8'}:
                        <br />
                        <table class="table-time">
                            <tr>
                                <th></th>
                                <th>{$elementInfor.txtE_ShoppingAPopen|escape:'htmlall':'UTF-8'}</th>
                                <th>{$elementInfor.txtE_ShoppingAPclose|escape:'htmlall':'UTF-8'}</th>
                            </tr>
                            {foreach from=$elementInfor.operatingHours item=elementTime}
                                {foreach from=$elementTime.timeOpen key=index item=time}
                                <tr>
                                    <td>{if $index==0}{$elementTime.dayOfWeek|escape:'htmlall':'UTF-8'}{/if}</td>
                                    <td>{$time|escape:'htmlall':'UTF-8'}</td>
                                    {if isset($elementTime.timeClose)}
                                    <td>{$elementTime.timeClose[$index]|escape:'htmlall':'UTF-8'}</td>
                                    {/if}
                                </tr>
                                {/foreach}
                            {/foreach}
                        </table>
                    </div>
                        <br />
                        <br />
                    <div class="col-lg-4 col-md-12 text-center">
                        <i class="fas fa-map-marker-alt ups-marker">
                        </i>
                        <br>
                        <span id="nearbySpace">{$elementInfor.distance|escape:'htmlall':'UTF-8'} {$elementInfor.unit|escape:'htmlall':'UTF-8'}</span>
                        <button type="button" onclick="selectAddressButton('{$elementInfor.indexArrayListInforAP|escape:'htmlall':'UTF-8'}', '{$Infor|count}');" id="btn_select_{$elementInfor.indexArrayListInforAP|escape:'htmlall':'UTF-8'}" class="btn btn-outline-dark my-0 ml-0 btn-block btn_select">{$elementInfor.txtSelect|escape:'htmlall':'UTF-8'}</button>
                    </div>
                </div>
            {/block}
        {/foreach}
    {/block}
</div>
