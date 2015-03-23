<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/db.php');
	sro('/PHP5/lib/PHPLang/sql_stmts.php');
	global $sql_stmts;

	if (!requireRank(3, FALSE)) echo "Insufficient permissions";
	else
	if (array_key_exists("id",$_GET) and
	    is_numeric($_GET["id"])) {
		$res = NULL;
		sql_getone($sql_stmts["word_id->last_changed"], $res, ["i", $_GET["id"]]);
		if (!$res) exit("word with id {$_GET['id']} did not exist");
		echo $res;
	} else exit("\$_GET was invalid");
?>
