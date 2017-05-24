<?php

$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__)."/../../../..");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("NO_AGENT_STATISTIC",true);
define('NO_AGENT_CHECK', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
Bitrix\Main\Loader::includeModule("travelsoft.currency");

use PHPUnit\Framework\TestCase;

/**
 * Тест конвертера валюты
 *
 * @author dimabresky
 */
class ConverterTests extends TestCase {
    
    private $converter;
    
    protected function setUp() {
        
        $this->converter = \travelsoft\currency\Converter::getInstance();
    }
    
    public function testConvert() {
        $currency = new \travelsoft\currency\Currency();
        
        $currency->setISO("BYN");
        
        $currency->setId(1);
        
        $arCourse = array(
            "BYN" => 1,
            "USD" => 1.8,
            "EUR" => 2
        );
        
        foreach ($arCourse as $ISO => $value) {
            $currency->addCourse($ISO, new \travelsoft\currency\Course((float)$value, date("d.m.Y H:i:s")));
        }
        
        $this->converter->setCurrency($currency);
        
        $arResult = $this->converter->convert(1, "BYN", "BYN")->getLikeArray();
        
        $this->assertEquals(1, $arResult["price"]);
        
        $arResult = $this->converter->convert(1.8, "BYN", "USD")->getLikeArray();
        $this->assertEquals(1, $arResult["price"]);
    }
    
}
