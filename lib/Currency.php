<?php

namespace travelsoft\currency;

use travelsoft\currency\interfaces\Getter;

/**
 * Класс объектов валюты
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class Currency extends Getter{
    
    /**
     * @var int
     */
    public $id = null;
    
    /**
     * @var string
     */
    public $ISO = null;
    
    /**
     * @var \stdClass
     */
    public $courses = null;
    
    /**
     * @param string $ISO
     * @param int $id
     * @throws \Exception
     */
    public function __construct(string $ISO, int $id = null) {
        
        $this->setISO($ISO);
        
        if ($id) {
            $this->setISO($id);
        }
        
        $this->courses = new \stdClass();
    }
    
    /**
     * Устанавливает id валюты
     * @param int $id
     * @throws \Exception
     */
    public function setId (int $id) {
        if ($id <= 0) {
            throw new \Exception(get_called_class(). ": Currency ID must be > 0");
        }
        $this->id = $id;
    }
    
    /**
     * Устанавливает ISO код валюты
     * @param string $ISO
     */
    public function setISO (string $ISO) {
        $this->_checkISO($ISO);
        $this->ISO = $ISO;
    }
    
    /**
     * Добавляет курс валюты
     * @param string $ISO
     * @param \travelsoft\currency\Course $course
     */
    public function addCourse (string $ISO, Course $course) {
        $this->_checkISO($ISO);
        $this->courses->$ISO = $course;
    }
    
    /**
     * @param string $ISO
     * @throws \Exception
     */
    protected function _checkISO (string $ISO) {
        if (preg_match("#[A-Z]{3}#", $ISO) !== 1) {
            throw new \Exception(get_called_class() . ': The ISO code length must be 3 characters and consist of Latin letters in uppercase');
        }
    }
    
}
