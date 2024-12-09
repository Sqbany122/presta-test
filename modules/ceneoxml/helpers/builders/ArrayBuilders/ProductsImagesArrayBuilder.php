<?php

class ProductsImagesArrayBuilder
{
    public static function Build($products_images)
    {
        $images_by_prod_id = array();

        foreach($products_images as $product_image)
        {
            $prod_id = $product_image['id_product'];
            $prod_attr_id = $product_image['id_product_attribute'];

            if(!isset($images_by_prod_id[$prod_id][$prod_attr_id]))
            {
                $images_by_prod_id[$prod_id][$prod_attr_id] = array();	
            }

            $images_by_prod_id[$prod_id][$prod_attr_id][] = array(
                    'id_image' 	=>  $product_image['id_image']
            );
        }

        return $images_by_prod_id;
    }
}