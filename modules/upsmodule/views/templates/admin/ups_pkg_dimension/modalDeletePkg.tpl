{**
 * @author    United Parcel Service of America, Inc.*
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
 * @license   This work is Licensed under the Academic Free License version 3.0http://opensource.org/licenses/afl-3.0.php*
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page*
 *}

<div class="modal-content">
    <div class="modal-header">
        <label class="modal-title txt_info">{$content.txtPkgRemove|escape:'htmlall':'UTF-8'}</label>
        <input type="hidden" id="packageHiddenDeleteID" name="packageHiddenDeleteID">
        <button type="button" class="close" data-dismiss="modal">
            <span>X</span>
        </button>
    </div>
    <div class="modal-body">
        <p class="text-center mb00">{$content.txtPkgOkToRemove|escape:'htmlall':'UTF-8'|htmlspecialchars_decode:3}</p>
        <p class="text-center mb00">{$content.txtConfirm2|escape:'htmlall':'UTF-8'|htmlspecialchars_decode:3}</p>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" id="deletePackage" onclick="confirmDelete();" data-dismiss="modal" >{$content.txtArcOk|escape:'htmlall':'UTF-8'}</button>
    </div>
</div>
