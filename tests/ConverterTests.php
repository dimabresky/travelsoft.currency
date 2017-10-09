<?php

$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../../../..");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("NO_AGENT_STATISTIC", true);
define('NO_AGENT_CHECK', true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
Bitrix\Main\Loader::includeModule("travelsoft.currency");

/**
 * Тест конвертера валюты
 *
 * @author dimabresky
 */
class ConverterTests extends PHPUnit\Framework\TestCase {

    private $__decPoint;
    private $__decimal;
    private $__ssep;
    private $__converter;

    protected function setUp() {
        $this->__decPoint = travelsoft\currency\Settings::formatDecPoint();
        $this->__decimal = travelsoft\currency\Settings::formatDecimal();
        $this->__ssep = travelsoft\currency\Settings::formatSSep();
        $this->__converter = travelsoft\currency\factory\Converter::getInstance(null, $this->__decimal, $this->__decPoint, $this->__ssep);
    }

    public function testConverter() {
        
        $arCourses = travelsoft\currency\stores\Courses::getById(travelsoft\currency\Settings::currentCourseId());
        
        $this->assertEquals("1".$this->__decPoint."00 USD", $this->__converter->convert($arCourses['UF_USD'], "BYN", "USD")->getResult());
        $this->assertEquals("1".$this->__decPoint."00 EUR", $this->__converter->convert($arCourses['UF_EUR'], "BYN", "EUR")->getResult());
        $this->assertEquals("1".$this->__decPoint."00 RUB", $this->__converter->convert($arCourses['UF_RUB'], "BYN", "RUB")->getResult());
        $this->assertEquals("1".$this->__decPoint."00 BYN", $this->__converter->convert(1, "BYN", "BYN")->getResult());
        
        $array = $this->__converter->getResultLikeArray();
        $this->assertArrayHasKey("price", $array);
        $this->assertArrayHasKey("ISO", $array);
        
        $this->assertInstanceOf("\\travelsoft\\currency\\CuContainer", $this->__converter->getCuContainer());
        $this->assertEquals(travelsoft\currency\Settings::formatDecPoint(), $this->__converter->getDecPoint());
        $this->assertEquals(travelsoft\currency\Settings::formatDecimal(), $this->__converter->getDecimal());
        
    }

}
