<?php

class ProductsSpecialPricesBuilder
{
    const ExplodeSeperator = ';';
    private $global_priorities;
    
    function __construct($global_priorities) {
        $this->global_priorities = explode(self::ExplodeSeperator, $global_priorities);
    }

    public function Build($special_prices)
    {
        $special_prices_by_prod_id = array();

        foreach($special_prices as $special_price)
        {
            $prod_id = $special_price['id_product'];
            $prod_attr_id = $special_price['id_product_attribute'];

            if(!isset($special_prices_by_prod_id[$prod_id][$prod_attr_id]))
            {
                $special_prices_by_prod_id[$prod_id][$prod_attr_id] = self::CreateSpecialPriceEntry($special_price);	
            }
            else
            {
                if($this->CheckIfNewPriceIsMoreImportantThanCurrent($special_prices_by_prod_id[$prod_id][$prod_attr_id], $special_price))
                {
                    $special_prices_by_prod_id[$prod_id][$prod_attr_id] = self::CreateSpecialPriceEntry($special_price);
                }
            }
        }

        return $special_prices_by_prod_id;
    }
    
    private function CheckIfNewPriceIsMoreImportantThanCurrent($special_price_current, $special_price_new)
    {
        $priorities = is_null($special_price_new['priority']) ? 
                $this->global_priorities : 
                explode(self::ExplodeSeperator,$special_price_new['priority']);

        foreach($priorities as $key => $value)
        {
            if($special_price_new[$value] > $special_price_current[$value])
            {
                return true;
            }
            else if($special_price_new[$value] < $special_price_current[$value])
            {
                return false;
            }
        }
        
        if(is_null($special_price_current['date_from']) && is_null($special_price_current['date_to']) && 
            (!is_null($special_price_new['date_from']) || !is_null($special_price_new['date_to'])))
        {
            return true;
        }
        
        return false;
    }

    private function CreateSpecialPriceEntry($special_price)
    {	
        return array(
            'price' 		=> $special_price['price'],
            'reduction' 	=> $special_price['reduction'], 
            'reduction_tax'	=> $special_price['reduction_tax'], 
            'reduction_type'	=> $special_price['reduction_type'], 
            'id_currency'	=> $special_price['id_currency'],
            'id_country'	=> $special_price['id_country'],
            'date_from'		=> $special_price['date_from'],
            'date_to' 		=> $special_price['date_to'],
            'id_group' 		=> $special_price['id_group'],
            'id_shop' 		=> $special_price['id_shop']
        );
    }
}