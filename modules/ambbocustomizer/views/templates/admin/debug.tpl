{*
* (c) 2013 Ambris Informatique
*
* @module       All ambris modules
* @file         debug.tpl
* @subject      template for debug system
* @copyright    Copyright (c) 2013 Ambris Informatique SARL (http://www.ambris.com/)
* @author       Richard Stefan (@RicoStefan)
* @license      Commercial license
* Support by mail: support@ambris.com
*}

<input type="hidden" id="amb_module_config_url" value="{$amb_module_config_url|escape:'htmlall':'UTF-8'}" />

{if !$compat}
<div class="container-fluid">
<div class="row">


<form class="form-horizontal">
<div class="panel col-xs-12">
    <div class="panel-heading">
    Debug mode
    </div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3">Activate debug mode</label>
                <div class="col-lg-9">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" class="amb_switch get_logs" data-configuration="AMB_DEBUG" name="switch_debug" id="switch_debug_on" value="1" {if $amb->debug}checked="checked"{/if}><label for="switch_debug_on" class="radioCheck">Yes</label><input type="radio" class="amb_switch hide_logs" data-configuration="AMB_DEBUG" name="switch_debug" id="switch_debug_off" value="0"  {if !$amb->debug}checked="checked"{/if}><label for="switch_debug_off" class="radioCheck">No</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>

            <div class="form-group" id="use_console_logging" style="{if !$amb->debug}display: none;{/if}">
                <label class="control-label col-lg-3">Use console logging
                </label>
                <div class="col-lg-9">
                    {AmbModule::generateAjaxSwitch(AMB_CONSOLE_LOGGING)|escape:'quotes':'UTF-8'}
                </div>
            </div>
        </div>
</div>

<div class="panel col-xs-12">
    <div class="panel-heading">
    Debug actions
    </div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3">Use debugReset()</label>
                <div class="col-lg-9">
                    <button type="button" name="debugReset" data-amb-action="debugReset" class="button btn btn-danger fixed-width-lg">DEBUG RESET</button>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-3">Optimize tables</label>
                <div class="col-lg-9">
                    <button type="button" name="optimizeTables" data-amb-action="optimizeTables" class="button btn btn-primary fixed-width-lg">Optimize</button>
                </div>
            </div>
        </div>
</div>


<div class="panel col-xs-12" style="{if !$amb->debug}display: none;{/if}" id="logs_container">
    <div class="panel-heading">
    Logs
    <span class="panel-heading-action">
        <a class="list-toolbar-btn get_logs">
            <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Refresh" data-html="true" data-placement="top">
                <i class="process-icon-refresh"></i>
            </span>
        </a>
        <a class="list-toolbar-btn delete_logs">
            <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Reset logs" data-html="true" data-placement="top">
                <i class="process-icon-delete"></i>
            </span>
        </a>
    </span>
    </div>
    <pre><code id="amb_logs">{if $amb->debug}{$amb->getLogs()|escape:'htmlall':'UTF-8'}{/if}</code></pre>
</div>

</form>
</div>
</div>
{else}

    <div style="clear:both;">&nbsp;</div>

    <fieldset>
        <legend><img src="{$path|escape:'urlpathinfo':'UTF-8'}logo.gif" alt="" title="" />Debug mode</legend>

        <label>Activate debug mode</label>
        <div class="margin-form">
            <input type="radio" size="20" class="amb_switch get_logs" data-configuration="AMB_DEBUG" value="1" name="switch_debug" {if $amb->debug}checked{/if}/> <img src="{$path|escape:'urlpathinfo':'UTF-8'}views/img/enabled.gif" alt="Yes" title="Yes">
            <input type="radio" size="20" class="amb_switch hide_logs" data-configuration="AMB_DEBUG" name="switch_debug" value="0" {if !$amb->debug}checked{/if} /> <img src="{$path|escape:'urlpathinfo':'UTF-8'}views/img/disabled.gif" alt="No" title="No">
        </div>
        <div style="clear:both;">
            <p class="clear"></p>
        </div>

        <div id="use_console_logging" style="{if !$amb->debug}display: none;{/if}">
            <label>Use console logging</label>
            <div class="margin-form">
                <input type="radio" size="20" class="amb_switch" data-configuration="AMB_CONSOLE_LOGGING" value="1" name="switch_AMB_CONSOLE_LOGGING" {if $amb->console_logging}checked{/if}/> <img src="{$path|escape:'urlpathinfo':'UTF-8'}views/img/enabled.gif" alt="Yes" title="Yes">
                <input type="radio" size="20" class="amb_switch" data-configuration="AMB_CONSOLE_LOGGING" name="switch_AMB_CONSOLE_LOGGING" value="0" {if !$amb->console_logging}checked{/if} /> <img src="{$path|escape:'urlpathinfo':'UTF-8'}views/img/disabled.gif" alt="No" title="No">
            </div>
        </div>
    </fieldset>

    <div style="clear:both;">&nbsp;</div>

    <fieldset>
        <legend><img src="{$path|escape:'urlpathinfo':'UTF-8'}logo.gif" alt="" title="" />
        Debug actions
        </legend>
        <label>Use debugReset()</label>
        <div class="margin-form">
            <button type="button" name="debugReset" data-amb-action="debugReset" class="button btn btn-danger fixed-width-lg">DEBUG RESET</button>
        </div>
        <div style="clear:both;">
            <p class="clear"></p>
        </div>
        <label>Optimize tables</label>
        <div class="margin-form">
            <button type="button" name="debugReset" data-amb-action="debugReset" class="button btn btn-danger fixed-width-lg">OPTIMIZE</button>
        </div>
    </fieldset>

    <div style="clear:both;">&nbsp;</div>

    <div style="{if !$amb->debug}display: none;{/if}" id="logs_container">
        <fieldset>
        <legend><img src="{$path|escape:'urlpathinfo':'UTF-8'}logo.gif" alt="" title="" />Logs</legend>
        <div align="right">
            <button class="get_logs">
                <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Refresh" data-html="true" data-placement="top">
                    Refresh
                </span>
            </button>
            <button class="delete_logs">
                <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Reset logs" data-html="true" data-placement="top">
                    Delete
                </span>
            </button>
        </div>

        <pre><code id="amb_logs">{if $amb->debug}{$amb->getLogs()|escape:'htmlall':'UTF-8'}{/if}</code></pre>
        </fieldset>
    </div>

{/if}