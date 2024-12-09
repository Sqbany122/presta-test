<?php

return [
'txtSeEn' => 'Ulepszenia zabezpieczeń',
'txtSeEnAc' => 'Aktywacja ulepszeń zabezpieczeń',
'txtByInstalling' => 'Instalując wtyczkę UPS, zyskałeś wygodną opcję zaimplementowania zalecanych przez UPS ulepszeń zabezpieczeń w swoim e-sklepie. Dzięki wprowadzeniu poniższych ulepszeń w zakresie bezpieczeństwa możesz sprawić, że Twój sklep internetowy będzie mniej podatny na ataki hakerów.<br>Zauważ, że możesz pozostawić zalecane  przez UPS ulepszenia bezpieczeństwa wyłączone, jeśli już wdrożyłeś te środki bezpieczeństwa w inny sposób (poprzez poprawne skonfigurowanie swoich serwerów internetowych, przez programowanie własnych haków PrestaShop lub za pomocą innej wtyczki, która ma na celu wprowadzenie podobnych ulepszeń).',
'txtClickjacking' => 'Ochrona przed clickjackingiem',
'txtDescClickjacking' => 'Więcej informacji można znaleźć w Wikipedii  [https: //en.wikipedia.org/wiki/Clickjacking]. Jeśli włączysz tę ochronę, wtyczka UPS zabezpieczy Twój sklep, przekazując zasady ramek do przeglądarki użytkownika za pośrednictwem nagłówka odpowiedzi HTTP "X-Frame-Options: Sameorigin" i przez wstrzyknięcie skryptu java "frame-breaker" dla starszych przeglądarek. - Wtyczka UPS może zabezpieczyć strony front office w twoim sklepie internetowym zgodnie z zalecanym przez OWASP arkuszem  "ClickJacking Defense Cheat Sheet". Wyłącz tę ochronę, jeśli chcesz włączyć ramki dla interfejsu graficznego [GUI] twojego sklepu internetowego na stronach internetowych podmiotów trzecich; lub jeśli już wdrożyłeś tę ochronę w inny sposób.',
'txtUseXFrame' => 'Użyj nagłówka odpowiedzi X-Frame-Options',
'txtUseFrameKill' => 'Użyj skryptu frame-killer:',
'txtUseContent' => 'Uzyj nagłówka odpowiedzi Content-Security-Policy',
'txtDoNotUse' => 'Nie używaj',
'txtDeny' => 'Odmów',
'txtSameOrigin' => 'SameOrigin',
'txtFrameAncestorsNone' => 'frame-ancestors \'none\'',
'txtFrameAncestorsSefl' => 'frame-ancestors \'sefl\'',
'txtContentSniffing' => 'Content Sniffing Prevention',
'txtDesContent' => 'Zobacz stronę Wikipedii, aby uzyskać więcej informacji. Jeśli zaznaczysz tę opcję, wtyczka UPS zabezpieczy Twój Front Office poprzez implementację
<br><span class="label label-default">X-Content-Type-Options: nosniff</span>
<br>nagłówka odpowiedzi HTTP na wszystkie strony Front Office PrestaShop. To zabezpieczy przeglądarkę klienta (w szczególności Internet Explorer) przed MIME-sniffingiem odpowiedzi od zadeklarowanego typu zawartości. Być może nie jest to konieczne, ponieważ użytkownicy nie powinni przesyłać potencjalnie szkodliwych plików do swojego sklepu internetowego (np. JavaScript zamiast obrazu). Jednak nadal dobrym zwyczajem jest włączanie tego specjalnego nagłówka bezpieczeństwa, ponieważ nie wiąże się z nim spadek wydajności.',
'txtEnablesCross' => 'Umożliwia stosowanie filtrów skryptów cross-site w przeglądarkach obsługujących dyrektywę X-XSS-Protection',
'txtDescCrossScripting' => 'Zobacz stronę Wikipedii, aby uzyskać więcej informacji. Jeśli zaznaczysz tę opcję, wtyczka UPS zabezpieczy Twój Front Office poprzez implementację
<br><span class="label label-default">X-XSS-Protection: "1; mode=block"</span>
<br>nagłówka odpowiedzi HTTP na wszystkie strony Front Office PrestaShop.',
'txtProtectEshop' => 'Chroń swoją witrynę internetową przed atakami aktualizacji wstecznej protokołu i przechwytywaniem plików cookie',
'txtDescStrictTransport' => 'Zobacz stronę Wikipedii, aby uzyskać więcej informacji. Jeśli zaznaczysz tę opcję, wtyczka UPS zabezpieczy Twój Front Office poprzez implementację<br><span class="label label-default">Strict-Transport-Security "max-age=31536000; includeSubDomains"</span><br>nagłówka odpowiedzi HTTP na wszystkie strony Front Office PrestaShop.',
'txtPrevent' => 'Chroń wrażliwe strony przed buforowaniem przez przeglądarkę.',
'txtDescCaching' => 'Zobacz najczęściej zadawane pytania na stronie OWASP, aby uzyskać więcej informacji. Jeśli zaznaczysz tę opcję, wtyczka UPS zabezpieczy Twój Front Office poprzez implementację
<br><span class="label label-default">ache-Control: no-cache, no-store<br>Expires: 0<brPragma: no-cache</span><br>nagłówka odpowiedzi HTTP na odpowiednie strony.',
'txtApplytoPages' => 'Zastosuj do stron',
'txtAll' => 'Wszystkie',
'txtCheckOnly' => 'Tylko potwierdzenie zamówienia (checkout)',
];
