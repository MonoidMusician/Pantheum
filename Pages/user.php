<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	if (isLoggedIn()) {
        sro('/Pages/user/user.php');
	} else {
        sro('/Pages/restricted/logged-out.php');
	}
?>
