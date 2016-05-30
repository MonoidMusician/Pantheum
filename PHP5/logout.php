<?php
	require_once('/var/www/config.php');
	session_start();

	sro('/Includes/mysql.php');

	global $sli, $suid, $suname, $srank;

	$_SESSION = array();

	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}


	$sli = "";
	$suid = "";
	$suname = "";
	$srank = "";

	session_destroy();

	header("Location: /logged-out.php");
?>

