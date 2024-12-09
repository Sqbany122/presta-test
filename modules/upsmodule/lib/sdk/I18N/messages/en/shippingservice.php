<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

return [
'ttlSPService' => 'Shipping Services',
'txtDesc' => 'Select the UPS services you want to be visible in the checkout for your e-shoppers. To learn more about UPS services, refer UPS Tariff/Terms and Conditions of Service on UPS.COM',
'ttlAP' => 'Deliver to UPS Access Point™ (to-AP delivery)',
'ttlAPUS' => 'Ship to a UPS Access Point location (to-AP delivery)',
'ttlAP2' => 'Deliver to an Access Point™', 
'ttlAP2US' => 'Deliver to a UPS Access Point™ location',
'txtAPDesc' => 'UPS delivers the parcel to a UPS Access Point™ that the customer selected, then customer picks up their order there',
'txtAPDescUS' => 'UPS delivers the parcel to a UPS Access Point location selected by the customer for them to collect at their convenience',
'txtShipOption' => 'Set as default shipping option',
'txtSelect' => 'Select the shipping services for your customers to choose',
'outside_eu' => 'International shipping only - visible when shipping address is outside of EU',
'outside_us' => 'International shipping only - visible when delivery address is outside the U.S.',
'outside_us_line1' => '(to U.S.)',
'outside_us_line2' => '(to international)',
'txtAPSetting' => 'Access Point display setting',
'txtAPSettingUS' => 'UPS Access Point display setting',
'txtAPNumber' => 'Number of Access Points visible to customers',
'txtAPNumberUS' => 'Number of locations visible to customers',
'txtAPRange' => 'Display all the Access Points in range of',
'txtAPRangeUS' => 'Display all the locations in range of',
'txtAPRangeDesc' => 'kilometer around consignee\'s selected address',
'txtAPRangeDescUS' => 'Miles around consignee\'s selected address',
'txtChoose' => 'Choose Account Number for this option',
'ttlAD' => 'Deliver to consignee address (to-address delivery)',
'txtADDesc' => 'UPS delivers the parcel to the shipping address provided by customer.',
'txtCot' => 'Cut off time',
'txtCotDesc' => 'The cut off time selected here will be used in calculating the schedule delivery date and time, which will be displayed on your website checkout.',
'txtCotDesc2' => 'For example, if the cut off time is selected as 5 PM and your e-shoppers views your webstore at 5.01 PM, all UPS scheduled delivery dates and time will be calculated from the following business day. When deciding this cut off time, please ensure that you have sufficient time to fulfill the order before the UPS scheduled pick up or you are able to drop off the packages at a UPS Access Point™.',
'txtCotDesc1US' => 'Order checkouts before this time will display delivery dates based on same day fulfillment. Order checkouts after this time will display delivery dates based on next day fulfillment.',
'txtCotDesc2US' => 'Selecting “Disable” will result in approximate delivery dates being displayed without fulfillment considerations.',
'txtCotDesc3' => 'If you are unsure about fulfilling customer orders on the same day, you can select "Disable" which will result in the e-shopper seeing a generic delivery schedule for each available UPS service. For Example; "UPS® Standard - In most cases, delivered within 1 to 3 business days in Europe."',
'txtCotDesc3US' => '',
'ID'                => 'ID',
'customer'          => 'Customer',
'total'             => 'Total',
'status'            => 'Status',
'date'              => 'Date',
'PDF'               => 'PDF',
'txtDeliveryHeader1' => '1. Real time shipping rates – the merchant’s delivery rates are displayed to the e-shopper based on the default package dimensions configured in the previous screen.  The delivery rates include base transportation and fuel charges, so merchants may adjust the “% of UPS shipping rates” field to cover their common ad hoc charges. <br>
Ad hoc charges can be found in the Daily Rate and Service Guide posted here: https://www.ups.com/us/en/shipping/daily-rates.page',
'txtDeliveryHeader2' => 'Flat rates – merchants set delivery rates that display to the e-shopper based on their order value.  Orders less than or equal to the specified “order value threshold” will show the delivery rate entered for that threshold.  Any order value above the highest threshold will default to a delivery rate of $0.
Example: an “order value threshold” of $50 with a delivery rate of $15 will show a delivery rate of $15 for orders less than or equal to $50 and a delivery rate of $0 for orders over $50.',
'txtAdultSingature' => 'Do any of your orders require Adult Signatures?'
];
