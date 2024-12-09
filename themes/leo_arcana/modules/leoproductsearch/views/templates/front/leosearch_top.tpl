{*
 *  Leo Theme for Prestashop 1.6.x
 *
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
*}

<!-- Block search module -->
<div id="leo_search_block_top" class="block exclusive search-by-category">
	<form method="get" action="{$link->getPageLink('productsearch', true)|escape:'html':'UTF-8'}" id="leosearchtopbox">
		<input type="hidden" name="fc" value="module" />
		<input type="hidden" name="module" value="leoproductsearch" />
		<input type="hidden" name="controller" value="productsearch" />
		<input type="hidden" name="orderby" value="position" />
		<input type="hidden" name="orderway" value="desc" />

<input class="search_query form-control grey" placeholder="Wpisz czego szukasz...." type="text" id="leo_search_query_top" name="search_query" value="{$search_query|escape:'htmlall':'UTF-8'|stripslashes}" />

		<div class="block_content clearfix leoproductsearch-content">		
			<div class="list-cate-wrapper">
				<input id="leosearchtop-cate-id" name="cate" value="" type="hidden">
				{*<a id="dropdownListCateTop" class="select-title" rel="nofollow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span>Wszędzie</span>
					<i class="material-icons pull-xs-right">keyboard_arrow_down</i>
				</a>
				<div class="list-cate dropdown-menu" aria-labelledby="dropdownListCateTop">
					<a href="#" data-cate-id="" data-cate-name="Wszędzie" class="cate-item active">Wszędzie</a>				
					<a href="#" data-cate-id="2" data-cate-name="Główna" class="cate-item cate-level-1">Główna</a>
					
  <a href="#" data-cate-id="1133" data-cate-name="Dla jeźdźca" class="cate-item cate-level-2">--Dla jeźdźca</a>
  <a href="#" data-cate-id="1207" data-cate-name="Dla konia" class="cate-item cate-level-2">--Dla konia</a>
  <a href="#" data-cate-id="1441" data-cate-name="Strefa Kibica" class="cate-item cate-level-2">--Strefa Kibica</a>
  <a href="#" data-cate-id="1454" data-cate-name="Akcesoria treningowe" class="cate-item cate-level-2">--Akcesoria treningowe</a>
  <a href="#" data-cate-id="1292" data-cate-name="Książki" class="cate-item cate-level-2">--Książki</a>
  <a href="#" data-cate-id="1293" data-cate-name="Padok i pastwisko" class="cate-item cate-level-2">--Padok i pastwisko</a>
  <a href="#" data-cate-id="1300" data-cate-name="Podkuwnictwo" class="cate-item cate-level-2">--Podkuwnictwo</a>
  <a href="#" data-cate-id="1304" data-cate-name="Stajnia i siodlarnia" class="cate-item cate-level-2">--Stajnia i siodlarnia</a>
  <a href="#" data-cate-id="1356" data-cate-name="Smakołyki" class="cate-item cate-level-2">--Smakołyki</a>
  <a href="#" data-cate-id="1380" data-cate-name="Pasza i suplementy" class="cate-item cate-level-2">--Pasza i suplementy</a>
  <a href="#" data-cate-id="1449" data-cate-name="Bony Podarunkowe" class="cate-item cate-level-2">--Bony Podarunkowe</a>
  <a href="#" data-cate-id="1313" data-cate-name="Upominki" class="cate-item cate-level-2">--Upominki</a>
  <a href="#" data-cate-id="1463" data-cate-name="Na specjalne okazje " class="cate-item cate-level-2">--Na specjalne okazje </a>
  <a href="#" data-cate-id="1285" data-cate-name="Dla psa" class="cate-item cate-level-2">--Dla psa</a>
  
				</div>*}
			</div>

			<button type="submit" id="leo_search_top_button" class="btn btn-default button button-small"><span><i class="icon-magnifying-glass icomoon search"></i></span></button> 
		</div>
	</form>
</div>
<!-- /Block search module -->