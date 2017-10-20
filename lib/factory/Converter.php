<?php
namespace travelsoft\currency\factory;

/**
 * Класс фабрика класса \travelsoft\currency\Converter
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class Converter extends \travelsoft\currency\interfaces\Factory{
    
    /**
     * @staticvar array $instances
     * @param \travelsoft\currency\CuContainer $cuContainer
     * @param int $decimal
     * @param string $decPoint
     * @param string $ssep
     * @return \travelsoft\currency\Converter
     */
    public static function getInstance (\travelsoft\currency\CuContainer $cuContainer = null, int $decimal = null, string $decPoint = null, string $ssep = null) : \travelsoft\currency\Converter {
        
        static $instances = array();
        
        if (!$cuContainer) {
            $cuContainer = CuContainer::getInstance();
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
            $instances[$hash] = new \travelsoft\currency\Converter($cuContainer, $decimal, $decPoint, $ssep);
        }
        
        return $instances[$hash];
    }
}
