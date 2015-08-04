<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/common.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/lib/PHPLang/string.php');
sro('/PHP5/lib/PHPLang/misc.php');

$input = $_GET["input"];
$syntax = $_GET["syntax"];
$dict = nano_dfdict();
global $DEBUG_STRING_PHP;
$expression = nanomacro($syntax,$dict,4);
$DEBUG_STRING_PHP = safe_get("debug", $_GET) === "true";
ob_start();
$result = compare_syntax3($syntax, $input, $dict, !!safe_get("matchall", $_GET));
$log = ob_get_contents();
ob_end_clean();
echo json_encode([
	"expression"=>$expression,
	"result"=>$result,
	"log"=>$log,
]);

