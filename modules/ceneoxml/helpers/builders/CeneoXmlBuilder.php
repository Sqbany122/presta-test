<?php
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR . 'ProductCodeTypeProvider.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR . 'AvailabilityStatusProvider.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'calculators' . DIRECTORY_SEPARATOR . 'PriceCalculator.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'classes' . DIRECTORY_SEPARATOR . 'CeneoXmlWriter.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'classes' . DIRECTORY_SEPARATOR . 'FeatureEnum.php');
require_once(__CENEO_XML_MODULE_PATH__ . 'classes' . DIRECTORY_SEPARATOR . 'CeneoConfig.php');

class CeneoXmlBuilder
{
    private $dom;
    private $offers;
    private $product_types_by_cat;
    private $context;
    private $url_provider;

    private $xml_writer;
    private $category_helper;
    
    public function __construct($context, $category_helper, $url_provider)
    {
        $this->context = $context;
        $this->category_helper = $category_helper;
        $this->url_provider = $url_provider;
        $this->product_types_by_cat = array();

        $this->xml_writer = new XMLWriter();
        $this->xml_writer->openUri('php://output');
        $this->xml_writer->setIndent(true);
        $this->xml_writer->startDocument('1.0', CeneoConfig::Encoding);
        $this->xml_writer->startElement('offers');		
        $this->xml_writer->startAttribute('xmlns:xsi');
            $this->xml_writer->text('http://www.w3.org/2001/XMLSchema-instance');
        $this->xml_writer->endAttribute();
        $this->xml_writer->startAttribute('version');
            $this->xml_writer->text('1');
        $this->xml_writer->endAttribute();
    }

    public function DisplayXml()
    {
        header("Content-type: text/xml; charset=utf-8");
        
        $this->xml_writer->endElement();
        $this->xml_writer->endDocument();
        $this->xml_writer->flush();
    }

    public function Build($products, $attributes, $special_prices_info, $images, $features)
    { 
        $mem_xml_writer = new CeneoXmlWriter($this->context);
        $mem_xml_writer->openMemory();
        $mem_xml_writer->setIndent(true);

        if(is_array($products))
        {   
            foreach ($products as $product) 
            {
                $prod_id = $product['id_product'];
                $product_images = isset($images[$prod_id])? $images[$prod_id] : array();
                $product['weight'] = 0;
                $product['features'] = array();

                //product features
                if(isset($features[$prod_id]))
                {
                    if(isset($features[$prod_id][FeatureEnum::Weight]))
                    {
                        $product['weight'] = $features[$prod_id][FeatureEnum::Weight];
                        unset($features[$prod_id][FeatureEnum::Weight]);
                    }

                    $product['features'] = $features[$prod_id];
                }

                //product variants
                if(isset($attributes[$prod_id]))
                {
                    $prod_special_prices = isset($special_prices_info[$prod_id])? $special_prices_info[$prod_id] : null;
                    foreach ($attributes[$prod_id] as $product_attributes) 
                    {
                        $product_variant = $this->CreateProductVariant($product, $product_attributes, $prod_special_prices, $product_images);
                        $this->CreateXmlOffer($product_variant, $mem_xml_writer);
                    }
                }
                //product without variants
                else
                {	
                    if($product['minimal_quantity'] == 1)
                    {
                        $product['special_price_data'] = isset($special_prices_info[$prod_id][0])? $special_prices_info[$prod_id][0] : array();		      
                        $product['images'] = isset($product_images[0])? $product_images[0] : array();

                        $this->CreateXmlOffer($product, $mem_xml_writer);
                    }
                }      
            } 
        }    

        $this->xml_writer->writeRaw($mem_xml_writer->outputMemory(true));
        $mem_xml_writer->flush();
        unset($mem_xml_writer);
    }

    private function CreateXmlOffer($product, $mem_xml_writer)
    {
        $availability_status = AvailabilityStatusProvider::GetAvailabilityStatus($product['quantity'], $product['out_of_stock']);
        
        if($availability_status > 0)
        {
            $prod_attr_id = isset($product['id_product_attribute'])? $product['id_product_attribute'] : null; 
            $url = $this->url_provider->getProductLink($product['id_product'], $product['id_category_default'], $prod_attr_id);
            $price = PriceCalculator::CalculateFinalProductPrice($product['price'], $product['special_price_data'], $product['tax_rate']);

            if(strlen($url) <= CeneoConfig::XmlUrlLength && $price > 0)
            {                
                $mem_xml_writer->startElement('o');

                    $mem_xml_writer->setAttribute('id', $product['id_product']);

                    $mem_xml_writer->setAttribute('url', $url);
                    $mem_xml_writer->setAttribute('price', $price);

                    $mem_xml_writer->setAttribute('avail', $availability_status);
                    $mem_xml_writer->setAttribute('set', 0);
                    $mem_xml_writer->setAttribute('basket', 1);

                    if($product['weight'] > 0)
                    {
                        $mem_xml_writer->setAttribute('weight', number_format($product['weight'],2));
                    }
                    
                    if($product['quantity'] > 0)
                    {
                        $mem_xml_writer->setAttribute('stock', $product['quantity']);
                    }
                    
                    $mem_xml_writer->AddCategoryToXml($this->category_helper->GetCategoryPathInfo($product['id_category_default']));

                    $mem_xml_writer->AddNameToXml($product['name']);

                    $mem_xml_writer->AddDescriptionToXml($product['description']);

                    $mem_xml_writer->AddImagesToXml($product['images'], $product['link_rewrite']);

                    $this->AddAttributesToXml($mem_xml_writer, $product);

                $mem_xml_writer->endElement();
            }
        }
    }
    
    private function GetAvailabilityStatus($quantity, $out_of_stock)
    {
        if($quantity > 0)
        {
            return AvailabilityEnum::Available;
        }
        else if($quantity <= 0 && $out_of_stock == 1)
        {
            return AvailabilityEnum::LackOfInformation;
        }
        
        return AvailabilityEnum::Unavailable;
    }
    
    private function AddAttributesToXml($mem_xml_writer, $product)
    {
        $mem_xml_writer->startElement('attrs');
            //mandatory attributes
            $mem_xml_writer->AddCeneoAttribute('Producent', $product['manufacturer_name']);
            $mem_xml_writer->AddCeneoAttribute('Kod_producenta', $product['reference']);

            if(!isset($this->product_types_by_cat[$product['id_category_default']]))
            {
                $this->product_types_by_cat[$product['id_category_default']] = ProductCodeTypeProvider::GetCodeType($product['ean13']);
            }

            $mem_xml_writer->AddCeneoAttribute($this->product_types_by_cat[$product['id_category_default']], $product['ean13']);

            //extra data assigned to attributes for future analysis 
            foreach ($product['features'] as $name => $value) {
                $mem_xml_writer->AddCeneoAttribute($name, $value);
            }

        $mem_xml_writer->endElement();
    }



    private function CreateProductVariant($product, $product_attributes, $prod_special_prices, $product_images)
    {
        $product_variant = $product;
        $data_was_added_once = false;

        foreach ($product_attributes as $product_attribute) 
        {
          $product_variant['id_product'] .= sprintf("_%s", $product_attribute['id_attribute']);
          $product_variant['name'] .= sprintf(" %s", $product_attribute['attr_value']);

          if(!$data_was_added_once)
          {
            $prod_attr_id = $product_attribute['id_product_attribute'];

            $product_variant['price'] += $product_attribute['price'];
            $product_variant['weight'] += $product_attribute['weight'];
            $product_variant['id_product_attribute'] = $prod_attr_id;
            $product_variant['quantity'] = $product_attribute['quantity'];
            $product_variant['out_of_stock'] = $product_attribute['out_of_stock'];
            $product_variant['images'] = isset($product_images[$prod_attr_id]) ? 
                            $product_images[$prod_attr_id] : 
                            (isset($product_images[0]) ? $product_images[0] : array());

            $product_variant['special_price_data'] = isset($prod_special_prices[$product_attribute['id_product_attribute']]) ?
                            $prod_special_prices[$product_attribute['id_product_attribute']] :
                            (isset($prod_special_prices[0]) ? $prod_special_prices[0] : array());

            if($product_attribute['ean13'] != null)
            {
                $product_variant['ean13'] = $product_attribute['ean13'];
            }
            
            if($product_attribute['reference'] != null)
            {
                $product_variant['reference'] = $product_attribute['reference'];
            }
            
            $data_was_added_once = true;
          }		  
        }

        return $product_variant;
    }
}