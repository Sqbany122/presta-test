{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{hook h='displayTopColumn'}
				
{*if $category->id == 1380 OR $category->id == 1397 OR $category->id == 1415 OR $category->id == 1398 OR $category->id == 1399 OR $category->id == 1400 OR $category->id == 1401 OR $category->id == 1402 OR $category->id == 1403 OR $category->id == 1404 OR $category->id == 1405 OR $category->id == 1406 OR $category->id == 1407 OR $category->id == 1408 OR $category->id == 1409 OR $category->id == 1410 OR $category->id == 1411 OR $category->id == 1412 OR $category->id == 1413 OR $category->id == 1414 OR $category->id == 1438 OR $category->id == 1416 OR $category->id == 1417 OR $category->id == 1418 OR $category->id == 1419 OR $category->id == 1420 OR $category->id == 1421 OR $category->id == 1422 OR $category->id == 1423 OR $category->id == 1424 OR $category->id == 1425 OR $category->id == 1426 OR $category->id == 1427 OR $category->id == 1428 OR $category->id == 1429 OR $category->id == 1430 OR $category->id == 1431 OR $category->id == 1432 OR $category->id == 1433 OR $category->id == 1434 OR $category->id == 1435 OR $category->id == 1436 OR $category->id == 1437}
<a href="/info/wysylka-pasz.html" target="_blank"><div style="text-align:center;width:100%;padding:5px;background:#e36767;color:#fff;font-weight:bold;">ZOBACZ TERMINY WYSYŁKI PASZ I SUPLEMENTÓW W OKRESIE ŚWIĄTECZNO-NOWOROCZNYM</div></a>{/if*}

{* Advanced Search 4 - Start of custom search variable *}
{if isset($as4_5d323c4a338c4)}{$as4_5d323c4a338c4}{/if}
{* /Advanced Search 4 - End of custom search variable *}

<div id="center_products">
{include file="$tpl_dir./errors.tpl"}
		<h1 class="page-heading{if (isset($subcategories) && !$products) || (isset($subcategories) && $products) || !isset($subcategories) && $products} product-listing{/if}"><span class="cat-name">{$category->name|escape:'html':'UTF-8'}{if isset($categoryNameComplement)}&nbsp;{$categoryNameComplement|escape:'html':'UTF-8'}{/if}</span></h1>

{if isset($category)}
	{if $category->id AND $category->active}
    	{if $scenes || $category->description || $category->id_image}
			<div class="content_scene_cat">
            	 {if $scenes}
                 	<div class="content_scene">
                        <!-- Scenes -->
                        {include file="$tpl_dir./scenes.tpl" scenes=$scenes}
                        {if $category->description}
                            <div class="cat-desc rte">
                                <div>{$category->description}</div>
                            </div>
                        {/if}
                    </div>
				{else}
                    <!-- Category image -->
                    <div class="content_scene_cat_bg scene_cat">
						{if $category->id_image}
						<div class="image">
                    		<img class="img-responsive" src="{$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')|escape:'html':'UTF-8'}" alt="{$category->name|escape:'htmlall':'UTF-8'}" title="{$category->name|escape:'htmlall':'UTF-8'}" id="categoryImage"  /> 
						</div>
                    	{/if}
                        {if $category->description}
                            <div class="cat-desc">                            
                                <div class="rte">{$category->description}</div>
                            </div>
                        {/if}
                     </div>
                  {/if}
            </div>
		{/if}
		
		{if isset($subcategories)}

		<!-- Subcategories -->
		<div id="subcategories" class="hidden-md-up">

			{foreach from=$subcategories item=subcategory}
					<a class="subcategory-name btn" href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}">{$subcategory.name|escape:'html':'UTF-8'}</a>
			{/foreach}
			</ul>
		</div>

		{/if}


		{if $products}
			{include file="$tpl_dir./sub/product/product-list-form.tpl"}
		{/if}

	{elseif $category->id}
		<p class="alert alert-warning">{l s='This category is currently unavailable.'}</p>
	{/if}
{/if}
</div>