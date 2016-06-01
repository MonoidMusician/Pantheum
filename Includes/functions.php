<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions/input.php');
	sro('/Includes/functions/email.php');
	sro('/Includes/functions/users.php');

	function requireRank($rank, $die=true) {
		global $srank;

		error_log("Warning: requireRank is depricated.");

		if (!requireLoggedIn($die)) return false;

		if ($die and $srank > $rank) {
			sro('/Pages/restricted/admin.php');
			die("");
		} else return $srank <= $rank;
	}

	function requireLoggedIn($die) {
		global $sli;

		if ($die and $sli != 'true') {
			sro('/Pages/restricted/logged-out.php');
			die("");
		} else return $sli == 'true';
	}

	function isLoggedIn() {
		global $sli;

		return isset($sli) && $sli == 'true';
	}

	function hasACL($name, $action, $level, $uid=false) {

		if (!isLoggedIn(false)) {
			return false;
		}

		global $suid, $mysqli;

		$n = cleanInput('/[^a-zA-Z0-9_]/', strtolower($name));
		$a = strtolower($action);
		$l = strtoupper($level);
		$u = cleanInput('/[^0-9]/', strtolower($suid));

		if ($uid != false) {
			$u = cleanInput('/[^0-9]/', strtolower($uid));
		}

		$M_result = $mysqli->query("SELECT $n FROM acls WHERE user_id=$u;");
		if ($M_result == false) {
			return false;
		}

		$M_row = $M_result->fetch_assoc();
		$v = strtoupper($M_row[$n]);
		$s = -1;

		if ($a == 'read' || $a == 'r') {
			$s = 0;
		} else if ($a == 'write' || $a == 'w') {
			$s = 1;
		}

		if ($s == -1) {
			error_log("Invalid ACL action: $a / $action.");
			return false;
		}

		$g = substr($v, $s, 1);

		if ($g == $l || ($g == 'E' && $l == 'S')) {
			return true;
		} else {
			return false;
		}
	}

	function logEvent($script, $type, $content) {
		global $suid, $mysqli;
		$ip = $_SERVER['REMOTE_ADDR'];
		$ctime = time();
		$userid = '';
		if ((isset($suid)) && ($suid != '')) {
			$userid = $suid;
		}

		$M_query = "INSERT INTO logs (ip, time, userid, script, type, content) VALUES ('$ip', '$ctime', '$userid', '$script', '$type', '$content');";
		$M_result = $mysqli->query($M_query);
	}

	function hasher($text) {
		return strtolower(hash('sha512', strtolower(hash('whirlpool', strtolower(hash('md5', strtolower(hash('md5', strtolower(hash('whirlpool', strtolower(hash('sha512', $text))))))))))));
	}
?>
