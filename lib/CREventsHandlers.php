<?php
namespace travelsoft;

/**
 * Класс обработки событий для модуля валюты
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class CREventsHandlers {
    
    protected static $module_id = "travelsoft.currency";
    
    /**
     * Добавление поля для курса при создании новой валюты
     * @param int $elementId
     * @throws Exception
     */
    public static function addCourseISOField ($elementId) {
        
        $ISO = currency\stores\Currencies::getISObyId($elementId);
        $HL_ID = \Bitrix\Main\Config\Option::get(self::$module_id, "COURSES_HL_ID");
        if ($ISO && $HL_ID) {
            
            $oUserTypeEntity = new \CUserTypeEntity();
            
            if (!$oUserTypeEntity->Add( array(
                "ENTITY_ID" =>  'HLBLOCK_' . $HL_ID,
                "FIELD_NAME" => "UF_" . $ISO,
                "USER_TYPE_ID" => 'string',
                "XML_ID" => "",
                "SORT" => 100,
                "MULTIPLE" => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'N',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' => array(
                    'DEFAULT_VALUE' => "",
                    'SIZE' => '20',
                    'ROWS' => 1,
                    'MIN_LENGTH' => 0,
                    'MAX_LENGTH' => 0,
                    'REGEXP' => ''
                ),
                'EDIT_FORM_LABEL'   => array(
                    'ru'    => $ISO
                )
            ) )) {
                throw new Exception("Error with create property " .$HL_ID . "[UF_".$ISO."]" . $oUserTypeEntity->LAST_ERROR);
            }
        }
    }
    
    /**
     * Сохранение ISO кода валюты перед его удалением
     * @param type $arElement
     */
    public static function saveISOBeforeDelete ($arElement) {
        
        \Bitrix\Main\Config\Option::set(self::$module_id, "ISO_SAVED", currency\stores\Currencies::getISObyId($arElement["ID"]));
    }
    
    /**
     * Удаление поля для ввода курса при удалении валюты
     * @param array $arElement
     */
    public static function deleteCourseISOField () {
        
        $ISO = \Bitrix\Main\Config\Option::get(self::$module_id, "ISO_SAVED");
        $HL_ID = \Bitrix\Main\Config\Option::get(self::$module_id, "COURSES_HL_ID");
        if ($ISO && $HL_ID) {
            
            $oUserTypeEntity = new \CUserTypeEntity();
            $arField = $oUserTypeEntity->GetList( array(), array("ENTITY_ID" => 'HLBLOCK_'.$HL_ID, "FIELD_NAME" => "UF_" . $ISO) )->Fetch();
            if ($arField["ID"] > 0) {
                $oUserTypeEntity->Delete($arField["ID"]);
            }
        }
        \Bitrix\Main\Config\Option::delete("travelsoft.currency", array('name' => 'ISO_SAVED'));
    }
    
    /**
     * Устанавливаем добавленный курс в качестве текущего
     * @param type $elementId
     * @param type $arFields
     */
    public static function setCurrenctCourse ($elementId, $arFields) {
        
        if ($arFields["UF_ACTIVE"]) {
            \Bitrix\Main\Config\Option::set(self::$module_id, "CURRENT_COURSE_ID", $elementId);
        }
    }
}
