<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/display.php');

	if (!hasACL('admin_panel', 'R', 'S')) {
		sro('/Pages/restricted/admin.php');
		die("");
	}

	global $mysqli;

	$M_query = "SELECT * FROM words LIMIT 100;";
	$M_result = $mysqli->query($M_query);
	while ($M_row = $M_result->fetch_assoc()) {
		$id = $M_row['word_id'];
		$name = $M_row['word_name'];
		$spart = format_spart($M_row['word_spart']);

		print "$id{(,)}$name{(,)}$spart{[,]}";
	}
?>
