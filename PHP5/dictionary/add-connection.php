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
		$cons = [[$_GET["from"],$_GET["to"]]];
		if (array_key_exists("mutual", $_GET) and
		    $_GET["mutual"] === "true")
			$cons = [$cons[0],[$_GET["to"],$_GET["from"]]];
		foreach ($cons as list($ab,$ad)) {
			$w = WORD(defaultDB(), intval($ab));
			$t = WORD(defaultDB(), intval($ad));
			$c = CONNECTION($w, $t, $_GET["type"]);
			$w->add_connection($c);
		}
		exit("success");
	} else exit("\$_GET was invalid");
?>
