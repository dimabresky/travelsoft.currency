<?php

namespace travelsoft\currency;

/**
 * Класс конвертер валюты (Singleton)
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class Converter {
    
    /**
     * @var \travelsoft\currency\CuContainer
     */
    protected $_cuContainer = null;

    /**
     * @var float
     */
    protected $_value = null;

    /**
     * @var string
     */
    protected $_ISO = null;

    /**
     * @var int
     */
    protected $_decimal = null;

    /**
     * @var string
     */
    protected $_decPoint = null;

    /**
     * @var boolean
     */
    protected $_ssep = false;
    
    /**
     * @param \travelsoft\currency\CuContainer $cuContainer
     * @param int $decimal
     * @param string $decPoint
     * @param bool $ssep
     */
    public function __construct(CuContainer $cuContainer, int $decimal = null, string $decPoint = '.', bool $ssep = false) {
        
        $this->_cuContainer = $cuContainer;
        $this->_decimal = $decimal >= 0 ? $decimal : 2;
        $this->_decPoint = $decPoint;
        $this->_ssep = $ssep;
        
    }

    /**
     * Производит конвертацию цены из одной валюты в другую
     * @param float $price
     * @param string $in
     * @param string $out
     * @return $this
     * @throws \Exception
     */
    public function convert(float $price, string $in, string $out = null) {
        
        if ($price <= 0) {

            throw new \Exception(get_called_class() . ": Price must be > 0");
        }

        $currencyIn = $this->_findCurrency($in);
        if (!$currencyIn->ISO) {

            throw new \Exception(get_called_class() . ": The currency from which we need convert is not found");
        }

        if (is_null($out)) {

            $out = $this->_cuContainer->currentIso;
        } else {
            
            if (!$currencyIn->courses->{$out}->value) {

                throw new \Exception(get_called_class() . ": The currency in which we convert is not found");
            }
        }
        
        $this->_value = $price / $currencyIn->courses->{$out}->value;
        $this->_ISO = (string) $out;
        return $this;
    }

    /**
     * Возвращает отформатированный результат конвертации цены
     * @return string
     */
    public function getResult(): string {

        return format($this->_value, $this->_ISO, $this->_decimal, $this->_decPoint, $this->_ssep);
    }

    /**
     * Возвращает массив результат конвертации цены в виде 
     * array("price" => price, "ISO" => iso currency)
     * @return array
     */
    public function getResultLikeArray(): array {

        return array("price" => $this->_value, "ISO" => $this->_ISO);
    }
    
    /**
     * Возвращает cuContainer
     * @return \travelsoft\currency\CuContainer
     */
    public function getCuContainer () : CuContainer {
        return $this->_cuContainer;
    }
    
    /**
     * Возвращает количество знаков после запятой
     * @return int
     */
    public function getDecimal () : int {
        return $this->_decimal;
    }
    
    /**
     * Возвращает разделитель дробной и целой части
     * @return string
     */
    public function getDecPoint () : string{
        return $this->_decPoint;
    }
    
    /**
     * Возвращает объект валюты ISO коду
     * @param string $ISO
     * @return \stdClass|Currency
     */
    protected function _findCurrency(string $ISO) {

        if ($this->_cuContainer->{$ISO}) {

            return $this->_cuContainer->{$ISO};
        }
        return new \stdClass();
    }

}
