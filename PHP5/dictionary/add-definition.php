<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/db.php');

	if (!requireRank(3, FALSE)) echo "Insufficient permissions";
	else
	if (array_key_exists("id",$_GET) and
	    array_key_exists("val",$_GET) and
	    array_key_exists("path",$_GET) and
	    is_numeric($_GET["id"])) {
		$w = WORD(defaultDB(), intval($_GET["id"]));
		$d = DEFINITION(defaultDB(), NULL, $w);
		if (array_key_exists("lang",$_GET))
			$d->set_lang($_GET["lang"]);
		else $d->set_lang("en");
		$d->set_value($_GET["val"]);
		if ($_GET["path"]) {
			$p = PATH($w, $_GET["path"]);
			$d->set_path($p);
		}
		$d = $w->add_definition($d);
		exit("success");
	} else exit("\$_GET was invalid (".var_export($_GET,1).")");
?>
