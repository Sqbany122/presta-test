<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

return [
'txtSeEn' => 'Amélioration de la sécurité',
'txtByInstalling' => "En installant le plug-in UPS, il vous est désormais possible de recourir à l’option d’appliquer les améliorations de sécurité recommandées par UPS à votre boutique en ligne. En activant celles citées ci-dessous, vous pourrez la rendre moins vulnérable à certaines des cyberattaques les plus courantes. Nous vous rappelons que vous pouvez désactiver les améliorations de sécurité recommandées par UPS si vous avez déjà mis en œuvre ces mesures d’une autre manière (en configurant correctement vos serveurs Web, en codant vos propres hooks PrestaShop ou en utilisant un autre plug-in qui vise à apporter des améliorations similaires).",
'txtDesContent' => 'Consultez Wikipédia pour plus de détails. Si vous cochez cette option, le plug-in UPS sécurisera les pages consultées par vos clients dans votre boutique en ligne en mettant en œuvre
<br><span class="label label-default">X-Content-Type-Options: nosniff</span>
<br>En-tête de réponse HTTP à toutes les pages consultées par vos clients dans PrestaShop. Cela empêchera le navigateur du client (notamment Internet Explorer) de faire une détection MIME d’une réponse en dehors du type de contenu annoncé. Il se peut que vous n’en ayez pas vraiment besoin, car vos utilisateurs ne sont pas censés charger des fichiers potentiellement dommageables sur votre boutique en ligne (p. ex., fichier en JavaScript au lieu d’une image). Cependant, il est toujours de bonne pratique d’activer cet en-tête de sécurité spécifique, car aucun problème de performance n’y est associé.',
'txtDescCrossScripting' => 'Consultez Wikipédia pour plus de détails. Si vous cochez cette option, le plug-in UPS sécurisera les pages consultées par vos clients dans votre boutique en ligne en mettant en œuvre
<br><span class="label label-default">X-XSS-Protection: "1; mode=block"</span>
<br>En-tête de réponse HTTP à toutes les pages consultées par vos clients dans PrestaShop.',
'txtDescStrictTransport' => 'Consultez Wikipédia pour plus de détails. Si vous cochez cette option, le plug-in UPS sécurisera les pages consultées par vos clients dans votre boutique en ligne en mettant en œuvre
<br><span class="label label-default">Strict-Transport-Security "max-age=31536000; includeSubDomains"</span>
<br>En-tête de réponse HTTP à toutes les pages consultées par vos clients dans PrestaShop.',
'txtDescCaching' => 'Consultez la FAQ d’OWASP pour plus de détails. Si vous cochez cette option, le plug-in UPS sécurisera le processus de paiement en mettant en œuvre
<br><span class="label label-default">Cache-Control: no-cache, no-store
<br>Expires: 0
<br>Pragma: no-cache</span>
<br>En-têtes de réponse HTTP vers les pages correspondantes',
'txtSeEnAc' => 'ACTIVATION DES AMÉLIORATIONS DE SÉCURITÉ',
'txtClickjacking' => 'Détournement de clic',
'txtDescClickjacking' => 'Consultez [Wikipédia|https://fr.wikipedia.org/wiki/Détournement_de_clic] pour plus de détails. Si vous activez cette défense, le plug-in UPS sécurisera votre boutique en communiquant la politique de cadrage au navigateur de l’utilisateur via « X-Frame-Options » : option d’en-tête HTTP « Sameorigin » et en injectant un script java « frame-breaker » pour les navigateurs plus anciens. Le plug-in UPS peut sécuriser les pages consultées par vos clients dans votre boutique en ligne, conformément aux recommandations de l’aide-mémoire sur le détournement de clic d’OWASP. Désactivez cette défense si vous souhaitez activer le cadrage de l’interface graphique de votre boutique en ligne sur des sites Web tiers ou si vous avez déjà déployé cette défense autrement.',
'txtUseXFrame' => 'Utiliser l’en-tête de réponse X-Frame-Options',
'txtUseFrameKill' => 'Utiliser le script frame-killer:',
'txtUseContent' => 'Utiliser l’en-tête de réponse Content-Security-Policy',
'txtDoNotUse' => 'NE PAS UTILISER',
'txtDeny' => 'Refuser',
'txtSameOrigin' => 'SameOrigin',
'txtFrameAncestorsNone' => '\'none\' frame-ancestors',
'txtFrameAncestorsSefl' => '\'self\' frame-ancestors',
'txtContentSniffing' => 'Prévention de la détection de contenu',
'txtEnablesCross' => 'Active les filtres de scripts intersites dans les navigateurs qui prennent en charge la directive X-XSS-Protection',
'txtProtectEshop' => 'Protégez le site Web de votre boutique en ligne contre les attaques de dégradation de protocole et le détournement de cookie',
'txtPrevent' => 'Empêche les pages sensibles d’être mises en cache par le navigateur.',
'txtApplytoPages' => 'Appliquer aux pages',
'txtAll' => 'Tous',
'txtCheckOnly' => 'Paiement seulement'
];