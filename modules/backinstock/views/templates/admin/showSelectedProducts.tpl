{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Admin tpl file
*}

<script>
    function deleteSelectedProduct(productId, current) {
        $('input[name="availability_form[excluded_products_hidden]"]').val(removeIdFromCommaString($('input[name="availability_form[excluded_products_hidden]"]').val(), productId, ','));
        $('input[name="availability_form[product_name]"]').val('');
        $(current).parent().remove();
    }
    
    function removeIdFromCommaString(list, value, separator) {
        separator = separator || ",";
        var values = list.split(separator);
        for (var i = 0; i < values.length; i++) {
            if (values[i] == value) {
                values.splice(i, 1);
                return values.join(separator);
            }
        }
        return list;
    }
</script>

<div id="kb_excluded_product_holder">
    {if isset($selectedproducts) && !empty($selectedproducts)}
        {foreach $selectedproducts as $productDetails}
            <div class="form-control-static">
                <button type="button" onclick="deleteSelectedProduct({$productDetails['product_id']|escape:'htmlall':'UTF-8'}, this);" class="delExcludedProduct btn btn-default" name="{$productDetails['product_id']|escape:'htmlall':'UTF-8'}"><i class="icon-remove text-danger"></i></button>
                &nbsp;{$productDetails['title']|escape:'htmlall':'UTF-8'} ({l s='ref' mod='backinstock'}: {$productDetails['reference']|escape:'htmlall':'UTF-8'})
            </div> 
        {/foreach}
    {/if}

</div>
