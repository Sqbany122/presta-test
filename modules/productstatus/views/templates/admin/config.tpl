{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
 *  @author Andreika
 *  @copyright  Andreika
 *  @version  2.8.5
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{*<pre>{$statuses|print_r}</pre>*}
<form action="" method="post">

    <div class="panel">
        <h3><i class="icon-cogs"></i>{l s='Configure first product statuses' mod='productstatus'}</h3>
        <div class="row">
            <div class="col-lg-5">
                <div class="panel">
                    <div class="panel-heading">{l s='OUT OF STOCK status' mod='productstatus'}</div>

                        <select name="outofstock_status">
                            <option value="0">--</option>
                            {foreach $statuses as $status}
                                <option value="{$status.id_order_state|escape:'htmlall':'UTF-8'}" {if $settings.out_of_stock_status == $status.id_order_state}selected="selected"{/if}>{$status.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5">

                <div class="panel">
                    <div class="panel-heading">{l s='IN STOCK status' mod='productstatus'}</div>
                    <select name="instock_status">
                        <option value="0">--</option>
                        {foreach $statuses as $status}
                            <option value="{$status.id_order_state|escape:'htmlall':'UTF-8'}" {if $settings.in_stock_status == $status.id_order_state}selected="selected"{/if}>{$status.name|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>

            </div>
        </div>
        <div class="clearfix"></div>

        <div class="panel-footer">
            <button class="btn btn-default pull-right" name="configureSave" type="submit">
                <i class="process-icon-save"></i>
                Save
            </button>
        </div>
    </div>

</form>