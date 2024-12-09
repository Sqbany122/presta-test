<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

return [
'txtSeEn' => 'Mehr Sicherheit',
'txtSeEnAc' => 'AKTIVIERUNG DER ERWEITERTEN SICHERHEITSFUNKTIONEN',
'txtClickjacking' => 'Schutz vor Clickjacking',
'txtDescClickjacking' => 'Weitere Informationen erhalten Sie auf [https://de.wikipedia.org/wiki/Clickjacking]. Wenn Sie diesen Schutzmechanismus aktivieren, schützt das UPS-Plugin Ihren Store, indem die Rahmenrichtlinie über den HTTP-Response-Header „X-Frame-Options: Sameorigin“ an den Browser des Benutzers übermittelt wird und indem das Java-Skript „frame-breaker“ für veraltete Browser eingefügt wird. Das UPS-Plugin kann die Handelsseiten Ihres e-Shops in Übereinstimmung mit dem von OWASP empfohlenen „Spickzettel für den Schutz vor Clickjacking“ schützen. Deaktivieren Sie den Schutzmechanismus, falls Sie das Framing der GUI Ihres e-Shops auf den Webseiten Dritter erlauben möchten oder falls Sie bereits anderweitig für einen entsprechenden Schutz gesorgt haben.',
'txtUseXFrame' => 'Response-Header „X-Frame-Options“ verwenden',
'txtDoNotUse' => 'Nicht verwenden',
'txtDeny' => 'Ablehnen',
'txtSameOrigin' => 'SameOrigin',
'txtUseFrameKill' => '„Frame-Killer“-Skript verwenden:',
'txtUseContent' => 'Response-Header „Content-Security-Policy“ verwenden',
'txtFrameAncestorsNone' => 'frame-ancestors \'none\'',
'txtFrameAncestorsSefl' => 'frame-ancestors \'sefl\'',
'txtContentSniffing' => 'Schutz vor „Content Sniffing“',
'txtEnablesCross' => 'In Browsern mit Unterstützung der „X-XSS-Protection“-Richtlinie werden Filter für webseitenübergreifendes Skripting aktiviert.',
'txtProtectEshop' => 'Schützen Sie Ihre e-Shop-Webseite vor Angriffen, die das Protokoll herabsetzen, sowie vor Cookie-Diebstahl',
'txtPrevent' => 'Verhindern Sie das Caching sensibler Seiten durch den Browser.',
'txtApplytoPages' => 'Auf folgenden Seiten anwenden',
'txtAll' => 'Alle',
'txtCheckOnly' => 'Nur Bezahlvorgang',
'txtByInstalling' => 'Die Installation des UPS-Plugins bedeutet eine praktische Option für die Anwendung der von UPS empfohlenen erweiterten Sicherheitsfunktionen für Ihren e-Shop. Indem Sie bestimmte erweiterte Sicherheitsfunktionen aktivieren, schützen Sie Ihren e-Shop vor gängigen Hackangriffen.
Wenn Sie bereits auf andere Weise Sicherheitsvorkehrungen getroffen haben (durch die Optimierung der Konfigurationen Ihres Webservers, durch die Codierung eigener PrestaShop-Hooks oder durch die Verwendung eines anderen Plugins, das ähnliche Sicherheitsfunktionen bereitstellt), müssen Sie die von UPS empfohlenen erweiterten Sicherheitsfunktionen nicht aktivieren.',
'txtDesContent' => 'Auf Wikipedia finden Sie weitere Informationen. Wenn Sie diese Option aktivieren, schützt das UPS-Plugin die Handelsseiten Ihres e-Shops durch Anwendung des Response-Headers 
<br><span class="label label-default">„X-Content-Type-Options: nosniff“</span>
<br>auf allen PrestaShop-Handelsseiten. Dadurch wird verhindert, dass ein Aufspüren (MIME-Sniffing) der Antwort eines erklärten Content-Typs durch den Browser des Kunden (insbesondere Internet Explorer) erfolgt. Sie benötigen diese Funktion vermutlich nicht, da die Benutzer keine potenziell gefährlichen Dateien (z. B. ein JavaScript anstelle eines Bilds) in Ihrem e-Shop hochladen sollten. Es handelt sich dennoch um eine bewährte Verfahrensweise, da die Aktivierung dieses speziellen Security-Headers nicht mit Leistungseinbußen einhergeht.',
'txtDescCrossScripting' => 'Auf Wikipedia finden Sie weitere Informationen. Wenn Sie diese Option aktivieren, schützt das UPS-Plugin die Handelsseiten Ihres e-Shops durch Anwendung des Response-Headers
<br><span class="label label-default">„X-XSS-Protection: 1; mode=block“</span>
<br>auf allen PrestaShop-Handelsseiten.',
'txtDescStrictTransport' => 'Auf Wikipedia finden Sie weitere Informationen. Wenn Sie diese Option aktivieren, schützt das UPS-Plugin die Handelsseiten Ihres e-Shops durch Anwendung des Response Headers
<br><span class="label label-default">„Script-Transport-Security: max-age=31536000; includeSubDomains“</span><br>
auf allen PrestaShop-Handelsseiten.',
'txtDescCaching' => 'In den FAQ von OWASP erhalten Sie weitere Informationen. Wenn Sie diese Option aktivieren, schützt das UPS-Plugin den Bezahlvorgang durch Anwendung des Response-Headers
<br><span class="label label-default">„Cache-Control: no-cache, no-store
<br>Expires: 0
<br>Pragma: no-cache“</span>
<br>auf den entsprechenden Seiten',
];
