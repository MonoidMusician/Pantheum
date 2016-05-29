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
            $result = $M_row;

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

    function setForceLogout($uid) {
        global $mysqli;
        $fl = '' . time() . ',' . $_SERVER['REMOTE_ADDR'] . '';
        $M_query = "UPDATE users SET forcelogout='$fl' WHERE id='" . $uid . "';";
        error_log($M_query);
        $M_result = $mysqli->query($M_query);

        logEvent('users', 'force-logout', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`"));
    }
?>
