<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/db.php');

	if (!requireRank(3, FALSE)) echo "Insufficient permissions";
	else
	if (array_key_exists("id",$_GET) and
	    is_numeric($_GET["id"])) {
		$w = WORD(defaultDB(), intval($_GET["id"]));
		$w->remove();
		exit("success");
	} else exit("\$_GET was invalid");
?>
