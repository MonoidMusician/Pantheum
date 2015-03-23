<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/make_example.php');
	sro('/PHP5/lib/PHPLang/display.php');

	if (!requireRank(3, FALSE)) echo "Insufficient permissions";
	else
	if (array_key_exists("id",$_GET) and
	    array_key_exists("attr", $_GET) and
	    is_numeric($_GET["id"])) {
		$w = WORD(defaultDB(), intval($_GET["id"]));
		$attrs = vec_norm(explode(",", $_GET["attr"]), "trim");
		foreach ($attrs as $attr) {
			if (!$attr) continue;
			$a = NULL;
			if ($reverse = (substr($attr, 0, 1) === "!")) {
				$attr = substr($attr, 1);
			}
			if (strpos($attr,"=") === FALSE) {
				if (!$reverse) exit("invalid format: $attr");
				$a = ATTR($attr);
			} else {
				if ($reverse) exit("invalid format: !$attr");
				list ($name,$value) = explode("=",$attr,2);
				$a = ATTR($name,$value);
			}
			if ($a !== NULL) {
				if ($reverse)
					$w->remove_attr($a);
				else
					$w->add_attr($a);
			}
		}
		exit("success");
	} else exit("\$_GET was invalid");
?>
