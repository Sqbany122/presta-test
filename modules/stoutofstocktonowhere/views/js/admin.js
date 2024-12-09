/*
* 2007-2017 PrestaShop
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
*  @author    ST-themes <hellolee@gmail.com>
*  @copyright 2007-2017 ST-themes
*  @license   Use, by you or one client for one Prestashop instance.
*/
jQuery(function($){
    $('.st_sidebar a').click(function(){
        $(this).parent().addClass('active').siblings().removeClass('active');
        var fieldset_arr = $(this).attr('data-fieldset').split(',');
        var fieldset_dom = $('form.defaultForm .panel');
        fieldset_dom.removeClass('selected');
        $.each(fieldset_arr,function(i,n){
            $('.panel[id^="fieldset_'+n+'"]').each(function(){
                var id = $(this).attr('id').replace('fieldset_', '').replace(/_\d+/, '');
                if(id == n) {
                    $(this).addClass('selected');
                    $('input[name="id_tab_index"]').val(n);
                }
                    
            });
        });
    });
    if (typeof(id_tab_index) == 'undefined' || !id_tab_index) {
        $('.st_sidebar a:first').trigger('click');
    } else {
        if ($('.st_sidebar a[data-fieldset="'+id_tab_index+'"]').length > 0) {
            $('.st_sidebar a[data-fieldset="'+id_tab_index+'"]').trigger('click');
        } else {
            $('.st_sidebar a[data-fieldset$=",'+id_tab_index+'"]').trigger('click');
        }   
    }
});