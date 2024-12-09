<span style="font-size: 25px; font-family: 'times new roman', times;">
	<table width="100%">
		<tr>
			<td align="center"><h2><u>FORMULARZ ZWROTU TOWARU</u></h2></td>
		</tr>
	</table><br />Uzupełnij dokument i postępuj zgodnie z informacjami. Więcej informacji na stronie www.pegazshop.pl	<br />
	<br />
	<table border="1" cellpadding="4" width="500px" >
		<tr>
			<td>IMIĘ I NAZWISKO: {$address->lastname} {$address->firstname}</td>
			<td>ADRES: {$address->address1} {$address->address2}</td>
		</tr>
		<tr>
			<td>ADRES E-MAIL: {$address->email}</td>
			<td>KOD POCZTOWY: {$address->postcode}</td>
		</tr>
		<tr>
			<td>TELEFON: {$address->phone}</td>
			<td>MIEJSCOWOŚĆ: {$address->city}</td>
		</tr>
	</table>
	<br />
<u>Twój numer zamówienia</u> <table cellspacing="0" cellpadding="1" border="2">
		<tr>
			<td width="150" height="12" align="center"><b>{$id_order}</b></td>
		</tr>
		</table>
	<br />Jeżeli dokonałeś płatności za pobraniem/przelewem bankowym wpisz nr konta, na który zostaną zwrócone środki.<br /><br />
<u>Numer konta bankowego</u>
	<br /><br />
	<table  width="100%" >
		<tr>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td align="center">-</td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td align="center">-</td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td align="center">-</td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td align="center">-</td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td align="center">-</td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td align="center">-</td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
			<td border="1" width="15" height="20" ></td>
		</tr>
	</table>
	<br />* Numer konta bankowego musi składać się z 26 cyfr, proszę dokłądnie sprawdzić ten numer.<br />
	<br />
	<b>Zaznacz “X” produkty, które chcesz zwrócić I wpisz numer z listy powodów zwrotu:</b>
	<br />
	<table border="1" width="100%" cellpadding="4">
		<tr>
			<td width="20" align="center">X</td>
			<td width="85" align="center">EAN</td>
			<td width="252" align="center">Nazwa Produktu</td>
			<td width="40" align="center">Ilość</td>
			<td width="53" align="center">Ilość<br />zwracana</td>
			<td width="50" align="center">Powód zwrotu</td>
		</tr>
		{foreach from=$order_details item=order_detail}
		<tr>
			<td></td>
			<td  align="left">{$order_detail.product_ean13}</td>
			<td align="left">{$order_detail.product_name}</td>
			<td align="center">{$order_detail.product_quantity}</td>
			<td></td>
			<td></td>
		</tr>
		{/foreach}
	</table>
	<br />
	<table>
		<tr>
			<td align="left"><span style="font-weight: bold;"><u>Lista numerów powodów zwrotu</u></span></td>
		</tr>
	</table>
	<br />
	<table border="1" cellpadding="4" width="500px" >
		<tr>
			<td  width="25%" >1. Rozmiar za mały</td>
			<td width="50%" >2. Rozmiar za duży</td>
			<td width="25%" >3. Szerokie</td>
		</tr>
		<tr>
			<td>4. Niewygodne</td>
			<td>5. Ciasne</td>
			<td>6. Uszkodzone</td>
		</tr>
		<tr>
			<td>7. Błędnie wysłąne</td>
			<td>8. Wygląda inaczej niż na zdjęciu</td>
			<td>9. Inne</td>
		</tr>
	</table>
	<br />
	<br />
	<b><u>Adres zwrotu towaru:</u></b><br /><br />
	Salon Jeździecki PEGAZ<br />
	ul.Zamkowa 2,<br />
	62-310 Pyzdry
	<table>
		<tr>
			<td width="250"></td>
			<td align="center">...........................................</td>
		</tr>
		<tr>
			<td ></td>
			<td align="center">Imię i nazwisko</td>
		</tr>
	<table>
</span>