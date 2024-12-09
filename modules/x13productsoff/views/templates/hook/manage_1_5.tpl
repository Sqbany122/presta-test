<fieldset>
		<legend>{l s='Shop' mod='x13productsoff'} {$shop_name}</legend>					
				<div id="check-update-warning-{$shop_id}" class="alert alert-warning" style="display: none">
					{l s='Progress... Please do not leave this page' mod='x13productsoff'}
				</div>	
		
				<div id="check-update-empty-warning-{$shop_id}" class="alert alert-warning" style="display: none">
					{l s='There are no products to update' mod='x13productsoff'}
				</div>	
			
				<div class="row">
					<label>
						<span>
							{l s='Update' mod='x13productsoff'}
						</span>
					</label>
					<div class="margin-form">
						<a href="#" data-shopid="{$shop_id}" class="btn button btn-default btn-attributes-update"><span>{l s='Go update' mod='x13productsoff'}</span></a>
					</div>
				</div>
			
				<div class="row" id="change-default-attribute-list-{$shop_id}" style="display: none">
					<label>
						{l s='Updated products' mod='x13productsoff'}
					</label>
					<div class="margin-form">
						<table class="table">
							<thead>
								<tr>
									<th>{l s='ID' mod='x13productsoff'}</th>
									<th>{l s='Name' mod='x13productsoff'}</th>
									<th>{l s='Result' mod='x13productsoff'}</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				
				<div class="row">
					<label for="shop_cron_update_{$shop_id}">
						<span>
							{l s='URL Cron' mod='x13productsoff'}
						</span>
					</label>
					<div class="margin-form">
						<input size="120" id="shop_cron_update_{$shop_id}" class="form-control" value="{$module_url}x13productsoff-check.php?token={$token_check}&id_shop={$shop_id}" type="text" />
						<p class="help-block">{l s='Last update on: %s' sprintf=$lastCronUpdate mod='x13productsoff'}</p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3">
						<span>
							{l s='Enebling and showing all products' mod='x13productsoff'} 

							<br/>({l s='attention: this option' mod='x13productsoff'} <b>{l s='active and show all' mod='x13productsoff'}</b> {l s='products' mod='x13productsoff'})
						</span>
					</label>
					<div class="col-lg-9">
						<a
							href="{$reset_products_url}&id_shop={$shop_id}"
							class="btn button btn-default"
							onclick="return confirm('{l s='The operation will work on all products in this store, are you sure?' mod='x13productsoff'}')"
						>
							<span>{l s='Enable and show product' mod='x13productsoff'}</span>
						</a>
					</div>
					</div>
				</div>	
			</div>	
	</div>
</fieldset>
<script type="text/javascript">
var update_url = '{$module_url}x13productsoff-check.php?token={$token_check}&id_shop=';
var update_mode = '{if $mode == 1}{l s='Disabled' mod='x13productsoff'}{else}{l s='Hidden' mod='x13productsoff'}{/if}'
var enable_mode = '{if $mode == 1}{l s='Enabled' mod='x13productsoff'}{else}{l s='Visible again' mod='x13productsoff'}{/if}'
</script>
