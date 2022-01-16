<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;

Loc::loadMessages(__FILE__);

class marfa_login__by_sms extends CModule {
    var $MODULE_ID;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    public function __construct() {
        if (file_exists(__DIR__.'/version.php')) {
            $arModuleVersion = array();
            include_once(__DIR__.'/version.php');

            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = str_replace('_', '.', get_class($this));
        $this->MODULE_NAME = Loc::getMessage('MARFA_LOGIN_BY_SMS_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('MARFA_LOGIN_BY_SMS_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('MARFA_LOGIN_BY_SMS_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('MARFA_LOGIN_BY_SMS_PARTNER_URI');

        return false;
    }

    public function DoInstall() {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->InstallUserProp();
        $this->InstallEvents();
        RegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile("Установка модуля marfa_btx_sms_login", $DOCUMENT_ROOT."/local/modules/marfa_btx_sms_login/install/step.php");
        return true;
    }

    public function DoUninstall() {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->UnInstallUserProp();
        $this->UnInstallEvents();
        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile("Деинсталляция  модуля marfa_btx_sms_login", $DOCUMENT_ROOT."/local/modules/marfa_btx_sms_login/install/unstep.php");
        return true;
    }

    public function InstallUserProp() {
        $oUserTypeEntity = new CUserTypeEntity();
        $aUserFields = array(
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_MARFA_LOGIN_BY_SMS_PHONE_CONFIRM',
            'USER_TYPE_ID' => 'boolean',
            'XML_ID' => 'UF_MARFA_LOGIN_BY_SMS_PHONE_CONFIRM',
            'SORT' => 4900,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => array(
                'DEFAULT_VALUE' => 0,
                'DISPLAY' => 'CHECKBOX',
                'LABEL' => array(),
                'LABEL_CHECKBOX' => ''
            ),
            'EDIT_FORM_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_PHONE_CONFIRM_LABEL')),
            'LIST_COLUMN_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_PHONE_CONFIRM_LABEL')),
            'LIST_FILTER_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_PHONE_CONFIRM_LABEL')),
            'ERROR_MESSAGE' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_PHONE_CONFIRM_LABEL')),
            'HELP_MESSAGE' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_PHONE_CONFIRM_LABEL'))
        );
        $oUserTypeEntity->Add($aUserFields);

        $aUserFields = array(
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_MARFA_LOGIN_BY_SMS_SMS_CODE',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => 'UF_MARFA_LOGIN_BY_SMS_SMS_CODE',
            'SORT' => 5100,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => '',
            'EDIT_IN_LIST' => '',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => array(
                'DEFAULT_VALUE' => '',
                'SIZE' => 4,
                'ROWS' => 1,
                'MIN_LENGTH' => 0,
                'MAX_LENGTH' => 0,
                'REGEXP' => ''
            ),
            'EDIT_FORM_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_CODE_LABEL')),
            'LIST_COLUMN_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_CODE_LABEL')),
            'LIST_FILTER_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_CODE_LABEL')),
            'ERROR_MESSAGE' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_CODE_LABEL')),
            'HELP_MESSAGE' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_CODE_LABEL'))
        );
        $oUserTypeEntity->Add($aUserFields);

        $aUserFields = array(
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_MARFA_LOGIN_BY_SMS_DATE_GENERATE',
            'USER_TYPE_ID' => 'datetime',
            'XML_ID' => 'UF_MARFA_LOGIN_BY_SMS_DATE_GENERATE',
            'SORT' => 5200,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => array(
                'DEFAULT_VALUE' => array(
                    'TYPE' => 'NONE',
                    'VALUE' => ''
                ),
                'USE_SECOND' => 'Y'
            ),
            'EDIT_FORM_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_DATE_GENERATE_LABEL')),
            'LIST_COLUMN_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_DATE_GENERATE_LABEL')),
            'LIST_FILTER_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_DATE_GENERATE_LABEL')),
            'ERROR_MESSAGE' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_DATE_GENERATE_LABEL')),
            'HELP_MESSAGE' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_DATE_GENERATE_LABEL'))
        );
        $oUserTypeEntity->Add($aUserFields);

        $aUserFields = array(
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_MARFA_LOGIN_BY_SMS_BLOCKED',
            'USER_TYPE_ID' => 'boolean',
            'XML_ID' => 'UF_MARFA_LOGIN_BY_SMS_BLOCKED',
            'SORT' => 5300,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => array(
                'DEFAULT_VALUE' => 0,
                'DISPLAY' => 'CHECKBOX',
                'LABEL' => array(),
                'LABEL_CHECKBOX' => ''
            ),
            'EDIT_FORM_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_BLOCKED_LABEL')),
            'LIST_COLUMN_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_BLOCKED_LABEL')),
            'LIST_FILTER_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_BLOCKED_LABEL')),
            'ERROR_MESSAGE' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_BLOCKED_LABEL')),
            'HELP_MESSAGE' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_BLOCKED_LABEL'))
        );
        $oUserTypeEntity->Add($aUserFields);

        $aUserFields = array(
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_MARFA_LOGIN_BY_SMS_DESCR_BLOCKED',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => 'UF_MARFA_LOGIN_BY_SMS_DESCR_BLOCKED',
            'SORT' => 5400,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => array(
                'SIZE' => 120,
                'ROWS' => 5,
                'REGEXP' => '',
                'MIN_LENGTH' => 0,
                'MAX_LENGTH' => 0,
                'DEFAULT_VALUE' => ''
            ),
            'EDIT_FORM_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_DESCR_BLOCKED_LABEL')),
            'LIST_COLUMN_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_DESCR_BLOCKED_LABEL')),
            'LIST_FILTER_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_DESCR_BLOCKED_LABEL')),
            'ERROR_MESSAGE' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_DESCR_BLOCKED_LABEL')),
            'HELP_MESSAGE' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_DESCR_BLOCKED_LABEL'))
        );
        $oUserTypeEntity->Add($aUserFields);

        $aUserFields = array(
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_MARFA_LOGIN_BY_SMS_COUNT_TRY',
            'USER_TYPE_ID' => 'integer',
            'XML_ID' => 'UF_MARFA_LOGIN_BY_SMS_COUNT_TRY',
            'SORT' => 5500,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => array(
                'SIZE' => 2,
                'MIN_VALUE' => 0,
                'MAX_VALUE' => 0,
                'DEFAULT_VALUE' => 0
            ),
            'EDIT_FORM_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_COUNT_TRY_LABEL')),
            'LIST_COLUMN_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_COUNT_TRY_LABEL')),
            'LIST_FILTER_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_COUNT_TRY_LABEL')),
            'ERROR_MESSAGE' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_COUNT_TRY_LABEL')),
            'HELP_MESSAGE' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_COUNT_TRY_LABEL'))
        );
        $oUserTypeEntity->Add($aUserFields);

        $aUserFields = array(
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_MARFA_LOGIN_BY_SMS_COUNT_GENERATE',
            'USER_TYPE_ID' => 'integer',
            'XML_ID' => 'UF_MARFA_LOGIN_BY_SMS_COUNT_GENERATE',
            'SORT' => 5600,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => array(
                'SIZE' => 2,
                'MIN_VALUE' => 0,
                'MAX_VALUE' => 0,
                'DEFAULT_VALUE' => 0
            ),
            'EDIT_FORM_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_COUNT_GENERATE_LABEL')),
            'LIST_COLUMN_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_COUNT_GENERATE_LABEL')),
            'LIST_FILTER_LABEL' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_COUNT_GENERATE_LABEL')),
            'ERROR_MESSAGE' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_COUNT_GENERATE_LABEL')),
            'HELP_MESSAGE' => array('ru' => Loc::getMessage('UF_MARFA_LOGIN_BY_SMS_COUNT_GENERATE_LABEL'))
        );
        $oUserTypeEntity->Add($aUserFields);
    }

    public function UnInstallUserProp() {
        $oUserTypeEntity = new CUserTypeEntity();
        $arDeletedProps = array(
            'UF_MARFA_LOGIN_BY_SMS_PHONE_CONFIRM',
            'UF_MARFA_LOGIN_BY_SMS_SMS_CODE',
            'UF_MARFA_LOGIN_BY_SMS_DATE_GENERATE',
            'UF_MARFA_LOGIN_BY_SMS_BLOCKED',
            'UF_MARFA_LOGIN_BY_SMS_DESCR_BLOCKED',
            'UF_MARFA_LOGIN_BY_SMS_COUNT_TRY',
            'UF_MARFA_LOGIN_BY_SMS_COUNT_GENERATE'
        );
        foreach ($arDeletedProps as $sPropCode) {
            $obUserProp = CUserTypeEntity::GetList(array(), array('XML_ID' => $sPropCode));
            if ($arUserProp = $obUserProp->Fetch()) {
                $oUserTypeEntity->Delete($arUserProp['ID']);
            }
        }
    }

    public function InstallEvents() {
        EventManager::getInstance()->registerEventHandler(
            'main',
            'OnBeforeUserUpdate',
            $this->MODULE_ID,
            'Marfa\Auth\MarfaSms',
            'OnBeforeUserUpdateHandler'
        );

        EventManager::getInstance()->registerEventHandler(
            'main',
            'OnBeforeUserRegister',
            $this->MODULE_ID,
            'Marfa\Auth\MarfaSms',
            'OnBeforeUserRegisterHandler'
        );

        EventManager::getInstance()->registerEventHandler(
            'main',
            'OnAfterUserRegister',
            $this->MODULE_ID,
            'Marfa\Auth\MarfaSms',
            'OnAfterUserRegisterHandler'
        );

        EventManager::getInstance()->registerEventHandler(
            'main',
            'OnAfterUserAuthorize',
            $this->MODULE_ID,
            'Marfa\Auth\MarfaSms',
            'OnAfterUserAuthorizeHandler'
        );
    }

    public function UnInstallEvents() {
        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnBeforeUserUpdate',
            $this->MODULE_ID,
            'Marfa\Auth\MarfaSms',
            'OnBeforeUserUpdateHandler'
        );

        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnBeforeUserRegister',
            $this->MODULE_ID,
            'Marfa\Auth\MarfaSms',
            'OnBeforeUserRegisterHandler'
        );

        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnAfterUserRegister',
            $this->MODULE_ID,
            'Marfa\Auth\MarfaSms',
            'OnAfterUserRegisterHandler'
        );

        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnAfterUserAuthorize',
            $this->MODULE_ID,
            'Marfa\Auth\MarfaSms',
            'OnAfterUserAuthorizeHandler'
        );
    }
}