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
{if count($categoryProducts) > 0 && $categoryProducts !== false}
<div class="page-product-box blockproductscategory products_block block">
	<h3 class="page-subheading productscategory_h3">{l s='Related Products' mod='productscategory'}</h3>
	<div id="productscategory_list" class="clearfix product_list grid">
		{assign var ='tabname' value='blockproductscategory'}
		{assign var='itemsperpage' value='4'}
		{assign var='columnspage' value='4'}
		{$products = $categoryProducts}
		<div class="productscategory list-product-home" id="{$tabname}">
			<div class="owl-carousel owl-loading category-inner">
			{foreach from=$products item=product name=products}
				<div class="item {if $smarty.foreach.mypLoop.first}active{/if} product_block ajax_block_product{if isset($productClassWidget)} {$productClassWidget}{/if}">
					{if isset($productProfileDefault) && $productProfileDefault}
					    {capture name=productPath}{$tpl_dir}./profiles/{$productProfileDefault}.tpl{/capture}
					    {include file="{$smarty.capture.productPath}" callFromModule=isset($class)}
					{else}
					    {include file="$tpl_dir./sub/product-item/product-item.tpl" callFromModule=isset($class)}
					{/if}
				</div>
			{/foreach}
			</div>
		</div>
	</div>
</div>
{addJsDefL name=min_item}{l s='Please select at least one product' js=1}{/addJsDefL}
{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' sprintf=$comparator_max_item js=1}{/addJsDefL}
{addJsDef comparator_max_item=$comparator_max_item}
{addJsDef comparedProductsIds=$compared_products}
{/if}

{literal}
<script>

$(window).load(function(){
	$('.owl-carousel').owlCarousel({
            items : 5,
            itemsDesktop : [1199,6],            itemsDesktopSmall : [979,4],            itemsTablet : [768,3],
            itemsMobile : [479,1],            itemsCustom : false,            singleItem : false,         // true : show only 1 item
            itemsScaleUp : false,
            slideSpeed : 200,  //  change speed when drag and drop a item
            paginationSpeed : 800, // change speed when go next page

            autoPlay : true,   // time to show each item
            stopOnHover : false,
            navigation : true,
            navigationText : ["&lsaquo;", "&rsaquo;"],

            scrollPerPage : false,
            
            pagination : false, // show bullist
            paginationNumbers : false, // show number
            
            responsive : true,
            //responsiveRefreshRate : 200,
            //responsiveBaseWidth : window,
            
            //baseClass : "owl-carousel",
            //theme : "owl-theme",
            
            lazyLoad : true,
            lazyFollow : false,  // true : go to page 7th and load all images page 1...7. false : go to page 7th and load only images of page 7th
            lazyEffect : "fade",
            
            autoHeight : true,

            //jsonPath : false,
            //jsonSuccess : false,

            //dragBeforeAnimFinish
            mouseDrag : true,
            touchDrag : true,
            
            addClassActive : true,
                        //transitionStyle : "owl_transitionStyle",
            
            //beforeUpdate : false,
            //afterUpdate : false,
            //beforeInit : false,
            afterInit: OwlLoaded,
            //beforeMove : false,
            //afterMove : false,
            afterAction : SetOwlCarouselFirstLast,
            //startDragging : false,
            //afterLazyLoad: false
    

        });
}); 
function OwlLoaded(el){
	el.removeClass('owl-loading').addClass('owl-loaded');
};

function SetOwlCarouselFirstLast(el){
	el.find(".owl-item").removeClass("first");
	el.find(".owl-item.active").first().addClass("first");

	el.find(".owl-item").removeClass("last");
	el.find(".owl-item.active").last().addClass("last");
}
</script>
{/literal}