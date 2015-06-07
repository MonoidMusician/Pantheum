<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/sql_stmts.php');

global $mysqli;

$stmt = $mysqli->prepare("
	SELECT word_name FROM words
	WHERE word_id IN (
		SELECT word_id FROM attributes
		WHERE attr_tag = 'template' AND attr_value = 'true'
	)
");
$res = [];
sql_getmany($stmt, $res, []);
echo json_encode($res);
?>
