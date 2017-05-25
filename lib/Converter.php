<?php

namespace travelsoft\currency;

use travelsoft\currency\Settings;
use travelsoft\currency\Result;
use travelsoft\currency\Course;
use travelsoft\currency\Currency;


/**
 * Класс конвертер валюты (Singleton)
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class Converter {

    /**
     * @var self
     */
    protected static $instance = null;

    /**
     * @var string
     */
    protected $DCISO = null;

    /**
     * @var array
     */
    protected $currencies = null;

    private function __construct() {}

    private function __clone() {}

    /**
     * Возвращает объект класса
     * @return self
     */
    public function getInstance () : self {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Устанавливает валюту
     * @param Currency $currency
     */
    public function setCurrency (Currency $currency) {

        $this->currencies[$currency->ISO] = $currency;
    }

    /**
     * Производит конвертацию цены из одной валюты в другую
     * @param float $price
     * @param string $in
     * @param string $out
     * @return \travelsoft\currency\Result
     * @throws \Exception
     */
    public function convert (float $price, string $in, string $out = null) : \travelsoft\currency\Result {

        if ($price <= 0) {

            throw new \Exception(get_called_class() . ": Price must be > 0");
        }

        $currencyIn = $this->_findCurrency($in);
        if (!$currencyIn->ISO) {

            throw new \Exception(get_called_class() . ": The currency from which we need convert is not found");
        }

        if ($out === null) {

            $out = $this->DCISO;
        } else {
            
            if ($currencyIn->courses->{$out}->value) {
                throw new \Exception(get_called_class() . ": The currency in which we convert is not found");
            }
        }

        return new Result((float)$price/$currencyIn->courses->{$out}->value, (string)$out);
    }

    /**
     * Устанавливает текущую валюту
     * для конвертации по-умолчанию
     * @param string $val
     * @return self
     */
    public function  setDefaultConversionISO (string $val) : self {
        
        $currency = $this->_findCurrency($val);
        if (! $currency->ISO ) {
            throw new \Exception(get_called_class() . ': The currency ('.$val.') you want to install is not found');
        }
        $this->DCISO = $currency->ISO;
        return $this;
    }

    /**
     * Возвращает объект валюты ISO коду
     * @param string $val
     * @return Currency
     * @throws \Exception
     */
    public function getCurrency (string $val) : Currency {
        
        $currency = $this->_findCurrency($val);
        if ( !$currency->ISO ) {
            throw new \Exception(get_called_class() . ': The currency "'.$val.'" not found');
        }
        return $currency;
    }

    /**
     * Инициализация объекта класса из настроек модуля
     */
    public function initDefault () {

        $defaultCurrency = Settings::defaultCurrency();
        $this->currencies[$defaultCurrency->ISO] = $defaultCurrency;
        $this->_setCrossCourse($defaultCurrency);
        $this->DCISO = $defaultCurrency->ISO;
    }

    /**
     * Производит расчёт кросс-курсов
     * @param Currency $currency
     */
    protected function _setCrossCourse (Currency $currency) {

        $arCourses = (array)$currency->courses;
        $arrCourses = $arCourses;
        unset($arCourses[$currency->ISO]);

        foreach ($arCourses as $ISO => $course) {

            $this->currencies[$ISO] = new Currency($ISO);

            foreach ($arrCourses as $IISO => $ccourse) {
                $this->currencies[$ISO]->addCourse($IISO, new Course($course->value/$ccourse->value)
                );
            }
        }
    }

    /**
     * Возвращает объект валюты ISO коду
     * @param string $ISO
     * @return \stdClass|Currency
     */
    protected function _findCurrency (string $ISO) {
        if ($this->currencies[$ISO]) {
            return $this->currencies[$ISO];
        }
        return new \stdClass();
    }
}
