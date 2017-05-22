<?php
namespace travelsoft\currency;

use Bitrix\Main\Config\Option as options;

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
     * Возвращает id базового курса
     * @return int
     */
    public static function baseCourseId () : int {
        return (int)options::get("travelsoft.currency", "BASE_COURSE_ID"); 
    }
    
    /**
     * Возвращает количество десячичных знаков
     * @return string
     */
    public function formatDecimal () : string {
        return (string)options::get("travelsoft.currency", "FORMAT_DECIMAL");
    }
    
    /**
     * Возвращает разделитель дробной и целой части
     * @return string
     */
    public function formatDecPoint () : string {
        return (string)options::get("travelsoft.currency", "FORMAT_DEC_POINT");
    }
    
    /**
     * Возвращает признак использования разделитель разрядов числа
     * @return boolean
     */
    public function formatSSep () : bool {
        return (string)options::get("travelsoft.currency", "FORMAT_THOUSANDS_SEP") === "Y";
    }
    
}
