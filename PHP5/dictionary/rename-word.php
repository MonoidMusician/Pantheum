<?php
	require_once('/var/www/latin/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/db.php');

	if (!requireRank(3, FALSE)) echo "Insufficient permissions";
	else
	if (array_key_exists("id",$_GET) and
	    array_key_exists("newname", $_GET) and
	    is_numeric($_GET["id"])) {
		$w = WORD(defaultDB(), intval($_GET["id"]));
		$w->set_name(normalize_spaces($_GET["newname"]));
		exit("success");
	} else exit("\$_GET was invalid");
?>
