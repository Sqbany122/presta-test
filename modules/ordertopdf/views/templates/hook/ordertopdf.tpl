<div id="ordertopdf_block" class="panel">
	<div class="panel-heading">
          Formularz zwrotu towaru
    </div>
  <div class="block_content">
  
	<a class="btn btn-default" href="{$link->getAdminLink('OrderToPdf')|escape:'html':'UTF-8'}&amp;id_order={$smarty.get.id_order}">
		<i class="icon-print"></i>
		{l s='Pobierz Formularz zwrotu towaru'}
	</a>

  </div>
</div>