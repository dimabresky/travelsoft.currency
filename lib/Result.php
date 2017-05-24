<?php

namespace travelsoft\currency;

use travelsoft\currency\Settings;

/**
 * Класс результата конвертации валюты
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class Result {
    
    /**
     * @var float
     */
    protected $price = null;
    
    /**
     * @var string
     */
    protected $currency = null;
    
    /**
     * @param float $price
     * @param string $currency
     */
    public function __construct(float $price, string $currency) {
        $this->price = $price;
        $this->currency = $currency;
    }
    
    /**
     * Возвращает отформатированный результат конвертации цены
     * @return string
     */
    public function getFormatted () : string {
        return  (string)number_format(
                    $this->price,
                    Settings::formatDecimal(),
                    Settings::formatDecPoint(), 
                    Settings::formatDecPoint() ? " " : ""
                ) . " " .$this->currency;
    }
    
    /**
     * Возвращает массив результат конвертации цены в виде 
     * array("price" => price, "currency" => iso currency)
     * @return array
     */
    public function getLikeArray () : array {
        return array("price" => $this->price, "currency" => $this->currency);
    }
    
}
