<?php

namespace travelsoft\currency\factory;

/**
 * Интерфейс фабрик
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
abstract class AbstractFactory {

    abstract public static function getInstance();

    public static function hashGeneration(array $parameters): string {

        $str = '';
        foreach ($parameters as $parameter) {
            $str .= serialize($parameter);
        }

        return md5($str);
    }

}
