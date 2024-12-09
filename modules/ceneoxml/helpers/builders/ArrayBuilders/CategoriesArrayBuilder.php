<?php

class CategoriesArrayBuilder
{
    public static function Build($categories)
    {
        $categories_by_id = array();

        foreach ($categories as $category) 
        {
            $categories_by_id[$category['id_category']] = $category;
        }
        
        return $categories_by_id;
    }
}