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
    <div class="alert alert-info">
        {l s='The validation URL sent to PayU for every order is shown below. Make sure it reflects the address of your store.' mod='prestacafenewpayu'}
        <br/>
        <strong>{$validation_link|escape:'htmlall':'UTF-8'}</strong>
    </div>
{else}
    <div class="alert alert-info">
        {l s='The validation URL sent to PayU for every order is shown below. Make sure it reflects the address of your store.' mod='prestacafenewpayu'}
        <br/>
        <strong>{$validation_link|escape:'htmlall':'UTF-8'}</strong>
    </div>
{/if}
