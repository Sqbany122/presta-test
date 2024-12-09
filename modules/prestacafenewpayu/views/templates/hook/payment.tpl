{*
 * 2009-2017 Blue Train Lukasz Kowalczyk / PrestaCafe
 *
 * NOTICE OF LICENSE
 *
 * Lukasz Kowalczyk is the owner of the copyright of this module.
 * All rights of any kind, which are not expressly granted in this
 * License, are entirely and exclusively reserved to and by Lukasz
 * Kowalczyk.
 *
 * You may not rent, lease, transfer, modify or create derivative
 * works based on this module.
 *
 * @author    Lukasz Kowalczyk <lkowalczyk@prestacafe.pl>
 * @copyright Lukasz Kowalczyk
 * @license   LICENSE.txt
*}
{assign var=account_payment_link value=$link->getModuleLink('prestacafenewpayu', 'payment', [], true)}
{assign var=card_payment_link value=$link->getModuleLink('prestacafenewpayu', 'payment', ['pbl' => 'c'], true)}
{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
    {* PayU payment *}
    {if $show_basic_payment}
        <p class="payment_module prestacafenewpayu_payment_module" id="prestacafenewpayu_payment_button">
            <a title="{l s='Pay in PayU' mod='prestacafenewpayu'}" class="prestacafenewpayu"
                {if $disable_javascript_payment_block}
                    href="{$account_payment_link|escape:'htmlall':'UTF-8'}">
                {else}
                    onclick="PrestaCafePayu.paymentRedirect(event || window.event, this, '{$account_payment_link|escape:'javascript':'UTF-8'}');"
                    href="#">
                {/if}
                <img src="{$module_img_dir|escape:'htmlall':'UTF-8'}payment_img.png" width="86" height="49" />
                {l s='Pay in PayU' mod='prestacafenewpayu'} <span>{l s='with quick transfer or by card' mod='prestacafenewpayu'}</span>
                {if $surcharge > 0}
                    <br /><strong>{l s='Additional surcharge:' mod='prestacafenewpayu'} {convertPriceWithCurrency price=$surcharge currency=$currency}</strong>
                {/if}
            </a>
        </p>
    {/if}

    {* Direct card payment *}
    {if $show_direct_card}
        <p class="payment_module prestacafenewpayu_payment_module" id="prestacafenewpayu_direct_card_payment_button">
            <a title="{l s='Pay in PayU' mod='prestacafenewpayu'}" class="prestacafenewpayu"
                {if $disable_javascript_payment_block}
                    href="{$card_payment_link|escape:'htmlall':'UTF-8'}">
                {else}
                    onclick="PrestaCafePayu.paymentRedirect(event || window.event, this, '{$card_payment_link|escape:'javascript':'UTF-8'}');"
                    href="#">
                {/if}
                <img src="{$module_img_dir|escape:'htmlall':'UTF-8'}payment_card_img.png" width="86" height="49" />
                {l s='Pay by card' mod='prestacafenewpayu'} <span>{l s='in PayU' mod='prestacafenewpayu'}</span>
                {if $surcharge > 0}
                    <br /><strong>{l s='Additional surcharge:' mod='prestacafenewpayu'} {convertPriceWithCurrency price=$surcharge currency=$currency}</strong>
                {/if}
            </a>
        </p>
    {/if}
{else}
    {* PayU payment *}
    {if $show_basic_payment}
        <div class="row">
            <div class="col-xs-12">
                <p class="payment_module prestacafenewpayu_payment_module" id="prestacafenewpayu_payment_button">
                    <a class="prestacafenewpayu"
                        {if $disable_javascript_payment_block}
                            href="{$account_payment_link|escape:'htmlall':'UTF-8'}" title="{l s='Pay in PayU' mod='prestacafenewpayu'}">
                        {else}
                            onclick="PrestaCafePayu.paymentRedirect(event || window.event, this, '{$account_payment_link|escape:'javascript':'UTF-8'}');"
                            href="#">
                        {/if}
                        {l s='Pay in PayU' mod='prestacafenewpayu'} <span>{l s='with quick transfer or by card' mod='prestacafenewpayu'}</span>
                        {if $surcharge > 0}
                            <br /><strong>{l s='Additional surcharge:' mod='prestacafenewpayu'} {convertPriceWithCurrency price=$surcharge currency=$currency}</strong>
                        {/if}
                    </a>
                </p>
            </div>
        </div>
    {/if}

    {* Direct card payment *}
    {if $show_direct_card}
        <div class="row">
            <div class="col-xs-12">
                <p class="payment_module prestacafenewpayu_payment_module" id="prestacafenewpayu_direct_card_payment_button">
                    <a class="prestacafenewpayu"
                        {if $disable_javascript_payment_block}
                            href="{$card_payment_link|escape:'htmlall':'UTF-8'}" title="{l s='Pay in PayU' mod='prestacafenewpayu'}">
                        {else}
                            onclick="PrestaCafePayu.paymentRedirect(event || window.event, this, '{$card_payment_link|escape:'javascript':'UTF-8'}');"
                            href="#">
                        {/if}
                        {l s='Pay by card' mod='prestacafenewpayu'} <span>{l s='in PayU' mod='prestacafenewpayu'}</span>
                        {if $surcharge > 0}
                            <br /><strong>{l s='Additional surcharge:' mod='prestacafenewpayu'} {convertPriceWithCurrency price=$surcharge currency=$currency}</strong>
                        {/if}
                    </a>
                </p>
            </div>
        </div>
    {/if}
{/if}
