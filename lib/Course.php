<?php

namespace travelsoft\currency;

/**
 * Класс объектов курса валют
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class Course {

    /**
     * @var float
     */
    protected $_value = null;

    /**
     * @var string
     */
    protected $_date = null;

    /**
     * 
     * @var float
     */
    protected $comission = null;

    /**
     * @param float $value
     * @param string $date
     * @param float $comission
     * @throws \Exception
     */
    public function __construct(float $value, string $date = null, $comission = null) {

        $this->setValue($value);

        if ($date) {
            $this->setDate($date);
        }

        if ($comission) {
            $this->comission = $comission;
        }
    }

    /**
     * @param string $name
     * @return float|string
     * @throws \Exception
     */
    public function __get($name) {
        switch ($name) {
            case "value":
                return $this->_value;
            case "date":
                return $this->_date;
            case "comission":
                return $this->comission;
            case "sourceValue":
                $cval = $this->comission / 100;
                return $this->_value / (1 + $cval);
            default:
                throw new \Exception("Unknown parameter \"" . $name . "\"");
        }
    }

    /**
     * Устанавливает значение курса
     * @param float $value
     * @throws \Exception
     */
    public function setValue(float $value) {

        if ($value <= 0) {
            throw new \Exception(get_called_class() . ": The value of the exchange rate should be > 0");
        }
        $this->_value = $value;
    }

    /**
     * Устанавливает дату курса
     * @param string $date
     * @throws \Exception
     */
    public function setDate(string $date = null) {

        if (preg_match("#^\d{2}\.\d{2}\.\d{4}\s\d{2}\:\d{2}\:\d{2}$#", $date) !== 1) {
            throw new \Exception(get_called_class() . ": The date of the course must match the format");
        }
        $this->_date = $date;
    }

    /**
     * 
     * @param float $comission
     */
    function setComission($comission = null) {
        $this->comission = $comission;
    }

}
