<?php

class CategoryPathInfo {
    private $category_path;
    private $category_path_length;  
    
    function __construct($category_path, $category_length) {
        $this->category_path = $category_path;
        $this->category_path_length = $category_length;
    }
    
    function getCategory_path() {
        return $this->category_path;
    }

    function getCategoryPath_length() {
        return $this->category_path_length;
    }
}
