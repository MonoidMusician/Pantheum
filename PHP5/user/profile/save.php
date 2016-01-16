<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	if (isset($_GET['id'])) {
		global $suid;
		$uid = $suid;
		$id = cleanInput('/[^0-9a-zA-Z]/', $_GET['id']);
		$val = cleanInput('/[^0-9a-zA-Z]/', $_GET['val']);
		$user = json_decode(getUser($uid), true);

		if ($id != $_GET['id']) {
			die("error");
		}

		if (($id == 'username') || ($id == 'email')) {
			if ($id == 'email') {
				$val = cleanInput('/[^a-zA-Z0-9\@\.\_\-]/', $_GET['val']);
			}

			if ($id == 'username') {
				global $suname;
				$_SESSION['username'] = cleanInput('/[^a-zA-Z0-9\@\.\_\-]/', $_GET['val']);
				$suname = $_SESSION['username'];
			}

			$M_query = "UPDATE users SET $id='$val' WHERE id='$uid';";
		} else if ($id == 'password') {
			global $suname;
			$op = cleanInput('/[^0-9a-zA-Z]/', $_GET['opassword']);
			$np = cleanInput('/[^0-9a-zA-Z]/', $_GET['npassword']);
			$cp = cleanInput('/[^0-9a-zA-Z]/', $_GET['cpassword']);
			$user = $suname;

			if (strlen($op) != strlen(hash('md5', 'pi'))) {
				$op = strtolower(hash('md5', hasher(hasher($_GET['opassword'])) . hasher(hasher($user))));
			}

			if (strlen($np) != strlen(hash('md5', 'pi'))) {
				$op = strtolower(hash('md5', hasher(hasher($_GET['opassword'])) . hasher(hasher($user))));
			}

			if (strlen($cp) != strlen(hash('md5', 'pi'))) {
				$op = strtolower(hash('md5', hasher(hasher($_GET['opassword'])) . hasher(hasher($user))));
			}

			$M_query = "SELECT * FROM users WHERE id='$suid';";
			$M_result = $mysqli->query($M_query);
			$M_count = $M_result->num_rows;

			if ($M_count == 1) {
				$M_row = $M_result->fetch_assoc();
				$op = strtolower(hash('md5', hasher(hasher($M_row['createip'] . $op . $M_row['id']))));
				if ($op == $M_row['password']) {
					if ($np == $cp) {
						$password = strtolower(hash('md5', hasher(hasher($M_row['createip'] . $np . $M_row['id']))));

						$M_query = "UPDATE users SET password='$password' WHERE id='$uid';";
					} else {
						logEvent('profile', 'password-mismatch', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, GET: ['" . implode("','", array_keys($_GET)) . "'], {'" . implode("', '", $_GET) . "'}, M_query: `$M_query`, M_count: `$M_count`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}"));
						die("Passwords are not the same.");
					}
				} else {
					logEvent('profile', 'password-wrong', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, GET: ['" . implode("','", array_keys($_GET)) . "'], {'" . implode("', '", $_GET) . "'}, M_query: `$M_query`, M_count: `$M_count`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}"));
					die("Wrong password.");
				}
			} else {
				logEvent('profile', 'password-multiple-users', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, GET: ['" . implode("','", array_keys($_GET)) . "'], {'" . implode("', '", $_GET) . "'}, M_query: `$M_query`, M_count: `$M_count`"));
				die("error");
			}
		} else {
			$tmp = json_decode($user['settings'], true);
			$val = encodeHex($_GET['val']);
			$tmp[$id] = $val;

			$settings = json_encode($tmp);

			$M_query = "UPDATE users SET settings='$settings' WHERE id='$uid';";
		}

		global $mysqli;
		$M_result = $mysqli->query($M_query);

		if ($M_result) {
			print 'success';
			logEvent('profile', 'success', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, GET: ['" . implode("','", array_keys($_GET)) . "'], {'" . implode("', '", $_GET) . "'}, M_query: `$M_query`"));
		} else {
			logEvent('profile', 'query-fail', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, GET: ['" . implode("','", array_keys($_GET)) . "'], {'" . implode("', '", $_GET) . "'}, M_query: `$M_query`"));
			die("error");
		}
	} else {
		logEvent('profile', 'missing-id', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, GET: ['" . implode("','", array_keys($_GET)) . "'], {'" . implode("', '", $_POST) . "'}"));
		die("error");
	}
?>
