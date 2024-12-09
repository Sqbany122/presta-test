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
{if $forms}
    {if $position == 'footer'}
        {if $ps17}
            <div class="col-md-2 links wrapper lc_block_support lc_block_support_{$position|escape:'html':'UTF-8'}">
                <p class="h3 hidden-sm-down">{l s='support center' mod='ets_livechat'}</p>
                <div class="title clearfix hidden-md-up collapsed" data-target="#footer_ticket" data-toggle="collapse" aria-expanded="false">
                    <span class="h3">{l s='support center' mod='ets_livechat'}</span>
                    <span class="float-xs-right">
                      <span class="navbar-toggler collapse-icons">
                        <i class="material-icons add">&#xE313;</i>
                        <i class="material-icons remove">&#xE316;</i>
                      </span>
                    </span>
                  </div>
                <ul id="footer_ticket" class="collapse">
                    {foreach from=$forms item='form'}
                        <li class="item"><a href="{$form.link|escape:'html':'UTF-8'}" title="{$form.title|escape:'html':'UTF-8'}">{$form.title|escape:'html':'UTF-8'}</a></li>
                    {/foreach}
                </ul>
            </div>
        {else}
            <section class="footer-block col-xs-12 col-sm-4">
            	<h4>{l s='Submit new ticket' mod='ets_livechat'}</h4>
            	<div class="block_content toggle-footer" style="">
            		<ul class="bullet">
                        {foreach from=$forms item='form'}
                        <li class="item"><a href="{$form.link|escape:'html':'UTF-8'}" title="{$form.title|escape:'html':'UTF-8'}">{$form.title|escape:'html':'UTF-8'}</a></li>
                        <li><a href="{$form.link|escape:'html':'UTF-8'}" title="{$form.title|escape:'html':'UTF-8'}">{$form.title|escape:'html':'UTF-8'}</a></li>	
                        {/foreach}	
                    </ul>
            	</div>
            </section>
        {/if}
    {elseif $position == 'left' || $position == 'right'}
        <div class="lc_block_support card card-block block lc_block_support_{$position|escape:'html':'UTF-8'}">
            <h4 class="text-uppercase h6">{l s='Submit new ticket' mod='ets_livechat'}</h4>
            <ul class="content_block">
                {foreach from=$forms item='form'}
                    <li class="item">
                        <a href="{$form.link|escape:'html':'UTF-8'}" title="{$form.title|escape:'html':'UTF-8'}">
                            {$form.title|escape:'html':'UTF-8'}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    {elseif $position == 'top_nav'}
        <div class="lc_block_support lc_block_support_{$position|escape:'html':'UTF-8'}">
            <span class="tit">{l s='Submit new ticket' mod='ets_livechat'}</span>
            <ul class="content_block">
                {foreach from=$forms item='form'}
                    <li class="item">
                        <a href="{$form.link|escape:'html':'UTF-8'}" title="{$form.title|escape:'html':'UTF-8'}">
                            {$form.title|escape:'html':'UTF-8'}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    {else}
        <div class="lc_block_support lc_block_support_{$position|escape:'html':'UTF-8'}">
            <h4 class=" title_block">{l s='Submit new ticket' mod='ets_livechat'}</h4>
            <ul class="content_block">
                {foreach from=$forms item='form'}
                    <li class="item">
                        <a href="{$form.link|escape:'html':'UTF-8'}" title="{$form.title|escape:'html':'UTF-8'}">
                            {$form.title|escape:'html':'UTF-8'}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    {/if}
{/if}