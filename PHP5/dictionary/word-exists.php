<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/make_example.php');
	sro('/PHP5/lib/PHPLang/display.php');
	sro('/PHP5/lib/PHPLang/db.php');
	sro('/PHP5/lib/PHPLang/misc.php');
	sro('/PHP5/lib/PHPLang/templates.php');

	if (!array_key_exists("lang", $_GET) or !(
		$langs = vec_norm(explode(",", $_GET["lang"]), "trim")
		))
		{ $langs = ['la']; }

	if (!array_key_exists("name", $_GET) or !(
		$names = vec_norm(explode(",", $_GET["name"]), "trim")
		))
		{ $names = NULL; }

	if (!array_key_exists("spart", $_GET) or !(
		$sparts = vec_norm(explode(",", $_GET["spart"]), "trim")
		))
		{ $sparts = NULL; }

	if (!array_key_exists("attr", $_GET) or !(
		$attrs = vec_norm(explode(",", $_GET["attr"]), "trim")
		))
		{ $attrs = []; }

	$definitions = safe_get("definitions", $_GET);
	$connections = safe_get("connections", $_GET);
	$forms = safe_get("forms", $_GET);

	if ($langs and count($langs) == 1 and
	    $names and count($names) == 1 and
	    $sparts and count($sparts) == 1) {
		$w = defaultDB()->searcher()->name($names[0])->spart($sparts[0])->lang($langs[0])->all();
		if (count($w)) exit("present");
		else exit("absent");
	} else exit("Bad \$_GET");
?>