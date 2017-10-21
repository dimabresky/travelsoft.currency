<?php
namespace travelsoft\currency;

/**
 * Класс-контейнер для валют ()
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class CuContainer {
    
    /**
     * @var \stdClass
     */
    protected $_currencies = null;
    
    protected $_currentIso = null;
    
    public function __construct() {
        $this->_currencies = new \stdClass();
    }
    
    /**
     * Добавляет объект валюты в контейнер
     * @param \travelsoft\currency\Currency $currency
     */
    public function addCurrency (Currency $currency) {
        $this->_currencies->{$currency->ISO} = $currency; 
    }
    
    /**
     * Устанавливает iso валюты по-умолчанию
     * @param string $iso
     * @throws \Exception
     */
    public function setCurrentCurrencyIso (string $iso) {
        if (!$this->_currencies->{$iso}) {
            throw new \Exception("Unknown currency \"".$iso."\"");
        }
        $this->_currentIso = $iso;
    }
    
    /**
     * Возвращает информацию по валютам в виде массива
     * @return array
     */
    public function getCurrenciesLikeArray() : array{
        return (array)$this->_currencies;
    }
    
    /**
     * @param string $name
     * @return string|\travelsoft\currency\Currency
     * @throws \Exception
     */
    public function __get($name) {
        if ($name === 'currentIso') {
            return $this->_currentIso;
        }
        if (!$this->_currencies->{$name}) {
            throw new \Exception("Unknown currency");
        }
        return $this->_currencies->{$name};
    }
}
