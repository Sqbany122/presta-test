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
<script>
    function toggle() {
        var qsa = document.getElementsByClassName("hook-chk-box"),
            l = qsa.length, i;
        for (i = 0; i < l; i++) qsa[i].checked = !qsa[i].checked;
    }

    function setAll(value) {
        var qsa = document.getElementsByClassName("hook-chk-box"),
            l = qsa.length, i;
        for (i = 0; i < l; i++) qsa[i].checked = value;
    }

    function expandAll() {
        $('#accordion .panel-collapse').collapse('toggle');
    }
</script>
<form method="post">
    <input type="hidden" name="submit-gdpr-unhook" value="1">
    <div class="well" id="admin-gdpr-module-selector">
        <div class="btn-group" role="group">
            <button type="submit" class="btn btn-success" name="submit-gdpr-unhook">
                <i class="icon-save"></i>
                {l s='Save settings' mod='gdprpro'}
            </button>
            <a class="btn btn-default" onclick='setAll(true)'>
                <i class="icon-check-square-o"></i>
                {l s='Check all' mod='gdprpro'}
            </a>

            <a class="btn btn-default" onclick='setAll(false)'>
                <i class="icon-check-square"></i>
                {l s='Un-check all' mod='gdprpro'}
            </a>
            <a class="btn btn-default" onclick='toggle()'>
                <i class="icon-toggle-on"></i>
                {l s='Toggle' mod='gdprpro'}
            </a>
            <a class="btn btn-default" onclick='expandAll()'>
                <i class="icon-expand"></i>
                {l s='Toggle panels' mod='gdprpro'}
            </a>
            <a class="btn btn-default" href="{$link->getAdminLink('AdminGdprConfig')}">
                <i class="icon-wrench"></i>
                {l s='Back to configuration' mod='gdprpro'}
            </a>
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading">
            {l s='GDPR enabled modules' mod='gdprpro'}
        </div>
        <div class="form-wrapper">
            {$defaultFormLanguage = Context::getContext()->language->id}
            <div class="bs-example">
                <div class="panel-group" id="accordion">
                    {foreach $modules_to_unhook as $name => $module}
                        {if Module::isInstalled($name)}
                            <div class="panel {if isset($module.checked) && $module.checked}panel-success{else}panel-default{/if}">
                                <div class="panel-heading" style="height: 34px">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#{$module.name}_module"
                                           class="collapsed module-name">
                                            {if isset($moduleImages[$module.name])}
                                                <img src="{$moduleImages[$module.name]}" style="width: 32px; height: 32px;">
                                            {/if}
                                            {$module.name}
                                        </a>
                                    </h4>
                                </div>
                                <div id="{$module.name}_module" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ul class="list-unstyled">
                                            <li>
                                                <label>
                                                    {l s='Module category' mod='gdprpro'}
                                                    <select name="modules_to_unload[{$module.name}][category]">
                                                        {foreach GdprPro::$cookieCategories as $cookieCategory}
                                                            <option value="{$cookieCategory}"
                                                                    {if isset($module.category) && $module.category == $cookieCategory}selected{/if}>
                                                                {l s=ucfirst($cookieCategory) mod='gdprpro'}
                                                            </option>
                                                        {/foreach}
                                                    </select>
                                                </label>
                                                {capture name="unusedTranslationStuff"}
                                                    {l s='Necessary' mod='gdprpro'}
                                                    {l s='Preferences' mod='gdprpro'}
                                                    {l s='Statistics' mod='gdprpro'}
                                                    {l s='Marketing' mod='gdprpro'}
                                                    {l s='Unclassified' mod='gdprpro'}
                                                {/capture}
                                            </li>
                                            <li>
                                                <label>
                                                    <input class="hook-chk-box"
                                                           name="modules_to_unload[{$module.name}][enabled]"
                                                           type="checkbox" value="1"
                                                           {if isset($module.checked) && $module.checked}checked{/if}>
                                                    {l s='Enable' mod='gdprpro'}
                                                </label>
                                            </li>
                                            <li>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label style="display: inline-block">
                                                                {l s='Expiry' mod='gdprpro'}
                                                            </label>
                                                        </div>
                                                        {foreach $languages as $language}
                                                            <div class="translatable-field lang-{$language.id_lang}"
                                                                 {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                                                <div class="col-lg-9">
                                                                    <input type="text"
                                                                           id="modules_to_unload[{$module.name}][expiry][{$language.id_lang}]"
                                                                           name="modules_to_unload[{$module.name}][expiry][{$language.id_lang}]"
                                                                           class=""
                                                                           value="{if isset($module.expiry[$language.id_lang])}{$module.expiry[$language.id_lang]}{/if}"/>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <button type="button"
                                                                            class="btn btn-default dropdown-toggle"
                                                                            tabindex="-1" data-toggle="dropdown">
                                                                        {$language.iso_code}
                                                                        <i class="icon-caret-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        {foreach from=$languages item=language}
                                                                            <li>
                                                                                <a href="javascript:hideOtherLanguage({$language.id_lang});"
                                                                                   tabindex="-1">{$language.name}
                                                                                </a>
                                                                            </li>
                                                                        {/foreach}
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label style="display: inline-block">
                                                                {l s='Provider' mod='gdprpro'}
                                                            </label>
                                                        </div>
                                                        {foreach $languages as $language}
                                                            <div class="translatable-field lang-{$language.id_lang}"
                                                                 {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                                                <div class="col-lg-9">
                                                                    <input type="text"
                                                                           id="modules_to_unload[{$module.name}][provider][{$language.id_lang}]"
                                                                           name="modules_to_unload[{$module.name}][provider][{$language.id_lang}]"
                                                                           class=""
                                                                           value="{if isset($module.provider[$language.id_lang])}{$module.provider[$language.id_lang]}{/if}"/>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <button type="button"
                                                                            class="btn btn-default dropdown-toggle"
                                                                            tabindex="-1" data-toggle="dropdown">
                                                                        {$language.iso_code}
                                                                        <i class="icon-caret-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        {foreach from=$languages item=language}
                                                                            <li>
                                                                                <a href="javascript:hideOtherLanguage({$language.id_lang});"
                                                                                   tabindex="-1">{$language.name}
                                                                                </a>
                                                                            </li>
                                                                        {/foreach}
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label style="display: inline-block">
                                                                {l s='Frontend name' mod='gdprpro'}
                                                            </label>
                                                        </div>
                                                        {foreach $languages as $language}
                                                            <div class="translatable-field lang-{$language.id_lang}"
                                                                 {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                                                <div class="col-lg-9">
                                                                    <input type="text"
                                                                           id="modules_to_unload[{$module.name}][frontend_name][{$language.id_lang}]"
                                                                           name="modules_to_unload[{$module.name}][frontend_name][{$language.id_lang}]"
                                                                           class=""
                                                                           value="{if isset($module.frontend_name[$language.id_lang])}{$module.frontend_name[$language.id_lang]}{/if}"/>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <button type="button"
                                                                            class="btn btn-default dropdown-toggle"
                                                                            tabindex="-1" data-toggle="dropdown">
                                                                        {$language.iso_code}
                                                                        <i class="icon-caret-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        {foreach from=$languages item=language}
                                                                            <li>
                                                                                <a href="javascript:hideOtherLanguage({$language.id_lang});"
                                                                                   tabindex="-1">{$language.name}
                                                                                </a>
                                                                            </li>
                                                                        {/foreach}
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label style="display: inline-block">
                                                                {l s='Description' mod='gdprpro'}
                                                            </label>
                                                        </div>
                                                        {foreach $languages as $language}
                                                            <div class="translatable-field lang-{$language.id_lang}"
                                                                 {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                                                <div class="col-lg-9">
                                                                    {*<input type="text"*}
                                                                    {*id="modules_to_unload[{$name}][description][{$language.id_lang}]"*}
                                                                    {*name="modules_to_unload[{$name}][description][{$language.id_lang}]"*}
                                                                    {*class=""*}
                                                                    {*value="{$module.description[$language.id_lang]}"/>*}
                                                                    <textarea
                                                                            id="modules_to_unload[{$module.name}][description][{$language.id_lang}]"
                                                                            name="modules_to_unload[{$module.name}][description][{$language.id_lang}]"
                                                                            class="">{if isset($module.description[$language.id_lang])}{$module.description[$language.id_lang]}{/if}</textarea>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <button type="button"
                                                                            class="btn btn-default dropdown-toggle"
                                                                            tabindex="-1" data-toggle="dropdown">
                                                                        {$language.iso_code}
                                                                        <i class="icon-caret-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        {foreach from=$languages item=language}
                                                                            <li>
                                                                                <a href="javascript:hideOtherLanguage({$language.id_lang});"
                                                                                   tabindex="-1">
                                                                                    {$language.name}
                                                                                </a>
                                                                            </li>
                                                                        {/foreach}
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" value="1" id="configuration_form_submit_btn"
                    name="submit-gdpr-unhook"
                    class="btn btn-default pull-right">
                <i class="process-icon-save"></i>
                {l s='Save' mod='gdprpro'}
            </button>
        </div>
    </div>
</form>
<style>
    .bootstrap .panel-title {
        line-height: 32px;
        text-decoration: none !important;
    }

    #content.bootstrap .panel-heading {

        margin-bottom: 0;
    }

    .bootstrap .panel-group .panel-heading + .panel-collapse .panel-body {
        border-top: none;
    }

    #content.bootstrap h3:not(.modal-title), #content.bootstrap .panel-heading {
        border-top-left-radius: 7px;
        border-top-right-radius: 7px;
        margin: -20px -20px 15px -20px;
    }

    .panel-heading a:after {
        /*font-family:'Material Icons';*/
        content: "-";
        float: right;
        color: grey;
        font-size: 24px;
        width: 24px;
        text-align: center;
    }

    .panel-heading a.collapsed:after {
        content: "+";
    }

    .module-name {
        text-decoration: none !important;
    }
</style>
<script type="text/javascript">
    $(document).ready(function () {
        $('.hook-chk-box').change(function () {
            console.log('blaaa');
            $(this).closest('.panel').toggleClass('panel-default').toggleClass('panel-success');
        });
    });
</script>
