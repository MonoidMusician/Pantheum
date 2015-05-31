<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/quiz/common.php');

$id = array_key_exists("id",$_GET) ? $_GET["id"] : NULL;
if ($id !== NULL and
    QUIZ($id)->is_authorized()) {
	$q = QUIZ($id);
	if ($q->delete())
		print("success");
} else print "Cannot find the quiz, or it was not valid, or you are not the correct user";

?>
