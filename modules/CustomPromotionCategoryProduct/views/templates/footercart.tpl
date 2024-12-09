<h1>Te produkty możesz zakupić w cenie 1 zł</h1>
{if $custom_products}

	{include file="$tpl_dir./product-list.tpl" products=$custom_products}

	<div class="content_sortPagiBar">
        <div class="bottom-pagination-content clearfix">
        	{include file="$tpl_dir./product-compare.tpl"}
			{include file="$tpl_dir./pagination.tpl" paginationId='bottom'}
        </div>
	</div>
{/if}