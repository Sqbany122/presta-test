<?php

class ProductsAttributeCombinationsArrayBuilder
{
    public static function Build($attr_combinations)
    {
        $attr_combinations_by_prod_id = array();

        foreach ($attr_combinations as $combination) 
        {
            $prod_id = $combination['id_product'];
            $prod_attr_id = $combination['id_product_attribute'];

            if(!isset($attr_combinations_by_prod_id[$prod_id][$prod_attr_id]))
            {
                $attr_combinations_by_prod_id[$prod_id][$prod_attr_id] = array();
            }

            $attr_combinations_by_prod_id[$prod_id][$prod_attr_id][] = array
            (
                'id_attribute' 		=> $combination['id_attribute'],
                'id_product_attribute' 	=> $prod_attr_id, 
                'attr_value' 		=> $combination['attr_value'], 
                'price' 		=> $combination['price'], 
                'weight' 		=> $combination['weight'],
                'quantity' 		=> $combination['quantity'],
                'out_of_stock' 		=> $combination['out_of_stock'],
                'ean13'                 => $combination['ean13'],
                'reference'             => $combination['reference']
            );
        }

        return $attr_combinations_by_prod_id;
    }
}