{extends file='page.tpl'}

{block name="page_content"}
	<p>
			
		<span style="font-size: 16px; font-weight: bold;">Wystąpiły błędy przy realizacji płatności.</span>
		
		<br /><br /><br />
			
		<span style="font-size: 16px;">Numer wniosku kredytowego: <b>{$wniosekId}</b></span>.
			
		<br /><br />
			
		<span style="font-size: 16px;">Numer zamówienia w sklepie: <b>{$orderId}</b></span>.
		<br/>            
		<hr/>
		<p>
			{$errors}
		</p>
		<hr/>
		<br/>	
		W razie jakichkolwiek pytań prosimy o kontakt z <a href="{$base_dir}contact-form.php">Działem Obsługi Klienta</a>.
		
	</p>
{/block}