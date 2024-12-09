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
{literal}
<style>
.breadcrumb{display:none!important;}
.pagenotfound {margin: 0!important;max-width:none;}
#er404{display:none!important;}
#pagenotfound .pagenotfound h1{font-size:26px;}
@media(max-width:768px){}
</style>
{/literal}
<div id="pagenotfound">
<div class="pagenotfound">
<h1>Poszukiwany produkt znajduje się w&nbsp;innej&nbsp;kategorii</h1>
	<div style="border:3px solid #d1be8f;padding-bottom:15px;">
	<h2>{l s='Skorzystaj z wyszukiwarki i przejdź do produktu'}</h2>
	<form action="{$link->getPageLink('search')|escape:'html':'UTF-8'}" method="post" class="std">
		<fieldset>
			<div>
				<label for="search_query">{l s='Wyszukiwarka:'}</label>
				<input id="search_query" name="search_query" type="text" class="form-control grey" />
                <button style="height:32px;font-size:16px;font-weight:bold;margin:-4px 0px 0px 0px;padding:5px 15px;" type="submit" name="Submit" placeholder="Wpisz słowo kluczowe lub nazwe produktu" value="OK" class="btn btn-outline button button-small btn-sm"><span>{l s='SZUKAJ'}</span></button>
			</div>
		</fieldset>
	</form>
	</div>
	
	<a href="/bony-podarunkowe/"><img src="/themes/leo_arcana/img/modules/leoslideshow/bony.jpg" style="width:100%;height:auto;margin:15px 0px;" alt="Bony podarunkowe PegazShop"></a>
	
	<!--<a href="/szukaj?controller=search&orderby=position&orderway=desc&search_query=YoungStar+2020&submit_search="><img src="/themes/leo_arcana/img/modules/leoslideshow/pikeur.jpg" style="width:100%;height:auto;" alt="Kolekcja Pikeur Jesień/Zima"></a>
	<div class="row" style="margin-bottom:15px;">
	<a href="/bony-podarunkowe/"><img src="/themes/leo_arcana/img/modules/appagebuilder/images/bony.jpg" class="col-md-6" alt="Bony podarunkowe pegazshop"></a>
	<a href="/szukaj?controller=search&orderby=position&orderway=desc&search_query=kep&submit_search="><img src="/themes/leo_arcana/img/modules/appagebuilder/images/b404a.jpg"  class="col-md-6" alt="Kaski premium KEP Italia"></a>
	</div>
	<a href="/8_pikeur"><img src="/themes/leo_arcana/img/modules/leoslideshow/esk-ys.jpg" style="width:100%;height:auto;" alt="Kolekcja YoungStar Eskadron"></a>!-->
	{hook h='displayRightColumn'}
</div>
</div>
{*<div class="pagenotfound">
	<h1>{l s='This page is not available'}</h1>

	<p>
		{l s='We\'re sorry, but the Web address you\'ve entered is no longer available.'}
	</p>

	<h3>{l s='To find a product, please type its name in the field below.'}</h3>
	<form action="{$link->getPageLink('search')|escape:'html':'UTF-8'}" method="post" class="std">
		<fieldset>
			<div>
				<label for="search_query">{l s='Search our product catalog:'}</label>
				<input id="search_query" name="search_query" type="text" class="form-control grey" />
                <button type="submit" name="Submit" value="OK" class="btn btn-outline button button-small btn-sm"><span>{l s='Ok'}</span></button>
			</div>
		</fieldset>
	</form>

	<div class="buttons"><a class="btn btn-outline button button-medium" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{l s='Home'}"><span>{l s='Home page'}</span></a></div>
</div>
*}