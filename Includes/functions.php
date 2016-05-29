<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions/input.php');
	sro('/Includes/functions/code.php');
	sro('/Includes/functions/email.php');
	sro('/Includes/functions/users.php');

	function requireRank($rank, $die=TRUE) {
		global $srank;

		if (!requireLoggedIn($die)) return FALSE;

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

	function hasACL($name, $action, $level) {
		global $suid, $mysqli;

		$n = cleanInput('/[^a-zA-Z0-9_]/', strtolower($name));
		$a = strtolower($action);
		$l = strtoupper($level);

		$M_result = $mysqli->query("SELECT $n FROM acls WHERE user_id=$suid;");
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
