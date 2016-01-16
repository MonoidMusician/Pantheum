<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	global $mysqli;

	requireRank('1');

	$M_query = "SELECT * FROM deleted;";
	$M_result = $mysqli->query($M_query);
	while ($M_row = $M_result->fetch_assoc()) {
		$id = $M_row['id'];
		$fid = $M_row['fid'];
		$data = $M_row['data'];

		print "$id{(,)}$fid{(,)}$data{[,]}";
	}
?>
