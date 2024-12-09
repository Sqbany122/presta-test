<?php

require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'calculators' . DIRECTORY_SEPARATOR . 'PriceCalculator.php'); 

class PriceCalculatorTest extends PHPunit_Framework_Testcase
{
    public function test_CalculateValueWithTax_ShouldCalculateProperValueWithTax()
    {
        $this->assertEquals(PriceCalculator::CalculateValueWithTax(56.71, 23), 69.75);
        $this->assertEquals(PriceCalculator::CalculateValueWithTax(781.23412, 0), 781.23);
    }
    
    public function test_CalculateValueWithTax_ShouldCalculateProperPriceIfSpecialPriceDataIsEmpty()
    {
        $this->assertEquals(PriceCalculator::CalculateFinalProductPrice(73.314, array(), 23), 90.18);
        $this->assertEquals(PriceCalculator::CalculateFinalProductPrice(33.143, array(), 0), 33.14);
    }
    
    public function test_CalculateValueWithTax_ShouldCalculateProperPriceForFixedSpecialPrice()
    {
        $special_price_data = array(
            'reduction' => 0,  
            'price' => 100
        );
        
        $this->assertEquals(PriceCalculator::CalculateFinalProductPrice(234.55, $special_price_data, 23), 123);
    }
    
    public function test_CalculateValueWithTax_ShouldCalculateProperPriceAfterPercenteReduction()
    {
        $special_price_data = array(
            'reduction_type' => 0,
            'reduction' => 0.1,  
            'price' => null
        );
        
        $this->assertEquals(PriceCalculator::CalculateFinalProductPrice(178.12, $special_price_data, 23), 197.18);
    }
    
    public function test_CalculateValueWithTax_ShouldCalculateProperPriceAfterPercenteReductionAndFixedPrice()
    {
        $special_price_data = array(
            'reduction_type' => 0,
            'reduction' => 0.2,  
            'price' => 200
        );
        
        $this->assertEquals(PriceCalculator::CalculateFinalProductPrice(77, $special_price_data, 23), 196.8);
    }
    
    public function test_CalculateValueWithTax_ShouldCalculateProperPriceAfterValueReductionWithTax()
    {
        $special_price_data = array(
            'reduction_type' => 1,
            'reduction' => 50,  
            'reduction_tax' => 1,
            'price' => null
        );
        
        $this->assertEquals(PriceCalculator::CalculateFinalProductPrice(145, $special_price_data, 23), 128.35);
    }
    
    public function test_CalculateValueWithTax_ShouldCalculateProperPriceAfterValueReductionWithoutTax()
    {
        $special_price_data = array(
            'reduction_type' => 1,
            'reduction' => 50,  
            'reduction_tax' => 0,
            'price' => null
        );
        
        $this->assertEquals(PriceCalculator::CalculateFinalProductPrice(145, $special_price_data, 23), 116.85);
    }
    
    public function test_CalculateValueWithTax_ShouldCalculateProperPriceAfterValueReductionWithTaxAndFixPrice()
    {
        $special_price_data = array(
            'reduction_type' => 1,
            'reduction' => 50,  
            'reduction_tax' => 1,
            'price' => 100
        );
        
        $this->assertEquals(PriceCalculator::CalculateFinalProductPrice(1099.99, $special_price_data, 23), 73);
    }
    
    public function test_CalculateValueWithTax_ShouldCalculateProperPriceAfterValueReductionWithoutTaxAndFixPrice()
    {
        $special_price_data = array(
            'reduction_type' => 1,
            'reduction' => 50,  
            'reduction_tax' => 0,
            'price' => 100
        );
        
        $this->assertEquals(PriceCalculator::CalculateFinalProductPrice(1099.99, $special_price_data, 23), 61.5);
    }
}
