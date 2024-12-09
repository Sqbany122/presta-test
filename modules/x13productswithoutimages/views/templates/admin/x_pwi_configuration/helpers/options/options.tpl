{extends file="helpers/options/options.tpl"}

{block name="input"}
    {if $field.type == 'update_button'}
        {if $is_bootstrap}<div class="col-lg-9">{/if}
        <a
            href="{$field.update_button}"
            class="button btn btn-default"
            {if isset($field.confirm) && $field.confirm eq true}onclick="return confirm('{l s="Are you sure?" js=1 mod='x13productswithoutimages'}')"{/if}
        >
            {if isset($field.button_label)}
            {$field.button_label}
            {else}
            {l s='Upgrade' mod='x13productswithoutimages'}
            {/if}
        </a>
        {if isset($field.desc)}<p class="help-block">{$field.desc}</p>{/if}
        {if $is_bootstrap}</div>{/if}
    {elseif $field.type == 'cron_link'}
        {if $is_bootstrap}<div class="col-lg-9">{/if}
        <input type="text" readonly="readonly" value="{$field.cron_link}">
        {if $is_bootstrap}</div>{/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
