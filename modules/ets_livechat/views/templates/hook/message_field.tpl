{*
* 2007-2019 ETS-Soft
*
* NOTICE OF LICENSE
*
* This file is not open source! Each license that you purchased is only available for 1 wesite only.
* If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
* You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
* 
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2019 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
{if $fields}
    {foreach from=$fields item='field'}
        {if $field.type=='file'}
            {if $field.id_download!=-1}
                <p><strong>{$field.label|escape:'html':'UTF-8'}</strong>: {if $field.link_download===false}{$field.value|escape:'html':'UTF-8'} ({l s='Login to back office to download attachment' mod='ets_livechat'}){else}<a target="_blank" href="{$field.link_download|escape:'html':'UTF-8'}">{$field.value|escape:'html':'UTF-8'}</a><span class="file_size"> ({$field.file_size|escape:'html':'UTF-8'} MB)</span>{/if}</p>
            {else}
                <p><strong>{$field.label|escape:'html':'UTF-8'}</strong>: <b class="sent_file">{$field.value|escape:'html':'UTF-8'}</b></p>
            {/if}
        {else}
            <p><strong>{$field.label|escape:'html':'UTF-8'}</strong>: {$field.value|escape:'html':'UTF-8'}</p>
        {/if}
    {/foreach}
{/if}