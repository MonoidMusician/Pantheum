<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	if (isLoggedIn()) {
		sro('/Pages/home/home-logged-in.php');
	} else {
		sro('/Pages/home/home-logged-out.php');
	}
?>
