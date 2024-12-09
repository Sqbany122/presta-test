<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

return [
'txtDelivery' => 'Checkout Shipping Rates',
'txtDeliveryUPSAp' => 'Ship to UPS Access Point™',
'txtDeliveryUPSApUS' => 'Ship to UPS Access Point™ location',
'txtDeliveryFlatRates' => 'Flat rates',
'txtDeliveryRealTime' => 'Real time shipping rates',
'txtDeliveryValueThresholds' => 'Order Value threshold',
'txtDeliveryRatesIs' => 'Delivery rates is ',
'txtDeliveryShippingRates' => ' % of UPS shipping rates',
'txtDeliveryYourShopper' => 'The e-shopper will see the % of real time UPS rates. These rates are calculated using the default package weight and dimensions that you configured in the previous screen (Package Dimensions).',
'txtNote' => 'Please Note : <br>1. UPS real time shipping rates are exclusive of VAT. If you want to display the rates including VAT, please add the VAT % to the shipping rate. E.g. If VAT is 23%, and you want to display rates including VAT, you should insert 123% above.<br>2. The e-shopper may select COD as a payment option within the PrestaShop checkout after the shipping rates are calculated. In this scenario, UPS COD surcharges which will be billed, are not added to the displayed shipping rates automatically. This is due to the order in which PrestaShop displays shipping and payment options.',
'txtNoteUS' => 'Please Note : 1. UPS real time shipping rates are exclusive of ad hoc charges such as residential surcharge, additional handling, delivery area surcharge, international duties & taxes, etc.  Please refer to the UPS Daily Rate and Service Guide for these charges and adjust the % of real time UPS shipping rates as necessary: <a href="https://www.ups.com/us/en/shipping/daily-rates.page">https://www.ups.com/us/en/shipping/daily-rates.page</a>?
<br>2. The e-shopper may select COD as a payment option within the PrestaShop checkout after the shipping rates are calculated. In this scenario, UPS COD surcharges which will be billed, are not added to the displayed shipping rates automatically. This is due to the order in which PrestaShop displays shipping and payment options.',
'txtDeliveryUPSAdd' => 'Ship to Address',
'txtDeliveryRates' => 'Delivery rates',
'txtDeliveryHeader0' => 'Shipping rate types:',
'txtDeliveryHeader1' => '1. Real time shipping rates – the merchant’s delivery rates are displayed to the e-shopper based on the default package dimensions configured in the previous screen.  The delivery rates include base transportation and fuel charges, so merchants may adjust the “% of UPS shipping rates” field to cover their common ad hoc charges. 
Ad hoc charges can be found in the Daily Rate and Service Guide posted here: ',
'txtDeliveryHeader1Link' => 'https://www.ups.com/us/en/shipping/daily-rates.page',
'txtDeliveryHeader2' => '2. Flat rates – merchants set delivery rates that display to the e-shopper based on their order value.  Orders less than or equal to the specified “order value threshold” will show the delivery rate entered for that threshold.  Any order value above the highest threshold will default to a delivery rate of $0.
Example: an “order value threshold” of $50 with a delivery rate of $15 will show a delivery rate of $15 for orders less than or equal to $50 and a delivery rate of $0 for orders over $50.',
];
