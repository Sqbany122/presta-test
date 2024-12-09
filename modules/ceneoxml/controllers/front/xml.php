<?php
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'builders' . DIRECTORY_SEPARATOR . 'CeneoXmlBuilder.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'builders' . DIRECTORY_SEPARATOR . 'ArrayBuilders' . DIRECTORY_SEPARATOR . 'ProductsSpecialPricesBuilder.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'builders' . DIRECTORY_SEPARATOR . 'ArrayBuilders' . DIRECTORY_SEPARATOR . 'CategoriesArrayBuilder.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'builders' . DIRECTORY_SEPARATOR . 'ArrayBuilders' . DIRECTORY_SEPARATOR . 'ProductsImagesArrayBuilder.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'builders' . DIRECTORY_SEPARATOR . 'ArrayBuilders' . DIRECTORY_SEPARATOR . 'ProductsFeaturesArrayBuilder.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'builders' . DIRECTORY_SEPARATOR . 'ArrayBuilders' . DIRECTORY_SEPARATOR . 'ProductsAttributeCombinationsArrayBuilder.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR . 'UrlProviderFactory.php');
require_once(__CENEO_XML_MODULE_PATH__ . 'repositories' . DIRECTORY_SEPARATOR . 'PrestaShopRepository.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'repositories' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR . 'SqlQueryProviderFactory.php');
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'CategoryHelper.php'); 

class ceneoxmlXmlModuleFrontController extends ModuleFrontController
{
    private $xml_generator;
    private $repository;
    private $version;

    const PROD_NUMBER = 1000;

    public function __construct()
    {
        if(version_compare(_PS_VERSION_, '1.5', '<'))
        {
            die("Twoja wersja PrestaShop nie jest kompatybilna z modułem Ceneo.");
        }
        
        parent::__construct();
        
        $this->version = PrestaShopHelper::GetPSVersionBasePart(_PS_VERSION_);
        
        $this->repository = new PrestaShopRepository(
            SqlQueryProviderFactory::Create($this->version, $this->context)
        );
    }

    public function initContent()
    {  	
        ini_set ( 'max_execution_time', 0 ); 

        if(ini_get('safe_mode'))
        {
            die("Moduł Ceneo działa poprawnie gdy opcja safe_mode w php.ini jest ustawiona na 'Off'.");
        }

        //disable page elements
        $this->display_header = false;
        $this->display_footer = false;

        $offset = 0;

        $categories = $this->repository->GetAllCategories();

        $xml_builder = new CeneoXmlBuilder(
                            $this->context, 
                            new CategoryHelper(CategoriesArrayBuilder::Build($categories)),
                            UrlProviderFactory::Create($this->version, $this->context)
                        );
        
        $special_price_builder = new ProductsSpecialPricesBuilder(Configuration::get('PS_SPECIFIC_PRICE_PRIORITIES'));

        while($products = $this->repository->GetProducts(self::PROD_NUMBER, $offset))
        {
            //get data from db
            $product_ids = $this->GetProductIds($products);
            $product_attr_combinations = ProductsAttributeCombinationsArrayBuilder::Build($this->repository->GetProductsAttributeCombinations($product_ids));  
            $products_special_prices = $special_price_builder->Build($this->repository->GetProductsSpecialPrices($product_ids));     
            $products_images = ProductsImagesArrayBuilder::Build($this->repository->GetProductsImages($product_ids));
            $products_features = ProductsFeaturesArrayBuilder::Build($this->repository->GetProductsFeatures($product_ids));

            $xml_builder->Build($products, $product_attr_combinations, $products_special_prices, $products_images, $products_features);

            $offset += self::PROD_NUMBER;
        }

        $xml_builder->DisplayXml();
    }

    private function GetProductIds($products)
    {
        $product_ids = array();

        foreach ($products as $product) {
            $product_ids[] = $product['id_product'];
        }

        return $product_ids;
    }
}