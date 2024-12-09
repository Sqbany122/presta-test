<div class="content_fields">
    {*<div class="url_base_setting"><a href="{$url_base|escape:'htmlall':'UTF-8'}"><i class="icon-refresh process-icon-refresh"></i>{l s='Reset filters' mod='exportproducts'}</a></div>*}

    <div class="productTabs ">
        <div class="fields_list list-group">
            <a class="list-group-item active" data-tab="exportTabInformation">{l s='Information' mod='exportproducts'}</a>
            <a class="list-group-item" data-tab="exportTabPrices">{l s='Prices' mod='exportproducts'}</a>
            <a class="list-group-item" data-tab="exportTabSeo">{l s='SEO' mod='exportproducts'}</a>
            <a class="list-group-item" data-tab="exportTabAssociations">{l s='Associations' mod='exportproducts'}</a>
            <a class="list-group-item" data-tab="exportTabShipping">{l s='Shipping' mod='exportproducts'}</a>
            <a class="list-group-item" data-tab="exportTabCombinations">{l s='Combinations' mod='exportproducts'}</a>
            <a class="list-group-item" data-tab="exportTabQuantities">{l s='Quantities' mod='exportproducts'}</a>
            <a class="list-group-item" data-tab="exportTabImages">{l s='Images' mod='exportproducts'}</a>
            <a class="list-group-item" data-tab="exportTabFeatures">{l s='Features' mod='exportproducts'}</a>
            <a class="list-group-item" data-tab="exportTabCustomization">{l s='Customization' mod='exportproducts'}</a>
            <a class="list-group-item" data-tab="exportTabAttachments">{l s='Attachments' mod='exportproducts'}</a>
            <a class="list-group-item" data-tab="exportTabSuppliers">{l s='Suppliers' mod='exportproducts'}</a>
        </div>
    </div>
    <div class="block_all_fields">


        {foreach $all_fields AS $key => $block}
            <div class="field_list_base field_list_{$key|escape:'htmlall':'UTF-8'} {if $key == 'exportTabInformation'}active{/if}">
                <div class="field_list_header">
                    <input class="search_base_fields" placeholder="{l s='Search' mod='exportproducts'}">
                </div>
                <ul class="block_base_fields">
                    {foreach  $block AS $value}
                        {if $set && !in_array($value['name'], $set)}
                            <li data-tab="{$value['tab']|escape:'htmlall':'UTF-8'}" data-xml="{if isset($value['xml_head'])}{$value['xml_head']|escape:'htmlall':'UTF-8'}{/if}" data-name="{$value['name']|escape:'htmlall':'UTF-8'}" data-value="{$value['val']|escape:'htmlall':'UTF-8'}" {if !isset($value['hint']) || !$value['hint']} class="field_item" {/if} {if isset($value['hint']) && $value['hint']}class="isset_hint field_item" data-hint="{$value['hint']|escape:'htmlall':'UTF-8'}"{/if}>
                                {if isset($value['hint']) && $value['hint']}  <i class="icon-info icon-info-fields"></i>  {/if}
                                {$value['name']|escape:'htmlall':'UTF-8'}
                            </li>
                        {/if}
                        {if !$set}
                            <li data-tab="{$value['tab']|escape:'htmlall':'UTF-8'}" data-xml="{if isset($value['xml_head'])}{$value['xml_head']|escape:'htmlall':'UTF-8'}{/if}" data-name="{$value['name']|escape:'htmlall':'UTF-8'}" data-value="{$value['val']|escape:'htmlall':'UTF-8'}" {if !isset($value['hint']) || !$value['hint']} class="field_item" {/if} {if isset($value['hint']) && $value['hint']}class="isset_hint field_item"  data-hint="{$value['hint']|escape:'htmlall':'UTF-8'}"{/if}>
                                {if isset($value['hint']) && $value['hint']}  <i class="icon-info icon-info-fields"></i>  {/if}
                                {$value['name']|escape:'htmlall':'UTF-8'}
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            </div>
        {/foreach}

    </div>
    <div class="navigation-fields navigation-fields-base">

        <div class="navigation-button">
            <button type="button" class="btn btn-default add_base_filds_all add_fild_right">{l s='Add all ' mod='exportproducts'}<i class="icon-arrow-right"></i></button>
            <button type="button" class="btn btn-default add_base_filds add_fild_right">{l s='Add ' mod='exportproducts'}<i class="icon-arrow-right"></i></button>
            <button type="button" class="btn btn-default remove_base_filds add_fild_right">{l s='Remove ' mod='exportproducts'}<i class="icon-arrow-left"></i></button>
            <button type="button" class="btn btn-default remove_base_filds_all add_fild_right">{l s='Remove all ' mod='exportproducts'}<i class="icon-arrow-left"></i></button>
        </div>

    </div>
    <div class="block_selected_fields">

        <div class="field_list_selected">
            <div class="field_list_header">
                <input class="search_selected_fields" placeholder="{l s='Search' mod='exportproducts'}">
            </div>
            <ul class="selected_fields">
                {foreach from=$selected key=key item=select}
                    <li data-tab="{$select['tab']|escape:'htmlall':'UTF-8'}" data-xml="{if isset($select['xml_head'])}{$select['xml_head']|escape:'htmlall':'UTF-8'}{/if}" data-name="{$select['name']|escape:'htmlall':'UTF-8'}" data-value="{$key|escape:'htmlall':'UTF-8'}" class="field_item{if isset($select['hint']) && $select['hint']} isset_hint {/if}"  {if isset($select['hint']) && $select['hint']}data-hint="{$select['hint']|escape:'htmlall':'UTF-8'}"{/if}>
                        {if isset($select['hint']) && $select['hint']}  <i class="icon-info icon-info-fields"></i>  {/if}
                        {$select['name']|escape:'htmlall':'UTF-8'}
                        <i class="icon-arrows icon-arrows-select-fields"></i>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
</div>