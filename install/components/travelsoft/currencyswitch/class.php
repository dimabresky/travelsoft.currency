<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class TravelsoftCurrencySwitch extends CBitrixComponent {

    public function executeComponent() {

        if (!\Bitrix\Main\Loader::includeModule('travelsoft.currency')) {
            throw new Exception(Loc::getMessage('TSCS_MOIDULE_NOT_FOUND'));
        }

        foreach (\travelsoft\currency\stores\Currencies::get() as $arCurrency) {
            $this->arResult['CURRENCY_ISO_LIST'][] = htmlspecialcharsbx($arCurrency['UF_ISO']);
        }

        if (in_array($_REQUEST['currency'], $this->arResult['CURRENCY_ISO_LIST'])) {

            $_SESSION['__TRAVELSOFT']['CURRENT_CURRENCY_ISO'] = $_REQUEST['currency'];
        } elseif (!in_array($_SESSION['__TRAVELSOFT']['CURRENT_CURRENCY_ISO'], $this->arResult['CURRENCY_ISO_LIST'])) {

            if (in_array($this->arParams['DEFAULT_CURRENCY_ISO'], $this->arResult['CURRENCY_ISO_LIST'])) {

                $_SESSION['__TRAVELSOFT']['CURRENT_CURRENCY_ISO'] = $this->arParams['DEFAULT_CURRENCY_ISO'];
            } else {

                $_SESSION['__TRAVELSOFT']['CURRENT_CURRENCY_ISO'] = current($this->arResult['CURRENCY_ISO_LIST']);
            }
        }

        $this->arResult['CURRENT_CURRENCY_ISO'] = $_SESSION['__TRAVELSOFT']['CURRENT_CURRENCY_ISO'];

        // формируем конвертер валюты для дальгейшего использования
        \travelsoft\currency\factory\Converter::getInstance(
                \travelsoft\currency\factory\CuContainer::getInstance(
                        \travelsoft\currency\factory\Currency::getInstance($this->arResult['CURRENT_CURRENCY_ISO'])
                )
        );

        $this->IncludeComponentTemplate();
    }

}
