<?php
namespace travelsoft\currency\factory;

/**
 * Класс фабрика класса \travelsoft\currency\Converter
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class Converter {

    public static function getInstance (\travelsoft\currency\Currency $currency = null, int $decimal = null, string $decPoint = null, string $ssep = null) {
        
        static $instances;
        
        if (!$currency) {
            // генерируем объект Currency по-умолчанию
        }
        
        if (!$decimal) {
            $decimal = \travelsoft\currency\Settings::formatDecimal();
        }
        
        if (!$decPoint) {
            $decPoint = \travelsoft\currency\Settings::formatDecPoint();
        }
        
        if (!$ssep) {
            $ssep = \travelsoft\currency\Settings::formatSSep();
        }
        
        $hash = md5(serialize($currency) . serialize($ssep) . serialize($decPoint) . serialize($decimal));
        
        if (!$instances[$hash]) {
            // генерируем класс конвертера
        }
        
        return $instances[$hash];
    }
}
