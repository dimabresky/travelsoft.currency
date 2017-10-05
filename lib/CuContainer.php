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
}
