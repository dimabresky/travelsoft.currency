<?php
use Bitrix\Main\Localization\Loc;

if( ! $USER->isAdmin() || ! \Bitrix\Main\Loader::includeModule("highloadblock") ) return ;

Loc::loadMessages(__FILE__);

global $APPLICATION;

$module_id = "travelsoft.currency";

$dataClass = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity(
               \Bitrix\Highloadblock\HighloadBlockTable::getById(Bitrix\Main\Config\Option::get( $module_id, 'CURRENCY_HL_ID' ))->fetch())->getDataClass();

 $dbList = $dataClass::getList();
 while ($arCurrency = $dbList->fetch()) {
     $arCurrencies[] = array($arCurrency['ID'], $arCurrency['UF_ISO']);
 }
 
$dataClass = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity(
               \Bitrix\Highloadblock\HighloadBlockTable::getById(Bitrix\Main\Config\Option::get( $module_id, 'COURSES_HL_ID' ))->fetch())->getDataClass();

 $dbList = $dataClass::getList();
 while ($arCourse = $dbList->fetch()) {
     $arCourses[] = array($arCourse['ID'], $arCourse['UF_DATE']);
 }
 
$all_options = array(
   'BASE_CURRENCY_ID' => array('desc' => Loc::getMessage('TRAVELSOFT_CURRENCY_BASE_CURRENCY_ID'), 'type' => 'select', 'def' => $arCurrencies),
   'BASE_COURSE_ID' => array('desc' => Loc::getMessage('TRAVELSOFT_CURRENCY_BASE_COURSES_ID'), 'type' => 'select', 'def' => $arCourses),
   'FORMAT_DECIMAL' => array('desc' => Loc::getMessage('TRAVELSOFT_CURRENCY_CURRENCY_FORMAT_DECIMAL'), 'type' => 'text'),
   'FORMAT_DEC_POINT' => array('desc' => Loc::getMessage('TRAVELSOFT_CURRENCY_CURRENCY_FORMAT_DEC_POINT'), 'type' => 'text'),
   'FORMAT_THOUSANDS_SEP' => array('desc' => Loc::getMessage('TRAVELSOFT_CURRENCY_CURRENCY_FORMAT_THOUSANDS_SEP'), 'type' => 'checkbox')
);
$tabs = array(
    array(
            "DIV" => "edit1",
            "TAB" => Loc::getMessage("TRAVELSOFT_CURRENCY_TAB_NAME"),
            "ICON" => "erip-icon",
            "TITLE" => Loc::getMessage("TRAVELSOFT_CURRENCY_TAB_DESC")
    ),
);
		
$o_tab = new CAdminTabControl("TravelsoftTabControl", $tabs);
if( $REQUEST_METHOD == "POST" && strlen( $save . $reset ) > 0 && check_bitrix_sessid() )
{
	if( strlen($reset) > 0 ) {
            foreach( $all_options as $name => $desc ) {
                \Bitrix\Main\Config\Option::delete( $module_id, array('name' => $name) );
            }
	}
	else {
            foreach( $all_options as $name => $desc ) {

                if( isset( $_REQUEST[$name] ) ) {
                    \Bitrix\Main\Config\Option::set( $module_id, $name, $_REQUEST[$name] );
                } elseif ($desc['type'] === "checkbox") {
                    \Bitrix\Main\Config\Option::delete( $module_id, array('name' => $name) );
                }

            }
	}
	
	LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&".$o_tab->ActiveTabParam());
}
$o_tab->Begin();
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&amp;lang=<?echo LANGUAGE_ID?>">
<?
$o_tab->BeginNextTab();
foreach( $all_options as $name => $desc ):
	$cur_opt_val = htmlspecialcharsbx(Bitrix\Main\Config\Option::get( $module_id, $name ));
	$name = htmlspecialcharsbx($name);
?>
	<tr>
            <td width="40%">
                <label for="<?echo $name?>"><?echo $desc['desc']?>:</label>
            </td>
            <td width="60%">
                <?if($desc['type'] == "select"):?>
                    <select id="<?echo $name?>" name="<?echo $name?>">
                        <?foreach ($desc['def'] as $val) :?>
                        <option <?if ($cur_opt_val == $val[0]) :?>selected<?endif?> value="<?= $val[0]?>"><?= $val[1]?></option>
                        <?endforeach?>
                    </select>
                <?elseif ($desc["type"] == "checkbox"):?>
                    <input type="checkbox" id="<?echo $name?>" value="Y" name="<?echo $name?>">
                <?else:?>
                    <input type="text" id="<?echo $name?>" value="<?= $cur_opt_val?>" name="<?echo $name?>">
                <?endif?>
            </td>
	</tr>
<?endforeach?>
<?$o_tab->Buttons();?>
    <input type="submit" name="save" value="<?= Loc::getMessage("TRAVELSOFT_CURRENCY_SAVE_BTN_NAME")?>" title="<?= Loc::getMessage("TRAVELSOFT_CURRENCY_SAVE_BTN_NAME")?>" class="adm-btn-save">
    <input type="submit" name="reset" title="<?= Loc::getMessage("TRAVELSOFT_CURRENCY_RESET_BTN_NAME")?>" OnClick="return confirm('<?echo AddSlashes(Loc::getMessage("TRAVELSOFT_CURRENCY_RESTORE_WARNING"))?>')" value="<?= Loc::getMessage("TRAVELSOFT_CURRENCY_RESET_BTN_NAME")?>">
    <?=bitrix_sessid_post();?>
<?$o_tab->End();?>
</form>