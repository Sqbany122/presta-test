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

function upgrade_module_5_8_0($module)
{
    if (SensbitInpostTools::installSql(array(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sensbitinpost_template_customer_group` (
            `id_template` int(11) NOT NULL,
            `id_customer_group` int(11) NOT NULL,
            PRIMARY KEY (`id_template`,`id_customer_group`)
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
        ))) {

        $customer_groups = Group::getGroups(Context::getContext()->language->id);

        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'sensbitinpost_template_customer_group`');
        $templates = Db::getInstance()->executeS('SELECT id_template FROM `'._DB_PREFIX_.'sensbitinpost_template`');

        if (!empty($templates)) {
            $data_to_insert = array();
            foreach ($templates as $template) {
                foreach ($customer_groups as $group) {
                    $data_to_insert[] = array(
                        'id_template' => (int)$template['id_template'],
                        'id_customer_group' => (int)$group['id_group']
                    );
                }
            }
            if (!empty($data_to_insert)) {
                Db::getInstance()->insert('sensbitinpost_template_customer_group', $data_to_insert);
            }
        }
    }
    return true;
}
