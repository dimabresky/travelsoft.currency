<?php

namespace travelsoft\currency;

/**
 * Класс выгрузки валюты
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class Importer {

    /**
     * Импорт курсов валют из Национального Банка Республики Беларусь
     */
    public static function fromNationalBankRepublicOfBelarus() {

        $now = time();
        $URL = "http://www.nbrb.by/API/ExRates/Rates?onDate=" . date('Y-m-d', $now) . "&Periodicity=0";
        $start = mktime(0, 0, 0, date("n", $now), date("j", $now), date('Y', $now));
        $end = mktime(23, 59, 59, date("n", $now), date("j", $now), date('Y', $now));
        $arCourse = current(stores\Courses::get(array("order" => array("ID" => "DESC"), "limit" => 1, "filter" => array("><UF_UNIX_DATE" => array($start, $end)))));

        if (!$arCourse["ID"]) {
            # получение валюты на текущий день из нац.банка
            $arImportCurrencyCourses = json_decode(file_get_contents($URL), true);
            $arCurrencies = stores\Currencies::get();
            foreach ($arCurrencies as $arCurrency) {
                $acceptableISO[$arCurrency["UF_ISO"]] = $arCurrency["ID"];
            }
            # фильтрация "нужных" курсов валют
            $arCoursesNeeded = array_filter($arImportCurrencyCourses, function ($arItem) use ($acceptableISO) {
                return $acceptableISO[$arItem["Cur_Abbreviation"]] > 0;
            });
            if (!empty($arCoursesNeeded)) {
                $arSave = array(
                    "UF_BYN" => 1,
                    "UF_BASE_ID" => $acceptableISO["BYN"],
                    "UF_ACTIVE" => 1,
                    "UF_DATE" => date("d.m.Y H:i:s", $now),
                    "UF_UNIX_DATE" => $now
                );
                foreach ($arCoursesNeeded as $arrCurrency) {
                    $arSave["UF_" . $arrCurrency["Cur_Abbreviation"]] = $arrCurrency["Cur_OfficialRate"] / $arrCurrency["Cur_Scale"];
                }
                stores\Courses::add($arSave);
            }
        }
    }

}
