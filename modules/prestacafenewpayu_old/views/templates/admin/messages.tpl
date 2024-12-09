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
{if $errors && count($errors)}
    <div class="alert alert-danger">
        <h4>{l s='Error' mod='prestacafenewpayu'}</h4>
        <ul class="list-unstyled">
            {foreach from=$errors item='msg'}
                <li>{$msg|escape:'htmlall':'UTF-8'}</li>
            {/foreach}
        </ul>
    </div>
{/if}
{if $warnings && count($warnings)}
    <div class="alert alert-warning">
        <ul class="list-unstyled">
            {foreach from=$warnings item='msg'}
                <li>{$msg|escape:'htmlall':'UTF-8'}</li>
            {/foreach}
        </ul>
    </div>
{/if}
{if $messages && count($messages)}
    <div class="{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}module_confirmation conf confirm{else}alert alert-success{/if}">
        <ul class="list-unstyled">
            {foreach from=$messages item='msg'}
                <li>{$msg|escape:'htmlall':'UTF-8'}</li>
            {/foreach}
        </ul>
    </div>
{/if}