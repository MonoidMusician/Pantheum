<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');
	sro('/PHP5/lib/PHPLang/display.php');
	sro('/PHP5/quiz/common.php');

	$result = [quiz_getvalue("score"),quiz_getvalue("out_of")];
	
	print json_encode($result);
?>
