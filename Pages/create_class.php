<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	global $sli;

	if (isset($sli)) {
		if (hasACL('teacher_panel', 'W', 'S')) {
			sro('/Pages/create_class/index.php');
		} else {
			sro('/Pages/restricted/teacher.php');
		}
	} else {
		sro('/Pages/restricted/logged-out.php');
	}
?>
