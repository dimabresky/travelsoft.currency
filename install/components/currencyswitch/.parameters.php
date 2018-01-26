<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

\Bitrix\Main\Loader::includeModule('travelsoft.currency');

$arCurrenciesParameterValues = array();

foreach (\travelsoft\currency\stores\Currencies::get() as $currency) {
    
    $arCurrenciesParameterValues[$currency['UF_ISO']] = $currency['UF_ISO'];
}

$arComponentParameters['PARAMETERS']['DEFAULT_CURRENCY_ISO'] = array(
    "PARENT" => "BASE",
    "NAME" => GetMessage("TSCS_DEFAULT_CURRENCY_PARAMETER_NAME"),
    "TYPE" => "LIST",
    "VALUES" => $arCurrenciesParameterValues
);