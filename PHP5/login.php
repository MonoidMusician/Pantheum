<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	global $mysqli;

	if ((isset($sli)) && ($sli == 'true')) {
		logEvent('login', 'logged-in', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}"));
		die('1');
	} else {
		$username = cleanInput('/[^a-zA-Z0-9]/', $_POST['u']);
		$password = cleanInput('/[^a-zA-Z0-9]/', $_POST['p']);
		$password2 = cleanInput('/[^a-zA-Z0-9]/', $_POST['p2']);
		if (($username != $_POST['u']) || ($username == '') || ($password == '')) {
			logEvent('login', 'blank-input', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}"));
			die('4');
		}

		$M_query = "SELECT * FROM users WHERE username='$username';";
		$M_result = $mysqli->query($M_query);
		$M_count = $M_result->num_rows;

		if ($M_count == 1) {
			$M_row = $M_result->fetch_assoc();

			if (strlen($password) != strlen(hash('md5', 'pi'))) {
				$password = strtolower(hash('md5', hasher(hasher($_POST['p'])) . hasher(hasher($username))));
			}
			$password = strtolower(hash('md5', hasher(hasher($M_row['createip'] . $password . $M_row['id']))));

			if ($M_row['rank'] == 'b') {
				logEvent('login', 'banned-user', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}"));
				die ('3');
			}


			$ip = $_SERVER['REMOTE_ADDR'];
			$current = $ip;

			$current = json_decode($M_row['currentip'] ? $M_row['currentip'] : '[]', true);
			$current[] = $ip;
			$current = json_encode($current);

			if ($M_row['old_password'] == $password || ($password2 != '' && $M_row['password'] == $password2)) {
				$_SESSION['li'] = 'true';
				$_SESSION['username'] = $username;
				$_SESSION['uid'] = $M_row['id'];
				$_SESSION['rank'] = $M_row['rank'];
				$_SESSION['udata'] = $M_row['udata'];

				$M_query6 = "UPDATE users SET currentip='$current' WHERE id='" . $M_row['id'] . "';";
				$M_result6 = $mysqli->query($M_query6);

				if ($M_result6) {
					logEvent('login', 'success', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}, M_query6: `$M_query6`"));
					print "success";
				} else {
					logEvent('login', 'ip-error', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}, M_query6: `$M_query6`"));
					die('1');
				}
			} else {
				logEvent('login', 'bad-password', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, password: `$password`, M_query: `$M_query`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}"));
				die('2');
			}
		} else {
			logEvent('login', 'no-user', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`"));
			die('2');
		}
	}
?>
