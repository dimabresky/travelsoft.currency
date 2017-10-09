<?php

namespace travelsoft {

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
}

namespace travelsoft\currency {
    
    /**
     * Форматирует и возвращает цену
     * @param float $price
     * @param string $currency
     * @param int $decimal
     * @param string $decPoint
     * @param bool $ssep
     * @return string
     */
    function format (float $price, string $currency, int $decimal = 2, string $decPoint = '.', bool $ssep = false) : string {
        return (string) number_format(
                        $price, $decimal, $decPoint, $ssep ? " " : ""
                ) . " " . $currency;
    }
}