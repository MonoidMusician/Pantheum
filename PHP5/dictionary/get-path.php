<?php
	require_once('/var/www/latin/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/db.php');

	if (array_key_exists("id",$_GET) and
	    array_key_exists("path",$_GET) and
	    is_numeric($_GET["id"])) {
		$w = WORD(defaultDB(), intval($_GET["id"]));
		$w->read_paths();
		$p = PATH($w, $_GET["path"]);
		if ($p->valid()) {
			exit($p->get());
		} else {
			exit("Path $p was invalid");
		}
	} else exit("\$_GET was invalid");
?>
