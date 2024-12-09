<div class="container cartborder">
	<div class="col-md-12 text-right">
        <form action="#" method="POST" name="pf" class="pff" id="pfform">
            <p>
				<input type="checkbox" value="1" name="pfi" {if $type == 1} checked {/if} style="margin-left: 30px;"/><label style="margin-left: 5px;"> <b>Chcę otrzymać fakturę VAT</b>
				<input type="hidden" value="{$id_cart}" name="pf_id" id="pf_id"/>
			</p>
		</form>
	</div>
</div>