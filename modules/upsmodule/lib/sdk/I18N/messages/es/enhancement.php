<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

return [
'txtSeEn' => 'Mejora de seguridad',
'txtSeEnAc' => 'ACTIVACIÓN DE LAS MEJORAS DE SEGURIDAD',
'txtClickjacking' => 'Protección contra el clickjacking',
'txtDescClickjacking' => 'Para más información, consulta [wikipedia|https://en.wikipedia.org/wiki/Clickjacking]. Si activas dicha protección, el complemento de UPS protegerá tu tienda comunicando la política de framing al navegador del usuario mediante el encabezado HTTP de respuesta "X-Frame-Options: Sameorigin" e introduciendo un script frame-breaker de Java para los navegadores tradicionales.- El complemento de UPS puede proteger las páginas de Servicio al cliente de tu tienda en línea de acuerdo con la Hoja de referencia sobre protección contra el clickjacking recomendada por OWASP. Desactiva esta protección si quieres permitir el framing de la interfaz gráfica del usuario de tu tienda en línea en sitios web de terceros o si ya has implementado esta protección de otra forma.',
'txtUseXFrame' => 'Usa el encabezado de respuesta X-Frame-Options',
'txtDoNotUse' => 'No uses',
'txtDeny' => 'Deny',
'txtSameOrigin' => 'SameOrigin',
'txtUseFrameKill' => 'Usa el script frame-killer:',
'txtUseContent' => 'Usa el encabezado de respuesta Content-Security-Policy',
'txtFrameAncestorsNone' => "frame-ancestors 'none'",
'txtFrameAncestorsSefl' => "frame-ancestors 'sefl'",
'txtContentSniffing' => 'Prevención contra el rastreo de contenido',
'txtEnablesCross' => 'Activa los filtros de scripting cross-site en navegadores compatibles con la X-XSS-Protection',
'txtProtectEshop' => 'Protege el sitio web de tu tienda en línea contra los ataques de degradación del protocolo y el pirateo de las cookies.',
'txtPrevent' => 'Evita que el navegador almacene las páginas confidenciales.',
'txtApplytoPages' => 'Aplicar a las páginas',
'txtAll' => 'Todas',
'txtCheckOnly' => 'Solo las del proceso de pago',
'txtByInstalling' => 'Mediante la instalación del complemento de UPS, conseguirás esta práctica opción que implementa los Aumentos de seguridad recomendados por UPS a tu tienda en línea. Para hacerla menos vulnerable a los ataques más comunes de los piratas informáticos, activa las siguientes mejoras de seguridad.
Ten en cuenta que puedes tener desactivadas las mejoras de seguridad recomendadas por UPS si ya has implementado estas medidas de seguridad de otra forma: ajustando correctamente la configuración de tus servidores web, codificando tus propios hooks de PretaShop o usando otro complemento que implemente mejoras similares.',
'txtDesContent' => 'Consulta Wikipedia para obtener más información. Si marcas esta opción, el complemento de UPS protegerá el Servicio al cliente de tu tienda en línea aplicando el encabezado HTTP de respuesta 
<br><span class="label label-default">X-Content-Type-Options: nosniff</span>
<br>en todas las páginas de Servicio al cliente de PretaShop Esto evitará que el navegador del cliente (principalmente Internet Explorer) realice el olfateo MIME a una respuesta fuera del content-type declarado. Quizás no necesites esto, ya que los usuarios no deberían subir archivos potencialmente peligrosos a tu tienda en línea (por ejemplo, JavaScript en lugar de una imagen). Sin embargo, no pasa nada por activar este encabezado de seguridad especial, puesto que no se cree que afecte al rendimiento.',
'txtDescCrossScripting' => 'Consulta Wikipedia para obtener más información. Si marcas esta opción, el complemento de UPS protegerá el Servicio al cliente de tu tienda en línea aplicando 
<br><span class="label label-default">el encabezado HTTP de respuesta X-XSS-Protection:"1; mode=block"</span>
<br>a todas las páginas de Servicio al cliente de PrestaShop.',
'txtDescStrictTransport' => 'Consulta Wikipedia para obtener más información. Si marcas esta opción, el complemento de UPS protegerá el Servicio al cliente de tu tienda en línea aplicando 
<br><span class="label label-default">el encabezado HTTP de respuesta Strict-Transport-Security "max-age=31536000; includeSubDomains"</span><br>
a todas las páginas de Servicio al cliente de PrestaShop.',
'txtDescCaching' => 'Consulta las preguntas frecuentes de OWASP para obtener más información. Si marcas esta opción, el complemento de UPS protegerá el proceso de pago aplicando los encabezados HTTP de respuesta 
<br><span class="label label-default">Cache-Control: no-cache, no-store 
<br>Expires: 0 
<br>Pragma: no-cache</span>
<br>a las páginas correspondientes',
];
