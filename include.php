<?php

$classes = array(
    "travelsoft\\currency\\interfaces\\Getter" => "lib/interfaces/Getter.php",
    "travelsoft\\currency\\interfaces\\Store" => "lib/interfaces/Store.php",
    "travelsoft\\currency\\Converter" => "lib/Converter.php",
    "travelsoft\\currency\\Course" => "lib/Course.php",
    "travelsoft\\currency\\Currency" => "lib/Currency.php",
    "travelsoft\\currency\\Settings" => "lib/Settings.php",
    "travelsoft\\currency\\stores\\Courses" => "lib/stores/Courses.php",
    "travelsoft\\currency\\stores\\Currencies" => "lib/stores/Currencies.php",
    "travelsoft\\currency\\CurrencyImporter" => "lib/CurrencyImporter.php",
    "travelsoft\\CREventsHandlers" => "lib/CREventsHandlers.php"
);
CModule::AddAutoloadClasses("travelsoft.currency", $classes);
require_once 'lib/functions.php';