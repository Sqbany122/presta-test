<script type="text/javascript" src="{$module_dir}js/santanderCredit.js"></script>
<h1>{l s='Podsumowanie zamówienia' mod='santandercredit'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}
<style>
{literal}
#left_column {display:none}
.container_9 .grid_5 {width: 755px;}
{/literal}
</style>
<h2>{l s='Zakupy na raty z systemem eRaty Santander Consumer Bank' mod='santandercredit'}</h2>

<br>
      Twoje zamówienie został zarejestrowane w sklepie.
      Całkowita wartość Twojego zamówienia wynosi <span style="color: #ff0000;" class="bold">{convertPrice price=$cart->getOrderTotal()}</span>
      <br/>
      <h3> Teraz wypełnij wniosek o kredyt ratalny.</h3>

<form name="santanderCreditForm" id="santanderCreditForm" action="https://wniosek.eraty.pl/formularz/" method="post">
	
	{$productsInputs}
	<input type="hidden"  name="typProduktu" value="0" />
	<input type="hidden"  name="wariantSklepu" value="1" />
	<input type="hidden"  name="nrZamowieniaSklep" value="{$orderId}" />
	<input type="hidden"  name="wartoscTowarow" value="{$cart->getOrderTotal()}" />
	<input type="hidden"  name="action" value="getklientdet_si" />
	<input type="hidden"  name="pesel" value="" />
	<input type="hidden"  name="imie" value="{$imie}" />
	<input type="hidden"  name="nazwisko" value="{$nazwisko}" />
	<input type="hidden"  name="email" value="{$email}" />
	<input type="hidden"  name="telKontakt" value="{$telKontakt}" />
	<input type="hidden"  name="ulica" value="{$ulica}" />
	<input type="hidden"  name="nrDomu" value="{$ulica2}" />
	<input type="hidden"  name="nrMieszkania" value="" />
	<input type="hidden"  name="miasto" value="{$miasto}" />
	<input type="hidden"  name="kodPocz" value="{$kodPocz}" />
	<input type="hidden"  name="char" value="UTF" />
	<input type="hidden"  name="numerSklepu" value="{$shopId}" />
	<input type="hidden"  name="shopName" value="{$shopName}" />
	<input type="hidden"  name="shopHttp" value="{$shopHttp}" />
	<input type="hidden"  name="wniosekZapisany" value="{$returnTrue}" />
	<input type="hidden"  name="wniosekAnulowany" value="{$returnFalse}" />	
	<input type="hidden"  name="shopMailAdress" value="{$shopMailAdress}" />
	<input type="hidden"  name="shopPhone" value="{$shopPhone}" />
	
  <br />
   
  <br clear="all" />
  	
  <div class="cart_navigation">
    <table style="width:100%">
	<tbody>
		<tr>
			<td style="float: right;">
				<input type="submit" name="submit" value="{l s='Wypełnij wniosek kredytowy' mod='santandercredit'}" class="exclusive_large" style="width:320px" />
			</td>
		</tr>
	</tbody>
    </table>
  </div>
	
</form>