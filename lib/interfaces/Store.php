<?php

namespace travelsoft\currency\interfaces;

use travelsoft\currency\Settings;
use Bitrix\Highloadblock\HighloadBlockTable as HL;

\Bitrix\Main\Loader::includeModule("highloadblock");

/**
 * Абстрактный класс для работы с таблицами валют и курсов
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
abstract class Store {

    protected static $storeName = "";

    /**
     * Возвращает полученные данные из таблицы
     * @param array $query
     * @param callable $callback
     * @return array
     */
    public static function get(array $query = null, callable $callback = null): array {

        $table = self::getTable();
        $dbList = $table::getList((array) $query);
        $result = array();
        if ($callback) {
            while ($res = $dbList->fetch()) {
                $callback($res);
                $result[$res["ID"]] = $res;
            }
        } else {
            while ($res = $dbList->fetch()) {
                $result[$res["ID"]] = $res;
            }
        }

        return (array) $result;
    }

    /**
     * Добавляет запись в таблицу
     * @param array $fields
     * @return int
     */
    public static function add(array $fields): int {
        $table = self::getTable();
        return (int) $table::add($fields)->getId();
    }

    /**
     * Обновление записи по id
     * @param int $id
     * @param array $fields
     * @return boolean
     */
    public static function update(int $id, array $fields): bool {
        $table = self::getTable();
        return boolval($table::update($id, $fields));
    }

    /**
     * Обновление записи по id
     * @param int $id
     * @return boolean
     */
    public static function delete(int $id): bool {
        $table = self::getTable();
        return boolval($table::delete($id));
    }

    /**
     * @return string
     */
    private static function getTable(): string {
        $class = get_called_class();
        $tableId = $class::$storeName . "StoreId";
        return HL::compileEntity(HL::getById(Settings::$tableId())->fetch())->getDataClass();
    }

}
