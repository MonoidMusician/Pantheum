<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/db.php');

	if (!requireRank(3, FALSE)) echo "Insufficient permissions";
	else
	if (array_key_exists("from",$_GET) and
	    array_key_exists("to",$_GET) and
	    array_key_exists("type",$_GET) and
	    is_numeric($_GET["from"]) and is_numeric($_GET["to"])) {
		$w = WORD(defaultDB(), intval($_GET["from"]));
		$t = WORD(defaultDB(), intval($_GET["to"]));
		$c = CONNECTION($w, $t, $_GET["type"]);
		$w->remove_connection($c);
		exit("success");
	} else exit("\$_GET was invalid");
?>
