{*
* 2016 Sensbit
*
* MODUŁ ZOSTAŁ UDOSTĘPNIONY NA PODSTAWIE LICENCJI NA JEDNO STANOWISKO/DOMENĘ
* NIE MASZ PRAWA DO JEGO KOPIOWANIA, EDYTOWANIA I SPRZEDAWANIA
* W PRZYPADKU PYTAŃ LUB BŁĘDÓW SKONTAKTUJ SIĘ Z AUTOREM
*
* ENGLISH:
* MODULE IS LICENCED FOR ONE-SITE / DOMAIM
* YOU ARE NOT ALLOWED TO COPY, EDIT OR SALE
* IN CASE OF ANY QUESTIONS CONTACT AUTHOR
*
* ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** *
*
* EN: ODWIEDŹ NASZ SKLEP PO WIĘCEJ PROFESJONALNYCH MODUŁÓW PRESTASHOP
* PL: VISIT OUR ONLINE SHOP FOR MORE PROFESSIONAL PRESTASHOP MODULES
* HTTPS://sensbit.pl
*
* ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** *
*
* @author    Tomasz Dacka (kontakt@sensbit.pl)
* @copyright 2016 sensbit.pl
* @license   One-site license (jednostanowiskowa, bez możliwości kopiowania i udostępniania innym)
*}

<script>
	$(function () {
		$('.fbox').fancybox({
			type: 'iframe',
			width: 1200,
			height: 600,
			helpers: {
				overlay: {
					locked: false
				}
			}
		});
		$('.fbox-s').fancybox({
			type: 'iframe',
			width: 600,
			height: 600,
			helpers: {
				overlay: {
					locked: false
				}
			}
		});

	{if $update}
		sensbitdpd.update();
	{/if}
	});
</script>

<div class="panel alert-info">
	<h2>Integracja z kurierem DPD. Ważne informacje!</h2>
	<p>Oddaje w Państwa ręce potężne narzędzie integrujące Państwa sklep z kurierem DPD.</p>
	<p>Moduł umożliwia sprawne generowanie etykiet, protokołów czy zamawianie kuriera za pośrednictwem dosłownie kilku kliknięć myszką.</p>
	<p>Proszę śmiało testować moduł na danych testowych, a jeśli zauważą Państwo jakiś problem lub braki w module to proszę o kontakt.</p>
	<p>Jestem zawsze otwarty na wszelkie propozycje zmian czy dodatków do modułu.</p>
	<p>Do poprawnego działania modułu potrzebne są Państwa dane logowania do api DPD: login, hasło i numer numkat (FID).</p>
	<p>Jeśli chcesz moduł przetestować bez ponoszenia żadnych kosztów, proszę włączyć tryb testowy oraz wprowadzić dane: login: test, hasło: thetu4Ee, fid: 1495.<br/> Jest to globalne konto testowe DPD. Proszę o rozwagę gdyż każdy mający do niego dostęp może pobierać wygenerowane wcześniej etykiety, a tym samym otrzymać informację o danych klientów czy nadawców zawartych na nich.</p>
	<p>Po wprowadzeniu poprawnych danych, proszę przejrzeć wszystkie dodtkowe zakładki w konfiguracji i je uzupełnić.</p>
	<p>Dodawanie przesyłek odbywa się w podglądzie danego zamówienia lub na liście masowego dodawania zamówień w zakładce DPD > Zamówienia.</p>
	<p>Przed dodaniem przesyłek musisz skonfigurować szablony przesyłek według swoich upodobań <a href="{$link_template}">tutaj</a>.</p>
	<p>Jeśli chcesz zawsze możesz przejrzeć listę stworzonych przez Ciebie przesyłek <a href="{$link_shipments}">tutaj</a>.</p>
	<p>W przypadku pytań lub wątpliwości proszę o wiadomość na adres kontakt@sensbit.pl</p>
</div>

<div class="panel">
	<h2>Pierwsze uruchomienie modułu</h2>
	<p>Po świężej instalacji modułu prosimy wykonać następujące kroki by moduł działał poprawnie:</p>
	<ol style='list-style: decimal;'>
		<li><strong>Dokończenie poniżej konfiguracji</strong><br/>
			Wprowadzamy przede wszystkim nasze unikalne dane logowania do systemów API DPD oraz wskazujemy modułowi sposób w jaki ma się zachowywać później.<br/>
			Danymi logowania nie są dane, którymi logujemy się do panelu DPD. API ma osobne dane i przesyłki tworzone przez api nie będą widoczne w panelu DPD.<br/>
			Prosimy przejrzeć wszystkie dostępne opcje łącznie ze wszystkimi zakładkami.</li>
		<li><strong>Stworzenie nowych przewoźników w sklepie dla przesyłek krajowych, zagranicznych i punktów odbiorów.</strong><br/>
			Jeśli chcą Państwo oferować również pobrania to zalecamy stworzenie dodatkowych przewoźników osobno dla pobrań.<br/>
			Jeśli interesuje Państwa tylko przesyłka krajowa to nie trzeba tworzyć przewoźników dla przesyłki zagranicznej i do punktów odbioru.</li>
			{*<li><strong>Stworzenie profilów nadawcy.</strong><br/>
			Podczas tworzenia przesyłek będzie można wybrać kto jest nadawcą przesyłki. Wymagamy przynajmniej jednego stworzonego profilu.</li>*}
		<li><strong>Stworzenie szablonów przesyłek.</strong><br/>
			Szablonem przesyłki można nazwać zbiór domyślnych ustawień dla danej przesyłki, którą wysyłają Państwo fizycznie w sklepie.<br/>
			Dany szablon powiązujemy ze stworzonym przez nas przewoźnikiem oraz metodą płatności tak, że później moduł sam będzie sugerował dany szablon dla danego zamówienia.</li>
		<li><strong>Automatyczne aktualizacje danych modułu</strong><br/>
			Prosimy uruchomić zadanie CRON opisane poniżej w celu uzupełnienia brakujących danych w module.<br/>
			Najlepiej też ustawić zadanie w CRONie na serwerze, by ten uruchamiał je cyklycznie np. 4/6 razy na dobę. Zadanie aktualizuje statusy i informacje o punktach odbioru przesyłek.</li>
		<li><strong>Dodaj przesyłkę do zamówienia</strong><br/>
			W tym momencie moduł masz już skonfigurowany w 99%, ten 1% to poprawki, których dokonasz by korzystać z modułu jeszcze wydajniej i szybciej.<br/>
			Stwórz przesyłkę w podglądzie danego zamówienia albo skorzystaj z naszego panelu masowego dodawania przesyłek DPD > Zamówienia, gdzie jednym kliknięciem dodasz wkrótce kilka-kilkanaście przesyłek od razu.</li>
		<li><strong>Oznacz przesyłki pobranymi etykietami</strong><br/>
			Po stworzeniu przesyłek masz możliwość od razu pobrania etykiet.<br/>
			Ciekawostką dla Ciebie może być fakt, że stworzyliśmy system pobierania i generowania etykiet w jednym pliku, tak by praca przebiegała jeszcze szybciej i przyjemniej.</li>
		<li><strong>Zamów kuriera bądź oczekuj na jego przyjazd</strong><br/>
			W zależności od Twojej umowy, możesz zlecić w module przyjazd kuriera, bądź też jeśli masz tak zwany Stały Zbiór, kurier sam przyjedzie po przesyłki.<br/>
			W jednym i drugim przypadku wygeneruj z zakładki DPD > Przesyłki protokół nadania przesyłek tak by kurier mógł potwierdzić ilości odebranych paczek.</li>
		<li><strong>Przyjmuj zamówienia i oceń moduł</strong><br/>
			Zachęcamy do korzystania z modułu do nadawania przesyłek gdyż został stworzony tak by faktycznie uprościć i ukrócić czas potrzebny na obsługę przesyłek.<br/>
			Gdyby mieli Państwo jakieś pytania bądź sugestie, która mogłaby jeszcze bardziej usprawnić działanie modułu zachęcamy do kontaktu z autorem, którego dane wyświetlają się poniżej.<br/>
			Po wyrobieniu swojej opinii zachęcamy do publicznego jej ujawnienia na naszej stronie sensbit.pl. Po zalogowaniu w strefie klienta możesz ocenić moduł i pomóc innym w wyborze.
		</li>
	</ol>
</div>
<div class="panel">
	<h2>CRON</h2>
	<p>W trosce o dostarczanie najlepszej jakości usług pobieramy zawsze aktualną listę punktów odbioru, która używana jest do szybkiej wyszukiwarki punktu odbioru klienta w koszyku</p>
	<p>Aktualizacja przebiega w tle podczas wchodzenia w konfigurację naszego modułu. Jeśli jednak nie chcesz często tu zaglądać, ustaw automatyczne zadanie CRON na serwerze korzystając z poniższego linku:</p>
	<a href="{$cron_update}" target="_blank">{$cron_update}</a>
	<p><em><i class="icon-info-circle"></i> Czy wiesz, że nasz system aktualizacji danych posiada sprytny mechanizm uniemożliwiający chwilowy brak danych w bazie podczas aktualizacji? ;)</em></p>
</div>