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
    
    protected $storeName = "currency";
     
    /**
     * Возвращает ISO по id валюты
     * @param int $id
     * @return string
     */
    public static function getISObyId (int $id) : string {
       $arCurrency = current(parent::get(array("filter" => array("ID" => $id)))); 
       return  (string)$arCurrency["UF_ISO"];
    }
    
}
