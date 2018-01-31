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
     * @staticvar type $instances
     * @param \travelsoft\currency\Currency $currency
     * @return \travelsoft\currency\CuContainer
     */
    public static function getInstance (\travelsoft\currency\Currency $currency = null) : \travelsoft\currency\CuContainer{
       
        static $instances = null;
        
        $hesh = md5(serialize(func_get_args()));
        
        if (!$instances[$hesh]) { // генерируем класс контейнера валют
            
            if (!$currency) {
                $currency = Currency::getInstance();
            }
            
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
            
            $instances[$hesh] = $cuContainer;
        }
        
        return $instances[$hesh];
        
    }
}
