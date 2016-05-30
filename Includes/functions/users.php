<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');

    function getUser($uid) {
        global $mysqli, $suid;

        if (!isset($uid)) {
            $uid = $suid;
        }

        $result = array();
        $M_query = "SELECT * FROM users WHERE id='$uid';";
        $M_result = $mysqli->query($M_query);
        if ($M_row = $M_result->fetch_assoc()) {
            $result = json_encode($M_row);

            return $result;
        } else {
            return false;
        }
    }

    function getNamedRank($rnumber) {
        $rank = 'None';
        switch ($rnumber) {
            case '1':
                $rank = 'Admin';
                break;
            case '2':
                $rank = 'TA';
                break;
            case '3':
                $rank = 'Editor';
                break;
            case '4':
                $rank = 'User';
                break;
            case 'b1':
                $rank = 'Banned&nbsp;Admin';
                break;
            case 'b2':
                $rank = 'Banned&nbsp;TA';
                break;
            case 'b3':
                $rank = 'Banned&nbsp;Editor';
                break;
            case 'b4':
                $rank = 'Banned&nbsp;User';
                break;
        }

        return $rank;
    }

	function getClasses($uid) {
		global $mysqli, $suid;

		$u = cleanInput('/[^0-9]/', strtolower($uid));

		$result = [];

		if (hasACL("teacher_panel", "R", "E")) {
			$M_result = $mysqli->query("SELECT name,id FROM class;");
			while ($M_row = $M_result->fetch_assoc()) {
				$result[] = [ "id" => $M_row['id'], "name" => $M_row['name'] ];
			}
		} else {
			$M_result = $mysqli->query("SELECT class_id FROM class_acls WHERE user_id=$uid;");
			while ($M_row = $M_result->fetch_assoc()) {
				$M_result2 = $mysqli->query("SELECT name FROM class WHERE id=" . $M_row['class_id'] . ";");
				$n = "Unknown";
				if ($M_result2 != false) {
					$M_row2 = $M_result2->fetch_assoc();
					$n = $M_row2['name'];
				}
				$result[] = [ "id" => $M_row['class_id'], "name" => $n ];
			}
		}

		return $result;
	}

    function setForceLogout($uid) {
        global $mysqli;
        $fl = '' . time() . ',' . $_SERVER['REMOTE_ADDR'] . '';
        $M_query = "UPDATE users SET forcelogout='$fl' WHERE id='" . $uid . "';";
        error_log($M_query);
        $M_result = $mysqli->query($M_query);

        logEvent('users', 'force-logout', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`"));
    }
?>
