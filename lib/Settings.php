<?php
namespace travelsoft\currency;

use Bitrix\Main\Config\Option as options;
use travelsoft\currency\Currency;
use travelsoft\currency\Course;
use travelsoft\currency\stores\Currencies;
use travelsoft\currency\stores\Courses;

/**
 * Класс настроек модуля
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class Settings {
    
    /**
     * Возвращает id таблицы валют
     * @return int
     */
    public static function currencyStoreId () : int {
        return (int)options::get("travelsoft.currency", "CURRENCY_HL_ID");
    }
    
    /**
     * Возвращает id таблицы курсов валют
     * @return int
     */
    public static function coursesStoreId () : int {
        return (int)options::get("travelsoft.currency", "COURSES_HL_ID");
    }
    
    /**
     * Возвращает id базовой валюты
     * @return int
     */
    public static function baseCurrencyId () : int {
        return (int)options::get("travelsoft.currency", "BASE_CURRENCY_ID"); 
    }
    
    /**
     * Возвращает id текущего курса
     * @return int
     */
    public static function currentCourseId () : int {
        return (int)options::get("travelsoft.currency", "CURRENT_COURSE_ID"); 
    }
    
    /**
     * Возвращает количество десячичных знаков
     * @return string
     */
    public static function formatDecimal () : string {
        return (string)options::get("travelsoft.currency", "FORMAT_DECIMAL");
    }
    
    /**
     * Возвращает разделитель дробной и целой части
     * @return string
     */
    public static function formatDecPoint () : string {
        return (string)options::get("travelsoft.currency", "FORMAT_DEC_POINT");
    }
    
    /**
     * Возвращает признак использования разделитель разрядов числа
     * @return boolean
     */
    public static function formatSSep () : bool {
        return (string)options::get("travelsoft.currency", "FORMAT_THOUSANDS_SEP") === "Y";
    }
    
    /**
     * Возвращает объект валюты по-умолчанию
     * @return \stdClass
     */
    public static function defaultCurrency () : Currency {
        
        $arCurrencies = Currencies::get();
        $arCourse = current(Courses::get(array("filter" => array("ID" => self::currentCourseId()))));
  
        $currency = new Currency((string)$arCurrencies[$arCourse["UF_BASE_ID"]]["UF_ISO"], $arCourse["UF_BASE_ID"]);
        foreach ($arCurrencies as $arCurrency) {
            $currency->addCourse($arCurrency["UF_ISO"], new Course($arCourse["UF_" . $arCurrency["UF_ISO"]], $arCourse["UF_DATE"]));
        }

        
        return $currency;
    }
}
