<?php

namespace travelsoft\currency\stores;

use travelsoft\currency\interfaces\Store;

/**
 * Класс для работы с таблицей валюты
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class Currencies extends Store {

    protected static $storeName = "currency";

    /**
     * Возвращает ISO по id валюты
     * @param int $id
     * @return string
     */
    public static function getISObyId(int $id): string {

        $arCurrency = current(parent::get(array("filter" => array("ID" => $id))));
        return (string) $arCurrency["UF_ISO"];
    }

    /**
     * Возвращает список доступных ISO кодов в системе
     * @return array
     */
    public static function getAcceptableISO(): array {

        $arr = parent::get(null, function ($el) {
                    $tmp = $el;
                    $el = $tmp["UF_ISO"];
                });
        return (array) array_values($arr);
    }

}
