{*
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 *}
{capture name="unusedTranslationStuff"}
    {l s='Necessary' mod='gdprpro'}
    {l s='Preferences' mod='gdprpro'}
    {l s='Statistics' mod='gdprpro'}
    {l s='Marketing' mod='gdprpro'}
    {l s='Unclassified' mod='gdprpro'}
{/capture}
    <div class="gdpr-consent-tabs">

        <div class="div_control_your_privacy">
            {if $showPopupTitle}
                <h3 class="h3_popuptitle">{$welcomeTabTitle}</h3>
            {/if}
            
            <div class="div_text">
                {$welcomeTabText nofilter} {* HTML comment, no escape necessary *}
            </div>
        </div>

        <div class="div_summary_checkboxes">

            {foreach GdprPro::$cookieCategories as $cookie_Category}
                {$cookies_InCategory = 0}
                {foreach $modules_to_unload as $name => $module}
                    {if isset($module.enabled) && $module.enabled == 1 && isset($module.category) && $module.category == $cookie_Category}
                        {$cookies_InCategory =$cookies_InCategory+1}
                    {/if}
                {/foreach}
                {if $cookies_InCategory > 0}
                    <div id="div_{$cookie_Category}" class="div_cookie_category">
                            <span class="span-{$cookie_Category} {if $cookie_Category == 'necessary'}necessary{/if}">
                                    {l s=ucfirst($cookie_Category) mod='gdprpro'}
                            </span>
                    </div>
                {/if}
            {/foreach}
            {if isset($under16Enable) && $under16Enable}
                {include file="./under-16.tpl"}
            {/if}
            {if isset($under18Enable) && $under18Enable}
                {include file="./under-18.tpl"}
            {/if}
        </div>
        <div class="div_hide_show">
            <span class="hide_details">{l s='Hide details' mod='gdprpro'}</span>
            <span class="show_details">{l s='Show details' mod='gdprpro'}</span>
        </div>
        <div class="div_top_buttons">
            <footer>
                <label>
                    <span id="gdpr-selected-count">0</span>
                    /
                    <span id="gdpr-available-count">
							{$modules_to_unload_count}
						</span>
                    {l s='selected' mod='gdprpro'}
                </label>
                <button type="button" id="accept-all-gdpr"
                        style="background: {if isset($acceptAllBtnBgColor) && $acceptAllBtnBgColor}{$acceptAllBtnBgColor}{else}green{/if}; color:{if isset($acceptAllBtnTextColor) && $acceptAllBtnTextColor}{$acceptAllBtnTextColor}{else}white{/if};">
                    {l s='Accept all' mod='gdprpro'}
                </button>
                {if $rejectAllBtnShow == '1'}
                    <button type="button" id="reject-all-gdpr"
                            style="background: {if isset($rejectAllBtnBgColor) && $rejectAllBtnBgColor}{$rejectAllBtnBgColor}{else}green{/if}; color:{if isset($rejectAllBtnTextColor) && $rejectAllBtnTextColor}{$rejectAllBtnTextColor}{else}white{/if};">
                        {l s='Reject all' mod='gdprpro'}
                    </button>
                {/if}
                <button type="button" id="close-gdpr-consent"
                        style="background: {if isset($saveBtnBgColor) && $saveBtnBgColor}{$saveBtnBgColor}{else}green{/if}; color:{if isset($saveBtnTextColor) && $saveBtnTextColor}{$saveBtnTextColor}{else}white{/if};"
                >
                    {l s='Save' mod='gdprpro'}
                </button>
            </footer>
        </div>
        <div class="div_center_area">
            <nav>
                <ul class="gdpr-consent-tabs-navigation">
                    {foreach GdprPro::$cookieCategories as $cookieCategory}
                        {$cookieCategoryTabContent = ""}
                        {$cookiesInCategory = 0}
                        {foreach $modules_to_unload as $name => $module}
                            {if isset($module.enabled) && $module.enabled == 1 && isset($module.category) && $module.category == $cookieCategory}
                                {$cookiesInCategory =$cookiesInCategory+1}
                            {/if}
                        {/foreach}
                        {if $cookiesInCategory > 0}
                            <li class="{$cookieCategory}-tab-menu cookie-category-side-menu">
                                <a data-content="{$cookieCategory}-cookies-tab"
                                   {if $cookieCategory == 'necessary'}class="selected"{/if}>
										<span>
											{l s=ucfirst($cookieCategory) mod='gdprpro'} ({$cookiesInCategory})
										</span>
                                </a>
                            </li>
                        {/if}
                    {/foreach}
                    <li>
                        <a data-content="store" href="{$tabContentLink}" target="_blank">
                            {$tabNameLink}
                        </a>
                    </li>
                </ul>
            </nav>

            <ul class="gdpr-consent-tabs-content">
                {foreach GdprPro::$cookieCategories as $cookieCategory}
                    {$cookieCategoryTabContent = ""}
                    {$cookiesInCategory = 0}
                    {capture name="cookieCategoryTab"}
                        <li data-content="{$cookieCategory}-cookies-tab"
                            class="div_{$cookieCategory} cookie-category-tab {if $cookieCategory == 'necessary'}selected{/if}">
                            <h3>
                                {l s=ucfirst($cookieCategory) mod='gdprpro'}
                            </h3>
                            <div class="div_text">
                                <p class="cookie_cat_description">
                                    {$cookieCategoryDescriptions.$cookieCategory nofilter} {* HTML comment, no escape necessary *}
                                </p>
                                <table class="table table-bordered table-responsive">
                                    <thead>
                                    <tr>
                                        <th>{l s='Name' mod='gdprpro'}</th>
                                        <th>{l s='Provider' mod='gdprpro'}</th>
                                        <th>{l s='What it does' mod='gdprpro'}</th>
                                        <th>{l s='Expiry' mod='gdprpro'}</th>
                                        <th>
                                            {l s='Allow' mod='gdprpro'}
                                            <small id="gdpr-check-all-modules">{l s='(Check all)' mod='gdprpro'}</small>
                                        </th>
                                    </tr>
                                    </thead>
                                    {foreach $modules_to_unload as $name => $module}
                                        {if isset($module.enabled) && $module.enabled == 1 && isset($module.category) && $module.category == $cookieCategory}
                                            {$cookiesInCategory =$cookiesInCategory+1}
                                            <tr id="module_{md5($name)}">
                                                <td class="td_name">
                                                    {$module.frontend_name[$langId]}
                                                </td>
                                                <td class="td_provider">{$module.provider[$langId]}</td>
                                                <td class="td_description">
                                                    <span class="tooltiptext">{$module.description[$langId]}</span>
                                                    <span class="description">{$module.description[$langId]}</span>
                                                </td>
                                                <td class="td_expiry">{$module.expiry[$langId]}</td>
                                                <td class="td_checkbox">
                                                    <input type="checkbox" id="module-{$name}-chkbox" name="{$name}"
                                                           class="module-cookies-chkbox {if $cookieCategory == 'necessary' }necessary{/if}"
                                                           data-mdl="{$name}"
                                                            {if $cookieCategory == 'necessary' }
                                                    checked disabled
                                                            {/if}>
                                                    <label for="module-{$name}-chkbox"> {l s='Allow' mod='gdprpro'}</label>
                                                </td>
                                            </tr>
                                        {/if}
                                    {/foreach}
                                </table>
                            </div>
                        </li>
                    {/capture}
                    {if $cookiesInCategory > 0} {$smarty.capture.cookieCategoryTab nofilter} {/if} {* HTML comment, no escape necessary *}
                {/foreach}
            </ul>
        </div>
    </div>