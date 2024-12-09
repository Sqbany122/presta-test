{if $status == 'false'}

	<p class="warning">
	
			Zrezygnowałes z otrzymania kredytu ratalnego.
			
			<br />
			
			W przypadku pytań prosimy o kontakt z <a href="{$base_dir}contact-form.php">Działem Obsługi Klienta</a>.
			
	</p>	

{else}

    <p>
		
		<span style="font-size: 16px; font-weight: bold;">Dziękujemy za złożenie wniosku w Santander Consumer Bank.</span>
		
		<br /><br /><br />
		
		<span style="font-size: 16px;">Numer wniosku kredytowego: <b>{$wniosekId}</b></span>.
		
		<br /><br />
		
		<span style="font-size: 16px;">Numer zamówienia w sklepie: <b>{$orderId}</b></span>.
		
		<br /><br /><br />	
		
		<b>W przypadku pozytywnej wstępnej weryfikacji oczekuj na kontakt telefoniczny z konsultantem Santander.</b>
		
		<br /><br />
		
		Podczas rozmowy telefonicznej sporządzi razem z Toba umowę ratalna.
		
		<br /><br />
		
		<b>Przygotuj: dowód osobisty oraz drugi dokument tożsamosci.</b>
		
		<br /><br />
		
		Kiedy tylko otrzymamy informację o otrzymaniu płatności będziesz mógł śledzić stan swojego zamówienia w sekcji "Moje konto".
		
		<br /><br />
		
		W razie jakichkolwiek pytań prosimy o kontakt z <a href="{$base_dir}contact-form.php">Działem Obsługi Klienta</a>.
	
	</p>
	
{/if}