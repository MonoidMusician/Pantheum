<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	global $mysqli;

	if ((isset($sli)) && ($sli == 'true')) {
		logEvent('validation', 'logged-in', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}"));
		die('1');
	} else {
		$username = cleanInput('/[^a-zA-Z0-9]/', $_POST['u']);
		$email = cleanInput('/[^a-zA-Z0-9\@\.\_\-]/', $_POST['e']);
		$seccode = cleanInput('/[^a-zA-Z0-9]/', $_POST['s']);
		$code = cleanInput('/[^a-zA-Z0-9]/', $_POST['v']);
		$vc = checkValidCode($code);
		if ($vc == 0) {
			if (($username == $_POST['u']) && ($username != '') && ($email != '') && ($seccode != '') && (count($_POST) == 4)) {
				$M_query = "SELECT * FROM users WHERE username='$username' AND email='$email';";
				$M_result = $mysqli->query($M_query);
				$M_count = $M_result->num_rows;

				if ($M_count == 1) {
					$M_row = $M_result->fetch_assoc();

					if ($M_row['rank'] == 'n') {
						$ctime = time();
						$lvtime = $ctime-(60*60*24);

						$rseccode = split(',', $M_row['seccode']);
						if ($rseccode[1] >= $lvtime) {
							if ($seccode == $rseccode[0]) {
								$M_query1 = "UPDATE users SET rank='4', seccode='' WHERE username='$username' AND email='$email'";
								$M_result1 = $mysqli->query($M_query1);
								if ($M_result1) {
									logEvent('validation', 'success', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_count: `$M_count`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}, lvtime: `$lvtime`, rseccode: ['" . implode("','", array_keys($rseccode)) . "'], {'" . implode("', '", $rseccode) . "'}, seccode: `$seccode`, M_query1: `$M_query1`"));
									print "success";
								} else {
									logEvent('validation', 'error', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_count: `$M_count`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}, lvtime: `$lvtime`, rseccode: ['" . implode("','", array_keys($rseccode)) . "'], {'" . implode("', '", $rseccode) . "'}, seccode: `$seccode`, M_query1: `$M_query1`"));
									die('8');
								}
							} else {
								logEvent('validation', 'incorrect', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_count: `$M_count`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}, lvtime: `$lvtime`, rseccode: ['" . implode("','", array_keys($rseccode)) . "'], {'" . implode("', '", $rseccode) . "'}, seccode: `$seccode`"));
								die('7');
							}
						} else {
							logEvent('validation', 'expired', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_count: `$M_count`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}, lvtime: `$lvtime`, rseccode: ['" . implode("','", array_keys($rseccode)) . "'], {'" . implode("', '", $rseccode) . "'}"));
							die('6');
						}
					} else {
						logEvent('validation', 'approved', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_count: `$M_count`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}"));
						die('5');
					}
				} else {
					if ($M_count == 0) {
						logEvent('validation', 'no-account', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_count: `$M_count`"));
					} else {
						$M_row = $M_result->fetch_assoc();
						logEvent('validation', 'multiple-accounts', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_count: `$M_count`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}"));
					}
					die('4');
				}
			} else {
				logEvent('validation', 'blank-input', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}"));
				die('3');
			}
		} else {
			logEvent('validation', 'invalid-code', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}"));
			die('2');
		}
	}
?>
