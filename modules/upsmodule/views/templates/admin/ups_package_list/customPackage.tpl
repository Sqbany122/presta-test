{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}

<script type="text/javascript" src="{$js_dir|escape:'htmlall':'UTF-8'}/admin.js?v=1.7.3.2"></script>
<div class="form-group row row_pack5" id="customPackage{$numberPackage|escape:'htmlall':'UTF-8'}">
	<div class="col-lg-2"style="text-align: center;"><input class="form-control ups-form-control" size="5" id="packageWeight{$numberPackage|escape:'htmlall':'UTF-8'}" name="packageWeight{$numberPackage|escape:'htmlall':'UTF-8'}" placeholder="" type="text" style="text-align: center;">{$texts.txtAddPackageWeight|escape:'htmlall':'UTF-8'}<span style="color:red;">*</span></div>
	<div class="col-lg-2">
		<select class="form-control ups-form-control" id="packageWeightUnit{$numberPackage|escape:'htmlall':'UTF-8'}" name="packageWeightUnit{$numberPackage|escape:'htmlall':'UTF-8'}">
			<option value="KGS" {if !$isUSA} selected {/if}>Kg</option>
			<option value="LBS" {if $isUSA} selected {/if}>Pounds</option>
		</select>
	</div>
	<div class="col-lg-2" style="text-align: center;"><input class="form-control ups-form-control" size="5" id="packageLength{$numberPackage|escape:'htmlall':'UTF-8'}" name="packageLength{$numberPackage|escape:'htmlall':'UTF-8'}" placeholder="" type="text" style="text-align: center;">{$texts.txtAddPackageLength|escape:'htmlall':'UTF-8'}<span style="color:red;">*</span></div>
	<div class="col-lg-2" style="text-align: center;"><input class="form-control ups-form-control" size="5" id="packageWidth{$numberPackage|escape:'htmlall':'UTF-8'}" name="packageWidth{$numberPackage|escape:'htmlall':'UTF-8'}" placeholder="" type="text" style="text-align: center;">{$texts.txtAddPackageWidth|escape:'htmlall':'UTF-8'}<span style="color:red;">*</span></div>
	<div class="col-lg-2" style="text-align: center;"><input class="form-control ups-form-control" size="5" id="packageHeight{$numberPackage|escape:'htmlall':'UTF-8'}" name="packageHeight{$numberPackage|escape:'htmlall':'UTF-8'}" placeholder="" type="text" style="text-align: center;">{$texts.txtAddPackageHeight|escape:'htmlall':'UTF-8'}<span style="color:red;">*</span></div>
	<div class="col-lg-2">
		<select class="form-control ups-form-control" id="packageHeightUnit{$numberPackage|escape:'htmlall':'UTF-8'}" name="packageHeightUnit{$numberPackage|escape:'htmlall':'UTF-8'}">
			<option value="CM" {if !$isUSA} selected {/if}>Cm</option>
			<option value="IN" {if $isUSA} selected {/if}>Inch</option>
		</select>
	</div>
</div>
