{**
* 2013 - 2018 HiPresta
*
* MODULE Facebook Connect
*
* @author    HiPresta <suren.mikaelyan@gmail.com>
* @copyright HiPresta 2018
* @license   Addons PrestaShop license limitation
* @link      http://www.hipresta.com
*
* NOTICE OF LICENSE
*
* Don't use this module on several shops. The license provided by PrestaShop Addons
* for all its modules is valid only once for a single shop.
*}

{if $psv >= 1.6}
	<div class="panel">
		<h3>{l s='Changelog' mod='hifacebookconnect'}</h3>
{else}
	<fieldset id="fieldset_0">
		<legend>{l s='Changelog' mod='hifacebookconnect'}</legend>
{/if}
	<ul>
		<li><b>{l s='Version' mod='hifacebookconnect'} 1.1.1</b></li>
		<li>* {l s='Improvements' mod='hifacebookconnect'}</li>
	</ul>
	<ul>
		<li><b>{l s='Version' mod='hifacebookconnect'} 1.1.0</b></li>
		<li>* {l s='Added PrestaShop 1.7 support' mod='hifacebookconnect'}</li>
		<li>* {l s='Bug fixes / improvements.' mod='hifacebookconnect'}</li>
	</ul>
	<ul>
		<li><b>{l s='Version' mod='hifacebookconnect'} 1.0.0</b></li>
		<li>* {l s='Initial release' mod='hifacebookconnect'}</li>
	</ul>
{if $psv >= 1.6}
	</div>
{else}
	</fieldset>
{/if}