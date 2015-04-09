<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/quiz/common.php');

$c=CURRENTQUIZ();
if ($suid and !$c) die("Session expired");
if (!$suid or $c->finish())
	print("success");

?>
