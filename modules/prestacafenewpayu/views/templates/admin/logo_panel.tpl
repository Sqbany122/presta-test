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
{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
    <fieldset>
        <legend>PrestaCafé</legend>
        <p>
            {l s='PayU integration from PrestaCafé.' mod='prestacafenewpayu'}
            <strong><a href="http://addons.prestashop.com/pl/2_community-developer?contributor=140043" target="_blank">{l s='See more of our modules on Addons Marketplace' mod='prestacafenewpayu'}</a></strong>
        </p>
        <p>
            {l s='If you need support, please' mod='prestacafenewpayu'} <a href="{$support_link|escape:'html':'UTF-8'}" target="_blank">{l s='click here' mod='prestacafenewpayu'}</a>.
        </p>
    </fieldset>
    <br/>
{else}
    <div class="bootstrap panel">
        <h3><i class="icon-cogs"></i> PrestaCafé</h3>
        <p>
            {l s='PayU integration from PrestaCafé.' mod='prestacafenewpayu'}
            <strong><a href="http://addons.prestashop.com/pl/2_community-developer?contributor=140043" target="_blank">{l s='See more of our modules on Addons Marketplace' mod='prestacafenewpayu'}</a></strong>
        </p>
        <p>
            {l s='If you need support, please' mod='prestacafenewpayu'} <a href="{$support_link|escape:'html':'UTF-8'}" target="_blank">{l s='click here' mod='prestacafenewpayu'}</a>.
        </p>
    </div>
{/if}
