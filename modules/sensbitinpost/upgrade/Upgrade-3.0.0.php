<?php
/* *
 * MODUŁ ZOSTAŁ UDOSTĘPNIONY NA PODSTAWIE LICENCJI NA JEDNO STANOWISKO/DOMENĘ
 * NIE MASZ PRAWA DO JEGO KOPIOWANIA, EDYTOWANIA I SPRZEDAWANIA
 * W PRZYPADKU PYTAŃ LUB BŁĘDÓW SKONTAKTUJ SIĘ Z AUTOREM
 *
 * ENGLISH:
 * MODULE IS LICENCED FOR ONE-SITE / DOMAIM
 * YOU ARE NOT ALLOWED TO COPY, EDIT OR SALE
 * IN CASE OF ANY QUESTIONS CONTACT AUTHOR
 *
 * ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** *
 *
 * EN: ODWIEDŹ NASZ SKLEP PO WIĘCEJ PROFESJONALNYCH MODUŁÓW PRESTASHOP
 * PL: VISIT OUR ONLINE SHOP FOR MORE PROFESSIONAL PRESTASHOP MODULES
 * HTTPS://SKLEP.SENSBIT.PL
 *
 * ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** *
 *
 * @author    Tomasz Dacka (kontakt@sensbit.pl)
 * @copyright 2016 sensbit.pl
 * @license   One-site license (jednostanowiskowa, bez możliwości kopiowania i udostępniania innym)
 */


require_once dirname(__FILE__).'/../sensbitinpost.php';

function upgrade_module_3_0_0($module)
{
    $true = $module->installTabs();
    $true = $true && SensbitInpostTools::installSql(array(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sensbitinpost_dispatch` (
             `id_dispatch_order` int(11) NOT NULL AUTO_INCREMENT,
             `dispatch_order` varchar(50) NOT NULL,
             `status` varchar(50) NOT NULL,
             `id_pickup_sender` int(11) NOT NULL DEFAULT 0,
             `options` longtext NOT NULL,
             `date_add` datetime NOT NULL,
             `date_upd` datetime NOT NULL,
             PRIMARY KEY (`id_dispatch_order`),
             KEY `dispatch_order` (`dispatch_order`)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sensbitinpost_dispatch_shipment` (
             `id_dispatch_order` int(11) NOT NULL,
             `id_shipment` int(11) NOT NULL,
             PRIMARY KEY (`id_dispatch_order`,`id_shipment`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sensbitinpost_pickup_sender` (
                `id_pickup_sender` int(11) NOT NULL AUTO_INCREMENT,
                `pickup_sender_name` varchar(255) NOT NULL,
                `address` varchar(255) NOT NULL,
                `building_number` varchar(255) NOT NULL,
                `country_code` varchar(5) NOT NULL,
                `city` varchar(255) NOT NULL,
                `name` varchar(255) NOT NULL,
                `company` varchar(255) NOT NULL,
                `phone` varchar(50) NOT NULL,
                `post_code` varchar(50) NOT NULL,
                `default` tinyint(1) NOT NULL,
                PRIMARY KEY (`id_pickup_sender`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
    ));
    return $true;
}
