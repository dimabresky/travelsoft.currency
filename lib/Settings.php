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
    public static function formatDecimal(): string {
        return (string) options::get("travelsoft.currency", "FORMAT_DECIMAL");
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

    /**
     * Возвращает объект валюты по-умолчанию
     * @return Currency
     */
    public static function defaultCurrency(): Currency {

        $arCurrencies = Currencies::get();
        $arCourse = current(Courses::get(array("filter" => array("ID" => self::currentCourseId()))));

        $arCommissions = self::commissions();

        $currency = new Currency((string) $arCurrencies[$arCourse["UF_BASE_ID"]]["UF_ISO"], intVal($arCourse["UF_BASE_ID"]));

        if (!empty($arCommissions)) {

            foreach ($arCurrencies as $arCurrency) {

                $value = $arCourse["UF_" . $arCurrency["UF_ISO"]];
                if (!$arCourse["UF_BASE_ID"] !== $arCurrency["ID"] && $arCommissions[$arCurrency["UF_ISO"]] > 0) {
                    # расчёт курса с комиссией
                    $value = $value + $value * ($arCommissions[$arCurrency["UF_ISO"]] / 100);
                }
                $currency->addCourse($arCurrency["UF_ISO"], new Course((float) $value, (string) $arCourse["UF_DATE"]));
            }
        } else {
        
            foreach ($arCurrencies as $arCurrency) {
                
                $currency->addCourse($arCurrency["UF_ISO"], new Course((float) $arCourse["UF_" . $arCurrency["UF_ISO"]], (string) $arCourse["UF_DATE"]));
            }
        }

        return $currency;
    }

}
