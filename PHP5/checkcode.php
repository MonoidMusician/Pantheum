<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	if (checkValidCode($_GET['c']) == 0) {
		print "success";
	} else {
		print "fail";
	}
?>
