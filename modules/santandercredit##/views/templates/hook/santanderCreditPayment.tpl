<h2 style="margin-left: auto; margin-right: auto; color:red; text-align: center;">{l s='Zakupy na raty z systemem eRaty Santander Consumer Bank' mod='santandercredit'}</h2>
<div style="margin-left: auto; margin-right: auto; margin-bottom: 3em; color:red; text-align: center;">Zarejestruj wniosek o kredyt ratalny aby sfinalizowac płatność.</div>

<!--default action="https://wniosek.eraty.pl/formularz/" (with backslash at the end)-->
<form name="santanderCreditForm" id="santanderCreditForm" action="{$applicationURL}" method="post">

    {assign var='nr' value='0'}
    {foreach from=$products item=product}
        {$nr = $nr + 1}
        <input name="idTowaru{$nr}" readonly="readonly" type="hidden" value="{$product['id_product']}" />
        <input name="nazwaTowaru{$nr}" readonly="readonly" type="hidden" value="{$product['name']}" />
        <input name="wartoscTowaru{$nr}" readonly="readonly" type="hidden" value="{round($product['price_wt'], 2)}" />
        <input name="liczbaSztukTowaru{$nr}" readonly="readonly" type="hidden" value="{$product['quantity']}" />
        <input name="jednostkaTowaru{$nr}" readonly="readonly" type="hidden" value="szt" />        
    {/foreach}

    {if $shipping gt 0}
        {$nr = $nr + 1}
        <input type="hidden" name="idTowaru{$nr}" readonly="readonly" value="KosztPrzesylki" />
        <input type="hidden" name="nazwaTowaru{$nr}" readonly="readonly" value="Koszt przesyłki" />
        <input type="hidden" name="wartoscTowaru{$nr}" readonly="readonly" value="{$shipping}" />
        <input type="hidden" name="liczbaSztukTowaru{$nr}" readonly="readonly" value="1" />
        <input type="hidden" name="jednostkaTowaru{$nr}" readonly="readonly" value="szt" />'
    {/if}

    <input type="hidden" name="liczbaSztukTowarow" value="{$nr}" />

    <input type="hidden"  name="typProduktu" value="0" />
    <input type="hidden"  name="wariantSklepu" value="1" />
    <input type="hidden"  name="nrZamowieniaSklep" value="{$orderId}" id="orderId"/>
    <input type="hidden"  name="wartoscTowarow" value="{$totalOrder}" />
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

    <p>  
        <input type="checkbox" id="santanderAgreement" onclick="santanderCreditValidateForm();"/>
        Zapoznałem się 	<a onclick="return jakKupic();" style="cursor: pointer;"><b><u>z procedurą udzielenia kredytu konsumenckiego na zakup towarów i usług eRaty Santander Consumer Bank</u></b></a> 
    </p>
    <p>
        <input type="submit" id="scbSubmitBtn" name="submit" disabled="disabled" value="{l s='Kupuję na raty z Santander Consumer Bank' mod='santandercredit'}" class="btn btn-primary center-block"/>
    </p>

</form>



