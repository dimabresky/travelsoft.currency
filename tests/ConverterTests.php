<?php

require "header.php";

/**
 * Тест конвертера валюты
 *
 * @author dimabresky
 */
class ConverterTests extends PHPUnit\Framework\TestCase {
    
    private $__converter;
    
    protected function setUp() {
        
        $this->__converter = \travelsoft\currency\Converter::getInstance();
    }
    
    public function testConverter() {
        
        $currency = new \travelsoft\currency\Currency("BYN");
        
        $currency->addCourse("USD", new travelsoft\currency\Course(1.8));
        $currency->addCourse("EUR", new travelsoft\currency\Course(2));
        $currency->addCourse("RUB", new travelsoft\currency\Course(0.003));
        
        $this->__converter->setCurrency($currency);
        
        $this->assertEquals("1.00 USD", $this->__converter->convert(1.8, "BYN", "USD")->getResult());
        $this->assertEquals("1.00 EUR", $this->__converter->convert(2, "BYN", "EUR")->getResult());
        $this->assertEquals("1,000 RUB", $this->__converter->convert(0.003, "BYN", "RUB")->setDecimal(3)->setDecPoint(',')->getResult());
        
        $array = $this->__converter->getResultLikeArray();
        $this->assertArrayHasKey("price", $array);
        $this->assertArrayHasKey("ISO", $array);
        $this->assertEquals(1.000, $array["price"]);
        $this->assertEquals("RUB", $array["ISO"]);   
    }
}
