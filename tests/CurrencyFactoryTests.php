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
class CurrencyFactoryTests extends PHPUnit\Framework\TestCase {

    public function testCreateDefaultCurrencyObject() {

        $currency = \travelsoft\currency\factory\Currency::getInstance();
        
        $this->assertInstanceOf("\\travelsoft\\currency\\Currency", $currency);
    }

}
