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
 * Тестирование класса \travelsoft\currency\Course
 *
 * @author dimabresky
 */
class CourseTests extends TestCase{
    
    private $course;
    
    protected function setUp () {
        
        $this->course = new Course();
    }
    
    protected function tearDown () {
        
        $this->course = null;
    }
}
