{*
 * 2007-2014 PrestaShop
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
 *         DISCLAIMER   *
 * *************************************** */
/* Do not edit or add to this file if you wish to upgrade Prestashop to newer
* versions in the future.
* ****************************************************
*
*  @author     BEST-KIT.COM (contact@best-kit.com)
*  @copyright  http://best-kit.com
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
	<form id="opc_configuration_form" class="defaultForm form-horizontal bestkit_opc" action="{$opc_controller_url|escape}" method="post" enctype="multipart/form-data" novalidate="">
		<h3><i class="icon-dashboard"></i> {l s='One Page Checkout' mod='bestkit_opc'}</h3>
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
		  <li class="active"><a href="#settings" role="tab" data-toggle="tab">{l s='Settings' mod='bestkit_opc'}</a></li>
		  <li><a href="#order_summary" role="tab" data-toggle="tab">{l s='Order summary' mod='bestkit_opc'}</a></li>
		  {*<li><a href="#checkout_fields" role="tab" data-toggle="tab">{l s='Checkout fields' mod='bestkit_opc'}</a></li>*}
		  <li><a href="#ship_to_pay" role="tab" data-toggle="tab">{l s='Carrier dependency' mod='bestkit_opc'}</a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
		  <div class="tab-pane active" id="settings">{$opc_settings|escape:false}</div>
		  <div class="tab-pane" id="order_summary">{$opc_order_summary|escape:false}</div>
          {*<div class="tab-pane" id="checkout_fields">{$opc_checkout_fields|escape:false}</div>*}
		  <div class="tab-pane" id="ship_to_pay">{$opc_ship_to_pay|escape:false}</div>
		</div>

		<div class="panel-footer">
			<button type="submit" value="1" id="configuration_form_submit_btn" name="submitUpdate" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save' mod='bestkit_opc'}
			</button>
		</div>
	</form>

    {*<div id="opc_checkout_fields_new" style="display: none">
        <form class="defaultForm form-horizontal bestkit_opc" action="{$opc_controller_url|escape}" method="post" enctype="multipart/form-data" novalidate="">
            {$opc_checkout_fields_new|escape:false}
            <input type="hidden" name="submit_new_field" value="1" />
        </form>
    </div>*}

	<div id="opc_checkout_field_edit_wrapper" style="display: none">
		<form class="defaultForm form-horizontal bestkit_opc" action="{$opc_controller_url|escape}" method="post" enctype="multipart/form-data" novalidate="">
			<div id="opc_checkout_field_edit"></div>
			<input type="hidden" name="submit_edit_field" value="1" />
		</form>
	</div>
</div>


{* TO DO: will be done at the next major version
{literal}
<script>
	var opc_controller_ajax = "{/literal}{$opc_controller_ajax|strip_tags}{literal}";

	function opc_set_cookie ( cookie_name, cookie_value, lifespan_in_days, valid_domain ) {
		var domain_string = valid_domain ? ("; domain=" + valid_domain) : '' ;
		document.cookie = cookie_name +
						   "=" + encodeURIComponent( cookie_value ) +
						   "; max-age=" + 60 * 60 *
						   24 * lifespan_in_days +
						   "; path=/" + domain_string ;
	}

	function opc_get_cookie ( cookie_name ) {
		var value = "; " + document.cookie;
		var parts = value.split("; " + cookie_name + "=");
		if (parts.length == 2) return parts.pop().split(";").shift();
	}

	$(document).ready(function() {

		/*begin*/
		var current_active_id = opc_get_cookie('active_opc_tab');
		var current_item = '';
        if (typeof current_active_id != "undefined") {
            current_active_id = current_active_id.replace (/#|%23/g, "");

            $('#opc_configuration_form ul.nav-tabs li a').each(function(i, item){
                current_item = $(item).attr('href').replace (/#|%23/g, "");
                if (current_item  == current_active_id) {
                    $(item).click();
                    //full checkout fields will be presented at the version 2
                    /*if (current_active_id == 'checkout_fields') {
                        $('#opc_checkout_fields_new').show();
                    }*/
                }
            })
        }
		/*end*/

		$('#opc_configuration_form ul.nav-tabs').click(function (e) {
			e.preventDefault()
			$(this).tab('show')
		})

		$('#opc_configuration_form ul.nav-tabs li a').click(function (e) {
			var active_id = $(this).attr('href');
			opc_set_cookie('active_opc_tab', active_id, 10);

            //will be presented at the version 2
             if (active_id == '#checkout_fields') {
                //$('#opc_checkout_fields_new').show();
             } else {
                //$('#opc_checkout_fields_new').hide();
                $('#opc_checkout_field_edit_wrapper').hide();
             }

		})

		$( ".icon-pencil" ).click(function(){
			var id = $(this).attr("attr-id");

			if (!$(this).hasClass('disabled')) {
				$( ".icon-pencil" ).addClass('disabled');

				$.ajax({
					url: opc_controller_ajax,
					data: { fields_id: id }
				}).done(function(result) {
					if (result) {
						$('#opc_checkout_field_edit').html(result);
						$('#opc_checkout_field_edit_wrapper').show();
						$('html, body').animate({
							scrollTop: $("#opc_checkout_field_edit_wrapper").offset().top
						}, 2000);
					}
					$( ".icon-pencil" ).removeClass('disabled');
				});
			}
		});

		$("#cancel_field").live('click', function(){
			$("#opc_checkout_field_edit_wrapper").slideUp('slow');
		});

		$(".icon-remove").click(function(){
			if (confirm("{/literal}{l s='Are you sure?' mod='bestkit_opc'}{literal}")) {
				return true;
			} else {
				return false;
			}
		});
	});
</script>
{/literal}
*}