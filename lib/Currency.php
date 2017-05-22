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
    public $iso = null;
    
    /**
     * @var \stdClass
     */
    public $courses = null;
    
    /**
     * @param int $id
     * @param string $iso
     * @throws \Exception
     */
    public function __construct(int $id, string $iso) {
        
        if ($id <= 0) {
            throw new \Exception(get_called_class(). ": Currency ID must be > 0");
        }
        $this->id = $id;
        
        if (preg_match("#[A-Z]{3}#", $iso) !== 1) {
            throw new \Exception(get_called_class() . ': The ISO code length must be 3 characters and consist of Latin letters in uppercase');
        }
        $this->iso = $iso;
        
        $this->courses = new \stdClass();
    }
    
    public function addCourse (string $iso, Course $course) {
        $this->courses->$iso = $course;
    }
    
}
