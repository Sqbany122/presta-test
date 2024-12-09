<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

return [
'txtDeny' => "Weigeren",
'txtCheckOnly' => "Alleen op betalingsprocedure",
'txtUseFrameKill' => "Gebruik frame-killer-script:",
'txtFrameAncestorsNone' => "frame-ancestors 'none'",
'txtSeEnAc' => "ACTIVATIE VAN BEVEILIGINGSVERBETERINGEN",
'txtContentSniffing' => "Preventie van Content Sniffing",
'txtDescClickjacking' => "Zie [wikipedia|https://en.wikipedia.org/wiki/Clickjacking] voor meer informatie. Als u deze verdediging inschakelt, zal UPS Plugin uw winkel beveiligen door het framingbeleid naar de browser van de gebruiker te communiceren met behulp van een 'X-Frame-Options: Sameorigin' HTTP-antwoordheader en door een 'frame-breaker'-javascript bij oudere browsers te injecteren.- UPS Plugin kan de Front Office-pagina's van uw digitale winkel beveiligen in overeenstemming met het door OWASP aanbevolen Clickjacking Defense Cheat Sheet. Schakel deze verdediging uit als u framing van de grafische vormgeving van uw webwinkel op websites van derden wilt inschakelen; of als u deze verdediging al op een andere manier hebt ingevoerd.",
'txtProtectEshop' => "Bescherm uw digitale winkel tegen aanvallen die uw protocol downgraden en tegen sessiehijacking.",
'txtDoNotUse' => "Niet gebruiken",
'txtAll' => "Alle",
'txtApplytoPages' => "Toepassen op pagina's",
'txtSameOrigin' => "SameOrigin",
'txtUseContent' => "Gebruik Content-Security-Policy-antwoordheader",
'txtSeEn' => "Beveiligingsverbetering",
'txtFrameAncestorsSefl' => "frame-ancestors 'sefl'",
'txtClickjacking' => "Clickjacking Defense",
'txtEnablesCross' => "Schakelt scriptfilters tussen websites in browsers in die de X-XSS-Protection-instructie ondersteune",
'txtUseXFrame' => "Gebruik X-Frame-Options-antwoordheader",
'txtPrevent' => "Voorkom dat gevoelige pagina's door de browser in het cachegeheugen worden opgeslagen.",
'txtByInstalling' => 'Door UPS Plugin te installeren hebt u deze handige optie verkregen om de door UPS aanbevolen beveiligingsverbeteringen op uw digitale winkel toe te passen. Door hieronder bepaalde beveiligingsverbeteringen in te schakelen, maakt u uw webwinkel minder kwetsbaar voor enkele gebruikelijke aanvallen door hackers.
Let op: u kunt de door UPS aanbevolen beveiligingsverbeteringen uitgeschakeld laten als u deze beveiligingsmaatregelen al op een andere manier hebt ingevoerd (door de configuratie van uw webservers op de juiste manier in te stellen, door de zogenaamde eigen hooks van PrestaShop te coderen of door andere invoegtoepassingen te gebruiken die dezelfde verbeteringen implementeren).',
'txtDesContent' => 'Zie Wikipedia voor meer informatie. Als u deze optie aanvinkt, zal UPS Plugin de Front Office van uw digitale winkel beveiligen door toepassing van 
<br><span class="label label-default">X-Content-Type-Options: nosniff</span>
<br>HTTP-antwoordheader voor alle PrestaShop Front Office-pagina\'s. Dit zal voorkomen dat de browser van de klant (met name Internet Explorer) een antwoord verwijdert van het gedeclareerde contenttype via MIME-sniffing. U hebt dit misschien niet echt nodig, omdat uw gebruikers niet geacht worden potentieel schadelijke bestanden te uploaden naar uw webwinkel (zoals JavaScript in plaats van een afbeelding). Het is echter nog steeds een goed idee om deze speciale beveiligingsheader in te schakelen, aangezien er geen nadelige gevolgen voor de prestatie mee gepaard gaan.',
'txtDescCrossScripting' => 'Zie Wikipedia voor meer informatie. Als u deze optie aanvinkt, zal UPS Plugin de Front Office van uw digitale winkel beveiligen door toepassing van 
<br><span class="label label-default">X-XSS-Protection: "1; mode=block"</span>
<br>HTTP-antwoordheader voor alle PrestaShop Front Office-pagina\'s.',
'txtDescStrictTransport' => 'Zie Wikipedia voor meer informatie. Als u deze optie aanvinkt, zal UPS Plugin de Front Office van uw digitale winkel beveiligen door toepassing van 
<br><span class="label label-default">Strict-Transport-Security "max-age=31536000; includeSubDomains"</span><br>
HTTP-antwoordheader voor alle PrestaShop Front Office-pagina\'s.',
'txtDescCaching' => 'Zie de veelgestelde vragen van OWASP voor meer informatie. Als u deze optie aanvinkt, zal UPS Plugin de betalingsprocedure beveiligen door toepassing van 
<br><span class="label label-default">Cache-Control: no-cache, no-store
<br>Vervalt: 0
<br>Pragma: no-cache</span>
<br>HTTP-antwoordheaders voor de overeenstemmende pagina\'s',
];
