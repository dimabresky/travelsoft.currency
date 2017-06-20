<?php

namespace travelsoft\currency;

use travelsoft\currency\Settings;
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
    protected static $_instance = null;

    /**
     * @var string
     */
    protected $_DCISO = null;

    /**
     * @var array
     */
    protected $_currencies = null;

    /**
     * @var float
     */
    protected $_price = null;

    /**
     * @var string
     */
    protected $_ISO = null;

    /**
     * @var int
     */
    protected $_decimal = 2;

    /**
     * @var string
     */
    protected $_decPoint = '.';

    /**
     * @var boolean
     */
    protected $_sSep = true;

    private function __construct() {
        
    }

    private function __clone() {
        
    }

    /**
     * Возвращает объект класса
     * @return self
     */
    public function getInstance(): self {

        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Устанавливает валюту
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency) {

        $this->_currencies[$currency->ISO] = $currency;
    }

    /**
     * Производит конвертацию цены из одной валюты в другую
     * @param float $price
     * @param string $in
     * @param string $out
     * @return self
     * @throws \Exception
     */
    public function convert(float $price, string $in, string $out = null): self {

        if ($price <= 0) {

            throw new \Exception(get_called_class() . ": Price must be > 0");
        }

        $currencyIn = $this->_findCurrency($in);
        if (!$currencyIn->ISO) {

            throw new \Exception(get_called_class() . ": The currency from which we need convert is not found");
        }

        if (is_null($out)) {

            $out = $this->_DCISO;
        } else {

            if (!$currencyIn->courses->{$out}->value) {

                throw new \Exception(get_called_class() . ": The currency in which we convert is not found");
            }
        }

        $this->_price = $price / $currencyIn->courses->{$out}->value;
        $this->_ISO = $out;
        return $this;
    }

    /**
     * Возвращает отформатированный результат конвертации цены
     * @return string
     */
    public function getResult(): string {

        return (string) number_format(
                        $this->_price, $this->_decimal, $this->_decPoint, $this->_sSep ? " " : ""
                ) . " " . $this->_ISO;
    }

    /**
     * Возвращает массив результат конвертации цены в виде 
     * array("price" => price, "ISO" => iso currency)
     * @return array
     */
    public function getResultLikeArray(): array {

        return array("price" => $this->_price, "ISO" => $this->_ISO);
    }

    /**
     * Возвращает iso текущей валюты приложения
     * @return string
     */
    public function getDefaultCurrencyIso(): string {

        return (string) $this->_DCISO;
    }

    /**
     * Установка количество знаков после запятой
     * @param int $dec
     * @return self
     */
    public function setDecimal(int $dec): self {

        $this->_decimal = intVal($dec);
        return $this;
    }

    /**
     * Установка разделителя целой и дробной части
     * @param string $decPoint
     * @return self
     */
    public function setDecPoint(string $decPoint): self {

        $this->_decPoint = $decPoint;
        return $this;
    }

    /**
     * Установка признака разделения знаков тысячных разрядов пробелом
     * @param bool $sSep
     * @return self
     */
    public function setSSep(bool $sSep): self {

        $this->_sSep = $sSep;
        return $this;
    }

    /**
     * Устанавливает текущую валюту
     * для конвертации по-умолчанию
     * @param string $val
     * @return self
     */
    public function setDefaultConversionISO(string $val): self {

        $currency = $this->_findCurrency($val);
        if (!$currency->ISO) {

            throw new \Exception(get_called_class() . ': The currency (' . $val . ') you want to install is not found');
        }

        $this->_DCISO = $currency->ISO;
        return $this;
    }

    /**
     * Возвращает объект валюты ISO коду
     * @param string $val
     * @return Currency
     * @throws \Exception
     */
    public function getCurrency(string $val): Currency {

        $currency = $this->_findCurrency($val);
        if (!$currency->ISO) {

            throw new \Exception(get_called_class() . ': The currency "' . $val . '" not found');
        }
        return $currency;
    }

    /**
     * Инициализация объекта класса из настроек модуля
     * @return \self
     */
    public function initDefault(): self {

        $defaultCurrency = Settings::defaultCurrency();
        $this->_currencies[$defaultCurrency->ISO] = $defaultCurrency;
        $this->_setCrossCourse($defaultCurrency);
        $this->setDefaultConversionISO($defaultCurrency->ISO);
        $this->_decimal = Settings::formatDecimal();
        $this->_decPoint = Settings::formatDecPoint();
        $this->_sSep = Settings::formatSSep();
        return $this;
    }

    /**
     * Производит расчёт кросс-курсов
     * @param Currency $currency
     */
    protected function _setCrossCourse(Currency $currency) {

        $arCourses = (array) $currency->courses;
        $arrCourses = $arCourses;
        unset($arCourses[$currency->ISO]);

        foreach ($arCourses as $ISO => $course) {

            $this->_currencies[$ISO] = new Currency($ISO);

            foreach ($arrCourses as $IISO => $ccourse) {

                $this->_currencies[$ISO]->addCourse($IISO, new Course($course->value / $ccourse->value)
                );
            }
        }
    }

    /**
     * Возвращает объект валюты ISO коду
     * @param string $ISO
     * @return \stdClass|Currency
     */
    protected function _findCurrency(string $ISO) {

        if ($this->_currencies[$ISO]) {

            return $this->_currencies[$ISO];
        }
        return new \stdClass();
    }

}
