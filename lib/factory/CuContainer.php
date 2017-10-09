<?php

namespace travelsoft\currency\factory;

/**
 * Фабрика объекта CuContainer
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class CuContainer extends \travelsoft\currency\interfaces\Factory{
    
    /**
     * @param \travelsoft\currency\Currency $currency
     * @return \travelsoft\currency\CuContainer
     */
    public static function getInstance (\travelsoft\currency\Currency $currency = null) : \travelsoft\currency\CuContainer {
       
        static $instances = array();
        
        if (!$currency) {
            $currency = Currency::getInstance();
        }
                
        $hash = parent::hashGeneration(array($currency));
        
        if (!$instances[$hash]) { // генерируем класс контейнера валют
            
            $arCourses = (array) $currency->courses;
            
            $arrCourses = $arCourses;
            
            $cuContainer = new \travelsoft\currency\CuContainer;
            
            $cuContainer->addCurrency($currency);
            
            $cuContainer->setCurrentCurrencyIso($currency->ISO);
            
            unset($arCourses[$currency->ISO]);
            
            foreach ($arCourses as $ISO => $course) {

                $tmpCurrency = new \travelsoft\currency\Currency($ISO);

                foreach ($arrCourses as $IISO => $ccourse) {

                    $tmpCurrency->addCourse($IISO, new \travelsoft\currency\Course($ccourse->value / $course->value, $course->date));
                }
                
                $cuContainer->addCurrency($tmpCurrency);
            }
            
            $instances[$hash] = $cuContainer;
        }
        
        return $instances[$hash];
        
    }
}
