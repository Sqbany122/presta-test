<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

return [
'txtSeEn' => 'Security Enhancement',
'txtSeEnAc' => 'Security Enhancements Activation',
'txtByInstalling' => 'By installing UPS Plugin you gained this convenient option to apply UPS recommended Security Enhancements to your e-shop.By enabling particular security enhancements below you can make your e-shop less vulnerable to some common hackers\' attacks.<br>Note, you can keep UPS recommended Security Enhancements disabled if you have already implemented these security measures in other way (by tweaking your web servers configurations properly, by coding own PrestaShop hooks or by using some other plugin that aims to implement the similar enhancements).',
'txtClickjacking' => 'Clickjacking Defense',
'txtDescClickjacking' => 'See [wikipedia|https://en.wikipedia.org/wiki/Clickjacking] for more details. If you enable this defense, then UPS plugin will secure your store by communicating framing policy to user\'s browser through "X-Frame-Options: Sameorigin" HTTP response header and by injecting "frame-breaker" java script for legacy browsers.- UPS Plugin can secure your e-shop Front Office pages in accordance with OWASP recommended Clickjacking Defense Cheat Sheet Disable this defense if you wish to enable framing of your e-shop GUI on 3rd party websites; or if you have already implemented this defense differently.',
'txtUseXFrame' => 'Use X-Frame-Options response header',
'txtUseFrameKill' => 'Use frame-killer script:',
'txtUseContent' => 'Use Content-Security-Policy response header',
'txtDoNotUse' => 'Do not use',
'txtDeny' => 'Deny',
'txtSameOrigin' => 'SameOrigin',
'txtFrameAncestorsNone' => 'frame-ancestors \'none\'',
'txtFrameAncestorsSefl' => 'frame-ancestors \'sefl\'',
'txtContentSniffing' => 'Content Sniffing Prevention',
'txtDesContent' => 'See wikipedia for more details.If you check this option, UPS plugin will secure your e-shop Front Office by applying
<br><span class="label label-default">X-Content-Type-Options: nosniff</span>
<br>HTTP Response Header to all PrestaShop Front Office pages. That will prevent the client\'s browser (notably Internet Explorer) from MIME-sniffing a response away from the declared content-type. You may not really need this as your users are not supposed to upload any potentially harmful files to your e-shop (e.g. JavaScript instead of an Image). However, it is still good practice to enable this special security header as there is no performance penalty associated with it.',
'txtEnablesCross' => 'Enables cross-site scripting filters in browsers that support the X-XSS-Protection directive',
'txtDescCrossScripting' => 'See wikipedia for more details.If you check this option, UPS plugin will secure your e-shop Front Office by applying
<br><span class="label label-default">X-XSS-Protection: "1; mode=block"</span>
<br>HTTP response header to all PrestaShop Front Office pages.',
'txtProtectEshop' => 'Protect your e-shop website against protocol downgrade attacks and cookie hijacking',
'txtDescStrictTransport' => 'See wikipedia for more details. If you check this option, UPS plugin will secure your e-shop Front Office by applying
<br><span class="label label-default">Strict-Transport-Security "max-age=31536000; includeSubDomains"</span>
<br>HTTP response header to all PrestaShop Front Office pages.',
'txtPrevent' => 'Prevent sensitive pages from caching by browser.',
'txtDescCaching' => 'See OWASP FAQs for more details. If you check this option, UPS plugin will secure the Checkout process by applying<br><span class="label label-default">Cache-Control: no-cache, no-store<br>Expires: 0<br>Pragma: no-cache</span><br>HTTP response headers to the corresponding pages',
'txtApplytoPages' => 'Apply to pages',
'txtAll' => 'All',
'txtCheckOnly' => 'Checkout only',
];
