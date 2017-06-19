<?php
use Bitrix\Main\Localization\Loc,
        Bitrix\Main\ModuleManager,
                Bitrix\Main\Loader,
                    Bitrix\Main\Config\Option;
                    

Loc::loadMessages(__FILE__);

class new_travelsoft_currency extends CModule
{
    public $MODULE_ID = "new.travelsoft.currency";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = "N";
    protected $namespaceFolder = "travelsoft";
    protected $currency = array();
    protected $courses = array();
    protected $baseCurrencyId = null;

    function __construct()
    {
        $arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->MODULE_NAME = Loc::getMessage("NEW_TRAVELSOFT_CURRENCY_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("NEW_TRAVELSOFT_CURRENCY_MODULE_DESC");
        $this->PARTNER_NAME = "dimabresky";
        $this->PARTNER_URI = "https://github.com/dimabresky/";
        
        Loader::includeModule('highloadblock');
        
    }
    
    public function createHighload($HLNAME) {
        $result = Bitrix\Highloadblock\HighloadBlockTable::add(array(
            'NAME' => $HLNAME,
            'TABLE_NAME' => strtolower($HLNAME),
        ));
        if (!$result->isSuccess()) {
            throw new Exception (implode("<br>", $result->getErrorMessages()));
        }
        return $result->getId();
    }
    
    public function addHLFields($HL_FIELDS) {
        
        $oUserTypeEntity = new CUserTypeEntity();
                
        foreach ($HL_FIELDS as $aUserFields) {

            if (!$oUserTypeEntity->Add( $aUserFields )) {
                throw new Exception("Возникла ошибка при добавлении свойства " .$aUserFields["ENTITY_ID"] . "[".$aUserFields["FIELD_NAME"]."]" . $oUserTypeEntity->LAST_ERROR);
            }

        }
        
    }
    
    public function prepareRequest() {
   
        if (isset($_REQUEST['course'])) {
            $c = $_REQUEST['course'];
            foreach ($c['iso'] as $k => $v) {
                if ($v !== "" && $c['values'][$k] !== "") {
                   $this->courses[] = array(strtoupper($v), (float)str_replace(',', '.', $c['values'][$k]));
                   $this->currency[] = strtoupper($v);
                }
            }
            
            if (empty($this->courses)) {
                $GLOBALS['ERRORS_FORM'][] = Loc::getMessage('NEW_TRAVELSOFT_CURRENCY_COURSES_NOT_SET');
            }
        }
        
    }
    
    public function getHLDataClass ($id) {
        return \Bitrix\Highloadblock\HighloadBlockTable::compileEntity(
               \Bitrix\Highloadblock\HighloadBlockTable::getById($id)->fetch())->getDataClass();
    }
    
    public function addCurrency() {
       $dataClass = $this->getHLDataClass(Option::get($this->MODULE_ID, 'CURRENCY_HL_ID'));
       $arSave = array("UF_ACTIVE" => 1);
       $first = true;
       foreach ($this->currency as $v) {
           $arSave["UF_ISO"] = $v;
           $ID = $dataClass::add($arSave)->getId();
           if ($first && $ID) {
               
               $first = false;
               $this->baseCurrencyId = $ID;
           }
       }
    }
    
    public function getCoursesFields () {
        
        $HL_ID = Option::get($this->MODULE_ID, 'COURSES_HL_ID');
        $arFields = array(
            array(
                "ENTITY_ID" => 'HLBLOCK_' . $HL_ID,
                "FIELD_NAME" => "UF_BASE_ID",
                "USER_TYPE_ID" => 'integer',
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
                    'ru'    => 'ID базового курса'
                )
            ),
            array(
                "ENTITY_ID" =>  'HLBLOCK_' . $HL_ID,
                "FIELD_NAME" => "UF_ACTIVE",
                "USER_TYPE_ID" => 'boolean',
                "XML_ID" => "",
                "SORT" => 100,
                "MULTIPLE" => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'N',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' => array(
                        'DEFAULT_VALUE' => "0",
                        'DISPLAY' => 'CHECKBOX',
                    ),
                'EDIT_FORM_LABEL'   => array(
                    'ru'    => 'Активность',
                )
            ),
            array(
                "ENTITY_ID" =>  'HLBLOCK_' . $HL_ID,
                "FIELD_NAME" => "UF_DATE",
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
                    'ru'    => "Дата"
                )
            ),
            array(
                "ENTITY_ID" =>  'HLBLOCK_' . $HL_ID,
                "FIELD_NAME" => "UF_UNIX_DATE",
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
                    'ru'    => "Unix дата"
                )
            )
        );
        foreach ($this->courses as $v) {
            $arFields[] = array(
                "ENTITY_ID" =>  'HLBLOCK_' . $HL_ID,
                "FIELD_NAME" => "UF_" . $v[0],
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
                    'ru'    => $v[0]
                )
            );
        }
        return $arFields;
    }
    
    public function addCourses() {
        $dataClass = $this->getHLDataClass(Option::get($this->MODULE_ID, "COURSES_HL_ID"));
        $now = time();
        $arSave = array("UF_BASE_ID" => $this->baseCurrencyId, "UF_ACTIVE" => 1, "UF_DATE" => date('d.m.Y H:i:s', $now), "UF_UNIX_DATE" => $now);
        foreach ($this->courses as $v) {
            $arSave["UF_" . $v[0]] = $v[1];
        }
        $ID = $dataClass::add($arSave)->getId();
        Option::set($this->MODULE_ID, "CURRENT_COURSE_ID", $ID);
    }
    
    public function makeCurrencyStore () {
        // create HL currency
        $CURRENCY_HL_ID = $this->createHighload('TSCURRENCY');
        Option::set($this->MODULE_ID, 'CURRENCY_HL_ID', $CURRENCY_HL_ID);
        $this->addHLFields(@include_once 'currency_fields.php');
        // add currency
        $this->addCurrency();
    }
    
    public function makeCoursesStore () {
        // create HL courses currency
        $COURSES_HL_ID = $this->createHighload('TSCOURSES');
        Option::set($this->MODULE_ID, 'COURSES_HL_ID', $COURSES_HL_ID);
        $this->addHLFields($this->getCoursesFields());
        // add courses
        $this->addCourses();
    }
    
    public function setFormatOptions () {
        Option::set($this->MODULE_ID, 'FORMAT_DECIMAL', 2);
        Option::set($this->MODULE_ID, 'FORMAT_DEC_POINT', '.');
        Option::set($this->MODULE_ID, 'FORMAT_THOUSANDS_SEP', '');
    }
    
    public function unsetFormatOptions () {
        Option::delete($this->MODULE_ID, array('name' => 'FORMAT_DECIMAL'));
        Option::delete($this->MODULE_ID, array('name' => 'FORMAT_DEC_POINT'));
        Option::delete($this->MODULE_ID, array('name' => 'FORMAT_THOUSANDS_SEP'));
    }
    
    public function unsetCurrencyStore () {
        $CURRENCY_HL_ID = Option::get($this->MODULE_ID, "CURRENCY_HL_ID");
        if ($CURRENCY_HL_ID) {
            Bitrix\Highloadblock\HighloadBlockTable::delete($CURRENCY_HL_ID);
            Option::delete($this->MODULE_ID, array('name' => 'CURRENCY_HL_ID'));
        }
    }
    
    public function unsetCoursesStore () {
        $COURSES_HL_ID = Option::get($this->MODULE_ID, "COURSES_HL_ID");
        if ($COURSES_HL_ID) {
            Bitrix\Highloadblock\HighloadBlockTable::delete($COURSES_HL_ID);
            Option::delete($this->MODULE_ID, array('name' => 'COURSES_HL_ID'));
        }
        Option::delete($this->MODULE_ID, array('name' => 'CURRENT_COURSE_ID'));
    }
        
    public function eh ($function) {
        
        $ID_CR = Option::get($this->MODULE_ID, "CURRENCY_HL_ID");
        $ID_CO = Option::get($this->MODULE_ID, "COURSES_HL_ID");
        $HL_CR = \Bitrix\Highloadblock\HighloadBlockTable ::getById($ID_CR)->fetch();
        $HL_CO = \Bitrix\Highloadblock\HighloadBlockTable ::getById($ID_CO)->fetch();
        $function("", $HL_CR["NAME"] . "OnAfterAdd", $this->MODULE_ID, "travelsoft\\CREventsHandlers", "addCourseISOField");
        $function("", $HL_CR["NAME"] . "OnBeforeDelete", $this->MODULE_ID, "travelsoft\\CREventsHandlers", "saveISOBeforeDelete");
        $function("", $HL_CR["NAME"] . "OnAfterDelete", $this->MODULE_ID, "travelsoft\\CREventsHandlers", "deleteCourseISOField");
        $function("", $HL_CO["NAME"] . "OnAfterAdd", $this->MODULE_ID, "travelsoft\\CREventsHandlers", "setCurrenctCourse");
    }
    
    public function DoInstall()
    {
        try {
            
            if ( !ModuleManager::isModuleInstalled("highloadblock") )
                throw new Exception(Loc::getMessage("NEW_TRAVELSOFT_CURRENCY_HIGHLOADBLOCK_MODULE_NOT_INSTALL_ERROR"));
            
            $this->prepareRequest();
            
            if ($_REQUEST['step'] == "next" && !empty($this->currency) && !empty($this->courses)) {     
                
                // register module
                ModuleManager::registerModule($this->MODULE_ID);
                
                $this->makeCurrencyStore();
                
                $this->makeCoursesStore();
                
                $this->setFormatOptions();
                
                Option::set($this->MODULE_ID, 'COMMISSIONS', "");
                
                $this->eh("RegisterModuleDependences");
                
                return true;
            } 
            
            $GLOBALS['MODULE_ID'] = $this->MODULE_ID;
            // form add currency and currency courses
            $GLOBALS['APPLICATION']->IncludeAdminFile('Pre-options', $_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/pre_options_form.php");
            
            
        } catch (Exception $ex) {
            $GLOBALS["APPLICATION"]->ThrowException($ex->getMessage());
            $this->DoUninstall();
            return false;
        }
        
        return true;
    }
    
    public function DoUninstall()
    {
        
        $this->eh("UnRegisterModuleDependences");
        $this->unsetFormatOptions();
        $this->unsetCoursesStore();
        $this->unsetCurrencyStore();
        
        Option::delete($this->MODULE_ID, array('name' => 'COMMISSIONS'));
        
        // unregister module
        ModuleManager::UnRegisterModule($this->MODULE_ID);
        
        return true;
    }
}
