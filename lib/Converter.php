<?php

namespace travelsoft\currency;

use travelsoft\currency\Settings;
use travelsoft\currency\Result;
use travelsoft\currency\stores\Courses;
use travelsoft\currency\stores\Currencies;
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
     * @var $this 
     */
    protected $instance = null;
    
    /**
     * @var string
     */
    protected $currentCurrencyISO = null;
    
    /**
     * @var array
     */
    protected $currencies = null;
    
    private function __construct() {
        
        # рассчёт курсов валют
        $this->setCourseById( Settings::baseCourseId() );
        
        # установка текущией валюты сайта
        if ($_REQUEST["currency"]) {
            
            $this->setCurrentCurrency($_REQUEST["currency"])->_saveCurrentCurrencyISOinSession();
        } elseif (($cciso = $this->_getCurrentCurrencyISOFromSession())) {
            
            $this->setCurrentCurrency($cciso);
        } else {
            
            $this->setCurrentCurrency(Settings::baseCurrencyId())->_saveCurrentCurrencyISOinSession();
        }
    }
    
    private function __clone() {}
    
    /**
     * Возвращает объект класса
     * @return $this
     */
    public function getInstance () {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Производит конвертацию цены из одной валюты в другую
     * @param mixed $price
     * @param string|null $in
     * @param string|null $out
     * @throws \Exception
     */
    public function convert ($price, $in, $out = null) {
        
        $price = (float)$price;
        
        if ($price <= 0) {
            
            throw new \Exception(get_called_class() . ": Price must be > 0");
        }

        if (!($currencyIn = $this->_findCurrency($in))) {
            
            throw new \Exception(get_called_class() . ": The currency from which we can not convert is not found");
        }
        
        if ($out === null) {
            
            $currencyOut = $this->_findByISO($this->currentCurrencyISO);
        } elseif( !($currencyOut->_findCurrency($out) ) )  {
            
            throw new \Exception(get_called_class() . ": The currency in which we convert is not found");
        }
                
        return new Result($price/$currencyIn->courses->{$currencyOut->iso}->value, $currencyOut->iso);
        
    }
    
    /**
     * Устанавливает текущую валюту сайта
     * @param mixed $val
     * @return $this
     */
    public function setCurrentCurrency ( $val ) {
        
        if (! ($currency = $this->_findCurrency($val)) ) {
            throw new \Exception(get_called_class() . ': The currency ('.$val.') you want to install is not found');
        }
        $this->currentCurrencyISO = $currency->iso;
        return $this;
    }
    
    /**
     * Устанавливает текущий курс по id
     * @param int $id
     * @return $this
     * @throws \Exception
     */
    public function setCourseById (int $id) {
        
        $arCourse = current(Courses::get(array("filter" => array("ID" => $id))));
        
        if (!empty($arCourse)) {
            
            $arCurrencies = Currencies::get();
            
            foreach ($arCurrencies as $arCurrency) {
                
                if (!$this->currencies[$arCurrency["UF_ISO"]]) {
                    $this->currencies[$arCurrency["UF_ISO"]] = new Currency(
                    $arCurrency["ID"],
                    $arCurrency["UF_ISO"]
                    );
                } 
                
                if ($arCurrency["ID"] === $arCourse["UF_BASE_ID"]) {
                    $this->currencies[$arCurrency["UF_ISO"]]->addCourse(
                        $arCurrency["UF_ISO"], 
                        new Course(
                                (float)$arCourse["UF_" . $arCurrency["UF_ISO"]], 
                                (string)$arCourse["UF_DATE"]
                                )
                        );
                } else {
                    $this->_setCrossCourse($arCurrency["UF_ISO"], $arCourse, $arCurrencies);
                }
                
            }
            
            return $this;
        }   
        
        throw new \Exception(get_called_class() . ': Course with id "'.$id.'" not found');
    }
    
    /**
     * Возвращает объект валюты по id или ISO коду
     * @param mixed $val
     * @return \stdClass
     * @throws \Exception
     */
    public function getCurrency ($val) {
        
        if ( !($currency = $this->_findCurrency($val)) ) {
            throw new \Exception(get_called_class() . ': The currency "'.$val.'" not found');
        }
        return $currency;
    }
    
    /**
     * Возвращает объект текущей валюты сайта
     * @return \stdClass
     */
    public function getCurrentCurrency() {
        
        return $this->getCurrency($this->currentCurrencyISO);
    }
    
    /**
     * Сохраняет текущий ISO валюты сайта в сессию
     */
    protected function _saveCurrentCurrencyISOinSession () {
        
        $_SESSION["__TRAVELSOFT"]["CURRENCY_ISO"] = $this->currentCurrencyISO;
    }
    
    /**
     * Возвращает iso валюты из сессии
     * @return mixed
     */
    protected function _getCurrentCurrencyISOFromSession() {
        
        return $_SESSION["__TRAVELSOFT"]["CURRENCY_ISO"];
    }
    
    /**
     * Производит расчёт кросс-курсов
     * @param type $oCurrency
     * @param type $arCourse
     * @param type $arCurrencies
     */
    protected function _setCrossCourse (&$iso, &$arCourse, &$arCurrencies) {
        
        foreach ($arCurrencies as $arCurrency) {
            $this->currencies[$iso]->addCourse(
                        $arCurrency["UF_ISO"],
                        new Course(
                                (float)($arCourse["UF_" . $iso]/$arCourse["UF_" . $arCurrency["UF_ISO"]]),
                                (string)$arCourse["UF_DATE"]
                                )
                    );
        }
        
    }
    
    /**
     * Возвращает объект валюты по id
     * @param int $id
     * @return \stdClass|null
     */
    protected function _findById (int $id) {
        
        foreach ($this->currencies as $currency) {
            if ($id === $currency->id) { return $currency; }
        }
        return null;
    }
    
    /**
     * Возвращает объект валюты по iso коду
     * @param string $iso
     * @return \stdClass|null
     */
    protected function _findByISO (string $iso) {
        
        if ($this->currencies[$iso]) {
            return $this->currencies[$iso];
        }
        return null;
    }
    
    /**
     * Возвращает объект валюты по id или ISO коду
     * @param string|null $val
     * @return \stdClass|null
     */
    protected function _findCurrency ($val) {
        
        if ( !($currency = $this->_findById((int)$val)) ) {
            $currency = $this->_findByISO((string)$val);
        }
        if ($currency) {
            return $currency;
        }
        return null;
    }
    
}
