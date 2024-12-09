#Back-office Customizer Module

##Presentation

###Overview
Save time and improve your shop management and customer service by customizing your back-office to fit your personal needs. All your activity data is available instantly in the products, orders and customers lists...

###What this module does for you.
Ease your daily catalog, orders and customer service tasks
Save time on back-office navigation and consultation in your product catalog, orders, customer accounts, carts, messages, etc.
Adapt your back-office to fit your own needs by selecting the columns and information to display in the products, customers, orders lists...
Get quick access to the important informations of your shop.
Filter easily on the columns of your choice.

###Features
Complete customization of products, orders, customers, carts, messages (customer service), categories and merchandise returns lists.
All your shop data is availably directly without having to consult the detailled product, order or client page.
Instantly displays information that is usually located in the detail pages (example : product combinations, list of products in a cart or order, etc.)
Numerous links allow quick navigations through screens to facilitate your daily management and your customer service (link from your orders to the customer page, link to the customer thread related to an order, etc.)
Access to statistics per product (monthly/annual sales, monthly/annual quantities sold, etc.)
New search fields to find the informations you need easily.
CSV export for all the information listed

Informations available on the products list :
- ID, Image, Name, Reference, Category, Base price, Final pric, Quantity, Statu, EAN-13 or JAN barcode, UPC, Additional shipping cost, Wholesale price, Available, Online only, Customizable, On sale, Advanced Stock Management, Short description, Description, Friendly URL, Meta description, Meta title, Tax rules groups, Manufacturer, Default supplier, Attachments, Combinations, Features, Suppliers, Warehouses, Type, Visibility, Status, Tags, Profit, Profit percentage, Sales this month, Profit margin, Multiplying factor, Sales last 30 days, Sales this year, Quantity sold this month, Quantity sold last 30 days, Quantity sold this year

Informations available on the orders list :
- ID, Reference, New client, Delivery, Link to customer, Total, Total (Tax excl.), Total taxes, Products (tax excl.), Products (tax incl.), Total vouchers (Tax excl.), Total vouchers (Tax incl.), Total shipping (Tax excl.), Shipping price (Tax incl.), Payment, Status, Date, PDF,  Email, Delivery address, Delivery phone number, Invoice address, Invoice phone number, Messages, Thread status, Private note, Products, Payments, Returned products, Links to tracking numbers, Carriers, Link to cart, Vouchers, Link to download order slip, Invoice country, Invoice city, Profit, Profit margin

Informations available on the customers list :
- ID, Social title, First name, Last name, Email address, Sales, Active, Newsletter, Opt-in, Registration, Last visit, Address, Orders, Carts, Birthday, Age, Language, Groups, Private note, Last order, Products (Tax excl.), Discounts, Abandoned carts, Ordered carts, Current thread

Informations available on the customer service list :
- ID, Customer, Email, Type, Language, Status, Employee, Messages, Last message, First message, Link to customer, Link to order, Link to messages, Number of messages, Show last message

Informations available on the carts list :
- ID, Order ID,  Total (Tax incl.), Carrier, Date, Online, Products list, Link to order, Create order from cart, Link to customer

Informations available on the categories list :
- ID, Name, Description, Position, Displayed, Image, Meta title, Meta description, Meta tags, Friendly URL, Group access, Number of products, Link to products filtered on the category

Informations available on the merchandise returns list :
- ID, Order ID, Status, Date issued, Link to order, Link to customer, Returned products

###What your customers will like
- A more efficient customer service, since it is easier to retrieve your customer information.
- A more up-to-date catalog thanks to a facilitated product management.
- Better logistics through an improved order tracking

##How-to's

###Installation
Simply use the Prestashop automated installer : Your module is ready to use !
- Go to « Modules » in your back-office
- Click on « add new module » in the top right corner
- Upload ambjolisearch.zip
- Click on « install »

###Configuring the module
The list configurations is accessible :
- through the module "configure" button in the modules list
- through the menu link "Administration > Customize lists".
Activate the customization of any of the following lists :
- Catalog >> Products
- Catalog >> Categories
- Orders >> Orders
- Orders >> Merchandise returns
- Customers >> Customers
- Customers >> Shopping carts
- Customers >> Customer service
On any of these lists, if the bocustomizer is enabled, it is possible to access directly to the configuration panel by clicking the little "gear" button on the top right of the list.

###Recommandation
Depending on your catalog size or your customer and order history, the display of too many columns may harm visibility and response time. Try to always favorise the display of only the most pertinent information for your daily use.

##About the author

Ambris Informatique SARL is a consulting and IT services company mainly focused on small and medium-sized companies. We are currently focusing on the development of prestashop modules. Our main realization at the moment is the JoliSearch module, an ajax-based instant search featuring a beautified dropdown list and auto-corrective searches.

#Release notes
- --
## 1.0.0
- Initial release
- Available editable lists are AdminCarts, AdminCategories, AdminCustomers, AdminCustomerThreads, AdminOrders, AdminProducts and AdminReturn

- --
## 1.1.0
- Added views to easily switch between different configurations
- Fixed a bug on installation due to the language

### 1.1.1
- Fixed tab installation for languages (unexisting method Language::getIds before 1.6.1)
- Prevented unwanted calls of AmbData because of hooks.

- --
## 1.2.0
- Prestashop 1.5 compatibility
- Prices rules list for products

### 1.2.1
- Only load JS files when necessary

### 1.2.2
- Allow fields override
- Fix % bug in orders
- Reset filters on column toggle

### 1.2.3
- Fix ambiguous email db field in scores query
- Fix list of specific prices of products
- Fix issue with bad commas in SQL queries

### 1.2.4
- Fix permanent reset of filter

### 1.2.5
- Release notes are now part of the Readme.md
- Add Invoices and Order Slips to orders page (not translated yet)
- Fix function signature in AdminController override

### 1.2.6
- Quickfix disable generateTranslatables in production

### 1.2.7
- fix prefix issues in fields json

### 1.3.0
- fix issues with php version 5.3 (+ remove backtrace usage)
- prevent installation of overrides when prestashop version >= 1.6
- fix prefixes in queries in json fields
- remove notices
- fix multiple cookie generation issue

### 1.3.1
- Fix compatibility issue with php 7

### 1.3.2
- Add custom callback system for easier custom implementations

### 1.3.3
- Fix viewchanged for earlier php versions
- Fix order_by issue (temporary)

### 1.3.4
- Maintain sort orders between views if sort column exists