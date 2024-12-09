{*
 * 2007-2013 PrestaShop
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

{*
<div class="create-new-field">
	<h3>
		<i class="process-icon-new"></i> {l s='Create new' mod='bestkit_opc'}
	</h3>
</div>

<div class="new-field-form">
	<div class="form-group">
		<label class="control-label ">{l s='Step' mod='bestkit_opc'}</label>
		<input type="text" name="step" id="new_step" value="" class="t form-control field" placeholder="{l s='Step' mod='bestkit_opc'}">
	</div>
</div>
*}
{if count($checkout_fields)}
    <div id="checkout_fields_wrapper">
        {foreach $checkout_fields as $checkout_field}
            <h3 class="step-header">{$checkout_field.step|escape}</h3>
			{if count($checkout_field.fields)}
			<ul class="ui-sortable">
				{foreach $checkout_field.fields as $field}
				<li>
					<div class="filed_container">
						<div class="name-container left{if !$field.active} disabled{/if}">
							<span>{$field.public_name|escape}</span>
						</div>
						<div class="actions-container">
							<span class=""><i class="icon-pencil" attr-id="{$field.id_bestkit_opc_checkoutfield|escape}"></i></span>
							{if !$field.standard}<span class=""><a href="{$opc_controller_url}&delete_field=1&id_bestkit_opc_checkoutfield={$field.id_bestkit_opc_checkoutfield|intval}"><i class="icon-remove" attr-id="{$field.id_bestkit_opc_checkoutfield|intval}"></i></a></span>{/if}
						<div>
					</div>
					<input type="hidden" name="checkout_field[{$checkout_field.step|escape}][{$field.position|escape}]" value="{$field.id_bestkit_opc_checkoutfield|escape}" />
				</li>
				{/foreach}
			</ul>
			{/if}
        {/foreach}
    </div>
{else}
    <div class="alert alert-warning">
        {l s='You doesn\'t have any carrier' mod='bestkit_opc'}
    </div>
{/if}

{literal}
	<script>
	$(document).ready(function() {
		$( ".ui-sortable" ).sortable();
		$( ".ui-sortable" ).disableSelection();
	});
	</script>
	
	<style>
		#opc_checkout_fields_new, #opc_checkout_field_edit {
			margin-top: 40px;
		}
		.new-field-form {
			margin-bottom: 50px;
		}
		.new-field-form .form-group {
			margin-left: -15px!important;
		}
		.new-field-form label{
			width: 100%;
			text-align: left!important;
			font-weight: 700!important;
		}
		.new-field-form .field{
			width: 25%!important;
		}
		.create-new-field {
			padding: 10px 0px;
		}
		.create-new-field i {
			float: left;
		}
		.create-new-field h3 {
			cursor: pointer;
			width: 150px;
		}
		h3.step-header {
			margin-bottom: 0px !important;
			padding-bottom: 0px !important;
		}
		.ui-sortable {
			list-style-type: none;
			margin: 0px 0px 10px 0px;
			padding: 0px 0px 10px 0px;
			margin-left: -15px;
		}
		.ui-sortable .left {
			float: left
		}
		.ui-sortable .filed_container {
			overflow: hidden;
			width: 380px;
		}
		.ui-sortable .actions-container {
			float: left;
		}
		.ui-sortable .actions-container span {
			//border: 1px solid rgb(181, 184, 175);
			width: 29px;
			height: 29px;
			padding: 5px;
			margin: 1px;
			display: block;
			float: left;
		}
		.ui-sortable .actions-container {
			cursor: pointer;
		}
		.ui-sortable .name-container {
			padding: 5px;
			border: 1px solid rgb(181, 184, 175);
			cursor: move;
			display: list-item;
			margin: 1px;
			width: 300px;
		}
		.ui-sortable .disabled {
			//background-color: rgba(255, 152, 152, 0.5);
			opacity: 0.6;
		}
		.ui-sortable .icon-pencil {
			color: #1D9DCC;
		}
	</style>
{/literal}