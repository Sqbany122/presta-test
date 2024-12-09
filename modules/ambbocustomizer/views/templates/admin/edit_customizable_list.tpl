 {*
 *    ambBOcustomizer Module : customize the prestashop back-office
 *
 *    @module    BO Customizer (AmbBoCustomizer)
 *    @file      views/templates/admin/edit_customizable_list.tpl
 *    @subject   Management of the fields of the selected list
 *    @author    Ambris Informatique
 *    @copyright Copyright (c) 2013-2015 Ambris Informatique SARL
 *    @license   Commercial license
 *
 *    Support by mail: support@ambris.com
 *}
{if $compat}<script type="text/javascript">var update_success_msg="{$success_msg|escape:'htmlall':'UTF-8'}"</script>
<script type="text/javascript">var token="{$token|escape:'htmlall':'UTF-8'}"</script>
{/if}

    <input type="hidden" value="{$token|escape:'htmlall':'UTF-8'}" id="token" />
    {if !$compat}
<div class="panel col-xs-12">
    <div class="panel-heading">
    {else}
    <div class="toolbarBox toolbarHead"><div class="pageTitle"><h3>
    {/if}
        {l s='Custom columns for ' mod='ambbocustomizer'} {$amb_data->getHeaders()->category|escape:'quotes':'UTF-8'} > {$amb_data->getHeaders()->name|escape:'quotes':'UTF-8'}
    {if !$compat}
    </div>
    {else}
    </h3>
    </div>
    </div>
    <fieldset>
    {/if}

    <div class="form-group">
        <div id="conf_id_PS_ALLOW_HTML_IFRAME">

        <label class="control-label col-lg-3">
           {l s='Activate custom columns' mod='ambbocustomizer'}
        </label>

        <div class="col-lg-7">
             <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio" class="activate_amblist" name="amblist" id="amblist_on" value="1"  data-controller_name="{Tools::getValue('name')|escape:'htmlall':'UTF-8'}"  {if $list_active}checked="checked"{/if}><label for="amblist_on" class="radioCheck">{l s='Yes' mod='ambbocustomizer'}</label><input type="radio" class="activate_amblist" name="amblist" id="amblist_off"  data-controller_name="{Tools::getValue('name')|escape:'htmlall':'UTF-8'}"  value="0" {if !$list_active}checked="checked"{/if}><label for="amblist_off" class="radioCheck">{l s='No' mod='ambbocustomizer'}</label>
            <a class="slide-button btn"></a>
            </span>
        </div>
         </div>
    </div>

{if !$compat}
</div>
<div class="panel col-xs-12">
{/if}
    {if !$compat}
    <div class="panel-heading">
        {l s='Current view' mod='ambbocustomizer'}
    </div>
    {else}
        <br />
    {/if}
    <div class="form-wrapper form-horizontal">
        <div class="form-group">
            <label class="control-label col-lg-3">
               {l s='Change current view' mod='ambbocustomizer'}
            </label>

            <div class="col-lg-7">
                    <div class="col-md-2">
                    <select id="amb_customizer_view" data-url="{$url|escape:'quotes':'UTF-8'}" name="amb_customizer_view_id">
                        {foreach from=$view_names key=view_name item=display_name}
                        <option value="{$view_name|escape:'htmlall':'UTF-8'}" class="display-name-{$view_name|escape:'htmlall':'UTF-8'}" {if $current_view_name==$view_name}selected="selected"{/if}>{$display_name|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                    </div>

            </div>
        </div>
    </div>
{if !$compat}
</div>
{else}
</fieldset>
{/if}

{if !$compat}
<div class="panel col-xs-12" id="manage-views">
{else}
<br /><br /><br /><br />
<fieldset id="manage-views">
{/if}
     {if !$compat}
    <div class="panel-heading">
    {else}
    <legend>
    {/if}
    {l s='Manage views' mod='ambbocustomizer'}
      {if !$compat}
    </div>
    {else}
    </legend>
    {/if}

    <div class="form-wrapper form-horizontal">

        {foreach from=$view_names key=view_name item=display_name}
            <div class="form-group">

                <div id="show-view-{$view_name|escape:'htmlall':'UTF-8'}">

                    <label class="control-label col-lg-3">
                        {if $current_view_name==$view_name}
                        <strong><span class="display-name-{$view_name|escape:'htmlall':'UTF-8'}">{$display_name|escape:'htmlall':'UTF-8'}</span></strong>
                        {else}
                        <span class="display-name-{$view_name|escape:'htmlall':'UTF-8'}">{$display_name|escape:'htmlall':'UTF-8'}</span>
                        {/if}
                    </label>
                    <div class="col-lg-7">
                        <a href="{$link->getAdminLink('AdminAmbBoCustomizerParams')|escape:'htmlall':'UTF-8'}&name={Tools::getValue('name')|escape:'htmlall':'UTF-8'}&amb_customizer_view_id={$view_name|escape:'htmlall':'UTF-8'}"><button class="btn btn-link"><i class="icon-eye"></i></button></a>
                        <button class="btn btn-link" data-edit-view="{$view_name|escape:'htmlall':'UTF-8'}">
                            <i class="icon-pencil"></i>
                        </button>


                        {if !in_array($view_name, $amb_data->default_views)}
                            <form action="#main" method="post" style="display:inline-block">
                            <button class="btn btn-link btn-danger" onclick="if(!confirm('{l s='Are you sure you want to delete this view ?' mod='ambbocustomizer'}')){ return false; }"><i class="icon-trash"></i></button>
                            <input type="hidden" name="delete_view" value="{$view_name|escape:'htmlall':'UTF-8'}" />
                            </form>
                        {/if}
                    </div>

                </div>
                <div id="edit-view-{$view_name|escape:'htmlall':'UTF-8'}" style="display:none;">

                    <div class="col-lg-3 ">
                        {if $compat}<label class="control-label">{/if}
                       <input type="text" id="new-value-{$view_name|escape:'htmlall':'UTF-8'}" class="fixed-width-lg pull-right" value="{$display_name|escape:'htmlall':'UTF-8'}" />
                        {if $compat}</label>{/if}
                    </div>
                    <div class="col-lg-7">
                        <button class="btn btn-primary fixed-width-lg" {if $compat}style="margin-top:3px;"{/if} data-save-edit-view="{$view_name|escape:'htmlall':'UTF-8'}">{l s='Save' mod='ambbocustomizer'}</button>
                    </div>
                    {if $compat}<br />{/if}
                </div>


            </div>




        {/foreach}


        <div class="form-group">
            <form action="#main" method="post">
            <div class="col-lg-3">
            {if $compat}<label class="control-label">{/if}
               <input type="text" name="amb_customizer_view" class="fixed-width-lg pull-right" placeholder="{l s='New view' mod='ambbocustomizer'}" />
            {if $compat}</label>{/if}
            </div>

            <div class="col-lg-7">

                    <input type="submit" {if $compat}style="margin-top:3px;"{/if} class="btn btn-primary fixed-width-lg" value="{l s='Create' mod='ambbocustomizer'}" />

            </div>
            </form>
        </div>


    </div>

{if !$compat}
    </div>
{else}
</fieldset>
{/if}



<div class="panel col-xs-12">

    {if !$compat}
    <div class="panel-heading">
    {else}
    <br /><br />
    <h1>
    {/if}
    {l s='Manage fields' mod='ambbocustomizer'}
    {if !$compat}
    </div>
    {else}
    </h1>
    {/if}
    <input type="hidden" id="controller_name" value="{Tools::getValue('name')|escape:'htmlall':'UTF-8'}" />

    {if count($fields) > 0}
    <table id="fields" class="table tableDnD" style="width:100%">
        <thead>
            <tr class="nodrag nodrop">

                <th class="col-xs-1">{l s='Position' mod='ambbocustomizer'}</th>
                <th class="col-xs-2">{l s='Name' mod='ambbocustomizer'}</th>
                <th class="col-xs-7">{l s='Description' mod='ambbocustomizer'}</th>
                <th class="col-xs-2 text-center">{l s='Active' mod='ambbocustomizer'}</th>

            </tr>
        </thead>
        <tbody id="amb-fields">
        {assign var="i" value=0}

            {foreach from=$fields key=field_name item=field}
                <tr id="{$field_name|escape:'htmlall':'UTF-8'}" class="amb_table_element">
                <td id="td_{$i|escape:'htmlall':'UTF-8'}" class="pointer dragHandle center">
                        <div class="dragGroup">
                            <div class="positions">
                                {($i+1)|escape:'htmlall':'UTF-8'}
                            </div>
                        </div>
                    </td>
                    <td>
                   {$controller->getTranslation('title', $field['field'])|escape:'quotes':'UTF-8'}{if isset($field['field']->is_core) && $field['field']->is_core}*{/if}
                   </td>
                   <td>
                   <i>{$controller->getTranslation('description', $field['field'], true)|escape:'quotes':'UTF-8'}</i>
                   </td>
                   <td class="text-center">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" class="activate_field" name="{$field_name|escape:'htmlall':'UTF-8'}" id="{$field_name|escape:'htmlall':'UTF-8'}_on" data-controller_name="{Tools::getValue('name')|escape:'htmlall':'UTF-8'}" value="1" {if isset($field['field']->mandatory) && $field['field']->mandatory}disabled="disabled"{/if} {if $field['active']}checked="checked"{/if}><label for="{$field_name|escape:'htmlall':'UTF-8'}_on" class="radioCheck">{l s='Yes' mod='ambbocustomizer'}</label><input type="radio" class="activate_field" name="{$field_name|escape:'htmlall':'UTF-8'}" id="{$field_name|escape:'htmlall':'UTF-8'}_off" value="0" {if !$field['active']}checked="checked"{/if} {if isset($field['field']->mandatory) && $field['field']->mandatory}disabled="disabled"{/if}><label for="{$field_name|escape:'htmlall':'UTF-8'}_off" class="radioCheck">{l s='No' mod='ambbocustomizer'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                    </td>
                </tr>
                {assign var="i" value=$i+1}
            {/foreach}

        </tbody>
     </table>
     <div class="text-center"><br />
     <i>{l s='* : These fields are present in prestashop by default.' mod='ambbocustomizer'}</i>
     </div>

      {else}
                <div class="alert alert-warning">{l s='No fields available, please check your data file at ' mod='ambbocustomizer'} data/fields/{Tools::getValue('name')|escape:'htmlall':'UTF-8'}.json</div>
    {/if}



     <div class="panel-footer">

     {if $compat}<br /><br />{/if}

        <a id="desc-category-back" {if $compat}style="float:left;"{/if} class="{if $compat}button ambButton{/if} btn btn-default" href="{$link->getAdminLink('AdminAmbBoCustomizerParams')|escape:'htmlall':'UTF-8'}">
            <i class="process-icon-back "></i> <span>{l s='Back to list' mod='ambbocustomizer'}</span>
        </a>

        <a id="desc-category-back" {if $compat}style="float:right;"{/if} class="{if $compat}button ambButton{/if} btn btn-default pull-right" href="{$link->getAdminLink($amb_data->controller_name)|escape:'htmlall':'UTF-8'}">
            <i class="process-icon-preview "></i> <span>{l s='Show list' mod='ambbocustomizer'}</span>
        </a>
     </div>
</div>
{if $compat}<br /><br /><br />{/if}