{literal}<style>div.well.hidden-print a.btn.btn-default:first-child{display:none!important;}</style>{/literal}
{if $type=='Faktura' || $type=='Invoice'}
<div id="ordertopdf_block" class="panel">
	<div class="panel-heading">
          Faktura VAT
    </div>
  <div class="block_content">
  	<h4 style="color:red;">Klient chce otrzymać Fakture VAT</h4>
  </div>
</div>
{/if}