<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

return [
'txtDeny' => 'Deny',
'txtCheckOnly' => 'Solo Checkout',
'txtUseFrameKill' => 'Usare script frame-killer:',
'txtFrameAncestorsNone' => 'frame-ancestors \'none\'',
'txtSeEnAc' => 'ATTIVAZIONE DEL POTENZIAMENTO DELLA SICUREZZA',
'txtContentSniffing' => 'Prevenzione Content Sniffing',
'txtDescClickjacking' => 'Consulta [wikipedia|https://it.wikipedia.org/wiki/Clickjacking] per ulteriori informazioni. Attivando questa difesa, il plugin UPS renderà sicuro il tuo negozio comunicando la framing policy al browser dell’utente attraverso l’intestazione di risposta HTTP "X-Frame-Options: Sameorigin" e inserendo un "frame-breaker" java script per i browser precedenti. Il plugin UPS può mettere al sicuro le pagine di Front Office del tuo negozio online in conformità con la guida di difesa contro il clickjacking consigliata da OWASP. Disabilita questo meccanismo di difesa se desideri abilitare il framing dell’interfaccia grafica del tuo negozio su siti di terzi, oppure se hai già implementato queste difese in altri modi.',
'txtProtectEshop' => 'Proteggi il tuo sito di e-commerce contro attacchi di indebolimento del protocollo e cookie hijacking (dirottamento dei cookie)',
'txtDoNotUse' => 'Non usare',
'txtAll' => 'Tutte',
'txtApplytoPages' => 'Applica alle pagine',
'txtSameOrigin' => 'SameOrigin',
'txtUseContent' => 'Usare intestazione di risposta Content-Security-Policy',
'txtSeEn' => 'Potenziamento della sicurezza',
'txtFrameAncestorsSefl' => 'frame-ancestors \'sefl\'',
'txtClickjacking' => 'Difesa contro il clickjacking',
'txtEnablesCross' => 'Abilita filtri Cross-Site Scripting nei browser che supportano la direttiva X-XSS-Protection',
'txtUseXFrame' => 'Usare intestazione di risposta X-Frame-Options',
'txtPrevent' => 'Previene il caching di pagine sensibili a seconda del browser.',
'txtByInstalling' => 'Con l’installazione del plugin UPS hai acquisito questa comoda opzione per applicare al tuo negozio e-commerce i potenziamenti della sicurezza raccomandati da UPS. Attivando i particolari potenziamenti della sicurezza qui sotto potrai rendere il tuo sito e-commerce meno vulnerabile ad alcuni attacchi comuni degli hacker.
Nota che puoi lasciare disabilitati i potenziamenti di sicurezza consigliati da UPS se hai già implementato queste misure di sicurezza in altri modi (modificando correttamente la configurazione dei tuoi server, programmando i tuoi hook PrestaShop o utilizzando un qualche altro plugin il cui obiettivo è quello di implementare simili potenziamenti).',
'txtDesContent' => 'Consulta Wikipedia per ulteriori informazioni. Selezionando questa opzione, il plugin UPS renderà sicuro il Front Office del tuo negozio applicando 
<br><span class="label label-default">X-Content-Type-Options: nosniff</span>
<br>Intestazione di risposta HTTP in tutte le pagine Front Office di PrestaShop. Questo impedirà al browser dell’utente (in particolare Internet Explorer) di fare “MIME-sniffing” cercando una risposta diversa dal tipo di contenuto dichiarato. Questa funzione potrebbe non servire davvero, dal momento che i tuoi clienti non dovrebbero caricare alcun tipo di file potenzialmente dannoso sul tuo negozio online (ad es. JavaScript al posto di un immagine). Tuttavia è sempre buona prassi abilitare questa intestazione speciale di sicurezza, poiché a essa non sono associati cali di performance.',
'txtDescCrossScripting' => 'Consulta Wikipedia per ulteriori informazioni. Selezionando questa opzione, il plugin UPS renderà sicuro il Front Office del tuo negozio applicando 
<br><span class="label label-default">X-XSS-Protection: "1; mode=block"</span>
<br>Intestazione di risposta HTTP in tutte le pagine Front Office di PrestaShop.',
'txtDescStrictTransport' => 'Consulta wikipedia per ulteriori informazioni. Selezionando questa opzione, il plugin UPS renderà sicuro il Front Office del tuo negozio online applicando 
<br><span class="label label-default">Strict-Transport-Security "max-age=31536000; includeSubDomains"</span><br>
Intestazione di risposta HTTP in tutte le pagine Front Office di PrestaShop.',
'txtDescCaching' => 'Consulta le domande frequenti OWASP per ulteriori informazioni. Selezionando questa opzione, il plugin UPS renderà sicuro il procedimento di Checkout applicando 
<br><span class="label label-default">Cache-Control: no-cache, no-store
<br>Expires: 0
<br>Pragma: no-cache</span>
<br>Intestazioni di risposta HTTP alle pagine corrispondenti',
];
