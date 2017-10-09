<?php

namespace travelsoft\currency;

/**
 * Класс объектов валюты
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class Currency {

    /**
     * @var int
     */
    protected $_id = null;

    /**
     * @var string
     */
    protected $_ISO = null;

    /**
     * @var \stdClass
     */
    protected $_courses = null;

    /**
     * @param string $ISO
     * @param int $id
     * @throws \Exception
     */
    public function __construct(string $ISO) {

        $this->setISO($ISO);

        $this->_courses = new \stdClass();
    }
    
    /**
     * @param string $name
     * @return float|string|\stdClass
     * @throws \Exception
     */
    public function __get($name) {
        switch ($name) {
            case "ISO":
                return $this->_ISO;
            case "id":
                return $this->_id;
            case "courses":
                return $this->_courses;
            default:
                throw new \Exception("Unknown parameter \"".$name."\"");
        }
    }
    
    /**
     * Устанавливает id валюты
     * @param int $id
     * @throws \Exception
     */
    public function setId(int $id) {
        if ($id <= 0) {
            throw new \Exception(get_called_class() . ": Currency ID must be > 0");
        }
        $this->_id = $id;
    }

    /**
     * Устанавливает ISO код валюты
     * @param string $ISO
     */
    public function setISO(string $ISO) {
        $this->_checkISO($ISO);
        $this->_ISO = $ISO;
    }

    /**
     * Добавляет курс валюты
     * @param string $ISO
     * @param \travelsoft\currency\Course $course
     */
    public function addCourse(string $ISO, Course $course) {
        $this->_checkISO($ISO);
        $this->_courses->$ISO = $course;
    }

    /**
     * @param string $ISO
     * @throws \Exception
     */
    protected function _checkISO(string $ISO) {
        if (preg_match("#[A-Z]{3}#", $ISO) !== 1) {
            throw new \Exception(get_called_class() . ': The ISO code length must be 3 characters and consist of Latin letters in uppercase');
        }
    }

}
