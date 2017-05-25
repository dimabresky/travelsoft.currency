<?php

namespace travelsoft\currency;

/**
 * Класс выгрузки валюты
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class CurrencyImporter {
    
    /**
     * Импорт курсов валют из Национального Банка Республики Беларусь
     */
    public static function importFromNationalBankRepublicOfBelarus() {
        
        $URL = "http://www.nbrb.by/API/ExRates/Rates?onDate=".date('Y-m-d')."&Periodicity=0";
        $now = time();
        $start = mktime(0, 0, 0, date("n", $now), date("m", $now), date('Y', $now));
        $end = mktime(23, 59, 59, date("n", $now), date("m", $now), date('Y', $now));
        $arCourse = current(stores\Courses::get(array("order" => array("ID" => "DESC"), "limit" => 1, "filter" => array("><UF_UNIX_DATE" => array($start, $end)))));
        if (!$arCourse["ID"]) {
            # получение валюты на текущий день из нац.банка
            $arImportCurrencyCourses = Bitrix\Main\Web\Json::decode(file_get_contents($URL));
            $acceptableISO = stores\Currencies::getAcceptableISO();
            # фильтрация "нужных" курсов валют
            $arCoursesNeeded = array_filter($arImportCurrencyCourses, function ($arItem) use ($acceptableISO) {
                return in_array($arItem["Cur_Abbreviation"], $acceptableISO);
            });
            if (!empty($arCoursesNeeded)) {
                $arSave = array(
                    "UF_" =>$IBLOCK_ID, 
                    "NAME" => $CURDATE,
                    "CODE" => date('d-m-Y'),
                    "ACTIVE" => "Y",
                    "PROPERTY_VALUES" => array(
                        "DATE" => $CURDATE,
                        $arBase["iso"] => 1
                    )
                );
            }
        }
    }
}
