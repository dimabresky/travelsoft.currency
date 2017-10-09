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
    public static function currencyStoreId(): int {
        return (int) options::get("travelsoft.currency", "CURRENCY_HL_ID");
    }

    /**
     * Возвращает id таблицы курсов валют
     * @return int
     */
    public static function coursesStoreId(): int {
        return (int) options::get("travelsoft.currency", "COURSES_HL_ID");
    }

    /**
     * Возвращает id текущего курса
     * @return int
     */
    public static function currentCourseId(): int {
        return (int) options::get("travelsoft.currency", "CURRENT_COURSE_ID");
    }

    /**
     * Возвращает количество десячичных знаков
     * @return string
     */
    public static function formatDecimal(): int {
        return (int) options::get("travelsoft.currency", "FORMAT_DECIMAL");
    }

    /**
     * Возвращает разделитель дробной и целой части
     * @return string
     */
    public static function formatDecPoint(): string {
        return (string) options::get("travelsoft.currency", "FORMAT_DEC_POINT");
    }

    /**
     * Возвращает признак использования разделитель разрядов числа
     * @return boolean
     */
    public static function formatSSep(): bool {
        return (string) options::get("travelsoft.currency", "FORMAT_THOUSANDS_SEP") === "Y";
    }

    /**
     * Возвращает комиссию по валютам
     * @return array
     */
    public static function commissions(): array {
        return (array) \travelsoft\sta(options::get("travelsoft.currency", "COMMISSIONS"));
    }

}
