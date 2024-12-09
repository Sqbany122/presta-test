{*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript" src="/modules/advancedordernotes/views/js/global.js"></script>
<div style="clear:both"></div>
	<form method="post" action="#productcomments" class="form-horizontal clearfix" id="form-productcomments">
		
		<div class="panel col-lg-12">
		<div class="panel-heading"><i class="icon-cogs"></i> &nbsp;{l s='Latest Notes' mod='advancedordernotes'}</div>
			<div class="table-responsive clearfix">
				<table class="table productcomments" style="width:100%;">
					<thead>
						<tr class="nodrag nodrop">
							<th class="" width="50">
								<span class="title_box">{l s='ID Order' mod='advancedordernotes'}</span>
							</th>
							<th class="">
								<span class="title_box">{l s='Customer name' mod='advancedordernotes'}</span>
							</th>
							<th class="">
								<span class="title_box">{l s='Email' mod='advancedordernotes'}</span>
							</th>
							<th class="">
								<span class="title_box">{l s='Phone number' mod='advancedordernotes'}</span>
							</th>
							<th class="">
								<span class="title_box">{l s='Address' mod='advancedordernotes'}</span>
							</th>
							<th class="">
								<span class="title_box">{l s='City' mod='advancedordernotes'}</span>
							</th>
							<th class=" ">
								<span class="title_box">{l s='Employee' mod='advancedordernotes'}</span>
							</th>
							<th class=" ">
								<span class="title_box">{l s='Last Note' mod='advancedordernotes'}</span>
							</th>

							<th class="">
								<span class="title_box" width="250">{l s='Date' mod='advancedordernotes'}</span>
							</th>
							<th class="">
							
							</th>
						</tr>


						<tr class="nodrag nodrop filter row_hover">
							<th class="text-center center" width="50">
								<input type="text" class="filter" id="search_order_id" name="" value="">
							</th>
							<th class="text-center center">
								<input type="text" class="filter" id="search_costumer_name" name="" value="">
							</th>
							<th class="text-center center">
								<input type="text" class="filter" id="search_email" name="" value="">
							</th>
							<th class="text-center center">
								<input type="text" class="filter" id="search_phone_number" name="" value="">
							</th>
							<th class="">
								--
							</th>
							<th class="">
								--
							</th>
							<th class="">
								--
							</th>
							<th class="">
								--
							</th>
							<th class="">
								--
							</th>
							<th>
								<span class="pull-right">
									<button type="submit" id="search_for_order_notes" name="submitFilter" class="btn btn-default" data-list-id="product">
										<i class="icon-search">{l s='Search' mod='advancedordernotes'}</i> 
									</button>
								</span>
							</th>

						</tr>

					</thead>

					<tbody id="advcategories_list">
							{foreach from=$notes item=note}
								<tr class=" phone_status">
									<td class="pointer"><a target="_blank" href="index.php?controller=AdminOrders&id_order={$note.id_order|escape:'htmlall':'UTF-8'}&vieworder&token={$token_orders|escape:'htmlall':'UTF-8'}" style="text-decoration: underline;">{$note.id_order|escape:'htmlall':'UTF-8'}</a></td>
									<td class="pointer">{$note.full_name|escape:'htmlall':'UTF-8'}</td>
									<td class="pointer">{$note.email|escape:'htmlall':'UTF-8'}</td>
									<td class="pointer">{$note.phone_number|escape:'htmlall':'UTF-8'}</td>
									<td class="pointer">{$note.address|escape:'htmlall':'UTF-8'}</td>
									<td class="pointer">{$note.city|escape:'htmlall':'UTF-8'}</td>
									<td class="pointer">{$note.employee|escape:'htmlall':'UTF-8'}</td>
									<td class="pointer"><p title="{$note.note|escape:'htmlall':'UTF-8'}">{$note.note|escape:'htmlall':'UTF-8'}</p></td>
									<td class="pointer">{$note.date|escape:'htmlall':'UTF-8'}</td>
									<td class="pointer"></td>
								</tr>
							{/foreach}
				
					</tbody>
				</table>
			</div>
			<div class="row order_pagination">
				<div class="col-lg-6" style="margin-top:15px;">
				<ul class="pagination">
				
					{if $page != 1}
						<li class="">
							<a href="{$current_index2|escape:'htmlall':'UTF-8'}{($page-1)|escape:'htmlall':'UTF-8'}" class="pagination-link">
								<i class="icon-angle-left"></i>
							</a>
						</li>
					{/if}
					 {for $i=1 to $total_pages}
							<li class="{if $page == $i}active{/if}">
								<a href="{$current_index2|escape:'htmlall':'UTF-8'}{$i|escape:'htmlall':'UTF-8'}" class="pagination-link">{$i|escape:'htmlall':'UTF-8'}</a>
							</li>
					  {/for}

				
						{if $page < $total_pages }
							<li>
								<a href="{$current_index2|escape:'htmlall':'UTF-8'}{($page+1)|escape:'htmlall':'UTF-8'}" class="pagination-link"  >
									<i class="icon-angle-right"></i>
								</a>
						</li>
						{/if}
					
				</ul>
				</div>
			</div>
		</div>
	</form>


<script type="text/javascript">
	
	$(document).ready(function(){

					$( document ).on( "click", "#search_for_order_notes", function() {

							if( $('#search_order_id').val().length  == 0 && $('#search_costumer_name').val().length == 0 && $('#search_email').val().length == 0  && $('#search_phone_number').val().length == 0  )
							{
								window.location.reload();
								return false;
							}

							var response_z = $.ajax({ type: "POST",

									url: admin_order_notes,  
									cache: false,
									data: {  
										action: 'search_orders',
										id_order: $('#search_order_id').val(),
										customer_name: $('#search_costumer_name').val(),
										email: $('#search_email').val(),
										phone: $('#search_phone_number').val(),
										token: '{$aon_token|escape:'htmlall':'UTF-8'}'

									}, 
									async: true,
									success: function(data) {

										obj = JSON.parse(data);
										$('.order_pagination').hide();

										$('#advcategories_list').html('');

										$.each(obj, function(index, value) {
										    var tr = '<tr><td>'+value.id_order+'</td><td>'+value.full_name+'</td><td>'+value.email+'</td><td>'+value.phone_number+'</td><td>'+value.address+'</td><td>'+value.city+'</td><td>'+value.employee+'</td><td>'+value.note+'</td><td>'+value.date+'</td><td></td></tr>';


											$('#advcategories_list').append(tr);
										});
																	
										}
									}).responseText;
							return false;
							
				});



	});

</script>