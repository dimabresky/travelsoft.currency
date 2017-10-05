<?php

namespace travelsoft\currency\factory;

/**
 * Фабрика объекта CuContainer
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class CuContainer {
    
    public static function getInstace (\travelsoft\currency\Currency $currency = null) {
       
        if (!$currency) {
            // генерируем объект валюты по-умолчанию
        }
        
    }
}
