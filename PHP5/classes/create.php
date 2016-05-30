<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	global $sli, $suid;

	if (isset($sli)) {
		if (!hasACL('teacher_panel', 'W', 'S')) {
			die('{"result": "Must be a teacher to access."}');
		}
	} else {
		die('{"result": "Must be logged in."}');
	}

	die('{"result": "Not implemented"}');
?>
