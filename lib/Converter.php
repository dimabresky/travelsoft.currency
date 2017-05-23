<?php

namespace travelsoft\currency;

use travelsoft\currency\Settings;
use travelsoft\currency\Result;
use travelsoft\currency\Course;
use travelsoft\currency\Currency;
use travelsoft\currency\stores\Courses;
use travelsoft\currency\stores\Currencies;


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
     * @return self
     */
    public function getInstance () : self {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Производит конвертацию цены из одной валюты в другую
     * @param float $price
     * @param string $in
     * @param string $out
     * @return \travelsoft\currency\Result
     * @throws \Exception
     */
    public function convert ( float $price, string $in, string $out = null) : \travelsoft\currency\Result {
        
        if ($price <= 0) {
            
            throw new \Exception(get_called_class() . ": Price must be > 0");
        }
        
        $currencyIn = $this->_findCurrency((string)$in);
        if (!$currencyIn->id) {
            
            throw new \Exception(get_called_class() . ": The currency from which we can not convert is not found");
        }
        
        if ($out === null) {
            
            $currencyOut = $this->_findByISO($this->currentCurrencyISO);
        } else {
            
            $currencyOut = $this->_findCurrency((string)$out);
            if (!$currencyOut->id) {
                throw new \Exception(get_called_class() . ": The currency in which we convert is not found");
            }
        }
                
        return new Result((float)$price/$currencyIn->courses->{$currencyOut->iso}->value, (string)$currencyOut->iso);
        
    }
    
    /**
     * Устанавливает текущую валюту сайта
     * @param mixed $val
     * @return self
     */
    public function setCurrentCurrency ( $val ) : self {
        
        if (! ($currency = $this->_findCurrency($val)) ) {
            throw new \Exception(get_called_class() . ': The currency ('.$val.') you want to install is not found');
        }
        $this->currentCurrencyISO = $currency->iso;
        return $this;
    }
    
    /**
     * Устанавливает текущий курс по id
     * @param int $id
     * @return self
     * @throws \Exception
     */
    public function setCourseById (int $id) : self {
        
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
    public function getCurrency ($val) : \stdClass {
        
        if ( !($currency = $this->_findCurrency($val)) ) {
            throw new \Exception(get_called_class() . ': The currency "'.$val.'" not found');
        }
        return $currency;
    }
    
    /**
     * Возвращает объект текущей валюты сайта
     * @return \stdClass
     */
    public function getCurrentCurrency() : \stdClass {
        
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
     * @return string
     */
    protected function _getCurrentCurrencyISOFromSession() : string {
        
        return (string)$_SESSION["__TRAVELSOFT"]["CURRENCY_ISO"];
    }
    
    /**
     * Производит расчёт кросс-курсов
     * @param &$iso
     * @param &$arCourse
     * @param &$arCurrencies
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
     * @return \stdClass
     */
    protected function _findById (int $id) : \stdClass {
        
        foreach ($this->currencies as $currency) {
            if ($id === $currency->id) { return $currency; }
        }
        return \stdClass;
    }
    
    /**
     * Возвращает объект валюты по iso коду
     * @param string $iso
     * @return \stdClass|null
     */
    protected function _findByISO (string $iso) : \stdClass {
        
        if ($this->currencies[$iso]) {
            return $this->currencies[$iso];
        }
        return \stdClass;
    }
    
    /**
     * Возвращает объект валюты по id или ISO коду
     * @param string|null $val
     * @return \stdClass
     */
    protected function _findCurrency ($val) : \stdClass {
        
        if ( !($currency = $this->_findById((int)$val)) ) {
            $currency = $this->_findByISO((string)$val);
        }
        if ($currency) {
            return $currency;
        }
        return \stdClass;
    }
    
}
