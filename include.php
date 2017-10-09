<?php

$classes = array(
    "travelsoft\\currency\\interfaces\\Factory" => "lib/interfaces/Factory.php",
    "travelsoft\\currency\\interfaces\\Store" => "lib/interfaces/Store.php",
    "travelsoft\\currency\\factory\\Converter" => "lib//factory/Converter.php",
    "travelsoft\\currency\\Converter" => "lib/Converter.php",
    "travelsoft\\currency\\factory\\CuContainer" => "lib/factory/CuContainer.php",
    "travelsoft\\currency\\CuContainer" => "lib/CuContainer.php",
    "travelsoft\\currency\\Course" => "lib/Course.php",
    "travelsoft\\currency\\factory\\Currency" => "lib/factory/Currency.php",
    "travelsoft\\currency\\Currency" => "lib/Currency.php",
    "travelsoft\\currency\\Settings" => "lib/Settings.php",
    "travelsoft\\currency\\stores\\Courses" => "lib/stores/Courses.php",
    "travelsoft\\currency\\stores\\Currencies" => "lib/stores/Currencies.php",
    "travelsoft\\currency\\CurrencyImporter" => "lib/CurrencyImporter.php",
    "travelsoft\\CREventsHandlers" => "lib/CREventsHandlers.php"
);
CModule::AddAutoloadClasses("travelsoft.currency", $classes);
require_once 'lib/functions.php';