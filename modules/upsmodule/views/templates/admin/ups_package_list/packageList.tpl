{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}

<div class="form-group row package{$numberPackage|escape:'htmlall':'UTF-8'}" id="package{$numberPackage|escape:'htmlall':'UTF-8'}">
	<div class="control-label col-lg-2 label-right">{$texts.txtOpenPackage|escape:'htmlall':'UTF-8'}{$numberPackage|escape:'htmlall':'UTF-8'}</div>
	<div class="col-lg-9">
		<select  name="shipment_package_{$numberPackage|escape:'htmlall':'UTF-8'}" id="shipment_package_{$numberPackage|escape:'htmlall':'UTF-8'}"
		onChange="addCustomPackage(this, {$numberPackage|escape:'htmlall':'UTF-8'});">
			{html_options options=$packageOptions selected=''}
		</select>
	</div>
	<div class="col-lg-1 col-sm-2">
		<a href="javascript:void(0)" onclick="deletePackage({$numberPackage|escape:'htmlall':'UTF-8'});" class="deletePackage" id="deletePackage">
			<i class="icon-times text-danger"></i>
		</a>
	</div>
</div>
