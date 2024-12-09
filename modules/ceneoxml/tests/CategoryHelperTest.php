<?php

require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'CategoryHelper.php'); 

class CategoryHelperTest extends PHPunit_Framework_Testcase
{
    private $category_helper;

    public function setUp()
    { 
        $this->category_helper = new CategoryHelper($this->GetCategoriesData());
    }

    public function test_GetCategoryPathInfo_ShouldReturnProperPath()
    {
        $this->assertEquals($this->category_helper->GetCategoryPathInfo(7)->getCategory_path(), "Clothes/Men/Tops");
        $this->assertEquals($this->category_helper->GetCategoryPathInfo(2)->getCategory_path(), "Clothes/Woman");
        $this->assertEquals($this->category_helper->GetCategoryPathInfo(1)->getCategory_path(), "Clothes");
        $this->assertEquals($this->category_helper->GetCategoryPathInfo(123)->getCategory_path(), "");
    }
    
        public function test_GetCategoryPathInfo_ShouldReturnProperPathLength()
    {
        $this->assertEquals($this->category_helper->GetCategoryPathInfo(7)->getCategoryPath_length(), 16);
        $this->assertEquals($this->category_helper->GetCategoryPathInfo(2)->getCategoryPath_length(), 13);
        $this->assertEquals($this->category_helper->GetCategoryPathInfo(1)->getCategoryPath_length(), 7);
        $this->assertEquals($this->category_helper->GetCategoryPathInfo(123)->getCategoryPath_length(), 0);
    }

    private function GetCategoriesData()
    {
        return array(
            1 => array(
                "id_category"	=>  "1",
                "id_parent"	=>  "0",
                "name"		=>  "Clothes"
            ),
            2 => array(
                "id_category"	=>  "2",
                "id_parent"	=>  "1",
                "name"		=>  "Woman"
            ),
            3 => array(
                "id_category"	=>  "3",
                "id_parent"	=>  "1",
                "name"		=>  "Men"
            ),
            4 => array(
                "id_category"	=>  "4",
                "id_parent"	=>  "2",
                "name"		=>  "Tops"
            ),
            5 => array(
                "id_category"	=>  "5",
                "id_parent"	=>  "4",
                "name"		=>  "T-shirts"
            ),
            6 => array(
                "id_category"	=>  "6",
                "id_parent"	=>  "4",
                "name"		=>  "Blouses"
            ),
            7 => array(
                "id_category"	=>  "7",
                "id_parent"	=>  "3",
                "name"		=>  "Tops"
            ),
            8 => array(
                "id_category"	=>  "8",
                "id_parent"	=>  "7",
                "name"		=>  "Trousers"
            )
        );
    }

}
