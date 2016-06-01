<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

    if (isLoggedin()) {
        sro('/Pages/restricted/logged-in.php');
    } else {
        sro('/Pages/login/login.php');
    }
?>
