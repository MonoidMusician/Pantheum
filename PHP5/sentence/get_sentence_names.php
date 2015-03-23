<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/misc.php');
	sro('/PHP5/lib/PHPLang/make_example.php');
	sro('/PHP5/lib/PHPLang/db.php');
	sro('/PHP5/lib/PHPLang/display.php');
	$dir = "/var/www/Data/";
	$sentences_data = json_decode(file_get_contents("$dir/la/sentences.json"),true);
	$keys = array_keys($sentences_data);
	$_ = $keys;
	if ($a = safe_get("sentence",$_GET))
	foreach ($_ as $i=>$k) {
		if (strpos($k,$a) === false) {
			unset($keys[$i]);
		}
	}
	echo json_encode(array_values($keys));
?>
