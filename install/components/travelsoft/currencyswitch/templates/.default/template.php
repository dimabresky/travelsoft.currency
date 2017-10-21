<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!empty($arResult['CURRENCY_ISO_LIST'])) {
    
?>
<form id="currency-form" name="currency-form" action="<?= $APPLICATION->GetCurPageParam("", array('currency'), false)?>">
    <select name="currency" onchange="document.getElementById('currency-form').submit()">
        <?foreach ($arResult['CURRENCY_ISO_LIST'] as $iso):?>
        <option <?if ($iso == $arResult['CURRENT_CURRENCY_ISO']):?>selected=""<?endif?> value="<?= $iso?>"><?= $iso?></option>
        <?endforeach?>
    </select>
</form>
<?
    
}