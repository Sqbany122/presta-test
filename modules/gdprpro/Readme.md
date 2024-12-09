# GDPR Compliance Pro

The GDPR module for PrestaShop is an All-In-One solution that, once set-up is completed, will assure compliance with the new GDPR legislation and will allow the online store to function legally while avoiding fines.

## Benefits

* This is the single module out there which is covering exactly what GDPR means. AS a Shop owner you need to find a way to give the Visitor an option to Opt-out from any Cookies which the site uses. WE made sure that by a simple selection from the module list you can offer the options to the client.
* The module allows the client to opt-out from : Facebook Pixel, Google Tag Manager, Google Ad words Tracking, Standard Prestashop Tracking!
* Assures compliance with the GDPR legislation allowing the store to operate legally, avoiding fines of up to 20 million euros or 4% of the global annual turnover.
* The pop-up presented to first-time visitors is fast, responsive and we made it as unintrusive as possible to make sure that possible clients are not turned away by it.
* Shop owners or data protection officers can be quickly notified if anyone requests the erasure of their data and act on this request in the shortest time possible.
* The GDPR module allows adding detailed description about what personal data is needed and for what purposes it is used, ensuring that customers can make an informed decision when opting to activate other modules on the site.
* Customers will be informed of what personal data is required of them and how it will be used.
* Customers can choose which modules they want to keep active during their visit on the site.
* A special link in the footer allows them to re-open the pop-up and customize their preferences at any time during their visit.
* Customers can request to have their personal data erased from the store at any time.

## Requirements
* PHP 5.6+
* PrestaShop 1.6.0.4+

## JS Events

You can watch which cookies were changed by with the `gdprModuleCheckBoxesChanged` event

```javascript
$(document).on('gdprModuleCheckBoxesChanged', function (event, name, isEnabled) {

});
```

## Body css classes

Every time when a module/script gets disable or enabled the bodies class is changed. For example if you have the `ps_currencyselector` enabled the body will have the `ps-currencyselector-on` class otherwise `ps-currencyselector-off`. (Please note that the underscores are replaced by the minus '-' sign).

**Note**: currently this only works with PrestaShop v1.7