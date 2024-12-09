<?php
require_once(__CENEO_XML_MODULE_PATH__ . 'repositories' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR . 'SqlQueryProviderBase.php'); 

class SqlQueryProvider_1_5 extends SqlQueryProviderBase
{
    public function GetProductsSpecialPricesSql($prod_ids)
    {
        $current_date_time = date("Y-m-d H:i:s");

        return 'SELECT 
                    psp.`id_product`, psp.`id_product_attribute`, IF(psp.`price` = -1, null, psp.`price`) as price, psp.`reduction`, psp.`id_group`,
                    1 as \'reduction_tax\', IF(psp.`reduction_type` = \'percentage\', 0, 1) as reduction_type, psp.`id_currency`, psp.`id_country`,  
                    IF(psp.`from` = \'0000-00-00 00:00:00\', null, psp.`from`) as date_from, 
                    IF(psp.`to` = \'0000-00-00 00:00:00\', null, psp.`to`) as date_to,
                    0 as id_shop, pspp.`priority`
                FROM 
                    `'._DB_PREFIX_.'specific_price` psp LEFT JOIN
                    `'._DB_PREFIX_.'specific_price_priority` pspp ON psp.`id_product` = pspp.`id_product`
                WHERE 
                    psp.`id_product` IN ('.$prod_ids.') AND
                    (psp.`id_group` = 0 OR psp.`id_group` = 1) AND
                    psp.`id_customer` = 0 AND
                    psp.`from_quantity` = 1 AND (psp.`id_country` = 0 OR psp.`id_country` = '.$this->context->country->id.') AND 
                    (psp.`id_currency` = 0 OR psp.`id_currency` = '.$this->context->currency->id.') AND
                    ((psp.`from` = \'0000-00-00 00:00:00\' AND psp.`to` = \'0000-00-00 00:00:00\') OR 
                    (psp.`to` != \'0000-00-00 00:00:00\' AND \''.$current_date_time.'\' >= psp.`from` AND \''.$current_date_time.'\' <= psp.`to`) OR
                     (psp.`from` != \'0000-00-00 00:00:00\' AND psp.`to` = \'0000-00-00 00:00:00\' AND \''.$current_date_time.'\' >= psp.`from`))';	
    }
}
