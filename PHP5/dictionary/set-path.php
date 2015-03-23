<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/db.php');

	if (!requireRank(3, FALSE)) echo "Insufficient permissions";
	else
	if (array_key_exists("id",$_GET) and
	    array_key_exists("path",$_GET) and
	    array_key_exists("val",$_GET) and
	    is_numeric($_GET["id"]) and
	    $_GET["val"] !== "") {
		$w = WORD(defaultDB(), intval($_GET["id"]));
		$w->read_paths();
		$p = PATH($w, $_GET["path"]);
		foreach ($w->paths() as $_)
			if ((string)$_ === (string)$p) $p=$_;
		#error_log(var_export($p->map,1));
		/*error_log(
			(string)$p ."(" . $p->id() . ")" . $p->get()
		);*/
		if ($p->valid()) {
			$exists = $p->hasvalue();
			$p->set($_GET["val"]);
			#error_log(json_encode($p->resolve_hash($w),1));
			#error_log(var_export($p->get(),1));
			$p->set_value($_GET["val"]);
			if (!$exists)
				$p = $w->add_path($p);
			$p->set_value($_GET["val"]);
			if ($p->value() === $_GET["val"])
				exit("success");
			else exit("Path did not return correct value");
		} else {
			exit("Path $p was invalid");
		}
	} else exit("\$_GET was invalid");
?>
