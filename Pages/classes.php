<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	global $sli;

	if (isset($sli)) {
		if (hasACL('teacher_panel', 'R', 'S')) {
			if (!isset($_GET['class'])) {
				sro('/Pages/classes/select-class.php');
			} else {
				sro('/Pages/classes/teacher.php');
			}
		} else if (hasACL('class', 'R', 'S')){
			if (!isset($_GET['class'])) {
				sro('/Pages/classes/select-class.php');
			} else {
				sro('/Pages/classes/student.php');
			}
		} else {
			sro('/Pages/restricted/student.php');
		}
	} else {
		sro('/Pages/restricted/logged-out.php');
	}
?>
