<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	global $sli;

	if (isset($sli)) {
		if (!hasACL('teacher_panel', 'R', 'S') || !hasACL('class', 'R', 'S')) {
			sro('/Pages/restricted/teacher.php');
			die("");
		}
	} else {
		sro('/Pages/restricted/logged-out.php');
		die("");
	}
?>
<header>
	<h1>Class Dashboard</h1>
</header>
<article>
</article>
