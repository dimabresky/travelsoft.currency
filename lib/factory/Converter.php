<?php
namespace travelsoft\currency\factory;

/**
 * Класс фабрика класса \travelsoft\currency\Converter
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class Converter extends AbstractFactory{

    public static function getInstance (travelsoft\currency\CuContainer $cuContainer = null, int $decimal = null, string $decPoint = null, string $ssep = null) {
        
        static $instances = array();
        
        if (!$cuContainer) {
            // генерируем объект $cuContainer по-умолчанию
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
        
        $hash = self::hashGeneration(array($cuContainer, $decimal, $decPoint, $ssep));
        
        if (!$instances[$hash]) {
            // генерируем класс конвертера
        }
        
        return $instances[$hash];
    }
}
