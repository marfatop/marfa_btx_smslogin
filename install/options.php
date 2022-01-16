<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialcharsbx($request['mid'] != '' ? $request['mid'] : $request['id']);

Loader::includeModule($module_id);

$aTabs = array(
    array(
        'DIV' => 'edit',
        'TAB' => Loc::getMessage('MARFA_LOGIN_BY_SMS_OPTIONS_TAB_NAME'),
        'TITLE' => Loc::getMessage('MARFA_LOGIN_BY_SMS_OPTIONS_TAB_NAME'),
        'OPTIONS' => array(
            array(
                'MAX_COUNT_TRY',
                Loc::getMessage('MARFA_LOGIN_BY_SMS_COUNT_TRY'),
                '3',
                array('text', 2)
            ),
            array(
                'MARFA_LOGIN_BY_SMS_MAX_COUNT_GENERATE',
                Loc::getMessage('MARFA_LOGIN_BY_SMS_COUNT_GENERATE'),
                '3',
                array('text', 2)
            ),
            array(
                'MARFA_LOGIN_BY_SMS_CODE_TIME_LIFE',
                Loc::getMessage('MARFA_LOGIN_BY_SMS_CODE_TIME_LIFE'),
                '5',
                array('text', 2)
            )
        )
    )
);

if ($request->isPost() && check_bitrix_sessid()) {
    foreach ($aTabs as $aTab) {
        foreach ($aTab['OPTIONS'] as $arOption) {
            if (!is_array($arOption)) continue;
            if ($arOption['note']) continue;

            if ($request['apply']) {
                $optionValue = $request->getPost($arOption[0]);
                Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(',', $optionValue) : $optionValue);
            } elseif ($request['default']) {
                Option::set($module_id, $arOption[0], $arOption[2]);
            }
        }
    }
    LocalRedirect($APPLICATION->GetCurPage().'?mid='.$module_id.'&lang='.LANG);
}

$tabControl = new CAdminTabControl(
    'tabControl',
    $aTabs
);

$tabControl->Begin();?>

    <form action="<?=$APPLICATION->GetCurPage()?>?mid=<?=$module_id?>&lang=<?=LANG?>" method="post">
        <?foreach ($aTabs as $aTab) {
            if ($aTab['OPTIONS']) {
                $tabControl->BeginNextTab();
                __AdmSettingsDrawList($module_id, $aTab['OPTIONS']);
            }
        }
        $tabControl->Buttons();?>

        <input type="submit" name="apply" value="<?=Loc::GetMessage('MARFA_LOGIN_BY_SMS_OPTIONS_INPUT_APPLY')?>" class="adm-btn-save" />
        <input type="submit" name="default" value="<?=Loc::GetMessage('MARFA_LOGIN_BY_SMS_OPTIONS_INPUT_DEFAULT')?>" />
        <?=bitrix_sessid_post()?>
    </form>

<?$tabControl->End();