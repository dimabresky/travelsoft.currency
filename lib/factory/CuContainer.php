<?php

namespace travelsoft\currency\factory;

/**
 * Фабрика объекта CuContainer
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class CuContainer extends \travelsoft\currency\interfaces\Factory {

    /**
     * @staticvar type $instances
     * @param \travelsoft\currency\Currency $currency
     * @return \travelsoft\currency\CuContainer
     */
    public static function getInstance(\travelsoft\currency\Currency $currency = null): \travelsoft\currency\CuContainer {

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

            $arCurrencies = \travelsoft\currency\stores\Currencies::get();
            $arCourse = current(\travelsoft\currency\stores\Courses::get(array("filter" => array("ID" => \travelsoft\currency\Settings::currentCourseId()))));

            $baseIso = (string) $arCurrencies[$arCourse["UF_BASE_ID"]]["UF_ISO"];

            foreach ($arCourses as $ISO => $course) {

                $tmpCurrency = new \travelsoft\currency\Currency($ISO);

                foreach ($arrCourses as $IISO => $ccourse) {

                    $comission = $ccourse->comission;
                    $val = $ccourse->value / $course->value;

                    if ($ISO != $baseIso && $IISO != $baseIso && $ISO != $IISO) {
                        $cval = $comission / 100;
                        $val = $ccourse->sourceValue / $course->sourceValue;
                        $val = $val + ($val * $cval);
                    }
                    $tmpCurrency->addCourse($IISO, new \travelsoft\currency\Course($val, $course->date, $comission));
                }

                $cuContainer->addCurrency($tmpCurrency);
            }

            $instances[$hesh] = $cuContainer;
        }

        return $instances[$hesh];
    }

}
