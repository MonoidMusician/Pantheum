<?php
	require_once('/var/www/latin/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/db.php');

	if (!requireRank(3, FALSE)) echo "Insufficient permissions";
	else
	if (array_key_exists("id",$_GET) and
	    array_key_exists("path",$_GET) and
	    is_numeric($_GET["id"])) {
		$w = WORD(defaultDB(), intval($_GET["id"]));
		$w->read_paths();
		$p = PATH($w, $_GET["path"]);
		foreach ($w->paths() as $_)
			if ((string)$_ === (string)$p) $p=$_;
		if ($p->id() === NULL)
			exit("Path $p did not exist");
		if (!$p->valid())
			exit("Path $p was invalid");
		$p->remove();
		exit("success");
	} else exit("\$_GET was invalid");
?>
