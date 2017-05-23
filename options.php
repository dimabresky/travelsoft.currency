<?php
use Bitrix\Main\Localization\Loc;

require_once 'lib/functions.php';

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
 
$main_options = array(
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
        foreach( $main_options as $name => $desc ) {
            \Bitrix\Main\Config\Option::delete( $module_id, array('name' => $name) );
        }
        \Bitrix\Main\Config\Option::delete( $module_id, array('name' => "COMMISSIONS") );
    }
    else {
        foreach( $main_options as $name => $desc ) {

            if( isset( $_REQUEST[$name] ) ) {
                \Bitrix\Main\Config\Option::set( $module_id, $name, $_REQUEST[$name] );
            } elseif ($desc['type'] === "checkbox") {
                \Bitrix\Main\Config\Option::delete( $module_id, array('name' => $name) );
            }
        }
        if ($_REQUEST["COMMISSION_FOR"]) {
            \Bitrix\Main\Config\Option::delete( $module_id, array('name' => "COMMISSIONS") );
            \Bitrix\Main\Config\Option::set( $module_id, "COMMISSIONS", travelsoft\ats((array)$_REQUEST["COMMISSION_FOR"]) );
        }
    }
    
    
    
    LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&".$o_tab->ActiveTabParam());
}
$o_tab->Begin();
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&amp;lang=<?echo LANGUAGE_ID?>">
<?
$o_tab->BeginNextTab();
foreach( $main_options as $name => $desc ):
	$cur_opt_val = htmlspecialcharsbx(Bitrix\Main\Config\Option::get( $module_id, $name ));
	$name = htmlspecialcharsbx($name);
?>
    <tr>
        <td width="40%">
            <label for="<?echo $name?>"><?echo $desc['desc']?>:</label>
        </td>
        <td width="60%">
            <?if($desc['type'] == "select"):?>
            <select onchange="renderCommissions(this)" id="<?echo $name?>" name="<?echo $name?>">
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
        
    
    <?if ($name === "BASE_CURRENCY_ID"):
            $commissions = travelsoft\sta((string)Bitrix\Main\Config\Option::get($module_id, "COMMISSIONS"));
        ?>
    
    <script>
    // перерисовка надбавок по валютам
    function renderCommissions (that) {
        
        var currency = JSON.parse('<?= \Bitrix\Main\Web\Json::encode($desc['def'])?>');
        var title = '<?= Loc::getMessage("TRAVELSOFT_CURRENCY_COMMISSION")?>';
        var commissions = JSON.parse('<?= \Bitrix\Main\Web\Json::encode($commissions)?>');
        var html = '';
        
        for (var i = 0; i < currency.length; i++) {

            document.getElementById('commission-for-' + currency[i][1]).innerHTML =  getHtml(currency[i], commissions, title, currency[i][0] == that.value);
        }
        
    }
    // html ввода данных
    function getHtml (c, com, t, hide) {
        var html = '';
        if (hide) {
            html += '<td width="40%" style="display: none" class="adm-detail-content-cell-l">';
        } else {
            html += '<td width="40%" class="adm-detail-content-cell-l">';
        }
        html += '<label for="COMMISSION_FOR[' + c[1] + ']">'+t.replace("#ISO#", c[1])+'</label>';
        html += '</td>';
        if (hide) {
            html += '<td width="60%" style="display: none" class="adm-detail-content-cell-r">';
        } else {
            html += '<td width="40%" class="adm-detail-content-cell-r">';
        }
        html += '<input name="COMMISSION_FOR[' + c[1] + ']" value="'+ ( com[c[1]] || "" ) +'" type="text">';
        html += '</td>';
        return html;
    }
    </script>    
    
    <?foreach ($desc['def'] as $val):?>
    <?$style = ""; if ($val[0] == $cur_opt_val) {$style = 'style="display:none"';}?>
    <tr id="commission-for-<?= $val[1]?>">
        <td width="40%" <?= $style?>>
            <label for="COMMISSION_FOR[<?= $val[1]?>]"><?= Loc::getMessage("TRAVELSOFT_CURRENCY_COMMISSION", array("#ISO#" => $val[1]))?></label>
        </td>
        <td width="60%" <?= $style?>>
            <input name="COMMISSION_FOR[<?= $val[1]?>]" value="<?= $commissions[$val[1]]?>" type="text">
        </td>
    </tr>
    <?endforeach;
    endif?>
        
        
<?endforeach?>
<?$o_tab->Buttons();?>
    <input type="submit" name="save" value="<?= Loc::getMessage("TRAVELSOFT_CURRENCY_SAVE_BTN_NAME")?>" title="<?= Loc::getMessage("TRAVELSOFT_CURRENCY_SAVE_BTN_NAME")?>" class="adm-btn-save">
    <input type="submit" name="reset" title="<?= Loc::getMessage("TRAVELSOFT_CURRENCY_RESET_BTN_NAME")?>" OnClick="return confirm('<?echo AddSlashes(Loc::getMessage("TRAVELSOFT_CURRENCY_RESTORE_WARNING"))?>')" value="<?= Loc::getMessage("TRAVELSOFT_CURRENCY_RESET_BTN_NAME")?>">
    <?=bitrix_sessid_post();?>
<?$o_tab->End();?>
</form>