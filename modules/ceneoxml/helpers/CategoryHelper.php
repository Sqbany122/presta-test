<?php
require_once(__CENEO_XML_MODULE_PATH__ . 'classes' . DIRECTORY_SEPARATOR . 'CategoryPathInfo.php');

class CategoryHelper
{
    private $categories;
    private $categories_path;

    public function __construct($categories)
    {
        $this->categories = $categories;
    }

    public function GetCategoryPathInfo($category_id)
    {
        if(!isset($this->categories_path[$category_id]))
        {
            $category_path = $this->CreateCategoryPath($category_id);
            $this->categories_path[$category_id] = new CategoryPathInfo($category_path, mb_strlen($category_path));
        }

        return $this->categories_path[$category_id];
    }

    private function CreateCategoryPath($category_id)
    {
        $category_path = '';

        while(isset($this->categories[$category_id]))
        {
            $category_path = $this->categories[$category_id]['name'] . (empty($category_path)? '' : '/') . $category_path;
            $category_id = $this->categories[$category_id]['id_parent'];
        }

        return $category_path;
    }
}