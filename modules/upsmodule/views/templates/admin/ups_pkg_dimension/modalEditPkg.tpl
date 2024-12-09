{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}

    <div class="modal-header">
        <label class="modal-title">{$content.txtPkgEditing|escape:'htmlall':'UTF-8'}</label>
        <button type="button" class="close" data-dismiss="modal">
            X
        </button>
    </div>
    <div class="modal-body">
    <input type="hidden" id="packageHiddenID" name="packageHiddenID" value="">
        <div class="form-group row">
            <label class="control-label col-sm-2">{$content.txtPkgPackageName|escape:'htmlall':'UTF-8'}</label>
            <div class="col-sm-5">
                    <input class="form-control ups-form-control" type="text" size="60" id="namePkg" onchange="enabledBtnSave();" required minlength="1" maxlength="50">
                </div>
        </div>
        <div class="form-group row">
            <label class="control-label col-sm-2">{$content.txtAddPackageWeight|escape:'htmlall':'UTF-8'}</label>
            <div class="col-sm-2">
                    <input class="form-control ups-form-control" type="number" size="auto" id="weight" onchange="enabledBtnSave();" required max="9999.99">
            </div>
            <label class="control-label col-sm-2">{$content.txtPkgUnit|escape:'htmlall':'UTF-8'}</label>
            <div class="col-sm-2">
                <select class="form-control ups-form-control" id="weightUnit"  onchange="enabledBtnSave();">
                    {foreach $content.weightUnits as $symbol => $name}
                    <option value="{$symbol|escape:'htmlall':'UTF-8'}" >{$name|escape:'htmlall':'UTF-8'} </option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label col-sm-2">{$content.txtPkgDimension|escape:'htmlall':'UTF-8'}</label>
            <div class="col-sm-2">
                    <input class="form-control ups-form-control" type="number" size="auto" id="lenght" placeholder="" onchange="enabledBtnSave();" required max="9999.99">
            </div>
            <label class="control-label col-sm-1" style="width: 20px;">x</label>
            <div class="col-sm-2">
                <input class="form-control ups-form-control" type="number" size="auto" id="width" onchange="enabledBtnSave();" required max="9999.99">
            </div>
        <label class="control-label col-sm-1" style="width: 20px;">x</label>
        <div class="col-sm-2">
            <input class="form-control ups-form-control" type="number" size="auto" id="height" placeholder="" onchange="enabledBtnSave();" required max="9999.99">
        </div>
        <label class="control-label col-sm-1" style="padding-left: 0%;">{$content.txtPkgUnit|escape:'htmlall':'UTF-8'}</label>
        <div class="col-sm-1" style="width: auto;" >
            <select class="form-control ups-form-control" id="lenghtUnit" style="margin-left:8px" onchange="enabledBtnSave();">
                {foreach $content.lengthUnits as $symbol => $name}
                <option value="{$symbol|escape:'htmlall':'UTF-8'}">{$name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
    </div>
</div>
<div class="alert alert-danger hidden errorPackage"></div>
<div class="modal-footer">
    <a name="submitEdit" id="submitEdit" class="save btn btn-default pull-right" onclick="savePackage();">{$content.txtPkgSave|escape:'htmlall':'UTF-8'}</a>
</div>
