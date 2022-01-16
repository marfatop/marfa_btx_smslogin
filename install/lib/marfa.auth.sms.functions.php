<?
namespace Marfa\Auth;

/**
 * Класс для работы с авторизацией и регистрацией по СМС
 *
 * @example
 * use Marfa\Auth\MarfaSms;
 * if (Bitrix\Main\Loader::includeModule('auth.by.sms')) {}
 */
class MarfaSms {
	/**
	 * Поиск пользователя по id
	 *
	 * @example
	 * if ($arUser = Sms::getUserInfoById(65792)) print_r($arUsers);
	 *
	 * @param int $id - id пользователя
	 * @return array/false - массив с необходимыми для работы класса полями или false если пользователь не найден
	 */
	public static function getUserInfoById($id) {
		$obUser = \Bitrix\Main\UserTable::getList(array(
			'select' => array(
				'ID',
				'NAME',
				'PERSONAL_PHONE',
				'EMAIL',
				'UF_PHONE_CONFIRM',
				'UF_SMS_CODE',
				'UF_DATE_GENERATE',
				'UF_BLOCKED',
				'UF_DESCR_BLOCKED',
				'UF_COUNT_TRY',
				'UF_COUNT_GENERATE'
			),
			'filter' => array('ID' => $id)
		));
		if ($arUser = $obUser->fetch()){
			return $arUser;
		} else {
			return false;
		}
	}

	/**
	 * Поиск не оптовых пользователей по телефону
	 *
	 * @example
	 * if ($arUsers = Sms::getUserInfoByPhone('+7(905) 585-84-85')) print_r($arUsers);
	 *
	 * @param string $phone - телефон пользователя
	 * @return array/false - массив со всеми найденными пользователями или false если ничего не найдено
	 */
	public static function getUserInfoByPhone($phone) {
		$obUser = \Bitrix\Main\UserTable::getList(array(
			'select' => array(
				'ID',
				'NAME',
				'PERSONAL_PHONE',
				'EMAIL',
				'UF_PHONE_CONFIRM',
				'UF_SMS_CODE',
				'UF_DATE_GENERATE',
				'UF_BLOCKED',
				'UF_DESCR_BLOCKED',
				'UF_COUNT_TRY',
				'UF_COUNT_GENERATE'
			),
			'filter' => array('ACTIVE' => 'Y', 'PERSONAL_PHONE' => $phone)
		));
		$arUsers = array();
		while ($arUser = $obUser->fetch()) {
			$arGroups = \CUser::GetUserGroup($arUser['ID']);
			if (!in_array(9, $arGroups)) $arUsers[] = $arUser;
		}
		if (!empty($arUsers)) {
			return $arUsers;
		} else {
			return false;
		}
	}

	/**
	 * Поиск не оптовых пользователей по email
	 *
	 * @example
	 * if ($arUsers = Sms::getUserInfoByEmail('khanova@smartraf.ru')) print_r($arUsers);
	 *
	 * @param string $email - email пользователя
	 * @return array/false - массив со всеми найденными пользователями или false если ничего не найдено
	 */
	public static function getUserInfoByEmail($email) {
		$obUser = \Bitrix\Main\UserTable::getList(array(
			'select' => array(
				'ID',
				'NAME',
				'PERSONAL_PHONE',
				'EMAIL',
				'UF_PHONE_CONFIRM',
				'UF_SMS_CODE',
				'UF_DATE_GENERATE',
				'UF_BLOCKED',
				'UF_DESCR_BLOCKED',
				'UF_COUNT_TRY',
				'UF_COUNT_GENERATE'
			),
			'filter' => array('ACTIVE' => 'Y', 'EMAIL' => $email)
		));
		$arUsers = array();
		while ($arUser = $obUser->fetch()) {
			$arGroups = \CUser::GetUserGroup($arUser['ID']);
			if (!in_array(9, $arGroups)) $arUsers[] = $arUser;
		}
		if (!empty($arUsers)) {
			return $arUsers;
		} else {
			return false;
		}
	}

	/**
	 * Генерирует новый код подтверждения для пользователя
	 *
	 * @example
	 * $arCodeResult = Sms::generateNewCode(65792);
	 * if ($arCodeResult['result'] == 'success') {} else {}
	 *
	 * @param int $userId - id пользователя
	 * @return array - сгенерированный код или текст ошибки
	 */
	public static function generateNewCode($userId) {
		$arUser = Sms::getUserInfoById($userId);
		$countGenerate = (int)(\Bitrix\Main\Config\Option::get('auth.by.sms', 'MAX_COUNT_GENERATE', '3'));

		if ((int)$arUser['UF_COUNT_GENERATE'] >= $countGenerate && !$arUser['UF_BLOCKED']) {
			$arUser['UF_BLOCKED'] = true;
			Sms::setBlockUser($userId, 'Превышено число разрешённых генераций кода');
			$sError = 'Превышено число разрешённых генераций кода. Потверждение по СМС заблокировано, для разблокировки позвоните оператору.';
			return array('result' => 'error', 'text' => $sError);
		}
		if (!$arUser['UF_BLOCKED']) {
			$newCode = rand(0, 9999);
			while (strlen($newCode) != 4) $newCode = '0'.$newCode;
			$objDateTime = new \Bitrix\Main\Type\DateTime();
			$user = new \CUser;
			$arFields = array(
				'UF_SMS_CODE' => $newCode,
				'UF_DATE_GENERATE' => $objDateTime->toString(),
				'UF_BLOCKED' => 0,
				'UF_DESCR_BLOCKED' => '',
				'UF_COUNT_TRY' => 0,
				'UF_COUNT_GENERATE' => ((int)$arUser['UF_COUNT_GENERATE'] + 1)
			);
			$user->Update($userId, $arFields);
			return array('result' => 'success', 'text' => $newCode);
		}
	}

	/**
	 * Деактивирует указанного пользователя
	 *
	 * @example
	 * Sms::setDeactiveteUser(35197, 'Дублирует аккаунт пользователя 66238');
	 *
	 * @param int $userId - id пользователя
	 * @param string $message - причина деактивации и блокировки
	 * @return
	 */
	public static function setDeactiveteUser($userId, $message = '') {
		$user = new \CUser;
		$arFields = array(
			'ACTIVE' => 'N',
			'UF_BLOCKED' => 1,
			'UF_DESCR_BLOCKED' => $message
		);
		$user->Update($userId, $arFields);
	}

	/**
	 * Ставит флаг блокировки отправки смс указанному пользователю
	 *
	 * @example
	 * Sms::setBlockUser(35197, 'Превышено число разрешённых генераций кода');
	 *
	 * @param int $userId - id пользователя
	 * @param string $message - причина блокировки
	 * @return
	 */
	public static function setBlockUser($userId, $message = '') {
		$user = new \CUser;
		$arFields = array(
			'UF_BLOCKED' => 1,
			'UF_DESCR_BLOCKED' => $message
		);
		$user->Update($userId, $arFields);
	}

	/**
	 * Проверяет код из СМС
	 *
	 * @example
	 * $arCheckResult = Sms::checkCode(35197, '2307');
	 * if ($arCheckResult['result'] == 'success') {} else { echo $arCheckResult['text']; }
	 *
	 * @param int $userId - id пользователя
	 * @param string $sCode - код из СМС
	 * @return array - массив с результатом проверки и описанием ошибки, если возникла
	 */
	public static function checkCode($userId, $sCode) {
		$arUser = Sms::getUserInfoById($userId);
		$timeLife = (float)(\Bitrix\Main\Config\Option::get('auth.by.sms', 'CODE_TIME_LIFE', '5'));
		$objDateTime = new \Bitrix\Main\Type\DateTime();
		$curTime = $objDateTime->getTimestamp();
		$obDateGen = new \Bitrix\Main\Type\DateTime($arUser['UF_DATE_GENERATE']);
		$generateTime = $obDateGen->getTimestamp();
		$arResult = array('result' => 'success', 'text' => '');
		if ($sCode != $arUser['UF_SMS_CODE']) $arResult = array('result' => 'error', 'text' => 'Неверно введён код.');
		if ((($curTime - $generateTime) / 60) >= $timeLife) $arResult = array('result' => 'error', 'text' => 'Код не действителен, превышено время ожидания.');
		return $arResult;
	}

	/**
	 * Проверяет код из СМС
	 *
	 * @example
	 * if (Sms::incCountTry(35197)) {}
	 *
	 * @param int $userId - id пользователя
	 * @return boolean true/false
	 */
	public static function incCountTry($userId) {
		$arUser = Sms::getUserInfoById($userId);
		$countTry = (int)(\Bitrix\Main\Config\Option::get('auth.by.sms', 'MAX_COUNT_TRY', '3'));
		if ($countTry > (int)$arUser['UF_COUNT_TRY']) {
			$user = new \CUser;
			$arFields = array('UF_COUNT_TRY' => ((int)$arUser['UF_COUNT_TRY'] + 1));
			$user->Update($userId, $arFields);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Устанавливает флаг подтверждённого телефона
	 *
	 * @example
	 * Sms::setConfirmPhone(35197);
	 *
	 * @param int $userId - id пользователя
	 * @return
	 */
	public static function setConfirmPhone($userId) {
		$user = new \CUser;
		$arFields = array('UF_PHONE_CONFIRM' => 1);
		$user->Update($userId, $arFields);
	}

	/**
	 * Устанавливает телефон пользователя
	 *
	 * @example
	 * if (!$sError = Sms::setPhone(35197, '+7(905) 585-84-85')) {} else { echo $sError; }
	 *
	 * @param int $userId - id пользователя
	 * @param string $phone - телефон пользователя
	 * @return string - описание ошибки или пустая в случае успеха
	 */
	public static function setPhone($userId, $sPhone) {
		$user = new \CUser;
		$arFields = array('PERSONAL_PHONE' => $sPhone);
		$user->Update($userId, $arFields);
		return $user->LAST_ERROR;
	}

	/**
	 * Создаёт временного пользователя для подтверждения телефона
	 *
	 * @example
	 * $arCurUser = Sms::addTempUser($sUserPhone);
	 *
	 * @param string $phone - телефон пользователя
	 * @param array $arNewUser - массив для регистрации пользователя без создания временного
	 * @return array/string - массив полей временного пользователя или описание ошибки
	 */
	public static function addTempUser($sPhone, $arNewUser = '') {
		$sName = (!empty($arNewUser)) ? $arNewUser['name'] : 'tmp_sms_user_'.md5($sPhone);
		$sEmail = (!empty($arNewUser)) ? $arNewUser['email'] : md5($sPhone).'@temp.ru';
		$objDateTime = new \Bitrix\Main\Type\DateTime();
		$user = new \CUser;
		$arFields = array(
			'NAME' => $sName,
			'EMAIL' => $sEmail,
			'LOGIN' => $sEmail,
			'PERSONAL_PHONE' => $sPhone,
			'LID' => 'ru',
			'ACTIVE' => 'Y',
			'PASSWORD' => md5($objDateTime->getTimestamp()),
			'CONFIRM_PASSWORD' => md5($objDateTime->getTimestamp()),
			'UF_SMS_CODE' => '',
			'UF_DATE_GENERATE' => '',
			'UF_BLOCKED' => 0,
			'UF_DESCR_BLOCKED' => '',
			'UF_COUNT_TRY' => 0,
			'UF_COUNT_GENERATE' => 0
		);
		if ($arFields['ID'] = $user->Add($arFields)) {
			return $arFields;
		} else {
			return $user->LAST_ERROR;
		}
	}

	/**
	 * Проверяет телефон на изменения
	 *
	 * @param array $arFields - массив полей изменяемого пользователя
	 * @return
	 */
	public static function OnBeforeUserUpdateHandler(&$arFields) {
		$arUser = Sms::getUserInfoById($arFields['ID']);
		if ($arUser['PERSONAL_PHONE'] != $arFields['PERSONAL_PHONE'] && !empty($arFields['PERSONAL_PHONE'])) {
			$arUsers = Sms::getUserInfoByPhone($arFields['PERSONAL_PHONE']);
			if (!empty($arUsers)) {
				$GLOBALS['APPLICATION']->ThrowException('Пользователь с таким телефоном уже зарегистрирован на сайте.');
				return false;
			} else {
				$arFields['UF_PHONE_CONFIRM'] = 0;
				$arFields['UF_COUNT_TRY'] = 0;
				$arFields['UF_COUNT_GENERATE'] = 0;
			}
		}
	}

	/**
	 * Проверяет телефон на уникальность перед регистрацией
	 *
	 * @param array $arFields - массив полей регистрации нового пользователя
	 * @return
	 */
	public static function OnBeforeUserRegisterHandler(&$arFields) {
		$arUsers = Sms::getUserInfoByPhone($arFields['PERSONAL_PHONE']);
		if (!empty($arUsers)) {
			if (count($arUsers) == 1) {
				$pos = strpos($arUsers[0]['NAME'], 'tmp_sms_user_');
				if ($pos === 0) return true;
			}
			/*if ($arUsers[0]['UF_PHONE_CONFIRM'] == 0) {
				$GLOBALS['APPLICATION']->ThrowException('Телефон не подтверждён.');
				return false;
			}*/
			$GLOBALS['APPLICATION']->ThrowException('Пользователь с таким телефоном уже зарегистрирован на сайте.');
			return false;
		} else {
			/*$GLOBALS['APPLICATION']->ThrowException('Телефон не подтверждён.');
			return false;*/
		}
	}

	/**
	 * Удаляет временного пользователя после регистрации
	 *
	 * @param array $arFields - массив полей регистрации нового пользователя
	 * @return
	 */
	public static function OnAfterUserRegisterHandler(&$arFields) {
		if ($arFields['USER_ID'] > 0) {
			$arUsers = Sms::getUserInfoByPhone($arFields['PERSONAL_PHONE']);
			foreach ($arUsers as $arUser) {
				$pos = strpos($arUser['NAME'], 'tmp_sms_user_');
				if ($pos === 0) \CUser::Delete($arUser['ID']);
			}

			$arUsers = Sms::getUserInfoByEmail($arFields['EMAIL']);
			foreach ($arUsers as $arUser) {
				if ($arUser['ID'] != $arFields['USER_ID']) {
					Sms::setDeactiveteUser($arUser['ID'], 'Дублирует email аккаунта пользователя '.$arFields['ID']);
				}
			}
			$arUsers = Sms::getUserInfoByPhone($arFields['PERSONAL_PHONE']);
			foreach ($arUsers as $arUser) {
				if ($arUser['ID'] != $arFields['USER_ID']) {
					Sms::setDeactiveteUser($arUser['ID'], 'Дублирует телефон аккаунта пользователя '.$arFields['ID']);
				}
			}

			$user = new \CUser;
			$arFlds = array('UF_PHONE_CONFIRM' => 1);
			$user->Update($arFields['USER_ID'], $arFlds);
		}
	}

	/**
	 * Обнуляет счётчики попыток после успешной авторизации
	 *
	 * @param array $arFields - массив всех полей пользователя
	 * @return
	 */
	public static function OnAfterUserAuthorizeHandler(&$arFields) {
		if (strpos($arFields['user_fields']['EMAIL'], 'default-') === false ) {
			$user = new \CUser;
			$arFlds = array('UF_COUNT_TRY' => 0, 'UF_COUNT_GENERATE' => 0);
			$user->Update($arFields['user_fields']['ID'], $arFlds);

			if (!empty($arFields['user_fields']['EMAIL'])) {
				$arUsers = Sms::getUserInfoByEmail($arFields['user_fields']['EMAIL']);
				foreach ($arUsers as $arUser) {
					if ($arUser['ID'] != $arFields['user_fields']['ID']) {
						Sms::setDeactiveteUser($arUser['ID'], 'Дублирует email аккаунта пользователя '.$arFields['user_fields']['ID']);
					}
				}
			}
			if (!empty($arFields['user_fields']['PERSONAL_PHONE'])) {
				$arUsers = Sms::getUserInfoByPhone($arFields['user_fields']['PERSONAL_PHONE']);
				foreach ($arUsers as $arUser) {
					if ($arUser['ID'] != $arFields['user_fields']['ID']) {
						Sms::setDeactiveteUser($arUser['ID'], 'Дублирует телефон аккаунта пользователя '.$arFields['user_fields']['ID']);
					}
				}
			}
		}
	}
}