<?php
/**
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(_PS_MODULE_DIR_.'advancedordernotes/advancedordernotes.php');
include_once(_PS_MODULE_DIR_.'advancedordernotes/libraries/order_notes.php');

$aon_token =  Configuration::get('aon_token');

$page = (int)Tools::getValue('page');
if($page <= 0)
	$page = 1;

$notes  = OrderNotesCore::get_latest_notes($page);
?>
<?php 	        
	if (version_compare(_PS_VERSION_, '1.6', '<=')) {
 ?>

<link href="<?php echo Tools::getHttpHost(true).__PS_BASE_URI__.'modules/advancedphoneorder/views/css/style.css' ?>" rel="stylesheet" type="text/css"/>
 <?php } else { ?>
<link href="<?php echo Tools::getHttpHost(true).__PS_BASE_URI__.'modules/advancedphoneorder/views/css/style16.css' ?>" rel="stylesheet" type="text/css"/>
<?php } ?>

<style type="text/css">
	.warn{ display:none !important;}
</style>
<script type="text/javascript" src="<?php echo Tools::getHttpHost(true).__PS_BASE_URI__.'modules/advancedordernotes/views/js/global.js' ?>"></script>
<br/><br/>



<div id="content" class="bootstrap" style="padding-top:0px !important;">
<div style="clear:both"></div>
	<form method="post" action="#productcomments" class="form-horizontal clearfix" id="form-productcomments">
		
		<div class="panel col-lg-12">
		<div class="panel-heading"><i class="icon-cogs"></i> &nbsp;Latest Notes</div>
			<div class="table-responsive clearfix">
				<table class="table productcomments">
					<thead>
						<tr class="nodrag nodrop">
							<th class="" width="50">
								<span class="title_box">ID Order</span>
							</th>
							<th class="">
								<span class="title_box">Customer name</span>
							</th>
							<th class="">
								<span class="title_box">Email</span>
							</th>
							<th class="">
								<span class="title_box">Phone number</span>
							</th>
							<th class="">
								<span class="title_box">Address</span>
							</th>
							<th class="">
								<span class="title_box">City</span>
							</th>
							<th class=" ">
								<span class="title_box">Employee</span>
							</th>
							<th class=" ">
								<span class="title_box">Last Note</span>
							</th>

							<th class="">
								<span class="title_box" width="250">Date</span>
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
										<i class="icon-search"></i> Search
									</button>
								</span>
							</th>

						</tr>

					</thead>

					<tbody id="advcategories_list">
							<?php foreach($notes as $note): ?>
								<tr class=" phone_status">
									<td class="pointer"><?php echo $note['id_order'] ?></td>
									<td class="pointer"><?php echo $note['full_name'] ?></td>
									<td class="pointer"><?php echo $note['email'] ?></td>
									<td class="pointer"><?php echo $note['phone_number'] ?></td>
									<td class="pointer"><?php echo $note['address'] ?></td>
									<td class="pointer"><?php echo $note['city'] ?></td>
									<td class="pointer"><?php echo $note['employee'] ?></td>
									<td class="pointer"><p title="<?php echo $note['note'] ?>"><?php echo Tools::substr($note['note'], 0 ,350) ?></p></td>
									<td class="pointer"><?php echo $note['date'] ?></td>
									<td class="pointer"></td>
								</tr>
							<?php endforeach; ?>
				
					</tbody>
				</table>
			</div>
			<div class="row">
				<div class="col-lg-6" style="margin-top:15px;">
					<?php $total_pages = OrderNotesCore::get_pagination_count();

							$cookie = new Cookie('psAdmin', '', (int)Configuration::get('PS_COOKIE_LIFETIME_BO'));
							$token = Tools::getAdminToken('AdminAdvancedOrderNotes'.(int)(Tab::getIdFromClassName('AdminAdvancedOrderNotes')).(int)($cookie->id_employee));

						     $currentIndex ='index.php?controller=AdminAdvancedOrderNotes&token='.$token.'&page=';




					  ?>
					 <span>Page</span> 
					<?php for($i = 1; $i<= $total_pages; $i++): ?>
						<a style="display:inline-block; border:1px solid #ccc; border-radius:5px; padding:6px; <?php if($page == $i) echo 'background-color:#00aff0 !important; color:#fff !important'; ?>" href="<?php echo    $currentIndex ?><?php echo $i ?>"><?php echo $i ?></a>
					<?php endfor; ?>
				</div>
			</div>
		</div>
	</form>
</div>





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
										token: '<?php echo $aon_token ?>'

									}, 
									async: true,
									success: function(data) {

										obj = JSON.parse(data);

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