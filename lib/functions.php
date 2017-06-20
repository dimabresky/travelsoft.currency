<?php

namespace travelsoft;

if (!function_exists("\\travelsoft\\ats")) {

    /**
     * Конвертирует массив в строку
     * @param array $array
     * @return string
     */
    function ats(array $array): string {
        return (string) base64_encode(gzcompress(serialize($array), 9));
    }

}

if (!function_exists("\\travelsoft\\sta")) {

    /**
     * Коневертирует строку в массив
     * @param string $str
     * @return array
     */
    function sta(string $str): array {
        return (array) unserialize(gzuncompress(base64_decode($str)));
    }

}