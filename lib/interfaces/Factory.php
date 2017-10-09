<?php

namespace travelsoft\currency\interfaces;

/**
 * Интерфейс фабрик
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
abstract class Factory {
        
    abstract public static function getInstance();

    public static function hashGeneration(array $parameters): string {

        return md5(serialize($parameters));
    }

}
