{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}
{assign var="itemCount" value=4 scope="global"}
<div class="alert alert-danger hidden errorAddPackage"></div>
<div class="row">
    <div class="col-lg-12">
        <form class="defaultForm form-horizontal">
            <div class="panel card">
                <div class="panel-heading">
                    <i class="icon-tag"></i> {$content.txtPkgDefault|escape:'htmlall':'UTF-8'}
                </div>
                <div class="card-body form-wrapper">
                    <span>{$content.txtPkgWeightSize|escape:'htmlall':'UTF-8'|htmlspecialchars_decode:3}</span>
                    <br/>
                    <span>{$content.txtPkgWeightSize2|escape:'htmlall':'UTF-8'|htmlspecialchars_decode:3}</span>
                    <div class="form-horizontal">
                        <div class="form-group" style="padding-left: 50px; padding-bottom: 20px;">
                            {if isset($listPkg) }
                                {foreach $listPkg as $pkg => $value}
                                    {if $itemCount % 3 == 0}<div class="row">{/if}
                                    <div class="col-md-4" style="padding-top: 30px; padding-right: 90px;" >
                                        <p>
                                            <span class="control-label" style="font-weight:bold; word-wrap: break-word;">
                                                {$value.name|escape:'htmlall':'UTF-8'} {if isset($value.isDefault)}({$content.txtPkgDefaultPackage|escape:'htmlall':'UTF-8'}){/if}
                                            </span>
                                            <br>
                                            {$value.lenght|escape:'htmlall':'UTF-8'} x {$value.width|escape:'htmlall':'UTF-8'} x {$value.height|escape:'htmlall':'UTF-8'} {$value.lenghtUnit|escape:'htmlall':'UTF-8'}, {$value.weight|escape:'htmlall':'UTF-8'} {$value.weightUnit|escape:'htmlall':'UTF-8'}
                                        </p>
                                        <a class="edit btn btn-default" onclick="getKeyPkg({$value.id|escape:'htmlall':'UTF-8'});">
                                            {$content.txtPkgEdit|escape:'htmlall':'UTF-8'}
                                        </a>
                                        <a class="delete btn btn-default {if isset($value.isDefault)}hidden{/if} " onclick="deletePackage({$value.id|escape:'htmlall':'UTF-8'});">
                                            {$content.txtPkgDelete|escape:'htmlall':'UTF-8'}
                                        </a>
                                    </div>
                                    {if $itemCount % 3 == 0}</div>{/if}
                                    <p style="display:none">{$itemCount++|escape:'htmlall':'UTF-8'}</p>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                    <i class="card-header" id="headingFive" data-toggle="collapse" data-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
                        <a class="card-link" href="#" data-target="#myModal_add">
                            <i class="icon-plus"></i>{$content.txtPkgAddNewPackage|escape:'htmlall':'UTF-8'}
                        </a>
                    </i>
                    <div id="collapseFive" class="collapse" aria-labelledby="headingFive" action="" method="POST" name="addForm" style="padding-right: 15px;">
                        <div class="modal-body" style="padding: 15px 15px 0px 15px;">
                            <div class="form-group row">
                                <label class="control-label col-sm-2">{$content.txtPkgPackageName|escape:'htmlall':'UTF-8'}</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <input class="form-control ups-form-control" type="text" size="50" name="namePackage" placeholder="{$content.txtPkgExample|escape:'htmlall':'UTF-8'}" id = "namePackage" required minlength="1" maxlength="50" >
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-2">{$content.txtAddPackageWeight|escape:'htmlall':'UTF-8'}</label>
                                <div class="col-sm-2">
                                    <div class="input-group" style="width:100%;">
                                        <input class="form-control ups-form-control" type="number" name="weight" value="1" required id="weightadd">
                                    </div>
                                </div>
                                <label class="control-label col-sm-1" style="margin-left: 6px;">{$content.txtPkgUnit|escape:'htmlall':'UTF-8'}</label>
                                <div class="col-sm-1">
                                    <select class="form-control ups-form-control" name="weightUnit" style="width:90px;" id="addweightUnit">
                                        {foreach $content.weightUnits as $symbol => $name}
                                        <option {if $symbol == $content.selected_weightUnit } selected {/if}
										value="{$symbol|escape:'htmlall':'UTF-8'}">{$name|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-2">{$content.txtPkgDimension|escape:'htmlall':'UTF-8'}</label>
                                <div class="col-sm-2"   >
                                    <div class="input-group" style="width:100%;">
                                        <input class="form-control ups-form-control" type="number" size="5" name="lenght" placeholder="{$content.txtAddPackageLength|escape:'htmlall':'UTF-8'}" required id="lengthadd">
                                    </div>
                                </div>
                                <label class="control-label col-sm-1" style="padding-left:25px;">{$content.txtPkgx|escape:'htmlall':'UTF-8'}</label>
                                <div class="col-sm-2">
                                    <div class="input-group" style="width:100%;">
                                        <input class="form-control ups-form-control" type="number" size="5" name="width" placeholder="{$content.txtAddPackageWidth|escape:'htmlall':'UTF-8'}" required id="widthadd">
                                    </div>
                                </div>
                                <label class="control-label col-sm-1" style="padding-left:25px;">{$content.txtPkgx|escape:'htmlall':'UTF-8'}</label>
                                <div class="col-sm-2">
                                    <div class="input-group" style="width:100%;">
                                        <input class="form-control ups-form-control" type="number" size="5" name="height" placeholder="{$content.txtAddPackageHeight|escape:'htmlall':'UTF-8'}" required id="heightadd">
                                    </div>
                                </div>
                                <label class="control-label col-sm-1">{$content.txtPkgUnit|escape:'htmlall':'UTF-8'}</label>
                                <div class="col-sm-1">
                                    <select class="form-control ups-form-control" name="lenghtUnit" id="addlengthUnit" style="width:70px;">
                                        {foreach $content.lengthUnits as $symbol => $name}
                                        <option {if $symbol == $content.selected_lenghtUnit } selected {/if}
										value="{$symbol|escape:'htmlall':'UTF-8'}">{$name|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <a class="btn btn-default pull-right" onclick="addPackage();"><i class="process-icon-new"></i>{$content.txtPkgAddPackage|escape:'htmlall':'UTF-8'}</a>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" name="submitNext" class="btn btn-default pull-right" onclick="handleNextbutton();"><i class="process-icon-next"></i>{$content.txtNext|escape:'htmlall':'UTF-8'}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="ups-modal-alert" style="z-index: 1051 !important" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ups-modal-alert-title">{$content.ttlWarning|escape:'htmlall':'UTF-8'}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="ups-modal-alert-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="button button-secondary btn-cancel" data-dismiss="modal">{$content.btnCancel|escape:'htmlall':'UTF-8'}</button>
        <button type="button" class="button button-primary" id="submitFormPackage" data-dismiss="modal">{$content.txtArcOk|escape:'htmlall':'UTF-8'}</button>
      </div>
    </div>
  </div>
</div>


