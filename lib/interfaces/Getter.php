<?php

namespace travelsoft\currency\interfaces;

/**
 * Абстрактный класс для геттера
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
abstract class Getter {

    public function __get($name) {
        $class = get_called_class();
        if (!property_exists($class, $name)) {
            throw new \Exception($class . ": Property does not exist");
        }
        return $this->$name;
    }

    protected function __set($name, $value) {
        $this->$name = $value;
    }

}
