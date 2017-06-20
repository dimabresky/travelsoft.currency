<?php

return array(
    array(
        "ENTITY_ID" => 'HLBLOCK_' . $CURRENCY_HL_ID,
        "FIELD_NAME" => "UF_ISO",
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
        'EDIT_FORM_LABEL' => array(
            'ru' => 'ISO Код'
        )
    ),
    array(
        "ENTITY_ID" => 'HLBLOCK_' . $CURRENCY_HL_ID,
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
        'EDIT_FORM_LABEL' => array(
            'ru' => 'Активность',
        )
    ),
);
