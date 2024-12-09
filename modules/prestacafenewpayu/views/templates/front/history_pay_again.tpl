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
    <a class="color-myaccount"
       href="{$link->getModuleLink('prestacafenewpayu', 'payagain', ['secure_key_hash' => $secure_key_hash, 'secure_token' => $secure_token, 'id_cart' => $id_cart], true)|escape:'html':'UTF-8'}">
        {l s='Pay again in PayU' mod='prestacafenewpayu'}
    </a>
{elseif version_compare($smarty.const._PS_VERSION_, '1.7', '<')}
    <a class="btn btn-default button button-small"
       href="{$link->getModuleLink('prestacafenewpayu', 'payagain', ['secure_key_hash' => $secure_key_hash, 'secure_token' => $secure_token, 'id_cart' => $id_cart], true)|escape:'html':'UTF-8'}">
        <span>
            {l s='Pay again in PayU' mod='prestacafenewpayu'}<i class="icon-chevron-right right"></i>
        </span>
    </a>
{else}{* 1.7 *}
    <a href="{$link->getModuleLink('prestacafenewpayu', 'payagain', ['secure_key_hash' => $secure_key_hash, 'secure_token' => $secure_token, 'id_cart' => $id_cart], true)|escape:'html':'UTF-8'}">
        {l s='Pay again in PayU' mod='prestacafenewpayu'}
    </a>
{/if}