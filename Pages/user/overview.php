<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');

    if (!isLoggedIn()) {
		sro('/Pages/restricted/logged-out.php');
		die("");
	}

	global $suid;
	$level = 'S';
	if (isset($_GET['uid']) && $suid != $_GET['uid']) {
		$level = 'E';
	}

	if (!hasACL('user_settings', 'R', $level) && !hasACL('user_password', 'R', $level)) {
		sro('/Pages/restricted/admin.php');
		die("");
	}
?>
<h2 data-i18n="overview">Overview</h2>

<span data-i18n>Username</span>: <?= $suname ?>
<br>
<span data-i18n>Rank</span>: <?= getNamedRank($srank) ?>
