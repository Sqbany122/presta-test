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
<script type="text/javascript">
    {if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
        PrestaCafePayuAdmin.onPageLoadPs15("{$smarty.request.prestacafe_current_tab|default:'prestacafe_tab_general'|escape:'htmlall':'UTF-8'}");
    {elseif version_compare($smarty.const._PS_VERSION_, '1.7', '<')}
        PrestaCafePayuAdmin.onPageLoadPs16("{$smarty.request.prestacafe_current_tab|default:'prestacafe_tab_general'|escape:'htmlall':'UTF-8'}");
    {else}
        PrestaCafePayuAdmin.onPageLoadPs17("{$smarty.request.prestacafe_current_tab|default:'prestacafe_tab_general'|escape:'htmlall':'UTF-8'}");
    {/if}
</script>
