<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Smartraf\Auth\Sms;
use Bitrix\Main\Diag\Debug;

define("MARFA_LOG_FILENAME",  "/local/logs/marfa_reg_sms_" . date("Y-m-d") . ".log");

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$log_time=Bitrix\Main\Diag\Helper::getCurrentMicrotime();
