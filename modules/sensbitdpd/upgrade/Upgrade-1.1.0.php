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
 * HTTPS://sensbit.pl
 *
 * ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** *
 *
 * @author    Tomasz Dacka (kontakt@sensbit.pl)
 * @copyright 2016 sensbit.pl
 * @license   One-site license (jednostanowiskowa, bez możliwości kopiowania i udostępniania innym)
 */


require_once dirname(__FILE__).'/../sensbitdpd.php';

function upgrade_module_1_1_0($module)
{
    /* Usunięcie niepotrzebnych plików - pozostałości po innym module */
    unlink(dirname(__FILE__).'/Upgrade-1.3.2.php');
    unlink(dirname(__FILE__).'/Upgrade-2.0.0.php');
    unlink(dirname(__FILE__).'/Upgrade-2.0.1.php');
    unlink(dirname(__FILE__).'/Upgrade-2.6.0.php');
    unlink(dirname(__FILE__).'/Upgrade-2.8.0.php');
    unlink(dirname(__FILE__).'/Upgrade-2.8.1.php');

    /* Dogranie dodatkowych tabel i sql */
    $install_sql = array(
        'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sensbitdpd_pickup_sender` (
                `id_pickup_sender` int(11) NOT NULL AUTO_INCREMENT,
                `pickup_sender_name` varchar(255) NOT NULL,
                `address` varchar(255) NOT NULL,
                `city` varchar(255) NOT NULL,
                `name` varchar(255) NOT NULL,
                `company` varchar(255) NOT NULL,
                `phone` varchar(50) NOT NULL,
                `post_code` varchar(50) NOT NULL,
                `default` tinyint(1) NOT NULL,
                PRIMARY KEY (`id_pickup_sender`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;',
        'ALTER TABLE `'._DB_PREFIX_.'sensbitdpd_dispatch` ADD `id_pickup_sender` INT(11) NOT NULL DEFAULT 0;'
    );

    /* Dogranie nowej zakładki */
    $tabs = array(
        array(
            'name' => array(
                'en' => 'Points',
                'pl' => 'Punkty odbioru dla kuriera'
            ),
            'class' => 'AdminSensbitDpdPickupSender',
            'parent' => 'AdminSensbitDpd'
        ),
    );

    foreach ($tabs as $tab) {
        SensbitDpdTools::createTab($tab['name'], $tab['class'], $tab['parent'], $module->name);
    }

    return SensbitDpdTools::installSql($install_sql);
}
