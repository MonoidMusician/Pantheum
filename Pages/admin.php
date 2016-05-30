<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	global $sli;
	
	if (isset($sli)) {
		if (hasACL('admin_panel', 'R', 'S')) {
			sro('/Pages/admin/admin.php');
		} else {
			sro('/Pages/restricted/admin.php');
		}
	} else {
		sro('/Pages/restricted/logged-out.php');
	}
?>
