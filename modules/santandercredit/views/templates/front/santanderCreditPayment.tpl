<script type="text/javascript" src="{$modDir}/js/santanderCredit.js"></script>
<script type="text/javascript" src="{$modDir}/js/jquery.blockUI.js"></script>
<h1>{l s='Podsumowanie zamówienia' mod='santandercredit'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}
<style>
{literal}
#left_column {display:none; visibility:hidden;}
#center_column {width:100%;}
/*.container_9 .grid_5 {width: 755px;}*/
{/literal}
</style>
<h2>{l s='Zakupy na raty z systemem eRaty Santander Consumer Bank' mod='santandercredit'}</h2>


<br>
Całkowita wartość Twojego zamówienia wynosi <span style="color: #ff0000;" class="bold">{convertPrice price=$cart->getOrderTotal()}</span>
<br><br>
Przed złożeniem wniosku możesz zapoznać się z procedurą udzielenia kredytu ratalnego oraz obliczyć raty.

<!--<form name="santanderCreditForm" id="santanderCreditForm" action="https://wniosek.eraty.pl/formularz/" method="post" onsubmit="return santanderCreditValidateForm();">-->
<form name="santanderCreditForm" id="santanderCreditForm" action="https://wniosek.eraty.pl/formularz/" method="post">
	
	{$productsInputs}
	<input type="hidden"  name="typProduktu" value="0" />
	<input type="hidden"  name="wariantSklepu" value="1" />
	<input type="hidden"  name="nrZamowieniaSklep" value="{$orderId}" id="orderId"/>
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
	
  
  <br clear="all" />
  
  <div>
  
    <input type="checkbox" id="santanderAgreement" />
  	Zapoznałem się 	<a onclick="return jakKupic();" style="cursor: pointer;"><b><u>z procedurą udzielenia kredytu konsumenckiego na zakup towarów i usług eRaty Santander Consumer Bank</u></b></a> 

	</div>
  
    
  <br /><br />
  
  
	<b>Aby rozpocząć proces składania wniosku ratalnego należy zapoznać się z procedurą udzielenia kredytu, a następnie kliknąć na poniższy przycisk &quot;Kupuję na raty z Santander Consumer Bank&quot;</b>
	
	<br /><br />
	
	<div class="cart_navigation">
    <table style="width:100%">
			<tbody>
				<tr>
					<td style="float: left; padding: 2px 0px 2px 0px">
						<!--<a href="{$base_dir_ssl}order.php?step=3" class="button_large">Inne formy płatności</a>-->
                                                <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}" class="btn btn-alert button_large">Inne formy płatności</a>
					</td>
					<td style="float: left; padding: 2px 0px 2px 0px">
						<input type="button" value="{l s='Kupuję na raty z Santander Consumer Bank' mod='santandercredit'}" class="btn btn-warning button-medium button button-small pull-left" style="width:100%" onclick="santanderCreditValidateForm();"/>
					</td>
				</tr>
			</tbody>
    </table>
  </div>
  <div><input type="submit" id="submitBtn" name="submit" value="{l s='Kupuję na raty z Santander Consumer Bank' mod='santandercredit'}" class="exclusive_large" style="width:320px;visibility:hidden;"/></div>
</form>