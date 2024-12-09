 {*
 *    ambBOcustomizer Module : customize the prestashop back-office
 *
 *    @module    BO Customizer (AmbBoCustomizer)
 *    @file      views/templates/admin/customizable_lists.tpl
 *    @subject   Shows all lists that can be customized
 *    @author    Ambris Informatique
 *    @copyright Copyright (c) 2013-2015 Ambris Informatique SARL
 *    @license   Commercial license
 *
 *    Support by mail: support@ambris.com
 *}

 <div class="bootstrap">
    <div class="table-responsive">
        {if Tools::strlen($title) > 0}
            <div class="title" {$builder->getCSS('title')|escape:'quotes':'UTF-8'}>
                {$title|escape:'quotes':'UTF-8'}
                <span class="badge" {$builder->getCSS('badge')|escape:'quotes':'UTF-8'}>{count($rows)|escape:'quotes':'UTF-8'}</span>
            </div>
        {/if}
        <table class="table" {$builder->getCSS('table', $css)|escape:'quotes':'UTF-8'}>

        {if (count($columns) > 0)}
            <thead  {$builder->getCSS('thead')|escape:'quotes':'UTF-8'} >
                <tr {$builder->getCSS('thead > tr')|escape:'quotes':'UTF-8'} >
                {foreach from=$columns item=column key=i}
                    <th class="{$column.align|escape:'quotes':'UTF-8'}" {$builder->getCSS('th', $i)|escape:'quotes':'UTF-8'}>{$column.header|escape:'quotes':'UTF-8'}</th>
                {/foreach}
                </tr>
            </thead>
        {/if}

        {if (count($rows) > 0)}
            <tbody {$builder->getCSS('tbody')|escape:'quotes':'UTF-8'} >

            {foreach from=$rows item=row name=tr}
                <tr class="{if ($smarty.foreach.tr.index % 2 != 0)}odd{/if}" {$builder->getCSS('tbody > tr')|escape:'quotes':'UTF-8'} >
                {foreach from=$row item=value key=i}
                    <td class="{if isset($columns[$i]['align'])}{$columns[$i]['align']|escape:'quotes':'UTF-8'}{/if}" {$builder->getCSS('td', $i)|escape:'quotes':'UTF-8'} >
                    {if isset($html_columns[$i]) && $html_columns[$i] === true}{$value|escape:'quotes':'UTF-8'}{else}{$value|escape:'quotes':'UTF-8'}{/if}
                    </td>
                {/foreach}
                </tr>
            {/foreach}
            </tbody>
        {/if}
        </table>
    </div>
</div>